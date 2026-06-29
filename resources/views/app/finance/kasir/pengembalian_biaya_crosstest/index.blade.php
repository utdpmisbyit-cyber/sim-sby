
@extends('layouts.index')

@section('title', 'Pengembalian Biaya Cross Test')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Pengembalian Biaya Cross Test</h4>
            <small class="text-muted">Retur biaya uji cocok serasi (cross test) berdasarkan No. FPUP</small>
        </div>
        <a href="{{ route('finance.pengembalian_biaya_crosstest.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Retur Baru
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="No Retur / No FPUP / Nama Pasien / RS">
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">Dari</span>
                        <input type="date" name="tgl_dari" value="{{ request('tgl_dari') }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">Sampai</span>
                        <input type="date" name="tgl_sampai" value="{{ request('tgl_sampai') }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No Retur</th>
                            <th>No FPUP</th>
                            <th>Tgl Retur</th>
                            <th>Nama Pasien</th>
                            <th>Rumah Sakit</th>
                            <th>Kelas Rawat</th>
                            <th class="text-end">Total Retur</th>
                            <th>Kasir</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $row)
                        <tr>
                            <td>{{ $row->no_retur }}</td>
                            <td>{{ $row->no_fpup }}</td>
                            <td>{{ optional($row->tgl_retur)->format('d/m/Y H:i') }}</td>
                            <td>{{ $row->nama_pasien }}</td>
                            <td>{{ $row->nama_rs }}</td>
                            <td>{{ $row->kelas_rawat }}</td>
                            <td class="text-end">Rp {{ number_format((float) $row->total_retur, 0, ',', '.') }}</td>
                            <td>{{ $row->kode_kasir }}</td>
                            <td>
                                <span class="badge bg-{{ $row->status === 'disimpan' ? 'success' : ($row->status === 'batal' ? 'danger' : 'secondary') }}">
                                    {{ ucfirst($row->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('finance.pengembalian_biaya_crosstest.edit', $row) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('finance.pengembalian_biaya_crosstest.print', $row) }}" target="_blank"
                                   class="btn btn-sm btn-outline-secondary" title="Cetak Ulang">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <form action="{{ route('finance.pengembalian_biaya_crosstest.destroy', $row) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Hapus data retur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Belum ada data retur.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $data->links() }}
        </div>
    </div>
</div>
@endsection