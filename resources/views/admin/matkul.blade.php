@extends('layouts.admin')

@section('title', 'Manajemen Mata Kuliah')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Daftar Mata Kuliah</h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 md:p-8">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Filter & Pencarian</h2>
                <div class="flex flex-wrap gap-2">
                    <button id="btn-add-matkul" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                        + Tambah Matkul
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="filter_period" class="block text-sm font-semibold text-gray-700 mb-2">Periode Konfigurasi</label>
                    <select id="filter_period" class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Daftar Mata Kuliah</h2>
            </div>
            <div class="p-4 sm:p-6">
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
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($matkuls as $matkul)
                                @php
                                    $activeConfig = $matkul->matkulConfigs->first();
                                @endphp
                                @php
                                    $matkulJson = [
                                        "id" => $matkul->id,
                                        "kode" => $matkul->kode,
                                        "nama" => $matkul->nama,
                                        "sks" => $matkul->sks,
                                        "open_file_access" => $matkul->open_file_access,
                                        "curriculum_semester" => $matkul->curriculum_semester,
                                        "is_required" => $matkul->is_required,
                                    ];
                                @endphp
                                <tr class="matkul-row cursor-pointer hover:bg-gray-50 transition-colors"
                                    data-sks="{{ $matkul->sks }}"
                                    data-access="{{ $matkul->open_file_access ? '1' : '0' }}"
                                    data-search="{{ strtolower($matkul->kode . ' ' . $matkul->nama) }}"
                                    data-matkul-id="{{ $matkul->id }}"
                                    data-matkul-json='@json($matkulJson)'>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900"
                                            data-matkul-row-anchor
                                            data-sks="{{ $matkul->sks }}"
                                            data-access="{{ $matkul->open_file_access ? '1' : '0' }}"
                                            data-search="{{ strtolower($matkul->kode . ' ' . $matkul->nama) }}"
                                            data-matkul-id="{{ $matkul->id }}"
                                            data-matkul-json='@json($matkulJson)'>{{ $matkul->kode }}</div>
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
                                                onclick="event.stopPropagation(); navigate()">
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="detail-panel" class="hidden border-t border-gray-200 bg-white animate-none">
                <!-- Panel header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 px-4 sm:px-6 py-4 bg-slate-50/80 border-b border-gray-200">
                    <div class="min-w-0 flex-1 space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span id="detail-kode-badge" class="px-2.5 py-1 text-xs font-bold font-mono rounded-md bg-indigo-100 text-indigo-800 flex-shrink-0"></span>
                            <h3 id="detail-title" class="text-base sm:text-lg font-bold text-gray-900 break-words"></h3>
                        </div>
                        <div id="detail-meta-badges" class="flex flex-wrap items-center gap-1.5 sm:gap-2"></div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
                        <button id="detail-edit-btn" type="button" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            <span>Edit Matkul</span>
                        </button>
                        <button id="detail-delete-btn" type="button" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Hapus</span>
                        </button>
                        <button id="detail-close-btn" type="button" title="Tutup panel" class="ml-auto sm:ml-1 p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-200/60 rounded-lg transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Tab bar -->
                <div class="flex overflow-x-auto border-b border-gray-200 bg-white px-4 sm:px-6 gap-1 sm:gap-2">
                    <button data-tab="config" class="detail-tab inline-flex items-center gap-2 px-3 sm:px-4 py-3 text-xs sm:text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600 transition-colors whitespace-nowrap flex-shrink-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                        Konfigurasi Jadwal
                    </button>
                    <button data-tab="sections" class="detail-tab inline-flex items-center gap-2 px-3 sm:px-4 py-3 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors whitespace-nowrap flex-shrink-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Kelas Paralel
                    </button>
                    <button data-tab="modules" class="detail-tab inline-flex items-center gap-2 px-3 sm:px-4 py-3 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors whitespace-nowrap flex-shrink-0">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Modul Praktikum
                    </button>
                </div>

                <!-- Tab content container -->
                <div class="p-4 sm:p-6">
                    <!-- Tab: Konfigurasi -->
                    <div id="tab-config" class="tab-content">
                        <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 lg:gap-6">
                            <!-- Kolom Kiri: Form Konfigurasi -->
                            <div class="xl:col-span-8 bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-sm min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-gray-100">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 w-full sm:w-auto min-w-0">
                                        <label for="detail-config-period" class="text-sm font-semibold text-gray-700 flex-shrink-0">Periode</label>
                                        <select id="detail-config-period" class="w-full sm:w-auto sm:min-w-[240px] max-w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach ($periods as $p)
                                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <span id="detail-config-status" class="self-start sm:self-center flex-shrink-0 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Memuat...</span>
                                </div>

                                <form id="detail-config-form" class="space-y-4">
                                    <input type="hidden" id="detail-config-id">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                                        <div class="sm:col-span-2 lg:col-span-1 min-w-0">
                                            <label for="detail-config-semester" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Semester Penjadwalan</label>
                                            <select id="detail-config-semester" class="w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="">- Tidak diatur -</option>
                                                @for ($i = 1; $i <= 8; $i++)<option value="{{ $i }}">Semester {{ $i }}</option>@endfor
                                            </select>
                                        </div>
                                        <div class="min-w-0">
                                            <label for="detail-config-practicum" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Jam Praktikum / Minggu</label>
                                            <div class="relative flex items-center">
                                                <input type="number" id="detail-config-practicum" min="0" max="12" step="1" class="w-full pl-3.5 pr-12 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <span class="absolute right-3 text-xs font-medium text-gray-400 pointer-events-none">jam</span>
                                            </div>
                                        </div>
                                        <div class="min-w-0">
                                            <label for="detail-config-responsi" class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">Jam Responsi / Minggu</label>
                                            <div class="relative flex items-center">
                                                <input type="number" id="detail-config-responsi" min="0" max="12" step="1" class="w-full pl-3.5 pr-12 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                                <span class="absolute right-3 text-xs font-medium text-gray-400 pointer-events-none">jam</span>
                                            </div>
                                        </div>
                                    </div>

                                    <label class="flex items-start sm:items-center gap-2.5 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100/70 transition-colors">
                                        <input type="checkbox" id="detail-config-manual" class="mt-0.5 sm:mt-0 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 flex-shrink-0">
                                        <span class="text-xs sm:text-sm text-gray-700 font-medium leading-snug">Jadwalkan manual saja (tidak diikutkan ke penjadwalan otomatis)</span>
                                    </label>

                                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 pt-3 border-t border-gray-100">
                                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Simpan Konfigurasi
                                        </button>
                                        <button type="button" id="detail-config-reset-btn" class="hidden inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-white border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            Reset Konfigurasi
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Kolom Kanan: Card Salin Konfigurasi -->
                            <div class="xl:col-span-4 bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-5 flex flex-col justify-between min-w-0">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <h4 class="text-sm font-bold text-gray-800">Salin dari Periode Lain</h4>
                                    </div>
                                    <p class="text-xs text-gray-500 leading-relaxed mb-4">
                                        Salin pengaturan jam praktikum &amp; responsi mata kuliah ini dari periode sebelumnya ke periode yang sedang dipilih. Tidak menimpa jika sudah ada.
                                    </p>
                                    <div>
                                        <label for="copy-from-period" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1.5">Periode Sumber</label>
                                        <select id="copy-from-period" class="w-full max-w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                            @foreach ($periods as $p)
                                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button id="copy-single-config-btn" type="button" class="w-full mt-4 inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-semibold text-sm rounded-lg transition-colors">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2"/>
                                    </svg>
                                    Salin ke Periode Ini
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Kelas Paralel -->
                    <div id="tab-sections" class="tab-content hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2.5 sm:gap-3 w-full sm:w-auto">
                                <label for="detail-sections-period" class="text-sm font-semibold text-gray-700 flex-shrink-0">Periode</label>
                                <select id="detail-sections-period" class="w-full sm:w-auto sm:min-w-[240px] max-w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    @foreach ($periods as $p)
                                        <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                                    @endforeach
                                </select>
                                <button id="detail-sections-load-btn" type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-3.5 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">Muat</button>
                            </div>
                        </div>

                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-4">
                            <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wide mb-3">Tambah Kelas Paralel</h4>
                            <form id="detail-section-add-form" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3 items-end">
                                <div class="sm:col-span-1 xl:col-span-2">
                                    <label for="sec-label" class="block text-xs font-semibold text-gray-600 mb-1">Label Kelas</label>
                                    <input type="text" id="sec-label" maxlength="10" placeholder="A" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 uppercase">
                                </div>
                                <div class="sm:col-span-1 xl:col-span-3">
                                    <label for="sec-enrollment" class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Mahasiswa</label>
                                    <input type="number" id="sec-enrollment" min="0" max="9999" placeholder="30" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="sm:col-span-2 xl:col-span-4">
                                    <label for="sec-lecturer" class="block text-xs font-semibold text-gray-600 mb-1">Dosen Pengampu (opsional)</label>
                                    <input type="text" id="sec-lecturer" placeholder="Nama dosen..." class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="sm:col-span-2 xl:col-span-3">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                                        + Tambah Kelas
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Label Kelas</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Jumlah Mahasiswa</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Dosen Pengampu</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detail-sections-tbody" class="bg-white divide-y divide-gray-200">
                                        <tr><td colspan="4" class="text-center py-6 text-gray-400 text-sm">Pilih periode lalu klik Muat.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <p id="detail-sections-empty" class="text-sm text-gray-400 text-center py-6 bg-white hidden">Belum ada kelas paralel untuk periode ini.</p>
                        </div>
                    </div>

                    <!-- Tab: Modul -->
                    <div id="tab-modules" class="tab-content hidden">
                        <div id="detail-modules-content"><p class="text-sm text-gray-400">Pilih matkul untuk melihat modul.</p></div>
                    </div>
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
                            class="w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="2">2 SKS</option>
                            <option value="3">3 SKS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Semester Kurikulum (opsional)</label>
                        <select id="matkul-semester" class="w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">- Tidak diatur -</option>
                            @for ($i = 1; $i <= 8; $i++)<option value="{{ $i }}">Semester {{ $i }}</option>@endfor
                        </select>

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
                            class="w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ke periode</label>
                        <select id="copy-target"
                            class="w-full pl-3.5 pr-10 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}">{{ $p->academic_year }} - {{ $p->semester }}{{ $p->active ? ' (Aktif)' : '' }}</option>
                            @endforeach
                        </select>

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
            const copyModal = document.getElementById('copy-modal');
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

            const hideCopyModal = () => copyModal.classList.add('hidden');
            ['copy-close-btn', 'copy-cancel-btn'].forEach(id => document.getElementById(id).addEventListener('click', hideCopyModal));
            document.getElementById('copy-modal-overlay').addEventListener('click', e => { if (e.target.id === 'copy-modal-overlay') hideCopyModal(); });

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

            // ---- Master-Detail Panel ----
            let currentDetailMatkulId = null;
            const detailPanel = document.getElementById('detail-panel');

            function syncMatkulRows() {
                document.querySelectorAll('[data-matkul-row-anchor]').forEach(anchor => {
                    const row = anchor.closest('tr');
                    if (!row) return;
                    row.classList.add('matkul-row', 'cursor-pointer', 'hover:bg-gray-50', 'transition-colors');
                    row.dataset.sks = anchor.dataset.sks;
                    row.dataset.access = anchor.dataset.access;
                    row.dataset.search = anchor.dataset.search;
                    row.dataset.matkulId = anchor.dataset.matkulId;
                    row.dataset.matkulJson = anchor.dataset.matkulJson;
                    row.classList.toggle('bg-indigo-50', currentDetailMatkulId && row.dataset.matkulId === String(currentDetailMatkulId));
                    if (!row.dataset.boundClick) {
                        row.dataset.boundClick = '1';
                        row.addEventListener('click', () => {
                            const matkul = JSON.parse(row.dataset.matkulJson);
                            openDetailPanel(matkul);
                        });
                    }
                });
            }

            syncMatkulRows();
            const dtWrapper = document.getElementById('matkul-datatable-wrapper');
            if (dtWrapper) {
                new MutationObserver(() => syncMatkulRows()).observe(dtWrapper, { childList: true, subtree: true });
            }

            document.querySelectorAll('.matkul-row').forEach(row => {
                if (!row.dataset.boundClick) {
                    row.dataset.boundClick = '1';
                    row.addEventListener('click', () => {
                        const matkul = JSON.parse(row.dataset.matkulJson);
                        openDetailPanel(matkul);
                    });
                }
            });

            function openDetailPanel(matkul) {
                currentDetailMatkulId = matkul.id;
                const kodeBadge = document.getElementById('detail-kode-badge');
                if (kodeBadge) kodeBadge.textContent = matkul.kode;
                document.getElementById('detail-title').textContent = matkul.nama;
                const metaBadges = document.getElementById('detail-meta-badges');
                if (metaBadges) {
                    const semText = matkul.curriculum_semester ? `Semester ${matkul.curriculum_semester}` : 'Semester belum diatur';
                    const reqClass = matkul.is_required ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800';
                    const reqText = matkul.is_required ? 'Wajib' : 'Pilihan';
                    const accClass = matkul.open_file_access ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800';
                    const accText = matkul.open_file_access ? 'Akses File Terbuka' : 'Akses File Tertutup';
                    metaBadges.innerHTML = `
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">${matkul.sks} SKS</span>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">${semText}</span>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full ${reqClass}">${reqText}</span>
                        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full ${accClass}">${accText}</span>
                    `;
                }
                document.querySelectorAll('.matkul-row').forEach(r => r.classList.remove('bg-indigo-50'));
                const targetRow = document.querySelector(`.matkul-row[data-matkul-id="${matkul.id}"]`);
                if (targetRow) targetRow.classList.add('bg-indigo-50');
                const editBtn = document.getElementById('detail-edit-btn');
                Object.assign(editBtn.dataset, { id: matkul.id, kode: matkul.kode, nama: matkul.nama, sks: matkul.sks, access: matkul.open_file_access ? '1' : '0', semester: matkul.curriculum_semester ?? '', required: matkul.is_required ? '1' : '0' });
                document.getElementById('detail-delete-btn').dataset.id = matkul.id;
                document.getElementById('detail-delete-btn').dataset.kode = matkul.kode;
                detailPanel.classList.remove('hidden');
                detailPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                activateTab('config');
                if (selectedPeriodId) document.getElementById('detail-config-period').value = selectedPeriodId;
                loadDetailConfig();
            }

            document.getElementById('detail-close-btn').addEventListener('click', () => {
                detailPanel.classList.add('hidden');
                document.querySelectorAll('.matkul-row').forEach(r => r.classList.remove('bg-indigo-50'));
                currentDetailMatkulId = null;
            });

            document.querySelectorAll('.detail-tab').forEach(btn => btn.addEventListener('click', () => activateTab(btn.dataset.tab)));

            function activateTab(tab) {
                document.querySelectorAll('.detail-tab').forEach(b => {
                    const on = b.dataset.tab === tab;
                    b.classList.toggle('border-indigo-600', on); b.classList.toggle('text-indigo-600', on);
                    b.classList.toggle('border-transparent', !on); b.classList.toggle('text-gray-500', !on);
                });
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
                document.getElementById(`tab-${tab}`).classList.remove('hidden');
                if (tab === 'sections') { if (selectedPeriodId) document.getElementById('detail-sections-period').value = selectedPeriodId; loadDetailSections(); }
                if (tab === 'modules') loadDetailModules();
            }

            // Tab Konfigurasi
            async function loadDetailConfig() {
                const periodId = document.getElementById('detail-config-period').value;
                const status = document.getElementById('detail-config-status');
                status.textContent = 'Memuat...';
                status.className = 'px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600';
                try {
                    const res = await sendJson(`/admin/matkul/${currentDetailMatkulId}/configs/${periodId}`, 'GET');
                    const cfg = res.config;
                    document.getElementById('detail-config-id').value = cfg?.id ?? '';
                    document.getElementById('detail-config-semester').value = cfg?.scheduling_semester ?? '';
                    document.getElementById('detail-config-practicum').value = cfg ? parseInt(cfg.practicum_hours, 10) : 0;
                    document.getElementById('detail-config-responsi').value = cfg ? parseInt(cfg.responsi_hours, 10) : 0;
                    document.getElementById('detail-config-manual').checked = !!cfg?.is_manual;
                    document.getElementById('detail-config-reset-btn').classList.toggle('hidden', !cfg);
                    status.textContent = cfg ? 'Sudah dikonfigurasi' : 'Belum dikonfigurasi';
                    status.className = 'px-3 py-1 rounded-full text-xs font-semibold ' + (cfg ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600');
                } catch (e) { Swal.fire('Error', e.message, 'error'); }
            }
            document.getElementById('detail-config-period').addEventListener('change', loadDetailConfig);
            document.getElementById('detail-config-form').addEventListener('submit', async e => {
                e.preventDefault();
                const id = document.getElementById('detail-config-id').value;
                const periodId = document.getElementById('detail-config-period').value;
                const payload = {
                    scheduling_semester: document.getElementById('detail-config-semester').value || null,
                    practicum_hours: Number(document.getElementById('detail-config-practicum').value),
                    responsi_hours: Number(document.getElementById('detail-config-responsi').value),
                    is_manual: document.getElementById('detail-config-manual').checked,
                };
                if (!id) payload.period_id = periodId;
                try {
                    showLoading(id ? 'Updating...' : 'Creating...');
                    await sendJson(id ? `/admin/matkul/${currentDetailMatkulId}/configs/${id}` : `/admin/matkul/${currentDetailMatkulId}/configs`, id ? 'PUT' : 'POST', payload);
                    Swal.fire('Berhasil!', 'Konfigurasi disimpan', 'success').then(() => location.reload());
                } catch (e) { Swal.fire('Error', e.message, 'error'); }
            });
            document.getElementById('detail-config-reset-btn').addEventListener('click', async () => {
                const id = document.getElementById('detail-config-id').value;
                const confirm = await Swal.fire({ title: 'Reset Konfigurasi?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Reset!' });
                if (!confirm.isConfirmed) return;
                try { showLoading('Deleting...'); await sendJson(`/admin/matkul/${currentDetailMatkulId}/configs/${id}`, 'DELETE'); Swal.fire('Terhapus!', '', 'success').then(() => location.reload()); }
                catch (e) { Swal.fire('Error', e.message, 'error'); }
            });
            document.getElementById('copy-single-config-btn').addEventListener('click', async () => {
                const fromPeriod = document.getElementById('copy-from-period').value;
                const toPeriod = document.getElementById('detail-config-period').value;
                if (fromPeriod === toPeriod) { Swal.fire('Error', 'Periode sumber dan tujuan sama.', 'error'); return; }
                try {
                    showLoading('Menyalin...');
                    const res = await sendJson(`/admin/matkul/${currentDetailMatkulId}/configs/${fromPeriod}`, 'GET');
                    if (!res.config) { Swal.fire('Info', 'Tidak ada konfigurasi di periode sumber.', 'info'); return; }
                    const src = res.config;
                    await sendJson(`/admin/matkul/${currentDetailMatkulId}/configs`, 'POST', {
                        period_id: toPeriod, scheduling_semester: src.scheduling_semester,
                        practicum_hours: parseInt(src.practicum_hours, 10), responsi_hours: parseInt(src.responsi_hours, 10), is_manual: src.is_manual,
                    });
                    Swal.fire('Berhasil!', 'Konfigurasi disalin', 'success').then(() => loadDetailConfig());
                } catch (e) { Swal.fire('Error', e.message, 'error'); }
            });

            // Tab Kelas Paralel
            async function loadDetailSections() {
                const periodId = document.getElementById('detail-sections-period').value;
                const tbody = document.getElementById('detail-sections-tbody');
                const empty = document.getElementById('detail-sections-empty');
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-400 text-sm">Memuat...</td></tr>';
                empty.classList.add('hidden');
                try {
                    const res = await sendJson(`/admin/matkul/${currentDetailMatkulId}/sections/${periodId}`, 'GET');
                    tbody.innerHTML = '';
                    if (!res.sections.length) { empty.classList.remove('hidden'); return; }
                    res.sections.forEach(sec => tbody.insertAdjacentHTML('beforeend', renderSectionRow(sec)));
                    bindSectionRowEvents(currentDetailMatkulId);
                } catch (e) { tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-red-500 text-sm">${e.message}</td></tr>`; }
            }
            document.getElementById('detail-sections-load-btn').addEventListener('click', loadDetailSections);
            document.getElementById('detail-sections-period').addEventListener('change', loadDetailSections);

            function renderSectionRow(sec) {
                const safeLabel = String(sec.label ?? '').replace(/"/g, '&quot;');
                const safeLecturer = String(sec.lecturer ?? '').replace(/"/g, '&quot;');
                return `<tr data-section-id="${sec.id}" data-label="${safeLabel}" data-enrollment="${sec.enrollment}" data-lecturer="${safeLecturer}" class="hover:bg-gray-50/70 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 font-bold text-xs">${sec.label}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 font-medium whitespace-nowrap">${sec.enrollment} mahasiswa</td>
                    <td class="px-4 py-3 text-gray-700 min-w-[160px]">${sec.lecturer || '<span class="text-gray-400 italic">Belum diatur</span>'}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button type="button" class="btn-sec-edit inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </button>
                        <button type="button" class="btn-sec-delete inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg ml-1.5 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    </td></tr>`;
            }
            function bindSectionRowEvents(matkulId) {
                document.querySelectorAll('#detail-sections-tbody .btn-sec-delete').forEach(btn => {
                    btn.addEventListener('click', async () => {
                        const row = btn.closest('tr'); const sectionId = row.dataset.sectionId;
                        const confirm = await Swal.fire({ title: 'Hapus kelas?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc2626', confirmButtonText: 'Hapus' });
                        if (!confirm.isConfirmed) return;
                        try { await sendJson(`/admin/matkul/${matkulId}/sections/${sectionId}`, 'DELETE'); row.remove(); if (!document.querySelector('#detail-sections-tbody tr[data-section-id]')) document.getElementById('detail-sections-empty').classList.remove('hidden'); }
                        catch (e) { Swal.fire('Gagal', e.message, 'error'); }
                    });
                });
                document.querySelectorAll('#detail-sections-tbody .btn-sec-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const row = btn.closest('tr'); const sectionId = row.dataset.sectionId; const cells = row.querySelectorAll('td');
                        const ol = row.dataset.label || cells[0].textContent.trim();
                        const oe = row.dataset.enrollment || parseInt(cells[1].textContent.trim(), 10) || 0;
                        const olc = row.dataset.lecturer !== undefined ? row.dataset.lecturer : (cells[2].textContent.trim() === '-' || cells[2].textContent.trim() === 'Belum diatur' ? '' : cells[2].textContent.trim());
                        cells[0].innerHTML = `<input class="w-20 border border-gray-300 rounded-lg px-2.5 py-1 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500" value="${ol}" maxlength="10">`;
                        cells[1].innerHTML = `<input type="number" class="w-24 border border-gray-300 rounded-lg px-2.5 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" value="${oe}" min="0" max="9999">`;
                        cells[2].innerHTML = `<input class="w-full min-w-[140px] border border-gray-300 rounded-lg px-2.5 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" value="${olc}" placeholder="Nama dosen...">`;
                        cells[3].innerHTML = `<button type="button" class="btn-sec-save px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">Simpan</button><button type="button" class="btn-sec-cancel px-3 py-1.5 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded-lg ml-1.5 transition-colors">Batal</button>`;
                        cells[3].querySelector('.btn-sec-cancel').addEventListener('click', loadDetailSections);
                        cells[3].querySelector('.btn-sec-save').addEventListener('click', async () => {
                            try { await sendJson(`/admin/matkul/${matkulId}/sections/${sectionId}`, 'PUT', { label: cells[0].querySelector('input').value.toUpperCase(), enrollment: parseInt(cells[1].querySelector('input').value, 10), lecturer: cells[2].querySelector('input').value || null }); await loadDetailSections(); }
                            catch (e) { Swal.fire('Gagal', e.message, 'error'); }
                        });
                    });
                });
            }
            document.getElementById('detail-section-add-form').addEventListener('submit', async e => {
                e.preventDefault();
                try {
                    await sendJson(`/admin/matkul/${currentDetailMatkulId}/sections`, 'POST', {
                        period_id: document.getElementById('detail-sections-period').value,
                        label: document.getElementById('sec-label').value.toUpperCase(),
                        enrollment: parseInt(document.getElementById('sec-enrollment').value, 10) || 0,
                        lecturer: document.getElementById('sec-lecturer').value || null,
                    });
                    e.target.reset(); await loadDetailSections();
                } catch (e) { Swal.fire('Gagal', e.message, 'error'); }
            });

            // Tab Modul — endpoint GET /admin/matkul/{matkul}/modules/json
            async function loadDetailModules() {
                const content = document.getElementById('detail-modules-content');
                content.innerHTML = '<p class="text-sm text-gray-400">Memuat...</p>';
                try {
                    const res = await sendJson(`/admin/matkul/${currentDetailMatkulId}/modules/json`, 'GET');
                    const headerHtml = `
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 mb-4">
                            <p class="text-xs text-gray-500">Daftar modul praktikum untuk mata kuliah ini.</p>
                            <a href="/admin/matkul/${currentDetailMatkulId}/modules" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition-colors">
                                Kelola Modul Lengkap
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    `;
                    if (!res.modules?.length) {
                        content.innerHTML = headerHtml + '<div class="border border-dashed border-gray-200 rounded-xl p-6 sm:p-8 text-center text-sm text-gray-400">Belum ada modul yang diunggah untuk mata kuliah ini.</div>';
                        return;
                    }
                    const activeModule = res.modules.find(m => m.active);
                    let html = headerHtml + '<div class="space-y-3">';
                    if (activeModule) {
                        html += `<div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-start gap-3.5 min-w-0 flex-1">
                                <div class="p-2.5 bg-emerald-100 text-emerald-700 rounded-lg flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-emerald-200/80 text-emerald-900 mb-1">Modul Aktif</span>
                                    <p class="text-sm font-bold text-gray-900 break-words">${activeModule.file?.original_name ?? '-'}</p>
                                    <p class="text-xs text-gray-600 mt-0.5">Workload: <strong>${activeModule.workload_hours} jam</strong></p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                                <a href="/admin/repository/file/${activeModule.file_id}/download" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition-colors" onclick="showLoading('Downloading...')">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Download
                                </a>
                                <button type="button" onclick="editModule('${currentDetailMatkulId}','${activeModule.id}',${activeModule.workload_hours},1)" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" onclick="deleteModule('${currentDetailMatkulId}','${activeModule.id}')" class="flex-1 sm:flex-initial justify-center inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </div>`;
                    } else {
                        html += `<div class="border border-dashed border-gray-200 rounded-xl p-6 text-center text-sm text-gray-500">Tidak ada modul aktif saat ini.</div>`;
                    }
                    html += '</div>';
                    content.innerHTML = html;
                } catch (e) { content.innerHTML = `<p class="text-sm text-red-500">${e.message}</p>`; }
            }

            // Edit/Hapus dari panel detail
            document.getElementById('detail-edit-btn').addEventListener('click', () => {
                const btn = document.getElementById('detail-edit-btn');
                isEditMode = true;
                document.getElementById('modal-title').textContent = 'Edit Mata Kuliah';
                ['id','kode','nama','sks'].forEach(k => document.getElementById(`matkul-${k}`).value = btn.dataset[k]);
                document.getElementById('matkul-access').checked = btn.dataset.access === '1';
                document.getElementById('matkul-semester').value = btn.dataset.semester || '';
                document.getElementById('matkul-required').checked = btn.dataset.required === '1';
                document.getElementById('matkul-modal').classList.remove('hidden');
            });
            document.getElementById('detail-delete-btn').addEventListener('click', async () => {
                const btn = document.getElementById('detail-delete-btn');
                const result = await Swal.fire({ title: 'Hapus Matkul?', text: `Yakin hapus ${btn.dataset.kode}?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal' });
                if (!result.isConfirmed) return;
                showLoading('Deleting...');
                try {
                    const res = await fetch(`/admin/matkul/${btn.dataset.id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
                    if (res.ok) Swal.fire('Terhapus!', '', 'success').then(() => location.reload());
                    else throw new Error('Gagal menghapus');
                } catch (e) { Swal.fire('Error', e.message, 'error'); }
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
