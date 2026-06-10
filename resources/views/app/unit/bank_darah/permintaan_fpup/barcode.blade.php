<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Barcode FPUP</title>

<style>
body{
    font-family: Arial, sans-serif;
    margin:0;
    padding:10px;
}

.label{
    width:320px;
    border:1px solid #ccc;
    padding:8px;
}

.header{
    text-align:center;
    font-size:12px;
    font-weight:bold;
}

.barcode{
    text-align:center;
    margin-top:5px;
}

.info{
    font-size:11px;
    margin-top:4px;
}

.nama{
    font-weight:bold;
}
</style>
</head>
<body onload="window.print()">

<div class="label">

    <div class="header">
        UDD PMI KOTA SBY
    </div>

    <div class="barcode">
        {!! DNS1D::getBarcodeHTML(
            $fpup->no_fpup,
            'C128',
            2,
            50
        ) !!}
    </div>

    <div class="info">
        {{ $fpup->no_fpup }}
        &nbsp;
        &lt;{{ now()->format('d-m-Y') }}&gt;
    </div>

    <div class="nama">
        OS: {{ strtoupper($fpup->nama_pasien) }}
    </div>

</div>

</body>
</html>