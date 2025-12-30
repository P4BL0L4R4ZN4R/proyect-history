<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate', 
        'capacity', 
        'photo',
        'site_id'
    ];

    // Relación con Site
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    // Relación con drivers
    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_unit', 'id_unit', 'id_driver')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    // Relación con routes
    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_unit', 'id_unit', 'id_route')
                    ->withTimestamps();
    }

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }
}
