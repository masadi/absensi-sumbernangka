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

class Analisis implements FromView, WithStyles, WithColumnWidths, WithTitle, WithEvents
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
        return view('unduhan.analisis.analisis', [
            'data' => $this->data
        ]);
    }
    public function styles(Worksheet $sheet)
    {
        $baris_akhir = 11 + $this->data->jawaban_soal->count() * $this->data->jadwal_ujian->soal_ujian->jumlah_pg;
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A10:N11')->getFont()->setBold(true);
        $sheet->getStyle('A10:N11')->getAlignment()->setWrapText(true);
        $sheet->getStyle('K12:N'.$baris_akhir)->getAlignment()->setWrapText(true);
    }
    public function columnWidths(): array
    {
        $default = [
            'A' => 4,
            'B' => 5,
            'C' => 9,
            'D' => 9,
            'E' => 5,
            'F' => 5,
            'G' => 10,
            'H' => 5,
            'I' => 10,
            'J' => 5,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 15,
        ];
        return $default;
    }
    public function title(): string
    {
        return 'Analisis';
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
                $huruf_terakhir = 7 + $jml;
                $event->sheet->getStyle('A10:N'.$huruf_terakhir)->applyFromArray([
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
