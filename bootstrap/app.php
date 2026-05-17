<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\CheckBanned;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Exclude routes dari CSRF verification ──────────────────────
        // Diperlukan agar Thunder Client (tanpa browser session) bisa test.
        $middleware->validateCsrfTokens(except: [
            'admin/login',
            'admin/lupa-password',
            'admin/verifikasi',
            'admin/logout',
            'simulasi/hitung',
            'artikel/*/komentar',
        ]);

        // ── Custom middleware aliases ──────────────────────────────────
        $middleware->alias([
            'admin'        => AdminAuth::class,
            'check.banned' => CheckBanned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
