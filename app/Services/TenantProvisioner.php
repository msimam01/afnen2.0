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
            $adminCredentials = TenantAdminProvisioner::getTestCredentials();
            TenantAdminProvisioner::provision($adminCredentials);

            Log::info('[TenantProvisioner] Tenant initialized successfully', [
                'tenant_id' => $tenant->id,
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
