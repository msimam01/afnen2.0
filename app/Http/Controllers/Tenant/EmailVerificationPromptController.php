<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Show the email verification prompt page.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $tenantName = tenant('data.name') ?? 'Tenant';

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('tenant.dashboard', absolute: false))
                    : Inertia::render('tenant/verify-email', [
                        'status' => $request->session()->get('status'),
                        'tenantName' => $tenantName,
                    ]);
    }
}
