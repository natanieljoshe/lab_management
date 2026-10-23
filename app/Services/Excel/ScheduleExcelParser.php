<?php

namespace App\Services\Excel;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ScheduleExcelParser
{
    /**
     * Known canonical courses and their aliases/typos.
     */
    protected array $courseAliases = [
        'aiml' => 'Artificial Intelligence and Machine Learning',
        'artificial intelligence and machine learning' => 'Artificial Intelligence and Machine Learning',
        'generative arstificial intelligence' => 'Generative Artificial Intelligence',
        'generative artificial intelligence' => 'Generative Artificial Intelligence',
        'enterprise resource palnning' => 'Enterprise Resource Planning',
        'enterprise resource planning' => 'Enterprise Resource Planning',
        'manajemen proyek teknologi informasi' => 'Manajemen Proyek TI',
        'manajemen proyek ti' => 'Manajemen Proyek TI',
        'pengembanganaplikasi berbasis android' => 'Pengembangan Aplikasi Berbasis Android',
        'pengembangan aplikasi berbasis android' => 'Pengembangan Aplikasi Berbasis Android',
        'text web  mining' => 'Text Web Mining',
        'text web mining' => 'Text Web Mining',
        'matematika  bisnis' => 'Matematika Bisnis',
        'matematika bisnis' => 'Matematika Bisnis',
        'presentasi dan visualisasi data' => 'Presentasi dan Visualisasi Data',
    ];

    /**
     * Map of column letters to room codes for the lab columns.
     */
    protected array $colToRoomCode = [
        'C' => 'SI',
        'D' => 'PG',
        'E' => 'MOBDEV',
        'F' => 'STUDIO',
        'G' => 'JK',
        'H' => 'SC',
        'I' => 'VR',
        'J' => 'MM',
        'K' => 'PA',
        'L' => 'PB',
        'M' => 'PC',
        'N' => 'PD',
    ];

    /**
     * Parse the Excel file at given path.
     *
     * @return array{
     *     meetings: array<int, array{
     *         course_name: string,
     *         section_label: string,
     *         day: string,
     *         start_min: int,
     *         end_min: int,
     *         room_code: ?string,
     *         room_label: string,
     *         raw_text: string,
     *         is_saturday: bool,
     *         enrollment: int
     *     }>,
     *     warnings: array<int, string>,
     *     summary: array<string, int>
     * }
     */
    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('JADWAL');

        if (! $sheet) {
            // Check if flat format sheet
            $activeSheet = $spreadsheet->getActiveSheet();
            if ($this->isFlatFormat($activeSheet)) {
                return $this->parseFlatFormat($activeSheet);
            }
            throw new \InvalidArgumentException('Sheet JADWAL tidak ditemukan di file Excel.');
        }

        return $this->parseScheduleSheet($sheet);
    }

    /**
     * Check if sheet is flat format.
     */
    protected function isFlatFormat($sheet): bool
    {
        $valA1 = strtolower(trim((string) $sheet->getCell('A1')->getValue()));

        return in_array($valA1, ['matkul', 'mata kuliah', 'course']);
    }

    /**
     * Parse flat format Excel.
     */
    public function parseFlatFormat($sheet): array
    {
        $highestRow = $sheet->getHighestRow();
        $meetings = [];
        $warnings = [];

        for ($row = 2; $row <= $highestRow; $row++) {
            $course = trim((string) $sheet->getCell('A'.$row)->getValue());
            if ($course === '') {
                continue;
            }

            $section = trim((string) $sheet->getCell('B'.$row)->getValue()) ?: 'A';
            $day = trim((string) $sheet->getCell('D'.$row)->getValue());
            $startStr = trim((string) $sheet->getCell('E'.$row)->getValue());
            $endStr = trim((string) $sheet->getCell('F'.$row)->getValue());
            $room = trim((string) $sheet->getCell('G'.$row)->getValue());
            $enrollment = (int) $sheet->getCell('H'.$row)->getValue() ?: 30;

            $startMin = $this->timeStringToMinutes($startStr);
            $endMin = $this->timeStringToMinutes($endStr);

            if ($startMin === null || $endMin === null || $day === '') {
                $warnings[] = "Baris $row: format jam atau hari tidak valid ($day, $startStr - $endStr)";

                continue;
            }

            $meetings[] = [
                'course_name' => $this->normalizeCourseName($course),
                'section_label' => $section,
                'day' => $day,
                'start_min' => $startMin,
                'end_min' => $endMin,
                'room_code' => $room ?: null,
                'room_label' => $room ?: 'Ruang Kelas',
                'raw_text' => "$course $section",
                'is_saturday' => strtolower($day) === 'sabtu',
                'enrollment' => $enrollment,
            ];
        }

        return [
            'meetings' => $meetings,
            'warnings' => $warnings,
            'summary' => [
                'total_meetings' => count($meetings),
                'warnings_count' => count($warnings),
            ],
        ];
    }

    /**
     * Parse original JADWAL grid format.
     */
    public function parseScheduleSheet($sheet): array
    {
        $warnings = [];

        // 1. Expand merged cells so every cell in a merged range has the value
        $mergedCells = $sheet->getMergeCells();
        $gridValues = [];

        $highestRow = min(100, $sheet->getHighestRow());
        $highestColStr = $sheet->getHighestColumn();
        $highestColIdx = min(42, Coordinate::columnIndexFromString($highestColStr));

        for ($r = 1; $r <= $highestRow; $r++) {
            for ($c = 1; $c <= $highestColIdx; $c++) {
                $colLetter = Coordinate::stringFromColumnIndex($c);
                $gridValues[$r][$colLetter] = trim((string) $sheet->getCell($colLetter.$r)->getValue());
            }
        }

        foreach ($mergedCells as $range) {
            $rangeParts = explode(':', $range);
            if (count($rangeParts) !== 2) {
                continue;
            }

            [$topLeft, $bottomRight] = $rangeParts;
            [$tlCol, $tlRow] = Coordinate::coordinateFromString($topLeft);
            [$brCol, $brRow] = Coordinate::coordinateFromString($bottomRight);

            $tlColIdx = Coordinate::columnIndexFromString($tlCol);
            $brColIdx = Coordinate::columnIndexFromString($brCol);

            $val = trim((string) $sheet->getCell($topLeft)->getValue());

            for ($r = $tlRow; $r <= min($highestRow, $brRow); $r++) {
                for ($c = $tlColIdx; $c <= min($highestColIdx, $brColIdx); $c++) {
                    $colLetter = Coordinate::stringFromColumnIndex($c);
                    $gridValues[$r][$colLetter] = $val;
                }
            }
        }

        // 2. Identify rows by Day and Time
        $currentDay = null;
        $rowTimeSlots = []; // row => ['day' => ..., 'start_min' => ..., 'end_min' => ...]

        for ($r = 3; $r <= $highestRow; $r++) {
            $dayCell = $gridValues[$r]['A'] ?? '';
            if ($dayCell !== '') {
                $currentDay = $this->normalizeDay($dayCell);
            }

            $timeCell = $gridValues[$r]['B'] ?? '';
            if ($timeCell === '') {
                continue;
            }

            $parsedTime = $this->parseTimeSlot($timeCell);
            if ($parsedTime && $currentDay) {
                $rowTimeSlots[$r] = [
                    'day' => $currentDay,
                    'start_min' => $parsedTime['start'],
                    'end_min' => $parsedTime['end'],
                ];
            }
        }

        // 3. For each column from C to highest, read blocks and merge vertical consecutive cells
        $rawBlocks = [];

        for ($c = 3; $c <= $highestColIdx; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $roomCode = $this->colToRoomCode[$colLetter] ?? null;
            $roomLabel = $roomCode ? "Lab $roomCode" : 'Ruang Kelas';

            $activeMeeting = null;

            foreach ($rowTimeSlots as $r => $slot) {
                $cellVal = $gridValues[$r][$colLetter] ?? '';

                // Filter out Kebaktian and Jam Kemahasiswaan
                if ($this->isForbiddenActivity($cellVal)) {
                    $cellVal = '';
                }

                if ($cellVal !== '') {
                    $parsed = $this->parseCellText($cellVal);

                    if (! $parsed) {
                        $warnings[] = "Baris $r, Kolom $colLetter: Teks tidak dapat diparse: '$cellVal'";

                        continue;
                    }

                    if ($activeMeeting !== null
                        && $activeMeeting['day'] === $slot['day']
                        && $activeMeeting['course_name'] === $parsed['course_name']
                        && $activeMeeting['section_label'] === $parsed['section_label']
                    ) {
                        // Extend current meeting
                        $activeMeeting['end_min'] = $slot['end_min'];
                        if (strlen($cellVal) > strlen($activeMeeting['raw_text'])) {
                            $activeMeeting['raw_text'] = $cellVal;
                        }
                    } else {
                        // Finish previous meeting if exists
                        if ($activeMeeting !== null) {
                            $rawBlocks[] = $activeMeeting;
                        }

                        // Start new meeting
                        $activeMeeting = [
                            'course_name' => $parsed['course_name'],
                            'section_label' => $parsed['section_label'],
                            'day' => $slot['day'],
                            'start_min' => $slot['start_min'],
                            'end_min' => $slot['end_min'],
                            'room_code' => $roomCode,
                            'room_label' => $roomLabel,
                            'raw_text' => $cellVal,
                            'is_saturday' => strtolower($slot['day']) === 'sabtu',
                            'enrollment' => 30,
                        ];
                    }
                } else {
                    if ($activeMeeting !== null) {
                        $rawBlocks[] = $activeMeeting;
                        $activeMeeting = null;
                    }
                }
            }

            if ($activeMeeting !== null) {
                $rawBlocks[] = $activeMeeting;
            }
        }

        // 4. Deduplication
        // "pertahankan entri terpanjang untuk matkul, seksi, dan hari yang sama yang tumpang tindih"
        $deduplicated = $this->deduplicateMeetings($rawBlocks);

        return [
            'meetings' => $deduplicated,
            'warnings' => $warnings,
            'summary' => [
                'total_meetings' => count($deduplicated),
                'warnings_count' => count($warnings),
            ],
        ];
    }

    /**
     * Deduplicate meetings that overlap for the same course, section, and day.
     */
    protected function deduplicateMeetings(array $meetings): array
    {
        $result = [];

        foreach ($meetings as $meeting) {
            $key = $meeting['course_name'].'|'.$meeting['section_label'].'|'.$meeting['day'];
            $matched = false;

            foreach ($result as $idx => $existing) {
                $existingKey = $existing['course_name'].'|'.$existing['section_label'].'|'.$existing['day'];

                if ($key === $existingKey) {
                    // Check if they overlap
                    $hasOverlap = ! ($meeting['end_min'] <= $existing['start_min'] || $meeting['start_min'] >= $existing['end_min']);

                    if ($hasOverlap) {
                        // Keep the longer duration or longer raw text
                        $existingDuration = $existing['end_min'] - $existing['start_min'];
                        $meetingDuration = $meeting['end_min'] - $meeting['start_min'];

                        if ($meetingDuration > $existingDuration || ($meetingDuration === $existingDuration && strlen($meeting['raw_text']) > strlen($existing['raw_text']))) {
                            // If existing was in a lab room and new one is not, keep room if applicable
                            if ($existing['room_code'] && ! $meeting['room_code']) {
                                $meeting['room_code'] = $existing['room_code'];
                                $meeting['room_label'] = $existing['room_label'];
                            }
                            $result[$idx] = $meeting;
                        }
                        $matched = true;
                        break;
                    }
                }
            }

            if (! $matched) {
                $result[] = $meeting;
            }
        }

        return array_values($result);
    }

    /**
     * Check if text is Kebaktian or Jam Kemahasiswaan.
     */
    protected function isForbiddenActivity(string $text): bool
    {
        $upper = strtoupper(trim($text));
        if (str_contains($upper, 'KEBAKTIAN UMUM') || str_contains($upper, 'JAM KEMAHASISWAAN')) {
            return true;
        }

        return false;
    }

    /**
     * Normalize day string.
     */
    public function normalizeDay(string $day): string
    {
        $d = strtolower(trim($day));
        if (str_starts_with($d, 'sen')) {
            return 'Senin';
        }
        if (str_starts_with($d, 'sel')) {
            return 'Selasa';
        }
        if (str_starts_with($d, 'rab')) {
            return 'Rabu';
        }
        if (str_starts_with($d, 'kam')) {
            return 'Kamis';
        }
        if (str_starts_with($d, 'jum')) {
            return 'Jumat';
        }
        if (str_starts_with($d, 'sab')) {
            return 'Sabtu';
        }

        return ucfirst($d);
    }

    /**
     * Parse time slot string (e.g. 07:30-08:30, 10:30:11:30, 12:30-13.00, 20:30:21:30).
     */
    public function parseTimeSlot(string $timeStr): ?array
    {
        $clean = trim($timeStr);
        if (preg_match('/(\d{1,2})[:.](\d{2})\s*[-:]\s*(\d{1,2})[:.](\d{2})/', $clean, $matches)) {
            $startHour = (int) $matches[1];
            $startMin = (int) $matches[2];
            $endHour = (int) $matches[3];
            $endMin = (int) $matches[4];

            return [
                'start' => $startHour * 60 + $startMin,
                'end' => $endHour * 60 + $endMin,
            ];
        }

        return null;
    }

    /**
     * Convert HH:MM string to integer minutes from 00:00.
     */
    public function timeStringToMinutes(string $str): ?int
    {
        $str = trim($str);
        if (preg_match('/(\d{1,2})[:.](\d{2})/', $str, $m)) {
            return ((int) $m[1]) * 60 + ((int) $m[2]);
        }

        return null;
    }

    /**
     * Parse cell text into course name and section label.
     */
    public function parseCellText(string $raw): ?array
    {
        $text = trim($raw);
        if ($text === '') {
            return null;
        }

        // Clean up unclosed parenthesis at end if any (e.g. "Pak Alvin")
        if (substr_count($text, '(') > substr_count($text, ')')) {
            $text .= ')';
        }

        // Extract section label:
        // Patterns:
        // 1. "Kls A", "Kls  A", "KLs A", "Kls A1 & A2", "Kls C1", "Kls H"
        // 2. "... A - IF...", "... B - IF...", "... C - IF..."
        // 3. "... A (Pak...", "... B (Bu..."
        // 4. "AIML - IF..." -> no section -> default 'A'
        $section = 'A';
        $coursePart = $text;

        if (preg_match('/\bKls\s+([A-Z0-9\s&]+?)(?=\s*\(|\s*:|$)/i', $text, $matches)) {
            $section = trim($matches[1]);
            $coursePart = substr($text, 0, strpos($text, $matches[0]));
        } elseif (preg_match('/^(.*?)\s+([A-D](?:[1-9])?)\s*-\s*[A-Z]/i', $text, $matches)) {
            $coursePart = trim($matches[1]);
            $section = trim($matches[2]);
        } elseif (preg_match('/^(.*?)\s+([A-D](?:[1-9])?)\s*\(/i', $text, $matches)) {
            $coursePart = trim($matches[1]);
            $section = trim($matches[2]);
        } elseif (preg_match('/^(.*?)\s+([A-D])\s*$/i', $text, $matches)) {
            $coursePart = trim($matches[1]);
            $section = trim($matches[2]);
        }

        $courseName = $this->normalizeCourseName(trim($coursePart));

        return [
            'course_name' => $courseName,
            'section_label' => $section,
        ];
    }

    /**
     * Normalize course name (fixing typos, aliases, trailing spaces).
     */
    public function normalizeCourseName(string $name): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $name));
        $lower = strtolower($clean);

        if (isset($this->courseAliases[$lower])) {
            return $this->courseAliases[$lower];
        }

        // Specific partial matching
        foreach ($this->courseAliases as $aliasKey => $canonical) {
            if ($lower === $aliasKey || str_starts_with($lower, $aliasKey)) {
                return $canonical;
            }
        }

        return $clean;
    }
}
