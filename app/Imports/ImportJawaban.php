<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Jawaban_soal;

class ImportJawaban implements ToModel, WithHeadingRow
{
    public function __construct($soal_ujian_id){
        $this->soal_ujian_id = $soal_ujian_id;
        return $this;
    }
    public function model(array $row)
    {
        if($row['NO_SOAL'] && $row['KUNCI_JAWABAN'])
        Jawaban_soal::updateOrCreate(
            [
                'soal_ujian_id' => $this->soal_ujian_id,
                'nomor_jawaban' => $row['NO_SOAL'],
            ],
            [
                'jawaban' => $row['KUNCI_JAWABAN'],
            ]
        );
    }
    
    public function headingRow(): int
    {
        return 6;
    }
}
