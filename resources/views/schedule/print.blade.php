<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Jadwal Praktikum & Responsi - {{ $period->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            color: #0f172a;
            font-size: 10px;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }
        .header p {
            font-size: 11px;
            color: #64748b;
            margin: 0;
        }
        .page-break {
            page-break-after: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px;
            text-align: center;
            vertical-align: top;
            overflow: hidden;
            word-wrap: break-word;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .time-col {
            width: 70px;
            background-color: #f8fafc;
            font-weight: 600;
        }
        .block-kelas {
            background-color: #e2e8f0;
            border: 1px solid #94a3b8;
            border-radius: 4px;
            padding: 3px;
            margin-bottom: 2px;
            font-size: 9px;
            text-align: left;
        }
        .block-prak {
            background-color: #d1fae5;
            border: 1px solid #34d399;
            border-radius: 4px;
            padding: 3px;
            margin-bottom: 2px;
            font-size: 9px;
            text-align: left;
        }
        .block-resp {
            background-color: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 4px;
            padding: 3px;
            margin-bottom: 2px;
            font-size: 9px;
            text-align: left;
        }
        .block-forbidden {
            background-color: #f1f5f9;
            background-image: repeating-linear-gradient(45deg, #e2e8f0, #e2e8f0 5px, #f1f5f9 5px, #f1f5f9 10px);
            color: #94a3b8;
            font-style: italic;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Cetak Dokumen (Ctrl + P)
        </button>
    </div>

    @foreach($days as $dayIndex => $day)
        <div class="day-section {{ $dayIndex < count($days) - 1 ? 'page-break' : '' }}">
            <div class="header">
                <h1>JADWAL PRAKTIKUM & RESPONSI — HARI {{ strtoupper($day) }}</h1>
                <p>Periode: {{ $period->name }} | Laboratorium Teknik Informatika</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="time-col">Jam</th>
                        @foreach($rooms as $room)
                            <th>
                                <div>{{ $room->code }}</div>
                                <div style="font-weight: normal; font-size: 8px; color: #64748b;">{{ $room->name }} ({{ $room->capacity }})</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($slotsByDay[$day] as $slot)
                        <tr style="height: 40px;">
                            <td class="time-col">{{ $slot['label'] }}</td>
                            @foreach($rooms as $room)
                                @if(!empty($slot['forbidden']))
                                    <td class="block-forbidden">Jam KU / Jam Kemahasiswaan</td>
                                @else
                                    <td>
                                        {{-- Class meetings --}}
                                        @foreach($classMeetings->where('room_id', $room->id)->where('day', $day)->where('start_min', $slot['start']) as $meeting)
                                            <div class="block-kelas">
                                                <strong>{{ $meeting->section->course->name }}</strong><br>
                                                Kelas {{ $meeting->section->label }} ({{ sprintf('%02d:%02d-%02d:%02d', floor($meeting->start_min / 60), $meeting->start_min % 60, floor($meeting->end_min / 60), $meeting->end_min % 60) }})
                                            </div>
                                        @endforeach

                                        {{-- Lab sessions --}}
                                        @foreach($placedSessions->where('room_id', $room->id)->where('day', $day)->where('start_min', $slot['start']) as $session)
                                            <div class="{{ $session->type === 'praktikum' ? 'block-prak' : 'block-resp' }}">
                                                <strong>{{ $session->section->course->name }}</strong><br>
                                                {{ ucfirst($session->type) }} {{ $session->section->label }}
                                                ({{ sprintf('%02d:%02d-%02d:%02d', floor($session->start_min / 60), $session->start_min % 60, floor($session->end_min / 60), $session->end_min % 60) }})
                                                @if($session->is_locked) [Kunci] @endif
                                            </div>
                                        @endforeach
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
