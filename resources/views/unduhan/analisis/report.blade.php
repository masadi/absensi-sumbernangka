<table border="1">
  <thead>
    <tr>
        <th align="center" colspan="11">DAFTAR NILAI UJIAN</th>
    </tr>
    <tr>
      <th colspan="11"></th>
    </tr>
    <tr>
      <th colspan="2">Sekolah</th>
      <th colspan="9">: {{$data->jadwal_ujian->rombongan_belajar->sekolah->nama}}</th>
    </tr>
    <tr>
      <th colspan="2">Nama Tes</th>
      <th colspan="9">: {{$data->jadwal_ujian->jadwal->nama}}</th>
    </tr>
    <tr>
      <th colspan="2">Mata Pelajaran</th>
      <th colspan="9">: {{$data->jadwal_ujian->mata_pelajaran->nama}}</th>
    </tr>
    <tr>
      <th colspan="2">Kelas/Program</th>
      <th colspan="9">: {{$data->jadwal_ujian->rombongan_belajar->nama}}</th>
    </tr>
    <tr>
      <th colspan="2">Tanggal Tes</th>
      <th colspan="9">: {{$data->jadwal_ujian->tanggal_indo}}</th>
    </tr>
    <tr>
      <th colspan="2">Materi Pokok</th>
      <th colspan="9">: </th>
    </tr>
    <tr>
      <th colspan="11"></th>
    </tr>
    <!--tr>
      <th colspan="2" align="center">KELOMPOK ATAS</th>
    </tr-->
    <tr>
      <th align="center">No.</th>
      <th align="center" rowspan="2" style="vertical-align: middle">NAMA SISWA</th>
      <th align="center" rowspan="2" style="vertical-align: middle" colspan="2">URAIAN JAWABAN SISWA DAN HASIL PEMERIKSAAN</th>
      <th align="center" colspan="2">JUMLAH</th>
      <th align="center" rowspan="2" style="vertical-align: middle">SKOR PG</th>
      <th align="center" rowspan="2" style="vertical-align: middle">SKOR URAIAN</th>
      <th align="center" rowspan="2" style="vertical-align: middle">TOTAL SKOR</th>
      <th align="center" rowspan="2" style="vertical-align: middle">NILAI</th>
      <th align="center" rowspan="2" style="vertical-align: middle">CATATAN</th>
    </tr>
    <tr>
      <th align="center">Urut</th>
      <th align="center">BENAR</th>
      <th align="center">SALAH</th> 
    </tr>
  </thead>
  <tbody>
    <?php
    $skor_benar = $data->jadwal_ujian->soal_ujian->skor_benar ?? 2;
    $skor_salah = $data->jadwal_ujian->soal_ujian->skor_salah ?? 0;
    $skala_nilai = $data->jadwal_ujian->soal_ujian->skala_nilai ?? 100;
    $jumlah_soal = $data->jawaban_soal->count();
    $batas_lulus = $data->jadwal_ujian->soal_ujian->batas_lulus ?? 60;
    $arr_skor = [];
    $arr_nilai = [];
    $peserta = 0;
    $lulus = 0;
    ?>
    @foreach($data->items as $item)
      <tr>
          <td align="center">{{ $loop->iteration }}</td>
          <td>{{ $item->nama }}</td>
          <td colspan="2">
          <?php
          $peserta++;
          $benar = 0;
          $salah = 0;
          ?>
          @foreach ($data->jawaban_soal as $index => $soal)
            @isset($item->anggota_rombel->jawaban_pd[$index])
              {{($item->anggota_rombel->jawaban_pd[$index]->jawaban) ? $item->anggota_rombel->jawaban_pd[$index]->jawaban : '-'}}
              <?php
              if ($data->jawaban_soal[$index]->jawaban == $item->anggota_rombel->jawaban_pd[$index]->jawaban){
                $benar++;
              } else {
                $salah++;
              }
              ?>
            @endisset
          @endforeach
          <?php
          $skor = $benar * $skor_benar - $salah * $skor_salah;
          $nilai = ($skor) ? ($skor / $skala_nilai) * ($jumlah_soal * $skor_benar): 0;
          $arr_skor[] = $skor;
          $arr_nilai[] = $nilai;
          if($nilai >= $batas_lulus){
            $lulus++;
          }
          ?>
          </td>
          <td align="center">{{$benar}}</td>
          <td align="center">{{$salah}}</td>
          <td align="center">{{$skor}}</td>
          <td align="center"></td>
          <td align="center">{{$nilai}}</td>
          <td align="center">{{$nilai}}</td>
          <td align="center">{{($nilai >= $batas_lulus) ? 'Lulus' : 'Tidak Lulus'}}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td rowspan="7" align="center" style="vertical-align: middle;">REKAPITULASI</td>
      <td align="center" colspan="10"></td>
    </tr>
    <tr>
      <td>- Jumlah peserta test</td>
      <td>: {{$peserta}} Orang</td>
      <td align="right" colspan="3">JUMLAH :</td>
      <td align="center">{{array_sum($arr_nilai)}}</td>
      <td align="center"></td>
      <td align="center"></td>
      <td align="center">{{array_sum($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td>- Jumlah yang lulus</td>
      <td>: {{$lulus}} Orang</td>
      <td align="right" colspan="3">TERKECIL  :</td>
      <td align="center">{{min($arr_nilai)}}</td>
      <td align="center"></td>
      <td align="center"></td>
      <td align="center">{{min($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td>- Jumlah yang tidak lulus</td>
      <td>: {{$peserta - $lulus}} Orang</td>
      <td align="right" colspan="3">TERBESAR  :</td>
      <td align="center">{{max($arr_nilai)}}</td>
      <td align="center"></td>
      <td align="center"></td>
      <td align="center">{{max($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td>- Jumlah yang di atas rata-rata</td>
      <td>: {{$lulus}} Orang</td>
      <td align="right" colspan="3">RATA-RATA  :</td>
      <td align="center">{{array_sum($arr_nilai)/count($arr_nilai)}}</td>
      <td align="center"></td>
      <td align="center"></td>
      <td align="center">{{array_sum($arr_nilai)/count($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td>- Jumlah yang di bawah rata-rata</td>
      <td>: {{$peserta - $lulus}} Orang</td>
      <td align="right" colspan="3">SIMPANGAN BAKU :</td>
      <td align="center">{{sd($arr_nilai)}}</td>
      <td align="center"></td>
      <td align="center"></td>
      <td align="center">{{sd($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td align="center" colspan="10"></td>
    </tr>
  </tfoot>
</table>