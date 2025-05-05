<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportJawaban implements FromView, ShouldAutoSize
{
    use Exportable;
    public function query(array $data)
    {
        $this->soal_ujian = $data['soal_ujian'];
		return $this;
    }
    public function view(): View
    {
        return view('cetak.template-kunci-jawaban', [
            'soal_ujian' => $this->soal_ujian,
        ]);
    }
}
