<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceBan extends Model
{
    protected $fillable = [
        'device_fingerprint',
        'ip_address',
        'reason',
        'banned_at',
        'expires_at',
    ];

    protected $casts = [
        'banned_at'  => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public static function isBanned(string $fingerprint): bool
    {
        return static::where('device_fingerprint', $fingerprint)->active()->exists();
    }
}
