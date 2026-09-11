<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\AuthenticatedSessionController;
use App\Http\Controllers\Tenant\EmailVerificationNotificationController;
use App\Http\Controllers\Tenant\EmailVerificationPromptController;
use App\Http\Controllers\Tenant\NewPasswordController;
use App\Http\Controllers\Tenant\PasswordResetLinkController;
use App\Http\Controllers\Tenant\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
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

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('tenant.password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('tenant.password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('tenant.password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('tenant.password.reset.store');
    });

    // Password settings (accessible even when must_change_password is true)
    Route::middleware(['auth'])->group(function () {
        Route::get('settings/password', function (Request $request) {
            return inertia('tenant/settings-password', [
                'mustChangePassword' => $request->user()->must_change_password,
                'tenantName' => tenant('data.name'),
            ]);
        })->name('tenant.password.edit');

        Route::put('settings/password', function (Request $request) {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $request->user()->update([
                'password' => Hash::make($validated['password']),
                'must_change_password' => false,
            ]);

            return back()->with('success', 'Password changed successfully.');
        })->name('tenant.settings.password.update');

        // Email verification
        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('tenant.verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('tenant.verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('tenant.verification.send');
    });

    // Protected tenant routes
    Route::middleware(['auth', 'must.change.password'])->group(function () {
        Route::get('dashboard', function () {
            return inertia('tenant/dashboard', [
                'tenantName' => tenant('data.name'),
                'userName' => auth()->user()?->name,
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
