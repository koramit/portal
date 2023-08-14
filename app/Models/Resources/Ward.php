<?php

namespace App\Models\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ward extends Model
{
    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function activeAdmissions()
    {
        return $this->admissions->whereNull('discharged_at');
    }
}
