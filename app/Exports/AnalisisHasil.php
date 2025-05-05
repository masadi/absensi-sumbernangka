<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Analisis\DataUtama;
use App\Exports\Analisis\Proses;
use App\Exports\Analisis\Analisis;
use App\Exports\Analisis\Report;
use App\Exports\Analisis\Nilai;
use App\Exports\Analisis\DayaSerap;

class AnalisisHasil implements WithMultipleSheets
{
    use Exportable;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function sheets(): array
    {
        $sheets = [
            new DataUtama($this->data),
            new Proses($this->data),
            new Analisis($this->data),
            new Report($this->data),
            new Nilai($this->data),
            new DayaSerap($this->data),
            /*new DayaSerap($this->data),
            new HasilUlangan($this->data),
            new ButirPg($this->data),
            new ExportKeterangan($this->data),*/
        ];
        return $sheets;
    }
}
