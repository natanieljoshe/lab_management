@extends('layouts.admin')

@section('title', 'Manajemen Mata Kuliah')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Daftar Mata Kuliah</h1>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Filter & Pencarian</h2>
                <div class="flex gap-2">
                    <button id="btn-copy-config" class="px-4 py-2 bg-white border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50">
                        Salin Konfigurasi
                    </button>
                    <button id="btn-add-matkul" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        + Tambah Matkul
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="filter_period" class="block text-sm font-semibold text-gray-700 mb-2">Periode Konfigurasi</label>
                    <select id="filter_period" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 bg-white">
                        @foreach ($periods as $p)
                            <option value="{{ $p->id }}" {{ $selectedPeriod && $selectedPeriod->id === $p->id ? 'selected' : '' }}>
                                {{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_sks" class="block text-sm font-semibold text-gray-700 mb-2">SKS</label>
                    <select id="filter_sks" class="filter-input" placeholder="Semua SKS...">
                        <option value="">Semua SKS</option>
                        <option value="2">2 SKS</option>
                        <option value="3">3 SKS</option>
                    </select>
                </div>

                <div>
                    <label for="filter_access" class="block text-sm font-semibold text-gray-700 mb-2">Akses File</label>
                    <select id="filter_access" class="filter-input" placeholder="Semua...">
                        <option value="">Semua</option>
                        <option value="1">Terbuka</option>
                        <option value="0">Tertutup</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                <div class="relative flex w-full">
                    <input id="datatable-search-input" type="search"
                        class="relative m-0 -mr-0.5 block w-[1px] min-w-0 flex-auto rounded-l border border-solid border-neutral-300 bg-transparent bg-clip-padding px-3 py-[0.6rem] text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none"
                        placeholder="Cari kode atau nama matkul..." />
                    <button
                        class="relative z-[2] flex items-center rounded-r bg-primary px-6 py-2.5 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-primary-700 hover:shadow-lg focus:bg-primary-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-primary-800 active:shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd"
                                d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <button id="reset-filter-btn"
                    class="px-6 py-2 bg-gray-200 w-full sm:w-auto text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Mata Kuliah</h2>
            </div>
            <div class="p-6">
                <div id="matkul-datatable-wrapper" class="overflow-x-auto">
                    <table id="matkul-table" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKS</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Curriculum Semester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Akses File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Root Folder</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Konfigurasi Jadwal
                                    <div class="normal-case font-normal text-[10px]">{{ $selectedPeriod ? $selectedPeriod->academic_year.' '.$selectedPeriod->semester.($selectedPeriod->active ? ' (Aktif)' : '') : 'Tidak ada periode' }}</div>
                                </th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($matkuls as $matkul)
                                @php
                                    $activeConfig = $matkul->matkulConfigs->first();
                                @endphp
                                <tr class="matkul-row" data-sks="{{ $matkul->sks }}"
                                    data-access="{{ $matkul->open_file_access ? '1' : '0' }}"
                                    data-search="{{ strtolower($matkul->kode . ' ' . $matkul->nama) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $matkul->kode }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $matkul->nama }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $matkul->sks }} SKS
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $matkul->curriculum_semester ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $matkul->is_required ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $matkul->is_required ? 'Wajib' : 'Pilihan' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $matkul->open_file_access ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $matkul->open_file_access ? 'Terbuka' : 'Tertutup' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($matkul->rootFolder)
                                            <a href="{{ route('admin.repository', ['folder' => $matkul->rootFolder->id]) }}"
                                                class="text-xs text-indigo-600 hover:text-indigo-800 font-mono underline flex items-center gap-1"
                                                onclick="navigate()">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z">
                                                    </path>
                                                </svg>
                                                {{ $matkul->rootFolder->full_path }}
                                            </a>
                                        @else
                                            <div class="text-xs text-gray-500 font-mono">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-700">
                                        @if (!$selectedPeriod)
                                            -
                                        @elseif (!$activeConfig)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                Belum dikonfigurasi
                                            </span>
                                        @else
                                            <span>P {{ (int) $activeConfig->practicum_hours }} jam · R {{ (int) $activeConfig->responsi_hours }} jam</span>
                                            @if ($activeConfig->is_manual)
                                                <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                    Manual
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button
                                            class="btn-config px-3 py-1.5 bg-purple-600 text-white hover:bg-purple-700 text-xs font-semibold rounded-md mr-2"
                                            data-id="{{ $matkul->id }}" data-kode="{{ $matkul->kode }}" data-nama="{{ $matkul->nama }}"
                                            data-semester="{{ $matkul->curriculum_semester }}">
                                            Konfigurasi
                                        </button>
                                        <button
                                            class="btn-view-modules px-3 py-1.5 bg-blue-500 text-white hover:bg-blue-600 text-xs font-semibold rounded-md mr-2"
                                            data-id="{{ $matkul->id }}" data-nama="{{ $matkul->nama }}"
                                            data-modules='@json($matkul->modules)'>
                                            Modules ({{ $matkul->modules->count() }})
                                        </button>
                                        <button
                                            class="btn-edit px-3 py-1.5 bg-yellow-500 text-white hover:bg-yellow-600 text-xs font-semibold rounded-md mr-2"
                                            data-id="{{ $matkul->id }}" data-kode="{{ $matkul->kode }}"
                                            data-nama="{{ $matkul->nama }}" data-sks="{{ $matkul->sks }}"
                                            data-access="{{ $matkul->open_file_access ? '1' : '0' }}"
                                            data-semester="{{ $matkul->curriculum_semester }}"
                                            data-required="{{ $matkul->is_required ? '1' : '0' }}">
                                            Edit
                                        </button>
                                        <button
                                            class="btn-delete px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 text-xs font-semibold rounded-md"
                                            data-id="{{ $matkul->id }}" data-kode="{{ $matkul->kode }}">
                                            Hapus
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

    {{-- Modal Add/Edit --}}
    <div id="matkul-modal" class="hidden" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t bg-gray-50">
                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Mata Kuliah</h3>
                    <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="matkul-form" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" id="matkul-id">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Matkul</label>
                        <input type="text" id="matkul-kode" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Mata Kuliah</label>
                        <input type="text" id="matkul-nama" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">SKS</label>
                        <select id="matkul-sks" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="2">2 SKS</option>
                            <option value="3">3 SKS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Semester Kurikulum (opsional)</label>
                        <select id="matkul-semester" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="">- Tidak diatur -</option>
                            @for ($i = 1; $i <= 8; $i++)<option value="{{ $i }}">Semester {{ $i }}</option>@endfor
                        </select>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="matkul-required" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Mata Kuliah Wajib (bukan pilihan)</span>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="matkul-access"
                                class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Akses File Terbuka</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" id="cancel-modal-btn"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Konfigurasi Jadwal --}}
    <div id="config-modal" class="hidden" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="config-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Konfigurasi Jadwal</h3>
                    <button id="config-close-btn" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="config-form" class="p-6 space-y-4">
                    <input type="hidden" id="config-matkul-id">
                    <input type="hidden" id="config-id">

                    <p id="config-matkul-label" class="text-sm font-semibold text-gray-800"></p>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Periode</label>
                        <select id="config-period"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p id="config-status" class="text-xs font-semibold"></p>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Semester Penjadwalan</label>
                        <select id="config-semester"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="">- Tidak diatur -</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Praktikum / minggu</label>
                        <input type="number" id="config-practicum" min="0" max="12" step="1" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">0 = tidak ada (dalam satuan jam bulat).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Responsi / minggu</label>
                        <input type="number" id="config-responsi" min="0" max="12" step="1" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">0 = tidak ada (dalam satuan jam bulat).</p>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="config-manual"
                                class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Jadwalkan manual saja (dikecualikan dari penjadwalan otomatis)</span>
                        </label>
                    </div>

                    <div class="flex justify-between items-center mt-6">
                        <div>
                            <button type="button" id="config-delete-btn"
                                class="hidden px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700">
                                Reset
                            </button>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" id="config-cancel-btn"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Salin Konfigurasi --}}
    <div id="copy-modal" class="hidden" role="dialog">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="copy-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Salin Konfigurasi</h3>
                    <button id="copy-close-btn" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="copy-form" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Dari periode</label>
                        <select id="copy-source"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ke periode</label>
                        <select id="copy-target"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <p class="text-xs text-gray-500">Hanya matkul yang belum punya konfigurasi di periode tujuan yang disalin; yang sudah ada tidak ditimpa.</p>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" id="copy-cancel-btn"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">
                            Salin
                        </button>
                    </div>
                </form>
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

        #matkul-datatable-wrapper,
        #matkul-table {
            width: 100% !important;
        }

        #matkul-table + div,
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
    #matkul-table { /* Tambahkan ID table kamu disini */
        width: 100% !important;
        max-width: 100% !important;
        display: table !important; /* Mencegah display: block yang kadang dipasang library */
    }

    /* FIX 3: Pastikan container utama juga full width */
    #matkul-datatable-wrapper,
    #matkul-table {
        width: 100% !important;
        display: block !important;
    }

    /* FIX 4: Pagination container agar sejajar */
    div[data-te-datatable-pagination-ref] {
        width: 100% !important;
    }
    </style>

    <script>
        let tomSelects = {};
        let dataTableInstance;
        let isEditMode = false;

        function navigate() {
            showLoading('Navigating...');
            setTimeout(() => {
                Swal.close();
            }, 1500);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('matkul').classList.add('bg-slate-100');
            document.getElementById('matkul').classList.add('active');

            tomSelects.sks = new TomSelect('#filter_sks', {
                plugins: ['clear_button']
            });
            tomSelects.access = new TomSelect('#filter_access', {
                plugins: ['clear_button']
            });

            dataTableInstance = new te.Datatable(document.getElementById('matkul-table'), {
                hover: true,
                pagination: true,
                entries: 10,
                entriesOptions: [5, 10, 25, 50]
            });

            document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                dataTableInstance.search(e.target.value);
            });

            function applyFilter() {
                const sks = tomSelects.sks.getValue();
                const access = tomSelects.access.getValue();

                document.querySelectorAll('.matkul-row').forEach(row => {
                    const matchSks = !sks || row.dataset.sks === sks;
                    const matchAccess = !access || row.dataset.access === access;
                    row.style.display = (matchSks && matchAccess) ? '' : 'none';
                });
            }

            tomSelects.sks.on('change', applyFilter);
            tomSelects.access.on('change', applyFilter);

            document.getElementById('reset-filter-btn').addEventListener('click', () => {
                Object.values(tomSelects).forEach(ts => ts.clear());
                document.getElementById('datatable-search-input').value = '';
                dataTableInstance.search('');
                applyFilter();
            });

            const modal = document.getElementById('matkul-modal');
            const closeModal = () => modal.classList.add('hidden');

            document.getElementById('btn-add-matkul').addEventListener('click', () => {
                isEditMode = false;
                document.getElementById('modal-title').textContent = 'Tambah Mata Kuliah';
                document.getElementById('matkul-form').reset();
                document.getElementById('matkul-id').value = '';
                modal.classList.remove('hidden');
            });

            document.getElementById('close-modal-btn').addEventListener('click', closeModal);
            document.getElementById('cancel-modal-btn').addEventListener('click', closeModal);
            document.getElementById('modal-overlay').addEventListener('click', (e) => {
                if (e.target.id === 'modal-overlay') closeModal();
            });

            const selectedPeriodId = @json($selectedPeriod?->id);
            const activePeriodId = @json($activePeriod?->id);
            const configModal = document.getElementById('config-modal');
            const configPeriod = document.getElementById('config-period');
            const copyModal = document.getElementById('copy-modal');
            let currentCurriculumSemester = '';

            const filterPeriodEl = document.getElementById('filter_period');
            if (filterPeriodEl) {
                filterPeriodEl.addEventListener('change', (e) => {
                    window.location.href = `/admin/matkul?period_id=${encodeURIComponent(e.target.value)}`;
                });
            }

            async function sendJson(url, method, body) {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: body ? JSON.stringify(body) : undefined
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal menyimpan');
                return result;
            }

            const hideConfigModal = () => configModal.classList.add('hidden');
            const hideCopyModal = () => copyModal.classList.add('hidden');
            ['config-close-btn', 'config-cancel-btn'].forEach(id => document.getElementById(id).addEventListener('click', hideConfigModal));
            ['copy-close-btn', 'copy-cancel-btn'].forEach(id => document.getElementById(id).addEventListener('click', hideCopyModal));
            document.getElementById('config-modal-overlay').addEventListener('click', e => { if (e.target.id === 'config-modal-overlay') hideConfigModal(); });
            document.getElementById('copy-modal-overlay').addEventListener('click', e => { if (e.target.id === 'copy-modal-overlay') hideCopyModal(); });

            async function loadConfigForm() {
                const matkulId = document.getElementById('config-matkul-id').value;
                const periodId = configPeriod.value;
                const status = document.getElementById('config-status');
                status.textContent = 'Memuat konfigurasi...';
                status.className = 'text-xs font-semibold text-gray-400';

                try {
                    const result = await sendJson(`/admin/matkul/${matkulId}/configs/${periodId}`, 'GET');
                    const cfg = result.config;
                    document.getElementById('config-id').value = cfg ? cfg.id : '';
                    document.getElementById('config-semester').value = cfg ? (cfg.scheduling_semester ?? '') : currentCurriculumSemester;
                    document.getElementById('config-practicum').value = cfg ? parseInt(cfg.practicum_hours, 10) : 0;
                    document.getElementById('config-responsi').value = cfg ? parseInt(cfg.responsi_hours, 10) : 0;
                    document.getElementById('config-manual').checked = cfg ? !!cfg.is_manual : false;
                    status.textContent = cfg ? 'Sudah dikonfigurasi di periode ini' : 'Belum dikonfigurasi di periode ini';
                    status.className = 'text-xs font-semibold ' + (cfg ? 'text-green-700' : 'text-gray-500');
                    document.getElementById('config-delete-btn').classList.toggle('hidden', !cfg);
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }

            async function openConfigModal(btn) {
                if (configPeriod.options.length === 0) {
                    Swal.fire('Info', 'Belum ada periode akademik. Tambahkan dulu di menu Periods.', 'info');
                    return;
                }
                document.getElementById('config-matkul-id').value = btn.dataset.id;
                document.getElementById('config-matkul-label').textContent = `${btn.dataset.kode} - ${btn.dataset.nama}`;
                currentCurriculumSemester = btn.dataset.semester || '';
                if (selectedPeriodId) configPeriod.value = selectedPeriodId;
                configModal.classList.remove('hidden');
                await loadConfigForm();
            }
            configPeriod.addEventListener('change', loadConfigForm);

            document.getElementById('config-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const matkulId = document.getElementById('config-matkul-id').value;
                const id = document.getElementById('config-id').value;
                const payload = {
                    scheduling_semester: document.getElementById('config-semester').value || null,
                    practicum_hours: Number(document.getElementById('config-practicum').value),
                    responsi_hours: Number(document.getElementById('config-responsi').value),
                    is_manual: document.getElementById('config-manual').checked
                };
                if (!id) payload.period_id = configPeriod.value;
                try {
                    showLoading(id ? 'Updating...' : 'Creating...');
                    await sendJson(id ? `/admin/matkul/${matkulId}/configs/${id}` : `/admin/matkul/${matkulId}/configs`, id ? 'PUT' : 'POST', payload);
                    Swal.fire('Berhasil!', 'Konfigurasi berhasil disimpan', 'success').then(() => location.reload());
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            });

            document.getElementById('config-delete-btn').addEventListener('click', async () => {
                const matkulId = document.getElementById('config-matkul-id').value;
                const id = document.getElementById('config-id').value;
                const confirm = await Swal.fire({
                    title: 'Reset Konfigurasi?', text: 'Konfigurasi matkul di periode ini akan dihapus.',
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset!', cancelButtonText: 'Batal'
                });
                if (!confirm.isConfirmed) return;
                try {
                    showLoading('Deleting...');
                    await sendJson(`/admin/matkul/${matkulId}/configs/${id}`, 'DELETE');
                    Swal.fire('Terhapus!', 'Konfigurasi direset', 'success').then(() => location.reload());
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            });

            document.getElementById('btn-copy-config').addEventListener('click', () => {
                const source = document.getElementById('copy-source');
                const target = document.getElementById('copy-target');
                if (source.options.length < 2) {
                    Swal.fire('Info', 'Dibutuhkan minimal 2 periode akademik untuk menyalin konfigurasi.', 'info');
                    return;
                }
                if (activePeriodId) target.value = activePeriodId;
                const firstOther = [...source.options].find(o => o.value !== target.value);
                source.value = firstOther.value;
                copyModal.classList.remove('hidden');
            });

            document.getElementById('copy-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                const source = document.getElementById('copy-source').value;
                const target = document.getElementById('copy-target').value;
                if (source === target) {
                    Swal.fire('Error', 'Periode sumber dan tujuan harus berbeda.', 'error');
                    return;
                }
                try {
                    showLoading('Menyalin...');
                    const result = await sendJson('/admin/matkul-configs/copy', 'POST', { source_period_id: source, target_period_id: target });
                    Swal.fire(result.copied > 0 ? 'Berhasil!' : 'Info', result.message, result.copied > 0 ? 'success' : 'info')
                        .then(() => { if (result.copied > 0) location.reload(); });
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            });

            document.body.addEventListener('click', async (e) => {
                if (e.target.closest('.btn-view-modules')) {
                    const btn = e.target.closest('.btn-view-modules');
                    const matkulId = btn.dataset.id;
                    const matkulNama = btn.dataset.nama;
                    const modules = JSON.parse(btn.dataset.modules);

                    if (modules.length === 0) {
                        try {
                            showLoading('Loading...');
                            const response = await fetch(
                                `/admin/matkul/${matkulId}/modules/deleted/details`);
                            const data = await response.json();
                            Swal.close();

                            if (data.success && data.deleted && data.deleted.length > 0) {
                                showDeletedOnlyModal(matkulNama, data.deleted);
                            } else {
                                Swal.fire('Info', 'No modules found for this matkul', 'info');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            Swal.fire('Error', error.message || 'Failed to load modules', 'error');
                        }
                        return;
                    }

                    showMatkulModulesModal(matkulId, matkulNama, modules);
                }

                if (e.target.closest('.btn-config')) {
                    openConfigModal(e.target.closest('.btn-config'));
                }

                if (e.target.closest('.btn-edit')) {
                    const btn = e.target.closest('.btn-edit');
                    isEditMode = true;
                    document.getElementById('modal-title').textContent = 'Edit Mata Kuliah';
                    document.getElementById('matkul-id').value = btn.dataset.id;
                    document.getElementById('matkul-kode').value = btn.dataset.kode;
                    document.getElementById('matkul-nama').value = btn.dataset.nama;
                    document.getElementById('matkul-sks').value = btn.dataset.sks;
                    document.getElementById('matkul-access').checked = btn.dataset.access === '1';
                    document.getElementById('matkul-semester').value = btn.dataset.semester || '';
                    document.getElementById('matkul-required').checked = btn.dataset.required === '1';
                    modal.classList.remove('hidden');
                }

                if (e.target.closest('.btn-delete')) {
                    const btn = e.target.closest('.btn-delete');
                    Swal.fire({
                        title: 'Hapus Matkul?',
                        text: `Yakin hapus ${btn.dataset.kode}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            showLoading("Deleting...");
                            try {
                                const response = await fetch(
                                    `/admin/matkul/${btn.dataset.id}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector(
                                                'meta[name="csrf-token"]').content,
                                            'Accept': 'application/json'
                                        }
                                    });
                                if (response.ok) {
                                    Swal.fire('Terhapus!', 'Matkul berhasil dihapus',
                                            'success')
                                        .then(() => location.reload());
                                } else {
                                    throw new Error('Gagal menghapus');
                                }
                            } catch (error) {
                                Swal.fire('Error', error.message, 'error');
                            }
                        }
                    });
                }
            });

            document.getElementById('matkul-form').addEventListener('submit', async (e) => {
                e.preventDefault();

                const id = document.getElementById('matkul-id').value;
                const data = {
                    kode: document.getElementById('matkul-kode').value,
                    nama: document.getElementById('matkul-nama').value,
                    sks: document.getElementById('matkul-sks').value,
                    open_file_access: document.getElementById('matkul-access').checked,
                    curriculum_semester: document.getElementById('matkul-semester').value || null,
                    is_required: document.getElementById('matkul-required').checked
                };

                const url = isEditMode ? `/admin/matkul/${id}` : '/admin/matkul';
                const method = isEditMode ? 'PUT' : 'POST';

                try {
                    showLoading(isEditMode ? "Updating..." : "Creating...");
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        Swal.fire('Berhasil!',
                                `Matkul berhasil ${isEditMode ? 'diupdate' : 'ditambahkan'}`, 'success')
                            .then(() => location.reload());
                    } else {
                        throw new Error(result.message || 'Gagal menyimpan');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            });
        });

        function showDeletedOnlyModal(matkulNama, deletedVersions) {
            console.log(deletedVersions);
            
            const deletedHtml = deletedVersions.map(m => `
                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-2 text-sm">
                    <p><strong>File:</strong> ${m.file.original_name}</p>
                    <p><strong>Deleted At:</strong> ${new Date(m.deleted_at).toLocaleString('id-ID')}</p>
                    <p><strong>Deleted By:</strong> ${m.deletor?.name || 'Unknown'}</p>
                </div>
            `).join('');

            Swal.fire({
                title: `Modules - ${matkulNama}`,
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-yellow-800">No active modules. Only deleted versions available.</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-600 mb-2">Deleted Versions</h4>
                            ${deletedHtml}
                        </div>
                    </div>
                `,
                width: '700px',
                showConfirmButton: false,
                showCloseButton: true
            });
        }

        async function showMatkulModulesModal(matkulId, matkulNama, modules) {
            const activeModule = modules.find(m => m.active);

            if (!activeModule) {
                return;
            }

            try {
                showLoading('Loading details...');
                const response = await fetch(`/admin/matkul/${matkulId}/modules/${activeModule.id}/details`);
                const data = await response.json();
                Swal.close();

                if (!data.success) {
                    throw new Error('Failed to load details');
                }

                let olderHtml = '<p class="text-gray-500 text-sm">No older versions</p>';
                if (data.older.length > 0) {
                    olderHtml = data.older.map(m => `
                        <div class="bg-white border border-gray-200 rounded-lg p-3 mb-2 text-sm">
                            <p><strong>File:</strong> ${m.file.original_name}</p>
                            <p><strong>Workload:</strong> ${m.workload_hours}h</p>
                            <p><strong>Last Edited:</strong> ${new Date(m.last_edited_at).toLocaleString('id-ID')}</p>
                            <a href="/admin/repository/file/${m.file_id}/download" 
                               class="inline-block mt-2 px-3 py-1.5 bg-blue-500 text-white hover:bg-blue-600 text-xs rounded-md"
                               onclick="showLoading('Downloading...')">Download</a>
                        </div>
                    `).join('');
                }

                let deletedHtml = '<p class="text-gray-500 text-sm">No deleted versions</p>';
                if (data.deleted.length > 0) {
                    deletedHtml = data.deleted.map(m => `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-2 text-sm">
                            <p><strong>File:</strong> ${m.file.original_name}</p>
                            <p><strong>Deleted At:</strong> ${new Date(m.deleted_at).toLocaleString('id-ID')}</p>
                            <p><strong>Deleted By:</strong> ${m.deletor?.name || 'Unknown'}</p>
                        </div>
                    `).join('');
                }

                Swal.fire({
                    title: `Modules - ${matkulNama}`,
                    html: `
                        <div class="text-left space-y-4">
                            <div class="bg-green-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-green-600 mb-2">Active Module</h4>
                                <div class="text-sm space-y-1">
                                    <p><strong>File:</strong> ${data.active.file.original_name}</p>
                                    <p><strong>Workload:</strong> ${data.active.workload_hours}h</p>
                                    <p><strong>Author:</strong> ${data.active.author.name}</p>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="/admin/repository/file/${data.active.file_id}/download" 
                                       class="px-3 py-1.5 bg-blue-500 text-white hover:bg-blue-600 text-xs rounded-md"
                                       onclick="showLoading('Downloading...')">Download</a>
                                    <button onclick="editModule('${matkulId}', '${data.active.id}', ${data.active.workload_hours}, ${data.active.active ? 1 : 0})" 
                                       class="px-3 py-1.5 bg-yellow-500 text-white hover:bg-yellow-600 text-xs rounded-md">Edit</button>
                                    <button onclick="deleteModule('${matkulId}', '${data.active.id}')" 
                                       class="px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 text-xs rounded-md">Delete</button>
                                </div>
                            </div>
                            <button onclick="toggleMatkulVersions()" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Show Older & Deleted Versions
                            </button>
                            <div id="matkul-older-versions" class="hidden">
                                <h4 class="font-semibold text-blue-600 mb-2">Older Versions</h4>
                                ${olderHtml}
                            </div>
                            <div id="matkul-deleted-versions" class="hidden">
                                <h4 class="font-semibold text-red-600 mb-2">Deleted Versions</h4>
                                ${deletedHtml}
                            </div>
                        </div>
                    `,
                    width: '700px',
                    showConfirmButton: false,
                    showCloseButton: true
                });
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        }

        function toggleMatkulVersions() {
            const older = document.getElementById('matkul-older-versions');
            const deleted = document.getElementById('matkul-deleted-versions');
            older.classList.toggle('hidden');
            deleted.classList.toggle('hidden');
        }

        async function editModule(matkulId, moduleId, workload, active) {
            const {
                value: formValues
            } = await Swal.fire({
                title: 'Edit Module',
                html: `
                    <div class="text-left space-y-3">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Workload (Hours)</label>
                            <input id="edit-workload" type="number" value="${workload}" min="0" class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input id="edit-active" type="checkbox" ${active ? 'checked' : ''} class="rounded">
                                <span class="ml-2 text-sm">Active</span>
                            </label>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    return {
                        workload_hours: document.getElementById('edit-workload').value,
                        active: document.getElementById('edit-active').checked
                    };
                }
            });

            if (formValues) {
                try {
                    showLoading('Updating...');
                    const response = await fetch(`/admin/matkul/${matkulId}/modules/${moduleId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formValues)
                    });

                    const result = await response.json();
                    if (response.ok) {
                        Swal.fire('Success!', 'Module updated', 'success').then(() => location.reload());
                    } else {
                        throw new Error(result.message || 'Update failed');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }

        async function deleteModule(matkulId, moduleId) {
            const result = await Swal.fire({
                title: 'Delete Module?',
                text: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete!'
            });

            if (result.isConfirmed) {
                try {
                    showLoading('Deleting...');
                    const response = await fetch(`/admin/matkul/${matkulId}/modules/${moduleId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        Swal.fire('Deleted!', 'Module deleted', 'success').then(() => location.reload());
                    } else {
                        throw new Error('Delete failed');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }
    </script>
@endsection
