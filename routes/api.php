// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReelController;

// Simple load test endpoint (no CSRF/session)
Route::get('/load-test', function () {
return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

// Functional download test endpoint
Route::get('/load-test', function() {
return response()->json(['status' => 'ok', 'timestamp' => now()]);
});

Route::post('/download-test', function() {
return response()->json([
'download_url' => 'https://www.instagram.com/reel/DU6HoHRCppE'
]);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);