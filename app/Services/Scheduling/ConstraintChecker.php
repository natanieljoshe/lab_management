<?php

namespace App\Services\Scheduling;

use App\Models\ClassMeeting;
use App\Models\ClassSection;
use App\Models\LabSession;
use App\Models\Labs as Room;

class ConstraintChecker
{
    public const DAY_INDEX = [
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6,
    ];

    public function __construct(
        protected SlotCalendar $slotCalendar
    ) {}

    /**
     * Check if a placement satisfies all hard constraints.
     * Returns an array with 'valid' (bool) and 'violations' (list of human-readable messages in Indonesian).
     *
     * @param  array{
     *     meetings?: array,
     *     other_placed_sessions?: array,
     *     allow_manual_hours?: bool,
     *     allow_manual_rooms?: bool
     * }  $context
     */
    public function validatePlacement(
        LabSession $session,
        Room $room,
        string $day,
        int $startMin,
        int $endMin,
        array $context = []
    ): array {
        $violations = [];

        // 1. Room type must be active lab
        if (! $room->is_active) {
            $violations[] = "Ruangan {$room->name} tidak aktif.";
        }

        // 2. Capacity check
        $section = $session->relationLoaded('section') ? $session->section : $session->section()->first();
        $enrollment = $section ? $section->enrollment : 30;
        if ($enrollment > $room->capacity) {
            $violations[] = "Kapasitas ruangan ({$room->capacity}) tidak mencukupi untuk {$enrollment} mahasiswa (Kapasitas kurang).";
        }

        // 3. Manual-only room check (generator vs manual)
        $allowManualRooms = $context['allow_manual_rooms'] ?? false;
        if ($room->is_manual_only && ! $allowManualRooms) {
            $violations[] = "Ruangan {$room->name} berstatus manual_only dan tidak dapat diisi otomatis.";
        }

        // 4. Forbidden window
        if ($this->slotCalendar->isTimeOverlappingForbidden($day, $startMin, $endMin)) {
            $violations[] = "Slot waktu tumpang tindih dengan jam KU / jam kemahasiswaan pada hari {$day}.";
        }

        // 5. Day & Auto schedule limits
        $allowManualHours = $context['allow_manual_hours'] ?? false;
        if (! $allowManualHours) {
            if (! in_array($day, $this->slotCalendar->getDays())) {
                $violations[] = "Hari {$day} bukan hari kerja untuk penjadwalan otomatis.";
            }
            if ($startMin < $this->slotCalendar->getDayAutoStartTime()) {
                $violations[] = 'Kegiatan tidak boleh dimulai sebelum jam 07.30.';
            }
            if ($endMin > $this->slotCalendar->getDayAutoEndTime($day)) {
                $autoEndStr = $this->minutesToTimeString($this->slotCalendar->getDayAutoEndTime($day));
                $violations[] = "Kegiatan harus selesai paling lambat jam {$autoEndStr} pada hari {$day}.";
            }
        }

        // 6. Precede class check (Section 9.1 Rule 2)
        $classMeetings = $context['meetings'] ?? ($section ? $section->meetings : []);
        $latestClass = $this->getLatestClassMeeting($classMeetings);

        if ($latestClass) {
            $latestDayIdx = self::DAY_INDEX[$latestClass->day] ?? 0;
            $proposedDayIdx = self::DAY_INDEX[$day] ?? 0;

            if ($latestDayIdx === 6) {
                // Saturday class
                $violations[] = 'Kelas hari Sabtu, tidak ada hari sesudahnya.';
            } elseif ($proposedDayIdx < $latestDayIdx) {
                $violations[] = "Praktikum/responsi tidak boleh mendahului kelas ({$latestClass->day} {$this->minutesToTimeString($latestClass->start_min)} - {$this->minutesToTimeString($latestClass->end_min)}).";
            } elseif ($proposedDayIdx === $latestDayIdx && $startMin < $latestClass->end_min) {
                $violations[] = "Praktikum/responsi pada hari yang sama harus dimulai setelah jam selesai kelas ({$this->minutesToTimeString($latestClass->end_min)}).";
            }
        }

        // 7. Self-overlap check (same section cannot have overlapping practicum and responsi)
        $otherSessionQuery = LabSession::where('class_section_id', $session->class_section_id)
            ->where('id', '!=', $session->id)
            ->where('status', 'SCHEDULED');
        if ($session->schedule_id) {
            $otherSessionQuery->where('schedule_id', $session->schedule_id);
        }
        $otherSession = $otherSessionQuery->get()->first(fn($s) => $s->day === $day);

        if ($otherSession) {
            if ($this->intervalsOverlap($startMin, $endMin, $otherSession->start_min, $otherSession->end_min)) {
                $violations[] = "Tumpang tindih dengan sesi {$otherSession->type} dari seksi yang sama pada jam {$this->minutesToTimeString($otherSession->start_min)} - {$this->minutesToTimeString($otherSession->end_min)}.";
            }
        }

        // 8. Room clash check (against class meetings and other lab sessions in the room)
        $roomClash = $this->checkRoomClash($room, $day, $startMin, $endMin, $session->id, $context);
        if ($roomClash) {
            $violations[] = $roomClash;
        }

        return [
            'valid' => empty($violations),
            'violations' => $violations,
        ];
    }

    /**
     * Get the latest meeting in the week for a collection of meetings.
     */
    public function getLatestClassMeeting($meetings): ?ClassMeeting
    {
        $latest = null;
        $maxDayIdx = -1;
        $maxEnd = -1;

        foreach ($meetings as $meeting) {
            $dayIdx = self::DAY_INDEX[$meeting->day] ?? 0;
            if ($dayIdx > $maxDayIdx) {
                $maxDayIdx = $dayIdx;
                $maxEnd = $meeting->end_min;
                $latest = $meeting;
            } elseif ($dayIdx === $maxDayIdx && $meeting->end_min > $maxEnd) {
                $maxEnd = $meeting->end_min;
                $latest = $meeting;
            }
        }

        return $latest;
    }

    /**
     * Check if room has clash with any class meeting or placed lab session.
     */
    public function checkRoomClash(
        Room $room,
        string $day,
        int $startMin,
        int $endMin,
        ?string $ignoreSessionId = null,
        array $context = []
    ): ?string {
        $dayIdx = self::DAY_INDEX[$day] ?? 1;

        // 1. Check class meetings in this room
        $meetings = ClassMeeting::where('lab_id', $room->id)
            ->where('day_of_week', $dayIdx)
            ->with('section.course')
            ->get();

        foreach ($meetings as $meeting) {
            if ($this->intervalsOverlap($startMin, $endMin, $meeting->start_min, $meeting->end_min)) {
                $cName = $meeting->section?->course?->name ?? 'Kelas';
                $sLabel = $meeting->section?->label ?? '';

                return "Bentrok ruangan {$room->name} dengan jadwal kelas {$cName} {$sLabel} ({$this->minutesToTimeString($meeting->start_min)} - {$this->minutesToTimeString($meeting->end_min)}).";
            }
        }

        // 2. Check placed lab sessions in this room
        $query = LabSession::where('lab_id', $room->id)
            ->where('day_of_week', $dayIdx)
            ->where('status', 'SCHEDULED')
            ->with('section.course');

        if ($ignoreSessionId) {
            $query->where('id', '!=', $ignoreSessionId);
        }

        $sessions = $query->get();
        foreach ($sessions as $placed) {
            if ($this->intervalsOverlap($startMin, $endMin, $placed->start_min, $placed->end_min)) {
                $cName = $placed->section?->course?->name ?? 'Sesi';
                $sLabel = $placed->section?->label ?? '';

                return "Bentrok ruangan {$room->name} dengan {$placed->type} {$cName} {$sLabel} ({$this->minutesToTimeString($placed->start_min)} - {$this->minutesToTimeString($placed->end_min)}).";
            }
        }

        return null;
    }

    /**
     * Fast hard constraint check for generator loop.
     * Returns null if valid, or a failure reason string.
     */
    public function checkHardConstraintForCandidate(
        LabSession $session,
        ClassSection $section,
        Room $room,
        string $day,
        int $startMin,
        int $endMin,
        $latestClassMeeting,
        $otherPlacedSession
    ): ?string {
        // Capacity
        if ($section->enrollment > $room->capacity) {
            return 'Kapasitas kurang';
        }

        // Manual only room
        if ($room->is_manual_only) {
            return 'Ruangan manual only';
        }

        // Forbidden window (KU / Jam Kemahasiswaan)
        if ($this->slotCalendar->isTimeOverlappingForbidden($day, $startMin, $endMin)) {
            return 'Jam KU / Kemahasiswaan';
        }

        // Auto schedule bounds
        if ($endMin > $this->slotCalendar->getDayAutoEndTime($day) || $startMin < $this->slotCalendar->getDayAutoStartTime()) {
            return 'Batas jam otomatis terlewati';
        }

        // Class precedence
        if ($latestClassMeeting) {
            $latestDayIdx = self::DAY_INDEX[$latestClassMeeting->day] ?? 0;
            $proposedDayIdx = self::DAY_INDEX[$day] ?? 0;

            if ($latestDayIdx === 6) {
                return 'Kelas hari Sabtu, tidak ada hari sesudahnya';
            }
            if ($proposedDayIdx < $latestDayIdx) {
                return 'Mendahului hari kelas';
            }
            if ($proposedDayIdx === $latestDayIdx && $startMin < $latestClassMeeting->end_min) {
                return 'Mendahului jam selesai kelas';
            }
        }

        // Self-overlap with other session of same section
        if ($otherPlacedSession && $otherPlacedSession->day === $day) {
            if ($this->intervalsOverlap($startMin, $endMin, $otherPlacedSession->start_min, $otherPlacedSession->end_min)) {
                return 'Tumpang tindih dengan sesi seksi sendiri';
            }
        }

        // Room clash
        if ($this->checkRoomClash($room, $day, $startMin, $endMin, $session->id)) {
            return 'Bentrok ruangan';
        }

        return null;
    }

    public function intervalsOverlap(int $s1, int $e1, int $s2, int $e2): bool
    {
        return max($s1, $s2) < min($e1, $e2);
    }

    public function minutesToTimeString(int $minutes): string
    {
        $h = floor($minutes / 60);
        $m = $minutes % 60;

        return sprintf('%02d.%02d', $h, $m);
    }
}
