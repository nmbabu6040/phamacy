<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            "site_name" => "City Pharmacy",
            "site_tagline" => "Your Trusted Health Partner",
            "phone" => "+880 1700-000000",
            "email" => "info@citypharmacy.test",
            "address" => "House 12, Road 5, Dhanmondi, Dhaka, Bangladesh",
            "facebook" => "https://facebook.com/citypharmacy",
            "instagram" => "https://instagram.com/citypharmacy",
            "twitter" => "https://twitter.com/citypharmacy",
            "youtube" => "",
            "currency_symbol" => "৳",
            "footer_text" => "© " . date("Y") . " City Pharmacy. All rights reserved.",
            "meta_title" => "City Pharmacy - Online Medicine & Healthcare Store",
            "meta_description" => "Buy genuine medicines online with fast delivery. Wide range of generic and branded medicines at the best price.",
            "meta_keywords" => "pharmacy, medicine, online pharmacy, healthcare, drug store",
            "google_analytics_id" => "",
        ];

        // Only sets keys that do not already exist locally — respects any settings already configured.
        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(["key" => $key], ["value" => $value]);
        }
    }
}
