<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opsi Path Jadwal - Semester {{ $selectedSemester }} ({{ $period->name }})</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 10px !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen flex flex-col font-sans antialiased">

<div class="min-h-screen flex flex-col w-full">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <a href="{{ $backUrl }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Kembali ke Grid Jadwal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-base font-bold text-slate-900 leading-tight">Opsi Path Kombinasi Jadwal</h1>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">
                            Semester {{ $selectedSemester }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">Laboratorium Teknik Informatika &bull; Periode {{ $period->name }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <button onclick="window.print()" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 transition">
                    <svg class="w-4 h-4 mr-1 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Path</span>
                </button>
                <a href="{{ $backUrl }}" class="inline-flex items-center px-3.5 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                    <span>Kembali ke Grid Jadwal</span>
                </a>
            </div>
        </div>

        <!-- Semester Selector Bar -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 border-t border-slate-100 flex items-center justify-between gap-2 overflow-x-auto">
            <span class="text-xs font-semibold text-slate-600 flex-shrink-0">Pilih Semester:</span>
            <div class="flex items-center space-x-1.5 overflow-x-auto">
                @foreach([1, 2, 3, 4, 5, 6, 7, 8] as $sem)
                    <a href="?semester={{ $sem }}" 
                       class="px-3 py-1 rounded-lg text-xs font-bold transition flex-shrink-0 {{ $selectedSemester === $sem ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Semester {{ $sem }}
                    </a>
                @endforeach
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 space-y-5">
        <!-- Summary Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <h2 class="text-base font-bold text-slate-900">
                        Hasil Analisis Opsi Path Mahasiswa Semester {{ $selectedSemester }}
                    </h2>
                </div>
                <p class="text-xs text-slate-500">
                    Kombinasi pemilihan seksi kelas dan praktikum/responsi untuk semua matkul wajib semester {{ $selectedSemester }} yang tidak saling bentrok waktu.
                </p>
            </div>

            <div class="flex items-center space-x-3 flex-shrink-0">
                <div class="px-4 py-2 rounded-xl bg-indigo-50 border border-indigo-200 text-center">
                    <span class="block text-xl font-black text-indigo-700">{{ $pathResult['total_paths'] }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500">Total Path Valid</span>
                </div>
                <div class="px-4 py-2 rounded-xl bg-slate-100 border border-slate-200 text-center">
                    <span class="block text-xl font-black text-slate-700">{{ count($pathResult['courses'] ?? []) }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Matkul Wajib</span>
                </div>
            </div>
        </div>

        <!-- Global Warnings if any -->
        @if(!empty($pathResult['warnings']))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                <div class="font-bold flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Catatan Semester:</span>
                </div>
                @foreach($pathResult['warnings'] as $w)
                    <p class="ml-5">&bull; {{ $w }}</p>
                @endforeach
            </div>
        @endif

        <!-- Courses Covered in this Semester -->
        @if(!empty($pathResult['courses']))
            <div class="bg-white rounded-xl p-3 border border-slate-200 text-xs flex flex-wrap items-center gap-2">
                <span class="font-bold text-slate-700 mr-1">Mata Kuliah Wajib Terhitung:</span>
                @foreach($pathResult['courses'] as $c)
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-semibold border border-slate-200">
                        {{ $c['name'] }}
                    </span>
                @endforeach
            </div>
        @endif

        <!-- Paths Grid List -->
        @if($pathResult['total_paths'] > 0)
            <div class="space-y-4">
                @foreach($pathResult['paths'] as $index => $path)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition hover:shadow-md">
                        <!-- Path Header -->
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center space-x-2.5">
                                <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                                    {{ $index + 1 }}
                                </span>
                                <h3 class="font-bold text-sm text-slate-800">
                                    Opsi Path #{{ $index + 1 }}
                                </h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Bebas Bentrok
                                </span>
                            </div>

                            @if(!empty($path['warnings']))
                                <span class="text-[11px] font-semibold text-amber-600">
                                    Ada {{ count($path['warnings']) }} catatan minor
                                </span>
                            @endif
                        </div>

                        <!-- Path Content Sections -->
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($path['sections'] as $sec)
                                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/50 space-y-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-900 truncate mr-2">{{ $sec['course_name'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 font-bold text-[10px] flex-shrink-0">
                                            Seksi {{ $sec['label'] }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        ID Seksi: #{{ $sec['id'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Path specific warnings if any -->
                        @if(!empty($path['warnings']))
                            <div class="px-4 py-2.5 bg-amber-50/60 border-t border-amber-100 text-[11px] text-amber-800 space-y-0.5">
                                @foreach($path['warnings'] as $pw)
                                    <div>&bull; {{ $pw }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl p-10 text-center border border-slate-200 space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-bold text-slate-800">Tidak Ditemukan Opsi Path Tanpa Bentrok</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Untuk Semester {{ $selectedSemester }}, belum ada kombinasi seksi yang sepenuhnya bebas dari tabrakan jadwal atau belum ada matkul wajib yang terjadwal.
                </p>
                <div class="pt-2">
                    <a href="{{ $backUrl }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                        Kembali ke Jadwal Utama
                    </a>
                </div>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white no-print">
        <p>Laboratorium Teknik Informatika &copy; {{ date('Y') }} — Analisis Path Jadwal {{ $period->name }}</p>
    </footer>
</div>
</body>
</html>
