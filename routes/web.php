<?php


use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUDrive Web Routes
|--------------------------------------------------------------------------
|
| Route utama aplikasi AUDrive. Route modular dimuat dari file terpisah.
| Semua route penting diberi nama konsisten.
|
*/


// Health Check — Endpoint monitoring
Route::get('/health', function () {
    return response()->json([
        'status'    => 'ok',
        'timestamp' => now()->toISOString(),
        'version'   => config('app.version', '1.0.0'),
        'environment' => app()->environment(),
        'timezone'  => config('app.timezone'),
    ]);
})->name('health.check');


// Public Pages
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/about', function () { return view('pages.about'); })->name('about');
Route::get('/contact', function () { return view('pages.contact'); })->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.send');

// Generic Dashboard Redirect (Role-based)
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }

    return match($user->role) {
        'super-admin', 'manager', 'sales', 'cashier', 'inventory', 'mechanic' => redirect()->route('admin.dashboard'),
        default => redirect()->route('customer.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');



// Load modular route files (web middleware group)
Route::middleware('web')->group(function () {
    require base_path('routes/auth.php');
    require base_path('routes/marketplace.php');
    require base_path('routes/workshop.php');
    require base_path('routes/spare-parts.php');
});


// Admin Panel Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'role:super-admin|manager|sales|cashier|inventory|mechanic'])
    ->group(base_path('routes/admin.php'));


// Customer Portal Routes
Route::prefix('customer')
    ->name('customer.')
    ->middleware(['auth', 'verified', 'role:customer'])
    ->group(base_path('routes/customer.php'));



