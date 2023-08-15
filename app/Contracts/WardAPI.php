<?php

namespace App\Contracts;

interface WardAPI
{
    public function getWard(int|string $number = null): array;

    public function getAdmissionDischargeDate(array $data): array;
}
