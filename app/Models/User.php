<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    // relação com a tabela de notes
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
