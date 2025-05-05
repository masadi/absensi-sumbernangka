<table>
    <tr>
        <td colspan="11">LAPORAN PELANGGARAN INDIVIDU PESERTA DIDIK</td>
    </tr>
    <tr>
        <td colspan="11">SMP AL-FALAH SUMBER GAYAM</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2">Kelas</td>
        <td colspan="9">: {{$nama_rombel}}</td>
    </tr>
    <tr>
        <td colspan="2">Periode Waktu</td>
        <td colspan="9">: {{$startStr}} s/d {{$endStr}}</td>
    </tr>
    <tr>
        <td colspan="2">Dicetak pada</td>
        <td colspan="9">: {{now()->translatedFormat('j F Y')}}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>Nama Peserta Didik</th>
            <th>JK</th>
            <th>NISN</th>
            <th>Tanggal Kejadian</th>
            <th>Waktu Kejadian</th>
            <th>Petugas</th>
            <th>Masalah/Pelanggaran</th>
            <th>Tindak Lanjut</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        @foreach ($data as $pd)
        @foreach ($pd->pelanggaran as $pelanggaran)
        <tr>
            <td>{{$i++}}</td>
            <td>{{$pd->nama}}</td>
            <td>{{$pd->jenis_kelamin}}</td>
            <td>{{$pd->nisn}}</td>
            <td>{{Helper::tglIndo($pelanggaran->tanggal)}}</td>
            <td>{{$pelanggaran->waktu}}</td>
            <td>{{$pelanggaran->petugas->name}}</td>
            <td>{{$pelanggaran->masalah}}</td>
            <td>{{$pelanggaran->tindak_lanjut}}</td>
            <td>{{$pelanggaran->catatan}}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td colspan="8">Mengetahui</td>
        <td colspan="3">Tangerang, {{now()->translatedFormat('j F Y')}}</td>
    </tr>
    <tr>
        <td colspan="8">Kepala Sekolah</td>
        <td colspan="3">Petugas BP/BK</td>
    </tr>
    @for ($i = 0; $i < 5; $i++)
    <tr>
        <td colspan="8"></td>
        <td colspan="3"></td>
    </tr>
    @endfor
    <tr>
        <td colspan="8">
            {{($rombel->sekolah->ptk_id) ? $rombel->sekolah->kasek->nama : '......................................'}}
        </td>
        <td colspan="3">{{($rombel->bp_id) ? $rombel->bp->nama : '......................................'}}</td>
    </tr>
</table>