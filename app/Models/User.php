<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'avatar',
        'last_login_at',
        'last_login_ip',
          'email_verified_at',
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts (IMPORTANT)
     */
    protected $casts = [
       'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'status' => 'boolean',
        'role' => UserRole::class, // 🔥 enum casting
    ];

    /**
     * Check roles helper functions
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::USER;
    }

    public function reviews()
{
    return $this->hasMany(Review::class);
}

public function reviewVotes()
{
    return $this->hasMany(ReviewVote::class);
}

public function reviewReports()
{
    return $this->hasMany(ReviewReport::class);
}


}
