<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = ["user_id","action","module","description","subject_type","subject_id","properties","ip_address","user_agent"];

    protected function casts(): array { return ["properties" => "array"]; }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public static function record(string $action, string $module, string $description, $subject = null, array $properties = []): void
    {
        static::create([
            "user_id" => auth()->id(),
            "action" => $action,
            "module" => $module,
            "description" => $description,
            "subject_type" => $subject ? get_class($subject) : null,
            "subject_id" => $subject?->id,
            "properties" => $properties,
            "ip_address" => request()->ip(),
            "user_agent" => request()->userAgent(),
        ]);
    }
}
