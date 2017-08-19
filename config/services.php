<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'facebook' => [
        'client_id' => '123942261577914',
        'client_secret' => '5857431529011c7c6a9ff38b0eaf55ce',
        'redirect' => 'http://mdb.dev/api/auth/facebook/callback',
    ],
    //
    //'twitter' => [
    //    'client_id' => 'C4bnB49HpZtSNqU1q1XwGlNVd',
    //    'client_secret' => 'ZE37roKAhzCM1Yx9mbycoCqmjZEUyFBHWYsPkIGAKCfIRTNXOq',
    //    'redirect' => 'http://mdb.dev/api/auth/twitter/callback',
    //],

];
