@extends('layouts.index')

@section('title', 'Detail Pengembalian Darah — ' . $pengembalian->nomor_kembali)

@push('styles')
<style>
    :root {
        --clr-primary:    #1a56a4;
        --clr-primary-dk: #133f80;
        --clr-accent:     #e63946;
        --clr-success:    #2d8a4e;
        --clr-surface:    #f0f4fa;
        --clr-border:     #c7d4e8;
        --clr-text:       #1e2533;
        --clr-muted:      #6b7a99;
        --radius:         6px;
        --shadow-sm:      0 1px 4px rgba(0,0,0,.10);
        --shadow-md:      0 3px 12px rgba(0,0,0,.13);
    }
    .page-header {
        background: linear-gradient(135deg, var(--clr-primary), var(--clr-primary-dk));
        color: #fff; padding: 12px 18px; border-radius: var(--radius);
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; box-shadow: var(--shadow-md);
    }
    .page-header h4 { margin:0; font-size:.95rem; font-weight:700; display:flex; align-items:center; gap:8px; }

    .info-card {
        background:#fff; border:1px solid var(--clr-border); border-radius:var(--radius);
        margin-bottom:14px; box-shadow:var(--shadow-sm); overflow:hidden;
    }
    .info-card .card-head {
        background: var(--clr-surface); border-bottom:1px solid var(--clr-border);
        padding:8px 14px; font-size:.77rem; font-weight:700;
        color:var(--clr-primary); letter-spacing:.4px; text-transform:uppercase;
        display:flex; align-items:center; gap:7px;
    }
    .info-card .card-body-inner { padding:14px 16px; }

    .info-row { display:flex; flex-wrap:wrap; gap:0; }
    .info-col { flex: 1 1 200px; padding: 6px 12px; border-right:1px solid #edf2fb; }
    .info-col:last-child { border-right:none; }
    .info-col .label { font-size:.72rem; font-weight:600; color:var(--clr-muted); margin-bottom:2px; text-transform:uppercase; letter-spacing:.3px; }
    .info-col .value { font-size:.83rem; color:var(--clr-text); font-weight:500; }
    .info-col .value.mono { font-family:'Courier New',monospace; font-size:.85rem; color:var(--clr-primary-dk); }

    .badge-baik       { background:#d1f0de; color:#1a6636; border:1px solid #a4dbb9; }
    .badge-rusak      { background:#fde0e0; color:#9b1c1c; border:1px solid #f5a7a7; }
    .badge-kadaluarsa { background:#fef3cd; color:#7d5a00; border:1px solid #f0d080; }
    .status-badge { font-size:.74rem; font-weight:600; padding:3px 11px; border-radius:20px; display:inline-block; }

    .tbl-detail { font-size:.78rem; margin:0; }
    .tbl-detail thead th { background:#2c4a7c; color:#fff; font-size:.72rem; font-weight:600; padding:7px 8px; border:none; }
    .tbl-detail tbody td { padding:6px 8px; vertical-align:middle; border-color:#e5ecf7; }
    .tbl-detail tbody tr:nth-child(even) td { background:#f7f9fd; }
    .no-stock-pill {
        font-family:'Courier New',monospace; font-size:.76rem;
        background:#eef3fc; border:1px solid var(--clr-border);
        border-radius:4px; padding:1px 7px; color:var(--clr-primary-dk);
    }
    .action-bar {
        background:var(--clr-surface); border:1px solid var(--clr-border);
        border-radius:var(--radius); padding:10px 16px;
        display:flex; align-items:center; gap:10px;
    }
    .btn-back { background:#fff; color:var(--clr-muted); border:1px solid var(--clr-border); border-radius:5px; padding:6px 16px; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-back:hover { background:#f0f4fa; color:var(--clr-text); }
    .btn-edit { background:var(--clr-primary); color:#fff; border:none; border-radius:5px; padding:6px 16px; font-size:.82rem; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-edit:hover { background:var(--clr-primary-dk); color:#fff; }
    .btn-del-page { background:var(--clr-accent); color:#fff; border:none; border-radius:5px; padding:6px 16px; font-size:.82rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; }
    .btn-del-page:hover { opacity:.88; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="page-header">
        <h4><i class="fas fa-exchange-alt"></i> Detail Pengembalian Darah</h4>
        <span class="badge bg-light text-primary fw-bold" style="font-family:'Courier New',monospace;font-size:.85rem;letter-spacing:.5px">
            {{ $pengembalian->nomor_kembali }}
        </span>
    </div>

    {{-- Informasi Dokumen --}}
    <div class="info-card">
        <div class="card-head"><i class="fas fa-hashtag"></i> Informasi Dokumen</div>
        <div class="card-body-inner p-0">
            <div class="info-row">
                <div class="info-col">
                    <div class="label">Nomor Kembali</div>
                    <div class="value mono">{{ $pengembalian->nomor_kembali }}</div>
                </div>
                <div class="info-col">
                    <div class="label">Tanggal Kembali</div>
                    <div class="value">{{ $pengembalian->tanggal_kembali_formatted }}</div>
                </div>
                <div class="info-col">
                    <div class="label">Status Kembali</div>
                    <div class="value">
                        @php
                            $bc = match($pengembalian->status_kembali) {
                                'Baik'       => 'badge-baik',
                                'Rusak'      => 'badge-rusak',
                                'Kadaluarsa' => 'badge-kadaluarsa',
                                default      => '',
                            };
                        @endphp
                        <span class="status-badge {{ $bc }}">{{ $pengembalian->status_kembali }}</span>
                    </div>
                </div>
                <div class="info-col">
                    <div class="label">Yang Mengembalikan</div>
                    <div class="value">{{ $pengembalian->yang_mengembalikan ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Petugas --}}
    <div class="info-card">
        <div class="card-head"><i class="fas fa-user-md"></i> Petugas Penerima</div>
        <div class="card-body-inner p-0">
            <div class="info-row">
                <div class="info-col">
                    <div class="label">Kode Petugas</div>
                    <div class="value mono">{{ $pengembalian->kode_petugas ?? '-' }}</div>
                </div>
                <div class="info-col">
                    <div class="label">Nama Petugas</div>
                    <div class="value">{{ $pengembalian->nama_petugas ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FPUP / RS --}}
    <div class="info-card">
        <div class="card-head"><i class="fas fa-file-medical"></i> Data FPUP / Pasien</div>
        <div class="card-body-inner p-0">
            <div class="info-row">
                <div class="info-col">
                    <div class="label">No. FPUP</div>
                    <div class="value mono">{{ $pengembalian->no_fpup ?? '-' }}</div>
                </div>
                <div class="info-col">
                    <div class="label">Tgl. FPUP</div>
                    <div class="value">{{ $pengembalian->tgl_fpup_formatted }}</div>
                </div>
                <div class="info-col">
                    <div class="label">No. Stock</div>
                    <div class="value mono">{{ $pengembalian->no_stock ?? '-' }}</div>
                </div>
                <div class="info-col">
                    <div class="label">Rumah Sakit</div>
                    <div class="value">
                        @if($pengembalian->kode_rumah_sakit)
                            <span class="text-muted" style="font-size:.75rem">{{ $pengembalian->kode_rumah_sakit }}</span> —
                        @endif
                        {{ $pengembalian->nama_rumah_sakit ?? '-' }}
                    </div>
                </div>
                <div class="info-col">
                    <div class="label">Tgl. Pemberian</div>
                    <div class="value">{{ $pengembalian->tgl_pemberian ? $pengembalian->tgl_pemberian->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="info-col">
                    <div class="label">Umur (Hari) Pemberian</div>
                    <div class="value">{{ $pengembalian->umur_hari_pemberian ?? '-' }}</div>
                </div>
            </div>
            @if($pengembalian->alasan_kembali)
            <div class="px-3 py-2 border-top" style="font-size:.8rem">
                <span class="text-muted fw-semibold">Alasan Kembali: </span> {{ $pengembalian->alasan_kembali }}
            </div>
            @endif
            @if($pengembalian->keterangan)
            <div class="px-3 py-2 border-top" style="font-size:.8rem">
                <span class="text-muted fw-semibold">Keterangan: </span> {{ $pengembalian->keterangan }}
            </div>
            @endif
        </div>
    </div>

    {{-- Detail Stock --}}
    <div class="info-card">
        <div class="card-head" style="justify-content:space-between">
            <span><i class="fas fa-tint"></i> Detail Stock Kantong Darah</span>
            <span class="text-muted" style="font-weight:400;font-size:.72rem">{{ $pengembalian->details->count() }} item</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered tbl-detail">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. Stock</th>
                        <th>Jns Darah</th>
                        <th>Gol</th>
                        <th>Rhesus</th>
                        <th>Sts</th>
                        <th>Status Kembali</th>
                        <th>Alasan Kembali</th>
                        <th>Tgl. Aftap</th>
                        <th>Kadaluarsa</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengembalian->details as $i => $d)
                    <tr>
                        <td class="text-center text-muted">{{ $i + 1 }}</td>
                        <td><span class="no-stock-pill">{{ $d->no_stock }}</span></td>
                        <td>{{ $d->jenis_darah ?? '-' }}</td>
                        <td class="text-center fw-bold">{{ $d->gol_darah ?? '-' }}</td>
                        <td class="text-center">{{ $d->rhesus ?? '-' }}</td>
                        <td class="text-center">{{ $d->sts ?? '-' }}</td>
                        <td>
                            @php $bc2 = match($d->status_kembali) { 'Baik'=>'badge-baik','Rusak'=>'badge-rusak','Kadaluarsa'=>'badge-kadaluarsa',default=>'' }; @endphp
                            <span class="status-badge {{ $bc2 }}">{{ $d->status_kembali }}</span>
                        </td>
                        <td>{{ $d->alasan_kembali ?? '-' }}</td>
                        <td>{{ $d->tgl_aftap_formatted }}</td>
                        <td>{{ $d->kadaluarsa_formatted }}</td>
                        <td class="text-center">{{ $d->jumlah }}</td>
                        <td>{{ $d->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="text-center text-muted py-4" style="font-size:.8rem">Tidak ada detail stock.</td></tr>
                    @endforelse
                </tbody>
                @if($pengembalian->details->count())
                <tfoot>
                    <tr style="background:#eef3fc;font-size:.78rem;font-weight:600">
                        <td colspan="10" class="text-end pe-3">Total Jumlah:</td>
                        <td class="text-center">{{ $pengembalian->details->sum('jumlah') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Action bar --}}
    <div class="action-bar">
        <a href="{{ route('referal.pengembalian_darah.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('referal.pengembalian_darah.edit', $pengembalian) }}" class="btn-edit">
            <i class="fas fa-edit"></i> Edit
        </a>
        <button type="button" class="btn-del-page"
                onclick="confirmDelete({{ $pengembalian->id }}, '{{ $pengembalian->nomor_kembali }}')">
            <i class="fas fa-trash-alt"></i> Hapus
        </button>
        <form id="del-{{ $pengembalian->id }}"
              action="{{ route('referal.pengembalian_darah.destroy', $pengembalian) }}"
              method="POST" class="d-none">
            @csrf @method('DELETE')
        </form>
    </div>
</div>

{{-- Delete modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius:8px;overflow:hidden">
            <div class="modal-header py-2" style="background:#fdeaea;border-bottom:1px solid #f5b0b0">
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
document.getElementById('confirmDelBtn').addEventListener('click', () => {
    if (_delId) document.getElementById('del-' + _delId).submit();
});
</script>
@endpush