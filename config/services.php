<?php
return [
    "postmark" => ["token" => env("POSTMARK_TOKEN")],
    "resend" => ["key" => env("RESEND_KEY")],
    "ses" => [
        "key" => env("AWS_ACCESS_KEY_ID"),
        "secret" => env("AWS_SECRET_ACCESS_KEY"),
        "region" => env("AWS_DEFAULT_REGION", "us-east-1"),
    ],

    // Generic SMS gateway used by App\Services\SmsService — see that class docblock for setup notes.
    "sms" => [
        "driver" => env("SMS_DRIVER", "log"),
        "api_url" => env("SMS_API_URL"),
        "api_key" => env("SMS_API_KEY"),
        "sender_id" => env("SMS_SENDER_ID"),
    ],
];
