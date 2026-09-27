<?php
namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification
{
    use Queueable;

    public function __construct(public Product $product) {}

    public function via($notifiable): array
    {
        return ["mail"];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Low Stock Alert: {$this->product->name}")
            ->greeting("Low Stock Alert")
            ->line("The product \"{$this->product->name}\" ({$this->product->code}) has fallen to or below its alert quantity.")
            ->line("Current stock: {$this->product->stock_qty} | Alert threshold: {$this->product->alert_qty}")
            ->action("View Product", route("admin.products.index", ["search" => $this->product->code]))
            ->line("Please arrange a new purchase soon to avoid a stock-out.");
    }
}
