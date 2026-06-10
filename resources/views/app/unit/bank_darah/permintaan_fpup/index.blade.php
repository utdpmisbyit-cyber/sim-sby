@extends('layouts.index')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --red:      #C8102E;
    --red-d:    #a00d24;
    --red-glow: rgba(200,16,46,.12);
    --navy:     #ffffff;
    --navy-2:   #f7faff;
    --teal:     #00a896;
    --amber:    #d97706;
    --sky:      #0284c7;
    --muted:    #64748b;
    --border:   #cbd5e1;
    --card:     #ffffff;
    --card-2:   #eef4ff;
    --text:     #1e293b;
    --text-dim: #64748b;
    --mono: 'JetBrains Mono', monospace;
    --sans: 'Plus Jakarta Sans', sans-serif;
}

*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--sans); background: #f1f5f9; color: var(--text); margin: 0; }

/* ── Header ── */
.fp-header {
    background: linear-gradient(135deg, #ef3b3b 0%, #1a2f45 100%);
    border-bottom: 2px solid var(--red);
    padding: 1.1rem 2rem;
    display: flex; align-items: center; gap: 1.25rem;
    box-shadow: 0 4px 24px rgba(0,0,0,.18);
    position: sticky; top: 0; z-index: 100;
}
.fp-header .logo-badge {
    background: var(--red);
    color: #fff;
    font-family: var(--mono);
    font-size: .6rem; font-weight: 700;
    letter-spacing: .12em;
    padding: .3rem .65rem;
    border-radius: 4px;
    text-transform: uppercase;
    flex-shrink: 0;
}
.fp-header h1 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #fff; }
.fp-header p  { margin: 0; font-size: .73rem; color: #94a3b8; }
.fp-header .ms-auto { margin-left: auto; }

/* ── Btn ── */
.btn-fp {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1.2rem;
    border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 600;
    cursor: pointer; transition: all .2s;
    font-family: var(--sans); text-decoration: none;
}
.btn-fp:active { transform: scale(.97); }
.btn-primary  { background: var(--red); color: #fff; }
.btn-primary:hover { background: var(--red-d); color: #fff; }
.btn-ghost    { background: #fff; color: var(--text); border: 1px solid var(--border); }
.btn-ghost:hover { background: #f1f5f9; }
.btn-sm { padding: .35rem .85rem; font-size: .76rem; }
.btn-teal  { background: rgba(0,168,150,.12); color: var(--teal); border: 1px solid rgba(0,168,150,.3); }
.btn-teal:hover { background: rgba(0,168,150,.22); }
.btn-amber { background: rgba(217,119,6,.12); color: var(--amber); border: 1px solid rgba(217,119,6,.3); }
.btn-amber:hover { background: rgba(217,119,6,.22); }
.btn-danger { background: rgba(200,16,46,.1); color: #dc2626; border: 1px solid rgba(200,16,46,.25); }
.btn-danger:hover { background: rgba(200,16,46,.2); }

/* ── Filter bar ── */
.filter-bar {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex; flex-wrap: wrap; align-items: flex-end; gap: .75rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.filter-bar input,
.filter-bar select {
    background: var(--navy-2);
    border: 1.5px solid var(--border);
    border-radius: 7px;
    padding: .48rem .85rem;
    font-size: .83rem;
    color: var(--text);
    font-family: var(--sans);
    outline: none;
    transition: border-color .2s;
}
.filter-bar input:focus,
.filter-bar select:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(0,168,150,.1); }
.filter-bar label {
    font-size: .68rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--muted); display: block; margin-bottom: .3rem;
}

/* ── Stats strip ── */
.stats-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: .9rem;
    margin-bottom: 1.25rem;
}
.stat-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: .9rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.stat-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.stat-icon.red   { background: var(--red-glow); color: var(--red); }
.stat-icon.teal  { background: rgba(0,168,150,.12); color: var(--teal); }
.stat-icon.amber { background: rgba(217,119,6,.12); color: var(--amber); }
.stat-icon.sky   { background: rgba(2,132,199,.12); color: var(--sky); }
.stat-val { font-size: 1.5rem; font-weight: 800; font-family: var(--mono); color: var(--text); line-height: 1; }
.stat-lbl { font-size: .7rem; color: var(--muted); margin-top: .15rem; }

/* ── Table ── */
.fp-table-wrap {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.fp-table-head {
    padding: .9rem 1.25rem;
    display: flex; align-items: center; gap: .75rem;
    border-bottom: 1px solid var(--border);
    background: var(--card);
}
.fp-table-head h2 { margin: 0; font-size: .88rem; font-weight: 700; color: var(--text); }
.row-count {
    background: var(--card-2);
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: .15rem .55rem;
    font-size: .72rem; font-family: var(--mono);
    color: var(--muted);
}

table.fp-tbl { width: 100%; border-collapse: collapse; }
table.fp-tbl thead tr { background: var(--card-2); }
table.fp-tbl thead th {
    padding: .75rem 1rem;
    text-align: left;
    font-size: .65rem; font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase;
    color: var(--muted); white-space: nowrap;
    border-bottom: 1px solid var(--border);
}
table.fp-tbl tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background .15s;
}
table.fp-tbl tbody tr:hover { background: #f8faff; }
table.fp-tbl tbody td {
    padding: .8rem 1rem;
    font-size: .8rem;
    vertical-align: middle;
    color: var(--text);
}
.mono { font-family: var(--mono); }
.text-dim { color: var(--text-dim); }

/* ── Badges ── */
.badge {
    display: inline-flex; align-items: center; gap: .3rem;
    border-radius: 5px; padding: .2rem .55rem;
    font-size: .68rem; font-weight: 700; letter-spacing: .04em;
    white-space: nowrap;
}
.badge-baru    { background: rgba(2,132,199,.12);  color: #0369a1; border: 1px solid rgba(2,132,199,.2); }
.badge-proses  { background: rgba(217,119,6,.12);  color: #b45309; border: 1px solid rgba(217,119,6,.2); }
.badge-selesai { background: rgba(0,168,150,.12);  color: #00766a; border: 1px solid rgba(0,168,150,.2); }
.badge-batal   { background: rgba(200,16,46,.1);   color: #dc2626; border: 1px solid rgba(200,16,46,.2); }
.badge-cito    { background: var(--red); color: #fff; border: 1px solid var(--red); }
.badge-biasa   { background: #f1f5f9; color: var(--muted); border: 1px solid var(--border); }

/* ── Action row ── */
.action-row { display: flex; align-items: center; gap: .35rem; }

/* ── Empty ── */
.empty-row td {
    text-align: center;
    padding: 3rem 1rem !important;
    color: var(--muted);
}
.empty-icon { font-size: 2rem; margin-bottom: .5rem; opacity: .25; color: var(--muted); }

/* ── Pagination ── */
.pagination-wrap { padding: .9rem 1.25rem; border-top: 1px solid var(--border); background: var(--card); }
.pagination-wrap .pagination { margin: 0; gap: .25rem; }
.page-item .page-link {
    background: var(--card);
    border-color: var(--border);
    color: var(--text-dim);
    border-radius: 6px !important;
    font-size: .78rem;
    padding: .35rem .7rem;
}
.page-item .page-link:hover { background: var(--card-2); color: var(--text); }
.page-item.active .page-link {
    background: var(--red);
    border-color: var(--red);
    color: #fff;
}

/* ── Toast ── */
#toast {
    position: fixed; top: 1.5rem; right: 1.5rem;
    background: #fff;
    color: var(--text); padding: .75rem 1.25rem;
    border-radius: 10px; font-size: .84rem;
    box-shadow: 0 8px 32px rgba(0,0,0,.15);
    z-index: 9999; opacity: 0; transform: translateY(-12px);
    transition: all .3s; border-left: 4px solid var(--teal);
    display: flex; align-items: center; gap: .5rem;
    pointer-events: none;
}
#toast.show { opacity: 1; transform: translateY(0); }
#toast.err  { border-left-color: var(--red); }

/* ── Nama Pasien di tabel ── */
.patient-name { font-weight: 600; color: var(--text); }

@media(max-width: 900px) {
    .stats-strip { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

@section('content')
<div id="toast"></div>

{{-- Header --}}
<div class="fp-header">
    <span class="logo-badge">PMI</span>
    <div>
        <h1>Permintaan Darah (FPUP)</h1>
        <p>Unit › Bank Darah › Permintaan FPUP › Daftar</p>
    </div>
    <div class="ms-auto">
        <a href="{{ route('unit.bank_darah.permintaan_fpup.create') }}" class="btn-fp btn-primary">
            <i class="fas fa-plus"></i> Tambah FPUP
        </a>
    </div>
</div>

<div class="container-fluid px-4 py-4">

    {{-- Alert success/error --}}
    @if(session('success'))
        <div id="alert-success" style="background:rgba(0,168,150,.08);border:1px solid rgba(0,168,150,.25);color:#00766a;padding:.75rem 1.25rem;border-radius:10px;margin-bottom:1rem;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stats ── --}}
    @php
        $total   = $data->total();
        $baru    = \App\Models\PermintaanFpup::where('status','baru')->count();
        $proses  = \App\Models\PermintaanFpup::where('status','proses')->count();
        $selesai = \App\Models\PermintaanFpup::where('status','selesai')->count();
    @endphp
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-file-medical"></i></div>
            <div>
                <div class="stat-val">{{ $total }}</div>
                <div class="stat-lbl">Total FPUP</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sky"><i class="fas fa-clock"></i></div>
            <div>
                <div class="stat-val">{{ $baru }}</div>
                <div class="stat-lbl">Baru</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-spinner"></i></div>
            <div>
                <div class="stat-val">{{ $proses }}</div>
                <div class="stat-lbl">Proses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-val">{{ $selesai }}</div>
                <div class="stat-lbl">Selesai</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('unit.bank_darah.permintaan_fpup.index') }}">
        <div class="filter-bar">
            <div>
                <label>Cari</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="No FPUP / Nama Pasien / RS..." style="width:260px;">
            </div>
            <div>
                <label>Status</label>
                <select name="status">
                    <option value="">— Semua Status —</option>
                    @foreach($status_list as $s)
                        <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tanggal</label>
                <input type="date" name="tgl" value="{{ $filters['tgl'] ?? '' }}">
            </div>
            <div>
                <label>Per Halaman</label>
                <select name="per_page">
                    @foreach([10,15,25,50] as $pp)
                        <option value="{{ $pp }}" @selected(($filters['per_page'] ?? 15) == $pp)>{{ $pp }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-fp btn-ghost">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="{{ route('unit.bank_darah.permintaan_fpup.index') }}" class="btn-fp btn-ghost">
                <i class="fas fa-times"></i> Reset
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="fp-table-wrap">
        <div class="fp-table-head">
            <h2><i class="fas fa-table" style="color:var(--teal);margin-right:.4rem;"></i> Data FPUP</h2>
            <span class="row-count">{{ $data->total() }} record</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="fp-tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No FPUP</th>
                        <th>Tgl Minta</th>
                        <th>Pasien</th>
                        <th>Rumah Sakit</th>
                        <th>Bagian</th>
                        <th>Jns Perm.</th>
                        <th>Darah</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $i => $row)
                    <tr>
                        <td class="mono text-dim">{{ $data->firstItem() + $i }}</td>

                        <td>
                            <span class="mono" style="color:var(--teal);font-size:.78rem;font-weight:600;">
                                {{ $row->no_fpup }}
                            </span>
                            @if($row->no_reg)
                            <div class="text-dim mono" style="font-size:.68rem;margin-top:.1rem;">
                                Reg: {{ $row->no_reg }}
                            </div>
                            @endif
                        </td>

                        <td class="mono" style="font-size:.76rem;color:var(--text);">
                            {{ $row->tgl_minta?->format('d/m/Y') }}<br>
                            <span class="text-dim">
                                {{ $row->jam_minta ? substr($row->jam_minta,0,5) : '' }}
                            </span>
                        </td>

                        <td>
                            <div class="patient-name">{{ $row->nama_pasien }}</div>
                            <div class="text-dim" style="font-size:.72rem;">
                                {{ $row->jenis_kelamin }}
                                @if($row->umur) · {{ $row->umur }} thn @endif
                            </div>
                        </td>

                        <td>
                            <div style="font-size:.8rem;font-weight:500;color:var(--text);">
                                {{ $row->nama_rs ?? '—' }}
                            </div>
                            @if($row->kode_rs)
                            <div class="text-dim mono" style="font-size:.7rem;">{{ $row->kode_rs }}</div>
                            @endif
                        </td>

                        <td class="text-dim">{{ $row->bagian ?? '—' }}</td>

                        <td>
                            @if($row->jns_permintaan === 'CITO')
                                <span class="badge badge-cito"><i class="fas fa-bolt"></i> CITO</span>
                            @elseif($row->jns_permintaan)
                                <span class="badge badge-biasa">{{ $row->jns_permintaan }}</span>
                            @else
                                <span class="text-dim">—</span>
                            @endif
                        </td>

                        <td>
                            @forelse($row->details as $d)
                                <div class="mono" style="font-size:.72rem;color:var(--text);">
                                    <span style="color:var(--red);font-weight:600;">{{ $d->jns_darah }}</span>
                                    {{ $d->gol_darah }}{{ $d->rhesus === 'Positif' ? '+' : ($d->rhesus === 'Negatif' ? '-' : '') }}
                                    <span class="text-dim">× {{ $d->jumlah }}</span>
                                </div>
                            @empty
                                <span class="text-dim">—</span>
                            @endforelse
                        </td>

                        <td class="text-dim">{{ $row->cara_pembayaran ?? '—' }}</td>

                        <td>
                            <span class="badge badge-{{ $row->status }}">
                                {{ ucfirst($row->status) }}
                            </span>
                        </td>

                        <td>
                            <div class="action-row" style="justify-content:center;">
                                <a href="{{ route('unit.bank_darah.permintaan_fpup.show', $row) }}"
                                   class="btn-fp btn-sm btn-teal" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('unit.bank_darah.permintaan_fpup.edit', $row) }}"
                                   class="btn-fp btn-sm btn-amber" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('unit.bank_darah.permintaan_fpup.barcode', $row->id) }}"
                                target="_blank"
                                class="btn-fp btn-sm btn-primary"
                                title="Cetak Barcode">
                                    <i class="fas fa-barcode"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('unit.bank_darah.permintaan_fpup.destroy', $row) }}"
                                      onsubmit="return confirm('Hapus FPUP {{ $row->no_fpup }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-fp btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="11">
                            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                            <div style="font-weight:600;color:var(--muted);margin-bottom:.35rem;">
                                Belum ada data FPUP.
                            </div>
                            <a href="{{ route('unit.bank_darah.permintaan_fpup.create') }}"
                               class="btn-fp btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah FPUP Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($data->hasPages())
        <div class="pagination-wrap">
            {{ $data->appends($filters)->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    const al = document.getElementById('alert-success');
    if (al) setTimeout(() => al.style.display = 'none', 4000);
</script>
@endpush