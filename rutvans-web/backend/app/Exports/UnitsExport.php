<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitsExport implements FromView, WithStyles, ShouldAutoSize
{
    protected $units;

    public function __construct($units)
    {
        $this->units = $units;
    }

    public function view(): View
    {
return view('units.units_excel', [
    'units' => $this->units
]);

    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
                'alignment' => ['horizontal' => 'center'],
            ],
            'A2:D1000' => [
                'borders' => ['outline' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F9F9F9']],
            ],
        ];
    }
}

?>