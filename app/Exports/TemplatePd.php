<?php

namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePd implements FromCollection, WithHeadings, WithMapping, WithEvents, WithStyles, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($request){
        $this->jumlah = $request['jumlah'];
    }
    public function headings(): array
    {
        $item = [
            'No',
            'Nama Siswa',
            'Kelas',
            'Jenis Kelamin',
            'NIS',
            'NISN',
            'Tempat Lahir',
            'Tanggal Lahir',
            'UUID',
        ];
        return $item;
    }
    public function map($data): array
    {
        $item = [
            $data['no'],
            $data['nama'],
            $data['kelas'],
            $data['jenis_kelamin'],
            $data['nis'],
            $data['nisn'],
            $data['tempat_lahir'],
            $data['tanggal_lahir'],
            $data['uuid'],
        ];
        return $item;
    }
    public function collection()
    {
        for($i=1;$i<=$this->jumlah;$i++){
            $data[] = [
                'no' => $i, 
                'nama' => NULL, 
                'kelas' => NULL, 
                'jenis_kelamin' => NULL, 
                'nis' => NULL, 
                'nisn' => NULL, 
                'tempat_lahir' => NULL, 
                'tanggal_lahir' => NULL, 
                'uuid' => Str::uuid(),
            ];
        }
        return collect($data);
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:A'.$this->jumlah+1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getProtection()->setSheet(true);
        $sheet->getStyle('A2:H'.$this->jumlah+1)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
        $validation = $sheet->getCell('D2'.$this->jumlah+1)->getDataValidation();
        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input error');
        $validation->setError('Hanya isi L atau P');
        $validation->setPromptTitle('Hanya isi L atau P');
        $validation->setPrompt('Isi L untuk Laki-laki. Isi P untuk Perempuan');
        $validation->setFormula1('"L,P"');
        $validation->setSqref('D2:D'.$this->jumlah);
        $sheet->getStyle('F2:F'.$this->jumlah)->getNumberFormat()->setFormatCode('0000000000');
        //for($i=3;$i<=$this->jumlah;$i++){
            //$sheet()->getCell('D'.$i)->setDataValidation(clone $validation);
        //}
    }
    public function columnWidths(): array
    {
        $default = [
            'A' => 5,
            'B' => 30,
            'C' => 10,
            'D' => 15,
            'E' => 20,
            'F' => 25,
            'G' => 25,
            'H' => 25,
            'i' => 40,
        ];
        return $default;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:I'.$this->jumlah+1)->applyFromArray([
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
