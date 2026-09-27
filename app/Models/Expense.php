<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ["expense_category_id","branch_id","title","amount","expense_date","attachment","created_by","note"];

    protected function casts(): array { return ["expense_date" => "date"]; }

    public function category(): BelongsTo { return $this->belongsTo(ExpenseCategory::class, "expense_category_id"); }
    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, "created_by"); }
}
