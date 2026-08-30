<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are for tenant-specific AFNEN operations.
| They are only accessible from tenant domains (e.g., gombe.afnen.com).
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return redirect()->route('tenant.dashboard');
    });

    // Tenant authentication routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('tenant.login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    });

    // Protected tenant routes
    Route::middleware(['auth'])->group(function () {
        Route::get('dashboard', function () {
            return inertia('tenant/dashboard', [
                'tenantName' => tenant('data.name'),
                'userName' => auth()->user()?->name,
                'userRole' => auth()->user()?->getRoleNames()->first(),
                'userPermissions' => auth()->user()?->getAllPermissions()->pluck('name') ?? [],
            ]);
        })->name('tenant.dashboard');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('tenant.logout');

        // Tenant module placeholder routes
        Route::get('farmers', function () {
            return inertia('tenant/farmers');
        })->name('tenant.farmers');

        Route::get('farms', function () {
            return inertia('tenant/farms');
        })->name('tenant.farms');

        Route::get('applications', function () {
            return inertia('tenant/applications');
        })->name('tenant.applications');

        Route::get('centers', function () {
            return inertia('tenant/centers');
        })->name('tenant.centers');

        Route::get('agents', function () {
            return inertia('tenant/agents');
        })->name('tenant.agents');

        Route::get('collections', function () {
            return inertia('tenant/collections');
        })->name('tenant.collections');

        Route::get('returns', function () {
            return inertia('tenant/returns');
        })->name('tenant.returns');

        Route::get('reports', function () {
            return inertia('tenant/reports');
        })->name('tenant.reports');

        Route::get('commodity-returns', function () {
            return inertia('tenant/commodity-returns');
        })->name('tenant.commodity-returns');

        Route::get('monetary-returns', function () {
            return inertia('tenant/monetary-returns');
        })->name('tenant.monetary-returns');

        Route::get('settings', function () {
            return inertia('tenant/settings');
        })->name('tenant.settings');
    });
});
