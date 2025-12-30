<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteUnitSchedule extends Model
{
    protected $table = 'route_unit_schedule';

    protected $fillable = [
        'route_unit_id',
        'schedule_date',
        'schedule_time',
        'status',
    ];

    public function routeUnit()
    {
        return $this->belongsTo(RouteUnit::class, 'route_unit_id');
    }

    public function getStartAttribute()
    {
        return $this->schedule_date . 'T' . $this->schedule_time;
    }
}
