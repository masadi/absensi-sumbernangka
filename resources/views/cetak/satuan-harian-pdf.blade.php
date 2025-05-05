@extends('layouts.cetak')
@section('content')
<h2 class="text-center">Laporan Presensi Harian</h2>
<table>
    <tr>
        <td>Nama</td>
        <td>: 
            @if($data->ptk)
                {{$data->ptk->nama}}
            @else
                {{$data->pd->nama}}
            @endif
        </td>
    </tr>
    <tr>
        <td>Satuan</td>
        <td>:
            @if($data->ptk)
                {{$data->ptk->sekolah->nama}}
            @else
                {{$data->pd->sekolah->nama}}
            @endif
        </td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>: {{$data->tanggal_scan}}</td>
    </tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="2" class="text-center">Kehadiran Hari Ini</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 70%">Datang</td>
            <td class="text-center">{{($data->absen_masuk) ? $data->absen_masuk->jam_scan : '-'}}</td>
        </tr>
        <tr>
            <td>Pulang</td>
            <td class="text-center">{{($data->absen_pulang) ? $data->absen_pulang->jam_scan : '-'}}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td class="text-center">
                {!! ($data->izin) ? $data->izin->keterangan.': '.$data->izin->alasan.'<br>' : '' !!}
                {!! ($data->absen_masuk) ? $data->absen_masuk->keterangan.'<br>' : '' !!}
                {!! ($data->absen_pulang) ? $data->absen_pulang->keterangan : '' !!}
            </td>
        </tr>
    </tbody>
</table>
<?php
$endStr = $data->date_scan;
if($data->ptk){
    $absen_masuk = $data->ptk->absen_masuk->count();
    $terlambat = 0;
    $pulang_cepat = 0;
    foreach ($data->ptk->absen as $absen) {
        $terlambat += $absen->terlambat;
        $pulang_cepat += $absen->pulang_cepat;
    }
    //$terlambat = $data->ptk->absen_masuk->sum('terlambat');
    //$pulang_cepat = $data->ptk->absen_pulang->sum('pulang_cepat');
    $tidak_hadir = tidak_hadir_ptk($data->ptk_id, $startStr, $endStr);
    $jml_hari_aktif = jml_hari_aktif_ptk($data->sekolah_id, $data->ptk_id, $startStr, $endStr)['jml_hari_aktif'];
} else {
    $absen_masuk = $data->pd->absen_masuk->count();
    $terlambat = $data->pd->absen_masuk->sum('terlambat');
    $pulang_cepat = $data->pd->absen_pulang->sum('pulang_cepat');
    $tidak_hadir = tidak_hadir_pd($data->peserta_didik_id, $startStr, $endStr);
    $jml_hari_aktif = jml_hari_aktif_pd($data->sekolah_id, $data->peserta_didik_id, $startStr, $endStr)['jml_hari_aktif'];
}
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th colspan="2" class="text-center">Kedisiplinan</th>
        </tr>
        <tr>
            <th colspan="2" class="text-center">Periode {{$start}} s/d {{--$end--}}{{$data->tanggal_scan_str}}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="width: 70%">Total Keterlambatan</td>
            <td class="text-center">{{$terlambat}}</td>
        </tr>
        <tr>
            <td>Total Pulang Cepat</td>
            <td class="text-center">{{$pulang_cepat}}</td>
        </tr>
        <!--tr>
            <td>Total Menit (keterlambatan + Pulang Cepat)</td>
            <td class="text-center">{{$terlambat + $pulang_cepat}}</td>
        </tr>
        <tr>
            <td>Total Jam (keterlambatan + Pulang Cepat)</td>
            <td class="text-center">{{jam($terlambat + $pulang_cepat)}}</td>
        </tr>
        <tr>
            <td>Total Hari (keterlambatan + Pulang Cepat)</td>
            <td class="text-center">{{hari($terlambat + $pulang_cepat)}}</td>
        </tr-->
        <tr>
            <td>Tidak Hadir</td>
            <td class="text-center">{{$tidak_hadir}}</td>
        </tr>
        <!--tr>
            <td>Total Keseluruhan</td>
            <td class="text-center">{{hari($terlambat + $pulang_cepat) + $tidak_hadir}}</td>
        </tr-->
    </tbody>
</table>
@endsection