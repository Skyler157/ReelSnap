<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;

Route::get('/', [ReelController::class, 'index']);
Route::post('/download', [ReelController::class, 'download'])->name('download');