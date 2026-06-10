
@extends('layouts.index')

@section('title', 'Pengembalian Kantong')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Header ── --}}
    <div class="d-flex align-items-center justify-content-between mb-3 mt-3">
        <div>
            <h4 class="mb-0 fw-bold">Pengembalian Kantong</h4>
            <small class="text-muted">Manajemen data pengembalian kantong darah</small>
        </div>
        <a href="{{ route('unit.pengembalian_kantong.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Pengembalian
        </a>
    </div>

    {{-- ── Alert ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Filter ── --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('unit.pengembalian_kantong.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label class="form-label form-label-sm mb-1">No. Kembali</label>
                    <input type="text" name="no_kembali" class="form-control form-control-sm"
                           value="{{ $params['no_kembali'] ?? '' }}" placeholder="KB250500...">
                </div>
                <div class="col-sm-3">
                    <label class="form-label form-label-sm mb-1">No. Kantong</label>
                    <input type="text" name="no_kantong" class="form-control form-control-sm"
                           value="{{ $params['no_kantong'] ?? '' }}" placeholder="Scan / ketik...">
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Kondisi</label>
                    <select name="kondisi" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        <option value="baik"  {{ ($params['kondisi'] ?? '') === 'baik'  ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ ($params['kondisi'] ?? '') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <label class="form-label form-label-sm mb-1">Tgl Kembali</label>
                    <input type="date" name="tgl_kembali" class="form-control form-control-sm"
                           value="{{ $params['tgl_kembali'] ?? '' }}">
                </div>
                <div class="col-sm-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                    <a href="{{ route('unit.pengembalian_kantong.index') }}"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>No. Kembali</th>
                            <th>Tgl Kembali</th>
                            <th>No. Kantong</th>
                            <th>Merk / Jenis</th>
                            <th>Ukuran</th>
                            <th class="text-center">Kondisi</th>
                            <th>Keterangan</th>
                            <th class="text-center">Detail</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $row)
                        <tr>
                            <td class="ps-3 text-muted">{{ $data->firstItem() + $i }}</td>
                            <td><code class="text-primary fw-semibold">{{ $row->no_kembali }}</code></td>
                            <td>{{ \Carbon\Carbon::parse($row->tgl_kembali)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $row->no_kantong }}</span></td>
                            <td>
                                <span class="fw-semibold">{{ $row->merk ?? '-' }}</span>
                                @if($row->jenis)
                                    <br><small class="text-muted">{{ $row->jenis }}</small>
                                @endif
                            </td>
                            <td>{{ $row->ukuran ?? '-' }}</td>
                            <td class="text-center">
                                @if($row->kondisi === 'baik')
                                    <span class="badge bg-success">Baik</span>
                                @else
                                    <span class="badge bg-danger">Rusak</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($row->keterangan, 35) ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                @if($row->details && $row->details->count())
                                    <button type="button"
                                            class="badge bg-info text-dark border-0 btn-detail"
                                            data-id="{{ $row->id }}"
                                            title="Lihat detail">
                                        {{ $row->details->count() }} item
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group btn-group-sm">
                                    <button type="button"
                                            class="btn btn-outline-info btn-lihat"
                                            data-id="{{ $row->id }}"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('unit.pengembalian_kantong.edit', $row->id) }}"
                                       class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-hapus"
                                            data-id="{{ $row->id }}"
                                            data-no="{{ $row->no_kembali }}"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-25"></i>
                                Tidak ada data pengembalian kantong.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($data->hasPages())
        <div class="card-footer py-2">
            {{ $data->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── Hidden form hapus ── --}}
<form id="form-hapus" method="POST" action="">
    @csrf @method('DELETE')
</form>

{{-- ── Modal Detail ── --}}
<div class="modal fade" id="modal-detail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-info-circle me-1 text-primary"></i>
                    Detail Pengembalian Kantong
                </h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <div class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <div class="mt-2 text-muted small">Memuat data...</div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <a href="#" id="btn-modal-edit" class="btn btn-sm btn-warning">
                    <i class="fas fa-pencil-alt me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Hapus ────────────────────────────────────────────────────────────────────
document.querySelectorAll('.btn-hapus').forEach(btn => {
    btn.addEventListener('click', function () {
        if (confirm(`Hapus pengembalian "${this.dataset.no}"? Data tidak dapat dipulihkan.`)) {
            const form = document.getElementById('form-hapus');
            form.action = `{{ url('unit/pengembalian_kantong') }}/${this.dataset.id}`;
            form.submit();
        }
    });
});

// ── Modal Detail ─────────────────────────────────────────────────────────────
function openDetail(id) {
    const modal   = new bootstrap.Modal(document.getElementById('modal-detail'));
    const body    = document.getElementById('modal-body-content');
    const editBtn = document.getElementById('btn-modal-edit');

    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <div class="mt-2 text-muted small">Memuat data...</div>
        </div>`;
    editBtn.href = `{{ url('unit/pengembalian_kantong') }}/${id}/edit`;
    modal.show();

    fetch(`{{ url('unit/pengembalian_kantong') }}/${id}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(d => {
        const kondisiBadge = d.kondisi === 'baik'
            ? '<span class="badge bg-success">Baik</span>'
            : '<span class="badge bg-danger">Rusak</span>';

        let detailRows = '';
        let totalJumlah = 0;
        if (d.details && d.details.length) {
            d.details.forEach((det, i) => {
                totalJumlah += det.jumlah;
                detailRows += `
                    <tr>
                        <td class="text-muted">${i + 1}</td>
                        <td>${det.tipe_kantong ? det.tipe_kantong.nama : '-'}</td>
                        <td class="text-center fw-bold">${det.jumlah.toLocaleString('id-ID')}</td>
                        <td class="text-center">
                            <span class="badge bg-${det.flag ? 'warning text-dark' : 'secondary'}">${det.flag}</span>
                        </td>
                    </tr>`;
            });
            detailRows += `
                <tr class="table-light fw-semibold">
                    <td colspan="2" class="text-end">Total:</td>
                    <td class="text-center text-primary">${totalJumlah.toLocaleString('id-ID')}</td>
                    <td></td>
                </tr>`;
        } else {
            detailRows = `<tr><td colspan="4" class="text-center text-muted">Tidak ada detail tipe kantong.</td></tr>`;
        }

        body.innerHTML = `
            <div class="row g-0 border rounded mb-3 overflow-hidden">
                ${infoRow('No. Kembali', `<code class="text-primary fw-semibold">${d.no_kembali}</code>`)}
                ${infoRow('Tgl Kembali', d.tgl_kembali_fmt)}
                ${infoRow('No. Kantong', `<span class="badge bg-light text-dark border">${d.no_kantong}</span>`)}
                ${infoRow('Merk', d.merk || '-')}
                ${infoRow('Jenis', d.jenis || '-')}
                ${infoRow('Tipe', d.tipe || '-')}
                ${infoRow('Ukuran', d.ukuran || '-')}
                ${infoRow('Kondisi', kondisiBadge)}
                ${infoRow('Keterangan', d.keterangan || '-', true)}
            </div>
            <p class="fw-semibold mb-2"><i class="fas fa-list me-1 text-primary"></i>Detail Tipe Kantong</p>
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Tipe Kantong</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Flag</th>
                    </tr>
                </thead>
                <tbody>${detailRows}</tbody>
            </table>`;
    })
    .catch(() => {
        body.innerHTML = `<div class="alert alert-danger">Gagal memuat data.</div>`;
    });
}

function infoRow(label, value, full = false) {
    const cls = full ? 'col-12' : 'col-sm-6';
    return `<div class="${cls} border-bottom px-3 py-2">
        <small class="text-muted d-block">${label}</small>
        <span class="fw-semibold">${value}</span>
    </div>`;
}

document.querySelectorAll('.btn-lihat, .btn-detail').forEach(btn => {
    btn.addEventListener('click', () => openDetail(btn.dataset.id));
});
</script>
@endpush
@endsection