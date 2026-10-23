@extends('layouts.app')

@section('title', 'Pengaturan Kelas - ' . $period->name)
@section('page_title', 'Pengaturan Kelas')
@section('active_period', $period->name)

@section('content')
<div x-data="importManager()" class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header banner -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 dark:text-white">Pengaturan & Manajemen Kelas</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Kelola jadwal pertemuan kuliah teori, seksi, dan kapasitas mahasiswa untuk periode <strong>{{ $period->name }}</strong>. Anda dapat mengimpor berkas Excel atau menambah dan mengedit seksi kelas secara manual.
            </p>
        </div>
        <div class="flex items-center space-x-3 flex-shrink-0">
            <a href="{{ route('import.template') }}" 
               class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Template Excel
            </a>
            <button @click="showManualModal = true"
                    class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Kelas Manual
            </button>
        </div>
    </div>

    <!-- Upload & Drop Area -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
        <label class="block text-sm font-bold text-slate-900 dark:text-white mb-2">Impor Berkas Excel (.xlsx, .xls)</label>
        
        <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-xl p-6 text-center hover:border-indigo-500 transition cursor-pointer relative"
             @dragover.prevent="isDragging = true"
             @dragleave.prevent="isDragging = false"
             @drop.prevent="handleFileDrop($event)"
             :class="{ 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-950/20': isDragging }">
            
            <input type="file" @change="handleFileSelect($event)" accept=".xlsx, .xls" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">

            <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Klik untuk memilih file Excel</span>
                    <span class="text-xs text-slate-500"> atau seret file ke area ini</span>
                </div>
                <span class="text-[11px] text-slate-400" x-text="fileName ? 'File terpilih: ' + fileName : 'Format didukung: Sheet JADWAL atau format kolom datar'"></span>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between" x-show="selectedFile">
            <span class="text-xs font-medium text-slate-600 dark:text-slate-300" x-text="'File terpilih: ' + fileName"></span>
            <button @click="uploadAndPreview()" :disabled="isLoading"
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-sm transition disabled:opacity-50 inline-flex items-center cursor-pointer">
                <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span x-text="isLoading ? 'Membaca File...' : 'Pratinjau Hasil Parsing'"></span>
            </button>
        </div>
    </div>

    <!-- Preview Modal / Section -->
    <template x-if="previewData">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Pratinjau Hasil Pembacaan Excel</h3>
                    <p class="text-xs text-slate-500 mt-0.5" x-text="'Ditemukan ' + previewData.total_meetings + ' data jadwal pertemuan kelas.'"></p>
                </div>
                <div class="flex items-center space-x-3">
                    <label class="flex items-center space-x-2 text-xs text-slate-600 dark:text-slate-400 cursor-pointer">
                        <input type="checkbox" x-model="overwriteExisting" class="rounded text-indigo-600 focus:ring-indigo-500">
                        <span>Timpa data kelas periode ini</span>
                    </label>
                    <button @click="commitImport()" :disabled="isCommitting"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition disabled:opacity-50 inline-flex items-center cursor-pointer">
                        <svg x-show="isCommitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span x-text="isCommitting ? 'Menyimpan...' : 'Konfirmasi & Simpan ke Database'"></span>
                    </button>
                </div>
            </div>

            <!-- Warnings if any -->
            <template x-if="previewData.warnings && previewData.warnings.length > 0">
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 text-xs text-amber-800 dark:text-amber-200 space-y-1">
                    <div class="font-bold flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>Peringatan Pembacaan Baris (Dilewati / Butuh Perhatian):</span>
                    </div>
                    <template x-for="w in previewData.warnings" :key="w">
                        <p class="ml-5" x-text="'• ' + w"></p>
                    </template>
                </div>
            </template>

            <!-- Preview Table -->
            <div class="overflow-x-auto max-h-96 border rounded-xl border-slate-200 dark:border-slate-800">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 sticky top-0">
                        <tr>
                            <th class="p-3">No</th>
                            <th class="p-3">Mata Kuliah (Ternormalisasi)</th>
                            <th class="p-3">Seksi</th>
                            <th class="p-3">Hari</th>
                            <th class="p-3">Jam Pertemuan</th>
                            <th class="p-3">Ruangan</th>
                            <th class="p-3">Teks Asli</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        <template x-for="(m, i) in previewData.preview_meetings" :key="i">
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50" :class="m.is_saturday ? 'bg-amber-50/30' : ''">
                                <td class="p-3 font-semibold text-slate-400" x-text="i + 1"></td>
                                <td class="p-3 font-bold text-slate-800 dark:text-slate-200" x-text="m.course_name"></td>
                                <td class="p-3 font-semibold" x-text="m.section_label"></td>
                                <td class="p-3">
                                    <span x-text="m.day" :class="m.is_saturday ? 'text-amber-600 font-bold' : ''"></span>
                                    <template x-if="m.is_saturday">
                                        <span class="ml-1 text-[9px] bg-amber-100 text-amber-800 px-1 rounded">Sabtu</span>
                                    </template>
                                </td>
                                <td class="p-3" x-text="formatMin(m.start_min) + ' - ' + formatMin(m.end_min)"></td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold"
                                          :class="m.room_code ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'"
                                          x-text="m.room_code ? 'Lab ' + m.room_code : (m.room_label || 'Ruang Kelas')">
                                    </span>
                                </td>
                                <td class="p-3 text-[11px] text-slate-400 truncate max-w-xs" x-text="m.raw_text"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <!-- Current Classes Table in Period (With Search, Filter, Add Manual & Edit Manual) -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Daftar Seksi & Pertemuan Kelas Saat Ini (Periode {{ $period->name }})</h3>
                <span class="text-xs text-slate-500">Total: {{ $sections->total() }} Seksi Kelas</span>
            </div>

            <!-- Search and Filters Form -->
            <form method="GET" action="{{ route('import.index') }}" class="flex flex-wrap items-center gap-2">
                <!-- Search input -->
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari matakuliah / seksi..." 
                           class="pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500 w-44 sm:w-56">
                    <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Semester filter -->
                <select name="semester" onchange="this.form.submit()" class="py-1.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="">Semua Sem</option>
                    @foreach([1, 2, 3, 4, 5, 6, 7, 8] as $s)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>Sem {{ $s }}</option>
                    @endforeach
                </select>

                <!-- Day filter -->
                <select name="day" onchange="this.form.submit()" class="py-1.5 text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-800 dark:text-slate-200">
                    <option value="">Semua Hari</option>
                    @foreach($days as $d)
                        <option value="{{ $d }}" {{ $selectedDay == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>

                @if($search || $semester || $selectedDay)
                    <a href="{{ route('import.index') }}" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition" title="Reset Filter">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto border rounded-xl border-slate-200 dark:border-slate-800">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    <tr>
                        <th class="p-3">Mata Kuliah</th>
                        <th class="p-3 text-center">Semester</th>
                        <th class="p-3 text-center">Seksi</th>
                        <th class="p-3 text-center">Mahasiswa</th>
                        <th class="p-3">Jadwal Pertemuan Kelas</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($sections as $sec)
                        @php
                            $primaryMeeting = $sec->meetings->first();
                            $meetingData = $primaryMeeting ? [
                                'day' => $primaryMeeting->day,
                                'start_time' => sprintf('%02d:%02d', floor($primaryMeeting->start_min / 60), $primaryMeeting->start_min % 60),
                                'end_time' => sprintf('%02d:%02d', floor($primaryMeeting->end_min / 60), $primaryMeeting->end_min % 60),
                                'room_id' => $primaryMeeting->room_id,
                            ] : [
                                'day' => 'Senin',
                                'start_time' => '07:30',
                                'end_time' => '09:30',
                                'room_id' => null,
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50">
                            <td class="p-3">
                                <span class="font-bold text-slate-900 dark:text-white">{{ $sec->course->name }}</span>
                                <span class="block text-[10px] text-slate-400 font-mono">{{ $sec->course->code }}</span>
                            </td>
                            <td class="p-3 text-center font-semibold text-slate-600 dark:text-slate-300">
                                Sem {{ $sec->course->curriculum_semester }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold">
                                    {{ $sec->label }}
                                </span>
                            </td>
                            <td class="p-3 text-center">{{ $sec->enrollment }} mhs</td>
                            <td class="p-3 space-y-1">
                                @forelse($sec->meetings as $m)
                                    <div class="text-[11px] flex items-center space-x-2">
                                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $m->day }}</span>
                                        <span class="font-mono text-slate-600 dark:text-slate-400">{{ sprintf('%02d:%02d - %02d:%02d', floor($m->start_min / 60), $m->start_min % 60, floor($m->end_min / 60), $m->end_min % 60) }}</span>
                                        <span class="text-slate-500">({{ $m->room ? $m->room->name : ($m->room_label ?: 'Ruang Kelas') }})</span>
                                    </div>
                                @empty
                                    <span class="text-slate-400 italic">Belum ada pertemuan</span>
                                @endforelse
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Edit Button -->
                                    <button @click="openEditModal({{ json_encode([
                                        'id' => $sec->id,
                                        'course_id' => $sec->course_id,
                                        'label' => $sec->label,
                                        'enrollment' => $sec->enrollment,
                                        'day' => $meetingData['day'],
                                        'start_time' => $meetingData['start_time'],
                                        'end_time' => $meetingData['end_time'],
                                        'room_id' => $meetingData['room_id'],
                                    ]) }})" 
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/60 dark:text-indigo-300 transition cursor-pointer">
                                        Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('import.section.destroy', $sec) }}" method="POST" onsubmit="return confirm('Hapus seksi kelas {{ $sec->course->name }} ({{ $sec->label }})?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition cursor-pointer">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">
                                Tidak ada data seksi kelas yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sections->links() }}
        </div>
    </div>

    <!-- Manual Create Meeting Modal -->
    <div x-cloak x-show="showManualModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4"
             @click.outside="showManualModal = false">
            <div class="flex items-center justify-between border-b pb-3 border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Tambah Jadwal Kelas Manual</h3>
                <button @click="showManualModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('import.meeting.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold mb-1">Mata Kuliah</label>
                    <select name="course_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (Sem {{ $c->curriculum_semester }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Seksi (Label)</label>
                        <input type="text" name="label" required placeholder="mis. A" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jumlah Mahasiswa (Enrollment)</label>
                        <input type="number" name="enrollment" value="30" min="1" max="100" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Hari</label>
                        <select name="day" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            @foreach($days as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jam Mulai (HH:MM)</label>
                        <input type="text" name="start_time" placeholder="07:30" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jam Selesai (HH:MM)</label>
                        <input type="text" name="end_time" placeholder="09:30" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Ruangan Lab (Opsional)</label>
                    <select name="room_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">-- Ruang Kelas Non-Lab --</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}">{{ $r->code }} - {{ $r->name }} (Kap: {{ $r->capacity }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showManualModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold cursor-pointer">
                        Simpan Pertemuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Section Modal -->
    <div x-cloak x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4"
             @click.outside="showEditModal = false">
            <div class="flex items-center justify-between border-b pb-3 border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-base text-slate-900 dark:text-white">Edit Seksi & Jadwal Kelas</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="'{{ url('/import/section') }}/' + editForm.id" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold mb-1">Mata Kuliah</label>
                    <select name="course_id" x-model="editForm.course_id" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} (Sem {{ $c->curriculum_semester }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Seksi (Label)</label>
                        <input type="text" name="label" x-model="editForm.label" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jumlah Mahasiswa (Enrollment)</label>
                        <input type="number" name="enrollment" x-model="editForm.enrollment" min="1" max="100" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Hari</label>
                        <select name="day" x-model="editForm.day" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                            @foreach($days as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jam Mulai (HH:MM)</label>
                        <input type="text" name="start_time" x-model="editForm.start_time" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jam Selesai (HH:MM)</label>
                        <input type="text" name="end_time" x-model="editForm.end_time" required class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Ruangan Lab (Opsional)</label>
                    <select name="room_id" x-model="editForm.room_id" class="w-full text-xs rounded-xl border border-slate-300 dark:border-slate-700 dark:bg-slate-800">
                        <option value="">-- Ruang Kelas Non-Lab --</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}">{{ $r->code }} - {{ $r->name }} (Kap: {{ $r->capacity }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 cursor-pointer">
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
function importManager() {
    return {
        isDragging: false,
        selectedFile: null,
        fileName: '',
        isLoading: false,
        isCommitting: false,
        overwriteExisting: true,
        previewData: null,
        showManualModal: false,
        showEditModal: false,
        editForm: {
            id: null,
            course_id: '',
            label: '',
            enrollment: 30,
            day: 'Senin',
            start_time: '07:30',
            end_time: '09:30',
            room_id: ''
        },

        openEditModal(data) {
            this.editForm = { ...data };
            this.showEditModal = true;
        },

        handleFileSelect(event) {
            const files = event.target.files;
            if (files.length > 0) {
                this.selectedFile = files[0];
                this.fileName = this.selectedFile.name;
            }
        },

        handleFileDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.selectedFile = files[0];
                this.fileName = this.selectedFile.name;
            }
        },

        formatMin(min) {
            const h = Math.floor(min / 60);
            const m = min % 60;
            return (h < 10 ? '0' : '') + h + ':' + (m < 10 ? '0' : '') + m;
        },

        async uploadAndPreview() {
            if (!this.selectedFile) return;
            this.isLoading = true;
            this.previewData = null;

            const formData = new FormData();
            formData.append('file', this.selectedFile);

            try {
                const res = await fetch("{{ route('import.preview') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const data = await res.json();
                this.isLoading = false;
                if (data.success) {
                    this.previewData = data;
                } else {
                    alert('Gagal memproses file: ' + data.message);
                }
            } catch (err) {
                this.isLoading = false;
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        },

        async commitImport() {
            this.isCommitting = true;
            try {
                const res = await fetch("{{ route('import.commit') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ overwrite: this.overwriteExisting })
                });
                const data = await res.json();
                this.isCommitting = false;
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan: ' + data.message);
                }
            } catch (err) {
                this.isCommitting = false;
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        }
    }
}
</script>
@endpush
