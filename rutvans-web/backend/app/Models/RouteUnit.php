<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteUnit extends Model
{
    protected $table = 'route_unit';

    protected $fillable = [
        'route_id',
        'unit_id',
        'driver_unit_id',
        'intermediate_location_id',
        'price',
    ];

    public function schedules()
    {
        return $this->hasMany(RouteUnitSchedule::class, 'route_unit_id');
    }
    
    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    
    public function unit()
    {
        return $this->driverUnit ? $this->driverUnit->unit : null;
    }
    
    public function getUnitAttribute()
    {
        if (!$this->relationLoaded('driverUnit')) {
            $this->load('driverUnit.unit');
        }
        return $this->driverUnit ? $this->driverUnit->unit : null;
    }


    // Relaciones opcionales si tienes las otras tablas

    public function driverUnit()
    {
        return $this->belongsTo(DriverUnit::class, 'driver_unit_id');
    }


}
