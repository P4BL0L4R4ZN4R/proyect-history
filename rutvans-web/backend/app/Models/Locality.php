<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Locality extends Model
{
    use HasFactory;

    protected $table = 'localities';

    protected $fillable = [
        'longitude',
        'latitude',
        'locality',
        'street',
        'postal_code',
        'municipality',
        'state',
        'country',
        'locality_type',
    ];

    protected $casts = [
        'longitude' => 'decimal:6',
        'latitude' => 'decimal:6'
    ];

    // Relación con sites (uno a muchos)
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    // Relación con routes como punto de inicio
    public function routesStart()
    {
        return $this->hasMany(Route::class, 'id_location_s');
    }

    // Relación con routes como punto final
    public function routesEnd()
    {
        return $this->hasMany(Route::class, 'id_location_f');
    }

    // Scope para buscar por municipio
    public function scopeByMunicipality($query, $municipality)
    {
        return $query->where('municipality', $municipality);
    }

    // Scope para buscar por estado
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }
}
