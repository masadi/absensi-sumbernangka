<?php
$jumlah_soal = $data->jawaban_soal->count();
$arr_skor = [];
$arr_nilai = [];
?>
<table border="1">
  <thead>
    <tr>
      <th align="center" colspan="9">DATA PEMERIKSAAN JAWABAN SISWA</th>
    </tr>
    <tr>
      <th align="center" colspan="9">TIPE SOAL : PILIHAN GANDA BIASA (MULTIPLE CHOICE)</th>
    </tr>
    <tr>
      <th align="center" colspan="9"></th>
    </tr>
    <tr>
      <th align="center" rowspan="8" style="vertical-align: middle;">DATA UMUM</th>
    </tr>
    <tr>
      <th> NAMA SEKOLAH</th>
      <th colspan="2">: {{$data->jadwal_ujian->rombongan_belajar->sekolah->nama}}</th>
      <th>SEMESTER</th>
      <th></th>
      <th align="center">:</th>
      <th>{{substr($data->jadwal_ujian->rombongan_belajar->semester->nama, 10, 10)}}</th>
      <th></th>
    </tr>
    <tr>
      <th> MATA PELAJARAN</th>
      <th colspan="2">: {{$data->jadwal_ujian->mata_pelajaran->nama}}</th>
      <th>TAHUN PELAJARAN</th>
      <th></th>
      <th align="center">:</th>
      <th>{{substr($data->jadwal_ujian->rombongan_belajar->semester->nama, 0, 9)}}</th>
      <th></th>
    </tr>
    <tr>
      <th> KELAS/PROGRAM</th>
      <th colspan="2">: {{$data->jadwal_ujian->rombongan_belajar->nama}}</th>
      <th>TANGGAL TES</th>
      <th></th>
      <th align="center">:</th>
      <th>{{$data->jadwal_ujian->jadwal->tanggal_indo}}</th>
      <th></th>
    </tr>
    <tr>
      <th> NAMA TES</th>
      <th colspan="2">: {{$data->jadwal_ujian->jadwal->nama}}</th>
      <th>TANGGAL DIPERIKSA</th>
      <th></th>
      <th align="center">:</th>
      <th>{{$data->jadwal_ujian->jadwal->tanggal_indo}}</th>
      <th></th>
    </tr>
    <tr>
      <th> MATERI POKOK</th>
      <th colspan="2">: </th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
      <th></th>
    </tr>
    <tr>
      <th>NAMA PENGAJAR</th>
      <th colspan="2">: </th>
      <th> NOMOR INDUK (NIP)</th>
      <th></th>
      <th align="center">:</th>
      <th></th>
      <th></th>
    </tr>
    <tr>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="9"></td>
    </tr>
    <tr>
      <td rowspan="2" colspan="3" align="center" style="vertical-align: middle;">DATA KHUSUS<br>SOAL PILIHAN GANDA</td>
      <td align="center" style="vertical-align: middle;">RINCIAN KUNCI JAWABAN</td>
      <td align="center">JUMLAH SOAL</td>
      <td align="center">JUMLAH OPTION</td>
      <td align="center">SKOR BENAR</td>
      <td align="center">SKOR SALAH</td>
      <td align="center">SKALA NILAI</td>
    </tr>
    <tr>
      <td align="center">
        @foreach ($data->jawaban_soal as $kunci)
            {{$kunci->jawaban}}
        @endforeach
      </td>
      <td align="center">{{$jumlah_soal}}</td>
      <td align="center">{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}</td>
      <td align="center">{{$data->jadwal_ujian->soal_ujian->skor_benar}}</td>
      <td align="center">{{$data->jadwal_ujian->soal_ujian->skor_salah}}</td>
      <td align="center">{{$data->jadwal_ujian->soal_ujian->skala_nilai}}</td>
    </tr>
    <tr>
      <td>Petunjuk Pengisian</td>
      @for ($i = 0; $i < 8; $i++)
      <td></td>
      @endfor
    </tr>
    <tr>
      <td align="center">1.</td>
      <td>biru</td>
      @for ($i = 0; $i < 7; $i++)
      <td></td>
      @endfor
    </tr>
    <tr>
      <td align="center">2.</td>
      <td>Lebar tiap kolom dan tinggi tiap baris boleh diubah. Namun jangan mengubah format yang ada !</td>
      @for ($i = 0; $i < 7; $i++)
      <td></td>
      @endfor
    </tr>
    <tr>
      <td></td>
    </tr>
    <tr>
      <td align="center" rowspan="2">No. Urut</td>
      <td align="center" rowspan="2" style="vertical-align: middle;">Nama</td>
      <td align="center" rowspan="2" style="vertical-align: middle;">JK</td>
      <td align="center">RINCIAN JAWABAN SISWA</td>
      <td align="center" colspan="2">JUMLAH</td>
      <td align="center" rowspan="2" style="vertical-align: middle;">SKOR</td>
      <td align="center" rowspan="2" style="vertical-align: middle;">NILAI</td>
      <td align="center" rowspan="2" style="vertical-align: middle;">KET.</td>
    </tr>
    <tr>
      <td align="center">(Gunakan huruf kapital, contoh : ABDCEADE ...)</td>
      <td align="center">BENAR</td>
      <td align="center">SALAH</td>
    </tr>
    @foreach($data->items as $item)
    <?php
    $skor_benar = $data->jadwal_ujian->soal_ujian->skor_benar ?? 2;
    $skor_salah = $data->jadwal_ujian->soal_ujian->skor_salah ?? 0;
    $skala_nilai = $data->jadwal_ujian->soal_ujian->skala_nilai ?? 100;
    $benar = 0;
    $salah = 0;
    ?>
    <tr>
      <td align="center">{{$loop->iteration}}</td>
      <td>{{$item->nama}}</td>
      <td align="center">{{$item->nisn}}</td>
      <td align="center">
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
        ?>
        <!--=IF(C7="";"";BC7*$B$64-BD7*$B$66)-->
        <!--=IF(BE7="";"";(BE7/$B$70)*$B$68)-->
      </td>
      <td align="center">{{$benar}}</td>
      <td align="center">{{$salah}}</td>
      <td align="center">{{$skor}}</td>
      <td align="center">
        {{$nilai}}
      </td>
      <td></td>
    </tr>
    @endforeach
    <tr>
      <td align="right" colspan="6">JUMLAH :</td>
      <td align="center">{{array_sum($arr_skor)}}</td>
      <td align="center">{{array_sum($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td align="right" colspan="6">TERKECIL :</td>
      <td align="center">{{min($arr_skor)}}</td>
      <td align="center">{{min($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td align="right" colspan="6">TERBESAR :</td>
      <td align="center">{{max($arr_skor)}}</td>
      <td align="center">{{max($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td align="right" colspan="6">RATA-RATA :</td>
      <td align="center">{{array_sum($arr_skor)/count($arr_skor)}}</td>
      <td align="center">{{array_sum($arr_nilai)/count($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
    <tr>
      <td align="right" colspan="6">SIMPANGAN BAKU :</td>
      <td align="center">{{sd($arr_nilai)}}</td>
      <td align="center">{{sd($arr_nilai)}}</td>
      <td align="center"></td>
    </tr>
  </tbody>
</table>