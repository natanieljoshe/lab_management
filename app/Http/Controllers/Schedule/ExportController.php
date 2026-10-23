<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Services\Excel\ScheduleExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        protected ScheduleExportService $exportService
    ) {}

    public function exportExcel(Request $request): StreamedResponse
    {
        $period = null;
        if ($request->filled('period_id')) {
            $period = Period::find($request->query('period_id'));
        }

        $period = $period ?? Period::where('active', true)->first();
        if (! $period) {
            $period = Period::first();
        }

        return $this->exportService->export($period);
    }
}
