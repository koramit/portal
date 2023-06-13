<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SiAD Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the settings for your SiAD integration.
    |
    */

    'check_user_url' => env('SIAD_CHECK_USER_URL'),
    'check_user_app_name' => env('SIAD_CHECK_USER_APP_NAME'),
    'check_user_api_key' => env('SIAD_CHECK_USER_API_KEY'),
    'auth_user_url' => env('SIAD_AUTH_USER_URL'),
    'auth_user_app_name' => env('SIAD_AUTH_USER_APP_NAME'),
    'auth_user_api_key' => env('SIAD_AUTH_USER_API_KEY'),
    'user_status_url' => env('SIAD_USER_STATUS_URL'),
    'login_format_validate_rule' => 'regex:/^[a-zA-Z]{1,24}\.[a-zA-Z]{3}$|^[a-zA-Z]{1,24}\.[a-zA-Z]{2}-$/',
];
