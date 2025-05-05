<table>
    <tr>
        <td>Laporan Presensi Individu</td>
    </tr>
    <tr>
        <td>Nama</td>
        <td>: {{$data->nama}}</td>
        <td></td>
        <td>Total Hari Aktif</td>
        <td>: {{jml_hari_aktif($startStr, $endStr)}}</td>
    </tr>
    <tr>
        <td>Satuan</td>
        <td>: {{$data->sekolah->nama}}</td>
        <td></td>
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
        <td>Tanggal</td>
        <td>: {{$start}} s/d {{$end}}</td>
        <td></td>
        <td>Total Sakit</td>
        <td>: 
            @if($ptk)
            {{izin_ptk($data->ptk_id, 'sakit')}}
            @else
            {{izin_pd($data->peserta_didik_id, 'sakit')}}
            @endif
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Total Izin</td>
        <td>:
            @if($ptk)
            {{izin_ptk($data->ptk_id, 'izin')}}
            @else
            {{izin_pd($data->peserta_didik_id, 'izin')}}
            @endif
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Total Cuti</td>
        <td>:
            @if($ptk)
            {{izin_ptk($data->ptk_id, 'cuti')}}
            @else
            {{izin_pd($data->peserta_didik_id, 'cuti')}}
            @endif
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Total Tidak hadir</td>
        <td>:
            @if($ptk)
            {{tidak_hadir_ptk($data->ptk_id, $startStr, $endStr) + izin_ptk($data->ptk_id, 'izin') + izin_ptk($data->ptk_id, 'sakit')}}
            @else
            {{tidak_hadir_pd($data->peserta_didik_id, $startStr, $endStr) + izin_pd($data->peserta_didik_id, 'izin') + izin_pd($data->peserta_didik_id, 'sakit')}}
            @endif
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Total Hadir</td>
        <td>: {{count($data->absen)}}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>Tanggal</th>
            <th>Jam Datang</th>
            <th>Keterlambatan (dalam menit)</th>
            <th>Jam Pulang</th>
            <th>Pulang Cepat  (dalam menit)</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data->absen as $absen)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$absen->created_at->format('d/m/Y')}}</td>
            <td>{{($absen->absen_masuk) ? $absen->absen_masuk->created_at->format('H:i:s') : '-'}}</td>
            <td>{{($absen->absen_masuk) ? $absen->absen_masuk->terlambat : '-'}}</td>
            <td>{{($absen->absen_pulang) ? $absen->absen_pulang->created_at->format('H:i:s') : '-'}}</td>
            <td>{{($absen->absen_pulang) ? $absen->absen_pulang->pulang_cepat : '-'}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>