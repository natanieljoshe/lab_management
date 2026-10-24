@extends('layouts.admin')

@section('title', 'Manajemen Item')

@section('body')
    <div class="flex flex-col md:flex-row w-full py-4 shadow-md items-center justify-center mb-5 px-6 md:px-4">
        <h1 class="text-center text-4xl uppercase font-bold mb-2 md:mb-0">Items & Components</h1>
    </div>

    {{-- ========================================================= --}}
    {{-- Filter Form --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-6">
        <div id="filter-form">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Filter Kontrol</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 filter-select">
                    {{-- Lab --}}
                    <div>
                        <label for="filter_lab_select"
                            class="block text-sm font-semibold text-gray-700 mb-2">Laboratorium</label>
                        <select id="filter_lab_select" name="lab_id" placeholder="Semua Lab...">
                            <option value="">Semua Laboratorium</option>
                            @foreach ($labs as $lab)
                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tipe --}}
                    <div>
                        <label for="filter_type_select" class="block text-sm font-semibold text-gray-700 mb-2">Tipe</label>
                        <select id="filter_type_select" name="type_id" placeholder="Semua Tipe...">
                            <option value="">Semua Tipe</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="filter_status" class="block text-sm font-semibold text-gray-700 mb-2">Status
                            Pemasangan</label>
                        <select id="filter_status" name="status" placeholder="Semua Status...">
                            <option value="">Semua Status</option>
                            <option value="unaffiliated">Belum Terpasang</option>
                            <option value="affiliated">Sudah Terpasang</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hanya berlaku untuk 'Item'.</p>
                    </div>

                    {{-- Kondisi --}}
                    <div>
                        <label for="filter_condition" class="block text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                        <select id="filter_condition" name="condition" placeholder="Semua Kondisi...">
                            <option value="">Semua Kondisi</option>
                            <option value="1">Baik</option>
                            <option value="0">Rusak</option>
                            <option value="pending_repair">Pending</option>
                            <option value="under_repair">Sedang Diperbaiki</option>
                            <option value="parent_pending_repair">Induk Pending</option>
                            <option value="parent_under_repair">Induk Sedang Diperbaiki</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">"Induk Sedang Diperbaiki" hanya untuk Components.</p>
                    </div>

                    {{-- Spesifikasi --}}
                    <div>
                        <label for="filter_spec_attr" class="block text-sm font-semibold text-gray-700 mb-2">Atribut
                            Spesifikasi</label>
                        <select id="filter_spec_attr" name="spec_attribute_id" placeholder="Pilih Atribut...">
                            <option value="">Semua Atribut</option>
                            @foreach ($specification as $spec)
                                <option value="{{ $spec->id }}" data-values='@json($spec->specValues)'>
                                    {{ $spec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="filter_spec_val" class="block text-sm font-semibold text-gray-700 mb-2">Value
                            Spesifikasi</label>
                        <select id="filter_spec_val" name="spec_value_id" placeholder="Pilih Atribut Dulu..." disabled>
                            {{-- JS akan mengisi ini --}}
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
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="h-5 w-5">
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
    </div>

    {{-- ========================================================= --}}
    {{-- Container Tabel Utama --}}
    {{-- ========================================================= --}}
    <div class="max-w-7xl mx-auto px-6 pb-12 space-y-8">

        {{-- ⬇️ Tombol Tambah Diubah ⬇️ --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3">
            <button id="open-create-set-modal-btn" type="button"
                class="px-6 py-3 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                + Tambah Set Item
            </button>
            <button id="open-create-modal-btn" type="button"
                class="px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg">
                + Tambah Item / Component
            </button>
        </div>
        {{-- ⬆️ ------------------- ⬆️ --}}

        {{-- Accordion untuk Tabel --}}
        <div id="accordion-items-components">

            {{-- Accordion Item 1: ITEMS --}}
            <div class="accordion-item bg-white rounded-xl shadow-sm border border-gray-200">
                <h2 class="accordion-header mb-0" id="items-heading">
                    <button
                        class="relative flex items-center w-full py-5 px-6 text-left text-xl font-semibold text-gray-800 bg-white border-0 rounded-xl transition focus:outline-none"
                        type="button" data-te-collapse-init data-te-target="#items-collapse" aria-expanded="true"
                        aria-controls="items-collapse">
                        <svg class="w-6 h-6 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        Daftar Items ({{ $items->count() }})
                        <span
                            class_pers="ml-auto h-5 w-5 shrink-0 rotate-[-180deg] transition-transform duration-200 ease-in-out motion-reduce:transition-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </button>
                </h2>
                <div id="items-collapse" class="!visible" data-te-collapse-item data-te-collapse-show
                    aria-labelledby="items-heading">
                    <div class="accordion-body p-6 border-t border-gray-200">
                        <div id="items-datatable" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Item</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Lokasi</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Spesifikasi</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kondisi</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($items as $item)
                                        {{-- SIAPKAN DATA SPESIFIKASI UNTUK FILTER JS --}}
                                        @php
                                            $itemSpecsJson = $item->specSetValues
                                                ->map(function ($s) {
                                                    return [
                                                        'attr_id' => $s->spec_attribute_id,
                                                        'val_id' => $s->spec_value_id,
                                                    ];
                                                })
                                                ->toJson();

                                            // Tentukan ID Lab (bisa dari Desk atau langsung Lab)
                                            $labId = $item->desk ? $item->desk->lab_id : $item->lab_id ?? '';

                                            // Tentukan Status Affiliasi
                                            $status = $item->desk || $item->lab ? 'affiliated' : 'unaffiliated';
                                            $hasPendingRepair = $item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0);
                                            $hasInProgressRepair = $item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1);
                                            $itemConditionFilter = $hasInProgressRepair
                                                ? 'under_repair'
                                                : ($hasPendingRepair ? 'pending_repair' : (string) $item->condition);
                                        @endphp

                                        <tr class="item-row group hover:bg-gray-50 transition-colors" data-lab-id="{{ $labId }}"
                                            data-type-id="{{ $item->type_id }}" data-status="{{ $status }}"
                                            data-condition="{{ $itemConditionFilter }}" data-specs='{{ $itemSpecsJson }}'
                                            data-search="{{ strtolower($item->name . ' ' . $item->serial_code) }}">

                                            {{-- ... (Isi kolom <td> biarkan sama seperti sebelumnya) ... --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">{{ $item->name }}</div>
                                                <div class="text-xs text-gray-600 font-mono">{{ $item->serial_code }}
                                                </div>
                                            </td>
                                            {{-- ... dst ... --}}

                                            {{-- PASTIKAN SEMUA KOLOM <td> ANDA MASIH ADA DI SINI --}}
                                                {{-- SAYA HANYA MENYINGKATNYA AGAR FOKUS KE TAG
                                        <tr> --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{-- Logika Lokasi Item --}}
                                                @if ($item->desk)
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $item->desk->lab->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-600">Meja {{ $item->desk->location }}
                                                    </div>
                                                @elseif ($item->lab)
                                                    <div class="text-sm font-medium text-indigo-700">
                                                        {{ $item->lab->name }}
                                                    </div>
                                                    <div class="text-sm text-indigo-600">Lemari Lab</div>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Belum
                                                        Terpasang</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $item->type->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                                @foreach ($item->specSetValues as $spec)
                                                    <span class="font-semibold">{{ $spec->specAttributes->name }}:</span>
                                                    {{ $spec->value }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($hasPendingRepair && !$hasInProgressRepair)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Pending</span>
                                                @elseif ($hasInProgressRepair)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Sedang
                                                        Diperbaiki</span>
                                                @elseif ($item->condition)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                                                @else
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                {{-- Tombol Aksi Item --}}
                                                <button type="button"
                                                    class="btn-open-action-item px-4 py-2 bg-indigo-600 text-gray-100 text-xs font-semibold rounded-md hover:bg-indigo-800"
                                                    data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}"
                                                    data-item-condition="{{ $item->condition }}"
                                                    data-item-desk-id="{{ $item->desk_id }}" data-item-lab-id="{{ $labId }}"
                                                    data-item-type-id="{{ $item->type_id }}">
                                                    Aksi
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-items-row">
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                Tidak ada item yang ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Accordion Item 2: COMPONENTS --}}
            <div class="accordion-item bg-white rounded-xl shadow-sm border border-gray-200 mt-6">
                <h2 class="accordion-header mb-0" id="components-heading">
                    <button
                        class="relative flex items-center w-full py-5 px-6 text-left text-xl font-semibold text-gray-800 bg-white border-0 rounded-xl transition focus:outline-none"
                        type="button" data-te-collapse-init data-te-collapse-collapsed data-te-target="#components-collapse"
                        aria-expanded="false" aria-controls="components-collapse">
                        <svg class="w-6 h-6 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        Daftar Components ({{ $components->count() }})
                        <span
                            class="ml-auto h-5 w-5 shrink-0 rotate-[-180deg] transition-transform duration-200 ease-in-out motion-reduce:transition-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </span>
                    </button>
                </h2>
                <div id="components-collapse" class="!visible" data-te-collapse-item aria-labelledby="components-heading">
                    <div class="accordion-body p-6 border-t border-gray-200">
                        <div id="components-datatable" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Komponen</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Terpasang di Item</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tipe</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Spesifikasi</th>
                                        <th scope="col" data-te-datatable-sortable="true"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kondisi</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($components as $comp)
                                        @php
                                            $compSpecsJson = $comp->specSetValues
                                                ->map(function ($s) {
                                                    return [
                                                        'attr_id' => $s->spec_attribute_id,
                                                        'val_id' => $s->spec_value_id,
                                                    ];
                                                })
                                                ->toJson();

                                            $compLabId = $comp->lab_id;
                                            // Jika attached ke item, cek itemnya ada di lab mana
                                            if (!$compLabId && $comp->item && $comp->item->desk) {
                                                $compLabId = $comp->item->desk->lab_id;
                                            } elseif (!$compLabId && $comp->item && $comp->item->lab) {
                                                $compLabId = $comp->item->lab_id;
                                            }

                                            // Kondisi Khusus Component
                                            $compConditionVal = $comp->condition;
                                            $hasPendingRepair = $comp->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0);
                                            $hasInProgressRepair = $comp->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1);
                                            $hasPendingParentRepair = $comp->item && $comp->item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0);
                                            $hasInProgressParentRepair = $comp->item && $comp->item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1);
                                            if ($hasInProgressRepair) {
                                                $compConditionVal = 'under_repair';
                                            } elseif ($hasPendingRepair) {
                                                $compConditionVal = 'pending_repair';
                                            } elseif ($hasInProgressParentRepair) {
                                                $compConditionVal = 'parent_under_repair';
                                            } elseif ($hasPendingParentRepair) {
                                                $compConditionVal = 'parent_pending_repair';
                                            }
                                        @endphp

                                        <tr class="component-row group hover:bg-gray-50 transition-colors"
                                            data-lab-id="{{ $compLabId }}" data-type-id="{{ $comp->type_id }}"
                                            data-condition="{{ $compConditionVal }}" data-specs='{{ $compSpecsJson }}'
                                            data-search="{{ strtolower($comp->name . ' ' . $comp->serial_code) }}">

                                            {{-- ... Isi kolom <td> komponen (Biarkan sama, hanya wrapper tr yang berubah) ...
                                                --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">{{ $comp->name }}</div>
                                                <div class="text-xs text-gray-600 font-mono">{{ $comp->serial_code }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($comp->item)
                                                    <div class="text-sm font-medium text-gray-900">{{ $comp->item->name }}
                                                    </div>
                                                @elseif ($comp->lab)
                                                    <div class="text-sm font-medium text-indigo-700">
                                                        {{ $comp->lab->name }}
                                                    </div>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Belum
                                                        Terpasang</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">{{ $comp->type->name ?? 'N/A' }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                                @foreach ($comp->specSetValues as $spec)
                                                    <span class="font-semibold">{{ $spec->specAttributes->name }}:</span>
                                                    {{ $spec->value }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{-- Badge Kondisi (Biarkan logika blade yang lama) --}}
                                                @if ($hasPendingRepair && !$hasInProgressRepair)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-700">Pending</span>
                                                @elseif ($hasInProgressRepair)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Sedang
                                                        Diperbaiki</span>
                                                @elseif ($hasPendingParentRepair)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Induk
                                                        Pending</span>
                                                @elseif ($hasInProgressParentRepair)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Induk
                                                        Sedang Diperbaiki</span>
                                                @elseif ($comp->condition)
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                                                @else
                                                    <span
                                                        class="condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <button type="button"
                                                    class="btn-open-action-component px-4 py-2 bg-indigo-600 text-gray-100 text-xs font-semibold rounded-md hover:bg-indigo-800"
                                                    data-item-id="{{ $comp->id }}" data-item-name="{{ $comp->name }}"
                                                    data-item-condition="{{ $comp->condition }}"
                                                    data-item-item-id="{{ $comp->item_id }}" data-item-lab-id="{{ $compLabId }}"
                                                    data-item-type-id="{{ $comp->type_id }}">
                                                    Aksi
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-components-row">
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                Tidak ada komponen yang ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 1: Buat Item --}}
    {{-- ========================================================= --}}
    <div id="create-item-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="create-item-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="create-item-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="create-item-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh]">

                <form id="create-item-form" data-action="{{ route('admin.items.create') }}"
                    class="space-y-0 flex flex-col max-h-[90vh]">
                    @csrf
                    {{-- Modal Header --}}
                    <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                        <h3 id="create-item-modal-title" class="text-xl font-semibold text-gray-900">
                            Input Item / Component Baru
                        </h3>
                        <button id="create-item-modal-close-btn" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body (Scrollable) --}}
                    <div class="p-6 space-y-6 overflow-y-auto">
                        {{-- Card untuk Info Dasar --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Detail Barang</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Barang</label>
                                    <div class="flex items-center space-x-6">
                                        <label class="flex items-center">
                                            <input type="radio" id="radio-is-item" name="is_component" value="0"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                                checked>
                                            <span class="ml-2 text-gray-700">Item (Induk)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" id="radio-is-component" name="is_component" value="1"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-gray-700">Component (Satuan)</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                        Barang</label>
                                    <input type="text" id="name" name="name" required
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="cth: PC Rakitan Spek A">
                                </div>
                                <div>
                                    <label for="serial_code" class="block text-sm font-semibold text-gray-700 mb-2">Serial
                                        Code</label>
                                    <input type="text" id="serial_code" name="serial_code" required
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="cth: PC-LSC-A-01">
                                </div>
                                <div>
                                    <label for="produced_at" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal
                                        Produksi</label>
                                    <input type="date" id="produced_at" name="produced_at"
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                                    <div class="flex items-center space-x-6 pt-3"> {{-- (Tambah padding) --}}
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="1"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                                checked>
                                            <span class="ml-2 text-gray-700">Baik</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="condition" value="0"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                            <span class="ml-2 text-gray-700">Rusak</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="type-selector"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                                    <select id="type-selector" name="type"
                                        placeholder="Cari atau tambah Type baru..."></select>
                                </div>
                            </div>
                        </div>

                        {{-- Card untuk Spesifikasi (Item Utama) --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-800">Spesifikasi <span
                                        id="spec-owner-label">(Item Utama)</span></h2>
                                <button type="button" id="add-spec-btn"
                                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                                    + Tambah Spesifikasi
                                </button>
                            </div>
                            <div id="specifications-container" class="space-y-4">
                                {{-- Baris spesifikasi akan ditambahkan di sini oleh JS --}}
                            </div>
                        </div>

                        {{-- Card untuk Komponen Bawaan --}}
                        <div id="components-section"
                            class="bg-gray-50 rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-xl font-semibold text-gray-800">Komponen Baru (Bawaan Item)</h2>
                                <button type="button" id="add-new-component-btn"
                                    class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                                    + Tambah Komponen Baru
                                </button>
                            </div>
                            <div id="new-components-container" class="space-y-6">
                                {{-- Template Komponen Baru akan di-clone di sini --}}
                            </div>
                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                        <button id="create-item-modal-cancel-btn" type="button"
                            class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center border border-gray-300">
                            Batal
                        </button>
                        <button type="button" id="submit-btn"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-40 h-[42px] flex items-center justify-center">
                            <span class="btn-text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 2: Aksi (Updated with Repair) --}}
    {{-- ========================================================= --}}
    <div id="action-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="action-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="action-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="action-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">
                {{-- Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="action-modal-title" class="text-xl font-semibold text-gray-900">
                        Aksi untuk [Nama Item]
                    </h3>
                    <button id="action-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                {{-- Body --}}
                <div class="p-6 grid grid-cols-2 gap-4">
                    <button id="action-btn-details"
                        class="p-6 bg-green-500 hover:bg-green-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.36 1.22-1.03 2.36-1.91 3.36M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 16.057 7.523 19 12 19c4.478 0 8.268-2.943 9.542-7 .36-1.22 1.03-2.36 1.91-3.36">
                            </path>
                        </svg>
                        <span class="font-semibold">Lihat Detail</span>
                    </button>
                    <button id="action-btn-condition"
                        class="p-6 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold">Ubah Kondisi</span>
                    </button>

                    {{-- ⬇️ TOMBOL REPAIR ⬇️ --}}
                    <button id="action-btn-repair"
                        class="p-6 bg-red-500 hover:bg-red-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        <span class="font-semibold">Perbaikan</span>
                    </button>
                    {{-- ⬆️ ------------- ⬆️ --}}

                    <button id="action-btn-attach"
                        class="p-6 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-semibold">Pasang ke Meja</span>
                    </button>
                    <button id="action-btn-attach-lab"
                        class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="font-semibold">Pasang ke Lab</span>
                    </button>
                    <button id="action-btn-manage-comp"
                        class="p-6 bg-gray-700 hover:bg-gray-800 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span class="font-semibold">Kelola Komponen</span>
                    </button>
                    <button id="action-btn-edit-comp"
                        class="p-6 bg-gray-400 hover:bg-gray-500 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                        <span class="font-semibold">Edit Komponen</span>
                    </button>
                    <button id="action-btn-detach-desk"
                        class="p-6 bg-red-500 hover:bg-red-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <span class="font-semibold">Lepas dari Meja</span>
                    </button>
                    <button id="action-btn-detach-lab"
                        class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <span class="font-semibold">Lepas dari Lab</span>
                    </button>
                    <button id="action-btn-detach-item"
                        class="p-6 bg-purple-500 hover:bg-purple-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <span class="font-semibold">Lepas dari Item</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 3: Pasang Item ke Meja --}}
    {{-- ========================================================= --}}
    <div id="attach-desk-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="attach-desk-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="attach-desk-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div id="attach-desk-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl flex flex-col max-h-[90vh]">
                {{-- Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="attach-desk-modal-title" class="text-xl font-semibold text-gray-900">
                        Pasang Item: <span id="attach-item-name" class="text-indigo-600"></span>
                    </h3>
                    <button id="attach-desk-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                {{-- Body (Konten Denah) --}}
                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <label for="lab-selector-modal" class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                            Laboratorium</label>
                        <select id="lab-selector-modal" placeholder="Pilih Lab..."></select>
                    </div>
                    <div id="desk-grid-container-modal" class="mb-8">
                        <div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 4: Lihat Detail Item/Component --}}
    {{-- ========================================================= --}}
    <div id="detail-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="detail-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[4000]"></div>
        <div id="detail-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[4001]">
            <div id="detail-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl flex flex-col max-h-[90vh]">
                {{-- Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="detail-modal-title" class="text-xl font-semibold text-gray-900">
                        Detail: <span id="detail-item-name" class="text-indigo-600">Nama Item</span>
                    </h3>
                    <button id="detail-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                {{-- Body (Konten Detail) --}}
                <div id="detail-modal-content" class="p-6 space-y-4 overflow-y-auto">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="create-set-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="create-set-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="create-set-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="create-set-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl flex flex-col max-h-[90vh]">

                <form id="create-set-form" data-action="{{ route('admin.items.set.create') }}"
                    class="space-y-0 flex flex-col max-h-[90vh]">
                    @csrf
                    {{-- Modal Header --}}
                    <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                        <h3 id="create-set-modal-title" class="text-xl font-semibold text-gray-900">
                            Buat Set Item Baru (4 Item)
                        </h3>
                        <button id="create-set-modal-close-btn" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body (Scrollable) --}}
                    <div class="p-6 space-y-6 overflow-y-auto bg-gray-50 flex-1">
                        {{-- Card untuk Info Set --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                            <h2 class="text-xl font-semibold text-gray-800 mb-6">Detail Set</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="set_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama
                                        Set</label>
                                    <input type="text" id="set_name" name="set_name" required
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="cth: Set PC Meja A1">
                                </div>
                                <div>
                                    <label for="set_note" class="block text-sm font-semibold text-gray-700 mb-2">Catatan
                                        <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                    <input type="text" id="set_note" name="set_note"
                                        class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                        placeholder="cth: Pembelian 2025">
                                </div>
                            </div>
                        </div>

                        {{-- Kontainer untuk 4 Item --}}
                        <div id="set-items-container" class="space-y-6">
                            {{-- JS akan mengisi 4 item form di sini --}}
                        </div>

                        {{-- ⬇️ BAGIAN ATTACH SEKARANG DI SINI (DALAM BODY) ⬇️ --}}
                        <div class="bg-blue-50 rounded-xl shadow-sm border border-blue-200 p-6 md:p-8 mt-6">
                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="attach_to_lab_checkbox"
                                    class="h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="attach_to_lab_checkbox"
                                    class="ml-2 block cursor-pointer text-sm font-semibold text-gray-700">
                                    Pasang Set ke Lab
                                </label>
                            </div>
                            <div id="lab-selection-section" class="hidden space-y-4">
                                <div>
                                    <label for="set-lab-selector"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                                        Laboratorium</label>
                                    <select id="set-lab-selector" placeholder="Pilih Lab..."></select>
                                </div>
                                <div class="flex items-center mt-4">
                                    <input type="checkbox" id="attach_to_desk_checkbox"
                                        class="h-4 w-4 cursor-pointer text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="attach_to_desk_checkbox"
                                        class="ml-2 block cursor-pointer text-sm font-semibold text-gray-700">
                                        Pasang Set ke Meja
                                    </label>
                                </div>
                                <div id="desk-attachment-section" class="hidden space-y-4">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <p class="text-sm text-yellow-800">
                                            <strong>Instruksi:</strong> Pilih 1 meja. Semua item dalam set ini akan dipasang
                                            ke
                                            meja tersebut.
                                        </p>
                                        <div id="set-selected-desks-display" class="mt-2 text-sm font-bold text-indigo-600">
                                            Belum ada meja dipilih.
                                        </div>
                                    </div>
                                    <div id="set-desk-grid-container">
                                        <div class="text-center py-8 text-gray-500">Pilih lab untuk melihat denah.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ⬆️ BAGIAN ATTACH SELESAI ⬆️ --}}

                    </div>

                    {{-- Modal Footer --}}
                    <div
                        class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b bg-gray-50 mt-auto">
                        <button id="create-set-modal-cancel-btn" type="button"
                            class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center border border-gray-300">
                            Batal
                        </button>
                        <button type="button" id="submit-set-btn"
                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-48 h-[42px] flex items-center justify-center">
                            <span class="btn-text">Simpan Set</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ⬇️ BARU: MODAL 6: Lapor Perbaikan (Updated) ⬇️ --}}
    <div id="repair-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="repair-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="repair-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div id="repair-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">

                <form id="repair-form" data-action="{{ route('admin.items.repair') }}"
                    class="space-y-0 flex flex-col max-h-[90vh]">
                    @csrf
                    {{-- Modal Header --}}
                    <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                        <h3 id="repair-modal-title" class="text-xl font-semibold text-gray-900">
                            Lapor Perbaikan
                        </h3>
                        <button id="repair-modal-close-btn" type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Modal Body (Scrollable) --}}
                    <div class="p-6 space-y-6 overflow-y-auto">
                        {{-- Hidden Inputs untuk Menyimpan ID --}}
                        <input type="hidden" id="repair-itemable-id" name="itemable_id">
                        <input type="hidden" id="repair-is-component" name="is_component">

                        {{-- Deskripsi Masalah --}}
                        <div>
                            <label for="repair-issue-description"
                                class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Masalah</label>
                            <textarea id="repair-issue-description" name="issue_description" rows="4" required
                                class="block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Jelaskan kerusakan yang terjadi..."></textarea>
                        </div>

                        {{-- Info Komponen Anak (Read Only) --}}
                        <div id="repair-components-section" class="hidden">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold text-gray-700">Komponen Anak yang Terbawa:</h3>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md">Info Only</span>
                            </div>
                            <div id="repair-components-list"
                                class="space-y-2 max-h-60 overflow-y-auto p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                {{-- JS akan mengisi ini dengan list READ ONLY --}}
                                <div class="text-center py-4 text-gray-500">Memuat informasi komponen...</div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 italic">
                                *Komponen di atas adalah bagian dari Item ini dan otomatis dianggap terbawa/terkait.
                            </p>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                        <button id="repair-modal-cancel-btn" type="button"
                            class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center border border-gray-300">
                            Batal
                        </button>
                        <button type="button" id="submit-repair-btn"
                            class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-40 h-[42px] flex items-center justify-center">
                            <span class="btn-text">Kirim Laporan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ⬆️ MODAL 6 SELESAI ⬆️ --}}

    {{-- ========================================================= --}}
    {{-- MODAL 7: Kelola Komponen --}}
    {{-- ========================================================= --}}
    <div id="manage-components-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="manage-components-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div id="manage-components-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Kelola Komponen: <span id="manage-comp-item-name" class="text-indigo-600"></span>
                    </h3>
                    <button id="manage-components-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6 overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Komponen Terpasang</h4>
                            <div id="attached-components-list" class="space-y-2 min-h-[200px]">
                                <div class="text-center py-8 text-gray-500">Memuat...</div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Komponen Tersedia</h4>
                            <div id="available-components-list" class="space-y-2 min-h-[200px]">
                                <div class="text-center py-8 text-gray-500">Memuat...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- TEMPLATES --}}
    {{-- ========================================================= --}}

    {{-- TEMPLATE 1: untuk baris spesifikasi --}}
    <template id="spec-row-template">
        <div class="grid grid-cols-12 gap-4 spec-row p-4 border border-gray-200 rounded-lg bg-gray-50">
            <div class="col-span-12 md:col-span-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Attribute</label>
                <select class="spec-attribute" placeholder="Cari attribute..."></select>
            </div>
            <div class="col-span-12 md:col-span-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">Value</label>
                <select class="spec-value" placeholder="Pilih attribute dulu..."></select>
            </div>
            <div class="col-span-12 md:col-span-2 flex items-end">
                <button type="button"
                    class="remove-spec-btn w-full px-4 py-3 bg-rose-50 text-rose-600 text-sm font-semibold rounded-lg hover:bg-rose-100 transition-colors">
                    Hapus
                </button>
            </div>
        </div>
    </template>

    {{-- TEMPLATE 2: UNTUK FORM KOMPONEN BARU (di dalam Item) --}}
    <template id="new-component-form-template">
        <div class="new-component-row border-2 border-purple-200 bg-purple-50 rounded-lg p-6 relative">
            <button type="button"
                class="remove-new-component-btn absolute -top-3 -right-3 w-8 h-8 bg-rose-500 hover:bg-rose-600 text-white rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Komponen</label>
                    <input type="text"
                        class="new-component-name block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                        placeholder="cth: RAM 16GB">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Serial Code</label>
                    <input type="text"
                        class="new-component-serial block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                        placeholder="cth: SN-RAM-001">
                </div>
                {{-- ⬇️ TAMBAHAN: Produced At Komponen ⬇️ --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tgl. Produksi Komponen</label>
                    <input type="date"
                        class="new-component-produced-at block w-full px-4 py-3 text-base border border-gray-300 rounded-lg">
                </div>
                {{-- ⬆️ --------------------------- ⬆️ --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi Komponen</label>
                    <div class="flex items-center space-x-6 pt-3"> {{-- (Tambah padding) --}}
                        <label class="flex items-center">
                            <input type="radio" class="new-component-condition" name="component_condition_NEW-INDEX"
                                value="1" class="h-4 w-4 text-purple-600" checked>
                            <span class="ml-2 text-gray-700">Baik</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" class="new-component-condition" name="component_condition_NEW-INDEX"
                                value="0" class="h-4 w-4 text-purple-600">
                            <span class="ml-2 text-gray-700">Rusak</span>
                        </label>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type Komponen</label>
                    <select class="new-component-type-select" placeholder="Pilih Tipe Komponen..."></select>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-purple-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Spesifikasi Komponen</h3>
                    <button type="button"
                        class="btn-add-new-comp-spec px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">
                        + Tambah Spek
                    </button>
                </div>
                <div class="new-component-specs-container space-y-4">
                    {{-- Template spek akan di-clone di sini --}}
                </div>
            </div>
        </div>
    </template>

    {{-- ⬇️ TEMPLATE 3: BARU UNTUK FORM ITEM DI DALAM SET ⬇️ --}}
    <template id="set-item-template">
        <div class="set-item-row bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-6 set-item-title">Item 1: Monitor</h3>
            <div class="space-y-6">
                {{-- Info Dasar Item --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" class="set-item-is-component" value="0"> {{-- Selalu Item --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Item</label>
                        <input type="text"
                            class="set-item-name block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                            placeholder="cth: Monitor LG 24MP59G" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Serial Code</label>
                        <input type="text"
                            class="set-item-serial block w-full px-4 py-3 text-base border border-gray-300 rounded-lg"
                            placeholder="cth: MON-LSC-01" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Produksi</label>
                        <input type="date"
                            class="set-item-produced-at block w-full px-4 py-3 text-base border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kondisi</label>
                        <div class="flex items-center space-x-6 pt-3">
                            <label class="flex items-center">
                                <input type="radio" class="set-item-condition" name="set_item_condition_INDEX" value="1"
                                    class="h-4 w-4 text-indigo-600" checked>
                                <span class="ml-2 text-gray-700">Baik</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" class="set-item-condition" name="set_item_condition_INDEX" value="0"
                                    class="h-4 w-4 text-indigo-600">
                                <span class="ml-2 text-gray-700">Rusak</span>
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Type</label>
                        <select class="set-item-type-select" placeholder="Pilih Tipe..." required></select>
                    </div>
                </div>

                {{-- Spesifikasi Item --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Spesifikasi Item</h3>
                        <button type="button"
                            class="btn-add-set-item-spec px-3 py-1 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">
                            + Tambah Spek
                        </button>
                    </div>
                    <div class="set-item-specs-container space-y-4">
                        {{-- Template spek akan di-clone di sini --}}
                    </div>
                </div>

                {{-- Komponen Bawaan Item --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Komponen Bawaan</h3>
                        <button type="button"
                            class="btn-add-set-item-comp px-3 py-1 bg-purple-600 text-white text-xs font-semibold rounded-lg hover:bg-purple-700">
                            + Tambah Komponen
                        </button>
                    </div>
                    <div class="set-item-components-container space-y-6">
                        {{-- Template komponen baru akan di-clone di sini --}}
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@section('script')
    {{-- Style untuk TomSelect --}}
    <style>
        .swal2-input {
            padding: 0 0.75rem !important;
        }

        /* DataTable Styling */
        #items-datatable table,
        #components-datatable table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }

        #items-datatable table th,
        #items-datatable table td,
        #components-datatable table th,
        #components-datatable table td {
            border: 1px solid #e5e7eb !important;
        }

        #items-datatable table thead th,
        #components-datatable table thead th {
            background-color: #f9fafb !important;
            font-weight: 600 !important;
            border-bottom: 2px solid #d1d5db !important;
        }

        .filter-select .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        .filter-select .ts-input {
            @apply text-sm;
        }

        .filter-select .ts-wrapper.single .ts-control .item {
            @apply bg-gray-200 text-gray-800 rounded-md px-2 py-1;
        }

        /* ⬇️ Style untuk TomSelect di dalam Modal ⬇️ */
        #create-item-modal .ts-control,
        #create-set-modal .ts-control {
            @apply block w-full px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }

        #create-item-modal .ts-input,
        #create-set-modal .ts-input {
            @apply text-sm;
        }
    </style>

    {{-- ========================================================= --}}
    {{-- JAVASCRIPT (FULL UPDATED) --}}
    {{-- ========================================================= --}}
    <script>
        document.getElementById('items').classList.add('bg-slate-100');
        document.getElementById('items').classList.add('active');

        // Data dari Blade
        const allTypes = @json($types);
        const allSpecAttributes = @json($specification);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Event listener untuk tombol aksi Item
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-open-action-item')) {
                const btn = e.target;
                currentItemId = btn.dataset.itemId;
                currentItemName = btn.dataset.itemName;
                currentItemCondition = parseInt(btn.dataset.itemCondition);
                currentItemType = 'item';
                currentDeskId = btn.dataset.itemDeskId || null;
                currentLabId = btn.dataset.itemLabId || null;
                currentItemIdParent = null;

                const modal = document.getElementById('action-modal');
                const titleEl = document.getElementById('action-modal-title');
                if (titleEl) titleEl.textContent = `Aksi untuk ${currentItemName}`;
                if (modal) modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                updateActionModalButtons('item');
            }

            // Event listener untuk tombol aksi Component
            if (e.target.classList.contains('btn-open-action-component')) {
                const btn = e.target;
                currentItemId = btn.dataset.itemId;
                currentItemName = btn.dataset.itemName;
                currentItemCondition = parseInt(btn.dataset.itemCondition);
                currentItemType = 'component';
                currentDeskId = null;
                currentItemIdParent = btn.dataset.itemItemId || null;
                currentLabId = btn.dataset.itemLabId || null;

                const modal = document.getElementById('action-modal');
                const titleEl = document.getElementById('action-modal-title');
                if (titleEl) titleEl.textContent = `Aksi untuk ${currentItemName}`;
                if (modal) modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                updateActionModalButtons('component');
            }
        });

        // =================================================================
        // Variabel Global
        // =================================================================
        let currentItemId = null;
        let currentItemName = null;
        let currentItemCondition = null;
        let currentItemType = 'item'; // 'item' or 'component'
        let currentIsComponent = 0; // 0 for item, 1 for component
        let currentDeskId = null; // untuk item
        let currentItemIdParent = null; // untuk component (item_id)
        let currentLabId = null; // untuk item dan component

        // Variabel untuk Modal Create Item
        let tomSelectInstances = {
            mainSpecs: [],
            newComponents: []
        };
        let newComponentIndex = 0;
        let tomSelectType;

        // Variabel untuk Modal Create Set
        let setItemTomInstances = [];
        let setItemComponentIndex = 0;

        let tomSelectLabModal;
        let modalLabDesks = [];

        // Objek untuk filter
        let filterTomSelects = {
            attr: null,
            val: null
        };

        // Sets
        let tomSelectSetLab;
        let setLabDesks = [];
        let setSelectedDeskLocations = [];

        function hideLoading() {
            Swal.close();
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        function showToast(title, message, icon = 'success') {
            Toast.fire({
                icon: icon,
                title: title,
                text: message
            });
        }

        // =================================================================
        // Fungsi Konfirmasi
        // =================================================================
        async function confirmCreate(name, type) {
            const result = await Swal.fire({
                title: `Tambah ${type} Baru?`,
                text: `"${name}" tidak ditemukan. Apakah Anda ingin menambahkannya sebagai ${type} baru?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambahkan!',
                cancelButtonText: 'Batal'
            });
            return result.isConfirmed;
        }

        function findTypeIdByName(name) {
            if (!name) return null;
            const normalizedName = name.trim().toLowerCase();
            const type = allTypes.find(t => t.name.toLowerCase() === normalizedName);
            return type ? type.id : null;
        }

        // Helper Cek Status Modal
        function checkModalStateAndToggleBodyOverflow() {
            const isAnyModalOpen = !document.getElementById('create-item-modal').classList.contains('hidden') ||
                !document.getElementById('action-modal').classList.contains('hidden') ||
                !document.getElementById('attach-desk-modal').classList.contains('hidden') ||
                !document.getElementById('detail-modal').classList.contains('hidden') ||
                !document.getElementById('create-set-modal').classList.contains('hidden') ||
                !document.getElementById('repair-modal').classList.contains('hidden') ||
                !document.getElementById('manage-components-modal').classList.contains('hidden');

            if (!isAnyModalOpen) {
                document.body.style.overflow = '';
            } else {
                document.body.style.overflow = 'hidden';
            }
        }

        // =================================================================
        // FUNGSI INISIALISASI MODAL
        // =================================================================

        function initializeCreateItemModal() {
            const modal = document.getElementById('create-item-modal');
            const form = document.getElementById('create-item-form');
            const submitBtn = document.getElementById('submit-btn');
            const addSpecBtn = document.getElementById('add-spec-btn');
            const closeBtn = document.getElementById('create-item-modal-close-btn');
            const cancelBtn = document.getElementById('create-item-modal-cancel-btn');
            const overlay = document.getElementById('create-item-modal-overlay');
            const specificationsContainer = document.getElementById('specifications-container');

            const radioIsItem = document.getElementById('radio-is-item');
            const radioIsComponent = document.getElementById('radio-is-component');
            const componentsSection = document.getElementById('components-section');
            const newComponentsContainer = document.getElementById('new-components-container');
            const addNewComponentBtn = document.getElementById('add-new-component-btn');
            const specOwnerLabel = document.getElementById('spec-owner-label');

            if (!modal) return;

            function toggleComponentSection() {
                if (radioIsItem.checked) {
                    componentsSection.style.display = 'block';
                    specOwnerLabel.textContent = '(Item Utama)';
                } else {
                    componentsSection.style.display = 'none';
                    specOwnerLabel.textContent = '(Component)';
                    newComponentsContainer.innerHTML = '';
                    if (tomSelectInstances.newComponents) {
                        tomSelectInstances.newComponents.forEach(comp => {
                            comp.typeSelect.destroy();
                            comp.specInstances.forEach(spec => {
                                spec.attr.destroy();
                                spec.val.destroy();
                            });
                        });
                    }
                    tomSelectInstances.newComponents = [];
                    newComponentIndex = 0;
                }
            }

            radioIsItem.addEventListener('change', toggleComponentSection);
            radioIsComponent.addEventListener('change', toggleComponentSection);

            window.openCreateItemModal = () => {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                form.reset();
                if (tomSelectType) tomSelectType.clear();
                specificationsContainer.innerHTML = '';
                newComponentsContainer.innerHTML = '';

                if (tomSelectInstances.mainSpecs) {
                    tomSelectInstances.mainSpecs.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                }
                if (tomSelectInstances.newComponents) {
                    tomSelectInstances.newComponents.forEach(comp => {
                        comp.typeSelect.destroy();
                        comp.specInstances.forEach(spec => {
                            spec.attr.destroy();
                            spec.val.destroy();
                        });
                    });
                }

                tomSelectInstances = {
                    mainSpecs: [],
                    newComponents: []
                };
                newComponentIndex = 0;
                radioIsItem.checked = true;
                toggleComponentSection();
            }
            window.closeCreateItemModal = () => {
                modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();
            }

            closeBtn.addEventListener('click', window.closeCreateItemModal);
            cancelBtn.addEventListener('click', window.closeCreateItemModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) window.closeCreateItemModal();
            });

            tomSelectType = new TomSelect('#type-selector', {
                options: allTypes.map(type => ({
                    value: type.id,
                    text: type.name
                })),
                plugins: ['dropdown_input', 'clear_button'],
                create: async function (input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Type');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                }
            });

            addSpecBtn.addEventListener('click', () => {
                const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                    .firstElementChild;
                specificationsContainer.appendChild(newSpecRow);

                const specTomSelects = initializeSpecRow(newSpecRow);
                if (!specTomSelects) return;

                const specInstance = {
                    row: newSpecRow,
                    attr: specTomSelects.attr,
                    val: specTomSelects.val
                };
                tomSelectInstances.mainSpecs.push(specInstance);

                newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                    specTomSelects.attr.destroy();
                    specTomSelects.val.destroy();
                    newSpecRow.remove();
                    tomSelectInstances.mainSpecs = tomSelectInstances.mainSpecs.filter(inst => inst.row !==
                        newSpecRow);
                });
            });

            addNewComponentBtn.addEventListener('click', () => {
                const newCompRow = document.getElementById('new-component-form-template').content.cloneNode(true)
                    .firstElementChild;
                const compIndex = newComponentIndex++;

                newCompRow.dataset.compIndex = compIndex;
                newCompRow.querySelectorAll('.new-component-condition').forEach(radio => {
                    radio.name = `component_condition_${compIndex}`;
                });

                const typeSelectEl = newCompRow.querySelector('.new-component-type-select');
                const addCompSpecBtn = newCompRow.querySelector('.btn-add-new-comp-spec');
                const newCompSpecContainer = newCompRow.querySelector('.new-component-specs-container');
                const removeCompBtn = newCompRow.querySelector('.remove-new-component-btn');

                const compTypeTomSelect = new TomSelect(typeSelectEl, {
                    options: allTypes.map(type => ({
                        value: type.id,
                        text: type.name
                    })),
                    plugins: ['dropdown_input', 'clear_button'],
                    create: async function (input, callback) {
                        const isConfirmed = await confirmCreate(input, 'Type');
                        if (isConfirmed) {
                            const newValue = `new::${input}`;
                            callback({
                                value: newValue,
                                text: input
                            });
                        } else {
                            callback();
                        }
                    },
                    render: {
                        option_create: (data, escape) =>
                            `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                    }
                });

                const componentInstanceData = {
                    id: compIndex,
                    row: newCompRow,
                    typeSelect: compTypeTomSelect,
                    specInstances: []
                };
                tomSelectInstances.newComponents.push(componentInstanceData);

                addCompSpecBtn.addEventListener('click', () => {
                    const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                        .firstElementChild;
                    newCompSpecContainer.appendChild(newSpecRow);
                    const specTomSelects = initializeSpecRow(newSpecRow);
                    if (!specTomSelects) return;

                    const specInstance = {
                        row: newSpecRow,
                        attr: specTomSelects.attr,
                        val: specTomSelects.val
                    };
                    componentInstanceData.specInstances.push(specInstance);

                    newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                        specTomSelects.attr.destroy();
                        specTomSelects.val.destroy();
                        newSpecRow.remove();
                        componentInstanceData.specInstances = componentInstanceData.specInstances
                            .filter(inst => inst.row !== newSpecRow);
                    });
                });

                removeCompBtn.addEventListener('click', () => {
                    compTypeTomSelect.destroy();
                    componentInstanceData.specInstances.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                    newCompRow.remove();
                    tomSelectInstances.newComponents = tomSelectInstances.newComponents.filter(inst => inst
                        .id !== compIndex);
                });

                newComponentsContainer.appendChild(newCompRow);
            });

            submitBtn.addEventListener('click', async function () {
                await submitCreateItemForm(this, form);
            });
        }

        function initializeSpecRow(rowElement) {
            const attrSelectEl = rowElement.querySelector('.spec-attribute');
            const valSelectEl = rowElement.querySelector('.spec-value');
            if (!attrSelectEl || !valSelectEl) return null;

            let valueTomSelect;

            const attrTomSelect = new TomSelect(attrSelectEl, {
                options: allSpecAttributes.map(attr => ({
                    value: attr.id,
                    text: attr.name,
                    spec_values: attr.spec_values
                })),
                plugins: ['dropdown_input', 'clear_button'],
                create: async function (input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Attribute');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input,
                            spec_values: []
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah attribute baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                },
                onChange: function (selectedAttrId) {
                    if (valueTomSelect) {
                        valueTomSelect.clear();
                        valueTomSelect.clearOptions();
                        valueTomSelect.disable();
                        if (!selectedAttrId) return;

                        let selectedValues = [];
                        if (String(selectedAttrId).startsWith('new::')) {
                            selectedValues = [];
                        } else {
                            const attributeData = this.options[selectedAttrId];
                            if (attributeData && attributeData.spec_values) {
                                selectedValues = attributeData.spec_values.map(val => ({
                                    value: val.id,
                                    text: val.value
                                }));
                            }
                        }
                        valueTomSelect.addOptions(selectedValues);
                        valueTomSelect.enable();
                        valueTomSelect.open();
                    }
                }
            });

            valueTomSelect = new TomSelect(valSelectEl, {
                plugins: ['dropdown_input', 'clear_button'],
                create: async function (input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Value');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah value baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                },
            });
            valueTomSelect.disable();

            return {
                attr: attrTomSelect,
                val: valueTomSelect
            };
        }

        async function submitCreateItemForm(submitBtn, form) {
            showLoading('Menyimpan Item...');
            submitBtn.disabled = true;

            const formData = {
                is_component: form.querySelector('input[name="is_component"]:checked').value,
                name: document.getElementById('name').value,
                serial_code: document.getElementById('serial_code').value,
                condition: form.querySelector('input[name="condition"]:checked').value,
                type: tomSelectType.getValue(),
                produced_at: document.getElementById('produced_at').value,
                _token: form.querySelector('input[name="_token"]').value,
                specifications: [],
                new_components: []
            };

            tomSelectInstances.mainSpecs.forEach(spec => {
                const attrVal = spec.attr.getValue();
                const valVal = spec.val.getValue();
                if (attrVal && valVal) {
                    formData.specifications.push({
                        attribute: attrVal,
                        value: valVal
                    });
                }
            });

            if (formData.is_component === '0') {
                tomSelectInstances.newComponents.forEach(compInstance => {
                    const compRow = compInstance.row;
                    const componentData = {
                        name: compRow.querySelector('.new-component-name').value,
                        serial_code: compRow.querySelector('.new-component-serial').value,
                        condition: compRow.querySelector('.new-component-condition:checked').value,
                        type: compInstance.typeSelect.getValue(),
                        produced_at: compRow.querySelector('.new-component-produced-at').value,
                        specifications: []
                    };
                    compInstance.specInstances.forEach(spec => {
                        const attrVal = spec.attr.getValue();
                        const valVal = spec.val.getValue();
                        if (attrVal && valVal) {
                            componentData.specifications.push({
                                attribute: attrVal,
                                value: valVal
                            });
                        }
                    });
                    formData.new_components.push(componentData);
                });
            }

            try {
                const response = await fetch(form.dataset.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        throw new Error(data.message || 'Data tidak valid.');
                    }
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }

                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                }).then(() => {
                    location.reload();
                });

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal Menyimpan', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }

        async function submitCreateSetForm(submitBtn, form) {
            const attachLabCheckbox = document.getElementById('attach_to_lab_checkbox');
            const attachDeskCheckbox = document.getElementById('attach_to_desk_checkbox');

            if (attachLabCheckbox.checked && !tomSelectSetLab.getValue()) {
                Swal.fire('Validasi Gagal', 'Silakan pilih laboratorium terlebih dahulu.', 'warning');
                return;
            }

            if (attachDeskCheckbox.checked && setSelectedDeskLocations.length !== 1) {
                Swal.fire('Validasi Gagal', 'Anda harus memilih TEPAT 1 MEJA untuk memasang set ini.', 'warning');
                return;
            }

            showLoading('Memproses...', 'Sedang membuat Set Item...');
            submitBtn.disabled = true;

            const formData = {
                set_name: document.getElementById('set_name').value,
                set_note: document.getElementById('set_note').value,
                _token: form.querySelector('input[name="_token"]').value,
                items: [],
                set_count: 0
            };

            if (attachLabCheckbox.checked) {
                formData.lab_id = tomSelectSetLab.getValue();
                if (attachDeskCheckbox.checked) {
                    formData.attach_to_desk = true;
                    formData.desk_location = setSelectedDeskLocations[0];
                } else {
                    formData.attach_to_lab = true;
                }
            }
            setItemTomInstances.forEach(itemInstance => {
                const itemRow = itemInstance.row;
                const itemData = {
                    is_component: '0',
                    name: itemRow.querySelector('.set-item-name').value,
                    serial_code: itemRow.querySelector('.set-item-serial').value,
                    condition: itemRow.querySelector('.set-item-condition:checked').value,
                    produced_at: itemRow.querySelector('.set-item-produced-at').value,
                    type: itemInstance.typeSelect.getValue(),
                    specifications: [],
                    new_components: []
                };

                // Specs
                itemInstance.mainSpecs.forEach(spec => {
                    const attrVal = spec.attr.getValue();
                    const valVal = spec.val.getValue();
                    if (attrVal && valVal) {
                        itemData.specifications.push({
                            attribute: attrVal,
                            value: valVal
                        });
                    }
                });

                // Components
                itemInstance.newComponents.forEach(compInstance => {
                    const compRow = compInstance.row;
                    const componentData = {
                        name: compRow.querySelector('.new-component-name').value,
                        serial_code: compRow.querySelector('.new-component-serial').value,
                        condition: compRow.querySelector('.new-component-condition:checked').value,
                        type: compInstance.typeSelect.getValue(),
                        produced_at: compRow.querySelector('.new-component-produced-at').value,
                        specifications: []
                    };
                    compInstance.specInstances.forEach(spec => {
                        const attrVal = spec.attr.getValue();
                        const valVal = spec.val.getValue();
                        if (attrVal && valVal) {
                            componentData.specifications.push({
                                attribute: attrVal,
                                value: valVal
                            });
                        }
                    });
                    itemData.new_components.push(componentData);
                });
                formData.items.push(itemData);
            });
            formData.set_count = formData.items.length;

            try {
                const response = await fetch(form.dataset.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        throw new Error(data.message || 'Data tidak valid.');
                    }
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }

                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                }).then(() => {
                    location.reload();
                });

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal Membuat Set', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }

        function initializeCreateSetModal() {
            const modal = document.getElementById('create-set-modal');
            const form = document.getElementById('create-set-form');
            const submitBtn = document.getElementById('submit-set-btn');
            const closeBtn = document.getElementById('create-set-modal-close-btn');
            const cancelBtn = document.getElementById('create-set-modal-cancel-btn');
            const overlay = document.getElementById('create-set-modal-overlay');
            const itemsContainer = document.getElementById('set-items-container');

            if (!modal) return;

            const prefilledTypes = ['Monitor', 'Mouse', 'CPU', 'Keyboard'];

            window.openCreateSetModal = () => {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                form.reset();
                itemsContainer.innerHTML = '';

                setItemTomInstances.forEach(item => {
                    item.typeSelect.destroy();
                    item.mainSpecs.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                    item.newComponents.forEach(comp => {
                        comp.typeSelect.destroy();
                        comp.specInstances.forEach(spec => {
                            spec.attr.destroy();
                            spec.val.destroy();
                        });
                    });
                });

                setItemTomInstances = [];
                setItemComponentIndex = 0;

                for (let i = 0; i < 4; i++) {
                    addSetItemCard(i, prefilledTypes[i] || `Item ${i + 1}`);
                }
            }

            window.closeCreateSetModal = () => {
                modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();
            }

            closeBtn.addEventListener('click', window.closeCreateSetModal);
            cancelBtn.addEventListener('click', window.closeCreateSetModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) window.closeCreateSetModal();
            });

            submitBtn.addEventListener('click', async function () {
                await submitCreateSetForm(this, form);
            });

            const attachLabCheckbox = document.getElementById('attach_to_lab_checkbox');
            const attachDeskCheckbox = document.getElementById('attach_to_desk_checkbox');
            const labSection = document.getElementById('lab-selection-section');
            const deskSection = document.getElementById('desk-attachment-section');
            const setLabSelectorEl = document.getElementById('set-lab-selector');

            attachLabCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    labSection.classList.remove('hidden');
                    loadLabsForSetAttachment();
                } else {
                    labSection.classList.add('hidden');
                    attachDeskCheckbox.checked = false;
                    deskSection.classList.add('hidden');
                    setSelectedDeskLocations = [];
                    updateSetSelectedDesksDisplay();
                    if (tomSelectSetLab) tomSelectSetLab.clear();
                }
            });

            attachDeskCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    const selectedLab = tomSelectSetLab.getValue();
                    if (!selectedLab) {
                        Swal.fire('Perhatian', 'Pilih laboratorium terlebih dahulu.', 'warning');
                        this.checked = false;
                        return;
                    }
                    deskSection.classList.remove('hidden');
                    fetchDeskMapForSet(selectedLab);
                } else {
                    deskSection.classList.add('hidden');
                    setSelectedDeskLocations = [];
                    updateSetSelectedDesksDisplay();
                }
            });

            tomSelectSetLab = new TomSelect(setLabSelectorEl, {
                create: false,
                placeholder: 'Pilih Lab...',
                onChange: (labId) => {
                    if (labId) {
                        setSelectedDeskLocations = [];
                        updateSetSelectedDesksDisplay();
                        if (attachDeskCheckbox.checked) {
                            fetchDeskMapForSet(labId);
                        }
                    }
                }
            });
        }

        function addSetItemCard(index, prefilledTypeName) {
            const itemsContainer = document.getElementById('set-items-container');
            const itemTemplate = document.getElementById('set-item-template');

            const newItemCard = itemTemplate.content.cloneNode(true).firstElementChild;
            newItemCard.dataset.itemIndex = index;

            newItemCard.querySelector('.set-item-title').textContent = `Item ${index + 1}: ${prefilledTypeName}`;
            newItemCard.querySelectorAll('.set-item-condition').forEach(radio => {
                radio.name = `set_item_condition_${index}`;
            });

            const typeSelectEl = newItemCard.querySelector('.set-item-type-select');
            const addSpecBtn = newItemCard.querySelector('.btn-add-set-item-spec');
            const specContainer = newItemCard.querySelector('.set-item-specs-container');
            const addCompBtn = newItemCard.querySelector('.btn-add-set-item-comp');
            const compContainer = newItemCard.querySelector('.set-item-components-container');

            const typeId = findTypeIdByName(prefilledTypeName);
            const typeTomSelect = new TomSelect(typeSelectEl, {
                options: allTypes.map(type => ({
                    value: type.id,
                    text: type.name
                })),
                plugins: ['dropdown_input', 'clear_button'],
                create: async function (input, callback) {
                    const isConfirmed = await confirmCreate(input, 'Type');
                    if (isConfirmed) {
                        const newValue = `new::${input}`;
                        callback({
                            value: newValue,
                            text: input
                        });
                    } else {
                        callback();
                    }
                },
                render: {
                    option_create: (data, escape) =>
                        `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                }
            });
            if (typeId) {
                typeTomSelect.setValue(typeId);
            }

            const itemInstanceData = {
                itemIndex: index,
                row: newItemCard,
                typeSelect: typeTomSelect,
                mainSpecs: [],
                newComponents: []
            };

            addSpecBtn.addEventListener('click', () => {
                const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                    .firstElementChild;
                specContainer.appendChild(newSpecRow);
                const specTomSelects = initializeSpecRow(newSpecRow);
                if (!specTomSelects) return;

                const specInstance = {
                    row: newSpecRow,
                    attr: specTomSelects.attr,
                    val: specTomSelects.val
                };
                itemInstanceData.mainSpecs.push(specInstance);

                newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                    specTomSelects.attr.destroy();
                    specTomSelects.val.destroy();
                    newSpecRow.remove();
                    itemInstanceData.mainSpecs = itemInstanceData.mainSpecs.filter(inst => inst.row !==
                        newSpecRow);
                });
            });

            addCompBtn.addEventListener('click', () => {
                const newCompRow = document.getElementById('new-component-form-template').content.cloneNode(true)
                    .firstElementChild;
                const compIndex = setItemComponentIndex++;

                newCompRow.dataset.compIndex = compIndex;
                newCompRow.querySelectorAll('.new-component-condition').forEach(radio => {
                    radio.name = `set_item_${index}_component_condition_${compIndex}`;
                });

                const compTypeSelectEl = newCompRow.querySelector('.new-component-type-select');
                const addCompSpecBtn = newCompRow.querySelector('.btn-add-new-comp-spec');
                const newCompSpecContainer = newCompRow.querySelector('.new-component-specs-container');
                const removeCompBtn = newCompRow.querySelector('.remove-new-component-btn');

                const compTypeTomSelect = new TomSelect(compTypeSelectEl, {
                    options: allTypes.map(type => ({
                        value: type.id,
                        text: type.name
                    })),
                    plugins: ['dropdown_input', 'clear_button'],
                    create: async function (input, callback) {
                        const isConfirmed = await confirmCreate(input, 'Type');
                        if (isConfirmed) {
                            const newValue = `new::${input}`;
                            callback({
                                value: newValue,
                                text: input
                            });
                        } else {
                            callback();
                        }
                    },
                    render: {
                        option_create: (data, escape) =>
                            `<div class="create">Tambah type baru: <strong>${escape(data.input)}</strong>&hellip;</div>`,
                    }
                });

                const componentInstanceData = {
                    id: compIndex,
                    row: newCompRow,
                    typeSelect: compTypeTomSelect,
                    specInstances: []
                };
                itemInstanceData.newComponents.push(componentInstanceData);

                addCompSpecBtn.addEventListener('click', () => {
                    const newSpecRow = document.getElementById('spec-row-template').content.cloneNode(true)
                        .firstElementChild;
                    newCompSpecContainer.appendChild(newSpecRow);
                    const specTomSelects = initializeSpecRow(newSpecRow);
                    if (!specTomSelects) return;

                    const specInstance = {
                        row: newSpecRow,
                        attr: specTomSelects.attr,
                        val: specTomSelects.val
                    };
                    componentInstanceData.specInstances.push(specInstance);

                    newSpecRow.querySelector('.remove-spec-btn').addEventListener('click', () => {
                        specTomSelects.attr.destroy();
                        specTomSelects.val.destroy();
                        newSpecRow.remove();
                        componentInstanceData.specInstances = componentInstanceData.specInstances
                            .filter(inst => inst.row !== newSpecRow);
                    });
                });

                removeCompBtn.addEventListener('click', () => {
                    compTypeTomSelect.destroy();
                    componentInstanceData.specInstances.forEach(spec => {
                        spec.attr.destroy();
                        spec.val.destroy();
                    });
                    newCompRow.remove();
                    itemInstanceData.newComponents = itemInstanceData.newComponents.filter(inst => inst
                        .id !== compIndex);
                });

                compContainer.appendChild(newCompRow);
            });

            setItemTomInstances.push(itemInstanceData);
            itemsContainer.appendChild(newItemCard);
        }

        async function submitCreateSetForm(submitBtn, form) {
            const attachCheckbox = document.getElementById('attach_to_desk_checkbox');
            const attachLabCheckbox = document.getElementById('attach_to_lab_checkbox');

            if (attachCheckbox.checked && setSelectedDeskLocations.length !== 1) {
                Swal.fire('Error', 'Anda harus memilih 1 meja untuk memasang set.', 'error');
                return;
            }

            if (attachLabCheckbox.checked && !tomSelectSetLab.getValue()) {
                Swal.fire('Error', 'Pilih laboratorium untuk menyimpan set.', 'error');
                return;
            }

            showLoading('Membuat Set Item...', 'Ini mungkin memakan waktu beberapa saat...');
            submitBtn.disabled = true;

            const formData = {
                set_name: document.getElementById('set_name').value,
                set_note: document.getElementById('set_note').value,
                _token: form.querySelector('input[name="_token"]').value,
                items: []
            };

            // Add desk attachment data if checkbox is checked
            if (attachCheckbox.checked) {
                formData.attach_to_desk = true;
                formData.lab_id = tomSelectSetLab.getValue();
                formData.desk_location = setSelectedDeskLocations[0];
            } else if (attachLabCheckbox.checked) {
                formData.attach_to_lab = true;
                formData.lab_id = tomSelectSetLab.getValue();
            }

            setItemTomInstances.forEach(itemInstance => {
                const itemRow = itemInstance.row;
                const itemData = {
                    is_component: '0',
                    name: itemRow.querySelector('.set-item-name').value,
                    serial_code: itemRow.querySelector('.set-item-serial').value,
                    condition: itemRow.querySelector('.set-item-condition:checked').value,
                    produced_at: itemRow.querySelector('.set-item-produced-at').value,
                    type: itemInstance.typeSelect.getValue(),
                    specifications: [],
                    new_components: []
                };

                itemInstance.mainSpecs.forEach(spec => {
                    const attrVal = spec.attr.getValue();
                    const valVal = spec.val.getValue();
                    if (attrVal && valVal) {
                        itemData.specifications.push({
                            attribute: attrVal,
                            value: valVal
                        });
                    }
                });

                itemInstance.newComponents.forEach(compInstance => {
                    const compRow = compInstance.row;
                    const componentData = {
                        name: compRow.querySelector('.new-component-name').value,
                        serial_code: compRow.querySelector('.new-component-serial').value,
                        condition: compRow.querySelector('.new-component-condition:checked').value,
                        type: compInstance.typeSelect.getValue(),
                        produced_at: compRow.querySelector('.new-component-produced-at').value,
                        specifications: []
                    };

                    compInstance.specInstances.forEach(spec => {
                        const attrVal = spec.attr.getValue();
                        const valVal = spec.val.getValue();
                        if (attrVal && valVal) {
                            componentData.specifications.push({
                                attribute: attrVal,
                                value: valVal
                            });
                        }
                    });
                    itemData.new_components.push(componentData);
                });

                formData.items.push(itemData);
            });

            formData.set_count = formData.items.length;

            try {
                const response = await fetch(form.dataset.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        throw new Error(data.message || 'Data tidak valid.');
                    }
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }

                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                }).then(() => {
                    location.reload();
                });

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal Membuat Set', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }

        // =================================================================
        // LOGIKA REPAIR (Updated sesuai Request)
        // =================================================================
        function initializeRepairModal() {
            const modal = document.getElementById('repair-modal');
            const overlay = document.getElementById('repair-modal-overlay');
            const closeBtn = document.getElementById('repair-modal-close-btn');
            const cancelBtn = document.getElementById('repair-modal-cancel-btn');
            const form = document.getElementById('repair-form');
            const submitBtn = document.getElementById('submit-repair-btn');

            if (!modal) return;

            const closeRepairModal = () => {
                modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();
            }

            closeBtn.addEventListener('click', closeRepairModal);
            cancelBtn.addEventListener('click', closeRepairModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeRepairModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeRepairModal();
            });

            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                submitRepairForm(form, submitBtn);
            });
        }

        async function openRepairModal() {
            const modal = document.getElementById('repair-modal');
            const titleEl = document.getElementById('repair-modal-title');
            const form = document.getElementById('repair-form');
            const componentsSection = document.getElementById('repair-components-section');
            const componentsList = document.getElementById('repair-components-list');
            const hiddenItemId = document.getElementById('repair-itemable-id');
            const hiddenIsComponent = document.getElementById('repair-is-component');

            if (!modal) return;

            form.reset();
            componentsList.innerHTML = '<div class="text-center py-4 text-gray-500">Memuat informasi komponen...</div>';

            hiddenItemId.value = currentItemId;
            hiddenIsComponent.value = currentIsComponent;

            titleEl.innerHTML = `Lapor Perbaikan: <span class="font-bold text-red-600">${currentItemName}</span>`;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            if (currentIsComponent == 0) {
                // Jika ITEM -> Tampilkan list anak (Read Only)
                componentsSection.classList.remove('hidden');
                await fetchItemComponentsForRepairReadOnly(currentItemId);
            } else {
                // Jika COMPONENT -> Sembunyikan section anak
                componentsSection.classList.add('hidden');
            }
        }

        async function fetchItemComponentsForRepairReadOnly(itemId) {
            const componentsList = document.getElementById('repair-components-list');
            if (!componentsList) return;

            try {
                const url = `/admin/items/${itemId}/details`;
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) throw new Error(`Gagal mengambil info komponen.`);
                const data = await response.json();

                if (data.components && data.components.length > 0) {
                    let html = '';
                    data.components.forEach(comp => {
                        const specHtml = (comp.spec_set_values && comp.spec_set_values.length > 0) ?
                            comp.spec_set_values.map(s =>
                                `${s.spec_attributes ? s.spec_attributes.name : 'Spec'}: ${s.value}`).join(
                                    ', ') :
                            '-';

                        html += `
                                        <div class="border border-gray-200 rounded p-2 bg-white flex flex-col text-sm">
                                            <div class="flex justify-between items-start">
                                                <span class="font-semibold text-gray-800">${comp.name}</span>
                                                <span class="text-xs font-mono text-gray-500 bg-gray-100 px-1 rounded">${comp.serial_code}</span>
                                            </div>
                                            <div class="flex justify-between items-end mt-1">
                                                <span class="text-xs text-purple-600">${comp.type ? comp.type.name : 'N/A'}</span>
                                                <span class="text-[10px] text-gray-400 max-w-[60%] truncate" title="${specHtml}">${specHtml}</span>
                                            </div>
                                        </div>
                                    `;
                    });
                    componentsList.innerHTML = html;
                } else {
                    componentsList.innerHTML =
                        '<p class="text-gray-400 italic text-center text-sm py-2">Item ini tidak memiliki komponen anak.</p>';
                }

            } catch (error) {
                console.error(error);
                componentsList.innerHTML =
                    `<p class="text-red-400 text-center text-sm py-2">Gagal memuat info komponen.</p>`;
            }
        }

        async function submitRepairForm(form, submitBtn) {
            showLoading('Mengirim Laporan...');
            submitBtn.disabled = true;

            const formData = new FormData(form);
            const description = formData.get('issue_description');
            const id = formData.get('itemable_id');
            const isComponent = parseInt(formData.get('is_component'), 10);

            let payload = {
                _token: csrfToken
            };

            if (isComponent === 1) {
                payload.components = [{
                    id: id,
                    issue_description: description
                }];
            } else {
                payload.items = [{
                    id: id,
                    issue_description: description
                }];
            }

            try {
                const actionUrl = form.dataset.action;
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        let msg = data.message;
                        if (data.errors) {
                            const keys = Object.keys(data.errors);
                            if (keys.length > 0) msg = data.errors[keys[0]][0];
                        }
                        throw new Error(msg || 'Data tidak valid.');
                    }
                    throw new Error(data.message || 'Terjadi kesalahan server.');
                }

                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message
                });

                document.getElementById('repair-modal').classList.add('hidden');
                document.getElementById('action-modal').classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal Mengirim', error.message, 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }

        // =================================================================
        // FUNGSI KELOLA KOMPONEN
        // =================================================================
        function initializeManageComponentsModal() {
            const modal = document.getElementById('manage-components-modal');
            const overlay = document.getElementById('manage-components-modal-overlay');
            const closeBtn = document.getElementById('manage-components-modal-close-btn');

            if (!modal) return;

            const closeModal = () => {
                modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();
            }

            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeModal();
            });
        }

        async function openManageComponentsModal() {
            const modal = document.getElementById('manage-components-modal');
            const itemNameEl = document.getElementById('manage-comp-item-name');

            if (itemNameEl) itemNameEl.textContent = currentItemName;
            if (modal) modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            await loadComponentsForItem();
        }

        async function loadComponentsForItem() {
            const attachedList = document.getElementById('attached-components-list');
            const availableList = document.getElementById('available-components-list');

            attachedList.innerHTML = '<div class="text-center py-8 text-gray-500">Memuat...</div>';
            availableList.innerHTML = '<div class="text-center py-8 text-gray-500">Memuat...</div>';

            try {
                const [detailsRes, availableRes] = await Promise.all([
                    fetch(`/admin/items/${currentItemId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    }),
                    fetch(`/admin/items/${currentItemId}/available-components`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                ]);

                const detailsData = await detailsRes.json();
                const availableData = await availableRes.json();

                renderAttachedComponents(detailsData.components || []);
                renderAvailableComponents(availableData.components || []);
            } catch (error) {
                attachedList.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat data</div>';
                availableList.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat data</div>';
            }
        }

        function renderAttachedComponents(components) {
            const list = document.getElementById('attached-components-list');
            if (!components || components.length === 0) {
                list.innerHTML = '<div class="text-center py-8 text-gray-400 italic">Tidak ada komponen terpasang</div>';
                return;
            }

            list.innerHTML = components.map(comp => `
                                <div class="border border-gray-200 rounded-lg p-3 bg-white hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">${comp.name}</div>
                                            <div class="text-xs text-gray-500 font-mono">${comp.serial_code}</div>
                                            <div class="text-xs text-purple-600 mt-1">${comp.type ? comp.type.name : 'N/A'}</div>
                                        </div>
                                        <button onclick="detachComponentFromItem('${comp.id}', '${comp.name}')" 
                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition">
                                            Lepas
                                        </button>
                                    </div>
                                </div>
                            `).join('');
        }

        function renderAvailableComponents(components) {
            const list = document.getElementById('available-components-list');
            if (!components || components.length === 0) {
                list.innerHTML = '<div class="text-center py-8 text-gray-400 italic">Tidak ada komponen tersedia</div>';
                return;
            }

            list.innerHTML = components.map(comp => `
                                <div class="border border-gray-200 rounded-lg p-3 bg-white hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="font-semibold text-gray-800">${comp.name}</div>
                                            <div class="text-xs text-gray-500 font-mono">${comp.serial_code}</div>
                                            <div class="text-xs text-purple-600 mt-1">${comp.type ? comp.type.name : 'N/A'}</div>
                                        </div>
                                        <button onclick="attachComponentToItem('${comp.id}', '${comp.name}')" 
                                            class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded transition">
                                            Pasang
                                        </button>
                                    </div>
                                </div>
                            `).join('');
        }

        async function attachComponentToItem(componentId, componentName) {
            const result = await Swal.fire({
                title: 'Pasang Komponen?',
                text: `Pasang '${componentName}' ke '${currentItemName}'?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pasang!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            showLoading('Memasang komponen...');
            try {
                const response = await fetch(`/admin/items/${currentItemId}/attach-component/${componentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memasang komponen');

                hideLoading();
                showToast('Berhasil!', data.message, 'success');
                await loadComponentsForItem();
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        async function detachComponentFromItem(componentId, componentName) {
            const result = await Swal.fire({
                title: 'Lepas Komponen?',
                text: `Lepas '${componentName}' dari '${currentItemName}'?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444'
            });

            if (!result.isConfirmed) return;

            showLoading('Melepas komponen...');
            try {
                const response = await fetch(`/admin/items/${currentItemId}/detach-component/${componentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal melepas komponen');

                hideLoading();
                showToast('Berhasil!', data.message, 'success');
                await loadComponentsForItem();
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        function initializeClientSideFiltering() {
            console.log("Inisialisasi Filter Client Side..."); // Debug check

            // Definisi Element Filter
            const els = {
                lab: document.getElementById('filter_lab_select'),
                type: document.getElementById('filter_type_select'),
                status: document.getElementById('filter_status'),
                condition: document.getElementById('filter_condition'),
                specAttr: document.getElementById('filter_spec_attr'),
                specVal: document.getElementById('filter_spec_val'),
                search: document.getElementById('datatable-search-input'),
                resetBtn: document.getElementById('reset-filter-btn')
            };

            // Objek untuk menyimpan instance TomSelect
            const tomSelects = {};

            // 1. Inisialisasi TomSelect Dasar
            ['lab', 'type', 'status', 'condition'].forEach(key => {
                if (els[key]) {
                    if (els[key].tomselect) {
                        tomSelects[key] = els[key].tomselect;
                    } else {
                        tomSelects[key] = new TomSelect(els[key], {
                            plugins: ['clear_button'],
                            onChange: filterTable
                        });
                    }
                }
            });

            // 2. Inisialisasi TomSelect Spec (Value disabled dulu)
            if (els.specVal) {
                if (els.specVal.tomselect) {
                    tomSelects.specVal = els.specVal.tomselect;
                } else {
                    tomSelects.specVal = new TomSelect(els.specVal, {
                        plugins: ['clear_button'],
                        onChange: filterTable
                    });
                }
                tomSelects.specVal.disable();
            }

            if (els.specAttr) {
                let attrTs;
                if (els.specAttr.tomselect) {
                    attrTs = els.specAttr.tomselect;
                } else {
                    attrTs = new TomSelect(els.specAttr, {
                        plugins: ['clear_button'],
                        onChange: handleAttrChange
                    });
                }
                tomSelects.specAttr = attrTs;

                // Setup event listener manual jika instance sudah ada atau baru dibuat
                attrTs.on('change', handleAttrChange);

                function handleAttrChange(attrId) {
                    if (tomSelects.specVal) {
                        tomSelects.specVal.clear();
                        tomSelects.specVal.clearOptions();
                        tomSelects.specVal.disable();

                        if (attrId) {
                            const rawOption = els.specAttr.querySelector(`option[value="${attrId}"]`);
                            if (rawOption && rawOption.dataset.values) {
                                try {
                                    const values = JSON.parse(rawOption.dataset.values);
                                    const options = values.map(v => ({ value: v.id, text: v.value }));
                                    tomSelects.specVal.addOptions(options);
                                    tomSelects.specVal.enable();
                                } catch (e) {
                                    console.error("Gagal parse spec values", e);
                                }
                            }
                        }
                    }
                    filterTable();
                }
            }

            // 3. Event Listener Search Input
            if (els.search) {
                els.search.addEventListener('input', filterTable);
            }

            // =========================================================
            // 4. LOGIKA RESET (Debugged Version)
            // =========================================================
            if (els.resetBtn) {
                console.log("Tombol Reset Ditemukan, memasang listener..."); // Debug check

                els.resetBtn.addEventListener('click', (e) => {
                    e.preventDefault(); 

                    // A. Clear Search Text
                    if (els.search) els.search.value = '';

                    // B. Clear TomSelects
                    Object.values(tomSelects).forEach(ts => {
                        if (ts) ts.clear();
                    });

                    // C. Disable Spec Value
                    if (tomSelects.specVal) tomSelects.specVal.disable();

                    // D. Reset DataTable Search (Gunakan Try-Catch agar tidak stop jika error)
                    try {
                        const itemDtEl = document.getElementById('items-datatable');
                        const compDtEl = document.getElementById('components-datatable');

                        if (itemDtEl && window.te && window.te.Datatable) {
                            const inst = window.te.Datatable.getInstance(itemDtEl);
                            if (inst) inst.search('');
                        }
                        if (compDtEl && window.te && window.te.Datatable) {
                            const inst = window.te.Datatable.getInstance(compDtEl);
                            if (inst) inst.search('');
                        }
                    } catch (err) {
                        console.warn('DataTable reset warning (diabaikan):', err);
                    }

                    // E. Jalankan Filter Ulang (Harusnya menampilkan semua data)
                    filterTable();
                });
            } else {
                console.error("Tombol Reset TIDAK Ditemukan di DOM");
            }

            // 5. Fungsi Utama Filter Table
            function filterTable() {
                const criteria = {
                    labId: els.lab ? els.lab.value : '',
                    typeId: els.type ? els.type.value : '',
                    status: els.status ? els.status.value : '',
                    condition: els.condition ? els.condition.value : '',
                    specAttrId: els.specAttr ? els.specAttr.value : '',
                    specValId: els.specVal ? els.specVal.value : '',
                    search: els.search ? els.search.value.toLowerCase() : ''
                };

                applyFilterToSelector('.item-row', criteria);
                applyFilterToSelector('.component-row', criteria);
            }

            function applyFilterToSelector(selector, c) {
                const rows = document.querySelectorAll(selector);
                let visibleCount = 0;

                rows.forEach(row => {
                    const rData = row.dataset;
                    let show = true;

                    if (c.labId && rData.labId != c.labId) show = false;
                    if (show && c.typeId && rData.typeId != c.typeId) show = false;
                    if (show && c.status && rData.status && rData.status !== c.status) show = false;

                    if (show && c.condition) {
                        if (c.condition === 'under_repair') {
                            if (rData.condition !== 'under_repair') show = false;
                        } else if (c.condition === 'parent_under_repair') {
                            if (rData.condition !== 'parent_under_repair') show = false;
                        } else {
                            if (rData.condition !== c.condition) show = false;
                        }
                    }

                    if (show && c.specAttrId) {
                        let hasSpec = false;
                        try {
                            const specs = JSON.parse(rData.specs || '[]');
                            hasSpec = specs.some(s => {
                                if (c.specValId) {
                                    return s.attr_id == c.specAttrId && s.val_id == c.specValId;
                                } else {
                                    return s.attr_id == c.specAttrId;
                                }
                            });
                        } catch (e) { }
                        if (!hasSpec) show = false;
                    }

                    if (show && c.search) {
                        if (!rData.search.includes(c.search)) show = false;
                    }

                    row.style.display = show ? '' : 'none';
                    if (show) visibleCount++;
                });

                // Handle "No Items Found" row
                const type = selector.includes('item') ? 'items' : 'components';
                const emptyRow = document.getElementById(`no-${type}-row`);
                if (emptyRow) {
                    // Tampilkan baris kosong jika visibleCount 0
                    if (visibleCount === 0) {
                        emptyRow.style.display = '';
                    } else {
                        emptyRow.style.display = 'none';
                    }
                }
            }
        }

        // =================================================================
        // Inisialisasi Halaman
        // =================================================================

        function initializeActionModal() {
            const modal = document.getElementById('action-modal');
            const overlay = document.getElementById('action-modal-overlay');
            const closeBtn = document.getElementById('action-modal-close-btn');

            if (!modal) return;

            const closeActionModal = () => {
                modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();

                currentItemId = null;
                currentItemName = null;
                currentItemCondition = null;
                currentItemType = 'item';
                currentIsComponent = 0;
                currentDeskId = null;
                currentItemIdParent = null;
                currentLabId = null;
            }

            closeBtn.addEventListener('click', closeActionModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeActionModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeActionModal();
            });

            document.getElementById('action-btn-details').addEventListener('click', openDetailModal);
            document.getElementById('action-btn-attach').addEventListener('click', openAttachDeskModal);
            document.getElementById('action-btn-attach-lab').addEventListener('click', openAttachLabModal);
            document.getElementById('action-btn-condition').addEventListener('click', submitChangeCondition);
            document.getElementById('action-btn-repair').addEventListener('click', openRepairModal);

            document.getElementById('action-btn-manage-comp').addEventListener('click', openManageComponentsModal);
            document.getElementById('action-btn-edit-comp').addEventListener('click', () => {
                Swal.fire('Fitur Dalam Pengembangan',
                    'Fitur untuk mengubah kondisi komponen sedang dibuat.', 'info');
            });

            document.getElementById('action-btn-detach-desk').addEventListener('click', async () => {
                const result = await Swal.fire({
                    title: 'Lepas dari Meja?',
                    text: `Item '${currentItemName}' akan dilepas dari meja.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lepas!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ef4444'
                });
                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(`/admin/items/${currentItemId}/detach-desk`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal melepas item');
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } catch (error) {
                    Swal.fire('Gagal', error.message, 'error');
                }
            });

            document.getElementById('action-btn-detach-lab').addEventListener('click', async () => {
                const url = currentItemType === 'item' ?
                    `/admin/items/${currentItemId}/detach-lab` :
                    `/admin/components/${currentItemId}/detach-lab`;

                const result = await Swal.fire({
                    title: 'Lepas dari Lab?',
                    text: `${currentItemType === 'item' ? 'Item' : 'Component'} '${currentItemName}' akan dilepas dari lemari lab.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lepas!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#6366f1'
                });
                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal melepas');
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } catch (error) {
                    Swal.fire('Gagal', error.message, 'error');
                }
            });

            document.getElementById('action-btn-detach-item').addEventListener('click', async () => {
                const result = await Swal.fire({
                    title: 'Lepas dari Item?',
                    text: `Component '${currentItemName}' akan dilepas dari item induk.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lepas!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#a855f7'
                });
                if (!result.isConfirmed) return;

                try {
                    const response = await fetch(`/admin/components/${currentItemId}/detach-item`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal melepas component');
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } catch (error) {
                    Swal.fire('Gagal', error.message, 'error');
                }
            });
        }

        function updateActionModalButtons(type = 'item') {
            const attachBtn = document.getElementById('action-btn-attach');
            const attachLabBtn = document.getElementById('action-btn-attach-lab');
            const manageCompBtn = document.getElementById('action-btn-manage-comp');
            const editCompBtn = document.getElementById('action-btn-edit-comp');
            const condBtn = document.getElementById('action-btn-condition');
            const detailBtn = document.getElementById('action-btn-details');
            const repairBtn = document.getElementById('action-btn-repair');
            const detachDeskBtn = document.getElementById('action-btn-detach-desk');
            const detachLabBtn = document.getElementById('action-btn-detach-lab');
            const detachItemBtn = document.getElementById('action-btn-detach-item');

            if (!attachBtn) return;

            detailBtn.style.display = 'flex';
            repairBtn.style.display = 'flex';
            detachDeskBtn.style.display = 'none';
            detachLabBtn.style.display = 'none';
            detachItemBtn.style.display = 'none';

            // Default: tampilkan tombol attach lab
            attachLabBtn.style.display = 'flex';

            if (currentItemCondition) {
                condBtn.innerHTML =
                    `<svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg><span class="font-semibold">Ubah ke Rusak</span>`;
                condBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                condBtn.classList.add('bg-yellow-500', 'hover:bg-yellow-600');
            } else {
                condBtn.innerHTML =
                    `<svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="font-semibold">Ubah ke Baik</span>`;
                condBtn.classList.remove('bg-yellow-500', 'hover:bg-yellow-600');
                condBtn.classList.add('bg-green-500', 'hover:bg-green-600');
            }

            if (type === 'item') {
                attachBtn.style.display = 'flex';
                manageCompBtn.style.display = 'flex';
                editCompBtn.style.display = 'none';
                currentIsComponent = 0;

                // Logika untuk Item
                if (currentDeskId) {
                    // Item terpasang di desk
                    attachLabBtn.style.display = 'none';
                    detachLabBtn.style.display = 'none';
                    detachDeskBtn.style.display = 'flex';
                } else if (currentLabId) {
                    // Item terpasang di lab (tidak di desk)
                    attachLabBtn.style.display = 'none';
                    detachLabBtn.style.display = 'flex';
                    detachDeskBtn.style.display = 'none';
                } else {
                    // Item belum terpasang
                    attachLabBtn.style.display = 'flex';
                    detachLabBtn.style.display = 'none';
                    detachDeskBtn.style.display = 'none';
                }
            } else {
                attachBtn.style.display = 'none';
                manageCompBtn.style.display = 'none';
                editCompBtn.style.display = 'flex';
                currentIsComponent = 1;

                // Logika untuk Component
                if (currentItemIdParent) {
                    // Component terpasang di item
                    attachLabBtn.style.display = 'none';
                    detachLabBtn.style.display = 'none';
                    detachItemBtn.style.display = 'flex';
                } else if (currentLabId) {
                    // Component terpasang di lab (tidak di item)
                    attachLabBtn.style.display = 'none';
                    detachLabBtn.style.display = 'flex';
                    detachItemBtn.style.display = 'none';
                } else {
                    // Component belum terpasang
                    attachLabBtn.style.display = 'flex';
                    detachLabBtn.style.display = 'none';
                    detachItemBtn.style.display = 'none';
                }
            }
        }

        async function openAttachLabModal() {
            const result = await Swal.fire({
                title: 'Pilih Laboratorium',
                html: '<select id="swal-lab-select" class="swal2-input" style="width: 80%; padding:0"><option value="">-- Pilih Lab --</option></select>',
                showCancelButton: true,
                confirmButtonText: 'Pasang',
                cancelButtonText: 'Batal',
                didOpen: async () => {
                    const select = document.getElementById('swal-lab-select');
                    try {
                        const response = await fetch("{{ route('admin.labs.list') }}");
                        const labs = await response.json();
                        labs.forEach(lab => {
                            const option = document.createElement('option');
                            option.value = lab.id;
                            option.textContent = lab.name;
                            select.appendChild(option);
                        });
                    } catch (error) {
                        console.error('Error loading labs:', error);
                    }
                },
                preConfirm: () => {
                    const labId = document.getElementById('swal-lab-select').value;
                    if (!labId) {
                        Swal.showValidationMessage('Silakan pilih laboratorium');
                        return false;
                    }
                    return labId;
                }
            });

            if (result.isConfirmed && result.value) {
                await attachToLab(result.value);
            }
        }

        async function attachToLab(labId) {
            showLoading('Memasang ke Lab...');
            try {
                const url = currentItemType === 'item' ?
                    `/admin/items/${currentItemId}/attach-lab/${labId}` :
                    `/admin/components/${currentItemId}/attach-lab/${labId}`;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memasang ke lab.');

                hideLoading();
                showToast('Berhasil!', data.message, 'success');

                document.getElementById('action-modal').classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();

                setTimeout(() => location.reload(), 1000);

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        async function submitChangeCondition() {
            const newConditionText = currentItemCondition ? "Rusak" : "Baik";
            const result = await Swal.fire({
                title: `Ubah Kondisi?`,
                text: `Anda yakin ingin mengubah kondisi '${currentItemName}' menjadi ${newConditionText}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            showLoading('Mengubah Kondisi...');
            try {
                const url = (currentItemType === 'item') ?
                    `/admin/items/${currentItemId}/condition` :
                    `/admin/components/${currentItemId}/condition`;

                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                });
                const data = await response.json();

                if (!response.ok) {
                    if (data.has_ongoing_repair) {
                        hideLoading();
                        await handleOngoingRepairError(data);
                        return;
                    }
                    throw new Error(data.message || 'Gagal update.');
                }

                hideLoading();
                showToast('Berhasil', data.message, 'success');

                const actionButton = (currentItemType === 'item') ?
                    document.querySelector(`.btn-open-action-item[data-item-id="${currentItemId}"]`) :
                    document.querySelector(`.btn-open-action-component[data-item-id="${currentItemId}"]`);

                if (actionButton) {
                    const row = actionButton.closest('tr');
                    if (row) {
                        actionButton.dataset.itemCondition = data.new_condition ? '1' : '0';
                        const badge = row.querySelector('.condition-badge');
                        if (badge) {
                            if (data.new_condition) {
                                badge.textContent = 'Baik';
                                badge.className =
                                    'condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800';
                            } else {
                                badge.textContent = 'Rusak';
                                badge.className =
                                    'condition-badge px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800';
                            }
                        }
                    }
                }

                document.getElementById('action-modal').classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        async function handleOngoingRepairError(data) {
            const repairData = data.repair_data;
            const result = await Swal.fire({
                title: 'Repair Sedang Berjalan',
                html: `
                                    <div class="text-left space-y-3">
                                        <p class="text-gray-700">${data.message}</p>
                                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                            <p class="text-sm font-semibold text-blue-800 mb-1">Masalah:</p>
                                            <p class="text-sm text-gray-700">${repairData.issue_description}</p>
                                        </div>
                                        <p class="text-sm text-gray-600">Pilih tindakan:</p>
                                    </div>
                                `,
                icon: 'warning',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Selesaikan Repair',
                denyButtonText: 'Lihat Halaman Repairs',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                denyButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280'
            });

            if (result.isConfirmed) {
                await showCompleteRepairForm(repairData);
            } else if (result.isDenied) {
                window.location.href = repairData.repair_url;
            }
        }

        async function showCompleteRepairForm(repairData) {
            const {
                value: formValues
            } = await Swal.fire({
                title: 'Selesaikan Repair',
                html: `
                                    <div class="text-left space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Hasil Perbaikan</label>
                                            <div class="flex gap-4 justify-center">
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" name="swal-is-successful" value="1" class="mr-2" checked>
                                                    <span class="text-sm">Berhasil (Baik)</span>
                                                </label>
                                                <label class="flex items-center cursor-pointer">
                                                    <input type="radio" name="swal-is-successful" value="0" class="mr-2">
                                                    <span class="text-sm">Gagal (Rusak)</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Teknisi</label>
                                            <textarea id="swal-repair-notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg" rows="3" placeholder="Contoh: Kabel diganti baru..."></textarea>
                                        </div>
                                    </div>
                                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const isSuccessful = document.querySelector('input[name="swal-is-successful"]:checked')
                        .value === '1';
                    const notes = document.getElementById('swal-repair-notes').value;
                    return {
                        isSuccessful,
                        notes
                    };
                }
            });

            if (formValues) {
                await submitCompleteRepair(repairData, formValues);
            }
        }

        async function submitCompleteRepair(repairData, formValues) {
            showLoading('Menyelesaikan Repair...');
            try {
                const url = (currentItemType === 'item') ?
                    `/admin/items/${currentItemId}/complete-repair` :
                    `/admin/components/${currentItemId}/complete-repair`;

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        repair_id: repairData.repair_id,
                        is_successful: formValues.isSuccessful,
                        repair_notes: formValues.notes
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal menyelesaikan repair.');

                hideLoading();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        function initializeDeskMapperModal() {
            const modal = document.getElementById('attach-desk-modal');
            const overlay = document.getElementById('attach-desk-modal-overlay');
            const closeBtn = document.getElementById('attach-desk-modal-close-btn');
            const labSelectorEl = document.getElementById('lab-selector-modal');

            if (!modal) return;

            tomSelectLabModal = new TomSelect(labSelectorEl, {
                create: false,
                placeholder: 'Pilih Lab...',
                onChange: (labId) => {
                    if (labId) fetchDeskMapForModal(labId);
                }
            });

            const closeAttachModal = () => {
                modal.classList.add('hidden');
                if (tomSelectLabModal) {
                    tomSelectLabModal.clear();
                }
                const gridContainer = document.getElementById('desk-grid-container-modal');
                if (gridContainer) {
                    gridContainer.innerHTML =
                        '<div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah.</div>';
                }
                checkModalStateAndToggleBodyOverflow();
            }

            closeBtn.addEventListener('click', closeAttachModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeAttachModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeAttachModal();
            });
        }

        async function openAttachDeskModal() {
            const itemNameEl = document.getElementById('attach-item-name');
            const modal = document.getElementById('attach-desk-modal');

            if (itemNameEl) itemNameEl.textContent = currentItemName || '';
            if (modal) modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            showLoading('Memuat Daftar Lab...');
            try {
                const response = await fetch("{{ route('admin.labs.list') }}");
                if (!response.ok) throw new Error('Gagal memuat daftar lab.');
                const labs = await response.json();

                if (tomSelectLabModal) {
                    tomSelectLabModal.clearOptions();
                    tomSelectLabModal.addOptions(labs.map(lab => ({
                        value: lab.id,
                        text: lab.name
                    })));
                    hideLoading();
                    tomSelectLabModal.open();
                }

            } catch (error) {
                hideLoading();
                Swal.fire('Error', error.message, 'error');
                if (modal) modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();
            }
        }

        async function fetchDeskMapForModal(labId) {
            const container = document.getElementById('desk-grid-container-modal');
            if (!container) return;

            container.innerHTML =
                `<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>`;

            try {
                const response = await fetch(`/admin/labs/${labId}/desks`);
                if (!response.ok) throw new Error('Gagal memuat denah meja.');
                modalLabDesks = await response.json();

                let maxRow = 5,
                    maxCol = 10;
                if (modalLabDesks.length > 0) {
                    modalLabDesks.forEach(d => {
                        const row = d.location.charCodeAt(0) - 64;
                        const col = parseInt(d.location.substring(1));
                        if (row > maxRow) maxRow = row;
                        if (col > maxCol) maxCol = col;
                    });
                }
                renderDeskGridModal(modalLabDesks, maxRow, maxCol);
            } catch (error) {
                container.innerHTML = `<div class="text-center py-12 text-red-500">${error.message}</div>`;
                showToast('Gagal Memuat Denah', error.message, 'error');
            }
        }

        function renderDeskGridModal(desks, maxRows, maxCols) {
            const container = document.getElementById('desk-grid-container-modal');
            let containerHTML =
                `<div class="overflow-x-auto pb-4">
                        <div id="desk-grid-modal" class="grid gap-4 border-2 min-w-fit border-slate-300 p-8" 
                             style="grid-template-columns: repeat(${maxCols}, minmax(140px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;

            const occupiedSlots = new Set(desks.map(d => d.location));

            desks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                let bgColorClass, iconColor, conditionText;

                switch (desk.overall_condition) {
                    case 'item_rusak':
                    case 'component_rusak':
                        bgColorClass = 'bg-rose-50 border-rose-300 hover:bg-rose-100';
                        iconColor = 'text-rose-600';
                        conditionText = 'Rusak';
                        break;
                    case 'item_tidak_lengkap':
                        bgColorClass = 'bg-amber-50 border-amber-300 hover:bg-amber-100';
                        iconColor = 'text-amber-600';
                        conditionText = 'Tidak Lengkap';
                        break;
                    case 'item_kosong':
                        bgColorClass = 'bg-gray-50 border-gray-300 hover:bg-gray-100';
                        iconColor = 'text-gray-500';
                        conditionText = 'Kosong';
                        break;
                    case 'Baik':
                    default:
                        bgColorClass = 'bg-emerald-50 border-emerald-300 hover:bg-emerald-100';
                        iconColor = 'text-emerald-600';
                        conditionText = 'Baik';
                        break;
                }

                containerHTML += `
                        <div data-desk-id="${desk.id}" data-desk-location="${desk.location}" 
                             style="grid-area: ${row} / ${col};" 
                             class="desk-item-modal group transition-all duration-200 flex flex-col items-center justify-center p-5 border-2 rounded-xl min-h-36 ${bgColorClass} cursor-pointer"
                             title="Klik untuk memasang item ke meja ${desk.location}">
                            <div class="text-center pointer-events-none">
                                <div class="mb-2"><svg class="w-8 h-8 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></div>
                                <span class="font-bold text-lg text-gray-800 block select-none">${desk.location}</span>
                                <span class="text-sm text-gray-600 mt-1 inline-block select-none">${conditionText}</span>
                            </div>
                        </div>`;
            });

            for (let r = 1; r <= maxRows; r++) {
                for (let c = 1; c <= maxCols; c++) {
                    const location = `${String.fromCharCode(64 + r)}${c}`;
                    if (!occupiedSlots.has(location)) {
                        containerHTML +=
                            `<div class="empty-slot-modal" style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                    }
                }
            }

            containerHTML += '</div></div>';
            container.innerHTML = containerHTML;

            document.querySelectorAll('.desk-item-modal').forEach(deskEl => {
                deskEl.addEventListener('click', () => {
                    confirmAttachToDesk(deskEl.dataset.deskId, deskEl.dataset.deskLocation);
                });
            });
        }

        async function confirmAttachToDesk(deskId, deskLocation) {
            const result = await Swal.fire({
                title: `Pasang item ke Meja ${deskLocation}?`,
                text: `Anda akan memasang '${currentItemName}' ke meja ${deskLocation}. Lanjutkan?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pasang!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            showLoading('Memasang Item...');

            try {
                const response = await fetch(`/admin/items/${currentItemId}/attach-desk/${deskId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memasang item.');

                hideLoading();
                showToast('Berhasil!', data.message, 'success');

                document.getElementById('attach-desk-modal').classList.add('hidden');
                document.getElementById('action-modal').classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();

                const actionButton = document.querySelector(
                    `.btn-open-action-item[data-item-id="${currentItemId}"]`);
                if (actionButton) {
                    const rowToRemove = actionButton.closest('tr');
                    if (rowToRemove) {
                        rowToRemove.style.transition = 'opacity 0.5s ease';
                        rowToRemove.style.opacity = '0';
                        setTimeout(() => {
                            rowToRemove.remove();
                            try {
                                const datatable = te.Datatable.getInstance(document.getElementById(
                                    'items-datatable'));
                                if (datatable) {
                                    datatable.search(document.getElementById('datatable-search-input')
                                        .value);
                                }
                            } catch (e) {
                                console.warn("Gagal refresh datatable instance:", e)
                            }
                        }, 500);
                    }
                }

            } catch (error) {
                hideLoading();
                Swal.fire('Gagal', error.message, 'error');
            }
        }

        function initializeDetailModal() {
            const modal = document.getElementById('detail-modal');
            const overlay = document.getElementById('detail-modal-overlay');
            const closeBtn = document.getElementById('detail-modal-close-btn');

            if (!modal) return;

            const closeDetailModal = () => {
                modal.classList.add('hidden');
                checkModalStateAndToggleBodyOverflow();
            }

            closeBtn.addEventListener('click', closeDetailModal);
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeDetailModal();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeDetailModal();
            });
        }

        async function openDetailModal() {
            const modal = document.getElementById('detail-modal');
            const titleEl = document.getElementById('detail-item-name');
            const contentEl = document.getElementById('detail-modal-content');
            if (!modal) return;

            titleEl.textContent = currentItemName;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            contentEl.innerHTML = `<div class="flex items-center justify-center py-12">
                                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
                                              </div>`;

            const url = (currentItemType === 'item') ?
                `/admin/items/${currentItemId}/details` :
                `/admin/components/${currentItemId}/details`;

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Gagal mengambil data. Status: ${response.status}`);
                }

                const data = await response.json();
                populateDetailModal(data);

            } catch (error) {
                contentEl.innerHTML = `<div class="text-center py-12 text-red-500">
                                                    <h3 class="font-semibold text-lg">Gagal Memuat Data</h3>
                                                    <p class="text-sm">${error.message}</p>
                                                  </div>`;
            }
        }

        function populateDetailModal(data) {
            const contentEl = document.getElementById('detail-modal-content');
            if (!contentEl) return;

            const conditionText = data.condition ?
                '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Baik</span>' :
                '<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rusak</span>';

            let locationHtml = '';
            let componentsHtml = '';
            const producedAt = data.produced_at ? new Date(data.produced_at).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }) : 'N/A';

            if (currentItemType === 'item') {
                if (data.desk) {
                    locationHtml = `
                            <dt class="col-span-1 font-semibold text-gray-800">Lokasi</dt>
                            <dd class="col-span-2 text-gray-600">${data.desk.lab.name} - Meja ${data.desk.location}</dd>
                        `;
                } else {
                    locationHtml = `
                            <dt class="col-span-1 font-semibold text-gray-800">Lokasi</dt>
                            <dd class="col-span-2 text-gray-500 italic">Belum terpasang</dd>
                        `;
                }
                if (data.components && data.components.length > 0) {
                    componentsHtml = `
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="text-md font-semibold text-gray-800 mb-2">Komponen Terpasang (${data.components.length}):</h4>
                                <ul class="list-disc pl-5 space-y-1">
                                    ${data.components.map(comp => `
                                                                                                                                                                                                                                <li class="text-sm text-gray-600">
                                                                                                                                                                                                                                    <strong>${comp.name}</strong> (${comp.serial_code})
                                                                                                                                                                                                                                    <span class="ml-2 px-1.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">${comp.type ? comp.type.name : 'N/A'}</span>
                                                                                                                                                                                                                                </li>
                                                                                                                                                                                                                                `).join('')}
                                </ul>
                            </div>
                        `;
                } else {
                    componentsHtml = `
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="text-md font-semibold text-gray-800 mb-2">Komponen Terpasang:</h4>
                                <p class="text-sm text-gray-500 italic">Tidak ada komponen terpasang.</p>
                            </div>
                        `;
                }

            } else {
                if (data.item) {
                    let itemLocation = data.item.desk ?
                        `${data.item.desk.lab.name} - Meja ${data.item.desk.location}` :
                        'Item induk belum terpasang';
                    locationHtml = `
                            <dt class="col-span-1 font-semibold text-gray-800">Terpasang di Item</dt>
                            <dd class="col-span-2 text-gray-600">${data.item.name} (${data.item.serial_code})</dd>
                            <dt class="col-span-1 font-semibold text-gray-800">Lokasi Item</dt>
                            <dd class="col-span-2 text-gray-600">${itemLocation}</dd>
                        `;
                } else {
                    locationHtml = `
                            <dt class="col-span-1 font-semibold text-gray-800">Lokasi</dt>
                            <dd class="col-span-2 text-gray-500 italic">Komponen bebas (belum terpasang ke item)</dd>
                        `;
                }
            }

            let specsHtml = '';
            if (data.spec_set_values && data.spec_set_values.length > 0) {
                specsHtml = `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Spesifikasi:</h4>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1">
                                ${data.spec_set_values.map(spec => `
                                                                                                                                                                                                                    <div class="col-span-1">
                                                                                                                                                                                                                        <dt class="font-semibold text-gray-800 text-sm">${spec.spec_attributes ? spec.spec_attributes.name : 'N/A'}</dt>
                                                                                                                                                                                                                        <dd class="text-gray-600 text-sm pl-2">${spec.value}</dd>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                    `).join('')}
                            </dl>
                        </div>
                    `;
            } else {
                specsHtml = `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Spesifikasi:</h4>
                            <p class="text-sm text-gray-500 italic">Tidak ada data spesifikasi.</p>
                        </div>
                    `;
            }

            contentEl.innerHTML = `
                    <div class="space-y-2">
                        <dl class="grid grid-cols-3 gap-x-4 gap-y-2">
                            <dt class="col-span-1 font-semibold text-gray-800">Nama</dt>
                            <dd class="col-span-2 text-gray-600">${data.name}</dd>

                            <dt class="col-span-1 font-semibold text-gray-800">Serial Code</dt>
                            <dd class="col-span-2 text-gray-600 font-mono">${data.serial_code}</dd>

                            <dt class="col-span-1 font-semibold text-gray-800">Tipe</dt>
                            <dd class="col-span-2 text-gray-600">${data.type ? data.type.name : 'N/A'}</dd>

                            <dt class="col-span-1 font-semibold text-gray-800">Kondisi</dt>
                            <dd class="col-span-2">${conditionText}</dd>

                            <dt class="col-span-1 font-semibold text-gray-800">Tgl. Produksi</dt>
                            <dd class="col-span-2 text-gray-600">${producedAt}</dd>

                            ${locationHtml}
                        </dl>

                        ${specsHtml}

                        ${componentsHtml}
                    </div>
                `;
        }

        function initializePageFilters() {
            const filterForm = document.getElementById('filter-form');
            if (!filterForm) return;

            new TomSelect('#filter_lab_select', {
                plugins: ['clear_button']
            });
            new TomSelect('#filter_type_select', {
                plugins: ['clear_button']
            });
            new TomSelect('#filter_status', {
                plugins: ['clear_button']
            });
            new TomSelect('#filter_condition', {
                plugins: ['clear_button']
            });

            filterTomSelects.val = new TomSelect('#filter_spec_val', {
                plugins: ['clear_button']
            });
            filterTomSelects.val.disable();

            filterTomSelects.attr = new TomSelect('#filter_spec_attr', {
                plugins: ['clear_button'],
                onChange: (value) => {
                    if (!filterTomSelects.val) return;
                    filterTomSelects.val.clear();
                    filterTomSelects.val.clearOptions();

                    if (!value) {
                        filterTomSelects.val.disable();
                    } else {
                        const selectedOptionData = filterTomSelects.attr.options[value];

                        if (!selectedOptionData || !selectedOptionData['values']) {
                            showToast('Gagal Memuat Filter',
                                'Data spesifikasi (values) tidak ditemukan di element Dropdown Spesifikasi Attribute.',
                                'error');
                            filterTomSelects.val.disable();
                            return;
                        }
                        try {
                            const values = JSON.parse(selectedOptionData['values']);
                            filterTomSelects.val.addOptions(values.map(v => ({
                                value: v.id,
                                text: v.value
                            })));
                            filterTomSelects.val.enable();
                        } catch (e) {
                            console.error("Gagal parse values:", e, "Data string:", selectedOptionData[
                                'values']);
                            showToast('Gagal Memuat Filter',
                                'Data spesifikasi (values) di element Dropdown Spesifikasi Attribute rusak.',
                                'error');
                            filterTomSelects.val.disable();
                        }
                    }
                }
            });

            const initialAttrId = '';
            const initialValId = '';

            if (initialAttrId && filterTomSelects.attr) {
                filterTomSelects.attr.setValue(initialAttrId, true);
                const selectedOption = filterTomSelects.attr.options[initialAttrId];
                if (selectedOption && selectedOption['values'] && filterTomSelects.val) {
                    try {
                        const values = JSON.parse(selectedOption['values']);
                        filterTomSelects.val.addOptions(values.map(v => ({
                            value: v.id,
                            text: v.value
                        })));
                        filterTomSelects.val.enable();
                        if (initialValId) {
                            filterTomSelects.val.setValue(initialValId, true);
                        }
                    } catch (e) {
                        console.error("Gagal parse data-values saat init:", e);
                    }
                }
            }
        }

        async function loadLabsForSetAttachment() {
            try {
                const response = await fetch("{{ route('admin.labs.list') }}");
                if (!response.ok) throw new Error('Gagal memuat daftar lab.');
                const labs = await response.json();

                if (tomSelectSetLab) {
                    tomSelectSetLab.clearOptions();
                    tomSelectSetLab.addOptions(labs.map(lab => ({
                        value: lab.id,
                        text: lab.name
                    })));
                }
            } catch (error) {
                showToast('Error', error.message, 'error');
            }
        }

        async function fetchDeskMapForSet(labId) {
            const container = document.getElementById('set-desk-grid-container');
            container.innerHTML =
                '<div class="flex items-center justify-center py-8"><div class="animate-spin rounded-full h-12 w-8 border-b-2 border-indigo-600"></div></div>';

            try {
                const response = await fetch(`/admin/labs/${labId}/desks`);
                if (!response.ok) throw new Error('Gagal memuat denah meja.');
                setLabDesks = await response.json();

                let maxRow = 5,
                    maxCol = 10;
                if (setLabDesks.length > 0) {
                    setLabDesks.forEach(d => {
                        const row = d.location.charCodeAt(0) - 64;
                        const col = parseInt(d.location.substring(1));
                        if (row > maxRow) maxRow = row;
                        if (col > maxCol) maxCol = col;
                    });
                }
                renderDeskGridForSet(setLabDesks, maxRow, maxCol);
            } catch (error) {
                container.innerHTML = `<div class="text-center py-8 text-red-500">${error.message}</div>`;
            }
        }

        function renderDeskGridForSet(desks, maxRows, maxCols) {
            const container = document.getElementById('set-desk-grid-container');
            let html =
                `<div class="overflow-x-auto pb-4"><div class="grid gap-3 border-2 min-w-fit border-slate-300 p-6" style="grid-template-columns: repeat(${maxCols}, minmax(100px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;

            const occupiedSlots = new Set(desks.map(d => d.location));

            desks.forEach(desk => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                const isSelected = setSelectedDeskLocations.includes(desk.location);

                // Style berbeda jika dipilih
                let bgColorClass = isSelected ?
                    'bg-indigo-600 border-indigo-700 text-white shadow-lg transform scale-105' : (desk
                        .overall_condition !== 'item_kosong' ?
                        'bg-red-50 border-red-300 hover:bg-red-100 text-red-800' :
                        'bg-gray-50 border-gray-300 hover:bg-gray-100 text-gray-800');

                let iconColor = isSelected ? 'text-white' : 'text-gray-400';

                html +=
                    `<div data-desk-location="${desk.location}" style="grid-area: ${row} / ${col};" class="set-desk-item group transition-all duration-200 flex flex-col items-center justify-center p-3 border-2 rounded-lg min-h-24 ${bgColorClass} cursor-pointer">`;
                html += `<span class="font-bold text-lg block select-none">${desk.location}</span>`;

                if (isSelected) {
                    html +=
                        `<span class="text-xs text-indigo-100 mt-1 inline-block select-none font-semibold">Terpilih</span>`;
                } else {
                    // Tampilkan kondisi meja jika tidak dipilih
                    let condText = (desk.overall_condition === 'item_kosong') ? 'Kosong' : 'Terisi Lengkap';
                    html += `<span class="text-xs text-gray-500 mt-1 inline-block select-none">${condText}</span>`;
                }

                html += `</div>`;
            });

            for (let r = 1; r <= maxRows; r++) {
                for (let c = 1; c <= maxCols; c++) {
                    const location = `${String.fromCharCode(64 + r)}${c}`;
                    if (!occupiedSlots.has(location)) {
                        html += `<div style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                    }
                }
            }

            html += '</div></div>';
            container.innerHTML = html;

            // Event Listener Click
            document.querySelectorAll('.set-desk-item').forEach(deskEl => {
                deskEl.addEventListener('click', () => {
                    const location = deskEl.dataset.deskLocation;

                    // LOGIKA: Selalu reset array menjadi hanya 1 item (item yang baru diklik)
                    setSelectedDeskLocations = [location];

                    updateSetSelectedDesksDisplay();
                    renderDeskGridForSet(setLabDesks, maxRows, maxCols); // Re-render untuk update tampilan
                });
            });
        }

        function updateSetSelectedDesksDisplay() {
            const display = document.getElementById('set-selected-desks-display');
            if (setSelectedDeskLocations.length === 0) {
                display.innerHTML = 'Belum ada meja dipilih.';
            } else {
                display.innerHTML = `Meja dipilih: ${setSelectedDeskLocations[0]}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            initializeCreateItemModal();
            initializeCreateSetModal();
            initializeActionModal();
            initializeDeskMapperModal();
            initializeDetailModal();
            initializeRepairModal();
            initializePageFilters();

            const filterForm = document.getElementById('filter-form');

            if (filterForm) {
                filterForm.addEventListener('submit', function (event) {
                    if (document.activeElement.id === 'apply-filter-btn') {
                        event.preventDefault();
                        showLoading('Loading...', 'Menerapkan filter...');
                        setTimeout(() => {
                            this.submit();
                        }, 100);
                    }
                });
            }

            try {
                let itemDataTable = new te.Datatable(document.getElementById('items-datatable'), {
                    hover: true,
                    striped: true,
                    bordered: true,
                    borderless: false,
                    borderColor: 'border-gray-300',
                    noFoundMessage: 'Tidak ada Item yang tercatat di Inventaris.',
                    loading: false,
                    fixedHeader: true,
                    maxHeight: 600,
                    pagination: true,
                    entries: 10,
                    entriesOptions: [5, 10, 25, 50, 100],
                });

                let componentDataTable = new te.Datatable(document.getElementById('components-datatable'), {
                    hover: true,
                    striped: true,
                    bordered: true,
                    borderless: false,
                    borderColor: 'border-gray-300',
                    noFoundMessage: 'Tidak ada Component yang tercatat di Inventaris.',
                    loading: false,
                    fixedHeader: true,
                    maxHeight: 600,
                    pagination: true,
                    entries: 10,
                    entriesOptions: [5, 10, 25, 50, 100],
                });

                document.getElementById('datatable-search-input').addEventListener('input', (e) => {
                    itemDataTable.search(e.target.value);
                    componentDataTable.search(e.target.value);
                });

            } catch (e) {
                showToast('Gagal Inisialisasi', 'Gagal memuat fungsionalitas tabel (sorting/pencarian).',
                    'warning');
            }

            const openCreateBtn = document.getElementById('open-create-modal-btn');
            if (openCreateBtn) {
                openCreateBtn.addEventListener('click', window.openCreateItemModal);
            }

            const openCreateSetBtn = document.getElementById('open-create-set-modal-btn');
            if (openCreateSetBtn) {
                openCreateSetBtn.addEventListener('click', window.openCreateSetModal);
            }

            const itemsDatatableEl = document.getElementById('items-datatable');
            const componentsDatatableEl = document.getElementById('components-datatable');
            const actionModal = document.getElementById('action-modal');
            const actionModalTitle = document.getElementById('action-modal-title');

            if (itemsDatatableEl && actionModal && actionModalTitle) {
                itemsDatatableEl.addEventListener('click', function (e) {
                    const actionButton = e.target.closest('.btn-open-action-item');
                    if (!actionButton) return;

                    currentItemId = actionButton.dataset.itemId;
                    currentItemName = actionButton.dataset.itemName;
                    currentItemCondition = actionButton.dataset.itemCondition === '1';
                    currentItemType = 'item';

                    actionModalTitle.innerHTML =
                        `Aksi untuk (Item): <span class="font-bold text-indigo-600">${currentItemName}</span>`;
                    updateActionModalButtons('item');

                    actionModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            if (componentsDatatableEl && actionModal && actionModalTitle) {
                componentsDatatableEl.addEventListener('click', function (e) {
                    const actionButton = e.target.closest('.btn-open-action-component');
                    if (!actionButton) return;

                    currentItemId = actionButton.dataset.itemId;
                    currentItemName = actionButton.dataset.itemName;
                    currentItemCondition = actionButton.dataset.itemCondition === '1';
                    currentItemType = 'component';

                    actionModalTitle.innerHTML =
                        `Aksi untuk (Component): <span class="font-bold text-purple-600">${currentItemName}</span>`;
                    updateActionModalButtons('component');

                    actionModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            // Initialize functions
            initializeManageComponentsModal();
            initializeClientSideFiltering();
        });
    </script>
@endsection
