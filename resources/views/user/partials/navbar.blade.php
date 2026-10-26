{{-- NAVBAR — Editorial Luxury (#1A2D4A, #FBFAF5, #F57E21) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500&family=Open+Sans:wght@400;600;700;800&display=swap"
    rel="stylesheet">

<style>
    #floating-navbar {
        --nv-navy: #1A2D4A;
        --nv-cream: #FBFAF5;
        --nv-orange: #F57E21;
        --nv-line: rgba(26, 45, 74, 0.14);
        --nv-ease: cubic-bezier(0.22, 1, 0.36, 1);
        --nv-ease-io: cubic-bezier(0.76, 0, 0.24, 1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999;
        font-family: 'Open Sans', sans-serif;
        background: rgba(251, 250, 245, 0.86);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border-bottom: 1px solid transparent;
        transition: background .5s var(--nv-ease), border-color .5s var(--nv-ease);
    }

    #floating-navbar.scrolled {
        background: rgba(251, 250, 245, 0.96);
        border-bottom-color: var(--nv-line);
    }

    #floating-navbar *,
    #mobile-nav * {
        box-sizing: border-box;
    }

    .nv-inner {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 28px;
        height: 84px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: height .5s var(--nv-ease);
    }

    #floating-navbar.scrolled .nv-inner {
        height: 70px;
    }

    /* Progress garis bawah */
    .nv-rule {
        position: absolute;
        left: 0;
        bottom: -1px;
        height: 1px;
        width: 100%;
        background: var(--nv-orange);
        transform-origin: left center;
        transform: scaleX(0);
        transition: transform 1.2s var(--nv-ease-io);
    }

    #floating-navbar.scrolled .nv-rule {
        transform: scaleX(1);
        opacity: .0;
    }

    /* Logo */
    .nv-logo {
        display: flex;
        align-items: center;
        gap: 16px;
        text-decoration: none;
    }

    .nv-logo-box {
        position: relative;
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--nv-navy);
        color: var(--nv-orange);
        font-size: 17px;
        overflow: hidden;
        isolation: isolate;
        transition: color .5s var(--nv-ease), border-color .5s var(--nv-ease);
    }

    .nv-logo-box::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: var(--nv-navy);
        transition: transform .7s var(--nv-ease-io);
        transform-origin: bottom;
    }

    .nv-logo:hover .nv-logo-box::before {
        transform: translateY(100%);
    }

    .nv-logo:hover .nv-logo-box {
        color: var(--nv-navy);
    }

    .nv-logo-text b {
        display: block;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.28rem;
        font-weight: 600;
        line-height: 1;
        letter-spacing: .04em;
        color: var(--nv-navy);
    }

    .nv-logo-text small {
        display: block;
        margin-top: 5px;
        font-size: .58rem;
        font-weight: 700;
        letter-spacing: .3em;
        color: rgba(26, 45, 74, .55);
    }

    /* Links */
    .nv-links {
        display: flex;
        align-items: center;
        gap: 44px;
    }

    .nav-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--nv-navy);
        font-weight: 700;
        font-size: .7rem;
        letter-spacing: .26em;
        text-decoration: none;
        padding: 8px 0;
        transition: color .4s var(--nv-ease);
    }

    .nav-link span.n {
        font-family: 'Cormorant Garamond', serif;
        font-style: italic;
        font-size: .95rem;
        letter-spacing: 0;
        color: var(--nv-orange);
        font-weight: 600;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        height: 1px;
        width: 100%;
        background: var(--nv-orange);
        transform: scaleX(0);
        transform-origin: right;
        transition: transform .6s var(--nv-ease-io);
    }

    .nav-link:hover {
        color: var(--nv-orange);
    }

    .nav-link:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }

    /* Tombol */
    .nav-btn-primary {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        overflow: hidden;
        isolation: isolate;
        padding: 14px 26px;
        border: 1px solid var(--nv-navy);
        background: var(--nv-navy);
        color: var(--nv-cream);
        font-family: 'Open Sans', sans-serif;
        font-weight: 700;
        font-size: .68rem;
        letter-spacing: .24em;
        text-decoration: none;
        cursor: pointer;
        transition: color .5s var(--nv-ease), border-color .5s var(--nv-ease);
    }

    .nav-btn-primary::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: var(--nv-orange);
        transform: translateX(-101%);
        transition: transform .65s var(--nv-ease-io);
    }

    .nav-btn-primary:hover {
        color: var(--nv-cream);
        border-color: var(--nv-orange);
    }

    .nav-btn-primary:hover::before {
        transform: translateX(0);
    }

    .nav-btn-primary i {
        transition: transform .5s var(--nv-ease);
    }

    .nav-btn-primary:hover i.arrow {
        transform: translateX(5px);
    }

    /* Dropdown */
    .nav-dropdown {
        position: relative;
    }

    .nav-dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 16px);
        min-width: 260px;
        background: var(--nv-cream);
        border: 1px solid var(--nv-line);
        box-shadow: 0 30px 60px -28px rgba(26, 45, 74, .45);
        clip-path: inset(0 0 100% 0);
        visibility: hidden;
        transition: clip-path .7s var(--nv-ease-io), visibility 0s linear .7s;
        z-index: 100;
    }

    .nav-dropdown::before {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        top: 100%;
        height: 18px;
    }

    .nav-dropdown:hover .nav-dropdown-menu,
    .nav-dropdown:focus-within .nav-dropdown-menu {
        clip-path: inset(0 0 0 0);
        visibility: visible;
        transition: clip-path .7s var(--nv-ease-io), visibility 0s;
    }

    .nav-dropdown-header {
        padding: 18px 22px 10px;
        font-size: .6rem;
        font-weight: 700;
        letter-spacing: .3em;
        color: var(--nv-orange);
        text-transform: uppercase;
    }

    .nav-dropdown-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 22px;
        overflow: hidden;
        isolation: isolate;
        font-size: .8rem;
        font-weight: 600;
        color: var(--nv-navy);
        text-decoration: none;
        transition: color .4s var(--nv-ease), padding .5s var(--nv-ease);
    }

    .nav-dropdown-item::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: var(--nv-navy);
        transform: translateX(-101%);
        transition: transform .55s var(--nv-ease-io);
    }

    .nav-dropdown-item:hover {
        color: var(--nv-cream);
        padding-left: 28px;
    }

    .nav-dropdown-item:hover::before {
        transform: translateX(0);
    }

    .nav-dropdown-item i {
        width: 16px;
        text-align: center;
        color: var(--nv-orange);
        flex-shrink: 0;
        font-size: .82rem;
    }

    .nav-dropdown-divider {
        height: 1px;
        background: var(--nv-line);
        margin: 6px 0;
    }

    .nav-dropdown-item.danger {
        color: #b3362a;
    }

    .nav-dropdown-item.danger i {
        color: #b3362a;
    }

    .nav-dropdown-item.danger::before {
        background: #b3362a;
    }

    .nav-dropdown-item.danger:hover {
        color: var(--nv-cream);
    }

    .nav-dropdown-item.danger:hover i {
        color: var(--nv-cream);
    }

    /* Hamburger */
    #mobile-menu-toggle {
        display: none;
        position: relative;
        width: 46px;
        height: 46px;
        background: transparent;
        border: 1px solid var(--nv-navy);
        cursor: pointer;
        padding: 0;
        transition: background .4s var(--nv-ease), border-color .4s var(--nv-ease);
    }

    #mobile-menu-toggle i {
        display: none;
    }

    #mobile-menu-toggle .bar {
        position: absolute;
        left: 12px;
        right: 12px;
        height: 1.5px;
        background: var(--nv-navy);
        transition: transform .6s var(--nv-ease-io), top .6s var(--nv-ease-io), background .4s var(--nv-ease), right .5s var(--nv-ease);
    }

    #mobile-menu-toggle .bar:nth-child(1) {
        top: 16px;
    }

    #mobile-menu-toggle .bar:nth-child(2) {
        top: 22px;
        right: 20px;
    }

    #mobile-menu-toggle .bar:nth-child(3) {
        top: 28px;
    }

    #mobile-menu-toggle[aria-expanded="true"] .bar:nth-child(1) {
        top: 22px;
        transform: rotate(45deg);
    }

    #mobile-menu-toggle[aria-expanded="true"] .bar:nth-child(2) {
        right: 12px;
        transform: scaleX(0);
    }

    #mobile-menu-toggle[aria-expanded="true"] .bar:nth-child(3) {
        top: 22px;
        transform: rotate(-45deg);
    }

    /* Mode menu terbuka */
    #floating-navbar.menu-open {
        background: transparent;
        border-bottom-color: rgba(251, 250, 245, .14);
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
    }

    #floating-navbar.menu-open .nv-logo-text b {
        color: var(--nv-cream);
    }

    #floating-navbar.menu-open .nv-logo-text small {
        color: rgba(251, 250, 245, .6);
    }

    #floating-navbar.menu-open .nv-logo-box {
        border-color: rgba(251, 250, 245, .5);
    }

    #floating-navbar.menu-open #mobile-menu-toggle {
        border-color: rgba(251, 250, 245, .5);
    }

    #floating-navbar.menu-open #mobile-menu-toggle .bar {
        background: var(--nv-cream);
    }

    /* Mobile overlay */
    #mobile-nav {
        position: fixed;
        inset: 0;
        z-index: 998;
        background: #1A2D4A;
        color: #FBFAF5;
        font-family: 'Open Sans', sans-serif;
        padding: 112px 28px 40px;
        overflow-y: auto;
        clip-path: inset(0 0 100% 0);
        visibility: hidden;
        transition: clip-path .9s cubic-bezier(0.76, 0, 0.24, 1), visibility 0s linear .9s;
        display: flex;
        flex-direction: column;
    }

    #mobile-nav::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: .5;
        background-image: linear-gradient(rgba(251, 250, 245, .07) 1px, transparent 1px), linear-gradient(90deg, rgba(251, 250, 245, .07) 1px, transparent 1px);
        background-size: 72px 72px;
        -webkit-mask-image: radial-gradient(ellipse at 80% 10%, #000, transparent 70%);
        mask-image: radial-gradient(ellipse at 80% 10%, #000, transparent 70%);
    }

    #mobile-nav.open {
        clip-path: inset(0 0 0 0);
        visibility: visible;
        transition: clip-path .9s cubic-bezier(0.76, 0, 0.24, 1), visibility 0s;
    }

    #mobile-nav>* {
        position: relative;
    }

    .mobile-nav-link {
        display: flex;
        align-items: baseline;
        gap: 16px;
        padding: 14px 0;
        font-family: 'Cormorant Garamond', serif;
        font-size: 2.3rem;
        font-weight: 500;
        line-height: 1.1;
        color: #FBFAF5;
        text-decoration: none;
        border-bottom: 1px solid rgba(251, 250, 245, .14);
        opacity: 0;
        transform: translateY(24px);
        transition: opacity .7s cubic-bezier(0.22, 1, 0.36, 1), transform .7s cubic-bezier(0.22, 1, 0.36, 1), color .4s;
    }

    #mobile-nav.open .mobile-nav-link {
        opacity: 1;
        transform: translateY(0);
        transition-delay: calc(.35s + var(--i, 0) * .07s);
    }

    .mobile-nav-link:hover {
        color: #F57E21;
    }

    .mobile-nav-link i {
        color: #F57E21;
        font-size: .9rem;
        width: 18px;
        text-align: center;
        flex-shrink: 0;
    }

    .mobile-nav-link.sm {
        font-family: 'Open Sans', sans-serif;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        padding: 16px 0;
        align-items: center;
    }

    .mobile-nav-link.danger,
    .mobile-nav-link.danger i {
        color: #ff8f84;
    }

    .mobile-nav-label {
        margin: 26px 0 4px;
        font-size: .62rem;
        font-weight: 700;
        letter-spacing: .3em;
        text-transform: uppercase;
        color: #F57E21;
        opacity: 0;
        transition: opacity .7s .4s;
    }

    #mobile-nav.open .mobile-nav-label {
        opacity: 1;
    }

    .mobile-nav-login {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        margin-top: 34px;
        padding: 20px;
        background: #F57E21;
        color: #FBFAF5;
        text-decoration: none;
        font-weight: 700;
        font-size: .74rem;
        letter-spacing: .24em;
        opacity: 0;
        transition: opacity .7s cubic-bezier(0.22, 1, 0.36, 1);
    }

    #mobile-nav.open .mobile-nav-login {
        opacity: 1;
        transition-delay: .6s;
    }

    @media (max-width: 767px) {
        .nv-links {
            display: none;
        }

        #mobile-menu-toggle {
            display: block;
        }

        .nv-inner {
            padding: 0 20px;
            height: 74px;
        }

        #floating-navbar.scrolled .nv-inner {
            height: 66px;
        }

        .nv-logo-text small {
            letter-spacing: .2em;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        #floating-navbar *,
        #mobile-nav,
        #mobile-nav * {
            transition-duration: .01ms !important;
            transition-delay: 0s !important;
        }
    }
</style>

<nav id="floating-navbar" role="navigation" aria-label="Navigasi Utama">
    <div class="nv-inner">
        {{-- Logo --}}
        <a href="{{ route('user.landing') }}" class="nv-logo" aria-label="Lab Gedung P - Beranda">
            <div class="nv-logo-text">
                <b>Laboratorium</b>
                <small>Informatics PCU</small>
            </div>
        </a>

        {{-- Desktop Nav --}}
        <div class="nv-links">
            <a href="{{ route('user.landing') }}#about" class="nav-link"><span class="n">i.</span> TENTANG</a>
            <a href="{{ route('user.landing') }}#services" class="nav-link"><span class="n">ii.</span> LAYANAN</a>

            @guest
                <a href="{{ route('user.login') }}" class="nav-btn-primary" id="nav-login-btn">
                    LOGIN
                    <i class="fa-solid fa-arrow-right-long arrow" style="font-size: 12px;"></i>
                </a>
            @endguest

            @auth
                <div class="nav-dropdown">
                    <button class="nav-btn-primary" aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-user-circle" style="font-size: 13px;"></i>
                        {{ strtoupper(explode(' ', Auth::user()->name)[0]) }}
                        <i class="fa-solid fa-chevron-down" style="font-size: 9px; opacity: .7;"></i>
                    </button>
                    <div class="nav-dropdown-menu" role="menu">
                        <div class="nav-dropdown-header">Menu Mahasiswa</div>
                        <a href="{{ route('user.booking.form') }}" class="nav-dropdown-item" role="menuitem">
                            <i class="fa-regular fa-calendar-plus"></i> Buat Peminjaman
                        </a>
                        <a href="{{ route('user.booking.history') }}" class="nav-dropdown-item" role="menuitem">
                            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Peminjaman
                        </a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="{{ route('user.helpdesk.index') }}" class="nav-dropdown-item" role="menuitem">
                            <i class="fa-solid fa-headset"></i> Pusat Bantuan
                        </a>
                        <a href="{{ route('user.helpdesk.history') }}" class="nav-dropdown-item" role="menuitem">
                            <i class="fa-solid fa-clipboard-list"></i> Riwayat Tiket
                        </a>
                        <div class="nav-dropdown-divider"></div>
                        <a href="{{ route('user.logout') }}" class="nav-dropdown-item danger" role="menuitem">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        {{-- Mobile hamburger --}}
        <button id="mobile-menu-toggle" aria-label="Buka menu" aria-expanded="false" aria-controls="mobile-nav">
            <span class="bar"></span><span class="bar"></span><span class="bar"></span>
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</nav>

{{-- Mobile Nav Panel --}}
<div id="mobile-nav" role="dialog" aria-label="Menu Mobile">
    <a href="{{ route('user.landing') }}#about" class="mobile-nav-link" style="--i:0">
        <i class="fa-solid fa-circle-info"></i> Tentang
    </a>
    <a href="{{ route('user.landing') }}#services" class="mobile-nav-link" style="--i:1">
        <i class="fa-solid fa-boxes-stacked"></i> Layanan
    </a>

    @guest
        <a href="{{ route('user.login') }}" class="mobile-nav-login">
            <i class="fa-solid fa-right-to-bracket"></i> LOGIN SEKARANG
        </a>
    @endguest

    @auth
        <div class="mobile-nav-label">Halo, {{ Auth::user()->name }}</div>
        <a href="{{ route('user.booking.form') }}" class="mobile-nav-link sm" style="--i:2">
            <i class="fa-regular fa-calendar-plus"></i> Buat Peminjaman
        </a>
        <a href="{{ route('user.booking.history') }}" class="mobile-nav-link sm" style="--i:3">
            <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Peminjaman
        </a>
        <a href="{{ route('user.helpdesk.index') }}" class="mobile-nav-link sm" style="--i:4">
            <i class="fa-solid fa-headset"></i> Pusat Bantuan
        </a>
        <a href="{{ route('user.helpdesk.history') }}" class="mobile-nav-link sm" style="--i:5">
            <i class="fa-solid fa-clipboard-list"></i> Riwayat Tiket
        </a>
        <a href="{{ route('user.logout') }}" class="mobile-nav-link sm danger" style="--i:6">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
    @endauth
</div>

<script>
    (function () {
        const navbar = document.getElementById('floating-navbar');
        const toggle = document.getElementById('mobile-menu-toggle');
        const mobileNav = document.getElementById('mobile-nav');

        function onScroll() {
            navbar.classList.toggle('scrolled', window.scrollY > 40);
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        function setMenu(open) {
            mobileNav.classList.toggle('open', open);
            navbar.classList.toggle('menu-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
            if (window.lenis) { open ? window.lenis.stop() : window.lenis.start(); }
            document.documentElement.style.overflow = open ? 'hidden' : '';
        }

        toggle.addEventListener('click', function () {
            setMenu(!mobileNav.classList.contains('open'));
        });

        // Tutup menu saat link diklik
        mobileNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { setMenu(false); });
        });

        // Tutup dengan Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mobileNav.classList.contains('open')) setMenu(false);
        });
    })();
</script>