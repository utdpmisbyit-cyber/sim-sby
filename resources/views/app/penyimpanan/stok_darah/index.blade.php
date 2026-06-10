@extends('layouts.index')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════
   CSS VARIABLES - LIGHT THEME (WHITE BACKGROUND)
═══════════════════════════════════════════ */
:root {
    --red:          #D32F2F;
    --red-deep:     #B71C1C;
    --red-soft:     #EF5350;
    --red-glow:     rgba(211, 47, 47, 0.1);
    --red-glow-lg:  rgba(211, 47, 47, 0.2);

    --bg:           #F8F9FA;
    --bg-card:      #FFFFFF;
    --bg-surface:   #F1F3F5;
    --bg-elevated:  #E9ECEF;
    --bg-hover:     #F8F9FA;

    --border:       rgba(0,0,0,0.08);
    --border-md:    rgba(0,0,0,0.12);
    --border-lg:    rgba(0,0,0,0.15);

    --text-1:       #212529;
    --text-2:       #495057;
    --text-3:       #6C757D;

    --green:  #2E7D32;
    --green-s:#43A047;
    --yellow: #F57C00;
    --yellow-s:#FB8C00;
    --blue:   #1976D2;
    --blue-s: #1E88E5;
    --purple: #8E24AA;

    --font:  'DM Sans', sans-serif;
    --mono:  'JetBrains Mono', monospace;

    --radius-sm: 6px;
    --radius:    10px;
    --radius-lg: 14px;
    --radius-xl: 18px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ═══════════════════════════════════════════
   BASE
═══════════════════════════════════════════ */
.penyimpanan-wrap {
    background: var(--bg);
    min-height: 100vh;
    font-family: var(--font);
    color: var(--text-1);
}

/* ═══════════════════════════════════════════
   TOP BAR
═══════════════════════════════════════════ */
.top-bar {
    position: sticky; top: 0; z-index: 200;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border-md);
    padding: 0 2rem;
    height: 58px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.top-bar-left { display: flex; align-items: center; gap: .85rem; }
.bar-icon {
    width: 34px; height: 34px;
    background: linear-gradient(135deg, var(--red-deep), var(--red-soft));
    border-radius: 9px;
    display: grid; place-items: center;
    font-size: 1rem;
    box-shadow: 0 0 18px var(--red-glow-lg);
    flex-shrink: 0;
    color: white;
}
.bar-title { font-size: .95rem; font-weight: 700; letter-spacing: -.01em; }
.bar-sub   { font-size: .72rem; color: var(--text-2); }

.top-bar-right { display: flex; align-items: center; gap: .65rem; }
.pill-live {
    display: inline-flex; align-items: center; gap: .35rem;
    background: rgba(46,125,50,.12);
    border: 1px solid rgba(46,125,50,.28);
    color: var(--green);
    font-size: .7rem; font-weight: 700; letter-spacing: .06em;
    text-transform: uppercase; font-family: var(--mono);
    padding: .28rem .7rem; border-radius: 100px;
}
.pill-live i {
    display: inline-block;
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--green);
    animation: blink 1.6s ease infinite;
}
@keyframes blink { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.3;transform:scale(.65)} }

#topClock {
    font-family: var(--mono); font-size: .75rem; color: var(--text-3);
    letter-spacing: .03em;
}

/* ═══════════════════════════════════════════
   MAIN LAYOUT
═══════════════════════════════════════════ */
.page-body {
    padding: 1.75rem 2rem;
    max-width: 1440px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* ═══════════════════════════════════════════
   SUMMARY STRIP
═══════════════════════════════════════════ */
.summary-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.s-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.1rem 1.3rem;
    display: flex; align-items: center; gap: 1rem;
    transition: border-color .2s, transform .2s;
    position: relative; overflow: hidden;
}
.s-card:hover { border-color: var(--border-md); transform: translateY(-2px); }
.s-card::after {
    content: '';
    position: absolute; inset: 0;
    background: var(--card-glow, transparent);
    pointer-events: none;
}
.s-icon {
    width: 42px; height: 42px; border-radius: var(--radius);
    display: grid; place-items: center;
    font-size: 1.25rem; flex-shrink: 0;
}
.s-card.c-red  .s-icon { background: rgba(211, 47, 47, 0.1); color: var(--red);}
.s-card.c-yel  .s-icon { background: rgba(245, 124, 0, 0.1); color: var(--yellow);}
.s-card.c-grn  .s-icon { background: rgba(46, 125, 50, 0.1); color: var(--green);}
.s-card.c-blu  .s-icon { background: rgba(25, 118, 210, 0.1); color: var(--blue);}

.s-val  { font-family: var(--mono); font-size: 1.75rem; font-weight: 700; line-height: 1; }
.s-lbl  { font-size: .72rem; color: var(--text-2); margin-top: .3rem; font-weight: 500; }
.s-card.c-red .s-val { color: var(--red); }
.s-card.c-yel .s-val { color: var(--yellow); }
.s-card.c-grn .s-val { color: var(--green); }
.s-card.c-blu .s-val { color: var(--blue); }

/* ═══════════════════════════════════════════
   FILTER BAR
═══════════════════════════════════════════ */
.filter-bar {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 1rem;
}
.fg { display: flex; flex-direction: column; gap: .35rem; flex: 1; min-width: 130px; }
.fg label {
    font-size: .68rem; font-weight: 700; color: var(--text-3);
    text-transform: uppercase; letter-spacing: .09em;
}
.fg select, .fg input[type="text"] {
    background: var(--bg-surface);
    border: 1px solid var(--border-md);
    border-radius: var(--radius);
    padding: .58rem .85rem;
    color: var(--text-1);
    font-family: var(--font); font-size: .85rem;
    outline: none; width: 100%;
    transition: border-color .18s, box-shadow .18s;
}
.fg select {
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236C757D'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right .8rem center;
    padding-right: 2rem;
}
.fg select option { background: var(--bg-elevated); }
.fg select:focus, .fg input:focus {
    border-color: var(--red);
    box-shadow: 0 0 0 3px var(--red-glow);
}

.btn { display: inline-flex; align-items: center; gap: .45rem; padding: .6rem 1.2rem;
    border-radius: var(--radius); border: none; cursor: pointer;
    font-family: var(--font); font-size: .85rem; font-weight: 600;
    transition: background .18s, box-shadow .18s, transform .1s; white-space: nowrap; }
.btn-primary { background: var(--red); color: #fff; }
.btn-primary:hover { background: var(--red-soft); box-shadow: 0 4px 18px var(--red-glow-lg); transform: translateY(-1px); }
.btn-ghost { background: var(--bg-elevated); color: var(--text-2); border: 1px solid var(--border-md); }
.btn-ghost:hover { background: var(--bg-hover); color: var(--text-1); }
.btn-sm { padding: .42rem .85rem; font-size: .8rem; }

/* ═══════════════════════════════════════════
   MAIN CONTENT GRID
═══════════════════════════════════════════ */
.content-row {
    display: grid;
    grid-template-columns: 1fr 310px;
    gap: 1.25rem;
    align-items: start;
}

/* ═══════════════════════════════════════════
   TABLE CARD
═══════════════════════════════════════════ */
.table-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
    position: relative;
}
.table-card-head {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 1rem;
    flex-wrap: wrap;
}
.table-card-head h2 { font-size: .9rem; font-weight: 700; flex: 1; }
.tc-meta { font-family: var(--mono); font-size: .72rem; color: var(--text-3); }

.search-wrap {
    display: flex; align-items: center; gap: .45rem;
    background: var(--bg-surface);
    border: 1px solid var(--border-md);
    border-radius: var(--radius);
    padding: .42rem .8rem;
}
.search-wrap svg { color: var(--text-3); flex-shrink: 0; }
.search-wrap input {
    background: none; border: none; outline: none;
    color: var(--text-1); font-family: var(--font); font-size: .83rem;
    width: 160px;
}
.search-wrap input::placeholder { color: var(--text-3); }

/* Tab bar */
.tab-row {
    display: flex; gap: .25rem;
    background: var(--bg-surface);
    padding: .3rem;
    border-radius: var(--radius);
}
.tab {
    flex: 1; padding: .38rem .65rem;
    border: none; border-radius: 7px;
    background: none; color: var(--text-3);
    font-family: var(--font); font-size: .78rem; font-weight: 600;
    cursor: pointer; transition: background .15s, color .15s;
    white-space: nowrap;
}
.tab.active { background: var(--bg-card); color: var(--text-1); box-shadow: 0 1px 4px rgba(0,0,0,.1); }

/* Scroll wrapper */
.tbl-scroll { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: .83rem; }
thead th {
    background: var(--bg-surface);
    color: var(--text-3); font-weight: 700;
    font-size: .67rem; text-transform: uppercase; letter-spacing: .09em;
    padding: .7rem 1rem; text-align: left;
    border-bottom: 1px solid var(--border); white-space: nowrap;
    position: sticky; top: 0;
}
tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: var(--bg-hover); }
tbody td { padding: .85rem 1rem; vertical-align: middle; }
.td-mono { font-family: var(--mono); font-size: .78rem; color: var(--text-2); }

/* Badges */
.blood-badge {
    display: inline-flex; align-items: center;
    padding: .22rem .6rem; border-radius: 6px;
    font-family: var(--mono); font-size: .8rem; font-weight: 700;
}
.blood-badge.A  { background: rgba(25,118,210,.14); color: var(--blue); border:1px solid rgba(25,118,210,.28); }
.blood-badge.B  { background: rgba(46,125,50,.14); color: var(--green); border:1px solid rgba(46,125,50,.28); }
.blood-badge.AB { background: rgba(142,36,170,.14); color: var(--purple); border:1px solid rgba(142,36,170,.28); }
.blood-badge.O  { background: rgba(211,47,47,.14); color: var(--red); border:1px solid rgba(211,47,47,.28);  }

.rhesus {
    font-family: var(--mono); font-size: .72rem; font-weight: 700;
    padding: .12rem .38rem; border-radius: 4px; margin-left: .2rem;
}
.rhesus.pos { background: rgba(46,125,50,.1); color: var(--green); }
.rhesus.neg { background: rgba(211,47,47,.1); color: var(--red); }

.status-pill {
    display: inline-flex; align-items: center; gap: .28rem;
    padding: .22rem .65rem; border-radius: 100px;
    font-size: .73rem; font-weight: 600; white-space: nowrap;
}
.status-pill::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }
.sp-tersedia  { background: rgba(46,125,50,.12); color: var(--green); }
.sp-expired   { background: rgba(211,47,47,.12); color: var(--red); }
.sp-mendekati { background: rgba(245,124,0,.12); color: var(--yellow); }
.sp-dipakai   { background: rgba(25,118,210,.12); color: var(--blue); }

/* Row coloring */
tr.r-expired  { background: rgba(211, 47, 47, 0.05); }
tr.r-warning  { background: rgba(245, 124, 0, 0.05); }
tr.r-expired td, tr.r-expired .td-mono { opacity: .8; }

/* Sisa hari */
.sisa-chip {
    font-family: var(--mono); font-size: .82rem; font-weight: 700;
    display: inline-block; min-width: 40px; text-align: center;
    padding: .15rem .45rem; border-radius: 5px;
    cursor: help;
}
.sisa-ok  { color: var(--green); background: rgba(46,125,50,.1); }
.sisa-warn { background: var(--yellow); color: #212529; }
.sisa-exp  { background: var(--red); color: white; }

/* Ruang chip */
.ruang-chip {
    font-family: var(--mono); font-size: .75rem;
    background: var(--bg-elevated); color: var(--text-2);
    padding: .18rem .5rem; border-radius: 5px;
    border: 1px solid var(--border-md);
}

/* Empty state */
.empty-state {
    text-align: center; padding: 4rem 2rem;
    color: var(--text-3);
}
.empty-state .es-icon { font-size: 2.8rem; margin-bottom: .85rem; opacity: .5; }
.empty-state p { font-size: .88rem; }

/* Skeleton rows */
.skeleton-row td { padding: .9rem 1rem; }
.skeleton-cell {
    height: 14px; border-radius: 4px;
    background: linear-gradient(90deg, var(--bg-elevated) 25%, var(--bg-hover) 50%, var(--bg-elevated) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.4s ease infinite;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Loading spinner overlay */
.tbl-loading {
    position: absolute; inset: 0;
    background: rgba(248,249,250,.8);
    display: none; align-items: center; justify-content: center;
    border-radius: var(--radius-xl); backdrop-filter: blur(3px); z-index: 10;
}
.tbl-loading.show { display: flex; }
.spin {
    width: 34px; height: 34px;
    border: 3px solid var(--border-md);
    border-top-color: var(--red);
    border-radius: 50%;
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Table footer */
.tbl-footer {
    padding: .9rem 1.5rem;
    border-top: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
    font-size: .78rem; color: var(--text-3);
    flex-wrap: wrap; gap: .75rem;
}

/* ═══════════════════════════════════════════
   SIDEBAR
═══════════════════════════════════════════ */
.sidebar { display: flex; flex-direction: column; gap: 1rem; }

.s-widget {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    overflow: hidden;
}
.sw-head {
    padding: .85rem 1.2rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: .5rem;
    font-size: .72rem; font-weight: 700; color: var(--text-2);
    text-transform: uppercase; letter-spacing: .09em;
}
.sw-head svg { color: var(--red); flex-shrink: 0; }
.sw-head.warn { color: var(--yellow); }
.sw-head.warn svg { color: var(--yellow); }
.sw-body { padding: .5rem 1.2rem .85rem; }

/* Golongan rows */
.gol-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .55rem 0;
    border-bottom: 1px solid var(--border);
}
.gol-row:last-child { border-bottom: none; }
.gol-left { display: flex; align-items: center; gap: .65rem; }
.gol-name { font-family: var(--mono); font-size: .83rem; font-weight: 700; min-width: 34px; }
.gol-bar { width: 80px; height: 5px; background: var(--bg-elevated); border-radius: 3px; overflow: hidden; }
.gol-fill { height: 100%; border-radius: 3px; background: var(--red); transition: width .7s cubic-bezier(.4,0,.2,1); }
.gol-right { text-align: right; }
.gol-cnt { font-family: var(--mono); font-size: .83rem; font-weight: 700; }
.gol-pct { font-size: .68rem; color: var(--text-3); }

/* Expired list */
.exp-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .55rem 0; border-bottom: 1px solid var(--border);
}
.exp-row:last-child { border-bottom: none; }
.exp-no  { font-family: var(--mono); font-size: .76rem; color: var(--text-1); }
.exp-meta { font-size: .7rem; color: var(--text-3); margin-top: .1rem; }
.exp-date { font-family: var(--mono); font-size: .76rem; color: var(--red); font-weight: 700; }
.exp-more { text-align: center; padding: .65rem 0 .1rem; font-size: .75rem; color: var(--text-3); }

/* Jenis list */
.jenis-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .55rem 0; border-bottom: 1px solid var(--border);
}
.jenis-row:last-child { border-bottom: none; }
.jenis-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); margin-right: .55rem; flex-shrink: 0; }
.jenis-name { font-size: .84rem; }
.jenis-cnt  { font-family: var(--mono); font-size: .8rem; color: var(--text-2); font-weight: 700; }

/* No data */
.no-data { text-align: center; padding: 1.5rem; font-size: .8rem; color: var(--text-3); }

/* ═══════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════ */
@media (max-width: 1100px) {
    .content-row { grid-template-columns: 1fr; }
    .sidebar { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); }
    .summary-strip { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 680px) {
    .page-body { padding: 1rem; }
    .top-bar   { padding: 0 1rem; }
    .summary-strip { grid-template-columns: 1fr 1fr; }
    .filter-bar { gap: .75rem; }
    .fg { min-width: 100%; }
}
</style>
@endpush

@section('content')
<div class="penyimpanan-wrap">

{{-- ══════════ TOP BAR ══════════ --}}
<header class="top-bar">
    <div class="top-bar-left">
        <div class="bar-icon">🩸</div>
        <div>
            <div class="bar-title">Informasi Stok Darah</div>
            <div class="bar-sub">Penyimpanan Produk Lisis — {{ $petugasNama ?? 'Petugas' }}</div>
        </div>
    </div>
    <div class="top-bar-right">
        <span class="pill-live"><i></i>Live</span>
        <span id="topClock"></span>
    </div>
</header>

<div class="page-body">

    {{-- ══════════ SUMMARY STRIP ══════════ --}}
        <div class="summary-strip" style="margin-bottom:.75rem">
        <div class="s-card c-grn">
            <div class="s-icon">✅</div>
            <div><div class="s-val" id="sumAktif">—</div><div class="s-lbl">Tersedia</div></div>
        </div>
        <div class="s-card c-yel">
            <div class="s-icon">⚠️</div>
            <div><div class="s-val" id="sumMendekati">—</div><div class="s-lbl">Mendekati Expired (≤3hr)</div></div>
        </div>
        <div class="s-card c-red">
            <div class="s-icon">🚫</div>
            <div><div class="s-val" id="sumExpired">—</div><div class="s-lbl">Sudah Expired</div></div>
        </div>
        <div class="s-card c-blu">
            <div class="s-icon">📦</div>
            <div><div class="s-val" id="sumTotal">—</div><div class="s-lbl">Total Kantong</div></div>
        </div>
    </div>
    {{-- Baris 2: Aliran --}}
    <div class="summary-strip">
        <div class="s-card c-grn">
            <div class="s-icon">📥</div>
            <div><div class="s-val" id="sumMasuk">—</div><div class="s-lbl">Total Masuk</div></div>
        </div>
        <div class="s-card c-red">
            <div class="s-icon">📤</div>
            <div><div class="s-val" id="sumKeluar">—</div><div class="s-lbl">Total Keluar</div></div>
        </div>
        <div class="s-card c-blu">
            <div class="s-icon">🔄</div>
            <div><div class="s-val" id="sumKembali">—</div><div class="s-lbl">Total Kembali</div></div>
        </div>
        <div class="s-card c-yel">
            <div class="s-icon">📊</div>
            <div><div class="s-val" id="sumSaldo">—</div><div class="s-lbl">Total Saldo Aktif</div></div>
        </div>
    </div>

    {{-- ══════════ FILTER BAR ══════════ --}}
    <div class="filter-bar" id="filterBar">
        <div class="fg">
            <label>Jenis Darah</label>
            <select id="fJenis">
                <option value="">— Semua —</option>
                @foreach($jenisOptions ?? [] as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg">
            <label>Golongan Darah</label>
            <select id="fGolongan">
                <option value="">— Semua —</option>
                @foreach($golonganOptions ?? [] as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg">
            <label>Rhesus</label>
            <select id="fRhesus">
                <option value="">— Semua —</option>
                @foreach($rhesusOptions ?? [] as $opt)
                <option value="{{ $opt }}">{{ $opt }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg">
            <label>Status</label>
            <select id="fStatus">
                <option value="">— Semua —</option>
                <option value="tersedia">Tersedia</option>
                <option value="dipakai">Terpakai</option>
                <option value="kadaluarsa">Kadaluarsa</option>
                <option value="dibuang">Dibuang</option>
            </select>
        </div>
        <div class="fg">
            <label>No Penerimaan</label>
            <input type="text" id="fNoPenerimaan" placeholder="Cari no penerimaan...">
        </div>
        <div style="display:flex;gap:.5rem;align-items:flex-end;flex-shrink:0">
            <button class="btn btn-primary" onclick="applyFilter()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                Tampilkan
            </button>
            <button class="btn btn-ghost" onclick="resetFilter()">Reset</button>
        </div>
    </div>

    {{-- ══════════ MAIN ROW ══════════ --}}
    <div class="content-row">

        {{-- TABLE CARD --}}
        <div class="table-card">
            <div class="tbl-loading" id="tblLoading"><div class="spin"></div></div>

            <div class="table-card-head">
                <div>
                    <h2>Detail Stok per Nomor Kantong</h2>
                    <div class="tc-meta" id="tcMeta">memuat data…</div>
                </div>
                <div class="tab-row" id="tabRow">
                    <button class="tab active" data-tab="semua"    onclick="switchTab(this)">Semua</button>
                    <button class="tab"        data-tab="tersedia" onclick="switchTab(this)">Tersedia</button>
                    <button class="tab"        data-tab="mendekati" onclick="switchTab(this)">Mendekati</button>
                    <button class="tab"        data-tab="expired"  onclick="switchTab(this)">Expired</button>
                </div>
                <div class="search-wrap">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" id="searchInput" placeholder="Cari kantong / stok…" oninput="clientSearch(this.value)">
                </div>
            </div>

            <div class="tbl-scroll">
                <table>
                    <thead>
                        <tr>
                            <th style="width:42px">#</th>
                            <th>No Stok</th>
                            <th>Jenis Darah</th>
                            <th>Gol / Rh</th>
                            <th>Tgl Aftap</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Expired</th>
                            <th style="text-align:center">Sisa</th>
                            <th>Ruang</th>
                            <th>Volume</th>
                            <th>Skrining</th>
                            <th>Status</th>
                            <th>Aliran (▲▼↺=)</th>
                            <th width="80">Detail</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        {{-- skeleton --}}
                        @for($i=0;$i<6;$i++)
                        <tr class="skeleton-row">
                            @for($j=0;$j<12;$j++)
                            <td><div class="skeleton-cell" style="width:{{ [30,80,60,55,70,70,70,30,50,45,55,70][$j] }}px"></div></td>
                            @endfor
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="tbl-footer">
                <span id="footerMeta">—</span>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <aside class="sidebar">

            {{-- Golongan Darah --}}
            <div class="s-widget">
                <div class="sw-head">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16A8 8 0 0010 2zm0 14a6 6 0 110-12 6 6 0 010 12z" clip-rule="evenodd"/></svg>
                    Stok per Golongan Darah
                </div>
                <div class="sw-body" id="widgetGol">
                    <div class="no-data">Memuat…</div>
                </div>
            </div>

            {{-- Stok Expired --}}
            <div class="s-widget">
                <div class="sw-head warn">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Stok Sudah Expired
                </div>
                <div class="sw-body" id="widgetExpired">
                    <div class="no-data">Memuat…</div>
                </div>
            </div>

            {{-- Jenis Darah --}}
            <div class="s-widget">
                <div class="sw-head">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 000 2H6a2 2 0 00-2 2v6a2 2 0 002 2h8a2 2 0 002-2V8a2 2 0 00-2-2h-.01a1 1 0 000-2H14a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg>
                    Stok per Jenis Darah
                </div>
                <div class="sw-body" id="widgetJenis">
                    <div class="no-data">Memuat…</div>
                </div>
            </div>

        </aside>
    </div>


{{-- ══════════ MODAL ALIRAN DARAH ══════════ --}}
<div id="modalAliran" style="
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,.45); backdrop-filter:blur(4px);
    align-items:center; justify-content:center;">
    <div style="
        background:var(--bg-card); border-radius:var(--radius-xl);
        width:min(700px,95vw); max-height:85vh;
        display:flex; flex-direction:column;
        box-shadow:0 20px 60px rgba(0,0,0,.25);">

        <!-- Modal Header -->
        <div style="
            padding:1.1rem 1.5rem;
            border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-weight:700; font-size:.95rem" id="aModalTitle">Aliran Darah</div>
                <div style="font-size:.73rem; color:var(--text-3); margin-top:.15rem" id="aModalSub">—</div>
            </div>
            <button onclick="closeAliran()" style="
                background:var(--bg-elevated); border:none; cursor:pointer;
                border-radius:8px; width:30px; height:30px;
                font-size:1rem; color:var(--text-2);">✕</button>
        </div>

        <!-- Saldo Bar -->
        <div id="aModalSaldo" style="
            display:grid; grid-template-columns:repeat(4,1fr);
            gap:.75rem; padding:1rem 1.5rem;
            border-bottom:1px solid var(--border);"></div>

        <!-- Timeline -->
        <div style="overflow-y:auto; padding:1rem 1.5rem; flex:1;">
            <div style="font-size:.7rem; font-weight:700; color:var(--text-3);
                text-transform:uppercase; letter-spacing:.09em; margin-bottom:.75rem;">
                Riwayat Transaksi
            </div>
            <div id="aModalTimeline">
                <div style="text-align:center;padding:2rem;color:var(--text-3)">Memuat…</div>
            </div>
        </div>
    </div>
</div>

</div>
</div>
@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════
   CONFIG
═══════════════════════════════════════════ */
const API_DATA    = "{{ route('penyimpanan.stok_darah.data') }}";
const API_SUMMARY = "{{ route('penyimpanan.stok_darah.summary') }}";
const API_ALIRAN  = (noStok) => `/penyimpanan/stok_darah/aliran/${encodeURIComponent(noStok)}`;

let allRows  = [];
let activeTab = 'semua';
let searchQ   = '';

/* ═══════════════════════════════════════════
   CLOCK
═══════════════════════════════════════════ */
(function clock() {
    const el = document.getElementById('topClock');
    function tick() {
        const n = new Date();
        el.textContent =
            n.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' })
            + '  ' + n.toLocaleTimeString('id-ID');
    }
    tick(); setInterval(tick, 1000);
})();

/* ═══════════════════════════════════════════
   FETCH SUMMARY (kartu atas — aliran total)
═══════════════════════════════════════════ */
async function fetchSummary() {
    try {
        const res  = await fetch(API_SUMMARY);
        const json = await res.json();

        document.getElementById('sumAktif').textContent     = json.tersedia    ?? 0;
        document.getElementById('sumMendekati').textContent = json.mendekati   ?? 0;
        document.getElementById('sumExpired').textContent   = json.expired     ?? 0;
        document.getElementById('sumTotal').textContent     = json.kantong     ?? 0;

        // kartu aliran
        document.getElementById('sumMasuk').textContent    = json.total_masuk   ?? 0;
        document.getElementById('sumKeluar').textContent   = json.total_keluar  ?? 0;
        document.getElementById('sumKembali').textContent  = json.total_kembali ?? 0;
        document.getElementById('sumSaldo').textContent    = json.total_saldo   ?? 0;
    } catch(e) { console.error('fetchSummary error', e); }
}

/* ═══════════════════════════════════════════
   FETCH DATA TABLE
═══════════════════════════════════════════ */
async function fetchData(params = {}) {
    showLoading(true);
    try {
        const qs   = new URLSearchParams(params).toString();
        const res  = await fetch(`${API_DATA}${qs ? '?' + qs : ''}`);
        const json = await res.json();

        allRows = json.data || [];
        renderTable();
        renderSidebar();
    } catch(e) {
        console.error(e);
        document.getElementById('tableBody').innerHTML =
            `<tr><td colspan="14" class="empty-state"><div class="es-icon">⚠️</div><p>Gagal memuat data.</p></td></tr>`;
    } finally {
        showLoading(false);
    }
}

/* ═══════════════════════════════════════════
   FILTER
═══════════════════════════════════════════ */
function applyFilter() {
    const params = {};
    const j = document.getElementById('fJenis').value;
    const g = document.getElementById('fGolongan').value;
    const r = document.getElementById('fRhesus').value;
    const n = document.getElementById('fNoPenerimaan').value.trim();
    const s = document.getElementById('fStatus')?.value;
    if (j) params.jenis_darah    = j;
    if (g) params.golongan_darah = g;
    if (r) params.rhesus         = r;
    if (n) params.no_penerimaan  = n;
    if (s) params.status_stok    = s;
    fetchData(params);
}

function resetFilter() {
    ['fJenis','fGolongan','fRhesus','fStatus'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('fNoPenerimaan').value = '';
    document.getElementById('searchInput').value   = '';
    searchQ   = '';
    activeTab = 'semua';
    document.querySelectorAll('.tab').forEach(t =>
        t.classList.toggle('active', t.dataset.tab === 'semua'));
    fetchData();
}

function switchTab(btn) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    activeTab = btn.dataset.tab;
    renderTable();
}

function clientSearch(q) {
    searchQ = q.toLowerCase();
    renderTable();
}

/* ═══════════════════════════════════════════
   DATE HELPERS
═══════════════════════════════════════════ */
function sisaHari(sisa) {
    // sisa sudah dihitung server sebagai integer
    return sisa;
}

/* ═══════════════════════════════════════════
   RENDER TABLE
═══════════════════════════════════════════ */
function renderTable() {
    let rows = allRows;

    // Tab filter — gunakan field 'status' dari server
    if (activeTab !== 'semua') {
        rows = rows.filter(r => r.status === activeTab);
    }

    if (searchQ) {
        rows = rows.filter(r =>
            Object.values(r).some(v => String(v).toLowerCase().includes(searchQ))
        );
    }

    document.getElementById('tcMeta').textContent   = `${rows.length} data ditampilkan`;
    document.getElementById('footerMeta').textContent = `${rows.length} dari ${allRows.length} total data`;

    if (!rows.length) {
        document.getElementById('tableBody').innerHTML =
            `<tr><td colspan="14"><div class="empty-state">
                <div class="es-icon">🩸</div>
                <p>Tidak ada data yang cocok dengan filter.</p>
            </div></td></tr>`;
        return;
    }

    const html = rows.map((r, i) => {
        const sisa   = r.sisa_hari;
        const status = r.status;
        const rowCls = status === 'expired' ? 'r-expired' : status === 'mendekati' ? 'r-warning' : '';

        // Sisa hari chip
        let sisaHtml = '<span style="color:var(--text-3)">—</span>';
        if (sisa !== null && sisa !== undefined) {
            let cls   = 'sisa-ok';
            let txt   = `${sisa}h`;
            let title = `${sisa} hari menuju expired`;
            if (sisa < 0) {
                cls   = 'sisa-exp'; txt = `${Math.abs(sisa)}†`;
                title = 'Sudah melewati tanggal expired';
            } else if (sisa <= 3) {
                cls   = 'sisa-warn';
                title = `⚠️ Expired dalam ${sisa} hari!`;
            }
            sisaHtml = `<span class="sisa-chip ${cls}" title="${title}">${txt}</span>`;
        }

        // Golongan + rhesus
        const gol    = r.golongan_darah || '';
        const rh     = r.rhesus || '';
        const rhCls  = (rh === '+' || rh.toLowerCase().includes('pos')) ? 'pos' : 'neg';
        const rhTxt  = (rh === '+' || rh.toLowerCase().includes('pos')) ? '+' : '−';
        const golBadge = gol
            ? `<span class="blood-badge ${gol}">${gol}</span><span class="rhesus ${rhCls}">${rhTxt}</span>`
            : '—';

        // Status pill
        const pillMap = {
            tersedia  : 'sp-tersedia',
            expired   : 'sp-expired',
            mendekati : 'sp-mendekati',
            dipakai   : 'sp-dipakai',
            dibuang   : 'sp-expired',
            kadaluarsa: 'sp-expired',
        };
        const pillLbl = {
            tersedia  : 'Tersedia',
            expired   : 'Expired',
            mendekati : '⚠️ Mendekati',
            dipakai   : 'Terpakai',
            dibuang   : 'Dibuang',
            kadaluarsa: 'Kadaluarsa',
        };
        const pillHtml = `<span class="status-pill ${pillMap[status]||'sp-tersedia'}">${pillLbl[status]||status}</span>`;

        // Volume
        const vol = r.ml ? `${r.ml} ml` : r.gr ? `${r.gr} gr` : '—';

        // Aliran mini
        const aliranHtml = `
            <div style="display:flex;gap:.25rem;flex-wrap:wrap;min-width:120px">
                <span title="Masuk"   style="font-family:var(--mono);font-size:.7rem;padding:.1rem .35rem;border-radius:4px;background:rgba(46,125,50,.1);color:var(--green)">▲${r.jumlah_masuk}</span>
                <span title="Keluar"  style="font-family:var(--mono);font-size:.7rem;padding:.1rem .35rem;border-radius:4px;background:rgba(211,47,47,.1);color:var(--red)">▼${r.jumlah_keluar}</span>
                <span title="Kembali" style="font-family:var(--mono);font-size:.7rem;padding:.1rem .35rem;border-radius:4px;background:rgba(25,118,210,.1);color:var(--blue)">↺${r.jumlah_kembali}</span>
                <span title="Saldo"   style="font-family:var(--mono);font-size:.7rem;padding:.1rem .35rem;border-radius:4px;background:rgba(0,0,0,.06);color:var(--text-1);font-weight:700">=${r.saldo}</span>
            </div>`;

        return `<tr class="${rowCls}">
            <td class="td-mono">${i + 1}</td>
            <td>
                <div style="font-family:var(--mono);font-size:.78rem">${r.no_stok || '—'}</div>
                <div style="font-size:.68rem;color:var(--text-3);margin-top:.1rem">${r.no_fpd || ''}</div>
            </td>
            <td style="font-weight:600">${r.jenis_darah || '—'}</td>
            <td>${golBadge}</td>
            <td class="td-mono">${r.tgl_aftap    || '—'}</td>
            <td class="td-mono">${r.tgl_produksi || '—'}</td>
            <td class="td-mono">${r.tgl_expired  || '—'}</td>
            <td style="text-align:center">${sisaHtml}</td>
            <td>${r.ruang ? `<span class="ruang-chip">${r.ruang}</span>` : '—'}</td>
            <td class="td-mono">${vol}</td>
            <td>${r.skrining
                ? `<span style="font-size:.74rem;padding:.15rem .45rem;border-radius:4px;background:rgba(25,118,210,.1);color:var(--blue);font-family:var(--mono)">${r.skrining}</span>`
                : '—'}</td>
            <td>${aliranHtml}</td>
            <td>${pillHtml}</td>
            <td>
                <button class="btn btn-sm btn-ghost"
                    onclick="openAliran('${r.no_stok}')"
                    title="Lihat riwayat aliran stok ini">
                    📋 Detail
                </button>
            </td>
        </tr>`;
    }).join('');

    document.getElementById('tableBody').innerHTML = html;
}

/* ═══════════════════════════════════════════
   RENDER SIDEBAR
═══════════════════════════════════════════ */
function renderSidebar() {
    // Golongan
    const golMap = {};
    allRows.forEach(r => {
        if (!r.golongan_darah) return;
        golMap[r.golongan_darah] = (golMap[r.golongan_darah] || 0) + 1;
    });
    const golTotal = Object.values(golMap).reduce((a,b) => a+b, 0) || 1;
    const golHtml  = ['A','B','AB','O'].filter(g => golMap[g]).map(g => {
        const cnt = golMap[g], pct = Math.round(cnt/golTotal*100);
        return `<div class="gol-row">
            <div class="gol-left">
                <span class="gol-name">${g}</span>
                <div class="gol-bar"><div class="gol-fill" style="width:${pct}%"></div></div>
            </div>
            <div class="gol-right">
                <div class="gol-cnt">${cnt}</div>
                <div class="gol-pct">${pct}%</div>
            </div>
        </div>`;
    }).join('');
    document.getElementById('widgetGol').innerHTML = golHtml || '<div class="no-data">Tidak ada data</div>';

    // Expired
    const expRows = allRows.filter(r => r.status === 'expired')
        .sort((a,b) => (a.tgl_expired||'').localeCompare(b.tgl_expired||''));
    let expHtml = expRows.slice(0,5).map(r =>
        `<div class="exp-row">
            <div>
                <div class="exp-no">${r.no_stok||'—'}</div>
                <div class="exp-meta">${r.jenis_darah||''} · ${r.golongan_darah||''}${r.rhesus||''}</div>
            </div>
            <div class="exp-date">${r.tgl_expired||''}</div>
        </div>`
    ).join('');
    if (expRows.length > 5) expHtml += `<div class="exp-more">+${expRows.length - 5} lainnya</div>`;
    document.getElementById('widgetExpired').innerHTML =
        expHtml || '<div class="no-data">Tidak ada stok expired ✓</div>';

    // Jenis
    const jenisMap = {};
    allRows.forEach(r => {
        if (!r.jenis_darah) return;
        jenisMap[r.jenis_darah] = (jenisMap[r.jenis_darah]||0) + 1;
    });
    document.getElementById('widgetJenis').innerHTML =
        Object.entries(jenisMap).sort((a,b) => b[1]-a[1]).map(([name,cnt]) =>
            `<div class="jenis-row">
                <div style="display:flex;align-items:center">
                    <div class="jenis-dot"></div>
                    <span class="jenis-name">${name}</span>
                </div>
                <span class="jenis-cnt">${cnt} kantong</span>
            </div>`
        ).join('') || '<div class="no-data">Tidak ada data</div>';
}

/* ═══════════════════════════════════════════
   MODAL ALIRAN
═══════════════════════════════════════════ */
async function openAliran(noStok) {
    document.getElementById('modalAliran').style.display = 'flex';
    document.getElementById('aModalTitle').textContent   = `Aliran Stok: ${noStok}`;
    document.getElementById('aModalSub').textContent     = 'Memuat data…';
    document.getElementById('aModalSaldo').innerHTML     = '';
    document.getElementById('aModalTimeline').innerHTML  =
        '<div style="text-align:center;padding:2rem;color:var(--text-3)">⏳ Memuat…</div>';

    try {
        const res  = await fetch(API_ALIRAN(noStok));
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        const s = json.stok;
        document.getElementById('aModalSub').textContent =
            `${s.jenis_darah} · Gol ${s.golongan_darah} ${s.rhesus} · Status: ${s.status_stok}`;

        // Saldo bar
        document.getElementById('aModalSaldo').innerHTML = `
            <div style="text-align:center;padding:.6rem;background:rgba(46,125,50,.08);border-radius:var(--radius)">
                <div style="font-family:var(--mono);font-size:1.3rem;font-weight:700;color:var(--green)">${s.jumlah_masuk}</div>
                <div style="font-size:.68rem;color:var(--text-3);margin-top:.2rem">▲ Masuk</div>
            </div>
            <div style="text-align:center;padding:.6rem;background:rgba(211,47,47,.08);border-radius:var(--radius)">
                <div style="font-family:var(--mono);font-size:1.3rem;font-weight:700;color:var(--red)">${s.jumlah_keluar}</div>
                <div style="font-size:.68rem;color:var(--text-3);margin-top:.2rem">▼ Keluar</div>
            </div>
            <div style="text-align:center;padding:.6rem;background:rgba(25,118,210,.08);border-radius:var(--radius)">
                <div style="font-family:var(--mono);font-size:1.3rem;font-weight:700;color:var(--blue)">${s.jumlah_kembali}</div>
                <div style="font-size:.68rem;color:var(--text-3);margin-top:.2rem">↺ Kembali</div>
            </div>
            <div style="text-align:center;padding:.6rem;background:rgba(0,0,0,.05);border-radius:var(--radius)">
                <div style="font-family:var(--mono);font-size:1.3rem;font-weight:700;color:var(--text-1)">${s.saldo}</div>
                <div style="font-size:.68rem;color:var(--text-3);margin-top:.2rem">= Saldo</div>
            </div>`;

        // Timeline
        const jenisStyle = {
            masuk   : { bg:'rgba(46,125,50,.1)',  color:'var(--green)',  icon:'▲', lbl:'Masuk' },
            keluar  : { bg:'rgba(211,47,47,.1)',  color:'var(--red)',    icon:'▼', lbl:'Keluar' },
            kembali : { bg:'rgba(25,118,210,.1)', color:'var(--blue)',   icon:'↺', lbl:'Kembali' },
            hapus   : { bg:'rgba(0,0,0,.08)',     color:'var(--text-3)', icon:'✕', lbl:'Hapus' },
        };

        if (!json.transaksi.length) {
            document.getElementById('aModalTimeline').innerHTML =
                '<div style="text-align:center;padding:2rem;color:var(--text-3)">Belum ada transaksi tercatat.</div>';
            return;
        }

        document.getElementById('aModalTimeline').innerHTML =
            json.transaksi.map(t => {
                const st = jenisStyle[t.jenis] || jenisStyle.hapus;
                return `<div style="
                    display:flex;align-items:flex-start;gap:.85rem;
                    padding:.75rem 0;border-bottom:1px solid var(--border);">
                    <div style="
                        width:32px;height:32px;border-radius:8px;flex-shrink:0;
                        background:${st.bg};color:${st.color};
                        display:grid;place-items:center;font-size:1rem;font-weight:700;">
                        ${st.icon}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
                            <span style="font-weight:700;font-size:.82rem;color:${st.color}">${st.lbl}</span>
                            <span style="font-family:var(--mono);font-size:.78rem;
                                background:${st.bg};color:${st.color};
                                padding:.1rem .35rem;border-radius:4px;">
                                ×${t.jumlah}
                            </span>
                            ${t.no_referensi
                                ? `<span style="font-family:var(--mono);font-size:.72rem;color:var(--text-3)">${t.no_referensi}</span>`
                                : ''}
                        </div>
                        ${t.sumber
                            ? `<div style="font-size:.73rem;color:var(--text-3);margin-top:.2rem">${t.sumber}</div>`
                            : ''}
                        ${t.keterangan
                            ? `<div style="font-size:.76rem;color:var(--text-2);margin-top:.15rem">${t.keterangan}</div>`
                            : ''}
                    </div>
                    <div style="font-family:var(--mono);font-size:.7rem;color:var(--text-3);white-space:nowrap;flex-shrink:0">
                        ${t.tanggal}
                    </div>
                </div>`;
            }).join('');

    } catch(e) {
        document.getElementById('aModalTimeline').innerHTML =
            `<div style="text-align:center;padding:2rem;color:var(--red)">Gagal memuat: ${e.message}</div>`;
    }
}

function closeAliran() {
    document.getElementById('modalAliran').style.display = 'none';
}

// Tutup modal klik di luar
document.getElementById('modalAliran').addEventListener('click', function(e) {
    if (e.target === this) closeAliran();
});

/* ═══════════════════════════════════════════
   LOADING
═══════════════════════════════════════════ */
function showLoading(show) {
    document.getElementById('tblLoading').classList.toggle('show', show);
}

/* ═══════════════════════════════════════════
   INIT
═══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    fetchSummary();
    fetchData();
});
</script>
@endpush