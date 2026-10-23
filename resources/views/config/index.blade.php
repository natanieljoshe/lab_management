@extends('layouts.app')

@section('title', 'Atur Praktikum - ' . $period->name)
@section('page_title', 'Atur Praktikum')
@section('active_period', $period->name)

@section('content')
<div x-data="configManager()" class="p-6 max-w-7xl mx-auto space-y-8">
    <!-- Header banner -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Aturan & Durasi Praktikum Mata Kuliah</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Tentukan semester penjadwalan, bobot jam praktikum, responsi, serta mode penjadwalan (otomatis atau manual) untuk periode <strong>{{ $period->name }}</strong>.
            </p>
        </div>
        <button @click="showAddModal = true" 
                class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Konfigurasi Matkul
        </button>
    </div>

    <!-- Config Table -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-200 dark:border-slate-800">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">konfigurasi praktikum dan responsi</h3>
            <span class="text-xs text-slate-500">{{ count($configs) }} Mata Kuliah Dikonfigurasi</span>
        </div>

        <div class="overflow-x-auto border rounded-xl border-slate-200 dark:border-slate-800">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <tr>
                        <th class="p-3">Mata Kuliah</th>
                        <th class="p-3 text-center">Semester Kurikulum</th>
                        <th class="p-3 text-center">Semester Penjadwalan</th>
                        <th class="p-3 text-center">Praktikum (Jam)</th>
                        <th class="p-3 text-center">Responsi (Jam)</th>
                        <th class="p-3 text-center">Mode</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($configs as $cfg)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                            <td class="p-3 font-bold text-slate-900 dark:text-white">
                                {{ $cfg->course->name }}
                                @if($cfg->course->curriculum_semester != $cfg->schedule_semester)
                                    <span class="ml-2 text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold" title="Semester jadwal berbeda dari kurikulum">
                                        Mengulang (Sem {{ $cfg->schedule_semester }})
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 text-center">{{ $cfg->course->curriculum_semester }}</td>
                            <td class="p-3 text-center font-bold text-indigo-600 dark:text-indigo-400">{{ $cfg->schedule_semester }}</td>
                            <td class="p-3 text-center">
                                @if($cfg->practicum_hours > 0)
                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-semibold">
                                        {{ $cfg->practicum_hours }} Jam ({{ $cfg->practicum_hours * 60 }} min)
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($cfg->responsi_hours > 0)
                                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 font-semibold">
                                        {{ $cfg->responsi_hours }} Jam ({{ $cfg->responsi_hours * 60 }} min)
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($cfg->is_manual)
                                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-semibold">Manual</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 font-semibold">Otomatis</span>
                                @endif
                            </td>
                            <td class="p-3 text-right space-x-2">
                                <button @click="editConfig({{ json_encode($cfg) }})" class="text-indigo-600 hover:text-indigo-800 font-semibold">Ubah</button>
                                <form action="{{ route('config.destroy', $cfg) }}" method="POST" class="inline" onsubmit="return confirm('Hapus konfigurasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Belum ada mata kuliah yang dikonfigurasi praktikum/responsi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Inline Enrollment Management -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Jumlah Mahasiswa per Seksi (Enrollment)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Digunakan oleh generator untuk validasi kapasitas ruangan laboratorium.</p>
            </div>
            <button @click="saveEnrollments()" :disabled="isSavingEnrollments"
                    class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition disabled:opacity-50 inline-flex items-center">
                <svg x-show="isSavingEnrollments" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span x-text="isSavingEnrollments ? 'Menyimpan...' : 'Simpan Perubahan Enrollment'"></span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto p-1">
            <template x-for="sec in sections" :key="sec.id">
                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between">
                    <div class="min-w-0 pr-2">
                        <div class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="sec.course.name"></div>
                        <div class="text-[10px] text-slate-500 font-semibold" x-text="'Seksi ' + sec.label"></div>
                    </div>
                    <div class="flex items-center space-x-1.5 flex-shrink-0">
                        <input type="number" min="1" max="100" x-model.number="enrollmentMap[sec.id]"
                               class="w-16 px-2 py-1 text-xs text-center font-bold rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <span class="text-[10px] text-slate-400">mhs</span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Add / Edit Config Modal -->
    <div x-cloak x-show="showAddModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between border-b pb-3 border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-white" x-text="editingConfig ? 'Ubah Konfigurasi' : 'Tambah Konfigurasi Matkul'"></h3>
                <button @click="showAddModal = false; editingConfig = null" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('config.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold mb-1">Mata Kuliah</label>
                    <select name="course_id" x-model="formCourseId" required class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($allCourses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (Kurikulum Sem {{ $c->curriculum_semester }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Semester Penjadwalan</label>
                    <input type="number" name="schedule_semester" x-model.number="formScheduleSemester" min="1" max="8" required
                           class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Contoh: PBO semester kurikulum 2, tapi di gasal diset semester jadwal 3 untuk mahasiswa mengulang.</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Praktikum (Jam)</label>
                        <select name="practicum_hours" x-model.number="formPracticumHours" required class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            <option value="0">0 (Tidak ada)</option>
                            <option value="1">1 Jam (60 menit)</option>
                            <option value="2">2 Jam (120 menit)</option>
                            <option value="3">3 Jam (180 menit)</option>
                            <option value="4">4 Jam (240 menit)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Responsi (Jam)</label>
                        <select name="responsi_hours" x-model.number="formResponsiHours" required class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            <option value="0">0 (Tidak ada)</option>
                            <option value="1">1 Jam (60 menit)</option>
                            <option value="2">2 Jam (120 menit)</option>
                            <option value="3">3 Jam (180 menit)</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="is_manual" value="1" x-model="formIsManual" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="font-semibold text-slate-800 dark:text-slate-200">Mode Manual (Jangan Dijadwalkan Otomatis)</span>
                    </label>
                    <span class="text-[10px] text-slate-400 block mt-1">Sesi akan langsung masuk ke panel Belum Terjadwal dengan alasan "Dijadwalkan manual oleh admin".</span>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showAddModal = false; editingConfig = null" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function configManager() {
    const rawSections = @json($sections);
    const initialEnrollmentMap = {};
    rawSections.forEach(s => { initialEnrollmentMap[s.id] = s.enrollment; });

    return {
        sections: rawSections,
        enrollmentMap: initialEnrollmentMap,
        isSavingEnrollments: false,
        showAddModal: false,
        editingConfig: null,

        formCourseId: '',
        formScheduleSemester: 1,
        formPracticumHours: 3,
        formResponsiHours: 2,
        formIsManual: false,

        editConfig(cfg) {
            this.editingConfig = cfg;
            this.formCourseId = cfg.course_id;
            this.formScheduleSemester = cfg.schedule_semester;
            this.formPracticumHours = cfg.practicum_hours;
            this.formResponsiHours = cfg.responsi_hours;
            this.formIsManual = cfg.is_manual;
            this.showAddModal = true;
        },

        async saveEnrollments() {
            this.isSavingEnrollments = true;
            const payload = Object.keys(this.enrollmentMap).map(id => ({
                id: parseInt(id),
                enrollment: this.enrollmentMap[id]
            }));

            try {
                const res = await fetch("{{ route('config.enrollments') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ enrollments: payload })
                });
                const data = await res.json();
                this.isSavingEnrollments = false;
                if (data.success) {
                    alert(data.message);
                } else {
                    alert('Gagal menyimpan enrollment.');
                }
            } catch (err) {
                this.isSavingEnrollments = false;
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        }
    }
}
</script>
@endpush
