<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Matkul as Course;
use App\Models\MatkulConfig as CourseConfig;
use App\Models\Period;
use App\Services\Scheduling\ClassImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseConfigController extends Controller
{
    public function __construct(
        protected ClassImportService $importService
    ) {}

    protected function getActivePeriod(): Period
    {
        $period = Period::where('active', true)->first();
        if (! $period) {
            $period = Period::first();
        }
        if (! $period) {
            $period = Period::create([
                'academic_year' => '2024/2025',
                'semester' => 'GASAL',
                'active' => true,
            ]);
        }

        return $period;
    }

    public function index(): View
    {
        $period = $this->getActivePeriod();

        $configs = CourseConfig::where('period_id', $period->id)
            ->with(['course.classSections' => fn ($q) => $q->where('period_id', $period->id)])
            ->get();

        $allCourses = Course::orderBy('nama')->get();

        // Sections in this period for inline enrollment editing
        $sections = ClassSection::where('period_id', $period->id)
            ->with('course')
            ->orderBy('matkul_id')
            ->orderBy('label')
            ->get();

        return view('config.index', [
            'period' => $period,
            'configs' => $configs,
            'allCourses' => $allCourses,
            'sections' => $sections,
        ]);
    }

    /**
     * Store or update a course configuration.
     */
    public function storeOrUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => 'required|string|exists:matkuls,id',
            'schedule_semester' => 'required|integer|min:1|max:8',
            'practicum_hours' => 'required|numeric|min:0|max:5',
            'responsi_hours' => 'required|numeric|min:0|max:5',
            'is_manual' => 'nullable|boolean',
        ]);

        $period = $this->getActivePeriod();

        $config = CourseConfig::updateOrCreate(
            [
                'period_id' => $period->id,
                'matkul_id' => $validated['course_id'],
            ],
            [
                'scheduling_semester' => $validated['schedule_semester'],
                'practicum_hours' => $validated['practicum_hours'],
                'responsi_hours' => $validated['responsi_hours'],
                'is_manual' => ! empty($validated['is_manual']),
            ]
        );

        // Sync lab sessions for all sections of this course in this period
        $sections = ClassSection::where('period_id', $period->id)
            ->where('matkul_id', $validated['course_id'])
            ->get();

        foreach ($sections as $section) {
            $this->importService->syncLabSessionsForSection($section);
        }

        return back()->with('success', 'Konfigurasi praktikum/responsi mata kuliah berhasil disimpan.');
    }

    /**
     * Batch update section enrollments.
     */
    public function updateEnrollments(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'enrollments' => 'required|array',
            'enrollments.*.id' => 'required|string|exists:class_sections,id',
            'enrollments.*.enrollment' => 'required|integer|min:1|max:100',
        ]);

        foreach ($validated['enrollments'] as $item) {
            ClassSection::where('id', $item['id'])->update(['enrollment' => $item['enrollment']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jumlah mahasiswa per seksi berhasil diperbarui.',
        ]);
    }

    /**
     * Delete course configuration.
     */
    public function destroy(CourseConfig $config): RedirectResponse
    {
        $config->delete();

        return back()->with('success', 'Konfigurasi mata kuliah berhasil dihapus.');
    }
}
