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

    'comunimap' => [
        'web_push_secret' => env('COMUNIMAP_WEB_PUSH_SECRET', ''),
        'image_upload_secret' => env('COMUNIMAP_IMAGE_UPLOAD_SECRET', ''),
    ],

    // Asistencia opcional para cartas formales (solo servidor).
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
        'fallback_model' => env('GEMINI_FALLBACK_MODEL', 'gemini-3.1-flash-lite'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    // Solo la recuperación de contraseña elige este proveedor.
    // No altera los demás correos de Laravel ni el funcionamiento de Android.
    'recuperacion_correo' => [
        'proveedor' => env('RECOVERY_MAIL_PROVIDER', 'smtp'),
        'remitente' => env('RESEND_FROM_ADDRESS', ''),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
