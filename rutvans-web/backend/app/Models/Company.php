<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'business_name',
        'rfc',
        'locality_id',
        'address',
        'phone',
        'email',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relación con Locality (muchos a uno)
    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    // Relación con Sites (uno a muchos)
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    // Relación con usuarios a través de CompanyUser (muchos a muchos)
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_users', 'company_id', 'user_id');
    }

    // Scopes para filtrar por estado
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
