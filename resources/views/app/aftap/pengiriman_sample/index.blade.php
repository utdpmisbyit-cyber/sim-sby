@extends('layouts.index')

@section('title')
    Pengiriman Sample – Unit Transfusi Darah
@endsection

@push('styles')
<style>
/* ─── TOKENS ─── */
:root {
    --blue:         #3B82F6;
    --blue-soft:    rgba(59,130,246,.10);
    --blue-border:  rgba(59,130,246,.25);
    --blue-dark:    #1D4ED8;

    --bg-page:      #F5F7FB;
    --bg-card:      #FFFFFF;
    --bg-card-head: #FFFFFF;
    --bg-input:     #FFFFFF;
    --bg-row-hover: rgba(59,130,246,.04);

    --line:         #E5E7EB;
    --line-2:       #D1D5DB;

    --text-1:       #111827;
    --text-2:       #4B5563;
    --text-3:       #9CA3AF;

    --green:        #22C55E;
    --amber:        #F59E0B;
    --red:          #EF4444;

    --r-sm:  6px;
    --r-md:  10px;
    --r-lg:  14px;
    --r-xl:  18px;

    --font: 'Inter', system-ui, sans-serif;
    --mono: 'JetBrains Mono', monospace;
}

.ps *, .ps *::before, .ps *::after { box-sizing: border-box; margin: 0; padding: 0; }

.ps {
    font-family: var(--font);
    background: var(--bg-page);
    min-height: 100vh;
    padding: 24px 20px 56px;
    -webkit-font-smoothing: antialiased;
    color: var(--text-1);
}

.ps-inner { max-width: 1200px; margin: 0 auto; }

/* ── HEADER ── */
.ps-hdr {
    display: flex; align-items: center;
    justify-content: space-between;
    flex-wrap: wrap; gap: 14px;
    margin-bottom: 24px;
}
.ps-hdr-left { display: flex; align-items: center; gap: 14px; }
.ps-hdr-icon {
    width: 52px; height: 52px;
    background: var(--blue);
    border-radius: var(--r-lg);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 22px; flex-shrink: 0;
    box-shadow: 0 0 24px rgba(59,130,246,.30);
}
.ps-hdr-text h1 { font-size: 1.15rem; font-weight: 700; letter-spacing: -.2px; line-height: 1.3; }
.ps-hdr-text p  { font-size: .80rem; color: var(--text-2); margin-top: 2px; }
.ps-fpd-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px;
    background: transparent;
    border: 1.5px solid var(--line-2);
    border-radius: 999px;
    font-size: .82rem; font-weight: 600; color: var(--text-2);
    letter-spacing: .3px; white-space: nowrap;
}

/* ── CARD ── */
.ps-card {
    background: var(--bg-card);
    border: 1px solid var(--line);
    border-radius: var(--r-xl);
    overflow: hidden;
    margin-bottom: 16px;
}
.ps-card-head {
    display: flex; align-items: center; gap: 9px;
    padding: 13px 20px;
    border-bottom: 1px solid var(--line);
}
.ps-card-head i   { font-size: 14px; color: var(--blue); }
.ps-card-head h3  { font-size: .72rem; font-weight: 700; color: var(--text-2); text-transform: uppercase; letter-spacing: .7px; }
.ps-card-body     { padding: 20px; }

/* ── FORM GRID ── */
.ps-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}
.ps-field label {
    display: flex; align-items: center; gap: 5px;
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .6px;
    color: var(--text-3); margin-bottom: 8px;
}
.ps-input-wrap { position: relative; }
.ps-input-wrap input,
.ps-input-wrap select {
    width: 100%; height: 44px;
    background: var(--bg-input);
    border: 1px solid var(--line-2);
    border-radius: var(--r-md);
    padding: 0 14px;
    font-size: .88rem; font-family: var(--font);
    color: var(--text-1); outline: none;
    transition: border-color .16s, box-shadow .16s;
    -webkit-appearance: none;
}
.ps-input-wrap input:focus,
.ps-input-wrap select:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.ps-input-wrap input::placeholder { color: var(--text-3); font-size: .83rem; }
.ps-input-wrap input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(.5); cursor: pointer; }

/* checkbox NAT */
.ps-check-wrap {
    display: flex; align-items: center; gap: 10px;
    height: 44px;
}
.ps-check-wrap input[type="checkbox"] {
    width: 18px; height: 18px; border-radius: 4px; cursor: pointer; accent-color: var(--blue);
}
.ps-check-wrap label { font-size: .86rem; color: var(--text-2); cursor: pointer; font-weight: 600; }

/* ── SCAN ── */
.ps-scan-wrap {
    display: flex; align-items: center;
    border: 1.5px dashed var(--line-2);
    border-radius: var(--r-md);
    overflow: hidden;
    transition: border-color .16s;
}
.ps-scan-wrap:focus-within { border-color: var(--blue); }
.ps-scan-ico {
    width: 52px; height: 52px;
    display: flex; align-items: center; justify-content: center;
    background: var(--blue-soft);
    border-right: 1px solid var(--line-2);
    color: var(--blue); font-size: 18px; flex-shrink: 0;
}
#ps_scan {
    flex: 1; height: 52px; border: none; background: transparent;
    padding: 0 18px; font-size: .92rem; font-family: var(--font);
    color: var(--text-1); outline: none;
}
#ps_scan::placeholder { color: var(--text-3); font-size: .84rem; }

/* ── TAB BAR ── */
.ps-tab-bar {
    display: flex; gap: 0;
    border-bottom: 1px solid var(--line);
    background: var(--bg-card-head);
    padding: 0 16px;
}
.ps-tab {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 13px 16px 12px;
    background: none; border: none;
    border-bottom: 2.5px solid transparent;
    font-family: var(--font); font-size: .80rem; font-weight: 600;
    color: var(--text-3); cursor: pointer;
    transition: color .16s, border-color .16s;
    margin-bottom: -1px;
}
.ps-tab i { font-size: 13px; }
.ps-tab:hover { color: var(--text-2); }
.ps-tab.active { color: var(--blue); border-bottom-color: var(--blue); }
.ps-tab-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px; padding: 0 5px;
    border-radius: 999px; font-size: .65rem; font-weight: 700;
    background: var(--blue-soft); color: var(--blue); border: 1px solid var(--blue-border);
}
.ps-tab-badge--gray {
    background: rgba(156,163,175,.15); color: var(--text-2); border-color: var(--line-2);
}

/* ── TABLE ── */
.ps-table-wrap { overflow-x: auto; }
.ps-table { width: 100%; border-collapse: collapse; font-size: .84rem; }
.ps-table thead th {
    padding: 11px 14px; font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .55px;
    color: var(--text-3); text-align: left;
    border-bottom: 1px solid var(--line); white-space: nowrap;
}
.ps-table tbody tr { border-bottom: 1px solid var(--line); transition: background .12s; }
.ps-table tbody tr:last-child { border-bottom: none; }
.ps-table tbody tr:hover { background: var(--bg-row-hover); }
.ps-table tbody td { padding: 11px 14px; vertical-align: middle; color: var(--text-2); }
.ps-table .ps-no-data td { text-align: center; padding: 52px 16px; color: var(--text-3); font-size: .82rem; }
.ps-table .ps-no-data i { display: block; font-size: 32px; margin-bottom: 10px; opacity: .3; }
.ps-row-in { animation: psRowIn .28s ease forwards; }
@keyframes psRowIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

.ps-mono { font-family: var(--mono); font-size: .78rem; font-weight: 500; color: var(--text-1); }

.ps-badge {
    display: inline-flex; align-items: center;
    padding: 2px 9px; border-radius: 999px;
    font-size: .69rem; font-weight: 700;
    background: var(--blue-soft); color: var(--blue); border: 1px solid var(--blue-border);
    white-space: nowrap;
}
.ps-badge--red   { background: rgba(239,68,68,.10);  color: var(--red);   border-color: rgba(239,68,68,.25); }
.ps-badge--green { background: rgba(34,197,94,.12);  color: #16A34A;     border-color: rgba(34,197,94,.25); }
.ps-badge--amber { background: rgba(245,158,11,.12); color: #B45309;     border-color: rgba(245,158,11,.25); }

/* Suhu sample per baris (editable) */
.ps-suhu-input {
    width: 74px;
    height: 30px;
    border: 1px solid var(--line-2);
    border-radius: var(--r-sm);
    padding: 0 8px;
    font-size: .78rem;
    font-family: var(--mono);
    color: var(--text-1);
    outline: none;
    background: #fff;
    transition: border-color .14s, box-shadow .14s;
}
.ps-suhu-input:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(59,130,246,.14); }
.ps-suhu-input.ps-suhu-warn { border-color: rgba(239,68,68,.5); background: rgba(239,68,68,.06); color: var(--red); }

/* ── BUTTONS ── */
.ps-btn-del {
    width: 30px; height: 30px; border-radius: var(--r-sm);
    border: 1px solid var(--line-2); background: none; color: var(--text-3);
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    font-size: 12px; transition: all .14s;
}
.ps-btn-del:hover { background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.25); color: var(--red); }

.ps-btn-detail {
    width: 30px; height: 30px; border-radius: var(--r-sm);
    border: 1px solid var(--line-2); background: none; color: var(--text-3);
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    font-size: 12px; transition: all .14s;
}
.ps-btn-detail:hover { background: var(--blue-soft); border-color: var(--blue-border); color: var(--blue); }

.ps-btn-tolak {
    width: 30px; height: 30px; border-radius: var(--r-sm);
    border: 1px solid var(--line-2); background: none; color: var(--text-3);
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    font-size: 12px; transition: all .14s;
}
.ps-btn-tolak.active { background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.25); color: var(--red); }
.ps-btn-tolak:hover  { background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.25); color: var(--red); }

/* ── CARD FOOTER ── */
.ps-card-foot {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px;
    padding: 13px 20px;
    border-top: 1px solid var(--line);
}
.ps-count-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 13px;
    background: var(--bg-input);
    border: 1px solid var(--line-2); border-radius: 999px;
    font-size: .78rem; color: var(--text-2);
}
.ps-count-pill b { color: var(--text-1); }

/* ── ACTION BUTTONS GROUP ── */
.ps-btn-group { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

.ps-btn {
    display: inline-flex; align-items: center; gap: 7px;
    height: 42px; padding: 0 18px;
    border-radius: var(--r-md);
    font-family: var(--font); font-size: .84rem; font-weight: 700;
    cursor: pointer; border: none;
    transition: opacity .16s, transform .1s;
    white-space: nowrap;
}
.ps-btn:hover   { opacity: .88; transform: translateY(-1px); }
.ps-btn:active  { transform: scale(.97); }
.ps-btn:disabled { opacity: .35; cursor: not-allowed; transform: none; box-shadow: none; }

/* Save = abu outline */
.ps-btn--save {
    background: #fff;
    color: var(--text-2);
    border: 1.5px solid var(--line-2);
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.ps-btn--save:hover { background: #F9FAFB; }

/* Cetak = hijau outline */
.ps-btn--print {
    background: #fff;
    color: #16A34A;
    border: 1.5px solid rgba(34,197,94,.35);
    box-shadow: 0 1px 4px rgba(34,197,94,.08);
}
.ps-btn--print:hover { background: rgba(34,197,94,.06); }

/* Kirim FPD = biru solid */
.ps-btn--send {
    background: var(--blue);
    color: #fff;
    box-shadow: 0 4px 16px rgba(59,130,246,.30);
}

/* ── HISTORY FILTER ── */
.ps-hist-filter {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    padding: 14px 20px; border-bottom: 1px solid var(--line);
    background: var(--bg-card-head);
}
.ps-btn-filter {
    display: inline-flex; align-items: center; gap: 7px;
    height: 44px; padding: 0 18px;
    border: none; border-radius: var(--r-md);
    background: var(--blue); color: #fff;
    font-family: var(--font); font-size: .84rem; font-weight: 700;
    cursor: pointer; white-space: nowrap; transition: opacity .16s;
}
.ps-btn-filter:hover { opacity: .88; }

/* ── PAGINATION ── */
.ps-btn-page {
    width: 34px; height: 34px; border-radius: var(--r-sm);
    border: 1px solid var(--line-2); background: none; color: var(--text-2);
    cursor: pointer; display: inline-flex; align-items: center; justify-content: center;
    font-size: 12px; transition: all .14s;
}
.ps-btn-page:hover:not(:disabled) { background: var(--blue-soft); border-color: var(--blue-border); color: var(--blue); }
.ps-btn-page:disabled { opacity: .35; cursor: not-allowed; }

/* ── MODAL ── */
.ps-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 88888;
    align-items: center; justify-content: center; padding: 20px;
}
.ps-modal-overlay.show { display: flex; }
.ps-modal {
    background: var(--bg-card); border: 1px solid var(--line);
    border-radius: var(--r-xl); width: 100%; max-width: 820px;
    overflow: hidden; animation: psModalIn .22s ease;
}
@keyframes psModalIn { from { opacity: 0; transform: scale(.96) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.ps-modal-head {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    padding: 16px 20px; border-bottom: 1px solid var(--line);
    background: var(--bg-card-head);
}
.ps-modal-head h3 { font-size: .90rem; font-weight: 700; color: var(--text-1); }
.ps-modal-head p  { font-size: .78rem; color: var(--text-3); margin-top: 2px; }
.ps-modal-body    { padding: 0; }

/* ── TOAST ── */
.ps-toast-area {
    position: fixed; top: 18px; right: 18px; z-index: 99999;
    display: flex; flex-direction: column; gap: 8px; pointer-events: none;
}
.ps-toast {
    display: inline-flex; align-items: center; gap: 9px;
    padding: 11px 15px; border-radius: var(--r-md);
    font-size: .82rem; font-weight: 600; color: #fff;
    min-width: 250px; pointer-events: auto;
    animation: psToastIn .22s ease; font-family: var(--font);
    border: 1px solid rgba(255,255,255,.10);
}
.ps-toast i { font-size: 15px; flex-shrink: 0; }
.ps-toast.t-ok   { background: #14532D; border-color: rgba(34,197,94,.20); }
.ps-toast.t-err  { background: #7F1D1D; border-color: rgba(239,68,68,.25); }
.ps-toast.t-warn { background: #78350F; border-color: rgba(245,158,11,.20); }
@keyframes psToastIn { from { opacity: 0; transform: translateX(18px); } to { opacity: 1; transform: translateX(0); } }

/* ── SPINNER ── */
@keyframes psSpin { to { transform: rotate(360deg); } }
.ps-spin { animation: psSpin .85s linear infinite; display: inline-block; }

/* ── STATUS BADGE di footer setelah save ── */
.ps-saved-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 14px;
    background: rgba(34,197,94,.10);
    border: 1px solid rgba(34,197,94,.30);
    border-radius: 999px;
    font-size: .78rem; font-weight: 700; color: #16A34A;
}

/* ── RESPONSIVE ── */
@media (max-width: 640px) {
    .ps-form-grid { grid-template-columns: 1fr; }
    .ps-hdr       { flex-direction: column; align-items: flex-start; }
    .ps-btn-group { width: 100%; justify-content: flex-end; }
}
</style>
@endpush

@section('content')

<div class="ps-toast-area" id="psToastArea"></div>

<div class="ps">
<div class="ps-inner">

    {{-- ── PAGE HEADER ── --}}
    <div class="ps-hdr">
        <div class="ps-hdr-left">
            <div class="ps-hdr-icon">
                <i class="fas fa-vials"></i>
            </div>
            <div class="ps-hdr-text">
                <h1>Pengiriman Sample</h1>
                <p>Buat &amp; catat formulir pengiriman darah (FPD) ke laboratorium</p>
            </div>
        </div>
        <div class="ps-fpd-pill">
            <i class="fas fa-hashtag" style="font-size:.72rem;color:var(--text-3)"></i>
            <span id="ps_fpd_display">{{ $no_fpd }}</span>
        </div>
    </div>

    {{-- ── CARD 1: Info FPD + Scan ── --}}
    <div class="ps-card">
        <div class="ps-card-head">
            <i class="fas fa-clipboard-list"></i>
            <h3>Informasi Formulir Pengiriman</h3>
        </div>
        <div class="ps-card-body">

            {{-- Baris 1: field utama --}}
            <div class="ps-form-grid">
                <div class="ps-field">
                    <label><i class="fas fa-calendar-alt"></i> Tanggal FPD</label>
                    <div class="ps-input-wrap">
                        <input type="date" id="ps_tanggal" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="ps-field">
                    <label><i class="fas fa-tint"></i> Type Kantong</label>
                    <div class="ps-input-wrap">
                        <select id="ps_type_kantong">
                            <option value="">-- Pilih type --</option>
                            <option value="BIASA">BIASA</option>
                        </select>
                    </div>
                </div>

                <div class="ps-field">
                    <label><i class="fas fa-thermometer-half"></i> Suhu Coolbox</label>
                    <div class="ps-input-wrap">
                        <input type="text" id="ps_suhu" placeholder="Contoh: 2-6°C">
                    </div>
                </div>
            </div>

            {{-- Baris 2: logger, no selang, suhu sample default, petugas, nat --}}
            <div class="ps-form-grid" style="margin-bottom:20px">
                <div class="ps-field">
                    <label><i class="fas fa-microchip"></i> ID Logger</label>
                    <div class="ps-input-wrap">
                        <input type="text" id="ps_id_logger" placeholder="ID data logger...">
                    </div>
                </div>

                <div class="ps-field">
                    <label><i class="fas fa-wave-square"></i> No Selang</label>
                    <div class="ps-input-wrap">
                        {{-- Terisi otomatis dari data scan pertama --}}
                        <input type="text" id="ps_no_selang" placeholder="Terisi otomatis saat scan...">
                    </div>
                </div>

                {{-- ── BARU: Suhu Sample (default) ── --}}
                <div class="ps-field">
                    <label><i class="fas fa-temperature-low"></i> Suhu Sample (Default)</label>
                    <div class="ps-input-wrap">
                        <input type="text" id="ps_suhu_sample_default" placeholder="Contoh: 4.5°C">
                    </div>
                </div>

                <div class="ps-field">
                    <label><i class="fas fa-user-md"></i> Petugas Pemeriksa</label>
                    <div class="ps-input-wrap">
                        <input type="text" id="ps_petugas"
                            value="{{ $petugas_nama }}"
                            readonly
                            style="background:#F9FAFB;color:var(--text-2);cursor:not-allowed">
                    </div>
                </div>

                <div class="ps-field">
                    <label><i class="fas fa-flask"></i> Opsi Tambahan</label>
                    <div class="ps-check-wrap">
                        <input type="checkbox" id="ps_is_nat">
                        <label for="ps_is_nat">Pemeriksaan NAT</label>
                    </div>
                </div>
            </div>

            {{-- Keterangan --}}
            <div class="ps-field" style="margin-bottom:20px">
                <label><i class="fas fa-sticky-note"></i> Keterangan</label>
                <div class="ps-input-wrap">
                    <input type="text" id="ps_keterangan" placeholder="Keterangan tambahan...">
                </div>
            </div>

            {{-- Scan Input --}}
            <div class="ps-scan-wrap">
                <div class="ps-scan-ico">
                    <i class="fas fa-barcode"></i>
                </div>
                <input
                    type="text"
                    id="ps_scan"
                    placeholder="Scan no. kantong di sini, lalu tekan Enter..."
                    autocomplete="off"
                    autofocus
                >
            </div>
            <div style="font-size:.72rem;color:var(--text-3);margin-top:6px">
                <i class="fas fa-info-circle"></i>
                Nilai <b>Suhu Sample (Default)</b> di atas otomatis dipakai untuk setiap kantong yang di-scan —
                bisa diubah lagi per baris di tabel bila suhu tiap sample berbeda.
            </div>

        </div>
    </div>

    {{-- ── CARD 2: Tab Kantong + Riwayat ── --}}
    <div class="ps-card">

        <div class="ps-tab-bar">
            <button class="ps-tab active" id="ps_tab_scan" onclick="psSwitchTab('scan')">
                <i class="fas fa-list-ul"></i>
                Daftar Kantong
                <span class="ps-tab-badge" id="ps_tab_scan_badge">0</span>
            </button>
            <button class="ps-tab" id="ps_tab_history" onclick="psSwitchTab('history')">
                <i class="fas fa-history"></i>
                Riwayat FPD
                <span class="ps-tab-badge ps-tab-badge--gray" id="ps_tab_hist_badge">–</span>
            </button>
        </div>

        {{-- ── PANEL SCAN ── --}}
        <div id="ps_panel_scan">
            <div class="ps-table-wrap">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th style="width:38px">#</th>
                            <th>No Kantong</th>
                            <th>No Selang</th>{{-- ← kolom no_selang --}}
                            <th>Suhu Sample</th>{{-- ── BARU ── --}}
                            <th>Jenis</th>
                            <th>No Donor</th>
                            <th>Nama Donor</th>
                            <th>Gol. Darah</th>
                            <th>Asal Darah</th>
                            <th>Tgl Aftap</th>
                            <th style="width:52px;text-align:center">Tolak</th>
                            <th style="width:52px;text-align:center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody id="ps_tbody">
                        <tr class="ps-no-data" id="ps_empty">
                            <td colspan="12">
                                <i class="fas fa-inbox"></i>
                                Belum ada kantong yang di-scan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ps-card-foot">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                    <div class="ps-count-pill">
                        <i class="fas fa-layer-group" style="font-size:.7rem;opacity:.5"></i>
                        Total: <b id="ps_total">0</b> kantong
                        &nbsp;|&nbsp;
                        Ditolak: <b id="ps_total_tolak" style="color:var(--red)">0</b>
                    </div>
                    {{-- Status saved --}}
                    <div id="ps_saved_status" style="display:none">
                        <span class="ps-saved-pill">
                            <i class="fas fa-check-circle"></i>
                            Tersimpan: <b id="ps_saved_no_fpd"></b>
                        </span>
                    </div>
                </div>

                {{-- Grup tombol aksi --}}
                <div class="ps-btn-group">
                    {{-- Save (simpan dulu ke DB) --}}
                    <button class="ps-btn ps-btn--save" id="ps_btnSave"
                        onclick="psSimpan()" disabled>
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>

                    {{-- Cetak (print halaman / window.print) --}}
                    <button class="ps-btn ps-btn--print" id="ps_btnCetak"
                        onclick="psCetak()" disabled>
                        <i class="fas fa-print"></i>
                        Cetak
                    </button>

                    {{-- Kirim FPD ke serologi --}}
                    <button class="ps-btn ps-btn--send" id="ps_btnKirim"
                        onclick="psKirimFpd()" disabled>
                        <i class="fas fa-paper-plane"></i>
                        Kirim FPD
                    </button>
                </div>
            </div>
        </div>

        {{-- ── PANEL HISTORY ── --}}
        <div id="ps_panel_history" style="display:none">
            <div class="ps-hist-filter">
                <div class="ps-input-wrap" style="flex:1;min-width:150px">
                    <input type="date" id="ps_hist_dari" value="{{ date('Y-m-d') }}">
                </div>
                <div class="ps-input-wrap" style="flex:1;min-width:150px">
                    <input type="date" id="ps_hist_sampai" value="{{ date('Y-m-d') }}">
                </div>
                <div class="ps-input-wrap" style="flex:2;min-width:180px">
                    <input type="text" id="ps_hist_keyword" placeholder="No FPD...">
                </div>
                <button class="ps-btn-filter" onclick="psLoadHistory()">
                    <i class="fas fa-search"></i> Cari
                </button>
            </div>

            <div class="ps-table-wrap">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th style="width:38px">#</th>
                            <th>No FPD</th>
                            <th>Tanggal</th>
                            <th>Type Kantong</th>
                            <th>Suhu</th>
                            <th>NAT</th>
                            <th style="text-align:center">Jml</th>
                            <th style="width:100px;text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ps_hist_tbody">
                        <tr class="ps-no-data">
                            <td colspan="8">
                                <i class="fas fa-search"></i>
                                Klik "Cari" untuk memuat riwayat
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="ps-card-foot" id="ps_hist_foot" style="display:none">
                <div class="ps-count-pill">
                    <i class="fas fa-layer-group" style="font-size:.7rem;opacity:.5"></i>
                    <span id="ps_hist_info">–</span>
                </div>
                <div style="display:flex;gap:6px">
                    <button class="ps-btn-page" id="ps_hist_prev" onclick="psHistPage(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="ps-btn-page" id="ps_hist_next" onclick="psHistPage(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- end card 2 --}}

</div>
</div>

{{-- ── MODAL DETAIL ── --}}
<div class="ps-modal-overlay" id="ps_modal" onclick="psModalClose(event)">
    <div class="ps-modal">
        <div class="ps-modal-head">
            <div>
                <h3 id="ps_modal_title">Detail FPD</h3>
                <p id="ps_modal_sub"></p>
            </div>
            <button class="ps-btn-del" onclick="psModalHide()" style="width:32px;height:32px">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="ps-modal-body">
            <div class="ps-table-wrap" style="max-height:400px;overflow-y:auto">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th style="width:36px">#</th>
                            <th>No Kantong</th>
                            <th>No Selang</th>
                            <th>Suhu Sample</th>{{-- ── BARU ── --}}
                            <th>Jenis</th>
                            <th>No Donor</th>
                            <th>Nama Donor</th>
                            <th>Gol. Darah</th>
                            <th>Asal Darah</th>
                            <th style="text-align:center">Tolak</th>
                        </tr>
                    </thead>
                    <tbody id="ps_modal_tbody">
                        <tr class="ps-no-data">
                            <td colspan="10"><i class="fas fa-spinner ps-spin"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ══════════════════════════════════════
   CONFIG
══════════════════════════════════════ */
const ROUTE_INDEX    = "{{ route('aftap.pengiriman_sample.index') }}";
const ROUTE_SCAN     = "{{ route('aftap.pengiriman_sample.scan') }}";
const ROUTE_STORE    = "{{ route('aftap.pengiriman_sample.store') }}";
const ROUTE_KIRIM    = "{{ route('aftap.pengiriman_sample.kirim_fpd', ':id') }}"; // ← baru
const NO_FPD_INIT    = "{{ $no_fpd }}";
const CSRF           = "{{ csrf_token() }}";

/* ══════════════════════════════════════
   STATE
══════════════════════════════════════ */
let items        = [];
let histPage     = 1;
let histTotal    = 0;
let savedId      = null;   // ID pengiriman_sample setelah disimpan
let savedNoFpd   = null;
const HIST_PER   = 10;

/* ══════════════════════════════════════
   TOAST
══════════════════════════════════════ */
function psToast(msg, type = 'ok') {
    const map = {
        ok  : ['t-ok',  'fa-check-circle'],
        err : ['t-err', 'fa-times-circle'],
        warn: ['t-warn','fa-exclamation-triangle'],
    };
    const [cls, ico] = map[type] ?? map.ok;
    const el = document.createElement('div');
    el.className = `ps-toast ${cls}`;
    el.innerHTML = `<i class="fas ${ico}"></i><span>${msg}</span>`;
    document.getElementById('psToastArea').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

/* ══════════════════════════════════════
   NAT
══════════════════════════════════════ */
function psTerapkanNat(isNat) {
    const chk     = document.getElementById('ps_is_nat');
    const selType = document.getElementById('ps_type_kantong');

    chk.checked = isNat;

    if (isNat) {
        if (![...selType.options].find(o => o.value === 'NAT')) {
            selType.add(new Option('NAT (Nucleic Acid Testing)', 'NAT'));
        }
        selType.value = 'NAT';
        psToast('Mode NAT aktif', 'warn');
    } else {
        // Kembalikan pilihan ke BIASA hanya jika value saat ini NAT
        if (selType.value === 'NAT') selType.value = 'BIASA';
    }
}

/* ══════════════════════════════════════
   TAB
══════════════════════════════════════ */
function psSwitchTab(tab) {
    const isScan = tab === 'scan';
    document.getElementById('ps_panel_scan')   .style.display = isScan  ? '' : 'none';
    document.getElementById('ps_panel_history').style.display = !isScan ? '' : 'none';
    document.getElementById('ps_tab_scan')    .classList.toggle('active',  isScan);
    document.getElementById('ps_tab_history') .classList.toggle('active', !isScan);
    if (!isScan && histTotal === 0) psLoadHistory();
}

/* ══════════════════════════════════════
   RENDER SCAN TABLE
══════════════════════════════════════ */
function psRender() {
    const tbody   = document.getElementById('ps_tbody');
    const empty   = document.getElementById('ps_empty');
    const btnSave  = document.getElementById('ps_btnSave');
    const btnCetak = document.getElementById('ps_btnCetak');
    const btnKirim = document.getElementById('ps_btnKirim');

    // Bersihkan baris lama
    [...tbody.querySelectorAll('tr:not(#ps_empty)')].forEach(r => r.remove());

    const ditolak = items.filter(x => x.tolak).length;

    if (items.length === 0) {
        empty.style.display = '';
        btnSave.disabled  = true;
        btnCetak.disabled = true;
        btnKirim.disabled = true;
    } else {
        empty.style.display = 'none';
        btnSave.disabled = false;
        // Cetak & Kirim FPD hanya aktif setelah data disimpan
        btnCetak.disabled = !savedId;
        btnKirim.disabled = !savedId;

        items.forEach((it, i) => {
            const golRhesus = [it.gol_darah, it.rhesus].filter(Boolean).join(' ');
            const tglAftap  = it.tanggal_aftap
                ? new Date(it.tanggal_aftap).toLocaleDateString('id-ID',
                    {day:'2-digit', month:'short', year:'numeric'})
                : '–';
            const natBadge = it.is_nat
                ? `<span class="ps-badge ps-badge--amber" style="margin-left:4px">NAT</span>`
                : `<span class="ps-badge" style="margin-left:4px">BIASA</span>`;

            const tr = document.createElement('tr');
            tr.className = 'ps-row-in';
            if (it.tolak) tr.style.opacity = '.5';
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${i + 1}</td>
                <td>
                    <span class="ps-mono">${it.no_kantong ?? '–'}</span>
                    ${natBadge}
                </td>
                <td>
                    <span class="ps-mono" style="color:var(--text-2)">${it.no_selang ?? '–'}</span>
                </td>
                <td>
                    <input type="text" class="ps-suhu-input"
                        value="${it.suhu_sample ?? ''}"
                        placeholder="–°C"
                        title="Suhu sample kantong ini"
                        oninput="psUpdateSuhuSample(${i}, this.value)">
                </td>
                <td><span class="ps-badge">${it.jenis_kantong ?? '–'}</span></td>
                <td><span class="ps-mono">${it.no_donor ?? '–'}</span></td>
                <td style="font-weight:600;color:var(--text-1)">${it.nama_donor ?? '–'}</td>
                <td>
                    <span class="ps-badge ps-badge--${(it.rhesus ?? '').includes('-') ? 'red':'green'}">
                        ${golRhesus || '–'}
                    </span>
                </td>
                <td>${it.kode_asal_darah ?? '–'}</td>
                <td style="font-size:.78rem;color:var(--text-3)">${tglAftap}</td>
                <td style="text-align:center">
                    <button class="ps-btn-tolak ${it.tolak ? 'active':''}"
                        onclick="psToggleTolakLocal(${i})"
                        title="${it.tolak ? 'Batalkan tolak':'Tandai ditolak'}">
                        <i class="fas fa-ban"></i>
                    </button>
                </td>
                <td style="text-align:center">
                    <button class="ps-btn-del" onclick="psHapus(${i})" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('ps_total').textContent          = items.length;
    document.getElementById('ps_total_tolak').textContent    = ditolak;
    document.getElementById('ps_tab_scan_badge').textContent = items.length;
}

/* ══════════════════════════════════════
   UPDATE SUHU SAMPLE (per baris, tanpa render ulang
   supaya fokus input tidak hilang)
══════════════════════════════════════ */
function psUpdateSuhuSample(idx, val) {
    if (!items[idx]) return;
    items[idx].suhu_sample = val;
    psResetSavedState();
}

/* ══════════════════════════════════════
   TOGGLE TOLAK
══════════════════════════════════════ */
function psToggleTolakLocal(idx) {
    items[idx].tolak = !items[idx].tolak;
    psRender();
}

/* ══════════════════════════════════════
   HAPUS
══════════════════════════════════════ */
function psHapus(idx) {
    items.splice(idx, 1);
    // Reset saved state jika item diubah setelah simpan
    psResetSavedState();
    psRender();
}

/* ══════════════════════════════════════
   SCAN
══════════════════════════════════════ */
async function psDoScan(no_kantong) {
    no_kantong = no_kantong.trim();
    if (!no_kantong) return;

    // Cek duplikat
    if (items.find(x => x.no_kantong === no_kantong)) {
        psToast(`Kantong ${no_kantong} sudah di-scan`, 'warn');
        return;
    }

    const isNat       = document.getElementById('ps_is_nat').checked;
    const suhuDefault = document.getElementById('ps_suhu_sample_default').value;
    const scanEl       = document.getElementById('ps_scan');
    scanEl.disabled    = true;
    scanEl.placeholder = 'Memproses...';

    try {
        const res  = await fetch(ROUTE_SCAN, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({ no_kantong }),
        });
        const json = await res.json();
        if (!json.status) throw new Error(json.msg ?? 'Scan gagal');

        // Isi no_selang dari data aftap jika field masih kosong
        const noSelangEl = document.getElementById('ps_no_selang');
        if (!noSelangEl.value && json.data.no_selang) {
            noSelangEl.value = json.data.no_selang;
        }

        // Reset saved state karena ada item baru (perlu simpan ulang)
        psResetSavedState();

        items.unshift({
            ...json.data,
            is_nat      : isNat,
            tolak       : false,
            suhu_sample : suhuDefault || null,   // ── Suhu khusus sample/kantong ini ──
        });

        psRender();
        psToast(`Kantong ${no_kantong} discan${isNat ? ' — NAT ✓' : ''}`, 'ok');

    } catch(e) {
        psToast(e.message, 'err');
    } finally {
        scanEl.disabled    = false;
        scanEl.value       = '';
        scanEl.placeholder = 'Scan no. kantong di sini, lalu tekan Enter...';
        scanEl.focus();
    }
}

/* ══════════════════════════════════════
   RESET SAVED STATE
══════════════════════════════════════ */
function psResetSavedState() {
    savedId    = null;
    savedNoFpd = null;
    document.getElementById('ps_saved_status').style.display = 'none';
    document.getElementById('ps_btnCetak').disabled = true;
    document.getElementById('ps_btnKirim').disabled = true;
}

/* ══════════════════════════════════════
   SIMPAN (Save draft ke DB)
══════════════════════════════════════ */
async function psSimpan() {
    const tanggal_fpd       = document.getElementById('ps_tanggal').value;
    const type_kantong       = document.getElementById('ps_type_kantong').value;
    const suhu               = document.getElementById('ps_suhu').value;
    const id_logger          = document.getElementById('ps_id_logger').value;
    const no_selang          = document.getElementById('ps_no_selang').value;
    const petugas_pemeriksa  = document.getElementById('ps_petugas').value;
    const is_nat             = document.getElementById('ps_is_nat').checked;
    const keterangan         = document.getElementById('ps_keterangan').value;

    if (!tanggal_fpd)       return psToast('Isi tanggal FPD', 'warn');
    if (items.length === 0) return psToast('Belum ada kantong yang di-scan', 'warn');

    const btn = document.getElementById('ps_btnSave');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner ps-spin"></i> Menyimpan...';

    try {
        const res = await fetch(ROUTE_STORE, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({
                no_fpd           : NO_FPD_INIT,
                tanggal_fpd, type_kantong, suhu,
                id_logger,
                id_coolbox       : no_selang,
                petugas_pemeriksa,
                is_nat, keterangan,
                items,   // setiap item sudah membawa field `suhu` (suhu sample per kantong)
            }),
        });
        const json = await res.json();
        if (!json.status) throw new Error(json.msg ?? 'Gagal menyimpan');

        savedId    = json.id;
        savedNoFpd = json.no_fpd;

        // Tampilkan status tersimpan
        document.getElementById('ps_saved_status').style.display = '';
        document.getElementById('ps_saved_no_fpd').textContent   = savedNoFpd;

        // Aktifkan Cetak & Kirim FPD
        document.getElementById('ps_btnCetak').disabled = false;
        document.getElementById('ps_btnKirim').disabled = false;

        psToast(`FPD ${savedNoFpd} berhasil disimpan`, 'ok');

    } catch(e) {
        psToast(e.message, 'err');
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Simpan';
    }
}

/* ══════════════════════════════════════
   CETAK — buka popup print FPD (format sesuai UTDSBY-FS005-PDK-L4-28-2019)
══════════════════════════════════════ */
function psCetak() {
    if (!savedId) {
        psToast('Simpan FPD terlebih dahulu sebelum mencetak', 'warn');
        return;
    }

    // ── Kumpulkan data dari form & state ──────────────────────────────
    const noFpd      = savedNoFpd ?? '–';
    const tglFpd     = document.getElementById('ps_tanggal').value
        ? new Date(document.getElementById('ps_tanggal').value)
            .toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
        : '–';
    const tglFpdJam  = (() => {
        const now = new Date();
        return (document.getElementById('ps_tanggal').value
            ? new Date(document.getElementById('ps_tanggal').value)
                .toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }))
            + ' ' + now.toLocaleTimeString('id-ID', { hour12: false });
    })();

    const isNat      = document.getElementById('ps_is_nat').checked;
    const suhu       = document.getElementById('ps_suhu').value       || '';
    const idLogger   = document.getElementById('ps_id_logger').value  || '';
    const noSelang   = document.getElementById('ps_no_selang').value  || '';
    const petugas    = document.getElementById('ps_petugas').value    || '';
    const keterangan = document.getElementById('ps_keterangan').value || '';

    // ── Ringkasan per golongan darah ──────────────────────────────────
    // Struktur: { A: {pos:0, neg:0}, B: {pos:0, neg:0}, AB: {pos:0, neg:0}, O: {pos:0, neg:0} }
    const golMap = { A: { pos: 0, neg: 0 }, B: { pos: 0, neg: 0 }, AB: { pos: 0, neg: 0 }, O: { pos: 0, neg: 0 } };
    items.forEach(it => {
        if (!it.tolak) {
            const gol = (it.gol_darah ?? '').toUpperCase().trim();
            const rh  = (it.rhesus   ?? '').trim();
            if (golMap[gol] !== undefined) {
                if (rh === '+') golMap[gol].pos++;
                else            golMap[gol].neg++;
            }
        }
    });

    const totalDiterima = items.filter(x => !x.tolak).length;
    const totalDitolak  = items.filter(x =>  x.tolak).length;

    // ── Baris tabel detail ────────────────────────────────────────────
    const rowsHtml = items.map((it, i) => {
        const tgl = it.tanggal_aftap
            ? (() => {
                const d = new Date(it.tanggal_aftap);
                const hh = String(d.getHours()).padStart(2,'0');
                const mm = String(d.getMinutes()).padStart(2,'0');
                return hh + ':' + mm;
              })()
            : '–';

        // Durasi (jam sejak aftap ke sekarang, atau dari field jika ada)
        const durasi = it.durasi ?? '–';

        const tolakStyle = it.tolak
            ? 'background:#fff0f0;text-decoration:line-through;color:#aaa;'
            : '';

        return `
        <tr style="${tolakStyle}">
            <td style="text-align:center">${i + 1}</td>
            <td>${it.no_aftap ?? it.aftap_id ?? '–'}</td>
            <td>${it.no_kantong ?? '–'}</td>
            <td>${it.jenis_kantong ?? '–'}</td>
            <td>${it.ukuran ?? '450 cc'}</td>
            <td style="text-align:center">${it.penuh ?? 'Ya'}</td>
            <td style="text-align:center">${it.smpl  ?? 'Ya'}</td>
            <td>${it.jenis_donor ?? 'Sukarela'}</td>
            <td style="text-align:center">${it.kode_asal_darah ?? '000000'}</td>
            <td style="text-align:center">${it.petugas_id ?? ''}</td>
            <td>${it.no_donor ?? '–'}</td>
            <td style="text-align:center;font-weight:700">${it.gol_darah ?? '–'}</td>
            <td style="text-align:center;font-weight:700">${it.rhesus ?? '–'}</td>
            <td style="text-align:center">&#9633;&#9633;</td>
            <td>${it.keterangan ?? keterangan}   
            <span style="display:inline-block;width:14px;height:14px;border:1px solid #555;vertical-align:middle;text-align:center;line-height:14px">
              
            </span>   &nbsp; 
            <span style="display:inline-block;width:14px;height:14px;border:1px solid #555;vertical-align:middle;text-align:center;line-height:14px">
              
            </span></td>
            <td style="text-align:center">${it.id_logger ?? idLogger}</td>
            <td style="text-align:center;font-weight:600">${it.suhu_sample ?? it.suhu ?? suhu ?? '–'}</td>
            <td style="text-align:center">${tgl}</td>
        </tr>`;
    }).join('');

    // ── Baris ringkasan golongan darah ────────────────────────────────
    const golKeys   = ['A', 'B', 'AB', 'O'];
    // Total per kolom
    let totPos = {}, totNeg = {}, totAll = {};
    let grandPos = 0, grandNeg = 0, grandAll = 0;
    golKeys.forEach(g => {
        totPos[g] = golMap[g].pos;
        totNeg[g] = golMap[g].neg;
        totAll[g] = golMap[g].pos + golMap[g].neg;
        grandPos += golMap[g].pos;
        grandNeg += golMap[g].neg;
        grandAll += golMap[g].pos + golMap[g].neg;
    });

    // Buat header kolom: A (Positif | Total) | B (Positif | Total) | O (Positif | Total) | Total
    const summaryHeaderRow1 = `
        <tr>
            <th rowspan="2" style="background:#444">Jenis</th>
            <th colspan="2" style="background:#444">A</th>
            <th colspan="2" style="background:#444">B</th>
            <th colspan="2" style="background:#444">AB</th>
            <th colspan="2" style="background:#444">O</th>
            <th rowspan="2" style="background:#444">Total</th>
        </tr>
        <tr>
            <th style="background:#555">Positif</th><th style="background:#555">Total</th>
            <th style="background:#555">Positif</th><th style="background:#555">Total</th>
            <th style="background:#555">Positif</th><th style="background:#555">Total</th>
            <th style="background:#555">Positif</th><th style="background:#555">Total</th>
        </tr>`;

    const summaryDataRow = `
        <tr>
            <td style="font-weight:700">Quadruple</td>
            <td style="text-align:center">${golMap.A.pos}</td>
            <td style="text-align:center">${totAll.A}</td>
            <td style="text-align:center">${golMap.B.pos}</td>
            <td style="text-align:center">${totAll.B}</td>
            <td style="text-align:center">${golMap.AB.pos}</td>
            <td style="text-align:center">${totAll.AB}</td>
            <td style="text-align:center">${golMap.O.pos}</td>
            <td style="text-align:center">${totAll.O}</td>
            <td style="text-align:center;font-weight:700">${grandAll}</td>
        </tr>
        <tr style="font-weight:700;background:#f0f0f0">
            <td>Total</td>
            <td style="text-align:center">${golMap.A.pos}</td>
            <td style="text-align:center">${totAll.A}</td>
            <td style="text-align:center">${golMap.B.pos}</td>
            <td style="text-align:center">${totAll.B}</td>
            <td style="text-align:center">${golMap.AB.pos}</td>
            <td style="text-align:center">${totAll.AB}</td>
            <td style="text-align:center">${golMap.O.pos}</td>
            <td style="text-align:center">${totAll.O}</td>
            <td style="text-align:center">${grandAll}</td>
        </tr>`;

    // ── Waktu cetak ───────────────────────────────────────────────────
    const now      = new Date();
    const printTgl = now.toLocaleDateString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric' });
    const printJam = now.toLocaleTimeString('id-ID', { hour12: false });
    const operator = petugas || 'ADM';

    // ── HTML dokumen cetak ────────────────────────────────────────────
    const html = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>FPD – ${noFpd}</title>
<style>
  @page { size: A4 landscape; margin: 8mm 10mm; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, sans-serif; font-size: 9.5px; color: #111; }

  /* ── KODE DOKUMEN ── */
  .doc-code {
    text-align: right; font-size: 8.5px; color: #555;
    margin-bottom: 3px; letter-spacing: .2px;
  }

  /* ── HEADER ── */
  .fpd-header {
    display: flex; align-items: center;
    margin-bottom: 4px;
  }
  .fpd-logo { display: flex; align-items: center; gap: 6px; width: 200px; flex-shrink: 0; }
  .fpd-logo img { width: 36px; height: 36px; object-fit: contain; }
  .fpd-logo-text { font-size: 9px; font-weight: bold; line-height: 1.5; }
  .fpd-logo-text span { display: block; font-size: 8px; font-weight: bold; }
  .fpd-title-wrap { flex: 1; text-align: center; }
  .fpd-title {
    font-size: 13px; font-weight: bold; text-transform: uppercase;
    letter-spacing: .8px; text-decoration: underline;
  }
  .fpd-spacer { width: 200px; }

  /* ── META BAR ── */
  .fpd-meta-bar {
    display: flex; align-items: stretch;
    border: 1px solid #999; margin-bottom: 5px; font-size: 9px;
  }
  .fpd-meta-cell {
    flex: 1; padding: 3px 6px;
    border-right: 1px solid #999;
    display: flex; flex-direction: column; gap: 1px;
  }
  .fpd-meta-cell:last-child { border-right: none; }
  .fpd-meta-cell label { font-size: 7.5px; font-weight: bold; color: #666; text-transform: uppercase; }
  .fpd-meta-cell span  { font-size: 10px; font-weight: bold; }

  /* ── NO FPD + TGL blok kiri ── */
  .fpd-info-row {
    display: flex; align-items: center; gap: 0;
    margin-bottom: 4px; font-size: 9px;
  }
  .fpd-info-row .block { margin-right: 20px; }
  .fpd-info-row .block label { font-weight: bold; margin-right: 4px; }

  /* ── TABLE DETAIL ── */
  table.detail { width: 100%; border-collapse: collapse; font-size: 8.5px; margin-bottom: 5px; }
  table.detail thead th {
    background: #003087; color: #fff;
    border: 1px solid #888; padding: 3px 4px;
    font-size: 8px; text-align: center;
    text-transform: uppercase; letter-spacing: .2px;
    white-space: nowrap;
  }
  table.detail tbody td {
    border: 1px solid #bbb; padding: 2.5px 4px;
    vertical-align: middle; white-space: nowrap;
  }
  table.detail tbody tr:nth-child(even) { background: #f2f5ff; }

  /* ── TABLE SUMMARY ── */
  table.summary { border-collapse: collapse; font-size: 8.5px; }
  table.summary th, table.summary td {
    border: 1px solid #999; padding: 2.5px 7px;
    text-align: center; vertical-align: middle;
  }
  table.summary thead th { color: #fff; font-size: 8px; }

  /* ── FOOTER ── */
  .fpd-footer-row {
    display: flex; align-items: flex-end;
    margin-top: 8px; gap: 20px;
  }
  .fpd-ttd-box { flex: 1; text-align: center; }
  .fpd-ttd-box .lbl  { font-size: 9px; font-weight: bold; margin-bottom: 28px; }
  .fpd-ttd-box .line { border-top: 1px solid #000; margin-bottom: 2px; }
  .fpd-ttd-box .name { font-size: 9.5px; font-weight: bold; }
  .fpd-info-bottom   { flex: 1; font-size: 9px; line-height: 1.8; }

  .fpd-print-line {
    font-size: 8.5px; color: #555;
    border-top: 1px solid #ccc;
    padding-top: 4px; margin-top: 6px;
    display: flex; justify-content: space-between;
  }
</style>
</head>
<body onload="window.print()">

  <!-- ── KODE DOKUMEN (kanan atas) ── -->
  <div class="doc-code">UTDSBY-FS005-PDK-L4-28-2019</div>

  <!-- ── HEADER ── -->
  <div class="fpd-header">
    <div class="fpd-logo">
      <img src="/sneat/assets/img/logok.png" alt="PMI">
      <div class="fpd-logo-text">
        Palang Merah Indonesia
        <span>UDD PMI KOTA SURABAYA</span>
      </div>
    </div>
    <div class="fpd-title-wrap">
      <div class="fpd-title">Formulir Pengantar Darah &amp; Sampel</div>
    </div>
    <div class="fpd-spacer"></div>
  </div>

  <!-- ── NO FPD + TGL FPD + NAT/BIASA + ASAL DARAH ── -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;font-size:9px;border-bottom:1px solid #aaa;padding-bottom:3px;">
    <div>
      <span style="font-weight:bold">NO FPD</span>&nbsp;:&nbsp;<span style="font-weight:bold">${noFpd}</span>
      &emsp;
      <span style="font-weight:bold">TGL FPD</span>&nbsp;:&nbsp;<span>${tglFpdJam}</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
      <span><b>NAT:</b>
        <span style="display:inline-block;width:14px;height:14px;border:1px solid #555;vertical-align:middle;text-align:center;line-height:14px">
          ${isNat ? '✔' : ''}
        </span>
      </span>
      <span><b>BIASA :</b>
        <span style="display:inline-block;width:14px;height:14px;border:1px solid #555;vertical-align:middle;text-align:center;line-height:14px">
          ${!isNat ? '✔' : ''}
        </span>
      </span>
    </div>
    <div>
      <span><b>ASAL DARAH :</b> 000000</span>
      &nbsp;|&nbsp;
      <span><b>UDD PMI KOTA SURABAYA</b></span>
    </div>
  </div>

  <!-- ── TABEL DETAIL ── -->
  <table class="detail">
    <thead>
      <tr>
        <th>No.</th>
        <th>No AFTAP</th>
        <th>No Ktg</th>
        <th>Jenis</th>
        <th>Ukuran</th>
        <th>Penuh</th>
        <th>Smpl</th>
        <th>Jns Donor</th>
        <th>Asal Drh</th>
        <th>Ptgs</th>
        <th>No Donor</th>
        <th>Gol</th>
        <th>RH</th>
        <th>NoFPUP</th>
        <th>Keterangan</th>
        <th>ID</th>
        <th>Suhu</th>
        <th>Durasi</th>
      </tr>
    </thead>
    <tbody>
      ${rowsHtml || '<tr><td colspan="18" style="text-align:center;padding:8px;color:#999">Tidak ada data</td></tr>'}
    </tbody>
  </table>

  <!-- ── RINGKASAN GOLONGAN DARAH ── -->
  <table class="summary">
    <thead>
      ${summaryHeaderRow1}
    </thead>
    <tbody>
      ${summaryDataRow}
    </tbody>
  </table>

  <!-- ── FOOTER ── -->
  <div class="fpd-footer-row">
    <div class="fpd-ttd-box">
      <div class="lbl">PETUGAS PENGANTAR</div>
      <div class="line"></div>
      <div class="name">${petugas}</div>
    </div>
    <div class="fpd-ttd-box">
      <div class="lbl">Dicek Oleh :</div>
      <div style="margin-bottom:6px;font-weight:bold;text-align:center">${petugas}</div>
      <div class="line"></div>
      <div class="name">&nbsp;</div>
    </div>
    <div class="fpd-ttd-box">
      <div class="lbl">PETUGAS PENERIMA</div>
      <div class="line" style="margin-top:28px"></div>
      <div class="name">&nbsp;</div>
    </div>
  </div>

  <div style="font-size:8.5px;margin-top:6px;line-height:2">
    Suhu Coolbox: ${suhu} &nbsp;&deg;C<br>
    ID Logger :${idLogger}
  </div>

  <!-- ── PRINT LINE ── -->
  <div class="fpd-print-line">
    <span>Dicetak Tgl : ${printTgl} ${printJam} &nbsp;&nbsp; Operator : ${operator}</span>
    <span>Page 1 of 1</span>
  </div>

</body>
</html>`;

    const w = window.open('', '_blank', 'width=1200,height=800');
    w.document.write(html);
    w.document.close();
}
/* ══════════════════════════════════════
   KIRIM FPD ke Serologi
══════════════════════════════════════ */
async function psKirimFpd() {
    if (!savedId) {
        psToast('Simpan FPD terlebih dahulu sebelum mengirim', 'warn');
        return;
    }
    if (!confirm(`Yakin Kirim FPD "${savedNoFpd}"?`)) return;

    const btn = document.getElementById('ps_btnKirim');
    btn.disabled  = true;
    btn.innerHTML = '<i class="fas fa-spinner ps-spin"></i> Mengirim...';

    try {
        const url = ROUTE_KIRIM.replace(':id', savedId);
        const res = await fetch(url, {
            method : 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
            body   : JSON.stringify({}),
        });
        const json = await res.json();
        if (!json.status) throw new Error(json.msg ?? 'Gagal mengirim');

        psToast(`FPD berhasil dikirim ke serologi (${json.kode})`, 'ok');

        // Reset form setelah kirim berhasil
        psResetForm();

    } catch(e) {
        psToast(e.message, 'err');
        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim FPD';
    }
}

/* ══════════════════════════════════════
   RESET FORM
══════════════════════════════════════ */
function psResetForm() {
    items     = [];
    histTotal = 0;
    psResetSavedState();
    psRender();

    ['ps_suhu','ps_id_logger','ps_no_selang','ps_suhu_sample_default','ps_keterangan']
        .forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
    document.getElementById('ps_is_nat').checked     = false;
    document.getElementById('ps_type_kantong').value = '';
    document.getElementById('ps_tab_hist_badge').textContent = '–';

    // Reset tombol kirim ke teks awal
    const btnKirim = document.getElementById('ps_btnKirim');
    btnKirim.disabled  = true;
    btnKirim.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim FPD';
}

/* ══════════════════════════════════════
   HISTORY
══════════════════════════════════════ */
async function psLoadHistory(silent = false) {
    const dari    = document.getElementById('ps_hist_dari').value;
    const sampai  = document.getElementById('ps_hist_sampai').value;
    const keyword = document.getElementById('ps_hist_keyword').value.trim();
    const tbody   = document.getElementById('ps_hist_tbody');

    if (!silent) {
        tbody.innerHTML = `<tr class="ps-no-data"><td colspan="8">
            <i class="fas fa-spinner ps-spin"></i> Memuat...
        </td></tr>`;
    }

    try {
        const params = new URLSearchParams({
            mode: 'history', dari, sampai, keyword,
            page: histPage, per: HIST_PER
        });
        const res  = await fetch(`${ROUTE_INDEX}?${params}`, {
            headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();

        histTotal = json.total ?? 0;
        const rows = json.data ?? [];

        document.getElementById('ps_tab_hist_badge').textContent = histTotal;

        if (rows.length === 0) {
            tbody.innerHTML = `<tr class="ps-no-data"><td colspan="8">
                <i class="fas fa-inbox"></i> Tidak ada data
            </td></tr>`;
            document.getElementById('ps_hist_foot').style.display = 'none';
            return;
        }

        tbody.innerHTML = '';
        rows.forEach((row, i) => {
            const offset = (histPage - 1) * HIST_PER;
            const tgl = row.tanggal_fpd
                ? new Date(row.tanggal_fpd).toLocaleDateString('id-ID',
                    {day:'2-digit', month:'short', year:'numeric'})
                : '–';
            const tr = document.createElement('tr');
            tr.className = 'ps-row-in';
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${offset + i + 1}</td>
                <td><span class="ps-mono">${row.no_fpd}</span></td>
                <td>${tgl}</td>
                <td>${row.type_kantong
                    ? `<span class="ps-badge ${row.type_kantong === 'NAT' ? 'ps-badge--amber':''}">${row.type_kantong}</span>`
                    : '–'}</td>
                <td>${row.suhu ?? '–'}</td>
                <td>${row.is_nat
                        ? '<span class="ps-badge ps-badge--amber"><i class="fas fa-flask" style="font-size:.6rem"></i> NAT</span>'
                        : '<span class="ps-badge">BIASA</span>'}</td>
                <td style="text-align:center">
                    <span class="ps-badge ps-badge--green">${row.detail_count ?? 0}</span>
                </td>
                <td style="text-align:center">
                    <div style="display:flex;gap:4px;justify-content:center">
                        <button class="ps-btn-detail"
                            onclick="psShowDetail(${row.id},'${row.no_fpd}','${tgl}')"
                            title="Lihat detail">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="ps-btn-del"
                            onclick="psHapusHistory(${row.id},this)"
                            title="Hapus">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });

        const totalPage = Math.ceil(histTotal / HIST_PER);
        document.getElementById('ps_hist_foot').style.display = '';
        document.getElementById('ps_hist_info').textContent =
            `${rows.length} dari ${histTotal} FPD (hal. ${histPage}/${totalPage})`;
        document.getElementById('ps_hist_prev').disabled = histPage <= 1;
        document.getElementById('ps_hist_next').disabled = histPage >= totalPage;

    } catch(e) {
        tbody.innerHTML = `<tr class="ps-no-data"><td colspan="8">
            <i class="fas fa-exclamation-triangle"></i> Gagal: ${e.message}
        </td></tr>`;
    }
}

function psHistPage(dir) {
    histPage = Math.max(1, histPage + dir);
    psLoadHistory();
}

async function psHapusHistory(id, btn) {
    if (!confirm('Yakin hapus FPD ini beserta semua detail kantong?')) return;
    btn.disabled = true;
    try {
        const res  = await fetch(`${ROUTE_INDEX.replace(/\/$/, '')}/${id}`, {
            method : 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest' }
        });
        const json = await res.json();
        if (!json.status) throw new Error(json.msg);
        psToast('FPD berhasil dihapus', 'ok');
        psLoadHistory(true);
    } catch(e) {
        psToast(e.message, 'err');
        btn.disabled = false;
    }
}

/* ══════════════════════════════════════
   MODAL DETAIL
══════════════════════════════════════ */
async function psShowDetail(id, noFpd, tgl) {
    document.getElementById('ps_modal_title').textContent = `Detail – ${noFpd}`;
    document.getElementById('ps_modal_sub').textContent   = `Tanggal: ${tgl}`;
    document.getElementById('ps_modal_tbody').innerHTML   =
        `<tr class="ps-no-data"><td colspan="10">
            <i class="fas fa-spinner ps-spin"></i>
        </td></tr>`;
    document.getElementById('ps_modal').classList.add('show');

    try {
        const res  = await fetch(`${ROUTE_INDEX}?mode=detail&id=${id}`, {
            headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }
        });
        const json = await res.json();
        const rows = json.data ?? [];
        const tbody = document.getElementById('ps_modal_tbody');

        if (rows.length === 0) {
            tbody.innerHTML = `<tr class="ps-no-data"><td colspan="10">
                <i class="fas fa-inbox"></i> Tidak ada detail
            </td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        rows.forEach((d, i) => {
            const golRhesus = [d.gol_darah, d.rhesus].filter(Boolean).join(' ');
            const natBadge  = d.is_nat
                ? `<span class="ps-badge ps-badge--amber" style="margin-left:4px;font-size:.6rem">NAT</span>`
                : `<span class="ps-badge" style="margin-left:4px;font-size:.6rem">BIASA</span>`;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="color:var(--text-3);font-size:.78rem">${d.urut ?? i + 1}</td>
                <td>
                    <span class="ps-mono">${d.no_kantong ?? '–'}</span>
                    ${natBadge}
                </td>
                <td>
                    <span class="ps-mono" style="color:var(--text-2)">${d.id_coolbox ?? '–'}</span>
                </td>
                <td style="text-align:center">
                    <span class="ps-mono">${d.suhu_sample ?? d.suhu ?? '–'}</span>
                </td>
                <td><span class="ps-badge">${d.jenis_kantong ?? '–'}</span></td>
                <td><span class="ps-mono">${d.no_donor ?? '–'}</span></td>
                <td style="font-weight:600;color:var(--text-1)">${d.nama_donor ?? '–'}</td>
                <td>
                    <span class="ps-badge ps-badge--${(d.rhesus ?? '').includes('-') ? 'red':'green'}">
                        ${golRhesus || '–'}
                    </span>
                </td>
                <td>${d.kode_asal_darah ?? '–'}</td>
                <td style="text-align:center">
                    ${d.tolak
                        ? '<span class="ps-badge ps-badge--red">Ditolak</span>'
                        : '<span class="ps-badge ps-badge--green">OK</span>'}
                </td>`;
            tbody.appendChild(tr);
        });

    } catch(e) {
        document.getElementById('ps_modal_tbody').innerHTML =
            `<tr class="ps-no-data"><td colspan="10">Gagal memuat detail</td></tr>`;
    }
}

function psModalHide()   { document.getElementById('ps_modal').classList.remove('show'); }
function psModalClose(e) { if (e.target === document.getElementById('ps_modal')) psModalHide(); }

/* ══════════════════════════════════════
   INIT
══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('ps_scan').addEventListener('keydown', e => {
        if (e.key === 'Enter') psDoScan(e.target.value);
    });

    document.getElementById('ps_hist_keyword').addEventListener('keydown', e => {
        if (e.key === 'Enter') { histPage = 1; psLoadHistory(); }
    });

    document.getElementById('ps_is_nat').addEventListener('change', e => {
        psTerapkanNat(e.target.checked);
    });

    psRender();
    psTerapkanNat(false);
});
</script>
@endpush