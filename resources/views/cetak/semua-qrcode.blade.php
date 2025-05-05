@extends('layouts.cetak')
@section('content')
<div>
    <table class="table table-bordered">
        @foreach ($collection_ptk as $item_ptk)
        <tr>
            @foreach ($item_ptk as $ptk)
            <td class="text-center">
                {{generate_qrcode($ptk->ptk_id)}}
                {{$ptk->nama}} <br>
                <img src="{{asset('storage/qrcodes/'.$ptk->ptk_id.'.svg')}}" alt="{{$ptk->nama}}">
            </td>
            @endforeach
        </tr>    
        @endforeach
        @foreach ($collection_pd as $item_pd)
        <tr>
            @foreach ($item_pd as $pd)
            <td class="text-center">
                {{generate_qrcode($pd->peserta_didik_id)}}
                {{$pd->nama}} <br>
                <img src="{{asset('storage/qrcodes/'.$pd->peserta_didik_id.'.svg')}}" alt="{{$pd->nama}}">
            </td>
            @endforeach
        </tr>    
        @endforeach
    </table>
</div>
@endsection