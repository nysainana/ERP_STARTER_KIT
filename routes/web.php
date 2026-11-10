<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')
        ->middleware('permission.auto')
        ->name('dashboard');

    Route::get('users', [UserController::class, 'index'])
        ->middleware('permission.auto')
        ->name('users.index');

    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission.auto')
        ->name('users.store');

    Route::patch('users/{user}', [UserController::class, 'update'])
        ->middleware('permission.auto')
        ->name('users.update');

    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission.auto')
        ->name('users.destroy');

    Route::get('roles', [RoleController::class, 'index'])
        ->middleware('permission.auto')
        ->name('roles.index');

    Route::post('roles', [RoleController::class, 'store'])
        ->middleware('permission.auto')
        ->name('roles.store');

    Route::patch('roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission.auto')
        ->name('roles.update');

    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission.auto')
        ->name('roles.destroy');
});

require __DIR__.'/settings.php';
