<?php

namespace App\Contracts;

interface WardAPI
{
    public function getWard(int|string|null $number = null): array;

    public function getAdmissionDischargeDate(array $data): array;
}
