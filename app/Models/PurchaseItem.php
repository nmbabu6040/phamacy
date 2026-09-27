<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $fillable = ["purchase_id","product_id","product_unit_id","unit_qty","batch_no","quantity","purchase_price","sale_price","manufacturing_date","expiry_date","subtotal"];

    protected function casts(): array { return ["expiry_date" => "date", "manufacturing_date" => "date"]; }

    public function purchase(): BelongsTo { return $this->belongsTo(Purchase::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function productUnit(): BelongsTo { return $this->belongsTo(ProductUnit::class); }
}
