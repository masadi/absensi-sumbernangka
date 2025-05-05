<?php

namespace App\Exports\Analisis;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Report implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    public function __construct($data)
    {
        $this->data = $data;
        $arr = [];
        for($x = 'A'; $x < 'ZZ'; $x++){
            $arr[] = $x;
        }
        $this->arr = $arr;
    }
    public function view(): View
    {
        return view('unduhan.analisis.report', [
            'data' => $this->data
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $header = 11;
        $footer = 7;
        $baris_akhir_atas = 1 + $header + $this->data->items->count();
        $baris_akhir_bawah = $header + $footer + $this->data->items->count();
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A10:K11')->getFont()->setBold(true);
        $sheet->getStyle('A10:K11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$baris_akhir_atas)->getAlignment()->setTextRotation(90);
        $sheet->getStyle('A'.$baris_akhir_atas)->getFont()->setBold(true);
        $sheet->getStyle('A'.$baris_akhir_atas.':A'.$baris_akhir_bawah)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('D3D3D3D3');
        $sheet->getStyle('B'.$baris_akhir_atas.':B'.$baris_akhir_bawah)->getFont()->setBold(true);
        $sheet->getStyle('D'.$baris_akhir_atas.':D'.$baris_akhir_bawah)->getFont()->setBold(true);
    }
    public function columnWidths(): array
    {
        $default = [
            'A' => 5,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'K' => 10,
        ];
        return $default;
    }
    public function title(): string
    {
        return 'Report';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $header = 11;
                $footer = 7;
                $baris_akhir = $header + $footer + $this->data->items->count();
                $event->sheet->getStyle('A10:K'.$baris_akhir)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
