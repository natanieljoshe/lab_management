<?php

namespace App\Services\Scheduling;

use App\Models\LabSession;
use App\Models\Period;
use App\Models\Schedule;

class ScheduleValidator
{
    public function __construct(
        protected ConstraintChecker $constraintChecker,
        protected SlotCalendar $slotCalendar
    ) {}

    /**
     * Validate all placed lab sessions in a period against all hard constraints.
     *
     * @return array{
     *     valid: bool,
     *     total_checked: int,
     *     violation_count: int,
     *     violations: array<int, array{
     *         session_id: string,
     *         course_name: string,
     *         section_label: string,
     *         type: string,
     *         day: string,
     *         time: string,
     *         room: string,
     *         reasons: array<int, string>
     *     }>
     * }
     */
    public function validateAllPlaced(Period $period, ?Schedule $schedule = null): array
    {
        $schedule = $schedule ?? Schedule::where('period_id', $period->id)->where('is_active', true)->first();
        if (! $schedule) {
            return [
                'valid' => true,
                'total_checked' => 0,
                'violation_count' => 0,
                'violations' => [],
            ];
        }

        $sessions = LabSession::where('schedule_id', $schedule->id)
            ->where('status', 'SCHEDULED')
            ->with(['room', 'section.course', 'section.classMeetings'])
            ->get();

        $violations = [];

        foreach ($sessions as $session) {
            $room = $session->room;
            if (! $room) {
                $violations[] = [
                    'session_id' => $session->id,
                    'course_name' => $session->section?->course?->name ?? 'Mata Kuliah',
                    'section_label' => $session->section?->label ?? '',
                    'type' => $session->type,
                    'day' => $session->day ?? 'None',
                    'time' => "{$session->start_min} - {$session->end_min}",
                    'room' => 'None',
                    'reasons' => ['Sesi tidak memiliki ruangan.'],
                ];

                continue;
            }

            $validation = $this->constraintChecker->validatePlacement(
                $session,
                $room,
                $session->day,
                $session->start_min,
                $session->end_min,
                [
                    'allow_manual_hours' => $session->is_manual_placed,
                    'allow_manual_rooms' => $session->is_manual_placed,
                ]
            );

            if (! $validation['valid']) {
                $timeStr = sprintf(
                    '%02d.%02d - %02d.%02d',
                    floor($session->start_min / 60),
                    $session->start_min % 60,
                    floor($session->end_min / 60),
                    $session->end_min % 60
                );

                $violations[] = [
                    'session_id' => $session->id,
                    'course_name' => $session->section?->course?->name ?? 'Mata Kuliah',
                    'section_label' => $session->section?->label ?? '',
                    'type' => $session->type,
                    'day' => $session->day,
                    'time' => $timeStr,
                    'room' => $room->name,
                    'reasons' => $validation['violations'],
                ];
            }
        }

        return [
            'valid' => empty($violations),
            'total_checked' => $sessions->count(),
            'violation_count' => count($violations),
            'violations' => $violations,
        ];
    }
}
