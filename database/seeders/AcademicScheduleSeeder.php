<?php

namespace Database\Seeders;

use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\LabSession;
use App\Models\Labs;
use App\Models\Matkul;
use App\Models\MatkulConfig;
use App\Models\Period;
use App\Models\Schedule;
use App\Services\Scheduling\ClassImportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AcademicScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding Academic Schedule...');

        DB::transaction(function () {
            // 1. Sinkronisasi Ruangan / Labs (Kondisi status grid sama persis dengan trial)
            $this->seedRooms();

            // 2. Setup Periode Aktif & Schedule Utama
            $period = $this->resolveActivePeriod();
            $schedule = $this->resolveActiveSchedule($period);

            // 3. Setup Mata Kuliah & Kurikulum
            $this->seedCourses();

            // 4. Setup Konfigurasi Praktikum Mata Kuliah
            $this->seedCourseConfigs($period);

            // 5. Ingest Kelas & Pertemuan dari file Excel (sama dengan trial)
            $this->importClassesFromExcel($period, $schedule);

            // 6. Tempatkan sesi jadwal pada grid (sama persis dengan trial)
            $this->seedPlacements($schedule);
        });

        $this->command->info('✅ Academic Schedule Seeder berhasil dijalankan!');
    }

    /**
     * Sinkronisasi data ruangan & status grid (manual_only vs otomatis).
     */
    protected function seedRooms(): void
    {
        $roomsData = [
            // Internal Labs
            [
                'code' => 'SI',
                'name' => 'Lab Sistem Informasi',
                'location' => 'P.205',
                'capacity' => 56,
                'manual_only' => false,
                'is_active' => true,
                'notes' => null,
            ],
            [
                'code' => 'PG',
                'name' => 'Lab Pemrograman',
                'location' => 'P.206',
                'capacity' => 44,
                'manual_only' => false,
                'is_active' => true,
                'notes' => '26 komputer + 18 meja; kapasitas dapat diubah admin',
            ],
            [
                'code' => 'STUDIO',
                'name' => 'Lab Studio',
                'location' => 'P.201a',
                'capacity' => 35,
                'manual_only' => false,
                'is_active' => true,
                'notes' => null,
            ],
            [
                'code' => 'SC',
                'name' => 'Lab Sistem Cerdas',
                'location' => 'P.207b',
                'capacity' => 19,
                'manual_only' => false,
                'is_active' => true,
                'notes' => null,
            ],
            [
                'code' => 'JK',
                'name' => 'Lab Jaringan Komputer',
                'location' => 'P.207a',
                'capacity' => 18,
                'manual_only' => false,
                'is_active' => true,
                'notes' => null,
            ],
            [
                'code' => 'MOBDEV',
                'name' => 'Lab Mobile Development',
                'location' => 'MOBDEV',
                'capacity' => 38,
                'manual_only' => false,
                'is_active' => true,
                'notes' => null,
            ],
            [
                'code' => 'VR',
                'name' => 'Lab Virtual Reality',
                'location' => 'VR',
                'capacity' => 20,
                'manual_only' => true, // Manual Only (Status Grid sama dengan trial)
                'is_active' => true,
                'notes' => 'manual_only: tampil di grid, tidak pernah diisi otomatis',
            ],
            [
                'code' => 'MM',
                'name' => 'Lab Multimedia',
                'location' => 'MM',
                'capacity' => 20,
                'manual_only' => true, // Manual Only (Status Grid sama dengan trial)
                'is_active' => true,
                'notes' => 'manual_only',
            ],
            // External Labs (Puskom)
            [
                'code' => 'PA',
                'name' => 'Puskom PA',
                'location' => 'PA',
                'capacity' => 40,
                'manual_only' => false,
                'is_active' => true,
                'notes' => 'Puskom',
            ],
            [
                'code' => 'PB',
                'name' => 'Puskom PB',
                'location' => 'PB',
                'capacity' => 40,
                'manual_only' => false,
                'is_active' => true,
                'notes' => 'Puskom',
            ],
            [
                'code' => 'PC',
                'name' => 'Puskom PC',
                'location' => 'PC',
                'capacity' => 20,
                'manual_only' => false,
                'is_active' => true,
                'notes' => 'Puskom',
            ],
            [
                'code' => 'PD',
                'name' => 'Puskom PD',
                'location' => 'PD',
                'capacity' => 40,
                'manual_only' => false,
                'is_active' => true,
                'notes' => 'Puskom',
            ],
        ];

        foreach ($roomsData as $r) {
            $lab = Labs::all()->first(fn ($item) => $item->code === $r['code']);

            if ($lab) {
                $lab->update([
                    'capacity' => $r['capacity'],
                    'manual_only' => $r['manual_only'],
                    'is_active' => $r['is_active'],
                    'notes' => $r['notes'] ?? $lab->notes,
                ]);
            } else {
                Labs::create([
                    'name' => $r['name'],
                    'location' => $r['location'],
                    'capacity' => $r['capacity'],
                    'manual_only' => $r['manual_only'],
                    'is_active' => $r['is_active'],
                    'notes' => $r['notes'],
                ]);
            }
        }
    }

    /**
     * Dapatkan periode aktif atau fallback ke periode pertama.
     */
    protected function resolveActivePeriod(): Period
    {
        $period = Period::where('active', 1)->first() ?? Period::first();

        if (! $period) {
            $period = Period::create([
                'semester' => 'Gasal',
                'academic_year' => '2024/2025',
                'active' => 1,
            ]);
        }

        return $period;
    }

    /**
     * Dapatkan schedule aktif atau buat baru.
     */
    protected function resolveActiveSchedule(Period $period): Schedule
    {
        $schedule = Schedule::where('period_id', $period->id)->where('is_active', true)->first();

        if (! $schedule) {
            $schedule = Schedule::create([
                'period_id' => $period->id,
                'name' => 'Jadwal Utama',
                'is_active' => true,
                'snapshot_json' => [],
            ]);
        }

        return $schedule;
    }

    /**
     * Setup kurikulum dan semester mata kuliah.
     */
    protected function seedCourses(): void
    {
        $courses = [
            ['name' => 'Algoritma dan Pemrograman', 'code' => 'IF101', 'semester' => 1],
            ['name' => 'Struktur Data', 'code' => 'IF102', 'semester' => 3],
            ['name' => 'Basis Data', 'code' => 'IF201', 'semester' => 3],
            ['name' => 'Pemrograman Berorientasi Obyek', 'code' => 'IF-PBO', 'semester' => 3],
            ['name' => 'Desain dan Analisis Algoritma', 'code' => 'IF-DAA', 'semester' => 3],
            ['name' => 'Grafika Komputer', 'code' => 'IF-GK', 'semester' => 5],
        ];

        foreach ($courses as $c) {
            $matkul = Matkul::where('nama', $c['name'])
                ->orWhere('nama', 'LIKE', '%' . $c['name'] . '%')
                ->first();

            if ($matkul) {
                $matkul->update([
                    'curriculum_semester' => $c['semester'],
                    'is_required' => true,
                ]);
            } else {
                // Ensure code unique
                $code = $c['code'];
                $counter = 1;
                while (Matkul::where('kode', $code)->exists()) {
                    $code = $c['code'] . '-' . $counter++;
                }

                Matkul::create([
                    'kode' => $code,
                    'nama' => $c['name'],
                    'sks' => '3',
                    'curriculum_semester' => $c['semester'],
                    'is_required' => true,
                ]);
            }
        }
    }

    /**
     * Setup konfigurasi praktikum dan responsi mata kuliah per periode.
     */
    protected function seedCourseConfigs(Period $period): void
    {
        $configs = [
            ['course' => 'Algoritma dan Pemrograman', 'semester' => 1, 'practicum' => 3, 'responsi' => 2, 'manual' => false],
            ['course' => 'Struktur Data', 'semester' => 3, 'practicum' => 3, 'responsi' => 2, 'manual' => false],
            ['course' => 'Pemrograman Berorientasi Obyek', 'semester' => 3, 'practicum' => 3, 'responsi' => 0, 'manual' => false],
            ['course' => 'Desain dan Analisis Algoritma', 'semester' => 3, 'practicum' => 0, 'responsi' => 2, 'manual' => false],
            ['course' => 'Basis Data', 'semester' => 3, 'practicum' => 3, 'responsi' => 0, 'manual' => false],
            ['course' => 'Grafika Komputer', 'semester' => 5, 'practicum' => 3, 'responsi' => 0, 'manual' => true],
        ];

        foreach ($configs as $cfg) {
            $matkul = Matkul::where('nama', $cfg['course'])->first();
            if ($matkul) {
                MatkulConfig::updateOrCreate(
                    [
                        'period_id' => $period->id,
                        'matkul_id' => $matkul->id,
                    ],
                    [
                        'scheduling_semester' => $cfg['semester'],
                        'practicum_hours' => $cfg['practicum'],
                        'responsi_hours' => $cfg['responsi'],
                        'is_manual' => $cfg['manual'],
                    ]
                );
            }
        }
    }

    /**
     * Import kelas, pertemuan perkuliahan, dan inisialisasi sesi lab dari Excel.
     */
    protected function importClassesFromExcel(Period $period, Schedule $schedule): void
    {
        $primaryPath = database_path('seeders/file/Copy_of_Praktikum_Gasal_26_27.xlsx');

        if (file_exists($primaryPath)) {
            $importService = app(ClassImportService::class);
            $importService->importFromFile($primaryPath, $period, overwrite: true);
        }
    }

    /**
     * Tempatkan sesi jadwal pada grid (identik 100% dengan trial).
     */
    protected function seedPlacements(Schedule $schedule): void
    {
        $allRooms = Labs::all()->keyBy(fn ($r) => $r->code);

        // 11 sesi yang terjadwal di trial
        $placements = [
            // Algoritma dan Pemrograman A
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'A', 'type' => 'PRAKTIKUM', 'room_code' => 'SI', 'day' => 2, 'start_min' => 810, 'end_min' => 990],
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'A', 'type' => 'RESPONSI', 'room_code' => 'SI', 'day' => 3, 'start_min' => 630, 'end_min' => 750],
            // Algoritma dan Pemrograman B
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'B', 'type' => 'PRAKTIKUM', 'room_code' => 'STUDIO', 'day' => 2, 'start_min' => 810, 'end_min' => 990],
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'B', 'type' => 'RESPONSI', 'room_code' => 'PG', 'day' => 3, 'start_min' => 630, 'end_min' => 750],
            // Pemrograman Berorientasi Obyek A
            ['course' => 'Pemrograman Berorientasi Obyek', 'section' => 'A', 'type' => 'PRAKTIKUM', 'room_code' => 'PG', 'day' => 5, 'start_min' => 450, 'end_min' => 630],
            // Struktur Data B
            ['course' => 'Struktur Data', 'section' => 'B', 'type' => 'RESPONSI', 'room_code' => 'SI', 'day' => 5, 'start_min' => 870, 'end_min' => 990],
            // Struktur Data A
            ['course' => 'Struktur Data', 'section' => 'A', 'type' => 'PRAKTIKUM', 'room_code' => 'SI', 'day' => 4, 'start_min' => 810, 'end_min' => 990],
            ['course' => 'Struktur Data', 'section' => 'A', 'type' => 'RESPONSI', 'room_code' => 'STUDIO', 'day' => 5, 'start_min' => 450, 'end_min' => 570],
            // Desain dan Analisis Algoritma A
            ['course' => 'Desain dan Analisis Algoritma', 'section' => 'A', 'type' => 'RESPONSI', 'room_code' => 'PG', 'day' => 2, 'start_min' => 630, 'end_min' => 750],
            // Desain dan Analisis Algoritma B
            ['course' => 'Desain dan Analisis Algoritma', 'section' => 'B', 'type' => 'RESPONSI', 'room_code' => 'STUDIO', 'day' => 3, 'start_min' => 750, 'end_min' => 870],
            // Basis Data A
            ['course' => 'Basis Data', 'section' => 'A', 'type' => 'PRAKTIKUM', 'room_code' => 'STUDIO', 'day' => 4, 'start_min' => 750, 'end_min' => 930],
        ];

        foreach ($placements as $p) {
            $room = $allRooms->get($p['room_code']);
            if (! $room) continue;

            $session = LabSession::where('schedule_id', $schedule->id)
                ->where('session_type', $p['type'])
                ->whereHas('section', function ($q) use ($p) {
                    $q->where('label', $p['section'])
                      ->whereHas('course', fn ($c) => $c->where('nama', $p['course']));
                })
                ->first();

            if ($session) {
                $session->update([
                    'lab_id' => $room->id,
                    'day_of_week' => $p['day'],
                    'start_min' => $p['start_min'],
                    'end_min' => $p['end_min'],
                    'status' => 'SCHEDULED',
                    'unscheduled_reason' => null,
                ]);
            }
        }

        // 7 sesi yang tidak terjadwal beserta alasannya (sama persis dengan trial)
        $unscheduled = [
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'D', 'type' => 'PRAKTIKUM', 'reason' => 'Kelas hari Sabtu, tidak ada hari sesudahnya'],
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'D', 'type' => 'RESPONSI', 'reason' => 'Kelas hari Sabtu, tidak ada hari sesudahnya'],
            ['course' => 'Struktur Data', 'section' => 'B', 'type' => 'PRAKTIKUM', 'reason' => 'Tidak ada slot kosong setelah kelas'],
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'C', 'type' => 'PRAKTIKUM', 'reason' => 'Tidak ada slot kosong setelah kelas'],
            ['course' => 'Algoritma dan Pemrograman', 'section' => 'C', 'type' => 'RESPONSI', 'reason' => 'Tidak ada slot kosong setelah kelas'],
            ['course' => 'Grafika Komputer', 'section' => 'A', 'type' => 'PRAKTIKUM', 'reason' => 'Dijadwalkan manual oleh admin'],
            ['course' => 'Grafika Komputer', 'section' => 'B', 'type' => 'PRAKTIKUM', 'reason' => 'Dijadwalkan manual oleh admin'],
        ];

        foreach ($unscheduled as $u) {
            $session = LabSession::where('schedule_id', $schedule->id)
                ->where('session_type', $u['type'])
                ->whereHas('section', function ($q) use ($u) {
                    $q->where('label', $u['section'])
                      ->whereHas('course', fn ($c) => $c->where('nama', $u['course']));
                })
                ->first();

            if ($session) {
                $session->update([
                    'lab_id' => null,
                    'day_of_week' => null,
                    'start_min' => null,
                    'end_min' => null,
                    'status' => 'UNSCHEDULED',
                    'unscheduled_reason' => $u['reason'],
                ]);
            }
        }
    }
}
