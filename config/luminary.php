<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Header format
    |--------------------------------------------------------------------------
    |
    | The accepted content type and response header
    |
    */
    'contentType' => 'application/vnd.api+json',
    'vendorTree' => 'application/vnd',
    'producer' => 'api',
    'mediaType' => 'json',

    /*
    |--------------------------------------------------------------------------
    | Location/Url Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix urls with a location. (useful for proxies)
    |
    */
    'location' => env('URL_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Dynamic Router
    |--------------------------------------------------------------------------
    |
    | Set if a dynamic router is preferred
    |
    */

    'dynamic_routing' => env('LUMINARY_DYNAMIC_ROUTER', false),
];
