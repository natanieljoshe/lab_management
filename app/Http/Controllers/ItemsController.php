<?php

namespace App\Http\Controllers;

use App\Models\Set;
use App\Models\Labs;
use App\Models\Type;
use App\Models\Desks;
use App\Models\Items;
use App\Models\Components;
use Illuminate\Support\Str;
use App\Models\SpecSetValue;
use Illuminate\Http\Request;
use App\Models\SpecAttributes;
use App\Models\Repairs_item;
use function Pest\Laravel\json;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    public function getItems()
    {
        $items = Items::all();
        return response()->json($items);
    }

    public function getItemsByLab(Labs $lab)
    {
        // Mengambil item yang belum dipinjam, dikelompokkan berdasarkan tipenya, dan dihitung jumlahnya.
        $available_items = Items::where('lab_id', $lab->id)
            ->select('type', DB::raw('count(*) as available_count'))
            ->groupBy('type')
            ->get();

        return response()->json($available_items);
    }
    public function getItemDetails(Items $item)
    {
        $item->load([
            'type',
            'desk.lab',
            'specSetValues.specAttributes',
            'components.type',
            'components.specSetValues.specAttributes',
        ]);

        return response()->json($item);
    }

    public function createItemSet(Request $request)
    {
        $data = $request->only(['set_name', 'set_note', 'items', 'attach_to_desk', 'attach_to_lab', 'lab_id', 'desk_location']);
        $valid = Validator::make($data, [
            'set_name' => 'required|string|max:255',
            'set_note' => 'nullable|string',

            'items' => 'required|array|size:4',
            'items.*' => 'required|array',

            'attach_to_desk' => 'nullable|boolean',
            'attach_to_lab' => 'nullable|boolean',

            'lab_id' => 'required_if:attach_to_desk,true|required_if:attach_to_lab,true|uuid|exists:labs,id',

            'desk_location' => 'required_if:attach_to_desk,true|string',
        ], [
            'set_name.required' => 'Nama Set wajib diisi.',

            'items.required' => 'Data item wajib diisi.',
            'items.array' => 'Data item harus berupa array.',
            'items.size' => 'Set harus terdiri dari 4 item.',

            'lab_id.required_if' => 'Lab harus dipilih.',

            'desk_location.required_if' => 'Lokasi meja harus dipilih.',
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

        $itemTemplates = $request->input('items'); // Ambil array 4 item
        $createdItems = [];

        DB::beginTransaction();
        try {
            $set = Set::create([
                'name' => $this->upperWords($data['set_name']),
                'note' => $data['set_note'] ?? null,
            ]);

            foreach ($itemTemplates as $index => $itemData) {

                $newItemData = $itemData;

                $newItemData['set_id'] = $set->id;

                $itemRequest = new Request($newItemData);
                $itemRequest->headers->set('Accept', 'application/json');

                $response = $this->createItems($itemRequest);

                $responseContent = json_decode($response->getContent(), true);

                if (!$response->isSuccessful()) {
                    $errorMessage = $responseContent['message'] ?? 'Terjadi kesalahan tidak diketahui.';

                    $itemNumber = $index + 1;
                    $sn = $newItemData['serial_code'] ?? 'SN-KOSONG';
                    throw new \Exception("Gagal membuat item ke-{$itemNumber} (SN: {$sn}): {$errorMessage}");
                }

                $createdItems[] = $responseContent['data'];
            }

            if ($request->filled('attach_to_desk') && $request->attach_to_desk) {
                $deskLocation = $request->desk_location;
                $labId = $request->lab_id;

                $desk = DB::table('desks')
                    ->where('lab_id', $labId)
                    ->where('location', $deskLocation)
                    ->first();

                if (!$desk) {
                    throw new \Exception("Meja {$deskLocation} tidak ditemukan di lab ini.");
                }

                foreach ($createdItems as $itemData) {
                    $item = Items::find($itemData['id']);
                    if ($item) {
                        $item->desk_id = $desk->id;
                        $item->save();
                    }
                }
            } elseif ($request->filled('attach_to_lab') && $request->attach_to_lab) {
                $labId = $request->lab_id;

                foreach ($createdItems as $itemData) {
                    $item = Items::find($itemData['id']);
                    if ($item) {
                        $item->lab_id = $labId;
                        $item->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Set '{$set->name}' dengan 4 item berhasil dibuat!",
                'set_id' => $set->id,
                'set_name' => $set->name,
                'created_items' => $createdItems
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error createItemSet: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createItems(Request $request)
    {
        $data = $request->only(['is_component', 'name', 'serial_code', 'condition', 'type', 'specifications', 'produced_at', 'set_id', 'new_components']);
        $rules = [
            'is_component' => 'required|boolean',
            'name' => 'required|string|max:255',
            'serial_code' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $existsInItems = Items::where('serial_code', $value)->exists();
                    $existsInComponents = Components::where('serial_code', $value)->exists();

                    if ($existsInItems) $fail("Serial code {$attribute} sudah terdaftar sebagai Item.");
                    if ($existsInComponents) $fail("Serial code {$attribute} sudah terdaftar sebagai Component.");
                },
            ],
            'produced_at' => 'nullable|date',
            'condition' => 'required|boolean',
            'type' => 'required', // Bisa UUID atau string dengan prefix 'new::' kalau type baru

            'specifications' => 'nullable|array',
            'specifications.*.attribute' => 'required|max:255',
            'specifications.*.value' => 'required|max:255',

            'set_id' => 'nullable|uuid|exists:sets,id',
        ];

        if ($data['is_component'] == '0') { // berupa Item
            $rules['new_components'] = 'nullable|array';
            $rules['new_components.*.name'] = 'required|string|max:255';
            $rules['new_components.*.serial_code'] = [
                'required',
                'string',
                'max:255',
                'distinct', // serial code unik di dalam array
                // Cek keunikan di DB
                function ($attribute, $value, $fail) {
                    // $attribute akan menjadi "new_components.0.serial_code", kita cek $value
                    $existsInItems = Items::where('serial_code', $value)->exists();
                    $existsInComponents = Components::where('serial_code', $value)->exists();

                    if ($existsInItems) $fail("Serial code komponen {$value} sudah terdaftar sebagai Item.");
                    if ($existsInComponents) $fail("Serial code komponen {$value} sudah terdaftar sebagai Component.");
                },
            ];
            $rules['new_components.*.condition'] = 'required|boolean';
            $rules['new_components.*.type'] = 'required';
            $rules['new_components.*.specifications'] = 'nullable|array';
            $rules['new_components.*.specifications.*.attribute'] = 'required|max:255';
            $rules['new_components.*.specifications.*.value'] = 'required|max:255';
            $rules['new_components.*.produced_at'] = 'nullable|date';
        }

        $valid = Validator::make($data, $rules, [
            'is_component.required' => 'Mohon pilih jenis barang (Item atau Component).',
            'is_component.boolean' => 'Pilihan jenis barang hanya ada Item atau Component.',

            'name.required' => 'Mohon lengkapi Nama barang.',
            'name.string' => 'Nama barang harus berupa teks.',
            'name.max' => 'Nama barang tidak boleh lebih dari :max karakter.',

            'serial_code.required' => 'Mohon lengkapi Serial code barang.',
            'serial_code.string' => 'Serial code harus berupa teks.',
            'serial_code.max' => 'Serial code tidak boleh lebih dari :max karakter.',

            'produced_at.date' => 'Tanggal produksi (item Dibuat) tidak valid.',
            'set_id.uuid' => 'Format Set ID tidak valid.',
            'set_id.exists' => 'Set ID yang dipilih tidak ditemukan.',

            'condition.required' => 'Mohon tentukan kondisi barang.',
            'condition.boolean' => 'Kondisi barang hanya boleh berupa Rusak atau Baik.',

            'type.required' => 'Mohon pilih tipe atau buat tipe baru untuk barang ini.',

            'specifications.array' => 'Format spesifikasi tidak valid.',

            'specifications.*.attribute.required' => 'Setiap baris spesifikasi harus memiliki Atribut.',
            'specifications.*.attribute.max' => 'Attribute dari Spesifikasi barang tidak boleh lebih dari :max karakter.',

            'specifications.*.value.required' => 'Setiap baris spesifikasi harus memiliki Nilai.',
            'specifications.*.value.max' => 'Value dari Spesifikasi barang tidak boleh lebih dari :max karakter.',

            'new_components.*.name.required' => 'Nama Komponen baru di Item ini tidak boleh kosong.',
            'new_components.*.serial_code.required' => 'Serial Code Komponen baru di Item ini tidak boleh kosong.',
            'new_components.*.serial_code.distinct' => 'Ada Serial Code Komponen baru di Item ini yang duplikat.',
            'new_components.*.condition.required' => 'Kondisi Komponen baru di Item ini harus dipilih.',
            'new_components.*.type.required' => 'Tipe Komponen baru di Item ini harus dipilih.',
            'new_components.*.produced_at.date' => 'Tanggal produksi Komponen (Komponen Dibuat) tidak valid.',
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
        $data['name'] = $this->upperWords($data['name'] ?? '');

        try {
            DB::beginTransaction();

            $typeId = $this->getOrCreateType($data['type']);

            $barang = null;

            if ($data['is_component'] == false) {
                $barang = Items::create([
                    'name' => $data['name'],
                    'serial_code' => $data['serial_code'],
                    'condition' => $data['condition'],
                    'produced_at' => $data['produced_at'] ?? null,
                    'set_id' => $data['set_id'] ?? null,
                    'type_id' => $typeId,
                ]);

                $newComponentsData = $data['new_components'] ?? [];
                if (!empty($newComponentsData)) {
                    foreach ($newComponentsData as $compData) {
                        $compTypeId = $this->getOrCreateType($compData['type']);

                        $newComp = Components::create([
                            'name' => $this->upperWords($compData['name']),
                            'serial_code' => $compData['serial_code'],
                            'condition' => $compData['condition'],
                            'produced_at' => $compData['produced_at'] ?? null,
                            'type_id' => $compTypeId,
                            'item_id' => $barang->id, // Langsung kaitkan ke Item parent
                        ]);

                        // Proses Spesifikasi untuk Komponen (Child)
                        $this->processSpecifications($newComp, $compData['specifications'] ?? []);
                    }
                }
            } else if ($data['is_component'] == true) {
                $barang = Components::create([
                    'name' => $data['name'],
                    'serial_code' => $data['serial_code'],
                    'condition' => $data['condition'],
                    'produced_at' => $data['produced_at'] ?? null,
                    'type_id' => $typeId,
                ]);
            }

            if (is_null($barang)) {
                DB::rollBack();
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Terjadi Kesalahan saat proses menyimpan Barang.'], 500);
                }
                return redirect()->back()->with('error', 'Terjadi Kesalahan saat proses menyimpan Barang.')->withInput();
            }

            $specs = $data['specifications'] ?? [];
            if (!empty($specs) && is_array($specs)) {
                foreach ($specs as $specRow) {
                    // Abaikan jika data tidak lengkap
                    if (empty($specRow['attribute']) || empty($specRow['value'])) {
                        continue;
                    }

                    // Panggil helper untuk membereskan atribut dan valuenya
                    $specValueId = $this->getOrCreateSpecification(
                        $specRow['attribute'],
                        $specRow['value']
                    );

                    $barang->specSetValues()->attach($specValueId);
                }
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan!', 'data' => $barang]);
            }

            return redirect()->route('admin.items.index')->with('success', 'Barang berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error createItems: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage())->withInput();
        }
    }

    public function createType(Request $request)
    {
        $data = $request->only(['type_name']);
        $valid = Validator::make($data, [
            'type_name' => 'required|unique:types,name',
        ], [
            'type_name.required' => 'Mohon masukkan nama dari Tipe yang ingin dibuat.',
            'type_name.unique' => 'Tipe dengan nama ini sudah ada.'
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
        try {
            $type = $this->getOrCreateType('new::' . $data['type_name']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tipe baru berhasil dibuat.',
                    'data' => [
                        'type_id' => $type
                    ],
                ]);
            }
            return redirect()->back()->with('success', 'Tipe baru berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Error createType: ' . $e->getMessage()); // Log untuk developer

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan server: ' . $e->getMessage())->withInput();
        }
    }

    public function updateCondition(Request $request, Items $item)
    {
        try {
            // Jika item rusak (0) dan ingin diubah jadi bagus (1)
            if ($item->condition == 0) {
                $ongoingRepair = Repairs_item::where('itemable_id', $item->id)
                    ->where('itemable_type', Items::class)
                    ->whereIn('status', [0, 1])
                    ->whereNull('completed_at')
                    ->with('repair')
                    ->first();

                if ($ongoingRepair) {
                    return response()->json([
                        'success' => false,
                        'has_ongoing_repair' => true,
                        'message' => 'Item ini masih dalam proses perbaikan. Selesaikan repair terlebih dahulu.',
                        'repair_data' => [
                            'repair_id' => $ongoingRepair->repair_id,
                            'itemable_id' => $item->id,
                            'issue_description' => $ongoingRepair->issue_description,
                            'repair_url' => route('admin.repairs.index')
                        ]
                    ], 422);
                }
            }

            $item->condition = !$item->condition;
            $item->save();

            $newConditionText = $item->condition ? 'Baik' : 'Rusak';

            return response()->json([
                'success' => true,
                'message' => "Kondisi '{$item->name}' berhasil diubah menjadi '{$newConditionText}'.",
                'new_condition' => $item->condition
            ]);
        } catch (\Exception $e) {
            Log::error('Error updateCondition: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengubah kondisi item.'], 500);
        }
    }

    public function completeRepairFromItem(Request $request, Items $item)
    {
        $validated = $request->validate([
            'repair_id' => 'required|uuid|exists:repairs,id',
            'is_successful' => 'required|boolean',
            'repair_notes' => 'nullable|string|max:255'
        ]);

        try {
            $repairItem = Repairs_item::where('repair_id', $validated['repair_id'])
                ->where('itemable_id', $item->id)
                ->where('itemable_type', Items::class)
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
                $item->update(['condition' => 1]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Repair berhasil diselesaikan.',
                'item_condition' => $item->fresh()->condition
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completeRepairFromItem: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyelesaikan repair.'], 500);
        }
    }

    public function attachToDesk(Request $request, Items $item, Desks $desk)
    {
        // // Validasi lagi untuk keamanan (mencegah race condition)
        // if ($item->desk_id) {
        //     return response()->json(['success' => false, 'message' => 'Item ini sudah terpasang di meja lain.'], 409); // 409 Conflict
        // }

        try {
            $item->desk_id = $desk->id;
            $item->lab_id = null; // Clear lab_id jika attach ke desk
            $item->save();

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' berhasil dipasang ke Meja '{$desk->location}'."
            ]);
        } catch (\Exception $e) {
            Log::error('Error attachToDesk: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memasang item ke meja.'], 500);
        }
    }

    public function attachToLab(Request $request, Items $item, Labs $lab)
    {
        if ($item->desk_id) {
            return response()->json(['success' => false, 'message' => 'Item ini sudah terpasang di meja. Lepas dari meja terlebih dahulu.'], 409);
        }

        // if ($item->lab_id) {
        //     return response()->json(['success' => false, 'message' => 'Item ini sudah terpasang di lab lain.'], 409);
        // }

        try {
            $item->lab_id = $lab->id;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' berhasil dipasang ke Lab '{$lab->name}'."
            ]);
        } catch (\Exception $e) {
            Log::error('Error attachToLab: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memasang item ke lab.'], 500);
        }
    }

    public function detachFromDesk(Request $request, Items $item)
    {
        if (!$item->desk_id) {
            return response()->json(['success' => false, 'message' => 'Item ini tidak terpasang di meja manapun.'], 400);
        }

        try {
            $desk = Desks::where('id', $item->desk_id)->first();
            $item->desk_id = null;
            if ($desk?->lab_id) {
                $item->lab_id = $desk->lab_id;
            }
            $item->save();

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' berhasil dilepas dari meja."
            ]);
        } catch (\Exception $e) {
            Log::error('Error detachFromDesk: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melepas item dari meja.'], 500);
        }
    }

    public function detachFromLab(Request $request, Items $item)
    {
        if (!$item->lab_id) {
            return response()->json(['success' => false, 'message' => 'Item ini tidak terpasang di lab manapun.'], 400);
        }

        try {
            $item->lab_id = null;
            $item->save();

            return response()->json([
                'success' => true,
                'message' => "Item '{$item->name}' berhasil dilepas dari lab."
            ]);
        } catch (\Exception $e) {
            Log::error('Error detachFromLab: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melepas item dari lab.'], 500);
        }
    }

    public function getItemFilters()
    {
        $types = Type::all();
        $specifications = SpecAttributes::with('specValues')->get();

        return response()->json([
            'success' => true,
            'message' => 'Sukses mengambil Type dan Spesifikasi yang ada',
            'data' => [
                'types' => $types,
                'specifications' => $specifications,
            ]
        ]);
    }

    public function getUnaffiliatedItems(Request $request)
    {
        $request->validate([
            'type_id' => 'nullable|uuid|exists:types,id',
            'spec_value_id' => 'nullable|uuid|exists:spec_set_value,id',
        ]);

        $typeId = $request->input('type_id');
        $specValueId = $request->input('spec_value_id');

        $query = Items::query()->where('condition', 1)->whereNull('desk_id'); // unaffiliated, kondisi bagus

        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        if ($specValueId) {
            $query->whereHas('specSetValues', function (Builder $q) use ($specValueId) {
                $q->where('spec_set_value.id', $specValueId);
            });
        }

        $items = $query->orderBy('name')
            ->get(['id', 'name', 'serial_code']);

        return response()->json([
            'success' => true,
            'message' => 'Sukses menganmbil Item yang Avaliable dan Berfungsi',
            'data' => [
                'items' => $items
            ],
        ]);
    }

    private function upperWords(?string $text): string
    {
        return strtoupper(trim($text ?? ''));
    }

    private function getOrCreateType($type)
    {
        if (Str::startsWith($type, 'new::')) {
            $newTypeName = trim(Str::after($type, 'new::'));
            if (empty($newTypeName)) {
                throw new \Exception('Nama tipe baru tidak boleh kosong.');
            }
            $newTypeName = $this->upperWords($newTypeName ?? '');
            $type = Type::firstOrCreate(['name' => $newTypeName]);
            return $type->id;
        }

        return $type; // UUID
    }

    private function getOrCreateSpecification($attrInput, $valInput)
    {
        if (Str::startsWith($attrInput, 'new::')) {
            $attrName = trim(Str::after($attrInput, 'new::'));
            if (empty($attrName)) throw new \Exception("Nama Atribut tidak valid.");
            $attrName = $this->upperWords($attrName ?? '');
            $attribute = SpecAttributes::firstOrCreate(['name' => $attrName]);
        } else {
            $attrInput = $this->upperWords($attrInput ?? '');
            $attribute = SpecAttributes::find($attrInput);
            if (!$attribute) throw new \Exception("Attribute dengan ID '$attrInput' tidak ditemukan.");
        }
        if (Str::startsWith($valInput, 'new::')) {
            $valName = trim(Str::after($valInput, 'new::'));
            if (empty($valName)) throw new \Exception("Nilai Value tidak valid.");
            $valName = $this->upperWords($valName ?? '');
            $specValue = SpecSetValue::firstOrCreate([
                'spec_attributes_id' => $attribute->id,
                'value' => $valName
            ]);
        } else {
            $valInput = $this->upperWords($valInput ?? '');
            $specValue = SpecSetValue::find($valInput);
            if (!$specValue) throw new \Exception("Spec Value dengan ID '$valInput' tidak ditemukan.");

            if ($specValue->spec_attributes_id !== $attribute->id) {
                throw new \Exception("Data tidak cocok: Value '$specValue->value' bukan milik Attribute '$attribute->name'.");
            }
        }

        return $specValue->id;
    }
    private function processSpecifications($model, $specifications)
    {
        if (empty($specifications) || !is_array($specifications)) {
            return;
        }

        foreach ($specifications as $specRow) {
            if (empty($specRow['attribute']) || empty($specRow['value'])) {
                continue;
            }

            $specValueId = $this->getOrCreateSpecification(
                $specRow['attribute'],
                $specRow['value']
            );

            $model->specSetValues()->attach($specValueId);
        }
    }

    public function getAvailableComponents(Request $request, Items $item)
    {
        try {
            $components = Components::whereNull('item_id')
                ->where('condition', 1)
                ->with(['type', 'specSetValues.specAttributes'])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'components' => $components
            ]);
        } catch (\Exception $e) {
            Log::error('Error getAvailableComponents: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat komponen.'], 500);
        }
    }

    public function attachComponent(Request $request, Items $item, Components $component)
    {
        // if ($component->item_id) {
        //     return response()->json(['success' => false, 'message' => 'Komponen sudah terpasang di item lain.'], 409);
        // }

        try {
            $component->item_id = $item->id;
            $component->lab_id = null;
            $component->save();

            return response()->json([
                'success' => true,
                'message' => "Komponen '{$component->name}' berhasil dipasang ke '{$item->name}'."
            ]);
        } catch (\Exception $e) {
            Log::error('Error attachComponent: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memasang komponen.'], 500);
        }
    }

    public function detachComponent(Request $request, Items $item, Components $component)
    {
        if ($component->item_id != $item->id) {
            return response()->json(['success' => false, 'message' => 'Komponen tidak terpasang di item ini.'], 400);
        }

        try {
            $component->item_id = null;
            $component->save();

            return response()->json([
                'success' => true,
                'message' => "Komponen '{$component->name}' berhasil dilepas dari '{$item->name}'."
            ]);
        } catch (\Exception $e) {
            Log::error('Error detachComponent: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal melepas komponen.'], 500);
        }
    }
}
