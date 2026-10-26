<?php

namespace App\Http\Controllers;

use App\Models\Labs;
use App\Models\Type;
use App\Models\Items;
use App\Models\Components;
use Illuminate\Http\Request;
use App\Models\SpecAttributes;
use Database\Seeders\specification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class AdminController extends Controller
{
    // user dengan unit_id selain mahasiswa biasa 

    public static function dashboard()
    {
        $admin = Auth::user();
        return view('admin.dashboard', ['name' => $admin->name, 'unit' => $admin->unit->name]);
    }

    public static function labs()
    {
        $labs = Labs::orderBy('name')->get();

        return view('admin.labs', ['labs' => $labs]);
    }

    public function items(Request $request)
{
    $types = Type::orderBy('name')->get();
    $specification = SpecAttributes::with('specValues')->orderBy('name')->get();
    $labs = Labs::orderBy('name')->get();

    // Load semua items dengan relasi yang diperlukan
    $items = Items::with([
        'type', 
        'specSetValues.specAttributes', 
        'desk.lab',
        'repairs' => function($q) {
            $q->whereIn('repairs_items.status', [0, 1]);
        }
    ])->orderBy('name')->get();

    // Load semua components dengan relasi yang diperlukan
    $components = Components::with([
        'type', 
        'specSetValues.specAttributes', 
        'item.desk.lab',
        'repairs' => function($q) {
            $q->whereIn('repairs_items.status', [0, 1]);
        },
        'item.repairs' => function($q) {
            $q->whereIn('repairs_items.status', [0, 1]);
        }
    ])->orderBy('name')->get();

    return view('admin.items', [
        'items' => $items,
        'components' => $components,
        'types' => $types,
        'specification' => $specification,
        'labs' => $labs,
    ]);
}
}
