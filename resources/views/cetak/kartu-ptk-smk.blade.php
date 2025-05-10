@extends('layouts.kartu-ptk')
@section('content')
<div class="qrcode">
    <!--img src="{{asset('storage/qrcodes/'.$item->ptk_id.'.svg')}}" alt="qrcodes" width="100"-->
    <img src="data:image/svg+xml;base64, {!! $qrcode !!}">
</div>
<div class="rounded" style="background-image: url({{url('storage/'.$item->photo)}})"></div>
<div class="kotak">
    <div class="nama {{(strlen($item->nama) > 25 ) ? 'panjang' : ''}}"><strong>{{$item->nama}}</strong></div>
    <div class="nisn"><strong>{{$item->nuptk}}</strong></div>
</div>
@endsection