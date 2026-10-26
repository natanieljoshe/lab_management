@extends('layouts.user')

@section('title', 'Riwayat Pengajuan Helpdesk')

@section('style')
    @include('user.partials.helpdesk-theme')
    <style>
        .hidden-card { display: none !important; }

        /* ============================================================
           HEADER
           ============================================================ */
        .hx-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 32px; flex-wrap: wrap; position: relative; }
        .hx-head .hx-title { margin-bottom: 22px; }

        /* ============================================================
           RINGKASAN
           ============================================================ */
        .hx-stats { display: grid; grid-template-columns: repeat(3, 1fr); border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); margin-bottom: 64px; }
        .hx-stat { padding: 30px 32px; }
        .hx-stat + .hx-stat { border-left: 1px solid var(--line); }
        .hx-stat-label { font-size: .64rem; font-weight: 700; letter-spacing: .26em; text-transform: uppercase; color: var(--clr-muted); }
        .hx-stat-num { margin-top: 14px; font-family: var(--serif); font-weight: 500; font-size: 3.6rem; line-height: 1; color: var(--clr-navy); }
        .hx-stat-num.is-orange { color: var(--clr-orange); }
        .hx-stat-num.is-ok { color: var(--ok); }

        /* ============================================================
           TAB
           ============================================================ */
        .hx-tabs { display: flex; gap: 44px; border-bottom: 1px solid var(--line); margin-bottom: 36px; }
        .hx-tab {
            position: relative; display: inline-flex; align-items: center; gap: 12px; padding: 0 0 18px; background: none; border: 0; cursor: pointer;
            font-family: var(--sans); font-size: .7rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--clr-muted);
            transition: color .4s var(--ease);
        }
        .hx-tab::after {
            content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 2px; background: var(--clr-orange);
            transform: scaleX(0); transform-origin: right; transition: transform .6s var(--ease-io);
        }
        .hx-tab:hover { color: var(--clr-navy); }
        .hx-tab.is-active { color: var(--clr-navy); }
        .hx-tab.is-active::after { transform: scaleX(1); transform-origin: left; }
        .hx-tab i { color: var(--clr-orange); }
        .hx-tab .count { font-family: var(--serif); font-style: italic; font-size: 1.15rem; letter-spacing: 0; color: var(--clr-orange); font-weight: 600; }

        /* ============================================================
           FILTER
           ============================================================ */
        .hx-filter { display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap; margin-bottom: 44px; }
        .hx-search { position: relative; flex: 1; min-width: 260px; max-width: 520px; }
        .hx-search i { position: absolute; left: 0; top: 50%; transform: translateY(-50%); color: var(--clr-orange); font-size: .85rem; }
        .hx-search .form-input { padding-left: 28px; }

        /* ============================================================
           KARTU
           ============================================================ */
        .hx-list { display: grid; gap: 32px; }
        .hx-card { background: var(--panel); border: 1px solid var(--line); box-shadow: 0 40px 80px -64px rgba(26, 45, 74, .5); }
        .hx-card-head { display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap; padding: 24px 32px; border-bottom: 1px solid var(--line); }
        .hx-card-id { display: flex; align-items: center; gap: 18px; min-width: 0; }
        .hx-card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid var(--line); color: var(--clr-orange); font-size: 1.05rem; }
        .hx-card-icon.is-bad { color: var(--danger); }
        .hx-card-icon.is-ok { color: var(--ok); }
        .hx-card-title { display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; }
        .hx-card-name { font-family: var(--serif); font-weight: 600; font-size: 1.55rem; line-height: 1.1; color: var(--clr-navy); }
        .hx-meta { margin-top: 6px; font-size: .74rem; color: var(--clr-muted); }
        .hx-meta i { margin-right: 6px; color: var(--clr-orange); }
        .hx-period { font-size: .64rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--clr-muted); }
        .hx-period strong { margin-left: 8px; font-family: var(--serif); font-size: 1.15rem; letter-spacing: 0; text-transform: none; color: var(--clr-navy); }

        .hx-card-body { padding: 32px; }
        .hx-split { display: grid; grid-template-columns: 1fr 270px; gap: 36px; align-items: start; }
        .hx-rows { display: grid; gap: 16px; min-width: 0; }

        .hx-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; padding: 22px 24px; border: 1px solid var(--line); background: rgba(255, 255, 255, .55); }
        .hx-row-main { flex: 1; min-width: 0; }
        .hx-row-title { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
        .hx-row-name { font-family: var(--serif); font-weight: 600; font-size: 1.3rem; line-height: 1.15; color: var(--clr-navy); }
        .hx-sn { margin-top: 6px; font-size: .72rem; color: var(--clr-muted); }
        .hx-issue { margin-top: 16px; padding: 12px 16px; border-left: 2px solid var(--clr-orange); background: rgba(245, 126, 33, .07); font-size: .84rem; line-height: 1.7; color: var(--clr-navy); }
        .hx-issue b, .hx-note b { display: block; margin-bottom: 3px; font-size: .6rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--clr-orange); }
        .hx-note { margin-top: 10px; padding: 12px 16px; border-left: 2px solid var(--clr-navy); background: rgba(26, 45, 74, .05); font-size: .84rem; line-height: 1.7; color: var(--clr-navy); }
        .hx-note b { color: var(--clr-navy); }
        .hx-row-side { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }
        .hx-date { font-size: .7rem; color: var(--clr-muted); }

        .hx-summary { position: relative; overflow: hidden; background: var(--clr-navy); color: var(--clr-bg); padding: 28px 26px; }
        .hx-summary h4 { font-size: .62rem; font-weight: 700; letter-spacing: .28em; text-transform: uppercase; color: var(--clr-orange); margin-bottom: 20px; }
        .hx-summary dl { margin: 0; }
        .hx-summary dt { font-size: .64rem; letter-spacing: .18em; text-transform: uppercase; color: rgba(251, 250, 245, .6); }
        .hx-summary dd { margin: 6px 0 0; font-family: var(--serif); font-size: 1.6rem; line-height: 1.1; font-weight: 500; color: var(--clr-bg); }
        .hx-summary div + div { margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--line-light); }

        /* Feedback */
        .hx-subject { font-family: var(--serif); font-weight: 600; font-size: 1.8rem; line-height: 1.1; color: var(--clr-navy); }
        .hx-desc-text { margin-top: 12px; font-size: .92rem; line-height: 1.85; color: var(--clr-muted); white-space: pre-line; }
        .hx-card-foot { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-top: 26px; padding-top: 22px; border-top: 1px solid var(--line); }
        .hx-loc { font-size: .78rem; color: var(--clr-muted); }
        .hx-loc i { margin-right: 8px; color: var(--clr-orange); }
        .hx-loc strong { color: var(--clr-navy); font-weight: 700; }

        /* Kosong */
        .hx-empty { display: flex; flex-direction: column; align-items: center; text-align: center; padding: 88px 24px; border: 1px dashed var(--line); background: rgba(255, 255, 255, .4); }
        .hx-empty-icon { font-size: 3.4rem; color: transparent; -webkit-text-stroke: 1px var(--clr-navy); margin-bottom: 26px; }
        .hx-empty-icon i { color: var(--clr-orange); font-size: 2.6rem; -webkit-text-stroke: 0; }
        .hx-empty h3 { font-family: var(--serif); font-weight: 500; font-size: 2.1rem; line-height: 1.1; color: var(--clr-navy); }
        .hx-empty p { margin: 14px auto 32px; max-width: 420px; font-size: .92rem; line-height: 1.8; color: var(--clr-muted); }

        /* ============================================================
           MODAL
           ============================================================ */
        .hx-modal { position: fixed; inset: 0; z-index: 1100; display: flex; align-items: center; justify-content: center; padding: 24px; font-family: var(--sans); }
        .hx-modal *, .hx-modal *::before, .hx-modal *::after { box-sizing: border-box; }
        .hx-modal h3, .hx-modal h4, .hx-modal p { margin: 0; }
        .hx-modal-bg { position: absolute; inset: 0; background: rgba(26, 45, 74, .62); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
        .hx-modal-panel {
            position: relative; width: 100%; max-width: 660px; max-height: 92vh; display: flex; flex-direction: column;
            background: #FBFAF5; border: 1px solid var(--line); box-shadow: 0 60px 100px -40px rgba(0, 0, 0, .55); color: var(--clr-navy);
            animation: hx-clip .85s var(--ease-io) both;
        }
        .hx-modal-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; padding: 28px 32px 24px; border-bottom: 1px solid var(--line); }
        .hx-modal-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        #modal-title { font-family: var(--serif); font-weight: 600; font-size: 1.9rem; line-height: 1.1; color: var(--clr-navy); }
        .hx-modal-meta { margin-top: 10px; font-size: .74rem; color: var(--clr-muted); }
        .hx-modal-meta .sep { margin: 0 8px; color: var(--clr-orange); }
        .hx-close {
            position: relative; width: 42px; height: 42px; flex-shrink: 0; overflow: hidden; isolation: isolate; cursor: pointer;
            border: 1px solid var(--line); background: transparent; color: var(--clr-navy); transition: color .5s var(--ease), border-color .5s var(--ease);
        }
        .hx-close::before { content: ''; position: absolute; inset: 0; z-index: -1; background: var(--clr-navy); transform: translateY(101%); transition: transform .6s var(--ease-io); }
        .hx-close:hover { color: var(--clr-bg); border-color: var(--clr-navy); }
        .hx-close:hover::before { transform: translateY(0); }
        .hx-modal-body { padding: 28px 32px; overflow-y: auto; display: grid; gap: 32px; }
        .hx-box { padding: 20px 22px; border: 1px solid var(--line); background: rgba(26, 45, 74, .03); }
        .hx-box h4 { margin-bottom: 10px; font-size: .62rem; font-weight: 700; letter-spacing: .28em; text-transform: uppercase; color: var(--clr-orange); }
        #modal-description { font-size: .92rem; line-height: 1.85; color: var(--clr-navy); white-space: pre-line; }
        .hx-resp-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 16px; margin-bottom: 20px; }
        .hx-resp-head h4 { font-family: var(--serif); font-weight: 600; font-size: 1.5rem; line-height: 1.1; }
        .hx-resp-head p { margin-top: 4px; font-size: .76rem; color: var(--clr-muted); }
        #modal-response-count { font-size: .62rem; font-weight: 700; letter-spacing: .22em; text-transform: uppercase; color: var(--clr-orange); }

        #modal-responses-container { position: relative; display: grid; gap: 14px; padding-left: 24px; }
        #modal-responses-container::before { content: ''; position: absolute; left: 4px; top: 8px; bottom: 8px; width: 1px; background: var(--line); }
        .response-entry { position: relative; padding: 18px 20px; border: 1px solid var(--line); background: #fff; animation: hx-in .7s var(--ease) both; }
        .response-entry::before { content: ''; position: absolute; left: -24px; top: 24px; width: 9px; height: 9px; background: var(--clr-orange); transform: rotate(45deg); box-shadow: 0 0 0 4px #FBFAF5; }
        .response-entry header { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 6px 16px; margin-bottom: 10px; }
        .response-entry .who { font-weight: 700; font-size: .82rem; color: var(--clr-navy); }
        .response-entry .who span { margin-left: 8px; font-family: var(--serif); font-style: italic; font-weight: 500; font-size: 1rem; color: var(--clr-orange); }
        .response-entry time { font-size: .7rem; color: var(--clr-muted); }
        .response-entry p { font-size: .9rem; line-height: 1.8; color: var(--clr-navy); white-space: pre-line; }
        .response-empty { padding: 36px 20px; text-align: center; border: 1px dashed var(--line); font-size: .86rem; line-height: 1.8; color: var(--clr-muted); background: rgba(26, 45, 74, .03); }
        .response-empty i { display: block; margin-bottom: 12px; font-size: 1.4rem; color: var(--clr-orange); }
        .hx-modal-foot { display: flex; justify-content: flex-end; padding: 18px 32px; border-top: 1px solid var(--line); }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 899px) {
            .hx-split { grid-template-columns: 1fr; }
            .hx-stats { margin-bottom: 48px; }
        }
        @media (max-width: 639px) {
            .hx-stats { grid-template-columns: 1fr; }
            .hx-stat + .hx-stat { border-left: 0; border-top: 1px solid var(--line); }
            .hx-stat { padding: 22px 4px; }
            .hx-stat-num { font-size: 3rem; }
            .hx-tabs { gap: 26px; overflow-x: auto; }
            .hx-tab { white-space: nowrap; }
            .hx-card-head, .hx-card-body { padding: 22px 20px; }
            .hx-row { flex-direction: column; padding: 20px 18px; }
            .hx-row-side { align-items: flex-start; flex-direction: row; flex-wrap: wrap; align-items: center; }
            .hx-subject { font-size: 1.5rem; }
            .hx-modal { padding: 12px; }
            .hx-modal-head, .hx-modal-body, .hx-modal-foot { padding-left: 20px; padding-right: 20px; }
            .hx-hide-sm { display: none; }
        }
    </style>
@endsection

@section('body')
    @include('user.partials.navbar')

    <div class="hx-page">
      <div class="hx-wrap">

        {{-- Page Header --}}
        <header class="hx-hero hx-head" data-reveal>
            <div class="hx-ghost" aria-hidden="true">Riwayat</div>
            <div style="position:relative;">
                <div class="hx-eyebrow hx-fade">Pusat bantuan laboratorium</div>
                <h1 class="hx-display hx-title">
                    <span class="hx-mask" style="--d:.05s"><span>Riwayat</span></span>
                    <span class="hx-mask" style="--d:.17s"><span><em>laporan</em></span></span>
                </h1>
                <p class="hx-lead hx-fade" style="--d:.4s">Pantau status laporan dan tanggapan tim laboratorium.</p>
            </div>
            <a href="{{ route('user.helpdesk.index') }}" class="hx-btn hx-fade" style="--d:.55s;position:relative;">
                <i class="fa-solid fa-plus"></i>
                <span class="hx-hide-sm">Buat Laporan Baru</span>
                <span class="hx-show-sm" style="display:none;">Lapor</span>
            </a>
        </header>

        @php
            $activeRepairs = $repairs->filter(fn ($repair) => $repair->items->contains(fn ($item) => (int) $item->pivot->status !== 2) || $repair->components->contains(fn ($component) => (int) $component->pivot->status !== 2))->count();
            $feedbackOpen = $feedbacks->whereIn('status', [1, 2])->count();
            $totalReports = $repairs->count() + $feedbacks->count();
            $activeReports = $activeRepairs + $feedbackOpen;
            $resolvedReports = max(0, $totalReports - $activeReports);
        @endphp

        {{-- Summary --}}
        <div class="hx-stats hx-fade" data-reveal>
            <div class="hx-stat"><p class="hx-stat-label">Total laporan</p><p class="hx-stat-num">{{ $totalReports }}</p></div>
            <div class="hx-stat"><p class="hx-stat-label">Sedang diproses</p><p class="hx-stat-num is-orange">{{ $activeReports }}</p></div>
            <div class="hx-stat"><p class="hx-stat-label">Selesai</p><p class="hx-stat-num is-ok">{{ $resolvedReports }}</p></div>
        </div>

        {{-- Tab Selector --}}
        <div class="hx-tabs hx-fade" data-reveal>
            <button type="button" onclick="switchHistTab('repair')" id="tab-btn-repair-hist" class="hx-tab is-active">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>Laporan Perbaikan</span>
                <span class="count">{{ $repairs->count() }}</span>
            </button>
            <button type="button" onclick="switchHistTab('feedback')" id="tab-btn-feedback-hist" class="hx-tab">
                <i class="fa-solid fa-comments"></i>
                <span>Keluhan &amp; Saran</span>
                <span class="count">{{ $feedbacks->count() }}</span>
            </button>
        </div>

        {{-- Filter Bar --}}
        <div class="hx-filter hx-fade" data-reveal>
            <div class="hx-search">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari tiket, nama barang, atau uraian..."
                    class="form-input" onkeyup="applyFilters()">
            </div>
            <button type="button" onclick="resetFilters()" class="hx-link danger">Reset Filter</button>
        </div>

        {{-- ========================================================= --}}
        {{-- PANE 1: RIWAYAT PERBAIKAN BARANG (REPAIR)               --}}
        {{-- ========================================================= --}}
        <div id="tab-pane-repair-hist">
            @if ($repairs->isEmpty())
                <div class="hx-empty hx-fade" data-reveal>
                    <div class="hx-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                    <h3>Belum ada riwayat perbaikan</h3>
                    <p>Anda belum pernah mengajukan laporan perbaikan perangkat lab.</p>
                    <a href="{{ route('user.helpdesk.index') }}" class="hx-btn">
                        Laporkan Kerusakan Sekarang <i class="fa-solid fa-arrow-right-long arrow"></i>
                    </a>
                </div>
            @else
                <div class="hx-list" id="repairList">
                    @foreach ($repairs as $repair)
                        @php
                            $allItems = $repair->items;
                            $allComps = $repair->components;
                            $totalCount = $allItems->count() + $allComps->count();
                            $searchTerms = strtolower($repair->name . ' ' . $allItems->pluck('name')->join(' ') . ' ' . $allComps->pluck('name')->join(' '));
                        @endphp
                        <div class="hx-card repair-card hx-fade" data-reveal style="--d: {{ min($loop->index, 5) * 0.08 }}s"
                            data-search="{{ $searchTerms }}">
                            {{-- Card Header --}}
                            <div class="hx-card-head">
                                <div class="hx-card-id">
                                    <div class="hx-card-icon">
                                        <i class="fa-solid fa-screwdriver-wrench"></i>
                                    </div>
                                    <div>
                                        <div class="hx-card-title">
                                            <span class="hx-card-name">{{ $repair->name }}</span>
                                            <span class="hx-tag hx-tag--orange">{{ $totalCount }} Perangkat</span>
                                        </div>
                                        <p class="hx-meta">
                                            <i class="fa-regular fa-clock"></i>{{ $repair->created_at->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="hx-period">
                                    Periode <strong>{{ $repair->period->academic_year ?? '-' }}</strong>
                                </div>
                            </div>

                            {{-- Card Body: Item List --}}
                            <div class="hx-card-body">
                                <div class="hx-split">
                                    <div class="hx-rows">
                                @foreach ($allItems as $item)
                                    @php
                                        $pivot = $item->pivot;
                                        $status = (int) $pivot->status;
                                    @endphp
                                    <div class="hx-row">
                                        <div class="hx-row-main">
                                            <div class="hx-row-title">
                                                <span class="hx-row-name">{{ $item->name }}</span>
                                                <span class="hx-tag">{{ $item->type->name ?? 'Item' }}</span>
                                                <span class="hx-tag hx-tag--mute">
                                                    {{ $item->desk ? 'Meja ' . $item->desk->location : ($item->lab->name ?? 'Storage Lab') }}
                                                </span>
                                            </div>
                                            <p class="hx-sn hx-mono">SN: {{ $item->serial_code }}</p>
                                            <div class="hx-issue">
                                                <b>Kendala</b>{{ $pivot->issue_description }}
                                            </div>
                                            @if ($pivot->repair_notes)
                                                <div class="hx-note">
                                                    <b><i class="fa-solid fa-wrench" style="margin-right:6px;color:var(--clr-orange);"></i>Catatan Teknisi</b>{{ $pivot->repair_notes }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="hx-row-side">
                                            @if ($status === 2)
                                                @if ($pivot->is_successful)
                                                    <span class="hx-tag hx-tag--ok">
                                                        <i class="fa-solid fa-circle-check"></i>Berhasil diperbaiki
                                                    </span>
                                                @else
                                                    <span class="hx-tag hx-tag--bad">
                                                        <i class="fa-solid fa-circle-xmark"></i>Tidak berhasil diperbaiki
                                                    </span>
                                                @endif
                                            @elseif ($status === 1)
                                                <span class="hx-tag hx-tag--orange">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>Sedang diproses
                                                </span>
                                            @else
                                                <span class="hx-tag hx-tag--mute">
                                                    <i class="fa-regular fa-clock"></i>Menunggu diproses
                                                </span>
                                            @endif

                                            @if ($pivot->start_date)
                                                <span class="hx-date">
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
                                    <div class="hx-row">
                                        <div class="hx-row-main">
                                            <div class="hx-row-title">
                                                <span class="hx-row-name">{{ $comp->name }}</span>
                                                <span class="hx-tag hx-tag--orange">Komponen: {{ $comp->type->name ?? 'Komponen' }}</span>
                                            </div>
                                            <div class="hx-issue">
                                                <b>Kendala</b>{{ $pivot->issue_description }}
                                            </div>
                                        </div>

                                        <div class="hx-row-side">
                                            @if ($status === 2)
                                                @if ($pivot->is_successful)
                                                    <span class="hx-tag hx-tag--ok">
                                                        <i class="fa-solid fa-circle-check"></i>Berhasil diperbaiki
                                                    </span>
                                                @else
                                                    <span class="hx-tag hx-tag--bad">
                                                        <i class="fa-solid fa-circle-xmark"></i>Tidak berhasil diperbaiki
                                                    </span>
                                                @endif
                                            @elseif ($status === 1)
                                                <span class="hx-tag hx-tag--orange">
                                                    <i class="fa-solid fa-spinner fa-spin"></i>Sedang diproses
                                                </span>
                                            @else
                                                <span class="hx-tag hx-tag--mute">
                                                    <i class="fa-regular fa-clock"></i>Menunggu diproses
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                    </div>

                                    <aside class="hx-summary">
                                        <h4>Ringkasan Laporan</h4>
                                        <dl>
                                            <div>
                                                <dt>Perangkat dilaporkan</dt>
                                                <dd>{{ $totalCount }} perangkat</dd>
                                            </div>
                                            <div>
                                                <dt>Periode</dt>
                                                <dd>{{ $repair->period->academic_year ?? '-' }}</dd>
                                            </div>
                                        </dl>
                                    </aside>
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
        <div id="tab-pane-feedback-hist" class="hidden">
            @if ($feedbacks->isEmpty())
                <div class="hx-empty hx-fade" data-reveal>
                    <div class="hx-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                    <h3>Belum ada tiket keluhan atau saran</h3>
                    <p>Punya masukan atau kendala fasilitas? Jangan ragu untuk menyampaikannya ke pengelola lab.</p>
                    <a href="{{ route('user.helpdesk.index') }}" class="hx-btn">
                        Kirim Masukan Sekarang <i class="fa-solid fa-arrow-right-long arrow"></i>
                    </a>
                </div>
            @else
                <div class="hx-list" id="feedbackList">
                    @foreach ($feedbacks as $fb)
                        @php
                            $statusClasses = [
                                1 => '',
                                2 => 'hx-tag--orange',
                                3 => 'hx-tag--ok',
                                4 => 'hx-tag--mute',
                                5 => 'hx-tag--bad',
                            ];
                            $searchTerms = strtolower($fb->ticket_number . ' ' . $fb->subject . ' ' . $fb->description);
                        @endphp
                        <div class="hx-card feedback-card hx-fade" data-reveal style="--d: {{ min($loop->index, 5) * 0.08 }}s"
                            data-search="{{ $searchTerms }}">
                            {{-- Card Header --}}
                            <div class="hx-card-head">
                                <div class="hx-card-id">
                                    <div class="hx-card-icon {{ $fb->type === 'KELUHAN' ? 'is-bad' : 'is-ok' }}">
                                        <i class="fa-solid {{ $fb->type === 'KELUHAN' ? 'fa-triangle-exclamation' : 'fa-lightbulb' }}"></i>
                                    </div>
                                    <div>
                                        <div class="hx-card-title">
                                            <span class="hx-tag hx-tag--solid hx-mono">{{ $fb->ticket_number }}</span>
                                            <span class="hx-tag {{ $fb->type === 'KELUHAN' ? 'hx-tag--bad' : 'hx-tag--ok' }}">{{ $fb->type }}</span>
                                        </div>
                                        <p class="hx-meta">
                                            <i class="fa-regular fa-clock"></i>{{ $fb->created_at->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <span class="hx-tag {{ $statusClasses[$fb->status] ?? 'hx-tag--mute' }}">
                                    {{ $fb->status_label }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="hx-card-body">
                                <h4 class="hx-subject">{{ $fb->subject }}</h4>
                                <p class="hx-desc-text">{{ $fb->description }}</p>

                                <div class="hx-card-foot">
                                    <div class="hx-loc">
                                        <i class="fa-solid fa-location-dot"></i>
                                        Lokasi: <strong>{{ $fb->lab ? $fb->lab->name : 'Fasilitas Umum' }}</strong>
                                    </div>

                                    <button type="button" onclick="openFeedbackDetail('{{ $fb->id }}')" class="hx-btn sm">
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
    <div id="feedbackDetailModal" class="hx-modal hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="hx-modal-bg" onclick="closeFeedbackDetail()"></div>

        <div class="hx-modal-panel">
            {{-- Modal Header --}}
            <div class="hx-modal-head">
                <div style="min-width:0;">
                    <div class="hx-modal-tags">
                        <span id="modal-ticket-badge" class="hx-tag hx-tag--solid hx-mono">FB-000000</span>
                        <span id="modal-type-badge" class="hx-tag">KELUHAN</span>
                        <span id="modal-status-badge" class="hx-tag">Open</span>
                    </div>
                    <h3 id="modal-title">Subjek Pengaduan</h3>
                    <p class="hx-modal-meta">
                        <span id="modal-reporter-info">Laporan Anda</span>
                        <span class="sep">◆</span>
                        <span id="modal-created-at">Tanggal</span>
                    </p>
                </div>
                <button type="button" onclick="closeFeedbackDetail()" aria-label="Tutup detail laporan" class="hx-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="hx-modal-body">
                <section class="hx-box">
                    <h4>Isi laporan</h4>
                    <p id="modal-description"></p>
                </section>

                <section>
                    <div class="hx-resp-head">
                        <div>
                            <h4>Riwayat tanggapan</h4>
                            <p>Pembaruan dari tim laboratorium.</p>
                        </div>
                        <span id="modal-response-count">0 tanggapan</span>
                    </div>
                    <div id="modal-responses-container">
                        {{-- Diisi secara dinamis oleh JS --}}
                    </div>
                </section>
            </div>

            {{-- Modal Footer --}}
            <div class="hx-modal-foot">
                <button type="button" onclick="closeFeedbackDetail()" class="hx-btn sm">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    let currentTab = 'repair';
    let feedbackModalPreviousOverflow = '';

    // Tombol "Lapor" versi mobile
    (function () {
        if (window.matchMedia('(max-width: 639px)').matches) {
            const s = document.querySelector('.hx-show-sm');
            if (s) s.style.display = 'inline';
        }
    })();

    function switchHistTab(tab) {
        currentTab = tab;
        const btnRepair = document.getElementById('tab-btn-repair-hist');
        const btnFeedback = document.getElementById('tab-btn-feedback-hist');
        const paneRepair = document.getElementById('tab-pane-repair-hist');
        const paneFeedback = document.getElementById('tab-pane-feedback-hist');

        if (tab === 'repair') {
            btnRepair.classList.add('is-active');
            btnFeedback.classList.remove('is-active');
            paneRepair.classList.remove('hidden');
            paneFeedback.classList.add('hidden');
        } else {
            btnFeedback.classList.add('is-active');
            btnRepair.classList.remove('is-active');
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
            typeBadge.className = `hx-tag ${fb.type === 'KELUHAN' ? 'hx-tag--bad' : 'hx-tag--ok'}`;

            const statusMap = {
                1: { label: 'Menunggu diproses', className: '' },
                2: { label: 'Sedang ditangani', className: 'hx-tag--orange' },
                3: { label: 'Selesai', className: 'hx-tag--ok' },
                4: { label: 'Ditutup', className: 'hx-tag--mute' },
                5: { label: 'Ditolak', className: 'hx-tag--bad' },
            };
            const currentStatus = statusMap[fb.status] || { label: 'Status tidak diketahui', className: 'hx-tag--mute' };
            const statusBadge = document.getElementById('modal-status-badge');
            statusBadge.textContent = currentStatus.label;
            statusBadge.className = `hx-tag ${currentStatus.className}`;

            const respContainer = document.getElementById('modal-responses-container');
            const responseCount = fb.responses?.length || 0;
            document.getElementById('modal-response-count').textContent = `${responseCount} tanggapan`;
            if (!fb.responses || fb.responses.length === 0) {
                respContainer.innerHTML = `
                    <div class="response-empty">
                        <i class="fa-regular fa-clock"></i>
                        Belum ada balasan dari tim pengelola laboratorium. Laporan Anda sedang dalam antrean tindak lanjut.
                    </div>
                `;
            } else {
                respContainer.innerHTML = fb.responses.map((r, index) => `
                    <article class="response-entry" style="animation-delay:${index * 0.08}s">
                        <header>
                            <span class="who">
                                ${escapeHtml(r.responder ? r.responder.name : 'Pengelola Lab')}
                                <span>Tanggapan ${index + 1}</span>
                            </span>
                            <time>${new Date(r.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</time>
                        </header>
                        <p>${escapeHtml(r.message || r.response || '-')}</p>
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