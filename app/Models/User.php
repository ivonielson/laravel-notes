<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    // relação com a tabela de notes
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    // app/Models/User.php

    public function auditLogs()
    {
        return $this->hasMany(\App\Models\AuditLog::class, 'user_id');
    }
}
