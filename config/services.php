<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Onex SMS Gateway
    'onex_sms' => [
        'url' => env('ONEX_SMS_URL', 'https://api.onex-aura.com/api/sms'),
        'api_key' => env('ONEX_SMS_API_KEY'),
        'sender' => env('ONEX_SMS_SENDER', 'SKICAP'),
    ],

    // Sandbox API (KYC Verification)
    'sandbox' => [
        'url' => env('SANDBOX_API_URL', 'https://api.sandbox.co.in'),
        'api_key' => env('SANDBOX_API_KEY'),
        'secret' => env('SANDBOX_SECRET'),
    ],

    // Razorpay IFSC API
    'razorpay' => [
        'ifsc_url' => env('RAZORPAY_IFSC_URL', 'https://ifsc.razorpay.com'),
    ],

    // Google reCAPTCHA
    'recaptcha' => [
        'ipv' => [
            'site_key' => env('RECAPTCHA_SITE_KEY_IPV'),
            'secret_key' => env('RECAPTCHA_SECRET_KEY_IPV'),
        ],
        'nomination' => [
            'site_key' => env('RECAPTCHA_SITE_KEY_NOMINATION'),
            'secret_key' => env('RECAPTCHA_SECRET_KEY_NOMINATION'),
        ],
    ],

];
