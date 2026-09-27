<?php
namespace App\Console\Commands;

use App\Models\ProductBatch;
use App\Models\User;
use App\Notifications\ExpiryAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendExpiryAlerts extends Command
{
    protected $signature = "pharmacy:expiry-alerts {--days=90}";
    protected $description = "Email Admin/Manager users a digest of batches expiring within N days.";

    public function handle(): int
    {
        $days = (int) $this->option("days");

        $batches = ProductBatch::with(["product","branch"])
            ->where("quantity", ">", 0)
            ->whereNotNull("expiry_date")
            ->whereBetween("expiry_date", [now(), now()->addDays($days)])
            ->orderBy("expiry_date")->get();

        if ($batches->isEmpty()) {
            $this->info("No batches expiring within {$days} days.");
            return self::SUCCESS;
        }

        $recipients = User::whereHas("role", fn ($q) => $q->whereIn("slug", ["admin", "manager"]))->get();
        Notification::send($recipients, new ExpiryAlert($batches, $days));

        $this->info("Sent expiry alert for {$batches->count()} batch(es) to {$recipients->count()} recipient(s).");
        return self::SUCCESS;
    }
}
