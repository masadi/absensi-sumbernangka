@extends('layouts.cetak')
@section('content')
<style>
    body{font-size: 10px;}
    .table thead tr th,
.table tbody tr th,
.table tfoot tr th,
.table thead tr td,
.table tbody tr td,
.table tfoot tr td {
  padding: 5px !important;
  }
</style>
<h2 class="text-center">Laporan Ketidakhadiran</h2>
<table class="table">
    <tr>
        <td>UNIT</td>
        <td>: </td>
    </tr>
    <tr>
        <td>PERIODE</td>
        <td>: {{$startStr}} s/d {{$endStr}}</td>
    </tr>
</table>
<br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;vertical-align: middle" >No</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" >Nama</th>
            <th class="text-center" style="width: 10%;vertical-align: middle" >UNIT</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $absen)
        <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            <td>{{$absen->ptk->nama}}</td>
            <td class="text-center">{{$absen->ptk->sekolah->nama}}</td>
            <td class="text-center">-</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection