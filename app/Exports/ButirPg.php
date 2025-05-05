<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\beforeWriting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

//rumus
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class ButirPg extends DefaultValueBinder implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents, WithCustomValueBinder
{
    use Exportable;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('unduhan.butir-pilihan-ganda', [
            'data' => $this->data
        ]);
    }
    public function bindValue(Cell $cell, $value)
    {
        if (is_string($value) && Str::startsWith($value, '=')) {
            //TYPE_STRING
            //=B11+C11	=B11-C11	10	=IF(B11>-1;((2*(B11-C11))/F11);"")	=IF(G11>=0,4;"√";"")	=IF(G11>0,39;"";IF(G11<0,2;"";"√"))	=IF(G11<=0,19;"√";"")	=IF(B11>-1;((D11)/F11);"")	=IF(K11<=0,3;"√";"")	=IF(K11<=0,3;"";IF(K11>=0,71;"";"√"))	=IF(K11>=0,71;"√";"")	=IF(H11="√";"diterima";IF(I11="√";"diperbaiki";IF(J11="√";"dibuang";"")))
            if(Str::startsWith($cell->getCoordinate(), 'D')){
                $row = substr($cell->getCoordinate(), 1, 10);
                $cell->setValueExplicit('=B'.$row.'+C'.$row, DataType::TYPE_FORMULA);
            }
            if(Str::startsWith($cell->getCoordinate(), 'E')){
                $row = substr($cell->getCoordinate(), 1, 10);
                $cell->setValueExplicit('=B'.$row.'-C'.$row, DataType::TYPE_FORMULA);
            }
            if(Str::startsWith($cell->getCoordinate(), 'G')){
                $row = substr($cell->getCoordinate(), 1, 10);
                $parent = $cell->getParent();
                $b = $parent->getParent()->getCell('B'.$row)->getValue();
                $c = $parent->getParent()->getCell('C'.$row)->getValue();
                $f = $parent->getParent()->getCell('F'.$row)->getValue();
                $setGValue = $this->isiValue($b, $c, $f, 'G');
                $setHValue = $this->isiValue($b, $c, $f, 'H');
                $parent->getParent()->getCell('G'.$row)->setValue($setGValue);
                //$parent->getParent()->getCell('H'.$row)->setValue($setGValue);
            }
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
    private function isiValue($b, $c, $f, $cell){
        if($cell == 'G'){
            $setValue = NULL;
            if($b > -1){
                $setValue = (2 * ($b - $c)) / $f;
            }
            return $setValue;
        }
        if($cell == 'H'){
        }
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 5,
            'C' => 5,
            'D' => 6,
            'E' => 6,
            'F' => 5,
            'G' => 9,
            'H' => 6,
            'I' => 7,
            'J' => 6,
            'K' => 9,
            'L' => 6,
            'M' => 6,
            'N' => 6,
            'O' => 13,
        ];
    }
    public function title(): string
    {
        return 'Butir Pilihan Ganda';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                /*dd($this->data->items);
                dump($event->sheet->getCell('A8')->getCoordinate());
                dump($event->sheet->getCell('G11')->getValue());
                dd();*/
                $event->sheet->setCellValue('A8', 1);
                $border_end = 10 + $this->data->jawaban_soal->count();
                $event->sheet->getStyle('A8:O'.$border_end)->applyFromArray([
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
    /*public static function beforeWriting(BeforeWriting $event) 
    {
        
        $event->sheet->getActiveSheet()->setCellValue('B8','=IF(C4>500,"profit","loss")');
    }*/
}
