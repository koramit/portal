<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAccessLog extends Model
{
    use HasFactory;

    protected $casts = [
        'payload' => AsEncryptedArrayObject::class,
    ];

    // belongs to personal token
    public function personalAccessToken()
    {
        return $this->belongsTo(PersonalAccessToken::class);
    }
}
