<table>
  <thead>
    <tr>
        <th align="center" colspan="{{$data->jawaban_soal->count() + 4}}">TABEL HASIL {{$data->jadwal_ujian->jadwal->nama}}</th>
    </tr>
    <tr>
        <th align="center" colspan="{{$data->jawaban_soal->count() + 4}}">TAHUN PELAJARAN {{substr($data->jadwal_ujian->rombongan_belajar->semester_id, 0, 4)}}/{{substr($data->jadwal_ujian->rombongan_belajar->semester_id, 0, 4) + 1}}</th>
    </tr>
    <tr>
      <th colspan="{{$data->jawaban_soal->count() + 4}}"></th>
    </tr>
    <tr>
      @for ($i = 0; $i < 9; $i++)
      <th></th>
      @endfor
      <th>Sekolah</th>
      @for ($i = 0; $i < 6; $i++)
      <th></th>
      @endfor
      <th>: {{$data->jadwal_ujian->rombongan_belajar->sekolah->nama}}</th>
    </tr>
    <tr>
      @for ($i = 0; $i < 9; $i++)
      <th></th>
      @endfor
      <th>Mata Pelajaran</th>
      @for ($i = 0; $i < 6; $i++)
      <th></th>
      @endfor
      <th>: {{$data->jadwal_ujian->mata_pelajaran->nama}}</th>
    </tr>
    <tr>
      @for ($i = 0; $i < 9; $i++)
      <th></th>
      @endfor
      <th>Kelas</th>
      @for ($i = 0; $i < 6; $i++)
      <th></th>
      @endfor
      <th>: {{$data->jadwal_ujian->rombongan_belajar->nama}}</th>
    </tr>
    <tr>
      @for ($i = 0; $i < 9; $i++)
      <th></th>
      @endfor
      <th>Di Analisis Oleh</th>
      @for ($i = 0; $i < 6; $i++)
      <th></th>
      @endfor
      <th>Aplikasi Absensi v.1.0.0</th>
    </tr>
    <tr>
      <th colspan="{{$data->jawaban_soal->count() + 4}}"></th>
    </tr>
    <!--tr>
      <th colspan="2" align="center">KELOMPOK ATAS</th>
    </tr-->
  </thead>
  <tbody>
    <tr>
      <td align="center">No.</td>
      <td rowspan="2" align="center" style="vertical-align: middle">NAMA SISWA</td>
      <td align="center">No. Soal</td>
      @foreach($data->jawaban_soal as $soal)
      <td align="center">{{$soal->nomor_jawaban}}</td>
      @endforeach
      <td align="center">Jml.</td>
    </tr>
    <tr>
      <td align="center">Urut</td>
      <td align="center">K. Jawab</td>
      @foreach($data->jawaban_soal as $soal)
      <td align="center">{{$soal->jawaban}}</td>
      @endforeach
      <td align="center">Skor</td>
    </tr>
    <?php
    $benar_bawah = [];
    ?>
    @foreach($data->items as $item)
      <tr>
          <td align="center">{{ $loop->iteration }}</td>
          <td>{{ $item->nama }}</td>
          <td></td>
          <?php
          $benar = 0;
          ?>
          @forelse ($item->anggota_rombel->jawaban_pd as $index => $jawaban_pd)
            @isset($data->jawaban_soal[$index])
              @if ($data->jawaban_soal[$index]->jawaban == $jawaban_pd->jawaban)
                  <?php
                  $benar++;
                  ?>
              @endif
            @endisset
            <td align="center">{{$jawaban_pd->jawaban}}</td>
          @empty
            @foreach($data->jawaban_soal as $soal)
              <td align="center">-</td>
            @endforeach
          @endforelse
          <td align="center">{{$benar}}</td>
      </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td align="center" colspan="3">JUMLAH MENJAWAB BENAR</td>
      @foreach($data->jawaban_soal as $soal)
        <td align="center">{{$soal->jawaban_benar->count()}}</td>
      @endforeach
      <td></td>
    </tr>
  </tfoot>
</table>