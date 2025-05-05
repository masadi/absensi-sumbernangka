<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\Pelanggaran;
use Carbon\Carbon;

class PelanggaranExport implements FromView, ShouldAutoSize, WithEvents
{
    use Exportable;
    public function query(array $data)
    {
        $this->start = $data['start'] ?? '2023-01-01';
        $this->end = $data['end'] ?? now()->format('Y-m-d');
        $this->sekolah_id = $data['sekolah_id'];
        $this->semester_id = $data['semester_id'];
		return $this;
    }
    public function view(): View
    {
        $data = Pelanggaran::with(['petugas', 'pd', 'anggota_rombel.rombongan_belajar' => function($query){
            $query->where('semester_id', $this->semester_id);
        }])->whereHas('anggota_rombel', function($query){
            $query->whereHas('peserta_didik', function($query){
                $query->where('sekolah_id', $this->sekolah_id);
            });
            $query->where('semester_id', $this->semester_id);
        })->when($this->end, function($query){
            $query->whereDate('tanggal', '>=', $this->start);
            $query->whereDate('tanggal', '<=', $this->end);
        })->get();
        return view('cetak.laporan-pelanggaran', [
            'data' => $data,
            'startStr' => Carbon::createFromTimeStamp(strtotime($this->start))->translatedFormat('j F Y'),
            'endStr' => Carbon::createFromTimeStamp(strtotime($this->end))->translatedFormat('j F Y'),
        ]);
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $awal = collect($event->sheet->getDelegate()->toArray());
                $awal->shift(7);
                $awal = $awal->all();
                $akhir = collect($awal);
                $akhir->pop(9);
                $akhir = $akhir->all();
                $jml = count($akhir);
                $start = 9 + $jml;
                $end = 16 + $jml;
                $border_end = 7 + $jml;
                $kasek = 'A'.$start.':I'.$end;
                $event->sheet->getStyle('A7:K'.$border_end)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getDelegate()->getStyle($kasek)
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle('A1:A2')
                                ->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
