<?php

namespace App\Http\Controllers;

use App\Models\ClassSection;
use App\Models\Matkul;
use App\Models\Period;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ClassSectionController extends Controller
{
    public function index(Matkul $matkul, Period $period)
    {
        $sections = $matkul->classSections()
            ->where('period_id', $period->id)
            ->orderBy('label')
            ->get(['id', 'label', 'enrollment', 'lecturer']);

        return response()->json([
            'success' => true,
            'sections' => $sections,
        ]);
    }

    public function store(Request $request, Matkul $matkul)
    {
        $validated = $request->validate([
            'period_id'  => 'required|exists:periods,id',
            'label'      => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'enrollment' => 'required|integer|min:0|max:9999',
            'lecturer'   => 'nullable|string|max:255',
        ]);

        try {
            $section = $matkul->classSections()->create([
                'period_id'  => $validated['period_id'],
                'label'      => $validated['label'],
                'enrollment' => $validated['enrollment'],
                'lecturer'   => $validated['lecturer'] ?? null,
            ]);
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000' || ($e->errorInfo[0] ?? null) === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Label sudah dipakai untuk matkul ini di periode tersebut.',
                ], 422);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'section' => $section,
        ]);
    }

    public function update(Request $request, Matkul $matkul, ClassSection $section)
    {
        abort_unless($section->matkul_id === $matkul->id, 404);

        $validated = $request->validate([
            'label'      => ['required', 'string', 'max:10', 'regex:/^[A-Z0-9]+$/'],
            'enrollment' => 'required|integer|min:0|max:9999',
            'lecturer'   => 'nullable|string|max:255',
        ]);

        try {
            $section->update([
                'label'      => $validated['label'],
                'enrollment' => $validated['enrollment'],
                'lecturer'   => $validated['lecturer'] ?? null,
            ]);
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000' || ($e->errorInfo[0] ?? null) === '23000') {
                return response()->json([
                    'success' => false,
                    'message' => 'Label sudah dipakai untuk matkul ini di periode tersebut.',
                ], 422);
            }
            throw $e;
        }

        return response()->json([
            'success' => true,
            'section' => $section,
        ]);
    }

    public function destroy(Matkul $matkul, ClassSection $section)
    {
        abort_unless($section->matkul_id === $matkul->id, 404);

        if ($section->labSessions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas ini sudah punya sesi jadwal; hapus sesi dulu.',
            ], 422);
        }

        $section->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
