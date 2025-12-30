<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'location_s_id',
        'location_f_id',
        'site_id'
    ];

    // Relación con Site
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Localidad de inicio
    public function locationStart()
    {
        return $this->belongsTo(Locality::class, 'location_s_id');
    }

    // Localidad de fin
    public function locationEnd()
    {
        return $this->belongsTo(Locality::class, 'location_f_id');
    }

    // Relación con units (muchos a muchos)
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'route_unit', 'route_id', 'unit_id')
                    ->withTimestamps();
    }

    // Relación con sales
    public function sales()
    {
        return $this->hasMany(Sale::class, 'route_id');
    }

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}

