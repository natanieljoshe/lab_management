<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use App\Models\MatkulConfig;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MatkulConfigController extends Controller
{
    public function show(Matkul $matkul, Period $period)
    {
        $config = $matkul->matkulConfigs()->where('period_id', $period->id)->first();
        return response()->json(['success' => true, 'config' => $config]);
    }

    public function store(Request $request, Matkul $matkul)
    {
        $validated = $request->validate([
            'period_id' => ['required', 'exists:periods,id',
                Rule::unique('matkul_configs', 'period_id')->where('matkul_id', $matkul->id)],
            'scheduling_semester' => 'nullable|integer|between:1,8',
            'practicum_hours' => 'required|integer|min:0|max:12',
            'responsi_hours' => 'required|integer|min:0|max:12',
            'is_manual' => 'boolean',
        ], ['period_id.unique' => 'Matkul ini sudah punya konfigurasi di periode tersebut.']);

        $config = $matkul->matkulConfigs()->create([
            'period_id' => $validated['period_id'],
            'scheduling_semester' => $validated['scheduling_semester'] ?? null,
            'practicum_hours' => $validated['practicum_hours'],
            'responsi_hours' => $validated['responsi_hours'],
            'is_manual' => $validated['is_manual'] ?? false,
        ]);

        return response()->json(['success' => true, 'config' => $config]);
    }

    public function update(Request $request, Matkul $matkul, MatkulConfig $config)
    {
        abort_unless($config->matkul_id === $matkul->id, 404);

        $validated = $request->validate([
            'scheduling_semester' => 'nullable|integer|between:1,8',
            'practicum_hours' => 'required|integer|min:0|max:12',
            'responsi_hours' => 'required|integer|min:0|max:12',
            'is_manual' => 'boolean',
        ]);

        $config->update([
            'scheduling_semester' => $validated['scheduling_semester'] ?? null,
            'practicum_hours' => $validated['practicum_hours'],
            'responsi_hours' => $validated['responsi_hours'],
            'is_manual' => $validated['is_manual'] ?? false,
        ]);

        return response()->json(['success' => true, 'config' => $config]);
    }

    public function destroy(Matkul $matkul, MatkulConfig $config)
    {
        abort_unless($config->matkul_id === $matkul->id, 404);
        $config->delete();
        return response()->json(['success' => true]);
    }

    public function copy(Request $request)
    {
        $validated = $request->validate([
            'source_period_id' => 'required|exists:periods,id',
            'target_period_id' => 'required|exists:periods,id|different:source_period_id',
        ], ['target_period_id.different' => 'Periode sumber dan tujuan harus berbeda.']);

        $existing = MatkulConfig::where('period_id', $validated['target_period_id'])->pluck('matkul_id');
        $sources = MatkulConfig::where('period_id', $validated['source_period_id'])
            ->whereHas('matkul')
            ->get();
        $toCopy = $sources->whereNotIn('matkul_id', $existing);

        DB::transaction(function () use ($toCopy, $validated) {
            foreach ($toCopy as $src) {
                MatkulConfig::create([
                    'period_id' => $validated['target_period_id'],
                    'matkul_id' => $src->matkul_id,
                    'scheduling_semester' => $src->scheduling_semester,
                    'practicum_hours' => $src->practicum_hours,
                    'responsi_hours' => $src->responsi_hours,
                    'is_manual' => $src->is_manual,
                ]);
            }
        });

        $copied = $toCopy->count();
        $skipped = $sources->count() - $copied;
        $message = $sources->isEmpty()
            ? 'Periode sumber tidak memiliki konfigurasi.'
            : "{$copied} konfigurasi disalin, {$skipped} dilewati (sudah ada di periode tujuan).";

        return response()->json(['success' => true, 'copied' => $copied, 'skipped' => $skipped, 'message' => $message]);
    }
}
