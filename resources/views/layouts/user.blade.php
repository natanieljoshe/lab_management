<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@if (isset($title))
            {{ $title }}
        @else
            @yield('title', 'Gedung P Petra')
        @endif
    </title>
    
    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('assets/utils/icons/logoAja.ico') }}" type="image/x-icon" />

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- CSS Libraries --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tw-elements/dist/css/tw-elements.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css"> 
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.13/dist/lenis.css">

    {{-- JS Libraries (Core) --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    
    {{-- Tailwind Configuration (Tetap menggunakan warna Petra user) --}}
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                fontFamily: {
                    sans: ["Open Sans", "sans-serif"],
                    body: ["Open Sans", "sans-serif"],
                    mono: ["ui-monospace", "monospace"],
                    roboto: ["Roboto", "sans-serif"],
                },
                extend: {
                    colors: {
                        petra: {
                            blue: '#003366',    
                            yellow: '#FFC107',  
                            gray: '#F8F9FA',    
                            darkgray: '#343A40',
                            wood: '#D2B48C'     
                        }
                    }
                }
            }
        }
    </script>

    {{-- JS Libraries (Utilities) --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    {{-- GSAP --}}
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

    {{-- Lenis (Smooth Scroll) --}}
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>

    {{-- ========================================================= --}}
    {{-- STYLE DARI ADMIN (FIXED VERSION UNTUK SWAL & TOM SELECT)  --}}
    {{-- ========================================================= --}}
    <style>
        /* --- TOM SELECT STYLES (Diambil dari Admin agar konsisten) --- */
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

        .ts-wrapper input { width: 100%; }

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

        /* Item/Pill Styling */
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

        .ts-dropdown .create { color: #2563eb; }

        /* --- SWEETALERT2 STYLES (Diambil dari Admin - Fixed Modern Colorful) --- */
        .swal2-container { z-index: 9997 !important; }

        .swal2-popup {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85)) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-radius: 20px !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.1) inset !important;
            padding: 2rem !important;
        }

        .dark .swal2-popup {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.95), rgba(31, 41, 55, 0.85)) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Base Toast Style */
        .swal2-toast-custom.swal2-popup {
            max-width: 400px !important;
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border-radius: 16px !important;
            padding: 1rem 1.25rem !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.18) inset !important;
            border: 2px solid !important;
        }

        /* SUCCESS TOAST - Hijau */
        .swal2-toast-custom.swal2-popup.swal2-icon-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95)) !important;
            border-color: rgba(16, 185, 129, 0.5) !important;
        }
        /* ERROR TOAST - Merah */
        .swal2-toast-custom.swal2-popup.swal2-icon-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95)) !important;
            border-color: rgba(239, 68, 68, 0.5) !important;
        }
        /* WARNING TOAST - Kuning/Orange */
        .swal2-toast-custom.swal2-popup.swal2-icon-warning {
            background: linear-gradient(135deg, rgba(251, 146, 60, 0.95), rgba(249, 115, 22, 0.95)) !important;
            border-color: rgba(251, 146, 60, 0.5) !important;
        }
        /* INFO TOAST - Biru */
        .swal2-toast-custom.swal2-popup.swal2-icon-info {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95)) !important;
            border-color: rgba(59, 130, 246, 0.5) !important;
        }

        /* Toast Content Styling */
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
        .swal2-toast-custom .swal2-timer-progress-bar {
            background: rgba(255, 255, 255, 0.5) !important;
        }
        
        /* Modal General Text */
        .swal2-title { color: #1f2937 !important; font-weight: 700 !important; margin-bottom: 1rem !important; }
        .swal2-html-container { color: #4b5563 !important; line-height: 1.6 !important; }
        
        /* Buttons */
        .swal2-confirm {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4) !important;
        }
        .swal2-cancel {
            background: rgba(229, 231, 235, 0.8) !important;
            color: #374151 !important;
        }
        .swal2-deny {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #dc2626 !important;
        }
        
        /* Inputs */
        .swal2-input, .swal2-textarea {
            border: 2px solid rgba(209, 213, 219, 0.5) !important;
            border-radius: 12px !important;
        }
        .swal2-input:focus, .swal2-textarea:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
        }

        /* Animation */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .swal2-toast-custom.swal2-show { animation: slideInRight 0.3s ease-out; }

        /* Mobile */
        @media (max-width: 640px) {
            .swal2-popup:not(.swal2-toast-custom) { padding: 1.5rem !important; }
            .swal2-toast-custom.swal2-popup { width: auto !important; margin: 0.5em !important; }
        }
    </style>

    @yield('style')
</head>

<body>
    {{-- Konten Utama --}}
    <div id="main">
        @yield('body')
    </div>

    {{-- Custom Modal Container --}}
    <div id="layout-modal" class="hidden" role="dialog" aria-modal="true" aria-labelledby="layout-modal-title">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-[2000]"></div>
        <div id="layout-modal-overlay" class="fixed inset-0 flex items-center justify-center p-4 z-[2001]">
            <div id="layout-modal-area" class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center p-4 border-b border-gray-200 rounded-t">
                    <h3 id="layout-modal-title" class="text-xl font-semibold text-gray-900"></h3>
                    <button id="layout-modal-close-button" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div id="layout-modal-body" class="p-6 space-y-6 overflow-y-auto"></div>
                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 rounded-b">
                    <button id="layout-modal-footer-close-button" type="button" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Akhir --}}
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/tw-elements.umd.min.js"></script>

    <script>
        // --- INITIALIZATIONS ---
        
        // AOS
        AOS.init({ once: true, duration: 700, offset: 80 });
        
        // GSAP
        gsap.registerPlugin(ScrollTrigger);

        // Lenis (Smooth Scroll)
        const lenis = new Lenis({ duration: 1.2 });
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // jQuery CSRF Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // --- GLOBAL FUNCTIONS (Updated from Admin) ---

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

        // --- MODAL & LAYOUT LOGIC ---
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
                    if (typeof destroyModalLenis === 'function') {
                        destroyModalLenis();
                    }
                    layoutModal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            };

            if (closeButtonHeader) closeButtonHeader.addEventListener('click', window.hideLayoutModal);
            if (closeButtonFooter) closeButtonFooter.addEventListener('click', window.hideLayoutModal);

            if (overlay) {
                overlay.addEventListener('click', function(event) {
                    if (event.target === overlay) window.hideLayoutModal();
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && layoutModal && !layoutModal.classList.contains('hidden')) {
                    window.hideLayoutModal();
                }
            });
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