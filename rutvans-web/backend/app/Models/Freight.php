<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freight extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'folio',
        'service_id',
        'driver_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'user_id',
        'origin',
        'destination',
        'number_people',
        'status',
        'amount',
    ];

    protected $casts = [
        'origin' => 'array',
        'destination' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Estados disponibles
    const STATUS_PENDING = 'Pendiente';
    const STATUS_IN_PROGRESS = 'En progreso';
    const STATUS_IN_PROGRESS_PAID = 'En progreso, Pagado';
    const STATUS_COMPLETED = 'Completado';

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el conductor
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Relación con el servicio
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Relación con el sitio
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Generar folio automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($freight) {
            $year = date('Y');
            $latest = Freight::whereYear('created_at', $year)
                ->latest()
                ->first();
            
            $nextId = $latest ? $latest->id + 1 : 1;
            $freight->folio = 'FRE-' . str_pad($nextId, 3, '0', STR_PAD_LEFT) . '-' . $year;
        });
    }

    /**
     * Scope para fletes de un usuario específico
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obtener dirección de origen formateada
     */
    public function getFormattedOriginAttribute()
    {
        $origin = $this->origin;
        return $origin['address'] . ', ' . $origin['city'] . ', ' . $origin['state'];
    }

    /**
     * Obtener dirección de destino formateada
     */
    public function getFormattedDestinationAttribute()
    {
        $destination = $this->destination;
        return $destination['address'] . ', ' . $destination['city'] . ', ' . $destination['state'];
    }

    /**
     * Determina si el registro está en estado "Pendiente"
     * para poder cancelarse
     */
    public function canBeCancelled()
    {
        return $this->status === 'Pendiente';
    }
}