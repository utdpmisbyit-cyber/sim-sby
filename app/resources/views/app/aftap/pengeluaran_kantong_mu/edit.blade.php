@extends('layouts.index')

@section('title', 'Edit Pengeluaran Kantong Mobile Unit')

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
        --font-main:      'Inter', 'Segoe UI', system-ui, sans-serif;
        --font-mono:      'JetBrains Mono', 'SF Mono', monospace;
        --transition:     .2s ease;
    }

    * { box-sizing: border-box; }

    /* ── Layout Full Width ────────────────────────────────────────────── */
    .pkmu-wrap {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 1rem 1.5rem;
    }

    /* Hapus pembatas container */
    .container, .container-fluid {
        max-width: 100%;
        width: 100%;
    }

    /* Content wrapper full width */
    .content-wrapper {
        padding: 0;
        margin: 0;
        width: 100%;
    }

    /* ── Page header ───────────────────────────────────────────────────── */
    .pkmu-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.5rem; background: var(--clr-surface);
        border-bottom: 1px solid var(--clr-border);
        box-shadow: var(--shadow-card);
        position: sticky; top: 0; z-index: 100;
        width: 100%;
    }
    .pkmu-header h1 { 
        font-size: 1.25rem; font-weight: 700; letter-spacing: -.01em; 
        margin: 0; display: flex; align-items: center; gap: 8px;
    }
    .pkmu-header .badge-no {
        font-family: var(--font-mono); font-size: .8rem; font-weight: 600;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: var(--clr-primary); border: 1px solid #bfdbfe;
        border-radius: 8px; padding: .35rem .85rem;
    }

    /* ── Grid 2 columns full width ─────────────────────────────────────── */
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        width: 100%;
    }

    @media (max-width: 992px) {
        .grid-2 { 
            grid-template-columns: 1fr; 
            gap: 1rem; 
        }
    }

    /* ── Card full width ───────────────────────────────────────────────── */
    .card {
        background: var(--clr-surface); 
        border-radius: var(--radius-card);
        border: 1px solid var(--clr-border); 
        box-shadow: var(--shadow-card);
        padding: 1.5rem; 
        margin-bottom: 1.5rem;
        transition: transform var(--transition), box-shadow var(--transition);
        width: 100%;
    }
    .card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); }
    
    .card-title {
        font-size: .75rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--clr-muted); margin-bottom: 1.25rem;
        display: flex; align-items: center; gap: .6rem;
        border-left: 3px solid var(--clr-primary);
        padding-left: 0.75rem;
    }
    .card-title svg { width: 16px; height: 16px; color: var(--clr-primary); }

    /* ── Form controls ─────────────────────────────────────────────────── */
    .form-group { 
        display: flex; 
        flex-direction: column; 
        gap: .4rem; 
        margin-bottom: 1rem; 
        width: 100%;
    }
    .form-group:last-child { margin-bottom: 0; }

    label {
        font-size: .75rem; font-weight: 600; color: var(--clr-muted);
        text-transform: uppercase; letter-spacing: .03em;
    }
    label .req { color: var(--clr-accent); margin-left: 3px; }

    .form-control {
        width: 100%; 
        padding: .6rem .85rem;
        border: 1.5px solid var(--clr-border); 
        border-radius: var(--radius-input);
        font-size: .875rem; 
        color: var(--clr-text); 
        background: #fff;
        transition: all var(--transition);
    }
    .form-control:focus {
        outline: none; border-color: var(--clr-primary);
        box-shadow: 0 0 0 3px var(--clr-scan-ring);
    }
    .form-control-static {
        width: 100%;
        padding: .6rem .85rem; 
        background: #f8fafc; 
        border: 1.5px solid var(--clr-border);
        border-radius: var(--radius-input); 
        font-size: .875rem;
        font-family: var(--font-mono); 
        color: var(--clr-primary); 
        font-weight: 600;
    }
    select.form-control { 
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M0 0l6 6 6-6z' fill='%2364748b'/%3E%3C/svg%3E"); 
        background-repeat: no-repeat; 
        background-position: right .85rem center; 
        padding-right: 2rem; 
        cursor: pointer;
    }

    /* ── Scan field ────────────────────────────────────────────────────── */
    .scan-wrap {
        display: flex; 
        gap: .75rem; 
        align-items: stretch; 
        margin-bottom: 1rem;
        width: 100%;
    }
    .scan-input {
        flex: 1; 
        font-family: var(--font-mono); 
        font-size: .9rem;
        border: 2px solid var(--clr-primary); 
        border-radius: var(--radius-input);
        padding: .7rem 1rem; 
        color: var(--clr-text);
        transition: all var(--transition);
    }
    .scan-input:focus {
        outline: none; 
        box-shadow: 0 0 0 4px var(--clr-scan-ring);
    }
    .scan-input.error { 
        border-color: var(--clr-accent); 
        background: #fef2f2; 
    }
    .scan-input.success { 
        border-color: var(--clr-success); 
        background: #f0fdf4; 
    }

    .btn-scan {
        padding: .7rem 1.5rem; 
        background: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-primary-dk) 100%);
        color: #fff; 
        border: none; 
        border-radius: var(--radius-input); 
        cursor: pointer;
        font-size: .85rem; 
        font-weight: 600; 
        white-space: nowrap;
        transition: all var(--transition); 
        display: flex; 
        align-items: center; 
        gap: .5rem;
    }
    .btn-scan:hover { 
        transform: translateY(-2px); 
        box-shadow: var(--shadow-btn); 
    }
    .btn-scan:active { transform: translateY(0); }

    .scan-msg {
        font-size: .78rem; 
        min-height: 1.5em; 
        padding: .3rem 0;
        font-weight: 500;
    }
    .scan-msg.ok  { color: var(--clr-success); }
    .scan-msg.err { color: var(--clr-accent); }

    /* ── Table styles ─────────────────────────────────────────────────── */
    .table-wrap { 
        overflow-x: auto; 
        margin-top: 1rem; 
        border-radius: var(--radius-input);
        border: 1px solid var(--clr-border);
        width: 100%;
    }
    .tbl {
        width: 100%; 
        border-collapse: collapse; 
        font-size: .825rem;
        min-width: 600px;
    }
    .tbl thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        font-size: .7rem; 
        text-transform: uppercase; 
        letter-spacing: .06em;
        font-weight: 700; 
        color: var(--clr-muted);
        padding: .8rem .75rem; 
        border-bottom: 2px solid var(--clr-border);
        white-space: nowrap; 
        text-align: left;
    }
    .tbl tbody td {
        padding: .7rem .75rem; 
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .tbl tbody tr { transition: background var(--transition); }
    .tbl tbody tr:hover { background: var(--clr-row-hover); }
    .tbl tbody tr.new-row { animation: slideIn .3s ease; }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-10px); }
        to   { opacity: 1; transform: translateX(0); }
    }

    .badge-jenis {
        display: inline-block; 
        padding: .2rem .6rem;
        border-radius: 6px; 
        font-size: .7rem; 
        font-weight: 600;
        background: #eff6ff; 
        color: var(--clr-primary); 
        border: 1px solid #bfdbfe;
    }
    .badge-empty {
        color: var(--clr-muted); 
        font-style: italic; 
        font-size: .8rem;
    }

    .btn-hapus {
        background: none; 
        border: 1px solid #fecaca; 
        border-radius: 6px;
        color: var(--clr-accent); 
        padding: .35rem .7rem; 
        cursor: pointer;
        font-size: .7rem; 
        font-weight: 600; 
        transition: all var(--transition);
        display: inline-flex; 
        align-items: center; 
        gap: .4rem;
    }
    .btn-hapus:hover { 
        background: #fee2e2; 
        color: #991b1b; 
        transform: scale(1.05); 
    }

    /* ── Summary bar ───────────────────────────────────────────────────── */
    .summary-bar {
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe; 
        border-radius: 10px;
        padding: .8rem 1.2rem; 
        margin-top: 1rem;
        width: 100%;
    }
    .summary-bar .lbl { 
        font-size: .8rem; 
        font-weight: 600; 
        color: var(--clr-muted); 
    }
    .summary-bar .val { 
        font-size: 1.4rem; 
        font-weight: 800; 
        color: var(--clr-primary); 
    }

    /* ── Action bar ───────────────────────────────────────────────────── */
    .action-bar {
        display: flex; 
        gap: 1rem; 
        justify-content: flex-end;
        padding: 1rem 1.5rem; 
        background: var(--clr-surface);
        border-top: 1px solid var(--clr-border); 
        position: sticky; 
        bottom: 0;
        box-shadow: 0 -4px 12px rgba(0,0,0,.05); 
        z-index: 90;
        width: 100%;
    }

    .btn {
        padding: .65rem 1.6rem; 
        border-radius: var(--radius-input);
        font-size: .875rem; 
        font-weight: 600; 
        cursor: pointer;
        border: 1.5px solid transparent; 
        transition: all var(--transition);
        display: inline-flex; 
        align-items: center; 
        gap: .5rem;
    }
    .btn-primary {
        background: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-primary-dk) 100%);
        color: #fff; 
        box-shadow: var(--shadow-btn);
    }
    .btn-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 4px 12px rgba(26,86,219,.4); 
    }
    .btn-primary:disabled { 
        opacity: 0.6; 
        cursor: not-allowed; 
        transform: none; 
    }

    .btn-outline {
        background: #fff; 
        color: var(--clr-text); 
        border-color: var(--clr-border);
    }
    .btn-outline:hover { 
        background: var(--clr-bg); 
        border-color: var(--clr-primary); 
    }

    /* ── Alert & Error ─────────────────────────────────────────────────── */
    .alert {
        border-radius: var(--radius-input); 
        padding: .9rem 1.2rem; 
        margin-bottom: 1rem;
        font-size: .85rem; 
        display: flex; 
        align-items: center; 
        gap: .8rem;
        width: 100%;
    }
    .alert-success { 
        background: #f0fdf4; 
        border: 1px solid #bbf7d0; 
        color: #14532d; 
    }
    .alert-error   { 
        background: #fef2f2; 
        border: 1px solid #fecaca; 
        color: #7f1d1d; 
    }

    /* ── Spinner ───────────────────────────────────────────────────────── */
    .spinner {
        display: inline-block; 
        width: 14px; 
        height: 14px;
        border: 2px solid rgba(255,255,255,.3); 
        border-top-color: #fff;
        border-radius: 50%; 
        animation: spin .6s linear infinite;
    }
    @keyframes spin { 
        to { transform: rotate(360deg); } 
    }

    /* ── Back button ───────────────────────────────────────────────────── */
    .back-button {
        display: inline-flex; 
        align-items: center; 
        gap: 0.5rem;
        padding: 0.5rem 1rem; 
        background: #f1f5f9;
        border-radius: 8px; 
        text-decoration: none; 
        color: var(--clr-text);
        font-size: 0.875rem; 
        font-weight: 500;
        transition: all var(--transition);
    }
    .back-button:hover {
        background: #e2e8f0; 
        transform: translateX(-3px);
    }

    /* ── Error text ────────────────────────────────────────────────────── */
    .error-text {
        font-size: 0.7rem; 
        color: var(--clr-accent); 
        margin-top: 0.25rem;
    }

    /* ── Body full width ───────────────────────────────────────────────── */
    body {
        background: var(--clr-bg);
        font-family: var(--font-main);
        color: var(--clr-text);
        width: 100%;
        overflow-x: hidden;
    }

    /* Main content full width */
    .main-content, #app, .wrapper {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="pkmu-header">
    <h1>
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 17H7A5 5 0 0 1 7 7h2"/>
            <path d="M15 7h2a5 5 0 0 1 0 10h-2"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Edit Pengeluaran Kantong
    </h1>
    <div style="display:flex; gap:1rem; align-items:center;">
        <span class="badge-no">{{ $nomorKeluar }}</span>
        <a href="{{ route('unit.pengeluaran_mobile_unit.index') }}" class="back-button">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali
        </a>
    </div>
</div>

<div class="pkmu-wrap">
    @if(session('success'))
    <div class="alert alert-success">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-error">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    </div>
    @endif

    <form id="frmPengeluaran" method="POST" action="{{ route('unit.pengeluaran_mobile_unit.update', $data->id) }}">
        @csrf
        @method('PUT')
        
        <div class="grid-2">
            <!-- Kolom 1: Nomor & Tanggal -->
            <div class="card">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Informasi Dasar
                </div>
                <div class="form-group">
                    <label>Nomor Pengeluaran</label>
                    <div class="form-control-static">
                        <svg width="14" height="14" style="display:inline;margin-right:6px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                        </svg>
                        {{ $data->no_keluar }}
                    </div>
                </div>
                <div class="form-group">
                    <label>Tanggal Pengeluaran <span class="req">*</span></label>
                    <input type="date" name="tgl_keluar" class="form-control @error('tgl_keluar') error @enderror"
                           value="{{ old('tgl_keluar', $data->tgl_keluar instanceof \Carbon\Carbon ? $data->tgl_keluar->format('Y-m-d') : $data->tgl_keluar) }}" required>
                    @error('tgl_keluar')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Tujuan / Instansi</label>
                    <input type="text" name="tujuan" class="form-control"
                           value="{{ old('tujuan', $data->tujuan) }}" 
                           placeholder="Contoh: RSUD Dr. Soetomo, PMI Kota Surabaya...">
                </div>
            </div>

            <!-- Kolom 2: Data Pengiriman -->
            <div class="card">
                <div class="card-title">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Data Pengiriman
                </div>
                <div class="form-group">
                    <label>Petugas <span class="req">*</span></label>
                    <select name="petugas_id" class="form-control @error('petugas_id') error @enderror" required>
                        <option value="">— Pilih Petugas —</option>
                        @foreach($petugasList as $p)
                        <option value="{{ $p->id }}" {{ old('petugas_id', $data->petugas_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->kode }} – {{ $p->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('petugas_id')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Nomor Permintaan <span class="req">*</span></label>
                    <select name="permintaan_mobile_unit_id" class="form-control @error('permintaan_mobile_unit_id') error @enderror" required>
                        <option value="">— Pilih No. Permintaan —</option>
                        @foreach($permintaans as $pmu)
                        <option value="{{ $pmu->id }}" {{ old('permintaan_mobile_unit_id', $data->permintaan_mobile_unit_id) == $pmu->id ? 'selected' : '' }}>
                            {{ $pmu->nomor }} ({{ \Carbon\Carbon::parse($pmu->tanggal)->format('d/m/Y') }})
                        </option>
                        @endforeach
                    </select>
                    @error('permintaan_mobile_unit_id')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Asal Darah <span class="req">*</span></label>
                    <select name="asal_darah_id" class="form-control @error('asal_darah_id') error @enderror" required>
                        <option value="">— Pilih Asal Darah —</option>
                        @foreach($asalDarahs as $ad)
                        <option value="{{ $ad->id }}" {{ old('asal_darah_id', $data->asal_darah_id) == $ad->id ? 'selected' : '' }}>
                            {{ $ad->kode }} – {{ $ad->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('asal_darah_id')<div class="error-text">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label>Mobil Unit <span class="req">*</span></label>
                    <select name="mobile_unit_id" class="form-control @error('mobile_unit_id') error @enderror" required>
                        <option value="">— Pilih Mobil Unit —</option>
                        @foreach($mobilUnits as $mu)
                        <option value="{{ $mu->id }}" {{ old('mobile_unit_id', $data->mobile_unit_id) == $mu->id ? 'selected' : '' }}>
                            {{ $mu->kode }} – {{ $mu->merk_mobil }} ({{ $mu->no_polisi }})
                        </option>
                        @endforeach
                    </select>
                    @error('mobile_unit_id')<div class="error-text">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <!-- Section Scan Kantong -->
        <div class="card">
            <div class="card-title">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="3" width="4" height="18"/>
                    <rect x="7" y="5" width="2" height="14"/>
                    <rect x="11" y="3" width="3" height="18"/>
                    <rect x="16" y="5" width="1" height="14"/>
                    <rect x="19" y="3" width="2" height="18"/>
                </svg>
                Daftar Kantong
            </div>

            <div class="scan-wrap">
                <input type="text" id="scanInput" class="scan-input" 
                       placeholder="Scan barcode atau ketik nomor kantong, lalu tekan Enter..."
                       autocomplete="off" autofocus>
                <button type="button" id="btnAddKantong" class="btn-scan">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Kantong
                </button>
            </div>
            <div class="scan-msg" id="scanMsg"></div>

            <div class="table-wrap">
                <table class="tbl" id="tblKantong">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>No. Kantong</th>
                            <th>Merk</th>
                            <th>Jenis</th>
                            <th>Ukuran</th>
                            <th>No. Lot</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tblKantongBody">
                        @forelse($kantongItems as $i => $item)
                        <tr data-nokantong="{{ $item['no_kantong'] }}">
                            <td>{{ $i + 1 }}</td>
                            <td><strong style="font-family:var(--font-mono);">{{ $item['no_kantong'] }}</strong></td>
                            <td>{{ $item['merk'] ?? '-' }}</td>
                            <td><span class="badge-jenis">{{ $item['jenis'] ?? '-' }}</span></td>
                            <td>{{ $item['ukuran'] ?? '-' }}</td>
                            <td>{{ $item['no_lot'] ?? '-' }}</td>
                            <td>
                                <button type="button" class="btn-hapus btn-remove" data-nokantong="{{ $item['no_kantong'] }}">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="7" style="text-align:center; padding: 2rem;">
                                <span class="badge-empty">
                                    <svg width="20" height="20" style="display:inline;margin-right:8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    Belum ada kantong. Scan atau ketik nomor kantong di atas.
                                </span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="summary-bar" id="summaryBar" style="{{ count($kantongItems) ? '' : 'display:none' }}">
                <span class="lbl">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="3" width="20" height="18" rx="2"/>
                        <line x1="8" y1="9" x2="16" y2="9"/>
                        <line x1="8" y1="13" x2="12" y2="13"/>
                    </svg>
                    Total Kantong
                </span>
                <span class="val" id="totalKantong">{{ count($kantongItems) }}</span>
            </div>
        </div>

        <!-- Section Keterangan -->
        <div class="card">
            <div class="card-title">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                Keterangan Tambahan
            </div>
            <textarea name="keterangan" class="form-control" rows="3" 
                      placeholder="Catatan penting atau informasi tambahan...">{{ old('keterangan', $data->keterangan) }}</textarea>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <a href="{{ route('unit.pengeluaran_mobile_unit.index') }}" class="btn btn-outline">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btnSimpan">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Update Pengeluaran
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // DOM Elements
    const scanInput   = document.getElementById('scanInput');
    const btnAdd      = document.getElementById('btnAddKantong');
    const scanMsg     = document.getElementById('scanMsg');
    const tbody       = document.getElementById('tblKantongBody');
    const summaryBar  = document.getElementById('summaryBar');
    const totalEl     = document.getElementById('totalKantong');
    const form        = document.getElementById('frmPengeluaran');
    const btnSimpan   = document.getElementById('btnSimpan');

    let rowCount = {{ count($kantongItems) }};

    function setMsg(text, type) {
        scanMsg.textContent = text;
        scanMsg.className = 'scan-msg ' + type;
        setTimeout(() => { 
            scanMsg.textContent = ''; 
            scanMsg.className = 'scan-msg'; 
        }, 3000);
    }

    function setScanState(state) {
        scanInput.classList.remove('error', 'success');
        if (state) scanInput.classList.add(state);
        setTimeout(() => scanInput.classList.remove('error', 'success'), 2000);
    }

    function updateSummary() {
        const rows = tbody.querySelectorAll('tr[data-nokantong]');
        rowCount = rows.length;
        if (totalEl) totalEl.textContent = rowCount;
        if (summaryBar) summaryBar.style.display = rowCount > 0 ? 'flex' : 'none';
        
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow && rowCount > 0) emptyRow.remove();
        if (!emptyRow && rowCount === 0 && tbody) {
            const tr = document.createElement('tr');
            tr.id = 'emptyRow';
            tr.innerHTML = `<td colspan="7" style="text-align:center; padding: 2rem;">
                <span class="badge-empty">
                    <svg width="20" height="20" style="display:inline;margin-right:8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Belum ada kantong. Scan atau ketik nomor kantong di atas.
                </span>
            </td>`;
            tbody.appendChild(tr);
        }
    }

    function escapeHtml(str) {
        if (!str) return str;
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    function buildRow(item, idx) {
        const tr = document.createElement('tr');
        tr.dataset.nokantong = item.no_kantong;
        tr.classList.add('new-row');
        tr.innerHTML = `
            <td style="text-align:center; font-weight:600;">${idx}</td>
            <td><strong style="font-family:var(--font-mono);">${escapeHtml(item.no_kantong)}</strong></td>
            <td>${escapeHtml(item.merk ?? '-')}</td>
            <td><span class="badge-jenis">${escapeHtml(item.jenis ?? '-')}</span></td>
            <td>${escapeHtml(item.ukuran ?? '-')}</td>
            <td>${escapeHtml(item.no_lot ?? '-')}</td>
            <td>
                <button type="button" class="btn-hapus btn-remove" data-nokantong="${escapeHtml(item.no_kantong)}">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    Hapus
                </button>
            </td>
        `;
        return tr;
    }

    async function doAddKantong() {
        const val = scanInput.value.trim();
        if (!val) {
            scanInput.focus();
            setMsg('✗ Masukkan nomor kantong terlebih dahulu', 'err');
            return;
        }

        btnAdd.disabled = true;
        const originalHtml = btnAdd.innerHTML;
        btnAdd.innerHTML = '<span class="spinner"></span> Memeriksa...';

        try {
            const res = await fetch('{{ route("unit.pengeluaran_mobile_unit.scan-kantong") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ no_kantong: val }),
            });
            const json = await res.json();

            if (json.success) {
                setScanState('success');
                setMsg('✓ ' + json.message, 'ok');
                if (tbody) {
                    tbody.innerHTML = '';
                    if (json.items && json.items.length > 0) {
                        json.items.forEach((item, i) => tbody.appendChild(buildRow(item, i + 1)));
                    }
                }
                updateSummary();
                scanInput.value = '';
            } else {
                setScanState('error');
                setMsg('✗ ' + json.message, 'err');
            }
        } catch (e) {
            setScanState('error');
            setMsg('✗ Gagal terhubung ke server. Periksa koneksi.', 'err');
            console.error(e);
        } finally {
            btnAdd.disabled = false;
            btnAdd.innerHTML = originalHtml;
            scanInput.focus();
        }
    }

    if (btnAdd) btnAdd.addEventListener('click', doAddKantong);
    if (scanInput) {
        scanInput.addEventListener('keydown', e => { 
            if (e.key === 'Enter') { 
                e.preventDefault(); 
                doAddKantong(); 
            } 
        });
    }

    // Remove kantong
    if (tbody) {
        tbody.addEventListener('click', async e => {
            const btn = e.target.closest('.btn-remove');
            if (!btn) return;
            
            const noKantong = btn.dataset.nokantong;
            if (!confirm(`Hapus kantong ${noKantong} dari daftar?`)) return;
            
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '...';

            try {
                const res = await fetch('{{ route("unit.pengeluaran_mobile_unit.remove-kantong") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ no_kantong: noKantong }),
                });
                const json = await res.json();
                
                if (json.success) {
                    if (tbody) tbody.innerHTML = '';
                    if (json.items && json.items.length > 0 && tbody) {
                        json.items.forEach((item, i) => tbody.appendChild(buildRow(item, i + 1)));
                    }
                    updateSummary();
                    setMsg('✓ Kantong berhasil dihapus', 'ok');
                } else {
                    setMsg('✗ Gagal menghapus kantong', 'err');
                }
            } catch (e) {
                setMsg('✗ Error: ' + e.message, 'err');
            } finally {
                if (btn.parentElement) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            }
        });
    }

    // Form validation before submit
    if (form) {
        form.addEventListener('submit', function(e) {
            const items = tbody ? tbody.querySelectorAll('tr[data-nokantong]') : [];
            if (items.length === 0) {
                e.preventDefault();
                setMsg('✗ Minimal satu kantong harus ditambahkan sebelum menyimpan.', 'err');
                if (scanInput) scanInput.focus();
                return;
            }
            
            if (btnSimpan) {
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<span class="spinner"></span> Menyimpan...';
            }
        });
    }

    // Focus scan input on page load
    if (scanInput) scanInput.focus();
})();
</script>
@endpush