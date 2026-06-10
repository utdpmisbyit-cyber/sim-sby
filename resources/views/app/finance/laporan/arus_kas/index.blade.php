@extends('layouts.index')

@section('content')

<div class="container">

    <h4 class="text-center fw-bold">PALANG MERAH INDONESIA KOTA SURABAYA</h4>
    <h5 class="text-center">LAPORAN ARUS KAS</h5>
    <p class="text-center">
        Periode {{ date('d F Y', strtotime($tglAwal)) }} -
        {{ date('d F Y', strtotime($tglAkhir)) }}
    </p>

    {{-- FILTER --}}
    <form method="GET" action="">
        <div class="row mb-4 justify-content-center">
            <div class="col-md-3">
                <label>Tanggal Awal:</label>
                <input type="date" name="tgl_awal" value="{{ $tglAwal }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label>Tanggal Akhir:</label>
                <input type="date" name="tgl_akhir" value="{{ $tglAkhir }}" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <a href="{{ route('finance.laporan.arus_kas.index') }}" class="btn btn-light w-100">Reset</a>
            </div>
        </div>
    </form>

    {{-- KOTAK ATAS --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="p-3 bg-success bg-opacity-25 text-center rounded shadow">
                <h5>Penerimaan</h5>
                <h4>Rp {{ number_format($penerimaan,0,',','.') }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-danger bg-opacity-25 text-center rounded shadow">
                <h5>Pembayaran</h5>
                <h4>Rp {{ number_format($pembayaran,0,',','.') }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-info bg-opacity-25 text-center rounded shadow">
                <h5>Investasi</h5>
                <h4>Rp {{ number_format($investasi,0,',','.') }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-warning bg-opacity-25 text-center rounded shadow">
                <h5>Saldo Akhir</h5>
                <h4>Rp {{ number_format($saldoAkhir,0,',','.') }}</h4>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- CHART --}}
        <div class="col-md-6">
            <canvas id="chartArusKas" height="200"></canvas>
        </div>

        {{-- TABEL DETAIL --}}
        <div class="col-md-6">
            <table class="table table-sm">
                <thead class="bg-light fw-bold">
                    <tr>
                        <th>Keterangan</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>

                <tbody>
                    <tr><td colspan="2" class="fw-bold bg-secondary text-white">Arus Kas Operasional</td></tr>
                    <tr>
                        <td>Penerimaan</td>
        <td class="text-end">Rp {{ number_format($penerimaan,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td>Pembayaran</td>
                        <td class="text-end">Rp {{ number_format($pembayaran,0,',','.') }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Total Operasional</td>
                        <td class="text-end">Rp {{ number_format($penerimaan - $pembayaran,0,',','.') }}</td>
                    </tr>

                    <tr><td colspan="2" class="fw-bold bg-secondary text-white">Arus Kas Investasi</td></tr>
                    <tr>
                        <td>Pembelian Aset Tetap</td>
                        <td class="text-end">Rp {{ number_format($investasi,0,',','.') }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Total Investasi</td>
                        <td class="text-end">Rp {{ number_format(-$investasi,0,',','.') }}</td>
                    </tr>

                    <tr><td colspan="2" class="fw-bold bg-secondary text-white">Kenaikan / Penurunan Kas</td></tr>
                    <tr>
                        <td>Saldo Akhir Periode</td>
                        <td class="text-end">Rp {{ number_format($saldoAkhir,0,',','.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartArusKas');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Penerimaan', 'Pembayaran', 'Investasi', 'Saldo Akhir'],
            datasets: [{
                label: 'Tahun {{ date("Y") }}',
                data: [
                    {{ $penerimaan }},
                    {{ $pembayaran }},
                    {{ $investasi }},
                    {{ $saldoAkhir }},
                ],
            }]
        },
    });
</script>
@endsection