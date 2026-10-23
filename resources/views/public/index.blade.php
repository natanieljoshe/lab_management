<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Praktikum & Responsi - {{ $period->name }}</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-full { width: 100% !important; overflow: visible !important; }
            body { background: white !important; color: black !important; font-size: 9px !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col font-sans antialiased">

<div x-data="publicScheduleManager()" x-init="init()" class="min-h-screen flex flex-col w-full">
    <!-- Floating button to restore navbar when closed / in full screen mode -->
    <div x-show="!showNavbar" x-cloak class="fixed top-3 right-4 z-50 no-print flex items-center space-x-2">
        <a href="{{ $pathUrl }}" 
           class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white shadow-xl shadow-purple-600/30 transition transform hover:scale-105 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
            <span>View Path</span>
        </a>

        <button @click="showNavbar = true" 
                class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xl shadow-indigo-600/30 transition transform hover:scale-105 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span>Buka Filter & Menu</span>
        </button>
    </div>

    <!-- Top Collapsible Navbar containing Branding, Actions, and All Filters -->
    <header x-show="showNavbar" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30 no-print">
        
        <div class="w-full px-3 sm:px-6 py-2.5 space-y-2.5">
            <!-- Top Row: Branding, Period, View Path, Download Excel, Close Navbar Toggle -->
            <div class="flex flex-wrap items-center justify-between gap-2.5">
                <!-- Branding -->
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold shadow">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h1 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">Jadwal Praktikum & Responsi</h1>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                Portal Mahasiswa
                            </span>
                            @if(isset($currentVersion) && $currentVersion)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700">
                                    Draft: {{ $currentVersion->name }}
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500">Laboratorium Teknik Informatika &bull; Periode {{ $period->name }}</p>
                    </div>
                </div>

                <!-- Right Actions: Period Picker, View Path, Excel Export, Close Navbar -->
                <div class="flex items-center space-x-2">
                    <!-- Period Filter Dropdown -->
                    <div class="relative" x-data="{ openPeriod: false }">
                        <button @click="openPeriod = !openPeriod" 
                                class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>{{ $period->name }}</span>
                            <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="openPeriod" @click.outside="openPeriod = false" x-cloak
                             class="absolute right-0 mt-2 w-56 rounded-xl shadow-xl bg-white border border-slate-200 py-1 z-50">
                            <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pilih Periode Semester</div>
                            @foreach($allPeriods as $p)
                                <a href="{{ route('public.schedule', ['period_id' => $p->id]) }}" 
                                   class="px-3 py-2 text-xs flex items-center justify-between hover:bg-slate-50 transition {{ $p->id === $period->id ? 'font-bold text-indigo-600 bg-indigo-50/50' : 'text-slate-700' }}">
                                    <span>{{ $p->name }}</span>
                                    @if($p->id === $period->id)
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- View Path Button (Navigates to /{slug}/path or /public/path) -->
                    <a href="{{ $pathUrl }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-purple-300 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 shadow-sm transition">
                        <svg class="w-4 h-4 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        <span>View Path</span>
                    </a>

                    <!-- Download Excel Button -->
                    <a href="{{ route('export.excel', ['period_id' => $period->id]) }}" 
                       class="inline-flex items-center px-3 py-1.5 border border-emerald-300 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 shadow-sm transition">
                        <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Unduh Excel</span>
                    </a>

                    <!-- Close Navbar (Full Screen Schedule Grid Mode) -->
                    <button @click="showNavbar = false" 
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition cursor-pointer"
                            title="Tutup Filter untuk Mode Layar Penuh">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                        <span>Tutup Filter (Layar Penuh)</span>
                    </button>
                </div>
            </div>

            <!-- Second Row: Day Tabs & Format Indicator -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-100">
                <!-- Day Selector Tabs: Semua Hari, Senin, Selasa, Rabu, Kamis, Jumat -->
                <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl overflow-x-auto">
                    <button @click="activeDay = 'all'" 
                            class="px-3 py-1 rounded-lg text-xs font-semibold transition flex-shrink-0 flex items-center space-x-1 cursor-pointer"
                            :class="activeDay === 'all' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span>Semua Hari</span>
                    </button>
                    <template x-for="day in days" :key="day">
                        <button @click="activeDay = day" 
                                class="px-3 py-1 rounded-lg text-xs font-semibold transition flex-shrink-0 cursor-pointer"
                                :class="activeDay === day ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900'"
                                x-text="day">
                        </button>
                    </template>
                </div>

                <!-- Format Indicator Label -->
                <div class="text-[11px] text-slate-500 font-medium">
                    <span>Format Tampilan: <strong class="text-slate-700">Grid Matriks Ruangan & Jam</strong></span>
                </div>
            </div>

            <!-- Third Row: Search, Semester, Jenis, Ruangan Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 pt-1">
                <!-- Cari matakuliah / seksi... -->
                <div class="relative">
                    <input type="text" x-model="searchQuery" placeholder="Cari matakuliah / seksi..." 
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-800 focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Semua Semester -->
                <div>
                    <select x-model="selectedSemester" class="w-full py-1.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-800">
                        <option value="">Semua Semester</option>
                        <option value="1">Semester 1</option>
                        <option value="3">Semester 3</option>
                        <option value="5">Semester 5</option>
                        <option value="7">Semester 7</option>
                    </select>
                </div>

                <!-- Semua Jenis (Praktikum, Responsi, Kuliah) -->
                <div>
                    <select x-model="selectedType" class="w-full py-1.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-800">
                        <option value="">Semua Jenis (Praktikum, Responsi, Kuliah)</option>
                        <option value="praktikum">Hanya Praktikum</option>
                        <option value="responsi">Hanya Responsi</option>
                        <option value="kelas">Hanya Kelas Kuliah</option>
                    </select>
                </div>

                <!-- Semua Ruangan Lab -->
                <div>
                    <select x-model="selectedRoom" class="w-full py-1.5 text-xs rounded-lg border border-slate-300 bg-white text-slate-800">
                        <option value="">Semua Ruangan Lab</option>
                        <template x-for="r in rooms" :key="r.id">
                            <option :value="r.id" x-text="r.code + ' (' + r.name + ')'"></option>
                        </template>
                    </select>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Public Container (Full width spreadsheet layout) -->
    <main class="flex-1 w-full px-2 sm:px-4 py-3 space-y-3">
        <!-- Compact Legend Bar & Table Controls -->
        <div class="flex flex-wrap items-center justify-between gap-3 text-[11px] font-medium px-1 text-slate-600 no-print">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded bg-slate-200 border border-slate-400 inline-block"></span>
                    <span>Kelas Kuliah</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded bg-emerald-100 border border-emerald-400 inline-block"></span>
                    <span>Praktikum</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded bg-amber-100 border border-amber-400 inline-block"></span>
                    <span>Responsi</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <span class="w-3.5 h-3.5 rounded bg-[repeating-linear-gradient(45deg,#e2e8f0,#e2e8f0_4px,#cbd5e1_4px,#cbd5e1_8px)] border border-slate-400 inline-block"></span>
                    <span>Jam KU / Jam Kemahasiswaan</span>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Reset Column/Row Widths button if modified -->
                <button x-show="hasCustomDimensions" @click="resetDimensions()" 
                        class="text-[10px] text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 transition">
                    Reset Ukuran Baris & Kolom
                </button>
                <span class="text-slate-400 text-[10px] hidden md:inline">
                    Tips: Geser garis batas kolom / baris untuk menyesuaikan lebar seperti di Google Sheets / Excel.
                </span>
            </div>
        </div>

        <!-- GRID VIEW (Stacked by Day when 'Semua Hari' or single day) -->
        <div class="space-y-4">
            <template x-for="dayName in displayDays" :key="dayName">
                <div class="bg-white rounded-xl shadow-sm border border-slate-300 overflow-hidden flex flex-col">
                    <!-- Day Section Header -->
                    <div class="bg-slate-200/90 px-3 py-2 border-b border-slate-300 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider" x-text="'HARI ' + dayName"></h2>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600" 
                              x-text="getDaySessionCount(dayName) + ' Sesi Terjadwal'"></span>
                    </div>

                    <!-- Google Sheets Compact Grid Table with Resizable Columns & Rows -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left border border-slate-300 table-fixed">
                            <!-- Column Room Headers -->
                            <thead class="bg-slate-100 border-b border-slate-300">
                                <tr>
                                    <th class="w-20 px-2 py-1 text-[11px] font-bold text-slate-600 uppercase text-center border-r border-slate-300 select-none">
                                        Jam
                                    </th>
                                    <template x-for="room in filteredRooms" :key="room.id">
                                        <th :id="'col-th-' + dayName + '-' + room.id"
                                            :style="colWidths[room.id] ? `width: ${colWidths[room.id]}px; min-width: ${colWidths[room.id]}px; max-width: ${colWidths[room.id]}px;` : 'width: 176px; min-width: 176px;'"
                                            class="relative select-none border-r border-slate-300 text-center px-1.5 py-1 text-xs font-bold overflow-hidden"
                                            :class="room.type === 'internal' ? 'bg-slate-100' : 'bg-slate-200/60'">
                                            
                                            <!-- Room info -->
                                            <div :class="colWidths[room.id] && colWidths[room.id] < 40 ? 'hidden' : ''">
                                                <div class="font-bold text-slate-800 leading-tight" x-text="room.code"></div>
                                                <div class="text-[9px] text-slate-500 font-normal truncate leading-tight" x-text="room.name"></div>
                                                <div class="text-[9px] text-slate-400 font-mono leading-tight mt-0.5" x-text="'Kap: ' + room.capacity"></div>
                                            </div>
                                            <div x-show="colWidths[room.id] && colWidths[room.id] < 40" class="text-[8px] text-slate-400 truncate">
                                                ...
                                            </div>

                                            <!-- Draggable Column Resizer handle (Google Sheets / Excel style) -->
                                            <div @mousedown="startColResize($event, room.id)"
                                                 @touchstart.passive="startColResize($event, room.id)"
                                                 title="Geser garis kolom untuk memperbesar atau memperkecil"
                                                 class="absolute right-0 top-0 bottom-0 w-2 hover:w-2.5 cursor-col-resize hover:bg-indigo-500/50 active:bg-indigo-600 transition-colors z-20 flex items-center justify-center">
                                            </div>
                                        </th>
                                    </template>
                                </tr>
                            </thead>

                            <!-- Time Slots Rows (Google Sheets Compact Row Height & Resizable) -->
                            <tbody class="divide-y divide-slate-300">
                                <template x-for="(slot, slotIndex) in (slotsByDay[dayName] || [])" :key="slotIndex">
                                    <tr :id="'slot-tr-' + dayName + '-' + slotIndex"
                                        :style="rowHeights[slotIndex] ? `height: ${rowHeights[slotIndex]}px;` : ''"
                                        class="h-8 min-h-[32px] border-b border-slate-300 relative" 
                                        :class="{
                                            'bg-[repeating-linear-gradient(45deg,#f8fafc,#f8fafc_6px,#f1f5f9_6px,#f1f5f9_12px)]': slot.forbidden,
                                            'bg-amber-50/20': slot.manual_only && !slot.forbidden
                                        }">
                                        
                                        <!-- Time Slot Label with Row Resizer Handle -->
                                        <td class="w-20 px-1 py-0.5 text-[10px] font-semibold text-slate-600 text-center border-r border-slate-300 bg-slate-50 select-none whitespace-nowrap relative">
                                            <span x-text="slot.label"></span>

                                            <!-- Draggable Row Resizer handle -->
                                            <div @mousedown="startRowResize($event, slotIndex)"
                                                 @touchstart.passive="startRowResize($event, slotIndex)"
                                                 title="Geser garis baris untuk memperlebar atau merapatkan baris"
                                                 class="absolute left-0 right-0 bottom-0 h-1.5 hover:h-2 cursor-row-resize hover:bg-indigo-500/50 active:bg-indigo-600 transition-colors z-20">
                                            </div>
                                        </td>

                                        <!-- Room Cells -->
                                        <template x-for="room in filteredRooms" :key="room.id">
                                            <td :style="colWidths[room.id] ? `width: ${colWidths[room.id]}px; min-width: ${colWidths[room.id]}px; max-width: ${colWidths[room.id]}px;` : 'width: 176px; min-width: 176px;'"
                                                class="p-0.5 border-r border-slate-300 relative align-top bg-white overflow-hidden">
                                                
                                                <!-- Jam KU / Jam Kemahasiswaan Notice -->
                                                <template x-if="slot.forbidden">
                                                    <div class="h-full min-h-[26px] w-full flex items-center justify-center text-[9px] text-slate-400 font-semibold italic select-none">
                                                        <span :class="colWidths[room.id] && colWidths[room.id] < 60 ? 'hidden' : ''">Jam KU / Jam Kemahasiswaan</span>
                                                    </div>
                                                </template>

                                                <template x-if="!slot.forbidden">
                                                    <div>
                                                        <!-- Placed Lab Sessions in this Slot (Multi-block occupancy) -->
                                                        <template x-for="session in getFilteredSessionsInSlot(dayName, room.id, slot)" :key="'session-' + session.id">
                                                            <div @click="openSessionModal(session, 'lab')"
                                                                 class="px-1.5 py-0.5 rounded border text-[10px] leading-tight cursor-pointer select-none mb-0.5 transition shadow-xs hover:shadow hover:scale-[1.01]"
                                                                 :class="session.type === 'praktikum' ? 'bg-emerald-100 border-emerald-400 text-emerald-950' : 'bg-amber-100 border-amber-400 text-amber-950'">
                                                                <div class="font-bold truncate text-[10px] leading-tight" x-text="session.section.course.name"></div>
                                                                <div class="flex items-center justify-between text-[9px] opacity-90 leading-tight"
                                                                     :class="colWidths[room.id] && colWidths[room.id] < 80 ? 'hidden' : ''">
                                                                    <span x-text="session.type.toUpperCase() + ' ' + session.section.label"></span>
                                                                    <span class="font-mono text-[9px]" x-text="formatTime(session.start_min) + '-' + formatTime(session.end_min)"></span>
                                                                </div>
                                                            </div>
                                                        </template>

                                                        <!-- Class Meetings in this Slot (Multi-block occupancy) -->
                                                        <template x-for="meeting in getFilteredMeetingsInSlot(dayName, room.id, slot)" :key="'meeting-' + meeting.id">
                                                            <div @click="openSessionModal(meeting, 'meeting')"
                                                                 class="px-1.5 py-0.5 rounded border border-slate-300 bg-slate-200/90 text-slate-900 text-[10px] leading-tight shadow-xs mb-0.5 cursor-pointer hover:bg-slate-300 transition">
                                                                <div class="font-bold truncate text-[10px] leading-tight" x-text="meeting.section.course.name"></div>
                                                                <div class="flex items-center justify-between text-[9px] text-slate-600 leading-tight"
                                                                     :class="colWidths[room.id] && colWidths[room.id] < 80 ? 'hidden' : ''">
                                                                    <span x-text="'Kelas ' + meeting.section.label"></span>
                                                                    <span class="font-mono text-[9px]" x-text="formatTime(meeting.start_min) + '-' + formatTime(meeting.end_min)"></span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- Session Detail Modal -->
    <div x-cloak x-show="modalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @keydown.escape.window="modalOpen = false">
        <template x-if="selectedSession">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4"
                 @click.outside="modalOpen = false">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                              :class="{
                                  'bg-emerald-100 text-emerald-800': selectedSession.type === 'praktikum',
                                  'bg-amber-100 text-amber-800': selectedSession.type === 'responsi',
                                  'bg-slate-200 text-slate-800': !selectedSession.type
                              }"
                              x-text="(selectedSession.type ? selectedSession.type.toUpperCase() : 'KELAS KULIAH') + ' ' + selectedSession.section.label">
                        </span>
                        <h3 class="font-bold text-base text-slate-900 mt-1" x-text="selectedSession.section.course.name"></h3>
                        <p class="text-xs text-slate-400 font-mono" x-text="selectedSession.section.course.code"></p>
                    </div>
                    <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Waktu Pelaksanaan:</span>
                        <span class="font-bold text-slate-900" x-text="selectedSession.day + ', ' + formatTime(selectedSession.start_min) + ' - ' + formatTime(selectedSession.end_min) + ' WIB'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Ruangan:</span>
                        <span class="font-bold text-indigo-600" x-text="selectedSession.room ? selectedSession.room.code + ' (' + selectedSession.room.name + ')' : (selectedSession.room_label || '-')"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Semester Kurikulum:</span>
                        <span class="font-semibold text-slate-800" x-text="'Semester ' + selectedSession.section.course.curriculum_semester"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Peserta Terdaftar:</span>
                        <span class="font-semibold text-slate-800" x-text="selectedSession.section.enrollment + ' Mahasiswa'"></span>
                    </div>
                </div>

                <!-- Lecture info if practical session -->
                <template x-if="selectedSession.section && selectedSession.section.meetings && selectedSession.section.meetings.length > 0">
                    <div class="text-xs space-y-1">
                        <span class="font-semibold text-slate-600">Jadwal Kuliah Teori Terkait:</span>
                        <template x-for="m in selectedSession.section.meetings" :key="m.id">
                            <div class="p-2 rounded bg-slate-100 flex justify-between">
                                <span x-text="m.day + ', ' + formatTime(m.start_min) + '-' + formatTime(m.end_min)"></span>
                                <span class="font-semibold" x-text="m.room_label || (m.room ? m.room.code : '-')"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex justify-end pt-2">
                    <button @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- Footer -->
    <footer class="mt-auto py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white no-print">
        <p>Laboratorium Teknik Informatika &copy; {{ date('Y') }} — Jadwal Resmi Periode {{ $period->name }}</p>
    </footer>
</div>

<script>
function publicScheduleManager() {
    return {
        showNavbar: true,
        days: @json($days),
        activeDay: 'all',
        rooms: @json($rooms),
        slotsByDay: @json($slotsByDay),
        placedSessions: @json($placedSessions),
        classMeetings: @json($classMeetings),

        searchQuery: '',
        selectedSemester: '',
        selectedType: '',
        selectedRoom: '',

        modalOpen: false,
        selectedSession: null,

        // Ephemeral Google Sheets / Excel Drag Resize State
        colWidths: {},
        rowHeights: {},
        isResizingCol: false,
        resizingColRoomId: null,
        colStartX: 0,
        colStartWidth: 0,
        isResizingRow: false,
        resizingRowIndex: null,
        rowStartY: 0,
        rowStartHeight: 0,

        init() {
            // initial setup
        },

        get hasCustomDimensions() {
            return Object.keys(this.colWidths).length > 0 || Object.keys(this.rowHeights).length > 0;
        },

        resetDimensions() {
            this.colWidths = {};
            this.rowHeights = {};
        },

        startColResize(e, roomId) {
            e.preventDefault();
            e.stopPropagation();
            this.isResizingCol = true;
            this.resizingColRoomId = roomId;
            this.colStartX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            
            // Find current width
            const firstHeader = document.querySelector(`[id^='col-th-'][id$='-${roomId}']`);
            this.colStartWidth = firstHeader ? firstHeader.offsetWidth : 176;

            const onMove = (ev) => {
                if (!this.isResizingCol) return;
                const currentX = ev.type.includes('touch') ? ev.touches[0].clientX : ev.clientX;
                const diff = currentX - this.colStartX;
                const newWidth = Math.max(16, this.colStartWidth + diff);
                this.colWidths[this.resizingColRoomId] = newWidth;
            };

            const onUp = () => {
                this.isResizingCol = false;
                this.resizingColRoomId = null;
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onUp);
                window.removeEventListener('touchmove', onMove);
                window.removeEventListener('touchend', onUp);
            };

            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onUp);
            window.addEventListener('touchmove', onMove);
            window.addEventListener('touchend', onUp);
        },

        startRowResize(e, slotIndex) {
            e.preventDefault();
            e.stopPropagation();
            this.isResizingRow = true;
            this.resizingRowIndex = slotIndex;
            this.rowStartY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;

            const firstRow = document.querySelector(`[id^='slot-tr-'][id$='-${slotIndex}']`);
            this.rowStartHeight = firstRow ? firstRow.offsetHeight : 32;

            const onMove = (ev) => {
                if (!this.isResizingRow) return;
                const currentY = ev.type.includes('touch') ? ev.touches[0].clientY : ev.clientY;
                const diff = currentY - this.rowStartY;
                const newHeight = Math.max(22, this.rowStartHeight + diff);
                this.rowHeights[this.resizingRowIndex] = newHeight;
            };

            const onUp = () => {
                this.isResizingRow = false;
                this.resizingRowIndex = null;
                window.removeEventListener('mousemove', onMove);
                window.removeEventListener('mouseup', onUp);
                window.removeEventListener('touchmove', onMove);
                window.removeEventListener('touchend', onUp);
            };

            window.addEventListener('mousemove', onMove);
            window.addEventListener('mouseup', onUp);
            window.addEventListener('touchmove', onMove);
            window.addEventListener('touchend', onUp);
        },

        get displayDays() {
            if (this.activeDay === 'all') {
                return this.days;
            }
            return [this.activeDay];
        },

        get filteredRooms() {
            if (!this.selectedRoom) return this.rooms;
            return this.rooms.filter(r => r.id === parseInt(this.selectedRoom));
        },

        formatTime(minutes) {
            if (!minutes) return '';
            const h = Math.floor(minutes / 60);
            const m = minutes % 60;
            return (h < 10 ? '0' : '') + h + '.' + (m < 10 ? '0' : '') + m;
        },

        matchesFilter(course, type) {
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                const name = (course.name || '').toLowerCase();
                const code = (course.code || '').toLowerCase();
                if (!name.includes(q) && !code.includes(q)) return false;
            }
            if (this.selectedSemester) {
                if (parseInt(course.curriculum_semester) !== parseInt(this.selectedSemester)) return false;
            }
            if (this.selectedType) {
                if (this.selectedType !== type) return false;
            }
            return true;
        },

        // Multi-block occupancy: session spans any slot it overlaps with
        getFilteredSessionsInSlot(day, roomId, slot) {
            return this.placedSessions.filter(s => {
                if (s.room_id !== roomId || s.day !== day) return false;
                if (s.start_min >= slot.end || s.end_min <= slot.start) return false;
                return this.matchesFilter(s.section.course, s.type);
            });
        },

        // Multi-block occupancy: meeting spans any slot it overlaps with
        getFilteredMeetingsInSlot(day, roomId, slot) {
            return this.classMeetings.filter(m => {
                if (m.room_id !== roomId || m.day !== day) return false;
                if (m.start_min >= slot.end || m.end_min <= slot.start) return false;
                return this.matchesFilter(m.section.course, 'kelas');
            });
        },

        getDaySessionCount(day) {
            const countPlaced = this.placedSessions.filter(s => s.day === day).length;
            const countMeetings = this.classMeetings.filter(m => m.day === day).length;
            return countPlaced + countMeetings;
        },

        openSessionModal(session, kind) {
            this.selectedSession = session;
            this.modalOpen = true;
        }
    };
}
</script>
</body>
</html>
