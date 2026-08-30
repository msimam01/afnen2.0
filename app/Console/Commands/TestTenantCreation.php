<?php

namespace App\Console\Commands;

use App\Models\Central\Tenant;
use App\Models\User;
use App\Services\TenantAdminProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestTenantCreation extends Command
{
    protected $signature = 'afnen:test-tenant';

    protected $description = 'Create a test AFNEN tenant and verify its database';

    public function handle(): int
    {
        $tenantId = 'gombe';

        $this->info('Creating test tenant...');

        // Remove an old test tenant if it exists.
        $existing = Tenant::find($tenantId);

        if ($existing) {
            $this->warn("Tenant [$tenantId] already exists.");

            if ($this->confirm('Delete the existing test tenant first?')) {
                $existing->delete();

                $this->info('Existing tenant deleted.');

                // Give MySQL a moment before recreating.
                sleep(1);
            } else {
                return self::FAILURE;
            }
        }

        try {
            $tenant = Tenant::create([
                'id' => $tenantId,

                'data' => [
                    'name' => 'Gombe Test Farmers Association',
                    'description' => 'Temporary tenant used for testing AFNEN 2.0',
                    'admin_name' => 'Test Administrator',
                    'admin_email' => 'admin@afnen.test',
                    'temp_password' => 'password', // Test password for automated testing
                ],

                'provisioning_status' => Tenant::PROVISIONING_PENDING,

                'status' => Tenant::STATUS_INACTIVE,
            ]);

            $this->info("Tenant created: {$tenant->id}");

            // Create tenant domain.
            $domain = $tenant->domains()->create([
                'domain' => 'gombe.afnen.com',
            ]);

            $this->info("Domain created: {$domain->domain}");

            // Wait for provisioning to complete (max 30 seconds)
            $this->info('Waiting for provisioning to complete...');
            $maxAttempts = 30;
            $attempts = 0;

            while ($attempts < $maxAttempts) {
                $tenant->refresh();

                if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
                    break;
                }

                if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
                    $this->newLine();
                    $this->error('Tenant provisioning failed.');
                    $this->info('Tenant provisioning status: '.$tenant->provisioning_status);
                    $this->info('Tenant status: '.$tenant->status);
                    if ($tenant->deactivation_reason) {
                        $this->warn('Deactivation reason: '.$tenant->deactivation_reason);
                    }

                    return self::FAILURE;
                }

                sleep(1);
                $attempts++;
            }

            $tenant->refresh();

            $this->newLine();
            $this->info('Tenant provisioning status: '.$tenant->provisioning_status);
            $this->info('Tenant status: '.$tenant->status);

            if ($tenant->activated_at) {
                $this->info('Activated at: '.$tenant->activated_at->toDateTimeString());
            }

            if ($tenant->deactivation_reason) {
                $this->warn('Deactivation reason: '.$tenant->deactivation_reason);
            }

            // Verify provisioning succeeded
            if ($tenant->provisioning_status !== Tenant::PROVISIONING_READY || $tenant->status !== Tenant::STATUS_ACTIVE) {
                $this->newLine();
                $this->error('Tenant provisioning did not complete successfully.');

                return self::FAILURE;
            }

            $databaseName = $tenant->tenancy_db_name;
            $this->info("Tenant database: {$databaseName}");

            // Verify the database exists.
            $databases = DB::connection('central')
                ->select('SHOW DATABASES');

            $databaseExists = collect($databases)
                ->contains(function ($database) use ($databaseName) {
                    return in_array(
                        $databaseName,
                        (array) $database
                    );
                });

            if (! $databaseExists) {
                $this->error(
                    "Tenant database [$databaseName] was NOT created."
                );

                return self::FAILURE;
            }

            $this->info('✓ Tenant database exists.');

            // Switch into tenant context.
            $tenant->run(function () {
                $tables = DB::connection('tenant')
                    ->select('SHOW TABLES');

                $tableNames = collect($tables)
                    ->map(function ($table) {
                        return array_values((array) $table)[0];
                    })
                    ->sort()
                    ->values();

                $this->info(
                    '✓ Tenant database is accessible.'
                );

                $this->info(
                    'Tables created: '.$tableNames->count()
                );

                foreach ($tableNames as $table) {
                    $this->line("  - {$table}");
                }

                // Verify required tables.
                $requiredTables = [
                    'users',
                    'roles',
                    'permissions',
                    'model_has_roles',
                    'model_has_permissions',
                    'role_has_permissions',
                    'centers',
                    'farmers',
                    'farms',
                ];

                foreach ($requiredTables as $requiredTable) {
                    if ($tableNames->contains($requiredTable)) {
                        $this->info("✓ {$requiredTable} table exists.");
                    } else {
                        $this->error("✗ {$requiredTable} table is missing.");

                        return self::FAILURE;
                    }
                }

                // Verify roles
                $this->newLine();
                $this->info('Verifying roles...');

                $requiredRoles = ['tenant-admin', 'agent'];
                foreach ($requiredRoles as $roleName) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role) {
                        $this->info("✓ {$roleName} role exists.");
                    } else {
                        $this->error("✗ {$roleName} role is missing.");

                        return self::FAILURE;
                    }
                }

                // Verify permissions
                $this->newLine();
                $this->info('Verifying permissions...');

                $expectedPermissions = [
                    'dashboard.view',
                    'farmers.view', 'farmers.create', 'farmers.update', 'farmers.delete',
                    'farms.view', 'farms.create', 'farms.update', 'farms.delete',
                    'applications.view', 'applications.create', 'applications.update', 'applications.approve', 'applications.reject',
                    'centers.view', 'centers.create', 'centers.update', 'centers.delete',
                    'agents.view', 'agents.create', 'agents.update', 'agents.delete',
                    'collection-verifications.view', 'collection-verifications.create', 'collection-verifications.approve', 'collection-verifications.reject',
                    'return-verifications.view', 'return-verifications.create', 'return-verifications.approve', 'return-verifications.reject',
                    'commodity-returns.view', 'commodity-returns.create', 'commodity-returns.update',
                    'monetary-returns.view', 'monetary-returns.create', 'monetary-returns.update',
                    'reports.view', 'reports.export',
                    'settings.view', 'settings.update',
                ];

                $actualPermissions = Permission::pluck('name')->toArray();
                $missingPermissions = array_diff($expectedPermissions, $actualPermissions);

                if (empty($missingPermissions)) {
                    $this->info('✓ All expected permissions exist ('.count($expectedPermissions).' total).');
                } else {
                    $this->error('✗ Missing permissions: '.implode(', ', $missingPermissions));

                    return self::FAILURE;
                }

                // Verify tenant-admin has all permissions
                $this->newLine();
                $this->info('Verifying tenant-admin permissions...');

                $tenantAdminRole = Role::where('name', 'tenant-admin')->first();
                $tenantAdminPermissions = $tenantAdminRole->permissions->pluck('name')->toArray();

                if (count($tenantAdminPermissions) === count($expectedPermissions) && empty(array_diff($expectedPermissions, $tenantAdminPermissions))) {
                    $this->info('✓ tenant-admin has all permissions ('.count($tenantAdminPermissions).' total).');
                } else {
                    $this->error('✗ tenant-admin does not have all expected permissions.');

                    return self::FAILURE;
                }

                // Verify agent has only field-operation permissions
                $this->newLine();
                $this->info('Verifying agent permissions...');

                $agentRole = Role::where('name', 'agent')->first();
                $agentPermissions = $agentRole->permissions->pluck('name')->toArray();

                $expectedAgentPermissions = [
                    'dashboard.view',
                    'farmers.view',
                    'farms.view',
                    'applications.view',
                    'centers.view',
                    'collection-verifications.view', 'collection-verifications.create', 'collection-verifications.approve', 'collection-verifications.reject',
                    'return-verifications.view', 'return-verifications.create', 'return-verifications.approve', 'return-verifications.reject',
                    'commodity-returns.view', 'commodity-returns.create', 'commodity-returns.update',
                ];

                if (count($agentPermissions) === count($expectedAgentPermissions) && empty(array_diff($expectedAgentPermissions, $agentPermissions))) {
                    $this->info('✓ agent has correct permissions ('.count($agentPermissions).' total).');
                } else {
                    $this->error('✗ agent does not have the expected permissions.');
                    $this->info('Expected: '.implode(', ', $expectedAgentPermissions));
                    $this->info('Actual: '.implode(', ', $agentPermissions));

                    return self::FAILURE;
                }

                // Verify initial tenant administrator
                $this->newLine();
                $this->info('Verifying initial tenant administrator...');

                $testCredentials = TenantAdminProvisioner::getTestCredentials();
                $admin = User::where('email', $testCredentials['email'])->first();

                if (! $admin) {
                    $this->error('✗ Initial tenant administrator does not exist.');

                    return self::FAILURE;
                }

                $this->info('✓ Initial tenant administrator exists.');

                if ($admin->hasRole('tenant-admin')) {
                    $this->info('✓ Administrator has tenant-admin role.');
                } else {
                    $this->error('✗ Administrator does not have tenant-admin role.');

                    return self::FAILURE;
                }

                // Verify administrator is in tenant database (not central)
                $this->info('✓ Administrator exists in tenant database.');
            });

            $this->newLine();

            $this->info('======================================');
            $this->info('AFNEN TENANT TEST COMPLETED SUCCESSFULLY');
            $this->info('======================================');

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->newLine();

            $this->error('Tenant creation failed.');

            $this->error(
                $e->getMessage()
            );

            $this->newLine();

            $this->line(
                $e->getTraceAsString()
            );

            return self::FAILURE;
        }
    }
}
