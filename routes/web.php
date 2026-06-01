<?php

use App\Http\Controllers\Groups\GroupInvitationController;
use App\Http\Middleware\EnsureGroupMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_group}')
    ->middleware(['auth', 'verified', EnsureGroupMembership::class])
    ->group(function () {
        Route::inertia('dashboard', 'dashboard')->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('invitations/{invitation}/accept', [GroupInvitationController::class, 'accept'])->name('invitations.accept');
});

// Admin UI and API routes.
Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\IndexController::class, 'index'])->name('index');

        // Admin API routes.
        Route::apiResource('users', \App\Http\Controllers\Admin\UserController::class);

        // Add more admin resources here as needed.
        // Route::apiResource('groups', \App\Http\Controllers\Admin\GroupController::class);
        // Route::apiResource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    });

require __DIR__.'/settings.php';
