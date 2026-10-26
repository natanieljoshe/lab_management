{{-- FOOTER — Editorial Luxury (#1A2D4A, #FBFAF5, #F57E21) --}}
<style>
    .ft {
        --ft-ease: cubic-bezier(0.22, 1, 0.36, 1);
        --ft-ease-io: cubic-bezier(0.76, 0, 0.24, 1);
        --ft-line: rgba(251, 250, 245, 0.16);
        position: relative;
        overflow: hidden;
        background: #1A2D4A;
        color: rgba(251, 250, 245, 0.72);
        font-family: 'Open Sans', sans-serif;
        padding: 120px 0 0;
    }

    .ft *,
    .ft *::before,
    .ft *::after {
        box-sizing: border-box;
    }

    .ft a {
        text-decoration: none;
    }

    .ft::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .5;
        background-image: linear-gradient(var(--ft-line) 1px, transparent 1px), linear-gradient(90deg, var(--ft-line) 1px, transparent 1px);
        background-size: 96px 96px;
        -webkit-mask-image: radial-gradient(ellipse at 20% 0%, #000 5%, transparent 65%);
        mask-image: radial-gradient(ellipse at 20% 0%, #000 5%, transparent 65%);
    }

    .ft-wrap {
        position: relative;
        z-index: 2;
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 28px;
    }

    /* Reveal */
    .ft-mask {
        display: block;
        overflow: hidden;
        padding-bottom: .12em;
        margin-bottom: -.12em;
    }

    .ft-mask>span {
        display: block;
        transform: translateY(112%);
        transition: transform 1.1s var(--ft-ease);
        transition-delay: var(--d, 0s);
    }

    .ft-in .ft-mask>span {
        transform: translateY(0);
    }

    .ft-fade {
        opacity: 0;
        filter: blur(8px);
        transition: opacity 1s var(--ft-ease), filter 1s var(--ft-ease);
        transition-delay: var(--d, 0s);
    }

    .ft-in .ft-fade {
        opacity: 1;
        filter: blur(0);
    }

    .ft-rule {
        display: block;
        height: 1px;
        width: 0;
        background: var(--ft-line);
        transition: width 1.5s var(--ft-ease);
        transition-delay: var(--d, 0s);
    }

    .ft-in .ft-rule {
        width: 100%;
    }

    /* Atas: pernyataan + CTA */
    .ft-top {
        display: grid;
        grid-template-columns: 1.4fr .6fr;
        gap: 60px;
        align-items: end;
        padding-bottom: 80px;
    }

    .ft-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .32em;
        text-transform: uppercase;
        color: #F57E21;
    }

    .ft-eyebrow::before {
        content: '';
        width: 46px;
        height: 1px;
        background: currentColor;
    }

    .ft-statement {
        font-family: 'Cormorant Garamond', serif;
        font-weight: 500;
        line-height: 1;
        letter-spacing: -.015em;
        font-size: clamp(2.4rem, 5.4vw, 4.6rem);
        color: #FBFAF5;
        margin: 26px 0 0;
    }

    .ft-statement em {
        font-style: italic;
        color: #F57E21;
    }

    .ft-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 16px;
        overflow: hidden;
        isolation: isolate;
        padding: 20px 34px;
        border: 1px solid #FBFAF5;
        color: #FBFAF5;
        background: transparent;
        font-weight: 700;
        font-size: .72rem;
        letter-spacing: .22em;
        text-transform: uppercase;
        transition: color .5s var(--ft-ease), border-color .5s var(--ft-ease);
    }

    .ft-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: #F57E21;
        transform: translateX(-101%);
        transition: transform .65s var(--ft-ease-io);
    }

    .ft-btn:hover {
        border-color: #F57E21;
        color: #FBFAF5;
    }

    .ft-btn:hover::before {
        transform: translateX(0);
    }

    .ft-btn i {
        transition: transform .5s var(--ft-ease);
    }

    .ft-btn:hover i {
        transform: translateX(6px);
    }

    /* Kolom */
    .ft-grid {
        display: grid;
        grid-template-columns: 1.3fr .7fr 1fr 1fr;
        gap: 56px;
        padding: 64px 0 80px;
    }

    .ft-brand {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }

    .ft-brand-box {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(251, 250, 245, .4);
        color: #F57E21;
        font-size: 17px;
        flex-shrink: 0;
    }

    .ft-brand b {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: .05em;
        color: #FBFAF5;
        line-height: 1;
    }

    .ft-about {
        font-size: .86rem;
        line-height: 1.85;
        color: rgba(251, 250, 245, 0.68);
        max-width: 340px;
        margin: 0;
    }

    .ft-h {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0 0 26px;
        font-size: .64rem;
        font-weight: 700;
        letter-spacing: .3em;
        text-transform: uppercase;
        color: #FBFAF5;
    }

    .ft-h::before {
        content: '';
        width: 22px;
        height: 1px;
        background: #F57E21;
    }

    .ft-list {
        list-style: none;
        margin: 0;
        padding: 0;
        font-size: .86rem;
    }

    .ft-list li+li {
        margin-top: 14px;
    }

    .ft-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: rgba(251, 250, 245, .72);
        padding-bottom: 3px;
        transition: color .4s var(--ft-ease), padding .5s var(--ft-ease);
    }

    .ft-link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 1px;
        width: 100%;
        background: #F57E21;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform .6s var(--ft-ease-io);
    }

    .ft-link:hover {
        color: #F57E21;
    }

    .ft-link:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }

    .ft-link.strong {
        color: #FBFAF5;
        font-weight: 600;
    }

    .ft-contact {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        line-height: 1.7;
        color: rgba(251, 250, 245, .72);
    }

    .ft-contact i {
        color: #F57E21;
        margin-top: 5px;
        flex-shrink: 0;
        width: 16px;
        text-align: center;
    }

    .ft-hours {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 16px;
        padding: 14px 0;
        border-top: 1px solid var(--ft-line);
        font-size: .84rem;
        color: rgba(251, 250, 245, .72);
    }

    .ft-hours:last-of-type {
        border-bottom: 1px solid var(--ft-line);
    }

    .ft-hours b {
        color: #FBFAF5;
        font-weight: 600;
    }

    .ft-hours.closed span {
        color: rgba(251, 250, 245, .45);
        font-style: italic;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.05rem;
    }

    .ft-hours-wrap {
        margin-top: -14px;
    }

    /* Wordmark raksasa */
    .ft-mark {
        position: relative;
        z-index: 1;
        user-select: none;
        pointer-events: none;
        text-align: center;
        white-space: nowrap;
        font-family: 'Cormorant Garamond', serif;
        font-weight: 500;
        font-style: italic;
        line-height: .78;
        font-size: clamp(5rem, 22vw, 20rem);
        color: transparent;
        -webkit-text-stroke: 1px rgba(251, 250, 245, .16);
        margin-top: -10px;
        overflow: hidden;
        padding-bottom: .02em;
    }

    .ft-mark span {
        display: block;
        transform: translateY(100%);
        transition: transform 1.6s var(--ft-ease);
        transition-delay: .3s;
    }

    .ft-in .ft-mark span {
        transform: translateY(0);
    }

    /* Bawah */
    .ft-bottom {
        position: relative;
        z-index: 2;
        border-top: 1px solid var(--ft-line);
    }

    .ft-bottom .ft-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 24px;
        padding-bottom: 26px;
        font-size: .72rem;
        letter-spacing: .06em;
        color: rgba(251, 250, 245, .5);
    }

    .ft-bottom p {
        margin: 0;
    }

    .ft-bottom .o {
        color: #F57E21;
    }

    @media (max-width: 1023px) {
        .ft-top {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .ft-grid {
            grid-template-columns: 1fr 1fr;
            gap: 48px 40px;
        }
    }

    @media (max-width: 639px) {
        .ft {
            padding-top: 90px;
        }

        .ft-wrap {
            padding: 0 20px;
        }

        .ft-grid {
            grid-template-columns: 1fr;
            gap: 44px;
            padding: 52px 0 64px;
        }

        .ft-bottom .ft-wrap {
            flex-direction: column;
            text-align: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .ft .ft-mask>span,
        .ft .ft-fade,
        .ft .ft-rule,
        .ft .ft-mark span {
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
            filter: none !important;
            width: 100% !important;
        }
    }
</style>

<footer class="ft" id="lxFooter">
    <div class="ft-wrap">

        <span class="ft-rule"></span>

        <div class="ft-grid">

            {{-- Brand info --}}
            <div class="ft-fade" style="--d:.1s">
                <div class="ft-brand">
                    <div class="ft-brand-box"><i class="fa-solid fa-building-columns"></i></div>
                    <b>LAB GEDUNG P</b>
                </div>
                <p class="ft-about">
                    Menyediakan fasilitas laboratorium dan peralatan terkini untuk menunjang kegiatan praktikum dan
                    penelitian mahasiswa Universitas Kristen Petra.
                </p>
            </div>

            {{-- Quick links --}}
            <div class="ft-fade" style="--d:.2s">
                <h4 class="ft-h">Tautan</h4>
                <ul class="ft-list">
                    <li><a href="#about" class="ft-link">Tentang Kami</a></li>
                    <li><a href="#services" class="ft-link">Fasilitas &amp; Layanan</a></li>
                    <li><a href="{{ route('user.booking.form') }}" class="ft-link strong">Formulir Booking</a></li>
                </ul>
            </div>

            {{-- Contact info --}}
            <div class="ft-fade" style="--d:.3s">
                <h4 class="ft-h">Hubungi Kami</h4>
                <ul class="ft-list">
                    <li class="ft-contact">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Gedung P, Universitas Kristen Petra.<br>Siwalankerto, Surabaya.</span>
                    </li>
                    <li class="ft-contact" style="align-items:center;">
                        <i class="fa-solid fa-envelope" style="margin-top:0;"></i>
                        <span>lab.gedungp@petra.ac.id</span>
                    </li>
                </ul>
            </div>

            {{-- Operational hours --}}
            <div class="ft-fade" style="--d:.4s">
                <h4 class="ft-h">Jam Operasional</h4>
                <div class="ft-hours-wrap">
                    <div class="ft-hours"><b>Senin – Jumat</b><span>07.30 – 16.30 WIB</span></div>
                    <div class="ft-hours closed"><b>Sabtu – Minggu</b><span>Tutup</span></div>
                </div>
            </div>

        </div>
    </div>

    <div class="ft-mark" aria-hidden="true"><span>Gedung P</span></div>

    {{-- Bottom line --}}
    <div class="ft-bottom">
        <div class="ft-wrap">
            <p>&copy; {{ date('Y') }} Laboratorium Gedung P · UK Petra. All Rights Reserved.</p>
            <p><span class="o">◆</span>&nbsp; Sistem Manajemen Laboratorium Terpadu</p>
        </div>
    </div>
</footer>

<script>
    (function () {
        var f = document.getElementById('lxFooter');
        if (!f) return;
        if (!('IntersectionObserver' in window)) { f.classList.add('ft-in'); return; }
        var io = new IntersectionObserver(function (en) {
            if (en[0].isIntersecting) { f.classList.add('ft-in'); io.disconnect(); }
        }, { threshold: 0.12 });
        io.observe(f);
    })();
</script>