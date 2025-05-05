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

class RekapSatuan implements FromView, ShouldAutoSize
{
    use Exportable;
    public function query(array $data)
    {
        $this->data = $data;
		return $this;
    }
    public function view(): View
    {
        return view('cetak.satuan-'.$this->data['jenis'].'-excel', $this->data);
    }
}
