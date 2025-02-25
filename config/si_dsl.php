<?php

return [
    'proxy_url' => env('SI_DSL_PROXY_URL'),
    'consumer_id' => env('SI_DSL_CONSUMER_ID'),
    'headers' => [
        'Content-Type' => 'application/json',
        'x-consumer-id' => env('SI_DSL_CONSUMER_ID'),
    ],
    'headers_fhir' => [
        'Content-Type' => 'application/json+fhir',
        'x-consumer-id' => env('SI_DSL_CONSUMER_ID'),
    ],
    'allergy_endpoint' => env('SI_DSL_ALLERGY_ENDPOINT'),
    'patient_endpoint' => env('SI_DSL_PATIENT_ENDPOINT'),
    'admission_endpoint' => env('SI_DSL_ADMISSION_ENDPOINT'),
    'appointment_endpoint' => env('SI_DSL_APPOINTMENT_ENDPOINT'),
    'medication_endpoint' => env('SI_DSL_MEDICATION_ENDPOINT'),
    'encounter_endpoint' => env('SI_DSL_ENCOUNTER_ENDPOINT'),
];
