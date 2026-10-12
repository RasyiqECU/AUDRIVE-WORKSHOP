<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\AiScanController;


/*
|--------------------------------------------------------------------------
| Customer AI Scan Routes
|--------------------------------------------------------------------------
*/


Route::prefix('ai-scan')->name('ai-scan.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [AiScanController::class, 'index'])->name('index');
    Route::get('/create', [AiScanController::class, 'create'])->name('create');
    Route::post('/', [AiScanController::class, 'store'])->name('store');
    Route::get('/{aiScan}', [AiScanController::class, 'show'])->name('show');
});



