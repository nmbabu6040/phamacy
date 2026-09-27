<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ["name", "slug", "image", "status"];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return "https://via.placeholder.com/1600x600?text=" . urlencode($this->name ?: "Category");
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

        return "https://via.placeholder.com/1600x600?text=" . urlencode($this->name ?: "Category");
    }
}
