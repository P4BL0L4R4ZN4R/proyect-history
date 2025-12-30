<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\Laboratorio;

class GestorBasesDeDatos
{
    private $conexiones = [];

    public function conectarBD()
    {
        try {
            $laboratorios = Laboratorio::all();

            foreach ($laboratorios as $laboratorio) {
                $connectionName = 'externo_' . $laboratorio->id;

                // Verificar si ya se ha configurado esta conexión
                if (!array_key_exists($connectionName, $this->conexiones)) {
                    // Configurar la conexión si no existe en el pool
                    $this->configurarConexion($connectionName, $laboratorio);
                }
            }

        } catch (\Exception $e) {
            Log::error('Error al configurar conexiones a bases de datos: ' . $e->getMessage());
            throw $e;
        }
    }



    private function configurarConexion($connectionName, $laboratorio)
    {
        try {
            // Configurar la conexión dinámicamente
            Config::set("database.connections.$connectionName", [
                'driver' => 'mysql',
                'host' => $laboratorio->bases_de_datos->servidor_sql,
                'port' => '3306',
                'database' => $laboratorio->bases_de_datos->base_de_datos,
                'username' => $laboratorio->bases_de_datos->usuario_sql,
                'password' => $laboratorio->bases_de_datos->password_sql,
            ]);

            // Establecer la conexión y almacenarla en el pool
            $this->conexiones[$connectionName] = DB::connection($connectionName);
        } catch (\Exception $e) {
            Log::error("Error al configurar la conexión $connectionName: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerConexion($connectionName)
    {
        // Verificar si la conexión está en el pool y devolverla
        if (array_key_exists($connectionName, $this->conexiones)) {
            return $this->conexiones[$connectionName];
        }

        // Si no está en el pool, configurarla y devolverla
        $laboratorio = Laboratorio::find(explode('_', $connectionName)[1]); // Obtener el ID del laboratorio
        $this->configurarConexion($connectionName, $laboratorio);

        return $this->conexiones[$connectionName];
    }


    

    public function conectarBDyBorrar($id_laboratorio)
    {
        try {
            $laboratorio = Laboratorio::findorfail($id_laboratorio);

           
                $connectionName = 'externo_' . $laboratorio->id;

                // Verificar si ya se ha configurado esta conexión
                if (!array_key_exists($connectionName, $this->conexiones)) {
                    // Configurar la conexión si no existe en el pool
                    $this->configurarConexion($connectionName, $laboratorio);
                }
            

        } catch (\Exception $e) {
            Log::error('Error al configurar conexiones a bases de datos: ' . $e->getMessage());
            throw $e;
        }
    }







    public function conectarBDyEditar($id)
    {
        try {
            $laboratorio = Laboratorio::findorfail($id);

           
                $connectionName = 'externo_' . $laboratorio->id;

                // Verificar si ya se ha configurado esta conexión
                if (!array_key_exists($connectionName, $this->conexiones)) {
                    // Configurar la conexión si no existe en el pool
                    $this->configurarConexion($connectionName, $laboratorio);
                }
            

        } catch (\Exception $e) {
            Log::error('Error al configurar conexiones a bases de datos: ' . $e->getMessage());
            throw $e;
        }
    }


}
