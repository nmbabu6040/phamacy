<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierBooking extends Model
{
    protected $fillable = [
        "sale_id","branch_id","provider","invoice_reference","consignment_id","tracking_code",
        "recipient_name","recipient_phone","recipient_address","cod_amount","item_description",
        "weight","status","failure_reason","response_payload","booked_by",
    ];

    protected function casts(): array
    {
        return ["response_payload" => "array"];
    }

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function bookedBy(): BelongsTo { return $this->belongsTo(User::class, "booked_by"); }

    public function providerLabel(): string
    {
        return config("couriers.providers.{$this->provider}", ucfirst($this->provider));
    }
}
