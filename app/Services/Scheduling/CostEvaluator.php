<?php

namespace App\Services\Scheduling;

use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\LabSession;
use App\Models\Labs as Room;
use App\Models\Matkul as Course;
use App\Models\Period;

class CostEvaluator
{
    protected array $config;

    public function __construct(
        protected PathCalculator $pathCalculator,
        ?array $config = null
    ) {
        $this->config = $config ?? config('scheduling', []);
    }

    /**
     * Evaluate the penalty cost of placing a session at a candidate slot.
     * Lower cost is better.
     *
     * @param  array{
     *     period: Period,
     *     target_semester_paths?: array,
     *     same_semester_sections?: array
     * }  $context
     */
    public function evaluate(
        LabSession $session,
        ClassSection $section,
        Room $room,
        string $day,
        int $startMin,
        int $endMin,
        array $context = []
    ): int {
        $cost = 0;
        $costsConfig = $this->config['costs'] ?? [];

        // 1. External lab cost (+5)
        if ($room->type === 'external') {
            $cost += $costsConfig['external_lab'] ?? 5;
        }

        $course = $section->relationLoaded('course') ? $section->course : $section->course()->first();
        $sem = $course ? $course->curriculum_semester : 1;

        // 2. Cross-semester rule (+50 if all paths in target semester clash)
        $targetSem = $this->config['cross_semester_pairs'][$sem] ?? null;
        if ($targetSem && isset($context['period'])) {
            $targetPaths = $context['target_semester_paths'][$targetSem] ?? null;
            if ($targetPaths === null) {
                $calc = $this->pathCalculator->calculatePathsForSemester($context['period'], $targetSem);
                $targetPaths = $calc['paths'];
            }

            if (! empty($targetPaths)) {
                $allPathsClash = true;
                foreach ($targetPaths as $p) {
                    $pathClashes = false;
                    $pathSecIds = array_column($p['sections'], 'id');
                    // Check if any activity of sections in this path clashes
                    $pathMeetings = ClassMeeting::whereIn('class_section_id', $pathSecIds)
                        ->get()
                        ->filter(fn($m) => $m->day === $day);

                    foreach ($pathMeetings as $m) {
                        if (max($startMin, $m->start_min) < min($endMin, $m->end_min)) {
                            $pathClashes = true;
                            break;
                        }
                    }

                    if (! $pathClashes) {
                        $pathSessions = LabSession::whereIn('class_section_id', $pathSecIds)
                            ->where('status', 'SCHEDULED')
                            ->get()
                            ->filter(fn($ps) => $ps->day === $day);

                        foreach ($pathSessions as $ps) {
                            if (max($startMin, $ps->start_min) < min($endMin, $ps->end_min)) {
                                $pathClashes = true;
                                break;
                            }
                        }
                    }

                    if (! $pathClashes) {
                        $allPathsClash = false;
                        break;
                    }
                }

                if ($allPathsClash) {
                    $cost += $costsConfig['cross_semester_clash'] ?? 50;
                }
            }
        }

        // 3. Path loss in same semester (+1000 if all sections of any mandatory course in same semester clash)
        if (isset($context['period'])) {
            $otherMandatoryCourses = Course::where('curriculum_semester', $sem)
                ->where('is_required', true)
                ->where('id', '!=', $course?->id)
                ->whereHas('classSections', fn ($q) => $q->where('period_id', $context['period']->id))
                ->with(['classSections' => fn ($q) => $q->where('period_id', $context['period']->id)->with(['classMeetings', 'labSessions' => fn ($s) => $s->where('status', 'SCHEDULED')])])
                ->get();

            $totalOtherSections = 0;
            $clashingOtherSections = 0;

            foreach ($otherMandatoryCourses as $otherCourse) {
                $courseAllSectionsClash = true;
                foreach ($otherCourse->classSections as $sec) {
                    $totalOtherSections++;
                    $secClashes = false;

                    foreach ($sec->classMeetings as $m) {
                        if ($m->day === $day && max($startMin, $m->start_min) < min($endMin, $m->end_min)) {
                            $secClashes = true;
                            break;
                        }
                    }
                    if (! $secClashes) {
                        foreach ($sec->labSessions as $ls) {
                            if ($ls->day === $day && max($startMin, $ls->start_min) < min($endMin, $ls->end_min)) {
                                $secClashes = true;
                                break;
                            }
                        }
                    }

                    if ($secClashes) {
                        $clashingOtherSections++;
                    } else {
                        $courseAllSectionsClash = false;
                    }
                }

                if ($courseAllSectionsClash && $otherCourse->classSections->isNotEmpty()) {
                    $cost += $costsConfig['path_loss'] ?? 1000;
                }
            }

            // 4. Smooth spread penalty (+3 * proportion of clashing sections)
            if ($totalOtherSections > 0) {
                $ratio = $clashingOtherSections / $totalOtherSections;
                $multiplier = $costsConfig['soft_spread_multiplier'] ?? 3;
                $cost += (int) round($multiplier * $ratio * 10);
            }
        }

        return $cost;
    }
}
