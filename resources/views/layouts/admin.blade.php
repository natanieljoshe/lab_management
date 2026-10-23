<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Script & CSS Utama --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- TW Elements (hanya 1x) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" />

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>

    {{-- TomSelect (CSS & JS) --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <title>Admin | @if (isset($title))
            {{ $title }}
        @else
            @yield('title', 'Lab Infor')
        @endif
    </title>
    <link rel="icon" href="data:,">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                fontFamily: {
                    sans: ["Open Sans", "sans-serif"],
                    body: ["Open Sans", "sans-serif"],
                    mono: ["ui-monospace", "monospace"],
                },
            },
        };
    </script>

    <style>
        /* Accordion Styles */
        .nav-group {
            transition: all 0.3s ease;
        }

        .nav-group-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 3px solid #6366f1;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-group-header:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
        }

        .nav-group-toggle {
            transition: transform 0.3s ease;
        }

        .nav-group-toggle.expanded {
            transform: rotate(90deg);
        }

        .nav-group-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .nav-group-content.expanded {
            max-height: 1000px;
        }
        
        @keyframes iconGlow {
            0%, 100% { filter: drop-shadow(0 0 2px currentColor); }
            50% { filter: drop-shadow(0 0 6px currentColor); }
        }
        
        a:hover svg, a.active svg { animation: iconGlow 2s ease-in-out infinite; }
        
        /* #labs:hover svg, #labs.active svg { color: #3b82f6; }
        #items:hover svg, #items.active svg { color: #8b5cf6; }
        #sets:hover svg, #sets.active svg { color: #10b981; }
        #softwares:hover svg, #softwares.active svg { color: #b3621a; }
        #repairs:hover svg, #repairs.active svg { color: #ef4444; }
        #bookings:hover svg, #bookings.active svg { color: #ec4899; }
        #roles:hover svg, #roles.active svg { color: #14b8a6; }
        #permissions:hover svg, #permissions.active svg { color: #6366f1; }
        #periods:hover svg, #periods.active svg { color: #f97316; }
        #matkul:hover svg, #matkul.active svg { color: #06b6d4; }
        #module:hover svg, #module.active svg { color: #22d3ee; }
        #report_repairs:hover svg, #report_repairs.active svg { color: #84cc16; }
        #repository:hover svg, #repository.active svg { color: #f59e0b; }
        #report_bookings:hover svg, #report_bookings.active svg { color: #a855f7; } */

        #sidenav-8 #labs:hover svg, #sidenav-8 #labs.active svg { color: #3b82f6; }
        #sidenav-8 #items:hover svg, #sidenav-8 #items.active svg { color: #8b5cf6; }
        #sidenav-8 #sets:hover svg, #sidenav-8 #sets.active svg { color: #10b981; }
        #sidenav-8 #softwares:hover svg, #sidenav-8 #softwares.active svg { color: #b3621a; }
        #sidenav-8 #repairs:hover svg, #sidenav-8 #repairs.active svg { color: #ef4444; }
        #sidenav-8 #bookings:hover svg, #sidenav-8 #bookings.active svg { color: #ec4899; }
        #sidenav-8 #roles:hover svg, #sidenav-8 #roles.active svg { color: #14b8a6; }
        #sidenav-8 #permissions:hover svg, #sidenav-8 #permissions.active svg { color: #6366f1; }
        #sidenav-8 #periods:hover svg, #sidenav-8 #periods.active svg { color: #f97316; }
        #sidenav-8 #matkul:hover svg, #sidenav-8 #matkul.active svg { color: #06b6d4; }
        #sidenav-8 #report_repairs:hover svg, #sidenav-8 #report_repairs.active svg { color: #84cc16; }
        #sidenav-8 #repository:hover svg, #sidenav-8 #repository.active svg { color: #f59e0b; }
        #sidenav-8 #report_bookings:hover svg, #sidenav-8 #report_bookings.active svg { color: #a855f7; }

        .ts-control {
            display: block;
            width: 100%;
            padding-top: 0.80rem;
            padding-bottom: 0.80rem;
            padding-left: 1rem;
            padding-right: 1rem;

            font-size: 1rem;
            line-height: 1.5rem;
            color: #1f2937;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .ts-wrapper input {
            width: 100%;
        }

        .ts-wrapper.focus .ts-control {
            border-color: transparent !important;
            box-shadow: 0 0 0 2px #6366f1;
            outline: none;
        }

        .ts-wrapper.dropdown-active .ts-control {
            display: block;
            border-radius: 0.5rem !important;

            border: 2px solid black !important;
            box-shadow: 0 0 0 2px #6366f1 !important;
            outline: none !important;
        }

        /* untuk item/pill yang DIPILIH */
        .ts-wrapper.multi .ts-control>div,
        .ts-wrapper.single .ts-control .item {
            background-color: #e0e7ff;
            color: #4338ca;
            border-radius: 0.25rem;
            padding: 0.125rem 0.5rem;
            margin: 0.125rem 0.25rem;
        }

        .ts-wrapper.multi .ts-control {
            padding-right: 2rem !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        .ts-control.dropdown-active {}

        .ts-dropdown {
            border: 1px solid #d1d5db;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 9000 !important;
        }

        .ts-dropdown .option {
            background-color: #ffffff;
            padding: 0.5rem 1rem;
        }

        .ts-dropdown .option.active,
        .ts-dropdown .option:hover {
            background-color: #e0e7ff;
        }

        .ts-dropdown .create {
            color: #2563eb;
        }

        /* ============================================ */
        /* CSS UNTUK RESIZER SIDEBAR (FIXED VERSION)    */
        /* ============================================ */
        :root {
            --sidebar-width: 240px;
        }

        #sidenav-8 {
            width: var(--sidebar-width) !important;
            transition: width 0s;
        }

        #main-content {
            transition: margin-left 0s;
        }

        @media (min-width: 768px) {
            #main-content {
                margin-left: var(--sidebar-width) !important;
            }
        }

        #sidenav-resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 6px;
            height: 100%;
            cursor: ew-resize;
            background: transparent;
            transition: background-color 0.2s;
            z-index: 1040;
        }

        #sidenav-resizer:hover,
        body.is-resizing #sidenav-resizer {
            background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.6));
        }

        body.is-resizing {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
        }

        #sidenav-resizer::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 40px;
            background: rgba(99, 102, 241, 0.3);
            border-radius: 2px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        #sidenav-resizer:hover::after,
        body.is-resizing #sidenav-resizer::after {
            opacity: 1;
        }

        /* ============================================ */
        /* AKHIR CSS RESIZER SIDEBAR                    */
        /* ============================================ */

        div[data-te-datatable-pagination-ref] {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            /* Paksa tetap satu baris */
            width: 100% !important;
            padding: 1rem 1.5rem !important;
            border-top: 1px solid #e5e7eb !important;
            min-height: 60px !important;
            overflow-x: auto !important;
            /* Jika layar sangat kecil, scroll pagination saja */
        }

        /* 2. Target Sisi Kiri (Rows per page + Input) dan Kanan (Navigasi) */
        div[data-te-datatable-pagination-ref]>div {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 15px !important;
            /* Jarak antar elemen */
            margin: 0 !important;
            width: auto !important;
        }

        /* 3. Fix Text Label agar tidak turun baris */
        div[data-te-datatable-pagination-ref] span {
            white-space: nowrap !important;
        }

        /* 4. Fix Wrapper Dropdown Select "Rows per page" */
        div[data-te-datatable-select-wrapper-ref] {
            display: inline-flex !important;
            align-items: center !important;
            margin: 0 !important;
            width: auto !important;
            min-width: 70px !important;
            /* Lebar minimum agar angka tidak terpotong */
        }

        /* 5. Fix Input di dalam Dropdown Select */
        div[data-te-datatable-select-wrapper-ref] input {
            padding-right: 30px !important;
            /* Ruang untuk panah dropdown */
            min-width: 60px !important;
            text-align: center !important;
        }

        /* 6. Pastikan ikon panah dropdown terlihat benar */
        div[data-te-datatable-select-wrapper-ref] span[role="button"] {
            right: 5px !important;
        }
    </style>

    {{-- ========================================================= --}}
    {{-- STYLE UNTUK SWEETALERT & TOAST (MODERN COLORFUL) - FIXED  --}}
    {{-- ========================================================= --}}
    <style>
        /* Base popup style dengan glassmorphism effect */
        .swal2-container {
            z-index: 9997 !important;
        }

        .swal2-popup {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85)) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset !important;
            padding: 2rem !important;
        }

        /* Dark mode support */
        .dark .swal2-popup {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.95), rgba(31, 41, 55, 0.85)) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* ============================================ */
        /* TOAST STYLING - WARNA SESUAI TYPE (FIXED)    */
        /* ============================================ */

        /* Base Toast Style */
        .swal2-toast-custom.swal2-popup {
            max-width: 400px !important;
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border-radius: 16px !important;
            padding: 1rem 1.25rem !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.18) inset !important;
            border: 2px solid !important;
        }

        /* SUCCESS TOAST - Hijau */
        .swal2-toast-custom.swal2-popup.swal2-icon-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95)) !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.9)) !important;
        }

        /* ERROR TOAST - Merah */
        .swal2-toast-custom.swal2-popup.swal2-icon-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95)) !important;
            border-color: rgba(239, 68, 68, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.9), rgba(220, 38, 38, 0.9)) !important;
        }

        /* WARNING TOAST - Kuning/Orange */
        .swal2-toast-custom.swal2-popup.swal2-icon-warning {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.95), rgba(249, 115, 22, 0.95)) !important;
            border-color: rgba(251, 146, 60, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-warning {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.9), rgba(249, 115, 22, 0.9)) !important;
        }

        /* INFO TOAST - Biru */
        .swal2-toast-custom.swal2-popup.swal2-icon-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95)) !important;
            border-color: rgba(59, 130, 246, 0.5) !important;
        }

        .dark .swal2-toast-custom.swal2-popup.swal2-icon-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.9), rgba(37, 99, 235, 0.9)) !important;
        }

        /* NETWORK ERROR TOAST - Ungu/Purple */
        .swal2-toast-network.swal2-popup {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.95), rgba(124, 58, 237, 0.95)) !important;
            border-color: rgba(139, 92, 246, 0.5) !important;
        }

        .dark .swal2-toast-network.swal2-popup {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.9), rgba(124, 58, 237, 0.9)) !important;
        }

        /* Toast Title & Text - Selalu Putih untuk Toast Berwarna */
        .swal2-toast-custom .swal2-title {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 1.125rem !important;
            margin: 0 !important;
        }

        .swal2-toast-custom .swal2-html-container {
            color: rgba(255, 255, 255, 0.95) !important;
            font-size: 0.875rem !important;
            margin: 0.25rem 0 0 0 !important;
        }

        /* Toast Icon - Putih dan Lebih Kecil */
        .swal2-toast-custom .swal2-icon {
            width: 2rem !important;
            height: 2rem !important;
            margin: 0 0.75rem 0 0 !important;
            border-width: 2px !important;
            border-color: rgba(255, 255, 255, 0.8) !important;
        }

        .swal2-toast-custom.swal2-popup .swal2-icon .swal2-icon-content {
            color: #ffffff !important;
            font-size: 1.5rem !important;
        }

        .swal2-toast-custom.swal2-popup.swal2-icon-success .swal2-success-ring {
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        .swal2-toast-custom.swal2-popup.swal2-icon-success .swal2-success [class^='swal2-success-line'] {
            background-color: #ffffff !important;
        }

        .swal2-toast-custom.swal2-popup.swal2-icon-error .swal2-error [class^='swal2-x-mark-line'] {
            background-color: #ffffff !important;
        }

        /* Timer Progress Bar - Putih Semi-transparan */
        .swal2-toast-custom .swal2-timer-progress-bar {
            background: rgba(255, 255, 255, 0.5) !important;
        }

        /* Title styling (untuk Modal) */
        .swal2-title {
            color: #1f2937 !important;
            font-weight: 700 !important;
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        .dark .swal2-title {
            color: #f9fafb !important;
        }

        /* HTML container & text (untuk Modal) */
        .swal2-html-container {
            color: #4b5563 !important;
            font-size: 1rem !important;
            line-height: 1.6 !important;
            margin: 0 !important;
        }

        .swal2-popup:not(.swal2-toast-custom) .swal2-html-container {
            margin: 1.25em 1.6em 0.3em !important;
        }

        .dark .swal2-html-container {
            color: #d1d5db !important;
        }

        /* Success icon (Modal) - Green */
        .swal2-icon.swal2-success {
            border-color: #10b981 !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #10b981 !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        /* Error icon (Modal) - Red */
        .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
        }

        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #ef4444 !important;
        }

        /* Warning icon (Modal) - Amber */
        .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }

        /* Info icon (Modal) - Blue */
        .swal2-icon.swal2-info {
            border-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }

        /* Confirm button - Indigo gradient */
        .swal2-confirm {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 0.75rem 2rem !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset !important;
            transition: all 0.3s ease !important;
            font-size: 1rem !important;
        }

        .swal2-confirm:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca) !important;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.3) inset !important;
            transform: translateY(-2px);
        }

        /* Cancel button - Gray */
        .swal2-cancel {
            background: rgba(229, 231, 235, 0.8) !important;
            color: #374151 !important;
            font-weight: 600 !important;
            padding: 0.75rem 2rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(209, 213, 219, 0.8) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset !important;
            transition: all 0.3s ease !important;
            font-size: 1rem !important;
        }

        .swal2-cancel:hover {
            background: rgba(209, 213, 219, 0.9) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.3) inset !important;
            transform: translateY(-2px);
        }

        .dark .swal2-cancel {
            background: rgba(55, 65, 81, 0.8) !important;
            color: #f9fafb !important;
            border: 1px solid rgba(75, 85, 99, 0.8) !important;
        }

        /* Deny button */
        .swal2-deny {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #dc2626 !important;
            font-weight: 600 !important;
            padding: 0.75rem 2rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(239, 68, 68, 0.3) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.1) !important;
            transition: all 0.3s ease !important;
            font-size: 1rem !important;
        }

        .swal2-deny:hover {
            background: rgba(239, 68, 68, 0.2) !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
            transform: translateY(-2px);
        }

        /* Actions container */
        .swal2-actions {
            gap: 0.75rem !important;
            margin-top: 1.5rem !important;
        }

        /* Timer progress bar (untuk Modal) */
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #6366f1, #8b5cf6) !important;
            height: 4px !important;
        }

        /* Input fields */
        .swal2-input,
        .swal2-textarea {
            border: 2px solid rgba(209, 213, 219, 0.5) !important;
            border-radius: 12px !important;
            padding: 0.75rem 1rem !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
        }

        .swal2-input:focus,
        .swal2-textarea:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
            outline: none !important;
        }

        .dark .swal2-input,
        .dark .swal2-textarea {
            background: rgba(31, 41, 55, 0.8) !important;
            color: #f9fafb !important;
            border-color: rgba(75, 85, 99, 0.5) !important;
        }

        .swal2-container.swal2-center {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }

        /* Toast animations */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .swal2-toast-custom.swal2-show {
            animation: slideInRight 0.3s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {

            /* Target Modal SAJA, bukan toast */
            .swal2-popup:not(.swal2-toast-custom) {
                padding: 1.5rem !important;
                border-radius: 16px !important;
            }

            /* Target Ikon Modal SAJA */
            .swal2-popup:not(.swal2-toast-custom) .swal2-icon {
                width: 4rem !important;
                height: 4rem !important;
                margin: 1rem auto !important;
            }

            /* Target Title Modal SAJA */
            .swal2-popup:not(.swal2-toast-custom) .swal2-title {
                font-size: 1.25rem !important;
            }

            /* Buat TOAST full-width di mobile */
            .swal2-toast-custom.swal2-popup {
                width: auto !important;
                max-width: none !important;
                margin: 0.5em !important;
            }
        }
    </style>

    @yield('style')
</head>
@php
    $header = '';
@endphp

<body>
    {{-- Sidenav (Resizable) --}}
    <nav id="sidenav-8"
        class="fixed left-0 top-0 z-[1035] h-full min-h-[100vh] -translate-x-full overflow-hidden bg-white shadow-[0_4px_12px_0_rgba(0,0,0,0.07),_0_2px_4px_rgba(0,0,0,0.05)] data-[te-sidenav-hidden='false']:translate-x-0 dark:bg-zinc-800 invisible md:visible"
        data-te-sidenav-init data-te-sidenav-hidden="false" data-te-sidenav-position="fixed" data-te-sidenav-mode="side"
        data-te-sidenav-accordion="true">

        <a class="mb-3 flex flex-col items-center justify-center border-b-2 border-solid border-gray-100 py-6 outline-none"
            href="#" data-te-ripple-init data-te-ripple-color="primary">
            <div class="flex items-center justify-center space-x-3 mb-3">
                <!-- <img src="{{ asset('assets/logo/logo-robot.png') }}" class="h-8" alt="infor" loading="lazy" /> -->
                <!-- <div class="border-l-2 border-gray-300 h-8"></div> -->
                <img src="{{ asset('assets/logo/logo-piciu.png') }}" class="h-8" alt="pcu" loading="lazy" />
            </div>
            <span class="text-center font-bold">Laboratorium Informatika <br>2025/2026</span>
        </a>
        <ul class="relative m-0 list-none px-[0.2rem] pb-12" data-te-sidenav-menu-ref>
            <li class="relative">
                <a id="overview"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    data-te-sidenav-link-ref href="{{ route('admin.dashboard') }}">
                    <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                            <path
                                d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                        </svg>
                    </span>
                    <span>Overview</span>
                </a>
            </li>
            <li class="relative pt-4 nav-group">
                <div class="nav-group-header flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer"
                    onclick="toggleNavGroup(this)">
                    <span class="text-[0.65rem] font-bold uppercase text-gray-700 dark:text-gray-300">
                        Dashboards</span>
                    <svg class="nav-group-toggle expanded w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="nav-group-content expanded">
                    <a id="labs"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 6a3 3 0 013-3h2.25a3 3 0 013 3v2.25a3 3 0 01-3 3H6a3 3 0 01-3-3V6zm9.75 0a3 3 0 013-3H18a3 3 0 013 3v2.25a3 3 0 01-3 3h-2.25a3 3 0 01-3-3V6zM3 15.75a3 3 0 013-3h2.25a3 3 0 013 3V18a3 3 0 01-3 3H6a3 3 0 01-3-3v-2.25zm9.75 0a3 3 0 013-3H18a3 3 0 013 3V18a3 3 0 01-3 3h-2.25a3 3 0 01-3-3v-2.25z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Laboratories</span>
                    </a>
                    <a id="items"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.items') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375z" />
                                <path fill-rule="evenodd"
                                    d="M3.087 9l.54 9.176A3 3 0 006.62 21h10.757a3 3 0 002.995-2.824L20.913 9H3.087zm6.163 3.75A.75.75 0 0110 12h4a.75.75 0 010 1.5h-4a.75.75 0 01-.75-.75z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Inventaris</span>
                    </a>
                    <a id="sets"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.sets') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z"
                                    clip-rule="evenodd" />
                                <path fill-rule="evenodd"
                                    d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zm9.586 4.594a.75.75 0 00-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 00-1.06 1.06l1.5 1.5a.75.75 0 001.116-.062l3-3.75z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Sets</span>
                    </a>
                    <a id="softwares"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.softwares') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M2.25 6a3 3 0 013-3h13.5a3 3 0 013 3v12a3 3 0 01-3 3H5.25a3 3 0 01-3-3V6zm3.97.97a.75.75 0 011.06 0l2.25 2.25a.75.75 0 010 1.06l-2.25 2.25a.75.75 0 01-1.06-1.06l1.72-1.72-1.72-1.72a.75.75 0 010-1.06zm4.28 4.28a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Softwares and OS</span>
                    </a>
                    <a id="repository"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.repository') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.906 9c.382 0 .749.057 1.094.162V9a3 3 0 00-3-3h-3.879a.75.75 0 01-.53-.22L11.47 3.66A2.25 2.25 0 009.879 3H6a3 3 0 00-3 3v3.162A3.756 3.756 0 014.094 9h15.812zM4.094 10.5a2.25 2.25 0 00-2.227 2.568l.857 6A2.25 2.25 0 004.951 21H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-2.227-2.568H4.094z" />
                            </svg>
                        </span>
                        <span>Repository</span>
                    </a>
                    <a id="repairs"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.repairs') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12 6.75a5.25 5.25 0 016.775-5.025.75.75 0 01.313 1.248l-3.32 3.319c.063.475.276.934.641 1.299.365.365.824.578 1.3.64l3.318-3.319a.75.75 0 011.248.313 5.25 5.25 0 01-5.472 6.756c-1.018-.086-1.87.1-2.309.634L7.344 21.3A3.298 3.298 0 112.7 16.657l8.684-7.151c.533-.44.72-1.291.634-2.309A5.342 5.342 0 0112 6.75zM4.117 19.125a.75.75 0 01.75-.75h.008a.75.75 0 01.75.75v.008a.75.75 0 01-.75.75h-.008a.75.75 0 01-.75-.75v-.008z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Repairs</span>
                    </a>
                    <a id="bookings"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.bookings') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12.75 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM7.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM9.75 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM10.5 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12.75 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM14.25 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                                <path fill-rule="evenodd"
                                    d="M6.75 2.25A.75.75 0 017.5 3v1.5h9V3A.75.75 0 0118 3v1.5h.75a3 3 0 013 3v11.25a3 3 0 01-3 3H5.25a3 3 0 01-3-3V7.5a3 3 0 013-3H6V3a.75.75 0 01.75-.75zm13.5 9a1.5 1.5 0 00-1.5-1.5H5.25a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Bookings</span>
                    </a>
                </div>
            </li>
            <li class="relative pt-4 nav-group">
                <div class="nav-group-header flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer"
                    onclick="toggleNavGroup(this)">
                    <span class="text-[0.65rem] font-bold uppercase text-gray-700 dark:text-gray-300">Access
                        Controls</span>
                    <svg class="nav-group-toggle expanded w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="nav-group-content expanded">
                    <a id="roles"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.roles') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.372.568A12.696 12.696 0 0112 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 01-.372-.568 6.787 6.787 0 011.019-4.38z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M5.082 14.254a8.287 8.287 0 00-1.308 5.135 9.687 9.687 0 01-1.764-.44l-.115-.04a.563.563 0 01-.373-.487l-.01-.121a3.75 3.75 0 013.57-4.047zM20.226 19.389a8.287 8.287 0 00-1.308-5.135 3.75 3.75 0 013.57 4.047l-.01.121a.563.563 0 01-.373.486l-.115.04c-.567.2-1.156.349-1.764.441z" />
                            </svg>
                        </span>
                        <span>Manage Roles</span>
                    </a>
                    <a id="permissions"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.permissions') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                        <span>Manage Access</span>
                    </a>
                </div>
            </li>
            <li class="relative pt-4 nav-group">
                <div class="nav-group-header flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer"
                    onclick="toggleNavGroup(this)">
                    <span class="text-[0.65rem] font-bold uppercase text-gray-700 dark:text-gray-300">Academics &
                        Periods</span>
                    <svg class="nav-group-toggle expanded w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="nav-group-content expanded">
                    <a id="matkul"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.matkul') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z" />
                            </svg>
                        </span>
                        <span>Mata Kuliah</span>
                    </a>
                    <a id="module"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.modules') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" />
                                <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                            </svg>
                        </span>
                        <span>Module</span>
                    </a>
                </div>
            </li>
            <li class="relative pt-4 nav-group">
                <div class="nav-group-header flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer"
                    onclick="toggleNavGroup(this)">
                    <span class="text-[0.65rem] font-bold uppercase text-gray-700 dark:text-gray-300">Academics Schedule</span>
                    <svg class="nav-group-toggle expanded w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="nav-group-content expanded">
                    <a id="schedule-main"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 {{ request()->routeIs('schedule.*') ? 'bg-indigo-50 text-indigo-600 font-semibold dark:bg-white/15' : '' }}"
                        href="{{ route('schedule.index') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 {{ request()->routeIs('schedule.*') ? '[&>svg]:text-indigo-600 dark:[&>svg]:text-indigo-400' : '[&>svg]:text-gray-400 dark:[&>svg]:text-gray-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        </span>
                        <span>Jadwal Utama</span>
                    </a>
                    <a id="schedule-classes"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 {{ request()->routeIs('import.*') ? 'bg-indigo-50 text-indigo-600 font-semibold dark:bg-white/15' : '' }}"
                        href="{{ route('import.index') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 {{ request()->routeIs('import.*') ? '[&>svg]:text-indigo-600 dark:[&>svg]:text-indigo-400' : '[&>svg]:text-gray-400 dark:[&>svg]:text-gray-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        </span>
                        <span>Pengaturan Kelas</span>
                    </a>
                    <a id="schedule-config"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 {{ request()->routeIs('config.*') ? 'bg-indigo-50 text-indigo-600 font-semibold dark:bg-white/15' : '' }}"
                        href="{{ route('config.index') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 {{ request()->routeIs('config.*') ? '[&>svg]:text-indigo-600 dark:[&>svg]:text-indigo-400' : '[&>svg]:text-gray-400 dark:[&>svg]:text-gray-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </span>
                        <span>Atur Praktikum</span>
                    </a>
                    <a id="schedule-versions"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 {{ request()->routeIs('versions.*') ? 'bg-indigo-50 text-indigo-600 font-semibold dark:bg-white/15' : '' }}"
                        href="{{ route('versions.index') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 {{ request()->routeIs('versions.*') ? '[&>svg]:text-indigo-600 dark:[&>svg]:text-indigo-400' : '[&>svg]:text-gray-400 dark:[&>svg]:text-gray-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </span>
                        <span>Draft Jadwal</span>
                    </a>
                    <a id="schedule-settings"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10 {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-600 font-semibold dark:bg-white/15' : '' }}"
                        href="{{ route('settings.index') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 {{ request()->routeIs('settings.*') ? '[&>svg]:text-indigo-600 dark:[&>svg]:text-indigo-400' : '[&>svg]:text-gray-400 dark:[&>svg]:text-gray-300' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </span>
                        <span>Pengaturan Periode</span>
                    </a>
                </div>
            </li>
            <li class="relative pt-4 nav-group">
                <div class="nav-group-header flex items-center justify-between px-4 py-3 rounded-lg cursor-pointer"
                    onclick="toggleNavGroup(this)">
                    <span class="text-[0.65rem] font-bold uppercase text-gray-700 dark:text-gray-300">Reports &
                        Statistics</span>
                    <svg class="nav-group-toggle expanded w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="nav-group-content expanded">
                    <a id="historylabs"
                        class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                        href="{{ route('admin.historylabs') }}" data-te-sidenav-link-ref>
                        <span class="mr-4 [&>svg]:h-4 [&>svg]:w-4 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z" />
                            </svg>
                        </span>
                        <span>Labs Booking History</span>
                    </a>
                    <a id="report_bookings"
                    class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                    href="{{ route('admin.historyinventaris') }}" data-te-sidenav-link-ref>
                    <span class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                        <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z" />
                        </svg>
                    </span>
                    <span>Item Booking History</span>
                </a>
                </div>
            </li>
        </ul>

        {{-- Resizer Handle --}}
        <div id="sidenav-resizer" title="Drag to resize sidebar"></div>
    </nav>

    {{-- Navbar Mobile --}}
    <nav
        class="flex-no-wrap relative flex w-full items-center justify-between bg-[#FBFBFB] py-2 shadow-md shadow-black/5 dark:bg-neutral-600 dark:shadow-black/10 lg:flex-wrap lg:justify-start lg:py-4 block md:hidden">
        <div class="flex w-full flex-wrap items-center justify-between px-3">
            <button
                class="block border-0 bg-transparent px-2 text-neutral-500 hover:no-underline hover:shadow-none focus:no-underline focus:shadow-none focus:outline-none focus:ring-0 dark:text-neutral-200 sm:hidden"
                type="button" data-te-collapse-init data-te-target="#navbarSupportedContent12"
                aria-controls="navbarSupportedContent12" aria-expanded="false" aria-label="Toggle navigation">
                <span class="[&>svg]:w-7">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7">
                        <path fill-rule="evenodd"
                            d="M3 6.75A.75.75 0 013.75 6h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 6.75zM3 12a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75A.75.75 0 013 12zm0 5.25a.75.75 0 01.75-.75h16.5a.75.75 0 010 1.5H3.75a.75.75 0 01-.75-.75z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            </button>

            <div class="!visible hidden flex-grow basis-[100%] items-center sm:!flex sm:basis-auto"
                id="navbarSupportedContent12" data-te-collapse-item>
                <ul class="list-style-none mr-auto flex flex-col pl-0 sm:flex-row" data-te-navbar-nav-ref>
                    <li class="my-4 pl-2 sm:my-0 sm:pl-0 sm:pr-1" data-te-nav-item-ref>
                        <a class="text-neutral-500 transition duration-200 hover:text-neutral-700 hover:ease-in-out focus:text-neutral-700 disabled:text-black/30 motion-reduce:transition-none dark:text-neutral-200 dark:hover:text-neutral-300 dark:focus:text-neutral-300 sm:px-2 [&.active]:text-black/90 dark:[&.active]:text-zinc-400"
                            href="{{ route('admin.dashboard') }}" data-te-nav-link-ref>Overview</a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Dashboards</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.labs') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Laboratories</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.items') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Inventaris</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.sets') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Sets</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.softwares') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Softwares and OS</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.repository') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19.906 9c.382 0 .749.057 1.094.162V9a3 3 0 00-3-3h-3.879a.75.75 0 01-.53-.22L11.47 3.66A2.25 2.25 0 009.879 3H6a3 3 0 00-3 3v3.162A3.756 3.756 0 014.094 9h15.812zM4.094 10.5a2.25 2.25 0 00-2.227 2.568l.857 6A2.25 2.25 0 004.951 21H19.05a2.25 2.25 0 002.227-1.932l.857-6a2.25 2.25 0 00-2.227-2.568H4.094z" />
                                </svg>
                            </span>
                            <span>Repository</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.repairs') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Repairs</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.bookings') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Bookings</span>
                        </a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Access
                            Controls</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.roles') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Manage Roles</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.permissions') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Manage Access</span>
                        </a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Academic
                            & Periods</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.matkul') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M11.25 4.533A9.707 9.707 0 006 3a9.735 9.735 0 00-3.25.555.75.75 0 00-.5.707v14.25a.75.75 0 001 .707A8.237 8.237 0 016 18.75c1.995 0 3.823.707 5.25 1.886V4.533zM12.75 20.636A8.214 8.214 0 0118 18.75c.966 0 1.89.166 2.75.47a.75.75 0 001-.708V4.262a.75.75 0 00-.5-.707A9.735 9.735 0 0018 3a9.707 9.707 0 00-5.25 1.533v16.103z" />
                                </svg>
                            </span>
                            <span>Mata Kuliah</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.modules') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625z" />
                                    <path d="M12.971 1.816A5.23 5.23 0 0114.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 013.434 1.279 9.768 9.768 0 00-6.963-6.963z" />
                                </svg>
                            </span>
                            <span>Module</span>
                        </a>
                    </li>
                    <li class="relative pt-4">
                        <span
                            class="px-6 py-4 text-[0.6rem] font-bold uppercase text-gray-600 dark:text-gray-400">Reports
                            and Statistics</span>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.historylabs') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Labs Booking History</span>
                        </a>
                        <a class="flex cursor-pointer items-center truncate rounded-[5px] px-6 py-[0.45rem] text-[0.85rem] text-gray-600 outline-none transition duration-300 ease-linear hover:bg-slate-50 hover:text-inherit hover:outline-none focus:bg-slate-50 focus:text-inherit focus:outline-none active:bg-slate-50 active:text-inherit active:outline-none data-[te-sidenav-state-active]:text-inherit data-[te-sidenav-state-focus]:outline-none motion-reduce:transition-none dark:text-gray-300 dark:hover:bg-white/10 dark:focus:bg-white/10 dark:active:bg-white/10"
                            href="{{ route('admin.historyinventaris') }}" data-te-sidenav-link-ref>
                            <span
                                class="mr-4 [&>svg]:h-3.5 [&>svg]:w-3.5 [&>svg]:text-gray-400 dark:[&>svg]:text-gray-300">
                                <svg class="w-[50px] h-[50px] fill-[#8e8e8e]" viewBox="0 0 576 512"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z">
                                    </path>
                                </svg>
                            </span>
                            <span>Item Booking History</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    {{-- Konten Utama --}}
    <div id="main-content" class="px-3 md:px-8 py-2 md:py-3">
        <div class="mt-3">
            @yield('body')
        </div>
    </div>

    {{-- Custom Modal --}}
    <div id="layout-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="layout-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="layout-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="layout-modal-area"
                class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="layout-modal-title" class="text-xl font-semibold text-gray-900"></h3>
                    <button id="layout-modal-close-button" type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div id="layout-modal-body" class="p-6 space-y-6 overflow-y-auto">
                </div>
                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b">
                    <button id="layout-modal-footer-close-button" type="button"
                        class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>

    <script>
        // Setup CSRF untuk JQuery AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // =================================================================
        // Fungsi Utility (Loading & Toast)
        // =================================================================
        function showLoading(title = 'Loading...', text = 'silakan tunggu...') {
            Swal.fire({
                title: title,
                text: text,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        function showLoadingToast(message = 'Loading...') {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 10000
            });

            toast.fire({
                icon: 'info',
                title: message,
                didOpen: () => Swal.showLoading()
            });
        }

        function showToast(title, message = '', type = 'success', autoHideTimeout = 3000) {
            const iconMap = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: autoHideTimeout,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                },
                customClass: {
                    popup: 'swal2-popup swal2-toast-custom'
                }
            });

            Toast.fire({
                icon: iconMap[type] || 'success',
                title: title,
                text: message
            });
        }

        /**
         * -----------------------------------------------------------------
         * CONTOH PENGGUNAAN showToast()
         * -----------------------------------------------------------------
         * showToast('Berhasil!', 'Data telah disimpan', 'success');
         * showToast('Error!', 'Gagal menyimpan data', 'error');
         * showToast('Peringatan', 'Periksa data Anda', 'warning');
         * showToast('Info', 'Proses sedang berjalan', 'info');
         */

        // Event Listener untuk semua fungsionalitas layout
        document.addEventListener('DOMContentLoaded', function() {

            const layoutModal = document.getElementById('layout-modal');
            const layoutModalTitle = document.getElementById('layout-modal-title');
            const layoutModalBody = document.getElementById('layout-modal-body');
            const overlay = document.getElementById('layout-modal-overlay');
            const closeButtonHeader = document.getElementById('layout-modal-close-button');
            const closeButtonFooter = document.getElementById('layout-modal-footer-close-button');

            window.showLayoutModal = (title, bodyHTML) => {
                if (layoutModal && layoutModalTitle && layoutModalBody) {
                    layoutModalTitle.textContent = title;
                    layoutModalBody.innerHTML = bodyHTML;
                    layoutModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            };

            window.hideLayoutModal = () => {
                if (layoutModal) {
                    layoutModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };

            if (closeButtonHeader) closeButtonHeader.addEventListener('click', window.hideLayoutModal);
            if (closeButtonFooter) closeButtonFooter.addEventListener('click', window.hideLayoutModal);

            if (overlay) {
                overlay.addEventListener('click', function(event) {
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

            // ============================================
            // LOGIKA RESIZE SIDEBAR (FIXED VERSION)
            // ============================================
            const resizer = document.getElementById('sidenav-resizer');
            const sidebar = document.getElementById('sidenav-8');
            const mainContent = document.getElementById('main-content');

            const minWidth = 220; // Lebar minimal sidebar (px)
            const maxWidth = 500; // Lebar maksimal sidebar (px)

            // Load saved width dari localStorage
            const savedWidth = localStorage.getItem('sidebarWidth');
            if (savedWidth) {
                const newWidth = Math.max(minWidth, Math.min(parseInt(savedWidth), maxWidth));
                document.documentElement.style.setProperty('--sidebar-width', newWidth + 'px');
            }

            // Event handler saat drag
            const doResize = (e) => {
                const newWidth = Math.max(minWidth, Math.min(e.clientX, maxWidth));
                document.documentElement.style.setProperty('--sidebar-width', newWidth + 'px');
            };

            // Event handler saat selesai drag
            const stopResize = () => {
                document.removeEventListener('mousemove', doResize);
                document.removeEventListener('mouseup', stopResize);
                document.body.classList.remove('is-resizing');

                const currentWidth = document.documentElement.style.getPropertyValue('--sidebar-width');
                localStorage.setItem('sidebarWidth', parseInt(currentWidth));
            };

            // Event handler saat mulai drag
            if (resizer) {
                resizer.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    document.addEventListener('mousemove', doResize);
                    document.addEventListener('mouseup', stopResize);
                    document.body.classList.add('is-resizing');
                });
            }

            // Toggle Nav Group Function
            window.toggleNavGroup = function(header) {
                const content = header.nextElementSibling;
                const toggle = header.querySelector('.nav-group-toggle');

                if (content.classList.contains('expanded')) {
                    content.classList.remove('expanded');
                    toggle.classList.remove('expanded');
                } else {
                    content.classList.add('expanded');
                    toggle.classList.add('expanded');
                }
            };
        });
    </script>

    @yield('script')

    {{-- Script untuk Flash Session --}}
    @if (session('error-access'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak',
                text: '{{ session('error-access') }}',
                confirmButtonText: 'OK'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if (session('success'))
        <script>
            showToast('Berhasil!', '{{ session('success') }}', 'success');
        </script>
    @endif
</body>

</html>
