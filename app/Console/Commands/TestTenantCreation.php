<?php

namespace App\Console\Commands;

use App\Models\Central\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestTenantCreation extends Command
{
    protected $signature = 'afnen:test-tenant';

    protected $description = 'Create a test AFNEN tenant and verify its database';

    public function handle(): int
    {
        $tenantId = 'gombe-test';

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
                ],

                'provisioning_status' =>
                    Tenant::PROVISIONING_PENDING,

                'status' =>
                    Tenant::STATUS_INACTIVE,
            ]);

            $this->info("Tenant created: {$tenant->id}");

            // Create tenant domain.
            $domain = $tenant->domains()->create([
                'domain' => 'gombe-test.afnen.com',
            ]);

            $this->info("Domain created: {$domain->domain}");

            // The TenantCreated event should already have
            // created and migrated the tenant database.
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
                    'Tables created: ' . $tableNames->count()
                );

                foreach ($tableNames as $table) {
                    $this->line("  - {$table}");
                }

                // Verify some critical tables.
                $requiredTables = [
                    'users',
                    'centers',
                    'farmers',
                    'farms',
                ];

                foreach ($requiredTables as $requiredTable) {
                    if ($tableNames->contains($requiredTable)) {
                        $this->info(
                            "✓ {$requiredTable} table exists."
                        );
                    } else {
                        $this->warn(
                            "⚠ {$requiredTable} table is missing."
                        );
                    }
                }
            });

            $this->newLine();

            $this->info('======================================');
            $this->info('AFNEN TENANT TEST COMPLETED');
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