<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $fillable = [
        "invoice_no","branch_id","supplier_id","purchase_date","total_amount","discount","tax",
        "shipping_cost","grand_total","paid_amount","due_amount","payment_status","status","created_by","note",
    ];

    protected function casts(): array { return ["purchase_date" => "date"]; }

    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function items(): HasMany { return $this->hasMany(PurchaseItem::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, "created_by"); }
}
