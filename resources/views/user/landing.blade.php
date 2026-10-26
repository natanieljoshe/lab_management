@extends('layouts.user')

@section('title', 'Selamat Datang di Laboratorium Gedung P')

@section('body')

    @include('user.partials.navbar')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Open+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --clr-bg: #FBFAF5;
            --clr-navy: #1A2D4A;
            --clr-orange: #F57E21;
            --clr-text: #1A2D4A;
            --clr-muted: rgba(26, 45, 74, 0.65);
            --line: rgba(26, 45, 74, 0.16);
            --line-light: rgba(251, 250, 245, 0.18);
            --serif: 'Cormorant Garamond', 'Times New Roman', serif;
            --sans: 'Open Sans', sans-serif;
            --ease: cubic-bezier(0.22, 1, 0.36, 1);
            --ease-io: cubic-bezier(0.76, 0, 0.24, 1);
        }

        html {
            scroll-behavior: auto !important;
        }

        html.lenis,
        html.lenis body {
            height: auto;
        }

        .lenis.lenis-smooth {
            scroll-behavior: auto !important;
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #FBFAF5;
        }

        ::-webkit-scrollbar-thumb {
            background: #1A2D4A;
            border: 2px solid #FBFAF5;
            border-radius: 8px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #F57E21;
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #1A2D4A #FBFAF5;
        }

        /* ============================================================
       BASE
       ============================================================ */
        .lx-page {
            background: var(--clr-bg);
            color: var(--clr-text);
            font-family: var(--sans);
            overflow-x: hidden;
            position: relative;
        }

        .lx-page *,
        .lx-page *::before,
        .lx-page *::after {
            box-sizing: border-box;
        }

        .lx-page a {
            text-decoration: none;
        }

        .lx-wrap {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 28px;
        }

        /* Grain halus di seluruh halaman */
        .lx-grain {
            position: fixed;
            inset: 0;
            z-index: 60;
            pointer-events: none;
            opacity: .055;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='180' height='180'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='2' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 .1 0 0 0 0 .18 0 0 0 0 .29 0 0 0 .9 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
        }

        /* Progress bar scroll */
        .lx-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 100%;
            z-index: 1001;
            background: linear-gradient(90deg, var(--clr-orange), #ffb066);
            transform-origin: left center;
            transform: scaleX(0);
        }

        /* ============================================================
       TYPE
       ============================================================ */
        .lx-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .32em;
            text-transform: uppercase;
            color: var(--clr-orange);
        }

        .lx-eyebrow::before {
            content: '';
            width: 46px;
            height: 1px;
            background: currentColor;
            display: block;
        }

        .lx-display {
            font-family: var(--serif);
            font-weight: 500;
            line-height: .98;
            letter-spacing: -.015em;
            color: var(--clr-navy);
        }

        .lx-display em {
            font-style: italic;
            font-weight: 500;
            color: var(--clr-orange);
        }

        .lx-lead {
            font-size: 1rem;
            line-height: 1.85;
            color: var(--clr-muted);
        }

        /* ============================================================
       REVEAL PRIMITIVES (tanpa floating / scale)
       ============================================================ */
        /* Teks naik dari balik mask */
        .lx-mask {
            display: block;
            overflow: hidden;
            padding-bottom: .12em;
            margin-bottom: -.12em;
        }

        .lx-mask>span {
            display: block;
            transform: translateY(112%);
            transition: transform 1.1s var(--ease);
            transition-delay: var(--d, 0s);
        }

        .is-in .lx-mask>span,
        .lx-mask.is-in>span {
            transform: translateY(0);
        }

        /* Fade + blur lembut */
        .lx-fade {
            opacity: 0;
            filter: blur(8px);
            transition: opacity 1s var(--ease), filter 1s var(--ease);
            transition-delay: var(--d, 0s);
        }

        .lx-fade.is-in {
            opacity: 1;
            filter: blur(0);
        }

        /* Gambar terbuka seperti tirai */
        .lx-wipe {
            clip-path: inset(100% 0 0 0);
            transition: clip-path 1.5s var(--ease-io);
            transition-delay: var(--d, 0s);
        }

        .lx-wipe.is-in {
            clip-path: inset(0 0 0 0);
        }

        .lx-wipe-x {
            clip-path: inset(0 100% 0 0);
            transition: clip-path 1.4s var(--ease-io);
            transition-delay: var(--d, 0s);
        }

        .lx-wipe-x.is-in {
            clip-path: inset(0 0 0 0);
        }

        /* Garis tergambar */
        .lx-line {
            display: block;
            height: 1px;
            background: var(--line);
            width: 0;
            transition: width 1.4s var(--ease);
            transition-delay: var(--d, 0s);
        }

        .lx-line.is-in {
            width: 100%;
        }

        .lx-line.on-dark {
            background: var(--line-light);
        }

        /* ============================================================
       BUTTONS — fill sweep, tanpa scale/float
       ============================================================ */
        .lx-btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 16px;
            overflow: hidden;
            padding: 18px 34px;
            border: 1px solid var(--clr-navy);
            font-family: var(--sans);
            font-weight: 700;
            font-size: .74rem;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--clr-bg);
            background: var(--clr-navy);
            cursor: pointer;
            isolation: isolate;
            transition: color .5s var(--ease), border-color .5s var(--ease);
        }

        .lx-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: var(--clr-orange);
            transform: translateX(-101%);
            transition: transform .65s var(--ease-io);
        }

        .lx-btn:hover {
            color: var(--clr-bg);
            border-color: var(--clr-orange);
        }

        .lx-btn:hover::before {
            transform: translateX(0);
        }

        .lx-btn i {
            transition: transform .5s var(--ease);
        }

        .lx-btn:hover i {
            transform: translateX(6px);
        }

        .lx-btn.ghost {
            background: transparent;
            color: var(--clr-navy);
        }

        .lx-btn.ghost::before {
            background: var(--clr-navy);
        }

        .lx-btn.ghost:hover {
            color: var(--clr-bg);
            border-color: var(--clr-navy);
        }

        .lx-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: .74rem;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--clr-navy);
            padding-bottom: 8px;
        }

        .lx-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 1px;
            width: 100%;
            background: var(--clr-orange);
            transform-origin: right;
            transition: transform .6s var(--ease-io);
        }

        .lx-link:hover {
            color: var(--clr-orange);
        }

        .lx-link:hover::after {
            transform-origin: left;
        }

        .lx-link i {
            transition: transform .5s var(--ease);
        }

        .lx-link:hover i {
            transform: translateX(6px);
        }

        /* ============================================================
       HERO
       ============================================================ */
        .lx-hero {
            position: relative;
            min-height: 100vh;
            padding: 150px 0 80px;
            display: flex;
            align-items: center;
            background: var(--clr-bg);
            overflow: hidden;
        }

        /* Cahaya mengikuti kursor */
        .lx-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(520px circle at var(--mx, 70%) var(--my, 30%), rgba(245, 126, 33, .11), transparent 60%);
            transition: background .2s;
        }

        /* Garis grid vertikal editorial */
        .lx-hero-grid {
            position: absolute;
            inset: 0;
            pointer-events: none;
            display: flex;
            justify-content: space-between;
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 28px;
        }

        .lx-hero-grid span {
            width: 1px;
            height: 100%;
            background: var(--line);
            opacity: .55;
        }

        .lx-hero-inner {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.18fr .82fr;
            gap: 72px;
            align-items: center;
        }

        .lx-hero-title {
            font-size: clamp(2.9rem, 7.2vw, 6.6rem);
            margin: 26px 0 34px;
        }

        .lx-hero-copy {
            max-width: 470px;
            margin-bottom: 42px;
        }

        .lx-hero-cta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 64px;
        }

        .lx-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            max-width: 560px;
        }

        .lx-stat {
            padding: 22px 0 0;
            position: relative;
        }

        .lx-stat+.lx-stat {
            padding-left: 26px;
            border-left: 1px solid var(--line);
        }

        .lx-stat-top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
        }

        .lx-stat-num {
            font-family: var(--serif);
            font-size: 3.1rem;
            font-weight: 500;
            line-height: 1;
            color: var(--clr-navy);
        }

        .lx-stat-num sup {
            font-size: 1.4rem;
            color: var(--clr-orange);
            vertical-align: top;
            margin-left: 2px;
        }

        .lx-stat-label {
            margin-top: 10px;
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .24em;
            text-transform: uppercase;
            color: var(--clr-muted);
        }

        /* Arch visual */
        .lx-arch-wrap {
            position: relative;
            padding: 0 0 0 26px;
        }

        .lx-arch-frame {
            position: absolute;
            top: 28px;
            left: 0;
            right: 28px;
            bottom: -28px;
            z-index: 0;
            border: 1px solid var(--clr-orange);
            border-radius: 999px 999px 0 0;
        }

        .lx-arch {
            position: relative;
            z-index: 1;
            aspect-ratio: 3 / 4;
            border-radius: 999px 999px 0 0;
            overflow: hidden;
            background: var(--clr-navy);
        }

        .lx-arch img {
            position: absolute;
            left: 0;
            top: -8%;
            width: 100%;
            height: 116%;
            object-fit: cover;
            will-change: transform;
            filter: saturate(.85) contrast(1.02);
        }

        .lx-arch::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(26, 45, 74, .05) 30%, rgba(26, 45, 74, .62) 100%);
        }

        .lx-badge {
            position: absolute;
            z-index: 3;
            left: 0;
            bottom: 56px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 22px 16px 18px;
            background: var(--clr-bg);
            border: 1px solid var(--line);
            box-shadow: 0 24px 50px -24px rgba(26, 45, 74, .45);
        }

        .lx-badge-dot {
            position: relative;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--clr-orange);
        }

        .lx-badge-dot::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: var(--clr-orange);
            animation: lx-ping 2.2s var(--ease) infinite;
        }

        @keyframes lx-ping {
            0% {
                opacity: .6;
                box-shadow: 0 0 0 0 rgba(245, 126, 33, .55);
            }

            100% {
                opacity: 0;
                box-shadow: 0 0 0 14px rgba(245, 126, 33, 0);
            }
        }

        .lx-badge b {
            display: block;
            font-size: .8rem;
            font-weight: 800;
            color: var(--clr-navy);
        }

        .lx-badge small {
            font-size: .7rem;
            color: var(--clr-muted);
        }

        .lx-caption {
            position: absolute;
            z-index: 3;
            right: -6px;
            top: 48%;
            transform-origin: right center;
            writing-mode: vertical-rl;
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .4em;
            text-transform: uppercase;
            color: var(--clr-muted);
        }

        /* Scroll cue */
        .lx-scroll {
            position: absolute;
            z-index: 3;
            left: 28px;
            bottom: 28px;
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: .62rem;
            font-weight: 700;
            letter-spacing: .32em;
            text-transform: uppercase;
            color: var(--clr-muted);
        }

        .lx-scroll i {
            display: block;
            width: 1px;
            height: 54px;
            background: linear-gradient(var(--clr-orange) 50%, var(--line) 50%);
            background-size: 100% 200%;
            animation: lx-scrollcue 2.4s var(--ease-io) infinite;
        }

        @keyframes lx-scrollcue {
            0% {
                background-position: 0 100%;
            }

            100% {
                background-position: 0 0;
            }
        }

        /* ============================================================
       MARQUEE
       ============================================================ */
        .lx-marquee {
            background: var(--clr-navy);
            color: var(--clr-bg);
            overflow: hidden;
            border-block: 1px solid var(--clr-navy);
        }

        .lx-marquee-track {
            display: flex;
            width: max-content;
            animation: lx-marquee 38s linear infinite;
        }

        .lx-marquee:hover .lx-marquee-track {
            animation-play-state: paused;
        }

        .lx-marquee-item {
            display: flex;
            align-items: center;
            gap: 38px;
            padding: 26px 38px 26px 0;
            white-space: nowrap;
            font-family: var(--serif);
            font-size: 2rem;
            font-style: italic;
            font-weight: 500;
        }

        .lx-marquee-item i {
            color: var(--clr-orange);
            font-size: .9rem;
        }

        .lx-marquee-item span.o {
            font-style: normal;
            font-family: var(--sans);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .3em;
            color: rgba(251, 250, 245, .55);
        }

        @keyframes lx-marquee {
            to {
                transform: translateX(-50%);
            }
        }

        /* ============================================================
       ABOUT
       ============================================================ */
        .lx-about {
            padding: 140px 0;
            position: relative;
        }

        .lx-about-grid {
            display: grid;
            grid-template-columns: .95fr 1.05fr;
            gap: 90px;
            align-items: start;
        }

        .lx-about-media {
            position: relative;
        }

        .lx-about-img {
            aspect-ratio: 4 / 5;
            overflow: hidden;
            background: var(--clr-navy);
        }

        .lx-about-img img {
            width: 100%;
            height: 115%;
            object-fit: cover;
            will-change: transform;
            filter: saturate(.85);
        }

        .lx-about-tag {
            position: absolute;
            right: -36px;
            bottom: 56px;
            z-index: 2;
            background: var(--clr-navy);
            color: var(--clr-bg);
            padding: 26px 30px;
            max-width: 230px;
        }

        .lx-about-tag b {
            display: block;
            font-family: var(--serif);
            font-size: 2.6rem;
            line-height: 1;
            font-weight: 500;
            color: var(--clr-orange);
        }

        .lx-about-tag span {
            display: block;
            margin-top: 10px;
            font-size: .68rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            line-height: 1.7;
            color: rgba(251, 250, 245, .75);
        }

        .lx-about-numeral {
            position: absolute;
            left: -18px;
            top: -62px;
            z-index: 0;
            pointer-events: none;
            font-family: var(--serif);
            font-size: 12rem;
            line-height: 1;
            font-weight: 500;
            color: transparent;
            -webkit-text-stroke: 1px var(--line);
        }

        .lx-about-title {
            font-size: clamp(2.4rem, 4.6vw, 4rem);
            margin: 22px 0 30px;
        }

        .lx-feats {
            margin-top: 48px;
        }

        .lx-feat {
            position: relative;
            display: grid;
            grid-template-columns: 56px 1fr 28px;
            gap: 22px;
            align-items: center;
            padding: 30px 18px 30px 0;
            overflow: hidden;
            isolation: isolate;
            transition: padding .6s var(--ease), color .5s var(--ease);
        }

        .lx-feat::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: var(--clr-navy);
            transform: translateY(101%);
            transition: transform .7s var(--ease-io);
        }

        .lx-feat:hover::before {
            transform: translateY(0);
        }

        .lx-feat:hover {
            padding-left: 22px;
            padding-right: 22px;
        }

        .lx-feat-no {
            font-family: var(--serif);
            font-size: 1.5rem;
            font-style: italic;
            color: var(--clr-orange);
        }

        .lx-feat h3 {
            font-family: var(--serif);
            font-size: 1.65rem;
            font-weight: 600;
            line-height: 1.1;
            margin: 0 0 8px;
            color: var(--clr-navy);
            transition: color .5s var(--ease);
        }

        .lx-feat p {
            margin: 0;
            font-size: .84rem;
            line-height: 1.75;
            color: var(--clr-muted);
            transition: color .5s var(--ease);
        }

        .lx-feat>i {
            color: var(--clr-navy);
            font-size: 1.05rem;
            transition: color .5s var(--ease), transform .5s var(--ease);
        }

        .lx-feat:hover h3 {
            color: var(--clr-bg);
        }

        .lx-feat:hover p {
            color: rgba(251, 250, 245, .72);
        }

        .lx-feat:hover>i {
            color: var(--clr-orange);
            transform: translateX(6px);
        }

        /* ============================================================
       SERVICES — Panel akordeon horizontal (gelap)
       ============================================================ */
        .lx-services {
            position: relative;
            background: var(--clr-navy);
            color: var(--clr-bg);
            padding: 140px 0;
            overflow: hidden;
        }

        .lx-services::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: .5;
            background-image: linear-gradient(var(--line-light) 1px, transparent 1px), linear-gradient(90deg, var(--line-light) 1px, transparent 1px);
            background-size: 96px 96px;
            -webkit-mask-image: radial-gradient(ellipse at 50% 40%, #000 10%, transparent 72%);
            mask-image: radial-gradient(ellipse at 50% 40%, #000 10%, transparent 72%);
        }

        .lx-services .lx-display {
            color: var(--clr-bg);
        }

        .lx-services .lx-lead {
            color: rgba(251, 250, 245, .68);
        }

        .lx-services-head {
            position: relative;
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 60px;
            align-items: end;
            margin-bottom: 70px;
        }

        .lx-services-title {
            font-size: clamp(2.4rem, 5vw, 4.4rem);
            margin-top: 22px;
        }

        .lx-panels {
            position: relative;
            display: flex;
            gap: 0;
            height: 600px;
            border-top: 1px solid var(--line-light);
            border-bottom: 1px solid var(--line-light);
        }

        .lx-panel {
            position: relative;
            flex: 1 1 0;
            min-width: 0;
            overflow: hidden;
            padding: 44px 36px;
            cursor: pointer;
            border-left: 1px solid var(--line-light);
            isolation: isolate;
            opacity: 0;
            transform: translateY(32px);
            filter: blur(8px);
            /* Transisi fade in saat load punya delay, tapi flex-grow saat hover instan tanpa delay */
            transition: opacity 1.1s var(--ease) var(--d, 0s),
                transform 1.2s var(--ease) var(--d, 0s),
                filter 1.1s var(--ease) var(--d, 0s),
                flex-grow .45s var(--ease);
        }

        .lx-panel:last-child {
            border-right: 1px solid var(--line-light);
        }

        /* Animasi Fade In Halus saat masuk viewport */
        .lx-panel.is-in,
        .lx-panels.is-in .lx-panel {
            opacity: 1;
            transform: translateY(0);
            filter: blur(0);
        }

        .lx-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: var(--clr-bg);
            clip-path: inset(100% 0 0 0);
            transition: clip-path .45s var(--ease-io);
        }

        .lx-panel.is-active {
            flex-grow: 2.7;
            cursor: default;
        }

        .lx-panel.is-active.is-in::before,
        .lx-panels.is-in .lx-panel.is-active::before {
            clip-path: inset(0 0 0 0);
        }

        /* Transisi halus elemen internal panel */
        .lx-panel .lx-panel-body,
        .lx-panel .lx-panel-no,
        .lx-panel .lx-panel-icon {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity 1s var(--ease) calc(var(--d, 0s) + .18s),
                transform 1s var(--ease) calc(var(--d, 0s) + .18s);
        }

        .lx-panel.is-in .lx-panel-body,
        .lx-panel.is-in .lx-panel-no,
        .lx-panel.is-in .lx-panel-icon,
        .lx-panels.is-in .lx-panel .lx-panel-body,
        .lx-panels.is-in .lx-panel .lx-panel-no,
        .lx-panels.is-in .lx-panel .lx-panel-icon {
            opacity: 1;
            transform: translateY(0);
        }

        .lx-panel-no {
            font-family: var(--serif);
            font-size: 1.4rem;
            font-style: italic;
            color: var(--clr-orange);
            letter-spacing: .04em;
        }

        .lx-panel-icon {
            position: absolute;
            right: 34px;
            top: 40px;
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line-light);
            color: var(--clr-bg);
            font-size: 1.15rem;
            transition: background .35s var(--ease), color .35s var(--ease), border-color .35s var(--ease), opacity 1s var(--ease), transform 1s var(--ease);
        }

        .lx-panel.is-active .lx-panel-icon {
            background: var(--clr-navy);
            border-color: var(--clr-navy);
            color: var(--clr-orange);
        }

        .lx-panel-body {
            position: absolute;
            left: 36px;
            right: 36px;
            bottom: 44px;
        }

        .lx-panel h3 {
            font-family: var(--serif);
            font-weight: 500;
            font-size: 2.3rem;
            line-height: 1;
            margin: 0;
            color: var(--clr-bg);
            transition: color .35s var(--ease);
        }

        .lx-panel.is-active h3 {
            color: var(--clr-navy);
        }

        .lx-panel-more {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height .45s var(--ease-io), opacity .25s var(--ease);
        }

        .lx-panel.is-active .lx-panel-more {
            max-height: 380px;
            opacity: 1;
            transition: max-height .45s var(--ease-io), opacity .35s var(--ease) .08s;
        }

        .lx-panel-more p {
            margin: 22px 0 18px;
            font-size: .88rem;
            line-height: 1.8;
            color: var(--clr-muted);
            max-width: 420px;
        }

        .lx-checks {
            list-style: none;
            margin: 0 0 28px;
            padding: 0;
        }

        .lx-checks li {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 11px 0;
            border-top: 1px solid var(--line);
            font-size: .82rem;
            font-weight: 600;
            color: var(--clr-navy);
        }

        .lx-checks li:last-child {
            border-bottom: 1px solid var(--line);
        }

        .lx-checks i {
            color: var(--clr-orange);
            font-size: .7rem;
        }

        .lx-panel .lx-btn {
            white-space: nowrap;
        }

        /* ============================================================
       PROSES — 3 langkah
       ============================================================ */
        .lx-steps {
            padding: 140px 0 40px;
        }

        .lx-steps-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 40px;
            flex-wrap: wrap;
            margin-bottom: 64px;
        }

        .lx-steps-title {
            font-size: clamp(2.2rem, 4.2vw, 3.6rem);
            margin-top: 22px;
        }

        .lx-steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .lx-step {
            position: relative;
            padding: 38px 36px 40px 0;
        }

        .lx-step+.lx-step {
            padding-left: 36px;
            border-left: 1px solid var(--line);
        }

        .lx-step-no {
            font-family: var(--serif);
            font-size: 5rem;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1px var(--clr-navy);
            font-weight: 500;
            transition: color .6s var(--ease), -webkit-text-stroke-color .6s var(--ease);
        }

        .lx-step:hover .lx-step-no {
            color: var(--clr-orange);
            -webkit-text-stroke-color: var(--clr-orange);
        }

        .lx-step h4 {
            font-family: var(--serif);
            font-size: 1.6rem;
            font-weight: 600;
            margin: 18px 0 10px;
            color: var(--clr-navy);
        }

        .lx-step p {
            margin: 0;
            font-size: .86rem;
            line-height: 1.8;
            color: var(--clr-muted);
            max-width: 300px;
        }

        /* ============================================================
       CTA
       ============================================================ */
        .lx-cta {
            position: relative;
            padding: 150px 0 160px;
            overflow: hidden;
        }

        .lx-cta::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(640px circle at var(--mx, 50%) var(--my, 50%), rgba(245, 126, 33, .12), transparent 62%);
        }

        .lx-cta-ghost {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            pointer-events: none;
            user-select: none;
            font-family: var(--serif);
            font-style: italic;
            font-weight: 500;
            font-size: clamp(8rem, 22vw, 20rem);
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 1px rgba(26, 45, 74, .1);
        }

        .lx-cta-inner {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 820px;
            margin: 0 auto;
        }

        .lx-cta-title {
            font-size: clamp(2.5rem, 5.6vw, 4.9rem);
            margin: 26px 0 30px;
        }

        .lx-cta-copy {
            max-width: 520px;
            margin: 0 auto 48px;
        }

        .lx-cta-ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 6px;
            color: var(--clr-orange);
            font-size: .75rem;
        }

        .lx-cta-ornament::before,
        .lx-cta-ornament::after {
            content: '';
            width: 70px;
            height: 1px;
            background: var(--clr-orange);
            opacity: .6;
        }

        /* ============================================================
       RESPONSIVE
       ============================================================ */
        @media (max-width: 1023px) {
            .lx-hero-inner {
                grid-template-columns: 1fr;
                gap: 64px;
            }

            .lx-arch-wrap {
                max-width: 420px;
                margin: 0 auto;
                width: 100%;
            }

            .lx-hero-grid {
                display: none;
            }

            .lx-about-grid {
                grid-template-columns: 1fr;
                gap: 90px;
            }

            .lx-about-media {
                max-width: 520px;
            }

            .lx-about-tag {
                right: -10px;
            }

            .lx-services-head {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .lx-steps-grid {
                grid-template-columns: 1fr;
            }

            .lx-step,
            .lx-step+.lx-step {
                padding: 34px 0;
                border-left: 0;
                border-top: 1px solid var(--line);
            }
        }

        @media (max-width: 899px) {

            /* Panel jadi tumpuk vertikal, semua terbuka */
            .lx-panels {
                flex-direction: column;
                height: auto;
                border: 0;
            }

            .lx-panel,
            .lx-panel.is-active {
                flex: none;
                border: 1px solid var(--line-light);
                border-bottom: 0;
                padding: 34px 26px 30px;
                min-height: 0;
            }

            .lx-panel:last-child {
                border-bottom: 1px solid var(--line-light);
            }

            .lx-panel::before {
                clip-path: inset(0 0 0 0);
            }

            .lx-panel-body {
                position: static;
                margin-top: 70px;
            }

            .lx-panel h3 {
                color: var(--clr-navy);
            }

            .lx-panel .lx-panel-icon {
                background: var(--clr-navy);
                border-color: var(--clr-navy);
                color: var(--clr-orange);
                right: 26px;
                top: 28px;
            }

            .lx-panel-more {
                max-height: none;
                opacity: 1;
            }
        }

        @media (max-width: 639px) {
            .lx-wrap {
                padding: 0 20px;
            }

            .lx-hero {
                padding: 120px 0 90px;
            }

            .lx-stat-num {
                font-size: 2.3rem;
            }

            .lx-stat+.lx-stat {
                padding-left: 16px;
            }

            .lx-about {
                padding: 100px 0;
            }

            .lx-about-numeral {
                font-size: 8rem;
                top: -40px;
            }

            .lx-about-tag {
                position: relative;
                right: auto;
                bottom: auto;
                max-width: none;
                margin-top: -1px;
            }

            .lx-feat {
                grid-template-columns: 40px 1fr;
            }

            .lx-feat>i {
                display: none;
            }

            .lx-services {
                padding: 100px 0;
            }

            .lx-steps {
                padding-top: 100px;
            }

            .lx-cta {
                padding: 110px 0 120px;
            }

            .lx-marquee-item {
                font-size: 1.5rem;
            }

            .lx-caption,
            .lx-scroll {
                display: none;
            }

            .lx-btn {
                padding: 17px 26px;
            }
        }

        /* Hormati reduced-motion */
        @media (prefers-reduced-motion: reduce) {

            .lx-mask>span,
            .lx-fade,
            .lx-wipe,
            .lx-wipe-x,
            .lx-line,
            .lx-panel,
            .lx-panel * {
                transition: none !important;
                transform: none !important;
                opacity: 1 !important;
                filter: none !important;
                clip-path: none !important;
                width: 100% !important;
            }

            .lx-marquee-track,
            .lx-scroll i,
            .lx-badge-dot::after {
                animation: none !important;
            }
        }
    </style>

    <div class="lx-progress" id="lxProgress"></div>
    <div class="lx-grain" aria-hidden="true"></div>

    <main class="lx-page">

        {{-- ══════════════════════════════════════════
        HERO
        ══════════════════════════════════════════ --}}
        <section class="lx-hero" id="lxHero" aria-label="Hero">
            <div class="lx-hero-grid" aria-hidden="true"><span></span><span></span><span></span><span></span></div>

            <div class="lx-wrap" style="width:100%;">
                <div class="lx-hero-inner">

                    {{-- Kiri: Copy --}}
                    <div data-reveal-group>
                        <div class="lx-eyebrow lx-fade">Universitas Kristen Petra</div>

                        <h1 class="lx-display lx-hero-title">
                            <span class="lx-mask" style="--d:.05s"><span>Pusat Inovasi &amp;</span></span>
                            <span class="lx-mask" style="--d:.17s"><span>Praktikum</span></span>
                            <span class="lx-mask" style="--d:.29s"><span><em>Gedung P</em></span></span>
                        </h1>

                        <p class="lx-lead lx-hero-copy lx-fade" style="--d:.5s">
                            Fasilitas laboratorium terpadu Universitas Kristen Petra untuk mendukung pembelajaran,
                            penelitian, dan pengembangan kompetensi mahasiswa dalam lingkungan yang profesional.
                        </p>

                        <div class="lx-hero-cta lx-fade" style="--d:.65s">
                            <a href="{{ route('user.booking.form') }}" class="lx-btn" id="hero-cta-booking">
                                RESERVASI SEKARANG
                                <i class="fa-solid fa-arrow-right-long"></i>
                            </a>
                            <a href="#about" class="lx-btn ghost" id="hero-cta-pelajari">
                                Pelajari Fasilitas
                            </a>
                        </div>

                        <div class="lx-stats">
                            <div class="lx-stat">
                                <span class="lx-line lx-stat-top" style="--d:.8s"></span>
                                <div class="lx-stat-num"><span data-count="10">0</span><sup>+</sup></div>
                                <div class="lx-stat-label">Lab Aktif</div>
                            </div>
                            <div class="lx-stat">
                                <span class="lx-line lx-stat-top" style="--d:.95s"></span>
                                <div class="lx-stat-num"><span data-count="500">0</span><sup>+</sup></div>
                                <div class="lx-stat-label">Inventaris</div>
                            </div>
                            <div class="lx-stat">
                                <span class="lx-line lx-stat-top" style="--d:1.1s"></span>
                                <div class="lx-stat-num">24<span
                                        style="color:var(--clr-orange);font-style:italic;">/</span>5</div>
                                <div class="lx-stat-label">Layanan</div>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan: Arch visual --}}
                    <div class="lx-arch-wrap" data-reveal-group>
                        <div class="lx-arch-frame lx-wipe" style="--d:.5s"></div>
                        <div class="lx-arch lx-wipe" style="--d:.1s">
                            <img data-parallax="0.08"
                                src="https://images.unsplash.com/photo-1581092160562-40aa08e78837?q=80&w=1400&auto=format&fit=crop"
                                alt="Suasana Laboratorium Gedung P Universitas Kristen Petra" loading="eager">
                        </div>
                        <div class="lx-caption lx-fade" style="--d:1.3s">Gedung P — UK Petra</div>
                    </div>

                </div>
            </div>

        </section>

        {{-- ══════════════════════════════════════════
        MARQUEE
        ══════════════════════════════════════════ --}}
        <div class="lx-marquee" aria-hidden="true">
            <div class="lx-marquee-track">
                @for ($k = 0; $k < 2; $k++)
                    <div class="lx-marquee-item">
                        <span>Reservasi Laboratorium</span> <i class="fa-solid fa-diamond"></i>
                        <span class="o">PC SPESIFIKASI TINGGI</span> <i class="fa-solid fa-diamond"></i>
                        <span>Peminjaman Inventaris</span> <i class="fa-solid fa-diamond"></i>
                        <span class="o">MICROCONTROLLER &amp; SENSOR</span> <i class="fa-solid fa-diamond"></i>
                        <span>Helpdesk &amp; Saran</span> <i class="fa-solid fa-diamond"></i>
                        <span class="o">REAL-TIME BOOKING</span> <i class="fa-solid fa-diamond"></i>
                    </div>
                @endfor
            </div>
        </div>

        {{-- ══════════════════════════════════════════
        ABOUT
        ══════════════════════════════════════════ --}}
        <section id="about" class="lx-about" aria-label="Tentang Fasilitas">
            <div class="lx-wrap">
                <div class="lx-about-grid">

                    {{-- Media --}}
                    <div class="lx-about-media" data-reveal-group>
                        <div class="lx-about-numeral lx-fade" aria-hidden="true">01</div>
                        <div class="lx-about-img lx-wipe" style="position:relative;z-index:1;">
                            <img data-parallax="0.07"
                                src="https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?q=80&w=1200&auto=format&fit=crop"
                                alt="Aktivitas Praktikum Mahasiswa di Laboratorium Gedung P" loading="lazy">
                        </div>
                        <div class="lx-about-tag lx-wipe-x" style="--d:.7s">
                            <b>Gedung P</b>
                            <span>Standar akademik &amp; industri terkini</span>
                        </div>
                    </div>

                    {{-- Teks --}}
                    <div data-reveal-group style="padding-top:34px;">
                        <div class="lx-eyebrow lx-fade">Tentang Fasilitas</div>
                        <h2 class="lx-display lx-about-title">
                            <span class="lx-mask" style="--d:.05s"><span>Tentang</span></span>
                            <span class="lx-mask" style="--d:.17s"><span><em>Fasilitas Kami</em></span></span>
                        </h2>

                        <p class="lx-lead lx-fade" style="--d:.35s; max-width:520px;">
                            Laboratorium di Gedung P UK Petra dirancang memenuhi standar akademik dan industri terkini. Kami
                            menyediakan lingkungan kondusif bagi mahasiswa untuk bereksperimen dan mengaplikasikan teori
                            yang dipelajari di kelas.
                        </p>

                        <div class="lx-feats">
                            <span class="lx-line" style="--d:.2s"></span>

                            <div class="lx-feat lx-fade" style="--d:.2s">
                                <span class="lx-feat-no">i.</span>
                                <div>
                                    <h3>Hardware Terkini</h3>
                                    <p>Perangkat komputasi spesifikasi tinggi untuk menunjang software modern dan
                                        pengembangan aplikasi.</p>
                                </div>
                                <i class="fa-solid fa-desktop"></i>
                            </div>
                            <span class="lx-line" style="--d:.35s"></span>

                            <div class="lx-feat lx-fade" style="--d:.3s">
                                <span class="lx-feat-no">ii.</span>
                                <div>
                                    <h3>Peralatan Lengkap</h3>
                                    <p>Ketersediaan alat ukur dan komponen elektronik untuk mendukung tugas akhir dan riset.
                                    </p>
                                </div>
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </div>
                            <span class="lx-line" style="--d:.5s"></span>

                            <div class="lx-feat lx-fade" style="--d:.4s">
                                <span class="lx-feat-no">iii.</span>
                                <div>
                                    <h3>Konektivitas Cepat</h3>
                                    <p>Jaringan internet dedicated bandwidth tinggi di seluruh ruang laboratorium Gedung P.
                                    </p>
                                </div>
                                <i class="fa-solid fa-wifi"></i>
                            </div>
                            <span class="lx-line" style="--d:.65s"></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════
        SERVICES
        ══════════════════════════════════════════ --}}
        <section id="services" class="lx-services" aria-label="Layanan dan Peminjaman">
            <div class="lx-wrap">

                <div class="lx-services-head" data-reveal-group>
                    <div>
                        <div class="lx-eyebrow lx-fade">Layanan</div>
                        <h2 class="lx-display lx-services-title">
                            <span class="lx-mask" style="--d:.05s"><span>Layanan &amp;</span></span>
                            <span class="lx-mask" style="--d:.17s"><span><em>Peminjaman</em></span></span>
                        </h2>
                    </div>
                    <p class="lx-lead lx-fade" style="--d:.35s">
                        Akses mudah untuk reservasi ruangan dan peminjaman inventaris melalui sistem terintegrasi kami.
                    </p>
                </div>

                <div class="lx-panels" id="lxPanels" data-reveal-group>

                    {{-- Panel 1: Laboratorium --}}
                    <article class="lx-panel is-active lx-fade" style="--d:.1s" tabindex="0">
                        <span class="lx-panel-no">01</span>
                        <span class="lx-panel-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" class="bi bi-laptop-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2.5 2A1.5 1.5 0 0 0 1 3.5V12h14V3.5A1.5 1.5 0 0 0 13.5 2zM0 12.5h16a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5" />
                            </svg></span>
                        <div class="lx-panel-body">
                            <h3>Laboratorium</h3>
                            <div class="lx-panel-more">
                                <p>Reservasi ruangan laboratorium komputer untuk praktikum mandiri, pengerjaan tugas besar,
                                    atau penelitian.</p>
                                <ul class="lx-checks">
                                    <li><i class="fa-solid fa-check"></i> PC Spesifikasi Tinggi</li>
                                    <li><i class="fa-solid fa-check"></i> Koneksi Internet Cepat</li>
                                    <li><i class="fa-solid fa-check"></i> Ruangan Ber-AC</li>
                                </ul>
                                <a href="{{ route('user.booking.form') }}" class="lx-btn" id="svc-booking-lab">
                                    BOOKING RUANGAN <i class="fa-solid fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                    {{-- Panel 2: Inventaris --}}
                    <article class="lx-panel lx-fade" style="--d:.22s" tabindex="0">
                        <span class="lx-panel-no">02</span>
                        <span class="lx-panel-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" class="bi bi-archive-fill" viewBox="0 0 16 16">
                                <path
                                    d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15zM5.5 7h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1M.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8z" />
                            </svg></span>
                        <div class="lx-panel-body">
                            <h3>Inventaris Alat</h3>
                            <div class="lx-panel-more">
                                <p>Peminjaman peralatan elektronik, kit development IoT, dan alat ukur untuk mendukung
                                    proyek Anda.</p>
                                <ul class="lx-checks">
                                    <li><i class="fa-solid fa-check"></i> Microcontroller &amp; Sensor</li>
                                    <li><i class="fa-solid fa-check"></i> Alat Ukur (Oscilloscope)</li>
                                    <li><i class="fa-solid fa-check"></i> Perangkat Jaringan</li>
                                </ul>
                                <a href="{{ route('user.booking.form') }}" class="lx-btn" id="svc-booking-item">
                                    PINJAM ALAT <i class="fa-solid fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                    {{-- Panel 3: Helpdesk --}}
                    <article class="lx-panel lx-fade" style="--d:.34s" tabindex="0">
                        <span class="lx-panel-no">03</span>
                        <span class="lx-panel-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" class="bi bi-headset" viewBox="0 0 16 16">
                                <path
                                    d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5" />
                            </svg></span>
                        <div class="lx-panel-body">
                            <h3>Helpdesk &amp; Saran</h3>
                            <div class="lx-panel-more">
                                <p>Pusat pengaduan kerusakan perangkat lab serta sarana penyampaian keluhan dan saran
                                    peningkatan mutu.</p>
                                <ul class="lx-checks">
                                    <li><i class="fa-solid fa-check"></i> Lapor Kerusakan PC &amp; Meja</li>
                                    <li><i class="fa-solid fa-check"></i> Keluhan Fasilitas Ruang</li>
                                    <li><i class="fa-solid fa-check"></i> Tracking Status Tiket</li>
                                </ul>
                                <a href="{{ route('user.helpdesk.index') }}" class="lx-btn" id="svc-helpdesk">
                                    PUSAT BANTUAN <i class="fa-solid fa-arrow-right-long"></i>
                                </a>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════
        LANGKAH (memakai informasi yang sudah ada)
        ══════════════════════════════════════════ --}}
        <section class="lx-steps" aria-label="Alur Reservasi">
            <div class="lx-wrap">
                <div class="lx-steps-head" data-reveal-group>
                    <div>
                        <div class="lx-eyebrow lx-fade">Alur Singkat</div>
                        <h2 class="lx-display lx-steps-title">
                            <span class="lx-mask" style="--d:.05s"><span>Tiga langkah,</span></span>
                            <span class="lx-mask" style="--d:.17s"><span><em>tepat waktu.</em></span></span>
                        </h2>
                    </div>
                    <a href="{{ route('user.booking.form') }}" class="lx-link lx-fade" style="--d:.4s">
                        Buka form reservasi <i class="fa-solid fa-arrow-right-long"></i>
                    </a>
                </div>

                <span class="lx-line" data-reveal-group></span>
                <div class="lx-steps-grid" data-reveal-group>
                    <div class="lx-step lx-fade" style="--d:.1s">
                        <div class="lx-step-no">01</div>
                        <h4>Pilih Layanan</h4>
                        <p>Tentukan reservasi laboratorium atau peminjaman inventaris sesuai kebutuhan proyek Anda.</p>
                    </div>
                    <div class="lx-step lx-fade" style="--d:.25s">
                        <div class="lx-step-no">02</div>
                        <h4>Cek Ketersediaan</h4>
                        <p>Lihat jadwal dan ketersediaan secara real-time melalui sistem booking terintegrasi.</p>
                    </div>
                    <div class="lx-step lx-fade" style="--d:.4s">
                        <div class="lx-step-no">03</div>
                        <h4>Mulai Praktikum</h4>
                        <p>Datang ke Gedung P dan manfaatkan fasilitas lab dalam lingkungan yang profesional.</p>
                    </div>
                </div>
                <span class="lx-line" data-reveal-group></span>
            </div>
        </section>

        {{-- ══════════════════════════════════════════
        CTA
        ══════════════════════════════════════════ --}}
        <section class="lx-cta" id="lxCta" aria-label="Call to Action">
            <div class="lx-cta-ghost" aria-hidden="true">Gedung P</div>
            <div class="lx-wrap">
                <div class="lx-cta-inner" data-reveal-group>
                    <div class="lx-cta-ornament lx-fade"><i class="fa-solid fa-diamond"></i></div>
                    <h2 class="lx-display lx-cta-title">
                        <span class="lx-mask" style="--d:.05s"><span>Permudah Jadwal</span></span>
                        <span class="lx-mask" style="--d:.17s"><span><em>Praktikum &amp; Riset</em> Anda</span></span>
                    </h2>
                    <p class="lx-lead lx-cta-copy lx-fade" style="--d:.4s">
                        Sistem booking kami memastikan Anda mendapatkan fasilitas yang dibutuhkan tepat waktu. Cek
                        ketersediaan secara real-time sekarang.
                    </p>
                    <div class="lx-fade" style="--d:.55s">
                        <a href="{{ route('user.booking.form') }}" class="lx-btn" id="cta-main-booking"
                            style="padding:22px 48px;">
                            MULAI RESERVASI
                            <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    @include('user.partials.footer')

@endsection

@section('script')
    {{-- Lenis Smooth Scroll Library --}}
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <script>
        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* ── Lenis smooth scroll ───────────────────────── */
            if (typeof Lenis !== 'undefined' && !reduce) {
                var lenis = new Lenis({
                    duration: 0.85,
                    easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
                    smoothWheel: true,
                    touchMultiplier: 1.2
                });

                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);
                window.lenis = lenis;
            }

            // Scroll otomatis langsung tanpa delay ke target anchor
            function scrollToTarget(targetEl) {
                if (!targetEl) return;
                if (window.lenis) {
                    window.lenis.scrollTo(targetEl, {
                        offset: -75,
                        duration: 0.65,
                        lock: false
                    });
                } else {
                    var targetPos = targetEl.getBoundingClientRect().top + window.pageYOffset - 75;
                    window.scrollTo({ top: targetPos, behavior: 'smooth' });
                }
            }

            document.querySelectorAll('a[href*="#"]').forEach(function (anchor) {
                anchor.addEventListener('click', function (e) {
                    var href = this.getAttribute('href');
                    if (!href) return;
                    var i = href.indexOf('#');
                    if (i === -1) return;
                    var id = href.substring(i);
                    if (id && id !== '#') {
                        var el = document.querySelector(id);
                        if (el) {
                            e.preventDefault();
                            if (window.history && window.history.pushState) {
                                window.history.pushState(null, '', id);
                            }
                            scrollToTarget(el);
                        }
                    }
                });
            });

            if (window.location.hash) {
                var hashEl = document.querySelector(window.location.hash);
                if (hashEl) {
                    if (window.lenis) {
                        window.lenis.scrollTo(hashEl, { offset: -75, immediate: true });
                    } else {
                        hashEl.scrollIntoView();
                    }
                }
            }

            /* ── Reveal on view ────────────────────────────── */
            var groups = document.querySelectorAll('[data-reveal-group]');
            var revealTargets = '.lx-mask, .lx-fade, .lx-wipe, .lx-wipe-x, .lx-line, .lx-panel';

            function runCounters(scope) {
                scope.querySelectorAll('[data-count]').forEach(function (el) {
                    if (el.dataset.done) return;
                    el.dataset.done = '1';
                    var end = parseInt(el.dataset.count, 10), start = null, dur = 1800;
                    if (reduce) { el.textContent = end; return; }
                    function tick(ts) {
                        if (!start) start = ts;
                        var p = Math.min((ts - start) / dur, 1);
                        var eased = 1 - Math.pow(1 - p, 4);
                        el.textContent = Math.round(end * eased);
                        if (p < 1) requestAnimationFrame(tick);
                    }
                    requestAnimationFrame(tick);
                });
            }

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (en) {
                        if (!en.isIntersecting) return;
                        var g = en.target;
                        g.classList.add('is-in');
                        g.querySelectorAll(revealTargets).forEach(function (n) { n.classList.add('is-in'); });
                        if (g.matches(revealTargets)) g.classList.add('is-in');
                        runCounters(g);
                        io.unobserve(g);
                    });
                }, { threshold: 0.05, rootMargin: '0px 0px -2% 0px' });
                groups.forEach(function (g) { io.observe(g); });
            } else {
                document.querySelectorAll(revealTargets).forEach(function (n) { n.classList.add('is-in'); });
                runCounters(document);
            }

            /* ── Scroll progress + parallax gambar (synced dengan Lenis) ─── */
            var bar = document.getElementById('lxProgress');
            var pars = document.querySelectorAll('[data-parallax]');

            function onScrollUpdate(scrollPos) {
                var h = document.documentElement.scrollHeight - window.innerHeight;
                if (bar && h > 0) bar.style.transform = 'scaleX(' + Math.min(scrollPos / h, 1) + ')';
                if (reduce) return;
                var vh = window.innerHeight;
                pars.forEach(function (img) {
                    var host = img.parentElement.getBoundingClientRect();
                    if (host.bottom < -100 || host.top > vh + 100) return;
                    var center = host.top + host.height / 2 - vh / 2;
                    img.style.transform = 'translate3d(0,' + (center * parseFloat(img.dataset.parallax) * -1) + 'px,0)';
                });
            }

            if (typeof lenis !== 'undefined' && lenis) {
                lenis.on('scroll', function (e) {
                    onScrollUpdate(e.scroll);
                });
            } else {
                window.addEventListener('scroll', function () {
                    onScrollUpdate(window.scrollY);
                }, { passive: true });
            }
            window.addEventListener('resize', function () {
                onScrollUpdate(window.scrollY);
            });
            onScrollUpdate(window.scrollY);

            /* ── Cahaya mengikuti kursor (hero & CTA) ──────── */
            ['lxHero', 'lxCta'].forEach(function (id) {
                var s = document.getElementById(id);
                if (!s || reduce) return;
                s.addEventListener('mousemove', function (e) {
                    var r = s.getBoundingClientRect();
                    s.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
                    s.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
                });
            });

            /* ── Panel layanan (akordeon horizontal) ───────── */
            var panelsWrap = document.getElementById('lxPanels');
            if (panelsWrap) {
                var panels = panelsWrap.querySelectorAll('.lx-panel');
                var desktop = window.matchMedia('(min-width: 900px)');
                function activate(p) {
                    if (!desktop.matches) return;
                    panels.forEach(function (x) { x.classList.toggle('is-active', x === p); });
                }
                panels.forEach(function (p) {
                    p.addEventListener('mouseenter', function () { activate(p); });
                    p.addEventListener('focusin', function () { activate(p); });
                    p.addEventListener('click', function () { activate(p); });
                });
            }
        })();
    </script>
@endsection