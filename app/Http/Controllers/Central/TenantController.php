<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('domains')
            ->latest()
            ->paginate(20);

        return inertia('Central/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    public function create()
    {
        return inertia('Central/Tenants/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id' => [
                'required',
                'string',
                'alpha_dash',
                'max:50',
                'unique:tenants,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'domain' => [
                'required',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $host = parse_url(
            $validated['domain'],
            PHP_URL_HOST
        ) ?: $validated['domain'];

        $host = strtolower($host);

        if (! preg_match(
            '/^[a-z0-9][a-z0-9-]*\.afnen\.com$/',
            $host
        )) {
            return back()
                ->withErrors([
                    'domain' => 'Domain must be a valid AFNEN subdomain, e.g. gombe.afnen.com',
                ])
                ->withInput();
        }

        if (
            \Stancl\Tenancy\Database\Models\Domain::where(
                'domain',
                $host
            )->exists()
        ) {
            return back()
                ->withErrors([
                    'domain' => 'This domain is already in use.',
                ])
                ->withInput();
        }

        try {
            $tenant = Tenant::create([
                'id' => $validated['id'],

                'data' => [
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'created_by' => auth()->id(),
                ],

                'provisioning_status' =>
                    Tenant::PROVISIONING_PENDING,

                'status' =>
                    Tenant::STATUS_INACTIVE,
            ]);

            $tenant->domains()->create([
                'domain' => $host,
            ]);

            return redirect()
                ->route('central.tenants.index')
                ->with(
                    'success',
                    'Tenant created. Database provisioning has started.'
                );

        } catch (\Throwable $e) {

            Log::error('Tenant creation failed', [
                'tenant_id' => $validated['id'],
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors([
                    'general' =>
                        'Unable to create tenant. Please try again.',
                ])
                ->withInput();
        }
    }
}