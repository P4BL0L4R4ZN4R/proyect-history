<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //

    protected $fillable = ['nombre'];
    protected $table = 'categorias';

    // Un producto puede estar en muchos detalles de venta
    public function producto()
    {
        return $this->hasMany(Producto::class);
    }


}
