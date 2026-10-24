@extends('layouts.admin')

@section('title', 'Manajemen Perbaikan')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Daftar Perbaikan</h1>
    </div>

    {{-- ========================================================= --}}
    {{-- Filter Form --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter & Pencarian</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-select">
                {{-- Status Perbaikan --}}
                <div>
                    <label for="filter_repair_status" class="block text-sm font-semibold text-gray-700 mb-2">Status
                        Item</label>
                    <select id="filter_repair_status" class="filter-input" placeholder="Semua Status...">
                        <option value="">Semua Status</option>
                        <option value="1">In Progress</option>
                        <option value="2">Completed</option>
                    </select>
                </div>

                {{-- Lab --}}
                <div>
                    <label for="filter_lab_select"
                        class="block text-sm font-semibold text-gray-700 mb-2">Laboratorium</label>
                    <select id="filter_lab_select" class="filter-input" placeholder="Semua Lab...">
                        <option value="">Semua Laboratorium</option>
                        @foreach ($labs as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                        @endforeach
                        <option value="null">Belum Terpasang / Gudang</option>
                    </select>
                </div>

                {{-- Tipe --}}
                <div>
                    <label for="filter_type_select" class="block text-sm font-semibold text-gray-700 mb-2">Tipe
                        Barang</label>
                    <select id="filter_type_select" class="filter-input" placeholder="Semua Tipe...">
                        <option value="">Semua Tipe</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Spesifikasi Attribute --}}
                <div>
                    <label for="filter_spec_attr" class="block text-sm font-semibold text-gray-700 mb-2">Atribut
                        Spesifikasi</label>
                    <select id="filter_spec_attr" class="filter-input" placeholder="Pilih Atribut...">
                        <option value="">Semua Atribut</option>
                        @foreach ($specification as $spec)
                            <option value="{{ $spec->id }}" data-values='@json($spec->specValues)'>
                                {{ $spec->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Spesifikasi Value --}}
                <div>
                    <label for="filter_spec_val" class="block text-sm font-semibold text-gray-700 mb-2">Nilai
                        Spesifikasi</label>
                    <select id="filter_spec_val" class="filter-input" placeholder="Pilih Atribut Dulu..." disabled>
                        {{-- Diisi oleh JS --}}
                    </select>
                </div>

                {{-- Pelapor --}}
                <div>
                    <label for="filter_reporter" class="block text-sm font-semibold text-gray-700 mb-2">Pelapor</label>
                    <select id="filter_reporter" class="filter-input" placeholder="Semua Pelapor...">
                        <option value="">Semua Pelapor</option>
                        @foreach ($repairs->unique('reported_by') as $r)
                            @if ($r->reporter)
                                <option value="{{ $r->reporter->name }}">{{ $r->reporter->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tombol Aksi Filter --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                {{-- Search Text --}}
                <div class="relative flex w-full">
                    <div class="relative flex w-full flex-wrap items-stretch">
                        <input id="datatable-search-input" type="search"
                            class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                            placeholder="Cari nama barang, pelapor, atau masalah..." aria-label="Search" />
                        <button
                            class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg"
                            type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd"
                                    d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tombol Reset --}}
                <button id="reset-filter-btn" type="button"
                    class="px-6 py-2 bg-gray-200 w-full sm:w-auto text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
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
                <h2 class="text-xl font-semibold text-gray-800">Daftar Laporan Perbaikan</h2>
            </div>
            <div class="p-6">
                <div id="repairs-datatable-wrapper" class="overflow-x-auto">
                    <table id="repairs-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    data-te-datatable-sortable="true">
                                    Barang & Tipe</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    data-te-datatable-sortable="true">
                                    Lokasi / Lab</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keluhan & Catatan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pelapor & Tiket</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    data-te-datatable-sortable="true">
                                    Waktu</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($repairs as $repair)
                                {{-- Merge Items dan Components dalam satu loop --}}
                                @php
                                    // Tambahkan flag untuk membedakan
                                    $mergedItems = $repair->items
                                        ->map(function ($item) {
                                            $item->is_component = false;
                                            return $item;
                                        })
                                        ->concat(
                                            $repair->components->map(function ($comp) {
                                                $comp->is_component = true;
                                                return $comp;
                                            }),
                                        );
                                @endphp

                                @foreach ($mergedItems as $item)
                                    @php
                                        $pivot = $item->pivot;

                                        $itemName = $item->name ?? 'Unknown';
                                        $itemCode = $item->serial_code ?? '-';
                                        $itemTypeId = $item->type_id ?? '';
                                        $itemTypeName = $item->type->name ?? 'N/A';

                                        // Logic Lokasi
                                        $labId = 'null';
                                        $locationName = 'Gudang / Belum Terpasang';
                                        if (!$item->is_component) {
                                            if ($item->desk) {
                                                $labId = $item->desk->lab_id;
                                                $locationName =
                                                    $item->desk->lab->name . ' - Meja ' . $item->desk->location;
                                            }
                                        } else {
                                            if ($item->item && $item->item->desk) {
                                                $labId = $item->item->desk->lab_id;
                                                $locationName =
                                                    $item->item->desk->lab->name .
                                                    ' - Meja ' .
                                                    $item->item->desk->location;
                                            }
                                        }

                                        // Logic Spesifikasi Filter
                                        $specValueIds = [];
                                        if ($item->specSetValues) {
                                            $specValueIds = $item->specSetValues->pluck('id')->toArray();
                                        }
                                        $specJson = json_encode($specValueIds);

                                        // Badge Status (Dari Pivot)
                                        $statusLabel = '';
                                        $statusClass = '';
                                        switch ($pivot->status) {
                                            case 1:
                                                $statusLabel = 'In Progress';
                                                $statusClass = 'bg-blue-100 text-blue-800';
                                                break;
                                            case 2:
                                                $statusLabel = $pivot->is_successful
                                                    ? 'Completed (Sukses)'
                                                    : 'Completed (Gagal)';
                                                $statusClass = $pivot->is_successful
                                                    ? 'bg-green-100 text-green-800'
                                                    : 'bg-red-100 text-red-800';
                                                break;
                                        }
                                    @endphp

                                    <tr class="repair-row" data-repair-status="{{ $pivot->status }}"
                                        data-lab-id="{{ $labId }}" data-type-id="{{ $itemTypeId }}"
                                        data-specs="{{ $specJson }}"
                                        data-reporter="{{ $repair->reporter->name ?? '' }}"
                                        data-search-text="{{ strtolower($itemName . ' ' . $itemCode . ' ' . ($repair->reporter->name ?? '') . ' ' . $pivot->issue_description . ' ' . $repair->name) }}">

                                        {{-- Kolom 1: Barang --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $itemName }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $itemCode }}</div>
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mt-1">
                                                {{ $item->is_component ? 'Component' : 'Item' }}: {{ $itemTypeName }}
                                            </span>
                                        </td>

                                        {{-- Kolom 2: Lokasi --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-700">{{ $locationName }}</div>
                                        </td>

                                        {{-- Kolom 3: Keluhan & Notes (Dari Pivot) --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 font-extrabold mb-1">Keluhan:</div>
                                            <div class="text-xs text-gray-600 max-w-xs whitespace-normal break-words mb-2">
                                                {{ Str::limit($pivot->issue_description, 60) }}
                                            </div>
                                            @if ($pivot->repair_notes)
                                                <div class="text-xs text-gray-500">
                                                    <div class="text-sm text-gray-900 font-extrabold mb-1">Catatan:</div>
                                                    <div
                                                        class="text-xs text-gray-600 max-w-xs whitespace-normal break-words mb-2">
                                                        {{ Str::limit($pivot->repair_notes, 50) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Kolom 4: Pelapor & Tiket --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $repair->reporter->name ?? 'System' }}</div>
                                            <div class="text-xs text-gray-400 mt-1" title="{{ $repair->name }}">Tiket:
                                                {{ Str::limit($repair->name, 15) }}</div>
                                        </td>

                                        {{-- Kolom 5: Status (Dari Pivot) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>

                                        {{-- Kolom 6: Waktu (Dari Pivot) --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col gap-1">
                                                <div class="text-xs text-gray-600">
                                                    <span class="font-semibold text-gray-500">Lapor:</span>
                                                    {{ $repair->created_at ? $repair->created_at->format('d/m H:i') : '-' }}
                                                </div>
                                                @if ($pivot->started_at)
                                                    <div class="text-xs text-gray-600">
                                                        <span class="font-semibold text-gray-500">Mulai:</span>
                                                        {{ \Carbon\Carbon::parse($pivot->started_at)->format('d/m H:i') }}
                                                    </div>
                                                @endif
                                                @if ($pivot->completed_at)
                                                    <div class="text-xs text-gray-600">
                                                        <span class="font-semibold text-gray-500">Selesai:</span>
                                                        {{ \Carbon\Carbon::parse($pivot->completed_at)->format('d/m H:i') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Kolom 7: Aksi --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{-- Kirim ID Item (pivot owner) bukan ID Repair --}}
                                            <button type="button"
                                                class="btn-open-update-status px-3 py-1.5 bg-indigo-600 text-white hover:bg-indigo-700 text-xs font-semibold rounded-md shadow-sm focus:outline-none transition-colors"
                                                data-repair-id="{{ $repair->id }}"
                                                data-itemable-id="{{ $item->id }}"
                                                data-current-status="{{ $pivot->status }}"
                                                data-current-success="{{ $pivot->is_successful }}"
                                                data-current-notes="{{ $pivot->repair_notes }}"
                                                data-item-name="{{ $itemName }}"
                                                data-issue="{{ $pivot->issue_description }}">
                                                Update
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL: Update Status Perbaikan (Partial Update) --}}
    {{-- ========================================================= --}}
    <div id="update-status-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="update-status-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md flex flex-col">

                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Update Status Item</h3>
                    <button id="close-modal-btn" type="button" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto max-h-[80vh]">
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <p class="text-xs text-blue-800 font-semibold mb-1">Barang:</p>
                        <p id="modal-item-name" class="text-sm text-gray-700 font-bold">Nama Item</p>
                        <p class="text-xs text-blue-800 font-semibold mt-2 mb-1">Masalah:</p>
                        <p id="modal-issue" class="text-xs text-gray-600 italic">...</p>
                    </div>

                    <form id="update-status-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" id="modal-repair-id" name="repair_id">
                        <input type="hidden" id="modal-itemable-id" name="itemable_id">

                        {{-- Select Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Baru</label>
                            <select id="modal-status-select" name="status" class="w-full">
                                <option value="1">In Progress (Sedang Dikerjakan)</option>
                                <option value="2">Completed (Selesai)</option>
                            </select>
                        </div>

                        {{-- Field Khusus Completed --}}
                        <div id="completed-fields" class="hidden space-y-4 pt-2 border-t border-gray-100 mt-2">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil Perbaikan</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="is_successful" value="1"
                                            class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Berhasil (Baik)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="is_successful" value="0"
                                            class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Gagal (Rusak)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan (Selalu muncul) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Teknisi</label>
                            <textarea name="repair_notes" id="modal-repair-notes" rows="3"
                                class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500"
                                placeholder="Contoh: Kabel diganti baru..."></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" id="cancel-modal-btn"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
                            <button type="submit" id="submit-status-btn"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <style>
        .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .ts-dropdown {
            z-index: 3005;
        }
    </style>

    <script>
        let tomSelects = {};
        let tomSelectStatusModal;
        let dataTableInstance;

        function showLoading(title = 'Loading...', text = 'silakan tunggu...') {
            Swal.fire({
                title: title,
                text: text,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('repairs').classList.add('bg-slate-100');
            document.getElementById('repairs').classList.add('active');

            // --- 1. INIT FILTER ---
            tomSelects.status = new TomSelect('#filter_repair_status', {
                plugins: ['clear_button']
            });
            tomSelects.lab = new TomSelect('#filter_lab_select', {
                plugins: ['clear_button']
            });
            tomSelects.type = new TomSelect('#filter_type_select', {
                plugins: ['clear_button']
            });
            tomSelects.reporter = new TomSelect('#filter_reporter', {
                plugins: ['clear_button']
            });
            tomSelects.specVal = new TomSelect('#filter_spec_val', {
                plugins: ['clear_button']
            });
            tomSelects.specVal.disable();

            tomSelects.specAttr = new TomSelect('#filter_spec_attr', {
                plugins: ['clear_button'],
                onChange: (value) => {
                    if (!tomSelects.specVal) return;
                    tomSelects.specVal.clear();
                    tomSelects.specVal.clearOptions();
                    if (!value) {
                        tomSelects.specVal.disable();
                    } else {
                        const selectedOption = tomSelects.specAttr.getOption(value);
                        if (selectedOption && selectedOption.dataset.values) {
                            try {
                                const values = JSON.parse(selectedOption.dataset.values);
                                tomSelects.specVal.addOptions(values.map(v => ({
                                    value: v.id,
                                    text: v.value
                                })));
                                tomSelects.specVal.enable();
                            } catch (e) {
                                console.error(e);
                            }
                        }
                    }
                    applyClientSideFilter();
                }
            });

            // --- 2. INIT DATATABLE ---
            try {
                const tableEl = document.getElementById('repairs-table');
                dataTableInstance = new te.Datatable(tableEl, {
                    hover: true,
                    fixedHeader: true,
                    pagination: true,
                    entries: 10,
                    entriesOptions: [5, 10, 25, 50],
                    noFoundMessage: 'Belum ada data perbaikan.',
                });

                document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                    dataTableInstance.search(e.target.value);
                    applyClientSideFilter();
                });
            } catch (e) {
                console.warn("DataTable init failed", e);
            }

            // --- 3. CLIENT SIDE FILTER LOGIC ---
            function applyClientSideFilter() {
                const statusVal = tomSelects.status.getValue();
                const labVal = tomSelects.lab.getValue();
                const typeVal = tomSelects.type.getValue();
                const reporterVal = tomSelects.reporter.getValue();
                const specValVal = tomSelects.specVal.getValue();

                const rows = document.querySelectorAll('.repair-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowStatus = row.dataset.repairStatus;
                    const rowLab = row.dataset.labId;
                    const rowType = row.dataset.typeId;
                    const rowReporter = row.dataset.reporter;
                    const rowSpecs = JSON.parse(row.dataset.specs || '[]');

                    let matchStatus = (statusVal === "" || rowStatus === statusVal);
                    let matchLab = (labVal === "" || rowLab === labVal);
                    let matchType = (typeVal === "" || rowType === typeVal);
                    let matchReporter = (reporterVal === "" || rowReporter === reporterVal);
                    let matchSpec = (specValVal === "" || rowSpecs.includes(parseInt(specValVal)));

                    if (matchStatus && matchLab && matchType && matchReporter && matchSpec) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noResultRow = document.getElementById('filter-no-result');
                if (visibleCount === 0 && rows.length > 0) {
                    noResultRow.classList.remove('hidden');
                } else {
                    noResultRow.classList.add('hidden');
                }
            }

            // Filter Event Listeners
            [tomSelects.status, tomSelects.lab, tomSelects.type, tomSelects.reporter, tomSelects.specVal].forEach(
                ts => {
                    ts.on('change', applyClientSideFilter);
                });

            document.getElementById('reset-filter-btn').addEventListener('click', () => {
                Object.values(tomSelects).forEach(ts => ts.clear());
                document.getElementById('datatable-search-input').value = '';
                if (dataTableInstance) dataTableInstance.search('');
                applyClientSideFilter();
            });

            // --- 4. MODAL UPDATE STATUS ---
            tomSelectStatusModal = new TomSelect('#modal-status-select', {
                create: false,
                plugins: ['dropdown_input'],
                onChange: (val) => {
                    const completedFields = document.getElementById('completed-fields');
                    if (val === '2') { // Completed
                        completedFields.classList.remove('hidden');
                        // Tambahkan required attribute saat completed
                        document.querySelectorAll('input[name="is_successful"]').forEach(el => el
                            .required = true);
                    } else {
                        completedFields.classList.add('hidden');
                        document.querySelectorAll('input[name="is_successful"]').forEach(el => el
                            .required = false);
                    }
                }
            });

            const modal = document.getElementById('update-status-modal');
            const closeModalBtns = [
                document.getElementById('close-modal-btn'),
                document.getElementById('cancel-modal-btn'),
                document.getElementById('update-status-overlay')
            ];

            // Listener Tombol Update (Set Data ke Modal)
            // Delegasi event karena Datatable mungkin merender ulang baris
            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.btn-open-update-status')) {
                    const btn = e.target.closest('.btn-open-update-status');

                    const repairId = btn.dataset.repairId;
                    const itemableId = btn.dataset.itemableId;
                    const currentStatus = btn.dataset.currentStatus;
                    const itemName = btn.dataset.itemName;
                    const issue = btn.dataset.issue;
                    const notes = btn.dataset.currentNotes;
                    const success = btn.dataset.currentSuccess; // "1", "0", or ""

                    document.getElementById('modal-repair-id').value = repairId;
                    document.getElementById('modal-itemable-id').value = itemableId;
                    document.getElementById('modal-item-name').textContent = itemName;
                    document.getElementById('modal-issue').textContent = issue;
                    document.getElementById('modal-repair-notes').value = notes;

                    tomSelectStatusModal.setValue(currentStatus);

                    // Reset Radio
                    document.querySelectorAll('input[name="is_successful"]').forEach(el => el.checked =
                        false);
                    if (success === "1") {
                        document.querySelector('input[name="is_successful"][value="1"]').checked = true;
                    } else if (success === "0") {
                        document.querySelector('input[name="is_successful"][value="0"]').checked = true;
                    }

                    modal.classList.remove('hidden');
                }
            });

            const closeModal = () => modal.classList.add('hidden');
            closeModalBtns.forEach(el => {
                if (el) el.addEventListener('click', (e) => {
                    if (e.target === el) closeModal();
                })
            });

            // Submit Form (Kirim Array items untuk Partial Update)
            const form = document.getElementById('update-status-form');
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = document.getElementById('submit-status-btn');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = 'Menyimpan...';
                showLoading('Loading...', 'Updating Status Reparasi')
                const repairId = document.getElementById('modal-repair-id').value;
                const itemableId = document.getElementById('modal-itemable-id').value;
                const status = tomSelectStatusModal.getValue();
                const notes = document.getElementById('modal-repair-notes').value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content');

                let isSuccessful = null;
                if (status === '2') {
                    const successRadio = document.querySelector('input[name="is_successful"]:checked');
                    if (successRadio) isSuccessful = (successRadio.value === '1');
                }

                // Construct JSON Payload (Partial Update Structure)
                const payload = {
                    items: [{
                        itemable_id: itemableId,
                        status: status,
                        repair_notes: notes,
                        is_successful: isSuccessful
                    }]
                };

                try {
                    const response = await fetch(`/admin/repairs/${repairId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (!response.ok) throw new Error(data.message || 'Gagal update status');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status item berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });

                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            });
        });
    </script>
@endsection
