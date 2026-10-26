@extends('layouts.user')

@section('title', 'Formulir Peminjaman')

@section('style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --petra-blue: #1A2D4A;
            --petra-yellow: #F57E21;
            --petra-wood: #8b4513;
            --petra-darkgray: #424242;
            --petra-gray: #FBFAF5;
            --booking-muted: rgba(26, 45, 74, .65);
            --booking-line: rgba(26, 45, 74, .16);
        }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #FBFAF5; }
        ::-webkit-scrollbar-thumb { background: #1A2D4A; border: 2px solid #FBFAF5; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: #F57E21; }
        * { scrollbar-width: thin; scrollbar-color: #1A2D4A #FBFAF5; }

        .booking-page { background-color: #FBFAF5; background-image: radial-gradient(520px circle at var(--mx, 70%) var(--my, 30%), rgba(245, 126, 33, .11), transparent 60%); background-repeat: no-repeat; color: #1A2D4A; font-family: 'Open Sans', sans-serif; transition: background .2s ease; }
        .booking-page .booking-wrap { width: 100%; max-width: 1240px; margin: 0 auto; padding: 0 28px; }
        .booking-page .booking-intro { padding: 150px 0 54px; }
        .booking-page .booking-eyebrow { display: inline-flex; align-items: center; gap: 14px; color: #F57E21; font-size: .7rem; font-weight: 700; letter-spacing: .32em; text-transform: uppercase; }
        .booking-page .booking-eyebrow::before { content: ''; width: 46px; height: 1px; background: currentColor; }
        .booking-page .booking-title { margin: 22px 0 16px; color: #1A2D4A; font-family: 'Cormorant Garamond', Georgia, serif; font-size: clamp(2.8rem, 6vw, 5rem); font-weight: 500; line-height: .98; letter-spacing: -.015em; }
        .booking-page .booking-lead { max-width: 620px; color: var(--booking-muted); font-size: 1rem; line-height: 1.85; }

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

        input[name="booking_type"]:checked+.type-card {
            border-color: var(--petra-blue);
            background: #f8fafc;
        }

        .desk-grid-container {
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--petra-gray);
            border-radius: 0.75rem;
            border: 2px solid #e2e8f0;
            min-width: fit-content;
        }

        .desk-card {
            transition: all 0.2s ease;
            min-height: 100px;
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
        }

        .desk-card.available {
            border-color: #10b981;
        }

        .desk-card.available:hover {
            border-color: var(--petra-blue);
        }

        .desk-card.full {
            border-color: #f87171;
            background-color: #fef2f2;
            cursor: not-allowed;
        }

        .desk-card.empty {
            border-color: #e2e8f0;
            background-color: #f8fafc;
            cursor: default;
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
            border-color: var(--petra-yellow);
            background-color: rgba(255, 202, 40, 0.1);
        }

        .item-card.unavailable {
            opacity: 0.6;
            background-color: #f1f5f9;
            cursor: not-allowed;
        }

        .cart-item {
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem;
            transition: all 0.2s;
        }

        .cart-item:hover {
            background-color: #f8fafc;
        }

        .booking-section {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .form-input {
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            border-color: var(--petra-blue);
            box-shadow: 0 0 0 3px rgba(26, 35, 126, 0.1);
            outline: none;
        }

        @media (min-width: 1024px) {
            .split-layout {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }
        }

        .overflow-auto::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .overflow-auto::-webkit-scrollbar-track {
            background: #FBFAF5;
        }

        .overflow-auto::-webkit-scrollbar-thumb {
            background: #1A2D4A;
            border: 2px solid #FBFAF5;
            border-radius: 8px;
        }

        .overflow-auto::-webkit-scrollbar-thumb:hover {
            background: #F57E21;
        }

        #item-list-container {
            max-height: 600px;
            overflow-y: auto;
        }

        #map-scroll-container {
            max-height: 600px;
        }

        .type-badge-cpu {
            background: #dbeafe;
            color: #1e40af;
        }

        .type-badge-monitor {
            background: #d1fae5;
            color: #047857;
        }

        .type-badge-mouse {
            background: #fef3c7;
            color: #b45309;
        }

        .type-badge-keyboard {
            background: #ede9fe;
            color: #6d28d9;
        }

        .type-badge-other {
            background: #f3f4f6;
            color: #4b5563;
        }

        .component-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: all 0.2s;
        }

        .component-card:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        .booking-page .booking-shell {
            border: 1px solid var(--booking-line);
            border-radius: 0;
            box-shadow: none;
            background: transparent;
        }
        .booking-page .booking-header {
            background: transparent;
            color: #1A2D4A;
            border-bottom: 1px solid var(--booking-line);
        }
        .booking-page .booking-header p { color: var(--booking-muted); }
        .booking-page .booking-header .booking-context {
            background: transparent;
            color: #1A2D4A;
            border: 1px solid var(--booking-line);
            border-radius: 0;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .booking-page .booking-card {
            border-width: 1px;
            border-radius: 0;
            box-shadow: none;
            background: transparent;
            border-color: var(--booking-line);
        }
        .booking-page .booking-card:hover {
            transform: none;
            box-shadow: none;
            border-color: #F57E21;
        }
        .booking-page .booking-card.active,
        .booking-page input[name="booking_type"]:checked + .type-card { border-color: #F57E21 !important; background: rgba(245, 126, 33, .06); box-shadow: none; }
        .booking-page .text-petra-blue { color: #1A2D4A; }
        .booking-page .bg-petra-blue { background-color: #1A2D4A; }
        .booking-page .hover\:bg-blue-800:hover { background-color: #F57E21; }
        .booking-page .type-card {
            display: grid;
            grid-template-columns: 2.5rem minmax(0, 1fr);
            column-gap: .75rem;
            row-gap: .15rem;
            text-align: left;
            padding: 1rem;
        }
        .booking-page .type-card > div:first-child {
            width: 2.5rem;
            height: 2.5rem;
            margin: 0;
            border-radius: 0;
            flex-shrink: 0;
            grid-row: span 2;
            background: rgba(245, 126, 33, .09);
            color: #F57E21;
        }
        .booking-page .type-card > div:first-child i { color: #F57E21; }
        .booking-page .booking-context i,
        .booking-page .booking-shell h3 .form-step,
        .booking-page .booking-shell h4 i,
        .booking-page .booking-shell label > i { color: #F57E21; }
        .booking-page .booking-shell h3 .form-step { background: #1A2D4A; }
        .booking-page .booking-note {
            border: 1px solid var(--booking-line);
            border-left: 3px solid #F57E21;
            border-radius: 0;
            background: rgba(26, 45, 74, .035);
        }
        .booking-page .booking-note i { color: #F57E21; }
        .booking-page .booking-note p,
        .booking-page .booking-note p span { color: #1A2D4A; }
        .booking-page .booking-help {
            border: 1px solid #1A2D4A;
            border-top: 3px solid #F57E21;
            border-radius: 0;
            background: #1A2D4A;
            color: #FBFAF5;
        }
        .booking-page .booking-help .booking-help-icon { background: rgba(245, 126, 33, .12); }
        .booking-page .booking-help .booking-help-icon i,
        .booking-page .booking-help .booking-contact i { color: #F57E21; }
        .booking-page .booking-help h4 { color: #FBFAF5; font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.65rem; font-weight: 600; }
        .booking-page .booking-help p { color: rgba(251, 250, 245, .72); }
        .booking-page .booking-help .booking-contact { color: #FBFAF5; }
        .booking-page .type-card h4 { font-size: .95rem; margin-bottom: .25rem; color: #1A2D4A; }
        .booking-page .type-card p { grid-column: 2; }
        .booking-page .desk-grid-container,
        .booking-page .item-card,
        .booking-page .form-input { border-width: 1px; }
        .booking-page .form-input { border-radius: 0; border-color: var(--booking-line); background: transparent; color: #1A2D4A; }
        .booking-page .form-input:focus { border-color: #F57E21; box-shadow: 0 0 0 2px rgba(245, 126, 33, .12); }
        .booking-page .desk-card:hover,
        .booking-page .item-card:hover { transform: none; box-shadow: none; }
        .booking-page h2, .booking-page h3 { color: #1A2D4A; }
        .booking-page h3 { font-size: 1rem; letter-spacing: .12em; text-transform: uppercase; }
        .booking-page .form-step {
            width: 1.75rem;
            height: 1.75rem;
            font-size: .75rem;
            flex-shrink: 0;
        }
        .booking-page #submit-btn { box-shadow: none; border-radius: 0; background: #1A2D4A; letter-spacing: .16em; text-transform: uppercase; }
        .booking-page #submit-btn:hover { background: #F57E21; }
        .booking-page #add-set-lab-btn {
            min-height: 44px;
            flex-shrink: 0;
            padding: 0 22px;
            border: 1px solid #1A2D4A;
            border-radius: 0;
            background: #1A2D4A;
            color: #FBFAF5;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            transition: background .25s ease, border-color .25s ease, color .25s ease;
        }
        .booking-page #add-set-lab-btn:hover { border-color: #F57E21; background: #F57E21; color: #1A2D4A; }
        .booking-page #add-set-lab-btn:focus-visible { outline: 2px solid #F57E21; outline-offset: 3px; }
        @media (max-width: 639px) {
            .booking-page .booking-wrap { padding: 0 20px; }
            .booking-page .booking-intro { padding: 120px 0 40px; }
        }
    </style>
@endsection

@section('body')
    @include('user.partials.navbar')
    <main class="booking-page min-h-screen pb-12">
        <div class="booking-wrap">

            <div class="booking-intro max-w-3xl">
                <p class="booking-eyebrow">Reservasi fasilitas laboratorium</p>
                <h1 class="booking-title">Buat peminjaman</h1>
                <p class="booking-lead">
                    Ajukan peminjaman fasilitas laboratorium untuk keperluan akademik Anda
                </p>
            </div>

            <!-- Main Form Card -->
            <div class="booking-shell bg-white overflow-hidden mb-8">

                <!-- Form Header -->
                <div class="booking-header p-6 md:p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Detail peminjaman</h2>
                            <p class="mt-1">Isi formulir sesuai kebutuhan Anda.</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="booking-context flex items-center gap-2 px-4 py-2 rounded-lg">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Sistem Reservasi Online</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6 md:p-8">

                    <!-- Step 1: Tipe Peminjaman -->
                    <div class="mb-12">
                        <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <span
                                 class="form-step flex items-center justify-center rounded-full bg-petra-blue text-white">1</span>
                            Tipe Peminjaman
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Lab Booking -->
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="lab" class="sr-only peer" checked>
                                <div class="type-card booking-card p-6 text-center peer-checked:border-petra-blue">
                                    <div
                                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fa-solid fa-building text-2xl text-petra-blue"></i>
                                    </div>
                                    <h4 class="font-bold text-lg text-petra-blue mb-2">Laboratorium</h4>
                                    <p class="text-sm text-gray-600">Reservasi ruang lab untuk kegiatan</p>
                                </div>
                            </label>

                            <!-- Set Booking -->
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="sets" class="sr-only peer">
                                <div class="type-card booking-card p-6 text-center peer-checked:border-petra-blue">
                                    <div
                                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <i class="fa-solid fa-desktop text-2xl text-petra-yellow"></i>
                                    </div>
                                    <h4 class="font-bold text-lg text-petra-blue mb-2">Set Lengkap</h4>
                                    <p class="text-sm text-gray-600">PC + Monitor untuk kebutuhan khusus</p>
                                </div>
                            </label>

                            <!-- Item Booking -->
                            <label class="cursor-pointer">
                                <input type="radio" name="booking_type" value="items" class="sr-only peer">
                                <div class="type-card booking-card p-6 text-center peer-checked:border-petra-blue">
                                    <div
                                        class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fa-solid fa-microchip text-2xl text-green-600"></i>
                                    </div>
                                    <h4 class="font-bold text-lg text-petra-blue mb-2">Item Individual</h4>
                                    <p class="text-sm text-gray-600">Pinjam peralatan elektronik spesifik</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Detail Umum -->
                    <div class="mb-12">
                        <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                            <span
                                class="form-step flex items-center justify-center rounded-full bg-petra-blue text-white">2</span>
                            Detail Umum
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Nama Kegiatan -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Nama Kegiatan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="event_name" required placeholder="Contoh: Praktikum Jarkom" class="form-input w-full">
                                    <p id="error-event_name" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                                    </p>
                                    
                                </div>

                                <!-- Tanggal Mulai -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Mulai Pinjam <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="borrowed_at" required class="form-input w-full">
                                    <p id="error-borrowed_at" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                                    </p>
                                </div>

                                <!-- Nomor WhatsApp -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        No. WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" id="phone_number" required pattern="^\d{10,20}$" placeholder="08123456789" class="form-input w-full">
                                    <p id="error-phone_number" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                                    </p>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Tipe Penggunaan -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Tipe Penggunaan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="type" required class="form-input w-full">
                                        <option value="0">Onsite</option>
                                        <option value="1">Remote</option>
                                        <option value="2">Keluar Lab</option>
                                    </select>
                                </div>

                                <!-- Tanggal Selesai -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Selesai Pinjam <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="return_deadline_at" required class="form-input w-full">
                                    <p id="error-return_deadline_at" class="hidden text-xs text-red-500 mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> <span></span>
                                    </p>
                                </div>

                                <!-- Detail Akademik (Opsional) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Detail Akademik (Opsional)
                                    </label>
                                    <textarea id="academic_detail" placeholder="Keterangan tambahan..." rows="3" class="form-input w-full"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Detail Spesifik -->
                    <div id="booking-details">
                        <!-- Lab Booking Section -->
                        <div id="lab-section" class="booking-section">
                            <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                                <span
                                    class="form-step flex items-center justify-center rounded-full bg-petra-blue text-white">3</span>
                                Detail Laboratorium
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pilih Lab -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Pilih Laboratorium <span class="text-red-500">*</span>
                                    </label>
                                    <select id="lab-select" class="form-input w-full">
                                        <option value="">-- Pilih Lab --</option>
                                    </select>
                                    
                                    <small id="lab-capacity-info" class="hidden text-gray-500 mt-2 block transition-all duration-300">
                                    <i class="fa-solid fa-users text-petra-blue mr-1"></i> 
                                        Kapasitas Maksimal: <span id="capacity-value" class="font-bold text-gray-800">0</span> orang
                                    </small>

                                </div>

                                <!-- Jumlah Peserta -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        Jumlah Peserta <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="attendee_count" min="1" placeholder="Contoh: 20"
                                        class="form-input w-full">
                                        <p id="capacity-error-msg" class="hidden text-xs text-red-500 mt-1">
                                            Jumlah peserta melebihi kapasitas lab!
                                        </p>
                                </div>
                            </div>

                            <div class="booking-note mt-6 p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-info-circle mt-1"></i>
                                    <div>
                                        <p class="text-sm">
                                            <span class="font-semibold">Catatan:</span> Peminjaman lab mencakup akses ke
                                            semua fasilitas dalam ruangan selama periode yang dipilih.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Set Booking Section -->
                        <div id="sets-section" class="booking-section hidden">
                            <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                                <span
                                    class="form-step flex items-center justify-center rounded-full bg-petra-blue text-white">3</span>
                                Detail Set Lengkap
                            </h3>

                            <div class="mb-6">
                                <div class="flex gap-2">
                                    <select id="set-lab-select" class="form-input flex-1">
                                        <option value="">-- Pilih Lab --</option>
                                    </select>
                                    <button type="button" id="add-set-lab-btn">
                                        Tambah Lab
                                    </button>
                                </div>
                            </div>

                            <!-- Lab List Container -->
                            <div id="set-labs-container" class="space-y-4">
                                <!-- Dynamic content will be added here -->
                            </div>

                            <div class="booking-note mt-6 p-4">
                                <div class="flex items-start gap-3">
                                    <i class="fa-solid fa-lightbulb mt-1"></i>
                                    <div>
                                        <p class="text-sm">
                                            <span class="font-semibold">Tip:</span> Set lengkap mencakup 1 unit PC +
                                            Monitor. Anda bisa meminjam dari beberapa lab sekaligus.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Booking Section -->
                        <div id="items-section" class="booking-section hidden">
                            <h3 class="text-xl font-bold text-petra-blue mb-6 flex items-center gap-3">
                                <span
                                    class="form-step flex items-center justify-center rounded-full bg-petra-blue text-white">3</span>
                                Pilih Item
                            </h3>

                            <!-- Lab Selection -->
                            <div class="mb-8">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Pilih Laboratorium <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-3">
                                    <select id="item-lab-select" class="form-input flex-1">
                                        <option value="">-- Pilih Lab --</option>
                                    </select>
                                    <button type="button" id="browse-items-btn"
                                        class="px-6 bg-petra-blue text-white font-semibold rounded-lg hover:bg-blue-800 transition">
                                        Tampilkan Denah
                                    </button>
                                </div>
                            </div>

                            <!-- Split Layout for Desk Map and Items -->
                            <div id="item-selection-container" class="hidden">
                                <!-- Lab Storage Section -->
                                <div id="lab-storage-section" class="mb-6 hidden">
                                    <div class="bg-white rounded-xl border border-gray-200">
                                        <div class=" bg-gray-50 px-4 py-3 border-b border-gray-200">
                                            <h4 class="font-bold text-gray-700 flex items-center gap-2">
                                                <i class="fa-solid fa-warehouse"></i>
                                                Lab Storage
                                                <span id="storage-count"
                                                    class="ml-2 bg-white text-gray-700 px-2 py-1 rounded-full text-xs">0</span>
                                            </h4>
                                        </div>
                                        <div id="lab-storage-container" class="p-4 max-h-96 overflow-y-auto">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    <!-- Left: Desk Map -->
                                    <div class="lg:col-span-2">
                                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                            <div
                                                class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center flex-wrap gap-2">
                                                <div>
                                                    <h4 class="font-bold text-gray-700">
                                                        Denah & Ketersediaan
                                                        <span id="selected-lab-name" class="text-petra-blue ml-2"></span>
                                                    </h4>
                                                    <div class="flex flex-wrap gap-3 mt-1 text-xs text-gray-600">
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>Ada
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-2 h-2 rounded-full bg-red-400"></div>Penuh
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <div class="w-2 h-2 rounded-full bg-blue-600"></div>Pilih
                                                        </div>
                                                    </div>
                                                </div>

                                                <div
                                                    class="flex items-center bg-white rounded-lg border border-gray-300 shadow-sm p-1">
                                                    <button type="button" onclick="adjustZoom(-0.1)"
                                                        class="p-1.5 hover:bg-gray-100 rounded text-gray-600"
                                                        title="Zoom Out">
                                                        <i class="fa-solid fa-magnifying-glass-minus"></i>
                                                    </button>
                                                    <span id="zoom-level-text"
                                                        class="text-xs font-mono w-12 text-center text-gray-500 select-none">100%</span>
                                                    <button type="button" onclick="adjustZoom(0.1)"
                                                        class="p-1.5 hover:bg-gray-100 rounded text-gray-600"
                                                        title="Zoom In">
                                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                    </button>
                                                    <div class="w-px h-4 bg-gray-300 mx-1"></div>
                                                    <button type="button" onclick="resetZoom()"
                                                        class="p-1.5 hover:bg-gray-100 rounded text-gray-600"
                                                        title="Reset Zoom">
                                                        <i class="fa-solid fa-rotate"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="p-4 relative">
                                                <div class="overflow-auto overscroll-auto border border-gray-200 rounded-lg bg-gray-50 relative"
                                                    id="map-scroll-container">

                                                    <div id="map-zoom-wrapper"
                                                        class="origin-top-left transition-transform duration-200 ease-out p-4 min-w-fit min-h-fit">
                                                        <div id="desk-map-container" class="min-w-max">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Item List -->
                                    <div class="lg:col-span-1">
                                        <div class="bg-white rounded-xl border border-gray-200 h-full flex flex-col">
                                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                                <h4 class="font-bold text-gray-700 flex items-center justify-between">
                                                    <span>Item pada Meja: <span id="selected-desk"
                                                            class="text-petra-blue">-</span></span>
                                                    <span
                                                        class="text-xs font-normal bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                        <span id="available-count">0</span> tersedia
                                                    </span>
                                                </h4>
                                            </div>
                                            <div id="item-list-container" class="p-4 space-y-3">
                                                <div class="text-center py-8 text-gray-400">
                                                    <i class="fa-solid fa-table-cells-large text-3xl mb-3"></i>
                                                    <p>Pilih meja untuk melihat item</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cart Section -->
                                <div class="mt-8">
                                    <div class="bg-white rounded-xl border border-gray-200">
                                        <div
                                            class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                            <h4 class="font-bold text-gray-700">
                                                Keranjang Item
                                                <span id="cart-count"
                                                    class="ml-2 bg-petra-blue text-white px-2 py-1 rounded-full text-xs">0</span>
                                            </h4>
                                            <button type="button" id="clear-cart-btn"
                                                class="text-sm text-red-600 hover:text-red-800">
                                                <i class="fa-solid fa-trash mr-1"></i> Kosongkan
                                            </button>
                                        </div>
                                        <div id="cart-container" class="p-4">
                                            <div id="cart-items" class="space-y-2">
                                                <div class="text-center py-8 text-gray-400">
                                                    <i class="fa-solid fa-shopping-cart text-3xl mb-3"></i>
                                                    <p>Belum ada item di keranjang</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <button type="button" id="submit-btn"
                            class="w-full md:w-auto px-12 py-4 bg-petra-blue text-white font-bold rounded-lg hover:bg-blue-800 transition-all flex items-center justify-center gap-3 text-lg">
                            <i class="fa-solid fa-paper-plane"></i>
                            Ajukan Peminjaman
                        </button>

                        <p class="text-sm text-gray-500 mt-4 text-center md:text-left">
                            <i class="fa-solid fa-circle-info mr-1"></i>
                            Pastikan semua data yang Anda isi sudah benar sebelum mengajukan
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="booking-help p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="booking-help-icon w-12 h-12 flex items-center justify-center">
                            <i class="fa-solid fa-headset text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold mb-2">Butuh Bantuan?</h4>
                        <p class="mb-3">
                            Jika Anda mengalami kesulitan dalam mengisi formulir, silakan hubungi administrator
                            laboratorium:
                        </p>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <span class="booking-contact inline-flex items-center gap-2">
                                <i class="fa-solid fa-envelope"></i>
                                <span>lab@petra.ac.id</span>
                            </span>
                            <span class="booking-contact inline-flex items-center gap-2">
                                <i class="fa-solid fa-phone"></i>
                                <span>(031) 1234-5678</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    @include('user.partials.footer')
@endsection

@section('script')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const bookingPage = document.querySelector('.booking-page');
        if (bookingPage && !window.matchMedia('(pointer: coarse), (prefers-reduced-motion: reduce)').matches) {
            bookingPage.addEventListener('pointermove', (event) => {
                const bounds = bookingPage.getBoundingClientRect();
                bookingPage.style.setProperty('--mx', `${((event.clientX - bounds.left) / bounds.width) * 100}%`);
                bookingPage.style.setProperty('--my', `${((event.clientY - bounds.top) / bounds.height) * 100}%`);
            });
        }
        let allLabs = [];
        let cart = [];
        let currentDeskMap = [];
        let selectedDeskIndex = null;
        let currentLabId = null;
        let currentZoom = 1;
        let mapLenisVertical = null;
        let mapLenisHorizontal = null;
        let itemListLenis = null;
        let modalLenis = null;
        let componentListLenis = null;
        let labStorageLenis = null;
        let currentLabStorage = {
            items: [],
            components: []
        };

        document.addEventListener('DOMContentLoaded', function() {
            initializeForm();
            setupEventListeners();
            loadLabs();
            initializeLenisInstances();

            // Set default dates
            const now = new Date();
            const tomorrow = new Date(now.getTime() + 24 * 60 * 60 * 1000);

            document.getElementById('borrowed_at').value = formatDateTime(now);
            document.getElementById('return_deadline_at').value = formatDateTime(tomorrow);
        });

        function formatDateTime(date) {
            return date.toISOString().slice(0, 16);
        }

        function initializeForm() {
            showSection('lab');

            document.querySelectorAll('input[name="booking_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const type = this.value;
                    showSection(type);
                });
            });
        }

        function showSection(type) {
            ['lab', 'sets', 'items'].forEach(section => {
                const el = document.getElementById(`${section}-section`);
                if (el) {
                    el.classList.add('hidden');
                }
            });

            const selectedSection = document.getElementById(`${type}-section`);
            if (selectedSection) {
                selectedSection.classList.remove('hidden');

                // Animate entrance
                gsap.fromTo(selectedSection, {
                    opacity: 0,
                    y: 20
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.3,
                    ease: "power2.out"
                });
            }
        }


        function adjustZoom(delta) {
            const wrapper = document.getElementById('map-zoom-wrapper');
            const textDisplay = document.getElementById('zoom-level-text');

            if (!wrapper) return;

            currentZoom = Math.min(Math.max(currentZoom + delta, 0.5), 2.0);

            wrapper.style.transform = `scale(${currentZoom})`;

            textDisplay.textContent = `${Math.round(currentZoom * 100)}%`;

            if (currentZoom < 1) {
                wrapper.style.width = `${100 / currentZoom}%`;
            } else {
                wrapper.style.width = 'auto';
            }
        }

        function resetZoom() {
            const wrapper = document.getElementById('map-zoom-wrapper');
            const textDisplay = document.getElementById('zoom-level-text');

            if (!wrapper) return;

            currentZoom = 1;
            wrapper.style.transform = 'scale(1)';
            wrapper.style.width = 'auto';
            textDisplay.textContent = '100%';
        }

        function initializeLenisInstances() {
            const mapContainer = document.getElementById('map-scroll-container');
            const itemListContainer = document.getElementById('item-list-container');

            if (mapContainer) {
                mapLenisVertical = new Lenis({
                    wrapper: mapContainer,
                    content: mapContainer.firstElementChild,
                    duration: 1.2,
                    orientation: 'vertical',
                    gestureOrientation: 'vertical',
                    smoothWheel: true,
                    wheelMultiplier: 1
                });

                mapLenisHorizontal = new Lenis({
                    wrapper: mapContainer,
                    content: mapContainer.firstElementChild,
                    duration: 1.2,
                    orientation: 'horizontal',
                    gestureOrientation: 'horizontal',
                    smoothWheel: true,
                    wheelMultiplier: 1
                });

                setupScrollChaining(mapContainer, mapLenisVertical, 'vertical');
                setupScrollChaining(mapContainer, mapLenisHorizontal, 'horizontal');

                function rafMap(time) {
                    if (mapLenisVertical) mapLenisVertical.raf(time);
                    if (mapLenisHorizontal) mapLenisHorizontal.raf(time);
                    requestAnimationFrame(rafMap);
                }
                requestAnimationFrame(rafMap);
            }

            if (itemListContainer) {
                itemListLenis = new Lenis({
                    wrapper: itemListContainer,
                    content: itemListContainer.firstElementChild,
                    duration: 1.2,
                    orientation: 'vertical',
                    gestureOrientation: 'vertical'
                });

                setupScrollChaining(itemListContainer, itemListLenis, 'vertical');

                function rafItemList(time) {
                    if (itemListLenis) {
                        itemListLenis.raf(time);
                    }
                    requestAnimationFrame(rafItemList);
                }
                requestAnimationFrame(rafItemList);
            }
        }

        function setupScrollChaining(element, lenisInstance, orientation) {
            element.addEventListener('wheel', (e) => {
                // Block vertical scroll saat shift ditekan
                if (e.shiftKey && orientation === 'vertical') {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }

                // Shift+scroll untuk horizontal
                if (e.shiftKey && orientation === 'horizontal') {
                    e.preventDefault();
                    e.stopPropagation();
                    element.scrollLeft += e.deltaY;
                    return;
                }

                const isVertical = orientation === 'vertical';
                const delta = isVertical ? e.deltaY : e.deltaX;

                // Get actual content size including zoom transform
                const content = element.firstElementChild;
                const contentRect = content ? content.getBoundingClientRect() : null;
                const elementRect = element.getBoundingClientRect();

                const scrollPos = isVertical ? element.scrollTop : element.scrollLeft;
                const contentSize = contentRect ? (isVertical ? contentRect.height : contentRect.width) : (
                    isVertical ? element.scrollHeight : element.scrollWidth);
                const clientSize = isVertical ? elementRect.height : elementRect.width;
                const maxScroll = Math.max(0, contentSize - clientSize);

                const atStart = scrollPos <= 1;
                const atEnd = scrollPos >= maxScroll - 1;

                if ((atStart && delta < 0) || (atEnd && delta > 0)) {
                    let parent = element.parentElement;
                    while (parent) {
                        const canScroll = isVertical ?
                            parent.scrollHeight > parent.clientHeight :
                            parent.scrollWidth > parent.clientWidth;

                        if (canScroll) {
                            if (isVertical) {
                                parent.scrollTop += delta * 0.5;
                            } else {
                                parent.scrollLeft += delta * 0.5;
                            }
                            break;
                        }
                        parent = parent.parentElement;
                    }
                }
            }, {
                passive: false
            });
        }

        function initializeModalLenis() {
            const modalBody = document.getElementById('layout-modal-body');

            if (modalBody && !modalLenis) {
                modalLenis = new Lenis({
                    wrapper: modalBody,
                    content: modalBody.firstElementChild,
                    duration: 1.2,
                    orientation: 'vertical',
                    gestureOrientation: 'vertical'
                });

                setupScrollChaining(modalBody, modalLenis, 'vertical');

                function rafModal(time) {
                    if (modalLenis) {
                        modalLenis.raf(time);
                    }
                    requestAnimationFrame(rafModal);
                }
                requestAnimationFrame(rafModal);
            }
        }

        function initializeComponentListLenis() {
            const componentListContainer = document.getElementById('component-list-scroll');

            if (componentListContainer && !componentListLenis) {
                componentListLenis = new Lenis({
                    wrapper: componentListContainer,
                    content: componentListContainer.firstElementChild,
                    duration: 1.2,
                    orientation: 'vertical',
                    gestureOrientation: 'vertical'
                });

                setupScrollChaining(componentListContainer, componentListLenis, 'vertical');

                function rafComponentList(time) {
                    if (componentListLenis) {
                        componentListLenis.raf(time);
                    }
                    requestAnimationFrame(rafComponentList);
                }
                requestAnimationFrame(rafComponentList);
            }
        }

        function destroyModalLenis() {
            if (modalLenis) {
                modalLenis.destroy();
                modalLenis = null;
            }
            if (componentListLenis) {
                componentListLenis.destroy();
                componentListLenis = null;
            }
        }

        function renderLabStorage() {
            const section = document.getElementById('lab-storage-section');
            const container = document.getElementById('lab-storage-container');
            const totalItems = currentLabStorage.items.length + currentLabStorage.components.length;

            document.getElementById('storage-count').textContent = totalItems;
            section.classList.remove('hidden');

            if (totalItems === 0) {
                container.innerHTML =
                    '<div class="text-center py-8 text-gray-400"><i class="fa-solid fa-box-open text-3xl mb-3"></i><p>Tidak ada Item maupun Component dalam Lemari Lab</p></div>';
                return;
            }

            let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';

            currentLabStorage.items.forEach(item => {
                html += renderStorageItemCard(item, false);
            });

            currentLabStorage.components.forEach(component => {
                html += renderStorageItemCard(component, true);
            });

            html += '</div>';
            container.innerHTML = html;
        }

        function renderStorageItemCard(item, isComponent) {
            const isAvailable = item.available;
            const inCart = cart.some(c => c.id === item.id && (isComponent ? c.is_component : !c.is_component));
            const itemData = encodeURIComponent(JSON.stringify(item));
            const typeName = (item.type || 'OTHER').toUpperCase();
            const typeClass = getTypeClass(typeName);

            let itemClass = 'item-card';
            let statusBadge = '';
            let actionButton = '';
            let statusText = '';

            if (!isAvailable) {
                itemClass += ' unavailable';
                if (item.condition === 0) {
                    statusBadge = '<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">Rusak</span>';
                    statusText =
                        '<div class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-wrench mr-1"></i>Item dalam kondisi rusak</div>';
                } else {
                    statusBadge = '<span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Dipinjam</span>';
                    statusText =
                        '<div class="text-xs text-red-500 mt-2"><i class="fa-solid fa-clock mr-1"></i>Item sedang dipinjam</div>';
                }
            } else if (inCart) {
                itemClass += ' selected';
                statusBadge = '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Dipilih</span>';
                actionButton = `
                    <button onclick="removeFromCart('${item.id}', ${isComponent})" 
                            class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                        <i class="fa-solid fa-times mr-1"></i> Batal
                    </button>
                `;
            } else {
                statusBadge = '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Tersedia</span>';
                actionButton = `
                    <button onclick="addToCart('${itemData}', ${isComponent})" 
                            class="px-3 py-1 bg-petra-blue text-white rounded-lg hover:bg-blue-800 text-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah
                    </button>
                `;
            }

            const hasComponents = !isComponent && item.components && item.components.length > 0;
            const componentBadge = isComponent ?
                '<span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full"><i class="fa-solid fa-puzzle-piece mr-1"></i>Component</span>' :
                '';

            return `
                <div class="${itemClass}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="${typeClass} text-xs px-2 py-1 rounded font-semibold">${typeName}</span>
                                ${componentBadge}
                            </div>
                            <div class="font-semibold text-gray-800">${item.name}</div>
                            <div class="text-xs text-gray-500 font-mono mt-1">${item.serial_code || 'No Serial'}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            ${statusBadge}
                        </div>
                    </div>

                    ${hasComponents ? `
                            <div class="mt-2 mb-2">
                                <div class="text-xs text-gray-600 font-semibold mb-1">
                                    <i class="fa-solid fa-puzzle-piece mr-1"></i>Components (${item.components.length})
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    ${item.components.slice(0, 3).map(comp => `
                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">${comp.type || 'N/A'}</span>
                                `).join('')}
                                    ${item.components.length > 3 ? `<span class="text-xs text-gray-500">+${item.components.length - 3} lagi</span>` : ''}
                                </div>
                            </div>
                        ` : ''}

                    <div class="flex justify-between items-center mt-2 gap-2">
                        ${hasComponents || (item.specifications && item.specifications.length > 0) ? `
                                <button onclick="showItemDetail('${itemData}')" 
                                        class="text-xs text-petra-blue hover:text-blue-800">
                                    <i class="fa-solid fa-info-circle mr-1"></i> Lihat Detail
                                </button>
                            ` : '<div></div>'}
                        <div>${actionButton}</div>
                    </div>

                    ${statusText}
                </div>
            `;
        }

        function destroyMapLenis() {
            if (mapLenisVertical) {
                mapLenisVertical.destroy();
                mapLenisVertical = null;
            }
            if (mapLenisHorizontal) {
                mapLenisHorizontal.destroy();
                mapLenisHorizontal = null;
            }
        }

        async function loadLabs() {
            try {
                const response = await fetch('/get/labs');
                if (!response.ok) throw new Error('Failed to load labs');
                allLabs = await response.json();

                ['lab-select', 'set-lab-select', 'item-lab-select'].forEach(id => {
                    const select = document.getElementById(id);
                    if (select) {
                        select.innerHTML = '<option value="">-- Pilih Lab --</option>';
                        allLabs.forEach(lab => {
                            select.innerHTML += `<option value="${lab.id}">${lab.name}</option>`;
                        });
                    }
                });
            } catch (error) {
                console.error('Error loading labs:', error);
                showToast('Gagal memuat data lab', '', 'error');
            }
        }

        function setupEventListeners() {
            document.getElementById('add-set-lab-btn')?.addEventListener('click', addSetLab);
            document.getElementById('browse-items-btn')?.addEventListener('click', browseItems);
            document.getElementById('clear-cart-btn')?.addEventListener('click', clearCart);
            document.getElementById('submit-btn')?.addEventListener('click', submitBooking);
            document.getElementById('borrowed_at').addEventListener('change', function() {
                // Re-validate return date jika sudah ada isinya
                const returnInput = document.getElementById('return_deadline_at');
                if (returnInput.value) {
                    validateField({ target: returnInput });
                }
            });

            window.addEventListener('resize', syncListHeight);

            // TAMBAHKAN INI: Listener saat Lab dipilih
            const labSelect = document.getElementById('lab-select');
            if (labSelect) {
                labSelect.addEventListener('change', function() {
                    const labId = this.value;
                    const lab = allLabs.find(l => l.id == labId);
                    const capacityInfo = document.getElementById('lab-capacity-info');
                    const capacityValue = document.getElementById('capacity-value');
                    const attendeeInput = document.getElementById('attendee_count');

                    if (lab && lab.capacity) {
                        // Update UI Kapasitas
                        capacityValue.textContent = lab.capacity;
                        capacityInfo.classList.remove('hidden');
                        
                        // Set atribut max pada input number agar browser juga membatasi (native)
                        attendeeInput.max = lab.capacity; 
                        
                        // Validasi ulang attendee_count jika user sudah terlanjur mengisi angka
                        validateField({ target: attendeeInput });
                    } else {
                        capacityInfo.classList.add('hidden');
                        attendeeInput.removeAttribute('max');
                    }
                });
            }

            // TAMBAHKAN: Daftarkan attendee_count agar divalidasi saat mengetik (input) dan saat keluar (blur)
            const attendeeInput = document.getElementById('attendee_count');
            if (attendeeInput) {
                attendeeInput.addEventListener('input', validateField); // Real-time check
                attendeeInput.addEventListener('blur', validateField);
            }

            ['event_name', 'phone_number', 'borrowed_at', 'return_deadline_at'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('blur', validateField);
                }
            });
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorEl = document.getElementById(`error-${fieldId}`);
            
            if (field) {
                field.classList.remove('border-green-500', 'border-gray-300');
                field.classList.add('border-red-500');
            }
            
            if (errorEl) {
                errorEl.querySelector('span').textContent = message;
                errorEl.classList.remove('hidden');
                // Animasi kecil
                gsap.fromTo(errorEl, {y: -5, opacity: 0}, {y: 0, opacity: 1, duration: 0.2});
            }
        }

        function clearError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorEl = document.getElementById(`error-${fieldId}`);
            
            if (field) {
                field.classList.remove('border-red-500');
                field.classList.add('border-green-500');
            }
            
            if (errorEl) {
                errorEl.classList.add('hidden');
            }
        }

        function validateField(e) {
            const field = e.target;
            const value = field.value.trim();
            const fieldId = field.id;
            let errorMessage = "";
            let isValid = true;

            if (value === '' && fieldId !== 'attendee_count') {
                showError(fieldId, 'Field ini wajib diisi');
                return false;
            }

            switch (fieldId) {
                case 'event_name':
                    if (value.length < 3) {
                        isValid = false;
                        errorMessage = "Nama kegiatan terlalu pendek (min. 3 karakter)";
                    } else if (value.length > 255) {
                        isValid = false;
                        errorMessage = "Nama kegiatan terlalu panjang (max. 255 karakter)";
                    }
                    break;

                case 'phone_number':
                    // Regex sesuai controller: /^\d{10,20}$/
                    const phoneRegex = /^\d{10,20}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        errorMessage = "Nomor WhatsApp harus angka (10-20 digit)";
                    }
                    break;

                case 'borrowed_at':
                    const borrowDate = new Date(value);
                    const now = new Date();
                    // Beri toleransi waktu sedikit (misal 1 menit) untuk delay input
                    if (isNaN(borrowDate.getTime())) {
                        isValid = false;
                        errorMessage = "Format tanggal tidak valid";
                    } else if (borrowDate < now) {
                        isValid = false;
                        errorMessage = "Waktu mulai tidak boleh di masa lalu";
                    }
                    break;

                case 'return_deadline_at':
                    const returnDate = new Date(value);
                    const borrowInput = document.getElementById('borrowed_at').value;
                    
                    if (isNaN(returnDate.getTime())) {
                        isValid = false;
                        errorMessage = "Format tanggal tidak valid";
                    } else if (!borrowInput) {
                        isValid = false;
                        errorMessage = "Isi waktu mulai pinjam terlebih dahulu";
                    } else {
                        const borrowDateRef = new Date(borrowInput);
                        if (returnDate <= borrowDateRef) {
                            isValid = false;
                            errorMessage = "Waktu selesai harus setelah waktu mulai";
                        }
                    }
                    break;

                case 'attendee_count':
                    // Logika attendee_count yang sudah kita buat sebelumnya
                    // Integrasikan logic error message di sini juga
                    const count = parseInt(value);
                    const labId = document.getElementById('lab-select').value;
                    const selectedLab = allLabs.find(l => l.id == labId);
                    const capErrorMsg = document.getElementById('capacity-error-msg'); // Menggunakan elemen yang sudah ada

                    if (!value || count < 1) {
                        isValid = false;
                        // Khusus attendee, kita pakai elemen error yang sudah dibuat sebelumnya atau disatukan
                        if(capErrorMsg) {
                            capErrorMsg.textContent = "Jumlah peserta minimal 1";
                            capErrorMsg.classList.remove('hidden');
                        }
                        field.classList.add('border-red-500');
                        return false; 
                    } else if (selectedLab && selectedLab.capacity && count > selectedLab.capacity) {
                        isValid = false;
                        if(capErrorMsg) {
                            capErrorMsg.textContent = `Melebihi kapasitas lab (${selectedLab.capacity} orang)`;
                            capErrorMsg.classList.remove('hidden');
                        }
                        field.classList.add('border-red-500');
                        return false;
                    } else {
                        if(capErrorMsg) capErrorMsg.classList.add('hidden');
                        field.classList.remove('border-red-500');
                        field.classList.add('border-green-500');
                        return true;
                    }
                    break;
            }

            // Terapkan hasil validasi
            if (isValid) {
                clearError(fieldId);
            } else {
                showError(fieldId, errorMessage);
            }

            return isValid;
        }

        async function addSetLab() {
            const labId = document.getElementById('set-lab-select').value;
            if (!labId) {
                showToast('Pilih lab terlebih dahulu', '', 'warning');
                return;
            }

            const start = document.getElementById('borrowed_at').value;
            const end = document.getElementById('return_deadline_at').value;
            if (!start || !end) {
                showToast('Isi tanggal peminjaman terlebih dahulu', '', 'warning');
                return;
            }

            showLoading('Memuat data set...');

            try {
                const response = await fetch(`/labs/${labId}/available-sets?start=${start}&end=${end}`);
                if (!response.ok) throw new Error('Failed to fetch');
                const data = await response.json();

                Swal.close();

                const lab = allLabs.find(l => l.id == labId);
                const container = document.getElementById('set-labs-container');

                const div = document.createElement('div');
                div.className = 'bg-gray-50 rounded-lg p-4 border border-gray-200';
                div.innerHTML = `
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <span class="font-bold text-gray-800">${lab.name}</span>
                            <span class="ml-2 text-sm text-gray-500">(Tersedia: ${data.available_count} set)</span>
                        </div>
                        <button type="button" 
                                onclick="removeSetLab(this)"
                                class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Jumlah Set</label>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="adjustSetQuantity(this, -1)" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-gray-100">-</button>
                            <input type="number" 
                                   data-lab-id="${labId}"
                                   value="1"
                                   min="1"
                                   max="${data.available_count}"
                                   class="form-input w-20 text-center">
                            <button type="button" onclick="adjustSetQuantity(this, 1)" class="w-8 h-8 rounded-full border border-gray-300 hover:bg-gray-100">+</button>
                        </div>
                    </div>
                `;

                container.appendChild(div);
                gsap.from(div, {
                    opacity: 0,
                    y: -10,
                    duration: 0.3
                });

                showToast('Lab ditambahkan', '', 'success');

            } catch (error) {
                Swal.close();
                showToast('Gagal memuat data set', '', 'error');
            }
        }

        function adjustSetQuantity(button, delta) {
            const container = button.closest('.flex.items-center');
            const input = container.querySelector('input');
            let value = parseInt(input.value) || 0;
            const max = parseInt(input.max) || 99;
            const newValue = Math.max(1, Math.min(max, value + delta));
            input.value = newValue;
        }

        function removeSetLab(button) {
            const container = button.closest('.bg-gray-50');
            gsap.to(container, {
                opacity: 0,
                height: 0,
                margin: 0,
                padding: 0,
                duration: 0.3,
                onComplete: () => container.remove()
            });
        }

        async function browseItems() {
            const labId = document.getElementById('item-lab-select').value;
            if (!labId) {
                showToast('Pilih lab terlebih dahulu', '', 'warning');
                return;
            }

            const start = document.getElementById('borrowed_at').value;
            const end = document.getElementById('return_deadline_at').value;
            if (!start || !end) {
                showToast('Isi tanggal peminjaman terlebih dahulu', '', 'warning');
                return;
            }

            showLoading('Memuat denah lab...');

            try {
                const response = await fetch(`/labs/${labId}/desk-map?start=${start}&end=${end}`);
                if (!response.ok) throw new Error('Failed to fetch');

                const data = await response.json();
                currentDeskMap = data.desks;
                currentLabStorage = data.storage;
                currentLabId = labId;

                Swal.close();

                const container = document.getElementById('item-selection-container');
                container.classList.remove('hidden');
                gsap.from(container, {
                    opacity: 0,
                    y: 20,
                    duration: 0.3
                });

                const lab = allLabs.find(l => l.id == labId);
                document.getElementById('selected-lab-name').textContent = lab.name;

                renderLabStorage();
                renderDeskMap();

            } catch (error) {
                Swal.close();
                showToast('Gagal memuat denah lab', '', 'error');
            }
        }

        function renderDeskMap() {
            const container = document.getElementById('desk-map-container');
            if (!currentDeskMap || currentDeskMap.length === 0) {
                container.innerHTML = '<div class="text-center py-12 text-gray-400">Tidak ada data meja tersedia</div>';
                return;
            }

            resetZoom();

            let maxRow = 0,
                maxCol = 0;
            currentDeskMap.forEach(desk => {
                if (!desk.location || desk.location.length < 2) return;
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));
                if (row > maxRow) maxRow = row;
                if (col > maxCol) maxCol = col;
            });

            let gridHTML =
                `<div class="desk-grid-container" style="grid-template-columns: repeat(${maxCol}, minmax(120px, 1fr));">`;

            const occupiedSlots = new Set(currentDeskMap.map(d => d.location));

            currentDeskMap.forEach((desk, index) => {
                const row = desk.location.charCodeAt(0) - 64;
                const col = parseInt(desk.location.substring(1));

                const availableItems = desk.items.filter(i => i.available).length;
                const totalItems = desk.items.length;

                let statusClass = 'available';
                let statusText = '';
                let clickable = true;

                if (totalItems === 0) {
                    statusClass = 'empty';
                    statusText = '<span class="text-xs text-gray-400">Kosong</span>';
                    clickable = false;
                } else if (availableItems === 0) {
                    statusClass = 'full';
                    statusText = '<span class="text-xs text-red-500 font-semibold">Penuh</span>';
                } else {
                    statusText = `
                        <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                            ${availableItems} tersedia
                        </div>
                    `;
                }

                const deskData = encodeURIComponent(JSON.stringify(desk));
                const onClick = clickable ? `onclick="selectDesk(this, ${index}, '${deskData}')"` : '';
                const cursorClass = clickable ? 'cursor-pointer' : 'cursor-default';

                gridHTML += `
                    <div style="grid-area: ${row} / ${col}"
                         class="desk-card ${statusClass} ${cursorClass}"
                         ${onClick}>
                        <div class="text-lg font-bold mb-2">${desk.location}</div>
                        ${statusText}
                    </div>
                `;
            });

            for (let r = 1; r <= maxRow; r++) {
                for (let c = 1; c <= maxCol; c++) {
                    const loc = String.fromCharCode(64 + r) + c;
                    if (!occupiedSlots.has(loc)) {
                        gridHTML += `<div class="desk-card empty" style="grid-area: ${r} / ${c};"></div>`;
                    }
                }
            }

            gridHTML += '</div>';
            container.innerHTML = gridHTML;

            setTimeout(syncListHeight, 50);

            // // Animate desks
            // gsap.from('.desk-card', {
            //     scale: 0.8,
            //     opacity: 0,
            //     duration: 0.3,
            //     stagger: 0.05,
            //     ease: "back.out(1.2)"
            // });
        }

        function selectDesk(element, index, deskEncoded) {
            const desk = JSON.parse(decodeURIComponent(deskEncoded));

            document.querySelectorAll('.desk-card').forEach(card => {
                card.classList.remove('selected');
            });

            if (element) {
                element.classList.add('selected');
            }

            document.getElementById('selected-desk').textContent = desk.location;
            selectedDeskIndex = index;

            populateItemList(desk);
        }

        function populateItemList(desk) {
            const container = document.getElementById('item-list-container');
            const availableCount = desk.items.filter(i => i.available).length;

            document.getElementById('available-count').textContent = availableCount;

            if (desk.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-ban text-3xl mb-3"></i>
                        <p>Tidak ada item pada meja ini</p>
                    </div>
                `;
                return;
            }

            let itemsHTML = '<div class="space-y-3">';

            desk.items.forEach((item, idx) => {
                console.log(item);

                const isAvailable = item.available;
                const inCart = cart.some(c => c.id === item.id);
                const itemData = encodeURIComponent(JSON.stringify(item));
                const typeName = (item.type || 'OTHER').toUpperCase();
                const typeClass = getTypeClass(typeName);

                let itemClass = 'item-card';
                let statusBadge = '';
                let actionButton = '';
                let statusText = '';

                if (!isAvailable) {
                    itemClass += ' unavailable';
                    if (item.condition === 0) {
                        statusBadge =
                            '<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">Rusak</span>';
                        statusText =
                            '<div class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-wrench mr-1"></i>Item dalam kondisi rusak</div>';
                    } else {
                        statusBadge =
                            '<span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Dipinjam</span>';
                        statusText =
                            '<div class="text-xs text-red-500 mt-2"><i class="fa-solid fa-clock mr-1"></i>Item sedang dipinjam</div>';
                    }
                } else if (inCart) {
                    itemClass += ' selected';
                    statusBadge =
                        '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">Dipilih</span>';
                    actionButton = `
                        <button onclick="removeFromCart('${item.id}')" 
                                class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                            <i class="fa-solid fa-times mr-1"></i> Batal
                        </button>
                    `;
                } else {
                    statusBadge =
                        '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Tersedia</span>';
                    actionButton = `
                        <button onclick="addToCart('${itemData}')" 
                                class="px-3 py-1 bg-petra-blue text-white rounded-lg hover:bg-blue-800 text-sm">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah
                        </button>
                    `;
                }

                const hasComponents = item.components && item.components.length > 0;

                itemsHTML += `
                    <div class="${itemClass}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="${typeClass} text-xs px-2 py-1 rounded font-semibold">${typeName}</span>
                                </div>
                                <div class="font-semibold text-gray-800">${item.name}</div>
                                <div class="text-xs text-gray-500 font-mono mt-1">${item.serial_code || 'No Serial'}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${statusBadge}
                            </div>
                        </div>

                        ${hasComponents ? `
                                <div class="mt-2 mb-2">
                                    <div class="text-xs text-gray-600 font-semibold mb-1">
                                        <i class="fa-solid fa-puzzle-piece mr-1"></i>Components (${item.components.length})
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        ${item.components.slice(0, 3).map(comp => `
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">${comp.type || 'N/A'}</span>
                                    `).join('')}
                                        ${item.components.length > 3 ? `<span class="text-xs text-gray-500">+${item.components.length - 3} lagi</span>` : ''}
                                    </div>
                                </div>
                            ` : ''}

                        <div class="flex justify-between items-center mt-2 gap-2">
                            ${hasComponents ? `
                                    <button onclick="showItemDetail('${itemData}')" 
                                            class="text-xs text-petra-blue hover:text-blue-800">
                                        <i class="fa-solid fa-info-circle mr-1"></i> Lihat Detail
                                    </button>
                                ` : '<div></div>'}
                            <div>${actionButton}</div>
                        </div>

                        ${statusText}
                    </div>
                `;
            });

            itemsHTML += '</div>';
            container.innerHTML = itemsHTML;
        }

        function getTypeClass(typeName) {
            const typeMap = {
                'CPU': 'type-badge-cpu',
                'MONITOR': 'type-badge-monitor',
                'MOUSE': 'type-badge-mouse',
                'KEYBOARD': 'type-badge-keyboard'
            };
            return typeMap[typeName] || 'type-badge-other';
        }

        function showItemDetail(itemEncoded) {
            const item = JSON.parse(decodeURIComponent(itemEncoded));
            const typeName = item.type || 'N/A';
            const typeClass = getTypeClass(typeName.toUpperCase());

            let detailHTML = `
                <div class="space-y-4">
                    <div class="border-b pb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="${typeClass} text-sm px-3 py-1 rounded font-semibold">${typeName}</span>
                            <span class="text-xs ${item.condition === 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} px-2 py-1 rounded-full">
                                ${item.condition === 1 ? 'Baik' : 'Rusak'}
                            </span>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800">${item.name}</h4>
                        <p class="text-sm text-gray-500 font-mono mt-1">${item.serial_code || 'No Serial'}</p>
                    </div>

                    ${item.specifications && item.specifications.length > 0 ? `
                            <div>
                                <h5 class="font-semibold text-gray-700 mb-2"><i class="fa-solid fa-list mr-2"></i>Spesifikasi Item</h5>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                    ${item.specifications.map(spec => `
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">${spec.name}:</span>
                                        <span class="font-medium text-gray-800">${spec.value}</span>
                                    </div>
                                `).join('')}
                                </div>
                            </div>
                        ` : ''}

                    ${item.components && item.components.length > 0 ? `
                            <div>
                                <h5 class="font-semibold text-gray-700 mb-2">
                                    <i class="fa-solid fa-puzzle-piece mr-2"></i>Components (${item.components.length})
                                </h5>
                                <div id="component-list-scroll" style="max-height: 400px; overflow-y: auto;">
                                    <div class="space-y-2">
                                    ${item.components.map(comp => {
                                        const compTypeName = comp.type || 'N/A';
                                        const compTypeClass = getTypeClass(compTypeName.toUpperCase());
                                        return `
                                        <div class="component-card">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="${compTypeClass} text-xs px-2 py-1 rounded font-semibold">${compTypeName}</span>
                                                    </div>
                                                    <div class="font-medium text-gray-800">${comp.name}</div>
                                                    <div class="text-xs text-gray-500 font-mono mt-1">${comp.serial_code || 'No Serial'}</div>
                                                </div>
                                            </div>
                                            ${comp.specifications && comp.specifications.length > 0 ? `
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        <div class="text-xs text-gray-600 space-y-1">
                                                            ${comp.specifications.map(spec => `
                                                            <div class="flex justify-between">
                                                                <span>${spec.name}:</span>
                                                                <span class="font-medium">${spec.value}</span>
                                                            </div>
                                                        `).join('')}
                                                        </div>
                                                    </div>
                                                ` : ''}
                                        </div>
                                    `;
                                    }).join('')}
                                    </div>
                                </div>
                            </div>
                        ` : ''}
                </div>
            `;

            showLayoutModal(`Detail: ${item.name}`, detailHTML);

            setTimeout(() => {
                initializeModalLenis();
                initializeComponentListLenis();
            }, 100);
        }

        function addToCart(itemEncoded, isComponent = false) {
            const item = JSON.parse(decodeURIComponent(itemEncoded));
            if (isComponent) item.is_component = true;

            if (cart.some(c => c.id === item.id && (c.is_component === item.is_component))) {
                showToast('Item sudah ada di keranjang', '', 'info');
                return;
            }

            cart.push(item);
            updateCartDisplay();
            refreshItemList();
            renderLabStorage();

            showToast(`${item.name} ditambahkan ke keranjang`, '', 'success');
        }

        function removeFromCart(itemId, isComponent = false) {
            cart = cart.filter(item => !(item.id === itemId && (item.is_component === isComponent)));
            updateCartDisplay();
            refreshItemList();
            renderLabStorage();

            showToast('Item dihapus dari keranjang', '', 'info');
        }

        function clearCart() {
            if (cart.length === 0) return;

            Swal.fire({
                title: 'Kosongkan Keranjang?',
                text: 'Semua item di keranjang akan dihapus',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1A2D4A',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Kosongkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    updateCartDisplay();
                    refreshItemList();
                    renderLabStorage();
                    showToast('Keranjang dikosongkan', '', 'success');
                }
            });
        }

        function updateCartDisplay() {
            const countElement = document.getElementById('cart-count');
            const container = document.getElementById('cart-items');

            if (countElement) {
                countElement.textContent = cart.length;
            }

            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-solid fa-shopping-cart text-3xl mb-3"></i>
                        <p>Belum ada item di keranjang</p>
                    </div>
                `;
                return;
            }

            let cartHTML = '<div class="space-y-2">';

            cart.forEach(item => {
                const badge = item.is_component ?
                    '<span class="text-xs bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded ml-1">Comp</span>' :
                    '';
                cartHTML += `
                    <div class="cart-item flex justify-between items-center">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">${item.name}${badge}</div>
                            <div class="text-xs text-gray-500">${item.serial_code || 'No Serial'}</div>
                        </div>
                        <button onclick="removeFromCart('${item.id}', ${item.is_component || false})" 
                                class="text-red-500 hover:text-red-700 ml-2">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                `;
            });

            cartHTML += '</div>';
            container.innerHTML = cartHTML;
        }

        function refreshItemList() {
            if (selectedDeskIndex !== null && currentDeskMap[selectedDeskIndex]) {
                populateItemList(currentDeskMap[selectedDeskIndex]);
            }
        }

        async function submitBooking() {
            if (!validateForm()) return;

            const bookingType = document.querySelector('input[name="booking_type"]:checked').value;
            const payload = buildPayload(bookingType);

            if (!payload) return;

            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...';

            showLoading('Mengajukan peminjaman...');

            try {
                const response = await fetch('/booking', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                Swal.close();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Peminjaman berhasil diajukan',
                        confirmButtonColor: '#1A2D4A'
                    });

                    window.location.href = "{{ route('user.booking.history') }}";
                } else {
                    throw new Error(data.message || 'Pengajuan gagal');
                }

            } catch (error) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message,
                    confirmButtonColor: '#1A2D4A'
                });
            }
        }

        function validateForm() {
            const requiredFields = [
                'event_name',
                'borrowed_at',
                'return_deadline_at',
                'phone_number'
            ];

            for (const fieldId of requiredFields) {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    showToast(`Harap isi ${field.previousElementSibling?.textContent || 'field ini'}`, '', 'warning');
                    field.focus();
                    return false;
                }
            }

            const phone = document.getElementById('phone_number').value;
            if (!/^\d{10,20}$/.test(phone)) {
                showToast('Nomor WhatsApp harus berupa angka dengan panjang 10 hingga 20 digit', '', 'warning');
                return false;
            }

            const start = new Date(document.getElementById('borrowed_at').value);
            const end = new Date(document.getElementById('return_deadline_at').value);

            if (start >= end) {
                showToast('Waktu selesai harus setelah waktu mulai', '', 'warning');
                return false;
            }

            const bookingType = document.querySelector('input[name="booking_type"]:checked').value;

            switch (bookingType) {
                case 'lab':
                    if (!document.getElementById('lab-select').value) {
                        showToast('Pilih laboratorium', '', 'warning');
                        return false;
                    }
                    if (!document.getElementById('attendee_count').value ||
                        document.getElementById('attendee_count').value < 1) {
                        showToast('Isi jumlah peserta', '', 'warning');
                        return false;
                    }
                    break;

                case 'sets':
                    const setInputs = document.querySelectorAll('input[data-lab-id]');
                    if (setInputs.length === 0) {
                        showToast('Tambahkan minimal 1 lab', '', 'warning');
                        return false;
                    }
                    break;

                case 'items':
                    if (cart.length === 0) {
                        showToast('Tambahkan minimal 1 item ke keranjang', '', 'warning');
                        return false;
                    }
                    break;
            }

            return true;
        }

        function buildPayload(bookingType) {
            const start = document.getElementById('borrowed_at').value;
            const end = document.getElementById('return_deadline_at').value;

            const basePayload = {
                booking_type: bookingType,
                event_name: document.getElementById('event_name').value.trim(),

                borrowed_at: start,
                return_deadline_at: end,

                event_started_at: start,
                event_ended_at: end,

                phone_number: document.getElementById('phone_number').value.trim(),
                type: document.getElementById('type').value,
                academic_detail: document.getElementById('academic_detail').value.trim()
            };

            switch (bookingType) {
                case 'lab':
                    basePayload.bookable_type = 'lab';
                    basePayload.bookable_id = document.getElementById('lab-select').value;
                    basePayload.attendee_count = parseInt(document.getElementById('attendee_count').value);
                    break;

                case 'sets':
                    const setInputs = document.querySelectorAll('input[data-lab-id]');
                    basePayload.sets = Array.from(setInputs).map(input => ({
                        lab_id: input.dataset.labId,
                        quantity: parseInt(input.value) || 0
                    }));
                    break;

                case 'items':
                    basePayload.items = cart.filter(item => !item.is_component).map(item => item.id);
                    basePayload.components = cart.filter(item => item.is_component).map(item => item.id);
                    break;
            }

            return basePayload;
        }

        function showLoading(title) {
            Swal.fire({
                title: title,
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function syncListHeight() {
            // Removed - using fixed max-height instead
        }
    </script>
@endsection
