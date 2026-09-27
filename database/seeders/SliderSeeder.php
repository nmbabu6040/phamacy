<?php
namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $sliders = [
            ["title" => "Your Health, Our Priority", "subtitle" => "Genuine medicines delivered to your doorstep", "button_text" => "Shop Now", "button_link" => "/shop", "image" => "assets/img/slider/slide-1.jpg", "sort_order" => 1],
            ["title" => "Up to 20% Off on Vitamins", "subtitle" => "Boost your immunity this season",           "button_text" => "Explore Offers", "button_link" => "/shop", "image" => "assets/img/slider/slide-2.jpg", "sort_order" => 2],
            ["title" => "24/7 Pharmacist Support",   "subtitle" => "Expert advice whenever you need it",         "button_text" => "Contact Us", "button_link" => "/contact", "image" => "assets/img/slider/slide-3.jpg", "sort_order" => 3],
        ];

        foreach ($sliders as $s) {
            Slider::firstOrCreate(["title" => $s["title"]], $s + ["status" => 1]);
        }
    }
}
