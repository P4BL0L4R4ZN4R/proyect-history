<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'route_name',
        'origin_locality',
        'destination_locality',
        'locality_id',
        'address',
        'phone',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relación con Company (muchos a uno)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Relación con Locality (muchos a uno)
    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }

    // Relación muchos a muchos con usuarios
    public function users()
    {
        return $this->belongsToMany(User::class, 'site_users')
                    ->withPivot('role', 'status')
                    ->withTimestamps();
    }

    // Relación con drivers
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    // Relación con coordinators
    public function coordinators()
    {
        return $this->hasMany(Coordinate::class);
    }

    // Relación con cashiers
    public function cashiers()
    {
        return $this->hasMany(Cashier::class);
    }

    // Relación con units
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    // Relación con routes
    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    // Relación con sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Relación con services
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // Relación con rates
    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    // Relación con freights
    public function freights()
    {
        return $this->hasMany(Freight::class);
    }

    // Relación con shipments
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    // Scopes para filtrar por estado
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
