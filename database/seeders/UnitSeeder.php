<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Permission_group;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitNames = [
            'MAHASISWA',
            'TU INFOR',
            'KEPALA LAB',
            'ASISTEN DOSEN',
            'ASISTEN LAB',
            'DOSEN',
            'PTIK',
            'UPPK',
            'ADMIN'
        ];

        $units = [];
        foreach ($unitNames as $name) {
            $units[$name] = Unit::create([
                'name' => $name,
                'description' => 'Unit kerja ' . ucfirst(strtolower($name)),
            ]);
        }

        $permissionStructure = [
            'Booking System' => [
                ['name' => 'View Booking Form', 'route' => 'user.booking.form', 'action' => 'GET'],
                ['name' => 'Submit Booking Request', 'route' => 'user.booking.request', 'action' => 'POST'],
            ],
            'Public Resources' => [
                ['name' => 'View Labs List', 'route' => 'user.get.labs', 'action' => 'GET'],
                ['name' => 'View All Items', 'route' => 'user.get.items', 'action' => 'GET'],
                ['name' => 'View Items By Lab', 'route' => 'user.get.items.by.lab', 'action' => 'GET'],
            ],
            'Admin Dashboard' => [
                ['name' => 'View Admin Dashboard', 'route' => 'admin.dashboard', 'action' => 'GET'],
            ],
            'Laboratory Management' => [
                ['name' => 'Admin View Labs', 'route' => 'admin.labs', 'action' => 'GET'],
                ['name' => 'Admin List Labs Data', 'route' => 'admin.labs.list', 'action' => 'GET'],
                ['name' => 'View Lab Desks', 'route' => 'admin.labs.desks', 'action' => 'GET'],
                ['name' => 'Update Desk Location', 'route' => 'admin.labs.desks.update.location', 'action' => 'POST'],
                ['name' => 'Batch Create Desks', 'route' => 'admin.labs.desks.batch.create', 'action' => 'POST'],
                ['name' => 'Batch Delete Desks', 'route' => 'admin.labs.desks.batch.delete', 'action' => 'POST'],
                ['name' => 'Store Items to Desk', 'route' => 'admin.desks.store.items', 'action' => 'POST'],
            ],
            'Item & Inventory Management' => [
                ['name' => 'Admin View Items', 'route' => 'admin.items', 'action' => 'GET'],
                ['name' => 'Create Single Item', 'route' => 'admin.items.create', 'action' => 'POST'],
                ['name' => 'Create Item Set', 'route' => 'admin.items.set.create', 'action' => 'POST'],
                ['name' => 'Get Item Filters', 'route' => 'admin.items.filters', 'action' => 'GET'],
                ['name' => 'Get Unaffiliated Items', 'route' => 'admin.items.unaffiliated', 'action' => 'GET'],
                ['name' => 'View Item Details', 'route' => 'admin.items.details', 'action' => 'GET'],
                ['name' => 'View Component Details', 'route' => 'admin.components.details', 'action' => 'GET'],
                ['name' => 'Update Item Condition', 'route' => 'admin.items.updateCondition', 'action' => 'PATCH'],
                ['name' => 'Update Component Condition', 'route' => 'admin.items.updateComponentCondition', 'action' => 'PATCH'],
                ['name' => 'Attach Item to Desk', 'route' => 'admin.items.attachToDesk', 'action' => 'POST'],
            ],
            'Repair Management' => [
                ['name' => 'View Repairs List', 'route' => 'admin.repairs', 'action' => 'GET'],
                ['name' => 'Update Repair Status', 'route' => 'admin.repairs.updateStatus', 'action' => 'PATCH'],
                ['name' => 'Apply/Report Repair', 'route' => 'admin.items.repair', 'action' => 'POST'],
            ],
            'Access Management' => [
                ['name' => 'View Permissions', 'route' => 'admin.permissions', 'action' => 'GET'],
                ['name' => 'Update User Permissions', 'route' => 'admin.permissions.update', 'action' => 'POST'],
                ['name' => 'View Roles', 'route' => 'admin.roles', 'action' => 'GET'],
                ['name' => 'Update User Roles', 'route' => 'admin.roles.update', 'action' => 'POST'],
                ['name' => 'Create Unit', 'route' => 'admin.units.create', 'action' => 'POST'],
            ],
            'Matkul Management' => [
                ['name' => 'View Matkul', 'route' => 'admin.matkul', 'action' => 'GET'],
                ['name' => 'Create Matkul', 'route' => 'admin.matkul.store', 'action' => 'POST'],
                ['name' => 'Update Matkul', 'route' => 'admin.matkul.update', 'action' => 'PUT'],
                ['name' => 'Delete Matkul', 'route' => 'admin.matkul.destroy', 'action' => 'DELETE'],
            ],
            'Repository Management' => [
                ['name' => 'View Repository', 'route' => 'admin.repository', 'action' => 'GET'],
                ['name' => 'Create Folder', 'route' => 'admin.repository.folder.create', 'action' => 'POST'],
                ['name' => 'Upload File', 'route' => 'admin.repository.upload', 'action' => 'POST'],
                ['name' => 'Rename Folder', 'route' => 'admin.repository.folder.rename', 'action' => 'PUT'],
                ['name' => 'Rename File', 'route' => 'admin.repository.file.rename', 'action' => 'PUT'],
                ['name' => 'Delete Folder', 'route' => 'admin.repository.folder.delete', 'action' => 'DELETE'],
                ['name' => 'Delete File', 'route' => 'admin.repository.file.delete', 'action' => 'DELETE'],
                ['name' => 'Download File', 'route' => 'admin.repository.file.download', 'action' => 'GET'],
                ['name' => 'Move Folder', 'route' => 'admin.repository.folder.move', 'action' => 'PUT'],
                ['name' => 'Move File', 'route' => 'admin.repository.file.move', 'action' => 'PUT'],
                ['name' => 'Get Folder Files', 'route' => 'admin.repository.folder.files', 'action' => 'GET'],
            ],
            'Module Management' => [
                ['name' => 'View All Modules', 'route' => 'admin.modules', 'action' => 'GET'],
                ['name' => 'View Matkul Modules', 'route' => 'admin.matkul.modules', 'action' => 'GET'],
                ['name' => 'Create Module', 'route' => 'admin.matkul.modules.store', 'action' => 'POST'],
                ['name' => 'Update Module', 'route' => 'admin.matkul.modules.update', 'action' => 'PUT'],
                ['name' => 'Delete Module', 'route' => 'admin.matkul.modules.destroy', 'action' => 'DELETE'],
            ],
        ];

        $allCreatedPermissions = [];

        foreach ($permissionStructure as $groupName => $routes) {
            $group = Permission_group::updateOrCreate(
                ['name' => $groupName],
                ['description' => "Permissions related to $groupName"]
            );

            foreach ($routes as $routeData) {

                $permission = Permission::updateOrCreate(
                    ['route' => $routeData['route']],
                    [
                        'name' => $routeData['name'],
                        'action' => $routeData['action'],
                        'permission_group_id' => $group->id,
                    ]
                );

                $allCreatedPermissions[] = $permission->id;
            }
        }

        $adminUnit = $units['ADMIN'];
        $adminUnit->permissions()->sync($allCreatedPermissions);
        
        User::create([
            'name' => 'Kevin - Super Admin',
            'email' => 'c14230088@john.petra.ac.id',
            'unit_id' => $adminUnit->id,
        ]);

        User::create([
            'name' => 'Nataniel - Super Admin',
            'email' => 'c14240154@john.petra.ac.id',
            'unit_id' => $adminUnit->id,
        ]);

        User::create([
            'name' => 'Evan - Super Admin',
            'email' => 'c14240069@john.petra.ac.id',
            'unit_id' => $adminUnit->id,
        ]);

        User::create([
            'name' => 'Jeremy - Super Admin',
            'email' => 'c14240090@john.petra.ac.id',
            'unit_id' => $adminUnit->id,
        ]);

        User::create([
            'name' => 'Bryan - Super Admin',
            'email' => 'c14240085@john.petra.ac.id',
            'unit_id' => $adminUnit->id,
        ]);

        User::create([
            'name' => 'Axel - Super Admin',
            'email' => 'c14240157@john.petra.ac.id',
            'unit_id' => $adminUnit->id,
        ]);
        $this->command->info('All permissions assigned to ADMIN Unit.');
    }
}
