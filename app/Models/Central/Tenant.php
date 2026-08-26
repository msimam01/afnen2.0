<?php

namespace App\Models\Central;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'data',
        'provisioning_status',
        'status',
        'activated_at',
        'deactivated_at',
        'deactivation_reason',
    ];

    protected $casts = [
        'data' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    public const PROVISIONING_PENDING = 'pending';
    public const PROVISIONING = 'provisioning';
    public const PROVISIONING_READY = 'ready';
    public const PROVISIONING_FAILED = 'failed';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    public function getNameAttribute(): string
{
    return $this->data['name'] ?? 'Unknown';
}

public function getDescriptionAttribute(): ?string
{
    return $this->data['description'] ?? null;
}

public function isProvisioningPending(): bool
{
    return $this->provisioning_status === self::PROVISIONING_PENDING;
}

public function isProvisioning(): bool
{
    return $this->provisioning_status === self::PROVISIONING;
}

public function isProvisioned(): bool
{
    return $this->provisioning_status === self::PROVISIONING_READY;
}

public function provisioningFailed(): bool
{
    return $this->provisioning_status === self::PROVISIONING_FAILED;
}

public function isActive(): bool
{
    return $this->status === self::STATUS_ACTIVE;
}

public function isInactive(): bool
{
    return $this->status === self::STATUS_INACTIVE;
}

public function isSuspended(): bool
{
    return $this->status === self::STATUS_SUSPENDED;
}
public function markAsProvisioning(): void
{
    $this->update([
        'provisioning_status' => self::PROVISIONING,
    ]);
}

public function markAsReady(): void
{
    $this->update([
        'provisioning_status' => self::PROVISIONING_READY,
        'status' => self::STATUS_ACTIVE,
        'activated_at' => now(),
        'deactivated_at' => null,
        'deactivation_reason' => null,
    ]);
}

public function markAsFailed(?string $reason = null): void
{
    $this->update([
        'provisioning_status' => self::PROVISIONING_FAILED,
        'status' => self::STATUS_INACTIVE,
        'deactivation_reason' => $reason,
    ]);
}

public function activate(): void
{
    $this->update([
        'status' => self::STATUS_ACTIVE,
        'activated_at' => now(),
        'deactivated_at' => null,
        'deactivation_reason' => null,
    ]);
}

public function deactivate(?string $reason = null): void
{
    $this->update([
        'status' => self::STATUS_INACTIVE,
        'deactivated_at' => now(),
        'deactivation_reason' => $reason,
    ]);
}

public function suspend(?string $reason = null): void
{
    $this->update([
        'status' => self::STATUS_SUSPENDED,
        'deactivated_at' => now(),
        'deactivation_reason' => $reason,
    ]);
}
public function scopeActive($query)
{
    return $query->where('status', self::STATUS_ACTIVE);
}

public function scopeInactive($query)
{
    return $query->where('status', self::STATUS_INACTIVE);
}

public function scopeSuspended($query)
{
    return $query->where('status', self::STATUS_SUSPENDED);
}

public function scopeReady($query)
{
    return $query->where(
        'provisioning_status',
        self::PROVISIONING_READY
    );
}
}