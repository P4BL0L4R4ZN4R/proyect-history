<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'role',
        'status'
    ];

    protected $casts = [
        'role' => 'string',
        'status' => 'string'
    ];

    // Relación con Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOwners($query)
    {
        return $query->where('role', 'admin');
    }
}
