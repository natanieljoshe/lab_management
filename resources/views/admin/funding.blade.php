@extends('layouts.admin')

@section('title', 'Kas & Keuangan Lab')

@section('body')
    {{-- Header Banner (Serasi dengan Items, Repairs, Bookings) --}}
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-between mb-5 px-6 md:px-8 bg-white">
        <h1 class="text-center md:text-left text-3xl md:text-4xl uppercase font-bold text-gray-800">Kas & Keuangan Lab</h1>

        <div class="flex items-center gap-3 mt-3 md:mt-0">
            <a href="{{ route('admin.funding.export') }}" 
               class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
            <button type="button" onclick="openAddModal()" 
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Transaksi
            </button>
        </div>
    </div>

    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto px-6 pb-8 space-y-6">

        {{-- Ringkasan Saldo Kas (Cards) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-gray-500">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $total_income_formatted }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-gray-500">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-rose-600 mt-1">{{ $total_expense_formatted }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wider font-semibold text-gray-500">Sisa Saldo Kas</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $balance_formatted }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Filter & Pencarian (Serasi dengan Items & Repairs) --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 space-y-5">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Filter & Pencarian</h2>

            {{-- Full-width Search Bar --}}
            <div>
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="filter-search" oninput="applyFilters()" 
                           placeholder="Cari transaksi berdasarkan keterangan, nomor nota, nama lab, kategori..." 
                           class="w-full pl-12 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:outline-none transition">
                </div>
            </div>

            {{-- Filter Grid (Tanggal Awal/Akhir, Tipe, Kategori, Lab) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 pt-1">
                {{-- Tanggal Awal --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" id="filter-start-date" onchange="applyFilters()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                {{-- Tanggal Akhir --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" id="filter-end-date" onchange="applyFilters()" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>

                {{-- Tipe Transaksi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tipe Transaksi</label>
                    <select id="filter-type" onchange="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="INCOME">Pemasukan</option>
                        <option value="EXPENSE">Pengeluaran</option>
                    </select>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select id="filter-category" onchange="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Laboratorium & Reset --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-700">Lab</label>
                        <button type="button" onclick="resetFilters()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold underline">Reset</button>
                    </div>
                    <select id="filter-lab" onchange="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <option value="">Semua Lab</option>
                        @foreach ($labs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Tabel Transaksi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs uppercase font-semibold text-gray-700">
                        <tr>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Tipe</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Lab</th>
                            <th class="px-5 py-3.5">Keterangan</th>
                            <th class="px-5 py-3.5">Nominal</th>
                            <th class="px-5 py-3.5 text-center">Nota</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-tbody" class="divide-y divide-gray-100">
                        @forelse ($transactions as $t)
                            @php
                                $itemType = $t->category->type ?? $t->type;
                            @endphp
                            <tr class="hover:bg-gray-50 transition transaction-row"
                                data-type="{{ $itemType }}"
                                data-category="{{ $t->category_id }}"
                                data-lab="{{ $t->lab_id }}"
                                data-date="{{ $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d') : '' }}"
                                data-search="{{ strtolower(($t->description ?? '') . ' ' . ($t->receipt_number ?? '') . ' ' . ($t->category->name ?? '') . ' ' . ($t->lab->name ?? '')) }}">
                                
                                {{-- Tanggal --}}
                                <td class="px-5 py-3.5 whitespace-nowrap text-gray-700">
                                    {{ $t->transaction_date ? \Carbon\Carbon::parse($t->transaction_date)->format('d M Y') : '-' }}
                                </td>

                                {{-- Tipe --}}
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if ($itemType === 'INCOME')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>

                                {{-- Kategori --}}
                                <td class="px-5 py-3.5 font-medium text-gray-800">
                                    {{ $t->category ? $t->category->name : '-' }}
                                </td>

                                {{-- Lab --}}
                                <td class="px-5 py-3.5 text-gray-600 font-medium">
                                    {{ $t->lab ? $t->lab->name : '-' }}
                                </td>

                                {{-- Keterangan --}}
                                <td class="px-5 py-3.5 max-w-xs">
                                    <p class="text-gray-800 truncate" title="{{ $t->description }}">{{ $t->description }}</p>
                                </td>

                                {{-- Nominal --}}
                                <td class="px-5 py-3.5 whitespace-nowrap font-bold {{ $itemType === 'INCOME' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $itemType === 'INCOME' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                                </td>

                                {{-- Bukti Nota & No Nota (Rapi & Di Tengah) --}}
                                <td class="px-5 py-3.5 whitespace-nowrap text-center text-xs">
                                    <div class="flex flex-col items-center justify-center gap-0.5">
                                        @if ($t->receipt_file)
                                            <a href="{{ route('admin.funding.receipt', $t) }}" target="_blank" 
                                               class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800 transition">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Lihat Nota
                                            </a>
                                        @endif
                                        @if ($t->receipt_number)
                                            <span class="text-xs text-gray-500 font-mono">
                                                {{ $t->receipt_number }}
                                            </span>
                                        @elseif (!$t->receipt_file)
                                            <span class="text-gray-400 font-semibold">-</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" 
                                                data-transaction='@json($t)'
                                                onclick="openEditModal(JSON.parse(this.dataset.transaction))"
                                                class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold px-2.5 py-1 rounded hover:bg-indigo-50 transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.funding.destroy', $t) }}" method="POST" onsubmit="return confirmDelete(event, this);" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold px-2.5 py-1 rounded hover:bg-rose-50 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                    Belum ada transaksi kas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="no-filter-match" class="hidden">
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                                Tidak ada data transaksi yang cocok dengan filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH TRANSAKSI                                    --}}
    {{-- ========================================================= --}}
    <div id="addModal" class="fixed inset-0 z-[3000] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" onclick="closeAddModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 z-10 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Catat Transaksi Kas Baru</h3>
                    <button type="button" onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form id="addForm" action="{{ route('admin.funding.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return showSubmitLoading(this);">
                    @csrf

                    {{-- Tipe Transaksi (Filter Kategori) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Transaksi <span class="text-red-500">*</span></label>
                        <select id="add_type" required onchange="filterCategoryByType('add')"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="" selected disabled>-- Pilih Tipe --</option>
                            <option value="INCOME">Pemasukan (Income)</option>
                            <option value="EXPENSE">Pengeluaran (Expense)</option>
                        </select>
                    </div>

                    {{-- Kategori (Tanpa label [INCOME] / [EXPENSE]) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" id="add_category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="" selected disabled>-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periode & Lab (Laboratorium Required) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Periode <span class="text-red-500">*</span></label>
                            <select name="period_id" id="add_period_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="" selected disabled>-- Pilih Periode --</option>
                                @foreach ($periods as $p)
                                    <option value="{{ $p->id }}">
                                        {{ $p->academic_year }} ({{ $p->semester }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Laboratorium <span class="text-red-500">*</span></label>
                            <select name="lab_id" id="add_lab_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="" selected disabled>Pilih Lab</option>
                                @foreach ($labs as $l)
                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nominal & Tanggal Transaksi (Maksimal 1 Milyar) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" min="1" max="1000000000" step="any" required placeholder="Contoh: 500000"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    {{-- No Nota & File Nota (Maksimal 100 Karakter) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Nota (Opsional)</label>
                            <input type="text" name="receipt_number" id="add_receipt_number" maxlength="100" oninput="validateNoSymbols(this, 'Nomor nota'); updateCharCount(this, 'add_receipt_count', 100);" placeholder="Contoh: NOTA 001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-[11px] text-gray-500">Maks. 100 karakter</span>
                                <span id="add_receipt_count" class="text-[11px] text-gray-400">0/100 karakter</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">File Nota (Opsional)</label>
                            <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf"
                                   class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                    </div>

                    {{-- Keterangan (Maksimal 300 Karakter) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Transaksi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="add_description" rows="3" maxlength="300" required oninput="validateNoSymbols(this, 'Keterangan transaksi'); updateCharCount(this, 'add_desc_count', 300);" placeholder="Tuliskan detail keterangan transaksi..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-[11px] text-gray-500">Maks. 300 karakter</span>
                            <span id="add_desc_count" class="text-[11px] text-gray-400">0/300 karakter</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" onclick="closeAddModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL EDIT TRANSAKSI                                      --}}
    {{-- ========================================================= --}}
    <div id="editModal" class="fixed inset-0 z-[3000] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm" onclick="closeEditModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 z-10 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Edit Transaksi Kas</h3>
                    <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="return showSubmitLoading(this);">
                    @csrf
                    @method('PUT')

                    {{-- Tipe Transaksi (Filter Kategori) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipe Transaksi <span class="text-red-500">*</span></label>
                        <select id="edit_type" required onchange="filterCategoryByType('edit')"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="INCOME">Pemasukan (Income)</option>
                            <option value="EXPENSE">Pengeluaran (Expense)</option>
                        </select>
                    </div>

                    {{-- Kategori (Tanpa label [INCOME] / [EXPENSE]) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" id="edit_category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <option value="" disabled>-- Pilih Kategori --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" data-type="{{ $cat->type }}">
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periode & Lab (Laboratorium Required) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Periode <span class="text-red-500">*</span></label>
                            <select name="period_id" id="edit_period_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="" disabled>-- Pilih Periode --</option>
                                @foreach ($periods as $p)
                                    <option value="{{ $p->id }}">{{ $p->academic_year }} ({{ $p->semester }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Laboratorium <span class="text-red-500">*</span></label>
                            <select name="lab_id" id="edit_lab_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                                <option value="" disabled>Pilih Lab</option>
                                @foreach ($labs as $l)
                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Nominal & Tanggal Transaksi (Maksimal 1 Milyar) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" id="edit_amount" min="1" max="1000000000" step="any" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                            <input type="date" name="transaction_date" id="edit_transaction_date" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>

                    {{-- No Nota & File Nota (Maksimal 100 Karakter) --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Nota (Opsional)</label>
                            <input type="text" name="receipt_number" id="edit_receipt_number" maxlength="100" oninput="validateNoSymbols(this, 'Nomor nota'); updateCharCount(this, 'edit_receipt_count', 100);" placeholder="Contoh: NOTA 001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-[11px] text-gray-500">Maks. 100 karakter</span>
                                <span id="edit_receipt_count" class="text-[11px] text-gray-400">0/100 karakter</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Ganti File Nota (Opsional)</label>
                            <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf"
                                   class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded file:border-0 file:text-xs file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                        </div>
                    </div>

                    {{-- Keterangan (Maksimal 300 Karakter) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Transaksi <span class="text-red-500">*</span></label>
                        <textarea name="description" id="edit_description" rows="3" maxlength="300" required oninput="validateNoSymbols(this, 'Keterangan transaksi'); updateCharCount(this, 'edit_desc_count', 300);" placeholder="Tuliskan detail keterangan transaksi..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-[11px] text-gray-500">Maks. 300 karakter</span>
                            <span id="edit_desc_count" class="text-[11px] text-gray-400">0/300 karakter</span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                            Perbarui Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

{{-- ========================================================= --}}
{{-- JAVASCRIPT LOGIC                                          --}}
{{-- ========================================================= --}}
<script>
    // -------------------------------------------------------------
    // CHARACTER COUNT HELPER
    // -------------------------------------------------------------
    function updateCharCount(inputEl, counterId, maxChars) {
        if (!inputEl) return;
        const count = inputEl.value ? inputEl.value.length : 0;
        const counterEl = document.getElementById(counterId);
        if (counterEl) {
            counterEl.textContent = `${count}/${maxChars} karakter`;
            if (count > maxChars) {
                counterEl.classList.remove('text-gray-400');
                counterEl.classList.add('text-rose-600', 'font-semibold');
            } else {
                counterEl.classList.remove('text-rose-600', 'font-semibold');
                counterEl.classList.add('text-gray-400');
            }
        }
    }

    // -------------------------------------------------------------
    // VALIDASI SIMBOL (HANYA HURUF DAN ANGKA)
    // -------------------------------------------------------------
    function validateNoSymbols(el, fieldLabel) {
        if (!el) return true;
        const regex = /^[a-zA-Z0-9\s]*$/;
        if (el.value && !regex.test(el.value)) {
            el.setCustomValidity(`${fieldLabel} tidak boleh ada simbol, hanya boleh angka dan huruf.`);
            return false;
        } else {
            el.setCustomValidity('');
            return true;
        }
    }

    // -------------------------------------------------------------
    // 1. MODAL CONTROLS
    // -------------------------------------------------------------
    function openAddModal() {
        const form = document.getElementById('addForm');
        if (form) form.reset();

        document.getElementById('add_type').value = '';
        document.getElementById('add_category_id').value = '';
        document.getElementById('add_period_id').value = '';
        document.getElementById('add_lab_id').value = '';

        const descInput = document.getElementById('add_description');
        const receiptInput = document.getElementById('add_receipt_number');
        if (descInput) descInput.setCustomValidity('');
        if (receiptInput) receiptInput.setCustomValidity('');

        updateCharCount(descInput, 'add_desc_count', 300);
        updateCharCount(receiptInput, 'add_receipt_count', 100);

        filterCategoryByType('add');
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    function openEditModal(transaction) {
        const form = document.getElementById('editForm');
        form.action = `/admin/funding/${transaction.id}/edit`;

        // Ambil tipe transaksi dari relasi kategori atau accessor
        const catType = transaction.category ? transaction.category.type : (transaction.type || '');
        document.getElementById('edit_type').value = catType;
        document.getElementById('edit_period_id').value = transaction.period_id;
        document.getElementById('edit_lab_id').value = transaction.lab_id || '';
        document.getElementById('edit_amount').value = parseFloat(transaction.amount) || '';
        
        let tDate = transaction.transaction_date;
        if (tDate && tDate.length > 10) {
            tDate = tDate.substring(0, 10);
        }
        document.getElementById('edit_transaction_date').value = tDate || '';
        
        const receiptInput = document.getElementById('edit_receipt_number');
        const descInput = document.getElementById('edit_description');
        receiptInput.value = transaction.receipt_number || '';
        descInput.value = transaction.description || '';
        receiptInput.setCustomValidity('');
        descInput.setCustomValidity('');

        updateCharCount(descInput, 'edit_desc_count', 300);
        updateCharCount(receiptInput, 'edit_receipt_count', 100);

        // Filter kategori sesuai tipe dan pasang nilainya
        filterCategoryByType('edit');
        document.getElementById('edit_category_id').value = transaction.category_id;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // -------------------------------------------------------------
    // 2. FILTER KATEGORI SESUAI TIPE (INCOME / EXPENSE) DI MODAL
    // -------------------------------------------------------------
    function filterCategoryByType(mode) {
        const typeSelect = document.getElementById(mode === 'add' ? 'add_type' : 'edit_type');
        const catSelect = document.getElementById(mode === 'add' ? 'add_category_id' : 'edit_category_id');
        const selectedType = typeSelect.value;

        Array.from(catSelect.options).forEach(opt => {
            if (!opt.value) return; // Opsi placeholder tetap ada
            const catType = opt.getAttribute('data-type');
            if (!selectedType || catType === selectedType) {
                opt.style.display = '';
                opt.hidden = false;
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.hidden = true;
                opt.disabled = true;
            }
        });

        const currentOpt = catSelect.options[catSelect.selectedIndex];
        if (currentOpt && currentOpt.value && currentOpt.getAttribute('data-type') !== selectedType) {
            catSelect.value = '';
        }
    }

    // -------------------------------------------------------------
    // 3. FILTER & SEARCH CLIENT-SIDE PADA TABEL
    // -------------------------------------------------------------
    function applyFilters() {
        const searchVal = (document.getElementById('filter-search').value || '').toLowerCase().trim();
        const startDate = document.getElementById('filter-start-date').value;
        const endDate = document.getElementById('filter-end-date').value;
        const typeVal = document.getElementById('filter-type').value;
        const catVal = document.getElementById('filter-category').value;
        const labVal = document.getElementById('filter-lab').value;

        const rows = document.querySelectorAll('.transaction-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowType = row.getAttribute('data-type');
            const rowCat = row.getAttribute('data-category');
            const rowLab = row.getAttribute('data-lab');
            const rowDate = row.getAttribute('data-date') || '';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchSearch = !searchVal || rowSearch.includes(searchVal);
            const matchType = !typeVal || rowType === typeVal;
            const matchCat = !catVal || rowCat === catVal;
            const matchLab = !labVal || rowLab === labVal;
            const matchStartDate = !startDate || (rowDate && rowDate >= startDate);
            const matchEndDate = !endDate || (rowDate && rowDate <= endDate);

            if (matchSearch && matchType && matchCat && matchLab && matchStartDate && matchEndDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noMatchEl = document.getElementById('no-filter-match');
        if (noMatchEl) {
            if (visibleCount === 0 && rows.length > 0) {
                noMatchEl.classList.remove('hidden');
            } else {
                noMatchEl.classList.add('hidden');
            }
        }
    }

    function resetFilters() {
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-start-date').value = '';
        document.getElementById('filter-end-date').value = '';
        document.getElementById('filter-type').value = '';
        document.getElementById('filter-category').value = '';
        document.getElementById('filter-lab').value = '';
        applyFilters();
    }

    // -------------------------------------------------------------
    // 4. SUBMIT LOADING & CONFIRM DELETE
    // -------------------------------------------------------------
    function showSubmitLoading(form) {
        const receiptInput = form.querySelector('[name="receipt_number"]');
        const descInput = form.querySelector('[name="description"]');
        
        if (receiptInput) validateNoSymbols(receiptInput, 'Nomor nota');
        if (descInput) validateNoSymbols(descInput, 'Keterangan transaksi');

        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }

        Swal.fire({
            title: 'Sedang Menyimpan...',
            text: 'Mohon tunggu, data transaksi sedang diproses.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        return true;
    }

    function confirmDelete(event, form) {
        event.preventDefault();
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: 'Apakah Anda yakin ingin menghapus transaksi kas ini? Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang Menghapus...',
                    text: 'Mohon tunggu sebentar.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
        return false;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const fundingNav = document.getElementById('funding');
        if (fundingNav) {
            fundingNav.classList.add('bg-slate-100');
        }

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                timer: 2500,
                showConfirmButton: false,
                timerProgressBar: true
            });
        @endif

        @if ($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: '<ul class="text-left text-sm space-y-1">@foreach ($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
                confirmButtonColor: '#4f46e5',
                confirmButtonText: 'Tutup'
            });
        @endif
    });
</script>
@endsection
