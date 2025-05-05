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
<h2 class="text-center">Laporan Presensi PTK (Individu)</h2>
<table class="table">
    <tr>
        <td>Nama</td>
        <td>: {{$data->nama}}</td>
        <td>Total Hari Aktif</td>
        <td>: 
            @if($ptk)
            {{jml_hari_aktif_ptk($data->sekolah_id, $data->ptk_id, $startStr, $endStr)['jml_hari_aktif']}}
            @else
            {{jml_hari_aktif_pd($data->sekolah_id, $data->peserta_didik_id, $startStr, $endStr)['jml_hari_aktif']}}
            @endif
        </td>
    </tr>
    <tr>
        <td>Satuan</td>
        <td>: {{$data->sekolah->nama}}</td>
        <td>Total Hari Libur</td>
        <td>: 
            @if($ptk)
            {{jml_hari_aktif_ptk($data->sekolah_id, $data->ptk_id, $startStr, $endStr)['libur']}}
            @else
            {{jml_hari_aktif_pd($data->sekolah_id, $data->peserta_didik_id, $startStr, $endStr)['libur']}}
            @endif
        </td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>: {{$start}} s/d {{$end}}</td>
        <td>Dicetak pada</td>
        <td>: {{$tanggal_cetak}}</td>
    </tr>
</table>
<strong><u>Rekap Kehadiran</u></strong><br>
<table class="table table-bordered">
    <tr>
        <td class="text-center" colspan="9">Total</td>
    </tr>
    <tr>
        <td class="text-center">Hadir</td>
        <td class="text-center">Tanpa Keterangan</td>
        <td class="text-center">Sakit</td>
        <td class="text-center">Ijin</td>
        <td class="text-center">Cuti</td>
        <td class="text-center">Tidak Hadir</td>
        <td class="text-center">Terlambat</td>
        <td class="text-center">Pulang Cepat</td>
        <td class="text-center">Tidak Scan Pulang</td>
    </tr>
    <tr>
        <td class="text-center">
            {{--$data->absen()->where(function($query) use ($startStr, $endStr){
                $query->whereHas('absen_masuk');
                $query->whereDate('created_at', '>=', $startStr);
                $query->whereDate('created_at', '<=', $endStr);
                $query->orwhereHas('izin', function($query){
                    $query->where('jenis', 'Sekolah');
                });
                $query->whereDate('created_at', '>=', $startStr);
                $query->whereDate('created_at', '<=', $endStr);
            })->count()--}}
            @if($ptk)
            {{total_hadir_ptk($data->ptk_id, $startStr, $endStr)}}
            @else
            {{total_hadir_pd($data->peserta_didik_id, $startStr, $endStr)}}
            @endif
        </td>
        <td class="text-center">
            @if($ptk)
                {{tidak_hadir_ptk($data->ptk_id, $startStr, $endStr, FALSE)}}
            @else
                {{tidak_hadir_pd($data->peserta_didik_id, $startStr, $endStr)}}
            @endif
        </td>
        <td class="text-center">
            @if($ptk)
                {{izin_ptk($data, 'sakit', $startStr, $endStr)}}
            @else
                {{izin_pd($data, 'sakit', $startStr, $endStr)}}
            @endif
        </td>
        <td class="text-center">
            @if($ptk)
                {{izin_ptk($data, 'izin', $startStr, $endStr)}}
            @else
                {{izin_pd($data, 'izin', $startStr, $endStr)}}
            @endif
        </td>
        <td class="text-center">
            @if($ptk)
                {{izin_ptk($data, 'cuti', $startStr, $endStr)}}
            @else
                {{izin_pd($data, 'cuti', $startStr, $endStr)}}
            @endif
        </td>
        <td class="text-center">
            @if($ptk)
                {{tidak_hadir_ptk($data->ptk_id, $startStr, $endStr)}}
            @else
                {{tidak_hadir_pd($data->peserta_didik_id, $startStr, $endStr)}}
            @endif
        </td>
        <td class="text-center">
            {{$data->absen_masuk()->where(function($query) use ($startStr, $endStr){
                $query->whereDate('absen_masuk.created_at', '>=', $startStr);
                $query->whereDate('absen_masuk.created_at', '<=', $endStr);
                $query->where('terlambat', '>', 0);
            })->count()}}
        </td>
        <td class="text-center">
            {{$data->absen_pulang()->where(function($query) use ($startStr, $endStr){
                $query->whereDate('absen_pulang.created_at', '>=', $startStr);
                $query->whereDate('absen_pulang.created_at', '<=', $endStr);
                $query->where('pulang_cepat', '>', 0);
            })->count()}}
        </td>
        <td class="text-center">
            {{$data->absen()->whereHas('absen_masuk', function($query) use ($startStr, $endStr){
                $query->whereDate('created_at', '>=', $startStr);
                $query->whereDate('created_at', '<=', $endStr);
            })->whereDoesntHave('absen_pulang', function($query) use ($startStr, $endStr){
                $query->whereDate('created_at', '>=', $startStr);
                $query->whereDate('created_at', '<=', $endStr);
            })->count()}}
        </td>
    </tr>
</table>
<strong><u>Data Rinci</u></strong><br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;vertical-align: middle" rowspan="2">No</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" rowspan="2">Tanggal</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" rowspan="2">Jam Datang</th>
            <th class="text-center" style="width: 11%;vertical-align: middle" colspan="2">Keterlambatan (Menit)</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" rowspan="2">Jam Pulang</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" colspan="2">Pulang Cepat (Menit)</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" rowspan="2">Ketidakhadiran</th>
        </tr>
        <tr>
            <th class="text-center">Waktu (Menit)</th>
            <th class="text-center">Alasan</th>
            <th class="text-center">Waktu (Menit)</th>
            <th class="text-center">Alasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->absen as $absen)
        <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            <td class="text-center">{{$absen->tanggal_scan}}</td>
            <td class="text-center">{{($absen->absen_masuk) ? $absen->absen_masuk->created_at->format('H:i:s') : '-'}}</td>
            <td class="text-center">{{($absen->absen_masuk) ? $absen->absen_masuk->terlambat : '-'}}</td>
            <td>
                {!! ($absen->absen_masuk) ? $absen->absen_masuk->keterangan : '' !!}
                {{--($absen->izin) ? ucwords($absen->izin->keterangan).': '.ucwords($absen->izin->alasan) : ''--}}
            </td>
            <td class="text-center">{{($absen->absen_pulang) ? $absen->absen_pulang->created_at->format('H:i:s') : '-'}}</td>
            <td class="text-center">
                {{($absen->absen_pulang) ? $absen->absen_pulang->pulang_cepat : '-'}}
            </td>
            <td>
                {!! ($absen->absen_pulang) ? $absen->absen_pulang->keterangan : '' !!}
            </td>
            <td>
                @if($absen->izin)
                    @if($absen->izin->jenis == 'Sekolah')
                    {!! ($absen->izin) ? ucwords($absen->izin->keterangan).': ' : '' !!}
                    {{$absen->izin->jenis}} (Dinas/Tugas Luar)
                    @else
                    {!! ($absen->izin) ? ucwords($absen->izin->keterangan).': '.ucwords($absen->izin->alasan).'<br>' : '' !!}
                    {{$absen->izin->jenis}}
                    @endif
                @endif
            </td>
            <!--td class="text-center">
                @if(($absen->absen_pulang))
                    @if(!Illuminate\Support\Str::contains($absen->absen_pulang->pulang_cepat, '-'))
                        {{$absen->absen_pulang->pulang_cepat}}
                    @else
                    0
                    @endif
                @else
                -
                @endif
            </td-->
            {{--
            <td class="text-center">
                {{($absen->izin) ? ucwords($absen->izin->keterangan) : ''}}
            </td>
            <td>
                {{($absen->izin) ? $absen->izin->alasan : ''}}
                {!! ($absen->absen_masuk) ? $absen->absen_masuk->keterangan.'<br>' : '' !!}
                {!! ($absen->absen_pulang) ? $absen->absen_pulang->keterangan : '' !!}
            </td>
            --}}
        </tr>
        @endforeach
    </tbody>
</table>
@endsection