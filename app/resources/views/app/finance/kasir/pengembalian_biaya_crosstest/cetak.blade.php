<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Retur {{ $pengembalianBiayaCrosstest->no_retur }}</title>
    <style>
        body { font-family: monospace, monospace; font-size: 12px; width: 320px; margin: 0 auto; }
        .center { text-align: center; }
        .right { text-align: right; }
        hr { border: none; border-top: 1px dashed #000; }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <strong>NOTA PENGEMBALIAN BIAYA</strong><br>
        CROSS TEST
    </div>
    <hr>
    No Retur : {{ $pengembalianBiayaCrosstest->no_retur }}<br>
    No FPUP&nbsp; : {{ $pengembalianBiayaCrosstest->no_fpup }}<br>
    Tgl&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ optional($pengembalianBiayaCrosstest->tgl_retur)->format('d/m/Y H:i') }}<br>
    Pasien&nbsp;&nbsp; : {{ $pengembalianBiayaCrosstest->nama_pasien }}<br>
    RS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : {{ $pengembalianBiayaCrosstest->nama_rs }}<br>
    <hr>
    @foreach ($pengembalianBiayaCrosstest->details as $d)
        {{ $d->jns_darah ?? '-' }} x{{ $d->jumlah }}<br>
        <div class="right">Rp {{ number_format((float) $d->subtotal, 0, ',', '.') }}</div>
    @endforeach
    <hr>
    <div class="right"><strong>TOTAL: Rp {{ number_format((float) $pengembalianBiayaCrosstest->total_retur, 0, ',', '.') }}</strong></div>
    <hr>
    Kasir: {{ $pengembalianBiayaCrosstest->nama_kasir }} ({{ $pengembalianBiayaCrosstest->kode_kasir }})<br>
    <div class="center">-- Terima Kasih --</div>
</body>
</html>