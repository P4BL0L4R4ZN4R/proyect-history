<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Relación con sales
    public function sales()
    {
        return $this->hasMany(Sale::class, 'id_payment');
    }

    // Scope para payments activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope por tipo de pago
    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
