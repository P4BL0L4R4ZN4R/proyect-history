<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'status',
        'site_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'string'
    ];

    // Relación con Site
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Relación con rates
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    // Scope para servicios activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
