<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

/**
 * Temporary, encrypted holding record for the initial tenant administrator's
 * provisioning credentials.
 *
 * This is NOT a user account table. The record exists only between tenant
 * creation and successful (or retried) provisioning; it is deleted once the
 * administrator user has been created inside the tenant database.
 *
 * The `password` attribute holds the temporary plaintext credential and is
 * encrypted/decrypted transparently by the `encrypted` cast. It must never be
 * logged.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $name
 * @property string $email
 * @property string $password
 */
class PendingTenantAdministrator extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'encrypted',
    ];
}
