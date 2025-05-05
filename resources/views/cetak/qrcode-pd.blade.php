@extends('layouts.kartu-pd')
@section('content')
<div class="qrcode">
    <img src="data:image/svg+xml;base64, {!! $qrcode !!}" width="120">
</div>
@endsection