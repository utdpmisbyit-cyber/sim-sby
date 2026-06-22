@extends('layouts.index')

@section('title', 'Penyisihan Darah Rusak')

@section('content')
<div class="pny-wrap">

    <div class="pny-header">
        <div>
            <h1 class="pny-title">Penyisihan Darah Rusak</h1>
            <p class="pny-subtitle">Daftar transaksi penyisihan / pemusnahan kantong darah rusak</p>
        </div>
        <a href="{{ route('crossmatch.penyisihan_crossmatch.create') }}" class="pny-btn pny-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Penyisihan
        </a>
    </div>

    @if (session('success'))
        <div class="pny-alert pny-alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="pny-filter-card">
        <div class="pny-filter-field" style="flex:2;">
            <label>Cari</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="No. Penyisihan / Petugas">
        </div>
        <div class="pny-filter-field">
            <label>Tanggal Dari</label>
            <input type="date" name="tanggal_dari" value="{{ $filters['tanggal_dari'] ?? '' }}">
        </div>
        <div class="pny-filter-field">
            <label>Tanggal Sampai</label>
            <input type="date" name="tanggal_sampai" value="{{ $filters['tanggal_sampai'] ?? '' }}">
        </div>
        <div class="pny-filter-actions">
            <button type="submit" class="pny-btn pny-btn-outline">Filter</button>
            <a href="{{ route('crossmatch.penyisihan_crossmatch.index') }}" class="pny-btn pny-btn-ghost">Reset</a>
        </div>
    </form>

    <div class="pny-card">
        <table class="pny-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>No. Penyisihan</th>
                    <th>Tanggal</th>
                    <th>Petugas</th>
                    <th class="pny-center">Jumlah Kantong</th>
                    <th class="pny-center">Status</th>
                    <th class="pny-center" style="width:140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $i => $row)
                    <tr>
                        <td>{{ $items->firstItem() + $i }}</td>
                        <td><span class="pny-mono">{{ $row->no_penyisihan }}</span></td>
                        <td>{{ optional($row->tanggal_penyisihan)->format('d/m/Y') }}</td>
                        <td>{{ $row->petugas ?? '-' }}</td>
                        <td class="pny-center">
                            <span class="pny-badge pny-badge-neutral">{{ $row->details_count ?? $row->jumlah }}</span>
                        </td>
                        <td class="pny-center">
                            @if ($row->status === 'selesai')
                                <span class="pny-badge pny-badge-success">Selesai</span>
                            @else
                                <span class="pny-badge pny-badge-warning">Draft</span>
                            @endif
                        </td>
                        <td class="pny-center">
                            <div class="pny-action-group">
                                <a href="{{ route('crossmatch.penyisihan_crossmatch.edit', $row->id) }}" class="pny-icon-btn" title="Lihat / Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form action="{{ route('crossmatch.penyisihan_crossmatch.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data penyisihan ini? Tindakan tidak dapat dibatalkan.');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pny-icon-btn pny-icon-btn-danger" title="Hapus">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="pny-empty">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <p>Belum ada data penyisihan darah rusak.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($items->hasPages())
        <div class="pny-pagination">
            {{ $items->links() }}
        </div>
    @endif
</div>

<style>
    .pny-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px 48px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1f2937; }
    .pny-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom: 20px; flex-wrap: wrap; }
    .pny-title { font-size: 22px; font-weight: 700; margin: 0 0 2px; color:#111827; }
    .pny-subtitle { font-size: 13.5px; color:#6b7280; margin:0; }

    .pny-btn { display:inline-flex; align-items:center; gap:6px; padding: 9px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration:none; border:1px solid transparent; cursor:pointer; transition: background .15s ease, transform .05s ease; }
    .pny-btn:active { transform: scale(0.98); }
    .pny-btn-primary { background:#7c2d92; color:#fff; box-shadow: 0 1px 2px rgba(124,45,146,.3); }
    .pny-btn-primary:hover { background:#6a2680; }
    .pny-btn-outline { background:#fff; color:#374151; border-color:#d1d5db; }
    .pny-btn-outline:hover { background:#f9fafb; }
    .pny-btn-ghost { background:transparent; color:#6b7280; }
    .pny-btn-ghost:hover { background:#f3f4f6; }

    .pny-alert { padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 16px; }
    .pny-alert-success { background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; }

    .pny-filter-card { display:flex; gap:14px; background:#fff; border:1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 18px; flex-wrap: wrap; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
    .pny-filter-field { display:flex; flex-direction:column; gap:5px; min-width: 150px; flex:1; }
    .pny-filter-field label { font-size: 11.5px; font-weight:600; color:#6b7280; text-transform: uppercase; letter-spacing:.03em; }
    .pny-filter-field input { border:1px solid #d1d5db; border-radius:7px; padding: 8px 10px; font-size: 13.5px; outline:none; }
    .pny-filter-field input:focus { border-color:#7c2d92; box-shadow: 0 0 0 3px rgba(124,45,146,.12); }
    .pny-filter-actions { display:flex; align-items:flex-end; gap:8px; }

    .pny-card { background:#fff; border:1px solid #e5e7eb; border-radius: 12px; overflow:hidden; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
    .pny-table { width:100%; border-collapse: collapse; font-size: 13.5px; }
    .pny-table thead th { text-align:left; background:#f9fafb; color:#6b7280; font-size: 11.5px; text-transform: uppercase; letter-spacing:.03em; font-weight:700; padding: 11px 14px; border-bottom: 1px solid #e5e7eb; }
    .pny-table td { padding: 12px 14px; border-bottom: 1px solid #f1f2f4; vertical-align: middle; }
    .pny-table tbody tr:last-child td { border-bottom: none; }
    .pny-table tbody tr:hover { background:#fafafa; }
    .pny-center { text-align:center; }
    .pny-mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight:600; color:#111827; }

    .pny-badge { display:inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight:600; }
    .pny-badge-neutral { background:#f3f4f6; color:#374151; }
    .pny-badge-success { background:#ecfdf5; color:#047857; }
    .pny-badge-warning { background:#fffbeb; color:#b45309; }

    .pny-action-group { display:inline-flex; gap:6px; }
    .pny-icon-btn { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; color:#4b5563; cursor:pointer; text-decoration:none; }
    .pny-icon-btn:hover { background:#f3f4f6; }
    .pny-icon-btn-danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

    .pny-empty { display:flex; flex-direction:column; align-items:center; gap:10px; padding: 48px 0; color:#9ca3af; }
    .pny-empty p { margin:0; font-size: 13.5px; }

    .pny-pagination { margin-top: 18px; }
</style>
@endsection