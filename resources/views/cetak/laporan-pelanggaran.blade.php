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
            <th>Rombel</th>
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
        @foreach ($data as $d)
        <tr>
            <td>{{$loop->iteration}}</td>
            <td>{{$d->anggota_rombel->rombongan_belajar->nama}}</td>
            <td>{{$d->pd->nama}}</td>
            <td>{{$d->pd->jenis_kelamin}}</td>
            <td>{{$d->pd->nisn}}</td>
            <td>{{tglIndo($d->tanggal)}}</td>
            <td>{{$d->waktu}}</td>
            <td>{{$d->petugas->name}}</td>
            <td>{{$d->masalah}}</td>
            <td>{{$d->tindak_lanjut}}</td>
            <td>{{$d->catatan}}</td>
        </tr>
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
        <td colspan="8">Sakimin, S.Ag., M.Pd</td>
        <td colspan="3">......................................</td>
    </tr>
</table>