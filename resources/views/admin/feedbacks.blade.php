@extends('layouts.admin')

@section('title', 'Manajemen Keluhan & Saran')

@section('style')
    <style>
        /* --- DATATABLES CUSTOM STYLING (MATCHING SOFTWARES) --- */
        .dataTables_wrapper .dataTables_filter {
            display: none;
        }

        .dataTables_wrapper .dataTables_length select {
            padding-right: 2rem;
            padding-left: 0.75rem;
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            border-color: #d1d5db;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 0.875rem;
            color: #6b7280;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding-top: 0.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.75rem;
            margin-left: 0;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            background-color: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled) {
            background-color: #f9fafb;
            color: #4f46e5;
            border-color: #c7d2fe;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            z-index: 10;
            background-color: #4f46e5 !important;
            color: white !important;
            border-color: #4f46e5 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f9fafb;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #e5e7eb !important;
        }

        .ts-control {
            border-radius: 0.5rem;
            padding: 10px 12px;
            border-color: #d1d5db;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            z-index: 10;
            position: relative;
        }

        .ts-dropdown {
            z-index: 3005;
        }

        .feedback-modal-backdrop {
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(3px);
        }

    </style>
@endsection

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Daftar Keluhan & Saran</h1>
    </div>

    {{-- Filter Form --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter & Pencarian</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-select">
                <div>
                    <label for="filter_status" class="block text-sm font-semibold text-gray-700 mb-2">Status Tiket</label>
                    <select id="filter_status" class="filter-input" placeholder="Semua Status...">
                        <option value="">Semua Status</option>
                        <option value="1">Open</option>
                        <option value="2">In Progress</option>
                        <option value="3">Resolved</option>
                        <option value="4">Closed</option>
                        <option value="5">Rejected</option>
                    </select>
                </div>

                <div>
                    <label for="filter_type" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Laporan</label>
                    <select id="filter_type" class="filter-input" placeholder="Semua Jenis...">
                        <option value="">Semua Jenis</option>
                        <option value="KELUHAN">KELUHAN</option>
                        <option value="SARAN">SARAN</option>
                    </select>
                </div>

                <div>
                    <label for="filter_lab" class="block text-sm font-semibold text-gray-700 mb-2">Laboratorium</label>
                    <select id="filter_lab" class="filter-input" placeholder="Semua Lab...">
                        <option value="">Semua Laboratorium</option>
                        <option value="umum">Umum (Tanpa Lab Spesifik)</option>
                        @foreach ($labs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                <div class="relative flex w-full">
                    <input id="datatable-search-input" type="search"
                        class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                        placeholder="Cari subjek, pesan, pelapor, atau nomor tiket..." />
                    <button class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <button id="reset-filter-btn" class="px-6 py-2 bg-gray-200 w-full sm:w-auto text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- Tabel Utama --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Tiket Keluhan & Saran</h2>
            </div>

            <div class="p-6">
                <div id="feedbacks-datatable-wrapper" class="w-full">
                    <table id="feedbacks-table" class="w-full text-sm pb-3 text-left text-gray-500 display responsive" style="width:100%">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tiket & Tipe</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Subjek & Pesan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Pelapor</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Lab Terkait</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($feedbacks as $fb)
                                @php
                                    $statusClasses = [
                                        1 => 'bg-blue-100 text-blue-800',
                                        2 => 'bg-amber-100 text-amber-800',
                                        3 => 'bg-emerald-100 text-emerald-800',
                                        4 => 'bg-gray-100 text-gray-800',
                                        5 => 'bg-red-100 text-red-800',
                                    ];
                                    $statusClass = $statusClasses[$fb->status] ?? 'bg-gray-100 text-gray-800';
                                    $searchText = strtolower($fb->ticket_number . ' ' . $fb->subject . ' ' . $fb->description . ' ' . ($fb->reporter->name ?? '') . ' ' . ($fb->lab->name ?? 'umum'));
                                @endphp
                                <tr class="feedback-row hover:bg-gray-50 transition-colors"
                                    data-status="{{ $fb->status }}"
                                    data-type="{{ $fb->type }}"
                                    data-lab="{{ $fb->lab_id ?? 'umum' }}"
                                    data-search="{{ $searchText }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-mono font-bold text-gray-900 text-xs">{{ $fb->ticket_number }}</div>
                                        <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold rounded {{ $fb->type === 'KELUHAN' ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ $fb->type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs" data-order="{{ $fb->created_at ? $fb->created_at->timestamp : 0 }}">
                                        <div class="font-bold text-gray-900 truncate" title="{{ $fb->subject }}">{{ $fb->subject }}</div>
                                        <div class="text-xs text-gray-500 truncate mt-0.5" title="{{ $fb->description }}">{{ $fb->description }}</div>
                                        <div class="text-[11px] text-gray-400 mt-1">
                                            <i class="fa-regular fa-clock mr-1"></i>{{ $fb->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900 text-xs">{{ $fb->reporter->name ?? 'User' }}</div>
                                        <div class="text-[11px] text-gray-500 font-mono">{{ $fb->reporter->email ?? '-' }}</div>
                                        <span class="inline-block px-1.5 py-0.2 rounded text-[10px] bg-slate-100 text-slate-700 mt-0.5">
                                            {{ $fb->reporter->unit->name ?? 'Mahasiswa' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        @if ($fb->lab)
                                            <span class="font-medium text-indigo-700">{{ $fb->lab->name }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Umum</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $statusClass }}">
                                            {{ $fb->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button type="button" onclick="openAdminFeedbackModal('{{ $fb->id }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors">
                                            <i class="fa-solid fa-comments"></i>
                                            <span>Tanggapi</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail & Tanggapi Feedback --}}
    <div id="admin-feedback-modal" class="hidden fixed inset-0 z-[3000] flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="admin-feedback-modal-title" aria-hidden="true">
        <div class="feedback-modal-backdrop absolute inset-0" onclick="closeAdminFeedbackModal()"></div>
        <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden border border-gray-200 flex flex-col max-h-[90vh]">
            {{-- Modal Header --}}
            <div class="p-5 border-b border-gray-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2.5">
                    <span id="modal-af-ticket" class="text-xs font-mono font-bold px-2.5 py-1 rounded bg-gray-200 text-gray-800"></span>
                    <span id="modal-af-type" class="text-xs font-bold px-2.5 py-1 rounded"></span>
                    <span id="modal-af-status-badge" class="text-xs font-bold px-2.5 py-1 rounded-full"></span>
                </div>
                    <h2 id="admin-feedback-modal-title" class="sr-only">Detail feedback</h2>
                    <button type="button" onclick="closeAdminFeedbackModal()" aria-label="Tutup detail feedback" class="text-gray-400 hover:text-gray-900 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            {{-- Modal Body (Scrollable) --}}
            <div class="p-6 space-y-6 overflow-y-auto flex-1 overscroll-contain">
                {{-- Data Pelapor & Laporan --}}
                <div class="bg-slate-50 p-4 rounded-xl border border-gray-200 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs border-b border-gray-200/60 pb-2">
                        <div>
                            <span class="text-gray-500">Pelapor:</span>
                            <strong id="modal-af-reporter-name" class="text-gray-900 ml-1"></strong>
                            <span id="modal-af-reporter-email" class="text-gray-500 font-mono ml-1"></span>
                            <span id="modal-af-reporter-unit" class="ml-1 px-1.5 py-0.5 rounded bg-gray-200 text-gray-700 text-[10px]"></span>
                        </div>
                        <div class="text-gray-500">
                            <i class="fa-regular fa-calendar mr-1"></i><span id="modal-af-date"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 block mb-1">Laboratorium Terkait:</span>
                        <span id="modal-af-lab" class="text-sm font-semibold text-gray-800"></span>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 block mb-1">Subjek:</span>
                        <h4 id="modal-af-subject" class="text-base font-bold text-gray-900"></h4>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 block mb-1">Uraian Pesan / Pengaduan:</span>
                        <p id="modal-af-desc" class="text-sm text-gray-800 whitespace-pre-line leading-relaxed bg-white p-3 rounded-lg border border-gray-200"></p>
                    </div>
                </div>

                {{-- Riwayat Tanggapan --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i> Riwayat Tanggapan:
                    </h4>
                    <div id="modal-af-responses-container" class="space-y-3"></div>
                </div>

                {{-- Form Kirim Tanggapan & Perubahan Status --}}
                <form id="admin-response-form" class="bg-indigo-50/40 p-4 rounded-xl border border-indigo-100 space-y-4">
                    <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-reply text-indigo-600"></i> Berikan Tanggapan / Tindak Lanjut:
                    </h4>

                    <div>
                        <label for="response-message" class="block text-xs font-semibold text-gray-700 mb-1">Pesan Balasan ke Mahasiswa <span class="text-red-500">*</span></label>
                        <textarea id="response-message" required rows="3" maxlength="3000"
                                  placeholder="Tuliskan respon atau solusi atas keluhan/saran ini..."
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label for="response-status" class="block text-xs font-semibold text-gray-700 mb-1">Perbarui Status Tiket</label>
                            <select id="response-status" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="2">In Progress (Sedang Ditangani)</option>
                                <option value="3">Resolved (Selesai Ditindaklanjuti)</option>
                                <option value="4">Closed (Ditutup)</option>
                                <option value="5">Rejected (Ditolak)</option>
                            </select>
                        </div>

                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" id="btn-submit-response" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Kirim Tanggapan</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="px-6 py-3 border-t border-gray-200 bg-gray-50 flex justify-end">
                <button type="button" onclick="closeAdminFeedbackModal()" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    let table;
    let tomSelects = {};
    let currentFeedbackId = null;

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('feedbacks')?.classList.add('bg-slate-100', 'active');

        // 1. Init Filter TomSelect
        tomSelects.status = new TomSelect('#filter_status', { plugins: ['clear_button'] });
        tomSelects.type = new TomSelect('#filter_type', { plugins: ['clear_button'] });
        tomSelects.lab = new TomSelect('#filter_lab', { plugins: ['clear_button'] });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !document.getElementById('admin-feedback-modal').classList.contains('hidden')) {
                closeAdminFeedbackModal();
            }
        });

        // 2. Init jQuery DataTables (Matching Softwares)
        table = $('#feedbacks-table').DataTable({
            responsive: true,
            autoWidth: false,
            dom: 't<"flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-6 border-t border-gray-100"<"flex items-center gap-4"li>p>',
            pageLength: 10,
            order: [[1, 'desc']],
            lengthMenu: [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, "All"]
            ],
            language: {
                lengthMenu: "Show _MENU_",
                emptyTable: "Belum ada keluhan atau saran yang masuk.",
                zeroRecords: "Tidak ada data keluhan & saran yang cocok.",
                paginate: {
                    previous: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>',
                    next: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>'
                }
            },
            columnDefs: [{
                orderable: false,
                targets: 5
            }],
        });

        // 3. Search Input
        $('#datatable-search-input').on('keyup input', function () {
            table.search(this.value).draw();
        });

        // 4. Custom Filter
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if (settings.nTable.id !== 'feedbacks-table') return true;

            const rowNode = table.row(dataIndex).node();
            if (!rowNode) return true;

            const status = tomSelects.status ? tomSelects.status.getValue() : '';
            const type = tomSelects.type ? tomSelects.type.getValue() : '';
            const lab = tomSelects.lab ? tomSelects.lab.getValue() : '';

            const rowStatus = rowNode.dataset.status;
            const rowType = rowNode.dataset.type;
            const rowLab = rowNode.dataset.lab;

            if (status && rowStatus !== status) return false;
            if (type && rowType !== type) return false;
            if (lab && rowLab !== lab) return false;

            return true;
        });

        function applyFilter() {
            table.draw();
        }

        tomSelects.status.on('change', applyFilter);
        tomSelects.type.on('change', applyFilter);
        tomSelects.lab.on('change', applyFilter);

        document.getElementById('reset-filter-btn').addEventListener('click', () => {
            [tomSelects.status, tomSelects.type, tomSelects.lab].forEach(ts => ts?.clear());
            const searchInput = document.getElementById('datatable-search-input');
            if (searchInput) searchInput.value = '';
            table.search('').draw();
        });

        // Submit tanggapan feedback
        document.getElementById('admin-response-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!currentFeedbackId) return;

            const message = document.getElementById('response-message').value.trim();
            const statusChange = document.getElementById('response-status').value;

            if (!message) {
                Swal.fire('Perhatian', 'Pesan tanggapan tidak boleh kosong.', 'warning');
                return;
            }

            try {
                showLoading('Mengirim tanggapan...');
                // 1. Kirim respon
                const res = await fetch(`/admin/feedbacks/${currentFeedbackId}/respond`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        status_change: statusChange
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Gagal mengirim tanggapan');

                Swal.fire('Berhasil!', 'Tanggapan telah terkirim ke mahasiswa.', 'success')
                    .then(() => location.reload());
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        });
    });

    async function openAdminFeedbackModal(feedbackId) {
        currentFeedbackId = feedbackId;
        const modal = document.getElementById('admin-feedback-modal');

        try {
            showLoading('Memuat detail...');
            const res = await fetch(`/admin/feedbacks/${feedbackId}/details`);
            const data = await res.json();
            Swal.close();

            if (!data.success) throw new Error(data.message || 'Gagal memuat detail');

            const fb = data.feedback;
            document.getElementById('modal-af-ticket').textContent = fb.ticket_number;
            const typeBadge = document.getElementById('modal-af-type');
            typeBadge.textContent = fb.type;
            typeBadge.className = `text-xs font-bold px-2.5 py-1 rounded ${fb.type === 'KELUHAN' ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800'}`;

            const statusMap = {
                1: { label: 'Open', class: 'bg-blue-100 text-blue-800' },
                2: { label: 'In Progress', class: 'bg-amber-100 text-amber-800' },
                3: { label: 'Resolved', class: 'bg-emerald-100 text-emerald-800' },
                4: { label: 'Closed', class: 'bg-gray-100 text-gray-800' },
                5: { label: 'Rejected', class: 'bg-red-100 text-red-800' },
            };
            const currStatus = statusMap[fb.status] || { label: 'Unknown', class: 'bg-gray-100 text-gray-800' };
            const statusBadge = document.getElementById('modal-af-status-badge');
            statusBadge.textContent = currStatus.label;
            statusBadge.className = `text-xs font-bold px-2.5 py-1 rounded-full ${currStatus.class}`;

            document.getElementById('modal-af-reporter-name').textContent = fb.reporter?.name || 'User';
            document.getElementById('modal-af-reporter-email').textContent = `(${fb.reporter?.email || '-'})`;
            document.getElementById('modal-af-reporter-unit').textContent = fb.reporter?.unit?.name || 'Mahasiswa';
            document.getElementById('modal-af-date').textContent = new Date(fb.created_at).toLocaleString('id-ID');
            document.getElementById('modal-af-lab').textContent = fb.lab ? fb.lab.name : 'Umum / Seluruh Lab Gedung P';
            document.getElementById('modal-af-subject').textContent = fb.subject;
            document.getElementById('modal-af-desc').textContent = fb.description;

            // Set default status di form
            document.getElementById('response-status').value = fb.status === 1 ? '2' : String(fb.status);
            document.getElementById('response-message').value = '';

            // Render responses
            const respContainer = document.getElementById('modal-af-responses-container');
            if (!fb.responses || fb.responses.length === 0) {
                respContainer.innerHTML = '<div class="p-3 bg-gray-50 border border-dashed rounded-lg text-xs text-gray-400 text-center">Belum ada tanggapan untuk tiket ini.</div>';
            } else {
                respContainer.innerHTML = fb.responses.map(r => `
                    <div class="p-3.5 rounded-xl bg-white border border-gray-200 shadow-2xs space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-gray-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-check text-indigo-600"></i> ${r.responder?.name || 'Staf Lab'}
                            </span>
                            <span class="text-gray-400 font-mono">${new Date(r.created_at).toLocaleString('id-ID')}</span>
                        </div>
                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">${r.message}</p>
                    </div>
                `).join('');
            }

            modal.dataset.previousOverflow = document.body.style.overflow;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            document.querySelector('#admin-feedback-modal button[aria-label="Tutup detail feedback"]')?.focus();
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    }

    function closeAdminFeedbackModal() {
        const modal = document.getElementById('admin-feedback-modal');
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = modal.dataset.previousOverflow || '';
        currentFeedbackId = null;
    }
</script>
@endsection
