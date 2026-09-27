<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = ["name","code","phone","email","address","is_main","status"];

    public function users(): HasMany { return $this->hasMany(User::class); }
    public function sales(): HasMany { return $this->hasMany(Sale::class); }
    public function purchases(): HasMany { return $this->hasMany(Purchase::class); }
    public function batches(): HasMany { return $this->hasMany(ProductBatch::class); }

    public static function main(): ?self
    {
        return static::where("is_main", true)->first() ?? static::first();
    }
}
