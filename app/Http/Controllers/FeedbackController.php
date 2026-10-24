<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\FeedbackResponse;
use App\Models\Labs;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with([
            'reporter.unit',
            'lab',
            'period',
            'responses.responder',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->filled('lab_id')) {
            $query->where('lab_id', $request->query('lab_id'));
        }

        $periods = Period::orderByDesc('academic_year')->orderByDesc('semester')->get();
        $activePeriod = Period::getCurrentPeriod();

        if ($request->filled('period_id')) {
            $query->where('period_id', $request->query('period_id'));
        }

        $feedbacks = $query->orderByDesc('created_at')->get();
        $labs = Labs::orderBy('name')->get();

        $stats = [
            'total' => Feedback::count(),
            'open' => Feedback::where('status', Feedback::STATUS_OPEN)->count(),
            'in_progress' => Feedback::where('status', Feedback::STATUS_IN_PROGRESS)->count(),
            'resolved' => Feedback::where('status', Feedback::STATUS_RESOLVED)->count(),
        ];

        return view('admin.feedbacks', compact(
            'feedbacks',
            'labs',
            'periods',
            'activePeriod',
            'stats'
        ));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load([
            'reporter.unit',
            'lab',
            'period',
            'responses.responder',
        ]);

        return response()->json([
            'success' => true,
            'feedback' => $feedback,
        ]);
    }

    public function respond(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:3000',
            'status_change' => 'nullable|integer|in:1,2,3,4,5',
        ], [
            'message.required' => 'Pesan tanggapan wajib diisi.',
        ]);

        try {
            $statusChange = !empty($validated['status_change']) ? (int) $validated['status_change'] : null;

            FeedbackResponse::create([
                'feedback_id' => $feedback->id,
                'responder_id' => Auth::id(),
                'message' => $validated['message'],
                'status_change' => $statusChange,
            ]);

            if ($statusChange) {
                $feedback->status = $statusChange;
                if ($statusChange === Feedback::STATUS_RESOLVED) {
                    $feedback->resolved_at = now();
                } elseif ($feedback->resolved_at) {
                    $feedback->resolved_at = null;
                }
            }

            // Jika staf membalas dan status masih Open, otomatis ubah jadi In Progress jika tidak dispesifikasikan lain
            if (!$statusChange && $feedback->status === Feedback::STATUS_OPEN) {
                $feedback->status = Feedback::STATUS_IN_PROGRESS;
            }

            $feedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Tanggapan berhasil dikirim dan status tiket diperbarui.',
                'feedback' => $feedback->fresh(['reporter.unit', 'lab', 'responses.responder']),
            ]);
        } catch (\Exception $e) {
            Log::error('Error admin respond feedback: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim tanggapan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => 'required|integer|in:1,2,3,4,5',
        ]);

        try {
            $newStatus = (int) $validated['status'];
            $feedback->status = $newStatus;

            if ($newStatus === Feedback::STATUS_RESOLVED) {
                $feedback->resolved_at = now();
            } elseif ($feedback->resolved_at) {
                $feedback->resolved_at = null;
            }

            $feedback->save();

            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diubah.',
                'feedback' => $feedback->fresh(['reporter.unit', 'lab', 'responses.responder']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
