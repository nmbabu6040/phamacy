<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = ["product_id","branch_id","type","quantity","stock_after","reference_type","reference_id","created_by","note"];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, "created_by"); }
}
