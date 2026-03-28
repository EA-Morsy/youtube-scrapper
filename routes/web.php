<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;

Route::get('/', [PlaylistController::class, 'index'])->name('playlists.index');

Route::prefix('api')->group(function () {
    Route::post('/scrape', [PlaylistController::class, 'scrape'])->name('api.scrape');
    Route::get('/playlists', [PlaylistController::class, 'getPlaylists'])->name('api.playlists');
    Route::get('/categories', [PlaylistController::class, 'getCategories'])->name('api.categories');
});

Route::get('/debug', function() {
    return view('debug');
});

Route::get('/api/test', [PlaylistController::class, 'test']);

Route::get('/logs', function() {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        // Get last 50 lines
        $lines = explode("\n", $logs);
        $recentLogs = array_slice($lines, -50);
        return '<pre>' . implode("\n", $recentLogs) . '</pre>';
    }
    return 'No log file found.';
});

Route::get('/api/simple-test', [PlaylistController::class, 'simpleTest']);

Route::get('/api/basic-test', [PlaylistController::class, 'basicTest']);
