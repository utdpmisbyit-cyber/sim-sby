@extends('layouts.index')
@php
    $record = $record ?? null;
@endphp

@section('title', isset($record) ? 'Edit Pengiriman Darah Prolis' : 'Tambah Pengiriman Darah Prolis')

@push('styles')
<style>
:root {
    --pd-red:    #c0392b;
    --pd-border: #e2e8f0;
    --pd-gray:   #64748b;
    --pd-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.pd-form-page   { 
    padding: 1.25rem; 
    background: #f1f5f9; 
    min-height: 100vh; 
}
.pd-form-card   { 
    background: #fff; 
    border-radius: .75rem; 
    border: 1px solid var(--pd-border);
    box-shadow: var(--pd-shadow); 
    max-width: 1400px; /* Diperlebar dari 900px ke 1400px */
    width: 100%;
    margin: 0 auto; 
    overflow: hidden; 
}
.pd-form-header { 
    background: linear-gradient(135deg,#c0392b,#962d22);
    padding: 1rem 1.5rem; 
    display: flex; 
    align-items: center; 
    gap: .75rem; 
}
.pd-form-header h2 { 
    color: #fff; 
    font-size: 1.1rem; 
    font-weight: 700; 
    margin: 0; 
}
.pd-form-body   { 
    padding: 1.5rem 1.5rem; 
}
.pd-section     { 
    margin-bottom: 2rem; 
}
.pd-section-title { 
    font-size: .8rem; 
    font-weight: 700; 
    text-transform: uppercase;
    letter-spacing: .07em; 
    color: var(--pd-gray); 
    margin-bottom: 1rem;
    padding-bottom: .5rem; 
    border-bottom: 2px solid #fee2e2; 
}
.pd-grid        { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Diperlebar dari 190px ke 250px */
    gap: 1rem; /* Diperbesar gap dari .75rem ke 1rem */
}
.pd-field label { 
    font-size: .8rem; 
    font-weight: 600; 
    color: #475569;
    display: block; 
    margin-bottom: .4rem; 
}
.pd-field label .req { 
    color: var(--pd-red); 
    margin-left: 2px; 
}
.pd-ctrl        { 
    width: 100%; 
    border: 1px solid var(--pd-border); 
    border-radius: .45rem;
    padding: .55rem .75rem; /* Diperbesar padding */
    font-size: .88rem; /* Diperbesar font */
    color: #1e293b;
    background: #fff; 
    transition: border-color .15s,box-shadow .15s; 
}
.pd-ctrl:focus  { 
    outline: none; 
    border-color: var(--pd-red);
    box-shadow: 0 0 0 3px rgba(192,57,43,.1); 
}
.pd-ctrl.is-invalid { 
    border-color: #f87171; 
}
.pd-error       { 
    font-size: .72rem; 
    color: #ef4444; 
    margin-top: .25rem; 
}
textarea.pd-ctrl { 
    resize: vertical; 
    min-height: 100px; 
}
.pd-form-actions { 
    display: flex; 
    gap: .6rem; 
    justify-content: flex-end;
    padding: 1rem 1.5rem; 
    border-top: 1px solid var(--pd-border);
    background: #fafafa; 
}
.pd-btn          { 
    display: inline-flex; 
    align-items: center; 
    gap: .4rem;
    padding: .55rem 1.25rem; 
    border-radius: .45rem; 
    font-size: .88rem;
    font-weight: 600; 
    cursor: pointer; 
    border: none; 
    transition: filter .15s; 
}
.pd-btn-primary  { 
    background: var(--pd-red); 
    color: #fff; 
}
.pd-btn-primary:hover { 
    filter: brightness(1.1); 
}
.pd-btn-ghost    { 
    background: #f1f5f9; 
    color: #475569;
    border: 1px solid var(--pd-border); 
    text-decoration: none; 
}
.pd-btn-ghost:hover { 
    background: #e2e8f0; 
}

/* ── Scan Section ── */
.scan-wrapper       { 
    background: #f8fafc; 
    border: 1px solid var(--pd-border);
    border-radius: .6rem; 
    overflow: hidden; 
    margin-top: 1rem;
}

.scan-toggle-bar    { 
    display: flex; 
    align-items: center; 
    gap: 1rem;
    padding: 0.85rem 1.25rem; 
    background: #fff;
    border-bottom: 1px solid var(--pd-border); 
    flex-wrap: wrap; 
}

/* Toggle switch */
.tc-toggle          { 
    position: relative; 
    display: inline-block; 
    width: 48px; 
    height: 26px;
    flex-shrink: 0; 
}
.tc-toggle input    { 
    opacity: 0; 
    width: 0; 
    height: 0; 
}
.tc-slider          { 
    position: absolute; 
    inset: 0; 
    background: #cbd5e1; 
    border-radius: 99px;
    cursor: pointer; 
    transition: background .2s; 
}
.tc-slider:before   { 
    content: ''; 
    position: absolute; 
    width: 20px; 
    height: 20px;
    left: 3px; 
    bottom: 3px; 
    background: #fff; 
    border-radius: 50%;
    transition: transform .2s; 
    box-shadow: 0 1px 3px rgba(0,0,0,.2); 
}
.tc-toggle input:checked + .tc-slider          { 
    background: #2563eb; 
}
.tc-toggle input:checked + .tc-slider:before   { 
    transform: translateX(22px); 
}

.tc-label           { 
    font-size: .9rem; 
    font-weight: 700; 
    color: #334155; 
}

.scan-mode-badge    { 
    margin-left: auto; 
    padding: .3rem .85rem; 
    border-radius: 99px;
    font-size: .78rem; 
    font-weight: 700; 
}
.scan-mode-kantong  { 
    background: #fdecea; 
    color: #c0392b; 
}
.scan-mode-stok     { 
    background: #eff6ff; 
    color: #2563eb; 
}

/* Scan box */
.scan-input-area    { 
    padding: 1rem 1.25rem; 
}
.scan-box           { 
    border: 2px dashed #fca5a5; 
    border-radius: .55rem;
    padding: 1rem; 
    background: #fff; 
    transition: border-color .2s; 
}
.scan-box-stok      { 
    border-color: #93c5fd; 
}
.scan-box-label     { 
    display: flex; 
    align-items: center; 
    gap: .5rem; 
    font-size: .8rem;
    font-weight: 700; 
    color: #c0392b; 
    margin-bottom: .75rem;
    text-transform: uppercase; 
    letter-spacing: .05em; 
}
.scan-input-row     { 
    display: flex; 
    gap: 0.75rem; 
}
.scan-ctrl          { 
    font-size: 1rem !important; 
    font-weight: 600;
    letter-spacing: .04em; 
    flex: 1; 
}
.scan-ctrl-stok     { 
    border-color: #93c5fd !important; 
}
.scan-ctrl-stok:focus { 
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,.12) !important; 
}
.scan-fire-btn      { 
    display: inline-flex; 
    align-items: center; 
    gap: .5rem;
    padding: .55rem 1.25rem; 
    background: #c0392b; 
    color: #fff;
    border: none; 
    border-radius: .45rem; 
    font-size: .88rem;
    font-weight: 700; 
    cursor: pointer; 
    white-space: nowrap;
    transition: filter .15s; 
    flex-shrink: 0; 
}
.scan-fire-btn:hover { 
    filter: brightness(1.1); 
}
.scan-fire-stok     { 
    background: #2563eb; 
}

/* Status */
.scan-status        { 
    margin-top: .75rem; 
    padding: .6rem .9rem; 
    border-radius: .4rem;
    font-size: .85rem; 
    font-weight: 600; 
}

/* Preview */
.scan-preview       { 
    margin-top: .85rem; 
    border: 1px solid #bbf7d0; 
    border-radius: .5rem;
    background: #f0fdf4; 
    overflow: hidden; 
}
.scan-preview-title { 
    display: flex; 
    align-items: center; 
    gap: .5rem; 
    padding: .6rem .9rem;
    background: #dcfce7; 
    font-size: .8rem; 
    font-weight: 700;
    color: #15803d; 
    border-bottom: 1px solid #bbf7d0; 
}
.scan-preview-grid  { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: .6rem; 
    padding: .85rem .9rem; 
}
.scan-preview-item  { 
    font-size: .82rem; 
}
.scan-preview-item .spk { 
    color: #64748b; 
    font-weight: 600; 
    display: block;
    text-transform: uppercase; 
    font-size: .72rem; 
    margin-bottom: .15rem; 
}
.scan-preview-item .spv { 
    color: #0f172a; 
    font-weight: 700; 
}

/* Field Suhu */
.pd-field-suhu {
    grid-column: span 1;
}
.suhu-display {
    display: inline-block;
    margin-left: 10px;
    font-size: 0.75rem;
    padding: 3px 8px;
    border-radius: 12px;
    background: #e2e8f0;
    color: #475569;
}
.suhu-normal {
    background: #10b98120;
    color: #10b981;
}
.suhu-warning {
    background: #f59e0b20;
    color: #f59e0b;
}
.suhu-danger {
    background: #ef444420;
    color: #ef4444;
}
.jenis-darah-dropdown option {
    padding: 6px 10px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .pd-form-card {
        max-width: 100%;
        margin: 0;
    }
    .pd-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    .scan-input-row {
        flex-direction: column;
    }
    .scan-fire-btn {
        justify-content: center;
    }
    .scan-preview-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="pd-form-page">
<div class="pd-form-card">

    {{-- Header --}}
    <div class="pd-form-header">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C12 2 5 10.5 5 15a7 7 0 0 0 14 0C19 10.5 12 2 12 2z"
                  fill="rgba(255,255,255,.85)"/>
        </svg>
        <h2>{{ isset($record) ? 'Edit' : 'Tambah' }} Pengiriman Darah Prolis</h2>
    </div>

    <form method="POST"
      id="pengirimanForm"
      action="{{ isset($record)
          ? route('produksi.pengiriman_darah_prolis.update', $record->id)
          : route('produksi.pengiriman_darah_prolis.store') }}">
    @csrf
    @if(isset($record)) @method('PUT') @endif

        <div class="pd-form-body">

            {{-- ── Identitas Pengiriman ── --}}
          <div class="pd-section">
                <div class="pd-section-title">Identitas Pengiriman</div>
                <div class="pd-grid">

                    <div class="pd-field">
                        <label>No Pengiriman</label>
                        <input class="pd-ctrl" name="no_pengiriman"
                            value="{{ old('no_pengiriman', $record->no_pengiriman ?? $noPengiriman ?? '') }}"
                            readonly style="background:#f8fafc;font-weight:700;">
                    </div>

                    <div class="pd-field">
                        <label>Tgl Pengiriman <span class="req">*</span></label>
                        <input class="pd-ctrl" type="date" name="tgl_pengiriman"
                            value="{{ old('tgl_pengiriman', isset($record) ? $record->tgl_pengiriman?->format('Y-m-d') : now()->format('Y-m-d')) }}">
                    </div>

                    {{-- ── Field Petugas ── --}}
                    <div class="pd-field" style="grid-column: span 2;">
                        <label>Petugas <span class="req">*</span></label>
                        <div style="position:relative;">

                            <div style="display:flex;gap:.5rem;align-items:center;">
                                <div style="position:relative;flex:1;">
                                    <span style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:#94a3b8;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    <input type="text"
                                        id="petugas_search"
                                        class="pd-ctrl"
                                        style="padding-left:2.2rem;"
                                        placeholder="Ketik nama atau kode petugas…"
                                        autocomplete="off"
                                        value="{{ isset($record) && $record->petugas ? $record->petugas->nama.' ('.$record->petugas->kode.')' : '' }}">
                                </div>

                                {{-- Tombol clear --}}
                                <button type="button" onclick="clearPetugas()"
                                        style="padding:.55rem .8rem;background:#f1f5f9;border:1px solid #e2e8f0;
                                            border-radius:.45rem;cursor:pointer;color:#64748b;font-size:.85rem;"
                                        title="Hapus pilihan">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Hidden input --}}
                            <input type="hidden" name="petugas_id" id="petugas_id"
                                value="{{ old('petugas_id', $record->petugas_id ?? '') }}">

                            {{-- Dropdown hasil pencarian --}}
                            <div id="petugas_dropdown"
                                style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;z-index:999;
                                        background:#fff;border:1px solid #e2e8f0;border-radius:.5rem;
                                        box-shadow:0 8px 24px rgba(0,0,0,.12);max-height:240px;overflow-y:auto;">
                            </div>
                        </div>

                        {{-- Badge terpilih --}}
                        <div id="petugas_badge" style="margin-top:.5rem;display:{{ (isset($record) && $record->petugas_id) ? 'flex' : 'none' }};
                                                    align-items:center;gap:.5rem;">
                            <span style="display:inline-flex;align-items:center;gap:.4rem;
                                        background:#eff6ff;border:1px solid #bfdbfe;
                                        border-radius:99px;padding:.3rem .85rem;
                                        font-size:.82rem;font-weight:700;color:#1d4ed8;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span id="petugas_badge_text">
                                    {{ isset($record) && $record->petugas ? $record->petugas->nama.' ('.$record->petugas->kode.')' : '' }}
                                </span>
                            </span>
                        </div>

                        @error('petugas_id')<div class="pd-error">{{ $message }}</div>@enderror
                    </div>

                </div>

                {{-- ── TC Pooled Toggle + Scan Box ── --}}
                <div class="scan-wrapper">

                    {{-- Toggle TC Pooled --}}
                    <div class="scan-toggle-bar">
                        <label class="tc-toggle">
                            <input type="checkbox" id="tc_pooled_check" name="tc_pooled" value="1" {{ old('tc_pooled', $record->tc_pooled ?? false) ? 'checked' : '' }}>
                            <span class="tc-slider"></span>
                        </label>
                        <span class="tc-label">TC Pooled</span>
                        <span id="scan-mode-badge" class="scan-mode-badge scan-mode-kantong">
                            Mode: Scan No Kantong
                        </span>
                    </div>

                    {{-- Scan Input Area --}}
                    <div class="scan-input-area" id="scan-area">

                        {{-- No Kantong (default tampil) --}}
                        <div id="box-kantong" class="scan-box scan-box-kantong">
                            <div class="scan-box-label">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                                Scan / Ketik No Kantong
                            </div>
                            <div class="scan-input-row">
                                <input class="pd-ctrl scan-ctrl"
                                    id="input_no_kantong"
                                    name="no_kantong"
                                    value="{{ old('no_kantong', $record->no_kantong ?? '') }}"
                                    placeholder="Arahkan scanner atau ketik no kantong…"
                                    autocomplete="off"
                                    autofocus>
                                <button type="button" class="scan-fire-btn" onclick="doScan()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    Cari
                                </button>
                            </div>
                        </div>

                        {{-- No Stok (tampil saat TC Pooled aktif) --}}
                        <div id="box-stok" class="scan-box scan-box-stok" style="display:none;">
                            <div class="scan-box-label" style="color:#2563eb;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>
                                </svg>
                                Scan / Ketik No Stok (TC Pooled)
                            </div>
                            <div class="scan-input-row">
                                <input class="pd-ctrl scan-ctrl scan-ctrl-stok"
                                    id="input_no_stok"
                                    name="no_stok"
                                    value="{{ old('no_stok', $record->no_stok ?? '') }}"
                                    placeholder="Arahkan scanner atau ketik no stok…"
                                    autocomplete="off">
                                <button type="button" class="scan-fire-btn scan-fire-stok" onclick="doScan()">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    Cari
                                </button>
                            </div>
                        </div>

                        {{-- Status hasil scan --}}
                        <div id="scan-status" class="scan-status" style="display:none;"></div>

                        {{-- Preview hasil scan --}}
                        <div id="scan-preview" class="scan-preview" style="display:none;">
                            <div class="scan-preview-title">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Data Ditemukan
                            </div>
                            <div class="scan-preview-grid" id="scan-preview-grid"></div>
                        </div>
                    </div>
                </div>
            </div>

           {{-- ── Data Darah ── --}}
                <div class="pd-section">
                    <div class="pd-section-title">Data Darah</div>
                    <div class="pd-grid">
                        <div class="pd-field">
                            <label>Jenis Darah <span class="req">*</span></label>
                            <select class="pd-ctrl jenis-darah-dropdown @error('jenis_darah') is-invalid @enderror"
                                    name="jenis_darah" id="jenis_darah_select">
                                <option value="">-- Pilih Jenis Darah --</option>
                                @foreach($jenisDarahList ?? [] as $id => $namaPendek)
                                    <option value="{{ $namaPendek }}" 
                                        data-id="{{ $id }}"
                                        data-nama="{{ $namaPendek }}"
                                        @selected(old('jenis_darah', $record->jenis_darah ?? '') == $namaPendek)>
                                        {{ $namaPendek }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="pd-field">
                            <label>No FPD</label>
                            <input class="pd-ctrl @error('no_fpd') is-invalid @enderror"
                                type="text"
                                name="no_fpd"
                                id="no_fpd_input"
                                value="{{ old('no_fpd', $record->no_fpd ?? '') }}"
                                placeholder="No FPD akan terisi otomatis saat scan"
                                readonly
                                style="background:#f8fafc;">
                            @error('no_fpd')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="pd-field">
                            <label>Golongan Darah</label>
                            <select class="pd-ctrl @error('golongan_darah') is-invalid @enderror"
                                    name="golongan_darah">
                                <option value="">-- Pilih --</option>
                                @foreach(['A','B','AB','O'] as $g)
                                    <option value="{{ $g }}"
                                        @selected(old('golongan_darah', $record->golongan_darah ?? '') == $g)>
                                        {{ $g }}
                                    </option>
                                @endforeach
                            </select>
                            @error('golongan_darah')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="pd-field">
                            <label>Rhesus</label>
                            <select class="pd-ctrl @error('rhesus') is-invalid @enderror"
                                    name="rhesus">
                                <option value="">-- Pilih --</option>
                                <option value="Positif" @selected(old('rhesus', $record->rhesus ?? '') == 'Positif')>Positif (+)</option>
                                <option value="Negatif" @selected(old('rhesus', $record->rhesus ?? '') == 'Negatif')>Negatif (-)</option>
                            </select>
                            @error('rhesus')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="pd-field">
                            <label>Nama Asal Darah</label>
                            <input class="pd-ctrl @error('nama_asal_darah') is-invalid @enderror"
                                name="nama_asal_darah"
                                id="nama_asal_darah_input"
                                value="{{ old('nama_asal_darah', $record->nama_asal_darah ?? '') }}"
                                placeholder="Nama / institusi…">
                            @error('nama_asal_darah')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        {{-- FIELD SUHU --}}
                        <div class="pd-field pd-field-suhu">
                            <label>Suhu Penyimpanan (°C) <span class="req">*</span></label>
                            <input class="pd-ctrl @error('suhu') is-invalid @enderror"
                                type="number" 
                                step="0.1"
                                name="suhu"
                                id="suhu_input"
                                value="{{ old('suhu', $record->suhu ?? '') }}"
                                placeholder="Contoh: 2.0, 4.5, -20"
                                autocomplete="off">
                            <span id="suhu_status" class="suhu-display"></span>
                            @error('suhu')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    {{-- Informasi suhu --}}
                    <div id="suhu_info" style="margin-top: 12px; padding: 10px 15px; background: #f0f9ff; border-radius: 8px; font-size: 0.8rem; color: #0369a1; display: none;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 6px;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span id="suhu_info_text"></span>
                    </div>
                </div>

                {{-- ── Detail Kantong ── --}}
                <div class="pd-section">
                    <div class="pd-section-title">Detail Kantong</div>
                    <div class="pd-grid">

                        {{-- TAMBAHKAN 3 FIELD INI --}}
                        <div class="pd-field">
                            <label>Tgl Aftap <span class="req">*</span></label>
                            <input class="pd-ctrl @error('tgl_aftap') is-invalid @enderror"
                                type="date" name="tgl_aftap" id="tgl_aftap_input"
                                value="{{ old('tgl_aftap', isset($record) ? $record->tgl_aftap?->format('Y-m-d') : now()->format('Y-m-d')) }}">
                            @error('tgl_aftap')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="pd-field">
                            <label>Tgl Produksi <span class="req">*</span></label>
                            <input class="pd-ctrl @error('tgl_produksi') is-invalid @enderror"
                                type="date" name="tgl_produksi" id="tgl_produksi_input"
                                value="{{ old('tgl_produksi', isset($record) ? $record->tgl_produksi?->format('Y-m-d') : now()->format('Y-m-d')) }}">
                            @error('tgl_produksi')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="pd-field">
                            <label>Tgl Expired <span class="req">*</span></label>
                            <input class="pd-ctrl @error('tgl_expired') is-invalid @enderror"
                                type="date" name="tgl_expired" id="tgl_expired_input"
                                value="{{ old('tgl_expired', isset($record) ? $record->tgl_expired?->format('Y-m-d') : now()->addDays(42)->format('Y-m-d')) }}">
                            @error('tgl_expired')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>

                <div class="pd-section">
                    <div class="pd-section-title">Detail Kantong</div>
                    <div class="pd-grid">
                        <div class="pd-field">
                            <label>Jumlah <span class="req">*</span></label>
                            <input class="pd-ctrl @error('jumlah') is-invalid @enderror"
                                type="text"
                                name="jumlah"
                                id="jumlah_input"
                                value="{{ old('jumlah', $record->jumlah ?? '1') }}"
                                placeholder="Jumlah akan terisi otomatis">
                            @error('jumlah')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="pd-field">
                            <label>GR (gram)</label>
                            <input class="pd-ctrl" type="text" name="gr" id="gr_input"
                                value="{{ old('gr', $record->gr ?? '') }}" placeholder="gram…">
                        </div>
                        
                        <div class="pd-field">
                            <label>ML (mililiter)</label>
                            <input class="pd-ctrl" type="text" name="ml" id="ml_input"
                                value="{{ old('ml', $record->ml ?? '') }}" placeholder="ml…">
                        </div>
                        
                        <div class="pd-field">
                            <label>Status</label>
                            <select class="pd-ctrl @error('status') is-invalid @enderror" name="status">
                                <option value="">-- Pilih --</option>
                                <option value="1" @selected(old('status', $record->status ?? '') == '1')>1 – Tersedia</option>
                                <option value="2" @selected(old('status', $record->status ?? '') == '2')>2 – Terpakai</option>
                                <option value="3" @selected(old('status', $record->status ?? '') == '3')>3 – Kadaluarsa</option>
                                <option value="4" @selected(old('status', $record->status ?? '') == '4')>4 – Rusak</option>
                            </select>
                            @error('status')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="pd-field">
                            <label>Skrining (SCR)</label>
                            <input class="pd-ctrl @error('skrining') is-invalid @enderror"
                                name="skrining"
                                id="skrining_input"
                                value="{{ old('skrining', $record->skrining ?? 'NEG') }}"
                                placeholder="NEG / POS…">
                            @error('skrining')<div class="pd-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                                  
            
                            {{-- ── Keterangan ── --}}
                            <div class="pd-section">
                                <div class="pd-section-title">Keterangan</div>
                                <div class="pd-field">
                                    <textarea class="pd-ctrl" name="keterangan"
                                              placeholder="Keterangan tambahan…" rows="4">{{ old('keterangan', $record->keterangan ?? '') }}</textarea>
                                </div>
                            </div>
            
                        </div>{{-- body --}}
            
                        <div class="pd-form-actions">
                            <a href="{{ route('produksi.pengiriman_darah_prolis.index') }}"
                               class="pd-btn pd-btn-ghost">Batal</a>
                            <button type="submit" class="pd-btn pd-btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                {{ isset($record) ? 'Perbarui' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
            
                </div>
                </div>
@push('scripts')
<script>
const SCAN_URL = "{{ route('produksi.pengiriman_darah_prolis.scan_kantong') }}";
let tcCheck, boxKantong, boxStok, modeBadge;

// ── Validate sebelum submit — HARUS di scope global ──
function validateFormBeforeSubmit() {
    const today      = new Date().toISOString().split('T')[0];
    const expiredDef = (() => { const d = new Date(); d.setDate(d.getDate()+42); return d.toISOString().split('T')[0]; })();

    const defaults = { tgl_aftap: today, tgl_produksi: today, tgl_expired: expiredDef, jenis: 'PRC' };
    Object.entries(defaults).forEach(([name, val]) => {
        const el = document.querySelector(`[name="${name}"]`);
        if (el && !el.value) el.value = val;
    });

    const required = [
        { name: 'tgl_pengiriman', label: 'Tgl Pengiriman' },
        { name: 'tgl_aftap',      label: 'Tgl Aftap' },
        { name: 'tgl_produksi',   label: 'Tgl Produksi' },
        { name: 'tgl_expired',    label: 'Tgl Expired' },
    ];

    let missing = [];
    required.forEach(({ name, label }) => {
        const el = document.querySelector(`[name="${name}"]`);
        if (!el || !el.value) {
            missing.push(label);
            if (el) el.classList.add('is-invalid');
        } else {
            if (el) el.classList.remove('is-invalid');
        }
    });

    if (missing.length) {
        showStatus(`⚠ Field wajib kosong: ${missing.join(', ')}`, 'warn');
        return false;
    }

    const jumlahEl = document.getElementById('jumlah_input');
    if (jumlahEl) {
        const jumlahVal = parseInt(jumlahEl.value);
        if (!jumlahEl.value || isNaN(jumlahVal) || jumlahVal <= 0) {
            jumlahEl.classList.add('is-invalid');
            jumlahEl.style.borderColor = '#f87171';
            showStatus('⚠ Jumlah harus diisi dan lebih dari 0.', 'warn');
            jumlahEl.focus();
            return false;
        } else {
            jumlahEl.classList.remove('is-invalid');
            jumlahEl.style.borderColor = '';
        }
    }

    return true;
}

async function doScan() {
    const isPooled  = tcCheck?.checked ?? false;
    const noKantong = !isPooled ? document.getElementById('input_no_kantong')?.value?.trim() : null;
    const noStok    =  isPooled ? document.getElementById('input_no_stok')?.value?.trim()    : null;

    if (!noKantong && !noStok) { showStatus('⚠ Masukkan nomor terlebih dahulu.', 'warn'); return; }

    showStatus('🔍 Mencari data…', 'info');
    hidePreview();

    try {
        const params = new URLSearchParams();
        if (noKantong) params.set('no_kantong', noKantong);
        if (noStok)    params.set('no_stok',    noStok);

        const res  = await fetch(`${SCAN_URL}?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const json = await res.json();

        if (!json.found) { showStatus(`⚠ ${json.message}`, 'warn'); return; }

        fillForm(json.data);

    } catch (err) {
        console.error(err);
        showStatus('✗ Gagal menghubungi server.', 'err');
    }
}

function fillForm(d) {
    const today      = new Date().toISOString().split('T')[0];
    const expiredDef = (() => { const x = new Date(); x.setDate(x.getDate()+42); return x.toISOString().split('T')[0]; })();
     // Jumlah — tampilkan 0 jika memang 0, bukan fallback ke 1
    const jumlahVal = (d.jumlah !== null && d.jumlah !== undefined && d.jumlah !== '')
        ? String(d.jumlah)
        : '0';

   
    const map = {
        no_kantong:      d.no_kantong,
        no_stok:         d.no_stok,
        jenis:           d.jenis_darah,
        golongan_darah:  d.golongan_darah,
        rhesus:          d.rhesus,
        nama_asal_darah: d.nama_asal_darah,
        no_fpd:          d.no_fpd,
        skrining:        d.skrining || 'NEG',
        suhu:            d.suhu,
        jumlah:          jumlahVal,
        gr:              d.gr      || '0',
        ml:              d.ml      || '0',
        status:          d.status  || '1',
        tgl_aftap:     (d.tgl_aftap    && d.tgl_aftap    !== '0000-00-00') ? d.tgl_aftap.substring(0,10)    : today,
        tgl_produksi:  (d.tgl_produksi && d.tgl_produksi !== '0000-00-00') ? d.tgl_produksi.substring(0,10) : today,
        tgl_expired:   (d.tgl_expired  && d.tgl_expired  !== '0000-00-00') ? d.tgl_expired.substring(0,10)  : expiredDef,
    };
     Object.entries(map).forEach(([name, val]) => {
        if (val === null || val === undefined) return;
        const el = document.querySelector(`[name="${name}"]`);
        if (el) {
            el.value = val;
            el.dispatchEvent(new Event('change'));
        }
    });

    // Highlight field jumlah jika nilainya 0 (perlu diisi manual)
    highlightJumlah(jumlahVal);
    function highlightJumlah(val) {
    const el = document.getElementById('jumlah_input');
    if (!el) return;

    if (!val || val === '0' || parseInt(val) === 0) {
        // Beri tanda merah — harus diisi manual
        el.style.borderColor = '#f87171';
        el.style.backgroundColor = '#fef2f2';
        el.title = 'Jumlah belum terdeteksi, silakan isi manual';
        // Fokus ke field jumlah agar user langsung tahu
        setTimeout(() => el.focus(), 300);

        showStatus('⚠ Data ditemukan, tapi jumlah = 0. Silakan isi jumlah manual.', 'warn');
    } else {
        // Hijau — sudah terisi otomatis
        el.style.borderColor = '#4ade80';
        el.style.backgroundColor = '#f0fdf4';
        el.title = `Jumlah terisi otomatis: ${val}`;
        setTimeout(() => {
            el.style.borderColor = '';
            el.style.backgroundColor = '';
        }, 2000);
    }
}
    // sync dropdown jenis darah
    const sel = document.getElementById('jenis_darah_select');
    if (sel && d.jenis_darah) {
        [...sel.options].forEach((opt, i) => {
            if (opt.value.toUpperCase() === d.jenis_darah.toUpperCase()) sel.selectedIndex = i;
        });
        sel.dispatchEvent(new Event('change'));
    }

    updateSuhuInfo();
    showPreview(d);
    showStatus('✓ Data ditemukan, silakan simpan.', 'ok');
}

function setVal(name, value) {
    if (value === null || value === undefined) return;
    const el = document.querySelector(`[name="${name}"]`);
    if (el) { el.value = value; el.dispatchEvent(new Event('change')); }
}

// ── Preview & Status ──
function showPreview(d) {
    const grid = document.getElementById('scan-preview-grid');
    if (!grid) return;
    const items = [
        ['No Kantong', d.no_kantong||'-'], ['No Stok', d.no_stok||'-'],
        ['No FPD', d.no_fpd||'-'], ['Jenis', d.jenis_darah||'-'],
        ['Gol Darah', d.golongan_darah||'-'], ['Rhesus', d.rhesus||'-'],
        ['Jumlah', d.jumlah||'1'], ['GR', d.gr||'0'], ['ML', d.ml||'0'],
        ['Asal Darah', d.nama_asal_darah||'-'], ['Skrining', d.skrining||'NEG'],
        ['Suhu', d.suhu ? d.suhu+' °C' : '-'],
        ['Tgl Aftap',    d.tgl_aftap    ? d.tgl_aftap.substring(0,10)    : '-'],
        ['Tgl Produksi', d.tgl_produksi ? d.tgl_produksi.substring(0,10) : '-'],
        ['Tgl Expired',  d.tgl_expired  ? d.tgl_expired.substring(0,10)  : '-'],
    ];
    grid.innerHTML = items.map(([k,v]) =>
        `<div class="scan-preview-item"><span class="spk">${k}</span><span class="spv">${v}</span></div>`
    ).join('');
    document.getElementById('scan-preview').style.display = 'block';
}

function hidePreview() {
    const el = document.getElementById('scan-preview');
    if (el) el.style.display = 'none';
}

function showStatus(msg, type) {
    const el = document.getElementById('scan-status');
    if (!el) return;
    const styles = {
        ok:   { bg:'#f0fdf4', color:'#16a34a', border:'#bbf7d0' },
        warn: { bg:'#fffbeb', color:'#d97706', border:'#fde68a' },
        err:  { bg:'#fef2f2', color:'#c0392b', border:'#fca5a5' },
        info: { bg:'#eff6ff', color:'#2563eb', border:'#bfdbfe' },
    };
    const s = styles[type] || styles.info;
    el.style.cssText = `display:block;background:${s.bg};color:${s.color};border:1px solid ${s.border}`;
    el.textContent = msg;
}

function clearStatus() {
    const el = document.getElementById('scan-status');
    if (el) el.style.display = 'none';
    hidePreview();
}

// ── TC Pooled toggle ──
function applyToggle() {
    if (!tcCheck || !boxKantong || !boxStok || !modeBadge) return;
    const p = tcCheck.checked;
    boxKantong.style.display = p ? 'none' : 'block';
    boxStok.style.display    = p ? 'block' : 'none';
    modeBadge.textContent    = p ? 'Mode: Scan No Stok (TC Pooled)' : 'Mode: Scan No Kantong';
    modeBadge.className      = 'scan-mode-badge ' + (p ? 'scan-mode-stok' : 'scan-mode-kantong');
    setTimeout(() => {
        const inp = p ? document.getElementById('input_no_stok') : document.getElementById('input_no_kantong');
        if (inp) inp.focus();
    }, 50);
    clearStatus();
}

// ── Suhu ──
const suhuReferensi = {
    WB:   { min:2,   max:6,   warning:'2-6°C' },
    PRC:  { min:2,   max:6,   warning:'2-6°C' },
    FFP:  { min:-30, max:-18, warning:'-30 s.d -18°C' },
    TC:   { min:20,  max:24,  warning:'20-24°C (suhu ruang)' },
    LP:   { min:2,   max:6,   warning:'2-6°C' },
    AP:   { min:-30, max:-18, warning:'-30 s.d -18°C' },
    CRYO: { min:-30, max:-18, warning:'-30 s.d -18°C' },
};

function updateSuhuInfo() {
    const sel   = document.getElementById('jenis_darah_select');
    const info  = document.getElementById('suhu_info');
    const text  = document.getElementById('suhu_info_text');
    const input = document.getElementById('suhu_input');
    if (!sel || !info || !text) return;
    const ref = suhuReferensi[sel.value?.toUpperCase()] || null;
    if (!ref) { info.style.display = 'none'; return; }
    text.innerHTML = `<strong>${sel.value}</strong> — Simpan pada suhu ${ref.warning}`;
    info.style.display = 'block';
    if (input) { input.placeholder = `${ref.min} – ${ref.max} °C`; if (input.value) validateSuhu(input.value, sel.value); }
}

function validateSuhu(val, jenis) {
    const badge = document.getElementById('suhu_status');
    if (!badge) return;
    if (!val) { badge.innerHTML = ''; badge.className = 'suhu-display'; return; }
    const n = parseFloat(val);
    if (isNaN(n)) { badge.innerHTML = '⚠ Format tidak valid'; badge.className = 'suhu-display suhu-danger'; return; }
    const ref = suhuReferensi[jenis?.toUpperCase()] || { min:2, max:6 };
    if (n >= ref.min && n <= ref.max) {
        badge.innerHTML = `✓ Normal (${ref.min}–${ref.max}°C)`; badge.className = 'suhu-display suhu-normal';
    } else {
        badge.innerHTML = `⚠ Di luar range! Ideal: ${ref.min}–${ref.max}°C`; badge.className = 'suhu-display suhu-danger';
    }
}
// ── Petugas Search ─────────────────────────────────────────────────────
const PETUGAS_URL = "{{ route('produksi.pengiriman_darah_prolis.search_petugas') }}";
let petugasTimer  = null;

function initPetugasSearch() {
    const input    = document.getElementById('petugas_search');
    const dropdown = document.getElementById('petugas_dropdown');
    if (!input || !dropdown) return;

    // Sudah ada nilai (mode edit) → tampilkan badge
    const hiddenEl = document.getElementById('petugas_id');
    if (hiddenEl?.value && input.value) {
        showPetugasBadge(input.value);
    }

    input.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(petugasTimer);

        if (!q) {
            dropdown.style.display = 'none';
            document.getElementById('petugas_id').value = '';
            hidePetugasBadge();
            return;
        }

        // Tampilkan loading
        dropdown.innerHTML = `
            <div style="padding:.75rem 1rem;color:#64748b;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     style="animation:spin 1s linear infinite;">
                    <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                </svg>
                Mencari…
            </div>`;
        dropdown.style.display = 'block';

        petugasTimer = setTimeout(() => fetchPetugas(q), 300);
    });

    // Tutup saat klik luar
    document.addEventListener('click', function (e) {
        if (!input.closest('.pd-field').contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Navigasi keyboard
    input.addEventListener('keydown', function (e) {
        const items = dropdown.querySelectorAll('.petugas-opt');
        const active = dropdown.querySelector('.petugas-opt.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = active ? active.nextElementSibling : items[0];
            if (next) { active?.classList.remove('active'); next.classList.add('active'); next.scrollIntoView({block:'nearest'}); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = active?.previousElementSibling;
            if (prev) { active.classList.remove('active'); prev.classList.add('active'); prev.scrollIntoView({block:'nearest'}); }
        } else if (e.key === 'Enter' && active) {
            e.preventDefault();
            active.click();
        } else if (e.key === 'Escape') {
            dropdown.style.display = 'none';
        }
    });
}

async function fetchPetugas(keyword) {
    const dropdown = document.getElementById('petugas_dropdown');
    if (!dropdown) return;

    try {
        const res  = await fetch(`${PETUGAS_URL}?q=${encodeURIComponent(keyword)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();

        if (!json.results?.length) {
            dropdown.innerHTML = `
                <div style="padding:.75rem 1rem;color:#94a3b8;font-size:.85rem;text-align:center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" style="vertical-align:middle;margin-right:4px;">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Petugas tidak ditemukan
                </div>`;
            return;
        }

        dropdown.innerHTML = json.results.map(p => `
            <div class="petugas-opt"
                 data-id="${p.id}"
                 data-text="${p.text}"
                 data-name="${p.name}"
                 style="padding:.65rem 1rem;cursor:pointer;font-size:.88rem;
                        border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:.6rem;
                        transition:background .1s;">
                <span style="width:32px;height:32px;border-radius:50%;background:#eff6ff;
                             display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <span>
                    <span style="display:block;font-weight:700;color:#1e293b;">${p.name}</span>
                    <span style="font-size:.78rem;color:#64748b;">${p.kode}</span>
                </span>
            </div>
        `).join('');

        // Event klik & hover tiap item
        dropdown.querySelectorAll('.petugas-opt').forEach(el => {
            el.addEventListener('mouseenter', () => {
                dropdown.querySelectorAll('.petugas-opt').forEach(x => x.classList.remove('active'));
                el.classList.add('active');
                el.style.background = '#f0f9ff';
            });
            el.addEventListener('mouseleave', () => el.style.background = '');
            el.addEventListener('click',      () => {
                selectPetugas(el.dataset.id, el.dataset.text, el.dataset.name, el.querySelector('span:last-child span:last-child')?.textContent);
            });
        });

    } catch (err) {
        console.error('fetchPetugas error:', err);
        dropdown.innerHTML = `
            <div style="padding:.75rem 1rem;color:#ef4444;font-size:.85rem;">
                ✗ Gagal memuat: ${err.message}
            </div>`;
    }
}

function selectPetugas(id, text, name, kode) {
    const input    = document.getElementById('petugas_search');
    const hiddenId = document.getElementById('petugas_id');
    const dropdown = document.getElementById('petugas_dropdown');

    if (input)    { input.value = text; input.style.borderColor = '#4ade80'; }
    if (hiddenId) hiddenId.value = id;
    if (dropdown) dropdown.style.display = 'none';

    showPetugasBadge(text);
}

function showPetugasBadge(text) {
    const badge     = document.getElementById('petugas_badge');
    const badgeText = document.getElementById('petugas_badge_text');
    if (badge)     badge.style.display = 'flex';
    if (badgeText) badgeText.textContent = text;
}

function hidePetugasBadge() {
    const badge = document.getElementById('petugas_badge');
    const input = document.getElementById('petugas_search');
    if (badge) badge.style.display = 'none';
    if (input) input.style.borderColor = '';
}

function clearPetugas() {
    document.getElementById('petugas_search').value = '';
    document.getElementById('petugas_id').value     = '';
    document.getElementById('petugas_dropdown').style.display = 'none';
    hidePetugasBadge();
    document.getElementById('petugas_search').focus();
}

// Tambahkan CSS spin animation
const styleEl = document.createElement('style');
styleEl.textContent = `
    @keyframes spin { to { transform: rotate(360deg); } }
    .petugas-opt.active { background: #f0f9ff !important; }
`;
document.head.appendChild(styleEl);
// ── DOMContentLoaded ──
document.addEventListener('DOMContentLoaded', function () {
    tcCheck    = document.getElementById('tc_pooled_check');
    boxKantong = document.getElementById('box-kantong');
    boxStok    = document.getElementById('box-stok');
    modeBadge  = document.getElementById('scan-mode-badge');

    if (tcCheck) { tcCheck.addEventListener('change', applyToggle); applyToggle(); }

    const inpK = document.getElementById('input_no_kantong');
    const inpS = document.getElementById('input_no_stok');
    if (inpK) inpK.addEventListener('keydown', e => { if (e.key==='Enter'){ e.preventDefault(); doScan(); } });
    if (inpS) inpS.addEventListener('keydown', e => { if (e.key==='Enter'){ e.preventDefault(); doScan(); } });

    const jenisSelect = document.getElementById('jenis_darah_select');
    const suhuInput   = document.getElementById('suhu_input');
    if (jenisSelect) {
        jenisSelect.addEventListener('change', () => {
            updateSuhuInfo();
            if (suhuInput?.value) validateSuhu(suhuInput.value, jenisSelect.value);
        });
        updateSuhuInfo();
    }
    if (suhuInput) suhuInput.addEventListener('input', function() {
        validateSuhu(this.value, document.getElementById('jenis_darah_select')?.value || '');
    });

    // Form submit
    const form = document.getElementById('pengirimanForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateFormBeforeSubmit()) e.preventDefault();
        });
    }
    initPetugasSearch();
});
</script>
@endpush
@endsection