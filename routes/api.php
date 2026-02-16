// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;

// Simple load test endpoint (no CSRF/session)
Route::get('/load-test', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Functional download test endpoint
Route::post('/download-test', [ReelController::class, 'download'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->middleware('throttle:60,1'); // optional: higher limit for testing