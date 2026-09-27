<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Minimal SMS gateway abstraction. Bangladesh SMS providers (SSL Wireless, BulkSMSBD,
 * Alpha SMS, etc.) all follow a similar "POST phone + message + api key" REST pattern,
 * so this ships with a generic HTTP driver you configure via .env, plus a "log" driver
 * (default) that just writes the message to the log — safe for local development.
 *
 * Configure in .env:
 *   SMS_DRIVER=log            # or "http" once you have a real gateway
 *   SMS_API_URL=https://your-gateway.example/api/send
 *   SMS_API_KEY=xxxx
 *   SMS_SENDER_ID=YourBrand
 */
class SmsService
{
    public static function send(string $phone, string $message): bool
    {
        $driver = config("services.sms.driver", "log");

        if ($driver === "log" || !config("services.sms.api_url")) {
            Log::info("[SMS:log] To: {$phone} | Message: {$message}");
            return true;
        }

        try {
            $response = Http::asForm()->post(config("services.sms.api_url"), [
                "api_key" => config("services.sms.api_key"),
                "sender_id" => config("services.sms.sender_id"),
                "number" => $phone,
                "message" => $message,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("SMS send failed: " . $e->getMessage());
            return false;
        }
    }
}
