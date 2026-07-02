
@extends('layouts.index')

@section('title', 'Pembayaran Dropping External')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    :root{
        --pde-maroon:#A11D33;
        --pde-maroon-dark:#7E1527;
        --pde-ink:#1F2430;
        --pde-bg:#F7F5F3;
        --pde-line:#E7E1DC;
        --pde-success:#1F8A57;
        --pde-amber:#B5750A;
        --pde-muted:#6B7280;
    }
    .pde-wrap{ font-family:'Inter',sans-serif; color:var(--pde-ink); }
    .pde-head{ display:flex; align-items:flex-end; justify-content:space-between; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap; }
    .pde-head h1{ font-family:'Poppins',sans-serif; font-weight:700; font-size:1.5rem; margin:0; }
    .pde-head .eyebrow{ font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:var(--pde-maroon); font-weight:600; }
    .pde-card{ background:#fff; border:1px solid var(--pde-line); border-radius:14px; box-shadow:0 1px 2px rgba(31,36,48,.04); }
    .pde-toolbar{ padding:1rem 1.25rem; border-bottom:1px solid var(--pde-line); display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; }
    .pde-toolbar .form-control, .pde-toolbar .form-select{ border-radius:10px; border-color:var(--pde-line); }
    .btn-pde-primary{ background:var(--pde-maroon); border-color:var(--pde-maroon); color:#fff; border-radius:10px; font-weight:600; }
    .btn-pde-primary:hover{ background:var(--pde-maroon-dark); border-color:var(--pde-maroon-dark); color:#fff; }
    table.pde-table{ width:100%; border-collapse:collapse; }
    table.pde-table thead th{
        font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--pde-muted);
        font-weight:600; border-bottom:1px solid var(--pde-line); padding:.85rem 1.1rem; white-space:nowrap;
        background:#FBFAF8;
    }
    table.pde-table tbody td{ padding:.85rem 1.1rem; border-bottom:1px solid var(--pde-line); font-size:.9rem; vertical-align:middle; }
    table.pde-table tbody tr:hover{ background:#FBF6F2; }
    .mono{ font-family:'JetBrains Mono', monospace; letter-spacing:.01em; }
    .badge-tunai{ background:#E9F6EE; color:var(--pde-success); border:1px solid #BFE7CE; }
    .badge-kredit{ background:#FCF1DE; color:var(--pde-amber); border:1px solid #F0DBAE; }
    .badge-batal{ background:#F4F4F5; color:var(--pde-muted); border:1px solid #E2E2E4; }
    .badge-status{ font-size:.72rem; font-weight:600; padding:.3rem .6rem; border-radius:999px; }
    .drop-dot{ display:inline-block; width:8px; height:8px; border-radius:50% 50% 50% 0; background:var(--pde-maroon); transform:rotate(45deg); margin-right:.5rem; }
    .pde-empty{ padding:3rem 1.5rem; text-align:center; color:var(--pde-muted); }
</style>
@endpush

@section('content')
<div class="pde-wrap container-fluid py-4">

    <div class="pde-head">
        <div>
            <div class="eyebrow">Keuangan &middot; Bank Darah Eksternal</div>
            <h1>Penerimaan Pembayaran Dropping External</h1>
        </div>
        <a href="{{ route('finance.pembayaran_dropping_external.create') }}" class="btn btn-pde-primary px-4">
            + Pembayaran Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="pde-card">
        <form method="GET" action="{{ route('finance.pembayaran_dropping_external.index') }}" class="pde-toolbar">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   class="form-control" style="max-width:260px"
                   placeholder="Cari nomor kirim / institusi tujuan...">
            <input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}" class="form-control" style="max-width:160px">
            <span class="text-muted small">s/d</span>
            <input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}" class="form-control" style="max-width:160px">
            <button class="btn btn-outline-secondary" type="submit">Filter</button>
            @if(($filters['search'] ?? null) || ($filters['dari'] ?? null) || ($filters['sampai'] ?? null))
                <a href="{{ route('finance.pembayaran_dropping_external.index') }}" class="btn btn-link text-muted">Reset</a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="pde-table">
                <thead>
                    <tr>
                        <th>Nomor Kirim</th>
                        <th>Tgl Bayar</th>
                        <th>Tujuan Darah</th>
                        <th>Jenis Biaya</th>
                        <th class="text-end">Harus Dibayar</th>
                        <th class="text-end">Pembayaran</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Kasir</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pembayarans as $p)
                    <tr>
                        <td class="mono fw-semibold">{{ $p->nomor_kirim }}</td>
                        <td>{{ optional($p->tanggal_bayar)->format('d/m/Y H:i') }}</td>
                        <td>{{ $p->institusi_tujuan ?? '-' }}</td>
                        <td><span class="drop-dot"></span>{{ $p->jenis_biaya }}</td>
                        <td class="text-end mono">{{ number_format($p->harus_dibayar, 0, ',', '.') }}</td>
                        <td class="text-end mono">{{ number_format($p->pembayaran, 0, ',', '.') }}</td>
                        <td class="text-capitalize">{{ $p->metode_bayar }}</td>
                        <td>
                            @php
                                $badge = match($p->status) {
                                    'lunas' => 'badge-tunai',
                                    'belum_lunas' => 'badge-kredit',
                                    default => 'badge-batal',
                                };
                            @endphp
                            <span class="badge-status {{ $badge }}">{{ str_replace('_',' ', $p->status) }}</span>
                        </td>
                        <td>{{ $p->nama_kasir }}</td>
                        <td class="text-end">
                            <a href="{{ route('finance.pembayaran_dropping_external.edit', $p) }}"
                               class="btn btn-sm btn-outline-secondary">Lihat / Edit</a>
                            <button type="button" class="btn btn-sm btn-outline-danger pde-delete"
                                    data-url="{{ route('finance.pembayaran_dropping_external.destroy', $p) }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            <div class="pde-empty">
                                Belum ada transaksi pembayaran dropping. Klik <strong>"+ Pembayaran Baru"</strong> untuk mulai scan nomor kirim.
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-3 py-3">
            {{ $pembayarans->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.pde-delete');
    if (!btn) return;

    Swal.fire({
        title: 'Hapus data ini?',
        text: 'Transaksi pembayaran yang sudah dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#A11D33',
    }).then((res) => {
        if (!res.isConfirmed) return;

        fetch(btn.dataset.url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(() => location.reload())
        .catch(() => Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error'));
    });
});
</script>
@endpush
