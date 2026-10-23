<?php

namespace App\Services\Scheduling;

use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\LabSession;
use App\Models\Labs as Room;
use App\Models\Matkul as Course;
use App\Models\MatkulConfig as CourseConfig;
use App\Models\Period;
use App\Models\Schedule;
use App\Services\Excel\ScheduleExcelParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClassImportService
{
    public const DAY_INDEX = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6,
        'Minggu' => 0,
    ];

    public function __construct(
        protected ScheduleExcelParser $parser
    ) {}

    /**
     * Import classes from an Excel file into a specific period.
     *
     * @return array{
     *     period: Period,
     *     sections_count: int,
     *     meetings_count: int,
     *     sessions_count: int,
     *     warnings: array<int, string>
     * }
     */
    public function importFromFile(string $filePath, Period $period, bool $overwrite = true): array
    {
        $parseResult = $this->parser->parse($filePath);
        $meetingsData = $parseResult['meetings'];
        $warnings = $parseResult['warnings'];

        return $this->importParsedMeetings($meetingsData, $period, $overwrite, $warnings);
    }

    /**
     * Import already parsed meetings data into period.
     */
    public function importParsedMeetings(array $meetingsData, Period $period, bool $overwrite = true, array $initialWarnings = []): array
    {
        $warnings = $initialWarnings;

        return DB::transaction(function () use ($meetingsData, $period, $overwrite, $warnings) {
            $schedule = Schedule::firstOrCreate(
                ['period_id' => $period->id, 'is_active' => true],
                ['name' => 'Jadwal Utama']
            );

            if ($overwrite) {
                // Delete existing sections, meetings, sessions for this period
                LabSession::where('schedule_id', $schedule->id)->delete();
                ClassMeeting::whereHas('classSection', fn ($q) => $q->where('period_id', $period->id))->delete();
                ClassSection::where('period_id', $period->id)->delete();
            }

            $allRooms = Room::all();
            $roomsByCode = [];
            foreach ($allRooms as $r) {
                $roomsByCode[$r->code] = $r;
                $roomsByCode[strtoupper($r->name)] = $r;
            }

            $configsByCourse = CourseConfig::where('period_id', $period->id)->get()->keyBy('matkul_id');

            $sectionsMap = []; // "course_id|label" => ClassSection
            $createdMeetingsCount = 0;
            $createdSessionsCount = 0;

            foreach ($meetingsData as $item) {
                $courseName = $item['course_name'];
                $sectionLabel = $item['section_label'];
                $day = $item['day'];
                $startMin = $item['start_min'];
                $endMin = $item['end_min'];
                $roomCode = $item['room_code'];
                $roomLabel = $item['room_label'] ?? ($roomCode ? "Lab $roomCode" : 'Ruang Kelas');
                $enrollment = $item['enrollment'] ?? 30;

                // Find or create course in matkuls
                $course = Course::where('nama', $courseName)->first();
                if (! $course) {
                    $randomCode = 'MK' . strtoupper(Str::random(4));
                    while (Course::where('kode', $randomCode)->exists()) {
                        $randomCode = 'MK' . strtoupper(Str::random(4));
                    }

                    $course = Course::create([
                        'nama' => $courseName,
                        'kode' => $randomCode,
                        'sks' => '3',
                        'curriculum_semester' => 5, // Default for non-curriculum / concentration course
                        'is_required' => false,
                    ]);
                }

                $sectionKey = $course->id.'|'.$sectionLabel;
                if (! isset($sectionsMap[$sectionKey])) {
                    $section = ClassSection::create([
                        'period_id' => $period->id,
                        'matkul_id' => $course->id,
                        'label' => $sectionLabel,
                        'enrollment' => $enrollment,
                    ]);
                    $sectionsMap[$sectionKey] = $section;

                    // Initialize LabSessions if course config exists
                    $config = $configsByCourse->get($course->id);
                    if ($config) {
                        // Create practicum session
                        if ($config->practicum_hours > 0) {
                            $isManual = $config->is_manual;
                            LabSession::create([
                                'schedule_id' => $schedule->id,
                                'class_section_id' => $section->id,
                                'session_type' => 'PRAKTIKUM',
                                'duration_min' => (int) ($config->practicum_hours * 60),
                                'status' => 'UNSCHEDULED',
                                'unscheduled_reason' => $isManual ? 'Dijadwalkan manual oleh admin' : null,
                                'is_locked' => false,
                                'is_manual_placed' => false,
                            ]);
                            $createdSessionsCount++;
                        }

                        // Create responsi session
                        if ($config->responsi_hours > 0) {
                            $isManual = $config->is_manual;
                            LabSession::create([
                                'schedule_id' => $schedule->id,
                                'class_section_id' => $section->id,
                                'session_type' => 'RESPONSI',
                                'duration_min' => (int) ($config->responsi_hours * 60),
                                'status' => 'UNSCHEDULED',
                                'unscheduled_reason' => $isManual ? 'Dijadwalkan manual oleh admin' : null,
                                'is_locked' => false,
                                'is_manual_placed' => false,
                            ]);
                            $createdSessionsCount++;
                        }
                    }
                } else {
                    $section = $sectionsMap[$sectionKey];
                }

                // Resolve room
                $room = null;
                if ($roomCode && isset($roomsByCode[$roomCode])) {
                    $room = $roomsByCode[$roomCode];
                }

                // Create meeting
                ClassMeeting::create([
                    'class_section_id' => $section->id,
                    'day_of_week' => self::DAY_INDEX[$day] ?? 1,
                    'start_min' => $startMin,
                    'end_min' => $endMin,
                    'lab_id' => $room?->id,
                    'room_label' => $roomLabel,
                    'raw_text' => "$courseName $sectionLabel",
                ]);
                $createdMeetingsCount++;
            }

            return [
                'period' => $period,
                'sections_count' => count($sectionsMap),
                'meetings_count' => $createdMeetingsCount,
                'sessions_count' => $createdSessionsCount,
                'warnings' => $warnings,
            ];
        });
    }

    /**
     * Synchronize lab sessions for a section based on its course config.
     */
    public function syncLabSessionsForSection(ClassSection $section): void
    {
        $config = CourseConfig::where('period_id', $section->period_id)
            ->where('matkul_id', $section->matkul_id)
            ->first();

        if (! $config) {
            return;
        }

        $schedule = Schedule::firstOrCreate(
            ['period_id' => $section->period_id, 'is_active' => true],
            ['name' => 'Jadwal Utama']
        );

        // Handle practicum
        $prakSession = LabSession::where('class_section_id', $section->id)
            ->where('schedule_id', $schedule->id)
            ->where('session_type', 'PRAKTIKUM')
            ->first();

        if ($config->practicum_hours > 0) {
            $duration = (int) ($config->practicum_hours * 60);
            if (! $prakSession) {
                LabSession::create([
                    'schedule_id' => $schedule->id,
                    'class_section_id' => $section->id,
                    'session_type' => 'PRAKTIKUM',
                    'duration_min' => $duration,
                    'status' => 'UNSCHEDULED',
                    'unscheduled_reason' => $config->is_manual ? 'Dijadwalkan manual oleh admin' : null,
                ]);
            } else {
                $prakSession->update(['duration_min' => $duration]);
            }
        } elseif ($prakSession) {
            $prakSession->delete();
        }

        // Handle responsi
        $respSession = LabSession::where('class_section_id', $section->id)
            ->where('schedule_id', $schedule->id)
            ->where('session_type', 'RESPONSI')
            ->first();

        if ($config->responsi_hours > 0) {
            $duration = (int) ($config->responsi_hours * 60);
            if (! $respSession) {
                LabSession::create([
                    'schedule_id' => $schedule->id,
                    'class_section_id' => $section->id,
                    'session_type' => 'RESPONSI',
                    'duration_min' => $duration,
                    'status' => 'UNSCHEDULED',
                    'unscheduled_reason' => $config->is_manual ? 'Dijadwalkan manual oleh admin' : null,
                ]);
            } else {
                $respSession->update(['duration_min' => $duration]);
            }
        } elseif ($respSession) {
            $respSession->delete();
        }
    }
}
