<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ["name", "slug", "logo"];

    // Slider মডেলের getImageUrlAttribute()-এর মতোই — ৩ ধরনের path নিজে বুঝে নেয়
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) return null;
        if (Str::startsWith($this->logo, ["http://", "https://"])) return $this->logo;
        if (Storage::disk("public")->exists($this->logo)) return asset("storage/" . $this->logo);
        if (file_exists(public_path($this->logo))) return asset($this->logo);

        return null;
    }
}
