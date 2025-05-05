<?php
$jumlah_soal = $data->jadwal_ujian->soal_ujian->jumlah_soal;
$skor_benar = $data->jadwal_ujian->soal_ujian->skor_benar ?? 2;
$skor_salah = $data->jadwal_ujian->soal_ujian->skor_salah ?? 0;
$skala_nilai = $data->jadwal_ujian->soal_ujian->skala_nilai ?? 100;
$total_skor = $jumlah_soal * $skor_benar;
$arr_skor = [];
$arr_nilai = [];
?>
<table border="1">
  <tr>
    <td align="center" style="vertical-align: middle;" colspan="3">RINCIAN KUNCI JAWABAN</td>
    <td align="center">JUMLAH SOAL</td>
    <td colspan="3" align="center">JUMLAH PESERTA</td>
  </tr>
  <tr>
    <td align="center" colspan="3">
      @foreach ($data->jawaban_soal as $kunci)
        {{$kunci->jawaban}}
      @endforeach
    </td>
    <td align="center">{{$jumlah_soal}}</td>
    <td align="center" colspan="3">{{$data->jadwal_ujian->soal_ujian->peserta}}</td>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <td rowspan="2" style="vertical-align: middle" align="center">No. Urut</td>
    <td rowspan="2" style="vertical-align: middle" align="center">Nama Siswa</td>
    <td align="center">RINCIAN JAWABAN SISWA</td>
    <td rowspan="2" style="vertical-align: middle" align="center">STATUS</td>
    <td colspan="{{$jumlah_soal}}" align="center">NOMOR SOAL</td>
    <td colspan="2" align="center">JUMLAH</td>
    <td rowspan="2" style="vertical-align: middle" align="center">SKOR</td>
    <td rowspan="2" style="vertical-align: middle" align="center">NILAI</td>
  </tr>
  <tr>
    <td align="center">(Gunakan huruf kapital, contoh : ABDCEADE ...)</td>
    @for ($i = 1; $i < ($jumlah_soal + 1); $i++)
    <td align="center">{{$i}}</td>
    @endfor
    <td align="center">BENAR</td>
    <td align="center">SALAH</td>
  </tr>
  @foreach($data->items as $item)
  <?php
  $benar = 0;
  $salah = 0;
  ?>
  <tr>
    <td align="center">{{$loop->iteration}}</td>
    <td>{{$item->nama}}</td>
    <td>
      <?php $length = 0; ?>
      @foreach ($data->jawaban_soal as $index => $soal)
        @isset($item->anggota_rombel->jawaban_pd[$index])
          {{($item->anggota_rombel->jawaban_pd[$index]->jawaban) ? $item->anggota_rombel->jawaban_pd[$index]->jawaban : '-'}}
          <?php
          $length++;
          ?>
        @endisset
      @endforeach
    </td>
    <td align="center">
      @if ($length)
        @if ($length == $jumlah_soal)
            OK !
        @endif
        @if ($length < $jumlah_soal)
            Kurang
        @endif
        @if ($length > $jumlah_soal)
            Lebih
        @endif
      @endif
    </td>
    @foreach($data->jawaban_soal as $index => $soal)
    <td align="center">
      @isset($item->anggota_rombel->jawaban_pd[$index])
      <?php
      if ($item->anggota_rombel->jawaban_pd[$index]->jawaban == $soal->jawaban){
        $benar++;
      } else {
        $salah++;
      }
      ?>
      {{($item->anggota_rombel->jawaban_pd[$index]->jawaban == $soal->jawaban) ? 1 : 0}}
      @endisset
    </td>
    @endforeach
    <?php
    $skor = $benar * $skor_benar - $salah * $skor_salah;
    $nilai = ($skor) ? ($skor / $skala_nilai) * ($jumlah_soal * $skor_benar): 0;
    $arr_nilai[] = $skor;
    ?>
    <td align="center">{{($length) ? $benar : ''}}</td>
    <td align="center">{{(($length) ? $salah : '')}}</td>
    <td align="center">
      @if($length)
      {{$skor}}
      @endif
    </td>
    <td align="center">
      @if($length)
      {{($skor * $total_skor) / $skala_nilai}}
      @endif
    </td>
  </tr>
  @endforeach
  <?php
  $jml_benar = 0;
  $jml_salah = 0;
  ?>
  <tr>
    <td align="right" colspan="4">JUMLAH :</td>
    @foreach($data->jawaban_soal as $index => $soal)
    <?php 
    $jml_benar += $soal->jawaban_benar->count();
    $jml_salah += $soal->jawaban_salah->count();
    ?>
    <td>{{$soal->jawaban_benar->count()}}</td>
    @endforeach
    <td align="center">{{$jml_benar}}</td>
    <td align="center">{{$jml_salah}}</td>
    <td align="center">{{($jml_benar) ? $jml_benar * $skor_benar - $jml_salah * $skor_salah : 0}}</td>
    <td align="center"></td>
  </tr>
  <tr>
    <td align="right" colspan="4">TERKECIL :</td>
    @for ($i = 0; $i < ($jumlah_soal + 2); $i++)
    <td align="right"></td>
    @endfor
    <td align="center">{{min(array_filter($arr_nilai))}}</td>
    <td></td>
  </tr>
  <tr>
    <td align="right" colspan="4">TERBESAR  :</td>
    @for ($i = 0; $i < ($jumlah_soal + 2); $i++)
    <td align="right"></td>
    @endfor
    <td align="center">{{max(array_filter($arr_nilai))}}</td>
    <td></td>
  </tr>
  <tr>
    <td align="right" colspan="4">RATA-RATA  :</td>
    @for ($i = 0; $i < ($jumlah_soal + 2); $i++)
    <td align="right"></td>
    @endfor
    <td align="center">{{array_sum($arr_nilai)/count(array_filter($arr_nilai))}}</td>
    <td></td>
  </tr>
  <tr>
    <td align="right" colspan="4">SIMPANGAN BAKU :</td>
    @for ($i = 0; $i < ($jumlah_soal + 2); $i++)
    <td align="right"></td>
    @endfor
    <td align="center">{{sd($arr_nilai)}}</td>
    <td></td>
  </tr>
</table>