@extends('layouts.app')

@section('title', 'Manajemen Ruangan Lab')
@section('page_title', 'Manajemen Ruangan Laboratorium')

@section('content')
<div x-data="roomManager()" class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header banner -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Daftar Ruangan Laboratorium</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Lab internal (diprioritaskan) dan lab eksternal (Puskom). Kolom grid mewakili ruangan ini. Flag <code>manual_only</code> menandakan ruangan hanya untuk penataan manual (mis. VR, MM).
            </p>
        </div>
        <button @click="openAddModal()" 
                class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Ruangan
        </button>
    </div>

    <!-- Rooms Table -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="overflow-x-auto border rounded-xl border-slate-200 dark:border-slate-800">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Nama Ruangan</th>
                        <th class="p-3 text-center">Tipe</th>
                        <th class="p-3 text-center">Kapasitas</th>
                        <th class="p-3 text-center">Status Grid</th>
                        <th class="p-3 text-center">Aktif</th>
                        <th class="p-3">Catatan</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($rooms as $room)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                            <td class="p-3 font-bold text-slate-900 dark:text-white text-sm">{{ $room->code }}</td>
                            <td class="p-3 font-medium">{{ $room->name }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold"
                                      class="{{ $room->type === 'internal' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                    {{ ucfirst($room->type) }}
                                </span>
                            </td>
                            <td class="p-3 text-center font-bold">{{ $room->capacity }}</td>
                            <td class="p-3 text-center">
                                @if($room->is_manual_only)
                                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold text-[10px]">Manual Only</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold text-[10px]">Otomatis & Manual</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($room->is_active)
                                    <span class="text-emerald-600 font-bold">✓</span>
                                @else
                                    <span class="text-rose-600 font-bold">✗</span>
                                @endif
                            </td>
                            <td class="p-3 text-slate-400 text-[11px]">{{ $room->notes ?: '-' }}</td>
                            <td class="p-3 text-right space-x-2">
                                <button @click="editRoom({{ json_encode($room) }})" class="text-indigo-600 hover:text-indigo-800 font-semibold">Ubah</button>
                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="return confirm('Hapus/nonaktifkan ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-slate-400">Belum ada data ruangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form Room -->
    <div x-cloak x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between border-b pb-3 border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-white" x-text="isEditing ? 'Ubah Ruangan' : 'Tambah Ruangan Baru'"></h3>
                <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="formAction" method="POST" class="space-y-4 text-xs">
                @csrf
                <template x-if="isEditing">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Kode Ruangan</label>
                        <input type="text" name="code" x-model="form.code" required placeholder="mis. PG" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="capacity" x-model.number="form.capacity" min="1" max="200" required class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Nama Ruangan</label>
                    <input type="text" name="name" x-model="form.name" required placeholder="mis. Pemrograman dan Game, P.206" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Tipe Ruangan</label>
                        <select name="type" x-model="form.type" required class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            <option value="internal">Internal (Lab Prodi)</option>
                            <option value="external">Eksternal (Puskom)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Catatan Tambahan</label>
                        <input type="text" name="notes" x-model="form.notes" placeholder="Opsional" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_manual_only" value="1" x-model="form.is_manual_only" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="font-semibold text-slate-800 dark:text-slate-200">Manual Only (Tidak pernah diisi otomatis)</span>
                    </label>

                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="font-semibold text-slate-800 dark:text-slate-200">Ruangan Aktif</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
                        Simpan Ruangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function roomManager() {
    return {
        showModal: false,
        isEditing: false,
        formAction: "{{ route('rooms.store') }}",
        form: {
            code: '',
            name: '',
            capacity: 30,
            type: 'internal',
            notes: '',
            is_manual_only: false,
            is_active: true
        },

        openAddModal() {
            this.isEditing = false;
            this.formAction = "{{ route('rooms.store') }}";
            this.form = {
                code: '',
                name: '',
                capacity: 30,
                type: 'internal',
                notes: '',
                is_manual_only: false,
                is_active: true
            };
            this.showModal = true;
        },

        editRoom(room) {
            this.isEditing = true;
            this.formAction = `/rooms/${room.id}`;
            this.form = {
                code: room.code,
                name: room.name,
                capacity: room.capacity,
                type: room.type,
                notes: room.notes || '',
                is_manual_only: !!room.is_manual_only,
                is_active: !!room.is_active
            };
            this.showModal = true;
        }
    }
}
</script>
@endpush
