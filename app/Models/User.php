<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ["role_id", "branch_id", "name", "email", "phone", "avatar", "password", "status"];

    protected $hidden = ["password", "remember_token"];

    protected function casts(): array
    {
        return ["email_verified_at" => "datetime", "password" => "hashed"];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function can($ability, $arguments = []): bool
    {
        if (!$this->role) return false;
        return $this->role->hasPermission($ability);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->slug === "admin";
    }
}
