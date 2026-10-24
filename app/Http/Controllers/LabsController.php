<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LabsController extends Controller
{
    public function labsList(Request $request)
    {
        // Ambil parameter untuk cek bentrok jadwal (opsional, tapi disarankan tetap ada)
        $start = $request->query('start');
        $end   = $request->query('end');

        $labs = Labs::orderBy('name')->get();

        // Mapping untuk mengirimkan data stok ke Frontend
        $labs = $labs->map(function ($lab) {
            // CONTOH LOGIKA STOK (Sesuaikan dengan nama kolom di DB Anda)
            // Di sini saya asumsikan tabel labs punya kolom qty_monitor, qty_pc, dll.
            // Atau Anda bisa ambil dari relasi inventory.

            return [
                'id' => $lab->id,
                'name' => $lab->name,
                'inventory' => [
                    'monitor'  => $lab->qty_monitor ?? 20, // Default 20 jika kolom null
                    'pc'       => $lab->qty_pc ?? 20,
                    'keyboard' => $lab->qty_keyboard ?? 20,
                    'mouse'    => $lab->qty_mouse ?? 20,
                ], 
                'capacity' => $lab->capacity ?? 0
            ];
        });

        return response()->json($labs);
    }

    public function getDesks(Labs $lab)
    {
        $desks = $lab->desks()
            ->with([
                'items.type',
                'items.specSetValues.specAttributes',
                'items.components.type',
                'items.components.specSetValues.specAttributes',
            ])
            ->orderBy('location')
            ->get();
        return response()->json($desks->toArray());
    }

    public function getLabStorage(Labs $lab)
    {
        $items = $lab->items()
            ->with([
                'type',
                'specSetValues.specAttributes',
                'components.type',
                'components.specSetValues.specAttributes',
            ])
            ->whereNull('desk_id')
            ->get();

        $components = \App\Models\Components::where('lab_id', $lab->id)
            ->with([
                'type',
                'specSetValues.specAttributes',
            ])
            ->whereNull('item_id')
            ->get();

        return response()->json([
            'items' => $items,
            'components' => $components
        ]);
    }

    public function getAvailableSets(Request $request, Labs $lab)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $requiredTypes = ['MONITOR', 'MOUSE', 'KEYBOARD', 'CPU'];

        $activeRepair = fn ($query) => $query
            ->whereIn('repairs_items.status', [0, 1])
            ->whereNull('repairs_items.completed_at');

        $desks = $lab->desks()->with(['items.type', 'items.repairs' => $activeRepair])->get();

        $availableDesks = [];

        foreach ($desks as $desk) {
            $items = $desk->items;

            if ($items->count() < 4) continue;

            $typeNames = $items->pluck('type.name')->toArray();
            $hasAllTypes = count(array_intersect($requiredTypes, $typeNames)) === 4;

            if (!$hasAllTypes) continue;

            $allGoodCondition = $items->every(fn($i) => $i->condition == 1);
            if (!$allGoodCondition) continue;

            $anyUnderRepair = $items->some(fn($i) => $i->repairs->isNotEmpty());
            if ($anyUnderRepair) continue;

            if ($start && $end) {
                $hasBookingConflict = $items->some(function ($item) use ($start, $end) {
                    return DB::table('bookings_items')
                        ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                        ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                        ->where('bookings_items.bookable_id', $item->id)
                        ->where('bookings.borrowed_at', '<', $end)
                        ->where('bookings.return_deadline_at', '>', $start)
                        ->where('bookings.approved', true)
                        ->exists();
                });

                if ($hasBookingConflict) continue;
            }

            $availableDesks[] = [
                'desk_id' => $desk->id,
                'location' => $desk->location,
                'items' => $items->map(fn($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                    'type' => $i->type->name
                ])
            ];
        }

        // Check storage items
        $storageItems = $lab->items()
            ->with(['type', 'repairs' => $activeRepair])
            ->whereNull('desk_id')
            ->whereIn('type_id', function($q) use ($requiredTypes) {
                $q->select('id')->from('types')->whereIn('name', $requiredTypes);
            })
            ->where('condition', 1)
            ->whereDoesntHave('repairs', $activeRepair)
            ->get();

        if ($start && $end) {
            $storageItems = $storageItems->filter(function($item) use ($start, $end) {
                return !DB::table('bookings_items')
                    ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                    ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                    ->where('bookings_items.bookable_id', $item->id)
                    ->where('bookings.borrowed_at', '<', $end)
                    ->where('bookings.return_deadline_at', '>', $start)
                    ->where('bookings.approved', true)
                    ->exists();
            });
        }

        $typeCounts = [];
        foreach ($requiredTypes as $type) {
            $typeCounts[$type] = $storageItems->filter(fn($i) => $i->type->name === $type)->count();
        }
        $storageSets = min($typeCounts);

        return response()->json([
            'available_count' => count($availableDesks) + $storageSets,
            'desks' => $availableDesks
        ]);
    }

    public function getDeskMapWithAvailability(Request $request, Labs $lab)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $activeRepair = fn ($query) => $query
            ->whereIn('repairs_items.status', [0, 1])
            ->whereNull('repairs_items.completed_at');

        $desks = $lab->desks()
            ->with([
                'items.type',
                'items.specSetValues.specAttributes',
                'items.repairs' => $activeRepair,
                'items.components.type',
                'items.components.repairs' => $activeRepair,
                'items.components.specSetValues.specAttributes',
            ])
            ->orderBy('location')
            ->get();

        $deskMap = $desks->map(function ($desk) use ($start, $end) {
            $items = $desk->items->map(function ($item) use ($start, $end) {
                $isUnderRepair = $item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1);
                $isPendingRepair = $item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0);
                $isBooked = $start && $end && DB::table('bookings_items')
                    ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                    ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                    ->where('bookings_items.bookable_id', $item->id)
                    ->where('bookings.borrowed_at', '<', $end)
                    ->where('bookings.return_deadline_at', '>', $start)
                    ->where('bookings.approved', true)
                    ->exists();

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'serial_code' => $item->serial_code,
                    'type' => $item->type ? $item->type->name : null,
                    'condition' => $item->condition,
                    'available' => $item->condition == 1 && !$isUnderRepair && !$isPendingRepair && !$isBooked,
                    'is_under_repair' => $isUnderRepair,
                    'is_pending_repair' => $isPendingRepair,
                    'specifications' => $item->specSetValues->map(fn($spec) => [
                        'name' => $spec->specAttributes->name,
                        'value' => $spec->value,
                    ]),
                    'components' => $item->components->map(fn($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'serial_code' => $c->serial_code,
                        'type' => $c->type ? $c->type->name : null,
                        'is_under_repair' => $c->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1),
                        'is_pending_repair' => $c->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0),
                        'specifications' => $c->specSetValues->map(fn($spec) => [
                            'name' => $spec->specAttributes->name,
                            'value' => $spec->value,
                        ]),
                    ])
                ];
            });
            return [
                'id' => $desk->id,
                'location' => $desk->location,
                'items' => $items
            ];
        });

        $storageItems = $lab->items()
            ->with([
                'type',
                'specSetValues.specAttributes',
                'repairs' => $activeRepair,
                'components.type',
                'components.repairs' => $activeRepair,
                'components.specSetValues.specAttributes',
            ])
            ->whereNull('desk_id')
            ->get()
            ->map(function ($item) use ($start, $end) {
                $isUnderRepair = $item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1);
                $isPendingRepair = $item->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0);
                $isBooked = $start && $end && DB::table('bookings_items')
                    ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                    ->where('bookings_items.bookable_type', 'App\\Models\\Items')
                    ->where('bookings_items.bookable_id', $item->id)
                    ->where('bookings.borrowed_at', '<', $end)
                    ->where('bookings.return_deadline_at', '>', $start)
                    ->where('bookings.approved', true)
                    ->exists();

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'serial_code' => $item->serial_code,
                    'type' => $item->type ? $item->type->name : null,
                    'condition' => $item->condition,
                    'available' => $item->condition == 1 && !$isUnderRepair && !$isPendingRepair && !$isBooked,
                    'is_under_repair' => $isUnderRepair,
                    'is_pending_repair' => $isPendingRepair,
                    'specifications' => $item->specSetValues->map(fn($spec) => [
                        'name' => $spec->specAttributes->name,
                        'value' => $spec->value,
                    ]),
                    'components' => $item->components->map(fn($c) => [
                        'id' => $c->id,
                        'name' => $c->name,
                        'serial_code' => $c->serial_code,
                        'type' => $c->type ? $c->type->name : null,
                        'is_under_repair' => $c->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1),
                        'is_pending_repair' => $c->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0),
                        'specifications' => $c->specSetValues->map(fn($spec) => [
                            'name' => $spec->specAttributes->name,
                            'value' => $spec->value,
                        ]),
                    ])
                ];
            });

        $storageComponents = \App\Models\Components::where('lab_id', $lab->id)
            ->with(['type', 'repairs' => $activeRepair, 'specSetValues.specAttributes'])
            ->whereNull('item_id')
            ->get()
            ->map(function ($component) use ($start, $end) {
                $isBooked = $start && $end && DB::table('bookings_items')
                    ->join('bookings', 'bookings_items.booking_id', '=', 'bookings.id')
                    ->where('bookings_items.bookable_type', 'App\\Models\\Components')
                    ->where('bookings_items.bookable_id', $component->id)
                    ->where('bookings.borrowed_at', '<', $end)
                    ->where('bookings.return_deadline_at', '>', $start)
                    ->where('bookings.approved', true)
                    ->exists();

                return [
                    'id' => $component->id,
                    'name' => $component->name,
                    'serial_code' => $component->serial_code,
                    'type' => $component->type ? $component->type->name : null,
                    'condition' => $component->condition,
                    'available' => $component->condition == 1 && !$isBooked,
                    'is_under_repair' => $component->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 1),
                    'is_pending_repair' => $component->repairs->contains(fn ($repair) => (int) $repair->pivot->status === 0),
                    'is_component' => true,
                    'specifications' => $component->specSetValues->map(fn($spec) => [
                        'name' => $spec->specAttributes->name,
                        'value' => $spec->value,
                    ])
                ];
            });

        return response()->json([
            'desks' => $deskMap,
            'storage' => [
                'items' => $storageItems,
                'components' => $storageComponents
            ]
        ]);
    }
}
