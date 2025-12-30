<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Remoto extends Model
{

    protected $table = 'bases_de_datos'; // Nombre de la tabla en la base de datos remota

    // protected $fillable = [
    //     'servidor_sql',
    //     'base_de_datos',
    //     'usuario_sql',
    //     'password_sql',
    // ];


    public function laboratorios()
    {
        return $this->hasMany(Laboratorio::class, 'laboratorio_id', 'id');
    }

}
