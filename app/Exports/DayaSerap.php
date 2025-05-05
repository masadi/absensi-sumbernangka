<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class DayaSerap implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    use Exportable;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('unduhan.daya-serap', [
            'data' => $this->data
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyle('A44')->getFont()->setBold(true);
        $sheet->getStyle('A44')->getFont()->setUnderline(true);
        $sheet->getStyle('A31:A35')->getAlignment()->setWrapText(true);
        $sheet->getStyle('B34')->getFont()->setUnderline(true);
    }
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 15,
            'D' => 17,
            'E' => 14,
            'F' => 15,
        ];
    }
    public function title(): string
    {
        return 'Daya Serap';
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
                $borderBottomRight = [
                    'borders' => [
                        'bottom' => $borderStyle,
                        'right' => $borderStyle,
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
                $event->sheet->getStyle('A8:F30')->applyFromArray($allBorders);
                //$sheet->getStyle('A31:A35')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('A31:A35')->applyFromArray($allBorders);
                $event->sheet->getStyle('B31:F31')->applyFromArray($borderBottomRight);
                $event->sheet->getStyle('B32:F32')->applyFromArray($borderBottomRight);
                $event->sheet->getStyle('B33:F33')->applyFromArray($borderBottomRight);
                $event->sheet->getStyle('B34:F35')->applyFromArray($borderBottomRight);
                //$event->sheet->getStyle('D40:E41')->applyFromArray($styleArray);
                //$event->sheet->getStyle('A24:M24')->applyFromArray($borderBottom);
            },
        ];
    }
}
