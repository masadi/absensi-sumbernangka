@extends('layouts.kartu-pd')
@section('content')
<div class="qrcode">
    <!--img src="{{asset('storage/qrcodes/'.$item->peserta_didik_id.'.svg')}}" alt="qrcodes" width="100"-->
    <img src="data:image/svg+xml;base64, {!! $qrcode !!}" width="100">
    {{--!! QrCode::size(100)->generate($item->peserta_didik_id); !!--}}
</div>
<div class="rounded" style="background-image: url({{asset('storage/'.$item->photo)}})"></div>
<div class="kotak">
    <div class="nama {{(strlen($item->nama) > 25 ) ? 'panjang' : ''}}"><strong>{{$item->nama}}</strong></div>
    <div class="nisn"><strong>{{$item->nisn}}</strong></div>
    <div class="kelas"><strong>{{$item->kelas?->nama_jurusan}}</strong></div>
</div>
@endsection