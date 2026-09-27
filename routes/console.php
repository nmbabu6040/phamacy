<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command("inspire", function () {
    $this->comment(Inspiring::quote());
})->purpose("Display an inspiring quote");

// Schedule an automatic daily backup (requires the scheduler cron entry to be set up on the server)
Illuminate\Support\Facades\Schedule::command("backup:run")->daily()->at("02:00");

// Email Admin/Manager users a daily digest of medicines expiring within 90 days
Illuminate\Support\Facades\Schedule::command("pharmacy:expiry-alerts --days=90")->dailyAt("08:00");
