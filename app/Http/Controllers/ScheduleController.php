<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\LabSession;
use App\Models\MatkulConfig;
use App\Models\Period;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $periods = Period::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();
        $activePeriod = Period::getCurrentPeriod();
        $selectedPeriod = ($request->filled('period_id') ? $periods->firstWhere('id', $request->query('period_id')) : null)
            ?? $activePeriod
            ?? $periods->first();

        $schedules = Schedule::with(['period', 'pic'])
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.schedules', compact('periods', 'activePeriod', 'selectedPeriod', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_id' => 'required|exists:periods,id',
            'name'      => 'required|string|max:100',
            'notes'     => 'nullable|string',
        ]);

        $schedule = DB::transaction(function () use ($validated) {
            $schedule = Schedule::create([
                'period_id' => $validated['period_id'],
                'pic_id'    => Auth::id(),
                'name'      => $validated['name'],
                'is_active' => false,
                'notes'     => $validated['notes'] ?? null,
            ]);

            $configs = MatkulConfig::where('period_id', $validated['period_id'])
                ->where(function ($q) {
                    $q->where('practicum_hours', '>', 0)
                      ->orWhere('responsi_hours', '>', 0);
                })
                ->get()
                ->keyBy('matkul_id');

            if ($configs->isNotEmpty()) {
                $sections = ClassSection::where('period_id', $validated['period_id'])
                    ->whereIn('matkul_id', $configs->keys())
                    ->whereHas('matkul')
                    ->get();

                foreach ($sections as $section) {
                    $cfg = $configs->get($section->matkul_id);
                    if (!$cfg) {
                        continue;
                    }

                    $practicumMin = (int) round((float) $cfg->practicum_hours * 60);
                    if ($practicumMin > 0) {
                        LabSession::firstOrCreate(
                            [
                                'schedule_id'      => $schedule->id,
                                'class_section_id' => $section->id,
                                'session_type'     => 'PRAKTIKUM',
                            ],
                            [
                                'duration_min' => $practicumMin,
                                'status'       => 'UNSCHEDULED',
                            ]
                        );
                    }

                    $responsiMin = (int) round((float) $cfg->responsi_hours * 60);
                    if ($responsiMin > 0) {
                        LabSession::firstOrCreate(
                            [
                                'schedule_id'      => $schedule->id,
                                'class_section_id' => $section->id,
                                'session_type'     => 'RESPONSI',
                            ],
                            [
                                'duration_min' => $responsiMin,
                                'status'       => 'UNSCHEDULED',
                            ]
                        );
                    }
                }
            }

            return $schedule;
        });

        return response()->json([
            'success'  => true,
            'schedule' => $schedule->load(['period', 'pic']),
        ]);
    }

    public function show(Schedule $schedule)
    {
        $sessions = $schedule->labSessions()
            ->with(['classSection.matkul', 'classSection.classMeetings', 'lab'])
            ->get();

        return response()->json([
            'success'  => true,
            'schedule' => $schedule,
            'sessions' => $sessions,
        ]);
    }

    public function setActive(Request $request, Schedule $schedule)
    {
        DB::transaction(function () use ($schedule) {
            Schedule::where('period_id', $schedule->period_id)->update(['is_active' => false]);
            $schedule->update(['is_active' => true]);
        });

        return response()->json([
            'success'  => true,
            'schedule' => $schedule->fresh(['period', 'pic']),
        ]);
    }

    public function duplicate(Schedule $schedule)
    {
        $copy = DB::transaction(function () use ($schedule) {
            $newSchedule = $schedule->replicate(['id']);
            $newSchedule->name = $schedule->name . ' - Salinan';
            $newSchedule->is_active = false;
            $newSchedule->pic_id = Auth::id() ?? $schedule->pic_id;
            $newSchedule->save();

            $sessions = $schedule->labSessions()->get();
            foreach ($sessions as $session) {
                $newSession = $session->replicate(['id']);
                $newSession->schedule_id = $newSchedule->id;
                $newSession->save();
            }

            return $newSchedule;
        });

        return response()->json([
            'success'  => true,
            'schedule' => $copy->load(['period', 'pic']),
        ]);
    }

    public function destroy(Schedule $schedule)
    {
        if ($schedule->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal yang sedang aktif tidak dapat dihapus.',
            ], 422);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function placeSession(Request $request, LabSession $session)
    {
        $validated = $request->validate([
            'lab_id'      => 'required|exists:labs,id',
            'day_of_week' => 'required|integer|between:0,6',
            'start_min'   => 'required|integer|between:0,1439',
            'end_min'     => 'required|integer|gt:start_min',
            'force'       => 'nullable|boolean',
        ]);

        $conflicts = LabSession::with('classSection.matkul')
            ->where('schedule_id', $session->schedule_id)
            ->where('lab_id', $validated['lab_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('id', '!=', $session->id)
            ->where('start_min', '<', $validated['end_min'])
            ->where('end_min', '>', $validated['start_min'])
            ->get();

        $violations = $conflicts->map(fn (LabSession $c) => [
            'id'           => $c->id,
            'label'        => $c->classSection?->label,
            'matkul'       => $c->classSection?->matkul?->nama ?? $c->classSection?->matkul?->kode ?? '-',
            'kode'         => $c->classSection?->matkul?->kode ?? '-',
            'session_type' => $c->session_type,
        ])->values()->all();
        $institutionalBlocks = [
            1 => ['start_min' => 630, 'end_min' => 780, 'title' => 'Kebaktian Umum', 'time' => '10:30-13:00'],
            5 => ['start_min' => 630, 'end_min' => 870, 'title' => 'Kegiatan Mahasiswa', 'time' => '10:30-14:30'],
        ];
        $day = (int) $validated['day_of_week'];
        if (isset($institutionalBlocks[$day])) {
            $block = $institutionalBlocks[$day];
            if ($validated['start_min'] < $block['end_min'] && $validated['end_min'] > $block['start_min']) {
                $violations[] = [
                    'id'           => 'constraint-' . $day,
                    'label'        => $block['time'],
                    'matkul'       => $block['title'],
                    'kode'         => 'JAM_TERLARANG',
                    'session_type' => 'CONSTRAINT',
                ];
            }
        }

        $force = $request->boolean('force');
        if (count($violations) > 0 && !$force) {
            return response()->json([
                'conflict'   => true,
                'violations' => $violations,
            ], 409);
        }

        $session->update([
            'status'           => 'SCHEDULED',
            'lab_id'           => $validated['lab_id'],
            'day_of_week'      => $validated['day_of_week'],
            'start_min'        => $validated['start_min'],
            'end_min'          => $validated['end_min'],
            'is_manual_placed' => true,
            'is_overridden'    => $force && count($violations) > 0,
            'violations'       => ($force && count($violations) > 0) ? $violations : null,
        ]);

        return response()->json([
            'success' => true,
            'session' => $session->fresh(['classSection.matkul', 'lab']),
        ]);
    }

    public function unplaceSession(LabSession $session)
    {
        $session->update([
            'lab_id'           => null,
            'day_of_week'      => null,
            'start_min'        => null,
            'end_min'          => null,
            'status'           => 'UNSCHEDULED',
            'is_manual_placed' => false,
            'is_overridden'    => false,
            'violations'       => null,
            'is_locked'        => false,
        ]);

        return response()->json([
            'success' => true,
            'session' => $session->fresh(['classSection.matkul', 'lab']),
        ]);
    }

    public function lockSession(Request $request, LabSession $session)
    {
        $session->update([
            'is_locked' => $request->boolean('locked'),
        ]);

        return response()->json([
            'success' => true,
            'session' => $session->fresh(['classSection.matkul', 'lab']),
        ]);
    }
}
