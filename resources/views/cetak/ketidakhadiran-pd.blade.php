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
            <th class="text-center" style="width: 10%;vertical-align: middle" >NISN</th>
            <th class="text-center" style="width: 11%;vertical-align: middle">Kelas</th>
            <th class="text-center" style="width: 10%;vertical-align: middle">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $absen)
        <tr>
            <td class="text-center">{{$loop->iteration}}</td>
            <td>{{$absen->pd->nama}}</td>
            <td class="text-center">{{$absen->pd->nisn}}</td>
            <td class="text-center">{{$absen->pd->kelas->nama}}</td>
            <td class="text-center">-</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection