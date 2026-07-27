<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'psr/*'],

    'allowed_methods' => ['*'],

    // Wildcard is safe here because supports_credentials is false (no
    // cookies are used - auth is an app-managed token passed explicitly by
    // the client, not an ambient browser credential), and tenant custom
    // domains are unbounded/user-registered, so a static allowlist would
    // need to be updated every time someone adds a new custom domain.
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
