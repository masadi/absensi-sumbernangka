<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportKeterangan implements FromView, WithStyles, WithTitle, WithEvents
{
    use Exportable;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('unduhan.keterangan', [
            'data' => $this->data
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A26')->getFont()->setBold(true);
        $underlines = ['D8', 'D15', 'D33', 'D40'];
        foreach($underlines as $underline){
            $sheet->getStyle($underline)->getFont()->setUnderline(true);
        }
    }
    public function title(): string
    {
        return 'Keterangan';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $borderStyle = [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ];
                $allBorders = [
                    'borders' => [
                        'allBorders' => $borderStyle,
                    ],
                ];
                $borderBottom = [
                    'borders' => [
                        'bottom' => $borderStyle,
                    ],
                ];
                $styleArray = [
                    'borders' => [
                        'bottom' => $borderStyle,
                        'top' => $borderStyle,
                        'right' => $borderStyle,
                        'left' => $borderStyle,
                    ],
                ];
                $event->sheet->getStyle('D8:E9')->applyFromArray($styleArray);
                $event->sheet->getStyle('D15:E16')->applyFromArray($styleArray);
                $event->sheet->getStyle('D33:E34')->applyFromArray($styleArray);
                $event->sheet->getStyle('D40:E41')->applyFromArray($styleArray);
                $event->sheet->getStyle('A24:M24')->applyFromArray($borderBottom);
            },
        ];
    }
}
