<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use App\Models\Module;
use App\Models\Folders;
use App\Models\Period;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatkulController extends Controller
{
    public function index(Request $request)
    {
        $periods = Period::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();
        $activePeriod = Period::getCurrentPeriod();
        $selectedPeriod = ($request->filled('period_id') ? $periods->firstWhere('id', $request->query('period_id')) : null)
            ?? $activePeriod
            ?? $periods->first();

        $matkuls = Matkul::with([
            'rootFolder',
            'matkulConfigs' => fn ($q) => $q->where('period_id', $selectedPeriod?->id),
        ])->get();

        return view('admin.matkul', compact('matkuls', 'periods', 'activePeriod', 'selectedPeriod'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:matkuls,kode',
            'nama' => 'required',
            'sks' => 'required|in:2,3',
            'open_file_access' => 'boolean',
            'curriculum_semester' => 'nullable|integer|between:1,8',
            'is_required' => 'boolean',
        ]);

        $folder = Folders::create([
            'name' => $validated['kode'],
            'parent_id' => null,
            'full_path' => '/matkul/' . $validated['kode'],
            'is_root' => true,
            'creator_id' => Auth::user()->id
        ]);

        $matkul = Matkul::create([
            'kode' => $validated['kode'],
            'nama' => $validated['nama'],
            'sks' => $validated['sks'],
            'root_folder_id' => $folder->id,
            'open_file_access' => $validated['open_file_access'] ?? false,
            'curriculum_semester' => $validated['curriculum_semester'] ?? null,
            'is_required' => $validated['is_required'] ?? true,
        ]);

        return response()->json(['success' => true, 'matkul' => $matkul->load('rootFolder')]);
    }

    public function update(Request $request, Matkul $matkul)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:matkuls,kode,' . $matkul->id,
            'nama' => 'required',
            'sks' => 'required|in:2,3',
            'open_file_access' => 'boolean',
            'curriculum_semester' => 'nullable|integer|between:1,8',
            'is_required' => 'boolean',
        ]);

        $matkul->update($validated);

        if ($matkul->rootFolder && $matkul->rootFolder->name !== $validated['kode']) {
            $matkul->rootFolder->update([
                'name' => $validated['kode'],
                'full_path' => '/matkul/' . $validated['kode']
            ]);
        }

        return response()->json(['success' => true, 'matkul' => $matkul->load('rootFolder')]);
    }

    public function destroy(Matkul $matkul)
    {
        $matkul->delete();
        return response()->json(['success' => true]);
    }

    public function modules(Matkul $matkul)
    {
        $modules = $matkul->modules()->with(['file', 'author', 'lastEditor'])->get();
        $modulesFolder = Folders::where('name', 'Modules')
            ->whereHas('parent', fn($q) => $q->whereNull('parent_id'))
            ->first();
        
        return view('admin.modules', compact('matkul', 'modules', 'modulesFolder'));
    }

    public function modulesJson(Matkul $matkul)
    {
        return response()->json([
            'success' => true,
            'modules' => $matkul->modules()->with('file', 'author')->get(),
        ]);
    }

    public function storeModule(Request $request, Matkul $matkul)
    {
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'workload_hours' => 'nullable|integer|min:0'
        ]);

        $module = $matkul->modules()->create([
            'file_id' => $validated['file_id'],
            'author_id' => Auth::user()->id,
            'workload_hours' => $validated['workload_hours'] ?? 0,
            'last_edited_at' => now(),
            'last_edited_by' => Auth::user()->id,
            'active' => true
        ]);

        return response()->json(['success' => true, 'module' => $module->load(['file', 'author', 'lastEditor'])]);
    }

    public function updateModule(Request $request, Matkul $matkul, $moduleId)
    {
        $validated = $request->validate([
            'workload_hours' => 'nullable|integer|min:0',
            'active' => 'boolean'
        ]);

        $module = $matkul->modules()->findOrFail($moduleId);
        
        if (isset($validated['active']) && $validated['active']) {
            $matkul->modules()->where('id', '!=', $moduleId)->update(['active' => false]);
        }
        
        $module->update([
            'workload_hours' => $validated['workload_hours'] ?? $module->workload_hours,
            'active' => $validated['active'] ?? $module->active,
            'last_edited_at' => now(),
            'last_edited_by' => Auth::user()->id
        ]);

        return response()->json(['success' => true, 'module' => $module->load(['file', 'author', 'lastEditor'])]);
    }

    public function destroyModule(Matkul $matkul, $moduleId)
    {
        $module = $matkul->modules()->findOrFail($moduleId);
        $wasActive = $module->active;
        
        $module->deleted_by = Auth::user()->id;
        $module->save();
        $module->delete();
        
        if ($wasActive) {
            $oldestVersion = $matkul->modules()
                ->orderBy('created_at', 'asc')
                ->first();
            
            if ($oldestVersion) {
                $oldestVersion->update(['active' => true]);
            }
        }
        
        return response()->json(['success' => true]);
    }

    public function allModules()
    {
        $modules = Module::with(['file.folder', 'matkul', 'author', 'lastEditor'])->get();
        $matkulsFolder = Folders::where('name', 'Matkuls')
            ->whereHas('parent', fn($q) => $q->whereNull('parent_id'))
            ->first();
        
        return view('admin.all-modules', compact('modules', 'matkulsFolder'));
    }

    public function getModuleDetails(Matkul $matkul, $moduleId = null)
    {
        if ($moduleId === 'deleted') {
            $deletedVersions = Module::onlyTrashed()
                ->where('matkul_id', $matkul->id)
                ->with(['file', 'author', 'lastEditor', 'deletor'])
                ->orderBy('deleted_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'deleted' => $deletedVersions
            ]);
        }
        
        $activeModule = $matkul->modules()->with(['file.folder', 'author', 'lastEditor'])->find($moduleId);
        
        $olderVersions = Module::withTrashed()
            ->where('matkul_id', $matkul->id)
            ->when($activeModule, fn($q) => $q->where('file_id', '!=', $activeModule->file_id))
            ->whereNull('deleted_at')
            ->with(['file.folder', 'author', 'lastEditor'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $deletedVersions = Module::onlyTrashed()
            ->where('matkul_id', $matkul->id)
            ->with(['file.folder', 'author', 'lastEditor', 'deletor'])
            ->orderBy('deleted_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'active' => $activeModule,
            'older' => $olderVersions,
            'deleted' => $deletedVersions
        ]);
    }
}
