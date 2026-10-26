@extends('layouts.admin')

@section('title', 'Manajemen Bookings')

@section('style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .admin-bookings-page {
            --booking-bg: #FBFAF5;
            --booking-navy: #1A2D4A;
            --booking-orange: #F57E21;
            position: relative;
            min-height: 100vh;
            padding-top: 1px;
            background-color: var(--booking-bg);
            background-image: radial-gradient(520px circle at var(--mx, 70%) var(--my, 30%), rgba(245, 126, 33, .11), transparent 60%);
            background-repeat: no-repeat;
            color: var(--booking-navy);
            font-family: 'Open Sans', sans-serif;
            transition: background .2s ease;
        }
        .admin-bookings-page .admin-bookings-title {
            color: var(--booking-navy);
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(2.6rem, 5vw, 4rem);
            font-weight: 500;
            letter-spacing: -.015em;
        }
        .admin-bookings-page .admin-surface { border: 1px solid rgba(26, 45, 74, .16); border-radius: 0; box-shadow: none; transition: border-color .3s ease; }
        .admin-bookings-page .admin-surface:hover { border-color: rgba(245, 126, 33, .65); }
        .admin-bookings-page .admin-surface-title { color: var(--booking-navy); font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.55rem; font-weight: 600; }
        .admin-bookings-page .filter-input:focus, .admin-bookings-page #search-input:focus { border-color: var(--booking-orange); --tw-ring-color: rgba(245, 126, 33, .2); }
        .admin-bookings-page #bookings-table tbody tr { transition: background-color .2s ease; }
        .admin-bookings-page #bookings-table tbody tr:hover { background-color: rgba(245, 126, 33, .06); }
        .admin-bookings-page #bookings-table thead { background-color: rgba(26, 45, 74, .045); }
        .admin-bookings-page #bookings-table th { color: rgba(26, 45, 74, .65); }
    </style>
@endsection

@section('body')
    <main class="admin-bookings-page" id="adminBookingsPage">
    <div class="flex flex-col md:flex-row w-full py-4 items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="admin-bookings-title text-center uppercase mb-2 md:mb-0">Kelola Bookings</h1>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="admin-surface bg-white p-6 md:p-8">
            <h2 class="admin-surface-title mb-4">Filter & Pencarian</h2>

            {{-- Filter Inputs --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-select">
                <div>
                    <label for="filter_status" class="block text-sm font-semibold text-gray-700 mb-2">Status Persetujuan</label>
                    <select id="filter_status" class="filter-input" placeholder="Semua Status...">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div>
                    <label for="filter_year" class="block text-sm font-semibold text-gray-700 mb-2">Tahun Ajaran</label>
                    <select id="filter_year" class="filter-input" placeholder="Semua Tahun...">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_semester" class="block text-sm font-semibold text-gray-700 mb-2">Semester</label>
                    <select id="filter_semester" class="filter-input" placeholder="Semua Semester...">
                        <option value="">Semua Semester</option>
                        <option value="GASAL">Gasal</option>
                        <option value="GENAP">Genap</option>
                    </select>
                </div>

                <div>
                    <label for="filter_type" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pinjam</label>
                    <select id="filter_type" class="filter-input" placeholder="Semua Tipe...">
                        <option value="">Semua Tipe</option>
                        <option value="Items">Items</option>
                        <option value="Components">Components</option>
                        <option value="Labs">Labs</option>
                    </select>
                </div>

                <div>
                    <label for="filter_return" class="block text-sm font-semibold text-gray-700 mb-2">Status Pengembalian</label>
                    <select id="filter_return" class="filter-input" placeholder="Semua Status...">
                        <option value="">Semua Status</option>
                        <option value="complete">Sudah Lengkap</option>
                        <option value="complete_damaged">Sudah Lengkap - Ada Rusak</option>
                        <option value="incomplete">Belum Lengkap</option>
                        <option value="not_returned">Belum Kembali</option>
                    </select>
                </div>

                <div>
                    <label for="filter_deadline" class="block text-sm font-semibold text-gray-700 mb-2">Jatuh Tempo</label>
                    <select id="filter_deadline" class="filter-input" placeholder="Semua...">
                        <option value="">Semua</option>
                        <option value="overdue">Sudah Jatuh Tempo</option>
                        <option value="upcoming">Belum Jatuh Tempo</option>
                    </select>
                </div>

                <div>
                    <label for="filter_approver" class="block text-sm font-semibold text-gray-700 mb-2">Disetujui Oleh</label>
                    <select id="filter_approver" class="filter-input" placeholder="Semua Approver...">
                        <option value="">Semua Approver</option>
                        @foreach($approvers as $approver)
                        <option value="{{ $approver->id }}">{{ $approver->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_unit" class="block text-sm font-semibold text-gray-700 mb-2">Unit Peminjam</label>
                    <select id="filter_unit" class="filter-input" placeholder="Semua Unit...">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                <div class="relative flex w-full">
                    <input id="search-input" type="search"
                        class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                        placeholder="Cari peminjam, event..." />
                    <button class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <button id="reset-filter-btn" type="button" class="px-6 py-2 bg-gray-200 w-full sm:w-auto text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">
        <div class="admin-surface bg-white">
            <div class="p-6 border-b border-gray-200">
                <h2 class="admin-surface-title">Daftar Bookings</h2>
            </div>
            <div class="p-6">
                {{-- Container Table --}}
                <div id="bookings-datatable-wrapper" class="overflow-x-auto w-full">
                    <table id="bookings-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Pinjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui Oleh</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bookings as $booking)
                            @php
                                $statusValue = is_null($booking->approved) ? 'pending' : ($booking->approved ? 'approved' : 'rejected');
                                $types = $booking->bookings_items->pluck('bookable_type')->map(fn($t) => class_basename($t))->unique()->implode(',');
                                $totalItems = $booking->bookings_items->count();
                                $returnedItems = $booking->bookings_items->where('returned_at', '!=', null)->count();
                                $damagedItems = $booking->bookings_items->where('returned_status', 0)->count();
                                $returnStatus = $returnedItems == 0 ? 'not_returned' : ($returnedItems == $totalItems ? ($damagedItems > 0 ? 'complete_damaged' : 'complete') : 'incomplete');
                                $now = \Carbon\Carbon::now('Asia/Jakarta');
                                $deadline = \Carbon\Carbon::parse($booking->return_deadline_at, 'Asia/Jakarta');
                                $deadlineStatus = $now->gt($deadline) ? 'overdue' : ($now->diffInDays($deadline) <= 3 ? 'upcoming' : 'normal');
                            @endphp
                            {{-- Atribut data-* digunakan untuk filtering di JS --}}
                            <tr class="booking-row-data"
                                data-status="{{ $statusValue }}"
                                data-year="{{ $booking->period->academic_year ?? '' }}"
                                data-semester="{{ $booking->period->semester ?? '' }}"
                                data-types="{{ $types }}"
                                data-return="{{ $returnStatus }}"
                                data-deadline="{{ $deadlineStatus }}"
                                data-approver="{{ $booking->approver->id ?? '' }}"
                                data-unit="{{ $booking->borrower->unit->id ?? '' }}"
                                >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $booking->borrower->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->phone_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->event_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->bookings_items->count() }} item(s)</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->borrowed_at)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->borrowed_at)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->return_deadline_at)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->return_deadline_at)->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(is_null($booking->approved))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @elseif($booking->approved)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $booking->approver->name ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button onclick="viewDetail('{{ $booking->id }}')" class="px-3 py-1.5 bg-blue-600 text-white hover:bg-blue-700 text-xs font-semibold rounded-md mr-2">Detail</button>
                                    @if(is_null($booking->approved))
                                    <button onclick="approveReject('{{ $booking->id }}')" class="px-3 py-1.5 bg-green-600 text-white hover:bg-green-700 text-xs font-semibold rounded-md mr-2">Approve</button>
                                    @endif
                                    @if($booking->approved)
                                    <button onclick="returnItems('{{ $booking->id }}')" class="px-3 py-1.5 bg-purple-600 text-white hover:bg-purple-700 text-xs font-semibold rounded-md">Return</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div id="detail-modal" class="hidden" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]" onclick="closeModal('detail-modal')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 z-[3001]" style="pointer-events: none;">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" style="pointer-events: auto;">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-semibold">Detail Booking</h3>
                    <button onclick="closeModal('detail-modal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="detail-content" class="p-6"></div>
            </div>
        </div>
    </div>

    {{-- Modal Return --}}
    <div id="return-modal" class="hidden" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]" onclick="closeModal('return-modal')"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 z-[3001]" style="pointer-events: none;">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto" style="pointer-events: auto;">
                <div class="flex justify-between items-center p-6 border-b">
                    <h3 class="text-xl font-semibold">Catat Pengembalian</h3>
                    <button onclick="closeModal('return-modal')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div id="return-content" class="p-6"></div>
            </div>
        </div>
    </div>
    </main>
    @include('user.partials.footer')
@endsection

@section('script')
<style>
    .ts-control {
        @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
    }

    /* --- NUCLEAR CSS FIX UNTUK PAGINATION --- */
    /* Kita gunakan !important untuk menimpa style inline dari library TE */
    
    /* 1. Kontainer Utama Pagination */
    div[data-te-datatable-pagination-ref] {
        display: flex !important;
        flex-direction: row !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: nowrap !important; /* Paksa satu baris */
        width: 100% !important;
        padding: 1rem 1.5rem !important;
        background-color: white !important;
        border-top: 1px solid #e5e7eb !important;
        min-height: 60px !important;
    }

    /* 2. Sisi Kiri (Rows per page + Dropdown) */
    div[data-te-datatable-pagination-ref] > div:first-child {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 1rem !important;
        margin: 0 !important;
    }

    /* 3. Sisi Kanan (Text "1-10 of 100" + Panah) */
    div[data-te-datatable-pagination-ref] > div:last-child {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        gap: 1rem !important;
        margin: 0 !important;
    }

    /* 4. Fix untuk Dropdown "Rows per page" agar tidak tumpuk */
    div[data-te-datatable-select-wrapper-ref] {
        display: inline-block !important;
        margin: 0 !important;
        width: auto !important;
        min-width: 70px !important; /* Beri lebar minimum */
    }
    
    /* 5. Input Select itu sendiri */
    div[data-te-datatable-select-wrapper-ref] input {
        padding-right: 2rem !important; /* Ruang untuk panah dropdown */
        width: auto !important;
    }

    /* 6. Pastikan Text tidak turun ke bawah */
    div[data-te-datatable-pagination-ref] span {
        white-space: nowrap !important;
    }

    #bookings-datatable-wrapper,
    #bookings-table {
        width: 100% !important;
    }

    #bookings-table + div,
    [data-te-datatable-pagination-ref] {
        width: 100% !important;
    }
    /* FIX 1: Paksa wrapper internal TE untuk lebar 100% */
    div[data-te-datatable-inner-ref] {
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* FIX 2: Paksa tabel itu sendiri untuk lebar 100% */
    table[data-te-datatable-ref],
    #bookings-table { /* Tambahkan ID table kamu disini */
        width: 100% !important;
        max-width: 100% !important;
        display: table !important; /* Mencegah display: block yang kadang dipasang library */
    }

    /* FIX 3: Pastikan container utama juga full width */
    #bookings-datatable-wrapper,
    #bookings-table {
        width: 100% !important;
        display: block !important;
    }

    /* FIX 4: Pagination container agar sejajar */
    div[data-te-datatable-pagination-ref] {
        width: 100% !important;
    }
</style>

<script>
document.getElementById('bookings').classList.add('bg-slate-100');
document.getElementById('bookings').classList.add('active');

const adminBookingsPage = document.getElementById('adminBookingsPage');
if (adminBookingsPage && !window.matchMedia('(pointer: coarse), (prefers-reduced-motion: reduce)').matches) {
    adminBookingsPage.addEventListener('pointermove', (event) => {
        const bounds = adminBookingsPage.getBoundingClientRect();
        adminBookingsPage.style.setProperty('--mx', `${((event.clientX - bounds.left) / bounds.width) * 100}%`);
        adminBookingsPage.style.setProperty('--my', `${((event.clientY - bounds.top) / bounds.height) * 100}%`);
    });
}

let tomSelects = {};
let dataTableInstance;
let allTableData = [];

document.addEventListener('DOMContentLoaded', function() {
    tomSelects.status = new TomSelect('#filter_status', { plugins: ['clear_button'] });
    tomSelects.year = new TomSelect('#filter_year', { plugins: ['clear_button'] });
    tomSelects.semester = new TomSelect('#filter_semester', { plugins: ['clear_button'] });
    tomSelects.type = new TomSelect('#filter_type', { plugins: ['clear_button'] });
    tomSelects.return = new TomSelect('#filter_return', { plugins: ['clear_button'] });
    tomSelects.deadline = new TomSelect('#filter_deadline', { plugins: ['clear_button'] });
    tomSelects.approver = new TomSelect('#filter_approver', { plugins: ['clear_button'] });
    tomSelects.unit = new TomSelect('#filter_unit', { plugins: ['clear_button'] });

    const originalRows = document.querySelectorAll('#bookings-table tbody tr');
    originalRows.forEach(row => {
        const cells = Array.from(row.children).map(cell => cell.innerHTML);
        allTableData.push({
            status: row.dataset.status,
            year: row.dataset.year,
            semester: row.dataset.semester,
            types: row.dataset.types,
            returnStatus: row.dataset.return,
            deadline: row.dataset.deadline,
            approver: row.dataset.approver,
            unit: row.dataset.unit,
            rowData: cells 
        });
    });

    try {
        const tableEl = document.getElementById('bookings-table');
        dataTableInstance = new te.Datatable(tableEl, {
            hover: true,
            fixedHeader: true,
            pagination: true,
            entries: 10,
            entriesOptions: [5, 10, 25, 50],
            noFoundMessage: 'Tidak ada booking ditemukan.',
        });

        // Patch JS juga untuk memastikan style applied setelah render
        setTimeout(fixPaginationLayout, 100);

        document.getElementById('search-input').addEventListener('input', (e) => {
            dataTableInstance.search(e.target.value);
            applyFilters(); 
            setTimeout(fixPaginationLayout, 100);
        });
        
        tableEl.addEventListener('click', () => setTimeout(fixPaginationLayout, 100));

    } catch (e) {
        console.warn("DataTable init failed", e);
    }

    Object.values(tomSelects).forEach(ts => {
        ts.on('change', () => {
            applyFilters();
            setTimeout(fixPaginationLayout, 100);
        });
    });

    document.getElementById('reset-filter-btn').addEventListener('click', () => {
        Object.values(tomSelects).forEach(ts => ts.clear());
        document.getElementById('search-input').value = '';
        dataTableInstance.update({
            rows: allTableData.map(item => item.rowData)
        }, { loading: false });
        setTimeout(fixPaginationLayout, 100);
    });
});

function applyFilters() {
    const filters = {
        status: tomSelects.status.getValue(),
        year: tomSelects.year.getValue(),
        semester: tomSelects.semester.getValue(),
        type: tomSelects.type.getValue(),
        returnStatus: tomSelects.return.getValue(),
        deadline: tomSelects.deadline.getValue(),
        approver: tomSelects.approver.getValue(),
        unit: tomSelects.unit.getValue(),
    };

    const filteredData = allTableData.filter(item => {
        if (filters.status && item.status !== filters.status) return false;
        if (filters.year && item.year !== filters.year) return false;
        if (filters.semester && item.semester !== filters.semester) return false;
        if (filters.type && !item.types.includes(filters.type)) return false;
        if (filters.returnStatus && item.returnStatus !== filters.returnStatus) return false;
        if (filters.deadline && item.deadline !== filters.deadline) return false;
        if (filters.approver && item.approver !== filters.approver) return false;
        if (filters.unit && item.unit !== filters.unit) return false;
        return true;
    });

    dataTableInstance.update({
        rows: filteredData.map(item => item.rowData)
    }, { loading: false });

    const searchValue = document.getElementById('search-input').value;
    if(searchValue) {
        dataTableInstance.search(searchValue);
    }
}

// Fungsi JS tambahan untuk 'memaksa' layout jika CSS saja tidak mempan
function fixPaginationLayout() {
    const paginations = document.querySelectorAll('[data-te-datatable-pagination-ref]');
    paginations.forEach(p => {
        p.style.setProperty('display', 'flex', 'important');
        p.style.setProperty('flex-direction', 'row', 'important');
        p.style.setProperty('justify-content', 'space-between', 'important');
        p.style.setProperty('width', '100%', 'important');
        p.style.setProperty('flex-wrap', 'nowrap', 'important');
        
        Array.from(p.children).forEach(child => {
            child.style.setProperty('display', 'flex', 'important');
            child.style.setProperty('flex-direction', 'row', 'important');
            child.style.setProperty('align-items', 'center', 'important');
            child.style.setProperty('gap', '10px', 'important');
            child.style.setProperty('margin', '0', 'important');
        });
    });
}

function viewDetail(bookingId) {
    showLoading('Memuat detail...', 'Mohon tunggu');
    
    fetch(`/admin/bookings/${bookingId}/details`)
        .then(res => res.json())
        .then(response => {
            if(response.success) {
                const booking = response.data;
                
                let html = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div><p class="text-sm text-gray-500">Peminjam</p><p class="font-medium">${booking.borrower?.name || '-'}</p><p class="text-xs text-gray-500">${booking.borrower?.unit?.name || ''}</p></div>
                            <div><p class="text-sm text-gray-500">Nomor Telepon</p><p class="font-medium">${booking.phone_number || '-'}</p></div>
                            <div><p class="text-sm text-gray-500">Nama Event</p><p class="font-medium">${booking.event_name}</p></div>
                            <div><p class="text-sm text-gray-500">Jumlah Peserta</p><p class="font-medium">${booking.attendee_count || '-'}</p></div>
                            <div><p class="text-sm text-gray-500">Waktu Event</p><p class="font-medium">${formatDate(booking.event_started_at)} - ${formatDate(booking.event_ended_at)}</p></div>
                            <div><p class="text-sm text-gray-500">Waktu Pinjam</p><p class="font-medium">${formatDate(booking.borrowed_at)} - ${formatDate(booking.return_deadline_at)}</p></div>
                            ${booking.thesis_title ? `<div class="col-span-2"><p class="text-sm text-gray-500">Judul Skripsi</p><p class="font-medium">${booking.thesis_title}</p></div>` : ''}
                            ${booking.supervisor ? `<div><p class="text-sm text-gray-500">Dosen Pembimbing</p><p class="font-medium">${booking.supervisor.name}</p><p class="text-xs text-gray-500">${booking.supervisor.unit?.name || ''}</p></div>` : ''}
                            ${booking.booking_detail ? `<div class="col-span-2"><p class="text-sm text-gray-500">Detail</p><p class="font-medium">${booking.booking_detail}</p></div>` : ''}
                            <div><p class="text-sm text-gray-500">Status</p><p class="font-medium">${booking.approved === null ? 'Pending' : (booking.approved ? 'Disetujui' : 'Ditolak')}</p></div>
                            ${booking.approver ? `<div><p class="text-sm text-gray-500">Disetujui Oleh</p><p class="font-medium">${booking.approver.name}</p><p class="text-xs text-gray-500">${booking.approver.unit?.name || ''}</p></div>` : ''}
                        </div>
                        
                        ${(() => {
                            const items = booking.bookings_items.filter(bi => bi.bookable_type.includes('Items'));
                            const components = booking.bookings_items.filter(bi => bi.bookable_type.includes('Components'));
                            const labs = booking.bookings_items.filter(bi => bi.bookable_type.includes('Labs'));
                            
                            if(items.length === 0 && components.length === 0 && labs.length === 0) return '';
                            
                            const typeCounts = {};
                            const setTypes = ['MONITOR', 'MOUSE', 'KEYBOARD', 'CPU'];
                            
                            items.forEach(item => {
                                const typeName = item.bookable?.type?.name;
                                if(typeName) {
                                    typeCounts[typeName] = (typeCounts[typeName] || 0) + 1;
                                }
                            });
                            
                            const setCounts = setTypes.map(t => typeCounts[t] || 0);
                            const minSets = setCounts.length > 0 && setCounts.every(c => c > 0) ? Math.min(...setCounts) : 0;
                            const otherItems = items.length - (minSets * 4);
                            
                            let summary = [];
                            if(minSets > 0) summary.push(`${minSets} Set PC Lengkap`);
                            if(otherItems > 0) summary.push(`${otherItems} Item lain`);
                            if(components.length > 0) summary.push(`${components.length} Component`);
                            if(labs.length > 0) summary.push(`${labs.length} Lab`);
                            
                            if(summary.length === 0) return '';
                            
                            return `
                                <div class="col-span-2 mt-2">
                                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3">
                                        <p class="text-sm font-semibold text-indigo-900">📦 Ringkasan Peminjaman:</p>
                                        <p class="text-sm text-indigo-800 mt-1">${summary.join(' + ')}</p>
                                    </div>
                                </div>
                            `;
                        })()}
                        </div>
                        
                        <div class="border-t pt-4 mt-4">
                            <h4 class="font-semibold text-lg mb-4 text-gray-800">Barang yang Dipinjam</h4>
                            <div class="space-y-4">
                                ${booking.bookings_items.map(item => {
                                    const bookable = item.bookable;
                                    const type = item.bookable_type.split('\\').pop();
                                    let name = bookable?.name || '-';
                                    let details = '';
                                    
                                    if(type === 'Items') {
                                        let location = '-';
                                        if(bookable?.desk) {
                                            location = `Meja ${bookable.desk.location}${bookable.desk.lab ? ' - ' + bookable.desk.lab.name : ''}`;
                                        } else if(bookable?.lab) {
                                            location = `Lab Storage ${bookable.lab.name}`;
                                        }
                                        
                                        let specs = '';
                                        const specValues = bookable?.spec_set_values || bookable?.specSetValues || [];
                                        if(specValues.length > 0) {
                                            specs = specValues.map(sv => 
                                                `${sv.spec_attributes?.name || sv.specAttributes?.name || ''}: ${sv.value || ''}`
                                            ).join(', ');
                                        }
                                        
                                        details = `
                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                <div><span class="text-gray-500">Type:</span> <span class="font-medium">${bookable?.type?.name || '-'}</span></div>
                                                <div><span class="text-gray-500">Lokasi:</span> <span class="font-medium">${location}</span></div>
                                                ${specs ? `<div class="col-span-2"><span class="text-gray-500">Spesifikasi:</span> <span class="font-medium">${specs}</span></div>` : ''}
                                            </div>
                                            ${bookable?.components && bookable.components.length > 0 ? `
                                                <div class="mt-3 pt-3 border-t">
                                                    <p class="text-sm font-semibold text-gray-700 mb-2">Components:</p>
                                                    <div class="space-y-2">
                                                        ${bookable.components.map(c => {
                                                            let cSpecs = '';
                                                            const cSpecValues = c.spec_set_values || c.specSetValues || [];
                                                            if(cSpecValues.length > 0) {
                                                                cSpecs = cSpecValues.map(sv => 
                                                                    `${sv.spec_attributes?.name || sv.specAttributes?.name || ''}: ${sv.value || ''}`
                                                                ).join(', ');
                                                            }
                                                            return `
                                                                <div class="bg-gray-50 rounded p-2 text-sm">
                                                                    <p class="font-medium">${c.name}</p>
                                                                    <p class="text-xs text-gray-600">Type: ${c.type?.name || '-'}</p>
                                                                    ${cSpecs ? `<p class="text-xs text-gray-600">Spesifikasi: ${cSpecs}</p>` : ''}
                                                                </div>
                                                            `;
                                                        }).join('')}
                                                    </div>
                                                </div>
                                            ` : ''}
                                        `;
                                    } else if(type === 'Components') {
                                        let location = '-';
                                        if(bookable?.item) {
                                            let itemLoc = bookable.item.name;
                                            if(bookable.item.desk?.lab) {
                                                itemLoc += ` (Meja ${bookable.item.desk.location} - ${bookable.item.desk.lab.name})`;
                                            }
                                            location = `Item ${itemLoc}`;
                                        } else if(bookable?.lab) {
                                            location = `Lab Storage ${bookable.lab.name}`;
                                        }
                                        
                                        let specs = '';
                                        const specValues = bookable?.spec_set_values || bookable?.specSetValues || [];
                                        if(specValues.length > 0) {
                                            specs = specValues.map(sv => 
                                                `${sv.spec_attributes?.name || sv.specAttributes?.name || ''}: ${sv.value || ''}`
                                            ).join(', ');
                                        }
                                        
                                        details = `
                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                <div><span class="text-gray-500">Type:</span> <span class="font-medium">${bookable?.type?.name || 'Component'}</span></div>
                                                <div><span class="text-gray-500">Lokasi:</span> <span class="font-medium">${location}</span></div>
                                                ${specs ? `<div class="col-span-2"><span class="text-gray-500">Spesifikasi:</span> <span class="font-medium">${specs}</span></div>` : ''}
                                            </div>
                                        `;
                                    } else if(type === 'Labs') {
                                        details = `
                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                <div><span class="text-gray-500">Type:</span> <span class="font-medium">Lab</span></div>
                                                ${bookable?.location ? `<div><span class="text-gray-500">Lokasi:</span> <span class="font-medium">${bookable.location}</span></div>` : ''}
                                                ${bookable?.capacity ? `<div><span class="text-gray-500">Kapasitas:</span> <span class="font-medium">${bookable.capacity} orang</span></div>` : ''}
                                            </div>
                                        `;
                                    }
                                    
                                    const returnStatus = item.returned_at ? 
                                        `<span class="px-2 py-1 text-xs rounded-full ${item.returned_status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${item.returned_status ? 'Baik' : 'Rusak'}</span>` : 
                                        `<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Belum Kembali</span>`;
                                    
                                    return `
                                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow bg-gradient-to-br from-white to-gray-50">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <p class="font-semibold text-lg text-gray-800">${name}</p>
                                                    <p class="text-xs text-gray-500 mt-1">${type}</p>
                                                </div>
                                                ${returnStatus}
                                            </div>
                                            <div class="mt-2">${details}</div>
                                            ${item.returned_at ? `
                                                <div class="mt-3 pt-3 border-t border-gray-200">
                                                    <div class="bg-blue-50 rounded-lg p-3 text-sm">
                                                        <p class="text-gray-700"><span class="font-semibold">Dikembalikan:</span> ${formatDate(item.returned_at)}</p>
                                                        ${item.returned_detail ? `<p class="text-gray-700 mt-1"><span class="font-semibold">Catatan:</span> ${item.returned_detail}</p>` : ''}
                                                    </div>
                                                </div>
                                            ` : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                `;
                
                document.getElementById('detail-content').innerHTML = html;
                document.getElementById('detail-modal').classList.remove('hidden');
                Swal.close();
            }
        })
        .catch(() => showToast('Error', 'Gagal memuat detail', 'error'));
}

function approveReject(bookingId) {
    Swal.fire({
        title: 'Setujui atau Tolak?',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Setujui',
        denyButtonText: 'Tolak',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if(result.isConfirmed || result.isDenied) {
            const approved = result.isConfirmed;
            
            fetch(`/admin/bookings/${bookingId}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ approved })
            })
            .then(res => res.json())
            .then(response => {
                if(response.success) {
                    showToast('Berhasil', response.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('Error', response.message, 'error');
                }
            })
            .catch(() => showToast('Error', 'Terjadi kesalahan', 'error'));
        }
    });
}

function returnItems(bookingId) {
    showLoading('Memuat data...', 'Mohon tunggu');
    
    fetch(`/admin/bookings/${bookingId}/details`)
        .then(res => res.json())
        .then(response => {
            if(response.success) {
                const booking = response.data;
                const unreturned = booking.bookings_items.filter(item => !item.returned_at);
                
                if(unreturned.length === 0) {
                    Swal.close();
                    showToast('Info', 'Semua barang sudah dikembalikan', 'info');
                    return;
                }
                
                let html = `
                    <form id="returnForm">
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-blue-900 mb-2">Cara Pengembalian Barang:</h4>
                                <ol class="text-sm text-blue-800 space-y-1 ml-4 list-decimal">
                                    <li>Centang barang yang dikembalikan</li>
                                    <li>Pilih status kondisi barang (Baik/Rusak)</li>
                                    <li>Tambahkan catatan jika diperlukan</li>
                                    <li>Gunakan Status/Catatan Global untuk mengisi semua barang sekaligus</li>
                                </ol>
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium mb-1">Status Global</label>
                                    <p class="text-xs text-gray-600 mb-2">*Isi ini untuk menerapkan status yang sama ke semua barang yang dicentang</p>
                                    <select id="globalStatus" class="w-full px-4 py-2 border rounded-lg">
                                        <option value="">-- Pilih untuk semua --</option>
                                        <option value="1">Baik</option>
                                        <option value="0">Rusak</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Catatan Global</label>
                                    <p class="text-xs text-gray-600 mb-2">*Isi ini untuk menerapkan catatan yang sama ke semua barang yang dicentang</p>
                                    <textarea id="globalDetail" class="w-full px-4 py-2 border rounded-lg" rows="2" placeholder="Contoh: Semua barang dalam kondisi baik"></textarea>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <h4 class="font-semibold mb-3">Pilih Barang yang Dikembalikan:</h4>
                            ${unreturned.map(item => {
                                const bookable = item.bookable;
                                const type = item.bookable_type.split('\\').pop();
                                let name = bookable?.name || '-';
                                let details = type === 'Items' ? `${bookable?.type?.name || ''}` : type === 'Components' ? 'Component' : 'Lab';
                                
                                return `
                                    <div class="border rounded-lg p-4 space-y-3">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="item_${item.id}" name="items[]" value="${item.id}" class="mr-3 w-4 h-4">
                                            <label for="item_${item.id}" class="flex-1">
                                                <p class="font-medium">${name}</p>
                                                <p class="text-sm text-gray-600">${details}</p>
                                            </label>
                                        </div>
                                        <div class="ml-7 space-y-2">
                                            <div>
                                                <label class="text-xs text-gray-600 mb-1 block">Kondisi Barang:</label>
                                                <select id="status_${item.id}" class="w-full px-3 py-2 border rounded">
                                                    <option value="">-- Pilih Status --</option>
                                                    <option value="1">✓ Baik</option>
                                                    <option value="0">✗ Rusak</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-600 mb-1 block">Catatan (opsional):</label>
                                                <textarea id="detail_${item.id}" class="w-full px-3 py-2 border rounded" rows="2" placeholder="Contoh: Ada goresan kecil di bagian belakang"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('return-modal')" class="px-4 py-2 border rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button>
                        </div>
                    </form>
                `;
                
                document.getElementById('return-content').innerHTML = html;
                document.getElementById('return-modal').classList.remove('hidden');
                Swal.close();
                
                document.getElementById('returnForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const selectedItems = [];
                    document.querySelectorAll('input[name="items[]"]:checked').forEach(input => {
                        const itemId = input.value;
                        const status = document.getElementById(`status_${itemId}`).value;
                        const detail = document.getElementById(`detail_${itemId}`).value;
                        
                        selectedItems.push({
                            booking_item_id: itemId,
                            returned_status: status || undefined,
                            returned_detail: detail || undefined
                        });
                    });
                    
                    if(selectedItems.length === 0) {
                        showToast('Error', 'Pilih minimal 1 barang', 'error');
                        return;
                    }

                    // --- VALIDASI DIPERBAIKI ---
                    let hasError = false;
                    const globalStatus = document.getElementById('globalStatus').value;

                    for(const item of selectedItems) {
                        // Jika status item kosong DAN status global juga kosong -> Error
                        if (!item.returned_status && !globalStatus) {
                            hasError = true;
                            break; // Stop loop jika ketemu error
                        }
                    }

                    if (hasError) {
                        showToast('Error', 'Pilih status untuk setiap item', 'error');
                        return; // Stop submit!
                    }
                    // --------------------------
                    
                    const data = {
                        items: selectedItems,
                        global_returned_status: document.getElementById('globalStatus').value || undefined,
                        global_returned_detail: document.getElementById('globalDetail').value || undefined
                    };
                    
                    fetch(`/admin/bookings/${bookingId}/return`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(response => {
                        if(response.success) {
                            closeModal('return-modal');
                            showToast('Berhasil', response.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast('Error', response.message, 'error');
                        }
                    })
                    .catch(() => showToast('Error', 'Terjadi kesalahan', 'error'));
                });
            }
        })
        .catch(() => showToast('Error', 'Gagal memuat data', 'error'));
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function formatDate(dateString) {
    if(!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>
@endsection
