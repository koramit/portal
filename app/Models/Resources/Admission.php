<?php

namespace App\Models\Resources;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string|null $age_unit
 * @property int|null $age
 */
class Admission extends Model
{
    protected $casts = [
        'dob' => 'date',
        'admitted_at' => 'datetime',
        'discharged_at' => 'datetime',
        'checked_at' => 'datetime',
    ];

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function admissionTransfers(): HasMany
    {
        return $this->hasMany(AdmissionTransfer::class);
    }

    protected function gender(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attributes['gender'] === 1 ? 'female' : 'male',
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->dob) {
                    return null;
                }

                $ageInMonths = $this->admitted_at->diffInMonths($this->dob);
                if ($ageInMonths < 12) {
                    return $ageInMonths;
                }

                return $this->admitted_at->diffInYears($this->dob);
            },
        );
    }

    protected function ageUnit(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->dob) {
                    return null;
                }

                $ageInMonths = $this->admitted_at->diffInMonths($this->dob);
                if ($ageInMonths < 12) {
                    return 'Mo';
                }

                return 'Yo';
            },
        );
    }
}
