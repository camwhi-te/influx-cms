<?php

use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\Groups\GroupController;
use App\Http\Controllers\Groups\GroupInvitationController;
use App\Http\Controllers\Groups\GroupMemberController;
use App\Http\Middleware\EnsureGroupMembership;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/security', [SecurityController::class, 'edit'])
        ->middleware(RequirePassword::class)
        ->name('security.edit');

    Route::put('settings/password', [SecurityController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::inertia('settings/appearance', 'settings/appearance')->name('appearance.edit');

    Route::get('settings/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('settings/groups', [GroupController::class, 'store'])->name('groups.store');

    Route::middleware(EnsureGroupMembership::class)->group(function () {
        Route::get('settings/groups/{group}', [GroupController::class, 'edit'])->name('groups.edit');
        Route::patch('settings/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
        Route::delete('settings/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
        Route::post('settings/groups/{group}/switch', [GroupController::class, 'switch'])->name('groups.switch');

        Route::patch('settings/groups/{group}/members/{user}', [GroupMemberController::class, 'update'])->name('groups.members.update');
        Route::delete('settings/groups/{group}/members/{user}', [GroupMemberController::class, 'destroy'])->name('groups.members.destroy');

        Route::post('settings/groups/{group}/invitations', [GroupInvitationController::class, 'store'])->name('groups.invitations.store');
        Route::delete('settings/groups/{group}/invitations/{invitation}', [GroupInvitationController::class, 'destroy'])->name('groups.invitations.destroy');
    });
});
