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

class DayaSerap implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents
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
        return view('unduhan.table-hasil-ulangan', [
            'data' => $this->data
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $baris_akhir = $this->data->jawaban_soal->count() + 6;
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A9')->getFont()->setBold(true);
        $sheet->getStyle('A'.$baris_akhir)->getFont()->setBold(true);
    }
    public function columnWidths(): array
    {
        $default = [
            'A' => 4,
            'B' => 20,
            'D' => 3,          
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
        return 'Daya Serap';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $awal = collect($event->sheet->getDelegate()->toArray());
                $awal->shift(7);
                $awal = $awal->all();
                $akhir = collect($awal);
                $akhir = $akhir->all();
                $jml = count($akhir);
                $start = 9 + $jml;
                $huruf_terakhir = 6 + $jml;
                $border_end = 3 + $this->data->jawaban_soal->count();
                $event->sheet->getStyle('A9:B9')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('A9:'.$this->arr[$border_end].$huruf_terakhir)->applyFromArray([
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
