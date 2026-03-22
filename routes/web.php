<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::patch('schedule/{event}/approve', [ScheduleController::class, 'approve'])->name('schedule.approve')->middleware('role:admin');
    Route::patch('schedule/{event}/reject', [ScheduleController::class, 'reject'])->name('schedule.reject')->middleware('role:admin');
});

require __DIR__.'/settings.php';
