<?php

use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::get('users', [UserRoleController::class, 'index'])->name('users.index');
    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])->name('users.roles.update');

    Route::resource('emails', EmailController::class)->except(['show']);
    Route::patch('emails/{email}/send', [EmailController::class, 'send'])->name('emails.send');
});
