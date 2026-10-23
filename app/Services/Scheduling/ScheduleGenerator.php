<?php

namespace App\Services\Scheduling;

use App\Models\LabSession;
use App\Models\Labs as Room;
use App\Models\MatkulConfig as CourseConfig;
use App\Models\Period;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class ScheduleGenerator
{
    public const DAY_INDEX = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
    ];

    public function __construct(
        protected SlotCalendar $slotCalendar,
        protected ConstraintChecker $constraintChecker,
        protected CostEvaluator $costEvaluator,
        protected PathCalculator $pathCalculator
    ) {}

    /**
     * Run the automated scheduling generator for a given period.
     *
     * @return array{
     *     total_placed: int,
     *     total_unscheduled: int,
     *     external_lab_count: int,
     *     soft_constraint_violations: int,
     *     unscheduled_items: array<int, array{
     *         session_id: string,
     *         course_name: string,
     *         section_label: string,
     *         type: string,
     *         reason: string
     *     }>
     * }
     */
    public function generate(Period $period, ?Schedule $schedule = null): array
    {
        return DB::transaction(function () use ($period, $schedule) {
            $schedule = $schedule ?? Schedule::firstOrCreate(
                ['period_id' => $period->id, 'is_active' => true],
                ['name' => 'Jadwal Utama']
            );

            // 1. Reset unlocked and non-manual-placed sessions to unscheduled
            $sessionsToReset = LabSession::where('schedule_id', $schedule->id)
                ->where('is_locked', false)
                ->where('is_manual_placed', false)
                ->get();

            foreach ($sessionsToReset as $s) {
                $s->update([
                    'day_of_week' => null,
                    'start_min' => null,
                    'end_min' => null,
                    'lab_id' => null,
                    'status' => 'UNSCHEDULED',
                    'unscheduled_reason' => null,
                ]);
            }

            // 2. Fetch all configs for this period
            $configs = CourseConfig::where('period_id', $period->id)
                ->with(['course.classSections' => function ($q) use ($period) {
                    $q->where('period_id', $period->id)
                        ->with(['classMeetings', 'labSessions']);
                }])
                ->get();

            // Sort configs: highest schedule semester first (e.g. 5, then 3, then 1)
            $sortedConfigs = $configs->sortByDesc('schedule_semester')->values();

            // 3. Fetch active lab rooms (ordered: internal first, then external)
            $rooms = Room::where('is_active', true)
                ->where('manual_only', false)
                ->orderBy('capacity', 'asc') // smallest fitting lab preferred
                ->get()
                ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
                ->values();

            $maxRoomCapacity = $rooms->max('capacity') ?? 0;

            $days = $this->slotCalendar->getDays();
            $context = ['period' => $period, 'schedule' => $schedule];

            // 4. Iterate and schedule sessions
            foreach ($sortedConfigs as $config) {
                $course = $config->course;
                if (! $course) continue;

                foreach ($course->classSections as $section) {
                    $meetings = $section->classMeetings;
                    $latestClassMeeting = $this->constraintChecker->getLatestClassMeeting($meetings);

                    // Check if class is on Saturday only
                    $isSaturdayClass = $latestClassMeeting && $latestClassMeeting->day === 'Sabtu';

                    // Process lab sessions for this section
                    $sessions = $section->labSessions
                        ->where('schedule_id', $schedule->id)
                        ->sortBy(function ($s) {
                            return $s->type === 'praktikum' ? 1 : 2;
                        });

                    foreach ($sessions as $session) {
                        // Skip if already locked or manually placed
                        if ($session->is_locked || $session->is_manual_placed) {
                            continue;
                        }

                        // Check if config is manual
                        if ($config->is_manual) {
                            $session->update([
                                'status' => 'UNSCHEDULED',
                                'unscheduled_reason' => 'Dijadwalkan manual oleh admin',
                            ]);

                            continue;
                        }

                        // Check if class is Saturday
                        if ($isSaturdayClass) {
                            $session->update([
                                'status' => 'UNSCHEDULED',
                                'unscheduled_reason' => 'Kelas hari Sabtu, tidak ada hari sesudahnya',
                            ]);

                            continue;
                        }

                        // Check capacity
                        if ($section->enrollment > $maxRoomCapacity) {
                            $session->update([
                                'status' => 'UNSCHEDULED',
                                'unscheduled_reason' => 'Kapasitas kurang',
                            ]);

                            continue;
                        }

                        // Find candidate slots across all days and rooms
                        $duration = $session->duration_min;
                        $otherSession = LabSession::where('class_section_id', $section->id)
                            ->where('schedule_id', $schedule->id)
                            ->where('id', '!=', $session->id)
                            ->where('status', 'SCHEDULED')
                            ->first();

                        $candidates = [];
                        $capacityFailureCount = 0;
                        $testedCombinationsCount = 0;

                        foreach ($days as $day) {
                            $timeSlots = $this->slotCalendar->getCandidateSlots($day, $duration);

                            foreach ($timeSlots as $slot) {
                                foreach ($rooms as $room) {
                                    $testedCombinationsCount++;

                                    if ($section->enrollment > $room->capacity) {
                                        $capacityFailureCount++;

                                        continue;
                                    }

                                    $failReason = $this->constraintChecker->checkHardConstraintForCandidate(
                                        $session,
                                        $section,
                                        $room,
                                        $day,
                                        $slot['start'],
                                        $slot['end'],
                                        $latestClassMeeting,
                                        $otherSession
                                    );

                                    if ($failReason === null) {
                                        $cost = $this->costEvaluator->evaluate(
                                            $session,
                                            $section,
                                            $room,
                                            $day,
                                            $slot['start'],
                                            $slot['end'],
                                            $context
                                        );

                                        $candidates[] = [
                                            'day' => $day,
                                            'start_min' => $slot['start'],
                                            'end_min' => $slot['end'],
                                            'room' => $room,
                                            'cost' => $cost,
                                        ];
                                    }
                                }
                            }
                        }

                        if (empty($candidates)) {
                            $reason = ($capacityFailureCount === $testedCombinationsCount && $testedCombinationsCount > 0)
                                ? 'Kapasitas kurang'
                                : 'Tidak ada slot kosong setelah kelas';

                            $session->update([
                                'status' => 'UNSCHEDULED',
                                'unscheduled_reason' => $reason,
                            ]);
                        } else {
                            // Pick best candidate: lowest cost, then earlier day, then earlier start time, then internal lab
                            usort($candidates, function ($a, $b) {
                                if ($a['cost'] !== $b['cost']) {
                                    return $a['cost'] <=> $b['cost'];
                                }
                                $dayA = self::DAY_INDEX[$a['day']] ?? 99;
                                $dayB = self::DAY_INDEX[$b['day']] ?? 99;
                                if ($dayA !== $dayB) {
                                    return $dayA <=> $dayB;
                                }
                                if ($a['start_min'] !== $b['start_min']) {
                                    return $a['start_min'] <=> $b['start_min'];
                                }
                                $typeA = $a['room']->type === 'internal' ? 0 : 1;
                                $typeB = $b['room']->type === 'internal' ? 0 : 1;
                                if ($typeA !== $typeB) {
                                    return $typeA <=> $typeB;
                                }

                                return strcmp($a['room']->id, $b['room']->id);
                            });

                            $best = $candidates[0];

                            $session->update([
                                'status' => 'SCHEDULED',
                                'unscheduled_reason' => null,
                                'day_of_week' => self::DAY_INDEX[$best['day']] ?? 1,
                                'start_min' => $best['start_min'],
                                'end_min' => $best['end_min'],
                                'lab_id' => $best['room']->id,
                            ]);
                        }
                    }
                }
            }

            // 5. Build summary
            $allSessions = LabSession::where('schedule_id', $schedule->id)->with(['room', 'section.course'])->get();
            $placedCount = $allSessions->where('status', 'placed')->count();
            $unscheduledSessions = $allSessions->where('status', 'unscheduled');
            $externalCount = $allSessions->where('status', 'placed')->filter(fn ($s) => $s->room && $s->room->type === 'external')->count();

            $unscheduledItems = [];
            foreach ($unscheduledSessions as $us) {
                $unscheduledItems[] = [
                    'session_id' => $us->id,
                    'course_name' => $us->section?->course?->name ?? 'Mata Kuliah',
                    'section_label' => $us->section?->label ?? '',
                    'type' => $us->type,
                    'reason' => $us->unscheduled_reason ?? 'Belum terjadwal',
                ];
            }

            return [
                'total_placed' => $placedCount,
                'total_unscheduled' => $unscheduledSessions->count(),
                'external_lab_count' => $externalCount,
                'soft_constraint_violations' => 0,
                'unscheduled_items' => $unscheduledItems,
            ];
        });
    }

    /**
     * Reset non-locked and non-manual-placed sessions for a period.
     */
    public function reset(Period $period, ?Schedule $schedule = null): int
    {
        $schedule = $schedule ?? Schedule::where('period_id', $period->id)->where('is_active', true)->first();
        if (! $schedule) {
            return 0;
        }

        return LabSession::where('schedule_id', $schedule->id)
            ->where('is_locked', false)
            ->where('is_manual_placed', false)
            ->update([
                'day_of_week' => null,
                'start_min' => null,
                'end_min' => null,
                'lab_id' => null,
                'status' => 'UNSCHEDULED',
                'unscheduled_reason' => 'Direset oleh admin',
            ]);
    }
}
