<?php

namespace App\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait PKHashable
{
    /**
     * Retrieve the model for a bound value.
     * type hint and return type must compatible with Illuminate\Database\Eloquent\Concerns\HasRouteKeyBinding::resolveRouteBinding
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->findByUnhashKey($value, $field ?? 'id')->firstOrFail();
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function scopeFindByUnhashKey($query, string $hashed, ?string $field = 'id')
    {
        return $query->where($field, app(Hashids::class)->decode($hashed)[0] ?? 0);
    }

    protected function hashedKey(): Attribute
    {
        return Attribute::make(
            get: fn () => app(Hashids::class)->encode($this->attributes['id']),
        )->shouldCache();
    }
}
