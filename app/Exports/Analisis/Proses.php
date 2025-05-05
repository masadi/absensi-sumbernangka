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

class Proses extends DefaultValueBinder implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents, WithCustomValueBinder
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
        return view('unduhan.analisis.proses', [
            'data' => $this->data
        ]);
    }
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && Str::contains($value, '-')) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
    public function styles(Worksheet $sheet)
    {
        $endCols = $this->arr[$this->data->jawaban_soal->count() + 7];
        $header = 5;
        $footer = 5;
        $baris_akhir_atas = 1 + $header + $this->data->items->count();
        $baris_akhir_bawah = $header + $footer + $this->data->items->count();
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1:G1')->getFont()->getColor()->setRGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
        $sheet->getStyle('E2')->getFont()->setBold(true);
        $sheet->getStyle('A4:C4')->getFont()->setBold(true);
        $sheet->getStyle('D4:'.$endCols.'5')->getFont()->setBold(true);
        $sheet->getStyle('A4')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A'.$baris_akhir_atas.':A'.$baris_akhir_bawah)->getFont()->setBold(true);
    }
    public function columnWidths(): array
    {
        $default = [
            'A' => 6,
            'B' => 25,
            'C' => 50,          
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
        return 'Proses';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $huruf_terakhir = 10 + $this->data->items->count();
                $border_end = 7 + $this->data->jawaban_soal->count();
                $event->sheet->getStyle('A1:G2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A4:'.$this->arr[$border_end].$huruf_terakhir)->applyFromArray([
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
