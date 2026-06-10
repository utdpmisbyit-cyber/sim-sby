<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pengeluaran Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #eee; }
        h2 { margin-bottom: 0; }
    </style>
</head>
<body>

<h2>LAPORAN PENGELUARAN BARANG</h2>
<small>Tanggal Cetak: {{ now()->format('d-m-Y H:i') }}</small>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No Transaksi</th>
            <th>Tanggal</th>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th>Bagian</th>
            <th>User Input</th>
        </tr>
    </thead>

    <tbody>
        @forelse($data as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $item->no_trans_keluar }}</td>
            <td>{{ \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') }}</td>
            <td>{{ $item->barang->nama ?? $item->nama_barang }}</td>
            <td>{{ $item->qty_keluar }}</td>
            <td>{{ $item->satuan }}</td>
            <td>{{ $item->bagian->nama ?? '-' }}</td>
            <td>{{ $item->user_input }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>