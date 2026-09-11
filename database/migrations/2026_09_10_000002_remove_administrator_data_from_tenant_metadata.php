<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Remove administrator credentials that a previous implementation stored
 * inside the tenants `data` JSON column. Administrator data now lives in the
 * `pending_tenant_administrators` table (temporary, encrypted) and the
 * administrator user itself lives in each tenant database.
 */
return new class extends Migration
{
    /**
     * @var list<string>
     */
    private array $adminKeys = [
        'admin_name',
        'admin_email',
        'admin_password_hash',
        'temp_password',
        'password',
    ];

    public function up(): void
    {
        foreach (DB::table('tenants')->select('id', 'data')->get() as $tenant) {
            $data = json_decode((string) $tenant->data, true);

            if (! is_array($data)) {
                continue;
            }

            $cleaned = array_diff_key($data, array_flip($this->adminKeys));

            if ($cleaned === $data) {
                continue;
            }

            Log::info('Removed legacy administrator data from tenant metadata', [
                'tenant_id' => $tenant->id,
                'removed_keys' => array_values(array_diff(array_keys($data), array_keys($cleaned))),
            ]);

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['data' => json_encode($cleaned)]);
        }
    }

    public function down(): void
    {
        // Removed administrator credentials cannot be restored.
    }
};
