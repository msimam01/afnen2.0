<?php

use App\Http\Controllers\Central\TenantController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Central Routes
|--------------------------------------------------------------------------
|
| These routes are for the central AFNEN administration.
| They are only accessible from central domains (localhost, 127.0.0.1).
|
*/

Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('welcome');
    })->name('home');

    // Central authentication routes
    require __DIR__.'/auth.php';

    // Protected central routes
    Route::middleware(['auth'])->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('dashboard');
        })->name('dashboard');

        // Central tenant management routes
        Route::resource('tenants', TenantController::class)
            ->names([
                'index' => 'tenants.index',
                'create' => 'tenants.create',
                'store' => 'tenants.store',
                'show' => 'tenants.show',
                'edit' => 'tenants.edit',
                'update' => 'tenants.update',
                'destroy' => 'tenants.destroy',
            ]);

        Route::get('commodities', function () {
            return Inertia::render('central/commodities');
        })->name('central.commodities');

        Route::get('commodity-categories', function () {
            return Inertia::render('central/commodity-categories');
        })->name('central.commodity-categories');

        Route::get('seasons', function () {
            return Inertia::render('central/seasons');
        })->name('central.seasons');

        Route::get('allocations', function () {
            return Inertia::render('central/allocations');
        })->name('central.allocations');

        Route::get('market-prices', function () {
            return Inertia::render('central/market-prices');
        })->name('central.market-prices');

        Route::get('reports', function () {
            return Inertia::render('central/reports');
        })->name('central.reports');

        // Central settings
        require __DIR__.'/settings.php';
    });
});

// Load tenant routes only for non-central domains
// This is safe because tenant routes have their own middleware to prevent access from central domains
$centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);
if (! in_array(request()->getHost(), $centralDomains)) {
    require __DIR__.'/tenant.php';
}
