<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'article_id',
        'user_name',
        'user_email',
        'content',
        'admin_reply',
        'replied_at',
        'status',
        'ip_address',
        'user_agent',
        'device_fingerprint',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    // Relationships
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Accessors
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'approved'    => ['label' => 'Disetujui', 'class' => 'bg-success-bg text-success-text'],
            'rejected'    => ['label' => 'Ditolak',   'class' => 'bg-danger-bg text-danger-text'],
            'shadowbanned'=> ['label' => 'Shadowban', 'class' => 'bg-gray-200 text-gray-600'],
            default       => ['label' => 'Menunggu',  'class' => 'bg-warning-bg text-warning-text'],
        };
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->user_name);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }
}
