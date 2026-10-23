<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\LabSession;
use App\Models\Labs as Room;
use App\Models\Period;
use App\Models\Schedule as ScheduleVersion;
use App\Services\Scheduling\PathCalculator;
use App\Services\Scheduling\SlotCalendar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicScheduleController extends Controller
{
    public function __construct(
        protected SlotCalendar $slotCalendar,
        protected PathCalculator $pathCalculator
    ) {}

    /**
     * Default public view (active period or requested period).
     */
    public function index(Request $request): View
    {
        $allPeriods = Period::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();

        if ($request->filled('period_id')) {
            $period = Period::find($request->query('period_id')) ?? Period::where('active', true)->first();
        } else {
            $period = Period::where('active', true)->first();
        }

        if (! $period) {
            $period = Period::first();
        }

        // Find published draft version if any
        $activeVersion = ScheduleVersion::where('period_id', $period->id)
            ->whereNotNull('snapshot_json')
            ->whereJsonContains('snapshot_json->is_published', true)
            ->orderBy('created_at', 'desc')
            ->first();

        $rooms = Room::where('is_active', true)
            ->get()
            ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
            ->values();

        $days = $this->slotCalendar->getDays();
        $slotsByDay = [];
        foreach ($days as $day) {
            $slotsByDay[$day] = $this->slotCalendar->getDaySlots($day);
        }

        if ($activeVersion && ! empty($activeVersion->snapshot_json['sessions'])) {
            $placedSessions = $this->buildSessionsFromSnapshot($activeVersion, $period, $rooms);
        } else {
            // Live sessions in active schedule
            $liveSchedule = ScheduleVersion::where('period_id', $period->id)->where('is_active', true)->first();
            $placedSessions = $liveSchedule
                ? LabSession::where('schedule_id', $liveSchedule->id)
                    ->where('status', 'SCHEDULED')
                    ->with(['room', 'section.course', 'section.classMeetings'])
                    ->get()
                : collect([]);
        }

        $gridClassMeetings = ClassMeeting::whereHas('section', fn ($q) => $q->where('period_id', $period->id))
            ->with(['room', 'section.course'])
            ->get();

        $currentSlug = $activeVersion?->snapshot_json['public_slug'] ?? null;
        $pathUrl = $currentSlug
            ? route('public.paths.slug', ['slug' => $currentSlug])
            : route('public.paths');

        return view('public.index', [
            'period' => $period,
            'allPeriods' => $allPeriods,
            'rooms' => $rooms,
            'days' => $days,
            'slotsByDay' => $slotsByDay,
            'placedSessions' => $placedSessions,
            'classMeetings' => $gridClassMeetings,
            'currentVersion' => $activeVersion,
            'currentSlug' => $currentSlug,
            'pathUrl' => $pathUrl,
        ]);
    }

    /**
     * Show schedule by custom public slug (e.g. /gasal20262027).
     */
    public function showBySlug(string $slug): View
    {
        $version = ScheduleVersion::with('period')
            ->whereJsonContains('snapshot_json->public_slug', $slug)
            ->first();

        if (! $version) {
            abort(404, 'Jadwal dengan link tersebut tidak ditemukan.');
        }

        $isPublished = $version->snapshot_json['is_published'] ?? false;
        if (! $isPublished) {
            return view('public.inactive', [
                'version' => $version,
                'period' => $version->period,
            ]);
        }

        $period = $version->period ?? Period::where('active', true)->first();
        $allPeriods = Period::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();

        $rooms = Room::where('is_active', true)
            ->get()
            ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
            ->values();

        $days = $this->slotCalendar->getDays();
        $slotsByDay = [];
        foreach ($days as $day) {
            $slotsByDay[$day] = $this->slotCalendar->getDaySlots($day);
        }

        $placedSessions = $this->buildSessionsFromSnapshot($version, $period, $rooms);

        $gridClassMeetings = ClassMeeting::whereHas('section', fn ($q) => $q->where('period_id', $period->id))
            ->with(['room', 'section.course'])
            ->get();

        $pathUrl = route('public.paths.slug', ['slug' => $slug]);

        return view('public.index', [
            'period' => $period,
            'allPeriods' => $allPeriods,
            'rooms' => $rooms,
            'days' => $days,
            'slotsByDay' => $slotsByDay,
            'placedSessions' => $placedSessions,
            'classMeetings' => $gridClassMeetings,
            'currentVersion' => $version,
            'currentSlug' => $slug,
            'pathUrl' => $pathUrl,
        ]);
    }

    /**
     * View Paths for students by custom slug (e.g. /{slug}/path).
     */
    public function showPathsBySlug(Request $request, string $slug): View
    {
        $version = ScheduleVersion::with('period')
            ->whereJsonContains('snapshot_json->public_slug', $slug)
            ->first();

        if (! $version) {
            abort(404, 'Jadwal dengan link tersebut tidak ditemukan.');
        }

        $isPublished = $version->snapshot_json['is_published'] ?? false;
        if (! $isPublished) {
            return view('public.inactive', [
                'version' => $version,
                'period' => $version->period,
            ]);
        }

        $period = $version->period;
        $semester = (int) $request->query('semester', 1);
        if ($semester < 1 || $semester > 8) {
            $semester = 1;
        }

        $pathResult = $this->pathCalculator->calculatePathsForSemester($period, $semester);

        return view('public.paths', [
            'period' => $period,
            'version' => $version,
            'currentSlug' => $slug,
            'selectedSemester' => $semester,
            'pathResult' => $pathResult,
            'backUrl' => url('/' . $slug),
        ]);
    }

    /**
     * View Paths default (/public/path).
     */
    public function showPaths(Request $request): View
    {
        $period = Period::where('active', true)->first();
        if (! $period) {
            $period = Period::first();
        }

        $activeVersion = ScheduleVersion::where('period_id', $period->id)
            ->whereJsonContains('snapshot_json->is_published', true)
            ->first();

        $semester = (int) $request->query('semester', 1);
        if ($semester < 1 || $semester > 8) {
            $semester = 1;
        }

        $pathResult = $this->pathCalculator->calculatePathsForSemester($period, $semester);
        $slug = $activeVersion?->snapshot_json['public_slug'] ?? null;
        $backUrl = $slug ? url('/' . $slug) : route('public.schedule');

        return view('public.paths', [
            'period' => $period,
            'version' => $activeVersion,
            'currentSlug' => $slug,
            'selectedSemester' => $semester,
            'pathResult' => $pathResult,
            'backUrl' => $backUrl,
        ]);
    }

    /**
     * Reconstitute and enrich session models from snapshot JSON.
     */
    protected function buildSessionsFromSnapshot(ScheduleVersion $version, Period $period, $rooms): \Illuminate\Support\Collection
    {
        $snapshot = $version->snapshot_json ?? [];
        $rawSessions = $snapshot['sessions'] ?? [];

        $sections = ClassSection::where('period_id', $period->id)
            ->with(['course', 'classMeetings'])
            ->get()
            ->keyBy('id');

        $roomsById = $rooms->keyBy('id');

        return collect($rawSessions)
            ->filter(fn ($s) => in_array(strtolower($s['status'] ?? ''), ['placed', 'scheduled']))
            ->map(function ($s) use ($sections, $roomsById) {
                $sec = $sections->get($s['class_section_id']);
                $roomId = $s['lab_id'] ?? $s['room_id'] ?? null;
                $room = $roomsById->get($roomId);

                return (object) [
                    'id' => $s['id'],
                    'type' => $s['type'] ?? strtolower($s['session_type'] ?? 'praktikum'),
                    'day' => $s['day'] ?? (isset($s['day_of_week']) ? [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',0=>'Minggu'][$s['day_of_week']] ?? 'Senin' : 'Senin'),
                    'start_min' => (int) $s['start_min'],
                    'end_min' => (int) $s['end_min'],
                    'room_id' => $roomId,
                    'lab_id' => $roomId,
                    'room' => $room,
                    'section' => $sec,
                    'is_locked' => $s['is_locked'] ?? false,
                    'is_manual_placed' => $s['is_manual_placed'] ?? false,
                ];
            })
            ->values();
    }
}
