<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'folio',
        'route_unit_id',
        'service_id',
        'user_id',
        'driver_id',
        'sender_name',
        'receiver_name',
        'package_description', // Corregido para coincidir con la tabla
        'package_image',       // Corregido para coincidir con la tabla
        'status',
        'amount',              // Corregido para coincidir con la tabla
    ];

    protected $casts = [
        'amount' => 'decimal:2', // Es buena práctica castear el decimal
        'status' => 'string',
    ];

    // Relación con Site
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
    
    // **NOTA:** He eliminado la relación 'freight()' porque 'freight_id' no está en tu esquema de tabla.

    // Scope para filtrar por site
    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    // Scope por estado
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope para envíos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'Pendiente'); // Usando el valor por defecto de la tabla
    }

    // Scope para envíos entregados
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }
    public function service()
{
    return $this->belongsTo(Service::class, 'service_id');
}

}