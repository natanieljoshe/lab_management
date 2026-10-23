<?php

namespace App\Services\Scheduling;

class SlotCalendar
{
    protected array $config;

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? config('scheduling', []);
    }

    public function getDays(): array
    {
        return $this->config['auto_schedule_days'] ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    }

    public function getDaySlots(string $day): array
    {
        return $this->config['slots'][$day] ?? [];
    }

    public function getForbiddenWindows(string $day): array
    {
        return $this->config['forbidden_windows'][$day] ?? [];
    }

    public function getDayAutoEndTime(string $day): int
    {
        return $this->config['day_auto_end_min'][$day] ?? 990;
    }

    public function getDayAutoStartTime(): int
    {
        return $this->config['auto_schedule_start_min'] ?? 450;
    }

    /**
     * Check if a time interval overlaps with any forbidden window on the given day.
     * Overlap occurs if max(start1, start2) < min(end1, end2).
     */
    public function isTimeOverlappingForbidden(string $day, int $startMin, int $endMin): bool
    {
        $windows = $this->getForbiddenWindows($day);
        foreach ($windows as $win) {
            $winStart = $win['start'];
            $winEnd = $win['end'];

            if (max($startMin, $winStart) < min($endMin, $winEnd)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if interval is within the auto schedule hours for that day.
     */
    public function isWithinAutoScheduleHours(string $day, int $startMin, int $endMin): bool
    {
        if ($startMin < $this->getDayAutoStartTime()) {
            return false;
        }

        if ($endMin > $this->getDayAutoEndTime($day)) {
            return false;
        }

        return true;
    }

    /**
     * Generate all valid candidate time slot intervals for a given day and duration (in minutes).
     *
     * @return array<int, array{start: int, end: int}>
     */
    public function getCandidateSlots(string $day, int $durationMin): array
    {
        $slots = $this->getDaySlots($day);
        $candidates = [];

        // Build continuous blocks from available auto slots
        $autoSlots = array_values(array_filter($slots, fn ($s) => ! empty($s['is_auto'])));
        $count = count($autoSlots);

        for ($i = 0; $i < $count; $i++) {
            $start = $autoSlots[$i]['start'];
            $targetEnd = $start + $durationMin;

            // Check if targetEnd exceeds day auto end
            if ($targetEnd > $this->getDayAutoEndTime($day)) {
                continue;
            }

            // Check forbidden overlap
            if ($this->isTimeOverlappingForbidden($day, $start, $targetEnd)) {
                continue;
            }

            // Verify that all intermediate intervals are covered by auto slots (continuous and available)
            $isContinuous = false;
            for ($j = $i; $j < $count; $j++) {
                if ($autoSlots[$j]['end'] === $targetEnd) {
                    // Check all slots between i and j are contiguous
                    $validContiguous = true;
                    for ($k = $i; $k < $j; $k++) {
                        if ($autoSlots[$k]['end'] !== $autoSlots[$k + 1]['start']) {
                            $validContiguous = false;
                            break;
                        }
                    }
                    if ($validContiguous) {
                        $isContinuous = true;
                    }
                    break;
                }
            }

            if ($isContinuous) {
                $candidates[] = [
                    'start' => $start,
                    'end' => $targetEnd,
                ];
            }
        }

        return $candidates;
    }
}
