<?php

namespace App\Services\Excel;

use App\Models\ClassMeeting;
use App\Models\LabSession;
use App\Models\Labs as Room;
use App\Models\Period;
use App\Models\Schedule;
use App\Services\Scheduling\PathCalculator;
use App\Services\Scheduling\SlotCalendar;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleExportService
{
    public function __construct(
        protected PathCalculator $pathCalculator,
        protected SlotCalendar $slotCalendar
    ) {}

    /**
     * Export the schedule as a streamed Xlsx response.
     */
    public function export(Period $period): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;

        // Sheet 1: Grid Jadwal (Matriks Kotak-kotak Ruangan x Waktu per Hari)
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Grid Jadwal');
        $this->buildGridSheet($sheet1, $period);

        // Sheet 2: Daftar Sesi
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Daftar Sesi');
        $this->buildJadwalSheet($sheet2, $period);

        // Sheet 3: Belum Terjadwal
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Belum Terjadwal');
        $this->buildBelumTerjadwalSheet($sheet3, $period);

        // Sheet 4: Path Mahasiswa
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle('Path Mahasiswa');
        $this->buildPathSheet($sheet4, $period);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Jadwal_Praktikum_'.str_replace(['/', ' '], '_', $period->name).'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Build visual matrix/grid sheet (kotak-kotak per hari).
     */
    protected function buildGridSheet($sheet, Period $period): void
    {
        $rooms = Room::where('is_active', true)
            ->get()
            ->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1)
            ->values();

        $days = $this->slotCalendar->getDays();

        $schedule = Schedule::where('period_id', $period->id)->where('is_active', true)->first();
        $scheduleId = $schedule?->id;

        // Placed lab sessions
        $placedSessions = $scheduleId
            ? LabSession::where('schedule_id', $scheduleId)
                ->where('status', 'SCHEDULED')
                ->with(['section.course', 'room'])
                ->get()
            : collect([]);

        // Class meetings occupying rooms
        $classMeetings = ClassMeeting::whereHas('classSection', fn ($q) => $q->where('period_id', $period->id))
            ->whereNotNull('lab_id')
            ->with(['section.course', 'room'])
            ->get();

        $lastColIndex = count($rooms) + 1;
        $lastColLetter = Coordinate::stringFromColumnIndex($lastColIndex);

        // Set column A width (Time column)
        $sheet->getColumnDimension('A')->setWidth(18);

        // Set Room columns width
        for ($c = 2; $c <= $lastColIndex; $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth(24);
        }

        $currentRow = 1;

        foreach ($days as $day) {
            $slots = $this->slotCalendar->getDaySlots($day);

            // 1. Day Banner Header
            $sheet->mergeCells("A{$currentRow}:{$lastColLetter}{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", "HARI " . strtoupper($day) . " — JADWAL PRAKTIKUM & RESPONSI ({$period->name})");
            $sheet->getRowDimension($currentRow)->setRowHeight(28);
            $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $currentRow++;

            // 2. Room Column Headers
            $sheet->setCellValue("A{$currentRow}", 'Jam');
            $sheet->getStyle("A{$currentRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '334155'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);

            $colIdx = 2;
            foreach ($rooms as $room) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                $roomText = "{$room->code}\n{$room->name}\n(Kap: {$room->capacity})";
                $sheet->setCellValue("{$colLetter}{$currentRow}", $roomText);
                $sheet->getStyle("{$colLetter}{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1E293B'], 'size' => 9],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $room->type === 'internal' ? 'F8FAFC' : 'E2E8F0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
                $colIdx++;
            }
            $sheet->getRowDimension($currentRow)->setRowHeight(42);

            $dayStartRow = $currentRow - 1;
            $currentRow++;

            // 3. Time Slots Rows
            foreach ($slots as $slot) {
                $sheet->setCellValue("A{$currentRow}", $slot['label']);
                $sheet->getStyle("A{$currentRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '475569'], 'size' => 8.5],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(28);

                $colIdx = 2;
                foreach ($rooms as $room) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                    $cellCoord = "{$colLetter}{$currentRow}";

                    if (! empty($slot['forbidden'])) {
                        $sheet->setCellValue($cellCoord, 'Jam KU / Jam Kemahasiswaan');
                        $sheet->getStyle($cellCoord)->applyFromArray([
                            'font' => ['italic' => true, 'color' => ['rgb' => '94A3B8'], 'size' => 7.5, 'bold' => true],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        ]);
                    } else {
                        // Check placed lab sessions that overlap this slot
                        $sessionsInSlot = $placedSessions->filter(fn ($s) => 
                            $s->day === $day &&
                            $s->lab_id === $room->id &&
                            $s->start_min < $slot['end'] &&
                            $s->end_min > $slot['start']
                        );

                        // Check class meetings that overlap this slot
                        $meetingsInSlot = $classMeetings->filter(fn ($m) => 
                            $m->day === $day &&
                            $m->lab_id === $room->id &&
                            $m->start_min < $slot['end'] &&
                            $m->end_min > $slot['start']
                        );

                        $items = [];
                        $bgColor = null;
                        $textColor = null;

                        foreach ($meetingsInSlot as $m) {
                            $items[] = "[KELAS] {$m->section->course->name} ({$m->section->label})\n"
                                . $this->formatMinutes($m->start_min) . ' - ' . $this->formatMinutes($m->end_min);
                            $bgColor = 'E2E8F0';
                            $textColor = '1E293B';
                        }

                        foreach ($sessionsInSlot as $s) {
                            $typeLabel = strtoupper($s->type);
                            $items[] = "[{$typeLabel}] {$s->section->course->name} ({$s->section->label})\n"
                                . $this->formatMinutes($s->start_min) . ' - ' . $this->formatMinutes($s->end_min)
                                . ' (' . $s->section->enrollment . ' Mhs)';

                            if ($s->type === 'praktikum') {
                                $bgColor = 'D1FAE5';
                                $textColor = '065F46';
                            } else {
                                $bgColor = 'FEF3C7';
                                $textColor = '92400E';
                            }
                        }

                        if (! empty($items)) {
                            $sheet->setCellValue($cellCoord, implode("\n---\n", $items));
                            $sheet->getStyle($cellCoord)->applyFromArray([
                                'font' => ['bold' => true, 'color' => ['rgb' => $textColor ?? '1E293B'], 'size' => 8],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor ?? 'FFFFFF']],
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                            ]);
                        } else {
                            $sheet->getStyle($cellCoord)->applyFromArray([
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            ]);
                        }
                    }

                    $colIdx++;
                }

                $currentRow++;
            }

            // Apply all borders for this day's grid table
            $dayEndRow = $currentRow - 1;
            $sheet->getStyle("A{$dayStartRow}:{$lastColLetter}{$dayEndRow}")
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->getColor()
                ->setRGB('CBD5E1');

            // 2 blank rows between days
            $currentRow += 2;
        }
    }

    protected function buildJadwalSheet($sheet, Period $period): void
    {
        $headers = ['No', 'Mata Kuliah', 'Seksi', 'Jenis Kegiatan', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Ruangan', 'Status'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Class meetings
        $meetings = ClassMeeting::whereHas('classSection', fn ($q) => $q->where('period_id', $period->id))
            ->with(['section.course', 'room'])
            ->get();

        $rows = [];
        $no = 1;
        foreach ($meetings as $m) {
            $roomName = $m->room ? $m->room->name : ($m->room_label ?: 'Ruang Kelas');
            $rows[] = [
                $no++,
                $m->section?->course?->name ?? 'Mata Kuliah',
                $m->section?->label ?? '',
                'Kelas',
                $m->day,
                $this->formatMinutes($m->start_min),
                $this->formatMinutes($m->end_min),
                $roomName,
                'Tetap (Kelas)',
            ];
        }

        // Lab sessions (placed)
        $schedule = Schedule::where('period_id', $period->id)->where('is_active', true)->first();
        $sessions = $schedule
            ? LabSession::where('schedule_id', $schedule->id)
                ->where('status', 'SCHEDULED')
                ->with(['section.course', 'room'])
                ->get()
            : collect([]);

        foreach ($sessions as $s) {
            $rows[] = [
                $no++,
                $s->section?->course?->name ?? 'Mata Kuliah',
                $s->section?->label ?? '',
                ucfirst($s->type),
                $s->day,
                $this->formatMinutes($s->start_min),
                $this->formatMinutes($s->end_min),
                $s->room ? $s->room->name : 'Lab',
                $s->is_locked ? 'Terkunci' : ($s->is_manual_placed ? 'Manual' : 'Otomatis'),
            ];
        }

        if (! empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        $highestRow = max(1, count($rows) + 1);
        $sheet->getStyle("A1:I{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected function buildBelumTerjadwalSheet($sheet, Period $period): void
    {
        $headers = ['No', 'Mata Kuliah', 'Seksi', 'Jenis Kegiatan', 'Durasi (Menit)', 'Alasan Belum Terjadwal'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B91C1C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        $schedule = Schedule::where('period_id', $period->id)->where('is_active', true)->first();
        $unscheduled = $schedule
            ? LabSession::where('schedule_id', $schedule->id)
                ->where('status', 'UNSCHEDULED')
                ->with(['section.course'])
                ->get()
            : collect([]);

        $rows = [];
        $no = 1;
        foreach ($unscheduled as $u) {
            $rows[] = [
                $no++,
                $u->section?->course?->name ?? 'Mata Kuliah',
                $u->section?->label ?? '',
                ucfirst($u->type),
                $u->duration_min,
                $u->unscheduled_reason ?? 'Belum dijadwalkan',
            ];
        }

        if (! empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        $highestRow = max(1, count($rows) + 1);
        $sheet->getStyle("A1:F{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected function buildPathSheet($sheet, Period $period): void
    {
        $headers = ['Semester', 'No Path', 'Kombinasi Seksi', 'Status Validasi'];
        $sheet->fromArray($headers, null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $rows = [];
        foreach ([1, 3, 5, 7] as $sem) {
            $res = $this->pathCalculator->calculatePathsForSemester($period, $sem);
            $pathIdx = 1;
            foreach ($res['paths'] as $p) {
                $combination = [];
                foreach ($p['sections'] as $sec) {
                    $combination[] = "{$sec['course_name']} ({$sec['label']})";
                }
                $rows[] = [
                    "Semester $sem",
                    "Path #$pathIdx",
                    implode(' + ', $combination),
                    'Valid (Bebas Bentrok)',
                ];
                $pathIdx++;
            }
            if (empty($res['paths'])) {
                $rows[] = [
                    "Semester $sem",
                    '-',
                    'Tidak ditemukan kombinasi bebas bentrok',
                    implode(', ', $res['warnings']),
                ];
            }
        }

        if (! empty($rows)) {
            $sheet->fromArray($rows, null, 'A2');
        }

        $highestRow = max(1, count($rows) + 1);
        $sheet->getStyle("A1:D{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected function formatMinutes(int $minutes): string
    {
        return sprintf('%02d:%02d', floor($minutes / 60), $minutes % 60);
    }
}
