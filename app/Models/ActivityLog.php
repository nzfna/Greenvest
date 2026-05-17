<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    const UPDATED_AT = null; // Hanya created_at

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getIconAttribute(): string
    {
        return match($this->action) {
            'login'            => 'ph-sign-in',
            'logout'           => 'ph-sign-out',
            'create_article'   => 'ph-file-plus',
            'update_article'   => 'ph-file-text',
            'delete_article'   => 'ph-trash',
            'update_profile'   => 'ph-user',
            'change_password'  => 'ph-lock',
            'verify_email'     => 'ph-envelope-simple-check',
            'approve_comment'  => 'ph-check-circle',
            'reject_comment'   => 'ph-x-circle',
            'shadowban'        => 'ph-eye-slash',
            'block_ip'         => 'ph-shield-slash',
            'reply_comment'    => 'ph-chat-circle',
            default            => 'ph-activity',
        };
    }

    public function getStatusLabelAttribute(): array
    {
        return match($this->action) {
            'create_article', 'approve_comment', 'login', 'verify_email'
                => ['label' => 'SELESAI', 'class' => 'bg-success-bg text-success-text'],
            'update_article', 'update_profile', 'change_password', 'reply_comment'
                => ['label' => 'PENTING', 'class' => 'bg-warning-bg text-warning-text'],
            'delete_article', 'reject_comment', 'shadowban', 'block_ip'
                => ['label' => 'TERHAPUS', 'class' => 'bg-danger-bg text-danger-text'],
            default
                => ['label' => 'INFO', 'class' => 'bg-gray-100 text-gray-600'],
        };
    }
}
