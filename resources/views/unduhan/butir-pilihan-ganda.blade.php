<table border="1">
  <thead>
    <tr>
        <th align="center" colspan="15">LEMBAR PERHITUNGAN DAYA PEMBEDA DAN TINGKAT KESUKARAN SOAL</th>
    </tr>
    <tr>
        <th align="center" colspan="15">{{$data->jadwal_ujian->jadwal->nama}} TAHUN PELAJARAN {{substr($data->jadwal_ujian->rombongan_belajar->semester_id, 0, 4)}}/{{substr($data->jadwal_ujian->rombongan_belajar->semester_id, 0, 4) + 1}}</th>
    </tr>
    <tr>
      <th colspan="15"></th>
    </tr>
    <tr>
      <th colspan="3">Sekolah</th>
      <th colspan="12">: {{$data->jadwal_ujian->rombongan_belajar->sekolah->nama}}</th>
    </tr>
    <tr>
      <th colspan="3">Mata Pelajaran</th>
      <th colspan="12">: {{$data->jadwal_ujian->mata_pelajaran->nama}}</th>
    </tr>
    <tr>
      <th colspan="3">Kelas</th>
      <th colspan="12">: {{$data->jadwal_ujian->rombongan_belajar->nama}}</th>
    </tr>
    <tr>
      <th colspan="15"></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td rowspan="3" align="center" style="vertical-align: middle">No.</td>
      <td rowspan="3" align="center" style="vertical-align: middle">BA</td>
      <td rowspan="3" align="center" style="vertical-align: middle">BB</td>
      <td rowspan="3" align="center" style="vertical-align: middle">BA+BB</td>
      <td rowspan="3" align="center" style="vertical-align: middle">BA-BB</td>
      <td rowspan="3" align="center" style="vertical-align: middle">n</td>
      <td align="center" colspan="4">DAYA PEMBEDA</td>
      <td align="center" colspan="4">TINGKAT KESUKARAN</td>
      <td align="center" rowspan="3" style="vertical-align: middle">Kesimpulan</td>
    </tr>
    <tr>
      <td align="center" rowspan="2" style="vertical-align: middle">Index DP</td>
      <td align="center" colspan="3">Kualifikasi</td>
      <td align="center" rowspan="2" style="vertical-align: middle">Index TK</td>
      <td align="center" colspan="3">Kualifikasi</td>
    </tr>
    <tr>
      <td align="center">Baik</td>
      <td align="center">Kurang</td>
      <td align="center">Jelek</td>
      <td align="center">Skr</td>
      <td align="center">Sdg</td>
      <td align="center">Mdh</td>
    </tr>
    @foreach($data->jawaban_soal as $soal)
    <?php
    $start = $loop->iteration;
    $angka = 10;
    ?>
    <tr>
      <td align="center">{{$start}}</td>
      <td>1</td>
      <td>2</td>
      <td>=B{{$angka + $start}}+C{{$angka + $start}}</td>
      <td>=B{{$angka + $start}}-C{{$angka + $start}}</td>
      <td>10</td>
      <td>=1</td>
      <td>=2</td>
      <td>=3</td>
      <td>=4</td>
      <td>=5</td>
      <td>=6</td>
      <td>=7</td>
      <td>=8</td>
      <td>=9</td>
      <!--td>=IF(B{{$angka + $start}}>-1;((2*(B{{$angka + $start}}-C{{$angka + $start}}))/F{{$angka + $start}});"")</td>
      <td>=IF(G{{$angka + $start}}>=0,4;"√";"")</td>
      <td>=IF(G11>0,39;"";IF(G11&lt;0,2;"";"√"))</td>
      <td>=IF(G{{$angka + $start}}&lt;=0,19;"√";"")</td>
      <td>=IF(B{{$angka + $start}}>-1;((D{{$angka + $start}})/F{{$angka + $start}});"")</td>
      <td>=IF(K{{$angka + $start}}&lt;=0,3;"√";"")</td>
      <td>=IF(K{{$angka + $start}}&lt;=0,3;"";IF(K{{$angka + $start}}>=0,71;"";"√"))</td>
      <td>=IF(K{{$angka + $start}}>=0,71;"√";"")</td>
      <td>=IF(H{{$angka + $start}}="√";"diterima";IF(I{{$angka + $start}}="√";"diperbaiki";IF(J{{$angka + $start}}="√";"dibuang";"")))</td-->
    </tr>
    @endforeach
    <tr>
      <td colspan="7" align="center">Mengetahui</td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="5" align="center">Tangerang, {{$data->jadwal_ujian->jadwal->tanggal_indo}}</td>
    </tr>
    <tr>
      <td colspan="7" align="center">Kepala {{$data->jadwal_ujian->rombongan_belajar->sekolah->nama}}</td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="5" align="center">Guru Mata Pelajaran</td>
    </tr>
    <tr>
      <td colspan="15"></td>
    </tr>
    <tr>
      <td colspan="15"></td>
    </tr>
    <tr>
      <td colspan="7" align="center">{{($data->jadwal_ujian->rombongan_belajar->sekolah->ptk_id) ? $data->jadwal_ujian->rombongan_belajar->sekolah->kasek->nama : '-'}}</td>
      <td></td>
      <td></td>
      <td></td>
      <td colspan="5" align="center">..............................</td>
    </tr>
  </tbody>
</table>