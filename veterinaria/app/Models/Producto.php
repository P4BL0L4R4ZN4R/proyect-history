<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{

        // Campos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'stock',
        'precio_compra',
        'precio_unitario',
        'precio_venta',
        'minimo_stock',
        'subcategoria_id',
        'categoria_id',
        'codigo',
        'descripcion',
        'caducidad',
        'lote',
        'created_at',
        'updated_at',
    ];

protected $casts = [
    'caducidad' => 'date', // Esto lo convierte en Carbon instance
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

    protected $table = 'productos';

    // Un producto puede estar en muchos detalles de venta
    public function detalle_venta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    // Relación con categoría (un producto PERTENECE a una categoría)
        // Producto.php
        public function categoria()
        {
            return $this->belongsTo(Categoria::class, 'categoria_id');
        }

        public function subcategoria()
        {
            return $this->belongsTo(Subcategoria::class, 'subcategoria_id') ;
        }


}
