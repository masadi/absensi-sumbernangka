<table>
    <thead>
    <tr>
        <td>Rekap All</td>
    </tr>
    <tr>
        <td>Satuan</td>
    </tr>
    <tr>
        <td>Periode Tanggal</td>
        <td>{{($start) ? $start.' - '.$end : ''}}</td>
        <td></td>
        <td></td>
        <td></td>
        <td>Tanggal Cetak {{date('d/m/Y')}}</td>
    </tr>
</table>
<table>
    <thead>
    <tr>
        <th>NO</th>
        <th>Nama</th>
        <th>Total Keterlambatan</th>
        <th>Total Pulang Cepat</th>
        <th>Total Menit (keterlambatan+ Pulang Cepat)</th>
        <th>Total Jam (keterlambatan+ Pulang Cepat)</th>
        <th>Total Hari (keterlambatan+ Pulang Cepat)</th>
        <th>Tidak Hadir</th>
        <th>Total Keseluruhan</th>
        <th>Keterangan</th>
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
        ?>
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$ptk->nama}}</td>
            <td>
                {{$ptk->absen_masuk->sum('terlambat')}}
            </td>
            <td>
                {{$ptk->absen_pulang->sum('pulang_cepat')}}
            </td>
            <td>
                {{$ptk->absen_masuk->sum('terlambat') + $ptk->absen_pulang->sum('pulang_cepat')}}
            </td>
            <td>
                {{jam($ptk->absen_masuk->sum('terlambat') + $ptk->absen_pulang->sum('pulang_cepat'))}}
            </td>
            <td>
                {{hari($ptk->absen_masuk->sum('terlambat') + $ptk->absen_pulang->sum('pulang_cepat'))}}
            </td>
            <td>
                {{jml_hari_aktif($from, $to) - $ptk->absen_masuk->count()}}
            </td>
            <td>
                {{hari($ptk->absen_masuk->sum('terlambat') + $ptk->absen_pulang->sum('pulang_cepat')) + (jml_hari_aktif($from, $to) - $ptk->absen_masuk->count())}}
            </td>
        </tr>            
        @endforeach
        @endif
        @if($collection_pd)
        @foreach ($collection_pd as $pd)
        <tr>
            <td>{{$loop->iteration + $no}}</td>
            <td>{{$pd->nama}}</td>
            <td>
                {{$pd->absen_masuk->sum('terlambat')}}
            </td>
            <td>
                {{$pd->absen_pulang->sum('pulang_cepat')}}
            </td>
            <td>
                {{$pd->absen_masuk->sum('terlambat') + $pd->absen_pulang->sum('pulang_cepat')}}
            </td>
            <td>
                {{jam($pd->absen_masuk->sum('terlambat') + $pd->absen_pulang->sum('pulang_cepat'))}}
            </td>
            <td>
                {{hari($pd->absen_masuk->sum('terlambat') + $pd->absen_pulang->sum('pulang_cepat'))}}
            </td>
            <td>
                {{jml_hari_aktif($from, $to) - $pd->absen_masuk->count()}}
            </td>
            <td>
                {{hari($pd->absen_masuk->sum('terlambat') + $pd->absen_pulang->sum('pulang_cepat')) + (jml_hari_aktif($from, $to) - $pd->absen_masuk->count())}}
            </td>
        </tr>            
        @endforeach
        @endif
    </tbody>
</table>