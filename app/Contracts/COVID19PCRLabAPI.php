<?php

namespace App\Contracts;

interface COVID19PCRLabAPI
{
    public function __invoke(int|string $hn, string $dateLab): array;
}
