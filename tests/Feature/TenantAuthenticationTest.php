<?php

use App\Models\Central\Tenant;
use App\Models\User;
use App\Services\TenantAdminProvisioner;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Clean up any existing test tenant
    $existing = Tenant::find('gombe-test');
    if ($existing) {
        $existing->delete();
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

    // Verify administrator exists in tenant database
    $tenant->run(function () {
        $testCredentials = TenantAdminProvisioner::getTestCredentials();
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin)->not->toBeNull();
        expect($admin->name)->toBe($testCredentials['name']);
        expect($admin->email)->toBe($testCredentials['email']);
    });
});

test('administrator has tenant-admin role', function () {
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
    $tenant->run(function () {
        $testCredentials = TenantAdminProvisioner::getTestCredentials();
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin->hasRole('tenant-admin'))->toBeTrue();
        expect($admin->hasRole('agent'))->toBeFalse();
    });
});

test('correct password can authenticate', function () {
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
    $tenant->run(function () {
        $testCredentials = TenantAdminProvisioner::getTestCredentials();
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
    $tenant->run(function () {
        $testCredentials = TenantAdminProvisioner::getTestCredentials();
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
    $tenant->run(function () {
        $testCredentials = TenantAdminProvisioner::getTestCredentials();
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
    $testCredentials = TenantAdminProvisioner::getTestCredentials();
    TenantAdminProvisioner::provision($testCredentials);
    tenancy()->end();

    // Verify no duplicate users were created
    $userCountAfterSecond = 0;
    $tenant->run(function () use (&$userCountAfterSecond) {
        $userCountAfterSecond = User::count();
    });

    expect($userCountAfterSecond)->toBe($userCountAfterFirst);
});
