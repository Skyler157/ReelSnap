<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;



Route::get('/', [ReelController::class, 'index'])->name('home');

Route::post('/download', [ReelController::class, 'download'])
    ->name('download')
    ->middleware('throttle:10,1'); // limits to 10 requests per minute per IP

Route::get('/download/file', [ReelController::class, 'downloadFile'])
    ->name('download.file')
    ->middleware('signed');

Route::get('/preview', [ReelController::class, 'preview'])
    ->name('preview')
    ->middleware('signed');
