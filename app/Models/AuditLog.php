<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/AuditLog.php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model', 'model_id',
        'ip_address', 'old_values', 'new_values',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

