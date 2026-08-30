<?php

use App\Models\Central\Tenant;
use App\Models\User;
use App\Services\TenantAdminProvisioner;
use Illuminate\Support\Facades\Auth;

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

test('central login page loads', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertInertia(function ($page) {
        expect($page->component)->toBe('auth/login');
    });
});

test('unauthenticated central user cannot access dashboard', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

test('tenant login page loads on tenant domain', function () {
    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Gombe Test Farmers Association',
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

    // Simulate tenant domain request
    $this->withServerVariables(['HTTP_HOST' => 'gombe-test.afnen.com'])
        ->get('/login')
        ->assertStatus(200);

    $tenant->delete();
});

test('unauthenticated tenant user cannot access dashboard', function () {
    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Gombe Test Farmers Association',
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

    // Simulate tenant domain request to dashboard
    $this->withServerVariables(['HTTP_HOST' => 'gombe-test.afnen.com'])
        ->get('/dashboard')
        ->assertRedirect('/login');

    $tenant->delete();
});

test('tenant administrator can authenticate', function () {
    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Gombe Test Farmers Association',
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

    $testCredentials = TenantAdminProvisioner::getTestCredentials();

    // Authenticate in tenant context
    $tenant->run(function () use ($testCredentials) {
        $authenticated = Auth::attempt([
            'email' => $testCredentials['email'],
            'password' => $testCredentials['password'],
        ]);

        expect($authenticated)->toBeTrue();
        Auth::logout();
    });

    $tenant->delete();
});

test('tenant authentication occurs against tenant database', function () {
    $tenant = Tenant::create([
        'id' => 'gombe-test',
        'data' => [
            'name' => 'Gombe Test Farmers Association',
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

    // Verify user exists in tenant database
    $tenant->run(function () {
        $testCredentials = TenantAdminProvisioner::getTestCredentials();
        $admin = User::where('email', $testCredentials['email'])->first();

        expect($admin)->not->toBeNull();
        expect($admin->status)->toBe('active');
    });

    // Verify user does NOT exist in central database
    $centralUser = User::where('email', 'admin@afnen.test')->first();
    expect($centralUser)->toBeNull();

    $tenant->delete();
});

test('existing afnen:test-tenant continues passing', function () {
    $this->artisan('afnen:test-tenant')
        ->assertExitCode(0);
});
