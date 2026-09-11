<?php

use App\Models\Central\PendingTenantAdministrator;
use App\Models\Central\Tenant;
use App\Models\User;
use App\Services\TenantAdminProvisioner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Clean up any existing test tenants and their databases
    $existingTenants = ['gombe-test', 'isolation-test'];
    foreach ($existingTenants as $tenantId) {
        $existing = Tenant::find($tenantId);
        if ($existing) {
            // Delete the tenant (this will drop the database)
            $existing->delete();
        }
    }
});

afterEach(function () {
    // Clean up test tenant after each test
    $existing = Tenant::find('gombe-test');
    if ($existing) {
        $existing->delete();
    }
});

test('tenant administrator exists after provisioning', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Check if pending admin was deleted (indicates successful provisioning)
    $pendingAdmin = PendingTenantAdministrator::where('tenant_id', $tenant->id)->first();
    expect($pendingAdmin)->toBeNull();

    // Verify administrator exists in tenant database
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin)->not->toBeNull();
        expect($admin->name)->toBe($testCredentials['name']);
        expect($admin->email)->toBe($testCredentials['email']);
        expect($admin->must_change_password)->toBeTrue();
    });
});

test('administrator has tenant-admin role', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Verify role assignment
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin->hasRole('tenant-admin'))->toBeTrue();
        expect($admin->hasRole('agent'))->toBeFalse();
    });
});

test('correct password can authenticate', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Verify authentication with correct password
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        $authenticated = Auth::attempt([
            'email' => $testCredentials['email'],
            'password' => $testCredentials['password'],
        ]);

        expect($authenticated)->toBeTrue();
        Auth::logout();
    });
});

test('incorrect password fails authentication', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Verify authentication fails with incorrect password
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        $authenticated = Auth::attempt([
            'email' => $testCredentials['email'],
            'password' => 'wrong-password',
        ]);

        expect($authenticated)->toBeFalse();
    });
});

test('authentication happens against tenant database', function () {
    // Use a different tenant ID to avoid conflicts
    $tenantId = 'isolation-test';
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    // Clean up if exists
    $existing = Tenant::find($tenantId);
    if ($existing) {
        $existing->delete();
    }

    $tenant = Tenant::create([
        'id' => $tenantId,
        'data' => [
            'name' => 'Isolation Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'isolation-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Verify user exists only in tenant database, not central
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin)->not->toBeNull();
    });

    // Verify user does NOT exist in central database
    $centralUser = User::where('email', 'admin@afnen.test')->first();
    expect($centralUser)->toBeNull();

    // Clean up
    $tenant->delete();
});

test('provisioning is idempotent', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for first provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Get user count after first provisioning
    $userCountAfterFirst = 0;
    $tenant->run(function () use (&$userCountAfterFirst) {
        $userCountAfterFirst = User::count();
    });

    // Run provisioning again (simulate re-provisioning)
    tenancy()->initialize($tenant);
    TenantAdminProvisioner::provision($testCredentials);
    tenancy()->end();

    // Verify no duplicate users were created
    $userCountAfterSecond = 0;
    $tenant->run(function () use (&$userCountAfterSecond) {
        $userCountAfterSecond = User::count();
    });

    expect($userCountAfterSecond)->toBe($userCountAfterFirst);
});

test('newly provisioned administrator must change password', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Verify administrator has must_change_password flag
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin->must_change_password)->toBeTrue();
    });
});

test('password change clears must_change_password flag', function () {
    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Test Tenant',
        ],
        'provisioning_status' => Tenant::PROVISIONING_PENDING,
        'status' => Tenant::STATUS_INACTIVE,
    ]);

    $tenant->domains()->create([
        'domain' => 'gombe-test.afnen.com',
    ]);

    // Create pending administrator record
    PendingTenantAdministrator::create([
        'tenant_id' => $tenant->id,
        'name' => $testCredentials['name'],
        'email' => $testCredentials['email'],
        'password' => $testCredentials['password'],
    ]);

    // Wait for provisioning
    $maxAttempts = 30;
    $attempts = 0;

    while ($attempts < $maxAttempts) {
        $tenant->refresh();
        if ($tenant->provisioning_status === Tenant::PROVISIONING_READY) {
            break;
        }
        if ($tenant->provisioning_status === Tenant::PROVISIONING_FAILED) {
            $this->fail('Tenant provisioning failed');
        }
        sleep(1);
        $attempts++;
    }

    // Simulate password change
    $tenant->run(function () use ($testCredentials) {
        $admin = User::where('email', $testCredentials['email'])->first();

        $admin->update([
            'password' => Hash::make('new-password-123'),
            'must_change_password' => false,
        ]);

        // Verify flag is cleared
        $admin->refresh();
        expect($admin->must_change_password)->toBeFalse();

        // Verify new password works
        $authenticated = Auth::attempt([
            'email' => $testCredentials['email'],
            'password' => 'new-password-123',
        ]);

        expect($authenticated)->toBeTrue();
        Auth::logout();
    });
});
