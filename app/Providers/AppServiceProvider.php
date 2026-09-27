<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance("current_branch", null);
    }

    public function boot(): void
    {
        // Share site settings + categories with every frontend view (guarded so it does not break before migrations run)
        View::composer(["frontend.*", "layouts.frontend"], function ($view) {
            if (Schema::hasTable("settings")) {
                $view->with("siteSettings", Setting::pluck("value", "key"));
            }
            if (Schema::hasTable("categories")) {
                $view->with("navCategories", Category::where("status", 1)->orderBy("name")->get());
            }
        });

        View::composer(["admin.*", "layouts.admin"], function ($view) {
            if (Schema::hasTable("settings")) {
                $view->with("siteSettings", Setting::pluck("value", "key"));
            }
        });
    }
}
