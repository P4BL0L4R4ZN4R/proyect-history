<?php

namespace App\Exports;

use App\Models\Locality; // Modelo que representa las localidades
use Illuminate\Contracts\View\View; // Contrato para renderizar vistas en Laravel
use Maatwebsite\Excel\Concerns\FromView; // Interfaz para exportar desde vistas Blade
use Maatwebsite\Excel\Concerns\WithStyles; // Interfaz para aplicar estilos al archivo Excel
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Interfaz para ajustar automáticamente el tamaño de las columnas
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Clase para definir estilos avanzados de hojas de cálculo

class LocalidadesExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $query; // Consulta filtrada que se pasará al archivo Excel

    // Constructor para inicializar la clase con la consulta filtrada
    public function __construct($query)
    {
        $this->query = $query;
    }

    // Define la vista Blade que se usará para generar el archivo Excel
    public function view(): View
    {
        return view('exports.localidades.localidades_excel', [
            'localidades' => $this->query->get() // Pasa las localidades filtradas a la vista
        ]);
    }

    // Define los estilos que se aplicarán a la hoja de cálculo
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la fila del encabezado (fila 1)
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], // Texto en blanco y negrita
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'FF6600']], // Fondo naranja
                'alignment' => ['horizontal' => 'center'], // Alineación centrada
            ],

            // Estilo para las filas de datos (A2:F1000)
            'A2:F1000' => [
                'borders' => ['outline' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]], // Bordes negros
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F2F2F2']], // Fondo gris claro
            ],
        ];
    }
}
