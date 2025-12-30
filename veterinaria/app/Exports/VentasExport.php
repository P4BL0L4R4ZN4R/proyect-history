<?php

namespace App\Exports;

use App\Models\DetalleVenta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        return DetalleVenta::with(['venta', 'producto'])
            ->when($this->fechaInicio && $this->fechaFin, function($query) {
                $query->whereBetween('created_at', [$this->fechaInicio, $this->fechaFin]);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Fecha',
            'Producto',
            'Cantidad',
            'Precio Unitario',
            'Subtotal',
            'Tipo de Pago',
            'Total'
        ];
    }

    public function map($venta): array
    {
        return [
            $venta->venta->folio,
            Carbon::parse($venta->created_at)->format('d/m/Y H:i'),
            $venta->producto->nombre,
            $venta->cantidad,
            number_format($venta->precio_unitario, 2),
            number_format($venta->subtotal, 2),
            $venta->venta->tipo_pago ? $this->getTipoPago($venta->venta->tipo_pago) : '---',
            number_format($venta->venta->total, 2)
        ];
    }

    public function getTipoPago($tipo)
{
    return match((int)$tipo) {
        1 => 'Efectivo',
        2 => 'Tarjeta',
        3 => 'Transferencia',
        default => '---'
    };
}


    public function styles(Worksheet $sheet)
    {
        // Estilo para los encabezados
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Autoajustar el ancho de las columnas
        foreach(range('A','H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Formato numérico para columnas de dinero
        $sheet->getStyle('E2:H' . ($sheet->getHighestRow()))
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');
    }
}
