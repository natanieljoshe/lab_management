@extends('layouts.app')

@section('title', 'Pengaturan Periode')
@section('page_title', 'Pengaturan Periode Akademik')

@section('content')
<div class="p-6 max-w-4xl mx-auto space-y-6">
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Periode Semester</h2>
        <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
            Pilih periode akademik yang aktif. Semua data jadwal kelas, konfigurasi, dan penataan praktikum terikat pada periode aktif.
        </p>
    </div>

    <!-- Active Periods Table -->
    <div x-data="{ editingPeriodId: null, editingPeriodName: '' }" class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="overflow-x-auto border rounded-xl border-slate-200 dark:border-slate-800">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <tr>
                        <th class="p-3">Nama Periode</th>
                        <th class="p-3 text-center">Jumlah Seksi</th>
                        <th class="p-3 text-center">Jumlah Sesi Praktikum</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($periods as $p)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                            <td class="p-3">
                                <template x-if="editingPeriodId !== {{ $p->id }}">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $p->name }}</span>
                                        <button @click="editingPeriodId = {{ $p->id }}; editingPeriodName = '{{ $p->name }}'" 
                                                class="text-slate-400 hover:text-indigo-600 p-1" title="Ubah Nama">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="editingPeriodId === {{ $p->id }}">
                                    <form action="{{ route('settings.period.update', $p) }}" method="POST" class="flex items-center space-x-2">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" x-model="editingPeriodName" required
                                               class="text-xs px-2 py-1 rounded border border-indigo-400 dark:bg-slate-800">
                                        <button type="submit" class="px-2 py-1 rounded bg-indigo-600 text-white font-semibold text-[11px]">Simpan</button>
                                        <button type="button" @click="editingPeriodId = null" class="px-2 py-1 rounded bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 text-[11px]">Batal</button>
                                    </form>
                                </template>
                            </td>
                            <td class="p-3 text-center">{{ $p->class_sections_count }}</td>
                            <td class="p-3 text-center">{{ $p->lab_sessions_count }}</td>
                            <td class="p-3 text-center">
                                @if($p->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-[10px]">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium text-[10px]">Nonaktif</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(!$p->is_active)
                                        <form action="{{ route('settings.period.active', $p) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-xs transition">
                                                Jadikan Aktif
                                            </button>
                                        </form>

                                        <form action="{{ route('settings.period.destroy', $p) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode {{ $p->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Hapus Periode">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs font-medium">Sedang Digunakan</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Period Form -->
    <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-5">
        <div class="border-b pb-3 border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                <span>Tambah Periode Baru</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Daftarkan periode akademik baru untuk mengelola seksi kelas dan penataan praktikum secara independen.
            </p>
        </div>

        <form action="{{ route('settings.period.store') }}" method="POST" class="space-y-5 text-xs">
            @csrf
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                    Nama Periode
                </label>
                <div class="relative">
                    <input type="text" name="name" required placeholder="mis. Genap 2026/2027"
                           class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-xs">
                </div>
                <p class="text-[11px] text-slate-400">Contoh format: <strong>Gasal 2026/2027</strong> atau <strong>Genap 2026/2027</strong></p>
            </div>

            <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:bg-slate-50 transition">
                <label class="flex items-center space-x-3 cursor-pointer select-none">
                    <input type="checkbox" name="is_active" value="1" 
                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <div>
                        <span class="block text-xs font-bold text-slate-800 dark:text-slate-200">Langsung aktifkan periode ini</span>
                        <span class="block text-[11px] text-slate-500">Jika dicentang, periode ini akan langsung menjadi periode aktif di seluruh sistem.</span>
                    </div>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center space-x-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold shadow-md shadow-indigo-600/20 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Simpan & Buat Periode Baru</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
