@extends('layouts.cetak')
@section('content')
<h2 class="text-center">Rekapitulasi Presensi</h2>
<h3 class="text-center">Periode {{$start}} s/d {{$end}}</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="vertical-align: middle;">NO</th>
            <th class="text-center" style="vertical-align: middle;">Unit</th>
            <th class="text-center" style="vertical-align: middle;">Nama</th>
            <th class="text-center" style="vertical-align: middle;">Total Keterlambatan</th>
            <th class="text-center" style="vertical-align: middle;">Total Pulang Cepat</th>
            <th class="text-center" style="vertical-align: middle;">Total Menit (keterlambatan+ Pulang Cepat)</th>
            <th class="text-center" style="vertical-align: middle;">Total Jam (keterlambatan+ Pulang Cepat)</th>
            <th class="text-center" style="vertical-align: middle;">Total Hari (keterlambatan+ Pulang Cepat)</th>
            <th class="text-center" style="vertical-align: middle;">Tidak Hadir</th>
            <th class="text-center" style="vertical-align: middle;">Total Keseluruhan</th>
            <th class="text-center" style="vertical-align: middle;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 0;
        ?>
        @if($collection_ptk)
        @foreach ($collection_ptk as $ptk)
        <?php
        $no++;
        $terlambat = $ptk->absen_masuk->sum('terlambat');
        $pulang_cepat = $ptk->absen_pulang->sum('pulang_cepat');
        ?>
        <tr>
            <td class="text-center" style="vertical-align: middle;">{{$loop->iteration}}</td>
            <td>{{$ptk->sekolah->nama}}</td>
            <td>{{$ptk->nama}}</td>
            <td class="text-center" style="vertical-align: middle;">
                {{$terlambat}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{$pulang_cepat}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{$terlambat + $pulang_cepat}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{jam($terlambat + $pulang_cepat)}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{hari($terlambat + $pulang_cepat)}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{tidak_hadir_ptk($ptk->ptk_id, $from, $to)}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{hari($terlambat + $pulang_cepat) + (tidak_hadir_ptk($ptk->ptk_id, $from, $to))}}
            </td>
            <td></td>
        </tr>            
        @endforeach
        @endif
        @if($collection_pd)
        @foreach ($collection_pd as $pd)
        <?php
        $terlambat = $pd->absen_masuk->sum('terlambat');
        $pulang_cepat = $pd->absen_pulang->sum('pulang_cepat');
        ?>
        <tr>
            <td class="text-center" style="vertical-align: middle;">{{$loop->iteration + $no}}</td>
            <td>{{$pd->sekolah->nama}}</td>
            <td>{{$pd->nama}}</td>
            <td class="text-center" style="vertical-align: middle;">
                {{$terlambat}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{$pulang_cepat}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{$terlambat + $pulang_cepat}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{jam($terlambat + $pulang_cepat)}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{hari($terlambat + $pulang_cepat)}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{tidak_hadir_pd($pd->peserta_didik_id, $from, $to)}}
            </td>
            <td class="text-center" style="vertical-align: middle;">
                {{hari($terlambat + $pulang_cepat) + (tidak_hadir_pd($pd->peserta_didik_id, $from, $to))}}
            </td>
            <td></td>
        </tr>            
        @endforeach
        @endif
    </tbody>
</table>
@endsection