<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Type;
use App\Models\Items;
use App\Models\Period;
use App\Models\Repair;
use App\Models\Booking;
use App\Models\Components;
use App\Models\Repairs_item;
use Illuminate\Http\Request;
use App\Models\SpecAttributes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RepairController extends Controller
{
    public function viewRepairs()
    {
        $repairs = Repair::with([
            'reporter',

            'items.type',
            'items.desk.lab',
            'items.specSetValues.specAttributes',

            'components.type',
            'components.item.desk.lab',
            'components.specSetValues.specAttributes'
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        $labs = Labs::all();
        $types = Type::all();
        $specification = SpecAttributes::with('specValues')->get();

        return view('admin.repairs', [
            'repairs'       => $repairs,
            'labs'          => $labs,
            'types'         => $types,
            'specification' => $specification
        ]);
    }

    public function applyRepair(Request $request)
    {
        if (!Auth::check()) {
            $message = 'Anda harus login terlebih dahulu untuk melaporkan kerusakan.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 401);
            }
            return redirect()->route('user.login')->with('error', $message);
        }

        $validated = $request->only(['items', 'components',]);
        $valid = Validator::make($request->all(), [
            'items'                     => 'nullable|array',
            'items.*.id'                => 'required|uuid|exists:items,id',
            'items.*.issue_description' => 'required|string',

            'components'                     => 'nullable|array',
            'components.*.id'                => 'required|uuid|exists:components,id',
            'components.*.issue_description' => 'required|string',
        ], [
            'items.array'                        => 'Format data item tidak valid.',
            'items.*.id.exists'                  => 'Salah satu item tidak ditemukan di database.',
            'items.*.issue_description.required' => 'Deskripsi masalah item wajib diisi.',

            'components.array'                        => 'Format data komponen tidak valid.',
            'components.*.id.exists'                  => 'Salah satu komponen tidak ditemukan di database.',
            'components.*.issue_description.required' => 'Deskripsi masalah komponen wajib diisi.',
        ]);

        if ($valid->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $valid->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($valid)->withInput();
        }

        if (empty($validated['items']) && empty($validated['components'])) {
            $msg = 'Mohon pilih minimal satu Item atau Component untuk diperbaiki.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $reportedTime = now('Asia/Jakarta');
        $reporter = Auth::user();
        $period = Period::getCurrentPeriod()->id;

        if (!$period) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada periode yang aktif, Mohon hubungi Admin.',
            ], 404);
        }
        try {
            if (!empty($validated['items'])) {
                $itemIds = collect($validated['items'])->pluck('id');

                $blockedItems = Repairs_item::whereIn('itemable_id', $itemIds)
                    ->whereIn('status', [0, 1])
                    ->whereNull('completed_at')
                    ->exists();

                if ($blockedItems) {
                    throw new \Exception('Salah satu item masih dalam proses perbaikan dan tidak bisa diajukan lagi.');
                }
            }

            if (!empty($validated['components'])) {
                $compIds = collect($validated['components'])->pluck('id');

                $blockedComponents = Repairs_item::whereIn('itemable_id', $compIds)
                    ->whereIn('status', [0, 1])
                    ->whereNull('completed_at')
                    ->exists();

                if ($blockedComponents) {
                    throw new \Exception('Salah satu item masih dalam proses perbaikan dan tidak bisa diajukan lagi.');
                }
            }

            DB::beginTransaction();

            $countItems = !empty($validated['items']) ? count($validated['items']) : 0;
            $countComps = !empty($validated['components']) ? count($validated['components']) : 0;
            $totalCount = $countItems + $countComps;

            $repair = Repair::create([
                'name'        => $reporter->name . '-' . $reportedTime->format('YmdHis') . '-BATCH-' . $totalCount,
                'reported_by' => $reporter->id,
                'period_id' => $period,
            ]);

            if (!empty($validated['items'])) {
                $itemAttachments = [];
                foreach ($validated['items'] as $itemData) {
                    $itemAttachments[$itemData['id']] = [
                        'issue_description' => $itemData['issue_description'],
                        'reported_at'       => $reportedTime,
                        'started_at'        => null,
                        'status'            => 0,
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
                        'reported_at'       => $reportedTime,
                        'started_at'        => null,
                        'status'            => 0,
                    ];
                    Components::where('id', $compData['id'])->update(['condition' => 0]);
                }
                $repair->components()->attach($compAttachments);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan perbaikan berhasil diajukan.'
                ], 201);
            }

            return redirect()->back()->with('success', 'Laporan perbaikan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan repair : ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?? 'Terjadi kesalahan pada server!',
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan laporan.');
        }
    }

    public function updateRepairStatus(Request $request, Repair $repair)
    {
        $isPartialUpdate = $request->has('items') && is_array($request->items);

        $rules = [
            'status'        => $isPartialUpdate ? 'nullable' : 'required|integer|in:0,1,2',
            'is_successful' => 'nullable|boolean',
            'repair_notes'  => 'nullable|string|max:255',

            'items'                  => 'nullable|array',
            'items.*.itemable_id'    => 'required|uuid',
            'items.*.status'         => 'nullable|integer|in:0,1,2',
            'items.*.is_successful'  => 'nullable|boolean',
            'items.*.repair_notes'   => 'nullable|string',
        ];

        $validated = $request->validate($rules);

        $completionMissingResult = $isPartialUpdate
            ? collect($validated['items'] ?? [])->contains(fn ($item) =>
                (int) ($item['status'] ?? 0) === 2
                && (!array_key_exists('is_successful', $item) || is_null($item['is_successful']))
            )
            : ((int) ($validated['status'] ?? 0) === 2
                && (!array_key_exists('is_successful', $validated) || is_null($validated['is_successful'])));

        if ($completionMissingResult) {
            return response()->json([
                'success' => false,
                'message' => 'Hasil perbaikan wajib dipilih saat status diselesaikan.',
            ], 422);
        }
        $currentTime = now('Asia/Jakarta');
        DB::beginTransaction();
        try {
            if ($isPartialUpdate) {
                foreach ($validated['items'] as $itemData) {
                    $pivotQuery = Repairs_item::where('repair_id', $repair->id)
                    ->where('itemable_id', $itemData['itemable_id']);
                    
                    $currentItem = $pivotQuery->first();
                    
                    if (!$currentItem) {
                        throw new \Exception("Id barang dan Repair tidak sesuai, tidak ada barang tersebut dalam Reparasi ini.");
                    };

                    $updateData = [];

                    if (isset($itemData['status'])) {
                        $newStatus = (int)$itemData['status'];

                        if ($newStatus < (int) $currentItem->status || $newStatus > ((int) $currentItem->status + 1)) {
                            throw new \Exception('Status reparasi hanya dapat maju satu tahap dan tidak dapat di-rollback.');
                        }

                        $updateData['status'] = $newStatus;

                        if ($newStatus === 1 && (int) $currentItem->status === 0) {
                            $updateData['started_at'] = $currentTime;
                        }

                        if ($newStatus === 2) {
                            $updateData['completed_at'] = $currentTime;
                        }
                    }

                    if (isset($itemData['repair_notes'])) {
                        $updateData['repair_notes'] = $itemData['repair_notes'];
                    }
                    if (isset($itemData['is_successful'])) {
                        $updateData['is_successful'] = $itemData['is_successful'];
                    }

                    if (!empty($updateData)) {
                        $pivotQuery->update($updateData);
                    }

                    if (
                        isset($itemData['status'])
                        && (int) $itemData['status'] === 2
                        && array_key_exists('is_successful', $itemData)
                        && (bool) $itemData['is_successful']
                    ) {
                        $this->restoreRepairableCondition($currentItem);
                    }
                }
            } else {
                $status = (int) $validated['status'];

                $pivotUpdateData = [
                    'status' => $status,
                ];

                if (isset($validated['is_successful'])) {
                    $pivotUpdateData['is_successful'] = $validated['is_successful'];
                }
                if (isset($validated['repair_notes'])) {
                    $pivotUpdateData['repair_notes'] = $validated['repair_notes'];
                }

                if ($status === 2) {
                    $pivotUpdateData['completed_at'] = $currentTime;
                } elseif ($status === 1) {
                    $pivotUpdateData['started_at'] = $currentTime;
                }

                $repairItems = Repairs_item::where('repair_id', $repair->id)->get();
                $repairItems->each->update($pivotUpdateData);

                if (
                    $status === 2
                    && array_key_exists('is_successful', $validated)
                    && (bool) $validated['is_successful']
                ) {
                    $repairItems->each(fn (Repairs_item $repairItem) => $this->restoreRepairableCondition($repairItem));
                }

                if ($request->has('items') && is_array($request->items)) {
                    foreach ($validated['items'] as $specificNote) {
                        if (isset($specificNote['repair_notes']) && isset($specificNote['itemable_id'])) {
                            Repairs_item::where('repair_id', $repair->id)
                                ->where('itemable_id', $specificNote['itemable_id'])
                                ->update(['repair_notes' => $specificNote['repair_notes']]);
                        }
                    }
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status perbaikan berhasil diperbarui.',
                    'data'    => $repair->load('repairs_item')
                ], 200);
            }

            return redirect()->back()->with('success', 'Status perbaikan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update status repair: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Server Error', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan pada server.');
        }
    }

    private function restoreRepairableCondition(Repairs_item $repairItem): void
    {
        $repairItem->loadMissing('itemable');

        if ($repairItem->itemable) {
            $repairItem->itemable->update(['condition' => 1]);
        }
    }
}
