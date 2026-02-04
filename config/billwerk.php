<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billwerk API Key
    |--------------------------------------------------------------------------
    |
    | Your Billwerk private API key. You can find this in your Billwerk
    | account settings.
    |
    */

    'apiKey' => env('BILLWERK_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    |
    | Set to true to use the Billwerk test environment.
    |
    */

    'testMode' => env('BILLWERK_TEST_MODE', true),

];
