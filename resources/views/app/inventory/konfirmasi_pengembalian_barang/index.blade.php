
@extends('layouts.index')

@section('title', 'Konfirmasi Pengembalian Barang')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Konfirmasi Pengembalian Barang</h5>
        <a href="{{ route('inventory.konfirmasi_pengembalian_barang.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus"></i> Tambah Pengembalian
        </a>
    </div>

    <div class="card-body">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filter --}}
        <form method="GET" action="{{ route('inventory.konfirmasi_pengembalian_barang.index') }}" class="row g-2 mb-3">
            <div class="col-md-2">
                <input type="text" name="no_kembali" class="form-control form-control-sm"
                       placeholder="No. Kembali" value="{{ $params['no_kembali'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="tgl_kembali" class="form-control form-control-sm"
                       value="{{ $params['tgl_kembali'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="departemen" class="form-control form-control-sm"
                       placeholder="Departemen" value="{{ $params['departemen'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="barang" class="form-control form-control-sm"
                       placeholder="Nama/Kode Barang" value="{{ $params['barang'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <select name="kondisi" class="form-select form-select-sm">
                    <option value="">-- Kondisi --</option>
                    <option value="baik"  @selected(($params['kondisi'] ?? '') === 'baik')>Baik</option>
                    <option value="rusak" @selected(($params['kondisi'] ?? '') === 'rusak')>Rusak</option>
                </select>
            </div>
            <div class="col-md-2 d-flex">
                <button type="submit" class="btn btn-sm btn-secondary me-1">Cari</button>
                <a href="{{ route('inventory.konfirmasi_pengembalian_barang.index') }}" class="btn btn-sm btn-light">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No. Kembali</th>
                        <th>Tgl Kembali</th>
                        <th>Departemen</th>
                        <th>Barang</th>
                        <th class="text-end">Jumlah</th>
                        <th>Kondisi</th>
                        <th>Keterangan</th>
                        <th class="text-center" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td>{{ $row->no_kembali }}</td>
                            <td>{{ optional($row->tgl_kembali)->format('d/m/Y') }}</td>
                            <td>{{ $row->departemen ?? '-' }}</td>
                            <td>
                                @foreach ($row->details as $d)
                                    <div>
                                        {{ optional($d->barang)->nama ?? '-' }}
                                        @if ($d->no_kantong)
                                            <span class="text-muted">({{ $d->no_kantong }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td class="text-end">
                                @foreach ($row->details as $d)
                                    <div>{{ $d->jumlah }}</div>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($row->details as $d)
                                    <span class="badge {{ $d->kondisi === 'baik' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($d->kondisi) }}
                                    </span>
                                @endforeach
                            </td>
                            <td>{{ $row->keterangan ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('inventory.konfirmasi_pengembalian_barang.edit', $row->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <form action="{{ route('inventory.konfirmasi_pengembalian_barang.destroy', $row->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus data ini? Stok yang sudah ditambahkan akan dikembalikan/dikurangi.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $data->links() }}
    </div>
</div>
@endsection