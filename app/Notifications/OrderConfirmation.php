<?php
namespace App\Notifications;

use App\Models\Sale;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification
{
    use Queueable;

    public function __construct(public Sale $sale) {}

    public function via($notifiable): array
    {
        $channels = [];
        if ($notifiable->routeNotificationFor("mail")) {
            $channels[] = "mail";
        }
        if ($notifiable->routeNotificationFor("sms") ?? $this->sale->customer_phone) {
            $channels[] = SmsChannel::class;
        }
        return $channels ?: ["mail"];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order Confirmation — {$this->sale->invoice_no}")
            ->greeting("Thank you for your order, {$this->sale->customer_name}!")
            ->line("Your order {$this->sale->invoice_no} has been received and is being processed.")
            ->line("Order Total: ৳" . number_format($this->sale->grand_total, 2))
            ->line("Payment Method: " . ucfirst($this->sale->payment_method))
            ->action("Track Your Order", route("order.track"))
            ->line("We will notify you again once your order ships.");
    }

    public function toSms($notifiable): string
    {
        return "Thank you! Your order {$this->sale->invoice_no} (Tk " . number_format($this->sale->grand_total, 0) . ") has been received and is being processed.";
    }
}
