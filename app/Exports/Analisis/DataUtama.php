<?php

namespace App\Exports\Analisis;

use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

//data_type
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
class DataUtama extends DefaultValueBinder implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents, WithCustomValueBinder
{
    use Exportable;
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
        return view('unduhan.analisis.data', [
            'data' => $this->data
        ]);
    }
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && Str::contains($value, '-')) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        if($cell->getCoordinate() == 'B16'){
            $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
            $richText->createText('Isikan data pada kolom-kolom yang telah disediakan. Data yang dapat diubah hanya pada kolom-kolom yang tercetak ');
            $payable = $richText->createTextRun('biru.');
            $payable->getFont()->setBold(true);
            //$payable->getFont()->setItalic(true);
            $payable->getFont()->setColor( new \PhpOffice\PhpSpreadsheet\Style\Color( \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE) );
            $cell->setValueExplicit($richText, DataType::TYPE_STRING);
            return true;
        }
        if($cell->getCoordinate() == 'A16' || $cell->getCoordinate() == 'A17'){
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
            // else return default behavior
        return parent::bindValue($cell, $value);
    }
    public function styles(Worksheet $sheet)
    {
        $akhir = $this->data->items->count() + 20;
        $plus_1 = $akhir + 1;
        $plus_6 = $akhir + 6;
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A4')->getFont()->setBold(true);
        $sheet->getStyle('A15')->getFont()->setBold(true);
        $sheet->getStyle('D13')->getFont()->setBold(true);
        $sheet->getStyle('D13')->getFont()->setSize(16);
        $sheet->getStyle('A19')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A19:I19')->getFont()->setBold(true);
        $sheet->getStyle('E13:I13')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E13:I14')->getFont()->setBold(true);
        $sheet->getStyle('E20:H'.$akhir)->getFont()->setBold(true);
        $sheet->getStyle('A'.$plus_1.':A'.$plus_6)->getFont()->setBold(true);
        $sheet->getStyle('A4')->getAlignment()->setTextRotation(90);
        $sheet->getStyle('A4:A11')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('D3D3D3D3');
        $sheet->getStyle('A13')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('D3D3D3D3');
        $sheet->getStyle('D13:I13')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('D3D3D3D3');
        $sheet->getStyle('A19:I20')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('FFFFFFCC');
        /*->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ]);*/
    }
    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 35,
            'C' => 11,
            'D' => 50,
            'E' => 9,
            'F' => 9,
            'G' => 9,
            'H' => 9,
            'I' => 9,
        ];
        $default = [
            'A' => 6,
            'B' => 35,
            'C' => 35,
            'D' => 66,
        ];
        $angka_akhir = $this->data->jawaban_soal->count() + 3;
        $arr = [];
        for($i = 4; $i < $angka_akhir; $i++){
            $arr[$this->arr[$i]] = 3;
        }
        $last = [
            $this->arr[$this->data->jawaban_soal->count() + 3] => 5
        ];
        $result = array_merge($default, $arr, $last);
        return $result;
    }
    public function title(): string
    {
        return 'Data';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $borderStyle = [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ];
                $border_end = 19 + $this->data->items->count();
                $plus_1 = $border_end + 1;
                $plus_2 = $border_end + 2;
                $plus_6 = $border_end + 6;
                $event->sheet->getStyle('A19:I'.$border_end)->applyFromArray([
                    'borders' => [
                        'allBorders' => $borderStyle,
                    ],
                ]);
                $event->sheet->getStyle('D13:I14')->applyFromArray(['borders' => [
                    'allBorders' => $borderStyle,
                ]]);
                $event->sheet->getStyle('A'.$plus_2.':I'.$plus_6)->applyFromArray([
                    'borders' => [
                        'allBorders' => $borderStyle,
                    ],
                ]);
                $styleArray = [
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'top' => $borderStyle,
                        'right' => $borderStyle,
                        'left' => $borderStyle,
                    ],
                ];
                $event->sheet->getStyle('A'.$plus_1.':I'.$plus_1)->applyFromArray($styleArray, false);
                $event->sheet->getStyle('A4:I11')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A13:I14')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'right' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                        'left' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A4:A11')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A13:C14')->applyFromArray(['borders' => [
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['argb' => '000000'],
                    ],
                ]]);
                $event->sheet->getStyle('A19:I19')->applyFromArray(['borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                        'color' => ['argb' => '000000'],
                    ],
                ]]);
            },
        ];
    }
}
