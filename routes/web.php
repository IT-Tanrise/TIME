<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Posts;
use App\Livewire\Tasks;
use App\Livewire\BusinessUnits;
use App\Livewire\Partners;
use App\Livewire\Lands;
use App\Livewire\Soils;
use App\Livewire\Projects;
use App\Livewire\RentLands;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;

use App\Livewire\SoilHistories;
   
Route::get('posts', Posts::class)->name('posts')->middleware('auth');
Route::get('tasks', Tasks::class)->name('tasks')->middleware('auth');

// Partners Routes
Route::middleware(['permission:ownerships.access'])->group(function () {
    Route::get('/partners', Partners::class)->name('partners.index');
    Route::get('/partners/business-unit/{businessUnit}', Partners::class)->name('partners.by-business-unit')
        ->where('businessUnit', '[0-9]+');
});

// Land Routes
Route::middleware(['permission:lands.access'])->group(function () {
    Route::get('/lands', Lands::class)->name('lands');
    Route::get('/lands/business-unit/{businessUnit}', Lands::class)->name('lands.by-business-unit')
        ->where('businessUnit', '[0-9]+');
});

Route::get('/projects', Projects::class)->name('projects');

// Business Units Routes
Route::get('/business-units/{view?}/{id?}', BusinessUnits::class)->name('business-units');

// Soil Routes
Route::middleware(['permission:soils.access'])->group(function () {
    Route::get('/soils', Soils::class)->name('soils');
    Route::get('/soils/{soilId}/show', Soils::class)->name('soils.show');
    Route::get('/soils/business-unit/{businessUnit}/{soilId?}', Soils::class)->name('soils.by-business-unit')
    ->where('businessUnit', '[0-9]+');
    // Soil history routes
    Route::get('/soils/{soilId}/history', SoilHistories::class)->name('soils.history');
    //csv
    Route::post('/soils/export', [App\Http\Controllers\SoilExportController::class, 'exportCsv'])
    ->name('soils.export');
});

// Rent Routes
Route::prefix('rents')->name('rents.')->group(function () {
    // Land Rentals
    Route::get('/lands', RentLands::class)->name('lands');
    Route::get('/lands/business-unit/{businessUnit}', RentLands::class)->name('lands.by-business-unit')
        ->where('businessUnit', '[0-9]+');
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['permission:admin.access'])->prefix('admin')->group(function () {
        
        // Role Management
        Route::middleware(['permission:roles.index'])->get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::middleware(['permission:roles.create'])->get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::middleware(['permission:roles.create'])->post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::middleware(['permission:roles.show'])->get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::middleware(['permission:roles.edit'])->get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::middleware(['permission:roles.edit'])->put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::middleware(['permission:roles.delete'])->delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');

        // Permission Management
        Route::middleware(['permission:permissions.index'])->get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::middleware(['permission:permissions.create'])->get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::middleware(['permission:permissions.create'])->post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::middleware(['permission:permissions.show'])->get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::middleware(['permission:permissions.edit'])->get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::middleware(['permission:permissions.edit'])->put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::middleware(['permission:permissions.delete'])->delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // User Management
        Route::middleware(['permission:users.index'])->get('/users', [UserController::class, 'index'])->name('users.index');
        Route::middleware(['permission:users.create'])->get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::middleware(['permission:users.create'])->post('/users', [UserController::class, 'store'])->name('users.store');
        Route::middleware(['permission:users.show'])->get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::middleware(['permission:users.edit'])->get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::middleware(['permission:users.edit'])->put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::middleware(['permission:users.delete'])->delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
});