<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Items;
use App\Models\Components;
use App\Models\Period;
use App\Models\Repair;
use App\Models\Repairs_item;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserHelpdeskController extends Controller
{
    public function index()
    {
        $labs = Labs::orderBy('name')->get();
        $activePeriod = Period::getCurrentPeriod();

        return view('user.helpdesk.index', compact('labs', 'activePeriod'));
    }

    public function storeRepair(Request $request)
    {
        $validated = $request->validate([
            'lab_id' => 'required|uuid|exists:labs,id',
            'items' => 'nullable|array',
            'items.*.id' => 'required|uuid|exists:items,id',
            'items.*.issue_description' => 'required|string|max:1000',

            'components' => 'nullable|array',
            'components.*.id' => 'required|uuid|exists:components,id',
            'components.*.issue_description' => 'required|string|max:1000',
        ], [
            'lab_id.required' => 'Laboratorium wajib dipilih.',
            'items.*.issue_description.required' => 'Deskripsi kerusakan item wajib diisi.',
            'components.*.issue_description.required' => 'Deskripsi kerusakan komponen wajib diisi.',
        ]);

        if (empty($validated['items']) && empty($validated['components'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal satu barang atau komponen yang ingin dilaporkan rusak.',
            ], 422);
        }

        $activePeriod = Period::getCurrentPeriod();
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada periode akademik aktif saat ini. Silakan hubungi admin.',
            ], 422);
        }

        $itemIds = collect($validated['items'] ?? [])->pluck('id');
        $compIds = collect($validated['components'] ?? [])->pluck('id');

        // Pastikan tidak ada barang yang sedang dalam proses perbaikan
        $blockedItems = Repairs_item::whereIn('itemable_id', $itemIds->concat($compIds))
            ->whereIn('status', [0, 1])
            ->whereNull('completed_at')
            ->exists();

        if ($blockedItems) {
            return response()->json([
                'success' => false,
                'message' => 'Salah satu barang yang Anda pilih sudah terdaftar dalam proses perbaikan aktif.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $reportedTime = now('Asia/Jakarta');
            $user = Auth::user();
            $totalCount = count($validated['items'] ?? []) + count($validated['components'] ?? []);

            $repair = Repair::create([
                'name' => $user->name . '-' . $reportedTime->format('YmdHis') . '-BATCH-' . $totalCount,
                'reported_by' => $user->id,
                'period_id' => $activePeriod->id,
            ]);

            if (!empty($validated['items'])) {
                $itemAttachments = [];
                foreach ($validated['items'] as $itemData) {
                    $itemAttachments[$itemData['id']] = [
                        'issue_description' => $itemData['issue_description'],
                        'started_at' => null,
                        'status' => 0,
                    ];
                    Items::where('id', $itemData['id'])->update(['condition' => 0]);
                }
                $repair->items()->attach($itemAttachments);
            }

            if (!empty($validated['components'])) {
                $compAttachments = [];
                foreach ($validated['components'] as $compData) {
                    $compAttachments[$compData['id']] = [
                        'issue_description' => $compData['issue_description'],
                        'started_at' => null,
                        'status' => 0,
                    ];
                    Components::where('id', $compData['id'])->update(['condition' => 0]);
                }
                $repair->components()->attach($compAttachments);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan perbaikan berhasil dikirim. Teknisi kami akan segera menindaklanjuti.',
                'repair_id' => $repair->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storeRepair UserHelpdesk: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:KELUHAN,SARAN',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:3000',
            'lab_id' => 'nullable|uuid|exists:labs,id',
        ], [
            'type.required' => 'Pilih jenis laporan (Keluhan atau Saran).',
            'subject.required' => 'Subjek laporan wajib diisi.',
            'description.required' => 'Pesan atau detail laporan wajib diisi.',
        ]);

        $activePeriod = Period::getCurrentPeriod();
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada periode akademik aktif saat ini. Silakan hubungi admin.',
            ], 422);
        }

        try {
            $feedback = Feedback::create([
                'type' => $validated['type'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'lab_id' => $validated['lab_id'] ?: null,
                'reporter_id' => Auth::id(),
                'status' => Feedback::STATUS_OPEN,
                'period_id' => $activePeriod->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Keluhan / saran Anda berhasil terkirim dengan tiket ' . $feedback->ticket_number,
                'feedback' => $feedback,
            ]);
        } catch (\Exception $e) {
            Log::error('Error storeFeedback UserHelpdesk: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function history()
    {
        $userId = Auth::id();

        $repairs = Repair::where('reported_by', $userId)
            ->with([
                'items' => fn($q) => $q->with(['type', 'desk.lab', 'lab']),
                'components' => fn($q) => $q->with(['type', 'item.desk.lab', 'lab']),
                'period'
            ])
            ->orderByDesc('created_at')
            ->get();

        $feedbacks = Feedback::where('reporter_id', $userId)
            ->with(['lab', 'responses.responder', 'period'])
            ->orderByDesc('created_at')
            ->get();

        return view('user.helpdesk.history', compact('repairs', 'feedbacks'));
    }

    public function feedbackDetail(Feedback $feedback)
    {
        if ($feedback->reporter_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk melihat tiket ini.',
            ], 403);
        }

        $feedback->load(['lab', 'responses.responder', 'period']);

        return response()->json([
            'success' => true,
            'feedback' => $feedback,
        ]);
    }
}
