<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreTenantRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Jobs\ProvisionTenantJob;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $tenants = Tenant::with('domains')
            ->when($search, function ($query, $search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('data->name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        return inertia('tenants/index', [
            'tenants' => $tenants,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return inertia('tenants/create');
    }

    public function store(StoreTenantRequest $request)
    {
        $validated = $request->validated();

        $baseDomain = config('tenancy.base_domain', 'afnen.com');
        $domain = "{$validated['slug']}.{$baseDomain}";

        if (Domain::where('domain', $domain)->exists()) {
            return back()
                ->withErrors([
                    'slug' => 'This tenant slug is already in use.',
                ])
                ->withInput();
        }

        try {
            // Generate temporary password for the administrator
            $tempPassword = Str::random(16);

            $tenant = Tenant::create([
                'id' => $validated['slug'],

                'data' => [
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'admin_name' => $validated['admin_name'],
                    'admin_email' => $validated['admin_email'],
                    'temp_password' => $tempPassword, // Temporary password for one-time display
                    'temp_password_shown' => false, // Flag to track if password was shown
                    'created_by' => auth()->id(),
                ],

                'provisioning_status' => Tenant::PROVISIONING_PENDING,

                'status' => Tenant::STATUS_INACTIVE,
            ]);

            $tenant->domains()->create([
                'domain' => $domain,
            ]);

            ProvisionTenantJob::dispatch($tenant);

            return redirect()
                ->route('tenants.show', $tenant)
                ->with('success', 'Tenant created successfully. Provisioning has started.');

        } catch (\Throwable $e) {
            Log::error('Tenant creation failed', [
                'tenant_id' => $validated['slug'],
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors([
                    'general' => 'Unable to create tenant. Please try again.',
                ])
                ->withInput();
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        // Get temporary password if available and not yet shown
        $tempPassword = null;
        if (isset($tenant->data['temp_password']) && ! ($tenant->data['temp_password_shown'] ?? false)) {
            $tempPassword = $tenant->data['temp_password'];

            // Mark password as shown and clear it from tenant data
            $tenant->update([
                'data' => array_merge($tenant->data, [
                    'temp_password_shown' => true,
                    'temp_password' => null, // Clear the password after showing
                ]),
            ]);
        }

        return inertia('tenants/show', [
            'tenant' => $tenant,
            'temp_password' => $tempPassword,
        ]);
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');

        return inertia('tenants/edit', [
            'tenant' => $tenant,
        ]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $validated = $request->validated();

        try {
            $existingData = $tenant->data ?? [];
            $tenant->update([
                'data' => array_merge($existingData, [
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                ]),
            ]);

            return redirect()
                ->route('tenants.show', $tenant)
                ->with('success', 'Tenant updated successfully.');

        } catch (\Throwable $e) {
            Log::error('Tenant update failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors([
                    'general' => 'Unable to update tenant. Please try again.',
                ])
                ->withInput();
        }
    }

    public function destroy(Tenant $tenant)
    {
        return back()
            ->withErrors([
                'general' => 'Tenant deletion is not currently available. Please contact system administrator.',
            ]);
    }
}
