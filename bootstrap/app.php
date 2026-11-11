<?php

use App\Http\Middleware\PermissionMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);

        // Arahkan tamu yang belum login ke portal login yang sesuai
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*') || $request->is('internal/*')) {
                return route('internal.login');
            }

            return route('login');
        });

        // Arahkan user yang sudah terautentikasi ke dashboard masing-masing
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            if ($user && $user->role !== 'customer') {
                return route('admin.dashboard');
            }

            return route('customer.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
