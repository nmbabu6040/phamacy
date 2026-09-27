<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck("value", "key");
        return view("admin.settings.index", compact("settings"));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            "site_name" => "nullable|string|max:255",
            "site_tagline" => "nullable|string|max:255",
            "site_logo" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "site_favicon" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "faq_image" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "phone" => "nullable|string|max:50",
            "email" => "nullable|email",
            "address" => "nullable|string",
            "facebook" => "nullable|string",
            "instagram" => "nullable|string",
            "twitter" => "nullable|string",
            "youtube" => "nullable|string",
            "currency_symbol" => "nullable|string|max:10",
            "footer_text" => "nullable|string",
            // SEO
            "meta_title" => "nullable|string|max:255",
            "meta_description" => "nullable|string",
            "meta_keywords" => "nullable|string",
            "og_image" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "google_analytics_id" => "nullable|string|max:50",
            "about_image" => "nullable|image|mimes:jpeg,jpg,png,webp,gif,svg,bmp,ico,tiff,tga,jfif,avif|max:3072",
            "about_badge" => "nullable|string|max:255",
            "about_heading" => "nullable|string|max:255",
            "about_description" => "nullable|string",
            "map_embed_url" => "nullable|url|max:1000",
        ]);

        foreach ($data as $key => $value) {
            if (in_array($key, ["site_logo", "site_favicon", "faq_image", "og_image", "about_image"]) && $request->hasFile($key)) {
                $value = $request->file($key)->store("settings", "public");
            } elseif (in_array($key, ["site_logo", "site_favicon", "faq_image", "og_image", "about_image"]) && empty($value)) {
                continue; // keep existing file if not re-uploaded
            }
            Setting::set($key, $value);
        }

        ActivityLog::record("updated", "Settings", "Updated site settings / SEO");

        return back()->with("success", "Settings updated successfully.");
    }
}
