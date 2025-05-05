@extends('layouts.cetak')
@section('content')
<style>
    .table thead tr th,
.table tbody tr th,
.table tfoot tr th,
.table thead tr td,
.table tbody tr td,
.table tfoot tr td {
  padding: 2px !important;
  }
.table tbody tr td.custom-class{
    border-top: #fff 1px solid;
    border-right: #fff 1px solid;
    border-bottom: #fff 1px solid;
}
.tengah {
  margin-left: auto;
  margin-right: auto;
}
</style>
<table class="table tengah" style="width: 100%">
    <tr>
        <td class="text-right" style="vertical-align:top;">
            <img src="{{asset('images/armet.png')}}" width="100" />
            {{--
            @if($sekolah->logo_sekolah)
            <img src="{{asset('storage/'.$sekolah->logo_sekolah)}}" width="100">
            @else
            <img src="{{asset('images/tutwuri.png')}}" width="100">
            @endif
            --}}
        </td>
        <td class="text-center" style="vertical-align:top;">
            <h2>KARTU LEGITIMASI</h2>
            <h3>
                <strong>
                    {{$jadwal->rombongan_belajar->sekolah->nama}} SUMBER GAYAM
                    <br>
                    TAHUN AJARAN {{nama_tapel($jadwal->rombongan_belajar->semester->nama)}}
                </strong>
                    <br>
                    <br>
                    <u>{{$jadwal->nama}}</u>
            </h3>
        </td>
        <td class="text-right" style="vertical-align:top;">
            <img src="{{asset('images/tutwuri.png')}}" width="100">
        </td>
    </tr>
</table>
<table class="table">
    <tr>
        <td class="text-center">
            <img src="{{asset('storage/'.$pd->photo)}}" width="100" style="border: #000 2px solid;">
        </td>
        <td>
            <table class="table">
                <tr>
                    <td style="width: 15%;padding: 2px;">Nama</td>
                    <td style="width: 85%;padding: 2px;">: {{$pd->nama}}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">NISN</td>
                    <td style="padding: 2px;">: {{$pd->nisn}}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">Kompetensi</td>
                    <td style="padding: 2px;">: {{$pd->kelas->nama_jurusan}}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">Rombel</td>
                    <td style="padding: 2px;">: {{$pd->kelas->nama}}</td>
                </tr>
                <tr>
                    <td style="padding: 2px;">No. Peserta</td>
                    <td style="padding: 2px;">: {{$pd->nisn}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="vertical-align: middle;">No</th>
            <th class="text-center" style="vertical-align: middle;">Hari, Tanggal</th>
            <th class="text-center" style="vertical-align: middle;">Ujian Ke-</th>
            <th class="text-center" style="vertical-align: middle;">Kode Mapel</th>
            <th class="text-center" style="vertical-align: middle;">Mata Pelajaran</th>
            <th class="text-center" style="vertical-align: middle;">Paraf Pengawas</th>
            <th class="text-center" style="vertical-align: middle;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($jadwal->jadwal_ujian as $jadwal_ujian)
        <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            <td class="text-center">{{$jadwal_ujian->tanggal_indo}}</td>
            <td class="text-center">{{$jadwal_ujian->jam_ke}}</td>
            <td class="text-center">{{$jadwal_ujian->mata_pelajaran->kode}}</td>
            <td>{{$jadwal_ujian->mata_pelajaran->nama}}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>
<table class="table table-bordered">
    <tr>
        <td class="text-center" style="vertical-align: top;width: 50%">
            Mengetahui <br>
            Kepala Sekolah <br>
            <br>
            <br>
            <br>
            <br>
            {{($jadwal->rombongan_belajar->sekolah->ptk_id) ? $jadwal->rombongan_belajar->sekolah->kasek->nama : '-'}}
        </td>
        <td class="text-center" style="vertical-align: top;">
            Tangerang, {{$jadwal->tanggal_indo}} <br>
            Ketua Pelaksana <br>
            <br>
            <br>
            <br>
            <br>
            {{$jadwal->ptk->nama}}
        </td>
    </tr>
</table>
<table class="table table-bordered">
    <tbody>
        <tr>
            <td style="vertical-align: top;width: 70%">
                Ketentuan:<br>
                <ol>
                    @foreach ($jadwal->catatan_ujian as $item)
                    <li>{{$item->catatan}}</li>
                    @endforeach
                </ol>
            </td>
            <td class="text-center custom-class" style="width: 30%;">
                <img src="{{asset('storage/qrcodes/'.$pd->nisn.'.svg')}}" alt="qrcodes" width="100">
            </td>
        </tr>
    </tbody>
</table>
<table class="table table-bordered">
    <tr>
        <td class="text-center">
            <i>Kartu ini milik {{$jadwal->rombongan_belajar->sekolah->nama}}, jika menemukan harap mengembalikan ke {{$jadwal->rombongan_belajar->sekolah->nama}}, {{$jadwal->rombongan_belajar->sekolah->alamat}}</i>
        </td>
    </tr>
</table>
@endsection