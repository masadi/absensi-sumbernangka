@extends('layouts.kartu-pelajar')
@section('content')
<div class="rounded" style="background-image: url({{url('storage/'.$item->photo)}})"></div>
<div class="kotak">
    <div class="{{(strlen($item->nama) > 20 ) ? 'panjang' : 'nama'}}"><strong>{{$item->nama}}</strong></div>
</div>
<div class="kotak_detil">
    <div class="kelas">TTL : {{$item->tetala}}</div>
    <div class="kelas">Jenjang : {{bentuk_pendidikan_id($item->sekolah->bentuk_pendidikan_id)}}</div>
    @if($item->sekolah->bentuk_pendidikan_id == 15)
    <div class="jurusan">{{$item->kelas->nama_jurusan}}</div>
    @endif
</div>
<div class="nisn"><strong>{{$item->nisn}}</strong></div>
@endsection