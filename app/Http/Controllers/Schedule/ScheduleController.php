<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\CustomPath;
use App\Models\LabSession;
use App\Models\Labs as Room;
use App\Models\Period;
use App\Models\Schedule;
use App\Services\Scheduling\ConstraintChecker;
use App\Services\Scheduling\PathCalculator;
use App\Services\Scheduling\ScheduleGenerator;
use App\Services\Scheduling\ScheduleValidator;
use App\Services\Scheduling\SlotCalendar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleGenerator $generator,
        protected ConstraintChecker $constraintChecker,
        protected SlotCalendar $slotCalendar,
        protected PathCalculator $pathCalculator,
        protected ScheduleValidator $validator
    ) {}

    /**
     * Get or set the active period.
     */
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

    /**
     * Get or create active schedule for period.
     */
    protected function getActiveSchedule(Period $period): Schedule
    {
        return Schedule::firstOrCreate(
            ['period_id' => $period->id, 'is_active' => true],
            ['name' => 'Jadwal Utama']
        );
    }

    /**
     * Main Schedule Grid View.
     */
    public function index(Request $request): View
    {
        $period = $this->getActivePeriod();
        $periods = Period::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();
        $schedule = $this->getActiveSchedule($period);

        // Saved Draft Versions for current period
        $savedVersions = Schedule::where('period_id', $period->id)
            ->whereNotNull('snapshot_json')
            ->orderBy('created_at', 'desc')
            ->get();

        $versionId = $request->query('version_id');
        $selectedVersion = null;
        if ($versionId) {
            $selectedVersion = Schedule::where('id', $versionId)->where('period_id', $period->id)->first();
        }

        $isDraftMode = (bool) $selectedVersion;
        $isEditMode = $isDraftMode && ($request->query('mode') === 'edit');

        // Rooms: internal first, then external
        $rooms = Room::where('is_active', true)
            ->get()
            ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
            ->values();

        $days = $this->slotCalendar->getDays();
        $slotsByDay = [];
        foreach ($days as $day) {
            $slotsByDay[$day] = $this->slotCalendar->getDaySlots($day);
        }

        if ($selectedVersion) {
            [$placedSessions, $unscheduledSessions] = $this->buildSessionsFromSnapshot($selectedVersion, $period, $rooms);
        } else {
            // Placed lab sessions in live active schedule
            $placedSessions = LabSession::where('schedule_id', $schedule->id)
                ->where(function ($q) {
                    $q->where('status', 'SCHEDULED')
                      ->orWhere('status', 'placed');
                })
                ->with(['room', 'section.course', 'section.classMeetings'])
                ->get();

            // Unscheduled lab sessions in live active schedule
            $unscheduledSessions = LabSession::where('schedule_id', $schedule->id)
                ->where(function ($q) {
                    $q->where('status', 'UNSCHEDULED')
                      ->orWhere('status', 'unscheduled');
                })
                ->with(['section.course', 'section.classMeetings'])
                ->get();
        }

        // Class meetings that occupy rooms (both lab and non-lab rooms)
        $classMeetings = ClassMeeting::whereHas('section', fn ($q) => $q->where('period_id', $period->id))
            ->with(['room', 'section.course'])
            ->get();

        // Custom paths
        $customPaths = CustomPath::where('period_id', $period->id)->get();

        return view('schedule.index', [
            'period' => $period,
            'periods' => $periods,
            'rooms' => $rooms,
            'days' => $days,
            'slotsByDay' => $slotsByDay,
            'placedSessions' => $placedSessions,
            'classMeetings' => $classMeetings,
            'unscheduledSessions' => $unscheduledSessions,
            'customPaths' => $customPaths,
            'savedVersions' => $savedVersions,
            'selectedVersion' => $selectedVersion,
            'isDraftMode' => $isDraftMode,
            'isEditMode' => $isEditMode,
        ]);
    }

    /**
     * Reconstitute placed and unscheduled sessions from draft snapshot.
     */
    protected function buildSessionsFromSnapshot(Schedule $version, Period $period, $rooms): array
    {
        $snapshot = $version->snapshot_json ?? [];
        $rawSessions = $snapshot['sessions'] ?? [];

        $sections = ClassSection::where('period_id', $period->id)
            ->with(['course', 'classMeetings.room'])
            ->get()
            ->keyBy('id');

        $roomsById = $rooms->keyBy('id');

        $placed = [];
        $unscheduled = [];

        foreach ($rawSessions as $s) {
            $sec = $sections->get($s['class_section_id']);
            $roomId = $s['lab_id'] ?? $s['room_id'] ?? null;
            $room = $roomsById->get($roomId);
            $dayName = $s['day'] ?? (isset($s['day_of_week']) ? [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',0=>'Minggu'][$s['day_of_week']] ?? null : null);
            $dayOfWeek = $s['day_of_week'] ?? (isset($s['day']) ? ConstraintChecker::DAY_INDEX[$s['day']] ?? null : null);

            $sessionObj = (object) [
                'id' => $s['id'],
                'schedule_id' => $version->id,
                'class_section_id' => $s['class_section_id'],
                'session_type' => $s['session_type'] ?? ($s['type'] ?? 'PRAKTIKUM'),
                'type' => $s['type'] ?? strtolower($s['session_type'] ?? 'praktikum'),
                'duration_min' => (int) ($s['duration_min'] ?? 120),
                'day_of_week' => $dayOfWeek,
                'day' => $dayName,
                'start_min' => isset($s['start_min']) ? (int) $s['start_min'] : null,
                'end_min' => isset($s['end_min']) ? (int) $s['end_min'] : null,
                'room_id' => $roomId,
                'lab_id' => $roomId,
                'room' => $room,
                'section' => $sec,
                'status' => in_array(strtolower($s['status'] ?? ''), ['placed', 'scheduled']) ? 'SCHEDULED' : 'UNSCHEDULED',
                'unscheduled_reason' => $s['unscheduled_reason'] ?? null,
                'is_locked' => (bool) ($s['is_locked'] ?? false),
                'is_manual_placed' => (bool) ($s['is_manual_placed'] ?? false),
            ];

            if ($sessionObj->status === 'SCHEDULED' && $sessionObj->room_id && $sessionObj->start_min !== null && $sessionObj->day) {
                $placed[] = $sessionObj;
            } else {
                $sessionObj->status = 'UNSCHEDULED';
                $unscheduled[] = $sessionObj;
            }
        }

        return [collect($placed), collect($unscheduled)];
    }

    /**
     * Automated Schedule Generation.
     */
    public function generate(Request $request): JsonResponse
    {
        $period = $this->getActivePeriod();
        $schedule = $this->getActiveSchedule($period);
        $summary = $this->generator->generate($period, $schedule);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal otomatis berhasil digenerate.',
            'summary' => $summary,
        ]);
    }

    /**
     * Reset unlocked sessions.
     */
    public function reset(Request $request): JsonResponse
    {
        $period = $this->getActivePeriod();
        $schedule = $this->getActiveSchedule($period);
        $count = $this->generator->reset($period, $schedule);

        return response()->json([
            'success' => true,
            'message' => "{$count} sesi jadwal berhasil di-reset.",
        ]);
    }

    /**
     * Handle Drag-and-drop Placement from UI.
     */
    public function updatePlacement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'room_id' => 'required|string|exists:labs,id',
            'day' => 'required|string',
            'start_min' => 'required|integer',
            'end_min' => 'required|integer',
            'override' => 'nullable|boolean',
            'version_id' => 'nullable|string|exists:schedules,id',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $day = $validated['day'];
        $startMin = (int) $validated['start_min'];
        $endMin = (int) $validated['end_min'];
        $override = ! empty($validated['override']);

        if ($request->filled('version_id')) {
            $version = Schedule::findOrFail($validated['version_id']);
            $snapshot = $version->snapshot_json ?? [];
            $sessions = $snapshot['sessions'] ?? [];

            $foundIndex = null;
            foreach ($sessions as $idx => $s) {
                if ($s['id'] == $validated['session_id']) {
                    $foundIndex = $idx;
                    break;
                }
            }

            if ($foundIndex !== null) {
                $sessions[$foundIndex]['status'] = 'SCHEDULED';
                $sessions[$foundIndex]['lab_id'] = $room->id;
                $sessions[$foundIndex]['room_id'] = $room->id;
                $sessions[$foundIndex]['day'] = $day;
                $sessions[$foundIndex]['day_of_week'] = ConstraintChecker::DAY_INDEX[$day] ?? 1;
                $sessions[$foundIndex]['start_min'] = $startMin;
                $sessions[$foundIndex]['end_min'] = $endMin;
                $sessions[$foundIndex]['unscheduled_reason'] = null;
                $sessions[$foundIndex]['is_manual_placed'] = true;
            }

            $snapshot['sessions'] = $sessions;
            $snapshot['placed_count'] = count(array_filter($sessions, fn ($s) => in_array(strtolower($s['status'] ?? ''), ['placed', 'scheduled'])));
            $version->snapshot_json = $snapshot;
            $version->save();

            return response()->json([
                'success' => true,
                'message' => 'Sesi draft berhasil ditempatkan.',
                'has_override_warning' => false,
                'violations' => [],
            ]);
        }

        $session = LabSession::with(['section.course', 'section.classMeetings'])->findOrFail($validated['session_id']);

        // Server-side validation
        $check = $this->constraintChecker->validatePlacement(
            $session,
            $room,
            $day,
            $startMin,
            $endMin,
            [
                'allow_manual_hours' => true, // Manual drag allows sore/malam slots
                'allow_manual_rooms' => true, // Manual drag allows manual-only rooms
            ]
        );

        if (! $check['valid'] && ! $override) {
            return response()->json([
                'success' => false,
                'violations' => $check['violations'],
                'can_override' => true,
                'message' => 'Penempatan melanggar aturan penjadwalan.',
            ], 422);
        }

        $session->update([
            'status' => 'SCHEDULED',
            'lab_id' => $room->id,
            'day_of_week' => ConstraintChecker::DAY_INDEX[$day] ?? 1,
            'start_min' => $startMin,
            'end_min' => $endMin,
            'unscheduled_reason' => null,
            'is_manual_placed' => true,
            'is_overridden' => ! $check['valid'],
            'violations' => ! $check['valid'] ? $check['violations'] : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi berhasil ditempatkan.',
            'session' => $session->fresh(['room', 'section.course']),
            'has_override_warning' => ! $check['valid'],
            'violations' => $check['violations'],
        ]);
    }

    /**
     * Drag back to unscheduled panel.
     */
    public function unplace(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'version_id' => 'nullable|string|exists:schedules,id',
        ]);

        if ($request->filled('version_id')) {
            $version = Schedule::findOrFail($validated['version_id']);
            $snapshot = $version->snapshot_json ?? [];
            $sessions = $snapshot['sessions'] ?? [];

            foreach ($sessions as $idx => $s) {
                if ($s['id'] == $validated['session_id']) {
                    $sessions[$idx]['status'] = 'UNSCHEDULED';
                    $sessions[$idx]['lab_id'] = null;
                    $sessions[$idx]['room_id'] = null;
                    $sessions[$idx]['day'] = null;
                    $sessions[$idx]['day_of_week'] = null;
                    $sessions[$idx]['start_min'] = null;
                    $sessions[$idx]['end_min'] = null;
                    $sessions[$idx]['unscheduled_reason'] = 'Dibatalkan ke panel belum terjadwal';
                    $sessions[$idx]['is_manual_placed'] = false;
                    break;
                }
            }

            $snapshot['sessions'] = $sessions;
            $snapshot['placed_count'] = count(array_filter($sessions, fn ($s) => in_array(strtolower($s['status'] ?? ''), ['placed', 'scheduled'])));
            $version->snapshot_json = $snapshot;
            $version->save();

            return response()->json([
                'success' => true,
                'message' => 'Sesi draft dikembalikan ke daftar belum terjadwal.',
            ]);
        }

        $session = LabSession::findOrFail($validated['session_id']);
        $session->update([
            'status' => 'UNSCHEDULED',
            'lab_id' => null,
            'day_of_week' => null,
            'start_min' => null,
            'end_min' => null,
            'unscheduled_reason' => 'Dibatalkan ke panel belum terjadwal',
            'is_manual_placed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi dikembalikan ke daftar belum terjadwal.',
            'session' => $session->fresh(['section.course']),
        ]);
    }

    /**
     * Toggle lock state of a session.
     */
    public function toggleLock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'version_id' => 'nullable|string|exists:schedules,id',
        ]);

        if ($request->filled('version_id')) {
            $version = Schedule::findOrFail($validated['version_id']);
            $snapshot = $version->snapshot_json ?? [];
            $sessions = $snapshot['sessions'] ?? [];
            $isLocked = false;

            foreach ($sessions as $idx => $s) {
                if ($s['id'] == $validated['session_id']) {
                    $sessions[$idx]['is_locked'] = !($s['is_locked'] ?? false);
                    $isLocked = $sessions[$idx]['is_locked'];
                    break;
                }
            }

            $snapshot['sessions'] = $sessions;
            $version->snapshot_json = $snapshot;
            $version->save();

            return response()->json([
                'success' => true,
                'is_locked' => $isLocked,
                'message' => $isLocked ? 'Sesi dikunci.' : 'Kunci sesi dibuka.',
            ]);
        }

        $session = LabSession::findOrFail($validated['session_id']);
        $session->update(['is_locked' => ! $session->is_locked]);

        return response()->json([
            'success' => true,
            'is_locked' => $session->is_locked,
            'message' => $session->is_locked ? 'Sesi dikunci.' : 'Kunci sesi dibuka.',
        ]);
    }

    /**
     * Validate a candidate slot on the fly (for drag preview).
     */
    public function validatePlacement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => 'required|string|exists:lab_sessions,id',
            'room_id' => 'required|string|exists:labs,id',
            'day' => 'required|string',
            'start_min' => 'required|integer',
            'end_min' => 'required|integer',
        ]);

        $session = LabSession::with(['section.course', 'section.classMeetings'])->findOrFail($validated['session_id']);
        $room = Room::findOrFail($validated['room_id']);

        $check = $this->constraintChecker->validatePlacement(
            $session,
            $room,
            $validated['day'],
            (int) $validated['start_min'],
            (int) $validated['end_min'],
            [
                'allow_manual_hours' => true,
                'allow_manual_rooms' => true,
            ]
        );

        return response()->json($check);
    }

    /**
     * Calculate and return valid paths for a semester.
     */
    public function getPaths(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'semester' => 'required|integer|in:1,2,3,4,5,6,7,8',
        ]);

        $period = $this->getActivePeriod();
        $result = $this->pathCalculator->calculatePathsForSemester($period, (int) $validated['semester']);

        return response()->json($result);
    }

    /**
     * Evaluate custom path combination.
     */
    public function evaluateCustomPath(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'semester' => 'required|integer',
            'section_ids' => 'required|array',
            'section_ids.*' => 'string|exists:class_sections,id',
        ]);

        $period = $this->getActivePeriod();
        $result = $this->pathCalculator->evaluateCustomPath(
            $period,
            (int) $validated['semester'],
            $validated['section_ids']
        );

        return response()->json($result);
    }

    /**
     * Save a custom path.
     */
    public function storeCustomPath(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'semester' => 'required|integer',
            'name' => 'required|string|max:100',
            'section_ids' => 'required|array',
            'section_ids.*' => 'string|exists:class_sections,id',
        ]);

        $period = $this->getActivePeriod();
        $path = CustomPath::create([
            'period_id' => $period->id,
            'semester' => $validated['semester'],
            'name' => $validated['name'],
            'section_ids' => $validated['section_ids'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Path kustom berhasil disimpan.',
            'path' => $path,
        ]);
    }

    /**
     * Printable view of the schedule.
     */
    public function printView(Request $request): View
    {
        $period = $this->getActivePeriod();
        $schedule = $this->getActiveSchedule($period);
        $rooms = Room::where('is_active', true)
            ->get()
            ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
            ->values();
        $days = $this->slotCalendar->getDays();

        $slotsByDay = [];
        foreach ($days as $day) {
            $slotsByDay[$day] = $this->slotCalendar->getDaySlots($day);
        }

        $placedSessions = LabSession::where('schedule_id', $schedule->id)
            ->where('status', 'SCHEDULED')
            ->with(['room', 'section.course'])
            ->get();

        $classMeetings = ClassMeeting::whereHas('section', fn ($q) => $q->where('period_id', $period->id))
            ->with(['room', 'section.course'])
            ->get();

        return view('schedule.print', [
            'period' => $period,
            'rooms' => $rooms,
            'days' => $days,
            'slotsByDay' => $slotsByDay,
            'placedSessions' => $placedSessions,
            'classMeetings' => $classMeetings,
        ]);
    }
}
