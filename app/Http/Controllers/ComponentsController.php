<?php

namespace App\Http\Controllers;

use App\Models\Components;
use App\Models\Repairs_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComponentsController extends Controller
{
    public function getComponentDetails(Components $component)
    {
        $component->load([
            'type',
            'item',
            'item.desk.lab',
            'specSetValues.specAttributes'
        ]);

        return response()->json($component);
    }

    public function updateComponentCondition(Request $request, Components $component)
    {
        try {
            // Jika component rusak (0) dan ingin diubah jadi bagus (1)
            if ($component->condition == 0) {
                $ongoingRepair = Repairs_item::where('itemable_id', $component->id)
                    ->where('itemable_type', Components::class)
                    ->whereIn('status', [0, 1])
                    ->whereNull('completed_at')
                    ->with('repair')
                    ->first();

                if ($ongoingRepair) {
                    return response()->json([
                        'success' => false,
                        'has_ongoing_repair' => true,
                        'message' => 'Component ini masih dalam proses perbaikan. Selesaikan repair terlebih dahulu.',
                        'repair_data' => [
                            'repair_id' => $ongoingRepair->repair_id,
                            'itemable_id' => $component->id,
                            'issue_description' => $ongoingRepair->issue_description,
                            'repair_url' => route('admin.repairs.index')
                        ]
                    ], 422);
                }
            }

            $component->condition = !$component->condition;
            $component->save();

            $newConditionText = $component->condition ? 'Baik' : 'Rusak';

            return response()->json([
                'success' => true,
                'message' => "Kondisi '{$component->name}' berhasil diubah menjadi '{$newConditionText}'.",
                'new_condition' => $component->condition
            ]);
        } catch (\Exception $e) {
            Log::error('Error updateComponentCondition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah kondisi komponen.'], 500);
        }
    }

    public function completeRepairFromComponent(Request $request, Components $component)
    {
        $validated = $request->validate([
            'repair_id' => 'required|uuid|exists:repairs,id',
            'is_successful' => 'required|boolean',
            'repair_notes' => 'nullable|string|max:255'
        ]);

        try {
            $repairItem = Repairs_item::where('repair_id', $validated['repair_id'])
                ->where('itemable_id', $component->id)
                ->where('itemable_type', Components::class)
                ->where('status', 1)
                ->first();

            if (!$repairItem) {
                return response()->json(['success' => false, 'message' => 'Repair tidak ditemukan atau sudah selesai.'], 404);
            }

            DB::beginTransaction();

            $repairItem->update([
                'status' => 2,
                'is_successful' => $validated['is_successful'],
                'repair_notes' => $validated['repair_notes'] ?? null,
                'completed_at' => now('Asia/Jakarta')
            ]);

            if ($validated['is_successful']) {
                $component->update(['condition' => 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Repair berhasil diselesaikan.',
                'component_condition' => $component->fresh()->condition
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completeRepairFromComponent: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyelesaikan repair.'], 500);
        }
    }

    public function attachToLab(Request $request, Components $component, \App\Models\Labs $lab)
    {
        if ($component->item_id) {
            return response()->json(['success' => false, 'message' => 'Component ini sudah terpasang di item. Lepas dari item terlebih dahulu.'], 409);
        }

        // if ($component->lab_id) {
        //     return response()->json(['success' => false, 'message' => 'Component ini sudah terpasang di lab lain.'], 409);
        // }

        try {
            $component->lab_id = $lab->id;
            $component->save();

            return response()->json([
                'success' => true,
                'message' => "Component '{$component->name}' berhasil dipasang ke Lab '{$lab->name}'."
            ]);
        } catch (\Exception $e) {
            Log::error('Error attachComponentToLab: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memasang component ke lab.'], 500);
        }
    }

    public function detachFromItem(Request $request, Components $component)
    {
        if (!$component->item_id) {
            return response()->json(['success' => false, 'message' => 'Component ini tidak terpasang di item manapun.'], 400);
        }

        try {
            $component->item_id = null;
            $component->save();

            return response()->json([
                'success' => true,
                'message' => "Component '{$component->name}' berhasil dilepas dari item."
            ]);
        } catch (\Exception $e) {
            Log::error('Error detachComponentFromItem: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melepas component dari item.'], 500);
        }
    }

    public function detachFromLab(Request $request, Components $component)
    {
        if (!$component->lab_id) {
            return response()->json(['success' => false, 'message' => 'Component ini tidak terpasang di lab manapun.'], 400);
        }

        try {
            $component->lab_id = null;
            $component->save();

            return response()->json([
                'success' => true,
                'message' => "Component '{$component->name}' berhasil dilepas dari lab."
            ]);
        } catch (\Exception $e) {
            Log::error('Error detachComponentFromLab: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melepas component dari lab.'], 500);
        }
    }
}
