@extends('layouts.index')

@section('title', 'Permintaan Darah External')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --red-primary: #C8102E;
    --red-dark: #8B0000;
    --red-light: #FF4444;
    --red-bg: #FFF5F5;
    --blue-status: #1565C0;
    --gold: #B8860B;
    --cream: #FEFCF8;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-400: #9CA3AF;
    --gray-600: #4B5563;
    --gray-700: #374151;
    --gray-800: #1F2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.10), 0 2px 6px rgba(0,0,0,0.06);
    --shadow-lg: 0 10px 30px rgba(0,0,0,0.12), 0 4px 12px rgba(0,0,0,0.06);
    --shadow-modal: 0 24px 60px rgba(0,0,0,0.20), 0 8px 24px rgba(0,0,0,0.12);
    --radius: 8px;
    --radius-sm: 4px;
    --radius-lg: 12px;
}

* { box-sizing: border-box; }

body {
    font-family: 'IBM Plex Sans', sans-serif;
    background: #ECEEF1;
    color: var(--gray-800);
}

/* ── Page Layout ─────────────────────────────── */
.pde-page {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
}

/* ── Page Header ─────────────────────────────── */
.pde-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.pde-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}
.pde-header-icon {
    width: 44px;
    height: 44px;
    background: var(--red-primary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
}
.pde-header-icon svg { color: #fff; }
.pde-title { font-size: 22px; font-weight: 700; color: var(--gray-900); margin: 0; }
.pde-subtitle { font-size: 12px; color: var(--gray-400); font-family: 'IBM Plex Mono', monospace; margin-top: 2px; }

/* ── Toolbar ─────────────────────────────────── */
.pde-toolbar {
    background: #fff;
    border-radius: var(--radius-lg);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
}
.pde-toolbar-divider {
    width: 1px;
    height: 28px;
    background: var(--gray-200);
    margin: 0 4px;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    font-family: inherit;
    white-space: nowrap;
    text-decoration: none;
}
.btn-primary {
    background: var(--red-primary);
    color: #fff;
    border-color: var(--red-primary);
}
.btn-primary:hover { background: var(--red-dark); border-color: var(--red-dark); color: #fff; }
.btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border-color: var(--gray-200);
}
.btn-secondary:hover { background: var(--gray-200); }
.btn-outline {
    background: transparent;
    color: var(--gray-700);
    border-color: var(--gray-300);
}
.btn-outline:hover { background: var(--gray-100); }
.btn-sm { padding: 6px 12px; font-size: 12px; }
.btn-danger { background: #FEF2F2; color: var(--red-primary); border-color: #FECACA; }
.btn-danger:hover { background: var(--red-primary); color: #fff; }
.btn-success { background: #F0FDF4; color: #15803D; border-color: #BBF7D0; }
.btn-success:hover { background: #15803D; color: #fff; }

/* ── Filter / Search Bar ─────────────────────── */
.pde-filter-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}
.search-wrap {
    position: relative;
}
.search-wrap input {
    width: 240px;
    padding: 8px 12px 8px 36px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    color: var(--gray-800);
    background: var(--gray-50);
    transition: border-color .15s;
}
.search-wrap input:focus { outline: none; border-color: var(--red-primary); background: #fff; }
.search-wrap .search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}
.filter-select {
    padding: 8px 12px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    color: var(--gray-700);
    background: var(--gray-50);
    cursor: pointer;
}
.filter-select:focus { outline: none; border-color: var(--red-primary); }

/* ── Stats Row ───────────────────────────────── */
.pde-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.stat-card {
    background: #fff;
    border-radius: var(--radius);
    padding: 16px 18px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 14px;
}
.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.stat-icon.total    { background: #EFF6FF; }
.stat-icon.belum    { background: #FEF2F2; }
.stat-icon.sebagian { background: #FFFBEB; }
.stat-icon.selesai  { background: #F0FDF4; }
.stat-val { font-size: 24px; font-weight: 700; line-height: 1; color: var(--gray-900); font-family: 'IBM Plex Mono', monospace; }
.stat-lbl { font-size: 11px; color: var(--gray-400); margin-top: 3px; text-transform: uppercase; letter-spacing: .5px; }

/* ── Table Card ──────────────────────────────── */
.table-card {
    background: #fff;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--gray-200);
    overflow: hidden;
}
.table-card-header {
    padding: 14px 18px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.table-card-title { font-size: 14px; font-weight: 600; color: var(--gray-800); }
.table-card-meta { font-size: 12px; color: var(--gray-400); font-family: 'IBM Plex Mono', monospace; }

table.pde-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.pde-table thead tr {
    background: var(--gray-50);
    border-bottom: 2px solid var(--gray-200);
}
.pde-table th {
    padding: 11px 14px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--gray-500, #6B7280);
    white-space: nowrap;
}
.pde-table tbody tr {
    border-bottom: 1px solid var(--gray-100);
    transition: background .1s;
    cursor: pointer;
}
.pde-table tbody tr:hover { background: var(--red-bg); }
.pde-table tbody tr:last-child { border-bottom: none; }
.pde-table td {
    padding: 12px 14px;
    color: var(--gray-700);
    vertical-align: middle;
}
.pde-table td.mono { font-family: 'IBM Plex Mono', monospace; font-size: 12px; }

/* ── Badges ──────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .3px;
}
.badge-sudah    { background: #DCFCE7; color: #15803D; }
.badge-belum    { background: #FEE2E2; color: #B91C1C; }
.badge-sebagian { background: #FEF9C3; color: #92400E; }
.badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* ── Pagination ──────────────────────────────── */
.pde-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-top: 1px solid var(--gray-100);
    background: var(--gray-50);
}
.pagination-info { font-size: 12px; color: var(--gray-400); font-family: 'IBM Plex Mono', monospace; }
.pagination-btns { display: flex; gap: 4px; }
.pg-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 12px;
    cursor: pointer;
    background: #fff;
    color: var(--gray-600);
    transition: all .15s;
}
.pg-btn:hover { background: var(--red-primary); color: #fff; border-color: var(--red-primary); }
.pg-btn.active { background: var(--red-primary); color: #fff; border-color: var(--red-primary); font-weight: 600; }
.pg-btn:disabled { opacity: .4; cursor: default; }

/* ── Empty State ─────────────────────────────── */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-400);
}
.empty-state svg { opacity: .3; margin-bottom: 14px; }
.empty-state p { font-size: 14px; }

/* ── Loading Spinner ─────────────────────────── */
.loading-row td {
    text-align: center;
    padding: 50px;
    color: var(--gray-400);
}
.spinner {
    width: 24px;
    height: 24px;
    border: 3px solid var(--gray-200);
    border-top-color: var(--red-primary);
    border-radius: 50%;
    animation: spin .7s linear infinite;
    display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ══════════════════════════════════════════════
   MODAL — overlay transparan
══════════════════════════════════════════════ */

/* [FIX 1] Overlay transparan + modal benar-benar di tengah layar */
.modal-overlay {
    display: none;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: rgba(0, 0, 0, 0.50);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    z-index: 99999 !important;          /* sangat tinggi agar di atas sidebar/navbar */
    align-items: center !important;
    justify-content: center !important;
    padding: 20px;
    margin: 0 !important;               /* reset margin dari layout parent */
    overflow-y: auto;
}

.modal-overlay.open {
    display: flex !important;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

@keyframes modalIn {
    from { opacity: 0; transform: translateY(-12px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0)    scale(1); }
}

.modal {
    background: #ffffff;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-modal);
    width: 100%;
    max-width: 1000px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: modalIn 0.25s cubic-bezier(0.34, 1.2, 0.64, 1);
}

.modal-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--gray-900);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}
.modal-header-icon {
    width: 36px;
    height: 36px;
    background: var(--red-primary);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.modal-title  { font-size: 16px; font-weight: 700; color: #fff; flex: 1; }
.modal-subtitle { font-size: 11px; color: var(--gray-400); font-family: 'IBM Plex Mono', monospace; margin-top: 1px; }
.modal-close {
    width: 30px;
    height: 30px;
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,.15);
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.7);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .15s;
}
.modal-close:hover { background: var(--red-primary); color: #fff; border-color: var(--red-primary); }

.modal-body {
    overflow-y: auto;
    padding: 22px;
    flex: 1;
}

/* ── Form Elements ───────────────────────────── */
.form-section { margin-bottom: 20px; }
.form-section-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--gray-400);
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px dashed var(--gray-200);
    display: flex;
    align-items: center;
    gap: 8px;
}
.form-section-title::before {
    content: '';
    width: 3px;
    height: 14px;
    background: var(--red-primary);
    border-radius: 2px;
}

.form-grid   { display: grid; gap: 14px; }
.form-grid-2 { grid-template-columns: 1fr 1fr; }
.form-grid-3 { grid-template-columns: 1fr 1fr 1fr; }
.form-grid-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }

.form-group  { display: flex; flex-direction: column; gap: 5px; position: relative; }
.form-label  { font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: .4px; }
.form-label.required::after { content: ' *'; color: var(--red-primary); }
.form-control {
    padding: 9px 12px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-family: inherit;
    color: var(--gray-800);
    background: var(--gray-50);
    transition: border-color .15s, box-shadow .15s;
    width: 100%;
}
.form-control:focus {
    outline: none;
    border-color: var(--red-primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(200,16,46,.08);
}
.form-control[readonly] { background: var(--gray-100); color: var(--gray-500, #6B7280); }
.form-control.mono { font-family: 'IBM Plex Mono', monospace; }
select.form-control { cursor: pointer; }

/* ── Nomor display ───────────────────────────── */
.nomor-display {
    padding: 9px 12px;
    background: var(--gray-900);
    color: #4ADE80;
    border-radius: var(--radius-sm);
    font-family: 'IBM Plex Mono', monospace;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 1px;
    border: 1px solid var(--gray-700);
    min-height: 38px;
    display: flex;
    align-items: center;
}
/* Animasi loading nomor */
.nomor-display.loading { color: var(--gray-400); }

/* ── Autocomplete Dropdown ───────────────────── */
.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid var(--gray-200);
    border-top: none;
    border-radius: 0 0 var(--radius-sm) var(--radius-sm);
    box-shadow: var(--shadow-md);
    z-index: 9999;
    max-height: 220px;
    overflow-y: auto;
    display: none;
}
.autocomplete-dropdown.show { display: block; }
.autocomplete-item {
    padding: 8px 12px;
    cursor: pointer;
    font-size: 13px;
    border-bottom: 1px solid var(--gray-100);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background .1s;
}
.autocomplete-item:last-child { border-bottom: none; }
.autocomplete-item:hover, .autocomplete-item.active { background: var(--red-bg); }
.autocomplete-kode {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    font-weight: 600;
    color: var(--red-primary);
    background: #FEF2F2;
    padding: 1px 6px;
    border-radius: 3px;
    white-space: nowrap;
}
.autocomplete-nama { color: var(--gray-700); }
.autocomplete-empty {
    padding: 12px;
    font-size: 12px;
    color: var(--gray-400);
    text-align: center;
}

/* ── Detail Table ────────────────────────────── */
.detail-table-wrap {
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    overflow: hidden;
    margin-top: 4px;
}
table.detail-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.detail-table thead tr { background: var(--gray-800); }
.detail-table th {
    padding: 9px 10px;
    text-align: left;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--gray-300);
    white-space: nowrap;
}
.detail-table tbody tr { border-bottom: 1px solid var(--gray-100); }
.detail-table tbody tr:last-child { border-bottom: none; }
.detail-table tbody tr:hover { background: var(--gray-50); }
.detail-table td { padding: 8px 10px; vertical-align: middle; color: var(--gray-700); }

.detail-input {
    padding: 5px 8px;
    border: 1px solid var(--gray-200);
    border-radius: 4px;
    font-size: 12px;
    font-family: inherit;
    width: 100%;
    background: #fff;
}
.detail-input:focus { outline: none; border-color: var(--red-primary); }
.detail-select { padding: 5px 6px; }

.btn-remove-row {
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #FECACA;
    border-radius: 4px;
    background: #FEF2F2;
    color: var(--red-primary);
    cursor: pointer;
    transition: all .15s;
}
.btn-remove-row:hover { background: var(--red-primary); color: #fff; }

.btn-add-row {
    margin-top: 10px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: transparent;
    border: 1px dashed var(--red-primary);
    border-radius: var(--radius-sm);
    color: var(--red-primary);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    font-family: inherit;
    transition: all .15s;
}
.btn-add-row:hover { background: var(--red-bg); }

/* Legend indicator */
.legend {
    display: flex;
    gap: 16px;
    margin-top: 10px;
    padding: 8px 12px;
    background: var(--gray-50);
    border-radius: var(--radius-sm);
    border: 1px solid var(--gray-200);
}
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--gray-600); }
.legend-dot { width: 10px; height: 10px; border-radius: 2px; }
.legend-dot.sudah  { background: #16A34A; }
.legend-dot.belum  { background: var(--red-primary); }

/* ── Modal Footer ────────────────────────────── */
.modal-footer {
    padding: 14px 22px;
    border-top: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--gray-50);
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}
.modal-footer-left  { display: flex; gap: 8px; }
.modal-footer-right { display: flex; gap: 8px; }

/* ── View Modal ──────────────────────────────── */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 18px;
}
.info-row   { display: flex; flex-direction: column; gap: 3px; }
.info-label { font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: var(--gray-400); font-weight: 600; }
.info-value { font-size: 13px; color: var(--gray-800); font-weight: 500; }
.info-value.mono { font-family: 'IBM Plex Mono', monospace; }

/* ── Toast ───────────────────────────────────── */
.toast-container { position: fixed; bottom: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
.toast {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--gray-900);
    color: #fff;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    font-size: 13px;
    min-width: 240px;
    animation: toastIn .25s ease;
}
@keyframes toastIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.toast.success { border-left: 3px solid #22C55E; }
.toast.error   { border-left: 3px solid var(--red-primary); }

/* ── Scrollbar ───────────────────────────────── */
::-webkit-scrollbar { width: 6px; height: 6px; }
::-webkit-scrollbar-track { background: var(--gray-100); }
::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--gray-400); }

/* ── Valid/Invalid state ─────────────────────── */
.input-valid   { border-color: #16A34A !important; background: #F0FDF4 !important; }
.input-invalid { border-color: #DC2626 !important; background: #FEF2F2 !important; }
.field-hint { font-size: 10px; margin-top: 3px; }
.field-hint.ok  { color: #16A34A; }
.field-hint.err { color: #DC2626; }
</style>
@endpush

@section('content')
<div class="pde-page">

    {{-- ── Header ── --}}
    <div class="pde-header">
        <div class="pde-header-left">
            <div class="pde-header-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div>
                <h1 class="pde-title">Permintaan Darah External</h1>
                <div class="pde-subtitle">DISTRIBUSI / PENYIMPANAN</div>
            </div>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="pde-stats" id="statsRow">
        <div class="stat-card">
            <div class="stat-icon total">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
            </div>
            <div><div class="stat-val" id="statTotal">–</div><div class="stat-lbl">Total Permintaan</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon belum">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div><div class="stat-val" id="statBelum">–</div><div class="stat-lbl">Belum Dipenuhi</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon sebagian">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div><div class="stat-val" id="statSebagian">–</div><div class="stat-lbl">Sebagian</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon selesai">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div><div class="stat-val" id="statSudah">–</div><div class="stat-lbl">Sudah Dipenuhi</div></div>
        </div>
    </div>

    {{-- ── Toolbar ── --}}
    <div class="pde-toolbar">
        <button class="btn btn-primary" onclick="openCreateModal()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah
        </button>
        <button class="btn btn-secondary" onclick="loadData()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
            Refresh
        </button>
        <button class="btn btn-outline" onclick="window.print()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak [F10]
        </button>

        <div class="pde-toolbar-divider"></div>

        <div class="pde-filter-bar">
            <div class="search-wrap">
                <svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" id="searchInput" placeholder="Cari nomor, nama, institusi…" oninput="debounceLoad()">
            </div>
            <select class="filter-select" id="statusFilter" onchange="loadData()">
                <option value="">Semua Status</option>
                <option value="BELUM_DIPENUHI">Belum Dipenuhi</option>
                <option value="SEBAGIAN">Sebagian</option>
                <option value="SUDAH_DIPENUHI">Sudah Dipenuhi</option>
            </select>
        </div>
    </div>

    {{-- ── Table Card ── --}}
    <div class="table-card">
        <div class="table-card-header">
            <span class="table-card-title">Daftar Permintaan</span>
            <span class="table-card-meta" id="tableMetaInfo">Memuat data…</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="pde-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nomor Permintaan</th>
                        <th>Tanggal</th>
                        <th>Nama Peminta</th>
                        <th>Institusi</th>
                        <th>Jenis Biaya</th>
                        <th>Dropping</th>
                        <th>Detail Darah</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr class="loading-row"><td colspan="10"><span class="spinner"></span></td></tr>
                </tbody>
            </table>
        </div>
        <div class="pde-pagination" id="pagination" style="display:none;">
            <span class="pagination-info" id="paginationInfo">–</span>
            <div class="pagination-btns" id="paginationBtns"></div>
        </div>
    </div>

</div>

{{-- ══════════ MODAL TAMBAH / EDIT ══════════ --}}
<div class="modal-overlay" id="modalForm">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-header-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </div>
            <div style="flex:1;">
                <div class="modal-title" id="modalFormTitle">ADD Darah Minta External</div>
                <div class="modal-subtitle" id="modalFormSub">Formulir permintaan darah baru</div>
            </div>
            <button class="modal-close" onclick="closeModal('modalForm')">✕</button>
        </div>

        <div class="modal-body">
            <form id="formPermintaan" onsubmit="return false;">
                <input type="hidden" id="editId">

                {{-- ── Info Permintaan ── --}}
                <div class="form-section">
                    <div class="form-section-title">Informasi Permintaan</div>
                    <div class="form-grid form-grid-3" style="margin-bottom:12px;">
                        {{-- [FIX 2] Nomor auto-generate tampil langsung --}}
                        <div class="form-group">
                            <label class="form-label">Nomor Permintaan</label>
                            <div class="nomor-display loading" id="displayNomor">Memuat…</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="fTanggal" value="{{ date('Y-m-d') }}" readonly>
                        </div>

                        {{-- [FIX 3] Petugas dengan autocomplete & auto-isi nama peminta --}}
                        <div class="form-group">
                            <label class="form-label required">Petugas</label>
                            <input type="text" class="form-control" id="fPetugasInput"
                                placeholder="Ketik nama / kode petugas…"
                                autocomplete="off">
                            <div class="autocomplete-dropdown" id="petugasDropdown"></div>
                            <input type="hidden" id="fPetugas">
                            <input type="hidden" id="fPetugasKode">
                            <div class="field-hint" id="petugasHint"></div>
                        </div>
                    </div>

                    <div class="form-grid form-grid-2">
                        <div class="form-group">
                            <label class="form-label required">Nama Peminta</label>
                            {{-- auto-isi dari petugas, tetap bisa diedit manual --}}
                            <input type="text" class="form-control" id="fNamaPeminta"
                                placeholder="Terisi otomatis dari petugas atau ketik manual" maxlength="100">
                        </div>

                        {{-- [FIX 4] Institusi autocomplete & auto-isi nilai --}}
                        <div class="form-group">
                            <label class="form-label required">Institusi</label>
                            <input type="text" class="form-control" id="fInstitusiInput"
                                placeholder="Ketik nama institusi…"
                                autocomplete="off">
                            <div class="autocomplete-dropdown" id="institusiDropdown"></div>
                            <input type="hidden" id="fInstitusi">
                            <div class="field-hint" id="institusiHint"></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Jenis Biaya</label>
                            <select class="form-control" id="fJenisBiaya">
                                <option value="">-- Pilih --</option>
                                <option value="Dropping">Dropping</option>
                                <option value="Konfalesen">Konfalesen</option>
                                <option value="BPJS">BPJS</option>
                                <option value="ASURASI">Asuransi</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Dropping</label>
                            <select class="form-control" id="fDropping">
                                <option value="">-- Pilih Metode --</option>
                                <option value="AMBIL_SENDIRI">Ambil Sendiri</option>
                                <option value="DIANTAR">Diantar</option>
                                <option value="KURIR">Kurir</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tanggal Permintaan</label>
                            <input type="date" class="form-control" id="fTanggalPerlu">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Keterangan / OS</label>
                            <input type="text" class="form-control" id="fKeterangan" placeholder="Keterangan tambahan">
                        </div>
                    </div>
                </div>

                {{-- ── Detail Darah ── --}}
                <div class="form-section">
                    <div class="form-section-title">Detail Data Darah</div>
                    <div class="detail-table-wrap">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis Darah</th>
                                    <th>Gol Darah</th>
                                    <th>RH</th>
                                    <th>Jumlah</th>
                                    <th>Tgl Perlu</th>
                                    <th>Donor Pengganti</th>
                                    <th>No. FPUP</th>
                                    <th>Ket.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="detailTableBody"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn-add-row" onclick="addDetailRow()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Tambah Baris
                    </button>
                    <div class="legend">
                        <div class="legend-item"><div class="legend-dot sudah"></div> Sudah Dipenuhi</div>
                        <div class="legend-item"><div class="legend-dot belum"></div> Belum Dipenuhi</div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <div class="modal-footer-left">
                <button class="btn btn-outline btn-sm" id="btnCetakFpup" style="display:none;" onclick="cetakFpup()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak FPUP
                </button>
            </div>
            <div class="modal-footer-right">
                <button class="btn btn-secondary btn-sm" onclick="closeModal('modalForm')">Batal [F9]</button>
                <button class="btn btn-primary btn-sm" id="btnSimpan" onclick="saveForm()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan [F8]
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════ MODAL DETAIL VIEW ══════════ --}}
<div class="modal-overlay" id="modalView">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-header-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </div>
            <div style="flex:1;">
                <div class="modal-title" id="viewTitle">Detail Permintaan</div>
                <div class="modal-subtitle" id="viewNomor">–</div>
            </div>
            <button class="modal-close" onclick="closeModal('modalView')">✕</button>
        </div>
        <div class="modal-body" id="viewBody"></div>
        <div class="modal-footer">
            <div class="modal-footer-left"></div>
            <div class="modal-footer-right">
                <button class="btn btn-secondary btn-sm" onclick="closeModal('modalView')">Tutup</button>
                <button class="btn btn-primary btn-sm" id="btnEditFromView">Edit Permintaan</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Toast Container ── --}}
<div class="toast-container" id="toastContainer"></div>
@endsection

@push('scripts')
<script>
// ── State ──────────────────────────────────────
let currentPage    = 1;
let debounceTimer  = null;
let detailRowIndex = 0;
let currentViewId  = null;
let jenisDarahList = [];   // [FIX] cache dari tabel jenis_darah

// ── Bootstrap ─────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadData();
    loadJenisDarah();   // [FIX] load jenis darah dari DB saat halaman siap
    document.addEventListener('keydown', handleKeyboard);
    initPetugasAutocomplete();
    initInstitusiAutocomplete();
    // Tutup dropdown saat klik di luar
    document.addEventListener('click', closeAllDropdowns);
});

function handleKeyboard(e) {
    if (e.key === 'F5')  { e.preventDefault(); openCreateModal(); }
    if (e.key === 'F8')  { e.preventDefault(); saveForm(); }
    if (e.key === 'F9')  { e.preventDefault(); closeModal('modalForm'); closeModal('modalView'); }
    if (e.key === 'F10') { e.preventDefault(); window.print(); }
}

function closeAllDropdowns(e) {
    if (!e.target.closest('#petugasDropdown') && !e.target.closest('#fPetugasInput')) {
        document.getElementById('petugasDropdown').classList.remove('show');
    }
    if (!e.target.closest('#institusiDropdown') && !e.target.closest('#fInstitusiInput')) {
        document.getElementById('institusiDropdown').classList.remove('show');
    }
}

// ══════════════════════════════════════════════
// [FIX 5] Load Jenis Darah dari tabel jenis_darah
// ══════════════════════════════════════════════
async function loadJenisDarah() {
    try {
        const res  = await fetch(`{{ route('penyimpanan.permintaan_external.jenisDarah') }}`,
            { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (json.success && json.data.length) {
            jenisDarahList = json.data;
        }
    } catch {
        // fallback ke hardcode jika API gagal
        jenisDarahList = [
            { nama_pendek: 'WB' },
            { nama_pendek: 'FFP' },
            { nama_pendek: 'PRC' },
            { nama_pendek: 'TC' },
            { nama_pendek: 'CRYO' },
        ];
    }
}

/** Helper: build <option> list untuk select jenis darah */
function buildJenisDarahOptions(selected = '') {
    const fallback = ['WB','FFP','PRC','TC','CRYO'];
    const list = jenisDarahList.length
        ? jenisDarahList.map(j => j.nama_pendek)
        : fallback;
    return '<option value="">Pilih</option>' +
        list.map(v => `<option value="${v}" ${selected === v ? 'selected' : ''}>${v}</option>`).join('');
}

// ══════════════════════════════════════════════
// [FIX 2] Generate nomor otomatis via API
// ══════════════════════════════════════════════
async function loadNextNomor() {
    const el = document.getElementById('displayNomor');
    el.classList.add('loading');
    el.textContent = 'Memuat…';
    try {
        const res  = await fetch(`{{ route('penyimpanan.permintaan_external.nextNomor') }}`,
            { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();
        if (json.success) {
            el.textContent = json.nomor;
            el.classList.remove('loading');
        } else {
            el.textContent = 'AUTO-GENERATED';
        }
    } catch {
        el.textContent = 'AUTO-GENERATED';
        el.classList.remove('loading');
    }
}

// ══════════════════════════════════════════════
// [FIX 3] Petugas Autocomplete
// ══════════════════════════════════════════════
function initPetugasAutocomplete() {
    const input    = document.getElementById('fPetugasInput');
    const dropdown = document.getElementById('petugasDropdown');
    let timer;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        clearPetugasSelection();
        if (q.length < 2) { dropdown.classList.remove('show'); return; }
        timer = setTimeout(() => fetchPetugasOptions(q), 300);
    });

    input.addEventListener('keydown', function (e) {
        const items = dropdown.querySelectorAll('.autocomplete-item');
        const active = dropdown.querySelector('.autocomplete-item.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = active ? active.nextElementSibling : items[0];
            if (next) { active?.classList.remove('active'); next.classList.add('active'); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = active ? active.previousElementSibling : items[items.length - 1];
            if (prev) { active?.classList.remove('active'); prev.classList.add('active'); }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (active) active.click();
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('show');
        }
    });
}

async function fetchPetugasOptions(q) {
    const dropdown = document.getElementById('petugasDropdown');
    dropdown.innerHTML = '<div class="autocomplete-empty">Mencari…</div>';
    dropdown.classList.add('show');
    try {
        const res  = await fetch(`{{ route('penyimpanan.permintaan_external.petugas.search') }}?q=${encodeURIComponent(q)}`,
            { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (!json.success || !json.data.length) {
            dropdown.innerHTML = '<div class="autocomplete-empty">Tidak ada hasil</div>';
            return;
        }
        dropdown.innerHTML = '';
        json.data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            div.innerHTML = `<span class="autocomplete-kode">${item.kode}</span><span class="autocomplete-nama">${item.nama}</span>`;
            div.addEventListener('click', () => selectPetugas(item));
            dropdown.appendChild(div);
        });
    } catch {
        dropdown.innerHTML = '<div class="autocomplete-empty">Gagal memuat data</div>';
    }
}

/**
 * [FIX 3] Saat petugas dipilih → isi fPetugas, fPetugasKode,
 * dan AUTO-ISI fNamaPeminta dengan nama petugas
 */
function selectPetugas(item) {
    document.getElementById('fPetugasInput').value  = `${item.nama} (${item.kode})`;
    document.getElementById('fPetugas').value        = item.nama;
    document.getElementById('fPetugasKode').value    = item.kode;
    document.getElementById('petugasHint').className = 'field-hint ok';
    document.getElementById('petugasHint').textContent = `✓ ${item.nama}`;
    document.getElementById('fPetugasInput').classList.add('input-valid');
    document.getElementById('fPetugasInput').classList.remove('input-invalid');

    // AUTO-ISI nama peminta (hanya jika field masih kosong atau sama dengan petugas sebelumnya)
    const namaPeminta = document.getElementById('fNamaPeminta');
    if (!namaPeminta.value.trim() || namaPeminta.dataset.autoFilled === '1') {
        namaPeminta.value = item.nama;
        namaPeminta.dataset.autoFilled = '1';
    }

    document.getElementById('petugasDropdown').classList.remove('show');
}

function clearPetugasSelection() {
    document.getElementById('fPetugas').value     = '';
    document.getElementById('fPetugasKode').value = '';
    document.getElementById('petugasHint').textContent = '';
    document.getElementById('fPetugasInput').classList.remove('input-valid', 'input-invalid');
}

// ══════════════════════════════════════════════
// [FIX 4] Institusi Autocomplete
// ══════════════════════════════════════════════
function initInstitusiAutocomplete() {
    const input    = document.getElementById('fInstitusiInput');
    const dropdown = document.getElementById('institusiDropdown');
    let timer;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();
        clearInstitusiSelection();
        if (q.length < 2) { dropdown.classList.remove('show'); return; }
        timer = setTimeout(() => fetchInstitusiOptions(q), 300);
    });

    input.addEventListener('keydown', function (e) {
        const items  = dropdown.querySelectorAll('.autocomplete-item');
        const active = dropdown.querySelector('.autocomplete-item.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = active ? active.nextElementSibling : items[0];
            if (next) { active?.classList.remove('active'); next.classList.add('active'); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = active ? active.previousElementSibling : items[items.length - 1];
            if (prev) { active?.classList.remove('active'); prev.classList.add('active'); }
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (active) active.click();
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('show');
        }
    });
}

async function fetchInstitusiOptions(q) {
    const dropdown = document.getElementById('institusiDropdown');
    dropdown.innerHTML = '<div class="autocomplete-empty">Mencari…</div>';
    dropdown.classList.add('show');
    try {
        const res  = await fetch(`{{ route('penyimpanan.permintaan_external.institusi.search') }}?q=${encodeURIComponent(q)}`,
            { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (!json.success || !json.data.length) {
            dropdown.innerHTML = '<div class="autocomplete-empty">Tidak ada hasil</div>';
            return;
        }
        dropdown.innerHTML = '';
        json.data.forEach(item => {
            const div = document.createElement('div');
            div.className = 'autocomplete-item';
            // Sesuaikan field nama/kode sesuai model TujuanDarah Anda
            const kode = item.kode   ?? item.kode_instansi   ?? '';
            const nama = item.nama   ?? item.nama_instansi   ?? '';
            div.innerHTML = `<span class="autocomplete-kode">${kode}</span><span class="autocomplete-nama">${nama}</span>`;
            div.addEventListener('click', () => selectInstitusi({ kode, nama }));
            dropdown.appendChild(div);
        });
    } catch {
        dropdown.innerHTML = '<div class="autocomplete-empty">Gagal memuat data</div>';
    }
}

/**
 * [FIX 4] Saat institusi dipilih → isi field tampil dan hidden
 */
function selectInstitusi(item) {
    document.getElementById('fInstitusiInput').value  = item.nama;
    document.getElementById('fInstitusi').value        = item.nama;
    document.getElementById('institusiHint').className = 'field-hint ok';
    document.getElementById('institusiHint').textContent = `✓ ${item.nama}`;
    document.getElementById('fInstitusiInput').classList.add('input-valid');
    document.getElementById('fInstitusiInput').classList.remove('input-invalid');
    document.getElementById('institusiDropdown').classList.remove('show');
}

function clearInstitusiSelection() {
    document.getElementById('fInstitusi').value = '';
    document.getElementById('institusiHint').textContent = '';
    document.getElementById('fInstitusiInput').classList.remove('input-valid', 'input-invalid');
}

// ── Load Table Data ───────────────────────────
async function loadData(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;

    document.getElementById('tableBody').innerHTML =
        '<tr class="loading-row"><td colspan="10"><span class="spinner"></span></td></tr>';

    try {
        const params = new URLSearchParams({ page, search, status });
        const res = await fetch(`{{ route('penyimpanan.permintaan_external.data') }}?${params}`,
            { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        const json = await res.json();

        if (json.success) {
            renderTable(json.data);
            renderPagination(json.pagination);
            updateStats(json.data);
        }
    } catch (err) {
        console.error(err);
        document.getElementById('tableBody').innerHTML =
            '<tr><td colspan="10" style="text-align:center;padding:40px;color:#DC2626;">Gagal memuat data</td></tr>';
    }
}

function updateStats(data) {
    document.getElementById('statTotal').textContent    = data.length;
    document.getElementById('statBelum').textContent    = data.filter(d => d.status === 'BELUM_DIPENUHI').length;
    document.getElementById('statSebagian').textContent = data.filter(d => d.status === 'SEBAGIAN').length;
    document.getElementById('statSudah').textContent    = data.filter(d => d.status === 'SUDAH_DIPENUHI').length;
}

function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    if (!data.length) {
        tbody.innerHTML = `
            <tr><td colspan="10">
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                    <p>Belum ada data permintaan</p>
                </div>
            </td></tr>`;
        return;
    }

    tbody.innerHTML = data.map((row, i) => {
        const statusBadge = {
            'SUDAH_DIPENUHI': `<span class="badge badge-sudah"><span class="badge-dot"></span>Sudah Dipenuhi</span>`,
            'BELUM_DIPENUHI': `<span class="badge badge-belum"><span class="badge-dot"></span>Belum Dipenuhi</span>`,
            'SEBAGIAN':       `<span class="badge badge-sebagian"><span class="badge-dot"></span>Sebagian</span>`,
        }[row.status] ?? `<span class="badge">${row.status}</span>`;

        const detailCount = row.details ? row.details.length : 0;
        const dropping    = row.dropping ? row.dropping.replace('_', ' ') : '–';

        return `<tr onclick="viewRow(${row.id})">
            <td style="color:var(--gray-400);width:40px;">${((currentPage-1)*15)+i+1}</td>
            <td class="mono"><strong>${row.nomor_permintaan}</strong></td>
            <td>${row.tanggal ?? '–'}</td>
            <td><strong>${row.nama_peminta}</strong></td>
            <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${row.institusi_lain}">${row.institusi_lain}</td>
            <td>${row.jenis_biaya}</td>
            <td style="font-size:11px;color:var(--gray-500);">${dropping}</td>
            <td>
                <span style="font-size:11px;background:var(--gray-100);padding:2px 8px;border-radius:10px;color:var(--gray-600);">
                    ${detailCount} item
                </span>
            </td>
            <td>${statusBadge}</td>
            <td style="text-align:right;">
                <div style="display:flex;gap:4px;justify-content:flex-end;" onclick="event.stopPropagation()">
                    <button class="btn btn-sm btn-outline" onclick="editRow(${row.id})" title="Edit">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteRow(${row.id})" title="Hapus">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                    </button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function renderPagination(p) {
    const pg = document.getElementById('pagination');
    pg.style.display = 'flex';
    const from = (p.current_page - 1) * p.per_page + 1;
    const to   = Math.min(p.current_page * p.per_page, p.total);
    document.getElementById('paginationInfo').textContent  = `Menampilkan ${from}–${to} dari ${p.total} data`;
    document.getElementById('tableMetaInfo').textContent   = `${p.total} record`;

    let btns = '';
    for (let i = 1; i <= p.last_page; i++) {
        if (i === 1 || i === p.last_page || Math.abs(i - p.current_page) <= 2) {
            btns += `<button class="pg-btn ${i===p.current_page?'active':''}" onclick="loadData(${i})">${i}</button>`;
        } else if (Math.abs(i - p.current_page) === 3) {
            btns += `<button class="pg-btn" disabled>…</button>`;
        }
    }
    document.getElementById('paginationBtns').innerHTML = btns;
}

// ── Open Create Modal ─────────────────────────
function resetModalForm() {
    document.getElementById('editId').value       = '';
    document.getElementById('fNamaPeminta').value  = '';
    document.getElementById('fNamaPeminta').dataset.autoFilled = '';
    document.getElementById('fJenisBiaya').value   = '';
    document.getElementById('fDropping').value     = '';
    document.getElementById('fTanggalPerlu').value = '';
    document.getElementById('fKeterangan').value   = '';
    document.getElementById('btnCetakFpup').style.display = 'none';

    // Reset petugas
    document.getElementById('fPetugasInput').value = '';
    document.getElementById('fPetugas').value      = '';
    document.getElementById('fPetugasKode').value  = '';
    document.getElementById('petugasHint').textContent = '';
    document.getElementById('fPetugasInput').className = 'form-control';

    // Reset institusi
    document.getElementById('fInstitusiInput').value = '';
    document.getElementById('fInstitusi').value       = '';
    document.getElementById('institusiHint').textContent = '';
    document.getElementById('fInstitusiInput').className = 'form-control';

    // Reset detail rows
    document.getElementById('detailTableBody').innerHTML = '';
    detailRowIndex = 0;
    addDetailRow();
}

async function openCreateModal() {
    resetModalForm();
    document.getElementById('modalFormTitle').textContent = 'ADD Darah Minta External';
    document.getElementById('modalFormSub').textContent   = 'Formulir permintaan darah baru';
    openModal('modalForm');
    // [FIX 2] Load nomor setelah modal terbuka
    await loadNextNomor();
}

// ── Open Edit Modal ───────────────────────────
async function editRow(id) {
    try {
        const res  = await fetch(`{{ url('penyimpanan/permintaan_external') }}/${id}`,
            { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        const d    = json.data;

        resetModalForm();

        document.getElementById('editId').value           = d.id;
        document.getElementById('modalFormTitle').textContent = 'Edit Permintaan Darah External';
        document.getElementById('modalFormSub').textContent   = d.nomor_permintaan;
        document.getElementById('displayNomor').textContent   = d.nomor_permintaan;
        document.getElementById('displayNomor').classList.remove('loading');
        document.getElementById('fNamaPeminta').value  = d.nama_peminta;
        document.getElementById('fJenisBiaya').value   = d.jenis_biaya;
        document.getElementById('fDropping').value     = d.dropping ?? '';
        document.getElementById('fTanggalPerlu').value = d.tanggal_perlu ?? '';
        document.getElementById('fKeterangan').value   = d.keterangan ?? '';
        document.getElementById('btnCetakFpup').style.display = 'inline-flex';

        // Set petugas
        if (d.petugas) {
            document.getElementById('fPetugasInput').value = d.petugas_kode
                ? `${d.petugas} (${d.petugas_kode})` : d.petugas;
            document.getElementById('fPetugas').value      = d.petugas;
            document.getElementById('fPetugasKode').value  = d.petugas_kode || '';
            document.getElementById('petugasHint').className    = 'field-hint ok';
            document.getElementById('petugasHint').textContent  = `✓ ${d.petugas}`;
            document.getElementById('fPetugasInput').classList.add('input-valid');
        }

        // Set institusi
        if (d.institusi_lain) {
            document.getElementById('fInstitusiInput').value  = d.institusi_lain;
            document.getElementById('fInstitusi').value        = d.institusi_lain;
            document.getElementById('institusiHint').className = 'field-hint ok';
            document.getElementById('institusiHint').textContent = `✓ ${d.institusi_lain}`;
            document.getElementById('fInstitusiInput').classList.add('input-valid');
        }

        // Render detail rows
        document.getElementById('detailTableBody').innerHTML = '';
        detailRowIndex = 0;
        (d.details && d.details.length ? d.details : [null]).forEach(det => addDetailRow(det));

        openModal('modalForm');
    } catch (err) {
        console.error(err);
        showToast('Gagal memuat data', 'error');
    }
}

// ── Add Detail Row ────────────────────────────
function addDetailRow(data = null) {
    const idx = detailRowIndex++;
    const row = document.createElement('tr');
    row.id = `dr_${idx}`;
    row.setAttribute('data-idx', idx);

    const idField = data?.id
        ? `<input type="hidden" name="details[${idx}][id]" value="${data.id}">` : '';

    row.innerHTML = `
        ${idField}
        <td style="color:var(--gray-400);font-size:11px;">${idx + 1}</td>
        <td>
            <select class="detail-input detail-select" name="details[${idx}][jenis_darah]">
                ${buildJenisDarahOptions(data?.jenis_darah ?? '')}
            </select>
        </td>
        <td>
            <select class="detail-input detail-select" name="details[${idx}][gol_darah]" style="width:60px;">
                <option value="">–</option>
                ${['A','B','O','AB'].map(v =>
                    `<option value="${v}" ${data?.gol_darah===v?'selected':''}>${v}</option>`
                ).join('')}
            </select>
        </td>
        <td>
            <select class="detail-input detail-select" name="details[${idx}][rhesus]" style="width:72px;">
                <option value="">–</option>
                <option value="+" ${data?.rh==='Positif'?'selected':''}>Positif</option>
                <option value="-" ${data?.rh==='Negatif'?'selected':''}>Negatif</option>
            </select>
        </td>
        <td><input type="number" class="detail-input" name="details[${idx}][jumlah]"
            value="${data?.jumlah||''}" min="1" style="width:70px;" placeholder="0"></td>
        <td><input type="date" class="detail-input" name="details[${idx}][tgl_perlu]"
            value="${data?.tgl_perlu||''}" style="width:120px;"></td>
        <td>
            <select class="detail-input detail-select" name="details[${idx}][donor_pengganti]"
                onchange="toggleNoFpup(${idx})" style="width:80px;">
                <option value="Tidak" ${!data||data.donor_pengganti==='Tidak'?'selected':''}>Tidak</option>
                <option value="Ya"    ${data?.donor_pengganti==='Ya'?'selected':''}>Ya</option>
            </select>
        </td>
        <td>
            <input type="text" class="detail-input" name="details[${idx}][no_fpup]"
                value="${data?.no_fpup||''}" style="width:110px;" placeholder="No.FPUP/BDL"
                ${data?.donor_pengganti==='Ya'?'required':''}>
        </td>
        <td><input type="text" class="detail-input" name="details[${idx}][keterangan]"
            value="${data?.keterangan||''}" placeholder="–"></td>
        <td>
            <button type="button" class="btn-remove-row" onclick="removeDetailRow('dr_${idx}')">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </td>`;

    document.getElementById('detailTableBody').appendChild(row);
    if (data?.donor_pengganti === 'Ya') toggleNoFpup(idx);
}

function toggleNoFpup(idx) {
    const sel   = document.querySelector(`select[name="details[${idx}][donor_pengganti]"]`);
    const input = document.querySelector(`input[name="details[${idx}][no_fpup]"]`);
    if (!sel || !input) return;
    if (sel.value === 'Ya') {
        input.required = true;
        input.style.borderColor = 'var(--red-primary)';
        input.style.background  = '#FFF5F5';
        input.placeholder = 'WAJIB DIISI';
    } else {
        input.required = false;
        input.style.borderColor = '';
        input.style.background  = '';
        input.placeholder = 'No.FPUP/BDL';
        input.value = '';
    }
}

function removeDetailRow(rowId) {
    if (document.getElementById('detailTableBody').children.length <= 1) {
        showToast('Minimal 1 baris detail', 'error');
        return;
    }
    document.getElementById(rowId)?.remove();
    reindexDetailRows();
}

function reindexDetailRows() {
    document.querySelectorAll('#detailTableBody tr').forEach((row, newIdx) => {
        row.querySelectorAll('[name]').forEach(input => {
            input.setAttribute('name',
                input.getAttribute('name').replace(/details\[\d+\]/, `details[${newIdx}]`));
        });
        const donorSel = row.querySelector('[name*="[donor_pengganti]"]');
        if (donorSel) donorSel.setAttribute('onchange', `toggleNoFpup(${newIdx})`);
        const firstTd = row.querySelector('td:first-child');
        if (firstTd) firstTd.textContent = newIdx + 1;
        row.id = `dr_${newIdx}`;
        row.setAttribute('data-idx', newIdx);
    });
}

// ── Save ──────────────────────────────────────
async function saveForm() {
    const id     = document.getElementById('editId').value;
    const isEdit = !!id;
    const btn    = document.getElementById('btnSimpan');

    // Validasi petugas
    const petugasNama = document.getElementById('fPetugas').value;
    if (!petugasNama) {
        showToast('Petugas harus dipilih dari daftar', 'error');
        document.getElementById('fPetugasInput').focus();
        return;
    }

    // Validasi institusi
    const institusi = document.getElementById('fInstitusi').value;
    if (!institusi) {
        showToast('Institusi harus dipilih dari daftar', 'error');
        document.getElementById('fInstitusiInput').focus();
        return;
    }

    // Kumpulkan detail
    const details = [];
    document.querySelectorAll('#detailTableBody tr').forEach((row, idx) => {
        const get = name => row.querySelector(`[name$="[${name}]"]`)?.value ?? '';
        const jenis  = get('jenis_darah');
        const jumlah = parseInt(get('jumlah')) || 0;
        if (!jenis || jumlah === 0) return;

        const donorSel   = row.querySelector('[name*="[donor_pengganti]"]');
        const noFpupInput = row.querySelector('[name*="[no_fpup]"]');
        if (donorSel?.value === 'Ya' && !noFpupInput?.value.trim()) {
            showToast(`Baris ${idx+1}: No.FPUP/BDL wajib diisi`, 'error');
            noFpupInput.style.borderColor = '#DC2626';
            details.push(null); // flag error
            return;
        }

        const hiddenId = row.querySelector('[name*="[id]"]')?.value ?? null;
        details.push({
            ...(hiddenId ? { id: parseInt(hiddenId) } : {}),
            jenis_darah:      jenis,
            gol_darah:        get('gol_darah'),
            rhesus:           get('rhesus'),
            jumlah,
            donor_pengganti:  get('donor_pengganti'),
            no_fpup:          get('no_fpup'),
            keterangan:       get('keterangan'),
            tgl_perlu:        get('tgl_perlu') || null,
        });
    });

    if (details.includes(null)) return; // ada error fpup
    if (!details.length) {
        showToast('Minimal satu detail darah harus diisi lengkap', 'error');
        return;
    }

    const payload = {
        nama_peminta:   document.getElementById('fNamaPeminta').value,
        petugas:        petugasNama,
        petugas_kode:   document.getElementById('fPetugasKode').value,
        institusi_lain: institusi,
        jenis_biaya:    document.getElementById('fJenisBiaya').value,
        dropping:       document.getElementById('fDropping').value || null,
        tanggal_perlu:  document.getElementById('fTanggalPerlu').value || null,
        keterangan:     document.getElementById('fKeterangan').value || null,
        details,
    };

    if (!payload.nama_peminta) { showToast('Nama Peminta harus diisi', 'error'); return; }
    if (!payload.jenis_biaya)  { showToast('Jenis Biaya harus dipilih', 'error'); return; }

    btn.disabled   = true;
    btn.textContent = 'Menyimpan…';

    try {
        const url    = isEdit
            ? `{{ url('penyimpanan/permintaan_external') }}/${id}`
            : `{{ route('penyimpanan.permintaan_external.store') }}`;
        const method = isEdit ? 'PUT' : 'POST';

        const res  = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const json = await res.json();

        if (json.success) {
            showToast(json.message, 'success');
            closeModal('modalForm');
            loadData(currentPage);
        } else {
            const errors = json.errors
                ? Object.values(json.errors).flat().join('\n') : json.message;
            showToast(errors, 'error');
        }
    } catch (err) {
        console.error(err);
        showToast('Terjadi kesalahan jaringan', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Simpan [F8]`;
    }
}

// ── Delete ────────────────────────────────────
async function deleteRow(id) {
    if (!confirm('Hapus permintaan ini? Tindakan tidak dapat dibatalkan.')) return;
    try {
        const res  = await fetch(`{{ url('penyimpanan/permintaan_external') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        const json = await res.json();
        if (json.success) { showToast(json.message, 'success'); loadData(currentPage); }
        else showToast(json.message, 'error');
    } catch { showToast('Gagal menghapus', 'error'); }
}

// ── View Detail ───────────────────────────────
async function viewRow(id) {
    currentViewId = id;
    try {
        const res  = await fetch(`{{ url('penyimpanan/permintaan_external') }}/${id}`,
            { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        const d    = json.data;

        document.getElementById('viewTitle').textContent  = 'Detail Permintaan';
        document.getElementById('viewNomor').textContent  = d.nomor_permintaan;
        document.getElementById('btnEditFromView').onclick = () => { closeModal('modalView'); editRow(id); };

        const statusBadge = {
            'SUDAH_DIPENUHI': `<span class="badge badge-sudah"><span class="badge-dot"></span>Sudah Dipenuhi</span>`,
            'BELUM_DIPENUHI': `<span class="badge badge-belum"><span class="badge-dot"></span>Belum Dipenuhi</span>`,
            'SEBAGIAN':       `<span class="badge badge-sebagian"><span class="badge-dot"></span>Sebagian</span>`,
        }[d.status] ?? d.status;

        const detailRows = (d.details ?? []).map((det, i) => `
            <tr style="background:${det.jumlah_dipenuhi >= det.jumlah && det.jumlah > 0 ? 'rgba(34,197,94,.06)' : ''}">
                <td style="color:var(--gray-400)">${i+1}</td>
                <td><strong>${det.jenis_darah}</strong></td>
                <td>${det.gol_darah}</td>
                <td>${det.rhesus === 'Positif' ? 'Positif' : 'Negatif'}</td>
                <td class="mono">${det.jumlah}</td>
                <td class="mono">${det.jumlah_dipenuhi || 0}</td>
                <td class="mono" style="color:${(det.jumlah-(det.jumlah_dipenuhi||0))>0?'var(--red-primary)':'var(--gray-400)'};">
                    ${det.jumlah-(det.jumlah_dipenuhi||0)}
                </td>
                <td>${det.donor_pengganti||'Tidak'}</td>
                <td class="mono">${det.no_fpup||'–'}</td>
                <td style="color:var(--gray-400);font-size:11px;">${det.keterangan||'–'}</td>
            </tr>`).join('');

        document.getElementById('viewBody').innerHTML = `
            <div class="info-grid">
                <div class="info-row"><span class="info-label">Nomor Permintaan</span><span class="info-value mono">${d.nomor_permintaan}</span></div>
                <div class="info-row"><span class="info-label">Status</span><span class="info-value">${statusBadge}</span></div>
                <div class="info-row"><span class="info-label">Tanggal</span><span class="info-value">${d.tanggal}</span></div>
                <div class="info-row"><span class="info-label">Petugas</span><span class="info-value">${d.petugas}${d.petugas_kode?' ('+d.petugas_kode+')':''}</span></div>
                <div class="info-row"><span class="info-label">Nama Peminta</span><span class="info-value">${d.nama_peminta}</span></div>
                <div class="info-row"><span class="info-label">Institusi</span><span class="info-value">${d.institusi_lain}</span></div>
                <div class="info-row"><span class="info-label">Jenis Biaya</span><span class="info-value">${d.jenis_biaya}</span></div>
                <div class="info-row"><span class="info-label">Dropping</span><span class="info-value">${d.dropping?d.dropping.replace('_',' '):'–'}</span></div>
                <div class="info-row"><span class="info-label">Tanggal Perlu</span><span class="info-value">${d.tanggal_perlu||'–'}</span></div>
                <div class="info-row"><span class="info-label">Keterangan</span><span class="info-value">${d.keterangan||'–'}</span></div>
            </div>
            <div class="form-section-title" style="margin-bottom:10px;">Detail Darah</div>
            <div class="detail-table-wrap">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Jenis Darah</th><th>Gol</th><th>RH</th>
                            <th>Diminta</th><th>Dipenuhi</th><th>Sisa</th>
                            <th>Donor Pengganti</th><th>No.FPUP</th><th>Ket.</th>
                        </tr>
                    </thead>
                    <tbody>${detailRows||'<tr><td colspan="10" style="text-align:center;color:var(--gray-400);padding:20px;">Tidak ada detail</td></tr>'}</tbody>
                </table>
            </div>`;

        openModal('modalView');
    } catch (err) {
        console.error(err);
        showToast('Gagal memuat detail', 'error');
    }
}

function cetakFpup() { showToast('Cetak FPUP…', 'success'); }

// ── Modal helpers ─────────────────────────────
function openModal(id) {
    document.getElementById(id)?.classList.add('open');
}
function closeModal(id) {
    document.getElementById(id)?.classList.remove('open');
}
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});

// ── Toast ─────────────────────────────────────
function showToast(msg, type = 'success') {
    const c  = document.getElementById('toastContainer');
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    const icon = type === 'success'
        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`
        : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
    el.innerHTML = `${icon}<span>${msg}</span>`;
    c.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Debounce ──────────────────────────────────
function debounceLoad() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(loadData, 380);
}
</script>
@endpush