<?php

namespace App\Services\Scheduling;

use App\Models\ClassSection;
use App\Models\Matkul as Course;
use App\Models\Period;

class PathCalculator
{
    protected int $maxPaths = 500;

    public function setMaxPaths(int $max): self
    {
        $this->maxPaths = $max;

        return $this;
    }

    /**
     * Calculate all valid paths for a specific semester in a period.
     *
     * @return array{
     *     semester: int,
     *     courses: array<int, array{id: string, name: string}>,
     *     total_paths: int,
     *     is_capped: bool,
     *     paths: array<int, array{
     *         sections: array<int, array{id: string, course_id: string, course_name: string, label: string}>,
     *         warnings: array<int, string>
     *     }>,
     *     warnings: array<int, string>
     * }
     */
    public function calculatePathsForSemester(Period $period, int $semester): array
    {
        // 1. Get mandatory courses for this semester that exist in the period's class sections
        $courses = Course::where('curriculum_semester', $semester)
            ->where('is_required', true)
            ->whereHas('classSections', fn ($q) => $q->where('period_id', $period->id))
            ->with(['classSections' => function ($q) use ($period) {
                $q->where('period_id', $period->id)
                    ->with(['classMeetings', 'labSessions' => fn ($s) => $s->where('status', 'SCHEDULED')]);
            }])
            ->get();

        $warnings = [];

        if ($courses->isEmpty()) {
            return [
                'semester' => $semester,
                'courses' => [],
                'total_paths' => 0,
                'is_capped' => false,
                'paths' => [],
                'warnings' => ["Tidak ada matkul wajib untuk semester {$semester} di periode ini."],
            ];
        }

        // Build course options
        $courseOptions = [];
        foreach ($courses as $course) {
            $sectionList = [];
            foreach ($course->classSections as $sec) {
                $activities = [];
                // Class meetings
                foreach ($sec->classMeetings as $m) {
                    $activities[] = [
                        'day' => $m->day,
                        'start' => $m->start_min,
                        'end' => $m->end_min,
                        'label' => 'Kelas '.$sec->label,
                    ];
                }
                // Placed lab sessions
                foreach ($sec->labSessions as $ls) {
                    if ($ls->day && $ls->start_min && $ls->end_min) {
                        $activities[] = [
                            'day' => $ls->day,
                            'start' => $ls->start_min,
                            'end' => $ls->end_min,
                            'label' => ucfirst($ls->type).' '.$sec->label,
                        ];
                    }
                }

                // Check self clash
                $selfClash = $this->hasInternalClash($activities);

                $sectionList[] = [
                    'id' => $sec->id,
                    'course_id' => $course->id,
                    'course_name' => $course->name,
                    'label' => $sec->label,
                    'activities' => $activities,
                    'has_internal_clash' => $selfClash,
                ];
            }

            // Filter out sections with internal clashes
            $validSectionList = array_values(array_filter($sectionList, fn ($s) => ! $s['has_internal_clash']));

            if (empty($validSectionList)) {
                $warnings[] = "Mata kuliah {$course->name} tidak memiliki seksi yang bebas dari bentrok internal.";
            }

            $courseOptions[] = [
                'course_id' => $course->id,
                'course_name' => $course->name,
                'sections' => $validSectionList,
            ];
        }

        // Check if any course has 0 options
        foreach ($courseOptions as $opt) {
            if (empty($opt['sections'])) {
                return [
                    'semester' => $semester,
                    'courses' => $courses->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all(),
                    'total_paths' => 0,
                    'is_capped' => false,
                    'paths' => [],
                    'warnings' => array_merge($warnings, ["Semester {$semester} memiliki 0 path valid karena satu atau lebih mata kuliah tidak memiliki opsi seksi valid."]),
                ];
            }
        }

        // Sort courseOptions by fewest sections first for optimal DFS pruning
        usort($courseOptions, fn ($a, $b) => count($a['sections']) <=> count($b['sections']));

        // Run DFS
        $paths = [];
        $isCapped = false;

        $this->dfs(
            $courseOptions,
            0,
            [],
            [],
            $paths,
            $isCapped
        );

        if (empty($paths)) {
            $warnings[] = "Semester {$semester} memiliki 0 path valid tanpa bentrok.";
        }

        return [
            'semester' => $semester,
            'courses' => $courses->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all(),
            'total_paths' => count($paths),
            'is_capped' => $isCapped,
            'paths' => $paths,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check if a list of activities has any mutual overlap.
     */
    protected function hasInternalClash(array $activities): bool
    {
        $count = count($activities);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                if ($activities[$i]['day'] === $activities[$j]['day']) {
                    if (max($activities[$i]['start'], $activities[$j]['start']) < min($activities[$i]['end'], $activities[$j]['end'])) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * DFS to find combinations of sections across courses without day/time clash.
     */
    protected function dfs(
        array &$courseOptions,
        int $courseIndex,
        array $currentSelected,
        array $occupiedByDay,
        array &$results,
        bool &$isCapped
    ): void {
        if ($isCapped) {
            return;
        }

        if ($courseIndex === count($courseOptions)) {
            $results[] = [
                'sections' => array_map(fn ($s) => [
                    'id' => $s['id'],
                    'course_id' => $s['course_id'],
                    'course_name' => $s['course_name'],
                    'label' => $s['label'],
                ], $currentSelected),
                'warnings' => [],
            ];

            if (count($results) >= $this->maxPaths) {
                $isCapped = true;
            }

            return;
        }

        $course = $courseOptions[$courseIndex];

        foreach ($course['sections'] as $section) {
            if ($this->sectionClashesWithOccupied($section, $occupiedByDay)) {
                continue; // Pruned
            }

            // Add section activities to occupied
            $newOccupied = $occupiedByDay;
            foreach ($section['activities'] as $act) {
                $newOccupied[$act['day']][] = [
                    'start' => $act['start'],
                    'end' => $act['end'],
                ];
            }

            $currentSelected[] = $section;

            $this->dfs(
                $courseOptions,
                $courseIndex + 1,
                $currentSelected,
                $newOccupied,
                $results,
                $isCapped
            );

            array_pop($currentSelected);

            if ($isCapped) {
                return;
            }
        }
    }

    /**
     * Check if section activities clash with already occupied intervals by day.
     */
    protected function sectionClashesWithOccupied(array $section, array $occupiedByDay): bool
    {
        foreach ($section['activities'] as $act) {
            $day = $act['day'];
            if (! isset($occupiedByDay[$day])) {
                continue;
            }

            foreach ($occupiedByDay[$day] as $occ) {
                if (max($act['start'], $occ['start']) < min($act['end'], $occ['end'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Evaluate a custom path selection.
     *
     * @param  array<int, string>  $sectionIds
     * @return array{
     *     valid: bool,
     *     clashes: array<int, array{
     *         course1: string,
     *         section1: string,
     *         course2: string,
     *         section2: string,
     *         day: string,
     *         time1: string,
     *         time2: string
     *     }>,
     *     suggestions: array<int, string>
     * }
     */
    public function evaluateCustomPath(Period $period, int $semester, array $sectionIds): array
    {
        $sections = ClassSection::whereIn('id', $sectionIds)
            ->with(['course', 'classMeetings', 'labSessions' => fn ($q) => $q->where('status', 'SCHEDULED')])
            ->get();

        $activitiesBySection = [];
        foreach ($sections as $sec) {
            $acts = [];
            foreach ($sec->classMeetings as $m) {
                $acts[] = [
                    'day' => $m->day,
                    'start' => $m->start_min,
                    'end' => $m->end_min,
                    'desc' => "Kelas {$sec->course->name} {$sec->label}",
                ];
            }
            foreach ($sec->labSessions as $ls) {
                if ($ls->day && $ls->start_min && $ls->end_min) {
                    $acts[] = [
                        'day' => $ls->day,
                        'start' => $ls->start_min,
                        'end' => $ls->end_min,
                        'desc' => ucfirst($ls->type)." {$sec->course->name} {$sec->label}",
                    ];
                }
            }
            $activitiesBySection[$sec->id] = [
                'section' => $sec,
                'activities' => $acts,
            ];
        }

        $clashes = [];
        $clashedSectionIds = [];

        $secIds = array_keys($activitiesBySection);
        $count = count($secIds);

        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $s1 = $activitiesBySection[$secIds[$i]];
                $s2 = $activitiesBySection[$secIds[$j]];

                foreach ($s1['activities'] as $act1) {
                    foreach ($s2['activities'] as $act2) {
                        if ($act1['day'] === $act2['day']) {
                            if (max($act1['start'], $act2['start']) < min($act1['end'], $act2['end'])) {
                                $clashes[] = [
                                    'course1' => $s1['section']->course->name,
                                    'section1' => $s1['section']->label,
                                    'course2' => $s2['section']->course->name,
                                    'section2' => $s2['section']->label,
                                    'day' => $act1['day'],
                                    'time1' => sprintf('%02d.%02d - %02d.%02d (%s)', floor($act1['start'] / 60), $act1['start'] % 60, floor($act1['end'] / 60), $act1['end'] % 60, $act1['desc']),
                                    'time2' => sprintf('%02d.%02d - %02d.%02d (%s)', floor($act2['start'] / 60), $act2['start'] % 60, floor($act2['end'] / 60), $act2['end'] % 60, $act2['desc']),
                                ];
                                $clashedSectionIds[$s1['section']->id] = $s1['section'];
                                $clashedSectionIds[$s2['section']->id] = $s2['section'];
                            }
                        }
                    }
                }
            }
        }

        // Suggestions for alternatives
        $suggestions = [];
        if (! empty($clashes)) {
            foreach ($clashes as $clash) {
                // Find other sections of course1
                $c1Other = ClassSection::where('period_id', $period->id)
                    ->whereHas('course', fn ($q) => $q->where('nama', $clash['course1']))
                    ->where('label', '!=', $clash['section1'])
                    ->pluck('label')
                    ->all();

                $c2Other = ClassSection::where('period_id', $period->id)
                    ->whereHas('course', fn ($q) => $q->where('nama', $clash['course2']))
                    ->where('label', '!=', $clash['section2'])
                    ->pluck('label')
                    ->all();

                $msg = "{$clash['course1']} {$clash['section1']} bentrok dengan {$clash['course2']} {$clash['section2']}.";
                $alternatives = [];
                if (! empty($c1Other)) {
                    $alternatives[] = 'coba '.$clash['course1'].' '.implode(' / ', $c1Other);
                }
                if (! empty($c2Other)) {
                    $alternatives[] = 'coba '.$clash['course2'].' '.implode(' / ', $c2Other);
                }

                if (! empty($alternatives)) {
                    $msg .= ' Saran: '.implode(', atau ', $alternatives).'.';
                }
                $suggestions[] = $msg;
            }
        }

        return [
            'valid' => empty($clashes),
            'clashes' => $clashes,
            'suggestions' => array_unique($suggestions),
        ];
    }
}
