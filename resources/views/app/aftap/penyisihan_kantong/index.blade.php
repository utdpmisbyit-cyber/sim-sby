@extends('layouts.index')

@section('title', 'Penyisihan Kantong AFTAP')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Penyisihan Kantong AFTAP</h4>
        <a href="{{ route('aftap.penyisihan_kantong_aftap.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Tambah Penyisihan
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            {{-- Filter --}}
            <form method="GET" action="{{ route('aftap.penyisihan_kantong_aftap.index') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Cari no. transaksi">
                </div>
                <div class="col-md-3">
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control">
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fa fa-search"></i> Cari
                    </button>
                    <a href="{{ route('aftap.penyisihan_kantong_aftap.index') }}" class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </form>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th class="text-center">Jumlah Kantong</th>
                            <th>Dibuat Oleh</th>
                            <th style="width: 140px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penyisihan as $index => $row)
                            <tr>
                                <td>{{ $penyisihan->firstItem() + $index }}</td>
                                <td>
                                    <a href="{{ route('aftap.penyisihan_kantong_aftap.edit', $row->id) }}">
                                        {{ $row->no_transaksi }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $row->details_count }}</span>
                                </td>
                                <td>{{ $row->creator->name ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('aftap.penyisihan_kantong_aftap.edit', $row->id) }}"
                                       class="btn btn-sm btn-outline-warning" title="Lihat / Edit">
                                        <i class="fa fa-pen"></i>
                                    </a>
                                    <form action="{{ route('aftap.penyisihan_kantong_aftap.destroy', $row->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus transaksi ini? Stok kantong akan dikembalikan ke status tersedia.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada data penyisihan kantong.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $penyisihan->links() }}
            </div>

        </div>
    </div>
</div>
@endsection