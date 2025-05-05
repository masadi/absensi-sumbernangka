<table border="1">
  <thead>
    <tr>
        <th align="center" colspan="16">ANALISIS BUTIR SOAL</th>
    </tr>
    <tr>
        <th align="center" colspan="16"></th>
    </tr>
    <tr>
      <th colspan="3">Sekolah</th>
      <th colspan="11">: {{$data->jadwal_ujian->rombongan_belajar->sekolah->nama}}</th>
    </tr>
    <tr>
      <th colspan="3">Mata Pelajaran</th>
      <th colspan="11">: {{$data->jadwal_ujian->mata_pelajaran->nama}}</th>
    </tr>
    <tr>
      <th colspan="3">Kelas</th>
      <th colspan="11">: {{$data->jadwal_ujian->rombongan_belajar->nama}}</th>
    </tr>
    <tr>
      <th colspan="3">Nama Ujian</th>
      <th colspan="11">: {{$data->jadwal_ujian->jadwal->nama}}</th>
    </tr>
    <tr>
      <th colspan="3">Tanggal Ujian</th>
      <th colspan="11">: {{$data->jadwal_ujian->jadwal->tanggal_indo}}</th>
    </tr>
    <tr>
      <th colspan="3">Materi Pokok</th>
      <th colspan="11">: </th>
    </tr>
    <tr>
      <th colspan="14"></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td align="center" rowspan="2" style="vertical-align: middle">No.</td>
      <td align="center" rowspan="2" style="vertical-align: middle">No. Item</td>
      <td align="center" colspan="3" style="vertical-align: middle">Statistik Item</td>
      <td align="center" colspan="5" style="vertical-align: middle">Statistik Option</td>
      <td align="center" colspan="4" style="vertical-align: middle">Tafsiran</td>
    </tr>
    <tr>
      <td align="center" style="vertical-align: middle">Prop. Correct</td>
      <td align="center" style="vertical-align: middle">Biser	Point</td>
      <td align="center" style="vertical-align: middle">Biser</td>
      <td align="center" style="vertical-align: middle">Opt.</td>
      <td align="center" style="vertical-align: middle">Prop. Endorsing</td>
      <td align="center" style="vertical-align: middle">Biser</td>
      <td align="center" style="vertical-align: middle">Point Biser</td>
      <td align="center" style="vertical-align: middle">Key</td>
      <td align="center" style="vertical-align: middle">Daya Pembeda</td>
      <td align="center" style="vertical-align: middle">Tingkat Kesulitan</td>
      <td align="center" style="vertical-align: middle">Efektifitas Option</td>
      <td align="center" style="vertical-align: middle">Status Soal</td>
    </tr>
    @foreach($data->jawaban_soal as $index => $soal)
      <?php
      $tertinggi = reratajmlJawabanOpsi($data->jadwal_ujian->soal_ujian, $data->items, $soal, $index);
      ?>
      @for ($i = 0; $i < $data->jadwal_ujian->soal_ujian->jumlah_pg; $i++)
        @if(!$i)
        <tr>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">{{ $loop->iteration }}</td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">{{ $loop->iteration }}</td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            {{prop_corrent($soal->jawaban_benar->count(), $data->jadwal_ujian->soal_ujian->peserta)}}
          </td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            {{getBiser($data->jadwal_ujian->soal_ujian, $soal, $index, $data->items)}}
          </td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            {{getPhpCorrelation($data->jadwal_ujian->soal_ujian, $soal, $index, $data->items)}}
          </td>
          <td align="center">{{getAlphabet($i)}}</td>
          <td align="center">{{jmlJawabanOpsi(getAlphabet($i), $data->jadwal_ujian->soal_ujian, $soal, $index, $data->items)}}</td>
          <td align="center">-</td>
          <td align="center">-</td>
          <td align="center">
            {{($soal->jawaban == getAlphabet($i)) ? '#' : ''}}
          </td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            @if(getBiser($data->jadwal_ujian->soal_ujian, $soal, $index, $data->items) > 0.21)
            Dapat Membedakan
            @else
            Tidak dapat membedakan
            @endif
          </td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            @if(prop_corrent($soal->jawaban_benar->count(), $data->jadwal_ujian->soal_ujian->peserta) >= 0.7)
            Mudah
            @elseif(prop_corrent($soal->jawaban_benar->count(), $data->jadwal_ujian->soal_ujian->peserta) >= 0.3 || prop_corrent($soal->jawaban_benar->count(), $data->jadwal_ujian->soal_ujian->peserta) < 0.7)
            Sedang
            @else
            Sulit
            @endif
          </td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            @if($tertinggi['max_1'] > $tertinggi['max_2'])
            Ada Option lain yang bekerja lebih baik
            @else
            Baik
            @endif
          </td>
          <td align="center" style="vertical-align: top" rowspan="{{$data->jadwal_ujian->soal_ujian->jumlah_pg}}">
            <?php
            if(getBiser($data->jadwal_ujian->soal_ujian, $soal, $index, $data->items) > 0.21){
              $a = 1;
            } else {
              $a = -2;
            }
            if(prop_corrent($soal->jawaban_benar->count(), $data->jadwal_ujian->soal_ujian->peserta) == 1 || prop_corrent($soal->jawaban_benar->count(), $data->jadwal_ujian->soal_ujian->peserta) == 0){
              $b = 0;
            } else {
              $b = 1;
            }
            if($tertinggi['max_1'] > $tertinggi['max_2']){
              $c = 0;
            } else {
              $c = 1;
            }
            $sum = $a + $b + 1;
            ?>
            @if($sum > 2)
            Dapat diterima
            @elseif($sum > 0 && $sum <= 2)
            Soal sebaiknya Direvisi
            @else
            Ditolak/Jangan Digunakan
            @endif
          </td>
        </tr>
        @else
        <tr>
          <td align="center">{{getAlphabet($i)}}</td>
          <td align="center">{{jmlJawabanOpsi(getAlphabet($i), $data->jadwal_ujian->soal_ujian, $soal, $index, $data->items)}}</td>
          <td align="center">-</td>
          <td align="center">-</td>
          <td align="center">
            {{($soal->jawaban == getAlphabet($i)) ? '#' : ''}}
          </td>
        </tr>
        @endif
      @endfor
    @endforeach
  </tbody>
</table>