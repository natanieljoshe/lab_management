<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\Period;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $periods = Period::withCount(['classSections'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('settings.index', compact('periods'));
    }

    public function setActive(Period $period): RedirectResponse
    {
        Period::query()->update(['active' => false]);
        $period->update(['active' => true]);

        return back()->with('success', "Periode aktif diubah menjadi {$period->name}.");
    }

    public function storePeriod(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $name = trim($validated['name']);
        $semester = 'GASAL';
        $year = '2024/2025';

        if (preg_match('/^(Gasal|Genap)\s+(.+)$/i', $name, $m)) {
            $semester = strtoupper($m[1]);
            $year = trim($m[2]);
        } elseif (preg_match('/(\d{4}\/\d{4})/', $name, $m)) {
            $year = $m[1];
            if (stripos($name, 'genap') !== false) {
                $semester = 'GENAP';
            }
        }

        // Check if exists
        if (Period::where('academic_year', $year)->where('semester', $semester)->exists()) {
            return back()->with('error', "Periode {$semester} {$year} sudah ada.");
        }

        if (! empty($validated['is_active'])) {
            Period::query()->update(['active' => false]);
        }

        $period = Period::create([
            'academic_year' => $year,
            'semester' => $semester,
            'active' => ! empty($validated['is_active']),
        ]);

        if (Period::count() === 1) {
            $period->update(['active' => true]);
        }

        return back()->with('success', "Periode '{$period->name}' berhasil ditambahkan.");
    }

    public function updatePeriod(Request $request, Period $period): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
        ]);

        $name = trim($validated['name']);
        if (preg_match('/^(Gasal|Genap)\s+(.+)$/i', $name, $m)) {
            $semester = strtoupper($m[1]);
            $year = trim($m[2]);

            // Check unique
            $exists = Period::where('academic_year', $year)
                ->where('semester', $semester)
                ->where('id', '!=', $period->id)
                ->exists();

            if ($exists) {
                return back()->with('error', "Periode {$semester} {$year} sudah ada.");
            }

            $period->update([
                'academic_year' => $year,
                'semester' => $semester,
            ]);
        }

        return back()->with('success', "Nama periode berhasil diubah menjadi '{$period->name}'.");
    }

    public function destroyPeriod(Period $period): RedirectResponse
    {
        if ($period->active) {
            return back()->with('error', 'Tidak dapat menghapus periode yang sedang aktif. Pilih periode lain terlebih dahulu.');
        }

        $name = $period->name;
        $period->delete();

        return back()->with('success', "Periode '{$name}' berhasil dihapus.");
    }
}
