<?php

namespace App\Traits;

trait PatientSensitiveDataRemovable
{
    protected array $sensitiveData = [
        'dob',
        'document_id',
        'race',
        'nation',
        'tel_no',
        'spouse',
        'address',
        'subdistrict',
        'district',
        'postcode',
        'province',
        'insurance_name',
        'marital_status',
        'alternative_contact',
        'photo',
        'date_death',
    ];

    protected function removeSensitiveData(array &$patient): void
    {
        foreach ($this->sensitiveData as $sensitiveData) {
            if (array_key_exists($sensitiveData, $patient)) {
                $patient[$sensitiveData] = null;
            }
        }
    }
}
