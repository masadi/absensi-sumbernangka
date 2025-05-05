<table>
    <tr>
        <td colspan="7">LAPORAN PELANGGARAN INDIVIDU PESERTA DIDIK</td>
    </tr>
    <tr>
        <td colspan="7">SMP AL-FALAH SUMBER GAYAM</td>
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
        <td colspan="2">Periode Waktu</td>
        <td colspan="5">: {{$startStr}} s/d {{$endStr}}</td>
    </tr>
    <tr>
        <td colspan="2">Dicetak pada</td>
        <td colspan="5">: {{now()->translatedFormat('j F Y')}}</td>
    </tr>
    <tr>
        <td colspan="2">Nama Peserta Didik</td>
        <td colspan="5">: {{$data->nama}}</td>
    </tr>
    <tr>
        <td colspan="2">Jenis Kelamin</td>
        <td colspan="5">: {{($data->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan'}}</td>
    </tr>
    <tr>
        <td colspan="2">NISN</td>
        <td colspan="5">: {{$data->nisn}}</td>
    </tr>
    <tr>
        <td colspan="2">Kelas</td>
        <td colspan="5">: {{$data->kelas->nama}}</td>
    </tr>
    
</table>
<table>
    <thead>
        <tr>
            <th>NO</th>
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
        @foreach ($data->pelanggaran as $pelanggaran)
        <tr>
            <td>{{$i++}}</td>
            <td>{{Helper::tglIndo($pelanggaran->tanggal)}}</td>
            <td>{{$pelanggaran->waktu}}</td>
            <td>{{$pelanggaran->petugas->name}}</td>
            <td>{{$pelanggaran->masalah}}</td>
            <td>{{$pelanggaran->tindak_lanjut}}</td>
            <td>{{$pelanggaran->catatan}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table>
    <tr>
        <td colspan="4">Mengetahui</td>
        <td colspan="3">Tangerang, {{now()->translatedFormat('j F Y')}}</td>
    </tr>
    <tr>
        <td colspan="4">Kepala Sekolah</td>
        <td colspan="3">Petugas BP/BK</td>
    </tr>
    @for ($i = 0; $i < 5; $i++)
    <tr>
        <td colspan="4"></td>
        <td colspan="3"></td>
    </tr>
    @endfor
    <tr>
        {{--
        <td colspan="4">Sakimin, S.Ag., M.Pd</td>
        <td colspan="3">......................................</td>
        --}}
        <td colspan="4">
            {{($data->kelas->sekolah->ptk_id) ? $data->kelas->sekolah->kasek->nama : '......................................'}}
        </td>
        <td colspan="3">{{($data->kelas->bp_id) ? $data->kelas->bp->nama : '......................................'}}</td>
    </tr>
</table>