<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelHistory extends Model
{
    protected $table = 'travel_history';

    protected $fillable = [
        'sale_id',
        'route_unit_schedule_id',
        'status',
        'actual_departure',
        'actual_arrival',
        'passenger_rating',
        'report', // 🔹 faltaba incluirlo
    ];

    protected $casts = [
        'status' => 'string',
        'actual_departure' => 'datetime',
        'actual_arrival' => 'datetime',
        'passenger_rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con sales
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sale_id');
    }

    // Relación con route_unit_schedule
    public function routeUnitSchedule(): BelongsTo
    {
        return $this->belongsTo(RouteUnitSchedule::class, 'route_unit_schedule_id');
    }
}