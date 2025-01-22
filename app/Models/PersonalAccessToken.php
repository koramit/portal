<?php

namespace App\Models;

use App\Traits\PKHashable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use PKHashable;

    const STATUSES = [
        /* 0 */ 'unverified',
        /* 1 */ 'active',
        /* 2 */ 'expired',
        /* 3 */ 'revoked',
    ];

    public function status(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->attributes['status'] === 1 && $this->attributes['expires_at'] < now()) {
                    $this->update(['status' => 'expired']);

                    return 'expired';
                }

                return self::STATUSES[$this->attributes['status']];
            },
            set: fn ($value) => $this->attributes['status'] = array_search($value, self::STATUSES, true),
        );
    }

    public function serviceAccessLogs(): HasMany
    {
        return $this->hasMany(ServiceAccessLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', array_search('active', self::STATUSES, true));
    }
}
