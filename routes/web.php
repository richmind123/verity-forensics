<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/api/timeline-data', function () {
    return response()->json(AuditLog::timelineData());
});

require __DIR__.'/settings.php';
