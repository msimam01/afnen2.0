<?php

namespace App\Jobs;

use App\Models\Central\Tenant;
use App\Services\TenantProvisioner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProvisionTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public Tenant $tenant
    ) {}

    public function handle(): void
    {
        $tenant = $this->tenant->fresh();

        Log::info('[ProvisionTenantJob] Starting', [
            'tenant_id' => $tenant->id,
            'provisioning_status' => $tenant->provisioning_status,
            'status' => $tenant->status,
        ]);

        $tenant->markAsProvisioning();

        try {
            TenantProvisioner::provision($tenant);

            $tenant->fresh()->markAsReady();

            Log::info('[ProvisionTenantJob] Completed', [
                'tenant_id' => $tenant->id,
                'provisioning_status' => $tenant->fresh()->provisioning_status,
                'status' => $tenant->fresh()->status,
            ]);

        } catch (\Throwable $e) {

            $tenant->fresh()->markAsFailed($e->getMessage());

            Log::error('[ProvisionTenantJob] Failed', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $tenant = $this->tenant->fresh();

        $tenant->markAsFailed($exception->getMessage());

        Log::error('[ProvisionTenantJob] Permanently failed', [
            'tenant_id' => $tenant->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
