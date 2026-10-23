<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\Labs as Room;
use App\Models\Matkul as Course;
use App\Models\Period;
use App\Services\Excel\ScheduleExcelParser;
use App\Services\Scheduling\ClassImportService;
use App\Services\Scheduling\SlotCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends Controller
{
    public function __construct(
        protected ScheduleExcelParser $parser,
        protected ClassImportService $importService,
        protected SlotCalendar $slotCalendar
    ) {}

    protected function getActivePeriod(): Period
    {
        $period = Period::where('active', true)->first();
        if (! $period) {
            $period = Period::first();
        }
        if (! $period) {
            $period = Period::create([
                'academic_year' => '2024/2025',
                'semester' => 'GASAL',
                'active' => true,
            ]);
        }

        return $period;
    }

    public function index(Request $request): View
    {
        $period = $this->getActivePeriod();

        $query = ClassSection::where('period_id', $period->id)
            ->with(['course', 'classMeetings.room']);

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($cq) use ($search) {
                      $cq->where('nama', 'like', "%{$search}%")
                         ->orWhere('kode', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('semester')) {
            $sem = (int) $request->query('semester');
            $query->whereHas('course', fn ($q) => $q->where('curriculum_semester', $sem));
        }

        if ($request->filled('day')) {
            $day = $request->query('day');
            $dayIdx = ClassImportService::DAY_INDEX[$day] ?? 1;
            $query->whereHas('classMeetings', fn ($q) => $q->where('day_of_week', $dayIdx));
        }

        $sections = $query->orderBy('matkul_id')->orderBy('label')->paginate(20)->withQueryString();

        $courses = Course::orderBy('nama')->get();
        $rooms = Room::where('is_active', true)->get();
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('import.index', [
            'period' => $period,
            'sections' => $sections,
            'courses' => $courses,
            'rooms' => $rooms,
            'days' => $days,
            'search' => $request->query('search', ''),
            'semester' => $request->query('semester', ''),
            'selectedDay' => $request->query('day', ''),
        ]);
    }

    /**
     * Preview uploaded Excel file without saving to database.
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $tempPath = $file->getRealPath();

        try {
            $parsed = $this->parser->parse($tempPath);

            // Store parsed in session for confirmed commit
            session(['pending_import_meetings' => $parsed['meetings']]);

            return response()->json([
                'success' => true,
                'total_meetings' => count($parsed['meetings']),
                'warnings' => $parsed['warnings'],
                'preview_meetings' => array_slice($parsed['meetings'], 0, 50),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel: '.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Confirm and commit pending imported meetings into database.
     */
    public function commit(Request $request): JsonResponse
    {
        $meetingsData = session('pending_import_meetings');
        if (empty($meetingsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data pratinjau impor yang tertunda. Silakan unggah kembali.',
            ], 400);
        }

        $period = $this->getActivePeriod();
        $overwrite = $request->boolean('overwrite', true);

        $result = $this->importService->importParsedMeetings($meetingsData, $period, $overwrite);
        session()->forget('pending_import_meetings');

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$result['sections_count']} seksi dan {$result['meetings_count']} pertemuan kelas ke periode {$period->name}.",
            'details' => $result,
        ]);
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate(Request $request): BinaryFileResponse|StreamedResponse
    {
        $templatePath = public_path('assets/file/template_file/Template_Jadwal_Kelas.xlsx');
        if (file_exists($templatePath)) {
            return response()->download($templatePath, 'Template_Jadwal_Kelas.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Kelas');

        // Flat format template headers
        $headers = ['Mata Kuliah', 'Seksi', 'Semester', 'Hari', 'Jam Mulai (HH:MM)', 'Jam Selesai (HH:MM)', 'Kode Ruangan (Opsional)', 'Jumlah Mahasiswa'];
        $sheet->fromArray($headers, null, 'A1');

        $sampleData = [
            ['Algoritma dan Pemrograman', 'A', 1, 'Senin', '07:30', '09:30', 'SI', 30],
            ['Algoritma dan Pemrograman', 'B', 1, 'Selasa', '09:30', '11:30', '', 30],
            ['Struktur Data', 'A', 3, 'Rabu', '13:00', '15:00', 'PG', 35],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Jadwal_Kelas.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Manual create class meeting.
     */
    public function storeMeeting(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|string|exists:matkuls,id',
            'label' => 'required|string|max:10',
            'enrollment' => 'required|integer|min:1|max:100',
            'day' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room_id' => 'nullable|string|exists:labs,id',
        ]);

        $period = $this->getActivePeriod();
        $startMin = $this->timeStringToMinutes($validated['start_time']);
        $endMin = $this->timeStringToMinutes($validated['end_time']);

        if ($startMin === null || $endMin === null || $startMin >= $endMin) {
            return back()->with('error', 'Format waktu tidak valid (mulai harus sebelum selesai).');
        }

        $section = ClassSection::firstOrCreate(
            [
                'period_id' => $period->id,
                'matkul_id' => $validated['course_id'],
                'label' => $validated['label'],
            ],
            ['enrollment' => $validated['enrollment']]
        );

        $room = ! empty($validated['room_id']) ? Room::find($validated['room_id']) : null;

        ClassMeeting::create([
            'class_section_id' => $section->id,
            'day_of_week' => ClassImportService::DAY_INDEX[$validated['day']] ?? 1,
            'start_min' => $startMin,
            'end_min' => $endMin,
            'lab_id' => $room?->id,
            'room_label' => $room ? $room->name : 'Ruang Kelas',
        ]);

        $this->importService->syncLabSessionsForSection($section);

        return back()->with('success', 'Jadwal pertemuan kelas berhasil ditambahkan.');
    }

    /**
     * Update an existing class section and its primary meeting.
     */
    public function updateSection(Request $request, ClassSection $section): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|string|exists:matkuls,id',
            'label' => 'required|string|max:10',
            'enrollment' => 'required|integer|min:1|max:100',
            'day' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'room_id' => 'nullable|string|exists:labs,id',
        ]);

        $startMin = $this->timeStringToMinutes($validated['start_time']);
        $endMin = $this->timeStringToMinutes($validated['end_time']);

        if ($startMin === null || $endMin === null || $startMin >= $endMin) {
            return back()->with('error', 'Format waktu tidak valid (mulai harus sebelum selesai).');
        }

        $section->update([
            'matkul_id' => $validated['course_id'],
            'label' => $validated['label'],
            'enrollment' => $validated['enrollment'],
        ]);

        $room = ! empty($validated['room_id']) ? Room::find($validated['room_id']) : null;

        $meeting = $section->classMeetings()->first();
        if ($meeting) {
            $meeting->update([
                'day_of_week' => ClassImportService::DAY_INDEX[$validated['day']] ?? 1,
                'start_min' => $startMin,
                'end_min' => $endMin,
                'lab_id' => $room?->id,
                'room_label' => $room ? $room->name : 'Ruang Kelas',
            ]);
        } else {
            ClassMeeting::create([
                'class_section_id' => $section->id,
                'day_of_week' => ClassImportService::DAY_INDEX[$validated['day']] ?? 1,
                'start_min' => $startMin,
                'end_min' => $endMin,
                'lab_id' => $room?->id,
                'room_label' => $room ? $room->name : 'Ruang Kelas',
            ]);
        }

        $this->importService->syncLabSessionsForSection($section);

        return back()->with('success', "Seksi {$section->course->name} (Kelas {$section->label}) berhasil diperbarui.");
    }

    /**
     * Delete a class section.
     */
    public function destroySection(ClassSection $section): RedirectResponse
    {
        $section->delete();

        return back()->with('success', 'Seksi kelas berhasil dihapus.');
    }

    protected function timeStringToMinutes(string $timeStr): ?int
    {
        $cleaned = trim(str_replace('.', ':', $timeStr));
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $cleaned, $m)) {
            $h = (int) $m[1];
            $min = (int) $m[2];
            if ($h >= 0 && $h <= 23 && $min >= 0 && $min <= 59) {
                return $h * 60 + $min;
            }
        }
        return null;
    }
}
