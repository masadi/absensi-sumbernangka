@extends('layouts.cetak')
@section('content')
<h2 class="text-center">Rekapitulasi Hari Libur</h2>
<h3 class="text-center">Periode {{$start}} s/d {{$end}}</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width: 25%;vertical-align: middle" colspan="2">Hari, Tanggal</th>
            <th class="text-center" style="width: 25%;vertical-align: middle" rowspan="2">Satuan Pendidikan</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" rowspan="2">Kategori</th>
            <th class="text-center" style="width: 40%;vertical-align: middle" rowspan="2">Nama Hari Libur</th>
        </tr>
        <tr>
            <th class="text-center" style="width: 10%;vertical-align: middle">Tanggal Mulai</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Tanggal Selesai</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($hari_libur as $item)
        <tr>
            <td class="text-center">{{$item->mulai}}</td>
            <td class="text-center">{{$item->sampai}}</td>
            <td>{{($item->kategori_libur->sekolah_id) ? $item->kategori_libur->sekolah->nama : 'UMUM'}}</td>
            <td>{{$item->kategori_libur->nama}}</td>
            <td>{{$item->title}}</td>
        </tr>
        @empty
        <td class="text-center" colspan="5">Periode {{$start}} s/d {{$end}} tidak ada agenda libur</td>
        @endforelse
    </tbody>
</table>
@endsection