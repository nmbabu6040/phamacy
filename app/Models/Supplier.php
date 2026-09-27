<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ["name", "company_name", "phone", "email", "address", "previous_due", "status"];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}
