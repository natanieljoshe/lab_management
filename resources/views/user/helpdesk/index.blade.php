@extends('layouts.user')

@section('title', 'Formulir Helpdesk & Pengaduan')

@section('style')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'petra-blue': '#1a237e',
                        'petra-yellow': '#ffca28',
                        'petra-wood': '#8b4513',
                        'petra-darkgray': '#424242',
                        'petra-gray': '#f1f5f9',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --petra-blue: #1a237e;
            --petra-yellow: #ffca28;
            --petra-wood: #8b4513;
            --petra-darkgray: #424242;
            --petra-gray: #f1f5f9;
        }

        .booking-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid #e2e8f0;
            background: white;
        }

        .booking-card:hover {
            border-color: var(--petra-blue);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .booking-card.active {
            border-color: var(--petra-blue) !important;
            background-color: rgba(26, 35, 126, 0.05);
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
        }

        .desk-card {
            transition: all 0.2s ease;
            min-height: 52px;
            border: 2px solid #cbd5e1;
            border-radius: 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            cursor: pointer;
            user-select: none;
            position: relative;
        }

        .desk-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--petra-blue);
        }

        .desk-card.selected {
            border-color: var(--petra-blue) !important;
            background-color: rgba(26, 35, 126, 0.1) !important;
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.2);
            z-index: 10;
        }

        .item-card {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            background: white;
            transition: all 0.2s;
        }

        .item-card:hover {
            border-color: var(--petra-blue);
            transform: translateY(-1px);
        }

        .item-card.selected {
            border-color: var(--petra-blue);
            background-color: rgba(26, 35, 126, 0.03);
        }

        .form-input {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
            width: 100%;
        }

        .form-input:focus {
            border-color: var(--petra-blue);
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
            outline: none;
        }

        .helpdesk-page {
            background: #f7f8fa;
        }

        .helpdesk-form-shell {
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        }

        .helpdesk-form-header {
            background: #fff;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
        }

        .helpdesk-form-header p { color: #6b7280; }
        .helpdesk-form-header a {
            background: #f8fafc;
            color: var(--petra-blue);
            border: 1px solid #dbe2ea;
        }
        .helpdesk-form-header a:hover { background: #eef2f7; }

        .helpdesk-page .booking-card {
            border-width: 1px;
            border-radius: .75rem;
            box-shadow: none;
        }
        .helpdesk-page .booking-card:hover {
            transform: none;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .05);
        }
        .helpdesk-page .booking-card.active {
            box-shadow: inset 0 0 0 1px var(--petra-blue);
        }
        .helpdesk-page .desk-card,
        .helpdesk-page .item-card,
        .helpdesk-page .form-input { border-width: 1px; }
        .helpdesk-page .desk-card:hover,
        .helpdesk-page .item-card:hover { transform: none; box-shadow: none; }
        .helpdesk-page .form-input { border-radius: .625rem; }
        .helpdesk-step {
            width: 1.75rem;
            height: 1.75rem;
            font-size: .75rem;
            flex-shrink: 0;
        }
        .helpdesk-page h3 { font-size: 1rem; letter-spacing: .01em; }
        .helpdesk-page .type-card {
            display: grid;
            grid-template-columns: 2.5rem minmax(0, 1fr);
            column-gap: .75rem;
            row-gap: .15rem;
            text-align: left;
            padding: 1rem;
        }
        .helpdesk-page .type-card > div:first-child {
            width: 2.5rem;
            height: 2.5rem;
            margin: 0;
            border-radius: .5rem;
            flex-shrink: 0;
            grid-row: span 2;
        }
        .helpdesk-page .type-card h4 { font-size: .95rem; margin-bottom: .25rem; }
        .helpdesk-page .type-card p { grid-column: 2; }
    </style>
@endsection

@section('body')
    @include('user.partials.navbar')

    <div class="helpdesk-page min-h-screen pt-24 pb-12">
        <div class="container mx-auto px-4 max-w-6xl">

            {{-- Hero Header --}}
            <div class="mb-8 max-w-3xl">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-gray-500 mb-3">Pusat bantuan laboratorium</p>
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-petra-blue mb-3">Buat laporan</h1>
                <p class="text-gray-600 max-w-2xl">
                    Laporkan kerusakan fasilitas laboratorium atau sampaikan keluhan dan saran untuk kenyamanan akademik bersama
                </p>
            </div>

            {{-- Main Form Card --}}
            <div class="helpdesk-form-shell bg-white overflow-hidden mb-8">

                {{-- Form Header Banner --}}
                <div class="helpdesk-form-header p-6 md:p-8">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Apa yang ingin Anda laporkan?</h2>
                            <p class="mt-1">Pilih jenis laporan, lalu lengkapi detailnya.</p>
                        </div>
                        <div>
                            <a href="{{ route('user.helpdesk.history') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm transition-all">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Riwayat Laporan Saya</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-6 md:p-8">

                    {{-- Step 1: Tipe Pengajuan --}}
                    <div class="mb-12">
                        <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <span class="helpdesk-step flex items-center justify-center rounded-full bg-petra-blue text-white">1</span>
                            Pilih Jenis Pengajuan
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Opsi 1: Kerusakan Barang -->
                            <label class="cursor-pointer" onclick="switchHelpdeskTab('repair')">
                                <input type="radio" name="helpdesk_service_type" value="repair" id="radio_type_repair" class="sr-only peer" checked>
                                <div id="card_type_repair" class="type-card booking-card active p-6 text-center rounded-xl peer-checked:border-petra-blue">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fa-solid fa-screwdriver-wrench text-2xl text-petra-blue"></i>
                                    </div>
                                    <h4 class="font-bold text-lg text-petra-blue mb-2">Lapor Kerusakan</h4>
                                    <p class="text-sm text-gray-600">Perbaikan perangkat PC, monitor, periferal, atau komponen meja</p>
                                </div>
                            </label>

                            <!-- Opsi 2: Keluhan & Saran -->
                            <label class="cursor-pointer" onclick="switchHelpdeskTab('feedback')">
                                <input type="radio" name="helpdesk_service_type" value="feedback" id="radio_type_feedback" class="sr-only peer">
                                <div id="card_type_feedback" class="type-card booking-card p-6 text-center rounded-xl peer-checked:border-petra-blue">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <i class="fa-solid fa-comments text-2xl text-petra-yellow"></i>
                                    </div>
                                    <h4 class="font-bold text-lg text-petra-blue mb-2">Keluhan & Saran</h4>
                                    <p class="text-sm text-gray-600">Saran fasilitas lab, kebersihan, AC, proyektor, atau kendala pelayanan</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- TAB 1: FORM LAPOR KERUSAKAN BARANG                      --}}
                    {{-- ========================================================= --}}
                    <div id="tab-pane-repair">
                        <form id="repair-form" class="space-y-10">
                            @csrf

                            {{-- Step 2: Lokasi & Perangkat --}}
                            <div>
                                <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">2</span>
                                    Lokasi & Perangkat Laboratorium
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label for="repair-lab" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Pilih Laboratorium <span class="text-red-500">*</span>
                                        </label>
                                        <select id="repair-lab" required class="form-input bg-white">
                                            <option value="">-- Pilih Laboratorium --</option>
                                            @foreach ($labs as $lab)
                                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Pilih laboratorium tempat barang yang rusak berada.</p>
                                    </div>

                                    <div id="device-source-section" class="hidden">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Penempatan Perangkat <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="cursor-pointer" onclick="toggleDeviceSource('desk')">
                                                <input type="radio" name="device_source" value="desk" checked class="sr-only">
                                                <div id="source-desk-card" class="booking-card active p-3 rounded-lg text-center">
                                                    <i class="fa-solid fa-desktop text-petra-blue mb-1"></i>
                                                    <div class="text-xs font-bold text-gray-800">Meja Praktikum</div>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer" onclick="toggleDeviceSource('storage')">
                                                <input type="radio" name="device_source" value="storage" class="sr-only">
                                                <div id="source-storage-card" class="booking-card p-3 rounded-lg text-center">
                                                    <i class="fa-solid fa-box-open text-gray-500 mb-1"></i>
                                                    <div class="text-xs font-bold text-gray-800">Alat Umum / Storage</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pilihan Meja Grid --}}
                                <div id="desk-selection-wrapper" class="hidden space-y-3 pt-4 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-semibold text-gray-700">Pilih Meja Praktikum:</label>
                                        <span id="current-location-badge" class="px-3 py-1 bg-blue-100 text-petra-blue text-xs font-bold rounded-full">
                                            Pilih meja
                                        </span>
                                    </div>
                                    <div id="desk-buttons-container" class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200 max-h-48 overflow-y-auto">
                                        {{-- Diisi secara dinamis oleh JavaScript --}}
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3: Daftar Barang & Masalah --}}
                            <div id="items-selection-section" class="hidden">
                                <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">3</span>
                                    Centang Barang & Deskripsikan Kerusakan <span class="text-red-500">*</span>
                                </h3>

                                <div id="items-list-container" class="space-y-4">
                                    {{-- Render item list oleh JavaScript --}}
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div id="repair-submit-section" class="hidden pt-6 border-t border-gray-200 flex justify-end">
                                <button type="submit" class="bg-petra-blue text-white px-8 py-3.5 rounded-xl shadow-md hover:bg-blue-800 transition-all font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Kirim Laporan Kerusakan</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- TAB 2: FORM KELUHAN & SARAN (FEEDBACK)                    --}}
                    {{-- ========================================================= --}}
                    <div id="tab-pane-feedback" class="hidden">
                        <form id="feedback-form" class="space-y-8">
                            @csrf

                            <div>
                                <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full bg-petra-blue text-white text-sm">2</span>
                                    Detail Keluhan / Saran Anda
                                </h3>

                                <div class="space-y-6">
                                    {{-- Kategori: Keluhan vs Saran --}}
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            Kategori Masukan <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <label class="cursor-pointer" onclick="toggleFeedbackType('KELUHAN')">
                                                <input type="radio" name="feedback_type" value="KELUHAN" checked class="sr-only peer">
                                                <div id="type-keluhan-card" class="booking-card active p-4 rounded-xl flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-bold text-gray-900 text-sm">Keluhan Fasilitas</h5>
                                                        <p class="text-xs text-gray-500">AC tidak dingin, proyektor redup, kebersihan, pelayanan</p>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="cursor-pointer" onclick="toggleFeedbackType('SARAN')">
                                                <input type="radio" name="feedback_type" value="SARAN" class="sr-only peer">
                                                <div id="type-saran-card" class="booking-card p-4 rounded-xl flex items-center gap-3">
                                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa-solid fa-lightbulb"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-bold text-gray-900 text-sm">Saran & Rekomendasi</h5>
                                                        <p class="text-xs text-gray-500">Usulan software baru, layout lab, aturan praktikum</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Cakupan Lab --}}
                                    <div>
                                        <label for="feedback-lab" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Terkait Laboratorium Spesifik (Opsional)
                                        </label>
                                        <select id="feedback-lab" class="form-input bg-white">
                                            <option value="">Fasilitas Umum / Semua Lab</option>
                                            @foreach ($labs as $lab)
                                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Subjek --}}
                                    <div>
                                        <label for="feedback-subject" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Judul / Subjek <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="feedback-subject" required maxlength="150"
                                            placeholder="Contoh: AC Lab P2.04 tidak dingin pada sesi praktikum sore"
                                            class="form-input">
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div>
                                        <label for="feedback-description" class="block text-sm font-semibold text-gray-700 mb-2">
                                            Uraian Lengkap Keluhan / Saran <span class="text-red-500">*</span>
                                        </label>
                                        <textarea id="feedback-description" required rows="5" maxlength="2000"
                                            placeholder="Tuliskan keluhan atau saran Anda secara rinci, termasuk waktu kejadian atau kondisi terkait..."
                                            class="form-input"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-6 border-t border-gray-200 flex justify-end">
                                <button type="submit" class="bg-petra-blue text-white px-8 py-3.5 rounded-xl shadow-md hover:bg-blue-800 transition-all font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Kirim Tiket Keluhan/Saran</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            {{-- Contact --}}
            <div class="helpdesk-contact bg-white rounded-xl border border-gray-200 p-6 md:p-7">
                <div class="flex flex-col md:flex-row md:items-center gap-5">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fa-solid fa-headset text-xl text-petra-blue"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-petra-blue mb-1">Butuh bantuan?</h3>
                        <p class="text-gray-600 mb-3">Jika kesulitan mengisi formulir, hubungi administrator laboratorium.</p>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700">
                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-envelope text-petra-blue"></i>lab@petra.ac.id</span>
                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-phone text-petra-blue"></i>(031) 1234-5678</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    let currentLabData = null;
    let selectedDeskId = null;

    function switchHelpdeskTab(type) {
        const cardRepair = document.getElementById('card_type_repair');
        const cardFeedback = document.getElementById('card_type_feedback');
        const paneRepair = document.getElementById('tab-pane-repair');
        const paneFeedback = document.getElementById('tab-pane-feedback');

        if (type === 'repair') {
            cardRepair.classList.add('active');
            cardFeedback.classList.remove('active');
            paneRepair.classList.remove('hidden');
            paneFeedback.classList.add('hidden');
        } else {
            cardFeedback.classList.add('active');
            cardRepair.classList.remove('active');
            paneFeedback.classList.remove('hidden');
            paneRepair.classList.add('hidden');
        }
    }

    function toggleFeedbackType(type) {
        const cardKeluhan = document.getElementById('type-keluhan-card');
        const cardSaran = document.getElementById('type-saran-card');
        if (type === 'KELUHAN') {
            cardKeluhan.classList.add('active');
            cardSaran.classList.remove('active');
        } else {
            cardSaran.classList.add('active');
            cardKeluhan.classList.remove('active');
        }
    }

    // Load data perangkat lab saat lab dipilih
    document.getElementById('repair-lab').addEventListener('change', async function() {
        const labId = this.value;
        const sourceSection = document.getElementById('device-source-section');
        const itemsSection = document.getElementById('items-selection-section');
        const submitSection = document.getElementById('repair-submit-section');

        if (!labId) {
            sourceSection.classList.add('hidden');
            itemsSection.classList.add('hidden');
            submitSection.classList.add('hidden');
            currentLabData = null;
            return;
        }

        try {
            Swal.fire({ title: 'Memuat data lab...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const res = await fetch(`/labs/${labId}/desk-map`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            Swal.close();

            if (!res.ok || !Array.isArray(data.desks)) {
                throw new Error(data.message || 'Gagal memuat data perangkat lab');
            }

            currentLabData = {
                desks: data.desks,
                storage_items: data.storage?.items || [],
                storage_components: data.storage?.components || [],
            };
            sourceSection.classList.remove('hidden');
            renderDeskButtons(currentLabData.desks);

            // Default pilih meja pertama jika ada
            if (currentLabData.desks.length > 0) {
                toggleDeviceSource('desk');
                selectDesk(data.desks[0].id);
            } else {
                toggleDeviceSource('storage');
                document.querySelector('input[name="device_source"][value="storage"]').checked = true;
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

    function toggleDeviceSource(source) {
        const deskWrapper = document.getElementById('desk-selection-wrapper');
        const cardDesk = document.getElementById('source-desk-card');
        const cardStorage = document.getElementById('source-storage-card');

        if (source === 'desk') {
            deskWrapper.classList.remove('hidden');
            cardDesk.classList.add('active');
            cardStorage.classList.remove('active');
            if (currentLabData && currentLabData.desks.length > 0) {
                const targetId = selectedDeskId || currentLabData.desks[0].id;
                selectDesk(targetId);
            }
        } else {
            deskWrapper.classList.add('hidden');
            cardStorage.classList.add('active');
            cardDesk.classList.remove('active');
            renderStorageItems();
        }
    }

    function renderDeskButtons(desks) {
        const container = document.getElementById('desk-buttons-container');
        if (!desks || desks.length === 0) {
            container.innerHTML = '<div class="col-span-full py-3 text-center text-xs text-gray-400">Tidak ada meja terdaftar di lab ini</div>';
            return;
        }

        container.innerHTML = desks.map(d => `
            <button type="button" onclick="selectDesk('${d.id}')" id="btn-desk-${d.id}"
                    class="desk-card p-2 text-xs font-bold transition-all ${selectedDeskId === d.id ? 'selected' : ''}">
                ${d.location}
            </button>
        `).join('');
    }

    function selectDesk(deskId) {
        selectedDeskId = deskId;
        document.querySelectorAll('.desk-card').forEach(btn => btn.classList.remove('selected'));
        const activeBtn = document.getElementById(`btn-desk-${deskId}`);
        if (activeBtn) activeBtn.classList.add('selected');

        const desk = currentLabData?.desks.find(d => d.id === deskId);
        if (!desk) return;

        document.getElementById('current-location-badge').textContent = `Meja ${desk.location}`;
        renderItemsList(desk.items, `Meja ${desk.location}`);
    }

    function renderStorageItems() {
        if (!currentLabData) return;
        const allItems = [...(currentLabData.storage_items || [])];
        const allComps = [...(currentLabData.storage_components || [])];
        document.getElementById('current-location-badge').textContent = 'Fasilitas / Storage Lab';
        renderItemsList(allItems, 'Storage Lab', allComps);
    }

    function renderItemsList(items, locationName, extraComponents = []) {
        const container = document.getElementById('items-list-container');
        const itemsSection = document.getElementById('items-selection-section');
        const submitSection = document.getElementById('repair-submit-section');

        itemsSection.classList.remove('hidden');
        submitSection.classList.remove('hidden');

        if ((!items || items.length === 0) && (!extraComponents || extraComponents.length === 0)) {
            container.innerHTML = `<div class="p-8 text-center text-sm text-gray-400 bg-gray-50 border border-dashed rounded-xl">Tidak ada perangkat terdaftar di ${locationName}.</div>`;
            return;
        }

        let html = '';

        items.forEach(item => {
            const isBroken = item.condition === 0;
            const isUnderRepair = item.is_under_repair;
            const isPendingRepair = item.is_pending_repair;
            const isRepairUnavailable = isUnderRepair || isPendingRepair;
            html += `
                <div class="item-card" id="item-card-wrapper-${item.id}">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="item-chk-${item.id}" value="${item.id}" data-type="item"
                               onchange="toggleItemDescription('item', '${item.id}')"
                               class="mt-1 h-4 w-4 text-petra-blue rounded border-gray-300 focus:ring-petra-blue cursor-pointer"
                               ${isRepairUnavailable ? 'disabled' : ''}>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <label for="item-chk-${item.id}" class="text-sm font-bold text-gray-900 cursor-pointer">${item.name}</label>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-petra-blue border border-blue-200">${item.type}</span>
                                ${isUnderRepair ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">Sedang Diperbaiki</span>' : ''}
                                ${isPendingRepair ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-gray-100 text-gray-700">Menunggu Diproses</span>' : ''}
                                ${isBroken && !isRepairUnavailable ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-red-100 text-red-800">Kondisi Rusak</span>' : ''}
                            </div>
                            <p class="text-xs text-gray-500 font-mono mt-0.5">SN: ${item.serial_code}</p>

                            {{-- Input Deskripsi Masalah --}}
                            <div id="desc-box-item-${item.id}" class="hidden mt-3 pt-3 border-t border-gray-100">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Uraikan kendala/kerusakan pada ${item.name}:</label>
                                <textarea id="desc-val-item-${item.id}" rows="2" maxlength="1000"
                                          placeholder="Contoh: Layar bergaris horizontal dan berkedip saat menyala..."
                                          class="form-input text-xs"></textarea>
                            </div>

                            {{-- Komponen Internal --}}
                            ${item.components && item.components.length > 0 ? `
                                <div class="mt-3 pl-3 border-l-2 border-petra-blue/20 space-y-2">
                                    <p class="text-[11px] font-bold text-petra-blue uppercase tracking-wider">Komponen Internal (Pilih jika spesifik):</p>
                                    ${item.components.map(comp => `
                                        <div class="flex items-start gap-2.5 bg-gray-50/80 p-2.5 rounded-lg border border-gray-200">
                                            <input type="checkbox" id="comp-chk-${comp.id}" value="${comp.id}" data-type="component"
                                                   onchange="toggleItemDescription('comp', '${comp.id}')"
                                                   class="mt-0.5 h-3.5 w-3.5 text-petra-blue rounded border-gray-300 focus:ring-petra-blue cursor-pointer"
                                                   ${(comp.is_under_repair || comp.is_pending_repair) ? 'disabled' : ''}>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <label for="comp-chk-${comp.id}" class="text-xs font-semibold text-gray-800 cursor-pointer">${comp.name}</label>
                                                    <span class="text-[10px] font-mono text-gray-500">(${comp.type})</span>
                                                    ${comp.is_under_repair ? '<span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold">Sedang Diperbaiki</span>' : ''}
                                                    ${comp.is_pending_repair ? '<span class="text-[9px] px-1.5 py-0.2 rounded bg-gray-100 text-gray-700 font-bold">Menunggu Diproses</span>' : ''}
                                                </div>
                                                <div id="desc-box-comp-${comp.id}" class="hidden mt-2">
                                                    <textarea id="desc-val-comp-${comp.id}" rows="2" maxlength="1000"
                                                              placeholder="Kendala komponen ini..."
                                                              class="form-input text-xs"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        });

        extraComponents.forEach(comp => {
            html += `
                <div class="item-card">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="comp-chk-${comp.id}" value="${comp.id}" data-type="component"
                               onchange="toggleItemDescription('comp', '${comp.id}')"
                               class="mt-1 h-4 w-4 text-petra-blue rounded border-gray-300 focus:ring-petra-blue cursor-pointer"
                               ${(comp.is_under_repair || comp.is_pending_repair) ? 'disabled' : ''}>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <label for="comp-chk-${comp.id}" class="text-sm font-bold text-gray-900 cursor-pointer">${comp.name}</label>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-50 text-petra-blue border border-blue-200">${comp.type}</span>
                                ${comp.is_under_repair ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-amber-100 text-amber-800">Sedang Diperbaiki</span>' : ''}
                                ${comp.is_pending_repair ? '<span class="px-2 py-0.5 text-[10px] font-bold rounded bg-gray-100 text-gray-700">Menunggu Diproses</span>' : ''}
                            </div>
                            <div id="desc-box-comp-${comp.id}" class="hidden mt-3 pt-3 border-t border-gray-100">
                                <textarea id="desc-val-comp-${comp.id}" rows="2" maxlength="1000"
                                          placeholder="Uraikan kendala komponen..."
                                          class="form-input text-xs"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function toggleItemDescription(type, id) {
        const chk = document.getElementById(`${type}-chk-${id}`);
        const box = document.getElementById(`desc-box-${type}-${id}`);
        const textarea = document.getElementById(`desc-val-${type}-${id}`);
        const wrapper = document.getElementById(`item-card-wrapper-${id}`);

        if (chk && chk.checked) {
            box.classList.remove('hidden');
            if (wrapper) wrapper.classList.add('selected');
            textarea.focus();
        } else {
            box.classList.add('hidden');
            if (wrapper) wrapper.classList.remove('selected');
        }
    }

    // Submit Laporan Perbaikan (Repair)
    document.getElementById('repair-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const labId = document.getElementById('repair-lab').value;
        if (!labId) {
            Swal.fire('Perhatian', 'Pilih laboratorium terlebih dahulu.', 'warning');
            return;
        }

        const itemsPayload = [];
        const compsPayload = [];

        document.querySelectorAll('input[type="checkbox"][data-type="item"]:checked').forEach(chk => {
            const id = chk.value;
            const desc = document.getElementById(`desc-val-item-${id}`)?.value.trim() || 'Perangkat rusak/berkendala.';
            itemsPayload.push({ id, issue_description: desc });
        });

        document.querySelectorAll('input[type="checkbox"][data-type="component"]:checked').forEach(chk => {
            const id = chk.value;
            const desc = document.getElementById(`desc-val-comp-${id}`)?.value.trim() || 'Komponen rusak/berkendala.';
            compsPayload.push({ id, issue_description: desc });
        });

        if (itemsPayload.length === 0 && compsPayload.length === 0) {
            Swal.fire('Perhatian', 'Centang minimal satu barang atau komponen yang rusak.', 'warning');
            return;
        }

        const confirm = await Swal.fire({
            title: 'Kirim Laporan Kerusakan?',
            text: `Anda akan melaporkan ${itemsPayload.length + compsPayload.length} barang/komponen rusak untuk ditindaklanjuti teknisi.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1a237e',
            confirmButtonText: 'Ya, Kirim Laporan',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        try {
            Swal.fire({ title: 'Mengirim laporan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const res = await fetch('/helpdesk/repair', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    lab_id: labId,
                    items: itemsPayload,
                    components: compsPayload
                })
            });

            const data = await res.json();
            Swal.close();

            if (res.ok && data.success) {
                Swal.fire({
                    title: 'Laporan Berhasil Terkirim!',
                    text: data.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'Lihat Riwayat Laporan',
                    cancelButtonText: 'Tutup'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('user.helpdesk.history') }}";
                    } else {
                        location.reload();
                    }
                });
            } else {
                throw new Error(data.message || 'Gagal mengirim laporan perbaikan.');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });

    // Submit Keluhan & Saran (Feedback)
    document.getElementById('feedback-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const type = document.querySelector('input[name="feedback_type"]:checked')?.value || 'KELUHAN';
        const labId = document.getElementById('feedback-lab').value || null;
        const subject = document.getElementById('feedback-subject').value.trim();
        const description = document.getElementById('feedback-description').value.trim();

        if (!subject || !description) {
            Swal.fire('Perhatian', 'Mohon isi subjek dan uraian laporan.', 'warning');
            return;
        }

        try {
            Swal.fire({ title: 'Mengirim masukan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const res = await fetch('/helpdesk/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    type,
                    lab_id: labId,
                    subject,
                    description
                })
            });

            const data = await res.json();
            Swal.close();

            if (res.ok && data.success) {
                Swal.fire({
                    title: 'Terima Kasih!',
                    text: data.message,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#1a237e',
                    confirmButtonText: 'Lihat Riwayat Tiket',
                    cancelButtonText: 'Selesai'
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('user.helpdesk.history') }}";
                    } else {
                        document.getElementById('feedback-form').reset();
                        toggleFeedbackType('KELUHAN');
                    }
                });
            } else {
                throw new Error(data.message || 'Gagal mengirim keluhan/saran.');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    });
</script>
@endsection
