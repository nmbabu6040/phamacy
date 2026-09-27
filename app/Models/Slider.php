<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Slider extends Model
{
    protected $fillable = ["title", "subtitle", "button_text", "button_link", "image", "sort_order", "status"];

    // image কলামে তিন ধরনের value থাকতে পারে — এই মেথড সবগুলো নিজে বুঝে সঠিক URL বানায়:
    //  ১. পুরো URL (https://...)
    //  ২. আসল আপলোড করা ফাইল (storage/app/public এর ভেতরে, যেমন "sliders/xyz.jpg")
    //  ৩. Seeder-এর পুরনো demo path (public/ এর ভেতরে সরাসরি, যেমন "assets/img/slider/slide-1.jpg")
    // ফাইল খুঁজে না পেলে placeholder ছবি দেখাবে, পেজ লেআউট ভাঙবে না।
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return "https://via.placeholder.com/1600x600?text=" . urlencode($this->title ?: "Slide");
        }
        if (Str::startsWith($this->image, ["http://", "https://"])) {
            return $this->image;
        }
        if (Storage::disk("public")->exists($this->image)) {
            return asset("storage/" . $this->image);
        }
        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        return "https://via.placeholder.com/1600x600?text=" . urlencode($this->title ?: "Slide");
    }
}
