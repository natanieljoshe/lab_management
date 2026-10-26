@extends('layouts.user')

@section('title', 'Riwayat Peminjaman Saya')

@section('style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Open+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'petra-blue': '#1A2D4A',
                        'petra-yellow': '#F57E21',
                        'petra-darkgray': '#424242',
                    }
                }
            }
        }
    </script>
    <style>
        /* ============================================================
           TOKENS — Editorial Luxury (#FBFAF5, #1A2D4A, #F57E21)
           ============================================================ */
        :root {
            --clr-bg: #FBFAF5;
            --clr-navy: #1A2D4A;
            --clr-orange: #F57E21;
            --panel: #FEFDFA;
            --clr-muted: rgba(26, 45, 74, 0.65);
            --line: rgba(26, 45, 74, 0.16);
            --line-light: rgba(251, 250, 245, 0.18);
            --ok: #2F7A55;
            --danger: #B3362A;
            --serif: 'Cormorant Garamond', 'Times New Roman', serif;
            --sans: 'Open Sans', sans-serif;
            --ease: cubic-bezier(0.22, 1, 0.36, 1);
            --ease-io: cubic-bezier(0.76, 0, 0.24, 1);
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--clr-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--clr-navy);
            border: 2px solid var(--clr-bg);
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--clr-orange);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--clr-navy) var(--clr-bg);
        }

        /* ============================================================
           BASE PAGE & WRAPPER
           ============================================================ */
        .history-page {
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
            background: var(--clr-bg);
            color: var(--clr-navy);
            font-family: var(--sans);
            padding: 150px 0 100px;
        }

        .history-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 720px;
            pointer-events: none;
            background: radial-gradient(540px circle at 75% 20%, rgba(245, 126, 33, 0.1), transparent 60%);
        }

        .history-wrap {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 28px;
        }

        .history-grain {
            position: fixed;
            inset: 0;
            z-index: 60;
            pointer-events: none;
            opacity: .055;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 .1 0 0 0 0 .18 0 0 0 0 .29 0 0 0 .9 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
        }

        .history-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 100%;
            z-index: 1001;
            background: linear-gradient(90deg, var(--clr-orange), #ffb066);
            transform-origin: left center;
            transform: scaleX(0);
        }

        /* ============================================================
           TYPOGRAPHY & HERO
           ============================================================ */
        .history-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            color: var(--clr-orange);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .32em;
            text-transform: uppercase;
        }

        .history-eyebrow::before {
            content: '';
            width: 46px;
            height: 1px;
            background: currentColor;
            display: block;
        }

        .history-hero {
            position: relative;
            padding-bottom: 56px;
        }

        .history-ghost {
            position: absolute;
            right: -10px;
            top: -30px;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(6rem, 16vw, 13rem);
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1px rgba(26, 45, 74, .09);
        }

        .history-title {
            margin: 22px 0 14px;
            color: var(--clr-navy);
            font-family: var(--serif);
            font-size: clamp(2.8rem, 6vw, 5.2rem);
            font-weight: 500;
            line-height: .98;
            letter-spacing: -.015em;
        }

        .history-title em {
            font-style: italic;
            font-weight: 500;
            color: var(--clr-orange);
        }

        .history-subtitle {
            color: var(--clr-muted);
            font-size: 1rem;
            line-height: 1.85;
            max-width: 580px;
        }

        /* ============================================================
           BUTTONS
           ============================================================ */
        .history-new-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            overflow: hidden;
            isolation: isolate;
            padding: 18px 32px;
            border: 1px solid var(--clr-navy);
            background: var(--clr-navy);
            color: var(--clr-bg);
            font-family: var(--sans);
            font-weight: 700;
            font-size: .72rem;
            letter-spacing: .22em;
            text-transform: uppercase;
            cursor: pointer;
            transition: color .5s var(--ease), border-color .5s var(--ease);
        }

        .history-new-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: var(--clr-orange);
            transform: translateX(-101%);
            transition: transform .65s var(--ease-io);
        }

        .history-new-btn:hover {
            color: var(--clr-bg);
            border-color: var(--clr-orange);
        }

        .history-new-btn:hover::before {
            transform: translateX(0);
        }

        .history-new-btn i {
            color: var(--clr-orange);
            transition: transform .5s var(--ease), color .4s var(--ease);
        }

        .history-new-btn:hover i {
            color: var(--clr-bg);
            transform: translateX(5px);
        }

        /* ============================================================
           FILTERS PANEL
           ============================================================ */
        .history-filters {
            position: relative;
            background: var(--panel);
            border: 1px solid var(--line);
            box-shadow: 0 40px 80px -60px rgba(26, 45, 74, .4);
            padding: 24px 28px;
            margin-bottom: 48px;
        }

        .history-filter-search {
            position: relative;
            min-width: 260px;
        }

        .history-filter-search i {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: var(--clr-orange);
            font-size: .85rem;
        }

        .history-input {
            width: 100%;
            background: transparent;
            border: 0;
            border-bottom: 1px solid rgba(26, 45, 74, .3);
            border-radius: 0;
            padding: 12px 0 12px 28px;
            font-family: var(--sans);
            font-size: .92rem;
            color: var(--clr-navy);
            transition: border-color .4s var(--ease), box-shadow .4s var(--ease);
        }

        .history-input:focus {
            outline: none;
            border-bottom-color: var(--clr-orange);
            box-shadow: 0 1px 0 0 var(--clr-orange);
        }

        .history-input::placeholder {
            color: rgba(26, 45, 74, .4);
        }

        .history-date-filter {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid var(--line);
            background: rgba(26, 45, 74, .025);
            padding: 8px 14px;
        }

        .history-date-filter input[type="date"] {
            border: 0;
            background: transparent;
            font-family: var(--sans);
            font-size: .84rem;
            color: var(--clr-navy);
            padding: 4px 0;
        }

        .history-date-filter input[type="date"]:focus {
            outline: none;
        }

        .history-date-filter span.sep {
            color: var(--clr-orange);
            font-weight: 700;
        }

        .history-select-wrap {
            position: relative;
        }

        .history-select-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--clr-orange);
            font-size: .82rem;
            pointer-events: none;
        }

        .history-select {
            appearance: none;
            -webkit-appearance: none;
            border: 1px solid var(--line);
            background: rgba(26, 45, 74, .025);
            color: var(--clr-navy);
            font-family: var(--sans);
            font-size: .84rem;
            font-weight: 600;
            padding: 12px 36px 12px 36px;
            cursor: pointer;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'><path d='M1 1l4 4 4-4' fill='none' stroke='%231A2D4A' stroke-width='1.5'/></svg>");
            background-repeat: no-repeat;
            background-position: right 14px center;
            transition: border-color .4s var(--ease);
        }

        .history-select:focus {
            outline: none;
            border-color: var(--clr-orange);
        }

        .history-select option {
            background: #FBFAF5;
            color: #1A2D4A;
        }

        .reset-filter {
            position: relative;
            background: none;
            border: 0;
            cursor: pointer;
            font-family: var(--sans);
            font-weight: 700;
            font-size: .7rem;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--clr-navy);
            padding: 10px 0;
            transition: color .4s var(--ease);
        }

        .reset-filter::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 4px;
            height: 1px;
            width: 100%;
            background: var(--clr-orange);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform .6s var(--ease-io);
        }

        .reset-filter:hover {
            color: var(--clr-orange);
        }

        .reset-filter:hover::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        /* ============================================================
           BOOKING CARDS
           ============================================================ */
        .history-grid {
            display: grid;
            gap: 32px;
        }

        .booking-card {
            background: var(--panel);
            border: 1px solid var(--line);
            box-shadow: 0 40px 80px -64px rgba(26, 45, 74, .5);
            transition: border-color .4s var(--ease), box-shadow .4s var(--ease);
        }

        .booking-card:hover {
            border-color: rgba(245, 126, 33, .65);
            box-shadow: 0 40px 90px -50px rgba(26, 45, 74, .55);
        }

        .booking-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            padding: 22px 32px;
            border-bottom: 1px solid var(--line);
            background: rgba(26, 45, 74, .02);
        }

        .booking-card-submit {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .78rem;
            color: var(--clr-muted);
        }

        .booking-card-submit i {
            color: var(--clr-orange);
            font-size: 1rem;
        }

        .booking-card-submit strong {
            color: var(--clr-navy);
            font-weight: 600;
        }

        /* Status Tags */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border: 1px solid var(--line);
            font-family: var(--sans);
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .2em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .status-badge i {
            font-size: .74rem;
        }

        .status-pending {
            color: var(--clr-orange);
            border-color: rgba(245, 126, 33, .5);
            background: rgba(245, 126, 33, .08);
        }

        .status-approved {
            color: var(--ok);
            border-color: rgba(47, 122, 85, .45);
            background: rgba(47, 122, 85, .08);
        }

        .status-rejected {
            color: var(--danger);
            border-color: rgba(179, 54, 42, .45);
            background: rgba(179, 54, 42, .08);
        }

        .booking-card-body {
            padding: 32px;
        }

        .booking-split {
            display: grid;
            grid-template-columns: 1fr 310px;
            gap: 36px;
            align-items: start;
        }

        .booking-main h2 {
            font-family: var(--serif);
            font-weight: 600;
            font-size: 1.85rem;
            line-height: 1.15;
            color: var(--clr-navy);
            margin: 0 0 14px;
        }

        .booking-meta-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .booking-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            font-size: .84rem;
            color: var(--clr-muted);
            line-height: 1.6;
        }

        .booking-meta-item i {
            color: var(--clr-orange);
            width: 18px;
            margin-top: 3px;
            text-align: center;
            flex-shrink: 0;
        }

        .booking-meta-item strong {
            color: var(--clr-navy);
            font-weight: 600;
        }

        .booking-note-box {
            margin-top: 22px;
            padding: 14px 18px;
            border-left: 2px solid var(--clr-orange);
            background: rgba(245, 126, 33, .06);
            font-size: .84rem;
            line-height: 1.7;
            color: var(--clr-navy);
        }

        .booking-note-box b {
            display: block;
            margin-bottom: 4px;
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .24em;
            text-transform: uppercase;
            color: var(--clr-orange);
        }

        /* Aside Items Summary */
        .booking-items-box {
            position: relative;
            background: rgba(26, 45, 74, .03);
            border: 1px solid var(--line);
            padding: 24px 22px;
        }

        .booking-items-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .booking-items-head h4 {
            margin: 0;
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .24em;
            text-transform: uppercase;
            color: var(--clr-navy);
        }

        .booking-items-count {
            font-family: var(--serif);
            font-style: italic;
            font-size: 1.15rem;
            color: var(--clr-orange);
            font-weight: 600;
        }

        .booking-items-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            gap: 10px;
        }

        .booking-item-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: .82rem;
            color: var(--clr-navy);
            line-height: 1.5;
        }

        .booking-item-row i {
            color: var(--clr-orange);
            font-size: .75rem;
            margin-top: 4px;
            flex-shrink: 0;
        }

        .lab-tag {
            display: inline-block;
            padding: 2px 7px;
            font-size: .6rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            background: var(--clr-navy);
            color: var(--clr-bg);
            margin-left: 6px;
        }

        .toggle-items {
            width: 100%;
            margin-top: 14px;
            padding: 10px 0;
            background: transparent;
            border: 1px dashed var(--line);
            cursor: pointer;
            font-family: var(--sans);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--clr-navy);
            transition: border-color .4s var(--ease), color .4s var(--ease), background .4s var(--ease);
        }

        .toggle-items:hover {
            border-color: var(--clr-orange);
            color: var(--clr-orange);
            background: rgba(245, 126, 33, .05);
        }

        /* ============================================================
           EMPTY & NO RESULT STATES
           ============================================================ */
        .history-empty-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 88px 24px;
            border: 1px dashed var(--line);
            background: rgba(255, 255, 255, .4);
        }

        .empty-icon {
            font-size: 3.4rem;
            color: transparent;
            -webkit-text-stroke: 1px var(--clr-navy);
            margin-bottom: 24px;
        }

        .empty-icon i {
            color: var(--clr-orange);
            font-size: 2.8rem;
            -webkit-text-stroke: 0;
        }

        .empty-title {
            font-family: var(--serif);
            font-weight: 500;
            font-size: 2.1rem;
            line-height: 1.1;
            color: var(--clr-navy);
            margin: 0 0 10px;
        }

        .empty-copy {
            max-width: 440px;
            font-size: .92rem;
            line-height: 1.8;
            color: var(--clr-muted);
            margin: 0 auto 28px;
        }

        /* Load more */
        .history-more {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 14px 28px;
            border: 1px solid var(--clr-navy);
            background: transparent;
            color: var(--clr-navy);
            font-family: var(--sans);
            font-weight: 700;
            font-size: .72rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            cursor: pointer;
            transition: color .4s var(--ease), border-color .4s var(--ease), background .4s var(--ease);
        }

        .history-more:hover {
            border-color: var(--clr-orange);
            color: var(--clr-bg);
            background: var(--clr-navy);
        }

        .history-more i {
            color: var(--clr-orange);
            transition: transform .4s var(--ease);
        }

        .history-more:hover i {
            transform: translateY(3px);
        }

        .hidden-card {
            display: none !important;
        }

        /* Responsive */
        @media (max-width: 899px) {
            .booking-split {
                grid-template-columns: 1fr;
            }

            .history-ghost {
                display: none;
            }

            .history-hero {
                padding-bottom: 40px;
            }
        }

        @media (max-width: 639px) {
            .history-page {
                padding: 118px 0 80px;
            }

            .history-wrap {
                padding: 0 20px;
            }

            .booking-card-head,
            .booking-card-body {
                padding: 20px;
            }

            .history-filters {
                padding: 20px 16px;
            }

            .booking-main h2 {
                font-size: 1.55rem;
            }
        }
    </style>
@endsection

@section('body')

    @include('user.partials.navbar')

    <main class="history-page">
        <div class="history-wrap">

            {{-- Hero Header --}}
            <header class="history-hero">
                <div class="history-ghost" aria-hidden="true">Riwayat</div>
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                    <div>
                        <p class="history-eyebrow">Reservasi fasilitas laboratorium</p>
                        <h1 class="history-title">Riwayat <em>Peminjaman</em></h1>
                        <p class="history-subtitle">Pantau status pengajuan peminjaman aset dan ruangan laboratorium Anda
                            secara berkala.</p>
                    </div>
                    <a href="{{ route('user.booking.form') }}" class="history-new-btn group">
                        <i class="fa-solid fa-plus"></i>
                        <span class="hidden sm:inline">Buat Peminjaman</span>
                        <span class="sm:hidden">Baru</span>
                    </a>
                </div>
            </header>

            @if(!$bookings->isEmpty())
                {{-- Filters Panel --}}
                <div class="history-filters">
                    <div class="flex flex-col xl:flex-row gap-5 items-stretch xl:items-center justify-between">

                        {{-- Search --}}
                        <div class="history-filter-search flex-1">
                            <i class="fa-solid fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Cari judul kegiatan atau mata kuliah..."
                                class="history-input" onkeyup="applyFilters()">
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center flex-wrap">

                            {{-- Date Range --}}
                            <div class="history-date-filter">
                                <input type="date" id="startDateFilter" placeholder="Dari" title="Dari Tanggal"
                                    onchange="applyFilters()">
                                <span class="sep">—</span>
                                <input type="date" id="endDateFilter" placeholder="Sampai" title="Sampai Tanggal"
                                    onchange="applyFilters()">
                            </div>

                            {{-- Sort Dropdown --}}
                            <div class="history-select-wrap">
                                <i class="fa-solid fa-sort"></i>
                                <select id="sortFilter" class="history-select" onchange="applyFilters()">
                                    <option value="newest_submit">Diajukan: Terbaru</option>
                                    <option value="oldest_submit">Diajukan: Terlama</option>
                                    <option value="soonest_event">Acara: Terdekat</option>
                                    <option value="latest_event">Acara: Terjauh</option>
                                </select>
                            </div>

                            <button type="button" onclick="resetFilters()" class="reset-filter">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if($bookings->isEmpty())
                {{-- Empty State --}}
                <div class="history-empty-box">
                    <div class="empty-icon">
                        <i class="fa-regular fa-folder-open"></i>
                    </div>
                    <h3 class="empty-title">Belum ada riwayat peminjaman</h3>
                    <p class="empty-copy">Anda belum pernah mengajukan peminjaman fasilitas atau inventaris laboratorium Gedung
                        P.</p>
                    <a href="{{ route('user.booking.form') }}" class="history-new-btn">
                        <i class="fa-solid fa-plus"></i>
                        <span>Mulai Buat Peminjaman</span>
                    </a>
                </div>
            @else
                {{-- Cards List --}}
                <div class="history-grid" id="bookingList">
                    @foreach($bookings as $booking)
                        <article class="booking-card" data-title="{{ strtolower($booking->event_name) }}"
                            data-submit-date="{{ $booking->created_at->timestamp }}"
                            data-event-date="{{ \Carbon\Carbon::parse($booking->event_started_at)->timestamp }}"
                            data-event-date-str="{{ \Carbon\Carbon::parse($booking->event_started_at)->format('Y-m-d') }}">

                            {{-- Card Header --}}
                            <div class="booking-card-head">
                                <div class="booking-card-submit">
                                    <i class="fa-regular fa-calendar-check"></i>
                                    <span>Diajukan pada
                                        <strong>{{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }}</strong></span>
                                </div>

                                @php
                                    $statusClass = 'status-pending';
                                    $statusIcon = 'fa-clock';
                                    $statusText = 'Menunggu Persetujuan';

                                    if ($booking->approved === 1) {
                                        $statusClass = 'status-approved';
                                        $statusIcon = 'fa-circle-check';
                                        $statusText = 'Disetujui';
                                    } elseif ($booking->approved === 0) {
                                        $statusClass = 'status-rejected';
                                        $statusIcon = 'fa-circle-xmark';
                                        $statusText = 'Ditolak';
                                    }
                                @endphp

                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="booking-card-body">
                                <div class="booking-split">
                                    <div class="booking-main">
                                        <h2>{{ $booking->event_name }}</h2>

                                        <div class="booking-meta-list">
                                            <div class="booking-meta-item">
                                                <i class="fa-solid fa-clock"></i>
                                                <span>
                                                    <strong>{{ \Carbon\Carbon::parse($booking->event_started_at)->translatedFormat('d M Y, H:i') }}</strong>
                                                    <span style="margin: 0 6px; color: var(--clr-orange);">—</span>
                                                    <strong>{{ \Carbon\Carbon::parse($booking->event_ended_at)->translatedFormat('d M Y, H:i') }}</strong>
                                                </span>
                                            </div>

                                            @if($booking->thesis_title)
                                                <div class="booking-meta-item">
                                                    <i class="fa-solid fa-graduation-cap"></i>
                                                    <span>Judul Skripsi: <em
                                                            style="font-family: var(--serif); font-size: 1.05rem; color: var(--clr-navy);">"{{ $booking->thesis_title }}"</em></span>
                                                </div>
                                            @endif

                                            @if($booking->supervisor)
                                                <div class="booking-meta-item">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                    <span>Dosen Pembimbing: <strong>{{ $booking->supervisor->name }}</strong></span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($booking->booking_detail)
                                            <div class="booking-note-box">
                                                <b>Catatan Tambahan</b>
                                                {{ $booking->booking_detail }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Items Column --}}
                                    <aside class="booking-items-box">
                                        <div class="booking-items-head">
                                            <h4>Item Dipinjam</h4>
                                            <span class="booking-items-count">{{ $booking->bookings_items->count() }}</span>
                                        </div>
                                        <ul class="booking-items-list">
                                            @foreach($booking->bookings_items as $index => $item)
                                                <li class="booking-item-row {{ $index >= 3 ? 'hidden extra-item-' . $booking->id : '' }}">
                                                    <i class="fa-solid fa-box"></i>
                                                    <span>
                                                        {{ $item->bookable->name ?? 'Unknown Item' }}
                                                        @if(str_contains($item->bookable_type, 'Labs'))
                                                            <span class="lab-tag">LAB</span>
                                                        @endif
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>

                                        @if($booking->bookings_items->count() > 3)
                                            <button type="button" onclick="toggleItems('{{ $booking->id }}', this)"
                                                class="toggle-items">
                                                <span>+ {{ $booking->bookings_items->count() - 3 }} item lainnya</span>
                                                <i class="fa-solid fa-chevron-down" style="margin-left: 6px;"></i>
                                            </button>
                                        @endif
                                    </aside>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Load More Wrapper --}}
                <div id="loadMoreWrapper" class="text-center mt-12 hidden">
                    <button type="button" onclick="loadMore()" class="history-more">
                        <span id="loadMoreText">Muat Lebih Banyak</span>
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <p style="font-size: .74rem; color: var(--clr-muted); margin-top: 10px;">
                        Menampilkan <span id="showingCount" style="font-weight: 700; color: var(--clr-navy);">0</span> dari
                        <span id="totalCount" style="font-weight: 700; color: var(--clr-navy);">0</span> riwayat
                    </p>
                </div>

                {{-- No Result State --}}
                <div id="noResultState" class="history-empty-box hidden" style="margin-top: 36px;">
                    <div class="empty-icon">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h3 class="empty-title">Data tidak ditemukan</h3>
                    <p class="empty-copy">Tidak ada riwayat peminjaman yang cocok dengan filter tanggal atau kata kunci yang
                        dimasukkan.</p>
                    <button type="button" onclick="resetFilters()" class="reset-filter">Reset Filter</button>
                </div>
            @endif

        </div>
    </main>

    @include('user.partials.footer')

@endsection

@section('script')
    <script>
        const ITEMS_PER_PAGE = 5;
        let currentVisibleCount = ITEMS_PER_PAGE;

        // Grain & Scroll progress
        (function () {
            var grain = document.createElement('div');
            grain.className = 'history-grain';
            grain.setAttribute('aria-hidden', 'true');
            document.body.appendChild(grain);

            var bar = document.createElement('div');
            bar.className = 'history-progress';
            document.body.appendChild(bar);

            function onScroll() {
                var h = document.documentElement.scrollHeight - window.innerHeight;
                bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(window.scrollY / h, 1) : 0) + ')';
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', onScroll);
            onScroll();
        })();

        function toggleItems(bookingId, btn) {
            const hiddenItems = document.querySelectorAll(`.extra-item-${bookingId}`);
            hiddenItems.forEach(item => item.classList.remove('hidden'));
            btn.style.display = 'none';
        }

        function loadMore() {
            currentVisibleCount += ITEMS_PER_PAGE;
            applyFilters(true);
        }

        function applyFilters(isLoadingMore = false) {
            const searchInputEl = document.getElementById('searchInput');
            const startDateEl = document.getElementById('startDateFilter');
            const endDateEl = document.getElementById('endDateFilter');
            const sortFilterEl = document.getElementById('sortFilter');

            if (!searchInputEl) return;

            const searchVal = searchInputEl.value.toLowerCase();
            const startDateVal = startDateEl ? startDateEl.value : '';
            const endDateVal = endDateEl ? endDateEl.value : '';
            const sortVal = sortFilterEl ? sortFilterEl.value : 'newest_submit';

            const isFiltering = (searchVal !== '' || startDateVal !== '' || endDateVal !== '');

            if (isFiltering && !isLoadingMore) {
                currentVisibleCount = 999999;
            } else if (!isFiltering && !isLoadingMore) {
                currentVisibleCount = ITEMS_PER_PAGE;
            }

            const container = document.getElementById('bookingList');
            if (!container) return;

            const cards = Array.from(container.getElementsByClassName('booking-card'));
            const noResult = document.getElementById('noResultState');
            const loadMoreBtn = document.getElementById('loadMoreWrapper');

            const sortedCards = cards.sort((a, b) => {
                const submitDateA = parseInt(a.getAttribute('data-submit-date') || '0', 10);
                const submitDateB = parseInt(b.getAttribute('data-submit-date') || '0', 10);
                const eventDateA = parseInt(a.getAttribute('data-event-date') || '0', 10);
                const eventDateB = parseInt(b.getAttribute('data-event-date') || '0', 10);

                switch (sortVal) {
                    case 'newest_submit':
                        return submitDateB - submitDateA;
                    case 'oldest_submit':
                        return submitDateA - submitDateB;
                    case 'soonest_event':
                        return eventDateA - eventDateB;
                    case 'latest_event':
                        return eventDateB - eventDateA;
                    default:
                        return 0;
                }
            });

            cards.forEach(card => card.remove());
            sortedCards.forEach(card => container.appendChild(card));

            let totalMatchCount = 0;
            let currentShown = 0;

            sortedCards.forEach((card) => {
                const title = card.getAttribute('data-title') || '';
                const eventDateStr = card.getAttribute('data-event-date-str') || '';

                let matchesSearch = title.includes(searchVal);
                let matchesStart = startDateVal === "" || eventDateStr >= startDateVal;
                let matchesEnd = endDateVal === "" || eventDateStr <= endDateVal;

                if (matchesSearch && matchesStart && matchesEnd) {
                    totalMatchCount++;
                    if (totalMatchCount <= currentVisibleCount) {
                        card.classList.remove('hidden-card');
                        currentShown++;
                    } else {
                        card.classList.add('hidden-card');
                    }
                } else {
                    card.classList.add('hidden-card');
                }
            });

            if (totalMatchCount === 0 && cards.length > 0) {
                if (noResult) noResult.classList.remove('hidden');
                if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
            } else {
                if (noResult) noResult.classList.add('hidden');

                if (!isFiltering && totalMatchCount > currentShown) {
                    if (loadMoreBtn) {
                        loadMoreBtn.classList.remove('hidden');
                        document.getElementById('showingCount').innerText = currentShown;
                        document.getElementById('totalCount').innerText = totalMatchCount;
                    }
                } else {
                    if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
                }
            }
        }

        function resetFilters() {
            const searchInputEl = document.getElementById('searchInput');
            const startDateEl = document.getElementById('startDateFilter');
            const endDateEl = document.getElementById('endDateFilter');
            const sortFilterEl = document.getElementById('sortFilter');

            if (searchInputEl) searchInputEl.value = '';
            if (startDateEl) startDateEl.value = '';
            if (endDateEl) endDateEl.value = '';
            if (sortFilterEl) sortFilterEl.value = 'newest_submit';

            currentVisibleCount = ITEMS_PER_PAGE;
            applyFilters();
        }

        document.addEventListener('DOMContentLoaded', () => {
            applyFilters();
        });
    </script>
@endsection