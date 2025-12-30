<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    // Tabla explícita, aunque Laravel infiere 'drivers' bien
    protected $table = 'drivers';

    // Campos que puedes asignar masivamente
    protected $fillable = [
        'user_id',
        'site_id'
    ];
    /**
     * Relación con documentos del conductor
     */
    public function documents()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id');
    }

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con Site
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function driverUnits()
    {
        return $this->hasMany(DriverUnit::class, 'driver_id');
    }

    /**
     * Relación con unidades a través de la tabla pivot
     */
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'driver_unit', 'driver_id', 'unit_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
