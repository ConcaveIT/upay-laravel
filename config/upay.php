<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upay Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Upay credentials and base URL. These will be
    | used by the package to interact with the Upay Payment Gateway API.
    |
    */
    'base_url'    => env('UPAY_BASE_URL', 'https://uat-pg.upay.systems'),
    'merchant_id' => env('UPAY_MERCHANT_ID', ''),
    'merchant_key'=> env('UPAY_MERCHANT_KEY', ''),
];
