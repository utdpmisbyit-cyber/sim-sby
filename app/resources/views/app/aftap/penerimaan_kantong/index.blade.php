@extends('layouts.index')

@section('title')
    Penerimaan Kantong – aftap Transfusi Darah
@endsection

@push('styles')
<style>
/* ─── TOKENS ─── */
:root {
    --red:          #E84040;
    --red-soft:     rgba(232,64,64,.10);
    --red-border:   rgba(232,64,64,.25);

    /* BACKGROUND */
    --bg-page:      #F5F7FB;
    --bg-card:      #FFFFFF;
    --bg-card-head: #FFFFFF;
    --bg-input:     #FFFFFF;
    --bg-row-hover: rgba(232,64,64,.04);

    /* BORDER */
    --line:         #E5E7EB;
    --line-2:       #D1D5DB;

    /* TEXT */
    --text-1:       #111827;
    --text-2:       #4B5563;
    --text-3:       #9CA3AF;

    --green:        #22C55E;
    --amber:        #F59E0B;

    --r-sm:  6px;
    --r-md:  10px;
    --r-lg:  14px;
    --r-xl:  18px;

    --font: 'Inter', system-ui, sans-serif;
    --mono: 'JetBrains Mono', monospace;
}

/* ─── RESET ─── */
.pkd *, .pkd *::before, .pkd *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* ─── WRAPPER ─── */
.pkd {
    font-family: var(--font);
    background: var(--bg-page);
    min-height: 100vh;
    padding: 24px 32px 56px;
    -webkit-font-smoothing: antialiased;
    color: var(--text-1);
}

.pkd-inner {
    /* ── FIX: sebelumnya 1100px, dilebarkan supaya memanfaatkan
       layar yang lebih besar ── */
    max-width: 1440px;
    margin: 0 auto;
}

/* ─────────────────────────────
   PAGE HEADER
───────────────────────────── */
.pkd-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 24px;
}

.pkd-hdr-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.pkd-hdr-icon {
    width: 52px;
    height: 52px;
    background: var(--red);
    border-radius: var(--r-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 0 24px rgba(232,64,64,.30);
}

.pkd-hdr-text h1 {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-1);
    letter-spacing: -.2px;
    line-height: 1.3;
}

.pkd-hdr-text p {
    font-size: .80rem;
    color: var(--text-2);
    margin-top: 2px;
}

.pkd-trx-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: transparent;
    border: 1.5px solid var(--line-2);
    border-radius: 999px;
    font-size: .82rem;
    font-weight: 600;
    color: var(--text-2);
    letter-spacing: .3px;
    white-space: nowrap;
}

.pkd-trx-pill i {
    font-size: .72rem;
    color: var(--text-3);
}

/* ─────────────────────────────
   CARD
───────────────────────────── */
.pkd-card {
    background: var(--bg-card);
    border: 1px solid var(--line);
    border-radius: var(--r-xl);
    overflow: hidden;
    margin-bottom: 16px;
}

.pkd-card-head {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 13px 20px;
    background: var(--bg-card-head);
    border-bottom: 1px solid var(--line);
}

.pkd-card-head i {
    font-size: 14px;
    color: var(--red);
}

.pkd-card-head h3 {
    font-size: .72rem;
    font-weight: 700;
    color: var(--text-2);
    text-transform: uppercase;
    letter-spacing: .7px;
}

.pkd-card-body {
    padding: 20px;
}

/* ─────────────────────────────
   FORM GRID
───────────────────────────── */
.pkd-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.pkd-field label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--text-3);
    margin-bottom: 8px;
}

.pkd-field label i {
    font-size: .68rem;
    color: var(--text-3);
}

.pkd-input-wrap {
    position: relative;
}

.pkd-input-wrap input {
    width: 100%;
    height: 44px;
    background: var(--bg-input);
    border: 1px solid var(--line-2);
    border-radius: var(--r-md);
    padding: 0 14px;
    font-size: .88rem;
    font-family: var(--font);
    color: var(--text-1);
    outline: none;
    transition: border-color .16s, box-shadow .16s;
    -webkit-appearance: none;
}

.pkd-input-wrap input:focus {
    border-color: var(--red);
    box-shadow: 0 0 0 3px rgba(232,64,64,.15);
}

.pkd-input-wrap input::placeholder {
    color: var(--text-3);
    font-size: .83rem;
}

/* date input calendar icon color fix */
.pkd-input-wrap input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(.5);
    cursor: pointer;
}

/* ─────────────────────────────
   STATUS SCAN ROW
───────────────────────────── */
.pkd-status-row {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px 16px;
    background: var(--bg-input);
    border: 1px solid var(--line-2);
    border-radius: var(--r-md);
    width: fit-content;
}

.pkd-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pkd-stat-item .s-label {
    font-size: .69rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .55px;
    color: var(--text-3);
}

.pkd-stat-item .s-val {
    font-size: .92rem;
    font-weight: 800;
    min-width: 18px;
    text-align: center;
}

.pkd-stat-item.si-kirim  .s-val { color: var(--amber); }
.pkd-stat-item.si-terima .s-val { color: var(--green); }
.pkd-stat-item.si-selisih .s-val { color: var(--red); }

.pkd-stat-divider {
    width: 1px;
    height: 16px;
    background: var(--line-2);
}

/* ─────────────────────────────
   SCAN INPUT
───────────────────────────── */
.pkd-scan-wrap {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1.5px dashed var(--line-2);
    border-radius: var(--r-md);
    overflow: hidden;
    transition: border-color .16s;
}

.pkd-scan-wrap:focus-within {
    border-color: var(--red);
}

.pkd-scan-ico {
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(232,64,64,.10);
    border-right: 1px solid var(--line-2);
    color: var(--red);
    font-size: 18px;
    flex-shrink: 0;
}

#pkd_scan {
    flex: 1;
    height: 52px;
    border: none;
    background: transparent;
    padding: 0 18px;
    font-size: .92rem;
    font-family: var(--font);
    color: var(--text-1);
    outline: none;
}

#pkd_scan::placeholder {
    color: var(--text-3);
    font-size: .84rem;
}

/* ─────────────────────────────
   TABLE
───────────────────────────── */
.pkd-table-wrap { overflow-x: auto; }

.pkd-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .84rem;
}

.pkd-table thead th {
    padding: 11px 14px;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .55px;
    color: var(--text-3);
    text-align: left;
    border-bottom: 1px solid var(--line);
    white-space: nowrap;
}

.pkd-table tbody tr {
    border-bottom: 1px solid var(--line);
    transition: background .12s;
}

.pkd-table tbody tr:last-child { border-bottom: none; }
.pkd-table tbody tr:hover { background: var(--bg-row-hover); }

.pkd-table tbody td {
    padding: 11px 14px;
    vertical-align: middle;
    color: var(--text-2);
}

.pkd-table .pkd-no-data td {
    text-align: center;
    padding: 52px 16px;
    color: var(--text-3);
    font-size: .82rem;
}

.pkd-table .pkd-no-data i {
    display: block;
    font-size: 32px;
    margin-bottom: 10px;
    opacity: .3;
}

/* row animation */
.pkd-row-in {
    animation: pkdRowIn .28s ease forwards;
}

@keyframes pkdRowIn {
    from { opacity: 0; transform: translateY(-5px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* mono */
.pkd-mono {
    font-family: var(--mono);
    font-size: .78rem;
    font-weight: 500;
    color: var(--text-1);
}

/* badge jenis */
.pkd-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: .69rem;
    font-weight: 700;
    background: rgba(59,130,246,.18);
    color: #93C5FD;
    border: 1px solid rgba(59,130,246,.25);
    white-space: nowrap;
}

/* btn del */
.pkd-btn-del {
    width: 30px;
    height: 30px;
    border-radius: var(--r-sm);
    border: 1px solid var(--line-2);
    background: none;
    color: var(--text-3);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all .14s;
}

.pkd-btn-del:hover {
    background: var(--red-soft);
    border-color: var(--red-border);
    color: var(--red);
}

/* ─────────────────────────────
   CARD FOOTER
───────────────────────────── */
.pkd-card-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding: 13px 20px;
    background: var(--bg-card-head);
    border-top: 1px solid var(--line);
}

.pkd-count-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 13px;
    background: var(--bg-input);
    border: 1px solid var(--line-2);
    border-radius: 999px;
    font-size: .78rem;
    color: var(--text-2);
}

.pkd-count-pill b { color: var(--text-1); }

.pkd-btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 42px;
    padding: 0 22px;
    border: none;
    border-radius: var(--r-md);
    background: var(--red);
    color: #fff;
    font-family: var(--font);
    font-size: .86rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(232,64,64,.30);
    transition: opacity .16s, transform .1s;
}

.pkd-btn-save:hover { opacity: .88; transform: translateY(-1px); }
.pkd-btn-save:active { transform: scale(.97); }
.pkd-btn-save:disabled {
    opacity: .35;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* ─────────────────────────────
   TOAST
───────────────────────────── */
.pkd-toast-area {
    position: fixed;
    top: 18px;
    right: 18px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 8px;
    pointer-events: none;
}

.pkd-toast {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 11px 15px;
    border-radius: var(--r-md);
    font-size: .82rem;
    font-weight: 600;
    color: #fff;
    min-width: 250px;
    pointer-events: auto;
    animation: pkdToastIn .22s ease;
    font-family: var(--font);
    border: 1px solid rgba(255,255,255,.10);
}

.pkd-toast i { font-size: 15px; flex-shrink: 0; }
.pkd-toast.t-ok   { background: #14532D; border-color: rgba(34,197,94,.20); }
.pkd-toast.t-err  { background: #7F1D1D; border-color: rgba(239,68,68,.25); }
.pkd-toast.t-warn { background: #78350F; border-color: rgba(245,158,11,.20); }

@keyframes pkdToastIn {
    from { opacity: 0; transform: translateX(18px); }
    to   { opacity: 1; transform: translateX(0); }
}

/* ─────────────────────────────
   SPINNER
───────────────────────────── */
@keyframes pkdSpin { to { transform: rotate(360deg); } }
.pkd-spin { animation: pkdSpin .85s linear infinite; display: inline-block; }

/* ─────────────────────────────
   RESPONSIVE
───────────────────────────── */
@media (max-width: 640px) {
    .pkd { padding: 16px 12px 40px; }
    .pkd-form-grid { grid-template-columns: 1fr; }
    .pkd-hdr       { flex-direction: column; align-items: flex-start; }
    .pkd-status-row { flex-wrap: wrap; }
}
/* ─────────────────────────────
   TAB BAR
───────────────────────────── */
.pkd-tab-bar {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--line);
    background: var(--bg-card-head);
    padding: 0 16px;
}

.pkd-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 13px 16px 12px;
    background: none;
    border: none;
    border-bottom: 2.5px solid transparent;
    font-family: var(--font);
    font-size: .80rem;
    font-weight: 600;
    color: var(--text-3);
    cursor: pointer;
    transition: color .16s, border-color .16s;
    margin-bottom: -1px;
}

.pkd-tab i { font-size: 13px; }

.pkd-tab:hover { color: var(--text-2); }

.pkd-tab.active {
    color: var(--red);
    border-bottom-color: var(--red);
}

.pkd-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    font-size: .65rem;
    font-weight: 700;
    background: var(--red-soft);
    color: var(--red);
    border: 1px solid var(--red-border);
}

.pkd-tab-badge--blue {
    background: rgba(59,130,246,.12);
    color: #3B82F6;
    border-color: rgba(59,130,246,.25);
}

/* ─────────────────────────────
   HISTORY FILTER BAR
───────────────────────────── */
.pkd-history-filter {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    padding: 14px 20px;
    border-bottom: 1px solid var(--line);
    background: var(--bg-card-head);
}

.pkd-btn-filter {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    height: 44px;
    padding: 0 18px;
    border: none;
    border-radius: var(--r-md);
    background: var(--red);
    color: #fff;
    font-family: var(--font);
    font-size: .84rem;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity .16s;
}

.pkd-btn-filter:hover { opacity: .88; }

/* ─────────────────────────────
   PAGINATION BUTTONS
───────────────────────────── */
.pkd-btn-page {
    width: 34px;
    height: 34px;
    border-radius: var(--r-sm);
    border: 1px solid var(--line-2);
    background: none;
    color: var(--text-2);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all .14s;
}

.pkd-btn-page:hover:not(:disabled) {
    background: var(--red-soft);
    border-color: var(--red-border);
    color: var(--red);
}

.pkd-btn-page:disabled { opacity: .35; cursor: not-allowed; }

/* btn detail row */
.pkd-btn-detail {
    width: 30px;
    height: 30px;
    border-radius: var(--r-sm);
    border: 1px solid var(--line-2);
    background: none;
    color: var(--text-3);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all .14s;
}

.pkd-btn-detail:hover {
    background: rgba(59,130,246,.10);
    border-color: rgba(59,130,246,.30);
    color: #3B82F6;
}

/* ─────────────────────────────
   MODAL
───────────────────────────── */
.pkd-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 88888;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.pkd-modal-overlay.show {
    display: flex;
}

.pkd-modal {
    background: var(--bg-card);
    border: 1px solid var(--line);
    border-radius: var(--r-xl);
    width: 100%;
    max-width: 900px;
    overflow: hidden;
    animation: pkdModalIn .22s ease;
}

@keyframes pkdModalIn {
    from { opacity: 0; transform: scale(.96) translateY(10px); }
    to   { opacity: 1; transform: scale(1)  translateY(0); }
}

.pkd-modal-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--line);
    background: var(--bg-card-head);
}

.pkd-modal-head h3 {
    font-size: .90rem;
    font-weight: 700;
    color: var(--text-1);
}
</style>
@endpush

@section('content')

<div class="pkd-toast-area" id="pkdToastArea"></div>

<div class="pkd">
<div class="pkd-inner">

    {{-- ── PAGE HEADER ── --}}
    <div class="pkd-hdr">
        <div class="pkd-hdr-left">
            <div class="pkd-hdr-icon">
                <i class="fas fa-tint"></i>
            </div>
            <div class="pkd-hdr-text">
                <h1>Penerimaan kantong darah</h1>
                <p>Scan &amp; catat kantong darah yang diterima dari gudang</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
            <div class="pkd-trx-pill" id="pkdEditBadge" style="display:none;background:#78350F;color:#fff;border-color:transparent">
                <i class="fas fa-pencil-alt"></i> Mode Edit
            </div>
            <div class="pkd-trx-pill" id="pkdTrxPill">
                <i class="fas fa-hashtag"></i>
                <span id="pkdTrxPillText">{{ $no_transaksi }}</span>
            </div>
        </div>
    </div>

    {{-- ── CARD 1: Info Transaksi + Scan ── --}}
    <div class="pkd-card">
        <div class="pkd-card-head">
            <i class="fas fa-clipboard-list"></i>
            <h3>Informasi Transaksi</h3>
        </div>

        <div class="pkd-card-body">

            {{-- Form Grid --}}
            <div class="pkd-form-grid">
                <div class="pkd-field">
                    <label><i class="fas fa-calendar-alt"></i> Tanggal Penerimaan</label>
                    <div class="pkd-input-wrap">
                        <input type="date" id="pkd_tanggal" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

               <div class="pkd-field" style="position:relative">
                <label><i class="fas fa-file-alt"></i> No. Permintaan</label>
                <div class="pkd-input-wrap">
                    <input type="text" id="pkd_kode"
                        placeholder="Ketik kode permintaan..."
                        autocomplete="off">
                </div>
                <div id="pkd_kode_dropdown" style="
                    display:none; position:absolute; top:100%; left:0; right:0; z-index:9999;
                    background:#fff; border:1px solid #D1D5DB; border-radius:8px;
                    box-shadow:0 8px 24px rgba(0,0,0,.12); max-height:220px; overflow-y:auto;
                    margin-top:2px;
                "></div>
            </div>
            
            <div class="pkd-field" style="position:relative">
                <label><i class="fas fa-warehouse"></i> No. Gudang Keluar</label>
                <div class="pkd-input-wrap">
                    <input type="text" id="pkd_no_keluar"
                        placeholder="Ketik no. gudang keluar..."
                        autocomplete="off">
                </div>
                <div id="pkd_keluar_dropdown" style="
                    display:none; position:absolute; top:100%; left:0; right:0; z-index:9999;
                    background:#fff; border:1px solid #D1D5DB; border-radius:8px;
                    box-shadow:0 8px 24px rgba(0,0,0,.12); max-height:220px; overflow-y:auto;
                    margin-top:2px;
                "></div>
            </div>
            </div>

            {{-- Status Scan --}}
            <div class="pkd-field" style="margin-bottom:20px">
                <label><i class="fas fa-exchange-alt"></i> Status Scan</label>
                <div class="pkd-status-row">
                    <div class="pkd-stat-item si-kirim">
                        <span class="s-label">Kirim</span>
                        <span class="s-val" id="pkd_kirim">–</span>
                    </div>
                    <div class="pkd-stat-divider"></div>
                    <div class="pkd-stat-item si-terima">
                        <span class="s-label">Terima</span>
                        <span class="s-val" id="pkd_terima">0</span>
                    </div>
                    <div class="pkd-stat-divider"></div>
                    <div class="pkd-stat-item si-selisih">
                        <span class="s-label">Selisih</span>
                        <span class="s-val" id="pkd_selisih">–</span>
                    </div>
                </div>
            </div>

            {{-- Scan Input --}}
            <div class="pkd-scan-wrap">
                <div class="pkd-scan-ico">
                    <i class="fas fa-barcode"></i>
                </div>
                <input
                    type="text"
                    id="pkd_scan"
                    placeholder="Scan nomor kantong di sini, lalu tekan Enter..."
                    autocomplete="off"
                    autofocus
                >
            </div>

        </div>{{-- end card-body --}}
    </div>{{-- end card 1 --}}

    {{-- ── CARD 2: Tab Scan + Riwayat ── --}}
<div class="pkd-card">

    {{-- Tab Header --}}
    <div class="pkd-tab-bar">
        <button class="pkd-tab active" id="pkd_tab_scan" onclick="pkdSwitchTab('scan')">
            <i class="fas fa-list-ul"></i>
            Daftar Kantong Discan
            <span class="pkd-tab-badge" id="pkd_tab_scan_badge">0</span>
        </button>
        <button class="pkd-tab" id="pkd_tab_history" onclick="pkdSwitchTab('history')">
            <i class="fas fa-history"></i>
            Riwayat Penerimaan
            <span class="pkd-tab-badge pkd-tab-badge--blue" id="pkd_tab_history_badge">0</span>
        </button>
    </div>

    {{-- ── PANEL: Daftar Kantong Discan ── --}}
    <div id="pkd_panel_scan">
        <div class="pkd-table-wrap">
            <table class="pkd-table">
                <thead>
                    <tr>
                        <th style="width:42px">#</th>
                        <th>No Kantong</th>
                        <th>Merk</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>No LOT</th>
                        <th style="width:52px;text-align:center">Hapus</th>
                    </tr>
                </thead>
                <tbody id="pkd_tbody">
                    <tr class="pkd-no-data" id="pkd_empty">
                        <td colspan="7">
                            <i class="fas fa-inbox"></i>
                            Belum ada kantong yang di-scan
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pkd-card-foot">
            <div class="pkd-count-pill">
                <i class="fas fa-layer-group" style="font-size:.7rem;opacity:.5"></i>
                Total: <b id="pkd_total">0</b> kantong
            </div>
            <div style="display:flex;gap:10px;align-items:center">
                <button class="pkd-btn-page" id="pkdBtnCancelEdit" onclick="pkdCancelEdit()"
                    style="display:none;width:auto;padding:0 16px;color:#EF4444;border-color:rgba(239,68,68,.35)">
                    <i class="fas fa-times"></i> Batal Edit
                </button>
                <button class="pkd-btn-save" id="pkd_btnSave" onclick="pkdSimpan()" disabled>
                    <i class="fas fa-save"></i>
                    <span id="pkdBtnSaveText">Simpan Penerimaan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── PANEL: Riwayat Penerimaan ── --}}
    <div id="pkd_panel_history" style="display:none">

        {{-- Filter Bar --}}
        <div class="pkd-history-filter">
            <div class="pkd-input-wrap" style="flex:1;min-width:160px">
                <input type="date" id="pkd_hist_tgl_dari"
                    value="{{ date('Y-m-d') }}"
                    placeholder="Dari tanggal">
            </div>
            <div class="pkd-input-wrap" style="flex:1;min-width:160px">
                <input type="date" id="pkd_hist_tgl_sampai"
                    value="{{ date('Y-m-d') }}"
                    placeholder="Sampai tanggal">
            </div>
            <div class="pkd-input-wrap" style="flex:1;min-width:160px">
                <input type="text" id="pkd_hist_keyword"
                    placeholder="Cari no transaksi / no keluar...">
            </div>
            <button class="pkd-btn-filter" onclick="pkdLoadHistory()">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>

        {{-- History Table --}}
        <div class="pkd-table-wrap">
            <table class="pkd-table">
               <thead>
                    <tr>
                        <th style="width:42px">#</th>
                        <th>No Transaksi</th>
                        <th>Tanggal</th>
                        <th>No Permintaan</th>
                        <th>No Gudang Keluar</th>
                        <th style="text-align:center">Diterima</th>
                        {{-- ── 3 kolom stok baru ── --}}
                        <th style="text-align:center">FPD Sample</th>
                        <th style="text-align:center">Serologi</th>
                        <th style="text-align:center;color:#16A34A">Sisa Stok</th>
                        <th style="width:110px;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pkd_hist_tbody">
                    <tr class="pkd-no-data">
                        <td colspan="10">
                            <i class="fas fa-search"></i>
                            Klik "Cari" untuk memuat riwayat
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- History Pagination --}}
        <div class="pkd-card-foot" id="pkd_hist_foot" style="display:none">
            <div class="pkd-count-pill">
                <i class="fas fa-layer-group" style="font-size:.7rem;opacity:.5"></i>
                <span id="pkd_hist_info">–</span>
            </div>
            <div style="display:flex;gap:6px">
                <button class="pkd-btn-page" id="pkd_hist_prev" onclick="pkdHistPage(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="pkd-btn-page" id="pkd_hist_next" onclick="pkdHistPage(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

</div>{{-- end card 2 --}}

{{-- ── MODAL DETAIL HISTORY ── --}}
<div class="pkd-modal-overlay" id="pkd_modal" onclick="pkdModalClose(event)">
    <div class="pkd-modal">
        <div class="pkd-modal-head">
            <div>
                <h3 id="pkd_modal_title">Detail Penerimaan</h3>
                <p id="pkd_modal_sub" style="font-size:.78rem;color:var(--text-3);margin-top:2px"></p>
            </div>
            <button class="pkd-btn-del" onclick="pkdModalHide()" style="width:32px;height:32px">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="pkd-table-wrap" style="max-height:360px;overflow-y:auto">
            <table class="pkd-table">
               <thead>
                    <tr>
                        <th style="width:36px">#</th>
                        <th>No Kantong</th>
                        <th>Merk</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>No LOT</th>
                        <th style="text-align:center">Status</th>
                        <th>Info Kirim</th>
                    </tr>
                </thead>
                <tbody id="pkd_modal_tbody">
                    <tr class="pkd-no-data"><td colspan="6"><i class="fas fa-spinner pkd-spin"></i></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>
</div>
@push('scripts')
<script>
/* ═══════════════════════════════════════
   CONFIG
═══════════════════════════════════════ */
const ROUTE_SCAN    = "{{ route('aftap.penerimaan.scan') }}";
const ROUTE_JUMLAH  = "{{ route('aftap.penerimaan.get_jumlah') }}";
const ROUTE_STORE   = "{{ route('aftap.penerimaan.store') }}";
const ROUTE_HISTORY = "{{ route('aftap.penerimaan.index') }}"; 
const ROUTE_SEARCH_KELUAR      = "{{ route('aftap.penerimaan.search_no_keluar') }}";
const ROUTE_SEARCH_PERMINTAAN  = "{{ route('aftap.penerimaan.search_no_permintaan') }}";
const ROUTE_KANTONG_BY_KELUAR  = "{{ route('aftap.penerimaan.kantong_by_no_keluar') }}";
const ROUTE_KANTONG_BY_PERM    = "{{ route('aftap.penerimaan.kantong_by_no_permintaan') }}";
 
let   NO_TRANSAKSI  = "{{ $no_transaksi }}";
const CSRF          = "{{ csrf_token() }}";
const ROUTE_NEXT_NO = "{{ route('aftap.penerimaan.next_no_transaksi') }}";

// base URL untuk edit/update/delete 1 transaksi penerimaan, mis:
// {{ url('aftap/penerimaan_kantong') }}/5/edit  → GET  ambil data utk edit
// {{ url('aftap/penerimaan_kantong') }}/5       → PUT  update
// {{ url('aftap/penerimaan_kantong') }}/5       → DELETE hapus
const PENERIMAAN_BASE_URL = "{{ url('aftap/penerimaan_kantong') }}";

/* ═══════════════════════════════════════
   STATE
═══════════════════════════════════════ */
let items       = [];
let jumlahKirim = null;
let histPage    = 1;
let histTotal   = 0;
const HIST_PER  = 10;
let editId      = null;   // id transaksi penerimaan yang sedang diedit (null = mode tambah baru)

/* ═══════════════════════════════════════
   TOAST
═══════════════════════════════════════ */
function pkdToast(msg, type = 'ok') {
    const map = {
        ok  : ['t-ok',  'fa-check-circle'],
        err : ['t-err', 'fa-times-circle'],
        warn: ['t-warn','fa-exclamation-triangle']
    };
    const [cls, ico] = map[type] ?? map.ok;
    const el = document.createElement('div');
    el.className = `pkd-toast ${cls}`;
    el.innerHTML = `<i class="fas ${ico}"></i><span>${msg}</span>`;
    document.getElementById('pkdToastArea').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

/* ═══════════════════════════════════════
   TAB SWITCH
═══════════════════════════════════════ */
function pkdSwitchTab(tab) {
    const isScan = tab === 'scan';
    document.getElementById('pkd_panel_scan')   .style.display = isScan  ? '' : 'none';
    document.getElementById('pkd_panel_history').style.display = !isScan ? '' : 'none';
    document.getElementById('pkd_tab_scan')    .classList.toggle('active',  isScan);
    document.getElementById('pkd_tab_history') .classList.toggle('active', !isScan);

    if (!isScan && histTotal === 0) pkdLoadHistory();
}

/* ═══════════════════════════════════════
   RENDER SCAN TABLE
═══════════════════════════════════════ */
function pkdRender() {
    const tbody   = document.getElementById('pkd_tbody');
    const empty   = document.getElementById('pkd_empty');
    const btnSave = document.getElementById('pkd_btnSave');

    [...tbody.querySelectorAll('tr:not(#pkd_empty)')].forEach(r => r.remove());

    if (items.length === 0) {
        empty.style.display = '';
        btnSave.disabled = true;
    } else {
        empty.style.display = 'none';
        btnSave.disabled = false;

        items.forEach((it, i) => {
            const tr = document.createElement('tr');
            tr.className = 'pkd-row-in';
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${i + 1}</td>
                <td><span class="pkd-mono">${it.no_kantong}</span></td>
                <td>${it.merk   ?? '–'}</td>
                <td><span class="pkd-badge">${it.jenis  ?? '–'}</span></td>
                <td>${it.ukuran ?? '–'}</td>
                <td><span class="pkd-mono">${it.no_lot ?? '–'}</span></td>
                <td style="text-align:center">
                    <button class="pkd-btn-del" onclick="pkdHapus(${i})" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('pkd_total').textContent           = items.length;
    document.getElementById('pkd_terima').textContent          = items.length;
    document.getElementById('pkd_tab_scan_badge').textContent  = items.length;
    pkdSelisih();
}

/* ═══════════════════════════════════════
   SELISIH
═══════════════════════════════════════ */
function pkdSelisih() {
    const el = document.getElementById('pkd_selisih');
    if (jumlahKirim === null) { el.textContent = '–'; return; }
    const s = jumlahKirim - items.length;
    el.textContent  = s;
    el.style.color  = s === 0 ? 'var(--green)' : s < 0 ? 'var(--red)' : 'var(--amber)';
}

/* ═══════════════════════════════════════
   HAPUS ITEM
═══════════════════════════════════════ */
function pkdHapus(idx) {
    items.splice(idx, 1);
    pkdRender();
}

/* ═══════════════════════════════════════
   FETCH JUMLAH KIRIM
═══════════════════════════════════════ */
async function pkdFetchJumlah(no_keluar) {
    if (!no_keluar.trim()) return;
    try {
        const res  = await fetch(ROUTE_JUMLAH, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ no_keluar })
        });
        const json = await res.json();
        jumlahKirim = json.jumlah_kirim ?? null;
        document.getElementById('pkd_kirim').textContent = jumlahKirim !== null ? jumlahKirim : '–';
        pkdSelisih();
    } catch(e) {
        pkdToast('Gagal mengambil jumlah kirim', 'warn');
    }
}

/* ═══════════════════════════════════════
   SCAN
═══════════════════════════════════════ */
async function pkdDoScan(no_kantong) {
    no_kantong = no_kantong.trim();
    if (!no_kantong) return;

    if (items.find(x => x.no_kantong === no_kantong)) {
        pkdToast(`Kantong ${no_kantong} sudah di-scan`, 'warn');
        return;
    }

    const scanEl = document.getElementById('pkd_scan');
    scanEl.disabled    = true;
    scanEl.placeholder = 'Memproses...';

    try {
        const res  = await fetch(ROUTE_SCAN, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ no_kantong })
        });
        const json = await res.json();
        if (!json.status) throw new Error(json.msg ?? 'Scan gagal');

        const d = json.data;
        items.unshift({
            no_kantong : d.no_kantong,
            merk       : d.merk   ?? null,
            jenis      : d.jenis  ?? null,
            ukuran     : d.ukuran ?? null,
            no_lot     : d.no_lot ?? null,
        });
        pkdRender();
        pkdToast(`Kantong ${no_kantong} berhasil discan`, 'ok');

    } catch(e) {
        pkdToast(e.message, 'err');
    } finally {
        scanEl.disabled    = false;
        scanEl.value       = '';
        scanEl.placeholder = 'Scan nomor kantong di sini, lalu tekan Enter...';
        scanEl.focus();
    }
}

/* ═══════════════════════════════════════
   SIMPAN (tambah baru ATAU update jika editId terisi)
═══════════════════════════════════════ */
async function pkdSimpan() {
    const tanggal   = document.getElementById('pkd_tanggal').value;
    const kode      = document.getElementById('pkd_kode').value;
    const no_keluar = document.getElementById('pkd_no_keluar').value;

    if (!tanggal)         return pkdToast('Isi tanggal penerimaan', 'warn');
    if (!no_keluar)       return pkdToast('Isi nomor gudang keluar', 'warn');
    if (items.length===0) return pkdToast('Belum ada kantong yang di-scan', 'warn');

    const isEdit = editId !== null;
    const btn    = document.getElementById('pkd_btnSave');
    btn.disabled  = true;
    btn.innerHTML = `<i class="fas fa-spinner pkd-spin"></i> ${isEdit ? 'Memperbarui...' : 'Menyimpan...'}`;

    const url    = isEdit ? `${PENERIMAAN_BASE_URL}/${editId}` : ROUTE_STORE;
    const method = isEdit ? 'PUT' : 'POST';
    const payload = isEdit
        ? { tanggal, kode, no_keluar, items }
        : { no_transaksi: NO_TRANSAKSI, tanggal, kode, no_keluar, items };

    try {
        const res  = await fetch(url, {
            method : method,
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify(payload)
        });
        const json = await res.json();
        if (!json.status) throw new Error(json.msg ?? 'Gagal menyimpan');

        pkdToast(isEdit ? 'Penerimaan berhasil diperbarui!' : 'Penerimaan berhasil disimpan!', 'ok');

        pkdCancelEdit();   // reset form + state edit + generate no transaksi baru
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <span id="pkdBtnSaveText">Simpan Penerimaan</span>';

        // refresh badge riwayat & reload jika sedang dibuka
        histTotal = 0;
        document.getElementById('pkd_tab_history_badge').textContent = '...';
        pkdLoadHistory(true);   // silent reload

    } catch(e) {
        pkdToast(e.message, 'err');
        btn.disabled  = false;
        btn.innerHTML = `<i class="fas fa-save"></i> <span id="pkdBtnSaveText">${isEdit ? 'Perbarui Penerimaan' : 'Simpan Penerimaan'}</span>`;
    }
}

/* ═══════════════════════════════════════
   EDIT — muat data transaksi lama ke form
═══════════════════════════════════════ */
async function pkdEditData(id) {
    try {
        const res  = await fetch(`${PENERIMAAN_BASE_URL}/${id}/edit`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();

        if (!json.success) {
            pkdToast(json.message ?? 'Gagal memuat data', 'err');
            return;
        }

        const d = json.data;

        editId = d.id;

        document.getElementById('pkd_tanggal').value   = d.tanggal;
        document.getElementById('pkd_kode').value      = d.kode_permintaan ?? '';
        document.getElementById('pkd_no_keluar').value = d.no_keluar ?? '';

        items = (d.items || []).map(it => ({
            no_kantong : it.no_kantong,
            merk       : it.merk,
            jenis      : it.jenis,
            ukuran     : it.ukuran,
            no_lot     : it.no_lot,
        }));
        pkdRender();

        // Jumlah kirim untuk pembanding selisih
        if (d.no_keluar) pkdFetchJumlah(d.no_keluar);

        // Tampilkan indikator mode edit
        document.getElementById('pkdEditBadge').style.display   = 'inline-flex';
        document.getElementById('pkdTrxPillText').textContent   = d.no_transaksi;
        document.getElementById('pkdBtnCancelEdit').style.display = 'inline-flex';
        document.getElementById('pkdBtnSaveText').textContent   = 'Perbarui Penerimaan';
        document.getElementById('pkd_btnSave').disabled = false;

        // Pindah ke tab "Daftar Kantong Discan" supaya form kelihatan
        pkdSwitchTab('scan');
        window.scrollTo({ top: 0, behavior: 'smooth' });

        pkdToast(`Mode edit aktif — ${d.no_transaksi}`, 'ok');

    } catch (e) {
        pkdToast('Gagal memuat data untuk edit', 'err');
    }
}

/* ═══════════════════════════════════════
   BATAL EDIT — reset form ke kondisi "tambah baru"
═══════════════════════════════════════ */
function pkdCancelEdit() {
    editId      = null;
    items       = [];
    jumlahKirim = null;
    pkdRender();

    document.getElementById('pkd_kirim').textContent   = '–';
    document.getElementById('pkd_selisih').textContent = '–';
    document.getElementById('pkd_kode').value          = '';
    document.getElementById('pkd_no_keluar').value     = '';
    document.getElementById('pkd_tanggal').value       = new Date().toISOString().split('T')[0];

    document.getElementById('pkdEditBadge').style.display     = 'none';
    document.getElementById('pkdBtnCancelEdit').style.display = 'none';
    document.getElementById('pkdBtnSaveText').textContent     = 'Simpan Penerimaan';

    pkdFetchNewNoTransaksi();
}

/* ═══════════════════════════════════════
   HAPUS TRANSAKSI PENERIMAAN
═══════════════════════════════════════ */
async function pkdDeleteData(id) {
    if (!confirm('Yakin hapus transaksi penerimaan ini? Kantong yang sudah diterima akan bisa diterima ulang.')) return;

    try {
        const res  = await fetch(`${PENERIMAAN_BASE_URL}/${id}`, {
            method : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const json = await res.json().catch(() => ({}));

        if (res.ok && json.status) {
            pkdToast('Transaksi berhasil dihapus', 'ok');

            // Kalau yang sedang diedit adalah yang barusan dihapus, batalkan mode edit
            if (editId === id) pkdCancelEdit();

            histTotal = 0;
            pkdLoadHistory(true);
        } else {
            pkdToast(json.msg ?? json.message ?? 'Gagal menghapus', 'err');
        }
    } catch (e) {
        pkdToast('Koneksi error saat menghapus', 'err');
    }
}

/* ═══════════════════════════════════════
   Ambil no transaksi baru (dipakai setelah batal edit / simpan sukses)
   agar transaksi berikutnya tidak bentrok "no_transaksi sudah pernah
   dipakai" — tanpa reload halaman supaya toast tetap terlihat.
═══════════════════════════════════════ */
async function pkdFetchNewNoTransaksi() {
    try {
        const res  = await fetch(ROUTE_NEXT_NO, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (json.no_transaksi) {
            NO_TRANSAKSI = json.no_transaksi;
            document.getElementById('pkdTrxPillText').textContent = NO_TRANSAKSI;
        }
    } catch (e) {
        console.warn('Gagal ambil no transaksi baru', e);
    }
}

/* ═══════════════════════════════════════
   LOAD HISTORY
═══════════════════════════════════════ */
async function pkdLoadHistory(silent = false) {
    const dari    = document.getElementById('pkd_hist_tgl_dari').value;
    const sampai  = document.getElementById('pkd_hist_tgl_sampai').value;
    const keyword = document.getElementById('pkd_hist_keyword').value.trim();
 
    const tbody = document.getElementById('pkd_hist_tbody');
    if (!silent) {
        tbody.innerHTML = `<tr class="pkd-no-data"><td colspan="10">
            <i class="fas fa-spinner pkd-spin"></i> Memuat data...
        </td></tr>`;
    }
 
    try {
        const params = new URLSearchParams({
            mode: 'history', dari, sampai, keyword,
            page: histPage, per: HIST_PER
        });
        const res  = await fetch(`${ROUTE_HISTORY}?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
 
        histTotal = json.total ?? 0;
        const rows = json.data ?? [];
 
        document.getElementById('pkd_tab_history_badge').textContent = histTotal;
 
        if (rows.length === 0) {
            tbody.innerHTML = `<tr class="pkd-no-data"><td colspan="10">
                <i class="fas fa-inbox"></i> Tidak ada data
            </td></tr>`;
            document.getElementById('pkd_hist_foot').style.display = 'none';
            return;
        }
 
        tbody.innerHTML = '';
        rows.forEach((row, i) => {
            const offset = (histPage - 1) * HIST_PER;
 
            // ── Warna sisa stok (3 tingkat, sinkron teks + background + border) ──
            // 0            → merah  (habis)
            // 1..LOW_STOCK → kuning (hampir habis / warning)
            // > LOW_STOCK  → hijau  (aman)
            const LOW_STOCK_THRESHOLD = 5;
            const sisa = row.sisa_stok ?? 0;

            let sisaColor, sisaBg, sisaBorder, sisaIcon;
            if (sisa === 0) {
                sisaColor  = '#EF4444';
                sisaBg     = 'rgba(239,68,68,.14)';
                sisaBorder = 'rgba(239,68,68,.35)';
                sisaIcon   = '<i class="fas fa-times-circle" style="font-size:.65rem;margin-right:3px"></i>';
            } else if (sisa < LOW_STOCK_THRESHOLD) {
                sisaColor  = '#B45309';
                sisaBg     = 'rgba(245,158,11,.16)';
                sisaBorder = 'rgba(245,158,11,.4)';
                sisaIcon   = '<i class="fas fa-exclamation-triangle" style="font-size:.62rem;margin-right:3px"></i>';
            } else {
                sisaColor  = '#16A34A';
                sisaBg     = 'rgba(34,197,94,.12)';
                sisaBorder = 'rgba(34,197,94,.25)';
                sisaIcon   = '';
            }
 
            const tr = document.createElement('tr');
            tr.className = 'pkd-row-in';
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${offset + i + 1}</td>
                <td><span class="pkd-mono">${row.no_transaksi}</span></td>
                <td>${row.tanggal}</td>
                <td>${row.kode_permintaan ?? '–'}</td>
                <td><span class="pkd-mono">${row.no_keluar ?? '–'}</span></td>
                <td style="text-align:center">
                    <span class="pkd-badge" style="background:rgba(59,130,246,.15);color:#3B82F6;border-color:rgba(59,130,246,.25)">
                        ${row.detail_count ?? 0}
                    </span>
                </td>
                <td style="text-align:center">
                    <span class="pkd-badge" style="background:rgba(245,158,11,.12);color:#B45309;border-color:rgba(245,158,11,.25)">
                        ${row.sudah_sample ?? 0}
                    </span>
                </td>
                <td style="text-align:center">
                    <span class="pkd-badge" style="background:rgba(139,92,246,.12);color:#7C3AED;border-color:rgba(139,92,246,.25)">
                        ${row.sudah_serologi ?? 0}
                    </span>
                </td>
                <td style="text-align:center">
                    <span class="pkd-badge" style="background:${sisaBg};color:${sisaColor};border-color:${sisaBorder};font-size:.75rem;font-weight:800">
                        ${sisaIcon}${sisa}
                    </span>
                </td>
                <td style="text-align:center;white-space:nowrap">
                    <button class="pkd-btn-detail"
                        onclick="pkdEditData(${row.id})"
                        title="Edit"
                        style="color:#3B82F6">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button class="pkd-btn-del"
                        onclick="pkdDeleteData(${row.id})"
                        title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <button class="pkd-btn-detail"
                        onclick="pkdShowDetail(${row.id}, '${row.no_transaksi}', '${row.tanggal}')"
                        title="Lihat detail">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });
 
        // Pagination
        const totalPage = Math.ceil(histTotal / HIST_PER);
        const foot = document.getElementById('pkd_hist_foot');
        foot.style.display = '';
        document.getElementById('pkd_hist_info').textContent =
            `${rows.length} dari ${histTotal} transaksi (halaman ${histPage}/${totalPage})`;
        document.getElementById('pkd_hist_prev').disabled = histPage <= 1;
        document.getElementById('pkd_hist_next').disabled = histPage >= totalPage;
 
    } catch(e) {
        tbody.innerHTML = `<tr class="pkd-no-data"><td colspan="10">
            <i class="fas fa-exclamation-triangle"></i> Gagal memuat: ${e.message}
        </td></tr>`;
    }
}
 
/* ═══════════════════════════════════════
   MODAL DETAIL (dengan kolom status kirim)
═══════════════════════════════════════ */
async function pkdShowDetail(id, noTrx, tgl) {
    document.getElementById('pkd_modal_title').textContent = `Detail – ${noTrx}`;
    document.getElementById('pkd_modal_sub').textContent   = `Tanggal: ${tgl}`;
    document.getElementById('pkd_modal_tbody').innerHTML   =
        `<tr class="pkd-no-data"><td colspan="8"><i class="fas fa-spinner pkd-spin"></i></td></tr>`;
    document.getElementById('pkd_modal').classList.add('show');
 
    try {
        const res  = await fetch(`${ROUTE_HISTORY}?mode=detail&id=${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        const rows = json.data ?? [];
 
        const tbody = document.getElementById('pkd_modal_tbody');
        if (rows.length === 0) {
            tbody.innerHTML = `<tr class="pkd-no-data"><td colspan="8">
                <i class="fas fa-inbox"></i> Tidak ada detail
            </td></tr>`;
            return;
        }
 
        tbody.innerHTML = '';
        rows.forEach((d, i) => {
 
            // Badge status per kantong
            let statusBadge = '';
            if (d.status_kirim === 'serologi') {
                statusBadge = `<span class="pkd-badge" style="background:rgba(139,92,246,.12);color:#7C3AED;border-color:rgba(139,92,246,.25)">
                    <i class="fas fa-flask" style="font-size:.6rem"></i> Serologi
                </span>`;
            } else if (d.status_kirim === 'sample') {
                statusBadge = `<span class="pkd-badge" style="background:rgba(245,158,11,.12);color:#B45309;border-color:rgba(245,158,11,.25)">
                    <i class="fas fa-vials" style="font-size:.6rem"></i> FPD Sample
                </span>`;
            } else {
                statusBadge = `<span class="pkd-badge" style="background:rgba(34,197,94,.12);color:#16A34A;border-color:rgba(34,197,94,.25)">
                    <i class="fas fa-check" style="font-size:.6rem"></i> Tersedia
                </span>`;
            }
 
            const tr = document.createElement('tr');
            // Beri style redup jika sudah dikirim
            if (d.status_kirim !== 'tersedia') tr.style.opacity = '.65';
 
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${i + 1}</td>
                <td><span class="pkd-mono">${d.no_kantong}</span></td>
                <td>${d.merk   ?? '–'}</td>
                <td><span class="pkd-badge">${d.jenis  ?? '–'}</span></td>
                <td>${d.ukuran ?? '–'}</td>
                <td><span class="pkd-mono">${d.no_lot ?? '–'}</span></td>
                <td style="text-align:center">${statusBadge}</td>
                <td>
                    <span class="pkd-mono" style="font-size:.72rem;color:var(--text-3)">
                        ${d.info_kirim ?? '–'}
                    </span>
                </td>`;
            tbody.appendChild(tr);
        });
 
    } catch(e) {
        document.getElementById('pkd_modal_tbody').innerHTML =
            `<tr class="pkd-no-data"><td colspan="8">Gagal memuat detail</td></tr>`;
    }
}
function pkdHistPage(dir) {
    histPage = Math.max(1, histPage + dir);
    pkdLoadHistory();
}

/* ═══════════════════════════════════════
   MODAL DETAIL
═══════════════════════════════════════ */
async function pkdShowDetail(id, noTrx, tgl) {
    document.getElementById('pkd_modal_title').textContent = `Detail – ${noTrx}`;
    document.getElementById('pkd_modal_sub').textContent   = `Tanggal: ${tgl}`;
    document.getElementById('pkd_modal_tbody').innerHTML   =
        `<tr class="pkd-no-data"><td colspan="6"><i class="fas fa-spinner pkd-spin"></i></td></tr>`;
    document.getElementById('pkd_modal').classList.add('show');

    try {
        const res  = await fetch(`${ROUTE_HISTORY}?mode=detail&id=${id}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        const rows = json.data ?? [];

        const tbody = document.getElementById('pkd_modal_tbody');
        if (rows.length === 0) {
            tbody.innerHTML = `<tr class="pkd-no-data"><td colspan="6">
                <i class="fas fa-inbox"></i> Tidak ada detail
            </td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        rows.forEach((d, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${i + 1}</td>
                <td><span class="pkd-mono">${d.no_kantong}</span></td>
                <td>${d.merk   ?? '–'}</td>
                <td><span class="pkd-badge">${d.jenis  ?? '–'}</span></td>
                <td>${d.ukuran ?? '–'}</td>
                <td><span class="pkd-mono">${d.no_lot ?? '–'}</span></td>`;
            tbody.appendChild(tr);
        });

    } catch(e) {
        document.getElementById('pkd_modal_tbody').innerHTML =
            `<tr class="pkd-no-data"><td colspan="6">Gagal memuat detail</td></tr>`;
    }
}

function pkdModalHide() {
    document.getElementById('pkd_modal').classList.remove('show');
}

function pkdModalClose(e) {
    if (e.target === document.getElementById('pkd_modal')) pkdModalHide();
}

/* ═══════════════════════════════════════
   INIT
═══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('pkd_scan').addEventListener('keydown', e => {
        if (e.key === 'Enter') pkdDoScan(e.target.value);
    });

   

    // Enter di filter history
    document.getElementById('pkd_hist_keyword').addEventListener('keydown', e => {
        if (e.key === 'Enter') { histPage = 1; pkdLoadHistory(); }
    });

    pkdRender();
});
function pkdShowDropdown(dropdownEl, items, onSelect) {
    dropdownEl.innerHTML = '';
    if (!items.length) {
        dropdownEl.innerHTML = `<div style="padding:10px 14px;font-size:.80rem;color:#9CA3AF">Tidak ditemukan</div>`;
        dropdownEl.style.display = '';
        return;
    }
    items.forEach(item => {
        const div = document.createElement('div');
        div.style.cssText = 'padding:9px 14px;font-size:.82rem;cursor:pointer;border-bottom:1px solid #F3F4F6;';
        div.innerHTML = item._html;
        div.addEventListener('mouseenter', () => div.style.background = '#F9FAFB');
        div.addEventListener('mouseleave', () => div.style.background = '');
        div.addEventListener('mousedown', (e) => { e.preventDefault(); onSelect(item); });
        dropdownEl.appendChild(div);
    });
    dropdownEl.style.display = '';
}
 
function pkdHideDropdown(dropdownEl) {
    dropdownEl.style.display = 'none';
    dropdownEl.innerHTML = '';
}
 
/* ════════════════════════════════════════════
   AUTOCOMPLETE NO GUDANG KELUAR
════════════════════════════════════════════ */
let debKeluar;
document.getElementById('pkd_no_keluar').addEventListener('input', async function () {
    clearTimeout(debKeluar);
    const q = this.value.trim();
    const dd = document.getElementById('pkd_keluar_dropdown');
 
    if (q.length < 2) { pkdHideDropdown(dd); return; }
 
    debKeluar = setTimeout(async () => {
        try {
            const res  = await fetch(`${ROUTE_SEARCH_KELUAR}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            pkdShowDropdown(dd, data.map(r => ({
                ...r,
                _html: `<b>${r.no_keluar}</b> <span style="color:#9CA3AF;font-size:.74rem;margin-left:6px">${r.jumlah} kantong</span>`
            })), async (item) => {
                document.getElementById('pkd_no_keluar').value = item.no_keluar;
                pkdHideDropdown(dd);
                await pkdLoadByNoKeluar(item.no_keluar);
            });
        } catch(e) { pkdHideDropdown(dd); }
    }, 350);
});
 
document.getElementById('pkd_no_keluar').addEventListener('blur', () => {
    setTimeout(() => pkdHideDropdown(document.getElementById('pkd_keluar_dropdown')), 200);
});
 
/* ── Auto-load kantong ketika no_keluar diisi & Enter ── */
document.getElementById('pkd_no_keluar').addEventListener('keydown', async function (e) {
    if (e.key === 'Enter') {
        pkdHideDropdown(document.getElementById('pkd_keluar_dropdown'));
        await pkdLoadByNoKeluar(this.value.trim());
    }
    // lama: pkdFetchJumlah() → tetap dipanggil untuk update counter Kirim
});
 
async function pkdLoadByNoKeluar(no_keluar) {
    if (!no_keluar) return;
 
    // Update counter Kirim
    pkdFetchJumlah(no_keluar);
 
    try {
        const res  = await fetch(ROUTE_KANTONG_BY_KELUAR, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ no_keluar })
        });
        const json = await res.json();
        if (!json.status) { pkdToast(json.msg ?? 'Gagal memuat kantong', 'err'); return; }
 
        // Auto isi kode permintaan dari kantong pertama jika kosong
        if (json.data.length && !document.getElementById('pkd_kode').value) {
            const firstPerm = json.data[0].no_permintaan;
            if (firstPerm) document.getElementById('pkd_kode').value = firstPerm;
        }
 
        // Tambahkan ke tabel scan (skip duplikat)
        let added = 0;
        json.data.forEach(d => {
            if (!items.find(x => x.no_kantong === d.no_kantong)) {
                items.unshift({
                    no_kantong : d.no_kantong,
                    merk       : d.merk   ?? null,
                    jenis      : d.jenis  ?? null,
                    ukuran     : d.ukuran ?? null,
                    no_lot     : d.no_lot ?? null,
                });
                added++;
            }
        });
 
        pkdRender();
        if (added > 0) pkdToast(`${added} kantong dimuat dari no. keluar ${no_keluar}`, 'ok');
        else pkdToast('Semua kantong sudah ada di daftar', 'warn');
 
    } catch(e) {
        pkdToast('Gagal memuat kantong: ' + e.message, 'err');
    }
}
 
/* ════════════════════════════════════════════
   AUTOCOMPLETE NO PERMINTAAN
════════════════════════════════════════════ */
let debPerm;
document.getElementById('pkd_kode').addEventListener('input', async function () {
    clearTimeout(debPerm);
    const q = this.value.trim();
    const dd = document.getElementById('pkd_kode_dropdown');
 
    if (q.length < 2) { pkdHideDropdown(dd); return; }
 
    debPerm = setTimeout(async () => {
        try {
            const res  = await fetch(`${ROUTE_SEARCH_PERMINTAAN}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            pkdShowDropdown(dd, data.map(r => ({
                ...r,
                _html: `<b>${r.no_permintaan}</b>
                    <span style="color:#9CA3AF;font-size:.74rem;margin-left:6px">
                        ${r.jumlah_kantong} kantong
                    </span>
                    <span style="color:#6B7280;font-size:.72rem;margin-left:8px">
                        <i class="fas fa-warehouse" style="font-size:.6rem"></i> ${r.no_keluar}
                    </span>`
            })), async (item) => {
                document.getElementById('pkd_kode').value     = item.no_permintaan;
                document.getElementById('pkd_no_keluar').value = item.no_keluar;
                pkdHideDropdown(dd);
                // Auto-load kantong sekaligus update counter
                await pkdLoadByNoPermintaan(item.no_permintaan);
                pkdFetchJumlah(item.no_keluar);
            });
        } catch(e) { pkdHideDropdown(dd); }
    }, 350);
});
 
document.getElementById('pkd_kode').addEventListener('blur', () => {
    setTimeout(() => pkdHideDropdown(document.getElementById('pkd_kode_dropdown')), 200);
});
 
document.getElementById('pkd_kode').addEventListener('keydown', async function (e) {
    if (e.key === 'Enter') {
        pkdHideDropdown(document.getElementById('pkd_kode_dropdown'));
        await pkdLoadByNoPermintaan(this.value.trim());
    }
});
 
async function pkdLoadByNoPermintaan(no_permintaan) {
    if (!no_permintaan) return;
    try {
        const res  = await fetch(ROUTE_KANTONG_BY_PERM, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ no_permintaan })
        });
        const json = await res.json();
        if (!json.status) { pkdToast(json.msg ?? 'Gagal memuat kantong', 'err'); return; }
 
        // Auto isi no_keluar dari kantong pertama jika field kosong
        if (json.data.length && !document.getElementById('pkd_no_keluar').value) {
            const firstKeluar = json.data[0].no_keluar;
            if (firstKeluar) {
                document.getElementById('pkd_no_keluar').value = firstKeluar;
                pkdFetchJumlah(firstKeluar);
            }
        }
 
        let added = 0;
        json.data.forEach(d => {
            if (!items.find(x => x.no_kantong === d.no_kantong)) {
                items.unshift({
                    no_kantong : d.no_kantong,
                    merk       : d.merk   ?? null,
                    jenis      : d.jenis  ?? null,
                    ukuran     : d.ukuran ?? null,
                    no_lot     : d.no_lot ?? null,
                });
                added++;
            }
        });
 
        pkdRender();
        if (added > 0) pkdToast(`${added} kantong dimuat dari permintaan ${no_permintaan}`, 'ok');
        else pkdToast('Semua kantong sudah ada di daftar', 'warn');
 
    } catch(e) {
        pkdToast('Gagal memuat kantong: ' + e.message, 'err');
    }
}
 



</script>
@endpush



@endsection