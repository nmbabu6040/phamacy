<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One sellable packaging tier for a product — e.g. Piece / Strip (10 pcs) / Box (100 pcs) / Bottle.
 * `conversion_factor` always expresses "how many base (piece) units this equals",
 * so a Strip of 10 has conversion_factor = 10, and the base unit itself always has factor = 1.
 */
class ProductUnit extends Model
{
    protected $fillable = [
        "product_id","unit_id","conversion_factor","purchase_price","sale_price","is_base_unit","barcode",
    ];

    protected function casts(): array
    {
        return ["is_base_unit" => "boolean"];
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }

    /** Convert a quantity expressed in THIS unit into base (piece) units. */
    public function toBaseQty(int $qty): int
    {
        return $qty * $this->conversion_factor;
    }
}
