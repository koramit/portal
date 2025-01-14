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
    'login_format_validate_rule' => 'string', // 'regex:/^(?!-)(?!.*--)[a-zA-Z-]{1,23}(?<!-)\.[a-zA-Z0-9]{3}$|^(?!-)(?!.*--)[a-zA-Z-]{1,23}(?<!-)\.[a-zA-Z0-9]{2}-$/',
    'alt_auth_url' => env('SIAD_ALT_AUTH_URL'),
    'alt_user_info_url' => env('SIAD_ALT_USER_INFO_URL'),
    'alt_user_org_id_url' => env('SIAD_ALT_USER_ORG_ID_URL'),
    'adfs_client_id' => env('SIAD_ADFS_CLIENT_ID'),
    'adfs_client_secret' => env('SIAD_ADFS_CLIENT_SECRET'),
    'adfs_auth_url' => env('SIAD_ADFS_AUTH_URL'),
];
