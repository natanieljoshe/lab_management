@extends('layouts.user')

@section('title', 'Riwayat Pengajuan Helpdesk')

@section('style')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'petra-blue': '#1a237e',
                        'petra-yellow': '#ffca28',
                        'petra-darkgray': '#424242',
                        'petra-gray': '#f1f5f9',
                    }
                }
            }
        }
    </script>
    <style>
        .card-hover:hover {
            transform: none;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
        }

        .booking-card {
            transition: all 0.3s ease-in-out;
        }

        .hidden-card {
            display: none !important;
        }

        .helpdesk-history-page { background: #f7f8fa; }
        .helpdesk-history-page .booking-card {
            border-radius: .75rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .03);
        }
        .helpdesk-history-page .booking-card:hover { transform: none; }
        .history-tabbar {
            width: 100%;
            border-bottom: 1px solid #e5e7eb;
            gap: 1.5rem;
        }
        .history-tabbar button {
            position: relative;
            padding: .75rem .15rem 1rem;
            border-radius: 0;
            color: #6b7280;
            font-weight: 600;
            box-shadow: none;
        }
        .history-tabbar button.is-active { color: #1a237e; }
        .history-tabbar button.is-active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 2px;
            background: #1a237e;
        }
        .history-filter {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            box-shadow: none;
        }
        .history-stat {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: .75rem;
            padding: 1rem 1.1rem;
        }
        .helpdesk-feedback-modal .feedback-modal-panel { box-shadow: 0 18px 45px rgba(15, 23, 42, .18); }
        .helpdesk-feedback-modal #modal-responses-container { position: relative; padding-left: 1.15rem; }
        .helpdesk-feedback-modal #modal-responses-container::before {
            content: '';
            position: absolute;
            left: .25rem;
            top: .5rem;
            bottom: .5rem;
            width: 1px;
            background: #dbe2ea;
        }
        .helpdesk-feedback-modal #modal-responses-container .response-entry { position: relative; border-radius: .75rem; }
        .helpdesk-feedback-modal #modal-responses-container .response-entry::before {
            content: '';
            position: absolute;
            left: -1.23rem;
            top: 1.1rem;
            width: .5rem;
            height: .5rem;
            border-radius: 999px;
            background: #1a237e;
            box-shadow: 0 0 0 3px #f7f8fa;
        }
        .helpdesk-feedback-modal #modal-responses-container .response-empty::before { display: none; }
    </style>
@endsection

@section('body')
    @include('user.partials.navbar')

    <div class="helpdesk-history-page min-h-screen px-4 pt-24 pb-12">
      <div class="container mx-auto max-w-5xl">

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-7 gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.18em] text-gray-500 mb-3">Pusat bantuan laboratorium</p>
                <h1 class="text-3xl font-bold tracking-tight text-petra-blue">Riwayat laporan</h1>
                <p class="text-gray-500 mt-2">Pantau status laporan dan tanggapan tim laboratorium.</p>
            </div>
            <a href="{{ route('user.helpdesk.index') }}"
                class="group bg-petra-blue text-white px-5 py-2.5 rounded-lg hover:bg-blue-800 transition-all flex items-center gap-2 font-medium">
                <i class="fa-solid fa-plus transition-transform group-hover:rotate-90"></i>
                <span class="hidden sm:inline">Buat Laporan Baru</span>
                <span class="sm:hidden">Lapor</span>
            </a>
        </div>

        @php
            $activeRepairs = $repairs->filter(fn ($repair) => $repair->items->contains(fn ($item) => (int) $item->pivot->status !== 2) || $repair->components->contains(fn ($component) => (int) $component->pivot->status !== 2))->count();
            $feedbackOpen = $feedbacks->whereIn('status', [1, 2])->count();
            $totalReports = $repairs->count() + $feedbacks->count();
            $activeReports = $activeRepairs + $feedbackOpen;
            $resolvedReports = max(0, $totalReports - $activeReports);
        @endphp

        {{-- Summary --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-7">
            <div class="history-stat"><p class="text-xs text-gray-500">Total laporan</p><p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalReports }}</p></div>
            <div class="history-stat"><p class="text-xs text-gray-500">Sedang diproses</p><p class="text-2xl font-bold text-amber-700 mt-1">{{ $activeReports }}</p></div>
            <div class="history-stat"><p class="text-xs text-gray-500">Selesai</p><p class="text-2xl font-bold text-emerald-700 mt-1">{{ $resolvedReports }}</p></div>
        </div>

        {{-- Tab Selector --}}
        <div class="history-tabbar flex items-center mb-6">
            <button type="button" onclick="switchHistTab('repair')" id="tab-btn-repair-hist"
                class="is-active text-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-screwdriver-wrench text-petra-blue"></i>
                <span>Laporan Perbaikan</span>
                <span class="text-xs font-bold text-gray-400">{{ $repairs->count() }}</span>
            </button>
            <button type="button" onclick="switchHistTab('feedback')" id="tab-btn-feedback-hist"
                class="text-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-comments text-indigo-500"></i>
                <span>Keluhan & Saran</span>
                <span class="text-xs font-bold text-gray-400">{{ $feedbacks->count() }}</span>
            </button>
        </div>

        {{-- Filter Bar --}}
        <div class="history-filter p-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="relative w-full sm:w-1/2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Cari tiket, nama barang, atau uraian..."
                        class="pl-10 w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-petra-blue focus:border-petra-blue transition-colors shadow-sm"
                        onkeyup="applyFilters()">
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button onclick="resetFilters()"
                        class="px-4 py-2 text-sm text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-transparent hover:border-red-100 whitespace-nowrap">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- PANE 1: RIWAYAT PERBAIKAN BARANG (REPAIR)               --}}
        {{-- ========================================================= --}}
        <div id="tab-pane-repair-hist" class="space-y-6">
            @if ($repairs->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada riwayat perbaikan</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Anda belum pernah mengajukan laporan perbaikan perangkat lab.</p>
                    <a href="{{ route('user.helpdesk.index') }}" class="bg-petra-blue text-white px-5 py-2.5 rounded-lg shadow-sm font-semibold text-sm hover:bg-blue-800">
                        Laporkan Kerusakan Sekarang
                    </a>
                </div>
            @else
                <div class="grid gap-6" id="repairList">
                    @foreach ($repairs as $repair)
                        @php
                            $allItems = $repair->items;
                            $allComps = $repair->components;
                            $totalCount = $allItems->count() + $allComps->count();
                            $searchTerms = strtolower($repair->name . ' ' . $allItems->pluck('name')->join(' ') . ' ' . $allComps->pluck('name')->join(' '));
                        @endphp
                        <div class="booking-card repair-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 card-hover"
                            data-search="{{ $searchTerms }}">
                            {{-- Card Header --}}
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-petra-blue flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900">{{ $repair->name }}</span>
                                            <span class="text-xs px-2 py-0.5 rounded bg-blue-50 text-petra-blue border border-blue-200 font-semibold">
                                                {{ $totalCount }} Perangkat
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <i class="fa-regular fa-clock mr-1"></i>{{ $repair->created_at->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 self-start sm:self-auto">
                                    Periode: <strong class="text-gray-700">{{ $repair->period->academic_year ?? '-' }}</strong>
                                </div>
                            </div>

                            {{-- Card Body: Item List --}}
                            <div class="p-6">
                                <div class="grid md:grid-cols-3 gap-6">
                                    <div class="md:col-span-2 space-y-4">
                                @foreach ($allItems as $item)
                                    @php
                                        $pivot = $item->pivot;
                                        $status = (int) $pivot->status;
                                    @endphp
                                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/70 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-bold text-gray-900">{{ $item->name }}</span>
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-100 text-petra-blue">{{ $item->type->name ?? 'Item' }}</span>
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-gray-200 text-gray-700">
                                                    {{ $item->desk ? 'Meja ' . $item->desk->location : ($item->lab->name ?? 'Storage Lab') }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 font-mono">SN: {{ $item->serial_code }}</p>
                                            <div class="text-xs text-gray-700 bg-white p-2.5 rounded-lg border border-gray-200 mt-2">
                                                <span class="font-bold text-gray-900">Kendala:</span> {{ $pivot->issue_description }}
                                            </div>
                                            @if ($pivot->repair_notes)
                                                <div class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 p-2.5 rounded-lg mt-2 flex items-start gap-2">
                                                    <i class="fa-solid fa-wrench text-emerald-600 mt-0.5"></i>
                                                    <div>
                                                        <span class="font-bold">Catatan Teknisi:</span> {{ $pivot->repair_notes }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex md:flex-col items-center md:items-end justify-between gap-2 flex-shrink-0">
                                            @if ($status === 2)
                                                @if ($pivot->is_successful)
                                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <i class="fa-solid fa-circle-check mr-1"></i>Berhasil diperbaiki
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <i class="fa-solid fa-circle-xmark mr-1"></i>Tidak berhasil diperbaiki
                                                    </span>
                                                @endif
                                            @elseif ($status === 1)
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>Sedang diproses
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                    <i class="fa-regular fa-clock mr-1"></i>Menunggu diproses
                                                </span>
                                            @endif

                                            @if ($pivot->start_date)
                                                <span class="text-[11px] text-gray-400">
                                                    Mulai: {{ \Carbon\Carbon::parse($pivot->start_date)->translatedFormat('d M Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @foreach ($allComps as $comp)
                                    @php
                                        $pivot = $comp->pivot;
                                        $status = (int) $pivot->status;
                                    @endphp
                                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/70 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-bold text-gray-900">{{ $comp->name }}</span>
                                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-indigo-100 text-indigo-800">Komponen: {{ $comp->type->name ?? 'Komponen' }}</span>
                                            </div>
                                            <div class="text-xs text-gray-700 bg-white p-2.5 rounded-lg border border-gray-200 mt-2">
                                                <span class="font-bold text-gray-900">Kendala:</span> {{ $pivot->issue_description }}
                                            </div>
                                        </div>

                                        <div class="flex md:flex-col items-center md:items-end justify-between gap-2 flex-shrink-0">
                                            @if ($status === 2)
                                                @if ($pivot->is_successful)
                                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        <i class="fa-solid fa-circle-check mr-1"></i>Berhasil diperbaiki
                                                    </span>
                                                @else
                                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <i class="fa-solid fa-circle-xmark mr-1"></i>Tidak berhasil diperbaiki
                                                    </span>
                                                @endif
                                            @elseif ($status === 1)
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <i class="fa-solid fa-spinner fa-spin mr-1"></i>Sedang diproses
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                                    <i class="fa-regular fa-clock mr-1"></i>Menunggu diproses
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                    </div>

                                    <div class="col-span-1 bg-gray-50 rounded-lg p-4 border border-gray-100 h-fit">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Ringkasan Laporan</h4>
                                        <dl class="space-y-3 text-sm">
                                            <div>
                                                <dt class="text-xs text-gray-500">Perangkat dilaporkan</dt>
                                                <dd class="font-semibold text-gray-800 mt-0.5">{{ $totalCount }} perangkat</dd>
                                            </div>
                                            <div>
                                                <dt class="text-xs text-gray-500">Periode</dt>
                                                <dd class="font-semibold text-gray-800 mt-0.5">{{ $repair->period->academic_year ?? '-' }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ========================================================= --}}
        {{-- PANE 2: RIWAYAT KELUHAN & SARAN (FEEDBACK)               --}}
        {{-- ========================================================= --}}
        <div id="tab-pane-feedback-hist" class="hidden space-y-6">
            @if ($feedbacks->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Belum ada tiket keluhan atau saran</h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6">Punya masukan atau kendala fasilitas? Jangan ragu untuk menyampaikannya ke pengelola lab.</p>
                    <a href="{{ route('user.helpdesk.index') }}" class="bg-petra-blue text-white px-5 py-2.5 rounded-lg shadow-sm font-semibold text-sm hover:bg-blue-800">
                        Kirim Masukan Sekarang
                    </a>
                </div>
            @else
                <div class="grid gap-6" id="feedbackList">
                    @foreach ($feedbacks as $fb)
                        @php
                            $statusClasses = [
                                1 => 'bg-blue-100 text-blue-800 border-blue-200',
                                2 => 'bg-amber-100 text-amber-800 border-amber-200',
                                3 => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                4 => 'bg-gray-100 text-gray-800 border-gray-200',
                                5 => 'bg-red-100 text-red-800 border-red-200',
                            ];
                            $searchTerms = strtolower($fb->ticket_number . ' ' . $fb->subject . ' ' . $fb->description);
                        @endphp
                        <div class="booking-card feedback-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 card-hover"
                            data-search="{{ $searchTerms }}">
                            {{-- Card Header --}}
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg {{ $fb->type === 'KELUHAN' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600' }} flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid {{ $fb->type === 'KELUHAN' ? 'fa-triangle-exclamation' : 'fa-lightbulb' }}"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono font-bold text-petra-blue bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                                {{ $fb->ticket_number }}
                                            </span>
                                            <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $fb->type === 'KELUHAN' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                {{ $fb->type }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            <i class="fa-regular fa-clock mr-1"></i>{{ $fb->created_at->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusClasses[$fb->status] ?? 'bg-gray-100 text-gray-700' }} self-start sm:self-auto">
                                    {{ $fb->status_label }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-6 space-y-4">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900 mb-1">{{ $fb->subject }}</h4>
                                    <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ $fb->description }}</p>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <i class="fa-solid fa-location-dot text-gray-400"></i>
                                        <span>Lokasi: <strong class="text-gray-700">{{ $fb->lab ? $fb->lab->name : 'Fasilitas Umum' }}</strong></span>
                                    </div>

                                    <button type="button" onclick="openFeedbackDetail('{{ $fb->id }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-petra-blue hover:bg-blue-800 text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>Lihat Tanggapan ({{ $fb->responses->count() }})</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

      </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL DIALOG DETAIL & BALASAN FEEDBACK                     --}}
    {{-- ========================================================= --}}
    <div id="feedbackDetailModal" class="helpdesk-feedback-modal fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:py-10">
            <div class="fixed inset-0 bg-slate-950/45" onclick="closeFeedbackDetail()"></div>

            <div class="feedback-modal-panel relative w-full max-w-xl overflow-hidden text-left bg-white rounded-xl border border-gray-200">
                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span id="modal-ticket-badge" class="px-2 py-1 rounded-md text-[11px] font-mono font-bold bg-blue-50 text-petra-blue border border-blue-100">
                                    FB-000000
                                </span>
                                <span id="modal-type-badge" class="px-2 py-1 rounded-md text-[11px] font-bold bg-gray-100 text-gray-700">
                                    KELUHAN
                                </span>
                                <span id="modal-status-badge" class="px-2 py-1 rounded-md text-[11px] font-bold bg-gray-100 text-gray-700">
                                    Open
                                </span>
                            </div>
                            <h3 id="modal-title" class="text-lg font-bold leading-snug text-gray-900">Subjek Pengaduan</h3>
                            <p class="mt-1 text-xs text-gray-500">
                                <span id="modal-reporter-info">Laporan Anda</span>
                                <span class="mx-1 text-gray-300">·</span>
                                <span id="modal-created-at">Tanggal</span>
                            </p>
                        </div>
                        <button type="button" onclick="closeFeedbackDetail()" aria-label="Tutup detail laporan"
                            class="flex-shrink-0 inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-800 transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-5 space-y-6 max-h-[68vh] overflow-y-auto">
                    <section class="rounded-lg border border-gray-200 bg-gray-50/70 p-4">
                        <p class="mb-2 text-[11px] font-bold uppercase tracking-[.14em] text-gray-500">Isi laporan</p>
                        <p id="modal-description" class="text-sm text-gray-700 whitespace-pre-line leading-relaxed"></p>
                    </section>

                    <section>
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900">Riwayat tanggapan</h4>
                                <p class="text-xs text-gray-500 mt-0.5">Pembaruan dari tim laboratorium.</p>
                            </div>
                            <span id="modal-response-count" class="text-xs font-semibold text-gray-500">0 tanggapan</span>
                        </div>
                        <div id="modal-responses-container" class="space-y-3">
                            {{-- Diisi secara dinamis oleh JS --}}
                        </div>
                    </section>
                </div>

                {{-- Modal Footer --}}
                <div class="px-5 py-3.5 border-t border-gray-200 flex justify-end">
                    <button type="button" onclick="closeFeedbackDetail()"
                        class="px-4 py-2 bg-petra-blue hover:bg-blue-800 text-white font-semibold text-xs rounded-md transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    let currentTab = 'repair';
    let feedbackModalPreviousOverflow = '';

    function switchHistTab(tab) {
        currentTab = tab;
        const btnRepair = document.getElementById('tab-btn-repair-hist');
        const btnFeedback = document.getElementById('tab-btn-feedback-hist');
        const paneRepair = document.getElementById('tab-pane-repair-hist');
        const paneFeedback = document.getElementById('tab-pane-feedback-hist');

        if (tab === 'repair') {
            btnRepair.className = 'is-active text-sm transition-all flex items-center gap-2';
            btnFeedback.className = 'text-sm transition-all flex items-center gap-2';
            paneRepair.classList.remove('hidden');
            paneFeedback.classList.add('hidden');
        } else {
            btnFeedback.className = 'is-active text-sm transition-all flex items-center gap-2';
            btnRepair.className = 'text-sm transition-all flex items-center gap-2';
            paneFeedback.classList.remove('hidden');
            paneRepair.classList.add('hidden');
        }
        applyFilters();
    }

    function applyFilters() {
        const query = (document.getElementById('searchInput')?.value || '').toLowerCase().trim();
        const activeCards = currentTab === 'repair'
            ? document.querySelectorAll('.repair-card')
            : document.querySelectorAll('.feedback-card');

        activeCards.forEach(card => {
            const text = (card.getAttribute('data-search') || '').toLowerCase();
            if (!query || text.includes(query)) {
                card.classList.remove('hidden-card');
            } else {
                card.classList.add('hidden-card');
            }
        });
    }

    function resetFilters() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';
        applyFilters();
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function openFeedbackDetail(feedbackId) {
        try {
            Swal.fire({ title: 'Memuat tanggapan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const res = await fetch(`/helpdesk/feedback/${feedbackId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            Swal.close();

            if (!data.success) throw new Error(data.message || 'Gagal memuat detail tanggapan');

            const fb = data.feedback;
            document.getElementById('modal-ticket-badge').textContent = fb.ticket_number;
            document.getElementById('modal-title').textContent = fb.subject;
            document.getElementById('modal-reporter-info').textContent = `Laporan Anda (${fb.type})`;
            document.getElementById('modal-created-at').textContent = new Date(fb.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
            document.getElementById('modal-description').textContent = fb.description;

            const typeBadge = document.getElementById('modal-type-badge');
            typeBadge.textContent = fb.type === 'KELUHAN' ? 'Keluhan' : 'Saran';
            typeBadge.className = `px-2 py-1 rounded-md text-[11px] font-bold ${fb.type === 'KELUHAN' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'}`;

            const statusMap = {
                1: { label: 'Menunggu diproses', className: 'bg-blue-50 text-blue-700' },
                2: { label: 'Sedang ditangani', className: 'bg-amber-50 text-amber-700' },
                3: { label: 'Selesai', className: 'bg-emerald-50 text-emerald-700' },
                4: { label: 'Ditutup', className: 'bg-gray-100 text-gray-700' },
                5: { label: 'Ditolak', className: 'bg-red-50 text-red-700' },
            };
            const currentStatus = statusMap[fb.status] || { label: 'Status tidak diketahui', className: 'bg-gray-100 text-gray-700' };
            const statusBadge = document.getElementById('modal-status-badge');
            statusBadge.textContent = currentStatus.label;
            statusBadge.className = `px-2 py-1 rounded-md text-[11px] font-bold ${currentStatus.className}`;

            const respContainer = document.getElementById('modal-responses-container');
            const responseCount = fb.responses?.length || 0;
            document.getElementById('modal-response-count').textContent = `${responseCount} tanggapan`;
            if (!fb.responses || fb.responses.length === 0) {
                respContainer.innerHTML = `
                    <div class="response-empty p-5 text-center text-xs text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                        <i class="fa-regular fa-clock text-lg text-amber-500 mb-1 block"></i>
                        Belum ada balasan dari tim pengelola laboratorium. Laporan Anda sedang dalam antrean tindak lanjut.
                    </div>
                `;
            } else {
                respContainer.innerHTML = fb.responses.map((r, index) => `
                    <article class="response-entry p-3.5 bg-white border border-gray-200 space-y-2">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 text-xs">
                            <span class="font-bold text-gray-800">
                                ${escapeHtml(r.responder ? r.responder.name : 'Pengelola Lab')}
                                <span class="ml-1 font-normal text-gray-400">Tanggapan ${index + 1}</span>
                            </span>
                            <time class="text-gray-400">${new Date(r.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</time>
                        </div>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">${escapeHtml(r.message || r.response || '-')}</p>
                    </article>
                `).join('');
            }

            const modal = document.getElementById('feedbackDetailModal');
            feedbackModalPreviousOverflow = document.body.style.overflow;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            modal.querySelector('button[aria-label="Tutup detail laporan"]')?.focus();
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    }

    function closeFeedbackDetail() {
        const modal = document.getElementById('feedbackDetailModal');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = feedbackModalPreviousOverflow;
    }

    document.addEventListener('keydown', event => {
        const modal = document.getElementById('feedbackDetailModal');
        if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeFeedbackDetail();
        }
    });
</script>
@endsection
