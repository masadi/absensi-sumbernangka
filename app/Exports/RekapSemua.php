<?php

namespace App\Exports;

use App\Models\Ptk;
use App\Models\Peserta_didik;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class RekapSemua implements FromView, ShouldAutoSize
{
    use Exportable;
    public function query($jenis, $start, $end)
    {
        $this->start = $start;
        $this->end = $end;
        $this->jenis = $jenis;
		return $this;
    }
    public function view(): View
    {
        return view('cetak.semua-excel', [
            'start' => Carbon::parse($this->start)->translatedFormat('d F Y'),
            'end' => Carbon::parse($this->end)->translatedFormat('d F Y'),
            'from' => $this->start,
            'to' => $this->end,
            'collection_pd' => ($this->jenis == 'pd' || $this->jenis == 'all') ? Peserta_didik::whereHas('absen', function($query){
                $query->whereDate('created_at', '>=', $this->start);
                $query->whereDate('created_at', '<=', $this->end);
            })->get() : NULL,
            'collection_ptk' => ($this->jenis == 'ptk' || $this->jenis == 'all') ? Ptk::whereHas('absen', function($query){
                $query->whereDate('created_at', '>=', $this->start);
                $query->whereDate('created_at', '<=', $this->end);
            })->get() : NULL,
        ]);
    }
}
