<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        // Kalau guest coba akses route yang butuh login -> lempar ke /login
        $middleware->redirectGuestsTo(fn () => route('login'));

        // Kalau user sudah login coba akses route guest-only (mis. /login) -> lempar ke dashboard
        // Ini yang tadinya menyebabkan redirect loop (default Laravel cari route 'dashboard'
        // yang tidak ada di project ini, sehingga fallback ke '/' dan mengulang lagi ke '/login').
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
