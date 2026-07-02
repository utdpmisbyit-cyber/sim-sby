@extends('layouts.index')

@section('title', 'Pengeluaran Kantong Mobile Unit')

@push('styles')
<style>
    /* ── Design Tokens ─────────────────────────────────────────────────── */
    :root {
        --clr-bg:         #f0f4f8;
        --clr-surface:    #ffffff;
        --clr-border:     #dde3ec;
        --clr-primary:    #1a56db;
        --clr-primary-dk: #1340a6;
        --clr-accent:     #e53e3e;
        --clr-success:    #16a34a;
        --clr-warning:    #d97706;
        --clr-text:       #1e293b;
        --clr-muted:      #64748b;
        --clr-row-hover:  #f1f5fd;
        --clr-scan-ring:  rgba(26,86,219,.25);
        --radius-card:    12px;
        --radius-input:   8px;
        --shadow-card:    0 2px 12px rgba(0,0,0,.07);
        --shadow-btn:     0 1px 4px rgba(26,86,219,.3);
        --font-main:      'Noto Sans', 'Segoe UI', sans-serif;
        --font-mono:      'JetBrains Mono', 'Consolas', monospace;
        --transition:     .15s ease;
    }

    * { box-sizing: border-box; }

    body { background: var(--clr-bg); font-family: var(--font-main); color: var(--clr-text); }

    /* ── Page header ───────────────────────────────────────────────────── */
    .pkmu-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 1.5rem; background: var(--clr-surface);
        border-bottom: 1px solid var(--clr-border);
        box-shadow: var(--shadow-card);
    }
    .pkmu-header h1 { font-size: 1.1rem; font-weight: 700; letter-spacing: -.01em; margin: 0; }
    .pkmu-header .badge-no {
        font-family: var(--font-mono); font-size: .8rem; font-weight: 600;
        background: #eff6ff; color: var(--clr-primary);
        border: 1px solid #bfdbfe; border-radius: 6px; padding: .25rem .6rem;
    }

    /* ── Layout ────────────────────────────────────────────────────────── */
   
    .pkmu-wrap {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 1rem 1.5rem;
    }

    /* Biar grid lebih proporsional */
    .grid-2 {
        display: grid;
        grid-template-columns: 1.2fr 1.8fr;
        gap: 1.25rem;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    @media (max-width: 992px) {
        .grid-2,
        .grid-3 {
            grid-template-columns: 1fr;
        }
    }
    /* ── Card ──────────────────────────────────────────────────────────── */
    .card {
        background: var(--clr-surface); border-radius: var(--radius-card);
        border: 1px solid var(--clr-border); box-shadow: var(--shadow-card);
        padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;
        width: 100%;
    }
    .card-title {
        font-size: .78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; color: var(--clr-muted); margin-bottom: 1rem;
        display: flex; align-items: center; gap: .5rem;
    }
    .card-title svg { width: 15px; height: 15px; }

    /* ── Form controls ─────────────────────────────────────────────────── */
    .form-group { display: flex; flex-direction: column; gap: .35rem; margin-bottom: .9rem; }
    .form-group:last-child { margin-bottom: 0; }

    label {
        font-size: .78rem; font-weight: 600; color: var(--clr-muted);
    }
    label .req { color: var(--clr-accent); margin-left: 2px; }

    .form-control {
        width: 100%; padding: .5rem .75rem;
        border: 1.5px solid var(--clr-border); border-radius: var(--radius-input);
        font-size: .875rem; color: var(--clr-text); background: #fff;
        transition: border-color var(--transition), box-shadow var(--transition);
        appearance: none;
    }
    .form-control:focus {
        outline: none; border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px var(--clr-scan-ring);
    }
    select.form-control { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2364748b'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .75rem center; padding-right: 2rem; }

    /* ── Scan field ────────────────────────────────────────────────────── */
    .scan-wrap {
        display: flex; gap: .5rem; align-items: stretch;
    }
    .scan-input {
        flex: 1; font-family: var(--font-mono); font-size: .9rem; letter-spacing: .03em;
        border: 2px solid var(--clr-primary); border-radius: var(--radius-input);
        padding: .55rem .85rem; color: var(--clr-text);
        transition: box-shadow var(--transition);
    }
    .scan-input:focus {
        outline: none; box-shadow: 0 0 0 4px var(--clr-scan-ring);
    }
    .scan-input.error { border-color: var(--clr-accent); box-shadow: 0 0 0 3px rgba(229,62,62,.2); }
    .scan-input.success { border-color: var(--clr-success); box-shadow: 0 0 0 3px rgba(22,163,74,.2); }

    .btn-scan {
        padding: .55rem 1.1rem; background: var(--clr-primary); color: #fff;
        border: none; border-radius: var(--radius-input); cursor: pointer;
        font-size: .85rem; font-weight: 600; white-space: nowrap;
        box-shadow: var(--shadow-btn); transition: background var(--transition), transform var(--transition);
        display: flex; align-items: center; gap: .4rem;
    }
    .btn-scan:hover { background: var(--clr-primary-dk); transform: translateY(-1px); }
    .btn-scan:active { transform: translateY(0); }

    .scan-msg {
        font-size: .78rem; min-height: 1.2em; padding: .2rem 0;
        transition: color var(--transition);
    }
    .scan-msg.ok  { color: var(--clr-success); }
    .scan-msg.err { color: var(--clr-accent); }

    /* ── Kantong table ─────────────────────────────────────────────────── */
    .table-wrap { overflow-x: auto; margin-top: .75rem; }
    .tbl {
        width: 100%; border-collapse: collapse; font-size: .825rem;
    }
    .tbl thead th {
        background: #f8fafc; font-size: .7rem; text-transform: uppercase;
        letter-spacing: .06em; font-weight: 700; color: var(--clr-muted);
        padding: .6rem .75rem; border-bottom: 2px solid var(--clr-border);
        white-space: nowrap; text-align: left;
    }
    .tbl tbody td {
        padding: .6rem .75rem; border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .tbl tbody tr { transition: background var(--transition); }
    .tbl tbody tr:hover { background: var(--clr-row-hover); }
    .tbl tbody tr.new-row { animation: slideIn .3s ease; }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .badge-jenis {
        display: inline-block; padding: .15rem .5rem;
        border-radius: 4px; font-size: .7rem; font-weight: 600;
        background: #eff6ff; color: var(--clr-primary); border: 1px solid #bfdbfe;
    }
    .badge-empty {
        color: var(--clr-muted); font-style: italic; font-size: .78rem;
    }

    .btn-hapus {
        background: none; border: 1px solid #fecaca; border-radius: 5px;
        color: var(--clr-accent); padding: .25rem .5rem; cursor: pointer;
        font-size: .75rem; font-weight: 600;
        transition: background var(--transition), color var(--transition);
        display: flex; align-items: center; gap: .3rem;
    }
    .btn-hapus:hover { background: #fee2e2; color: #991b1b; }

    /* ── Summary bar ───────────────────────────────────────────────────── */
    .summary-bar {
        display: flex; align-items: center; justify-content: space-between;
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px;
        padding: .6rem 1rem; margin-top: .75rem;
    }
    .summary-bar .lbl { font-size: .78rem; color: var(--clr-muted); }
    .summary-bar .val { font-size: 1.1rem; font-weight: 700; color: var(--clr-primary); }

    /* ── Bottom action bar ─────────────────────────────────────────────── */
    .action-bar {
        display: flex; gap: .75rem; justify-content: flex-end;
        padding: 1rem 1.5rem; background: var(--clr-surface);
        border-top: 1px solid var(--clr-border); position: sticky; bottom: 0;
        box-shadow: 0 -2px 12px rgba(0,0,0,.06); z-index: 10;
    }

    .btn {
        padding: .55rem 1.4rem; border-radius: var(--radius-input);
        font-size: .875rem; font-weight: 600; cursor: pointer;
        border: 1.5px solid transparent; transition: all var(--transition);
        display: inline-flex; align-items: center; gap: .45rem;
    }
    .btn-primary {
        background: var(--clr-primary); color: #fff; box-shadow: var(--shadow-btn);
    }
    .btn-primary:hover { background: var(--clr-primary-dk); transform: translateY(-1px); }
    .btn-primary:disabled { background: #93c5fd; cursor: not-allowed; transform: none; }

    .btn-outline {
        background: #fff; color: var(--clr-text); border-color: var(--clr-border);
    }
    .btn-outline:hover { background: var(--clr-bg); }

    /* ── Alert ─────────────────────────────────────────────────────────── */
    .alert {
        border-radius: var(--radius-input); padding: .75rem 1rem; margin-bottom: 1rem;
        font-size: .85rem; display: flex; align-items: flex-start; gap: .6rem;
    }
    .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #14532d; }
    .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; }

    /* ── History table ─────────────────────────────────────────────────── */
    .section-title {
        font-size: .95rem; font-weight: 700; color: var(--clr-text);
        margin-bottom: 1rem; display: flex; align-items: center; gap: .5rem;
    }
    .section-title::after {
        content: ''; flex: 1; height: 1px; background: var(--clr-border);
    }

    .tbl-history { font-size: .82rem; }
    .tbl-history thead th { background: #f1f5f9; }

    /* ── Spinner ───────────────────────────────────────────────────────── */
    .spinner {
        display: inline-block; width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
        border-radius: 50%; animation: spin .6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Readonly field ────────────────────────────────────────────────── */
    .form-control-static {
        padding: .5rem .75rem; background: #f8fafc; border: 1.5px solid var(--clr-border);
        border-radius: var(--radius-input); font-size: .875rem;
        font-family: var(--font-mono); color: var(--clr-primary); font-weight: 600;
    }

    /* ── Tabs ──────────────────────────────────────────────────────────── */
    .tabs { display: flex; gap: 0; border-bottom: 2px solid var(--clr-border); margin-bottom: 1.5rem; }
    .tab-btn {
        padding: .6rem 1.25rem; background: none; border: none; cursor: pointer;
        font-size: .875rem; font-weight: 600; color: var(--clr-muted);
        border-bottom: 2px solid transparent; margin-bottom: -2px;
        transition: color var(--transition), border-color var(--transition);
    }
    .tab-btn.active { color: var(--clr-primary); border-bottom-color: var(--clr-primary); }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
</style>
@endpush

@section('content')
<div class="pkmu-header">
    <h1>
        <svg style="width:18px;height:18px;vertical-align:-3px;margin-right:6px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17H7A5 5 0 0 1 7 7h2"/><path d="M15 7h2a5 5 0 0 1 0 10h-2"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Pengeluaran Kantong – Mobile Unit
    </h1>
    <div style="display:flex;gap:.6rem;align-items:center;">
        <span class="badge-no">{{ $nomorKeluar }}</span>
        <span style="font-size:.78rem;color:var(--clr-muted);">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
    </div>
</div>

<div class="pkmu-wrap">

    {{-- ── Alerts ─────────────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="alert alert-success">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->has('kantong'))
    <div class="alert alert-error">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ $errors->first('kantong') }}
    </div>
    @endif

    {{-- ── Tabs ─────────────────────────────────────────────────────────── --}}
    <div class="tabs">
        <button class="tab-btn active" data-tab="form">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:-2px;margin-right:4px;"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="9"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="15" x2="12" y2="15"/></svg>
            Form Pengeluaran
        </button>
        <button class="tab-btn" data-tab="history">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:-2px;margin-right:4px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Riwayat
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         TAB 1 : FORM PENGELUARAN
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="tab-panel active" id="tab-form">

        <form id="frmPengeluaran" method="POST" action="{{ route('aftap.pengeluaran_mobile_unit.store') }}">
            @csrf

            {{-- Row 1: No. Keluar + Tanggal ──────────────────────────────── --}}
            <div class="grid-2">
                <div class="card">
                    <div class="card-title">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3l-4 4-4-4"/></svg>
                        Nomor & Tanggal
                    </div>
                    <div class="form-group">
                        <label>No. Pengeluaran</label>
                        <div class="form-control-static">{{ $nomorKeluar }}</div>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Pengeluaran <span class="req">*</span></label>
                        <input type="date" name="tgl_keluar" class="form-control @error('tgl_keluar') error @enderror"
                               value="{{ old('tgl_keluar', date('Y-m-d')) }}" required>
                        @error('tgl_keluar')<span style="font-size:.75rem;color:var(--clr-accent)">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Tujuan</label>
                        <input type="text" name="tujuan" class="form-control"
                               value="{{ old('tujuan') }}" placeholder="Tujuan pengiriman...">
                    </div>
                </div>

                {{-- Row 2: Relasi ─────────────────────────────────────────── --}}
                <div class="card">
                    <div class="card-title">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Data Pengiriman
                    </div>
                    <div class="form-group">
                        <label>Petugas <span class="req">*</span></label>
                        <select name="petugas_id" class="form-control @error('petugas_id') error @enderror" required>
                            <option value="">— Pilih Petugas —</option>
                            @foreach($petugasList as $p)
                            <option value="{{ $p->id }}" {{ old('petugas_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->kode }} – {{ $p->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('petugas_id')<span style="font-size:.75rem;color:var(--clr-accent)">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>No. Permintaan <span class="req">*</span></label>

                        <select name="permintaan_mobile_unit_id"
                                class="form-control @error('permintaan_mobile_unit_id') error @enderror"
                                required>

                            <option value="">— Pilih No. Permintaan —</option>

                            @foreach($permintaans as $pmu)
                            <option value="{{ $pmu->id }}"
                                {{ old('permintaan_mobile_unit_id') == $pmu->id ? 'selected' : '' }}>

                                {{ $pmu->nomor }}
                                — {{ \Carbon\Carbon::parse($pmu->tanggal)->format('d/m/Y') }}

                            </option>
                            @endforeach
                        </select>

                        @error('permintaan_mobile_unit_id')
                        <span style="font-size:.75rem;color:var(--clr-accent)">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Asal Darah <span class="req">*</span></label>
                        <select name="asal_darah_id" class="form-control @error('asal_darah_id') error @enderror" required>
                            <option value="">— Pilih Asal Darah —</option>
                            @foreach($asalDarahs as $ad)
                            <option value="{{ $ad->id }}" {{ old('asal_darah_id') == $ad->id ? 'selected' : '' }}>
                                {{ $ad->kode }} – {{ $ad->nama }}
                            </option>
                            @endforeach
                        </select>
                        @error('asal_darah_id')<span style="font-size:.75rem;color:var(--clr-accent)">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Mobil Unit <span class="req">*</span></label>
                        <select name="mobile_unit_id" class="form-control @error('mobile_unit_id') error @enderror" required>
                            <option value="">— Pilih Mobil Unit —</option>
                            @foreach($mobilUnits as $mu)
                            <option value="{{ $mu->id }}" {{ old('mobile_unit_id') == $mu->id ? 'selected' : '' }}>
                                {{ $mu->kode }} – {{ $mu->merk_mobil }} ({{ $mu->no_polisi }})
                            </option>
                            @endforeach
                        </select>
                        @error('mobile_unit_id')<span style="font-size:.75rem;color:var(--clr-accent)">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- Scan Kantong ─────────────────────────────────────────────── --}}
            <div class="card">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="4" height="18"/><rect x="7" y="5" width="2" height="14"/><rect x="11" y="3" width="3" height="18"/><rect x="16" y="5" width="1" height="14"/><rect x="19" y="3" width="2" height="18"/></svg>
                    Scan / Input No. Kantong
                </div>

                <div class="scan-wrap">
                    <input type="text" id="scanInput" class="scan-input"
                           placeholder="Scan barcode atau ketik no. kantong, tekan Enter..."
                           autocomplete="off" autofocus>
                    <button type="button" id="btnAddKantong" class="btn-scan">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah
                    </button>
                </div>
                <div class="scan-msg" id="scanMsg"></div>

                {{-- Daftar kantong ditambahkan ──────────────────────────── --}}
                <div class="table-wrap">
                    <table class="tbl" id="tblKantong">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. Kantong</th>
                                <th>Merk Kantong</th>
                                <th>Jenis Kantong</th>
                                <th>Ukuran</th>
                                <th>No. Lot</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tblKantongBody">
                            {{-- Isi dari session --}}
                            @forelse($kantongItems as $i => $item)
                            <tr data-nokantong="{{ $item['no_kantong'] }}">
                                <td>{{ $i + 1 }}</td>
                                <td><span style="font-family:var(--font-mono);font-weight:600;">{{ $item['no_kantong'] }}</span></td>
                                <td>{{ $item['merk'] ?? '-' }}</td>
                                <td><span class="badge-jenis">{{ $item['jenis'] ?? '-' }}</span></td>
                                <td>{{ $item['ukuran'] ?? '-' }}</td>
                                <td>{{ $item['no_lot'] ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn-hapus btn-remove"
                                            data-nokantong="{{ $item['no_kantong'] }}">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyRow">
                                <td colspan="7" style="text-align:center;padding:2rem 0;">
                                    <span class="badge-empty">Belum ada kantong. Scan atau ketik nomor kantong di atas.</span>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="summary-bar" id="summaryBar" style="{{ count($kantongItems) ? '' : 'display:none' }}">
                    <span class="lbl">Total Kantong</span>
                    <span class="val" id="totalKantong">{{ count($kantongItems) }}</span>
                </div>
            </div>

            {{-- Keterangan ───────────────────────────────────────────────── --}}
            <div class="card">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Keterangan
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <textarea name="keterangan" class="form-control" rows="3"
                              placeholder="Keterangan tambahan (opsional)...">{{ old('keterangan') }}</textarea>
                </div>
            </div>

        </form>

    </div>{{-- /tab-form --}}

    {{-- ══════════════════════════════════════════════════════════════════
         TAB 2 : RIWAYAT
    ══════════════════════════════════════════════════════════════════════ --}}
    <div class="tab-panel" id="tab-history">
        <div class="card">
            {{-- Filter ────────────────────────────────────────────────── --}}
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1rem;align-items:flex-end;">
                <div class="form-group" style="margin-bottom:0;flex:1;min-width:160px;">
                    <label>Cari No. Keluar</label>
                    <input type="text" id="histSearch" class="form-control"
                           placeholder="K26..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Dari</label>
                    <input type="date" id="histDari" class="form-control" value="{{ request('tgl_dari') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Sampai</label>
                    <input type="date" id="histSampai" class="form-control" value="{{ request('tgl_sampai') }}">
                </div>
                <button type="button" id="btnFilter" class="btn btn-primary" style="margin-bottom:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
            </div>

            <div class="table-wrap">
                <table class="tbl tbl-history">
                    <thead>
                        <tr>
                            <th>No. Keluar</th>
                            <th>No. Permintaan</th>
                            <th>Tanggal</th>
                            <th>Petugas</th>
                            <th>Asal Darah</th>
                            <th>Mobil Unit</th>
                            <th style="text-align:center">Total Kantong</th>
                            <th style="text-align:center">Aksi</th> {{-- Kolom baru --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($list as $row)
                        <tr>
                            <td>{{ $row->no_keluar }}</td>
                            <td>{{ $row->no_permintaan ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tgl_keluar)->translatedFormat('d M Y') }}</td>
                            <td>{{ $row->petugas->nama ?? '-' }}</td>
                            <td>{{ $row->asalDarah->nama ?? '-' }}</td>
                            <td>{{ $row->mobilUnit ? $row->mobilUnit->merk_mobil . ' (' . $row->mobilUnit->no_polisi . ')' : '-' }}</td>
                            <td style="text-align:center">
                                <span style="font-weight:700;color:var(--clr-primary)">{{ $row->total_kantong }}</span>
                            </td>
                            <td style="text-align:center">
                                <div style="display:flex;gap:.5rem;justify-content:center;">
                                    <a href="{{ route('aftap.pengeluaran_mobile_unit.edit', $row->id) }}" 
                                    class="btn-edit" style="background:none;border:none;cursor:pointer;color:var(--clr-primary);">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M17 3l4 4-11 11-4 1 1-4L17 3z"/>
                                        </svg>
                                    </a>
                                    <button type="button" class="btn-delete" data-id="{{ $row->id }}" data-nokeluar="{{ $row->no_keluar }}"
                                            style="background:none;border:none;cursor:pointer;color:var(--clr-accent);">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:2rem 0;">
                                <span class="badge-empty">Belum ada data pengeluaran.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($list->hasPages())
            <div style="margin-top:1rem;display:flex;justify-content:flex-end;">
                {{ $list->links() }}
            </div>
            @endif
        </div>
    </div>{{-- /tab-history --}}

</div>{{-- /pkmu-wrap --}}

{{-- ── Sticky action bar (hanya tampil di tab form) ──────────────────────── --}}
<div class="action-bar" id="actionBar">
    <button type="button" class="btn btn-outline" onclick="resetForm()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.47"/></svg>
        Reset
    </button>
    <button type="submit" form="frmPengeluaran" class="btn btn-primary" id="btnSimpan">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Simpan Pengeluaran
    </button>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Tabs ─────────────────────────────────────────────────────────────────
    const tabBtns   = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');
    const actionBar = document.getElementById('actionBar');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            const panel = document.getElementById('tab-' + btn.dataset.tab);
            if (panel) panel.classList.add('active');
            actionBar.style.display = btn.dataset.tab === 'form' ? '' : 'none';
        });
    });

    // ── Scan / tambah kantong ─────────────────────────────────────────────────
    const scanInput   = document.getElementById('scanInput');
    const btnAdd      = document.getElementById('btnAddKantong');
    const scanMsg     = document.getElementById('scanMsg');
    const tbody       = document.getElementById('tblKantongBody');
    const summaryBar  = document.getElementById('summaryBar');
    const totalEl     = document.getElementById('totalKantong');

    let rowCount = {{ count($kantongItems) }};

    function setMsg(text, type) {
        scanMsg.textContent = text;
        scanMsg.className   = 'scan-msg ' + type;
        setTimeout(() => { scanMsg.textContent = ''; scanMsg.className = 'scan-msg'; }, 3000);
    }

    function setScanState(state) {
        scanInput.classList.remove('error', 'success');
        if (state) scanInput.classList.add(state);
    }

    function updateSummary() {
        const rows = tbody.querySelectorAll('tr[data-nokantong]');
        rowCount = rows.length;
        totalEl.textContent = rowCount;
        summaryBar.style.display = rowCount > 0 ? '' : 'none';
        // Hapus empty row jika ada
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow && rowCount > 0) emptyRow.remove();
    }

    function buildRow(item, idx) {
        const tr = document.createElement('tr');
        tr.dataset.nokantong = item.no_kantong;
        tr.classList.add('new-row');
        tr.innerHTML = `
            <td>${idx}</td>
            <td><span style="font-family:var(--font-mono);font-weight:600;">${item.no_kantong}</span></td>
            <td>${item.merk ?? '-'}</td>
            <td><span class="badge-jenis">${item.jenis ?? '-'}</span></td>
            <td>${item.ukuran ?? '-'}</td>
            <td>${item.no_lot ?? '-'}</td>
            <td>
                <button type="button" class="btn-hapus btn-remove" data-nokantong="${item.no_kantong}">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Hapus
                </button>
            </td>`;
        return tr;
    }

    function reindexRows() {
        tbody.querySelectorAll('tr[data-nokantong]').forEach((tr, i) => {
            tr.cells[0].textContent = i + 1;
        });
    }

    async function doAddKantong() {
        const val = scanInput.value.trim();
        if (!val) { scanInput.focus(); return; }

        btnAdd.disabled = true;
        btnAdd.innerHTML = '<span class="spinner"></span> Cek...';

        try {
            const res  = await fetch('{{ route("aftap.pengeluaran_mobile_unit.scan-kantong") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ no_kantong: val }),
            });
            const json = await res.json();

            if (json.success) {
                setScanState('success');
                setMsg('✓ ' + json.message, 'ok');
                // Re-render tabel dari data server (session)
                tbody.innerHTML = '';
                json.items.forEach((item, i) => tbody.appendChild(buildRow(item, i + 1)));
                updateSummary();
                scanInput.value = '';
            } else {
                setScanState('error');
                setMsg('✗ ' + json.message, 'err');
            }
        } catch (e) {
            setScanState('error');
            setMsg('✗ Gagal terhubung ke server.', 'err');
        } finally {
            btnAdd.disabled = false;
            btnAdd.innerHTML = `<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Tambah`;
            scanInput.focus();
        }
    }

    btnAdd.addEventListener('click', doAddKantong);
    scanInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doAddKantong(); } });

    // ── Hapus kantong ─────────────────────────────────────────────────────────
    tbody.addEventListener('click', async e => {
        const btn = e.target.closest('.btn-remove');
        if (!btn) return;
        const noKantong = btn.dataset.nokantong;
        btn.disabled = true;
        btn.textContent = '...';

        try {
            const res  = await fetch('{{ route("aftap.pengeluaran_mobile_unit.remove-kantong") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                },
                body: JSON.stringify({ no_kantong: noKantong }),
            });
            const json = await res.json();
            if (json.success) {
                tbody.innerHTML = '';
                if (json.items.length === 0) {
                    const tr = document.createElement('tr');
                    tr.id = 'emptyRow';
                    tr.innerHTML = `<td colspan="7" style="text-align:center;padding:2rem 0;"><span class="badge-empty">Belum ada kantong. Scan atau ketik nomor kantong di atas.</span></td>`;
                    tbody.appendChild(tr);
                } else {
                    json.items.forEach((item, i) => tbody.appendChild(buildRow(item, i + 1)));
                }
                updateSummary();
            }
        } catch (e) { /* silent */ }
    });

    // ── Reset ─────────────────────────────────────────────────────────────────
    window.resetForm = function () {
        if (!confirm('Reset semua data form & daftar kantong?')) return;
        fetch('{{ route("aftap.pengeluaran_mobile_unit.remove-kantong") }}', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ no_kantong: '__ALL__' }),
        }).catch(() => {});
        document.getElementById('frmPengeluaran').reset();
        tbody.innerHTML = `<tr id="emptyRow"><td colspan="7" style="text-align:center;padding:2rem 0;"><span class="badge-empty">Belum ada kantong. Scan atau ketik nomor kantong di atas.</span></td></tr>`;
        summaryBar.style.display = 'none';
        scanInput.focus();
    };

    // ── Filter riwayat ────────────────────────────────────────────────────────
    document.getElementById('btnFilter')?.addEventListener('click', () => {
        const s = document.getElementById('histSearch').value;
        const d = document.getElementById('histDari').value;
        const e = document.getElementById('histSampai').value;
        const url = new URL(window.location.href);
        url.searchParams.set('search', s);
        url.searchParams.set('tgl_dari', d);
        url.searchParams.set('tgl_sampai', e);
        window.location.href = url.toString();
    });

    // ── Validasi sebelum submit ───────────────────────────────────────────────
    document.getElementById('frmPengeluaran').addEventListener('submit', function (e) {
        const items = tbody.querySelectorAll('tr[data-nokantong]');
        if (items.length === 0) {
            e.preventDefault();
            setMsg('✗ Minimal satu kantong harus ditambahkan sebelum menyimpan.', 'err');
            scanInput.focus();
            return;
        }
        const btn = document.getElementById('btnSimpan');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Menyimpan...';
    });
    // Tambahkan di dalam script section
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            const noKeluar = this.dataset.nokeluar;
            
            if (!confirm(`Hapus pengeluaran ${noKeluar}?`)) return;
            
            try {
                const res = await fetch(`{{ url('unit/pengeluaran_mobile_unit') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                const json = await res.json();
                if (json.success) {
                    location.reload();
                } else {
                    alert('Gagal menghapus: ' + json.message);
                }
            } catch (e) {
                alert('Error: ' + e.message);
            }
        });
    });

})();
</script>
@endpush