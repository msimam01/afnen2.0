<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantAdminProvisioner
{
    /**
     * Provision the initial tenant administrator.
     *
     * This method must be called within a tenant context.
     *
     * @param  array  $adminData  Administrator data (name, email, password)
     */
    public static function provision(array $adminData): void
    {
        Log::info('[TenantAdminProvisioner] Provisioning tenant administrator');

        // Use firstOrCreate to ensure idempotency
        $admin = User::firstOrCreate(
            [
                'email' => $adminData['email'],
            ],
            [
                'name' => $adminData['name'],
                'password' => Hash::make($adminData['password']),
                'email_verified_at' => now(),
            ]
        );

        // Assign tenant-admin role
        if (! $admin->hasRole('tenant-admin')) {
            $admin->assignRole('tenant-admin');
            Log::info('[TenantAdminProvisioner] Assigned tenant-admin role to administrator', [
                'email' => $admin->email,
            ]);
        } else {
            Log::info('[TenantAdminProvisioner] Administrator already has tenant-admin role', [
                'email' => $admin->email,
            ]);
        }

        Log::info('[TenantAdminProvisioner] Tenant administrator provisioned successfully', [
            'email' => $admin->email,
        ]);
    }

    /**
     * Get default test administrator credentials for development/testing.
     */
    public static function getTestCredentials(): array
    {
        return [
            'name' => 'Test Administrator',
            'email' => 'admin@afnen.test',
            'password' => 'password',
        ];
    }
}
