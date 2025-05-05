@extends('layouts.kartu-pd')
@section('content')
<div class="rounded" style="background-image: url({{asset('storage/'.$item->photo)}})"></div>
<div class="kotak">
    <div class="nama {{(strlen($item->nama) > 25 ) ? 'panjang' : ''}}"><strong>{{$item->nama}}</strong></div>
    <div class="nisn"><strong>{{$item->tetala}}</strong></div>
    <div class="nisn"><strong>{{$item->nisn}}</strong></div>
    <div class="kelas"><strong>{{$item->kelas?->nama_jurusan}}</strong></div>
</div>
{{dd($item)}}
@endsection