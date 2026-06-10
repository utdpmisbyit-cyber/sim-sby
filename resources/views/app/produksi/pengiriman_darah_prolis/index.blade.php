
@extends('layouts.index')

@section('title', 'Produksi – Pengiriman Darah Prolis')

@push('styles')
<style>
/* ── Palette ──────────────────────────────────────────────────── */
:root {
    --pd-red:      #c0392b;
    --pd-red-dk:   #962d22;
    --pd-red-lt:   #fdecea;
    --pd-blue:     #2563eb;
    --pd-blue-lt:  #eff6ff;
    --pd-green:    #16a34a;
    --pd-green-lt: #f0fdf4;
    --pd-amber:    #d97706;
    --pd-amber-lt: #fffbeb;
    --pd-gray:     #64748b;
    --pd-gray-lt:  #f8fafc;
    --pd-border:   #e2e8f0;
    --pd-shadow:   0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
    --pd-shadow-md:0 4px 6px -1px rgba(0,0,0,.08),0 2px 4px -1px rgba(0,0,0,.04);
}

/* ── Layout ───────────────────────────────────────────────────── */
.pd-page         { padding: 1.25rem; background:#f1f5f9; min-height:100vh; }
.pd-card         { background:#fff; border-radius:.75rem; border:1px solid var(--pd-border);
                   box-shadow:var(--pd-shadow); overflow:hidden; }

/* ── Header bar ───────────────────────────────────────────────── */
.pd-header       { background:linear-gradient(135deg,var(--pd-red) 0%,var(--pd-red-dk) 100%);
                   padding:1rem 1.25rem; display:flex; align-items:center;
                   justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
.pd-header-left  { display:flex; align-items:center; gap:.75rem; }
.pd-header h1    { color:#fff; font-size:1.05rem; font-weight:700;
                   letter-spacing:.02em; margin:0; }
.pd-header small { color:rgba(255,255,255,.75); font-size:.78rem; }

/* ── Summary chips ────────────────────────────────────────────── */
.pd-chips        { display:flex; gap:.6rem; flex-wrap:wrap; padding:1rem 1.25rem;
                   border-bottom:1px solid var(--pd-border); background:#fafafa; }
.pd-chip         { display:flex; align-items:center; gap:.45rem; padding:.35rem .85rem;
                   border-radius:99px; font-size:.78rem; font-weight:600; white-space:nowrap; }
.pd-chip-total   { background:#e2e8f0; color:#334155; }
.pd-chip-green   { background:var(--pd-green-lt); color:var(--pd-green); }
.pd-chip-red     { background:var(--pd-red-lt); color:var(--pd-red); }
.pd-chip-amber   { background:var(--pd-amber-lt); color:var(--pd-amber); }
.pd-chip-blue    { background:var(--pd-blue-lt); color:var(--pd-blue); }

/* ── Filter panel ─────────────────────────────────────────────── */
.pd-filter       { padding:1rem 1.25rem; border-bottom:1px solid var(--pd-border);
                   background:#fff; }
.pd-filter-grid  { display:grid;
                   grid-template-columns:repeat(auto-fill,minmax(160px,1fr));
                   gap:.6rem; }
.pd-filter label { font-size:.72rem; font-weight:600; color:var(--pd-gray);
                   text-transform:uppercase; letter-spacing:.05em; display:block;
                   margin-bottom:.25rem; }
.pd-input        { width:100%; border:1px solid var(--pd-border); border-radius:.45rem;
                   padding:.42rem .65rem; font-size:.82rem; color:#1e293b;
                   background:#fff; transition:border-color .15s,box-shadow .15s; }
.pd-input:focus  { outline:none; border-color:var(--pd-red);
                   box-shadow:0 0 0 3px rgba(192,57,43,.12); }
.pd-input::placeholder { color:#94a3b8; }

/* ── Toolbar ──────────────────────────────────────────────────── */
.pd-toolbar      { display:flex; align-items:center; justify-content:space-between;
                   gap:.5rem; flex-wrap:wrap; padding:.75rem 1.25rem;
                   border-bottom:1px solid var(--pd-border); }
.pd-btn          { display:inline-flex; align-items:center; gap:.4rem;
                   padding:.42rem .9rem; border-radius:.45rem; font-size:.8rem;
                   font-weight:600; cursor:pointer; border:none; white-space:nowrap;
                   transition:filter .15s,transform .1s; }
.pd-btn:active   { transform:scale(.97); }
.pd-btn-primary  { background:var(--pd-red); color:#fff; }
.pd-btn-primary:hover { filter:brightness(1.1); }
.pd-btn-ghost    { background:transparent; color:var(--pd-gray);
                   border:1px solid var(--pd-border); }
.pd-btn-ghost:hover { background:#f8fafc; }
.pd-btn-sm       { padding:.3rem .65rem; font-size:.75rem; }
.pd-btn-danger   { background:var(--pd-red-lt); color:var(--pd-red);
                   border:1px solid #fca5a5; }
.pd-btn-danger:hover { background:#fecaca; }
.pd-btn-edit     { background:var(--pd-blue-lt); color:var(--pd-blue);
                   border:1px solid #93c5fd; }
.pd-btn-edit:hover { background:#dbeafe; }

/* ── Table ────────────────────────────────────────────────────── */
.pd-table-wrap   { overflow-x:auto; }
.pd-table        { width:100%; border-collapse:collapse; font-size:.8rem; }
.pd-table thead  { background:#f8fafc; position:sticky; top:0; z-index:1; }
.pd-table thead th {
    padding:.6rem .75rem; text-align:left; font-size:.7rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.06em; color:var(--pd-gray);
    border-bottom:2px solid var(--pd-border); white-space:nowrap; }
.pd-table tbody tr { border-bottom:1px solid #f1f5f9;
                     transition:background .1s; }
.pd-table tbody tr:hover { background:#f8fafc; }
.pd-table tbody tr.pd-row-active { background:#fef2f2; }
.pd-table td     { padding:.55rem .75rem; color:#334155; vertical-align:middle; }
.pd-table td.mono { font-family:'Courier New',monospace; font-size:.79rem;
                    font-weight:600; color:#0f172a; }

/* ── Badges ───────────────────────────────────────────────────── */
.pd-badge        { display:inline-block; padding:.18rem .6rem; border-radius:99px;
                   font-size:.7rem; font-weight:700; white-space:nowrap; }
.pd-badge-green  { background:var(--pd-green-lt); color:var(--pd-green); }
.pd-badge-red    { background:var(--pd-red-lt);   color:var(--pd-red); }
.pd-badge-amber  { background:var(--pd-amber-lt); color:var(--pd-amber); }
.pd-badge-blue   { background:var(--pd-blue-lt);  color:var(--pd-blue); }
.pd-badge-gray   { background:#f1f5f9; color:#64748b; }

/* ── Gol darah cell ───────────────────────────────────────────── */
.pd-gol          { font-weight:800; font-size:.85rem; }
.gol-a           { color:#c0392b; }
.gol-b           { color:#2563eb; }
.gol-ab          { color:#7c3aed; }
.gol-o           { color:#059669; }

/* ── Pagination ───────────────────────────────────────────────── */
.pd-pagination   { padding:.75rem 1.25rem; display:flex; align-items:center;
                   justify-content:space-between; flex-wrap:wrap; gap:.5rem;
                   border-top:1px solid var(--pd-border); background:#fafafa; }
.pd-pagination-info { font-size:.78rem; color:var(--pd-gray); }

/* ── Empty state ──────────────────────────────────────────────── */
.pd-empty        { text-align:center; padding:3rem 1rem; color:#94a3b8; }
.pd-empty svg    { margin:0 auto .75rem; display:block; opacity:.4; }

/* ── Flash ────────────────────────────────────────────────────── */
.pd-flash        { padding:.65rem 1rem; border-radius:.5rem; font-size:.82rem;
                   font-weight:600; margin-bottom:.75rem; }
.pd-flash-ok     { background:var(--pd-green-lt); color:var(--pd-green);
                   border:1px solid #bbf7d0; }
.pd-flash-err    { background:var(--pd-red-lt); color:var(--pd-red);
                   border:1px solid #fca5a5; }

/* ── Responsive ───────────────────────────────────────────────── */
@media(max-width:640px){
    .pd-filter-grid { grid-template-columns:1fr 1fr; }
    .pd-header h1   { font-size:.9rem; }
}
</style>
@endpush

@section('content')
<div class="pd-page">

    {{-- Flash --}}
    @if(session('success'))
        <div class="pd-flash pd-flash-ok">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="pd-flash pd-flash-err">✗ {{ session('error') }}</div>
    @endif

    <div class="pd-card">

        {{-- ── Header ── --}}
        <div class="pd-header">
            <div class="pd-header-left">
                {{-- blood-drop icon --}}
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C12 2 5 10.5 5 15a7 7 0 0 0 14 0C19 10.5 12 2 12 2z"
                          fill="rgba(255,255,255,.9)" stroke="rgba(255,255,255,.5)" stroke-width="1"/>
                </svg>
                <div>
                    <h1>Pengiriman Darah Prolis</h1>
                    <small>Modul Produksi &mdash; Data Kantong Darah</small>
                </div>
            </div>
            <a href="{{ route('produksi.pengiriman_darah_prolis.create') }}"
               class="pd-btn pd-btn-primary" style="background:#fff;color:var(--pd-red);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Tambah Data
            </a>
        </div>

        {{-- ── Summary chips ── --}}
        <div class="pd-chips">
            <span class="pd-chip pd-chip-total">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 17H5a2 2 0 0 0-2 2v2h18v-2a2 2 0 0 0-2-2h-4"/><circle cx="12" cy="7" r="4"/></svg>
                Total: {{ $summary['total'] }}
            </span>
            <span class="pd-chip pd-chip-green">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Tersedia: {{ $summary['tersedia'] }}
            </span>
            <span class="pd-chip pd-chip-blue">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Terpakai: {{ $summary['terpakai'] }}
            </span>
            <span class="pd-chip pd-chip-amber">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Kadaluarsa: {{ $summary['kadaluarsa'] }}
            </span>
        </div>

        {{-- ── Filter ── --}}
        <div class="pd-filter">
            <form method="GET" action="{{ route('produksi.pengiriman_darah_prolis.index') }}">
                <div class="pd-filter-grid">
                    <div>
                        <label>No Kantong</label>
                        <input class="pd-input" name="no_kantong"
                               value="{{ $filters['no_kantong'] ?? '' }}"
                               placeholder="Cari no kantong…">
                    </div>
                    <div>
                        <label>No Stock</label>
                        <input class="pd-input" name="no_stok"
                               value="{{ $filters['no_stok'] ?? '' }}"
                               placeholder="Cari no stok…">
                    </div>
                    <div>
                        <label>Data Barcode</label>
                        <input class="pd-input" name="data_barcode"
                               value="{{ $filters['data_barcode'] ?? '' }}"
                               placeholder="Scan / ketik…">
                    </div>
                    <div>
                        <label>Jenis</label>
                        <select class="pd-input" name="jenis">
                            <option value="">-- Semua --</option>
                            @foreach($optionsJenis ?? [] as $j)
                                <option value="{{ $j }}" @selected(($filters['jenis'] ?? '') == $j)>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Gol Darah</label>
                        <select class="pd-input" name="golongan_darah">
                            <option value="">-- Semua --</option>
                            @foreach(['A','B','AB','O'] as $g)
                                <option value="{{ $g }}" @selected(($filters['golongan_darah'] ?? '') == $g)>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Status</label>
                        <select class="pd-input" name="status">
                            <option value="">-- Semua --</option>
                            <option value="1" @selected(($filters['status'] ?? '') == '1')>Tersedia</option>
                            <option value="2" @selected(($filters['status'] ?? '') == '2')>Terpakai</option>
                            <option value="3" @selected(($filters['status'] ?? '') == '3')>Kadaluarsa</option>
                            <option value="4" @selected(($filters['status'] ?? '') == '4')>Rusak</option>
                        </select>
                    </div>
                    <div>
                        <label>Tgl Dari</label>
                        <input class="pd-input" type="date" name="tgl_dari"
                               value="{{ $filters['tgl_dari'] ?? '' }}">
                    </div>
                    <div>
                        <label>Tgl Sampai</label>
                        <input class="pd-input" type="date" name="tgl_sampai"
                               value="{{ $filters['tgl_sampai'] ?? '' }}">
                    </div>
                </div>
                <div style="margin-top:.75rem;display:flex;gap:.5rem;flex-wrap:wrap;">
                    <button type="submit" class="pd-btn pd-btn-primary">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Cari
                    </button>
                    <a href="{{ route('produksi.pengiriman_darah_prolis.index') }}"
                       class="pd-btn pd-btn-ghost">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.04"/></svg>
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- ── Toolbar ── --}}
        <div class="pd-toolbar">
            <span style="font-size:.8rem;color:var(--pd-gray);">
                Menampilkan <strong>{{ $data->firstItem() ?? 0 }}–{{ $data->lastItem() ?? 0 }}</strong>
                dari <strong>{{ $data->total() }}</strong> data
            </span>
            <div style="display:flex;gap:.5rem;">
                <a href="{{ route('produksi.pengiriman_darah_prolis.index') }}?{{ http_build_query(array_merge($filters, ['per_page' => 50])) }}"
                   class="pd-btn pd-btn-ghost pd-btn-sm">50 / hal</a>
                <a href="{{ route('produksi.pengiriman_darah_prolis.index') }}?{{ http_build_query(array_merge($filters, ['per_page' => 100])) }}"
                   class="pd-btn pd-btn-ghost pd-btn-sm">100 / hal</a>
            </div>
        </div>

        {{-- ── Table ── --}}
        <div class="pd-table-wrap">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>No Kantong</th>
                        <th>Jns</th>
                        <th>Gol</th>
                        <th>RH</th>
                        <th>Asal Darah</th>
                        <th>Tgl Aftap</th>
                        <th>Tgl Produksi</th>
                        <th>Tgl Kadaluarsa</th>
                        <th>STS</th>
                        <th>SCR</th>
                        <th>Jns Ktg</th>
                        <th>Keterangan</th>
                        <th style="width:90px;text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $row)
                    <tr class="{{ $row->is_expired ? 'pd-row-active' : '' }}">
                        <td style="color:#94a3b8;font-size:.72rem;">
                            {{ $data->firstItem() + $i }}
                        </td>
                        <td class="mono">{{ $row->no_kantong ?? '-' }}</td>
                        <td>
                            <span class="pd-badge pd-badge-gray">{{ $row->jenis ?? '-' }}</span>
                        </td>
                        <td>
                            @php $g = strtolower($row->golongan_darah ?? ''); @endphp
                            <span class="pd-gol gol-{{ $g }}">{{ strtoupper($g) ?: '-' }}</span>
                        </td>
                        <td>
                            @if(str_contains(strtolower((string)$row->rhesus), 'pos'))
                                <span class="pd-badge pd-badge-green">Positif</span>
                            @elseif(str_contains(strtolower((string)$row->rhesus), 'neg'))
                                <span class="pd-badge pd-badge-red">Negatif</span>
                            @else
                                <span class="pd-badge pd-badge-gray">{{ $row->rhesus ?? '-' }}</span>
                            @endif
                        </td>
                        <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                            title="{{ $row->nama_asal_darah }}">
                            {{ $row->nama_asal_darah ?? '-' }}
                        </td>
                        <td>{{ $row->tgl_aftap?->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>{{ $row->tgl_produksi?->format('d-m-Y H:i') ?? '-' }}</td>
                        <td>
                            @if($row->is_expired)
                                <span class="pd-badge pd-badge-red">
                                    {{ $row->tgl_expired?->format('d-m-Y H:i') ?? '-' }}
                                </span>
                            @else
                                {{ $row->tgl_expired?->format('d-m-Y H:i') ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @php $sts = $row->status; @endphp
                            @if($sts == '1')
                                <span class="pd-badge pd-badge-green">1</span>
                            @elseif($sts == '2')
                                <span class="pd-badge pd-badge-blue">2</span>
                            @elseif($sts == '3')
                                <span class="pd-badge pd-badge-amber">3</span>
                            @elseif($sts == '4')
                                <span class="pd-badge pd-badge-red">4</span>
                            @else
                                <span class="pd-badge pd-badge-gray">{{ $sts ?? '-' }}</span>
                            @endif
                        </td>
                        <td>{{ $row->skrining ?? 'NEG' }}</td>
                        <td>{{ $row->jumlah ?? '-' }}</td>
                        <td style="max-width:100px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                            title="{{ $row->keterangan }}">
                            {{ Str::limit($row->keterangan, 20) ?? '-' }}
                        </td>
                        <td style="text-align:center;white-space:nowrap;">
                            <a href="{{ route('produksi.pengiriman_darah_prolis.edit', $row->id) }}"
                               class="pd-btn pd-btn-edit pd-btn-sm">Edit</a>
                            <form method="POST"
                                  action="{{ route('produksi.pengiriman_darah_prolis.destroy', $row->id) }}"
                                  style="display:inline-block;"
                                  onsubmit="return confirm('Hapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="pd-btn pd-btn-danger pd-btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14">
                            <div class="pd-empty">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <p style="margin:0;font-size:.85rem;">Tidak ada data ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Pagination ── --}}
        @if($data->hasPages())
        <div class="pd-pagination">
            <span class="pd-pagination-info">
                Halaman {{ $data->currentPage() }} dari {{ $data->lastPage() }}
                &mdash; Jumlah <strong>{{ $data->total() }}</strong>
            </span>
            <div>
                {{ $data->links() }}
            </div>
        </div>
        @else
        <div class="pd-pagination">
            <span class="pd-pagination-info">
                Jumlah: <strong>{{ $data->total() }}</strong>
            </span>
        </div>
        @endif

    </div>{{-- .pd-card --}}
</div>{{-- .pd-page --}}
@endsection