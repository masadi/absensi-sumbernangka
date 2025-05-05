@extends('layouts.cetak')
@section('content')
<h2 class="text-center">Laporan Presensi Individu</h2>
<table class="table">
    <tr>
        <td style="width: 50%; vertical-align:top;">
            <table class="table">
                <tr>
                    <td>Nama</td>
                    <td>: {{$data->nama}}</td>
                </tr>
                <tr>
                    <td>Satuan</td>
                    <td>: {{$data->sekolah->nama}}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{$start}} s/d {{$end}}</td>
                </tr>
            </table>
        </td>
        <td style="width: 50%; vertical-align:top;">
            <table class="table">
                <tr>
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
                    <td>Total Tanpa Keterangan</td>
                    <td>: 
                        @if($ptk)
                        {{tidak_hadir_ptk($data->ptk_id, $startStr, $endStr)}}
                        @else
                        {{tidak_hadir_pd($data->peserta_didik_id, $startStr, $endStr)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Total Sakit</td>
                    <td>: 
                        @if($ptk)
                        {{izin_ptk($data, 'sakit', $startStr, $endStr)}}
                        @else
                        {{izin_pd($data, 'sakit', $startStr, $endStr)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Total Izin</td>
                    <td>:
                        @if($ptk)
                        {{izin_ptk($data, 'izin', $startStr, $endStr)}}
                        @else
                        {{izin_pd($data, 'izin', $startStr, $endStr)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Cuti</td>
                    <td>: 
                        @if($ptk)
                        {{izin_ptk($data, 'cuti', $startStr, $endStr)}}
                        @else
                        {{izin_pd($data, 'cuti', $startStr, $endStr)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Total Tidak Hadir</td>
                    <td>:
                        @if($ptk)
                        {{tidak_hadir_ptk($data->ptk_id, $startStr, $endStr)}}
                        @else
                        {{tidak_hadir_pd($data->peserta_didik_id, $startStr, $endStr)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Total Hadir</td>
                    <td>: {{$data->absen()->whereHas('absen_masuk', function($query) use ($startStr, $endStr){
                        $query->whereDate('created_at', '>=', $startStr);
                        $query->whereDate('created_at', '<=', $endStr);
                    })->count()}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;vertical-align: middle">NO</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Tanggal</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Jam Datang</th>
            <th class="text-center" style="width: 11%;vertical-align: middle">Keterlambatan (menit)</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Jam Pulang</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Pulang Cepat  (menit)</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Keterangan</th>
            <th class="text-center" style="width: 44%;vertical-align: middle">Alasan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->absen as $absen)
        <?php
        $izin = $data->izin_harian()->whereDate('izin.created_at', $absen->created_at->format('Y-m-d'))->first();
        ?>
        <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            <td class="text-center">{{$absen->created_at->format('d/m/Y')}}</td>
            <td class="text-center">{{($absen->absen_masuk) ? $absen->absen_masuk->created_at->format('H:i:s') : '-'}}</td>
            <td class="text-center">{{($absen->absen_masuk) ? $absen->absen_masuk->terlambat : '-'}}</td>
            <td class="text-center">{{($absen->absen_pulang) ? $absen->absen_pulang->created_at->format('H:i:s') : '-'}}</td>
            <td class="text-center">{{($absen->absen_pulang) ? $absen->absen_pulang->pulang_cepat : '-'}}</td>
            <td class="text-center">
                {{($izin) ? ucwords($izin->keterangan) : ''}}
            </td>
            <td>
                {{($izin) ? $izin->alasan : ''}}
                {!! ($absen->absen_masuk) ? $absen->absen_masuk->keterangan.'<br>' : '' !!}
                {!! ($absen->absen_pulang) ? $absen->absen_pulang->keterangan : '' !!}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection