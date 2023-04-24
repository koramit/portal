<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SiMRS API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for your SiMRS API integration.
    |
    */

    'host' => env('SIMRS_HOST'),
    'server_username' => env('SIMRS_SERVER_USERNAME'),
    'server_password' => env('SIMRS_SERVER_PASSWORD'),
    'api_username' => env('SIMRS_API_USERNAME'),
    'api_password' => env('SIMRS_API_PASSWORD'),
    'patient_url' => env('SIMRS_PATIENT_URL'),
    'user_url' => env('SIMRS_USER_URL'),
];
