@extends('layouts.index')

@section('title', 'Pemberian Awal Referal')

@section('content')
<style>
    .par-shell {
        --par-bg: #f1f5f9;
        --par-surface: #ffffff;
        --par-border: #e2e8f0;
        --par-ink: #1e293b;
        --par-muted: #64748b;
        --par-primary: #0f766e;
        --par-primary-dark: #0d5c56;
        --par-primary-soft: #ccfbf1;
        --par-blood: #be123c;
        --par-blood-soft: #ffe4e6;
        --par-radius: 14px;
        --par-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px -12px rgba(15,23,42,.10);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: var(--par-ink);
        background: var(--par-bg);
        padding: 28px 40px 56px;
        max-width: 100%;
        margin: 0 auto;
        box-sizing: border-box;
    }
    .par-shell *, .par-shell *::before, .par-shell *::after { box-sizing: border-box; }
    .par-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--par-primary); margin: 0 0 4px; }
    .par-title { font-size: 26px; font-weight: 800; margin: 0 0 4px; letter-spacing: -.01em; }
    .par-subtitle { font-size: 14px; color: var(--par-muted); margin: 0; }
    .par-headrow { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 22px; }

    .par-btn { display: inline-flex; align-items: center; gap: 6px; border: none; border-radius: 10px; padding: 10px 18px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; line-height: 1; white-space: nowrap; }
    .par-btn-primary { background: var(--par-primary); color: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.08); }
    .par-btn-primary:hover { background: var(--par-primary-dark); }
    .par-btn-secondary { background: #1e293b; color: #fff; }
    .par-btn-secondary:hover { background: #0f172a; }
    .par-btn-plus { font-size: 17px; font-weight: 800; line-height: 1; }

    .par-alert { border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 18px; border: 1px solid transparent; }
    .par-alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }

    .par-card { background: var(--par-surface); border: 1px solid var(--par-border); border-radius: var(--par-radius); box-shadow: var(--par-shadow); position: relative; overflow: hidden; margin-bottom: 22px; }
    .par-card::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--par-primary), #5eead4); }
    .par-card-pad { padding: 20px; }

    .par-filter { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; align-items: end; }
    @media (max-width: 880px) { .par-filter { grid-template-columns: repeat(2, 1fr); } }
    .par-field label { display: block; font-size: 12px; font-weight: 600; color: var(--par-muted); margin-bottom: 6px; }
    .par-field input, .par-field select { width: 100%; border: 1px solid var(--par-border); border-radius: 8px; padding: 9px 11px; font-size: 14px; color: var(--par-ink); background: #fff; }
    .par-field input:focus, .par-field select:focus { outline: none; border-color: var(--par-primary); box-shadow: 0 0 0 3px var(--par-primary-soft); }
    .par-filter-span2 { grid-column: span 2; }

    .par-table-wrap { overflow-x: auto; border-radius: var(--par-radius); }
    .par-table { width: 100%; border-collapse: collapse; font-size: 13.5px; min-width: 880px; }
    .par-table thead th { text-align: left; font-size: 11px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase; color: var(--par-muted); background: #f8fafc; padding: 12px 16px; border-bottom: 1px solid var(--par-border); white-space: nowrap; }
    .par-table tbody td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; white-space: nowrap; }
    .par-table tbody tr:hover { background: #f8fafc; }
    .par-table tbody tr:last-child td { border-bottom: none; }
    .par-cell-muted { color: var(--par-muted); font-size: 12.5px; }
    .par-cell-strong { font-weight: 600; color: var(--par-ink); }
    .par-empty-row td { text-align: center; color: var(--par-muted); padding: 48px 16px; white-space: normal; }

    .par-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    .par-badge-blood { background: var(--par-blood-soft); color: var(--par-blood); font-weight: 700; }
    .par-badge-draft { background: #f1f5f9; color: #475569; }
    .par-badge-diproses { background: #fff7ed; color: #c2410c; }
    .par-badge-selesai { background: #ecfdf5; color: #047857; }
    .par-badge-dibatalkan { background: #fef2f2; color: #b91c1c; }

    .par-row-actions { display: flex; justify-content: flex-end; gap: 4px; }
    .par-link-btn { border: none; background: transparent; font-size: 12.5px; font-weight: 600; padding: 6px 10px; border-radius: 6px; cursor: pointer; text-decoration: none; }
    .par-link-edit { color: var(--par-primary); }
    .par-link-edit:hover { background: var(--par-primary-soft); }
    .par-link-delete { color: var(--par-blood); }
    .par-link-delete:hover { background: var(--par-blood-soft); }

    .par-pager { display: flex; align-items: center; justify-content: center; gap: 14px; padding-top: 18px; }
    .par-pager-btn { border: 1px solid var(--par-border); background: #fff; color: var(--par-ink); font-size: 13px; font-weight: 600; padding: 8px 14px; border-radius: 8px; text-decoration: none; }
    .par-pager-btn:hover { background: #f8fafc; }
    .par-pager-btn.is-disabled { color: #cbd5e1; pointer-events: none; }
    .par-pager-info { font-size: 13px; color: var(--par-muted); }
</style>

<div class="par-shell">

    <div class="par-headrow">
        <div>
            <p class="par-eyebrow">Unit Donor Darah &middot; Referal</p>
            <h1 class="par-title">Pemberian Awal Referal</h1>
            <p class="par-subtitle">Catat pemberian kantong darah awal berdasarkan permintaan FPUP.</p>
        </div>
        <a href="{{ route('referal.pemberian_awal_referal.create') }}" class="par-btn par-btn-primary">
            <span class="par-btn-plus">+</span> Tambah Pemberian
        </a>
    </div>

    @if (session('success'))
        <div class="par-alert par-alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="par-card">
        <div class="par-card-pad">
            <div class="par-filter">
                <div class="par-field par-filter-span2">
                    <label>Cari</label>
                    <input type="text" name="cari" value="{{ $filters['cari'] ?? '' }}" placeholder="No Pemberian / No FPUP / Nama Pasien">
                </div>
                <div class="par-field">
                    <label>Status</label>
                    <select name="status">
                        <option value="">Semua</option>
                        @foreach (['draft' => 'Draft', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $label)
                            <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="par-field">
                    <label>Tgl Dari</label>
                    <input type="date" name="tgl_dari" value="{{ $filters['tgl_dari'] ?? '' }}">
                </div>
                <div class="par-field">
                    <label>Tgl Sampai</label>
                    <input type="date" name="tgl_sampai" value="{{ $filters['tgl_sampai'] ?? '' }}">
                </div>
            </div>
            <div style="margin-top:14px; display:flex; justify-content:flex-end;">
                <button class="par-btn par-btn-secondary">Filter</button>
            </div>
        </div>
    </form>

    <div class="par-card">
        <div class="par-table-wrap">
            <table class="par-table">
                <thead>
                    <tr>
                        <th>No Pemberian</th>
                        <th>No FPUP</th>
                        <th>Tgl FPUP</th>
                        <th>Pasien</th>
                        <th>Gol-Rh</th>
                        <th>RS Perujuk</th>
                        <th>Kantong</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td class="par-cell-strong">{{ $row->no_pemberian }}</td>
                            <td class="par-cell-muted">{{ $row->no_fpup }}</td>
                            <td class="par-cell-muted">{{ optional($row->tgl_fpup)->format('d-m-Y H:i') }}</td>
                            <td>
                                <div class="par-cell-strong">{{ $row->nama_pasien }}</div>
                                <div class="par-cell-muted">{{ $row->noktp_pasien }}</div>
                            </td>
                            <td><span class="par-badge par-badge-blood">{{ $row->gol_darah }} {{ Illuminate\Support\Str::substr($row->rhesus, 0, 1) }}</span></td>
                            <td class="par-cell-muted">{{ $row->nama_rs }}</td>
                            <td class="par-cell-strong">{{ $row->jumlah_kantong_per_seleksi }}</td>
                            <td class="par-cell-muted">Rp {{ number_format($row->total_biaya, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'draft' => 'par-badge-draft',
                                        'diproses' => 'par-badge-diproses',
                                        'selesai' => 'par-badge-selesai',
                                        'dibatalkan' => 'par-badge-dibatalkan',
                                    ][$row->status] ?? 'par-badge-draft';
                                @endphp
                                <span class="par-badge {{ $badgeClass }}">{{ ucfirst($row->status) }}</span>
                            </td>
                            <td>
                                <div class="par-row-actions">
                                    <a href="{{ route('referal.pemberian_awal_referal.edit', $row->id) }}" class="par-link-btn par-link-edit">Ubah</a>
                                    <form action="{{ route('referal.pemberian_awal_referal.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                        @csrf @method('DELETE')
                                        <button class="par-link-btn par-link-delete">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="par-empty-row">
                            <td colspan="10">Belum ada data pemberian awal referal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($data->hasPages())
            <div class="par-pager">
                <a href="{{ $data->previousPageUrl() }}"
                   class="par-pager-btn {{ $data->onFirstPage() ? 'is-disabled' : '' }}">&laquo; Sebelumnya</a>
                <span class="par-pager-info">Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}</span>
                <a href="{{ $data->nextPageUrl() }}"
                   class="par-pager-btn {{ ! $data->hasMorePages() ? 'is-disabled' : '' }}">Selanjutnya &raquo;</a>
            </div>
        @endif
    </div>
</div>
@endsection