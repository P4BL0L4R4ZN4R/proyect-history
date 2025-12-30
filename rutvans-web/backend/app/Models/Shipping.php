<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    // NOTA: Se quitó SoftDeletes porque la tabla no tiene deleted_at

    protected $table = 'shippings';

    protected $fillable = [
        'site_id',
        'folio',
        'route_unit_schedule_id',
        'user_id',
        'receiver_name',
        'receiver_description',
        'package_description',
        'length_cm',
        'width_cm',
        'height_cm',
        'weight_kg',
        'fragile',
        'package_image',
        'status',
        'amount'
    ];

    protected $casts = [
        'fragile' => 'boolean',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function routeUnitSchedule()
    {
        return $this->belongsTo(RouteUnitSchedule::class, 'route_unit_schedule_id');
    }

    // Accessor para dimensiones combinadas
    public function getDimensionsAttribute()
    {
        return $this->length_cm . '×' . $this->width_cm . '×' . $this->height_cm . ' cm';
    }

    // Accessor para volumen
    public function getVolumeAttribute()
    {
        return round(($this->length_cm * $this->width_cm * $this->height_cm) / 1000, 2); // Convertir a litros
    }

    // Scope para búsqueda
    public function scopeSearch($query, $search)
    {
        return $query->where('folio', 'LIKE', "%{$search}%")
                    ->orWhere('receiver_name', 'LIKE', "%{$search}%")
                    ->orWhere('package_description', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
    }
}