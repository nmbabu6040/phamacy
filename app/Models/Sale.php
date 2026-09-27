<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        "invoice_no","branch_id","channel","order_status","customer_id","customer_name","customer_phone","customer_email",
        "sale_date","total_amount","discount","tax","grand_total","shipping_address","shipping_cost",
        "paid_amount","due_amount","profit","payment_method","payment_status","status","created_by","note",
    ];

    protected function casts(): array { return ["sale_date" => "date"]; }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function items(): HasMany { return $this->hasMany(SaleItem::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, "created_by"); }
}
