<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DriverUnit extends Pivot
{
    protected $table = 'driver_unit';

    protected $fillable = [
        'driver_id',
        'unit_id',
        'status'
    ];
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'driver_unit', 'driver_id', 'unit_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function routeUnits()
    {
        return $this->hasMany(RouteUnit::class, 'driver_unit_id');
    }
}
