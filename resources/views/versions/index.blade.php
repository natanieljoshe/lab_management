@extends('layouts.app')

@section('title', 'Draft Jadwal - ' . $period->name)
@section('page_title', 'Draft Jadwal')
@section('active_period', $period->name)

@section('content')
<div x-data="versionManager()" class="p-6 max-w-6xl mx-auto space-y-6">
    <!-- Versions / Drafts List -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Draft Jadwal Tersimpan</h3>
                <span class="text-xs text-slate-500">{{ $versions->count() }} Draft Terdaftar</span>
            </div>
            <div>
                <a href="{{ route('public.schedule') }}" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <span>Buka Public View Default</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto border rounded-xl border-slate-200 dark:border-slate-800">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <tr>
                        <th class="p-3">Nama Draft</th>
                        <th class="p-3">Periode</th>
                        <th class="p-3 text-center">Status Publik</th>
                        <th class="p-3">Link Publik</th>
                        <th class="p-3 text-center">Sesi Terjadwal</th>
                        <th class="p-3 text-right">Aksi & Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($versions as $ver)
                        @php
                            $snap = $ver->snapshot_json;
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                            <td class="p-3">
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $ver->name }}</span>
                                    <button @click="openEditSlugModal({{ json_encode(['id' => $ver->id, 'name' => $ver->name, 'slug' => $ver->public_slug ?? '']) }})"
                                            class="text-slate-400 hover:text-indigo-600 p-0.5 transition cursor-pointer" title="Ubah Nama & Slug">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                </div>
                                <span class="block text-[10px] text-slate-400 mt-0.5">{{ $ver->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="p-3 font-semibold text-slate-600 dark:text-slate-300">
                                {{ $ver->period->name ?? '-' }}
                            </td>
                            <td class="p-3 text-center">
                                @if($ver->is_published)
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold text-[10px]">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400 font-semibold text-[10px]">
                                        TIDAK AKTIF
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if($ver->public_slug)
                                    <div class="flex items-center space-x-1.5">
                                        <a href="{{ url('/' . $ver->public_slug) }}" target="_blank"
                                           class="font-mono text-xs font-semibold {{ $ver->is_published ? 'text-indigo-600 hover:text-indigo-800 underline' : 'text-slate-400 line-through' }}">
                                            /{{ $ver->public_slug }}
                                        </a>
                                        <button @click="openEditSlugModal({{ json_encode(['id' => $ver->id, 'name' => $ver->name, 'slug' => $ver->public_slug]) }})" 
                                                class="text-slate-400 hover:text-indigo-600 p-0.5 transition cursor-pointer" title="Ubah Slug Link">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    <button @click="openEditSlugModal({{ json_encode(['id' => $ver->id, 'name' => $ver->name, 'slug' => '']) }})" 
                                            class="text-[11px] text-slate-400 hover:text-indigo-600 italic cursor-pointer">
                                        + Atur Slug Link
                                    </button>
                                @endif
                            </td>
                            <td class="p-3 text-center font-bold text-emerald-600">
                                {{ $snap['placed_count'] ?? '-' }} / {{ $snap['total_sessions'] ?? '-' }}
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Edit Draft in Grid -->
                                    <a href="{{ route('schedule.index', ['version_id' => $ver->id, 'mode' => 'edit']) }}" 
                                       class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-300 transition flex items-center space-x-1"
                                       title="Edit Draft Ini di Grid Jadwal">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        <span>Edit</span>
                                    </a>

                                    <!-- Public View Button for this version -->
                                    @if($ver->public_slug)
                                        <a href="{{ url('/' . $ver->public_slug) }}" target="_blank" 
                                           class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition flex items-center space-x-1"
                                           title="Buka Tampilan Publik Draft Ini">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            <span>Lihat Publik</span>
                                        </a>
                                    @endif

                                    <!-- Toggle Publish Status (Aktif / Tidak Aktif) -->
                                    <form action="{{ route('versions.toggle_publish', $ver) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer {{ $ver->is_published ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}">
                                            {{ $ver->is_published ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <!-- Restore Version -->
                                    <form action="{{ route('versions.restore', $ver) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Pulihkan jadwal ke draft {{ $ver->name }}? Perubahan yang belum disimpan pada jadwal utama akan ditimpa.')">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 text-slate-600 hover:text-indigo-600 text-xs font-semibold transition cursor-pointer" title="Pulihkan ke Jadwal Utama">
                                            Pulihkan
                                        </button>
                                    </form>

                                    <!-- Delete Version -->
                                    <form action="{{ route('versions.destroy', $ver) }}" method="POST" class="inline" onsubmit="return confirm('Hapus draft {{ $ver->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-rose-500 hover:text-rose-700 text-xs font-semibold transition cursor-pointer" title="Hapus Draft">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">
                                Belum ada draft jadwal yang disimpan.<br>
                                Gunakan tombol "Simpan Versi" pada menu Jadwal Utama untuk menyimpan jadwal saat ini sebagai draft.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit Slug Link -->
    <div x-cloak x-show="showSlugModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4"
             @click.outside="showSlugModal = false">
            <div class="flex items-center justify-between border-b pb-3 border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Ubah Draft & Link Publik</h3>
                <button @click="showSlugModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'{{ url('/admin/academics-schedule/versions') }}/' + slugForm.id + '/slug'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">Nama Draft Jadwal</label>
                    <input type="text" name="name" x-model="slugForm.name" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-300">
                        Slug Link Publik
                    </label>
                    <div class="flex items-center">
                        <span class="px-3 py-2 bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-300 dark:border-slate-700 rounded-l-xl text-slate-500 font-mono text-xs">
                            {{ url('/') }}/
                        </span>
                        <input type="text" name="public_slug" x-model="slugForm.slug" required placeholder="gasal20262027"
                               class="w-full px-3 py-2 text-xs rounded-r-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 font-mono">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Hanya huruf, angka, tanda strip (-) dan garis bawah (_).</p>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showSlugModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function versionManager() {
    return {
        showSlugModal: false,
        slugForm: {
            id: null,
            name: '',
            slug: ''
        },

        openEditSlugModal(data) {
            this.slugForm = { ...data };
            this.showSlugModal = true;
        }
    };
}
</script>
@endpush
