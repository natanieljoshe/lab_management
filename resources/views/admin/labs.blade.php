@extends('layouts.admin')

@section('title', 'Laboratories Mapping')

@section('body')
    <div class="flex flex-col w-full py-4 shadow-md items-center justify-center mb-5">
        <h1 class="text-center text-4xl uppercase font-bold mb-2">Laboratories</h1>
    </div>

    <div class="max-w-7xl mx-auto px-6">
        <!-- DAFTAR RUANGAN LABORATORIUM -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-200">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Daftar Ruangan Laboratorium</h2>
                    <p class="text-xs text-gray-500 mt-1 max-w-2xl leading-relaxed">
                        Lab internal (diprioritaskan) dan lab eksternal (Puskom). Kolom grid mewakili ruangan ini. Flag <code class="px-1.5 py-0.5 bg-gray-100 text-indigo-600 rounded text-xs font-mono">manual_only</code> menandakan ruangan hanya untuk penataan manual (mis. VR, MM).
                    </p>
                </div>
                <div>
                    <button type="button" onclick="openAddRoomModal()"
                        class="inline-flex items-center px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Ruangan
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto mt-4 border rounded-xl border-gray-200">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-[11px] font-semibold border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Nama Ruangan</th>
                            <th class="py-3 px-4 text-center">Tipe</th>
                            <th class="py-3 px-4 text-center">Kapasitas</th>
                            <th class="py-3 px-4 text-center">Status Grid</th>
                            <th class="py-3 px-4 text-center">Aktif</th>
                            <th class="py-3 px-4">Catatan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($labs->sortBy(fn($r) => $r->type === 'internal' ? 0 : 1) as $lab)
                            <tr class="hover:bg-gray-50/75 transition">
                                <td class="py-3 px-4 font-bold text-gray-900 text-sm">{{ $lab->code }}</td>
                                <td class="py-3 px-4 font-medium text-gray-800">{{ $lab->name }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold {{ $lab->type === 'internal' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($lab->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-gray-800">{{ $lab->capacity ?? 50 }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if ($lab->is_manual_only)
                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold text-[10px]">Manual Only</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold text-[10px]">Otomatis & Manual</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if ($lab->is_active)
                                        <span class="text-emerald-600 font-bold text-sm">✓</span>
                                    @else
                                        <span class="text-rose-600 font-bold text-sm">✗</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-[11px]">{{ $lab->notes ?: '-' }}</td>
                                <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                    <button type="button"
                                        onclick="openEditRoomModal({{ json_encode([
                                            'id' => $lab->id,
                                            'code' => $lab->code,
                                            'name' => $lab->name,
                                            'capacity' => $lab->capacity ?? 50,
                                            'type' => $lab->type,
                                            'notes' => $lab->notes ?? '',
                                            'is_manual_only' => (bool)$lab->is_manual_only,
                                            'is_active' => (bool)$lab->is_active,
                                        ]) }})"
                                        class="text-indigo-600 hover:text-indigo-800 font-semibold transition">
                                        Ubah
                                    </button>
                                    <form action="{{ route('rooms.destroy', $lab->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus/nonaktifkan ruangan {{ $lab->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-400">Belum ada data ruangan laboratorium.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL: Tambah / Ubah Ruangan Lab -->
        <div id="room-form-modal" class="hidden fixed inset-0 z-[5000] overflow-y-auto flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRoomModal()"></div>
            <div class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 z-10 space-y-4">
                <div class="flex items-center justify-between border-b pb-3 border-gray-200">
                    <h3 id="room-modal-title" class="font-bold text-base text-gray-900">Tambah Ruangan Baru</h3>
                    <button type="button" onclick="closeRoomModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form id="room-form" action="{{ route('rooms.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <input type="hidden" name="_method" id="room-form-method" value="POST">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Kode Ruangan</label>
                            <input type="text" name="code" id="room-input-code" required placeholder="mis. PG" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Kapasitas (Orang)</label>
                            <input type="number" name="capacity" id="room-input-capacity" min="1" max="200" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" value="50">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-700 mb-1">Nama Ruangan</label>
                        <input type="text" name="name" id="room-input-name" required placeholder="mis. Lab Pemrograman, P.206" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Tipe Ruangan</label>
                            <select name="type" id="room-input-type" required class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="internal">Internal (Lab Prodi)</option>
                                <option value="external">Eksternal (Puskom)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Catatan Tambahan</label>
                            <input type="text" name="notes" id="room-input-notes" placeholder="Opsional" class="w-full text-xs rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-gray-200">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_manual_only" id="room-input-manual" value="1" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-semibold text-gray-800">Manual Only (Hanya penataan manual)</span>
                        </label>

                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_active" id="room-input-active" value="1" checked class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-semibold text-gray-800">Ruangan Aktif</span>
                        </label>
                    </div>

                    <div class="flex justify-end space-x-2 pt-3 border-t border-gray-200">
                        <button type="button" onclick="closeRoomModal()" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
                            Simpan Ruangan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <label for="lab-selector" class="block text-sm font-semibold text-gray-700 mb-2">
                Pilih Laboratorium
            </label>
            <div class="relative">
                <select id="lab-selector"
                    class="block w-full md:w-2/3 lg:w-1/2 px-4 py-3 text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 appearance-none bg-white cursor-pointer">
                    <option value="" selected disabled>-- Pilih Laboratorium --</option>
                    @foreach ($labs as $lab)
                        <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="desk-grid-container" class="mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <p class="text-gray-500 text-lg">Silakan pilih laboratorium untuk menampilkan denah meja</p>
                </div>
            </div>
        </div>

        {{-- Lab Storage (Lemari) --}}
        {{-- Hapus class hidden default agar struktur tetap ada, kontrol via JS --}}
        <div id="lab-storage-container" class="mb-8 hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-800">Lemari Lab (Items & Components)</h2>
                </div>
                <div id="lab-storage-content" class="space-y-6">
                    <div class="text-center py-8 text-gray-500">Memuat data...</div>
                </div>
            </div>
        </div>
    </div>

    {{--
    =========================================================
    MODAL BARU: Detail Item / Component
    =========================================================
    --}}
    <div id="detail-popup-modal" class="hidden fixed inset-0 z-[6000]" role="dialog" aria-modal="true">
        <div id="detail-popup-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
            {{-- pointer-events-auto added to modal content to allow interaction --}}
            <div
                class="bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all flex flex-col max-h-[90vh] pointer-events-auto">
                {{-- Header --}}
                <div class="flex justify-between items-center p-4 border-b border-gray-200">
                    <h3 id="detail-modal-title" class="text-xl font-bold text-gray-900">Detail</h3>
                    <button onclick="closeDetailModal()"
                        class="text-gray-400 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div id="detail-modal-body" class="p-6 overflow-y-auto space-y-6">
                    {{-- Content injected via JS --}}
                </div>
                {{-- Footer --}}
                <div class="flex justify-end p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                    <button onclick="closeDetailModal()"
                        class="text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{--  MODAL: Tambah Item ke Meja --}}
    <div id="add-item-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="add-item-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[3000]"></div>
        <div id="add-item-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[3001]">
            <div id="add-item-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="add-item-modal-title" class="text-xl font-semibold text-gray-900">
                        Tambah Item ke Meja
                    </h3>
                    <button id="add-item-modal-close-button" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div id="add-item-modal-body" class="p-6 space-y-4 overflow-y-auto">
                    <fieldset id="item-filter-fieldset"
                        class="border border-gray-300 rounded-lg p-4 transition-opacity duration-300">
                        <legend class="text-sm font-semibold text-gray-700 px-2">Filter Item (Hanya item kondisi Baik &
                            belum terpasang)</legend>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="filter-type-select"
                                    class="block text-xs font-medium text-gray-600 mb-1">Berdasarkan Tipe</label>
                                <select id="filter-type-select" placeholder="Semua Tipe..."></select>
                            </div>
                            <div>
                                <label for="filter-attr-select"
                                    class="block text-xs font-medium text-gray-600 mb-1">Berdasarkan Atribut</label>
                                <select id="filter-attr-select" placeholder="Semua Atribut..."></select>
                            </div>
                            <div>
                                <label for="filter-value-select"
                                    class="block text-xs font-medium text-gray-600 mb-1">Berdasarkan Value</label>
                                <select id="filter-value-select" placeholder="Pilih Atribut dulu..." disabled></select>
                            </div>
                        </div>
                    </fieldset>
                    <div>
                        <label for="item-select-dropdown" class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                            Item (Multi-select)</label>
                        <div id="item-select-loading"
                            class="flex items-center text-gray-500 p-3 border border-gray-200 rounded-lg">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-400 mr-2"></div> Memuat
                            item...
                        </div>
                        <select id="item-select-dropdown" multiple placeholder="Pilih item..." class="hidden"></select>
                    </div>
                </div>
                <div class="flex items-center justify-end p-4 space-x-3 border-t border-gray-200 rounded-b">
                    <button id="add-item-modal-footer-cancel-button" type="button"
                        class="text-gray-700 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 border border-gray-300">
                        Batal
                    </button>
                    <button id="add-item-modal-footer-save-button" type="button"
                        class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 w-36 flex items-center justify-center">
                        <span class="btn-text">Simpan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ACTIONS CARD --}}
    <div id="item-action-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[4000]"></div>
        <div id="item-action-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[4001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="item-action-modal-title" class="text-xl font-semibold text-gray-900">Aksi untuk Item</h3>
                    <button id="item-action-modal-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4 overflow-y-auto">
                    <button id="action-attach-desk-card"
                        class="p-6 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span class="font-semibold">Pasang ke Meja</span>
                    </button>
                    <button id="action-attach-item-card"
                        class="p-6 bg-purple-500 hover:bg-purple-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg hidden">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                            </path>
                        </svg>
                        <span class="font-semibold">Pasang ke Item</span>
                    </button>
                    <button id="action-attach-lab-card"
                        class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="font-semibold">Pasang ke Lab</span>
                    </button>
                    <button id="action-detach-desk-card"
                        class="p-6 bg-red-500 hover:bg-red-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg hidden">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <span class="font-semibold">Lepas dari Meja</span>
                    </button>
                    <button id="action-detach-lab-card"
                        class="p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg flex flex-col items-center justify-center transition-all shadow-lg hidden">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <span class="font-semibold">Lepas dari Lab</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL ATTACH DESK MAP --}}
    <div id="attach-desk-map-modal" class="hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[5000]"></div>
        <div id="attach-desk-map-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[5001]">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-6xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Pilih Meja untuk: <span id="attach-desk-item-name"
                            class="text-indigo-600"></span></h3>
                    <button id="attach-desk-map-close-btn" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <label for="attach-lab-selector" class="block text-sm font-semibold text-gray-700 mb-2">Pilih
                            Laboratorium</label>
                        <select id="attach-lab-selector" placeholder="Pilih Lab..."></select>
                    </div>
                    <div id="attach-desk-map-container" class="mb-8">
                        <div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah meja.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        #desk-grid {
            transform-origin: top left;
        }

        .empty-slot {
            @apply bg-slate-100 border-2 border-dashed border-slate-300 rounded-xl min-h-36 flex items-center justify-center text-slate-500 font-semibold text-lg;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .empty-slot.drag-over,
        .desk-item.drag-over {
            @apply bg-indigo-100 border-indigo-400 text-indigo-500;
        }

        .desk-item.dragging {
            @apply opacity-50 border-indigo-500 shadow-2xl scale-95;
        }

        #desk-grid:not(.edit-mode) .empty-slot {
            visibility: hidden;
            @apply bg-transparent border-transparent;
        }

        #desk-grid.edit-mode .desk-item {
            cursor: grab;
        }

        #desk-grid.edit-mode .desk-item:active {
            cursor: grabbing;
        }

        #item-filter-fieldset:disabled {
            @apply opacity-50 cursor-not-allowed;
        }

        .swal2-input {
            padding: 0 0.75rem !important;
        }

        /* Animasi Modal Detail */
        #detail-popup-modal.hidden {
            display: none;
        }

        #detail-popup-modal:not(.hidden) {
            display: flex;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <script>
        document.getElementById('labs').classList.add('bg-slate-100');
        document.getElementById('labs').classList.add('active');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- DATA STORE UNTUK DETAIL MODAL ---
        window.inventoryData = new Map();

        // --- HELPER FUNCTIONS UNTUK DETAIL ---
        function formatDate(dateString) {
            if (!dateString) return '-';
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // --- HELPER UNTUK RENDER SPESIFIKASI (GRID) ---
        function renderSpecsGrid(specSetValues) {
            if (!specSetValues || specSetValues.length === 0) {
                return '<p class="text-gray-500 italic text-xs">Tidak ada spesifikasi.</p>';
            }
            let html = `<ul class="grid grid-cols-2 gap-2 mt-2">`;
            specSetValues.forEach(spec => {
                const label = spec.spec_attributes?.name || 'SPEC';
                html += `
                                        <li class="text-xs border border-gray-200 p-1.5 rounded bg-white">
                                            <span class="block text-[10px] text-gray-500 font-bold uppercase tracking-wide">${label}</span>
                                            <span class="font-medium text-gray-800">${spec.value}</span>
                                        </li>
                                    `;
            });
            html += `</ul>`;
            return html;
        }

        function showDetailModal(id) {
            const data = window.inventoryData.get(id);
            if (!data) return;

            const modal = document.getElementById('detail-popup-modal');
            const titleEl = document.getElementById('detail-modal-title');
            const bodyEl = document.getElementById('detail-modal-body');

            const isComponent = !data.items && !data.components; // Basic check if it's likely a component
            const typeName = data.type?.name || (isComponent ? 'Component' : 'Item');
            titleEl.textContent = `Detail ${typeName}`;

            // Tanggal Produksi
            const producedAt = data.produced_at ? formatDate(data.produced_at) : '-';

            // HTML untuk Spesifikasi Utama
            const mainSpecsHtml = renderSpecsGrid(data.spec_set_values);

            // HTML untuk Child Components (Jika ada)
            let componentsHtml = '';
            if (data.components && data.components.length > 0) {
                componentsHtml += `
                                        <div class="mt-6 pt-4 border-t border-gray-200">
                                            <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                Komponen Terpasang (${data.components.length})
                                            </h4>
                                            <div class="space-y-4">
                                    `;

                data.components.forEach(comp => {
                    const compProducedAt = comp.produced_at ? formatDate(comp.produced_at) : '-';
                    const compConditionClass = comp.condition == 1 ? 'text-emerald-600 bg-emerald-50' :
                        'text-rose-600 bg-rose-50';
                    const compConditionText = comp.condition == 1 ? 'Baik' : 'Rusak';
                    const compSpecsHtml = renderSpecsGrid(comp.spec_set_values);

                    componentsHtml += `
                                            <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <p class="font-bold text-gray-800 text-sm">${comp.name}</p>
                                                        <p class="text-xs text-gray-500 font-mono">${comp.serial_code}</p>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide ${compConditionClass}">${compConditionText}</span>
                                                </div>

                                                <div class="grid grid-cols-2 gap-4 text-xs text-gray-600 mb-2">
                                                    <div>
                                                        <span class="block font-semibold text-gray-500">Tipe:</span>
                                                        ${comp.type?.name || '-'}
                                                    </div>
                                                    <div>
                                                        <span class="block font-semibold text-gray-500">Diproduksi Pada:</span>
                                                        ${compProducedAt}
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <span class="block font-semibold text-xs text-gray-500 mb-1">Spesifikasi Komponen:</span>
                                                    ${compSpecsHtml}
                                                </div>
                                            </div>
                                        `;
                });
                componentsHtml += `</div></div>`;
            } else if (!isComponent) {
                // Jika Item tapi tidak punya komponen
                componentsHtml = `
                                        <div class="mt-6 pt-4 border-t border-gray-200 text-center text-gray-400 text-sm italic">
                                            Tidak ada komponen terpasang.
                                        </div>
                                    `;
            }

            bodyEl.innerHTML = `
                                    <div class="bg-blue-50 p-5 rounded-xl border border-blue-100 shadow-sm">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide mb-0.5">Nama</p>
                                                <p class="text-sm font-semibold text-gray-800">${data.name}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide mb-0.5">Serial Code</p>
                                                <p class="text-sm font-mono text-gray-700 bg-white inline-block px-1.5 rounded border border-gray-200">${data.serial_code}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide mb-0.5">Tipe</p>
                                                <p class="text-sm text-gray-800">${data.type?.name || '-'}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide mb-0.5">Diproduksi Pada</p>
                                                <p class="text-sm text-gray-800">${producedAt}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-bold text-gray-800 mb-2 pb-1 border-b border-gray-100">Spesifikasi Utama</h4>
                                        ${mainSpecsHtml}
                                    </div>

                                    ${componentsHtml}
                                `;

            modal.classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-popup-modal').classList.add('hidden');
        }

        // --- EXISTING ACTION FUNCTIONS ---
        async function detachItemFromDesk(itemId) {
            const result = await Swal.fire({
                title: 'Lepas dari Meja?',
                text: 'Item akan dilepas dari meja.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444'
            });
            if (!result.isConfirmed) return;
            try {
                const response = await fetch(`/admin/items/${itemId}/detach-desk`, {
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
        }

        async function detachItemFromLab(itemId) {
            const result = await Swal.fire({
                title: 'Lepas dari Lab?',
                text: 'Item akan dilepas dari lemari lab.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1'
            });
            if (!result.isConfirmed) return;
            try {
                const response = await fetch(`/admin/items/${itemId}/detach-lab`, {
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
        }

        async function detachComponentFromItem(componentId) {
            const result = await Swal.fire({
                title: 'Lepas dari Item?',
                text: 'Component akan dilepas dari item induk.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#a855f7'
            });
            if (!result.isConfirmed) return;
            try {
                const response = await fetch(`/admin/components/${componentId}/detach-item`, {
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
        }

        async function detachComponentFromLab(componentId) {
            const result = await Swal.fire({
                title: 'Lepas dari Lab?',
                text: 'Component akan dilepas dari lemari lab.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lepas!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1'
            });
            if (!result.isConfirmed) return;
            try {
                const response = await fetch(`/admin/components/${componentId}/detach-lab`, {
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
        }

        function hideLoading() {
            Swal.close();
        }

        function showLoading(title, text = '') {
            Swal.fire({
                title: title,
                text: text,
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function showToast(title, message, icon = 'success') {
            Toast.fire({
                icon: icon,
                title: title,
                text: message
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const labSelector = document.getElementById('lab-selector');
            const deskContainer = document.getElementById('desk-grid-container');
            let desks = [];
            let newDesks = [];
            let isAddDeskMode = false;
            let labConfig = {
                maxRows: 5,
                maxCols: 10
            };
            let currentZoom = 1.0;
            const maxZoom = 1.5,
                minZoom = 0.5,
                zoomStep = 0.1;
            let isMouseDown = false;
            let selectedSlots = new Set();
            let isDeletingMode = false;
            let slotsToCancel = new Set();
            let isMainDeleteMode = false;
            let isMouseDownMainDelete = false;
            let desksToMainDelete = new Set();

            let currentDeskId = null;
            let tomSelectType, tomSelectAttr, tomSelectValue, tomSelectItem;
            let allSpecAttributes = [];

            // --- CLICK OUTSIDE TO CLOSE DETAIL MODAL ---
            const detailOverlay = document.getElementById('detail-popup-overlay');
            if (detailOverlay) {
                detailOverlay.addEventListener('click', closeDetailModal);
            }
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !document.getElementById('detail-popup-modal').classList
                    .contains(
                        'hidden')) {
                    closeDetailModal();
                }
            });

            function applyZoom() {
                const deskGrid = document.getElementById('desk-grid');
                const zoomLevelDisplay = document.getElementById('zoom-level-display');
                if (deskGrid && zoomLevelDisplay) {
                    deskGrid.style.transform = '';
                    deskGrid.style.zoom = currentZoom;
                    zoomLevelDisplay.textContent = `${Math.round(currentZoom * 100)}%`;
                }
            }

            function autoZoomToFit() {
                const scroller = document.getElementById('grid-scroller');
                const grid = document.getElementById('desk-grid');
                if (!scroller || !grid) return;
                const scrollerWidth = scroller.clientWidth;
                const scrollerHeight = scroller.clientHeight;
                const gridWidth = grid.scrollWidth;
                const gridHeight = grid.scrollHeight;
                const zoomX = scrollerWidth / gridWidth;
                const zoomY = scrollerHeight / gridHeight;
                let newZoom = Math.min(zoomX, zoomY);
                newZoom = Math.max(minZoom, Math.min(maxZoom, newZoom));
                currentZoom = newZoom;
                applyZoom();
            }

            function cancelNewDesk(location) {
                if (!isAddDeskMode) return;
                const index = newDesks.findIndex(desk => desk.location === location);
                if (index > -1) {
                    newDesks.splice(index, 1);
                    renderDeskGrid(true);
                }
            }

            async function saveNewDesks() {
                if (newDesks.length === 0) return {
                    success: true,
                    newDesksSaved: 0
                };
                showLoading('Menyimpan Meja Baru...');
                const labId = labSelector.value;
                try {
                    const response = await fetch(`/admin/labs/${labId}/desks/batch-create`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            desks: newDesks
                        })
                    });
                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Server response was not ok.');
                    }
                    const data = await response.json();
                    if (data.success && data.created_desks) {
                        desks = [...desks, ...data.created_desks];
                        newDesks = [];
                        hideLoading();
                        showToast('Berhasil!', data.message, 'success');
                        return {
                            success: true,
                            newDesksSaved: data.created_desks.length
                        };
                    } else {
                        throw new Error(data.message || 'Gagal menyimpan meja baru.');
                    }
                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                    return {
                        success: false,
                        error: error.message
                    };
                }
            }

            document.addEventListener('mouseup', async () => {
                if (isAddDeskMode && isMouseDown) {
                    isMouseDown = false;
                    if (selectedSlots.size > 0) {
                        selectedSlots.forEach(location => {
                            const isOccupied = [...desks, ...newDesks].some(d => d.location ===
                                location);
                            if (!isOccupied) {
                                newDesks.push({
                                    location: location,
                                    isNew: true,
                                });
                            }
                        });
                        selectedSlots.clear();
                        renderDeskGrid(true);
                    }
                }
                if (isAddDeskMode && isDeletingMode) {
                    isDeletingMode = false;
                    if (slotsToCancel.size > 0) {
                        const count = slotsToCancel.size;
                        slotsToCancel.forEach(location => {
                            cancelNewDesk(location);
                        });
                        slotsToCancel.clear();
                        if (count > 1) renderDeskGrid(true);
                    }
                }
                if (isMainDeleteMode && isMouseDownMainDelete) {
                    isMouseDownMainDelete = false;
                    if (desksToMainDelete.size > 0) {
                        const count = desksToMainDelete.size;
                        const idsToDelete = [...desksToMainDelete];
                        const selectedDesks = desks.filter(d => idsToDelete.includes(String(d.id)));
                        const hasItems = selectedDesks.some(d => d.items && d.items.length > 0);
                        let deleteMode = null;
                        let isConfirmed = false;
                        if (hasItems) {
                            const result = await Swal.fire({
                                title: 'Hapus Meja/Item',
                                text: `Anda telah memilih ${count} meja (beberapa ada isinya). Apa yang ingin Anda lakukan?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6B7280',
                                confirmButtonText: 'Lanjutkan',
                                cancelButtonText: 'Batal',
                                input: 'radio',
                                inputOptions: {
                                    'delete_items_only': 'Hapus ITEM SAJA (Meja tetap ada)',
                                    'delete_all': 'Hapus MEJA DAN SEMUA ITEM (Permanen)'
                                },
                                inputValidator: (value) => !value &&
                                    'Anda harus memilih salah satu opsi!'
                            });
                            if (result.isConfirmed && result.value) {
                                deleteMode = result.value;
                                isConfirmed = true;
                            }
                        } else {
                            const result = await Swal.fire({
                                title: `Hapus ${count} Meja Kosong?`,
                                text: "Meja ini akan dihapus permanen. Lanjutkan?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                confirmButtonText: 'Ya, Hapus',
                                cancelButtonText: 'Batal'
                            });
                            if (result.isConfirmed) {
                                deleteMode = 'delete_all';
                                isConfirmed = true;
                            }
                        }
                        if (isConfirmed && deleteMode) {
                            showLoading('Menghapus Meja...');
                            const labId = labSelector.value;
                            try {
                                const response = await fetch(
                                    `/admin/labs/${labId}/desks/batch-delete`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        ids: idsToDelete,
                                        delete_mode: deleteMode
                                    })
                                });
                                if (!response.ok) {
                                    const errorData = await response.json();
                                    throw new Error(errorData.message || 'Gagal memproses permintaan.');
                                }
                                const data = await response.json();
                                if (data.success) {
                                    if (deleteMode === 'delete_all') {
                                        desks = desks.filter(d => !idsToDelete.includes(String(d.id)));
                                    } else {
                                        desks.forEach(desk => {
                                            if (idsToDelete.includes(String(desk.id))) {
                                                desk.items = [];
                                                desk.overall_condition = 'item_kosong';
                                            }
                                        });
                                    }
                                    desksToMainDelete.clear();
                                    renderDeskGrid(true);
                                    hideLoading();
                                    showToast('Berhasil!', data.message, 'success');
                                } else {
                                    throw new Error(data.message || 'Gagal menghapus.');
                                }
                            } catch (error) {
                                hideLoading();
                                Swal.fire('Gagal!', error.message, 'error');
                                desksToMainDelete.clear();
                                renderDeskGrid(true);
                            }
                        } else {
                            desksToMainDelete.clear();
                            renderDeskGrid(true);
                        }
                    }
                }
            });

            function renderDeskGrid(isEditMode = false) {
                const scroller = document.getElementById('grid-scroller');
                const scrollPos = {
                    left: scroller ? scroller.scrollLeft : 0,
                    top: scroller ? scroller.scrollTop : 0
                };
                if (!labSelector.value) return;
                const allDesks = [...desks, ...newDesks];
                const occupiedSlots = new Set(allDesks.map(d => d.location));

                // --- 1. DEFINISI KOMPONEN UI ---

                // UI: Zoom Controls (Tetap Sama)
                const zoomControlsHTML = `
                                    <div class="flex items-center border border-gray-200 rounded-lg p-1 bg-gray-50 mr-2 h-fit">
                                        <button id="zoom-out-btn" class="p-1.5 rounded-md hover:bg-gray-200 transition-colors" title="Zoom Out"><svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>
                                        <button id="zoom-reset-btn" class="px-2 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-200 rounded-md transition-colors w-12 text-center" title="Reset Zoom"><span id="zoom-level-display">${Math.round(currentZoom * 100)}%</span></button>
                                        <button id="zoom-in-btn" class="p-1.5 rounded-md hover:bg-gray-200 transition-colors" title="Zoom In"><svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>
                                    </div>
                                `;

                // --- PERBAIKAN 1: UI GRID CONTROL VERTIKAL (LESS SPACE) ---
                // Menggunakan flex-col agar tombol Baris dan Kolom menumpuk ke bawah
                const gridControlsHTML = `
                                    <div class="flex flex-col gap-1 bg-white p-1.5 rounded-lg border border-gray-300 shadow-sm mr-2 z-10 h-fit">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] font-bold text-gray-500 uppercase w-6">Row</span>
                                            <div class="flex items-center">
                                                <button id="dec-row-btn" class="w-6 h-6 flex items-center justify-center bg-gray-50 border border-gray-300 rounded-l hover:bg-red-50 hover:text-red-600 transition-colors text-xs font-bold text-gray-600">-</button>
                                                <span class="w-8 h-6 flex items-center justify-center bg-white border-y border-gray-300 text-xs font-bold text-gray-800">${labConfig.maxRows}</span>
                                                <button id="inc-row-btn" class="w-6 h-6 flex items-center justify-center bg-gray-50 border border-gray-300 rounded-r hover:bg-blue-50 hover:text-blue-600 transition-colors text-xs font-bold text-gray-600">+</button>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[10px] font-bold text-gray-500 uppercase w-6">Col</span>
                                            <div class="flex items-center">
                                                <button id="dec-col-btn" class="w-6 h-6 flex items-center justify-center bg-gray-50 border border-gray-300 rounded-l hover:bg-red-50 hover:text-red-600 transition-colors text-xs font-bold text-gray-600">-</button>
                                                <span class="w-8 h-6 flex items-center justify-center bg-white border-y border-gray-300 text-xs font-bold text-gray-800">${labConfig.maxCols}</span>
                                                <button id="inc-col-btn" class="w-6 h-6 flex items-center justify-center bg-gray-50 border border-gray-300 rounded-r hover:bg-blue-50 hover:text-blue-600 transition-colors text-xs font-bold text-gray-600">+</button>
                                            </div>
                                        </div>
                                    </div>
                                `;

                // --- 2. LOGIKA TOMBOL BERDASARKAN MODE ---
                let actionButtonsHTML = zoomControlsHTML;

                if (isEditMode) {
                    if (isAddDeskMode) {
                        // MODE: SEDANG MENAMBAH
                        // Gunakan ID berbeda (btn-finish-add) agar listener terpisah jelas
                        actionButtonsHTML += `
                                            <button id="btn-finish-add" class="h-10 px-5 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 transition-all flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                Selesai Menambah
                                            </button>
                                        `;
                    } else if (isMainDeleteMode) {
                        // MODE: SEDANG MENGHAPUS
                        actionButtonsHTML += `
                                            <button id="btn-finish-delete" class="h-10 px-5 py-2 text-sm font-bold text-white bg-rose-600 rounded-lg shadow-sm hover:bg-rose-700 transition-all flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                Selesai Menghapus
                                            </button>
                                        `;
                    } else {
                        // MODE: EDIT MENU UTAMA
                        // Masukkan Grid Control di sini agar rapi
                        actionButtonsHTML += gridControlsHTML;

                        actionButtonsHTML += `
                                            <div class="flex flex-col gap-2 h-fit">
                                                <button id="btn-start-add" class="px-4 py-1.5 text-sm font-semibold bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-sm text-left">
                                                    + Tambah Meja
                                                </button>
                                                <button id="btn-start-delete" class="px-4 py-1.5 text-sm font-semibold bg-white border border-gray-300 text-rose-600 rounded-lg hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm text-left">
                                                    - Hapus Meja
                                                </button>
                                            </div>
                                            <button id="edit-layout-btn" class="ml-2 h-10 px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors self-start">
                                                Simpan & Keluar
                                            </button>
                                        `;
                    }
                } else {
                    // MODE: VIEW ONLY
                    actionButtonsHTML += `
                                        <button id="edit-layout-btn" class="ml-2 px-4 py-2.5 text-sm font-semibold bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                                            Edit Denah
                                        </button>
                                    `;
                }

                // --- 3. RENDER CONTAINER UTAMA ---
                // Tambahkan items-start agar tombol tidak stretch ke bawah
                let containerHTML = `
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8">
                                    <div class="mb-6 flex flex-wrap items-start justify-between gap-4"> 
                                        <div>
                                            <h2 class="text-xl font-semibold text-gray-800">Denah Meja Laboratorium</h2>
                                            <div class="flex items-center gap-4 text-sm mt-2 flex-wrap">
                                                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-emerald-500 rounded-full"></span><span class="text-gray-600">Baik</span></div>
                                                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-amber-500 rounded-full"></span><span class="text-gray-600">Kurang</span></div>
                                                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-rose-500 rounded-full"></span><span class="text-gray-600">Rusak</span></div>
                                                <div class="flex items-center gap-2"><span class="w-3 h-3 bg-gray-300 rounded-full"></span><span class="text-gray-600">Kosong</span></div>
                                            </div>
                                        </div>

                                        <div class="flex items-start flex-wrap gap-2">
                                            ${actionButtonsHTML}
                                        </div>
                                    </div>
                                <div id="grid-scroller" class="overflow-x-auto pb-4 custom-scrollbar">
                                    <div id="desk-grid" class="grid gap-4 border border-dashed border-slate-300 bg-slate-50/50 rounded-xl p-8 overflow-hidden relative ${isEditMode ? 'edit-mode' : ''}" 
                                        style="grid-template-columns: repeat(${labConfig.maxCols}, minmax(130px, 1fr)); grid-template-rows: repeat(${labConfig.maxRows}, auto); zoom: ${currentZoom}">
                                `;

                // --- RENDER GRID ITEMS (Copy paste bagian loop allDesks dari kode asli Anda) ---
                allDesks.forEach(desk => {
                    const row = desk.location.charCodeAt(0) - 64;
                    const col = parseInt(desk.location.substring(1));
                    let bgColorClass, iconColor, conditionText, draggableAttr;

                    if (desk.isNew) {
                        bgColorClass = 'bg-blue-50 border-blue-400 border-dashed hover:bg-blue-100 cursor-pointer shadow-sm';
                        iconColor = 'text-blue-600';
                        conditionText = 'Baru';
                        draggableAttr = '';
                    } else {
                        draggableAttr = isEditMode ? 'draggable="true"' : '';
                        switch (desk.overall_condition) {
                            case 'item_rusak':
                            case 'component_rusak':
                                bgColorClass = 'bg-rose-50 border-rose-200 shadow-sm hover:shadow-md hover:border-rose-300';
                                iconColor = 'text-rose-500';
                                conditionText = 'Rusak';
                                break;
                            case 'item_tidak_lengkap':
                                bgColorClass = 'bg-amber-50 border-amber-200 shadow-sm hover:shadow-md hover:border-amber-300';
                                iconColor = 'text-amber-500';
                                conditionText = 'Tidak Lengkap';
                                break;
                            case 'item_kosong':
                                bgColorClass = 'bg-white border-gray-200 shadow-sm hover:shadow-md hover:border-gray-300';
                                iconColor = 'text-gray-400';
                                conditionText = 'Kosong';
                                break;
                            default:
                                bgColorClass = 'bg-emerald-50 border-emerald-200 shadow-sm hover:shadow-md hover:border-emerald-300';
                                iconColor = 'text-emerald-600';
                                conditionText = 'Baik';
                                break;
                        }
                    }
                    containerHTML +=
                        `<div id="desk-${desk.id || desk.location}" data-desk-id="${desk.id || ''}" data-location="${desk.location}" style="grid-area: ${row} / ${col};" class="desk-item group transition-all duration-200 flex flex-col items-center justify-center p-4 border rounded-xl min-h-[140px] ${bgColorClass} ${desk.isNew ? 'new-desk-item' : ''}" ${draggableAttr} title="${desk.isNew ? 'Klik dua kali untuk batal' : ''}">
                                            <div class="text-center pointer-events-none">
                                                <div class="mb-2 transition-transform group-hover:scale-110"><svg class="w-10 h-10 mx-auto ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
                                                <span class="font-bold text-lg text-gray-800 block select-none">${desk.location}</span>
                                                <span class="text-xs font-medium uppercase tracking-wide opacity-70 mt-1 inline-block select-none">${conditionText}</span>
                                            </div>
                                        </div>`;
                });

                // --- RENDER SLOT KOSONG (Copy paste loop empty slot) ---
                for (let r = 1; r <= labConfig.maxRows; r++) {
                    for (let c = 1; c <= labConfig.maxCols; c++) {
                        const location = `${String.fromCharCode(64 + r)}${c}`;
                        if (!occupiedSlots.has(location)) {
                            let slotClass = '', slotContent = '';
                            if (isEditMode) {
                                slotClass = isAddDeskMode ?
                                    'bg-white border-dashed border-slate-300 hover:bg-blue-50 hover:border-blue-400 cursor-pointer shadow-sm' :
                                    'bg-transparent border-dashed border-slate-200 opacity-100';
                                slotContent = isAddDeskMode ? `<span class="text-blue-300 text-3xl font-light">+</span>` :
                                    `<span class="text-slate-300 font-semibold text-sm">${location}</span>`;
                            } else {
                                slotClass = 'invisible';
                            }
                            containerHTML +=
                                `<div class="empty-slot transition-all duration-200 flex flex-col items-center justify-center border-2 rounded-xl min-h-[140px] ${slotClass}" data-location="${location}" style="grid-area: ${r} / ${c};">${slotContent}</div>`;
                        }
                    }
                }
                containerHTML += '</div></div></div>';
                deskContainer.innerHTML = containerHTML;

                // --- 6. SETUP LISTENERS ---
                setupCommonListeners(); // Setup ulang listener setiap kali render

                if (isEditMode) {
                    setupDragDropListeners();

                    // HANYA pasang listener resize Grid jika TIDAK sedang Add Mode dan TIDAK sedang Delete Mode
                    if (!isAddDeskMode && !isMainDeleteMode) {
                        setupGridResizeListeners();
                    }

                    if (isAddDeskMode) {
                        setupAddDeskListeners();
                        setupDeleteNewDeskListeners();
                    }
                    if (isMainDeleteMode) {
                        setupMainDeleteListeners();
                    }
                }

                const newScroller = document.getElementById('grid-scroller');
                if (newScroller) {
                    newScroller.scrollLeft = scrollPos.left;
                    newScroller.scrollTop = scrollPos.top;
                }
            }

            // Note: Paste all listener setup functions here from original code
            function setupMainDeleteListeners() {
                const existingDesks = document.querySelectorAll('.desk-item:not(.new-desk-item)');
                desksToMainDelete.clear();
                existingDesks.forEach(desk => {
                    const innerDiv = desk.querySelector('.text-center');
                    if (!innerDiv) return;
                    if (!desk.dataset.originalHtml) {
                        desk.dataset.originalHtml = innerDiv.innerHTML;
                    }
                    const deskLocation = desk.dataset.location;
                    const deleteFeedbackHtml =
                        `<div class="text-center pointer-events-none p-4"><svg class="w-12 h-12 mx-auto text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg><span class="font-bold text-sm text-rose-700 mt-2 block">Hapus ${deskLocation}?</span></div>`;
                    desk.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        if (isMouseDown) isMouseDown = false;
                        if (isDeletingMode) isDeletingMode = false;
                        isMouseDownMainDelete = true;
                        const deskId = desk.dataset.deskId;
                        if (!desksToMainDelete.has(deskId)) {
                            desksToMainDelete.add(deskId);
                            desk.classList.add('bg-rose-100', 'border-rose-400', 'border-dashed',
                                'opacity-90', 'scale-95');
                            innerDiv.innerHTML = deleteFeedbackHtml;
                        }
                    });
                    desk.addEventListener('mouseover', () => {
                        if (isMouseDownMainDelete) {
                            const deskId = desk.dataset.deskId;
                            if (!desksToMainDelete.has(deskId)) {
                                desksToMainDelete.add(deskId);
                                desk.classList.add('bg-rose-100', 'border-rose-400',
                                    'border-dashed', 'opacity-90', 'scale-95');
                                innerDiv.innerHTML = deleteFeedbackHtml;
                            }
                        }
                    });
                    desk.addEventListener('mouseleave', () => {
                        if (!isMouseDownMainDelete && !desksToMainDelete.has(desk.dataset.deskId)) {
                            desk.classList.remove('bg-rose-100', 'border-rose-400', 'border-dashed',
                                'opacity-90', 'scale-95');
                            innerDiv.innerHTML = desk.dataset.originalHtml;
                        }
                    });
                });
            }



            function setupCommonListeners() {
                // Zoom Controls
                const zoomInBtn = document.getElementById('zoom-in-btn');
                const zoomOutBtn = document.getElementById('zoom-out-btn');
                const zoomResetBtn = document.getElementById('zoom-reset-btn');

                if (zoomInBtn) zoomInBtn.addEventListener('click', () => {
                    if (currentZoom < maxZoom) { currentZoom = Math.min(maxZoom, currentZoom + zoomStep); applyZoom(); }
                });
                if (zoomOutBtn) zoomOutBtn.addEventListener('click', () => {
                    if (currentZoom > minZoom) { currentZoom = Math.max(minZoom, currentZoom - zoomStep); applyZoom(); }
                });
                if (zoomResetBtn) zoomResetBtn.addEventListener('click', () => autoZoomToFit());

                // Main Edit / Save Button
                const editLayoutBtn = document.getElementById('edit-layout-btn');
                if (editLayoutBtn) {
                    editLayoutBtn.addEventListener('click', async function () {
                        const deskGrid = document.getElementById('desk-grid');
                        const isCurrentlyEditMode = deskGrid && deskGrid.classList.contains('edit-mode');
                        if (isCurrentlyEditMode) {
                            const saveResult = await saveNewDesks();
                            if (!saveResult.success) { return; }
                            isAddDeskMode = false;
                            isMainDeleteMode = false;
                            renderDeskGrid(false);
                        } else {
                            renderDeskGrid(true);
                        }
                    });
                }

                // Grid Resize Controls (Row/Col)
                // (Listener dipindah ke setupGridResizeListeners agar lebih rapi, tapi inisialisasi di sini jika mau)

                // --- LOGIC TOMBOL TAMBAH & HAPUS YANG BARU ---

                // 1. Tombol MULAI Tambah (+ Tambah Meja)
                const btnStartAdd = document.getElementById('btn-start-add');
                if (btnStartAdd) {
                    btnStartAdd.addEventListener('click', () => {
                        isAddDeskMode = true;
                        isMainDeleteMode = false; // Reset delete mode if active
                        renderDeskGrid(true);
                    });
                }

                // 2. Tombol SELESAI Menambah (Ceklist)
                const btnFinishAdd = document.getElementById('btn-finish-add');
                if (btnFinishAdd) {
                    btnFinishAdd.addEventListener('click', async function () {
                        const saveResult = await saveNewDesks();
                        if (saveResult.success) {
                            isAddDeskMode = false;
                            renderDeskGrid(true);
                        }
                    });
                }

                // 3. Tombol MULAI Hapus (- Hapus Meja)
                const btnStartDelete = document.getElementById('btn-start-delete');
                if (btnStartDelete) {
                    btnStartDelete.addEventListener('click', () => {
                        isMainDeleteMode = true;
                        isAddDeskMode = false; // Reset add mode if active
                        renderDeskGrid(true);
                    });
                }

                // 4. Tombol SELESAI Menghapus
                const btnFinishDelete = document.getElementById('btn-finish-delete');
                if (btnFinishDelete) {
                    btnFinishDelete.addEventListener('click', () => {
                        isMainDeleteMode = false;
                        renderDeskGrid(true);
                    });
                }
            }

            function getOccupiedBounds() {
                // Gabungkan meja yang sudah tersimpan (desks) dan meja baru yang belum disave (newDesks)
                const allDesks = [...desks, ...newDesks];

                let maxRowFound = 0;
                let maxColFound = 0;

                allDesks.forEach(desk => {
                    // Lokasi format: "A1", "C5", dll.
                    // Ambil huruf pertama untuk Row
                    const rowChar = desk.location.charAt(0).toUpperCase();
                    // Ambil sisa string untuk Col
                    const colNum = parseInt(desk.location.substring(1));

                    // Konversi Huruf ke Angka (A=1, B=2, dst)
                    const rowIndex = rowChar.charCodeAt(0) - 64;

                    if (rowIndex > maxRowFound) maxRowFound = rowIndex;
                    if (colNum > maxColFound) maxColFound = colNum;
                });

                return { maxRow: maxRowFound, maxCol: maxColFound };
            }




            function setupGridResizeListeners() {
        const incRowBtn = document.getElementById('inc-row-btn');
        const decRowBtn = document.getElementById('dec-row-btn');
        const incColBtn = document.getElementById('inc-col-btn');
        const decColBtn = document.getElementById('dec-col-btn');

        // --- ROW CONTROLS ---
        if (incRowBtn) {
            incRowBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                labConfig.maxRows++;
                renderDeskGrid(true);
            });
        }

        if (decRowBtn) {
            decRowBtn.addEventListener('click', (e) => {
                e.stopPropagation();

                // 1. Cek batas meja yang ada
                const bounds = getOccupiedBounds();

                // 2. Jika Rows saat ini lebih besar dari posisi meja paling bawah, boleh kurangi
                if (labConfig.maxRows > bounds.maxRow) {
                    // Pastikan juga tidak kurang dari 1 (walaupun kosong)
                    if (labConfig.maxRows > 1) {
                        labConfig.maxRows--;
                        renderDeskGrid(true);
                    }
                } else {
                    // Tampilkan pesan error/warning jika mentok meja
                    showToast('Batas Maksimum', `Tidak bisa mengurangi baris. Ada meja di baris ke-${labConfig.maxRows} (${String.fromCharCode(64 + labConfig.maxRows)}).`, 'warning');
                }
            });
        }

        // --- COLUMN CONTROLS ---
        if (incColBtn) {
            incColBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                labConfig.maxCols++;
                renderDeskGrid(true);
            });
        }

        if (decColBtn) {
            decColBtn.addEventListener('click', (e) => {
                e.stopPropagation();

                // 1. Cek batas meja yang ada
                const bounds = getOccupiedBounds();

                // 2. Jika Cols saat ini lebih besar dari posisi meja paling kanan
                if (labConfig.maxCols > bounds.maxCol) {
                    if (labConfig.maxCols > 1) {
                        labConfig.maxCols--;
                        renderDeskGrid(true);
                    }
                } else {
                    showToast('Batas Maksimum', `Tidak bisa mengurangi kolom. Ada meja di kolom ke-${labConfig.maxCols}.`, 'warning');
                }
            });
        }
    }

                function setupDragDropListeners() {
                    if (isAddDeskMode || isMainDeleteMode) return;
                    const draggables = document.querySelectorAll('.desk-item');
                    const dropzones = document.querySelectorAll('.empty-slot, .desk-item');
                    draggables.forEach(draggable => {
                        draggable.addEventListener('dragstart', () => {
                            draggable.classList.add('dragging');
                        });
                        draggable.addEventListener('dragend', () => {
                            draggable.classList.remove('dragging');
                        });
                    });
                    dropzones.forEach(zone => {
                        zone.addEventListener('dragover', e => {
                            e.preventDefault();
                            const draggingElement = document.querySelector('.dragging');
                            if (draggingElement !== zone) {
                                zone.classList.add('drag-over');
                            }
                        });
                        zone.addEventListener('dragleave', () => {
                            zone.classList.remove('drag-over');
                        });
                        zone.addEventListener('drop', async e => {
                            e.preventDefault();
                            zone.classList.remove('drag-over');
                            const draggingElement = document.querySelector('.dragging');
                            if (!draggingElement || draggingElement === zone) return;
                            const deskId = draggingElement.dataset.deskId;
                            const newLocation = zone.dataset.location;
                            const labId = labSelector.value;
                            const originalDesks = JSON.parse(JSON.stringify(desks));
                            const movingDesk = desks.find(d => d.id == deskId);
                            const targetDesk = desks.find(d => d.location === newLocation);
                            if (targetDesk) {
                                const movingDeskOldLocation = movingDesk.location;
                                movingDesk.location = newLocation;
                                targetDesk.location = movingDeskOldLocation;
                            } else {
                                movingDesk.location = newLocation;
                            }
                            renderDeskGrid(true);
                            showLoading('Memindahkan Meja...');
                            try {
                                const response = await fetch(
                                    `/admin/labs/${labId}/desks/update/location/${deskId}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        location: newLocation
                                    })
                                });
                                if (!response.ok) throw new Error('Server response was not ok.');
                                const data = await response.json();
                                hideLoading();
                                if (data.success) {
                                    showToast('Berhasil', data.message ||
                                        `Posisi meja berhasil diperbarui.`, 'success');
                                } else {
                                    throw new Error(data.message ||
                                        'Gagal memperbarui posisi meja.');
                                }
                            } catch (error) {
                                hideLoading();
                                console.error('Error updating desk location:', error);
                                desks = originalDesks;
                                renderDeskGrid(true);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: error.message || 'Terjadi kesalahan jaringan.'
                                });
                            }
                        });
                    });
                }

                function setupAddDeskListeners() {
                    const emptySlots = document.querySelectorAll('.empty-slot');
                    selectedSlots.clear();
                    emptySlots.forEach(slot => {
                        slot.addEventListener('mousedown', (e) => {
                            e.preventDefault();
                            if (isDeletingMode) isDeletingMode = false;
                            isMouseDown = true;
                            const location = slot.dataset.location;
                            if (!selectedSlots.has(location)) {
                                selectedSlots.add(location);
                                slot.classList.add('bg-blue-200', 'border-blue-500');
                            }
                        });
                        slot.addEventListener('mouseover', () => {
                            if (isMouseDown) {
                                const location = slot.dataset.location;
                                if (!selectedSlots.has(location)) {
                                    selectedSlots.add(location);
                                    slot.classList.add('bg-blue-200', 'border-blue-500');
                                }
                            }
                        });
                    });
                }

                function setupDeleteNewDeskListeners() {
                    const newDeskItems = document.querySelectorAll('.new-desk-item');
                    newDeskItems.forEach(desk => {
                        desk.addEventListener('mousedown', (e) => {
                            e.preventDefault();
                            if (isMouseDown) isMouseDown = false;
                            isDeletingMode = true;
                            const location = desk.dataset.location;
                            if (!slotsToCancel.has(location)) {
                                slotsToCancel.add(location);
                                desk.classList.add('bg-rose-200', 'border-rose-500', 'opacity-60');
                            }
                        });
                        desk.addEventListener('mouseover', () => {
                            if (isDeletingMode) {
                                const location = desk.dataset.location;
                                if (!slotsToCancel.has(location)) {
                                    slotsToCancel.add(location);
                                    desk.classList.add('bg-rose-200', 'border-rose-500',
                                        'opacity-60');
                                }
                            }
                        });
                    });
                }


                labSelector.addEventListener('change', async function () {
                    if (typeof hideLayoutModal === 'function') hideLayoutModal();
                    showLoading('Memuat Denah Meja...');
                    deskContainer.innerHTML =
                        `<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8"><div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mr-4"></div><p class="text-gray-600 text-lg">Memuat data meja...</p></div></div>`;
                    try {
                        const response = await fetch(`/admin/labs/${this.value}/desks`);
                        if (!response.ok) throw new Error('Network response was not ok');
                        desks = await response.json();
                        let maxRow = 0,
                            maxCol = 0;
                        if (desks.length > 0) {
                            desks.forEach(d => {
                                const row = d.location.charCodeAt(0) - 64;
                                const col = parseInt(d.location.substring(1));
                                if (row > maxRow) maxRow = row;
                                if (col > maxCol) maxCol = col;
                            });
                        }
                        labConfig.maxRows = Math.max(5, maxRow);
                        labConfig.maxCols = Math.max(10, maxCol);
                        renderDeskGrid();
                        autoZoomToFit();
                        await loadLabStorage(this.value);
                    } catch (error) {
                        console.error('Error fetching desks:', error);
                        deskContainer.innerHTML =
                            `<div class="bg-white rounded-xl shadow-sm border border-red-200 p-8"><div class="text-center py-12"><svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-red-600 text-lg font-medium">Terjadi kesalahan saat mengambil data</p><p class="text-gray-500 mt-2">Silakan coba lagi atau hubungi administrator</p></div></div>`;
                    } finally {
                        hideLoading();
                    }
                });

                async function loadLabStorage(labId) {
                    const storageContainer = document.getElementById('lab-storage-container');
                    const storageContent = document.getElementById('lab-storage-content');
                    storageContainer.classList.remove('hidden');
                    storageContent.innerHTML = '<div class="text-center py-8 text-gray-500">Memuat data...</div>';
                    try {
                        const response = await fetch(`/admin/labs/${labId}/storage`);
                        if (!response.ok) throw new Error('Gagal memuat data lemari lab');
                        const data = await response.json();
                        renderLabStorage(data.items, data.components, labId);
                    } catch (error) {
                        console.error('Error loading lab storage:', error);
                        storageContent.innerHTML =
                            `<div class="text-center py-8 text-red-500">${error.message}</div>`;
                    }
                }

                function renderLabStorage(items, components, labId) {
                    const storageContent = document.getElementById('lab-storage-content');

                    // Jika TOTAL kosong (Items 0 DAN Components 0)
                    if (items.length === 0 && components.length === 0) {
                        storageContent.innerHTML =
                            `<div class="bg-gray-50 rounded-xl p-12 text-center border-2 border-dashed border-gray-300"><svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg><p class="text-gray-500 text-lg">Lemari Lab Kosong (Tidak ada Item maupun Komponen)</p></div>`;
                        return;
                    }

                    // Jika ada data salah satu, RENDER DUA KOLOM TETAP
                    let html = '<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">';

                    // --- KOLOM ITEMS ---
                    html += `<div class="bg-blue-50 rounded-xl p-6 border border-blue-200 h-full flex flex-col">`;
                    html += `  <div class="flex items-center gap-2 mb-4">
                                             <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                             <h3 class="text-lg font-bold text-blue-800">Items (${items.length})</h3>
                                           </div>`;

                    html += `<div class="space-y-3 max-h-96 overflow-y-auto flex-1">`;

                    if (items.length > 0) {
                        items.forEach(item => {
                            window.inventoryData.set(item.id, item);
                            const conditionClass = item.condition ? 'bg-green-100 text-green-800' :
                                'bg-red-100 text-red-800';
                            const conditionText = item.condition ? 'Baik' : 'Rusak';
                            const specs = item.spec_set_values?.map(s =>
                                `${s.spec_attributes?.name}: ${s.value}`).join(', ') || '-';

                            html += `
                                            <div class="bg-white p-4 rounded-lg border border-blue-200 hover:shadow-md transition-shadow">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div><p class="font-semibold text-gray-800">${item.name}</p><p class="text-xs text-gray-500 font-mono">${item.serial_code}</p></div>
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${conditionClass}">${conditionText}</span>
                                                </div>
                                                <p class="text-xs text-gray-600"><span class="font-semibold">Type:</span> ${item.type?.name || 'N/A'}</p>
                                                <p class="text-xs text-gray-600 truncate" title="${specs}"><span class="font-semibold">Spec:</span> ${specs}</p>
                                                <div class="mt-3 flex justify-end gap-2">
                                                    <button onclick="showDetailModal('${item.id}')" class="px-3 py-1 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold rounded transition-colors">Lihat Detail</button>
                                                    <button onclick="showItemActions('${item.id}', '${item.name}', null, '${labId}')" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded transition-colors">Aksi</button>
                                                    <button onclick="detachItemFromLab('${item.id}')" class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded transition-colors">Lepas dari Lab</button>
                                                </div>
                                            </div>`;
                        });
                    } else {
                        html +=
                            `<div class="text-center py-10 border-2 border-dashed border-blue-200 rounded-lg text-blue-400 italic text-sm">Tidak ada Item tersimpan.</div>`;
                    }
                    html += `  </div>`; // End scrolling div
                    html += `</div>`; // End Item Column

                    // --- KOLOM COMPONENTS ---
                    html += `<div class="bg-purple-50 rounded-xl p-6 border border-purple-200 h-full flex flex-col">`;
                    html += `  <div class="flex items-center gap-2 mb-4">
                                             <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                             <h3 class="text-lg font-bold text-purple-800">Components (${components.length})</h3>
                                           </div>`;

                    html += `<div class="space-y-3 max-h-96 overflow-y-auto flex-1">`;

                    if (components.length > 0) {
                        components.forEach(comp => {
                            window.inventoryData.set(comp.id, comp);
                            const conditionClass = comp.condition ? 'bg-green-100 text-green-800' :
                                'bg-red-100 text-red-800';
                            const conditionText = comp.condition ? 'Baik' : 'Rusak';
                            const specs = comp.spec_set_values?.map(s =>
                                `${s.spec_attributes?.name}: ${s.value}`).join(', ') || '-';

                            html += `
                                            <div class="bg-white p-4 rounded-lg border border-purple-200 hover:shadow-md transition-shadow">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div><p class="font-semibold text-gray-800">${comp.name}</p><p class="text-xs text-gray-500 font-mono">${comp.serial_code}</p></div>
                                                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${conditionClass}">${conditionText}</span>
                                                </div>
                                                <p class="text-xs text-gray-600"><span class="font-semibold">Type:</span> ${comp.type?.name || 'N/A'}</p>
                                                <p class="text-xs text-gray-600 truncate" title="${specs}"><span class="font-semibold">Spec:</span> ${specs}</p>
                                                <div class="mt-3 flex justify-end gap-2">
                                                    <button onclick="showDetailModal('${comp.id}')" class="px-3 py-1 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold rounded transition-colors">Lihat Detail</button>
                                                    <button onclick="showComponentActions('${comp.id}', '${comp.name}', null, '${labId}')" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded transition-colors">Aksi</button>
                                                    <button onclick="detachComponentFromLab('${comp.id}')" class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-semibold rounded transition-colors">Lepas dari Lab</button>
                                                </div>
                                            </div>`;
                        });
                    } else {
                        html +=
                            `<div class="text-center py-10 border-2 border-dashed border-purple-200 rounded-lg text-purple-400 italic text-sm">Tidak ada Component tersimpan.</div>`;
                    }
                    html += `  </div>`; // End scrolling div
                    html += `</div>`; // End Component Column

                    html += '</div>';
                    storageContent.innerHTML = html;
                }

                function renderAdditionalInfo(spec) {
                    if (!spec || !spec.set_values || spec.set_values.length === 0) return '';
                    let infoHtml = '<div class="mt-3 space-y-1">';
                    for (const setValue of spec.set_values) {
                        if (!setValue.spec_attributes) continue;
                        const key = setValue.spec_attributes.name;
                        const value = setValue.value;
                        const formattedKey = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                        infoHtml +=
                            `<div class="flex items-start gap-2 text-sm"><span class="text-gray-500 min-w-fit">${formattedKey}:</span><span class="text-gray-700 font-medium">${value}</span></div>`;
                    }
                    infoHtml += '</div>';
                    return infoHtml;
                }

                deskContainer.addEventListener('click', function (event) {
                    const deskGrid = document.getElementById('desk-grid');
                    if (deskGrid && deskGrid.classList.contains('edit-mode')) return;
                    const clickedDeskElement = event.target.closest('.desk-item');
                    if (!clickedDeskElement) return;

                    document.querySelectorAll('.desk-item').forEach(el => el.classList.remove('ring-4',
                        'ring-indigo-400', 'ring-opacity-50'));
                    clickedDeskElement.classList.add('ring-4', 'ring-indigo-400', 'ring-opacity-50');

                    const deskId = clickedDeskElement.dataset.deskId;
                    const selectedDesk = desks.find(d => d.id == deskId);

                    if (selectedDesk) {
                        const modalTitle = `Detail Inventaris Meja ${selectedDesk.location}`;
                        let modalBodyHTML = ``;

                        if (selectedDesk.items && selectedDesk.items.length > 0) {
                            modalBodyHTML += '<div class="space-y-4">';
                            selectedDesk.items.forEach((item) => {
                                window.inventoryData.set(item.id, item); // Store item for detail view
                                const itemConditionText = item.condition == 1 ? 'Baik' : 'Rusak';
                                const itemConditionClass = item.condition == 1 ?
                                    'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50';
                                const itemBorderClass = item.condition == 1 ? 'border-emerald-200' :
                                    'border-rose-200';
                                modalBodyHTML +=
                                    `<div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border ${itemBorderClass} transition-all duration-200"><div class="flex items-start justify-between mb-3"><div class="flex-1"><h4 class="font-bold text-lg text-gray-800">${item.name}</h4><p class="text-sm text-gray-500 font-mono mt-1">${item.serial_code}</p></div><span class="px-3 py-1 rounded-full text-sm font-semibold ${itemConditionClass}">${itemConditionText}</span></div>`;
                                modalBodyHTML += renderAdditionalInfo(item.spec);
                                modalBodyHTML +=
                                    `<div class="mt-3 flex justify-end gap-2"><button onclick="showDetailModal('${item.id}')" class="px-3 py-1 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold rounded transition-colors">Lihat Detail</button><button onclick="showItemActions('${item.id}', '${item.name}', '${selectedDesk.id}', null)" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded transition-colors">Aksi</button><button onclick="detachItemFromDesk('${item.id}')" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded transition-colors">Lepas dari Meja</button></div>`;

                                if (item.components && item.components.length > 0) {
                                    modalBodyHTML +=
                                        `<div class="mt-4 pt-4 border-t border-gray-200"><div class="flex items-center gap-2 mb-3"><svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg><p class="text-sm font-bold text-gray-700">Komponen (${item.components.length})</p></div><div class="space-y-3">`;
                                    item.components.forEach(component => {
                                        window.inventoryData.set(component.id,
                                            component); // Store component
                                        const compConditionText = component.condition == 1 ?
                                            'Baik' : 'Rusak';
                                        const compConditionClass = component.condition == 1 ?
                                            'text-emerald-600 bg-emerald-50' :
                                            'text-rose-600 bg-rose-50';
                                        const compBgClass = component.condition == 1 ?
                                            'bg-emerald-50/50' : 'bg-rose-50/50';
                                        modalBodyHTML +=
                                            `<div class="${compBgClass} p-4 rounded-lg border border-gray-200"><div class="flex items-start justify-between mb-2"><div><p class="font-semibold text-gray-800">${component.name}</p><p class="text-xs text-gray-500 font-mono mt-1">${component.serial_code}</p></div><span class="px-2 py-1 rounded-full text-xs font-semibold ${compConditionClass}">${compConditionText}</span></div>`;
                                        modalBodyHTML += renderAdditionalInfo(component.spec);
                                        modalBodyHTML +=
                                            `<div class="mt-3 flex justify-end gap-2"><button onclick="showDetailModal('${component.id}')" class="px-3 py-1 bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-semibold rounded transition-colors">Lihat Detail</button><button onclick="showComponentActions('${component.id}', '${component.name}', '${item.id}')" class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded transition-colors">Aksi</button><button onclick="detachComponentFromItem('${component.id}')" class="px-3 py-1 bg-purple-500 hover:bg-purple-600 text-white text-xs font-semibold rounded transition-colors">Lepas dari Item</button></div></div>`;
                                    });
                                    modalBodyHTML += '</div></div>';
                                }
                                modalBodyHTML += '</div>';
                            });
                            modalBodyHTML += '</div>';
                        } else {
                            modalBodyHTML +=
                                `<div class="bg-gray-50 rounded-xl p-12 text-center border-2 border-dashed border-gray-300"><svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg><p class="text-gray-500 text-lg">Tidak ada item yang terdaftar untuk meja ini</p></div>`;
                        }

                        if (typeof showLayoutModal === 'function') {
                            showLayoutModal(modalTitle, modalBodyHTML, deskId);
                        }
                    }
                });

                deskContainer.addEventListener('dblclick', function (event) {
                    const clickedDesk = event.target.closest('.new-desk-item');
                    const deskGrid = document.getElementById('desk-grid');
                    if (!clickedDesk || !deskGrid || !deskGrid.classList.contains('edit-mode')) return;
                    const location = clickedDesk.dataset.location;
                    cancelNewDesk(location);
                });

                // --- Layout Modal (Existing) ---
                const layoutModal = document.getElementById('layout-modal');
                const layoutModalArea = document.getElementById('layout-modal-area');
                const layoutModalTitle = document.getElementById('layout-modal-title');
                const layoutModalBody = document.getElementById('layout-modal-body');
                const layoutModalFooter = layoutModal.querySelector('.rounded-b');
                const overlay = document.getElementById('layout-modal-overlay');
                const closeButtonHeader = document.getElementById('layout-modal-close-button');
                const closeButtonFooter = document.getElementById('layout-modal-footer-close-button');
                if (layoutModalArea) {
                    layoutModalArea.classList.remove('max-w-3xl');
                    layoutModalArea.classList.add('max-w-5xl');
                }
                const addItemButton = document.createElement('button');
                addItemButton.id = 'layout-modal-add-item-button';
                addItemButton.type = 'button';
                addItemButton.className =
                    'text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-auto';
                addItemButton.textContent = 'Tambah Item ke Meja Ini';
                layoutModalFooter.prepend(addItemButton);

                window.showLayoutModal = (title, bodyHTML, deskId) => {
                    if (layoutModal && layoutModalTitle && layoutModalBody) {
                        layoutModalTitle.textContent = title;
                        layoutModalBody.innerHTML = bodyHTML;
                        addItemButton.dataset.deskId = deskId;
                        layoutModal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                };
                window.hideLayoutModal = () => {
                    if (layoutModal) {
                        layoutModal.classList.add('hidden');
                        if (document.getElementById('add-item-modal').classList.contains('hidden')) {
                            document.body.style.overflow = '';
                        }
                    }
                };
                if (closeButtonHeader) closeButtonHeader.addEventListener('click', window.hideLayoutModal);
                if (closeButtonFooter) closeButtonFooter.addEventListener('click', window.hideLayoutModal);
                if (overlay) {
                    overlay.addEventListener('click', function (event) {
                        if (event.target === overlay) {
                            window.hideLayoutModal();
                        }
                    });
                }
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && layoutModal && !layoutModal.classList.contains('hidden')) {
                        window.hideLayoutModal();
                    }
                });

                // ... (Rest of modal initializations: AddItemModal, ActionModal, AttachDeskMapModal - kept same as original) ...
                // [Paste the logic for initializeAddItemModal(), openAddItemModal(), closeAddItemModal(), loadFilterOptions(), etc here]
                // ... [Paste the logic for item actions here] ...

                // --- RE-INITIALIZING REQUIRED MODALS FROM ORIGINAL CODE ---
                const addItemModal = document.getElementById('add-item-modal');
                const addItemModalOverlay = document.getElementById('add-item-modal-overlay');
                const addItemCloseBtn = document.getElementById('add-item-modal-close-button');
                const addItemCancelBtn = document.getElementById('add-item-modal-footer-cancel-button');
                const addItemSaveBtn = document.getElementById('add-item-modal-footer-save-button');
                const itemSelectLoading = document.getElementById('item-select-loading');
                const itemFilterFieldset = document.getElementById('item-filter-fieldset');

                function initializeAddItemModal() {
                    tomSelectType = new TomSelect('#filter-type-select', {
                        create: false,
                        onChange: () => loadUnaffiliatedItems()
                    });
                    tomSelectAttr = new TomSelect('#filter-attr-select', {
                        create: false,
                        onChange: (attrId) => {
                            tomSelectValue.clear();
                            tomSelectValue.clearOptions();
                            if (attrId) {
                                const selectedAttr = allSpecAttributes.find(a => a.id == attrId);
                                if (selectedAttr && selectedAttr.spec_values) {
                                    const valueOptions = selectedAttr.spec_values.map(v => ({
                                        value: v.id,
                                        text: v.value
                                    }));
                                    tomSelectValue.addOptions(valueOptions);
                                    tomSelectValue.enable();
                                }
                            } else {
                                tomSelectValue.disable();
                            }
                            loadUnaffiliatedItems();
                        }
                    });
                    tomSelectValue = new TomSelect('#filter-value-select', {
                        create: false,
                        onChange: () => loadUnaffiliatedItems()
                    });
                    tomSelectItem = new TomSelect('#item-select-dropdown', {
                        create: false,
                        plugins: ['remove_button'],
                        render: {
                            no_results: function (data, escape) {
                                return '<div class="p-3 text-sm text-gray-500 text-center">Tidak ada item yang ditemukan. Coba ubah filter Anda.</div>';
                            }
                        }
                    });

                    addItemButton.addEventListener('click', openAddItemModal);
                    addItemCloseBtn.addEventListener('click', closeAddItemModal);
                    addItemCancelBtn.addEventListener('click', closeAddItemModal);
                    addItemSaveBtn.addEventListener('click', submitAddItems);
                    if (addItemModalOverlay) {
                        addItemModalOverlay.addEventListener('click', function (event) {
                            if (event.target === addItemModalOverlay) {
                                closeAddItemModal();
                            }
                        });
                    }
                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape' && addItemModal && !addItemModal.classList.contains(
                            'hidden')) {
                            closeAddItemModal();
                        }
                    });
                }

                async function openAddItemModal() {
                    currentDeskId = this.dataset.deskId;
                    if (!currentDeskId) return;
                    addItemModal.classList.remove('hidden');
                    showLoading('Memuat Filter & Item...');
                    await loadFilterOptions();
                    await loadUnaffiliatedItems();
                    hideLoading();
                }

                function closeAddItemModal() {
                    addItemModal.classList.add('hidden');
                    tomSelectType.clear();
                    tomSelectAttr.clear();
                    tomSelectValue.clear();
                    tomSelectItem.clear();
                    tomSelectValue.disable();
                    if (layoutModal.classList.contains('hidden')) {
                        document.body.style.overflow = '';
                    }
                }
                async function loadFilterOptions() {
                    itemFilterFieldset.disabled = true;
                    try {
                        const response = await fetch("{{ route('admin.items.filters') }}");
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message);
                        const filterData = data.data;
                        allSpecAttributes = filterData.specifications || [];
                        tomSelectType.clearOptions();
                        tomSelectType.addOptions(filterData.types.map(t => ({
                            value: t.id,
                            text: t.name
                        })));
                        tomSelectAttr.clearOptions();
                        tomSelectAttr.addOptions(allSpecAttributes.map(a => ({
                            value: a.id,
                            text: a.name
                        })));
                    } catch (error) {
                        console.error(error);
                        showToast('Error Filter', error.message, 'error');
                    } finally {
                        itemFilterFieldset.disabled = false;
                    }
                }
                async function loadUnaffiliatedItems() {
                    itemSelectLoading.classList.remove('hidden');
                    tomSelectItem.wrapper.classList.add('hidden');
                    tomSelectItem.clear();
                    tomSelectItem.clearOptions();
                    itemFilterFieldset.disabled = true;
                    const params = new URLSearchParams();
                    if (tomSelectType.getValue()) params.append('type_id', tomSelectType.getValue());
                    if (tomSelectValue.getValue()) params.append('spec_value_id', tomSelectValue.getValue());
                    try {
                        const response = await fetch(
                            `{{ route('admin.items.unaffiliated') }}?${params.toString()}`);
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message);
                        const items = data.data.items;
                        tomSelectItem.addOptions(items.map(i => ({
                            value: i.id,
                            text: `${i.name} (${i.serial_code})`
                        })));
                    } catch (error) {
                        console.error(error);
                        showToast('Error Item', error.message, 'error');
                    } finally {
                        itemSelectLoading.classList.add('hidden');
                        tomSelectItem.wrapper.classList.remove('hidden');
                        itemFilterFieldset.disabled = false;
                    }
                }
                async function submitAddItems() {
                    const itemIds = tomSelectItem.getValue();
                    if (!itemIds || itemIds.length === 0) {
                        showToast('Peringatan', 'Anda belum memilih item sama sekali.', 'warning');
                        return;
                    }
                    const result = await Swal.fire({
                        title: `Tambahkan ${itemIds.length} item?`,
                        text: 'Item yang dipilih akan ditambahkan ke meja ini.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Tambahkan!',
                        cancelButtonText: 'Batal'
                    });
                    if (!result.isConfirmed) return;
                    showLoading('Menambahkan Item...');
                    addItemSaveBtn.disabled = true;
                    try {
                        const response = await fetch(`/admin/desks/${currentDeskId}/items`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                item_ids: itemIds
                            })
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message);
                        hideLoading();
                        showToast('Berhasil', data.message, 'success');
                        closeAddItemModal();
                        window.hideLayoutModal();
                        labSelector.dispatchEvent(new Event('change'));
                    } catch (error) {
                        hideLoading();
                        Swal.fire('Gagal', error.message, 'error');
                    } finally {
                        addItemSaveBtn.disabled = false;
                    }
                }

                initializeAddItemModal();

                // Action Modal Logic
                const actionModal = document.getElementById('item-action-modal');
                const actionOverlay = document.getElementById('item-action-modal-overlay');
                const actionCloseBtn = document.getElementById('item-action-modal-close-btn');
                const closeActionModal = () => {
                    actionModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    currentActionItemId = null;
                    currentActionItemName = null;
                    currentActionItemType = 'item';
                    currentActionDeskId = null;
                    currentActionLabId = null;
                };
                actionCloseBtn.addEventListener('click', closeActionModal);
                actionOverlay.addEventListener('click', (e) => {
                    if (e.target === actionOverlay) closeActionModal();
                });
                document.getElementById('action-attach-desk-card').addEventListener('click', openAttachDeskMapModal);
                document.getElementById('action-attach-lab-card').addEventListener('click', handleAttachToLab);
                document.getElementById('action-attach-item-card').addEventListener('click', handleAttachToItem);
                document.getElementById('action-detach-desk-card').addEventListener('click', handleDetachFromDesk);
                document.getElementById('action-detach-lab-card').addEventListener('click', handleDetachFromLab);

                // Attach Desk Map Modal Logic
                const attachDeskModal = document.getElementById('attach-desk-map-modal');
                const attachDeskOverlay = document.getElementById('attach-desk-map-overlay');
                const attachDeskCloseBtn = document.getElementById('attach-desk-map-close-btn');
                const closeAttachDeskModal = () => {
                    attachDeskModal.classList.add('hidden');
                    if (tomSelectAttachLab) tomSelectAttachLab.clear();
                    document.getElementById('attach-desk-map-container').innerHTML =
                        '<div class="text-center py-12 text-gray-500">Pilih lab untuk melihat denah meja.</div>';
                    document.body.style.overflow = '';
                };
                attachDeskCloseBtn.addEventListener('click', closeAttachDeskModal);
                attachDeskOverlay.addEventListener('click', (e) => {
                    if (e.target === attachDeskOverlay) closeAttachDeskModal();
                });
                tomSelectAttachLab = new TomSelect('#attach-lab-selector', {
                    create: false,
                    placeholder: 'Pilih Lab...',
                    onChange: (labId) => {
                        if (labId) fetchDeskMapForAttach(labId);
                    }
                });

            }); // END DOMContentLoaded

            let currentActionItemId = null,
                currentActionItemName = null,
                currentActionItemType = 'item',
                currentActionDeskId = null,
                currentActionLabId = null,
                tomSelectAttachLab = null,
                attachDeskMapData = [];

            async function showItemActions(itemId, itemName, deskId = null, labId = null) {
                currentActionItemId = itemId;
                currentActionItemName = itemName;
                currentActionItemType = 'item';
                currentActionDeskId = deskId;
                currentActionLabId = labId;
                const modal = document.getElementById('item-action-modal');
                const title = document.getElementById('item-action-modal-title');
                const attachDeskCard = document.getElementById('action-attach-desk-card');
                const attachLabCard = document.getElementById('action-attach-lab-card');
                const attachItemCard = document.getElementById('action-attach-item-card');
                const detachDeskCard = document.getElementById('action-detach-desk-card');
                const detachLabCard = document.getElementById('action-detach-lab-card');
                title.textContent = `Aksi untuk ${itemName}`;
                attachDeskCard.classList.remove('hidden');
                attachLabCard.classList.remove('hidden');
                attachItemCard.classList.add('hidden');
                detachDeskCard.classList.add('hidden');
                detachLabCard.classList.add('hidden');
                const attachDeskText = attachDeskCard.querySelector('.font-semibold');
                attachDeskText.textContent = deskId ? 'Pasang ke Meja Lain' : 'Pasang ke Meja';
                if (deskId) {
                    attachLabCard.classList.add('hidden');
                    detachDeskCard.classList.remove('hidden');
                } else if (labId) {
                    detachLabCard.classList.remove('hidden');
                }
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            async function openAttachDeskMapModal() {
                const modal = document.getElementById('attach-desk-map-modal');
                const itemNameEl = document.getElementById('attach-desk-item-name');
                itemNameEl.textContent = currentActionItemName;
                modal.classList.remove('hidden');
                document.getElementById('item-action-modal').classList.add('hidden');
                showLoading('Memuat Daftar Lab...');
                try {
                    const response = await fetch('/admin/labs/list');
                    if (!response.ok) throw new Error('Gagal memuat daftar lab.');
                    const labs = await response.json();
                    if (tomSelectAttachLab) {
                        tomSelectAttachLab.clearOptions();
                        tomSelectAttachLab.addOptions(labs.map(lab => ({
                            value: lab.id,
                            text: lab.name
                        })));
                        hideLoading();
                        tomSelectAttachLab.open();
                    }
                } catch (error) {
                    hideLoading();
                    Swal.fire('Error', error.message, 'error');
                    modal.classList.add('hidden');
                }
            }

            async function fetchDeskMapForAttach(labId) {
                const container = document.getElementById('attach-desk-map-container');
                container.innerHTML =
                    '<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div></div>';
                try {
                    const response = await fetch(`/admin/labs/${labId}/desks`);
                    if (!response.ok) throw new Error('Gagal memuat denah meja.');
                    attachDeskMapData = await response.json();
                    let maxRow = 5,
                        maxCol = 10;
                    if (attachDeskMapData.length > 0) {
                        attachDeskMapData.forEach(d => {
                            const row = d.location.charCodeAt(0) - 64;
                            const col = parseInt(d.location.substring(1));
                            if (row > maxRow) maxRow = row;
                            if (col > maxCol) maxCol = col;
                        });
                    }
                    renderAttachDeskMap(attachDeskMapData, maxRow, maxCol, currentActionItemType === 'component');
                } catch (error) {
                    container.innerHTML = `<div class="text-center py-12 text-red-500">${error.message}</div>`;
                }
            }

            function renderAttachDeskMap(desks, maxRows, maxCols, isComponentMode = false) {
                const container = document.getElementById('attach-desk-map-container');
                const instruction = isComponentMode ? 'Klik pada meja untuk melihat items' :
                    'Klik pada meja untuk memasang item';
                let html =
                    `<div class="bg-white rounded-xl border border-gray-200 p-6"><div class="mb-4"><h3 class="text-lg font-semibold text-gray-800 mb-2">Denah Meja</h3><p class="text-sm text-gray-600">${instruction}</p></div><div class="overflow-x-auto pb-4"><div class="grid gap-3 border-2 border-slate-300 p-6 min-w-fit" style="grid-template-columns: repeat(${maxCols}, minmax(120px, 1fr)); grid-template-rows: repeat(${maxRows}, auto);">`;
                const occupiedSlots = new Set(desks.map(d => d.location));
                desks.forEach(desk => {
                    const row = desk.location.charCodeAt(0) - 64;
                    const col = parseInt(desk.location.substring(1));
                    let bgClass, iconColor, conditionText;
                    switch (desk.overall_condition) {
                        case 'item_rusak':
                        case 'component_rusak':
                            bgClass = 'bg-rose-50 border-rose-300 hover:bg-rose-100';
                            iconColor = 'text-rose-600';
                            conditionText = 'Rusak';
                            break;
                        case 'item_tidak_lengkap':
                            bgClass = 'bg-amber-50 border-amber-300 hover:bg-amber-100';
                            iconColor = 'text-amber-600';
                            conditionText = 'Tidak Lengkap';
                            break;
                        case 'item_kosong':
                            bgClass = 'bg-gray-50 border-gray-300 hover:bg-gray-100';
                            iconColor = 'text-gray-500';
                            conditionText = 'Kosong';
                            break;
                        default:
                            bgClass = 'bg-emerald-50 border-emerald-300 hover:bg-emerald-100';
                            iconColor = 'text-emerald-600';
                            conditionText = 'Baik';
                    }
                    const clickHandler = isComponentMode ? `showDeskItems('${desk.id}', '${desk.location}')` :
                        `confirmAttachToDesk('${desk.id}', '${desk.location}')`;
                    html +=
                        `<div data-desk-id="${desk.id}" data-desk-location="${desk.location}" style="grid-area: ${row} / ${col};" class="attach-desk-card cursor-pointer transition-all duration-200 flex flex-col items-center justify-center p-4 border-2 rounded-lg min-h-32 ${bgClass} hover:ring-2 hover:ring-indigo-400" onclick="${clickHandler}"><svg class="w-6 h-6 ${iconColor} mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg><span class="font-bold text-base text-gray-800">${desk.location}</span><span class="text-xs text-gray-600 mt-1">${conditionText}</span></div>`;
                });
                for (let r = 1; r <= maxRows; r++) {
                    for (let c = 1; c <= maxCols; c++) {
                        const location = `${String.fromCharCode(64 + r)}${c}`;
                        if (!occupiedSlots.has(location)) html +=
                            `<div style="grid-area: ${r} / ${c}; visibility: hidden;"></div>`;
                    }
                }
                html += '</div></div></div>';
                container.innerHTML = html;
            }

            async function confirmAttachToDesk(deskId, deskLocation) {
                const result = await Swal.fire({
                    title: `Pasang ke Meja ${deskLocation}?`,
                    text: `Item '${currentActionItemName}' akan dipasang ke meja ${deskLocation}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pasang!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3b82f6'
                });
                if (!result.isConfirmed) return;
                showLoading('Memasang Item...');
                try {
                    const response = await fetch(`/admin/items/${currentActionItemId}/attach-desk/${deskId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal memasang item');
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000
                    }).then(() => location.reload());
                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                }
            }

            async function handleAttachToLab() {
                const result = await Swal.fire({
                    title: 'Pilih Laboratorium',
                    html: '<select id="swal-lab-select" class="swal2-input" style="width: 80%; padding:0"><option value="">-- Pilih Lab --</option></select>',
                    showCancelButton: true,
                    confirmButtonText: 'Pasang',
                    cancelButtonText: 'Batal',
                    didOpen: async () => {
                        const select = document.getElementById('swal-lab-select');
                        try {
                            const response = await fetch('/admin/labs/list');
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
                    showLoading('Memasang ke Lab...');
                    try {
                        const url = currentActionItemType === 'item' ?
                            `/admin/items/${currentActionItemId}/attach-lab/${result.value}` :
                            `/admin/components/${currentActionItemId}/attach-lab/${result.value}`;
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Gagal memasang ke lab');
                        hideLoading();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000
                        }).then(() => location.reload());
                    } catch (error) {
                        hideLoading();
                        Swal.fire('Gagal', error.message, 'error');
                    }
                }
            }

            async function handleDetachFromDesk() {
                const result = await Swal.fire({
                    title: 'Lepas dari Meja?',
                    text: `Item '${currentActionItemName}' akan dilepas dari meja`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lepas!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ef4444'
                });
                if (!result.isConfirmed) return;
                showLoading('Melepas Item...');
                try {
                    const response = await fetch(`/admin/items/${currentActionItemId}/detach-desk`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal melepas item');
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000
                    }).then(() => location.reload());
                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                }
            }

            async function handleDetachFromLab() {
                const result = await Swal.fire({
                    title: 'Lepas dari Lab?',
                    text: `${currentActionItemType === 'item' ? 'Item' : 'Component'} '${currentActionItemName}' akan dilepas dari lab`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lepas!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#6366f1'
                });
                if (!result.isConfirmed) return;
                showLoading('Melepas...');
                try {
                    const url = currentActionItemType === 'item' ?
                        `/admin/items/${currentActionItemId}/detach-lab` :
                        `/admin/components/${currentActionItemId}/detach-lab`;
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal melepas');
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000
                    }).then(() => location.reload());
                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                }
            }

            async function showComponentActions(componentId, componentName, itemId = null, labId = null) {
                currentActionItemId = componentId;
                currentActionItemName = componentName;
                currentActionItemType = 'component';
                currentActionDeskId = null;
                currentActionLabId = labId;
                const modal = document.getElementById('item-action-modal');
                const title = document.getElementById('item-action-modal-title');
                const attachDeskCard = document.getElementById('action-attach-desk-card');
                const attachLabCard = document.getElementById('action-attach-lab-card');
                const attachItemCard = document.getElementById('action-attach-item-card');
                const detachDeskCard = document.getElementById('action-detach-desk-card');
                const detachLabCard = document.getElementById('action-detach-lab-card');
                title.textContent = `Aksi untuk ${componentName}`;
                attachDeskCard.classList.add('hidden');
                attachLabCard.classList.add('hidden');
                attachItemCard.classList.remove('hidden');
                detachDeskCard.classList.add('hidden');
                detachLabCard.classList.add('hidden');
                const attachItemText = attachItemCard.querySelector('.font-semibold');
                attachItemText.textContent = itemId ? 'Pasang ke Item Lain' : 'Pasang ke Item';
                if (itemId) {
                    detachDeskCard.classList.remove('hidden');
                } else {
                    attachLabCard.classList.remove('hidden');
                    if (labId) {
                        detachLabCard.classList.remove('hidden');
                    }
                }
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            async function handleAttachToItem() {
                document.getElementById('item-action-modal').classList.add('hidden');
                document.getElementById('attach-desk-map-modal').classList.remove('hidden');
                document.getElementById('attach-desk-item-name').textContent = currentActionItemName;
                showLoading('Memuat Daftar Lab...');
                try {
                    const response = await fetch('/admin/labs/list');
                    if (!response.ok) throw new Error('Gagal memuat daftar lab.');
                    const labs = await response.json();
                    if (tomSelectAttachLab) {
                        tomSelectAttachLab.clearOptions();
                        tomSelectAttachLab.addOptions(labs.map(lab => ({
                            value: lab.id,
                            text: lab.name
                        })));
                        hideLoading();
                        tomSelectAttachLab.open();
                    }
                } catch (error) {
                    hideLoading();
                    Swal.fire('Error', error.message, 'error');
                    document.getElementById('attach-desk-map-modal').classList.add('hidden');
                }
            }

            async function showDeskItems(deskId, deskLocation) {
                const desk = attachDeskMapData.find(d => d.id == deskId);
                if (!desk || !desk.items || desk.items.length === 0) {
                    Swal.fire('Info', `Meja ${deskLocation} tidak memiliki item`, 'info');
                    return;
                }
                let itemsHtml = '<div class="space-y-2 max-h-96 overflow-y-auto">';
                desk.items.forEach(item => {
                    itemsHtml +=
                        `<div class="p-3 border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors" onclick="confirmAttachToItem('${item.id}', '${item.name}')"><p class="font-semibold text-gray-800">${item.name}</p><p class="text-xs text-gray-500">${item.serial_code}</p></div>`;
                });
                itemsHtml += '</div>';
                Swal.fire({
                    title: `Items di Meja ${deskLocation}`,
                    html: itemsHtml,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: 'Tutup',
                    width: '500px'
                });
            }

            async function confirmAttachToItem(itemId, itemName) {
                Swal.close();
                const result = await Swal.fire({
                    title: `Pasang ke Item ${itemName}?`,
                    text: `Component '${currentActionItemName}' akan dipasang ke item ${itemName}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Pasang!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#a855f7'
                });
                if (!result.isConfirmed) return;
                showLoading('Memasang Component...');
                try {
                    const response = await fetch(`/admin/items/${itemId}/attach-component/${currentActionItemId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal memasang component');
                    hideLoading();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Component berhasil dipasang ke item',
                        timer: 2000
                    }).then(() => location.reload());
                } catch (error) {
                    hideLoading();
                    Swal.fire('Gagal', error.message, 'error');
                }
            }

            window.showItemActions = showItemActions;
            window.showComponentActions = showComponentActions;
            window.confirmAttachToDesk = confirmAttachToDesk;
            window.showDeskItems = showDeskItems;
            window.confirmAttachToItem = confirmAttachToItem;

            // --- FUNGSI MODAL RUANGAN LAB ---
            function openAddRoomModal() {
                document.getElementById('room-modal-title').textContent = 'Tambah Ruangan Baru';
                const form = document.getElementById('room-form');
                form.action = "{{ route('rooms.store') }}";
                document.getElementById('room-form-method').value = 'POST';
                document.getElementById('room-input-code').value = '';
                document.getElementById('room-input-name').value = '';
                document.getElementById('room-input-capacity').value = '50';
                document.getElementById('room-input-type').value = 'internal';
                document.getElementById('room-input-notes').value = '';
                document.getElementById('room-input-manual').checked = false;
                document.getElementById('room-input-active').checked = true;
                document.getElementById('room-form-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function openEditRoomModal(room) {
                document.getElementById('room-modal-title').textContent = 'Ubah Ruangan: ' + room.name;
                const form = document.getElementById('room-form');
                form.action = `/admin/academics-schedule/rooms/${room.id}`;
                document.getElementById('room-form-method').value = 'PUT';
                document.getElementById('room-input-code').value = room.code || '';
                document.getElementById('room-input-name').value = room.name || '';
                document.getElementById('room-input-capacity').value = room.capacity || 50;
                document.getElementById('room-input-type').value = room.type || 'internal';
                document.getElementById('room-input-notes').value = room.notes || '';
                document.getElementById('room-input-manual').checked = !!room.is_manual_only;
                document.getElementById('room-input-active').checked = !!room.is_active;
                document.getElementById('room-form-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeRoomModal() {
                document.getElementById('room-form-modal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            window.openAddRoomModal = openAddRoomModal;
            window.openEditRoomModal = openEditRoomModal;
            window.closeRoomModal = closeRoomModal;
        </script>
@endsection