<?php

namespace App\Contracts;

interface LabAPI
{
    public function getLabPendingReports(string $hn): array;

    public function getLabRecentlyOrders(string $hn): array;

    public function getLabFromRefNo(string $refNo): array;

    public function getLabFromServiceId(array $validated): array;

    public function getLabFromItemCode(array $validated): array;

    public function getLabFromItemCodeAllResults(array $validated): array;
}
