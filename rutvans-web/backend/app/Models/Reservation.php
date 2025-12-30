<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'unit_id',
        'user_id',
        'travel_date',
        'seats',
        'ticket_details',
        'total_amount',
        'status'
    ];

    protected $casts = [
        'seats' => 'array',
        'ticket_details' => 'array',
        'travel_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => 'confirmed'
    ];

    /**
     * Relación con la unidad
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para reservaciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope para reservaciones en una fecha específica
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('travel_date', $date);
    }

    /**
     * Verificar si la reservación está activa
     */
    public function isActive()
    {
        return $this->status === 'confirmed' &&
               Carbon::parse($this->travel_date)->isToday();
    }
}
