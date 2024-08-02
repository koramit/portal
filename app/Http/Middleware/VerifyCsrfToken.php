<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/lab-pending',
        '/lab-recently',
        '/lab-from-ref-no',
        '/lab-from-service-id',
        '/lab-from-item-code',
        '/lab-from-item-all',
    ];
}
