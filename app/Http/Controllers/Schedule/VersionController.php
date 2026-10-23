<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\LabSession;
use App\Models\Period;
use App\Models\Schedule as ScheduleVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VersionController extends Controller
{
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
        $allPeriods = Period::orderBy('academic_year', 'desc')->orderBy('semester', 'desc')->get();
        $versions = ScheduleVersion::with('period')
            ->whereNotNull('snapshot_json')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('versions.index', [
            'period' => $period,
            'allPeriods' => $allPeriods,
            'versions' => $versions,
        ]);
    }

    /**
     * Store current schedule snapshot as a named version/draft.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'public_slug' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9_\-]+$/',
            'is_published' => 'nullable|boolean',
        ]);

        $period = $this->getActivePeriod();

        $activeSchedule = ScheduleVersion::where('period_id', $period->id)->where('is_active', true)->first();
        $activeScheduleId = $activeSchedule?->id;

        // Capture all lab sessions with related sections and courses
        $sessions = LabSession::where('schedule_id', $activeScheduleId)
            ->with(['section.course', 'room'])
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'class_section_id' => $s->class_section_id,
                'session_type' => $s->session_type,
                'type' => $s->type,
                'duration_min' => $s->duration_min,
                'day_of_week' => $s->day_of_week,
                'day' => $s->day,
                'start_min' => $s->start_min,
                'end_min' => $s->end_min,
                'lab_id' => $s->lab_id,
                'room_id' => $s->lab_id,
                'status' => $s->status,
                'unscheduled_reason' => $s->unscheduled_reason,
                'is_locked' => $s->is_locked,
                'is_manual_placed' => $s->is_manual_placed,
            ])
            ->all();

        $isPublished = ! empty($validated['is_published']);
        $slug = $validated['public_slug'] ?? null;
        if (! empty($slug)) {
            $slug = Str::slug($slug);
        } elseif ($isPublished) {
            $baseSlug = Str::slug($period->name);
            $slug = $baseSlug;
            $counter = 1;
            while (ScheduleVersion::whereJsonContains('snapshot_json->public_slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
        }

        $snapshot = [
            'created_at' => now()->toIso8601String(),
            'period_id' => $period->id,
            'period_name' => $period->name,
            'total_sessions' => count($sessions),
            'placed_count' => count(array_filter($sessions, fn ($s) => $s['status'] === 'placed' || $s['status'] === 'SCHEDULED')),
            'is_published' => $isPublished,
            'public_slug' => $slug,
            'sessions' => $sessions,
        ];

        $version = ScheduleVersion::create([
            'period_id' => $period->id,
            'name' => $validated['name'],
            'is_active' => false,
            'snapshot_json' => $snapshot,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Draft '{$version->name}' berhasil disimpan.",
                'version' => $version,
            ]);
        }

        return back()->with('success', "Draft '{$version->name}' berhasil disimpan.");
    }

    /**
     * Toggle public publishing status of a draft.
     */
    public function togglePublish(Request $request, ScheduleVersion $version): RedirectResponse
    {
        $snapshot = $version->snapshot_json ?? [];
        $currentPub = $snapshot['is_published'] ?? false;
        $newStatus = ! $currentPub;

        // If activating and doesn't have a slug yet, generate one
        if ($newStatus && empty($snapshot['public_slug'])) {
            $baseSlug = Str::slug($version->period?->name ?? 'jadwal');
            $slug = $baseSlug;
            $counter = 1;
            while (ScheduleVersion::whereJsonContains('snapshot_json->public_slug', $slug)->where('id', '!=', $version->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $snapshot['public_slug'] = $slug;
        }

        $snapshot['is_published'] = $newStatus;
        $version->snapshot_json = $snapshot;
        $version->save();

        $statusText = $newStatus ? 'diaktifkan ke publik' : 'dinonaktifkan dari publik';

        return back()->with('success', "Link publik untuk draft '{$version->name}' berhasil {$statusText}.");
    }

    /**
     * Update public slug of a draft.
     */
    public function updateSlug(Request $request, ScheduleVersion $version): RedirectResponse
    {
        $validated = $request->validate([
            'public_slug' => 'required|string|max:100|regex:/^[a-zA-Z0-9_\-]+$/',
        ]);

        $slug = Str::slug($validated['public_slug']);
        $snapshot = $version->snapshot_json ?? [];
        $snapshot['public_slug'] = $slug;
        $version->snapshot_json = $snapshot;
        $version->save();

        return back()->with('success', "Slug link publik berhasil diubah menjadi /{$slug}.");
    }

    /**
     * Restore schedule from a snapshot version.
     */
    public function restore(ScheduleVersion $version): RedirectResponse
    {
        $snapshot = $version->snapshot_json;
        $sessions = $snapshot['sessions'] ?? [];

        $period = $version->period ?? $this->getActivePeriod();
        $activeSchedule = ScheduleVersion::firstOrCreate(
            ['period_id' => $period->id, 'is_active' => true],
            ['name' => 'Jadwal Utama']
        );

        DB::transaction(function () use ($sessions, $activeSchedule) {
            foreach ($sessions as $item) {
                LabSession::where('id', $item['id'])->update([
                    'schedule_id' => $activeSchedule->id,
                    'day_of_week' => $item['day_of_week'] ?? (isset($item['day']) ? ConstraintChecker::DAY_INDEX[$item['day']] ?? null : null),
                    'start_min' => $item['start_min'],
                    'end_min' => $item['end_min'],
                    'lab_id' => $item['lab_id'] ?? $item['room_id'] ?? null,
                    'status' => in_array(strtolower($item['status'] ?? ''), ['placed', 'scheduled']) ? 'SCHEDULED' : 'UNSCHEDULED',
                    'unscheduled_reason' => $item['unscheduled_reason'] ?? null,
                    'is_locked' => $item['is_locked'] ?? false,
                    'is_manual_placed' => $item['is_manual_placed'] ?? false,
                ]);
            }
        });

        return back()->with('success', "Jadwal berhasil dipulihkan dari draft '{$version->name}'.");
    }

    /**
     * Delete a version.
     */
    public function destroy(ScheduleVersion $version): RedirectResponse
    {
        $name = $version->name;
        $version->delete();

        return back()->with('success', "Draft '{$name}' berhasil dihapus.");
    }
}
