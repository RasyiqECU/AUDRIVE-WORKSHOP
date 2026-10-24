<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AiScanReviewController;


/*
|--------------------------------------------------------------------------
| Admin AI Scan Review Routes
|--------------------------------------------------------------------------
*/


Route::prefix('ai-scans')->name('ai-scans.')->middleware(['auth', 'role:technician,admin'])->group(function () {
    Route::get('/', [AiScanReviewController::class, 'index'])->name('index');
    Route::get('/{aiScan}', [AiScanReviewController::class, 'show'])->name('show');
    Route::patch('/{aiScan}/review', [AiScanReviewController::class, 'review'])->name('review');
    Route::get('/vehicle/{vehicleId}/history', [AiScanReviewController::class, 'vehicleHistory'])->name('vehicle-history');
});



