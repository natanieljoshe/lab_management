<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Labs;
use App\Models\Items;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetController extends Controller
{
    public function index()
    {
        $labs = Labs::orderBy('name')->get();
        $sets = Set::with(['items.type', 'items.desk.lab', 'items.components', 'items.repairs' => function($q) {
            $q->whereIn('repairs_items.status', [0, 1]);
        }])->orderBy('name')->get();
        
        $types = \App\Models\Type::orderBy('name')->get();
        $specification = \App\Models\SpecAttributes::with('specValues')->orderBy('name')->get();

        return view('admin.sets', [
            'sets' => $sets,
            'labs' => $labs,
            'types' => $types,
            'specification' => $specification,
        ]);
    }

    public function getSetDetails(Set $set)
    {
        $set->load([
            'items.type',
            'items.desk.lab',
            'items.specSetValues.specAttributes',
            'items.components.type',
            'items.components.specSetValues.specAttributes',
        ]);

        return response()->json($set);
    }

    public function attachSetToDesk(Request $request, Set $set)
    {
        $request->validate([
            'lab_id' => 'required|uuid|exists:labs,id',
            'desk_location' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $items = $set->items()->with('desk')->get();
            
            if ($items->count() !== 4) {
                throw new \Exception('Set harus memiliki 4 item.');
            }

            $desk = DB::table('desks')
                ->where('lab_id', $request->lab_id)
                ->where('location', $request->desk_location)
                ->first();

            if (!$desk) {
                throw new \Exception("Meja {$request->desk_location} tidak ditemukan di lab ini.");
            }

            foreach ($items as $item) {
                $item->desk_id = $desk->id;
                $item->lab_id = null;
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Set '{$set->name}' berhasil dipasang ke meja {$request->desk_location}."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function attachSetToLab(Request $request, Set $set)
    {
        $request->validate([
            'lab_id' => 'required|uuid|exists:labs,id'
        ]);

        DB::beginTransaction();
        try {
            $items = $set->items()->get();
            
            if ($items->count() !== 4) {
                throw new \Exception('Set harus memiliki 4 item.');
            }

            foreach ($items as $item) {
                $item->lab_id = $request->lab_id;
                $item->desk_id = null;
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Set '{$set->name}' berhasil dipasang ke lemari lab."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function detachSetFromDesks(Set $set)
    {
        DB::beginTransaction();
        try {
            $items = $set->items()->whereNotNull('desk_id')->get();
            
            if ($items->isEmpty()) {
                throw new \Exception('Tidak ada item dalam set ini yang terpasang di meja.');
            }

            foreach ($items as $item) {
                $item->desk_id = null;
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Set '{$set->name}' berhasil dilepas dari meja."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function detachSetFromLabs(Set $set)
    {
        DB::beginTransaction();
        try {
            $items = $set->items()->whereNotNull('lab_id')->get();
            
            if ($items->isEmpty()) {
                throw new \Exception('Tidak ada item dalam set ini yang terpasang di lemari lab.');
            }

            foreach ($items as $item) {
                $item->lab_id = null;
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Set '{$set->name}' berhasil dilepas dari lemari lab."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
