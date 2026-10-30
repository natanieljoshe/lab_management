@extends('layouts.user')

@section('title', 'Formulir Helpdesk & Pengaduan')

@section('style')
    @include('user.partials.helpdesk-theme')
    <style>
        .hx-page::before { height: 100%; }
        /* ============================================================
           SHELL
           ============================================================ */
        .hx-shell {
            background: var(--panel); border: 1px solid var(--line);
            box-shadow: 0 50px 90px -60px rgba(26, 45, 74, .45);
        }
        .hx-shell-head {
            display: flex; justify-content: space-between; align-items: center; gap: 24px; flex-wrap: wrap;
            padding: 36px 44px; border-bottom: 1px solid var(--line);
        }
        .hx-shell-body { padding: 56px 44px 52px; }

        /* Langkah */
        .hx-section { margin-bottom: 76px; }
        .hx-section:last-child { margin-bottom: 0; }
        .hx-step { display: flex; align-items: baseline; gap: 18px; margin-bottom: 34px; }
        .hx-step-no { font-family: var(--serif); font-style: italic; font-size: 1.7rem; color: var(--clr-orange); font-weight: 500; }
        .hx-step h3 { font-family: var(--serif); font-weight: 500; font-size: clamp(1.5rem, 2.6vw, 2.1rem); line-height: 1.1; color: var(--clr-navy); }
        .hx-step h3 .req { color: var(--clr-orange); }
        .hx-step::after { content: ''; flex: 1; height: 1px; background: var(--line); align-self: center; }

        /* ============================================================
           OPSI TERPILIH (fill sweep)
           ============================================================ */
        .hx-opt-wrap { display: block; cursor: pointer; }
        .hx-opt {
            position: relative; overflow: hidden; isolation: isolate; cursor: pointer;
            border: 1px solid var(--line); background: transparent; color: var(--clr-navy); height: 100%;
            transition: color .6s var(--ease), border-color .6s var(--ease);
        }
        .hx-opt::before {
            content: ''; position: absolute; inset: 0; z-index: -1; background: var(--clr-navy);
            clip-path: inset(100% 0 0 0); transition: clip-path .85s var(--ease-io);
        }
        .hx-opt:hover { border-color: var(--clr-navy); }
        .hx-opt:hover:not(.active)::before { clip-path: inset(calc(100% - 3px) 0 0 0); }
        .hx-opt.active { color: var(--clr-bg); border-color: var(--clr-navy); }
        .hx-opt.active::before { clip-path: inset(0 0 0 0); }
        .hx-opt p { color: var(--clr-muted); transition: color .6s var(--ease); }
        .hx-opt.active p { color: rgba(251, 250, 245, .72); }
        .hx-opt-icon {
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            border: 1px solid var(--line); color: var(--clr-orange);
            transition: border-color .6s var(--ease), background .6s var(--ease);
        }
        .hx-opt.active .hx-opt-icon { border-color: var(--line-light); }

        /* Besar: pilih jenis pengajuan */
        .hx-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
        .hx-opt--lg { display: grid; grid-template-columns: 60px 1fr; gap: 8px 24px; padding: 36px 32px; align-items: start; }
        .hx-opt--lg .hx-opt-icon { width: 60px; height: 60px; font-size: 1.3rem; grid-row: span 2; }
        .hx-opt--lg h4 { font-family: var(--serif); font-weight: 600; font-size: 1.75rem; line-height: 1.05; }
        .hx-opt--lg p { font-size: .86rem; line-height: 1.75; }

        /* Sedang: kategori masukan */
        .hx-opt--md { display: flex; align-items: flex-start; gap: 18px; padding: 24px; }
        .hx-opt--md .hx-opt-icon { width: 46px; height: 46px; font-size: 1rem; }
        .hx-opt--md h5 { font-family: var(--serif); font-weight: 600; font-size: 1.3rem; line-height: 1.1; margin-bottom: 6px; }
        .hx-opt--md p { font-size: .78rem; line-height: 1.7; }

        /* Kecil: penempatan perangkat */
        .hx-opt--sm { padding: 20px 14px; text-align: center; }
        .hx-opt--sm i { display: block; font-size: 1.15rem; margin-bottom: 10px; color: var(--clr-orange); }
        .hx-opt--sm span { font-size: .64rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }

        /* ============================================================
           FIELDS
           ============================================================ */
        .hx-form { display: block; }
        .hx-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
        .hx-fields { display: grid; gap: 40px; }
        .hx-fields-sm { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        /* Pilih meja */
        .hx-desk-wrap { margin-top: 38px; padding-top: 30px; border-top: 1px solid var(--line); }
        .hx-desk-head { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 16px; }
        #desk-buttons-container {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(86px, 1fr)); gap: 8px;
            padding: 16px; max-height: 240px; overflow-y: auto;
            border: 1px solid var(--line); background: rgba(26, 45, 74, .03);
        }
        .desk-card {
            position: relative; overflow: hidden; isolation: isolate; cursor: pointer; user-select: none;
            min-height: 54px; padding: 8px; display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--line); background: transparent; color: var(--clr-navy);
            font-family: var(--sans); font-size: .74rem; font-weight: 700; letter-spacing: .08em;
            transition: color .5s var(--ease), border-color .5s var(--ease);
        }
        .desk-card::before { content: ''; position: absolute; inset: 0; z-index: -1; background: var(--clr-navy); transform: translateY(101%); transition: transform .6s var(--ease-io); }
        .desk-card:hover { border-color: var(--clr-navy); }
        .desk-card.selected { color: var(--clr-bg); border-color: var(--clr-navy); }
        .desk-card.selected::before { transform: translateY(0); }
        .desk-card:focus-visible { outline: 2px solid var(--clr-orange); outline-offset: 2px; }

        /* Daftar barang */
        #items-list-container { display: grid; gap: 16px; }
        .item-card {
            position: relative; padding: 24px 26px 24px 30px; border: 1px solid var(--line); background: rgba(255, 255, 255, .55);
            transition: border-color .5s var(--ease), background .5s var(--ease);
            animation: hx-in .8s var(--ease) both;
        }
        .item-card::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--clr-orange);
            transform: scaleY(0); transform-origin: top; transition: transform .6s var(--ease-io);
        }
        .item-card.selected { border-color: var(--clr-navy); background: #fff; }
        .item-card.selected::before { transform: scaleY(1); }
        .hx-item-head { display: flex; align-items: flex-start; gap: 18px; }
        .hx-item-head > .hx-check { margin-top: 5px; }
        .hx-item-main { flex: 1; min-width: 0; }
        .hx-item-title { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; }
        .hx-item-name { font-family: var(--serif); font-weight: 600; font-size: 1.35rem; line-height: 1.15; cursor: pointer; color: var(--clr-navy); }
        .hx-sn { margin-top: 6px; font-size: .74rem; color: var(--clr-muted); }
        .hx-desc { margin-top: 18px; padding-top: 18px; border-top: 1px solid var(--line); }

        .hx-comps { margin-top: 22px; padding-left: 20px; border-left: 1px solid var(--line); display: grid; gap: 10px; }
        .hx-comps-label { font-size: .6rem; font-weight: 700; letter-spacing: .26em; text-transform: uppercase; color: var(--clr-orange); }
        .hx-comp { display: flex; align-items: flex-start; gap: 14px; padding: 14px 16px; border: 1px solid var(--line); background: rgba(26, 45, 74, .03); }
        .hx-comp > .hx-check { margin-top: 2px; width: 18px; height: 18px; }
        .hx-comp > .hx-check::after { left: 5px; top: 1px; }
        .hx-comp-name { font-weight: 700; font-size: .84rem; cursor: pointer; color: var(--clr-navy); }
        .hx-comp-type { font-size: .7rem; color: var(--clr-muted); }

        .hx-empty-items { padding: 56px 20px; text-align: center; border: 1px dashed var(--line); font-size: .86rem; color: var(--clr-muted); }

        .hx-submit { display: flex; justify-content: flex-end; margin-top: 56px; padding-top: 32px; border-top: 1px solid var(--line); }

        /* ============================================================
           KONTAK
           ============================================================ */
        .hx-contact { position: relative; margin-top: 56px; overflow: hidden; background: var(--clr-navy); color: var(--clr-bg); padding: 64px 52px; }
        .hx-contact::before {
            content: ''; position: absolute; inset: 0; pointer-events: none; opacity: .5;
            background-image: linear-gradient(var(--line-light) 1px, transparent 1px), linear-gradient(90deg, var(--line-light) 1px, transparent 1px);
            background-size: 80px 80px;
            -webkit-mask-image: radial-gradient(ellipse at 15% 0%, #000 5%, transparent 70%);
                    mask-image: radial-gradient(ellipse at 15% 0%, #000 5%, transparent 70%);
        }
        .hx-contact-inner { position: relative; display: grid; grid-template-columns: 1.1fr .9fr; gap: 56px; align-items: end; }
        .hx-contact .hx-eyebrow { margin-bottom: 22px; }
        .hx-contact h3 { font-family: var(--serif); font-weight: 500; font-size: clamp(2.2rem, 4.4vw, 3.6rem); line-height: 1; color: var(--clr-bg); }
        .hx-contact h3 em { font-style: italic; color: var(--clr-orange); }
        .hx-contact p { margin-top: 18px; font-size: .92rem; line-height: 1.8; color: rgba(251, 250, 245, .7); max-width: 420px; }
        .hx-contact-list { list-style: none; margin: 0; padding: 0; }
        .hx-contact-list li { display: flex; align-items: center; gap: 18px; padding: 20px 0; border-top: 1px solid var(--line-light); font-size: .95rem; color: var(--clr-bg); }
        .hx-contact-list li:last-child { border-bottom: 1px solid var(--line-light); }
        .hx-contact-list i { color: var(--clr-orange); width: 18px; text-align: center; }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 899px) {
            .hx-shell-head { padding: 28px 24px; }
            .hx-shell-body { padding: 40px 24px 36px; }
            .hx-grid-2, .hx-field-grid { grid-template-columns: 1fr; gap: 22px; }
            .hx-contact { padding: 44px 26px; }
            .hx-contact-inner { grid-template-columns: 1fr; gap: 36px; }
            .hx-section { margin-bottom: 56px; }
        }
        @media (max-width: 639px) {
            .hx-opt--lg { grid-template-columns: 48px 1fr; padding: 26px 22px; gap: 6px 18px; }
            .hx-opt--lg .hx-opt-icon { width: 48px; height: 48px; }
            .hx-item-name { font-size: 1.15rem; }
            .item-card { padding: 20px 18px 20px 22px; }
            .hx-submit .hx-btn { width: 100%; }
        }
    </style>
@endsection

@section('body')
    @include('user.partials.navbar')

    <div class="hx-page">
        <div class="hx-wrap">

            {{-- Hero Header --}}
            <header class="hx-hero" data-reveal>
                <div class="hx-ghost" aria-hidden="true">Helpdesk</div>
                <div class="hx-eyebrow hx-fade">Pusat bantuan laboratorium</div>
                <h1 class="hx-display hx-title">
                    <span class="hx-mask" style="--d:.05s"><span>Buat</span></span>
                    <span class="hx-mask" style="--d:.17s"><span><em>laporan</em></span></span>
                </h1>
                <p class="hx-lead hx-fade" style="--d:.4s">
                    Laporkan kerusakan fasilitas laboratorium atau sampaikan keluhan dan saran untuk kenyamanan akademik bersama
                </p>
            </header>

            {{-- Main Form Card --}}
            <section class="hx-shell hx-fade" data-reveal style="--d:.1s">

                {{-- Form Header --}}
                <div class="hx-shell-head">
                    <div>
                        <h2 class="hx-h2">Apa yang ingin Anda <em>laporkan?</em></h2>
                        <p class="hx-sub">Pilih jenis laporan, lalu lengkapi detailnya.</p>
                    </div>
                    <a href="{{ route('user.helpdesk.history') }}" class="hx-btn ghost sm">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Riwayat Laporan Saya</span>
                    </a>
                </div>

                {{-- Form Content --}}
                <div class="hx-shell-body">

                    {{-- Step 1: Tipe Pengajuan --}}
                    <div class="hx-section">
                        <div class="hx-step">
                            <span class="hx-step-no">01</span>
                            <h3>Pilih Jenis Pengajuan</h3>
                        </div>

                        <div class="hx-grid-2">
                            <!-- Opsi 1: Kerusakan Barang -->
                            <label class="hx-opt-wrap" onclick="switchHelpdeskTab('repair')">
                                <input type="radio" name="helpdesk_service_type" value="repair" id="radio_type_repair" class="sr-only peer" checked>
                                <div id="card_type_repair" class="hx-opt hx-opt--lg active">
                                    <div class="hx-opt-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                                    <h4>Lapor Kerusakan</h4>
                                    <p>Perbaikan perangkat PC, monitor, periferal, atau komponen meja</p>
                                </div>
                            </label>

                            <!-- Opsi 2: Keluhan & Saran -->
                            <label class="hx-opt-wrap" onclick="switchHelpdeskTab('feedback')">
                                <input type="radio" name="helpdesk_service_type" value="feedback" id="radio_type_feedback" class="sr-only peer">
                                <div id="card_type_feedback" class="hx-opt hx-opt--lg">
                                    <div class="hx-opt-icon"><i class="fa-solid fa-comments"></i></div>
                                    <h4>Keluhan &amp; Saran</h4>
                                    <p>Saran fasilitas lab, kebersihan, AC, proyektor, atau kendala pelayanan</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- TAB 1: FORM LAPOR KERUSAKAN BARANG                      --}}
                    {{-- ========================================================= --}}
                    <div id="tab-pane-repair">
                        <form id="repair-form" class="hx-form">
                            @csrf

                            {{-- Step 2: Lokasi & Perangkat --}}
                            <div class="hx-section">
                                <div class="hx-step">
                                    <span class="hx-step-no">02</span>
                                    <h3>Lokasi &amp; Perangkat Laboratorium</h3>
                                </div>

                                <div class="hx-field-grid">
                                    <div>
                                        <label for="repair-lab" class="hx-label">
                                            Pilih Laboratorium <span class="req">*</span>
                                        </label>
                                        <select id="repair-lab" required class="form-input">
                                            <option value="">-- Pilih Laboratorium --</option>
                                            @foreach ($labs as $lab)
                                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                            @endforeach
                                        </select>
                                        <p class="hx-hint">Pilih laboratorium tempat barang yang rusak berada.</p>
                                    </div>

                                    <div id="device-source-section" class="hidden">
                                        <label class="hx-label">
                                            Penempatan Perangkat <span class="req">*</span>
                                        </label>
                                        <div class="hx-fields-sm" style="margin-top:10px;">
                                            <label class="hx-opt-wrap" onclick="toggleDeviceSource('desk')">
                                                <input type="radio" name="device_source" value="desk" checked class="sr-only">
                                                <div id="source-desk-card" class="hx-opt hx-opt--sm active">
                                                    <i class="fa-solid fa-desktop"></i>
                                                    <span>Meja Praktikum</span>
                                                </div>
                                            </label>
                                            <label class="hx-opt-wrap" onclick="toggleDeviceSource('storage')">
                                                <input type="radio" name="device_source" value="storage" class="sr-only">
                                                <div id="source-storage-card" class="hx-opt hx-opt--sm">
                                                    <i class="fa-solid fa-box-open"></i>
                                                    <span>Alat Umum / Storage</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pilihan Meja Grid --}}
                                <div id="desk-selection-wrapper" class="hidden hx-desk-wrap">
                                    <div class="hx-desk-head">
                                        <label class="hx-label" style="margin:0;">Pilih Meja Praktikum:</label>
                                        <span id="current-location-badge" class="hx-tag hx-tag--orange">Pilih meja</span>
                                    </div>
                                    <div id="desk-buttons-container">
                                        {{-- Diisi secara dinamis oleh JavaScript --}}
                                    </div>
                                </div>
                            </div>

                            {{-- Step 3: Daftar Barang & Masalah --}}
                            <div id="items-selection-section" class="hidden hx-section">
                                <div class="hx-step">
                                    <span class="hx-step-no">03</span>
                                    <h3>Centang Barang &amp; Deskripsikan Kerusakan <span class="req">*</span></h3>
                                </div>

                                <div id="items-list-container">
                                    {{-- Render item list oleh JavaScript --}}
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div id="repair-submit-section" class="hidden hx-submit">
                                <button type="submit" class="hx-btn">
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
                        <form id="feedback-form" class="hx-form">
                            @csrf

                            <div class="hx-section">
                                <div class="hx-step">
                                    <span class="hx-step-no">02</span>
                                    <h3>Detail Keluhan / Saran Anda</h3>
                                </div>

                                <div class="hx-fields">
                                    {{-- Kategori: Keluhan vs Saran --}}
                                    <div>
                                        <label class="hx-label" style="margin-bottom:14px;">
                                            Kategori Masukan <span class="req">*</span>
                                        </label>
                                        <div class="hx-grid-2">
                                            <label class="hx-opt-wrap" onclick="toggleFeedbackType('KELUHAN')">
                                                <input type="radio" name="feedback_type" value="KELUHAN" checked class="sr-only peer">
                                                <div id="type-keluhan-card" class="hx-opt hx-opt--md active">
                                                    <div class="hx-opt-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                                    <div>
                                                        <h5>Keluhan Fasilitas</h5>
                                                        <p>AC tidak dingin, proyektor redup, kebersihan, pelayanan</p>
                                                    </div>
                                                </div>
                                            </label>

                                            <label class="hx-opt-wrap" onclick="toggleFeedbackType('SARAN')">
                                                <input type="radio" name="feedback_type" value="SARAN" class="sr-only peer">
                                                <div id="type-saran-card" class="hx-opt hx-opt--md">
                                                    <div class="hx-opt-icon"><i class="fa-solid fa-lightbulb"></i></div>
                                                    <div>
                                                        <h5>Saran &amp; Rekomendasi</h5>
                                                        <p>Usulan software baru, layout lab, aturan praktikum</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Cakupan Lab --}}
                                    <div>
                                        <label for="feedback-lab" class="hx-label">
                                            Terkait Laboratorium Spesifik (Opsional)
                                        </label>
                                        <select id="feedback-lab" class="form-input">
                                            <option value="">Fasilitas Umum / Semua Lab</option>
                                            @foreach ($labs as $lab)
                                                <option value="{{ $lab->id }}">{{ $lab->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Subjek --}}
                                    <div>
                                        <label for="feedback-subject" class="hx-label">
                                            Judul / Subjek <span class="req">*</span>
                                        </label>
                                        <input type="text" id="feedback-subject" required maxlength="150"
                                            placeholder="Contoh: AC Lab P2.04 tidak dingin pada sesi praktikum sore"
                                            class="form-input">
                                    </div>

                                    {{-- Deskripsi --}}
                                    <div>
                                        <label for="feedback-description" class="hx-label">
                                            Uraian Lengkap Keluhan / Saran <span class="req">*</span>
                                        </label>
                                        <textarea id="feedback-description" required rows="5" maxlength="2000"
                                            placeholder="Tuliskan keluhan atau saran Anda secara rinci, termasuk waktu kejadian atau kondisi terkait..."
                                            class="form-input"></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="hx-submit">
                                <button type="submit" class="hx-btn">
                                    <i class="fa-solid fa-paper-plane"></i>
                                    <span>Kirim Tiket Keluhan/Saran</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </section>

            {{-- Contact --}}
            <section class="hx-contact hx-fade" data-reveal>
                <div class="hx-contact-inner">
                    <div>
                        <div class="hx-eyebrow">Hubungi kami</div>
                        <h3>Butuh <em>bantuan?</em></h3>
                        <p>Jika kesulitan mengisi formulir, hubungi administrator laboratorium.</p>
                    </div>
                    <ul class="hx-contact-list">
                        <li><i class="fa-solid fa-envelope"></i><span>lab@petra.ac.id</span></li>
                        <li><i class="fa-solid fa-phone"></i><span>(031) 1234-5678</span></li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
    @include('user.partials.footer')
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
            container.innerHTML = '<div style="grid-column:1/-1;padding:14px 0;text-align:center;font-size:.78rem;color:rgba(26,45,74,.5);">Tidak ada meja terdaftar di lab ini</div>';
            return;
        }

        container.innerHTML = desks.map(d => `
            <button type="button" onclick="selectDesk('${d.id}')" id="btn-desk-${d.id}"
                    class="desk-card ${selectedDeskId === d.id ? 'selected' : ''}">
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
            container.innerHTML = `<div class="hx-empty-items">Tidak ada perangkat terdaftar di ${locationName}.</div>`;
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
                    <div class="hx-item-head">
                        <input type="checkbox" id="item-chk-${item.id}" value="${item.id}" data-type="item"
                               onchange="toggleItemDescription('item', '${item.id}')"
                               class="hx-check"
                               ${isRepairUnavailable ? 'disabled' : ''}>
                        <div class="hx-item-main">
                            <div class="hx-item-title">
                                <label for="item-chk-${item.id}" class="hx-item-name">${item.name}</label>
                                <span class="hx-tag">${item.type}</span>
                                ${isUnderRepair ? '<span class="hx-tag hx-tag--orange">Sedang Diperbaiki</span>' : ''}
                                ${isPendingRepair ? '<span class="hx-tag hx-tag--mute">Menunggu Diproses</span>' : ''}
                                ${isBroken && !isRepairUnavailable ? '<span class="hx-tag hx-tag--bad">Kondisi Rusak</span>' : ''}
                            </div>
                            <p class="hx-sn hx-mono">SN: ${item.serial_code}</p>

                            <div id="desc-box-item-${item.id}" class="hidden hx-desc">
                                <label class="hx-label" for="desc-val-item-${item.id}">Uraikan kendala/kerusakan pada ${item.name}:</label>
                                <textarea id="desc-val-item-${item.id}" rows="2" maxlength="1000"
                                          placeholder="Contoh: Layar bergaris horizontal dan berkedip saat menyala..."
                                          class="form-input"></textarea>
                            </div>

                            ${item.components && item.components.length > 0 ? `
                                <div class="hx-comps">
                                    <p class="hx-comps-label">Komponen Internal (Pilih jika spesifik)</p>
                                    ${item.components.map(comp => `
                                        <div class="hx-comp">
                                            <input type="checkbox" id="comp-chk-${comp.id}" value="${comp.id}" data-type="component"
                                                   onchange="toggleItemDescription('comp', '${comp.id}')"
                                                   class="hx-check"
                                                   ${(comp.is_under_repair || comp.is_pending_repair) ? 'disabled' : ''}>
                                            <div class="hx-item-main">
                                                <div class="hx-item-title" style="gap:8px;">
                                                    <label for="comp-chk-${comp.id}" class="hx-comp-name">${comp.name}</label>
                                                    <span class="hx-comp-type hx-mono">(${comp.type})</span>
                                                    ${comp.is_under_repair ? '<span class="hx-tag hx-tag--orange">Sedang Diperbaiki</span>' : ''}
                                                    ${comp.is_pending_repair ? '<span class="hx-tag hx-tag--mute">Menunggu Diproses</span>' : ''}
                                                </div>
                                                <div id="desc-box-comp-${comp.id}" class="hidden" style="margin-top:12px;">
                                                    <textarea id="desc-val-comp-${comp.id}" rows="2" maxlength="1000"
                                                              placeholder="Kendala komponen ini..."
                                                              class="form-input"></textarea>
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
                <div class="item-card" id="item-card-wrapper-${comp.id}">
                    <div class="hx-item-head">
                        <input type="checkbox" id="comp-chk-${comp.id}" value="${comp.id}" data-type="component"
                               onchange="toggleItemDescription('comp', '${comp.id}')"
                               class="hx-check"
                               ${(comp.is_under_repair || comp.is_pending_repair) ? 'disabled' : ''}>
                        <div class="hx-item-main">
                            <div class="hx-item-title">
                                <label for="comp-chk-${comp.id}" class="hx-item-name">${comp.name}</label>
                                <span class="hx-tag">${comp.type}</span>
                                ${comp.is_under_repair ? '<span class="hx-tag hx-tag--orange">Sedang Diperbaiki</span>' : ''}
                                ${comp.is_pending_repair ? '<span class="hx-tag hx-tag--mute">Menunggu Diproses</span>' : ''}
                            </div>
                            <div id="desc-box-comp-${comp.id}" class="hidden hx-desc">
                                <textarea id="desc-val-comp-${comp.id}" rows="2" maxlength="1000"
                                          placeholder="Uraikan kendala komponen..."
                                          class="form-input"></textarea>
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
            confirmButtonColor: '#1A2D4A',
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
                    confirmButtonColor: '#1A2D4A',
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
                    confirmButtonColor: '#1A2D4A',
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
