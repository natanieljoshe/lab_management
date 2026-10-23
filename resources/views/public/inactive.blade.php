<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Belum Dipublikasikan - {{ $period->name }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-slate-200 p-8 text-center space-y-4">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mx-auto shadow-inner">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>

        <h1 class="text-xl font-bold text-slate-900">Jadwal Belum Aktif / Dipublikasikan</h1>
        <p class="text-xs text-slate-500 leading-relaxed">
            Draft jadwal <strong>{{ $version->name }}</strong> (Periode {{ $period->name }}) saat ini berstatus <strong>Tidak Aktif</strong> atau telah dinonaktifkan oleh administrator laboratorium.
        </p>

        <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
            <p class="text-[11px] text-slate-400">Silakan hubungi admin laboratorium atau periksa kembali nanti.</p>
            <a href="{{ route('public.schedule') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                Buka Halaman Utama Jadwal Publik
            </a>
        </div>
    </div>
</body>
</html>
