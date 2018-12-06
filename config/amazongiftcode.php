<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Amazon Gift Card Endpoint
    |--------------------------------------------------------------------------
    |
    | Here you may want to specify endpoint based on you requirements. Amazon
    | Gift Card used different endpoint for Sandbox and Production.
    |
    */

    'endpoint' => env('GIFT_CARD_ENDPOINT', 'agcod-v2-gamma.amazon.com'),


    /*
    |--------------------------------------------------------------------------
    | Amazon Gift Card Access Key
    |--------------------------------------------------------------------------
    |
    | Amazon Gift Card On Demand API needs Access Key and Secret. You need to
    | specify both key and secret in order to generate gift cards.This will get
    | used to authenticate with Amazon Server Gift Card Gateway request.
    |
    */

    'key' => env('GIFT_CARD_KEY'),
    'secret' => env('GIFT_CARD_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Amazon Gift Card Partner ID
    |--------------------------------------------------------------------------
    |
    | Here you need to specified a unique identifier provided by (CASE SENSITIVE,
    | 1st letter is capitalized and the next four are lowercase) provided by
    | the Amazon GC team.
    |
    */

    'partner' => env('GIFT_CARD_PARTNER_ID'),


    'currency' => "US",

    'debug' => env('GIFT_CARD_DEBUG', false)

];