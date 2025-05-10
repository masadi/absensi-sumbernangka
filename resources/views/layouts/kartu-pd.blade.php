<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cetak Dokumen</title>
    <!-- Styles -->
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">
    @if ($item->sekolah->bentuk_pendidikan_id == 15)
    <style>
        body {
            background-image: url('images/depan-smk.jpg');
            background-position: top;
            background-repeat: no-repeat;
            background-image-resize: 4;
        }
    </style>
    @else
    <style>
        body {
            background-image: url('images/depan-smp.jpg');
            background-position: top;
            background-repeat: no-repeat;
            background-image-resize: 4;
        }
    </style>
    @endif
    <link rel="stylesheet" href="{{ asset('css/kartu-pd.css') }}" />
</head>
<body>
	@yield('content')
</body>
</html>
