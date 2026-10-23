<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ClassMeeting;
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

        // Placed lab sessions
        $placedSessions = LabSession::where('schedule_id', $schedule->id)
            ->where(function ($q) {
                $q->where('status', 'SCHEDULED')
                  ->orWhere('status', 'placed');
            })
            ->with(['room', 'section.course', 'section.classMeetings'])
            ->get();

        // Class meetings that occupy rooms (both lab and non-lab rooms)
        $classMeetings = ClassMeeting::whereHas('section', fn ($q) => $q->where('period_id', $period->id))
            ->with(['room', 'section.course'])
            ->get();

        // Unscheduled lab sessions
        $unscheduledSessions = LabSession::where('schedule_id', $schedule->id)
            ->where(function ($q) {
                $q->where('status', 'UNSCHEDULED')
                  ->orWhere('status', 'unscheduled');
            })
            ->with(['section.course', 'section.classMeetings'])
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
        ]);
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
            'session_id' => 'required|string|exists:lab_sessions,id',
            'room_id' => 'required|string|exists:labs,id',
            'day' => 'required|string',
            'start_min' => 'required|integer',
            'end_min' => 'required|integer',
            'override' => 'nullable|boolean',
        ]);

        $session = LabSession::with(['section.course', 'section.classMeetings'])->findOrFail($validated['session_id']);
        $room = Room::findOrFail($validated['room_id']);
        $day = $validated['day'];
        $startMin = (int) $validated['start_min'];
        $endMin = (int) $validated['end_min'];
        $override = ! empty($validated['override']);

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
            'session_id' => 'required|string|exists:lab_sessions,id',
        ]);

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
            'session_id' => 'required|string|exists:lab_sessions,id',
        ]);

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
