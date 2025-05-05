<table>
    <tr>
        <td colspan="2" align="center">TEMPLATE KUNCI JAWABAN</td>
    </tr>
    <tr>
        <td>Mata Ujian</td>
        <td>: {{$soal_ujian->jadwal_ujian->jadwal->nama}}</td>
    </tr>
    <tr>
        <td>Mata Pelajaran</td>
        <td>: {{$soal_ujian->jadwal_ujian->mata_pelajaran->nama}}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>: {{$soal_ujian->jadwal_ujian->rombongan_belajar->nama}}</td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th>NO_SOAL</th>
            <th>KUNCI_JAWABAN</th>
        </tr>
    </thead>
    <tbody>
        @for ($i = 0; $i < $soal_ujian->jumlah_soal; $i++)
        <tr>
            <td align="center">{{$i + 1}}</td>
            <td>{{($soal_ujian->jawaban_soal->count()) ? $soal_ujian->jawaban_soal[$i]?->jawaban : ''}}</td>
        </tr>
        @endfor
    </tbody>
</table>