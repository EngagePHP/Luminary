<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
   
    'supportsCredentials' => env('CORS_SUPPORTS_CREDENTIALS', false),
    'allowedOrigins' => env_list('CORS_ALLOWED_ORIGINS', '*'),
    'allowedHeaders' => env_list('CORS_ALLOWED_HEADERS', '*'),
    'allowedMethods' => env_list('CORS_ALLOWED_METHODS', '*'),
    'exposedHeaders' => env_list('CORS_EXPOSED_HEADERS'),
    'maxAge' => 0,

];
