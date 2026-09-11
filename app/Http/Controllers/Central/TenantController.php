<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreTenantRequest;
use App\Http\Requests\Central\UpdateTenantRequest;
use App\Jobs\ProvisionTenantJob;
use App\Models\Central\PendingTenantAdministrator;
use App\Models\Central\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // Securely generate the temporary administrator password. The plaintext
            // value is shown to the central administrator exactly once (via the
            // session flash on the redirect). It is persisted encrypted in the
            // pending_tenant_administrators table until provisioning completes,
            // and only ever stored hashed in the tenant database.
            $temporaryPassword = Str::random(16);

            $tenant = null;

            // Organization metadata only — administrator credentials never live
            // on the tenants table or inside `data`.
            //
            // NOTE: Tenant::create() must NOT run inside a DB::transaction().
            // It fires the synchronous TenantCreated pipeline (CREATE DATABASE
            // + tenant migrations). DDL statements make MySQL implicitly commit
            // the surrounding central transaction, which aborts the later
            // commit with "There is no active transaction".
            $tenant = Tenant::create([
                'id' => $validated['slug'],

                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,

                'created_by' => (string) auth()->id(),

                'provisioning_status' => Tenant::PROVISIONING_PENDING,

                'status' => Tenant::STATUS_INACTIVE,
            ]);

            // Domain + temporary encrypted provisioning input. These are plain
            // DML statements on the central connection, so a transaction is
            // safe here. The provisioner deletes the pending administrator
            // record once the administrator exists in the tenant database.
            DB::transaction(function () use ($tenant, $domain, $validated, $temporaryPassword) {
                $tenant->domains()->create([
                    'domain' => $domain,
                ]);

                PendingTenantAdministrator::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['admin_name'],
                    'email' => $validated['admin_email'],
                    'password' => $temporaryPassword,
                ]);
            });

            Log::info('[TenantController] Tenant administrator data received', [
                'tenant_id' => $tenant->id,
                'admin_name' => $validated['admin_name'],
                'admin_email' => $validated['admin_email'],
            ]);

            // Dispatch exactly ONE provisioning job. It starts only after the
            // central transaction (tenant + domain + pending administrator)
            // has committed.
            ProvisionTenantJob::dispatch($tenant)->afterCommit();

            return redirect()
                ->route('tenants.show', $tenant)
                ->with('success', 'Tenant created successfully. Provisioning has started.')
                ->with('temp_password', $temporaryPassword);

        } catch (\Throwable $e) {
            Log::error('Tenant creation failed', [
                'tenant_id' => $validated['slug'],
                'error' => $e->getMessage(),
            ]);

            // Best-effort cleanup of a partially created tenant. Deleting the
            // tenant also drops its database via the TenantDeleted pipeline.
            if ($tenant !== null) {
                try {
                    Tenant::find($tenant->id)?->delete();
                } catch (\Throwable $cleanup) {
                    Log::error('Tenant cleanup after failed creation failed', [
                        'tenant_id' => $tenant->id,
                        'error' => $cleanup->getMessage(),
                    ]);
                }
            }

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

        // One-time temporary password (only available on the redirect immediately
        // following creation, then removed from the session).
        $temporaryPassword = session()->pull('temp_password');

        // The pending administrator record only exists until provisioning
        // succeeds; its name/email are non-secret and safe to display.
        $pendingAdministrator = PendingTenantAdministrator::query()
            ->where('tenant_id', $tenant->id)
            ->first();

        return inertia('tenants/show', [
            'tenant' => $tenant,
            'temp_password' => $temporaryPassword,
            'pending_administrator' => $pendingAdministrator ? [
                'name' => $pendingAdministrator->name,
                'email' => $pendingAdministrator->email,
            ] : null,
        ]);
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');

        // Name/email of the not-yet-provisioned administrator are non-secret
        // and safe to display; nothing is shown once provisioning completed.
        $pendingAdministrator = PendingTenantAdministrator::query()
            ->where('tenant_id', $tenant->id)
            ->first();

        return inertia('tenants/edit', [
            'tenant' => $tenant,
            'pending_administrator' => $pendingAdministrator ? [
                'name' => $pendingAdministrator->name,
                'email' => $pendingAdministrator->email,
            ] : null,
        ]);
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $validated = $request->validated();

        try {
            $tenant->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
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
