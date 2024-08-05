<!DOCTYPE html>
<html>
<head>
    <title>Stocker</title>
    <style>
        @page { margin: 0.0px; }

        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: bold;
            src: url({{ storage_path("OpenSans-Bold.ttf") }}) format('truetype');
        }

        body {
            margin: 0.0px;
            font-family: 'Open Sans', sans-serif;
            font-weight: bold;
        }

        * {
            font-size: 13px;
        }

        img {
            width: 45px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td, table th{
            text-align: left;
            vertical-align: middle;
            padding: 0.0px;
            width: 100%;
        }
    </style>
</head>
<body>
    @foreach ($data as $d)
        <div style="{{ $loop->last ? '' : 'page-break-after: always;' }}">
            <hr style="margin-bottom: 25px;border-collapse: collapse;">
            <hr style="margin-top: 10px;border-style: dashed;border-collapse: collapse;">
            <h5 style="font-size: 11px;text-align: center;margin-top: 30px;margin-bottom: 3px;">{{ date('y-m', strtotime($d['month_year'])) }}</h5>
            <div style="margin-bottom: 0px;">
                <center>
                    <img style="margin-bottom: 0px;" src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->generate($d['id_month_year'])) !!}">
                </center>
            </div>
            <h5 style="font-size: 11px;text-align: center;margin-top: 3px;margin-bottom: 0px;">{{ $d['month_year_number'] }}</h5>
        </div>
    @endforeach
</body>
</html>
