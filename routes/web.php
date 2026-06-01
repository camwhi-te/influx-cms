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

require __DIR__.'/settings.php';
