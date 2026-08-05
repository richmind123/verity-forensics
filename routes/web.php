<?php

use Illuminate\Support\Facades\Route;
use App\Models\AuditLog;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/api/timeline-data', function () {
    return response()->json(AuditLog::timelineData());
});

require __DIR__.'/settings.php';
