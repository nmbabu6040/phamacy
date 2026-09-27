<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ExpiryAlert extends Notification
{
    use Queueable;

    /** @param Collection<int,\App\Models\ProductBatch> $batches */
    public function __construct(public Collection $batches, public int $withinDays = 90) {}

    public function via($notifiable): array
    {
        return ["mail"];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Medicine Expiry Alert — " . $this->batches->count() . " batch(es) expiring soon")
            ->greeting("Expiry Alert")
            ->line("The following batches are expiring within {$this->withinDays} days:");

        foreach ($this->batches->take(15) as $batch) {
            $mail->line("• {$batch->product->name} — Batch {$batch->batch_no} — Qty {$batch->quantity} — Expires {$batch->expiry_date->format('d M Y')}");
        }

        return $mail->action("View Batches", route("admin.batches.expiring"));
    }
}
