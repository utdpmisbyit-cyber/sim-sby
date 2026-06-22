@extends('layouts.index')

@section('title', 'Pengembalian Darah Crossmatch')

@push('styles')
<style>
    /* ── Root tokens ─────────────────────────────────────── */
    :root {
        --clr-primary:    #1a56a4;
        --clr-primary-dk: #133f80;
        --clr-accent:     #e63946;
        --clr-success:    #2d8a4e;
        --clr-warning:    #c08a00;
        --clr-surface:    #f0f4fa;
        --clr-border:     #c7d4e8;
        --clr-text:       #1e2533;
        --clr-muted:      #6b7a99;
        --radius:         6px;
        --shadow-sm:      0 1px 4px rgba(0,0,0,.10);
        --shadow-md:      0 3px 12px rgba(0,0,0,.13);
    }

    /* ── Page header ─────────────────────────────────────── */
    .page-header {
        background: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-primary-dk) 100%);
        color: #fff;
        padding: 14px 20px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        box-shadow: var(--shadow-md);
    }
    .page-header h4 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: .3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .page-header h4 i { font-size: 1.1rem; opacity: .85; }

    /* ── Filter card ─────────────────────────────────────── */
    .filter-card {
        background: #fff;
        border: 1px solid var(--clr-border);
        border-radius: var(--radius);
        padding: 14px 16px;
        margin-bottom: 16px;
        box-shadow: var(--shadow-sm);
    }
    .filter-card .row { --bs-gutter-x: 10px; }
    .filter-card label { font-size: .78rem; font-weight: 600; color: var(--clr-muted); margin-bottom: 3px; }
    .filter-card .form-control,
    .filter-card .form-select { font-size: .82rem; padding: 5px 9px; border-color: var(--clr-border); }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus { border-color: var(--clr-primary); box-shadow: 0 0 0 2px rgba(26,86,164,.18); }

    /* ── Table card ──────────────────────────────────────── */
    .table-card {
        background: #fff;
        border: 1px solid var(--clr-border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .table-card .card-header {
        background: var(--clr-surface);
        border-bottom: 1px solid var(--clr-border);
        padding: 9px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: .82rem;
        font-weight: 600;
        color: var(--clr-text);
    }

    /* ── Data table ──────────────────────────────────────── */
    .tbl-main { font-size: .8rem; margin: 0; }
    .tbl-main thead th {
        background: var(--clr-primary);
        color: #fff;
        font-weight: 600;
        font-size: .75rem;
        letter-spacing: .3px;
        padding: 8px 10px;
        white-space: nowrap;
        border: none;
        position: sticky;
        top: 0;
    }
    .tbl-main tbody tr { transition: background .12s; }
    .tbl-main tbody tr:hover { background: #eef3fc; }
    .tbl-main tbody td {
        padding: 7px 10px;
        vertical-align: middle;
        border-color: #e5ecf7;
        color: var(--clr-text);
    }
    .tbl-main tbody tr:nth-child(even) td { background: #f7f9fd; }
    .tbl-main tbody tr:nth-child(even):hover td { background: #eef3fc; }

    /* ── Badges ──────────────────────────────────────────── */
    .badge-baik       { background: #d1f0de; color: #1a6636; border: 1px solid #a4dbb9; }
    .badge-rusak      { background: #fde0e0; color: #9b1c1c; border: 1px solid #f5a7a7; }
    .badge-kadaluarsa { background: #fef3cd; color: #7d5a00; border: 1px solid #f0d080; }
    .status-badge {
        font-size: .72rem;
        font-weight: 600;
        padding: 2px 9px;
        border-radius: 20px;
        display: inline-block;
    }

    /* ── Action buttons ──────────────────────────────────── */
    .btn-act {
        padding: 3px 8px;
        font-size: .74rem;
        border-radius: 4px;
        border: 1px solid transparent;
        cursor: pointer;
        line-height: 1.5;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }
    .btn-act-edit   { background: #e8f1ff; color: var(--clr-primary); border-color: #b0ccf5; }
    .btn-act-edit:hover { background: var(--clr-primary); color: #fff; }
    .btn-act-show   { background: #e8f7ee; color: var(--clr-success); border-color: #9ddab8; }
    .btn-act-show:hover { background: var(--clr-success); color: #fff; }
    .btn-act-del    { background: #fdeaea; color: var(--clr-accent); border-color: #f5b0b0; }
    .btn-act-del:hover { background: var(--clr-accent); color: #fff; }

    /* ── Pagination override ─────────────────────────────── */
    .pagination { font-size: .8rem; margin-bottom: 0; }
    .page-link  { color: var(--clr-primary); border-color: var(--clr-border); padding: 4px 10px; }
    .page-item.active .page-link { background: var(--clr-primary); border-color: var(--clr-primary); }

    /* ── Empty state ─────────────────────────────────────── */
    .empty-state { padding: 48px 20px; text-align: center; color: var(--clr-muted); }
    .empty-state i { font-size: 2.4rem; display: block; margin-bottom: 10px; opacity: .45; }
    .empty-state p { font-size: .85rem; margin: 0; }

    /* ── No-stock column ─────────────────────────────────── */
    .no-stock-pill {
        font-family: 'Courier New', monospace;
        font-size: .76rem;
        background: #eef3fc;
        border: 1px solid #c7d4e8;
        border-radius: 4px;
        padding: 1px 7px;
        color: var(--clr-primary-dk);
        letter-spacing: .3px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- ── Alerts ──────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="font-size:.83rem">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert" style="font-size:.83rem">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Page header ─────────────────────────────────────────── --}}
    <div class="page-header">
        <h4>
            <i class="fas fa-exchange-alt"></i>
            Pengembalian Darah Crossmatch
        </h4>
        <a href="{{ route('crossmatch.pengembalian_darah.create') }}" class="btn btn-sm btn-light fw-semibold" style="font-size:.8rem">
            <i class="fas fa-plus me-1"></i> Tambah Baru
        </a>
    </div>

    {{-- ── Filter form ──────────────────────────────────────────── --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('crossmatch.pengembalian_darah.index') }}" id="filterForm">
            <div class="row align-items-end g-2">
                <div class="col-md-3">
                    <label>Cari (No. Kembali / FPUP / RS)</label>
                    <input type="text" name="search" class="form-control" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Ketik lalu Enter…">
                </div>
                <div class="col-md-2">
                    <label>Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" class="form-control" value="{{ $filters['tanggal_dari'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label>Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" class="form-control" value="{{ $filters['tanggal_sampai'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status_kembali" class="form-select">
                        <option value="">— Semua —</option>
                        @foreach(['Baik','Rusak','Kadaluarsa'] as $st)
                            <option value="{{ $st }}" {{ ($filters['status_kembali'] ?? '') === $st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-3" style="font-size:.8rem">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('crossmatch.pengembalian_darah.index') }}" class="btn btn-outline-secondary btn-sm px-3" style="font-size:.8rem">
                        <i class="fas fa-times me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ── Table card ───────────────────────────────────────────── --}}
    <div class="table-card">
        <div class="card-header">
            <span><i class="fas fa-list me-1 text-primary"></i> Daftar Pengembalian Darah</span>
            <span class="text-muted" style="font-weight:400">Total: {{ $list->total() }} data</span>
        </div>

        <div class="table-responsive" style="max-height:520px; overflow-y:auto;">
            <table class="table table-bordered tbl-main">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>No. Kembali</th>
                        <th>Tgl. Kembali</th>
                        <th>Petugas Terima</th>
                        <th>No. FPUP</th>
                        <th>Tgl. FPUP</th>
                        <th>Rumah Sakit</th>
                        <th>Alasan Kembali</th>
                        <th>Status</th>
                        <th>Tgl. Pemberian</th>
                        <th style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($list as $i => $row)
                    <tr>
                        <td class="text-center text-muted">{{ $list->firstItem() + $i }}</td>
                        <td>
                            <span class="no-stock-pill">{{ $row->nomor_kembali }}</span>
                        </td>
                        <td>{{ $row->tanggal_kembali_formatted }}</td>
                        <td>
                            @if($row->kode_petugas)
                                <small class="text-muted">{{ $row->kode_petugas }}</small><br>
                            @endif
                            {{ $row->nama_petugas ?? '-' }}
                        </td>
                        <td>
                            <a href="#" class="text-primary fw-semibold text-decoration-none">
                                {{ $row->no_fpup ?? '-' }}
                            </a>
                        </td>
                        <td>{{ $row->tgl_fpup_formatted }}</td>
                        <td>
                            @if($row->kode_rumah_sakit)
                                <small class="text-muted">{{ $row->kode_rumah_sakit }}</small>
                                {{ $row->nama_rumah_sakit }}
                            @else
                                {{ $row->nama_rumah_sakit ?? '-' }}
                            @endif
                        </td>
                        <td style="max-width:180px">
                            <span title="{{ $row->alasan_kembali }}">
                                {{ Str::limit($row->alasan_kembali, 40, '…') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $badgeCls = match($row->status_kembali) {
                                    'Baik'       => 'badge-baik',
                                    'Rusak'      => 'badge-rusak',
                                    'Kadaluarsa' => 'badge-kadaluarsa',
                                    default      => '',
                                };
                            @endphp
                            <span class="status-badge {{ $badgeCls }}">{{ $row->status_kembali }}</span>
                        </td>
                        <td>
                            {{ $row->tgl_pemberian ? $row->tgl_pemberian->format('d/m/Y') : '-' }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('crossmatch.pengembalian_darah.show', $row) }}" class="btn-act btn-act-show">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('crossmatch.pengembalian_darah.edit', $row) }}" class="btn-act btn-act-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn-act btn-act-del"
                                    onclick="confirmDelete({{ $row->id }}, '{{ $row->nomor_kembali }}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <form id="del-{{ $row->id }}"
                                  action="{{ route('crossmatch.pengembalian_darah.destroy', $row) }}"
                                  method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada data pengembalian darah.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($list->hasPages())
        <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center"
             style="background:#f7f9fd">
            <small class="text-muted">
                Menampilkan {{ $list->firstItem() }}–{{ $list->lastItem() }}
                dari {{ $list->total() }} data
            </small>
            {{ $list->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── Delete confirmation modal ────────────────────────────────── --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:8px; overflow:hidden">
            <div class="modal-header py-2" style="background:#fdeaea; border-bottom:1px solid #f5b0b0">
                <h6 class="modal-title text-danger mb-0" style="font-size:.85rem">
                    <i class="fas fa-exclamation-triangle me-1"></i> Hapus Data
                </h6>
            </div>
            <div class="modal-body py-3" style="font-size:.83rem">
                Hapus pengembalian <strong id="delNomor"></strong>?<br>
                <span class="text-muted">Tindakan ini tidak dapat dibatalkan.</span>
            </div>
            <div class="modal-footer py-2 d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDelBtn">
                    <i class="fas fa-trash-alt me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let _delId = null;
function confirmDelete(id, nomor) {
    _delId = id;
    document.getElementById('delNomor').textContent = nomor;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
document.getElementById('confirmDelBtn').addEventListener('click', function () {
    if (_delId) document.getElementById('del-' + _delId).submit();
});
</script>
@endpush