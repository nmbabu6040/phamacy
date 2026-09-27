<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    protected $fillable = [
        "product_id","branch_id","purchase_item_id","batch_no","quantity","initial_quantity",
        "purchase_price","sale_price","manufacturing_date","expiry_date",
    ];

    protected function casts(): array
    {
        return ["manufacturing_date" => "date", "expiry_date" => "date"];
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function purchaseItem(): BelongsTo { return $this->belongsTo(PurchaseItem::class); }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 90): bool
    {
        return $this->expiry_date && !$this->isExpired()
            && now()->diffInDays($this->expiry_date, false) <= $days;
    }

    public static function generateBatchNo(string $productCode): string
    {
        return "BATCH-" . strtoupper($productCode) . "-" . now()->format("ymd") . "-" . strtoupper(\Illuminate\Support\Str::random(4));
    }
}
