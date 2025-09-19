<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles; // Add this trait

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at', // Add this field
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime', // Add this cast
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get formatted last login time
     */
    public function getFormattedLastLoginAttribute()
    {
        if (!$this->last_login_at) {
            return 'Never logged in';
        }
        
        return $this->last_login_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . ' (GMT+7)';
    }

    /**
     * Get last login time in GMT+7
     */
    public function getLastLoginGmt7Attribute()
    {
        return $this->last_login_at ? $this->last_login_at->setTimezone('Asia/Jakarta') : null;
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }
}