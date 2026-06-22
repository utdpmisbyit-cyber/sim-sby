@extends('layouts.index')

@section('title', 'Permintaan Darah Referal')

@push('styles')
<style>
    :root { --utd-red: #c0392b; --utd-dark: #1a1a2e; --utd-blue: #2980b9; --utd-gray: #7f8c8d; --utd-border: #e8ecef; }
    * { box-sizing: border-box; }
    body { background: #f4f6f9; font-family: 'Segoe UI', system-ui, sans-serif; }
    
    .top-header {
        height: 64px;
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #c0392b 100%);
        display: flex; align-items: center; padding: 0 1.5rem; gap: .75rem;
        position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 12px rgba(0,0,0,.35);
    }
    .top-header .brand { display: flex; align-items: center; gap: .6rem; color: #fff; text-decoration: none; }
    .top-header .brand .ico { width: 36px; height: 36px; background: rgba(255,255,255,.15); border-radius: 8px; display: grid; place-items: center; font-size: 1.15rem; }
    .top-header .brand .title small { font-size: .63rem; opacity: .7; display: block; }
    .top-header .brand .title span { font-weight: 700; font-size: .92rem; }
    .top-header .spacer { flex: 1; }
    .btn-tambah { background: rgba(255,255,255,.18); color: #fff !important; border: 1px solid rgba(255,255,255,.35); border-radius: 8px; padding: .42rem 1.1rem; font-size: .82rem; font-weight: 600; display: flex; align-items: center; gap: .4rem; text-decoration: none; }
    .btn-tambah:hover { background: rgba(255,255,255,.3); }

    .page-body { padding: 1.4rem 1.5rem; }

    .stats-strip { display: grid; grid-template-columns: repeat(4, 1fr); gap: .8rem; margin-bottom: 1.25rem; }
    .stat-card { background: #fff; border-radius: 12px; border: 1px solid #e8ecef; padding: 1rem; display: flex; align-items: center; gap: .85rem; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .stat-ico { width: 44px; height: 44px; border-radius: 11px; display: grid; place-items: center; font-size: 1.2rem; flex-shrink: 0; }
    .stat-card .val { font-size: 1.55rem; font-weight: 800; color: #1a1a2e; }
    .stat-card .lbl { font-size: .72rem; color: #7f8c8d; margin-top: 3px; text-transform: uppercase; }
    .s-all .stat-ico { background: #fdecea; color: #c0392b; }
    .s-baru .stat-ico { background: #fef9e7; color: #e67e22; }
    .s-proses .stat-ico { background: #eaf4fb; color: #2980b9; }
    .s-selesai .stat-ico { background: #eafaf1; color: #27ae60; }

    .filter-bar { background: #fff; border: 1px solid #e8ecef; border-radius: 12px; padding: .85rem 1.1rem; display: flex; gap: .6rem; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .filter-bar .form-control, .filter-bar .form-select { font-size: .82rem; border-radius: 8px; border: 1px solid #dde1e7; height: 36px; background: #f8f9fa; }
    .filter-bar .form-control:focus, .filter-bar .form-select:focus { border-color: #c0392b; box-shadow: 0 0 0 .18rem rgba(192,57,43,.14); background: #fff; }
    .filter-bar .form-control { min-width: 220px; }
    .filter-bar .form-select { width: 150px; }
    .btn-cari { background: #c0392b; color: #fff; border: none; font-weight: 600; height: 36px; font-size: .82rem; border-radius: 8px; padding: 0 1rem; }
    .btn-cari:hover { background: #a93226; }

    .table-card { background: #fff; border: 1px solid #e8ecef; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,.04); }
    .table-card table { margin: 0; font-size: .82rem; }
    .table-card thead th { background: #f8f9fa; border-bottom: 2px solid #eaecef; font-weight: 700; font-size: .73rem; text-transform: uppercase; color: #6c757d; padding: .7rem .9rem; white-space: nowrap; }
    .table-card tbody td { padding: .62rem .9rem; vertical-align: middle; border-bottom: 1px solid #f4f6f8; color: #2c3e50; }
    .table-card tbody tr:hover { background: #fafbfc; }

    .no-ref-badge { font-family: 'Courier New', monospace; font-size: .78rem; font-weight: 700; color: #c0392b; background: #fdecea; padding: .15em .5em; border-radius: 5px; display: inline-block; }
    .bdg { font-size: .71rem; padding: .3em .7em; border-radius: 20px; font-weight: 700; display: inline-block; }
    .bdg-baru { background: #fef9e7; color: #d68910; }
    .bdg-proses { background: #eaf4fb; color: #1a5276; }
    .bdg-selesai { background: #eafaf1; color: #1e8449; }
    .bdg-pending { background: #f4f6f7; color: #7f8c8d; }
    .bdg-diterima { background: #eafaf1; color: #1e8449; }
    .bdg-ditolak { background: #fdecea; color: #c0392b; }
    .bdg-cito { background: #c0392b; color: #fff; }
    .bdg-biasa { background: #f0f3f4; color: #566573; }

    .action-btn { width: 30px; height: 30px; border-radius: 7px; border: 1px solid #dde1e7; background: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .82rem; color: #555; text-decoration: none; transition: all .15s; cursor: pointer; }
    .action-btn:hover { background: #fdecea; color: #c0392b; border-color: #c0392b; }
    .action-btn.del:hover { background: #c0392b; color: #fff; }

    .empty-state { text-align: center; padding: 4rem 1rem; color: #b0bec5; }
    .empty-state .empty-ico { width: 64px; height: 64px; background: #fdecea; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto .75rem; font-size: 1.6rem; color: #c0392b; }
    .pagi-footer { display: flex; justify-content: space-between; align-items: center; padding: .6rem 1rem; border-top: 1px solid #f0f2f4; font-size: .78rem; color: #7f8c8d; }
    .gol-pill { background: #c0392b; color: #fff; font-size: .75rem; font-weight: 700; padding: .2em .55em; border-radius: 5px; }

    @media (max-width: 992px) { .stats-strip { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .stats-strip { grid-template-columns: 1fr 1fr; } .filter-bar { flex-direction: column; align-items: stretch; } .filter-bar .form-control, .filter-bar .form-select { width: 100% !important; } }
</style>
@endpush




@section('content')
<div class="page-body">
    <header class="top-header">
    <a href="{{ route('referal.permintaan_fpup.index') }}" class="brand">
        <div class="ico"><i class="bi bi-droplet-fill"></i></div>
        <div class="title">
            <small>PASIEN SERVICE – UTD</small>
            <span>Permintaan Darah Referal</span>
        </div>
    </a>
    <div class="spacer"></div>
    <a href="{{ route('referal.permintaan_fpup.create') }}" class="btn-tambah">
        <i class="bi bi-plus-lg"></i> Tambah Referal
    </a>
</header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="stats-strip">
        <div class="stat-card s-all"><div class="stat-ico"><i class="bi bi-droplet-half"></i></div><div><div class="val">{{ number_format($stats['total']) }}</div><div class="lbl">Total</div></div></div>
        <div class="stat-card s-baru"><div class="stat-ico"><i class="bi bi-clock-history"></i></div><div><div class="val">{{ number_format($stats['baru']) }}</div><div class="lbl">Baru</div></div></div>
        <div class="stat-card s-proses"><div class="stat-ico"><i class="bi bi-arrow-repeat"></i></div><div><div class="val">{{ number_format($stats['proses']) }}</div><div class="lbl">Proses</div></div></div>
        <div class="stat-card s-selesai"><div class="stat-ico"><i class="bi bi-check2-circle"></i></div><div><div class="val">{{ number_format($stats['selesai']) }}</div><div class="lbl">Selesai</div></div></div>
    </div>

    <form method="GET" action="{{ route('referal.permintaan_fpup.index') }}" class="filter-bar">
        <i class="bi bi-funnel" style="color:#95a5a6"></i>
        <input type="search" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}" />
        <select name="status" class="form-select">
            <option value="">Semua Status</option>
            <option value="baru" @selected(request('status') === 'baru')>Baru</option>
            <option value="proses" @selected(request('status') === 'proses')>Proses</option>
            <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
        </select>
        <select name="status_referal" class="form-select">
            <option value="">Semua Referal</option>
            <option value="pending" @selected(request('status_referal') === 'pending')>Pending</option>
            <option value="diterima" @selected(request('status_referal') === 'diterima')>Diterima</option>
            <option value="ditolak" @selected(request('status_referal') === 'ditolak')>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-cari"><i class="bi bi-search me-1"></i>Cari</button>
        @if(request('search') || request('status') || request('status_referal'))
            <a href="{{ route('referal.permintaan_fpup.index') }}" class="btn btn-outline-secondary" style="height:36px;font-size:.82rem">Reset</a>
        @endif
    </form>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>No Referal</th>
                        <th>Tgl Minta</th>
                        <th>Nama Pasien</th>
                        <th>Rumah Sakit</th>
                        <th>Gol/Rh</th>
                        <th>Diagnosa</th>
                        <th>Status</th>
                        <th>Referal</th>
                        <th style="width:96px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $i => $row)
                    <tr>
                        <td class="text-muted" style="font-size:.75rem">{{ $items->firstItem() + $i }}</td>
                        <td><span class="no-ref-badge">{{ $row->no_referal ?? $row->no_fpup }}</span></td>
                        <td>{{ $row->tgl_minta?->format('d/m/Y') }}</td>
                        <td><strong>{{ $row->nama_pasien }}</strong></td>
                        <td>{{ $row->nama_rs ?? '–' }}</td>
                        <td>@if($row->gol_rh_os)<span class="gol-pill">{{ $row->gol_rh_os }}</span>@else–@endif</td>
                        <td><span style="font-size:.8rem">{{ Str::limit($row->diagnosa_klinis ?? '–', 25) }}</span></td>
                        <td>
                            @if($row->status === 'baru')<span class="bdg bdg-baru">Baru</span>
                            @elseif($row->status === 'proses')<span class="bdg bdg-proses">Proses</span>
                            @elseif($row->status === 'selesai')<span class="bdg bdg-selesai">Selesai</span>
                            @else<span class="bdg bdg-pending">–</span>@endif
                        </td>
                        <td>
                            @if($row->status_referal === 'diterima')<span class="bdg bdg-diterima">Diterima</span>
                            @elseif($row->status_referal === 'ditolak')<span class="bdg bdg-ditolak">Ditolak</span>
                            @else<span class="bdg bdg-pending">Pending</span>@endif
                        </td>
                        <td>
                            <a href="{{ route('referal.permintaan_fpup.show', $row->id) }}" class="action-btn" title="Lihat"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('referal.permintaan_fpup.edit', $row->id) }}" class="action-btn" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('referal.permintaan_fpup.destroy', $row->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn del"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10"><div class="empty-state"><div class="empty-ico">
                        <i class="bi bi-droplet"></i></div><p>Belum ada data</p>
                        <a href="{{ route('referal.permintaan_fpup.create') }}" class="btn btn-sm btn-cari">
                        <i class="bi bi-plus-lg me-1"></i>Tambah</a>
                    </div>
                </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="pagi-footer">
            <span>Halaman {{ $items->currentPage() }} dari {{ $items->lastPage() }}</span>
            {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection