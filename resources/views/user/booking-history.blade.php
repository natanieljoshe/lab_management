@extends('layouts.user')

@section('title', 'Riwayat Peminjaman Saya')

@section('style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
    :root { --history-bg: #FBFAF5; --history-navy: #1A2D4A; --history-orange: #F57E21; --history-muted: rgba(26, 45, 74, .65); --history-line: rgba(26, 45, 74, .16); }
    ::-webkit-scrollbar { width: 10px; height: 10px; }
    ::-webkit-scrollbar-track { background: var(--history-bg); }
    ::-webkit-scrollbar-thumb { background: var(--history-navy); border: 2px solid var(--history-bg); border-radius: 8px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--history-orange); }
    * { scrollbar-width: thin; scrollbar-color: var(--history-navy) var(--history-bg); }
    .history-page { min-height: 100vh; background: var(--history-bg); color: var(--history-navy); font-family: 'Open Sans', sans-serif; }
    .history-page .history-wrap { width: 100%; max-width: 1240px; margin: 0 auto; padding: 150px 28px 48px; }
    .history-page .history-eyebrow { display: inline-flex; align-items: center; gap: 14px; color: var(--history-orange); font-size: .7rem; font-weight: 700; letter-spacing: .32em; text-transform: uppercase; }
    .history-page .history-eyebrow::before { content: ''; width: 46px; height: 1px; background: currentColor; }
    .history-page .history-title { margin: 18px 0 8px; color: var(--history-navy); font-family: 'Cormorant Garamond', Georgia, serif; font-size: clamp(2.7rem, 5vw, 4.5rem); font-weight: 500; line-height: .98; }
    .history-page .history-subtitle { color: var(--history-muted); line-height: 1.8; }
    .history-page .history-new-btn { min-height: 48px; border: 1px solid var(--history-navy); border-radius: 0; background: var(--history-navy); box-shadow: none; font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; transition: background .25s ease, border-color .25s ease; }
    .history-page .history-new-btn:hover { border-color: var(--history-orange); background: var(--history-orange); }
    .history-page .history-new-btn i { color: var(--history-orange); transition: transform .4s ease, color .25s ease; }
    .history-page .history-new-btn:hover i { color: var(--history-navy); transform: translateX(4px); }
    .history-page .history-filters { border: 1px solid var(--history-line); border-radius: 0; background: transparent; box-shadow: none; }
    .history-page input:not([type="date"]), .history-page select { border-radius: 0; border-color: var(--history-line); background-color: transparent; color: var(--history-navy); box-shadow: none; }
    .history-page input[type="date"] { color: var(--history-navy); }
    .history-page .history-date-filter { border-color: var(--history-line); border-radius: 0; background: rgba(26, 45, 74, .035); }
    .history-page input:focus, .history-page select:focus { border-color: var(--history-orange); --tw-ring-color: rgba(245, 126, 33, .18); outline-color: var(--history-orange); }
    .history-page .filter-icon { color: var(--history-orange); }
    .history-page .reset-filter { border-radius: 0; color: var(--history-muted); }
    .history-page .reset-filter:hover { border-color: var(--history-orange); background: rgba(245, 126, 33, .08); color: var(--history-navy); }
    .history-page .history-empty, .history-page .booking-card { border: 1px solid var(--history-line); border-radius: 0; background: transparent; box-shadow: none; }
    .history-page .booking-card { transition: border-color .3s ease; }
    .history-page .booking-card:hover { border-color: rgba(245, 126, 33, .65); }
    .history-page .booking-card-head { border-bottom: 1px solid var(--history-line); background: rgba(26, 45, 74, .035); }
    .history-page .booking-card-head .fa-calendar-check { color: var(--history-orange); }
    .history-page .booking-card h2 { color: var(--history-navy); font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.8rem; font-weight: 600; }
    .history-page .booking-meta { color: var(--history-muted); }
    .history-page .booking-meta-icon { color: var(--history-orange); }
    .history-page .booking-note { border: 1px solid var(--history-line); border-left: 3px solid var(--history-orange); border-radius: 0; background: rgba(26, 45, 74, .035); color: var(--history-navy); }
    .history-page .booking-note i { color: var(--history-orange); }
    .history-page .booking-items { border: 1px solid var(--history-line); border-radius: 0; background: rgba(26, 45, 74, .025); }
    .history-page .booking-items-title { color: var(--history-muted); }
    .history-page .booking-items-count { background: rgba(245, 126, 33, .12); color: var(--history-navy); }
    .history-page .lab-tag { border-radius: 0; background: rgba(245, 126, 33, .12); color: #9A4C0D; }
    .history-page .booking-item-icon { color: var(--history-orange); }
    .history-page .toggle-items { border-color: var(--history-line); border-radius: 0; color: var(--history-navy); }
    .history-page .toggle-items:hover { background: rgba(245, 126, 33, .08); }
    .history-page .status-pending { border-color: rgba(245, 126, 33, .45); background: rgba(245, 126, 33, .1); color: #9A4C0D; }
    .history-page .status-approved { border-color: #A7CDB8; background: #EAF3ED; color: #276347; }
    .history-page .status-rejected { border-color: #E2B4B0; background: #F8EEEC; color: #91433C; }
    .history-page .history-more { border-color: var(--history-line); border-radius: 0; color: var(--history-navy); box-shadow: none; }
    .history-page .history-more:hover { border-color: var(--history-orange); background: rgba(245, 126, 33, .08); color: var(--history-navy); }
    .history-page .empty-icon { background: rgba(245, 126, 33, .1); color: var(--history-orange); }
    .history-page .empty-title { color: var(--history-navy); font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.7rem; }
    .history-page .empty-copy { color: var(--history-muted); }
    .history-page .empty-reset { color: var(--history-orange); }
    .history-page .empty-reset:hover { color: var(--history-navy); }
    .history-footer { border-color: var(--history-line); background: var(--history-bg); color: var(--history-muted); }
    .hidden-card { display: none; }
    @media (max-width: 639px) { .history-page .history-wrap { padding: 120px 20px 40px; } }
</style>
@endsection

@section('body')

@include('user.partials.navbar')

<main class="history-page">
<div class="history-wrap">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <p class="history-eyebrow">Reservasi fasilitas laboratorium</p>
            <h1 class="history-title">Riwayat Peminjaman</h1>
            <p class="history-subtitle">Pantau status pengajuan peminjaman aset laboratorium Anda.</p>
        </div>
        <a href="{{ route('user.booking.form') }}" class="history-new-btn group text-white px-5 py-2.5 flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            <span class="hidden sm:inline">Buat Peminjaman</span>
            <span class="sm:hidden">Baru</span>
        </a>
    </div>

    @if(!$bookings->isEmpty())
    <div class="history-filters p-4 mb-8">
        <div class="flex flex-col xl:flex-row gap-4 items-start xl:items-center justify-between">

            <div class="relative w-full xl:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="filter-icon fa-solid fa-search"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Cari judul kegiatan..."
                    class="pl-10 w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-petra-blue focus:border-petra-blue transition-colors shadow-sm"
                    onkeyup="applyFilters()">
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto flex-wrap">

                <div class="history-date-filter flex items-center gap-2 w-full sm:w-auto border p-1">
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="date" id="startDateFilter"
                            class="w-full sm:w-36 bg-transparent border-none text-sm focus:ring-0 text-gray-600 p-1"
                            placeholder="Dari"
                            title="Dari Tanggal"
                            onchange="applyFilters()">
                    </div>
                    <span class="text-gray-400 text-sm">-</span>
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="date" id="endDateFilter"
                            class="w-full sm:w-36 bg-transparent border-none text-sm focus:ring-0 text-gray-600 p-1"
                            placeholder="Sampai"
                            title="Sampai Tanggal"
                            onchange="applyFilters()">
                    </div>
                </div>

                <div class="relative w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="filter-icon fa-solid fa-sort"></i>
                    </div>
                    <select id="sortFilter"
                        class="pl-10 w-full sm:w-48 rounded-lg border-gray-300 border px-3 py-2 text-sm focus:ring-petra-blue focus:border-petra-blue text-gray-600 shadow-sm appearance-none"
                        onchange="applyFilters()">
                        <option value="newest_submit">Diajukan: Terbaru</option>
                        <option value="oldest_submit">Diajukan: Terlama</option>
                        <option value="soonest_event">Acara: Terdekat</option>
                        <option value="latest_event">Acara: Terjauh</option>
                    </select>
                </div>

                <button onclick="resetFilters()" class="reset-filter px-4 py-2 text-sm transition-colors border border-transparent whitespace-nowrap">
                    Reset
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($bookings->isEmpty())
    <div class="history-empty p-12 text-center flex flex-col items-center">
        <div class="empty-icon w-20 h-20 flex items-center justify-center mb-4">
            <i class="fa-regular fa-folder-open text-3xl"></i>
        </div>
        <h3 class="empty-title font-semibold mb-2">Belum ada riwayat</h3>
        <p class="empty-copy max-w-md mx-auto mb-6">Anda belum pernah mengajukan peminjaman.</p>
    </div>
    @else
    <div class="grid gap-6" id="bookingList">
        @foreach($bookings as $booking)
        <div class="booking-card overflow-hidden"
            data-title="{{ strtolower($booking->event_name) }}"
            data-submit-date="{{ $booking->created_at->timestamp }}"
            data-event-date="{{ \Carbon\Carbon::parse($booking->event_started_at)->timestamp }}"
            data-event-date-str="{{ \Carbon\Carbon::parse($booking->event_started_at)->format('Y-m-d') }}">

            <div class="booking-card-head px-6 py-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <i class="fa-regular fa-calendar-check text-petra-blue"></i>
                    <span class="text-sm font-semibold text-gray-600">
                        Diajukan: {{ \Carbon\Carbon::parse($booking->created_at)->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>

                @php
                $statusClass = 'status-pending';
                $statusIcon = 'fa-clock';
                $statusText = 'Menunggu Persetujuan';

                if($booking->approved === 1) {
                $statusClass = 'status-approved';
                $statusIcon = 'fa-check-circle';
                $statusText = 'Disetujui';
                } elseif($booking->approved === 0) {
                $statusClass = 'status-rejected';
                $statusIcon = 'fa-times-circle';
                $statusText = 'Ditolak';
                }
                @endphp

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                    <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
                </span>
            </div>

            <div class="p-6">
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="col-span-2">
                        <h2 class="text-xl font-bold text-petra-blue mb-1">{{ $booking->event_name }}</h2>
                        <div class="booking-meta flex flex-col gap-2 text-sm mt-3">
                            <div class="flex items-center gap-2">
                                <i class="booking-meta-icon fa-solid fa-clock w-5 text-center"></i>
                                <span>
                                    {{ \Carbon\Carbon::parse($booking->event_started_at)->format('d M H:i') }}
                                    <span class="mx-1 text-gray-300">|</span>
                                    {{ \Carbon\Carbon::parse($booking->event_ended_at)->format('d M H:i') }}
                                </span>
                            </div>
                            @if($booking->thesis_title)
                            <div class="flex items-start gap-2">
                                <i class="booking-meta-icon fa-solid fa-graduation-cap w-5 text-center mt-0.5"></i>
                                <span>Judul Skripsi: <span class="font-medium text-gray-800 italic">"{{ $booking->thesis_title }}"</span></span>
                            </div>
                            @endif
                            @if($booking->supervisor)
                            <div class="flex items-center gap-2">
                                <i class="booking-meta-icon fa-solid fa-user-tie w-5 text-center"></i>
                                <span>Pembimbing: <span class="font-medium text-gray-800">{{ $booking->supervisor->name }}</span></span>
                            </div>
                            @endif
                        </div>
                        @if($booking->booking_detail)
                        <div class="booking-note mt-4 p-3 text-sm flex gap-2">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <div><span class="font-bold">Catatan:</span> {{ $booking->booking_detail }}</div>
                        </div>
                        @endif
                    </div>
                    <div class="booking-items col-span-1 p-4 h-fit">
                        <h4 class="booking-items-title text-xs font-bold uppercase tracking-wider mb-3 flex justify-between items-center">
                            Item Dipinjam
                            <span class="booking-items-count py-0.5 px-2 text-[10px]">{{ $booking->bookings_items->count() }}</span>
                        </h4>
                        <ul class="space-y-2">
                            @foreach($booking->bookings_items as $index => $item)
                            <li class="flex items-start gap-2 text-sm text-gray-700 {{ $index >= 3 ? 'hidden extra-item-'.$booking->id : '' }}">
                                <i class="booking-item-icon fa-solid fa-box mt-1 text-xs"></i>
                                <span class="leading-tight">
                                    {{ $item->bookable->name ?? 'Unknown Item' }}
                                    @if(str_contains($item->bookable_type, 'Labs'))
                                    <span class="lab-tag text-[10px] px-1 ml-1 font-bold">LAB</span>
                                    @endif
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        @if($booking->bookings_items->count() > 3)
                        <button onclick="toggleItems('{{ $booking->id }}', this)"
                            class="toggle-items w-full mt-3 text-xs font-bold py-2 transition-colors flex items-center justify-center gap-1 border border-dashed">
                            <span>+ {{ $booking->bookings_items->count() - 3 }} item lainnya</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div> 
    <div id="loadMoreWrapper" class="text-center mt-8 hidden">
        <button onclick="loadMore()" class="history-more group px-6 py-2.5 border font-medium text-sm transition-all">
            <span id="loadMoreText">Muat Lebih Banyak</span>
            <i class="fa-solid fa-chevron-down ml-2 text-xs transition-transform group-hover:translate-y-0.5"></i>
        </button>
        <p class="text-xs text-gray-400 mt-2">Menampilkan <span id="showingCount">0</span> dari <span id="totalCount">0</span> riwayat</p>
    </div>
</div>

<div id="noResultState" class="hidden mt-8 text-center py-12">
    <div class="empty-icon inline-flex items-center justify-center w-16 h-16 mb-4">
        <i class="fa-solid fa-calendar-xmark text-2xl"></i>
    </div>
    <h3 class="empty-title font-semibold">Tidak ditemukan</h3>
    <p class="empty-copy">Tidak ada peminjaman dalam rentang tanggal atau kata kunci tersebut.</p>
    <button onclick="resetFilters()" class="empty-reset mt-4 font-bold hover:underline">Reset Filter</button>
</div>
@endif
</div>
</main>

<footer class="history-footer border-t mt-12 py-8">
    <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} Laboratorium Gedung P - UK Petra. All rights reserved.
    </div>
</footer>

@endsection

@section('script')
<script>
    const ITEMS_PER_PAGE = 5; 
    let currentVisibleCount = ITEMS_PER_PAGE;

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
        const searchVal = document.getElementById('searchInput').value.toLowerCase();
        const startDateVal = document.getElementById('startDateFilter').value;
        const endDateVal = document.getElementById('endDateFilter').value;
        const sortVal = document.getElementById('sortFilter').value;

        const isFiltering = (searchVal !== '' || startDateVal !== '' || endDateVal !== '');

        
        if (isFiltering && !isLoadingMore) {
            currentVisibleCount = 999999; 
        } else if (!isFiltering && !isLoadingMore) {
            currentVisibleCount = ITEMS_PER_PAGE;
        }

        const container = document.getElementById('bookingList');
        const cards = Array.from(container.getElementsByClassName('booking-card'));
        const noResult = document.getElementById('noResultState');
        const loadMoreBtn = document.getElementById('loadMoreWrapper');

        const sortedCards = cards.sort((a, b) => {
            const submitDateA = parseInt(a.getAttribute('data-submit-date'));
            const submitDateB = parseInt(b.getAttribute('data-submit-date'));
            const eventDateA = parseInt(a.getAttribute('data-event-date'));
            const eventDateB = parseInt(b.getAttribute('data-event-date'));

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
            const title = card.getAttribute('data-title');
            const eventDateStr = card.getAttribute('data-event-date-str');

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
            noResult.classList.remove('hidden');
            loadMoreBtn.classList.add('hidden');
        } else {
            noResult.classList.add('hidden');

            if (!isFiltering && totalMatchCount > currentShown) {
                loadMoreBtn.classList.remove('hidden');
                document.getElementById('showingCount').innerText = currentShown;
                document.getElementById('totalCount').innerText = totalMatchCount;
            } else {
                loadMoreBtn.classList.add('hidden');
            }
        }
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('startDateFilter').value = '';
        document.getElementById('endDateFilter').value = '';
        document.getElementById('sortFilter').value = 'newest_submit';

        currentVisibleCount = ITEMS_PER_PAGE;
        applyFilters();
    }

    document.addEventListener('DOMContentLoaded', () => {
        applyFilters();
    });
</script>
@endsection
