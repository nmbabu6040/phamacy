<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = ["sale_id","product_id","product_unit_id","unit_qty","quantity","sale_price","purchase_price","discount","subtotal","batch_allocations"];

    protected function casts(): array { return ["batch_allocations" => "array"]; }

    public function productUnit(): BelongsTo { return $this->belongsTo(ProductUnit::class); }

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
