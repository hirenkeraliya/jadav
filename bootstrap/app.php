<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'company.selected' => \App\Http\Middleware\EnsureCompanySelected::class,
            'company.theme'    => \App\Http\Middleware\SetActiveCompanyTheme::class,
            'super.admin'      => \App\Http\Middleware\SuperAdminOnly::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetActiveCompanyTheme::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
