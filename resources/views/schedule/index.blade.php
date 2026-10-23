@extends('layouts.app')

@section('title', 'Jadwal Praktikum & Responsi - ' . $period->name)
@section('page_title', 'Grid Jadwal Praktikum & Responsi')
@section('active_period', $period->name)

@section('content')
<div x-data="scheduleManager()" x-init="init()" class="h-full flex flex-col p-4 space-y-4">
    <!-- Toolbar -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
        <!-- Day Selector Tabs -->
        <div class="flex items-center space-x-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
            <button @click="activeDay = 'all'" 
                    class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center space-x-1.5"
                    :class="activeDay === 'all' ? 'bg-indigo-600 text-white shadow-sm font-bold' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                <span>Semua Hari</span>
            </button>
            <template x-for="day in days" :key="day">
                <button @click="activeDay = day" 
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition"
                        :class="activeDay === day ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'"
                        x-text="day">
                </button>
            </template>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
            <button @click="runGenerate()" :disabled="isLoading" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition disabled:opacity-50">
                <svg x-show="!isLoading" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span x-text="isLoading ? 'Menjadwalkan...' : 'Generate Otomatis'"></span>
            </button>

            <button @click="showResetModal = true" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Reset
            </button>

            <button @click="showSaveVersionModal = true" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Simpan Versi
            </button>

            <button @click="togglePathPanel()" 
                    class="inline-flex items-center px-3.5 py-2 rounded-lg border text-xs font-semibold shadow-sm transition"
                    :class="showPathPanel ? 'bg-indigo-50 border-indigo-400 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200'">
                <svg class="w-4 h-4 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                Panel Path
            </button>
        </div>
    </div>

    <!-- Legend Bar -->
    <div class="flex flex-wrap items-center gap-4 text-xs font-medium px-2 py-1 text-slate-600 dark:text-slate-400">
        <div class="flex items-center space-x-1.5">
            <span class="w-3.5 h-3.5 rounded bg-slate-200 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 inline-block"></span>
            <span>Kelas (Tetap dari Atasan)</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-3.5 h-3.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 inline-block"></span>
            <span>Kolom Non-Lab (Jadwal Kelas Teori)</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-3.5 h-3.5 rounded bg-emerald-100 dark:bg-emerald-900/60 border border-emerald-400 inline-block"></span>
            <span>Praktikum</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-3.5 h-3.5 rounded bg-amber-100 dark:bg-amber-900/60 border border-amber-400 inline-block"></span>
            <span>Responsi</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-3.5 h-3.5 rounded bg-[repeating-linear-gradient(45deg,#e2e8f0,#e2e8f0_5px,#cbd5e1_5px,#cbd5e1_10px)] dark:bg-[repeating-linear-gradient(45deg,#1e293b,#1e293b_5px,#0f172a_5px,#0f172a_10px)] border border-slate-300 inline-block"></span>
            <span>Jam KU / Jam Kemahasiswaan</span>
        </div>
        <div class="flex items-center space-x-1.5">
            <span class="w-3.5 h-3.5 rounded bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 inline-block"></span>
            <span>Manual Saja (Sore / Malam)</span>
        </div>
    </div>

    <!-- Main Schedule Content Area (Grid + Side Panels) -->
    <div class="flex-1 flex gap-4 min-h-0">
        <!-- Grid Time Table Container (Stacked by Day when 'all' or single day) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto space-y-6">
            <template x-for="currentDay in displayDays" :key="currentDay">
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col min-w-0 overflow-hidden flex-shrink-0">
                    <!-- Day Banner Header -->
                    <div class="bg-slate-100 dark:bg-slate-800/90 px-4 py-2.5 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between sticky top-0 z-20 backdrop-blur-sm">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            <span class="font-bold text-xs uppercase tracking-wider text-slate-900 dark:text-white" x-text="'HARI ' + currentDay"></span>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400" 
                              x-text="(slotsByDay[currentDay] || []).length + ' Slot Waktu'"></span>
                    </div>

                    <div class="overflow-auto max-h-[calc(100vh-210px)] min-h-[420px] relative border-t border-slate-200 dark:border-slate-700">
                        <table class="w-full border-separate border-spacing-0 text-left table-fixed">
                            <!-- Column Room Headers (Freeze Top Row) -->
                            <thead class="bg-slate-50 dark:bg-slate-800/90">
                                <tr>
                                    <!-- Top-Left Corner: Freeze Top & Freeze Left -->
                                    <th class="w-24 px-3 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider text-center border-r border-b border-slate-200 dark:border-slate-700 sticky top-0 left-0 z-30 bg-slate-100 dark:bg-slate-800 shadow-[1px_1px_0_0_rgba(203,213,225,0.8)] dark:shadow-[1px_1px_0_0_rgba(51,65,85,0.8)] select-none">
                                        Jam
                                    </th>

                                    <template x-for="room in rooms" :key="room.id">
                                        <!-- Freeze Top Room Header -->
                                        <th class="w-48 px-3 py-2 text-xs font-bold border-r border-b border-slate-200 dark:border-slate-700 text-center sticky top-0 z-20 shadow-[0_1px_0_0_rgba(203,213,225,0.8)] dark:shadow-[0_1px_0_0_rgba(51,65,85,0.8)]"
                                            :class="room.type === 'internal' ? 'bg-slate-50 dark:bg-slate-800' : 'bg-slate-100 dark:bg-slate-800/95'">
                                            <div class="font-bold text-slate-800 dark:text-white" x-text="room.code"></div>
                                            <div class="text-[10px] text-slate-500 font-normal truncate" x-text="room.name"></div>
                                            <div class="flex items-center justify-center space-x-1 mt-0.5">
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold"
                                                      :class="room.type === 'internal' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                                                      x-text="room.type === 'internal' ? 'Internal' : 'Eksternal'">
                                                </span>
                                                <span class="text-[10px] text-slate-500" x-text="'Kap: ' + room.capacity"></span>
                                                <template x-if="room.is_manual_only">
                                                    <span class="px-1 py-0.2 rounded text-[9px] font-semibold bg-rose-100 text-rose-700" title="Manual Saja">M</span>
                                                </template>
                                            </div>
                                        </th>
                                    </template>

                                    <!-- Column Non-Lab: Freeze Top Header di kolom paling akhir -->
                                    <th class="w-52 px-3 py-2 text-xs font-bold border-r border-b border-slate-200 dark:border-slate-700 text-center bg-slate-100 dark:bg-slate-800 sticky top-0 z-20 shadow-[0_1px_0_0_rgba(203,213,225,0.8)] dark:shadow-[0_1px_0_0_rgba(51,65,85,0.8)]">
                                        <div class="font-bold text-slate-800 dark:text-white">NON LAB</div>
                                        <div class="text-[10px] text-slate-500 font-normal truncate">Ruang Kelas Teori</div>
                                        <div class="flex items-center justify-center space-x-1 mt-0.5">
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300">
                                                Jadwal Kelas
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <!-- Time Slots Rows -->
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                <template x-for="(slot, slotIndex) in (slotsByDay[currentDay] || [])" :key="slotIndex">
                                    <tr class="h-16" :class="{
                                        'bg-[repeating-linear-gradient(45deg,#f8fafc,#f8fafc_6px,#f1f5f9_6px,#f1f5f9_12px)] dark:bg-[repeating-linear-gradient(45deg,#0f172a,#0f172a_6px,#090d16_6px,#090d16_12px)]': slot.forbidden,
                                        'bg-amber-50/40 dark:bg-amber-950/20': slot.manual_only && !slot.forbidden
                                    }">
                                        <!-- Slot Time Label Column (Freeze First Column) -->
                                        <td class="w-24 px-2 py-1 text-[11px] font-medium text-slate-600 dark:text-slate-300 text-center border-r border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 select-none sticky left-0 z-10 shadow-[1px_0_0_0_rgba(203,213,225,0.8)] dark:shadow-[1px_0_0_0_rgba(51,65,85,0.8)]">
                                            <span x-text="slot.label"></span>
                                        </td>

                                        <!-- Room Cells for this Slot -->
                                        <template x-for="room in rooms" :key="room.id">
                                            <td class="p-1 border-r border-b border-slate-200 dark:border-slate-800 relative transition-colors duration-150 align-top bg-white dark:bg-slate-900"
                                                :class="getCellBgClass(currentDay, room, slot)"
                                                @dragover.prevent="onCellDragOver($event, currentDay, room, slot)"
                                                @dragleave="onCellDragLeave($event)"
                                                @drop="onCellDrop($event, currentDay, room, slot)">

                                                <!-- Forbidden Window Notice: Jam KU / Jam Kemahasiswaan -->
                                                <template x-if="slot.forbidden">
                                                    <div class="h-full w-full flex items-center justify-center text-[10px] text-slate-400 dark:text-slate-500 font-semibold italic select-none">
                                                        Jam KU / Jam Kemahasiswaan
                                                    </div>
                                                </template>

                                                <!-- Render Events that start at this slot for this room -->
                                                <template x-if="!slot.forbidden">
                                                    <div>
                                                        <!-- Placed Lab Session Blocks -->
                                                        <template x-for="session in getSessionsInSlot(currentDay, room.id, slot)" :key="'session-' + session.id">
                                                            <div draggable="true"
                                                                 @dragstart="onSessionDragStart($event, session)"
                                                                 @dblclick="toggleLock(session)"
                                                                 class="p-1.5 rounded-lg border text-xs shadow-sm cursor-move select-none mb-1 transition hover:shadow-md relative group"
                                                                 :class="getBlockClass(session)">
                                                                <div class="flex items-start justify-between">
                                                                    <div class="font-bold truncate text-[11px]" x-text="session.section?.course?.name || session.section?.course?.nama || ''"></div>
                                                                    <div class="flex items-center space-x-1 ml-1 flex-shrink-0">
                                                                        <template x-if="session.is_locked">
                                                                            <svg class="w-3.5 h-3.5 text-indigo-700 dark:text-indigo-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                                                        </template>
                                                                        <template x-if="session.is_manual_placed">
                                                                            <span class="text-[9px] px-1 rounded bg-amber-200 text-amber-800 font-bold" title="Ditempatkan Manual/Override">M</span>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <div class="flex items-center justify-between text-[10px] mt-0.5 opacity-90">
                                                                    <span x-text="((session.type || session.session_type || 'PRAKTIKUM') + ' ' + (session.section?.label || session.section?.name || '')).toUpperCase()"></span>
                                                                    <span x-text="formatTime(session.start_min) + '-' + formatTime(session.end_min)"></span>
                                                                </div>
                                                                <!-- Lock toggle button overlay on hover -->
                                                                <button @click.stop="toggleLock(session)" 
                                                                        class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 p-0.5 rounded bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-200 shadow text-[9px]">
                                                                    <span x-text="session.is_locked ? 'Buka' : 'Kunci'"></span>
                                                                </button>
                                                            </div>
                                                        </template>

                                                        <!-- Class Meeting Blocks occupying Lab Rooms -->
                                                        <template x-for="meeting in getClassMeetingsInSlot(currentDay, room.id, slot)" :key="'meeting-' + meeting.id">
                                                            <div class="p-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-200 dark:bg-slate-700/80 text-slate-800 dark:text-slate-100 text-xs shadow-sm mb-1">
                                                                <div class="font-bold truncate text-[11px]" x-text="meeting.section?.course?.name || meeting.section?.course?.nama || ''"></div>
                                                                <div class="flex items-center justify-between text-[10px] mt-0.5 text-slate-600 dark:text-slate-300">
                                                                    <span x-text="'Kelas ' + (meeting.section?.label || meeting.section?.name || '')"></span>
                                                                    <span x-text="formatTime(meeting.start_min) + '-' + formatTime(meeting.end_min)"></span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                        </template>

                                        <!-- Non-Lab Cell for this Slot (Kolom paling akhir) -->
                                        <td class="p-1 border-r border-b border-slate-200 dark:border-slate-800 relative transition-colors duration-150 align-top bg-slate-50/60 dark:bg-slate-900/60">
                                            <div class="min-h-[56px] space-y-1">
                                                <template x-for="meeting in getClassMeetingsInSlot(currentDay, 'non_lab', slot)" :key="'meeting-nonlab-' + meeting.id">
                                                    <div class="p-1.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-slate-200 dark:bg-slate-700/80 text-slate-800 dark:text-slate-100 text-xs shadow-sm mb-1 hover:border-indigo-400 transition">
                                                        <div class="font-bold truncate text-[11px]" x-text="meeting.section?.course?.name || meeting.section?.course?.nama || ''"></div>
                                                        <div class="flex items-center justify-between text-[10px] mt-0.5 text-slate-600 dark:text-slate-300">
                                                            <span class="font-semibold text-indigo-700 dark:text-indigo-300" x-text="'Kelas ' + (meeting.section?.label || meeting.section?.name || '')"></span>
                                                            <span class="font-mono" x-text="formatTime(meeting.start_min) + '-' + formatTime(meeting.end_min)"></span>
                                                        </div>
                                                        <template x-if="meeting.room_label && meeting.room_label !== 'Ruang Kelas'">
                                                            <div class="text-[9px] text-slate-500 dark:text-slate-400 truncate mt-0.5" x-text="meeting.room_label"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </div>

        <!-- Right Side Panel: Belum Terjadwal (Droppable to unplace) -->
        <div class="w-80 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col flex-shrink-0 min-w-0 sticky top-4 self-start max-h-[calc(100vh-2rem)]"
             @dragover.prevent="onUnscheduledDragOver($event)"
             @dragleave="onUnscheduledDragLeave($event)"
             @drop="onUnscheduledDrop($event)">
            
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Belum Terjadwal</h2>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                      x-text="unscheduledSessions.length">
                </span>
            </div>

            <div class="p-2 text-[11px] text-slate-500 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-center flex-shrink-0">
                Tarik sesi ke grid atau lepas sesi dari grid ke sini untuk membatalkan
            </div>

            <!-- List of Unscheduled Sessions -->
            <div class="flex-1 overflow-y-auto min-h-0 p-3 space-y-2.5"
                 :class="{ 'bg-rose-50/50 dark:bg-rose-950/20 ring-2 ring-rose-400': isUnscheduledDragHover }">
                <template x-for="item in unscheduledSessions" :key="item.id">
                    <div draggable="true"
                         @dragstart="onSessionDragStart($event, item)"
                         class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-400 shadow-sm cursor-move select-none transition">
                        <div class="flex items-start justify-between">
                            <span class="font-bold text-xs text-slate-900 dark:text-white" x-text="item.section?.course?.name || item.section?.course?.nama || ''"></span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                  :class="(item.type || item.session_type || '').toLowerCase() === 'praktikum' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                                  x-text="((item.type || item.session_type || 'PRAKTIKUM') + ' ' + (item.section?.label || item.section?.name || '')).toUpperCase()">
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                            <span>Durasi: <strong x-text="item.duration_min + ' menit'"></strong></span>
                            <span>Mhs: <strong x-text="item.section.enrollment"></strong></span>
                        </div>
                        <template x-if="item.unscheduled_reason">
                            <div class="mt-2 text-[10px] text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 p-1.5 rounded border border-rose-200 dark:border-rose-900/50 flex items-start space-x-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span x-text="item.unscheduled_reason"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="unscheduledSessions.length === 0">
                    <div class="h-40 flex flex-col items-center justify-center text-center text-slate-400">
                        <svg class="w-8 h-8 mb-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Semua Sesi Terjadwal!</p>
                        <p class="text-[11px]">Tidak ada sesi di panel belum terjadwal.</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Sliding / Collapsible Path Panel -->
        <div x-cloak x-show="showPathPanel" 
             class="w-96 bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-800 flex flex-col flex-shrink-0 min-w-0 z-20 sticky top-4 self-start max-h-[calc(100vh-2rem)]">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <span>Analisis Path Mahasiswa</span>
                </h3>
                <button @click="showPathPanel = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Path Tabs: Semester & Custom -->
            <div class="flex border-b border-slate-200 dark:border-slate-800 text-xs font-medium flex-shrink-0">
                <button @click="pathTab = 'semesters'" 
                        class="flex-1 py-2.5 text-center border-b-2 transition"
                        :class="pathTab === 'semesters' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'">
                    Path per Semester
                </button>
                <button @click="pathTab = 'custom'" 
                        class="flex-1 py-2.5 text-center border-b-2 transition"
                        :class="pathTab === 'custom' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'">
                    Path Kustom
                </button>
            </div>

            <!-- Path Tab Content -->
            <div class="flex-1 overflow-y-auto min-h-0 p-4 space-y-4">
                <!-- Semester Paths Tab -->
                <template x-if="pathTab === 'semesters'">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-1 bg-slate-100 dark:bg-slate-800 p-1 rounded-lg">
                            <template x-for="sem in [1, 3, 5, 7]" :key="sem">
                                <button @click="loadPaths(sem)" 
                                        class="flex-1 py-1.5 rounded text-xs font-semibold transition"
                                        :class="selectedSemester === sem ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-400'"
                                        x-text="'Sem ' + sem">
                                </button>
                            </template>
                        </div>

                        <div class="text-xs flex items-center justify-between">
                            <span class="text-slate-500">Total Path Valid:</span>
                            <span class="font-bold text-indigo-600" x-text="currentPathsData.is_capped ? '500+ Path' : currentPathsData.total_paths + ' Path'"></span>
                        </div>

                        <!-- Warnings if 0 path -->
                        <template x-if="currentPathsData.warnings && currentPathsData.warnings.length > 0">
                            <div class="p-3 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 text-xs space-y-1">
                                <template x-for="w in currentPathsData.warnings" :key="w">
                                    <p class="font-medium" x-text="w"></p>
                                </template>
                            </div>
                        </template>

                        <!-- Paths List -->
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            <template x-for="(p, idx) in currentPathsData.paths" :key="idx">
                                <div @click="highlightPath(p)" 
                                     class="p-2.5 rounded-lg border text-xs cursor-pointer transition"
                                     :class="isPathHighlighted(p) ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-950/70 shadow' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-300'">
                                    <div class="flex items-center justify-between font-bold mb-1">
                                        <span x-text="'Path #' + (idx + 1)"></span>
                                        <span class="text-[10px] text-emerald-600 font-semibold">Valid</span>
                                    </div>
                                    <div class="space-y-0.5 text-[11px] text-slate-600 dark:text-slate-300">
                                        <template x-for="sec in p.sections" :key="sec.id">
                                            <div class="truncate" x-text="sec.course_name + ' (' + sec.label + ')'"></div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Custom Path Tab -->
                <template x-if="pathTab === 'custom'">
                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-semibold mb-1">Semester</label>
                            <select x-model.number="customPathSemester" @change="onCustomSemesterChange()"
                                    class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                                <option value="1">Semester 1</option>
                                <option value="3">Semester 3</option>
                                <option value="5">Semester 5</option>
                                <option value="7">Semester 7</option>
                            </select>
                        </div>

                        <!-- Dropdowns for each course in this semester -->
                        <div class="space-y-2">
                            <template x-for="course in customPathCourses" :key="course.id">
                                <div>
                                    <label class="block font-medium truncate mb-1" x-text="course.name"></label>
                                    <select x-model="customSelectedSections[course.id]" @change="evaluateCustomPath()"
                                            class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                                        <option value="">-- Pilih Seksi --</option>
                                        <template x-for="sec in course.sections" :key="sec.id">
                                            <option :value="sec.id" x-text="'Seksi ' + sec.label"></option>
                                        </template>
                                    </select>
                                </div>
                            </template>
                        </div>

                        <!-- Evaluation Result -->
                        <template x-if="customPathEvalResult">
                            <div class="p-3 rounded-lg border"
                                 :class="customPathEvalResult.valid ? 'bg-emerald-50 border-emerald-300 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200' : 'bg-rose-50 border-rose-300 text-rose-800 dark:bg-rose-950/40 dark:text-rose-200'">
                                <div class="font-bold flex items-center space-x-1 mb-1">
                                    <template x-if="customPathEvalResult.valid">
                                        <span>✓ Kombinasi Valid (Tanpa Bentrok)</span>
                                    </template>
                                    <template x-if="!customPathEvalResult.valid">
                                        <span>✗ Kombinasi Bentrok</span>
                                    </template>
                                </div>

                                <template x-if="!customPathEvalResult.valid && customPathEvalResult.clashes">
                                    <div class="space-y-1 mt-2 text-[11px]">
                                        <p class="font-semibold">Detail Bentrok:</p>
                                        <template x-for="(c, cIdx) in customPathEvalResult.clashes" :key="cIdx">
                                            <div class="p-1.5 rounded bg-white/60 dark:bg-slate-900/60 border border-rose-200 dark:border-rose-900">
                                                <div class="font-bold" x-text="c.course1 + ' ' + c.section1 + ' vs ' + c.course2 + ' ' + c.section2"></div>
                                                <div class="text-[10px]" x-text="c.day + ': ' + c.time1 + ' & ' + c.time2"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="customPathEvalResult.suggestions && customPathEvalResult.suggestions.length > 0">
                                    <div class="mt-2 text-[11px] p-2 bg-amber-50 dark:bg-amber-950/50 rounded border border-amber-300 text-amber-800 dark:text-amber-200">
                                        <p class="font-semibold mb-1">Saran Alternatif:</p>
                                        <template x-for="sug in customPathEvalResult.suggestions" :key="sug">
                                            <p x-text="sug"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Save Custom Path Form -->
                        <template x-if="customPathEvalResult && customPathEvalResult.valid">
                            <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                                <label class="block font-semibold">Beri Nama Path Kustom:</label>
                                <input type="text" x-model="customPathName" placeholder="mis. Jalur Rekomendasi A"
                                       class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                                <button @click="saveCustomPath()" 
                                        class="w-full py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                                    Simpan Path Kustom
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Override Confirmation Modal -->
    <div x-cloak x-show="showOverrideModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center space-x-3 text-amber-600">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Peringatan Pelanggaran Aturan</h3>
                    <p class="text-xs text-slate-500">Penempatan melanggar aturan penjadwalan</p>
                </div>
            </div>

            <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 text-xs text-amber-800 dark:text-amber-200 space-y-1">
                <p class="font-semibold">Aturan yang dilanggar:</p>
                <template x-for="v in pendingViolations" :key="v">
                    <p class="list-disc ml-3" x-text="'• ' + v"></p>
                </template>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-400">
                Apakah Anda ingin memaksakan penempatan ini (override)? Sesi yang dipaksa akan ditandai dengan ikon peringatan.
            </p>

            <div class="flex justify-end space-x-2 pt-2">
                <button @click="cancelOverride()" 
                        class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button @click="confirmOverride()" 
                        class="px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow">
                    Paksa Simpan (Override)
                </button>
            </div>
        </div>
    </div>

    <!-- Reset Confirmation Modal -->
    <div x-cloak x-show="showResetModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <h3 class="font-bold text-base text-slate-900 dark:text-white">Konfirmasi Reset Jadwal</h3>
            <p class="text-xs text-slate-600 dark:text-slate-400">
                Semua sesi otomatis yang belum dikunci akan dikembalikan ke panel "Belum Terjadwal". Sesi terkunci dan sesi yang ditempatkan secara manual tidak akan terhapus.
            </p>
            <div class="flex justify-end space-x-2 pt-2">
                <button @click="showResetModal = false" 
                        class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                    Batal
                </button>
                <button @click="runReset()" 
                        class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold shadow">
                    Ya, Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Save Version Modal -->
    <div x-cloak x-show="showSaveVersionModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <h3 class="font-bold text-base text-slate-900 dark:text-white">Simpan Versi Draft</h3>
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nama Versi / Draft</label>
                <input type="text" x-model="versionName" placeholder="mis. Draft Final v1"
                       class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button @click="showSaveVersionModal = false" 
                        class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100">
                    Batal
                </button>
                <button @click="saveVersion()" 
                        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Generate Summary Modal -->
    <div x-cloak x-show="showSummaryModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center space-x-3 text-emerald-600">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900 dark:text-white">Hasil Penjadwalan Otomatis</h3>
                    <p class="text-xs text-slate-500">Ringkasan eksekusi generator</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900 rounded-xl">
                    <div class="text-xl font-bold text-emerald-700 dark:text-emerald-300" x-text="generateSummary.total_placed"></div>
                    <div class="text-[10px] text-emerald-600 mt-0.5">Sesi Terjadwal</div>
                </div>
                <div class="p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 rounded-xl">
                    <div class="text-xl font-bold text-rose-700 dark:text-rose-300" x-text="generateSummary.total_unscheduled"></div>
                    <div class="text-[10px] text-rose-600 mt-0.5">Belum Terjadwal</div>
                </div>
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900 rounded-xl">
                    <div class="text-xl font-bold text-indigo-700 dark:text-indigo-300" x-text="generateSummary.external_lab_count"></div>
                    <div class="text-[10px] text-indigo-600 mt-0.5">Lab Eksternal</div>
                </div>
            </div>

            <!-- List of Unscheduled with reasons -->
            <template x-if="generateSummary.unscheduled_items && generateSummary.unscheduled_items.length > 0">
                <div class="space-y-1.5 max-h-48 overflow-y-auto border-t border-slate-200 dark:border-slate-800 pt-3">
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Daftar Belum Terjadwal beserta Alasan:</p>
                    <template x-for="item in generateSummary.unscheduled_items" :key="item.session_id">
                        <div class="p-2 rounded bg-slate-50 dark:bg-slate-800 text-[11px] flex items-center justify-between">
                            <span class="font-medium" x-text="item.course_name + ' (' + item.section_label + ') - ' + item.type"></span>
                            <span class="text-rose-600 dark:text-rose-400 font-semibold" x-text="item.reason"></span>
                        </div>
                    </template>
                </div>
            </template>

            <div class="flex justify-end pt-2">
                <button @click="showSummaryModal = false; window.location.reload()" 
                        class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow">
                    Tutup & Perbarui Grid
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function scheduleManager() {
    return {
        days: @json($days),
        activeDay: 'all',
        rooms: @json($rooms),
        slotsByDay: @json($slotsByDay),
        placedSessions: @json($placedSessions),
        classMeetings: @json($classMeetings),
        unscheduledSessions: @json($unscheduledSessions),

        isLoading: false,
        showResetModal: false,
        showSaveVersionModal: false,
        showOverrideModal: false,
        showSummaryModal: false,
        versionName: '',
        generateSummary: {},

        // Drag and drop state
        draggedSession: null,
        dragOverCell: null,
        isUnscheduledDragHover: false,
        pendingPlacement: null,
        pendingViolations: [],

        // Path Panel
        showPathPanel: false,
        pathTab: 'semesters',
        selectedSemester: 1,
        currentPathsData: { total_paths: 0, paths: [], is_capped: false, warnings: [] },
        highlightedSections: [],

        // Custom Path
        customPathSemester: 1,
        customPathCourses: [],
        customSelectedSections: {},
        customPathEvalResult: null,
        customPathName: '',

        init() {
            this.loadPaths(1);
            this.onCustomSemesterChange();
        },

        get displayDays() {
            if (this.activeDay === 'all') {
                return this.days;
            }
            return [this.activeDay];
        },

        get activeSlots() {
            if (this.activeDay === 'all') {
                return this.slotsByDay['Senin'] || [];
            }
            return this.slotsByDay[this.activeDay] || [];
        },

        formatTime(minutes) {
            if (!minutes) return '';
            const h = Math.floor(minutes / 60);
            const m = minutes % 60;
            return (h < 10 ? '0' : '') + h + '.' + (m < 10 ? '0' : '') + m;
        },

        getDayName(dayOfWeek) {
            const map = {1: 'Senin', 2: 'Selasa', 3: 'Rabu', 4: 'Kamis', 5: 'Jumat', 6: 'Sabtu', 0: 'Minggu'};
            return map[dayOfWeek] || '';
        },

        getSessionsInSlot(day, roomId, slot) {
            return this.placedSessions.filter(s => 
                (s.room_id === roomId || s.lab_id === roomId) && 
                (s.day === day || this.getDayName(s.day_of_week) === day) && 
                s.start_min === slot.start
            );
        },

        getClassMeetingsInSlot(day, roomId, slot) {
            if (roomId === 'non_lab') {
                return this.classMeetings.filter(m => 
                    (!m.room_id && !m.lab_id) && 
                    (m.day === day || this.getDayName(m.day_of_week) === day) && 
                    (m.start_min === slot.start || (m.start_min >= slot.start && m.start_min < slot.end))
                );
            }
            return this.classMeetings.filter(m => 
                (m.room_id === roomId || m.lab_id === roomId) && 
                (m.day === day || this.getDayName(m.day_of_week) === day) && 
                (m.start_min === slot.start || (m.start_min >= slot.start && m.start_min < slot.end))
            );
        },

        getCellBgClass(day, room, slot) {
            if (this.dragOverCell && 
                this.dragOverCell.day === day && 
                this.dragOverCell.roomId === room.id && 
                this.dragOverCell.slotStart === slot.start) {
                return this.dragOverCell.isValid ? 'bg-emerald-100/80 ring-2 ring-emerald-500' : 'bg-rose-100/80 ring-2 ring-rose-500';
            }
            return '';
        },

        getBlockClass(session) {
            let base = (session.type || session.session_type || '').toLowerCase() === 'praktikum' 
                ? 'bg-emerald-100 dark:bg-emerald-950/60 border-emerald-400 text-emerald-900 dark:text-emerald-100' 
                : 'bg-amber-100 dark:bg-amber-950/60 border-amber-400 text-amber-900 dark:text-amber-100';

            if (this.highlightedSections.includes(session.class_section_id)) {
                base += ' ring-2 ring-indigo-600 scale-[1.02] shadow-md';
            }
            return base;
        },

        // HTML5 Drag and Drop handlers
        onSessionDragStart(event, session) {
            this.draggedSession = session;
            event.dataTransfer.setData('text/plain', session.id);
            event.dataTransfer.effectAllowed = 'move';
        },

        onCellDragOver(event, day, room, slot) {
            if (slot.forbidden || !this.draggedSession) {
                event.dataTransfer.dropEffect = 'none';
                return;
            }

            // Quick client-side check
            const duration = this.draggedSession.duration_min;
            const targetEnd = slot.start + duration;
            const isValid = (this.draggedSession.section.enrollment <= room.capacity);

            this.dragOverCell = {
                day: day,
                roomId: room.id,
                slotStart: slot.start,
                isValid: isValid
            };
            event.dataTransfer.dropEffect = 'move';
        },

        onCellDragLeave(event) {
            this.dragOverCell = null;
        },

        async onCellDrop(event, day, room, slot) {
            this.dragOverCell = null;
            if (!this.draggedSession) return;

            const session = this.draggedSession;
            const targetStart = slot.start;
            const targetEnd = slot.start + session.duration_min;

            // Submit placement to server
            this.pendingPlacement = {
                session_id: session.id,
                room_id: room.id,
                day: day,
                start_min: targetStart,
                end_min: targetEnd
            };

            await this.submitPlacement(false);
            this.draggedSession = null;
        },

        async submitPlacement(override = false) {
            if (!this.pendingPlacement) return;

            try {
                const res = await fetch("{{ route('api.schedule.placement') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ...this.pendingPlacement,
                        override: override
                    })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.showOverrideModal = false;
                    window.location.reload();
                } else if (res.status === 422 && data.can_override) {
                    this.pendingViolations = data.violations;
                    this.showOverrideModal = true;
                } else {
                    alert(data.message || 'Gagal menempatkan sesi.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        },

        confirmOverride() {
            this.submitPlacement(true);
        },

        cancelOverride() {
            this.showOverrideModal = false;
            this.pendingPlacement = null;
            this.pendingViolations = [];
        },

        // Drop to Unscheduled Panel
        onUnscheduledDragOver(event) {
            if (this.draggedSession && this.draggedSession.status === 'placed') {
                this.isUnscheduledDragHover = true;
                event.dataTransfer.dropEffect = 'move';
            }
        },

        onUnscheduledDragLeave(event) {
            this.isUnscheduledDragHover = false;
        },

        async onUnscheduledDrop(event) {
            this.isUnscheduledDragHover = false;
            if (!this.draggedSession || this.draggedSession.status !== 'placed') return;

            try {
                const res = await fetch("{{ route('api.schedule.unplace') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ session_id: this.draggedSession.id })
                });

                if (res.ok) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
            this.draggedSession = null;
        },

        // Lock Toggle
        async toggleLock(session) {
            try {
                const res = await fetch("{{ route('api.schedule.toggle_lock') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ session_id: session.id })
                });
                const data = await res.json();
                if (data.success) {
                    session.is_locked = data.is_locked;
                }
            } catch (err) {
                console.error(err);
            }
        },

        // Generate
        async runGenerate() {
            this.isLoading = true;
            try {
                const res = await fetch("{{ route('api.schedule.generate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await res.json();
                this.isLoading = false;
                if (data.success) {
                    this.generateSummary = data.summary;
                    this.showSummaryModal = true;
                } else {
                    alert('Gagal generate jadwal: ' + data.message);
                }
            } catch (err) {
                this.isLoading = false;
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        },

        // Reset
        async runReset() {
            this.showResetModal = false;
            try {
                const res = await fetch("{{ route('api.schedule.reset') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                if (res.ok) {
                    window.location.reload();
                }
            } catch (err) {
                console.error(err);
            }
        },

        // Save Version
        async saveVersion() {
            if (!this.versionName) return;
            try {
                const res = await fetch("{{ route('versions.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name: this.versionName })
                });
                const data = await res.json();
                this.showSaveVersionModal = false;
                this.versionName = '';
                alert(data.message);
            } catch (err) {
                console.error(err);
            }
        },

        // Path Panel
        togglePathPanel() {
            this.showPathPanel = !this.showPathPanel;
        },

        async loadPaths(semester) {
            this.selectedSemester = semester;
            try {
                const res = await fetch(`{{ route('api.schedule.paths') }}?semester=${semester}`);
                this.currentPathsData = await res.json();
            } catch (err) {
                console.error(err);
            }
        },

        highlightPath(path) {
            const secIds = path.sections.map(s => s.id);
            if (JSON.stringify(this.highlightedSections) === JSON.stringify(secIds)) {
                this.highlightedSections = [];
            } else {
                this.highlightedSections = secIds;
            }
        },

        isPathHighlighted(path) {
            const secIds = path.sections.map(s => s.id);
            return JSON.stringify(this.highlightedSections) === JSON.stringify(secIds);
        },

        // Custom Path methods
        async onCustomSemesterChange() {
            this.customSelectedSections = {};
            this.customPathEvalResult = null;
            try {
                const res = await fetch(`{{ route('api.schedule.paths') }}?semester=${this.customPathSemester}`);
                const data = await res.json();
                this.customPathCourses = (data.courses || []).map(c => {
                    const sections = this.classMeetings
                        .filter(m => m.section && (m.section.course_id === c.id || m.section.matkul_id === c.id))
                        .map(m => m.section);
                    // Deduplicate sections
                    const uniqueSecs = [];
                    const seen = new Set();
                    for (const s of sections) {
                        if (!seen.has(s.id)) {
                            seen.add(s.id);
                            uniqueSecs.push(s);
                        }
                    }
                    return { id: c.id, name: c.name, sections: uniqueSecs };
                });
            } catch (err) {
                console.error(err);
            }
        },

        async evaluateCustomPath() {
            const secIds = Object.values(this.customSelectedSections).filter(id => !!id);
            if (secIds.length === 0) {
                this.customPathEvalResult = null;
                return;
            }

            try {
                const res = await fetch("{{ route('api.schedule.paths.evaluate') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        semester: this.customPathSemester,
                        section_ids: secIds
                    })
                });
                this.customPathEvalResult = await res.json();
            } catch (err) {
                console.error(err);
            }
        },

        async saveCustomPath() {
            if (!this.customPathName) return;
            const secIds = Object.values(this.customSelectedSections).filter(id => !!id);
            try {
                const res = await fetch("{{ route('api.schedule.paths.custom') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        semester: this.customPathSemester,
                        name: this.customPathName,
                        section_ids: secIds
                    })
                });
                const data = await res.json();
                if (data.success) {
                    alert('Path kustom berhasil disimpan!');
                    this.customPathName = '';
                }
            } catch (err) {
                console.error(err);
            }
        }
    }
}
</script>
@endpush
