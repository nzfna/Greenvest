<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo_profile',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function emailVerifications()
    {
        return $this->hasMany(EmailVerification::class);
    }

    // Helpers
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo_profile) {
            return asset('storage/' . $this->photo_profile);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1B4332&color=fff&size=128';
    }
}
