<?php

namespace App\APIs;

use App\Models\Resources\Ward;

class WardAPI implements \App\Contracts\WardAPI
{
    public function getWard(int|string $number = null): array
    {
        if ($number === null) {
            return Ward::query()
                ->withCount(['admissions' => fn ($q) => $q->whereNull('discharged_at')])
                ->get()
                ->filter(fn ($ward) => $ward->admissions_count > 0)
                ->transform(fn ($ward) => [
                    'number' => $ward->id,
                    'name' => $ward->name,
                    'admissions_count' => $ward->admissions_count,
                ])->values()
                ->all();
        }

        return Ward::query()
            ->where('id', $number)
            ->first()
            ->admissions()
            ->whereNull('discharged_at')
            ->get()
            ->transform(function ($admission) {
                return [
                    'an' => $admission->an,
                    'hn' => $admission->hn,
                    'name' => $admission->name,
                    'gender' => $admission->gender,
                    'age' => $admission->age,
                    'age_unit' => $admission->age_unit,
                    'admitted_at' => $admission->admitted_at,
                ];
            })->toArray();
    }
}
