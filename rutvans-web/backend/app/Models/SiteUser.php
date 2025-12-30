<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SiteUser extends Model
{
    use HasFactory;

    protected $table = 'site_users';

    protected $fillable = [
        'site_id',
        'user_id',
        'role',
        'status'
    ];

    protected $casts = [
        'role' => 'string',
        'status' => 'string'
    ];

    // Relación con Site
    public function site()
    {
        return $this->belongsTo(Site::class);
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

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeCoordinators($query)
    {
        return $query->where('role', 'coordinator');
    }
}
