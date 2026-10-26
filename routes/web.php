<?php

use App\Models\Items;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SetController;
use App\Http\Controllers\LabsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesksController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\MatkulController;
use App\Http\Controllers\MatkulConfigController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\FundingController;

Route::get('/', function () {
    return view('user.landing', ['title' => 'User | Landing Page']);
})->name('user.landing');

Route::get('/login', [UnitController::class, 'login'])->name('user.login');
Route::get('/processLogin/auth/google', [UnitController::class, 'processLogin'])->name('user.processLogin');
Route::get('/auth/google/callback', [UnitController::class, 'routeAdmin'])->name('user.processRouting');
Route::get('/logout', [UnitController::class, 'logout'])->name('user.logout');

Route::get('/dev-login', function () {
    $user = \App\Models\User::where('email', 'c14240157@john.petra.ac.id')->first();
    \Illuminate\Support\Facades\Auth::login($user);
    request()->session()->regenerate();
    return redirect()->route('admin.dashboard');
});

Route::middleware(['auth.permission'])->group(function () {
    Route::get('/booking', [BookingController::class, 'formBooking'])->name('user.booking.form');
    Route::post('/booking', [BookingController::class, 'storeBooking'])->name('user.booking.request');
    Route::get('/booking/history', [BookingController::class, 'getMyBookings'])->name('user.booking.history');

    Route::get('/get/labs', [LabsController::class, 'labsList'])->name('user.get.labs');
    Route::get('/get/items', [ItemsController::class, 'getItems'])->name('user.get.items');
    Route::get('/get/items/by-lab/{lab}', [ItemsController::class, 'getItemsByLab'])->name('user.get.items.by.lab');
    Route::get('/labs/{lab}/available-sets', [LabsController::class, 'getAvailableSets'])->name('user.labs.availableSets');
    Route::get('/labs/{lab}/desk-map', [LabsController::class, 'getDeskMapWithAvailability'])->name('user.labs.deskMap');

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/labs', [AdminController::class, 'labs'])->name('admin.labs');
        Route::get('/labs/list', [LabsController::class, 'labsList'])->name('admin.labs.list');
        Route::get('/labs/{lab}/desks', [LabsController::class, 'getDesks'])->name('admin.labs.desks');
        Route::get('/labs/{lab}/storage', [LabsController::class, 'getLabStorage'])->name('admin.labs.storage');
        Route::post('/labs/{lab}/desks/update/location/{desk}', [DesksController::class, 'updateLocation'])->name('admin.labs.desks.update.location');
        Route::post('/labs/{lab}/desks/batch-create', [DesksController::class, 'batchCreate'])->name('admin.labs.desks.batch.create');
        Route::post('/labs/{lab}/desks/batch-delete', [DesksController::class, 'batchDelete'])->name('admin.labs.desks.batch.delete');

        Route::get('/items', [AdminController::class, 'items'])->name('admin.items');
        Route::post('/items', [ItemsController::class, 'createItems'])->name('admin.items.create');
        Route::post('/items/set', [ItemsController::class, 'createItemSet'])->name('admin.items.set.create');

        Route::get('/items/filters', [ItemsController::class, 'getItemFilters'])->name('admin.items.filters'); // return all types dan all specsAttribute beserta valuesnya
        Route::get('/items/unaffiliated', [ItemsController::class, 'getUnaffiliatedItems'])->name('admin.items.unaffiliated');
        Route::post('/desks/{desk}/items', [DesksController::class, 'storeItems'])->name('admin.desks.store.items');

        Route::get('/items/{item}/details', [ItemsController::class, 'getItemDetails'])->name('admin.items.details');
        Route::get('/components/{component}/details', [ComponentsController::class, 'getComponentDetails'])->name('admin.components.details');

        Route::patch('/items/{item}/condition', [ItemsController::class, 'updateCondition'])->name('admin.items.updateCondition');
        Route::patch('/components/{component}/condition', [ComponentsController::class, 'updateComponentCondition'])->name('admin.items.updateComponentCondition');
        Route::post('/items/{item}/attach-desk/{desk}', [ItemsController::class, 'attachToDesk'])->name('admin.items.attachToDesk');
        Route::post('/items/{item}/detach-desk', [ItemsController::class, 'detachFromDesk'])->name('admin.items.detachFromDesk');
        Route::post('/items/{item}/attach-lab/{lab}', [ItemsController::class, 'attachToLab'])->name('admin.items.attachToLab');
        Route::post('/items/{item}/detach-lab', [ItemsController::class, 'detachFromLab'])->name('admin.items.detachFromLab');
        Route::post('/items/{item}/complete-repair', [ItemsController::class, 'completeRepairFromItem'])->name('admin.items.completeRepair');
        Route::post('/components/{component}/detach-item', [ComponentsController::class, 'detachFromItem'])->name('admin.components.detachFromItem');
        Route::post('/components/{component}/attach-lab/{lab}', [ComponentsController::class, 'attachToLab'])->name('admin.components.attachToLab');
        Route::post('/components/{component}/detach-lab', [ComponentsController::class, 'detachFromLab'])->name('admin.components.detachFromLab');
        Route::post('/components/{component}/complete-repair', [ComponentsController::class, 'completeRepairFromComponent'])->name('admin.components.completeRepair');

        // Manage components
        Route::get('/items/{item}/available-components', [ItemsController::class, 'getAvailableComponents'])->name('admin.items.availableComponents');
        Route::post('/items/{item}/attach-component/{component}', [ItemsController::class, 'attachComponent'])->name('admin.items.attachComponent');
        Route::post('/items/{item}/detach-component/{component}', [ItemsController::class, 'detachComponent'])->name('admin.items.detachComponent');

        // repair
        Route::get('/repairs', [RepairController::class, 'viewRepairs'])->name('admin.repairs.index');
        Route::get('/repairs/view', [RepairController::class, 'viewRepairs'])->name('admin.repairs');
        Route::patch('/repairs/{repair}/status', [RepairController::class, 'updateRepairStatus'])->name('admin.repairs.updateStatus');
        Route::post('/item/items/repair', [RepairController::class, 'applyRepair'])->name('admin.items.repair');

        // booking
        Route::get('/bookings', [BookingController::class, 'bookings'])->name('admin.bookings');
        Route::get('/bookings/{booking}/details', [BookingController::class, 'getBookingDetails'])->name('admin.bookings.details');
        Route::post('/bookings/{booking}/approve', [BookingController::class, 'approveBooking'])->name('admin.bookings.approve');
        Route::post('/bookings/{booking}/updateReturnDeadline', [BookingController::class, 'updateReturnDeadline'])->name('admin.bookings.updateReturnDeadline');
        Route::post('/bookings/{booking}/return', [BookingController::class, 'returnBooking'])->name('admin.bookings.return');

        // period
        Route::get('/periods', [PeriodController::class, 'periods'])->name('admin.periods');
        Route::post('/periods/create', [PeriodController::class, 'createPeriod'])->name('admin.periods.create');
        Route::patch('/periods/{id}/updateActive', [PeriodController::class, 'setActivePeriod'])->name('admin.periods.updateActive');

        // software
        Route::get('/softwares', [SoftwareController::class, 'softwares'])->name('admin.softwares');
        Route::post('/softwares/create', [SoftwareController::class, 'createSoftware'])->name('admin.softwares.create');
        Route::post('/softwares/{id}/update', [SoftwareController::class, 'editSoftware'])->name('admin.softwares.update');
        Route::post('/softwares/{id}/delete', [SoftwareController::class, 'deleteSoftware'])->name('admin.softwares.delete');

        // Sets
        Route::get('/sets', [SetController::class, 'index'])->name('admin.sets');
        Route::get('/sets/{set}/details', [SetController::class, 'getSetDetails'])->name('admin.sets.details');
        Route::post('/sets/{set}/attach-desks', [SetController::class, 'attachSetToDesk'])->name('admin.sets.attachDesks');
        Route::post('/sets/{set}/attach-lab', [SetController::class, 'attachSetToLab'])->name('admin.sets.attachLab');
        Route::post('/sets/{set}/detach-desks', [SetController::class, 'detachSetFromDesks'])->name('admin.sets.detachDesks');
        Route::post('/sets/{set}/detach-labs', [SetController::class, 'detachSetFromLabs'])->name('admin.sets.detachLabs');

        // History
        Route::get('/history/labs', [HistoryController::class, 'historylabs'])->name('admin.historylabs');
        Route::get('/history/inventaris', [HistoryController::class, 'historyinventaris'])->name('admin.historyinventaris');


        // RBAC
        // Permissions
        Route::get('/permissions', [PermissionController::class, 'permissions'])->name('admin.permissions');
        Route::post('/permissions/update', [PermissionController::class, 'updateUserPermissions'])->name('admin.permissions.update');

        // Roles
        Route::get('/roles', [PermissionController::class, 'roles'])->name('admin.roles');
        Route::post('/roles/update', [PermissionController::class, 'updateUserRoles'])->name('admin.roles.update');

        // Units
        Route::post('/units/create', [PermissionController::class, 'createUnit'])->name('admin.units.create');

        // Matkul
        Route::get('/matkul', [MatkulController::class, 'index'])->name('admin.matkul');
        Route::post('/matkul', [MatkulController::class, 'store'])->name('admin.matkul.store');
        Route::put('/matkul/{matkul}', [MatkulController::class, 'update'])->name('admin.matkul.update');
        Route::delete('/matkul/{matkul}', [MatkulController::class, 'destroy'])->name('admin.matkul.destroy');

        // Matkul Configs
        Route::get('/matkul/{matkul}/configs/{period}', [MatkulConfigController::class, 'show'])->name('admin.matkul.configs.show');
        Route::post('/matkul/{matkul}/configs', [MatkulConfigController::class, 'store'])->name('admin.matkul.configs.store');
        Route::put('/matkul/{matkul}/configs/{config}', [MatkulConfigController::class, 'update'])->name('admin.matkul.configs.update');
        Route::delete('/matkul/{matkul}/configs/{config}', [MatkulConfigController::class, 'destroy'])->name('admin.matkul.configs.destroy');
        Route::post('/matkul-configs/copy', [MatkulConfigController::class, 'copy'])->name('admin.matkul.configs.copy');

        // Repository
        Route::get('/repository', [RepositoryController::class, 'index'])->name('admin.repository');
        Route::post('/repository/folder', [RepositoryController::class, 'createFolder'])->name('admin.repository.folder.create');
        Route::post('/repository/upload', [RepositoryController::class, 'uploadFile'])->name('admin.repository.upload');
        Route::put('/repository/folder/{folder}/rename', [RepositoryController::class, 'renameFolder'])->name('admin.repository.folder.rename');
        Route::put('/repository/file/{file}/rename', [RepositoryController::class, 'renameFile'])->name('admin.repository.file.rename');
        Route::delete('/repository/folder/{folder}', [RepositoryController::class, 'deleteFolder'])->name('admin.repository.folder.delete');
        Route::delete('/repository/file/{file}', [RepositoryController::class, 'deleteFile'])->name('admin.repository.file.delete');
        Route::get('/repository/file/{file}/download', [RepositoryController::class, 'downloadFile'])->name('admin.repository.file.download');
        Route::put('/repository/folder/{folder}/move', [RepositoryController::class, 'moveFolder'])->name('admin.repository.folder.move');
        Route::put('/repository/file/{file}/move', [RepositoryController::class, 'moveFile'])->name('admin.repository.file.move');
        Route::get('/repository/folder/{folder}/files', [RepositoryController::class, 'getFolderFiles'])->name('admin.repository.folder.files');

        // Funding
        Route::get('/funding', [FundingController::class, 'viewFunding'])->name('admin.funding');
        Route::post('/funding/store', [FundingController::class, 'storeData'])->name('admin.funding.store');
        Route::delete('/funding/{transaction}', [FundingController::class, 'destroy'])->name('admin.funding.destroy');
        Route::put('/funding/{transaction}/edit', [FundingController::class, 'editData'])->name('admin.funding.edit');
        Route::get('/funding/{transaction}/receipt', [FundingController::class, 'showReceipt'])->name('admin.funding.receipt');
        Route::get('/funding/export', [FundingController::class, 'exportExcel'])->name('admin.funding.export');

        // Modules
        Route::get('/modules', [MatkulController::class, 'allModules'])->name('admin.modules');
        Route::get('/matkul/{matkul}/modules', [MatkulController::class, 'modules'])->name('admin.matkul.modules');
        Route::get('/matkul/{matkul}/modules/{module}/details', [MatkulController::class, 'getModuleDetails'])->name('admin.matkul.modules.details');
        Route::post('/matkul/{matkul}/modules', [MatkulController::class, 'storeModule'])->name('admin.matkul.modules.store');
        Route::put('/matkul/{matkul}/modules/{module}', [MatkulController::class, 'updateModule'])->name('admin.matkul.modules.update');
        Route::delete('/matkul/{matkul}/modules/{module}', [MatkulController::class, 'destroyModule'])->name('admin.matkul.modules.destroy');
    });
});
