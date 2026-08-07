<?php

use App\Http\Middleware\EnsureAccountIsActive;
use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\EnsureTwoFactorIsEnabled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\FortifyServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => EnsureRole::class,
            'active' => EnsureAccountIsActive::class,
        ]);

        // Every authenticated route should confirm the account is Active,
        // not just logged in — replaces the legacy "Pending" gate that used
        // to live inline in auth_check.php. Mandatory 2FA for Admin/Teacher
        // (Step: cross-cutting) is appended after it for the same reason —
        // both need to run on effectively every request, not just specific
        // route groups.
        $middleware->appendToGroup('web', [
            EnsureAccountIsActive::class,
            EnsureTwoFactorIsEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
