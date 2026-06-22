@extends('layouts.index')

@section('title', 'Pemberian Darah Referal')

@push('styles')
<style>
    :root {
        --bd-cyan: #00b4d8;
        --bd-cyan-dark: #0077b6;
        --bd-yellow: #ffe066;
        --bd-gray: #e9ecef;
    }
    .page-header {
        background: linear-gradient(135deg, var(--bd-cyan-dark), var(--bd-cyan));
        color: #fff;
        border-radius: 6px 6px 0 0;
        padding: 10px 16px;
    }
    .card-bd { border: 1px solid #b0c4de; border-radius: 6px; overflow: hidden; }
    .table-bd thead th {
        background: var(--bd-cyan-dark);
        color: #fff;
        font-size: .8rem;
        white-space: nowrap;
        vertical-align: middle;
        border: 1px solid #0077b6;
    }
    .table-bd tbody td {
        font-size: .82rem;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }
    .table-bd tbody tr:hover { background-color: #e8f4fd; }
    .badge-status { font-size: .72rem; padding: 3px 8px; border-radius: 10px; }
    .filter-bar { background: #f0f8ff; border: 1px solid #b0c4de; border-radius: 6px; padding: 12px 16px; margin-bottom: 14px; }
    .btn-bd-add { background: var(--bd-cyan-dark); color: #fff; border: none; }
    .btn-bd-add:hover { background: var(--bd-cyan); color: #fff; }
    .btn-icon { padding: 3px 8px; font-size: .78rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- ── Flash Messages ────────────────────────────────────────── --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Card Utama ─────────────────────────────────────────────── --}}
    <div class="card-bd">

        {{-- Header --}}
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-droplet-fill me-2"></i>
                <strong>Pemberian Darah Referal</strong>
            </div>
            <a href="{{ route('referal.pemberian_darah.create') }}" class="btn btn-sm btn-light fw-semibold">
                <i class="bi bi-plus-circle me-1"></i>Tambah Baru
            </a>
        </div>

        <div class="p-3">

            {{-- ── Filter ────────────────────────────────────────── --}}
            <form method="GET" action="{{ route('referal.pemberian_darah.index') }}" class="filter-bar">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label form-label-sm mb-1">Cari</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="No.Pemberian / FPUP / Pasien / Dokter"
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm mb-1">Tanggal Dari</label>
                        <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                               value="{{ request('tanggal_dari') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm mb-1">Tanggal S/D</label>
                        <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                               value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label form-label-sm mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            @foreach(['draft'=>'Draft','proses'=>'Proses','selesai'=>'Selesai','batal'=>'Batal'] as $val=>$label)
                                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        <a href="{{ route('referal.pemberian_darah.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- ── Tabel Data ─────────────────────────────────────── --}}
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-bd mb-2">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:40px">No</th>
                            <th>No. Pemberian</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>No. FPUP</th>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Rumah Sakit</th>
                            <th class="text-center">Gol/Rh</th>
                            <th>Jns Biaya</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($list as $idx => $row)
                        <tr>
                            <td class="text-center">{{ $list->firstItem() + $idx }}</td>
                            <td class="fw-semibold text-primary">{{ $row->no_pemberian }}</td>
                            <td>{{ $row->tanggal->format('d-m-Y') }}</td>
                            <td>{{ $row->jam_keluar ?? '-' }}</td>
                            <td>{{ $row->no_fpup ?? '-' }}</td>
                            <td>{{ $row->pasien ?? '-' }}</td>
                            <td>{{ $row->dokter ?? '-' }}</td>
                            <td>
                                <small>{{ $row->nama_rs ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                {{ $row->gol_darah_pasien }}
                                <small class="text-muted">{{ $row->rh_pasien }}</small>
                            </td>
                            <td>{{ $row->jns_biaya ?? '-' }}</td>
                            <td class="text-center">{{ $row->details_count ?? $row->details->count() }}</td>
                            <td class="text-center">
                                <span class="badge badge-status bg-{{ $row->status_badge }}">
                                    {{ $row->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('referal.pemberian_darah.show', $row) }}"
                                   class="btn btn-sm btn-outline-info btn-icon" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('referal.pemberian_darah.edit', $row) }}"
                                   class="btn btn-sm btn-outline-warning btn-icon" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-icon btn-hapus"
                                        title="Hapus"
                                        data-id="{{ $row->id }}"
                                        data-no="{{ $row->no_pemberian }}"
                                        data-bs-toggle="modal" data-bs-target="#modalHapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Tidak ada data pemberian darah.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $list->firstItem() }}–{{ $list->lastItem() }}
                    dari {{ $list->total() }} data
                </small>
                {{ $list->links('pagination::bootstrap-5') }}
            </div>

        </div>{{-- /p-3 --}}
    </div>{{-- /card-bd --}}
</div>

{{-- ── Modal Konfirmasi Hapus ──────────────────────────────────────── --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="bi bi-trash me-1"></i>Konfirmasi Hapus</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-3">
                <p class="mb-1">Hapus data pemberian:</p>
                <strong id="labelHapus" class="text-danger"></strong>
                <p class="text-muted small mt-2 mb-0">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="formHapus" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', function () {
        const id  = this.dataset.id;
        const no  = this.dataset.no;
        document.getElementById('labelHapus').textContent = no;
        document.getElementById('formHapus').action =
            '{{ url("referal/pemberian_darah") }}/' + id;
    });
});
</script>
@endpush