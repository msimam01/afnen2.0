<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TenantRoleProvisioner
{
    /**
     * Provision default roles and permissions for a tenant.
     *
     * This method must be called within a tenant context.
     */
    public static function provision(): void
    {
        Log::info('[TenantRoleProvisioner] Provisioning roles and permissions');

        // Create all permissions
        self::createPermissions();

        // Create roles and assign permissions
        self::createTenantAdminRole();
        self::createAgentRole();

        Log::info('[TenantRoleProvisioner] Roles and permissions provisioned successfully');
    }

    /**
     * Create all required permissions.
     */
    private static function createPermissions(): void
    {
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Farmers
            'farmers.view',
            'farmers.create',
            'farmers.update',
            'farmers.delete',

            // Farms
            'farms.view',
            'farms.create',
            'farms.update',
            'farms.delete',

            // Applications
            'applications.view',
            'applications.create',
            'applications.update',
            'applications.approve',
            'applications.reject',

            // Centers
            'centers.view',
            'centers.create',
            'centers.update',
            'centers.delete',

            // Agents
            'agents.view',
            'agents.create',
            'agents.update',
            'agents.delete',

            // Collection Verification
            'collection-verifications.view',
            'collection-verifications.create',
            'collection-verifications.approve',
            'collection-verifications.reject',

            // Return Verification
            'return-verifications.view',
            'return-verifications.create',
            'return-verifications.approve',
            'return-verifications.reject',

            // Commodity Returns
            'commodity-returns.view',
            'commodity-returns.create',
            'commodity-returns.update',

            // Monetary Returns
            'monetary-returns.view',
            'monetary-returns.create',
            'monetary-returns.update',

            // Reports
            'reports.view',
            'reports.export',

            // Tenant Settings
            'settings.view',
            'settings.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        Log::info('[TenantRoleProvisioner] Permissions created', [
            'count' => count($permissions),
        ]);
    }

    /**
     * Create tenant-admin role with all permissions.
     */
    private static function createTenantAdminRole(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'tenant-admin',
            'guard_name' => 'web',
        ]);

        // Assign all permissions to tenant-admin
        $allPermissions = Permission::all();
        $role->syncPermissions($allPermissions);

        Log::info('[TenantRoleProvisioner] tenant-admin role created with all permissions', [
            'permission_count' => $allPermissions->count(),
        ]);
    }

    /**
     * Create agent role with field-operation permissions.
     */
    private static function createAgentRole(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'agent',
            'guard_name' => 'web',
        ]);

        // Assign only field-operation permissions to agent
        $agentPermissions = [
            // Dashboard
            'dashboard.view',

            // Farmers
            'farmers.view',

            // Farms
            'farms.view',

            // Applications
            'applications.view',

            // Centers
            'centers.view',

            // Collection Verification
            'collection-verifications.view',
            'collection-verifications.create',
            'collection-verifications.approve',
            'collection-verifications.reject',

            // Return Verification
            'return-verifications.view',
            'return-verifications.create',
            'return-verifications.approve',
            'return-verifications.reject',

            // Commodity Returns
            'commodity-returns.view',
            'commodity-returns.create',
            'commodity-returns.update',
        ];

        $permissions = Permission::whereIn('name', $agentPermissions)->get();
        $role->syncPermissions($permissions);

        Log::info('[TenantRoleProvisioner] agent role created with field-operation permissions', [
            'permission_count' => $permissions->count(),
        ]);
    }
}
