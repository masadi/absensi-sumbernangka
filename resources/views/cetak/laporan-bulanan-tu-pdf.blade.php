@extends('layouts.cetak')
@section('content')
<style>
    body{font-size: 10px;}
    .table thead tr th,
.table tbody tr th,
.table tfoot tr th,
.table thead tr td,
.table tbody tr td,
.table tfoot tr td {
  padding: 5px !important;
  }
</style>
@if($sekolah)
    @if($sekolah->logo_sekolah)
        <img src="{{asset('storage/kop-surat/'.$sekolah->logo_sekolah)}}">
        <br>
        <br>
        <table width="100%">
            <tr>
                <td width="10%">PERIODE</td>
                <td width="90%">: {{$start}} s/d {{$end}}</td>
            </tr>
            <tr>
                <td>UNIT</td>
                <td>: {{$sekolah->nama}}</td>
            </tr>
        </table>
    @else
    <h4 class="text-center">REKAP KEHADIRAN @if($ptk) PTK @else PESERTA DIDIK @endif <br>
        {{$sekolah->nama}} <br>
        <u>PERIODE {{$start}} s/d {{$end}}</u></h4>
    @endif
@else
<h4 class="text-center">REKAP KEHADIRAN @if($ptk) PTK @else PESERTA DIDIK @endif <br>
SMP AL-FALAH SUMBER GAYAM <br>
<u>PERIODE {{$start}} s/d {{$end}}</u></h4>
@endif
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="vertical-align: middle;">NO</th>
            <th class="text-center" style="vertical-align: middle;">
                @if($ptk)
                NIK
                @else
                NISN
                @endif
            </th>
            <th class="text-center" style="vertical-align: middle;">NAMA</th>
            <th class="text-center" style="vertical-align: middle;">JK</th>
            <th class="text-center" style="vertical-align: middle;">Jenjang</th>
            <th class="text-center" style="vertical-align: middle;">Hari Aktif</th>
            <th class="text-center" style="vertical-align: middle;">Hari Libur</th>
            <th class="text-center" style="vertical-align: middle;">Tanpa Keterangan</th>
            <th class="text-center" style="vertical-align: middle;">Jml Sakit</th>
            <th class="text-center" style="vertical-align: middle;">Jml Ijin</th>
            <th class="text-center" style="vertical-align: middle;">Jml Cuti</th>
            <th class="text-center" style="vertical-align: middle;">Jml Terlambat (Hari)</th>
            <th class="text-center" style="vertical-align: middle;">Jml Pulang Cepat (Hari)</th>
            <th class="text-center" style="vertical-align: middle;">Total Tidak Hadir</th>
            <th class="text-center" style="vertical-align: middle;">Total Hadir</th>
            <th class="text-center" style="vertical-align: middle;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
        <tr>
            <td class="text-center" style="vertical-align: middle;">{{$loop->iteration}}</td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                {{$item->nuptk}}
                @else
                {{$item->nisn}}
                @endif
            </td>
            <td style="vertical-align: middle;">{{$item->nama}}</td>
            <td class="text-center" style="vertical-align: middle;">{{$item->jenis_kelamin}}</td>
            <td class="text-center" style="vertical-align: middle;">{{str_replace('ARIYA METTA', '', str_replace('SMKS', 'SMK', str_replace('SDS', 'SD', $item->sekolah->nama)))}}</td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{jml_hari_aktif_ptk($item->sekolah_id, $item->ptk_id, $startStr, $endStr)['jml_hari_aktif']}}
                @else
                    {{jml_hari_aktif_pd($item->sekolah_id, $item->peserta_didik_id, $startStr, $endStr)['jml_hari_aktif']}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{jml_hari_aktif_ptk($item->sekolah_id, $item->ptk_id, $startStr, $endStr)['libur']}}
                @else
                    {{jml_hari_aktif_pd($item->sekolah_id, $item->peserta_didik_id, $startStr, $endStr)['libur']}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{tidak_hadir_ptk($item->ptk_id, $startStr, $endStr)}}
                @else
                    {{tidak_hadir_pd($item->peserta_didik_id, $startStr, $endStr)}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{izin_ptk($item, 'sakit', $startStr, $endStr)}}
                @else
                    {{izin_pd($item, 'sakit', $startStr, $endStr)}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{izin_ptk($item, 'izin', $startStr, $endStr)}}
                @else
                    {{izin_pd($item, 'izin', $startStr, $endStr)}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{izin_ptk($item, 'cuti', $startStr, $endStr)}}
                @else
                    {{izin_pd($item, 'cuti', $startStr, $endStr)}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{$item->absen_masuk()->where(function($query) use ($startStr, $endStr){
                    $query->whereDate('absen_masuk.created_at', '>=', $startStr);
                    $query->whereDate('absen_masuk.created_at', '<=', $endStr);
                    $query->where('terlambat', '>', 0);
                })->count()}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{$item->absen_pulang()->where(function($query) use ($startStr, $endStr){
                    $query->whereDate('absen_pulang.created_at', '>=', $startStr);
                    $query->whereDate('absen_pulang.created_at', '<=', $endStr);
                    $query->where('pulang_cepat', '>', 0);
                })->count()}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                @if($ptk)
                    {{tidak_hadir_ptk($item->ptk_id, $startStr, $endStr)}}
                @else
                    {{tidak_hadir_pd($item->peserta_didik_id, $startStr, $endStr)}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{--$item->absen()->whereHas('absen_masuk', function($query) use ($startStr, $endStr){
                    $query->whereDate('created_at', '>=', $startStr);
                    $query->whereDate('created_at', '<=', $endStr);
                })->count()--}}
                @if($ptk)
                {{total_hadir_ptk($item->ptk_id, $startStr, $endStr)}}
                @else
                {{total_hadir_pd($item->peserta_didik_id, $startStr, $endStr)}}
                @endif
            </td>
            <td class="text-center" style="vertical-align: middle;"></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection