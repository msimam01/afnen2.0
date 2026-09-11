<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TenantAdminProvisioner
{
    /**
     * Provision the initial tenant administrator in the TENANT database.
     *
     * This method must be called within an initialized tenant context.
     *
     * The administrator data must contain a name, an email and a plaintext
     * `password` (resolved from the encrypted pending_tenant_administrators
     * record). The plaintext password is only used to create the hashed
     * `users.password` value and is NEVER logged and NEVER stored in
     * plaintext. An existing administrator's password is NEVER overwritten.
     *
     * @param  array  $adminData  Administrator data (name, email, password)
     */
    public static function provision(array $adminData): void
    {
        $email = $adminData['email'] ?? null;

        if (empty($email)) {
            throw new RuntimeException('[TenantAdminProvisioner] Administrator email is required for provisioning.');
        }

        $name = $adminData['name'] ?? null;

        if (empty($name)) {
            throw new RuntimeException('[TenantAdminProvisioner] Administrator name is required for provisioning.');
        }

        $password = $adminData['password'] ?? null;

        if (empty($password)) {
            throw new RuntimeException('[TenantAdminProvisioner] Administrator password is required for provisioning.');
        }

        Log::info('[TenantAdminProvisioner] Provisioning tenant administrator', [
            'email' => $email,
        ]);

        // Hash the plaintext credential. This is the ONLY place the plaintext
        // password exists, and it is never persisted or logged in plaintext.
        $passwordHash = Hash::make($password);

        // Use firstOrCreate to keep provisioning idempotent: if the
        // administrator already exists (e.g. the job was retried), their
        // password and attributes are NOT overwritten.
        $admin = User::firstOrCreate(
            [
                'email' => $email,
            ],
            [
                'name' => $name,
                'password' => $passwordHash,
                'email_verified_at' => now(),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            Log::info('[TenantAdminProvisioner] Administrator created in tenant database', [
                'email' => $admin->email,
            ]);
        } else {
            Log::info('[TenantAdminProvisioner] Administrator already exists, credentials left untouched', [
                'email' => $admin->email,
            ]);
        }

        // Assign tenant-admin role (idempotent)
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
     * Get default test administrator credentials for automated tests only.
     *
     * This MUST only be used by the test command (afnen:test-tenant) and test
     * fixtures — never by the normal tenant creation/provisioning flow.
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
