<?php
namespace App\Notifications\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, "toSms")) return;

        $phone = $notifiable->routeNotificationFor("sms") ?? $notifiable->phone ?? null;
        if (!$phone) return;

        $message = $notification->toSms($notifiable);
        SmsService::send($phone, $message);
    }
}
