@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Lab')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-sm bg-white border-b border-gray-200 items-center justify-between mb-5 px-6">
        <div>
            <h1 class="text-2xl md:text-3xl uppercase font-bold text-slate-900 tracking-tight">Jadwal Laboratorium</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola versi jadwal praktikum &amp; responsi serta penempatan sesi laboratorium</p>
        </div>
        <div class="flex items-center gap-3 mt-2 md:mt-0">
            <span id="active-schedule-indicator" class="px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-700 font-semibold">Pilih versi jadwal</span>
        </div>
    </div>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 pb-12 space-y-4">
        {{-- Top Control Bar: Periode & Versi Jadwal --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label for="filter_period" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Periode Akademik</label>
                        <select id="filter_period" class="min-w-[240px] pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}" {{ $selectedPeriod && $selectedPeriod->id === $p->id ? 'selected' : '' }}>
                                    {{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="schedule-select" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Versi Jadwal</label>
                        <div class="flex items-center gap-2">
                            <select id="schedule-select" class="min-w-[240px] pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">- Pilih Versi Jadwal -</option>
                            </select>
                            <span id="selected-schedule-badge" class="hidden px-2.5 py-1 text-xs font-bold uppercase rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200">AKTIF</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button id="btn-create-schedule" type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Versi Jadwal
                    </button>
                    <button id="btn-activate-schedule" type="button"
                        class="hidden inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Jadikan Aktif
                    </button>
                    <button id="btn-duplicate-schedule" type="button"
                        class="hidden inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplikasi
                    </button>
                    <button id="btn-delete-schedule" type="button"
                        class="hidden inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-red-300 text-red-600 hover:bg-red-50 text-xs font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Versi
                    </button>
                </div>
            </div>

            {{-- Daftar Kartu Versi Jadwal Ringkas --}}
            <div id="schedule-list" class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
                {{-- Di-render oleh JS --}}
            </div>
        </div>

        {{-- Bar Legenda & Tab Hari --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4 bg-slate-50/60">
                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-700">
                    <span class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Legenda:</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-emerald-600 inline-block shadow-sm"></span>
                        <span class="font-medium">Praktikum</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-amber-500 inline-block shadow-sm"></span>
                        <span class="font-medium">Responsi</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-3.5 h-3.5 rounded bg-orange-500 ring-2 ring-orange-300 inline-block"></span>
                        <span class="font-medium">Override Bentrok</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-4 h-3.5 rounded border border-slate-300 blocked-slot-pattern inline-block"></span>
                        <span class="font-medium">Jam Terlarang</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="font-medium">Terkunci</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-800 border border-amber-300">Manual</span>
                        <span class="font-medium">Lab Manual</span>
                    </div>
                </div>

                {{-- Select filter-day tetap ada untuk kompatibilitas --}}
                <div class="hidden">
                    <select id="filter-day" class="pl-3 pr-8 py-1 border border-gray-300 rounded-lg text-xs bg-white">
                        <option value="1" selected>Senin</option>
                        <option value="2">Selasa</option>
                        <option value="3">Rabu</option>
                        <option value="4">Kamis</option>
                        <option value="5">Jumat</option>
                        <option value="6">Sabtu</option>
                        <option value="all">Semua Hari</option>
                    </select>
                </div>
            </div>

            {{-- Navigasi Tab Hari Horizontal --}}
            <div class="px-5 bg-white border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                <div id="day-tabs" class="flex items-center gap-1 overflow-x-auto">
                    <button type="button" data-day="1" class="day-tab-btn px-4 py-3 text-sm font-bold border-b-2 border-slate-900 text-slate-900 transition-colors whitespace-nowrap">Senin</button>
                    <button type="button" data-day="2" class="day-tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors whitespace-nowrap">Selasa</button>
                    <button type="button" data-day="3" class="day-tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors whitespace-nowrap">Rabu</button>
                    <button type="button" data-day="4" class="day-tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors whitespace-nowrap">Kamis</button>
                    <button type="button" data-day="5" class="day-tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors whitespace-nowrap">Jumat</button>
                    <button type="button" data-day="6" class="day-tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors whitespace-nowrap">Sabtu</button>
                    <button type="button" data-day="all" class="day-tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors whitespace-nowrap">Semua Hari</button>
                </div>
                <div class="py-2 text-right">
                    <span id="timetable-title" class="text-xs font-bold text-slate-800 block">Timetable Laboratorium</span>
                    <span id="timetable-subtitle" class="text-[11px] text-slate-500 block">Senin 10:30–13:00 Kebaktian Umum · Jumat 10:30–14:30 Kegiatan Mahasiswa</span>
                </div>
            </div>
        </div>

        {{-- 2 Kolom Utama: Timetable Grid (Kiri, flex-1) & Panel Belum Terjadwal (Kanan, xl:w-[340px]) --}}
        <div class="flex flex-col xl:flex-row gap-5 items-start">
            {{-- Area Kiri: Main Timetable Grid --}}
            <div class="w-full flex-1 min-w-0 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div id="timetable-container" class="overflow-x-auto max-h-[80vh]">
                    <div class="p-12 text-center text-gray-400 text-sm">
                        Pilih atau buat versi jadwal di atas untuk mulai menyusun jadwal.
                    </div>
                </div>
            </div>

            {{-- Area Kanan: Panel Belum Terjadwal (UNSCHEDULED) --}}
            <div class="w-full xl:w-[340px] flex-shrink-0 bg-white rounded-xl shadow-sm border border-gray-200 p-4 sticky top-4">
                <div class="flex items-center justify-between mb-1.5">
                    <h2 class="text-sm font-bold text-slate-800">Belum Terjadwal (<span id="unscheduled-count">0</span>)</h2>
                </div>
                <p class="text-xs text-slate-500 mb-3">Seret kartu sesi ke sel tabel jadwal di sebelah kiri.</p>

                <div class="mb-3">
                    <input type="search" id="unscheduled-search" placeholder="Cari mata kuliah / kelas / dosen..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div id="unscheduled-list" class="space-y-2.5 min-h-[180px] max-h-[68vh] overflow-y-auto pr-1">
                    <p class="text-xs text-gray-400 text-center py-8">Pilih versi jadwal terlebih dahulu.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Buat Versi Jadwal --}}
    <div id="create-schedule-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="create-schedule-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t-xl bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Buat Versi Jadwal Baru</h3>
                    <button id="close-schedule-modal-btn" type="button" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="create-schedule-form" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Periode Akademik</label>
                        <select id="schedule-period-id" required class="w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}" {{ $selectedPeriod && $selectedPeriod->id === $p->id ? 'selected' : '' }}>
                                    {{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Versi Jadwal</label>
                        <input type="text" id="schedule-name" required maxlength="100" placeholder="Draft Ganjil 2025/2026"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (opsional)</label>
                        <textarea id="schedule-notes" rows="3" placeholder="Catatan versi jadwal..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" id="cancel-schedule-modal-btn"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                            Buat Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .session-chip.locked {
            cursor: not-allowed !important;
        }
        .timetable-cell.drag-over {
            background-color: #eef2ff !important;
            outline: 2px dashed #6366f1;
            outline-offset: -2px;
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .blocked-slot-pattern {
            background-image: repeating-linear-gradient(
                -45deg,
                #f1f5f9,
                #f1f5f9 8px,
                #e2e8f0 8px,
                #e2e8f0 16px
            );
        }
    </style>

    <script>
        const INITIAL_SCHEDULES = @json($schedules);
        const INITIAL_PERIOD_ID = @json($selectedPeriod?->id);

        const DAY_NAMES = {
            0: 'Minggu',
            1: 'Senin',
            2: 'Selasa',
            3: 'Rabu',
            4: 'Kamis',
            5: 'Jumat',
            6: 'Sabtu',
        };

        const SLOT_HEIGHT_PX = 44;

        const BLOCKED_SLOTS = {
            1: [{ startMin: 630, endMin: 780, label: 'Kebaktian Umum' }],     // Senin 10:30 - 13:00
            5: [{ startMin: 630, endMin: 870, label: 'Kegiatan Mahasiswa' }], // Jumat 10:30 - 14:30
        };

        const MATKUL_PALETTE = [
            { bg: '#1e3a5f', border: '#152943' }, // Deep Navy
            { bg: '#166534', border: '#14532d' }, // Forest Green
            { bg: '#1d4ed8', border: '#1e40af' }, // Royal Blue
            { bg: '#0f766e', border: '#115e59' }, // Deep Teal
            { bg: '#4338ca', border: '#3730a3' }, // Indigo
            { bg: '#6d28d9', border: '#5b21b6' }, // Purple
            { bg: '#be123c', border: '#9f1239' }, // Crimson
            { bg: '#b45309', border: '#92400e' }, // Warm Amber
            { bg: '#0369a1', border: '#075985' }, // Ocean Blue
            { bg: '#334155', border: '#1e293b' }, // Slate
        ];

        const ICON_LOCK_CLOSED = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>`;
        const ICON_LOCK_OPEN = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>`;
        const ICON_UNPLACE = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        const ICON_WARNING = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;

        let allSchedules = Array.isArray(INITIAL_SCHEDULES) ? [...INITIAL_SCHEDULES] : [];
        let currentPeriodId = INITIAL_PERIOD_ID || (document.getElementById('filter_period')?.value ?? '');
        let currentScheduleId = null;
        let currentDayFilter = '1';
        let activeLabs = [];
        let currentSessions = [];
        let sortableInstances = [];
        let placementInProgress = false;

        function getMatkulColor(matkulId, kode) {
            const key = String(kode || matkulId || 'default');
            let hash = 0;
            for (let i = 0; i < key.length; i++) {
                hash = ((hash << 5) - hash) + key.charCodeAt(i);
                hash |= 0;
            }
            return MATKUL_PALETTE[Math.abs(hash) % MATKUL_PALETTE.length];
        }

        function getBlockedSlot(day, slotMin) {
            const list = BLOCKED_SLOTS[Number(day)] || [];
            return list.find(b => slotMin >= b.startMin && slotMin < b.endMin) || null;
        }

        function formatMinToTime(mins) {
            const h = Math.floor(mins / 60);
            const m = mins % 60;
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        async function apiJson(url, method = 'GET', body = null) {
            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: body ? JSON.stringify(body) : undefined,
            });
            const data = await response.json().catch(() => ({}));
            return { ok: response.ok, status: response.status, data };
        }

        function syncDayTabsUI() {
            const selectEl = document.getElementById('filter-day');
            if (selectEl && selectEl.value !== currentDayFilter) {
                selectEl.value = currentDayFilter;
            }
            document.querySelectorAll('.day-tab-btn').forEach(btn => {
                const isActive = btn.dataset.day === String(currentDayFilter);
                btn.classList.toggle('border-slate-900', isActive);
                btn.classList.toggle('text-slate-900', isActive);
                btn.classList.toggle('font-bold', isActive);
                btn.classList.toggle('border-transparent', !isActive);
                btn.classList.toggle('text-slate-500', !isActive);
                btn.classList.toggle('font-medium', !isActive);
            });
        }

        document.addEventListener('DOMContentLoaded', async function () {
            const navEl = document.getElementById('schedules');
            if (navEl) {
                navEl.classList.add('bg-slate-100', 'active');
            }

            syncDayTabsUI();
            await loadLabs();
            renderScheduleList();

            // Auto-select active schedule in current period if any
            const periodSchedules = allSchedules.filter(s => String(s.period_id) === String(currentPeriodId));
            const defaultSchedule = periodSchedules.find(s => s.is_active) || periodSchedules[0];
            if (defaultSchedule) {
                await selectSchedule(defaultSchedule.id);
            }

            document.getElementById('filter_period').addEventListener('change', async (e) => {
                currentPeriodId = e.target.value;
                document.getElementById('schedule-period-id').value = currentPeriodId;
                currentScheduleId = null;
                currentSessions = [];
                renderScheduleList();
                const list = allSchedules.filter(s => String(s.period_id) === String(currentPeriodId));
                if (list.length > 0) {
                    await selectSchedule((list.find(s => s.is_active) || list[0]).id);
                } else {
                    updateHeaderIndicator();
                    renderUnscheduled();
                    renderTimetable();
                }
            });

            document.getElementById('schedule-select').addEventListener('change', async (e) => {
                const id = e.target.value;
                if (id) {
                    await selectSchedule(id);
                }
            });

            document.getElementById('btn-activate-schedule').addEventListener('click', async () => {
                if (currentScheduleId) await activateScheduleById(currentScheduleId);
            });
            document.getElementById('btn-duplicate-schedule').addEventListener('click', async () => {
                if (currentScheduleId) await duplicateScheduleById(currentScheduleId);
            });
            document.getElementById('btn-delete-schedule').addEventListener('click', async () => {
                if (currentScheduleId) await deleteScheduleById(currentScheduleId);
            });

            document.querySelectorAll('.day-tab-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentDayFilter = btn.dataset.day;
                    syncDayTabsUI();
                    updateHeaderIndicator();
                    renderTimetable();
                    initDragAndDrop();
                });
            });

            document.getElementById('filter-day').addEventListener('change', (e) => {
                currentDayFilter = e.target.value;
                syncDayTabsUI();
                updateHeaderIndicator();
                renderTimetable();
                initDragAndDrop();
            });

            document.getElementById('unscheduled-search').addEventListener('input', () => {
                renderUnscheduled();
                initDragAndDrop();
            });

            // Modal Buat Versi Jadwal
            const modal = document.getElementById('create-schedule-modal');
            const openModal = () => {
                document.getElementById('create-schedule-form').reset();
                document.getElementById('schedule-period-id').value = currentPeriodId;
                modal.classList.remove('hidden');
            };
            const closeModal = () => modal.classList.add('hidden');

            document.getElementById('btn-create-schedule').addEventListener('click', openModal);
            document.getElementById('close-schedule-modal-btn').addEventListener('click', closeModal);
            document.getElementById('cancel-schedule-modal-btn').addEventListener('click', closeModal);
            document.getElementById('create-schedule-overlay').addEventListener('click', (e) => {
                if (e.target.id === 'create-schedule-overlay') closeModal();
            });

            document.getElementById('create-schedule-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const period_id = document.getElementById('schedule-period-id').value;
                const name = document.getElementById('schedule-name').value.trim();
                const notes = document.getElementById('schedule-notes').value.trim() || null;

                showLoading('Membuat versi jadwal...');
                const { ok, data } = await apiJson('/admin/schedules', 'POST', { period_id, name, notes });
                Swal.close();

                if (!ok) {
                    Swal.fire('Gagal', data.message || 'Gagal membuat versi jadwal', 'error');
                    return;
                }

                closeModal();
                allSchedules.unshift(data.schedule);
                if (String(period_id) !== String(currentPeriodId)) {
                    currentPeriodId = period_id;
                    document.getElementById('filter_period').value = currentPeriodId;
                }
                renderScheduleList();
                await selectSchedule(data.schedule.id);
                showToast('Berhasil!', 'Versi jadwal berhasil dibuat', 'success');
            });
        });

        async function loadLabs() {
            const { ok, data } = await apiJson('/admin/labs/list', 'GET');
            if (ok && Array.isArray(data)) {
                activeLabs = data
                    .filter(l => l.is_active !== false)
                    .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0) || String(a.name).localeCompare(String(b.name)));
            }
        }

        async function activateScheduleById(id) {
            showLoading('Mengaktifkan jadwal...');
            const { ok, data } = await apiJson(`/admin/schedules/${id}/active`, 'PATCH');
            Swal.close();
            if (!ok) {
                Swal.fire('Gagal', data.message || 'Gagal mengaktifkan jadwal', 'error');
                return;
            }
            allSchedules = allSchedules.map(s => {
                if (String(s.period_id) === String(data.schedule.period_id)) {
                    return { ...s, is_active: String(s.id) === String(data.schedule.id) };
                }
                return s;
            });
            renderScheduleList();
            updateHeaderIndicator();
            showToast('Berhasil!', 'Versi jadwal diaktifkan', 'success');
        }

        async function duplicateScheduleById(id) {
            showLoading('Menduplikasi jadwal...');
            const { ok, data } = await apiJson(`/admin/schedules/${id}/duplicate`, 'POST');
            Swal.close();
            if (!ok) {
                Swal.fire('Gagal', data.message || 'Gagal menduplikasi jadwal', 'error');
                return;
            }
            allSchedules.unshift(data.schedule);
            renderScheduleList();
            await selectSchedule(data.schedule.id);
            showToast('Berhasil!', 'Versi jadwal diduplikasi', 'success');
        }

        async function deleteScheduleById(id) {
            const confirm = await Swal.fire({
                title: 'Hapus Versi Jadwal?',
                text: 'Versi jadwal yang dihapus tidak dapat dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
            });
            if (!confirm.isConfirmed) return;

            showLoading('Menghapus...');
            const { ok, data } = await apiJson(`/admin/schedules/${id}`, 'DELETE');
            Swal.close();
            if (!ok) {
                Swal.fire('Gagal', data.message || 'Gagal menghapus jadwal', 'error');
                return;
            }
            allSchedules = allSchedules.filter(s => String(s.id) !== String(id));
            if (String(currentScheduleId) === String(id)) {
                currentScheduleId = null;
                currentSessions = [];
            }
            renderScheduleList();
            const remaining = allSchedules.filter(s => String(s.period_id) === String(currentPeriodId));
            if (remaining.length > 0 && !currentScheduleId) {
                await selectSchedule((remaining.find(s => s.is_active) || remaining[0]).id);
            } else {
                renderUnscheduled();
                renderTimetable();
                updateHeaderIndicator();
            }
            showToast('Terhapus!', 'Versi jadwal berhasil dihapus', 'success');
        }

        function renderScheduleList() {
            const container = document.getElementById('schedule-list');
            const selectEl = document.getElementById('schedule-select');
            const badgeEl = document.getElementById('selected-schedule-badge');
            const btnActivate = document.getElementById('btn-activate-schedule');
            const btnDuplicate = document.getElementById('btn-duplicate-schedule');
            const btnDelete = document.getElementById('btn-delete-schedule');

            const filtered = allSchedules.filter(s => String(s.period_id) === String(currentPeriodId));

            if (selectEl) {
                if (filtered.length === 0) {
                    selectEl.innerHTML = '<option value="">- Belum ada versi jadwal -</option>';
                } else {
                    selectEl.innerHTML = filtered.map(s => `
                        <option value="${s.id}" ${String(s.id) === String(currentScheduleId) ? 'selected' : ''}>
                            ${escapeHtml(s.name)}${s.is_active ? ' (Aktif)' : ''}
                        </option>
                    `).join('');
                }
            }

            const currentSched = filtered.find(s => String(s.id) === String(currentScheduleId));
            if (badgeEl) badgeEl.classList.toggle('hidden', !currentSched?.is_active);
            if (btnActivate) btnActivate.classList.toggle('hidden', !currentSched || !!currentSched.is_active);
            if (btnDuplicate) btnDuplicate.classList.toggle('hidden', !currentSched);
            if (btnDelete) {
                btnDelete.classList.toggle('hidden', !currentSched);
                if (currentSched) {
                    btnDelete.disabled = !!currentSched.is_active;
                    btnDelete.classList.toggle('opacity-50', !!currentSched.is_active);
                    btnDelete.classList.toggle('cursor-not-allowed', !!currentSched.is_active);
                }
            }

            if (filtered.length === 0) {
                container.innerHTML = '<p class="text-xs text-gray-400 py-1">Belum ada versi jadwal di periode ini.</p>';
                return;
            }

            container.innerHTML = filtered.map(s => {
                const isSelected = String(s.id) === String(currentScheduleId);
                return `
                    <div class="schedule-card cursor-pointer rounded-lg border px-3 py-2 transition-all flex items-center gap-3 ${isSelected ? 'border-indigo-600 bg-indigo-50/70 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300'}"
                        data-schedule-id="${s.id}">
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-xs text-slate-900">${escapeHtml(s.name)}</span>
                            ${s.is_active ? '<span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-emerald-100 text-emerald-800">Aktif</span>' : ''}
                        </div>
                        <div class="flex items-center gap-1 ml-1">
                            ${!s.is_active ? `<button type="button" class="btn-schedule-activate px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-semibold rounded" data-id="${s.id}">Aktifkan</button>` : ''}
                            <button type="button" class="btn-schedule-duplicate px-2 py-0.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-semibold rounded border border-slate-300" data-id="${s.id}">Duplikasi</button>
                            <button type="button" class="btn-schedule-delete px-2 py-0.5 ${s.is_active ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-red-50 hover:bg-red-100 text-red-600 border border-red-200'} text-[10px] font-semibold rounded" data-id="${s.id}" ${s.is_active ? 'disabled title="Jadwal aktif tidak bisa dihapus"' : ''}>Hapus</button>
                        </div>
                    </div>
                `;
            }).join('');

            container.querySelectorAll('.schedule-card').forEach(card => {
                card.addEventListener('click', (e) => {
                    if (e.target.closest('button')) return;
                    selectSchedule(card.dataset.scheduleId);
                });
            });

            container.querySelectorAll('.btn-schedule-activate').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    await activateScheduleById(btn.dataset.id);
                });
            });

            container.querySelectorAll('.btn-schedule-duplicate').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    await duplicateScheduleById(btn.dataset.id);
                });
            });

            container.querySelectorAll('.btn-schedule-delete').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    await deleteScheduleById(btn.dataset.id);
                });
            });
        }

        function updateHeaderIndicator() {
            const sched = allSchedules.find(s => String(s.id) === String(currentScheduleId));
            const indicator = document.getElementById('active-schedule-indicator');
            const titleEl = document.getElementById('timetable-title');
            const subEl = document.getElementById('timetable-subtitle');
            if (!sched) {
                indicator.textContent = 'Pilih versi jadwal';
                titleEl.textContent = 'Timetable Laboratorium';
                subEl.textContent = 'Senin 10:30–13:00 Kebaktian Umum · Jumat 10:30–14:30 Kegiatan Mahasiswa';
                return;
            }
            const scheduledCount = currentSessions.filter(s => s.status === 'SCHEDULED').length;
            const unscheduledCount = currentSessions.filter(s => s.status === 'UNSCHEDULED').length;
            const dayLabel = currentDayFilter === 'all' ? 'Semua Hari (Senin–Sabtu)' : `Hari ${DAY_NAMES[currentDayFilter] || currentDayFilter}`;
            indicator.textContent = `Versi: ${sched.name} (${scheduledCount} terjadwal, ${unscheduledCount} belum terjadwal)`;
            titleEl.textContent = `${dayLabel} — ${sched.name}`;
            subEl.textContent = `${scheduledCount} terjadwal · ${unscheduledCount} belum terjadwal · Senin 10:30–13:00 Kebaktian Umum · Jumat 10:30–14:30 Kegiatan Mahasiswa`;
        }

        async function selectSchedule(scheduleId) {
            currentScheduleId = scheduleId;
            renderScheduleList();

            const container = document.getElementById('timetable-container');
            container.innerHTML = '<div class="p-12 text-center text-gray-400 text-sm">Memuat sesi jadwal...</div>';

            const { ok, data } = await apiJson(`/admin/schedules/${scheduleId}/sessions`, 'GET');
            if (!ok) {
                Swal.fire('Error', data.message || 'Gagal memuat sesi jadwal', 'error');
                return;
            }

            currentSessions = Array.isArray(data.sessions) ? data.sessions : [];
            renderAll();
        }

        function renderAll() {
            renderScheduleList();
            updateHeaderIndicator();
            renderUnscheduled();
            renderTimetable();
            initDragAndDrop();
        }

        function getSessionDisplayTitle(sec) {
            const matkulId = sec.class_section?.matkul?.id || sec.class_section?.matkul_id || '';
            const matkulNama = sec.class_section?.matkul?.nama || sec.class_section?.matkul?.kode || 'Matkul';
            const matkulKode = sec.class_section?.matkul?.kode || '-';
            const label = sec.class_section?.label || '-';
            return {
                matkulId,
                nama: matkulNama,
                kode: matkulKode,
                label,
                full: `${matkulNama} (${label})`,
                short: `${matkulKode ? matkulKode + ' · ' : ''}${matkulNama} (${label})`,
                lecturer: sec.class_section?.lecturer || '-',
                enrollment: sec.class_section?.enrollment ?? 0,
            };
        }

        function renderUnscheduled() {
            const listEl = document.getElementById('unscheduled-list');
            const countEl = document.getElementById('unscheduled-count');
            const query = (document.getElementById('unscheduled-search')?.value || '').toLowerCase().trim();

            if (!currentScheduleId) {
                countEl.textContent = '0';
                listEl.innerHTML = '<p class="text-xs text-gray-400 text-center py-8">Pilih versi jadwal terlebih dahulu.</p>';
                return;
            }

            const unscheduled = currentSessions.filter(s => s.status === 'UNSCHEDULED');
            countEl.textContent = String(unscheduled.length);

            const filtered = unscheduled.filter(s => {
                if (!query) return true;
                const info = getSessionDisplayTitle(s);
                return `${info.full} ${info.short} ${info.lecturer} ${s.session_type}`.toLowerCase().includes(query);
            });

            if (filtered.length === 0) {
                listEl.innerHTML = `<p class="text-xs text-gray-400 text-center py-8">${unscheduled.length === 0 ? 'Semua sesi sudah terjadwal!' : 'Tidak ada sesi yang cocok.'}</p>`;
                return;
            }

            listEl.innerHTML = filtered.map(s => {
                const info = getSessionDisplayTitle(s);
                const color = getMatkulColor(info.matkulId, info.kode);
                const typeBadgeClass = s.session_type === 'PRAKTIKUM'
                    ? 'bg-emerald-600 text-white'
                    : 'bg-amber-500 text-white';

                return `
                    <div class="session-chip unscheduled-chip cursor-grab active:cursor-grabbing rounded-lg border border-gray-200 border-l-4 p-3 bg-white shadow-sm hover:shadow-md transition-all select-none"
                        style="border-left-color: ${color.bg};"
                        draggable="true"
                        data-session-id="${s.id}"
                        data-duration-min="${s.duration_min}">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="text-xs font-bold text-slate-900 leading-snug">${escapeHtml(info.nama)} (${escapeHtml(info.label)})</h4>
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded ${typeBadgeClass} flex-shrink-0">${s.session_type}</span>
                        </div>
                        <p class="text-[11px] text-slate-600">Mahasiswa: <strong>${info.enrollment}</strong>${info.lecturer && info.lecturer !== '-' ? ` · Dosen: ${escapeHtml(info.lecturer)}` : ''}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Kode: ${escapeHtml(info.kode)} · Durasi: ${s.duration_min} menit</p>
                    </div>
                `;
            }).join('');
        }

        function computeTimeRange() {
            let minMin = 420;  // 07:00
            let maxMin = 1260; // 21:00

            currentSessions.forEach(s => {
                if (s.start_min !== null && s.start_min !== undefined) {
                    minMin = Math.min(minMin, Math.floor(s.start_min / 30) * 30);
                }
                if (s.end_min !== null && s.end_min !== undefined) {
                    maxMin = Math.max(maxMin, Math.ceil(s.end_min / 30) * 30);
                }
                const meetings = s.class_section?.class_meetings || [];
                meetings.forEach(m => {
                    if (m.start_min !== null && m.start_min !== undefined) {
                        minMin = Math.min(minMin, Math.floor(m.start_min / 30) * 30);
                    }
                    if (m.end_min !== null && m.end_min !== undefined) {
                        maxMin = Math.max(maxMin, Math.ceil(m.end_min / 30) * 30);
                    }
                });
            });

            return { minMin, maxMin };
        }

        function computeDaysToRender() {
            const dayFilter = currentDayFilter || document.getElementById('filter-day')?.value || '1';
            if (dayFilter !== 'all') {
                return [parseInt(dayFilter, 10)];
            }
            const hasSunday = currentSessions.some(s => s.status === 'SCHEDULED' && Number(s.day_of_week) === 0);
            return hasSunday ? [0, 1, 2, 3, 4, 5, 6] : [1, 2, 3, 4, 5, 6];
        }

        function renderSessionCardHtml(s, span = 1, clusterLength = 1) {
            const info = getSessionDisplayTitle(s);
            const color = getMatkulColor(info.matkulId, info.kode);
            const minHeightPx = Math.max(38, Math.floor((span * SLOT_HEIGHT_PX - 8) / Math.max(1, clusterLength)));

            const typePillClass = s.session_type === 'PRAKTIKUM'
                ? 'bg-emerald-500/90 text-white'
                : 'bg-amber-500/90 text-white';

            const ringClass = s.is_overridden
                ? 'ring-2 ring-orange-400 border-2 border-orange-300'
                : (s.is_locked ? 'ring-2 ring-white/80 border-2 border-white/80' : 'border');

            const timeLabel = (s.start_min !== null && s.end_min !== null)
                ? `${formatMinToTime(s.start_min)}–${formatMinToTime(s.end_min)}`
                : `${s.duration_min}m`;

            return `
                <div class="session-chip placed-chip w-full flex-1 flex flex-col justify-between rounded-md p-2.5 text-white shadow-sm text-xs select-none ${ringClass} ${s.is_locked ? 'locked' : 'cursor-grab active:cursor-grabbing'}"
                    style="background-color: ${color.bg}; border-color: ${color.border}; min-height: ${minHeightPx}px;"
                    draggable="${s.is_locked ? 'false' : 'true'}"
                    data-session-id="${s.id}"
                    data-duration-min="${s.duration_min}"
                    data-locked="${s.is_locked ? '1' : '0'}"
                    data-overridden="${s.is_overridden ? '1' : '0'}">
                    <div>
                        <div class="flex items-start justify-between gap-1.5">
                            <div class="min-w-0">
                                <p class="font-bold text-xs leading-tight text-white truncate" title="${escapeHtml(info.nama)}">${escapeHtml(info.nama)}</p>
                                <p class="text-[10px] text-white/80 font-mono mt-0.5">${escapeHtml(info.kode)}</p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button type="button"
                                    class="session-action-btn btn-lock-session p-1 rounded bg-black/20 hover:bg-black/35 text-white transition-colors"
                                    data-session-id="${s.id}"
                                    data-locked="${s.is_locked ? '1' : '0'}"
                                    title="${s.is_locked ? 'Buka Kunci Sesi' : 'Kunci Sesi'}">
                                    ${s.is_locked ? ICON_LOCK_CLOSED : ICON_LOCK_OPEN}
                                </button>
                                <button type="button"
                                    class="session-action-btn btn-unplace-session p-1 rounded bg-black/20 hover:bg-red-600 text-white transition-colors"
                                    data-session-id="${s.id}"
                                    title="Keluarkan dari jadwal (Unplace)">
                                    ${ICON_UNPLACE}
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                            <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded bg-white/20 text-white">KELAS ${escapeHtml(info.label)}</span>
                            <span class="px-1.5 py-0.5 text-[10px] font-bold uppercase rounded ${typePillClass}">${s.session_type}</span>
                        </div>
                    </div>

                    <div class="mt-2 pt-1.5 border-t border-white/15 flex items-center justify-between gap-1 text-[10px] text-white/90">
                        <span class="font-mono font-semibold">${timeLabel}</span>
                        <span>Mhs: ${info.enrollment}</span>
                    </div>
                    ${s.is_overridden ? `<div class="mt-1 inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-orange-500 text-white text-[10px] font-bold">${ICON_WARNING}<span>Override Bentrok</span></div>` : ''}
                </div>
            `;
        }

        function renderTimetable() {
            const container = document.getElementById('timetable-container');
            if (!currentScheduleId) {
                container.innerHTML = `
                    <div class="p-12 text-center text-gray-400 text-sm">
                        Pilih atau buat versi jadwal di atas untuk mulai menyusun jadwal.
                    </div>
                `;
                return;
            }

            if (activeLabs.length === 0) {
                container.innerHTML = `
                    <div class="p-12 text-center text-gray-400 text-sm">
                        Tidak ada laboratorium aktif yang ditemukan.
                    </div>
                `;
                return;
            }

            const { minMin, maxMin } = computeTimeRange();
            const days = computeDaysToRender();
            const scheduled = currentSessions.filter(s => s.status === 'SCHEDULED' && s.lab_id && s.day_of_week !== null && s.start_min !== null);

            // Group scheduled sessions into clusters per (day, lab) so rowspan never corrupts table geometry
            const cellMap = {}; // key: `${day}_${labId}_${slotMin}` -> { rowspan, sessions: [], skip: bool }

            days.forEach(day => {
                activeLabs.forEach(lab => {
                    const labSessions = scheduled
                        .filter(s => Number(s.day_of_week) === Number(day) && String(s.lab_id) === String(lab.id))
                        .sort((a, b) => a.start_min - b.start_min || b.end_min - a.end_min);

                    const clusters = [];
                    labSessions.forEach(sess => {
                        const sSlot = Math.floor(sess.start_min / 30) * 30;
                        const eSlot = Math.max(sSlot + 30, Math.ceil((sess.end_min || (sess.start_min + sess.duration_min)) / 30) * 30);
                        const last = clusters[clusters.length - 1];
                        if (last && sSlot < last.endSlot) {
                            last.endSlot = Math.max(last.endSlot, eSlot);
                            last.sessions.push(sess);
                        } else {
                            clusters.push({ startSlot: sSlot, endSlot: eSlot, sessions: [sess] });
                        }
                    });

                    clusters.forEach(cl => {
                        const span = Math.max(1, Math.round((cl.endSlot - cl.startSlot) / 30));
                        const startKey = `${day}_${lab.id}_${cl.startSlot}`;
                        cellMap[startKey] = { rowspan: span, sessions: cl.sessions, skip: false };
                        for (let m = cl.startSlot + 30; m < cl.endSlot; m += 30) {
                            cellMap[`${day}_${lab.id}_${m}`] = { rowspan: 0, sessions: [], skip: true };
                        }
                    });
                });
            });

            let html = `
                <table id="timetable-grid" class="min-w-full border-collapse text-xs">
                    <thead class="bg-slate-50 sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th class="border border-gray-200 px-3 py-3 text-left font-bold text-slate-700 w-32 sticky left-0 bg-slate-50 z-30">Jam / Lab</th>
                            ${activeLabs.map(lab => `
                                <th class="border border-gray-200 px-3 py-3 text-center font-bold text-slate-800 min-w-[185px]" data-lab-header="${lab.id}">
                                    <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                        <span class="font-bold text-slate-800 text-xs uppercase">${escapeHtml(lab.name)}</span>
                                        ${lab.manual_only ? '<span class="px-1.5 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-800 border border-amber-300">Manual</span>' : ''}
                                    </div>
                                    <div class="text-[11px] font-normal text-slate-500 mt-0.5">Kapasitas: ${lab.capacity ?? 0}</div>
                                </th>
                            `).join('')}
                        </tr>
                    </thead>
                    <tbody>
            `;

            days.forEach(day => {
                const dayName = DAY_NAMES[day] || `Hari ${day}`;
                if (days.length > 1) {
                    html += `
                        <tr class="bg-slate-800 text-white">
                            <td colspan="${activeLabs.length + 1}" class="px-4 py-2 font-bold uppercase tracking-wider text-xs sticky left-0">
                                Hari ${dayName}
                            </td>
                        </tr>
                    `;
                }

                for (let slotMin = minMin; slotMin < maxMin; slotMin += 30) {
                    const timeStr = `${formatMinToTime(slotMin)} - ${formatMinToTime(slotMin + 30)}`;
                    const blocked = getBlockedSlot(day, slotMin);
                    html += `<tr class="h-[44px]" style="height: ${SLOT_HEIGHT_PX}px;">`;
                    html += `
                        <td class="border border-gray-200 px-3 py-1.5 font-mono text-[11px] font-medium text-slate-600 bg-slate-50/90 whitespace-nowrap sticky left-0 z-10">
                            ${timeStr}
                        </td>
                    `;

                    activeLabs.forEach(lab => {
                        const key = `${day}_${lab.id}_${slotMin}`;
                        const entry = cellMap[key];
                        if (entry && entry.skip) {
                            return;
                        }

                        const hasSessions = entry && entry.sessions.length > 0;
                        const span = (entry && entry.rowspan > 1) ? entry.rowspan : 1;
                        const rowspanAttr = span > 1 ? `rowspan="${span}"` : '';
                        const cellHeightPx = span * SLOT_HEIGHT_PX;

                        if (blocked && !hasSessions) {
                            html += `
                                <td class="timetable-blocked-cell blocked-slot-pattern border border-gray-200 px-2 py-1 text-center align-middle select-none"
                                    style="height: ${SLOT_HEIGHT_PX}px;"
                                    data-lab-id="${lab.id}"
                                    data-day="${day}"
                                    data-start-min="${slotMin}"
                                    data-blocked="1">
                                    <span class="text-[11px] font-medium text-slate-500">${escapeHtml(blocked.label)}</span>
                                </td>
                            `;
                            return;
                        }

                        html += `
                            <td ${rowspanAttr}
                                class="timetable-cell border border-gray-200 align-top transition-colors ${hasSessions ? 'bg-slate-50/40' : 'bg-white hover:bg-indigo-50/20'}"
                                style="height: ${cellHeightPx}px; padding: 3px; vertical-align: top;"
                                data-lab-id="${lab.id}"
                                data-day="${day}"
                                data-start-min="${slotMin}">
                                <div class="cell-dropzone w-full h-full flex flex-col gap-1"
                                    style="height: 100%; min-height: ${cellHeightPx - 6}px;"
                                    data-lab-id="${lab.id}"
                                    data-day="${day}"
                                    data-start-min="${slotMin}">
                                    ${hasSessions ? entry.sessions.map(s => renderSessionCardHtml(s, span, entry.sessions.length)).join('') : ''}
                                </div>
                            </td>
                        `;
                    });

                    html += `</tr>`;
                }
            });

            html += `</tbody></table>`;
            container.innerHTML = html;

            // Bind lock and unplace buttons inside timetable
            container.querySelectorAll('.btn-unplace-session').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    await handleUnplaceSession(btn.dataset.sessionId);
                });
            });

            container.querySelectorAll('.btn-lock-session').forEach(btn => {
                btn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const sessionId = btn.dataset.sessionId;
                    const currentlyLocked = btn.dataset.locked === '1';
                    await handleLockSession(sessionId, !currentlyLocked);
                });
            });
        }

        function initDragAndDrop() {
            sortableInstances.forEach(inst => {
                try { inst.destroy(); } catch (_) {}
            });
            sortableInstances = [];

            if (!currentScheduleId) return;

            const unscheduledEl = document.getElementById('unscheduled-list');
            if (unscheduledEl && window.Sortable) {
                sortableInstances.push(new Sortable(unscheduledEl, {
                    group: {
                        name: 'sessions',
                        pull: true,
                        put: true,
                    },
                    animation: 150,
                    filter: '.locked, .session-action-btn',
                    preventOnFilter: false,
                    onAdd: async (evt) => {
                        const item = evt.item;
                        const sessionId = item.dataset.sessionId;
                        if (sessionId) {
                            await handleUnplaceSession(sessionId);
                        }
                    },
                }));
            }

            document.querySelectorAll('.cell-dropzone').forEach(zone => {
                if (window.Sortable) {
                    sortableInstances.push(new Sortable(zone, {
                        group: {
                            name: 'sessions',
                            pull: true,
                            put: true,
                        },
                        animation: 150,
                        filter: '.locked, .session-action-btn',
                        preventOnFilter: false,
                        onMove: (evt) => {
                            if (evt.dragged.classList.contains('locked') || evt.dragged.dataset.locked === '1') {
                                return false;
                            }
                            return true;
                        },
                        onAdd: async (evt) => {
                            const item = evt.item;
                            if (item.classList.contains('locked') || item.dataset.locked === '1') {
                                renderAll();
                                return;
                            }
                            const sessionId = item.dataset.sessionId;
                            const durationMin = parseInt(item.dataset.durationMin, 10) || 60;
                            const labId = zone.dataset.labId;
                            const day = parseInt(zone.dataset.day, 10);
                            const startMin = parseInt(zone.dataset.startMin, 10);
                            await handlePlaceSession(sessionId, labId, day, startMin, durationMin);
                        },
                    }));
                }
            });

            // Native HTML5 Drag-and-Drop fallback support on .session-chip and .timetable-cell
            document.querySelectorAll('.session-chip').forEach(chip => {
                chip.addEventListener('dragstart', (e) => {
                    if (chip.classList.contains('locked') || chip.dataset.locked === '1') {
                        e.preventDefault();
                        return;
                    }
                    if (e.dataTransfer) {
                        e.dataTransfer.setData('text/plain', JSON.stringify({
                            sessionId: chip.dataset.sessionId,
                            durationMin: parseInt(chip.dataset.durationMin, 10) || 60,
                        }));
                    }
                });
            });

            document.querySelectorAll('.timetable-cell').forEach(cell => {
                cell.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    cell.classList.add('drag-over');
                });
                cell.addEventListener('dragleave', () => {
                    cell.classList.remove('drag-over');
                });
                cell.addEventListener('drop', async (e) => {
                    e.preventDefault();
                    cell.classList.remove('drag-over');
                    if (placementInProgress) return;
                    let payload = null;
                    try {
                        const raw = e.dataTransfer?.getData('text/plain');
                        if (raw) payload = JSON.parse(raw);
                    } catch (_) {}
                    if (!payload || !payload.sessionId) return;

                    const labId = cell.dataset.labId;
                    const day = parseInt(cell.dataset.day, 10);
                    const startMin = parseInt(cell.dataset.startMin, 10);
                    await handlePlaceSession(payload.sessionId, labId, day, startMin, payload.durationMin);
                });
            });
        }

        async function handlePlaceSession(sessionId, labId, dayOfWeek, startMin, durationMin, force = false) {
            if (placementInProgress && !force) return;
            placementInProgress = true;

            const endMin = startMin + durationMin;
            try {
                const { ok, status, data } = await apiJson(`/admin/schedules/sessions/${sessionId}/place`, 'PATCH', {
                    lab_id: labId,
                    day_of_week: dayOfWeek,
                    start_min: startMin,
                    end_min: endMin,
                    force: force,
                });

                if (status === 409 && data.conflict) {
                    const conflictNames = (data.violations || [])
                        .map(v => `${v.matkul || v.kode || 'Sesi'} (${v.label || '-'})`)
                        .join(', ');

                    const confirm = await Swal.fire({
                        title: 'Bentrok Jadwal!',
                        text: `Ada bentrok dengan ${conflictNames || 'sesi lain'}. Paksa tempatkan?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#f97316',
                        confirmButtonText: 'Ya, Paksa Tempatkan!',
                        cancelButtonText: 'Batal',
                    });

                    if (confirm.isConfirmed) {
                        await handlePlaceSession(sessionId, labId, dayOfWeek, startMin, durationMin, true);
                        return;
                    } else {
                        renderAll();
                        return;
                    }
                }

                if (!ok) {
                    Swal.fire('Gagal', data.message || 'Gagal menempatkan sesi', 'error');
                    renderAll();
                    return;
                }

                // Update session in local state
                currentSessions = currentSessions.map(s => String(s.id) === String(sessionId) ? data.session : s);
                renderAll();
            } finally {
                placementInProgress = false;
            }
        }

        async function handleUnplaceSession(sessionId) {
            const { ok, data } = await apiJson(`/admin/schedules/sessions/${sessionId}/unplace`, 'PATCH');
            if (!ok) {
                Swal.fire('Gagal', data.message || 'Gagal mengeluarkan sesi dari jadwal', 'error');
                renderAll();
                return;
            }
            currentSessions = currentSessions.map(s => String(s.id) === String(sessionId) ? data.session : s);
            renderAll();
        }

        async function handleLockSession(sessionId, locked) {
            const { ok, data } = await apiJson(`/admin/schedules/sessions/${sessionId}/lock`, 'PATCH', { locked });
            if (!ok) {
                Swal.fire('Gagal', data.message || 'Gagal mengubah status kunci sesi', 'error');
                return;
            }
            currentSessions = currentSessions.map(s => String(s.id) === String(sessionId) ? data.session : s);
            renderAll();
        }
    </script>
@endsection
