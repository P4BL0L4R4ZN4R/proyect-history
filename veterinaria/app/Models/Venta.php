<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{

    protected $table =  'ventas';

    public function detalle_venta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
