<?php

namespace App\Services;

use App\Models\Central\Tenant;
use Illuminate\Support\Facades\Log;

class TenantProvisioner
{
    public static function provision(Tenant $tenant): void
    {
        Log::info('[TenantProvisioner] Provisioning tenant', [
            'tenant_id' => $tenant->id,
        ]);

        // Initialize the tenant database/context.
        tenancy()->initialize($tenant);

        try {
            // Provision roles and permissions
            TenantRoleProvisioner::provision();

            // Provision initial tenant administrator
            // Use provided admin credentials or fall back to test credentials
            if (isset($tenant->data['admin_email']) && ! empty($tenant->data['admin_email'])) {
                $adminName = $tenant->data['admin_name'] ?? 'Administrator';
                $adminEmail = $tenant->data['admin_email'];

                // Use the temporary password if it exists (from web UI), otherwise generate new one
                $adminPassword = $tenant->data['temp_password'] ?? TenantAdminProvisioner::generateSecurePassword();

                $adminCredentials = [
                    'name' => $adminName,
                    'email' => $adminEmail,
                    'password' => $adminPassword,
                ];
            } else {
                // Fall back to test credentials for backward compatibility with test command
                $adminCredentials = TenantAdminProvisioner::getTestCredentials();
            }

            TenantAdminProvisioner::provision($adminCredentials);

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
