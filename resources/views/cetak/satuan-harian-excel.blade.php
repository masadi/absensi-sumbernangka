<table>
    <tr>
        <td>Laporan Presensi Harian</td>
    </tr>
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
        <td>: {{$data->created_at->translatedFormat('d F Y')}}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th colspan="2">Kehadiran Hari Ini</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Datang</td>
            <td>{{($data->absen_masuk) ? $data->absen_masuk->created_at->format('H:i:s') : '-'}}</td>
        </tr>
        <tr>
            <td>Pulang</td>
            <td>{{($data->absen_pulang) ? $data->absen_pulang->created_at->format('H:i:s') : '-'}}</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>
                {!! ($data->absen_masuk) ? $data->absen_masuk->keterangan.'<br>' : '' !!}
                {!! ($data->absen_pulang) ? $data->absen_pulang->keterangan : '' !!}
            </td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th colspan="2">Kedisiplinan</th>
        </tr>
        <tr>
            <th colspan="2">Periode {{$start}} s/d {{$end}}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Keterlambatan</td>
            <td>
                @if($data->ptk)
                {{$data->ptk->absen_masuk->sum('terlambat')}}
                @else
                {{$data->pd->absen_masuk->sum('terlambat')}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Total Pulang Cepat</td>
            <td>
                @if($data->ptk)
                {{$data->ptk->absen_pulang->sum('pulang_cepat')}}
                @else
                {{$data->pd->absen_pulang->sum('pulang_cepat')}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Total Menit (keterlambatan + Pulang Cepat)</td>
            <td>
                @if($data->ptk)
                {{$data->ptk->absen_masuk->sum('terlambat') + $data->ptk->absen_pulang->sum('pulang_cepat')}}
                @else
                {{$data->pd->absen_masuk->sum('terlambat') + $data->pd->absen_pulang->sum('pulang_cepat')}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Total Jam (keterlambatan + Pulang Cepat)</td>
            <td>
                @if($data->ptk)
                {{jam($data->ptk->absen_masuk->sum('terlambat') + $data->ptk->absen_pulang->sum('pulang_cepat'))}}
                @else
                {{jam($data->pd->absen_masuk->sum('terlambat') + $data->pd->absen_pulang->sum('pulang_cepat'))}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Total Hari (keterlambatan + Pulang Cepat)</td>
            <td>
                @if($data->ptk)
                {{hari($data->ptk->absen_masuk->sum('terlambat') + $data->ptk->absen_pulang->sum('pulang_cepat'))}}
                @else
                {{hari($data->pd->absen_masuk->sum('terlambat') + $data->pd->absen_pulang->sum('pulang_cepat'))}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Tidak Hadir</td>
            <td class="text-center">
                @if($data->ptk)
                {{jml_hari_aktif($endStr) - $data->ptk->absen_masuk->count()}}
                @else
                {{jml_hari_aktif($endStr) - $data->pd->absen_masuk->count()}}
                @endif
            </td>
        </tr>
        <tr>
            <td>Total Keseluruhan</td>
            <td class="text-center">
                @if($data->ptk)
                {{hari($data->ptk->absen_masuk->sum('terlambat') + $data->ptk->absen_pulang->sum('pulang_cepat')) + (jml_hari_aktif($endStr) - $data->ptk->absen_masuk->count())}}
                @else
                {{hari($data->pd->absen_masuk->sum('terlambat') + $data->pd->absen_pulang->sum('pulang_cepat')) + (jml_hari_aktif($endStr) - $data->ptk->absen_masuk->count())}}
                @endif
            </td>
        </tr>
    </tbody>
</table>