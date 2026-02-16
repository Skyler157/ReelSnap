<?php

use Illuminate\Support\Facades\Route;

// Simple infrastructure endpoint for load tests.
Route::get('/load-test', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Functional endpoint for load test wiring checks.
Route::post('/download-test', function () {
    return response()->json([
        'download_url' => 'https://www.instagram.com/reel/DU6HoHRCppE',
    ]);
});
