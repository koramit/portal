<?php

namespace App\Contracts;

interface WardAPI
{
    public function getWard(int|string $number = null): array;
}
