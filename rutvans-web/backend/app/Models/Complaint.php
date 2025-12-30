<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Complaint extends Model
{
    protected $fillable = [
        'content',
        'date',
        'response',
        'response_date',
        'user_id'
    ];

    // Relación con el usuario que hizo la queja
    public function user()
    {
return $this->belongsTo(User::class);
    }
}