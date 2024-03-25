<?php

namespace App\Models\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionTransfer extends Model
{
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }
}
