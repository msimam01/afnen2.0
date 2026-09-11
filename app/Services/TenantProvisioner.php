<?php

namespace App\Services;

use App\Mail\TenantAdministratorOnboarding;
use App\Models\Central\PendingTenantAdministrator;
use App\Models\Central\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class TenantProvisioner
{
    /**
     * Provision the tenant database (roles, permissions and the initial
     * administrator).
     *
     * The administrator credentials are read from the CENTRAL
     * `pending_tenant_administrators` table BEFORE the tenant context is
     * initialized. There is NO fallback to test credentials: if the pending
     * administrator data is missing, provisioning fails loudly and the tenant
     * is left for manual/automatic retry.
     */
    public static function provision(Tenant $tenant): void
    {
        Log::info('[TenantProvisioner] Provisioning tenant', [
            'tenant_id' => $tenant->id,
        ]);

        // Resolve the pending administrator on the central connection BEFORE
        // initializing tenancy (after initialize(), the default database
        // context belongs to the tenant).
        $pendingAdmin = PendingTenantAdministrator::query()
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($pendingAdmin === null) {
            throw new RuntimeException(
                "[TenantProvisioner] Missing tenant administrator provisioning data for tenant [{$tenant->id}]. Provisioning aborted. A pending administrator record must be created with the tenant."
            );
        }

        $adminCredentials = [
            'name' => $pendingAdmin->name,
            'email' => $pendingAdmin->email,
            'password' => $pendingAdmin->password, // decrypted by the `encrypted` cast; TenantAdminProvisioner hashes it
        ];

        Log::info('[TenantProvisioner] Administrator data resolved', [
            'tenant_id' => $tenant->id,
            'admin_email' => $adminCredentials['email'],
        ]);

        // Initialize the tenant database/context.
        tenancy()->initialize($tenant);

        try {
            // Provision roles and permissions
            TenantRoleProvisioner::provision();

            // Provision the exact tenant administrator supplied at creation
            TenantAdminProvisioner::provision($adminCredentials);

            // Generate the tenant login URL
            $tenantDomain = $tenant->domains->first()->domain ?? null;
            $loginUrl = $tenantDomain ? "https://{$tenantDomain}/login" : null;

            // Send onboarding email with temporary credentials
            if ($loginUrl) {
                try {
                    Mail::to($adminCredentials['email'])->send(
                        new TenantAdministratorOnboarding(
                            tenantName: $tenant->name,
                            administratorName: $adminCredentials['name'],
                            administratorEmail: $adminCredentials['email'],
                            temporaryPassword: $adminCredentials['password'],
                            loginUrl: $loginUrl,
                        )
                    );

                    Log::info('[TenantProvisioner] Onboarding email sent successfully', [
                        'tenant_id' => $tenant->id,
                        'admin_email' => $adminCredentials['email'],
                    ]);
                } catch (\Throwable $emailError) {
                    // Log email failure but don't fail the provisioning
                    Log::error('[TenantProvisioner] Failed to send onboarding email', [
                        'tenant_id' => $tenant->id,
                        'admin_email' => $adminCredentials['email'],
                        'error' => $emailError->getMessage(),
                    ]);
                }
            }

            // Only after successful administrator creation and role
            // assignment, remove the temporary central credential record.
            $pendingAdmin->delete();

            Log::info('[TenantProvisioner] Tenant initialized successfully', [
                'tenant_id' => $tenant->id,
                'admin_email' => $adminCredentials['email'],
            ]);
        } catch (\Throwable $e) {
            Log::error('[TenantProvisioner] Provisioning failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            tenancy()->end();
        }
    }
}
