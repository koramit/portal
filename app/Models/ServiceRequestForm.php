<?php

namespace App\Models;

use App\Traits\PKHashable;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestForm extends Model
{
    use PKHashable;

    const STATUSES = [
        0 => 'pending',
        1 => 'approved',
        2 => 'disapproved',
        3 => 'canceled',
        4 => 'revoked',
    ];

    protected $casts = [
        'form' => AsCollection::class,
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function authority(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authority_id');
    }

    public function revokeAuthority(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoke_authority_id');
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUSES[$this->attributes['status']],
            set: fn ($value) => array_search($value, self::STATUSES, true),
        );
    }
}
