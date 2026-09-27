<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        "name","slug","code","category_id","generic_id","brand_id","unit_id",
        "strength","dosage_form","purchase_price","sale_price","discount","tax_percent",
        "stock_qty","alert_qty","expiry_date","image","description","status","is_featured",
    ];

    protected function casts(): array
    {
        return ["expiry_date" => "date"];
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function generic(): BelongsTo { return $this->belongsTo(Generic::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function unit(): BelongsTo { return $this->belongsTo(Unit::class); }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    /** All sellable packaging tiers (Piece, Strip, Box, Bottle...) with their own price. */
    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function baseUnit(): ?ProductUnit
    {
        return $this->units->firstWhere("is_base_unit", true) ?? $this->units->first();
    }

    /**
     * Keep the mirrored products.unit_id / purchase_price / sale_price in sync with
     * whichever ProductUnit row is flagged as the base unit — several older parts of
     * the system (dashboard, reports, storefront) read these columns directly.
     */
    public function syncBaseUnitMirror(): void
    {
        $base = $this->units()->where("is_base_unit", true)->first();
        if ($base) {
            $this->update([
                "unit_id" => $base->unit_id,
                "purchase_price" => $base->purchase_price,
                "sale_price" => $base->sale_price,
            ]);
        }
    }

    public function isLowStock(): bool
    {
        return $this->stock_qty <= $this->alert_qty;
    }

    public function isExpiringSoon(int $days = 90): bool
    {
        return $this->expiry_date && now()->diffInDays($this->expiry_date, false) <= $days && now()->diffInDays($this->expiry_date, false) >= 0;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /** Current stock quantity for one specific branch (sum of remaining batch quantities). */
    public function stockAtBranch(int $branchId): int
    {
        return (int) $this->batches()->where("branch_id", $branchId)->sum("quantity");
    }

    /**
     * Recompute the cached `stock_qty` column (global total across all branches)
     * from the batches table, and refresh the product's nearest expiry_date.
     */
    public function recalcStock(): void
    {
        $this->stock_qty = (int) $this->batches()->sum("quantity");
        $nearest = $this->batches()->where("quantity", ">", 0)->orderBy("expiry_date")->value("expiry_date");
        $this->expiry_date = $nearest;
        $this->save();
    }

    /**
     * Receive stock into a branch as a new batch (used by Purchases and manual "stock in").
     * Every unit of stock in the system lives inside a batch — this is the only way stock increases.
     */
    public function receiveBatch(int $branchId, int $qty, float $purchasePrice, ?float $salePrice = null, ?string $batchNo = null, $manufacturingDate = null, $expiryDate = null, $reference = null, ?string $note = null): ProductBatch
    {
        $batch = $this->batches()->create([
            "branch_id" => $branchId,
            "purchase_item_id" => $reference instanceof PurchaseItem ? $reference->id : null,
            "batch_no" => $batchNo ?: ProductBatch::generateBatchNo($this->code),
            "quantity" => $qty,
            "initial_quantity" => $qty,
            "purchase_price" => $purchasePrice,
            "sale_price" => $salePrice ?? $this->sale_price,
            "manufacturing_date" => $manufacturingDate,
            "expiry_date" => $expiryDate,
        ]);

        $this->recalcStock();

        $this->stockMovements()->create([
            "branch_id" => $branchId,
            "type" => "purchase",
            "quantity" => $qty,
            "stock_after" => $this->stock_qty,
            "reference_type" => $reference ? get_class($reference) : null,
            "reference_id" => $reference?->id,
            "created_by" => auth()->id(),
            "note" => $note ?: "Stock in — batch {$batch->batch_no}",
        ]);

        return $batch;
    }

    /**
     * Issue (deduct) stock from a branch using FEFO — First-Expiry-First-Out.
     * Returns the batch allocation breakdown: [["batch_id"=>, "batch_no"=>, "qty"=>], ...]
     * Throws if the branch does not have enough stock across its batches.
     */
    public function issueStock(int $branchId, int $qty, string $type = "sale", $reference = null, ?string $note = null): array
    {
        $available = $this->stockAtBranch($branchId);
        if ($available < $qty) {
            throw new \RuntimeException("Insufficient stock for {$this->name}. Available: {$available}, requested: {$qty}");
        }

        $batches = $this->batches()
            ->where("branch_id", $branchId)
            ->where("quantity", ">", 0)
            ->orderByRaw("expiry_date IS NULL, expiry_date ASC") // batches with no expiry consumed last
            ->orderBy("created_at")
            ->lockForUpdate()
            ->get();

        $remaining = $qty;
        $allocations = [];

        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            $take = min($batch->quantity, $remaining);
            $batch->decrement("quantity", $take);
            $remaining -= $take;
            $allocations[] = ["batch_id" => $batch->id, "batch_no" => $batch->batch_no, "qty" => $take];
        }

        $this->recalcStock();

        $this->stockMovements()->create([
            "branch_id" => $branchId,
            "type" => $type,
            "quantity" => -1 * $qty,
            "stock_after" => $this->stock_qty,
            "reference_type" => $reference ? get_class($reference) : null,
            "reference_id" => $reference?->id,
            "created_by" => auth()->id(),
            "note" => $note ?: "Stock out (FEFO)",
        ]);

        return $allocations;
    }

    /**
     * Manual stock adjustment (no batch/expiry tracking) — used for damage, correction, opening stock
     * without a real supplier batch. Creates a no-expiry adjustment batch for "in", or issues FEFO for "out".
     */
    public function adjustStock(int $qty, string $type, $reference = null, ?string $note = null, ?int $branchId = null): void
    {
        $branchId = $branchId ?: (\App\Models\Branch::main()?->id);
        if (!$branchId) return;

        if ($qty > 0) {
            $this->receiveBatch($branchId, $qty, $this->purchase_price, $this->sale_price, "ADJ-" . strtoupper(\Illuminate\Support\Str::random(6)), null, null, $reference, $note);
        } elseif ($qty < 0) {
            $this->issueStock($branchId, abs($qty), $type, $reference, $note);
        }
    }
}
