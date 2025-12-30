<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{

    protected $table='laboratorios';



    public function bases_de_datos()
    {
        return $this->belongsTo(Remoto::class, 'base_de_datos_id', 'id');
    }

}
