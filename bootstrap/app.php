<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\IsCentralAdmin;
use App\Http\Middleware\MustChangePassword;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'central.admin' => IsCentralAdmin::class,
            'must.change.password' => MustChangePassword::class,
        ]);

        $middleware->redirectGuestsTo(function () {
            $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);
            if (! in_array(request()->getHost(), $centralDomains)) {
                return route('tenant.login');
            }

            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
