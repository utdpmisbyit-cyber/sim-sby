@extends('layouts.index')

@section('title', 'Pelayanan referal — Hasil')
@section('page-title', 'Pelayanan referal — Hasil per Kantong')

@push('styles')
<style>
/* ── Core Layout ─────────────────────────────────────────────── */
.ct-card {
    background: #fff;
    border-radius: .75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
    border: 1px solid #e9ecef;
    overflow: hidden;
    margin-bottom: 1rem;
}
.ct-card-header {
    padding: .85rem 1.25rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.ct-card-header i { color: #e74c3c; font-size: 1.1rem; }
.ct-card-body { padding: 1.25rem; }

/* ── Scan Wrap ───────────────────────────────────────────────── */
.scan-wrap { display:flex; align-items:center; gap:.5rem; position:relative; }
.scan-wrap .form-control { flex:1; padding-left:2rem; }
.scan-icon { position:absolute; left:12px; color:#adb5bd; font-size:1rem; pointer-events:none; }

/* ── Buttons ─────────────────────────────────────────────────── */
.btn-utd {
    background: #e74c3c; border: none;
    padding: .5rem 1rem; border-radius: .5rem;
    color: white; font-weight: 500;
    transition: all .2s;
    display: inline-flex; align-items: center; gap: .5rem;
    cursor: pointer;
}
.btn-utd:hover { background: #c0392b; color: white; }
.btn-utd:disabled { opacity: .65; cursor: not-allowed; }
.btn-utd-outline {
    background: transparent; border: 1px solid #dee2e6;
    padding: .5rem 1rem; border-radius: .5rem;
    color: #495057; transition: all .2s;
    display: inline-flex; align-items: center; gap: .5rem;
    cursor: pointer;
}
.btn-utd-outline:hover { background: #f8f9fa; border-color: #adb5bd; }

/* ── Form ────────────────────────────────────────────────────── */
.form-label {
    font-size: .75rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .05em;
    color: #6c757d; margin-bottom: .25rem;
}
.form-control, .form-select {
    border-radius: .5rem; border: 1px solid #dee2e6;
    padding: .5rem .75rem; font-size: .875rem;
}
.form-control:focus, .form-select:focus {
    border-color: #e74c3c; outline: none;
    box-shadow: 0 0 0 3px rgba(231,76,60,.1);
}

/* ── Table ───────────────────────────────────────────────────── */
.ct-table { width:100%; border-collapse:collapse; font-size:.8125rem; }
.ct-table th {
    text-align:left; padding:.75rem 1rem;
    background:#f8f9fa; font-weight:600;
    color:#495057; border-bottom:1px solid #dee2e6;
}
.ct-table td { padding:.75rem 1rem; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.ct-table tr:hover { background:#fef9f9; }
.ct-table tr.row-pending { background:#fffde7 !important; }
.ct-table tr.row-pending:hover { background:#fff9c4 !important; }
.ct-table tr.row-expired td { background:#fff5f5; }

/* ── Badges ──────────────────────────────────────────────────── */
.badge-pill {
    display:inline-block; padding:.25rem .65rem;
    font-size:.7rem; font-weight:600;
    border-radius:2rem; text-align:center;
}
.badge-compatible   { background:#d4edda; color:#155724; }
.badge-incompatible { background:#f8d7da; color:#721c24; }
.badge-pending      { background:#fff3cd; color:#856404; }
.badge-proses       { background:#cce5ff; color:#004085; }
.badge-selesai      { background:#d4edda; color:#155724; }

/* ── Section Label ───────────────────────────────────────────── */
.section-label {
    margin:0 0 .5rem;
    font-size:.7rem; text-transform:uppercase;
    letter-spacing:.05em; color:#6c757d;
    font-weight:600; border-left:3px solid #e74c3c;
    padding-left:.65rem;
}
.section-label i { color:#e74c3c; margin-right:.25rem; }

/* ── FPUP Banner ─────────────────────────────────────────────── */
.fpup-card {
    border:1px solid #f0d6d6; border-radius:.75rem;
    background:linear-gradient(135deg,#fff8f8 0%,#fff 100%);
    padding:1rem 1.25rem;
}
.fpup-top {
    display:flex; flex-wrap:wrap; gap:1rem;
    align-items:flex-start; justify-content:space-between;
    border-bottom:1px solid #f5dada;
    padding-bottom:.75rem; margin-bottom:.75rem;
}
.fpup-no-label  { font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:#6c757d; font-weight:600; }
.fpup-no-value  { font-size:1.25rem; font-weight:700; color:#e74c3c; line-height:1.2; }
.fpup-pasien-name { font-size:1rem; font-weight:700; line-height:1.2; }
.fpup-pasien-sub  { font-size:.75rem; color:#6c757d; }
.fpup-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(160px,1fr));
    gap:.5rem 1rem;
}
.info-row { display:flex; justify-content:space-between; align-items:center; font-size:.75rem; padding:.25rem 0; }
.info-label { font-weight:600; color:#6c757d; }
.info-value { color:#212529; font-weight:500; }

/* ── Stat Cards ──────────────────────────────────────────────── */
.stat-row {
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:.75rem; margin-top:.5rem; margin-bottom:1rem;
}
.stat-card {
    border:1px solid #eef0f4; border-radius:.65rem;
    padding:.7rem 1rem;
    display:flex; align-items:center; gap:.75rem;
    background:#fff; transition:all .2s;
}
.stat-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.05); transform:translateY(-1px); }
.stat-card i { font-size:1.6rem; }
.stat-value { font-size:1.3rem; font-weight:700; line-height:1; }
.stat-label { font-size:.65rem; text-transform:uppercase; letter-spacing:.05em; color:#6c757d; font-weight:600; }
.stat-minta   i { color:#3498db; }
.stat-periksa i { color:#f39c12; }
.stat-cocok   i { color:#2ecc71; }
.stat-sisa    i { color:#e74c3c; }

/* ── Misc ────────────────────────────────────────────────────── */
.mono { font-family:'SF Mono','Monaco','Cascadia Code',monospace; font-size:.8rem; }
.border-top { border-top:1px solid #dee2e6; }
.form-switch .form-check-input { width:2.2rem; height:1.2rem; cursor:pointer; }
.form-switch .form-check-input:checked { background-color:#e74c3c; border-color:#e74c3c; }
.modal-content { border-radius:1rem; }

@media(max-width:768px){
    .stat-row { grid-template-columns:repeat(2,1fr); }
    .fpup-grid { grid-template-columns:1fr; }
    .ct-table { font-size:.7rem; }
    .ct-table th, .ct-table td { padding:.5rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
<div class="row g-3">

    {{-- ── Panel Scan No FPUP ─────────────────────────────────── --}}
    <div class="col-12">
        <div class="ct-card">
            <div class="ct-card-header">
                <i class="bi bi-upc-scan"></i> Scan / Input No FPUP
            </div>
            <div class="ct-card-body">
                <div class="row align-items-end">
                    <div class="col-md-5 col-lg-4">
                        <label class="form-label">No FPUP</label>
                        <div class="scan-wrap">
                            <i class="bi bi-upc-scan scan-icon"></i>
                            <input type="text" id="inputNoFpup" class="form-control"
                                   placeholder="Scan atau ketik No FPUP..."
                                   autofocus autocomplete="off">
                            <button class="btn-utd" id="btnScanFpup" type="button">
                                <i class="bi bi-search"></i> Cari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Section yang muncul setelah scan FPUP ─────────────── --}}
    <div class="col-12" id="formSection" style="display:none;">

        {{-- Banner Pasien --}}
        <div class="fpup-card mb-3" id="pasienBanner"></div>

        {{-- Stat Cards --}}
        <div class="stat-row" id="statRow"></div>

        {{-- Tabel Detail Kantong --}}
        <div class="ct-card">
            <div class="ct-card-header">
                <i class="bi bi-list-check"></i> Detail Kantong — FPUP Ini
                <span class="ms-auto text-muted" id="lblPendingBadge" style="font-size:.7rem;display:none;">
                    <span class="badge-pill badge-pending">
                        <i class="bi bi-hourglass-split me-1"></i>
                        <span id="lblPendingCount">0</span> pending belum disimpan
                    </span>
                </span>
            </div>
            <div class="ct-card-body p-0">
                <div class="table-responsive">
                    <table class="ct-table">
                        <thead>
                            <tr>
                                <th width="40">#</th>
                                <th>No Stok</th>
                                <th>Jns</th>
                                <th>Gol/Rh</th>
                                <th>Metode</th>
                                <th>Tgl Periksa</th>
                                <th>Batas</th>
                                <th>Hasil</th>
                                <th width="50">NAT</th>
                                <th>Skrining</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th width="90">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="currentTbody">
                            <tr>
                                <td colspan="13" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size:1.5rem;"></i><br>
                                    Belum ada data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Form Tambah/Edit --}}
        <div class="ct-card mt-3">
            <div class="ct-card-header">
                <i class="bi bi-droplet"></i>
                <span id="labelFormTitle">Form Hasil Referal</span>
            </div>
            <div class="ct-card-body">
                @include('app.referal.pelayanan_crosstest.form')
            </div>
        </div>
    </div>

    {{-- ── Tabel Global + Filter ───────────────────────────────── --}}
    <div class="col-12">
        <div class="ct-card">
            <div class="ct-card-header">
                <i class="bi bi-table"></i> Daftar Hasil referal
                <span class="ms-auto text-muted" style="font-size:.7rem;">
                    Total: {{ $pelayananList->total() ?? 0 }}
                </span>
            </div>
            <div class="ct-card-body">

                <form method="GET" action="{{ route('referal.pelayanan_crosstest_referal.index') }}"
                      class="row g-2 mb-4">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                               placeholder="🔍 No FPUP / No Stok / Pemeriksa"
                               value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(['pending','proses','selesai','batal'] as $s)
                                <option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="hasil" class="form-select">
                            <option value="">Semua Hasil</option>
                            @foreach(['Cocok','Tidak Cocok','Doubtful'] as $h)
                                <option value="{{ $h }}" @selected(($filters['hasil'] ?? '') === $h)>
                                    {{ $h }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tgl_from" class="form-control"
                               value="{{ $filters['tgl_from'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="tgl_to" class="form-control"
                               value="{{ $filters['tgl_to'] ?? '' }}">
                    </div>
                    <div class="col-md-1">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-utd w-100">
                                <i class="bi bi-search"></i>
                            </button>
                            <a href="{{ route('referal.pelayanan_crosstest_referal.index') }}"
                               class="btn-utd-outline" title="Reset">
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="ct-table">
                        <thead>
                            <tr>
                                <th>No FPUP</th>
                                <th>No Stok</th>
                                <th>Jns Darah</th>
                                <th>Gol/Rh</th>
                                <th>Metode</th>
                                <th>Hasil</th>
                                <th>NAT</th>
                                <th>Skrining</th>
                                <th>Pemeriksa</th>
                                <th>Tgl Periksa</th>
                                <th>Batas</th>
                                <th>Status</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelayananList ?? [] as $p)
                            <tr class="{{ ($p->batas && $p->batas->isPast()) ? 'row-expired' : '' }}">
                                <td class="mono fw-semibold">{{ $p->no_fpup ?? '-' }}</td>
                                <td class="mono">{{ $p->no_stock ?? '-' }}</td>
                                <td>{{ $p->jns_darah ?? '-' }}</td>
                                <td class="mono">{{ ($p->gol ?? '') . ($p->rhesus ?? '') }}</td>
                                <td><span class="mono">{{ $p->metode ?? '-' }}</span></td>
                                <td>
                                    @if($p->hasil)
                                        <span class="badge-pill {{ $p->hasil === 'Cocok' ? 'badge-compatible' : ($p->hasil === 'Tidak Cocok' ? 'badge-incompatible' : 'badge-pending') }}">
                                            {{ $p->hasil }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->nat)
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    @else
                                        <i class="bi bi-dash-circle text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($p->skrining && $p->skrining !== '-')
                                        <span class="badge-pill {{ $p->skrining === 'NEG' ? 'badge-compatible' : 'badge-incompatible' }}">
                                            {{ $p->skrining }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $p->pemeriksa ?? '-' }}</td>
                                <td class="mono" style="white-space:nowrap;">
                                    {{ $p->tgl_periksa ? $p->tgl_periksa->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="mono" style="white-space:nowrap;">
                                    {{ $p->batas ? $p->batas->format('d/m/Y H:i') : '-' }}
                                    @if($p->batas && $p->batas->isPast())
                                        <br><span class="badge-pill badge-incompatible" style="font-size:.6rem;">Kadaluarsa</span>
                                    @elseif($p->batas)
                                        <br><span class="badge-pill badge-compatible" style="font-size:.6rem;">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-pill
                                        @if($p->status === 'selesai') badge-compatible
                                        @elseif($p->status === 'batal') badge-incompatible
                                        @elseif($p->status === 'proses') badge-proses
                                        @else badge-pending
                                        @endif">
                                        {{ ucfirst($p->status ?? 'pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn-utd-outline py-1 px-2"
                                                onclick="editPelayanan({{ $p->id }})" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-utd-outline py-1 px-2"
                                                style="border-color:#e74c3c;color:#e74c3c"
                                                onclick="deletePelayanan({{ $p->id }},'{{ addslashes($p->no_stock ?? $p->no_fpup) }}')"
                                                title="Hapus">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                    <p class="mt-2 mb-0">Belum ada data hasil referal</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($pelayananList) && $pelayananList->hasPages())
                    <div class="mt-4 d-flex justify-content-end">
                        {{ $pelayananList->appends($filters ?? [])->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:2.5rem;"></i>
                <h5 class="mt-3 mb-2">Hapus Data?</h5>
                <p class="text-muted mb-0" id="hapusLabel"></p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button class="btn-utd-outline px-4" data-bs-dismiss="modal">Batal</button>
                <button class="btn-utd px-4" id="btnHapusConfirm">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════════════
   ROUTES
═══════════════════════════════════════════════════════════════════ */
const ROUTES = {
    scanFpup    : "{{ route('referal.pelayanan_crosstest_referal.scan_fpup') }}",
    scanStock   : "{{ route('referal.pelayanan_crosstest_referal.scan_stock') }}",
    scanPetugas : "{{ route('referal.pelayanan_crosstest_referal.scan_petugas') }}",
    store       : "{{ route('referal.pelayanan_crosstest_referal.store') }}",
    base        : "{{ url('/referal/pelayanan_crosstest_referal') }}",
};

/* ═══════════════════════════════════════════════════════════════════
   STATE
   pendingRows = kantong yang sudah di-scan TAPI belum disimpan ke DB
   activeFpup  = respons terakhir dari scanFpup (berisi existing + summary)
   editId      = ID record yang sedang diedit (null = mode tambah)
═══════════════════════════════════════════════════════════════════ */
let pendingRows    = [];
let activeFpup     = null;
let editId         = null;
let deleteTargetId = null;

/* ═══════════════════════════════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════════════════════════════ */
function $(id) { return document.getElementById(id); }
function fv(id) { return $(id)?.value ?? ''; }
function sv(id, val) { const el = $(id); if (el) el.value = val ?? ''; }

function toast(msg, type = 'success') {
    const COLORS = { success:'#27ae60', danger:'#e74c3c', warning:'#f39c12', info:'#2980b9' };
    const el = document.createElement('div');
    el.textContent = msg;
    Object.assign(el.style, {
        position:'fixed', bottom:'1.5rem', right:'1.5rem', zIndex:'9999',
        background: COLORS[type] || COLORS.success,
        color:'#fff', padding:'.75rem 1.25rem', borderRadius:'.5rem',
        boxShadow:'0 4px 14px rgba(0,0,0,.15)', fontSize:'.875rem',
        maxWidth:'360px', lineHeight:'1.4',
        opacity:'1', transition:'opacity .35s',
    });
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = 0; setTimeout(() => el.remove(), 400); }, 3500);
}

async function apiFetch(url, opts = {}) {
    const res = await fetch(url, {
        ...opts,
        headers: {
            'Content-Type' : 'application/json',
            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'Accept'       : 'application/json',
            ...(opts.headers ?? {}),
        },
    });

    // Coba parse JSON; tampilkan status jika gagal
    if (!res.ok && res.status >= 500) {
        const text = await res.text();
        console.error('Server error', res.status, text.substring(0, 500));
        throw new Error(`Server error ${res.status}`);
    }
    return res.json();
}

/* ═══════════════════════════════════════════════════════════════════
   DATE / KADALUARSA HELPERS
   Form pakai <input type="datetime-local"> → format "YYYY-MM-DDTHH:mm"
   Server (Laravel datetime) biasa kirim "YYYY-MM-DD HH:mm:ss"
═══════════════════════════════════════════════════════════════════ */
function pad(n) { return n.toString().padStart(2, '0'); }

function nowDatetimeLocal() {
    const d = new Date();
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

// Konversi string tanggal dari server → format input datetime-local
function toDatetimeLocal(value) {
    if (!value) return '';
    const d = new Date(value.replace(' ', 'T'));
    if (isNaN(d.getTime())) return '';
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

// Format tampilan tabel: dd/mm/yyyy HH:mm
function formatDateTime(value) {
    if (!value) return '—';
    const d = new Date(value.replace(' ', 'T'));
    if (isNaN(d.getTime())) return '—';
    return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

function isExpired(value) {
    if (!value) return false;
    const d = new Date(value.replace(' ', 'T'));
    if (isNaN(d.getTime())) return false;
    return d.getTime() < Date.now();
}

// Render kolom "Batas" di tabel (tanggal + badge Aktif/Kadaluarsa)
function batasCell(value) {
    if (!value) return '<span class="text-muted">—</span>';
    const label  = formatDateTime(value);
    const expired = isExpired(value);
    const badge = expired
        ? '<span class="badge-pill badge-incompatible" style="font-size:.6rem;">Kadaluarsa</span>'
        : '<span class="badge-pill badge-compatible" style="font-size:.6rem;">Aktif</span>';
    return `<span class="mono">${label}</span><br>${badge}`;
}

/* ═══════════════════════════════════════════════════════════════════
   PARSING JNS DARAH / GOL DARAH dari hasil scan No Stok
   Backend (service.scanStock) idealnya sudah balikin field `gol` &
   `rhesus` terpisah dan bersih. Tapi kalau yang balik cuma string
   gabungan (mis. dari kolom gol_rh_kantong: "B+", "AB Negatif", "O POS"),
   parsing fallback ini yang pecah jadi gol darah murni (A/B/AB/O)
   dan rhesus (+/-) sebelum diisi ke #inpGol & #inpRhesus.
═══════════════════════════════════════════════════════════════════ */
function parseGolRh(raw) {
    if (!raw) return { gol: '', rhesus: '' };

    const m = raw.trim().match(/^(AB|A|B|O)\s*(\+|-|positif|negatif|pos|neg)?$/i);
    if (!m) {
        // Tidak match pola standar → kembalikan apa adanya (uppercase) tanpa rhesus
        return { gol: raw.trim().toUpperCase(), rhesus: '' };
    }

    const gol   = m[1].toUpperCase();
    const rhRaw = (m[2] || '').toLowerCase();
    const rhesus = ['+', 'positif', 'pos'].includes(rhRaw) ? '+'
                 : ['-', 'negatif', 'neg'].includes(rhRaw) ? '-'
                 : '';

    return { gol, rhesus };
}

// Dipakai di tabel ("Detail Kantong — FPUP Ini") agar Jns Darah & Gol/Rh
// selalu tampil benar baik untuk baris existing (dari DB) maupun pending
// (belum disimpan) — sama-sama lewat fallback parsing gol_rh_kantong.
function displayJnsDarah(item) {
    const v = (item?.jns_darah ?? '').toString().trim();
    return v ? v.toUpperCase() : '-';
}

function displayGolRh(item) {
    let gol = (item?.gol    ?? '').toString().trim().toUpperCase();
    let rh  = (item?.rhesus ?? '').toString().trim();

    if ((!gol || !rh) && item?.gol_rh_kantong) {
        const parsed = parseGolRh(item.gol_rh_kantong);
        gol = gol || parsed.gol;
        rh  = rh  || parsed.rhesus;
    }

    return (gol || rh) ? `${gol}${rh}` : '-';
}

// Update badge status kantong di form sesuai isian inpBatas
function checkKadaluarsa() {
    const val   = fv('inpBatas');
    const badge = $('lblKadaluarsaBadge');
    if (!badge) return;

    if (!val) {
        badge.className   = 'badge-pill';
        badge.innerHTML   = '<i class="bi bi-dash-circle"></i> Belum diisi';
        return;
    }
    if (isExpired(val)) {
        badge.className = 'badge-pill badge-incompatible';
        badge.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Kadaluarsa';
    } else {
        badge.className = 'badge-pill badge-compatible';
        badge.innerHTML = '<i class="bi bi-check-circle"></i> Aktif';
    }
}

/* ═══════════════════════════════════════════════════════════════════
   SCAN FPUP
═══════════════════════════════════════════════════════════════════ */
async function scanFpup(noFpup) {
    noFpup = (noFpup ?? '').trim();
    if (!noFpup) { toast('Masukkan No FPUP', 'warning'); return; }

    const btn = $('btnScanFpup');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

    try {
        const res = await apiFetch(ROUTES.scanFpup, {
            method : 'POST',
            body   : JSON.stringify({ no_fpup: noFpup }),
        });

        if (!res.success) {
            toast(res.message || 'FPUP tidak ditemukan', 'danger');
            return;
        }

        // Reset state
        activeFpup  = res;
        pendingRows = [];
        editId      = null;

        // Isi hidden fields
        sv('hidNoFpup',           res.fpup.no_fpup);
        sv('hidCrossTestId',      res.cross_test_id);
        sv('hidPermintaanFpupId', res.permintaan_fpup_referal_id);

        // Render
        $('pasienBanner').innerHTML = renderBannerHTML(res.fpup);
        renderAll();

        $('formSection').style.display = '';
        resetForm();

    } catch (err) {
        toast('Gagal memuat data: ' + err.message, 'danger');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i> Cari';
    }
}

/* ═══════════════════════════════════════════════════════════════════
   SCAN NO STOK
   → Auto-fill jenis darah, gol, rhesus
   → Cek kecocokan dengan gol_rh_os pasien
   → Jika cocok → auto-set Hasil referal = "Cocok"
   → Otomatis masuk ke tabel pending
═══════════════════════════════════════════════════════════════════ */
async function scanStock(noStock) {
    noStock = (noStock ?? '').trim();
    if (!noStock) { toast('Masukkan No Stok', 'warning'); return; }

    if (!fv('hidCrossTestId')) {
        toast('Scan No FPUP terlebih dahulu', 'warning');
        return;
    }

    // Cegah duplikat
    if (pendingRows.some(r => r.no_stock === noStock)) {
        toast(`No Stok ${noStock} sudah ada di daftar pending`, 'warning');
        return;
    }
    if ((activeFpup?.existing ?? []).some(r => r.no_stock === noStock)) {
        toast(`No Stok ${noStock} sudah tersimpan untuk FPUP ini`, 'warning');
        return;
    }

    const btn = $('btnScanStock');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

    try {
        const res = await apiFetch(ROUTES.scanStock, {
            method : 'POST',
            body   : JSON.stringify({
                no_stock : noStock,
                no_fpup  : fv('hidNoFpup'),    // ← kirim untuk auto-check kecocokan
            }),
        });

        console.log('[scanStock] No Stok dikirim:', noStock);
        console.log('[scanStock] Response dari server:', res);

        if (!res.success) {
            console.warn('[scanStock] GAGAL:', res.message);
            toast(res.message || 'No Stok tidak ditemukan', 'danger');
            sv('inpJnsDarah', '');
            sv('inpGol',      '');
            sv('inpRhesus',   '+');
            sv('inpHasil',    '');
            return;
        }

        const s = res.stock;

        // ── Auto-fill identitas kantong (HANYA tampil di form, BELUM masuk pending) ──
        sv('inpNoStock',  s.no_stock  || noStock);
        sv('inpJnsDarah', (s.jns_darah || '').trim().toUpperCase());

        // ── Parsing Gol Darah & Rhesus ────────────────────────────
        let parsedGol = (s.gol    || '').trim().toUpperCase();
        let parsedRh  = (s.rhesus || '').trim();

        if ((!parsedGol || !parsedRh) && s.gol_rh_kantong) {
            const parsed = parseGolRh(s.gol_rh_kantong);
            parsedGol = parsedGol || parsed.gol;
            parsedRh  = parsedRh  || parsed.rhesus;
        }

        sv('inpGol',    parsedGol);
        sv('inpRhesus', parsedRh || '+');

        // ── Auto-fill Tgl Periksa & Batas (kadaluarsa) kantong ───
        sv('inpTglPeriksa', fv('inpTglPeriksa') || nowDatetimeLocal());
        sv('inpBatas',      toDatetimeLocal(s.tgl_kadaluarsa) || fv('inpBatas') || '');
        checkKadaluarsa();

        // ── Auto-set Hasil referal jika cocok (masih bisa diedit user sebelum ditambah) ──
        let hasilText = '';
        if (res.is_compatible) {
            sv('inpHasil', 'Cocok');
            hasilText = ' ✓ COCOK';
        } else {
            sv('inpHasil', '');
            if (res.pasien_gol_rh) {
                hasilText = ` (pasien ${res.pasien_gol_rh})`;
            }
        }

        // ── Otomatis masuk ke tabel "Detail Kantong — FPUP Ini" JUGA ──
        // (form tetap menampilkan data yang sama, tidak di-clear)
        pendingRows.push({
            _tempId    : Date.now(),
            no_stock   : s.no_stock || noStock,
            jns_darah  : fv('inpJnsDarah'),
            gol        : fv('inpGol'),
            rhesus     : fv('inpRhesus'),
            metode     : fv('inpMetode')    || 'GEL',
            tgl_periksa: fv('inpTglPeriksa') || nowDatetimeLocal(),
            batas      : fv('inpBatas')      || '',
            hasil      : fv('inpHasil')      || '',
            nat        : $('inpNat').checked,
            skrining   : fv('inpSkrining')   || '-',
            keterangan : fv('inpKeterangan') || '',
            catatan    : fv('inpCatatan')    || '',
            pemeriksa  : fv('inpPemeriksa')  || '',
            status     : fv('inpStatus')     || 'pending',
        });
        renderAll();

        const msg = res.is_compatible
            ? `✓ ${s.no_stock} (${s.jns_darah||''} ${parsedGol||''}${parsedRh||''}) → COCOK, sudah masuk daftar.`
            : `✓ ${s.no_stock} (${s.jns_darah||''} ${parsedGol||''}${parsedRh||''})${hasilText}, sudah masuk daftar.`;

        toast(msg, res.is_compatible ? 'success' : 'info');

        // Siapkan No Stok untuk scan kantong berikutnya (teks di-select,
        // jadi scan barcode selanjutnya otomatis menimpa tanpa perlu hapus manual)
        $('inpNoStock').focus();
        $('inpNoStock').select();

    } catch (err) {
        toast('Gagal scan stok: ' + err.message, 'danger');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-search"></i>';
    }
}

/* ═══════════════════════════════════════════════════════════════════
   TAMBAH KE PENDING (tombol "Tambah ke Daftar")
═══════════════════════════════════════════════════════════════════ */
function addToPending() {
    const noStock = fv('inpNoStock').trim();
    if (!noStock)             { toast('No Stok wajib diisi', 'warning'); return; }
    if (!fv('hidCrossTestId')){ toast('Scan FPUP terlebih dahulu', 'warning'); return; }
    if (!fv('inpBatas'))      { toast('Batas (kadaluarsa) wajib diisi', 'warning'); return; }

    // Cegah duplikat pending
    if (pendingRows.some(r => r.no_stock === noStock)) {
        toast(`No Stok ${noStock} sudah ada di daftar pending`, 'warning');
        return;
    }
    // Cegah duplikat dengan yang sudah tersimpan
    if ((activeFpup?.existing ?? []).some(r => r.no_stock === noStock)) {
        toast(`No Stok ${noStock} sudah tersimpan untuk FPUP ini`, 'warning');
        return;
    }

    pendingRows.push({
        _tempId    : Date.now(),
        no_stock   : noStock,
        jns_darah  : fv('inpJnsDarah'),
        gol        : fv('inpGol'),
        rhesus     : fv('inpRhesus'),
        metode     : fv('inpMetode'),
        tgl_periksa: fv('inpTglPeriksa') || nowDatetimeLocal(),
        batas      : fv('inpBatas'),
        hasil      : fv('inpHasil'),
        nat        : $('inpNat').checked,
        skrining   : fv('inpSkrining'),
        keterangan : fv('inpKeterangan'),
        catatan    : fv('inpCatatan'),
        pemeriksa  : fv('inpPemeriksa'),
        status     : fv('inpStatus'),
    });

    renderAll();
    clearStockFields();
    toast(`${noStock} ditambahkan ke daftar. Klik "Simpan Semua" untuk menyimpan ke database.`, 'success');
}

/* ═══════════════════════════════════════════════════════════════════
   HAPUS DARI PENDING (sebelum disimpan)
═══════════════════════════════════════════════════════════════════ */
window.removePending = function(tempId) {
    pendingRows = pendingRows.filter(r => r._tempId !== tempId);
    renderAll();
    toast('Dihapus dari daftar', 'info');
};

/* ═══════════════════════════════════════════════════════════════════
   SIMPAN SEMUA PENDING KE DB
═══════════════════════════════════════════════════════════════════ */
async function saveAllPending() {
    if (pendingRows.length === 0) {
        toast('Tidak ada data pending', 'warning');
        return;
    }

    const btn = $('btnSimpanSemua');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';

    const base = {
        cross_test_id      : fv('hidCrossTestId'),
        permintaan_fpup_id : fv('hidPermintaanFpupId'),
        no_fpup            : fv('hidNoFpup'),
    };

    let ok = 0, fail = 0;

    for (const row of [...pendingRows]) {
        const payload = { ...base, ...row };
        delete payload._tempId;

        try {
            const res = await apiFetch(ROUTES.store, {
                method : 'POST',
                body   : JSON.stringify(payload),
            });
            if (res.success) ok++;
            else { fail++; console.warn('Gagal:', row.no_stock, res); }
        } catch (e) {
            fail++;
            console.error(e);
        }
    }

    pendingRows = [];
    await refreshFpupSection();

    if (ok > 0) {
        toast(`${ok} kantong berhasil disimpan${fail ? `, ${fail} gagal` : ''}`,
              fail ? 'warning' : 'success');
    } else {
        toast(`Semua ${fail} kantong gagal disimpan`, 'danger');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-save-fill me-1"></i> Simpan Semua <span id="spnPendingCount" style="background:rgba(255,255,255,.25);border-radius:1rem;padding:0 .4rem;font-size:.75rem;margin-left:.25rem;">0</span>';
}

/* ═══════════════════════════════════════════════════════════════════
   SCAN PETUGAS
   Kirim sebagai 'keyword' — service akan cari di kolom yang tersedia
═══════════════════════════════════════════════════════════════════ */
async function scanPetugas(keyword) {
    keyword = (keyword ?? '').trim();
    if (!keyword) return;

    const btn = $('btnScanPetugas');
    btn.disabled = true;

    try {
        const res = await apiFetch(ROUTES.scanPetugas, {
            method : 'POST',
            body   : JSON.stringify({ nip: keyword }),   // controller masih pakai 'nip' sebagai key
        });
        if (!res.success) { toast(res.message || 'Petugas tidak ditemukan', 'danger'); return; }
        sv('inpPemeriksa', res.petugas.name);
        toast('Petugas: ' + res.petugas.name, 'info');
    } catch (e) {
        toast('Gagal scan petugas: ' + e.message, 'danger');
    } finally {
        btn.disabled = false;
    }
}

/* ═══════════════════════════════════════════════════════════════════
   REFRESH DATA DARI SERVER
═══════════════════════════════════════════════════════════════════ */
async function refreshFpupSection() {
    const noFpup = fv('hidNoFpup');
    if (!noFpup) return;

    try {
        const res = await apiFetch(ROUTES.scanFpup, {
            method : 'POST',
            body   : JSON.stringify({ no_fpup: noFpup }),
        });
        if (!res.success) return;
        activeFpup = res;
        renderAll();
    } catch (e) {
        console.error('refreshFpupSection:', e);
    }
}

/* ═══════════════════════════════════════════════════════════════════
   EDIT RECORD (dari DB)
═══════════════════════════════════════════════════════════════════ */
window.editPelayanan = async function(id) {
    try {
        const res = await apiFetch(`${ROUTES.base}/${id}`);
        if (!res.success) { toast('Data tidak ditemukan', 'danger'); return; }

        const p = res.data;
        editId = p.id;

        if (fv('hidNoFpup') !== p.no_fpup) {
            await scanFpup(p.no_fpup);
        }

        sv('inpNoStock',    p.no_stock);
        sv('inpJnsDarah',   p.jns_darah);
        sv('inpGol',        p.gol);
        sv('inpRhesus',     p.rhesus || '+');
        sv('inpMetode',     p.metode  || 'GEL');
        sv('inpTglPeriksa', toDatetimeLocal(p.tgl_periksa));
        sv('inpBatas',      toDatetimeLocal(p.batas));
        sv('inpHasil',      p.hasil   || '');
        $('inpNat').checked = !!p.nat;
        sv('inpSkrining',   p.skrining || '-');
        sv('inpKeterangan', p.keterangan);
        sv('inpCatatan',    p.catatan);
        sv('inpPemeriksa',  p.pemeriksa);
        sv('inpStatus',     p.status   || 'pending');
        checkKadaluarsa();

        // Ganti tombol ke mode edit
        $('btnAddToPending').style.display  = 'none';
        $('btnSimpanSemua').style.display   = 'none';
        $('btnUpdateRecord').style.display  = '';
        $('labelFormTitle').textContent     = `Edit — No Stok ${p.no_stock || p.no_fpup}`;

        document.querySelector('#formSection .ct-card:last-of-type')
            ?.scrollIntoView({ behavior: 'smooth' });

    } catch (err) {
        toast('Gagal load data: ' + err.message, 'danger');
    }
};

/* ═══════════════════════════════════════════════════════════════════
   UPDATE RECORD (simpan edit ke DB)
═══════════════════════════════════════════════════════════════════ */
async function updateRecord() {
    if (!editId) return;

    const btn = $('btnUpdateRecord');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';

    try {
        const res = await apiFetch(`${ROUTES.base}/${editId}`, {
            method : 'PUT',
            body   : JSON.stringify(collectPayload()),
        });

        if (res.success) {
            toast(res.message || 'Berhasil diperbarui', 'success');
            await refreshFpupSection();
            resetForm();
        } else {
            const msg = res.errors
                ? Object.values(res.errors).flat().join('\n')
                : (res.message || 'Gagal menyimpan');
            toast(msg, 'danger');
        }
    } catch (err) {
        toast('Error: ' + err.message, 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i> Update';
    }
}

/* ═══════════════════════════════════════════════════════════════════
   DELETE
═══════════════════════════════════════════════════════════════════ */
window.deletePelayanan = function(id, label) {
    deleteTargetId = id;
    $('hapusLabel').textContent = `Data kantong "${label}" akan dihapus permanen.`;
    new bootstrap.Modal($('modalHapus')).show();
};

/* ═══════════════════════════════════════════════════════════════════
   COLLECT PAYLOAD
═══════════════════════════════════════════════════════════════════ */
function collectPayload() {
    return {
        cross_test_id      : fv('hidCrossTestId'),
        permintaan_fpup_id : fv('hidPermintaanFpupId'),
        no_fpup             : fv('hidNoFpup'),
        no_stock            : fv('inpNoStock'),
        jns_darah           : fv('inpJnsDarah'),
        gol                 : fv('inpGol'),
        rhesus              : fv('inpRhesus'),
        metode              : fv('inpMetode'),
        tgl_periksa         : fv('inpTglPeriksa'),
        batas               : fv('inpBatas'),
        hasil               : fv('inpHasil'),
        nat                 : $('inpNat').checked ? 1 : 0,
        skrining            : fv('inpSkrining'),
        keterangan          : fv('inpKeterangan'),
        catatan             : fv('inpCatatan'),
        pemeriksa           : fv('inpPemeriksa'),
        status              : fv('inpStatus'),
    };
}

/* ═══════════════════════════════════════════════════════════════════
   RESET FORM → mode tambah
═══════════════════════════════════════════════════════════════════ */
function resetForm() {
    editId = null;
    $('formPelayanan').reset();
    $('inpNat').checked = false;
    sv('inpTglPeriksa', nowDatetimeLocal());
    sv('inpBatas', '');
    checkKadaluarsa();
    $('labelFormTitle').textContent = 'Form Hasil referal';
    $('btnAddToPending').style.display = '';
    $('btnUpdateRecord').style.display = 'none';
    updatePendingButton();   // tampilkan/sembunyikan simpan semua sesuai jumlah pending
}

function clearStockFields() {
    ['inpNoStock','inpJnsDarah','inpGol','inpKeterangan','inpCatatan'].forEach(id => sv(id, ''));
    sv('inpRhesus',  '+');
    sv('inpMetode',  'GEL');
    sv('inpHasil',   '');
    sv('inpSkrining','-');
    sv('inpStatus',  'pending');
    sv('inpTglPeriksa', nowDatetimeLocal());
    sv('inpBatas', '');
    checkKadaluarsa();
    $('inpNat').checked = false;
    $('inpNoStock').focus();
}

/* ═══════════════════════════════════════════════════════════════════
   RENDER SEMUA (summary + tabel)
═══════════════════════════════════════════════════════════════════ */
function renderAll() {
    if (!activeFpup) return;

    const existing = activeFpup.existing ?? [];
    const sv_db    = activeFpup.summary  ?? {};

    // Hitung ulang summary termasuk pending
    const pendingPeriksa = pendingRows.filter(r => r.hasil).length;
    const pendingCocok   = pendingRows.filter(r => r.hasil === 'Cocok').length;

    const jmlMinta   = sv_db.jml_minta   || 0;
    const jmlPeriksa = (sv_db.jml_periksa || 0) + pendingPeriksa;
    const jmlCocok   = (sv_db.jml_cocok   || 0) + pendingCocok;
    const sisa       = Math.max(0, jmlMinta - jmlPeriksa);

    $('statRow').innerHTML = renderStatsHTML({ jml_minta:jmlMinta, jml_periksa:jmlPeriksa, jml_cocok:jmlCocok, sisa });

    // Tabel: existing (dari DB) + pending (belum disimpan)
    const existingHTML = renderExistingRows(existing);
    const pendingHTML  = renderPendingRows(pendingRows);

    $('currentTbody').innerHTML = (existingHTML + pendingHTML) ||
        `<tr><td colspan="13" class="text-center py-4 text-muted">
            <i class="bi bi-inbox" style="font-size:1.5rem"></i><br>Belum ada data kantong
         </td></tr>`;

    // Badge pending di header tabel
    const lblBadge = $('lblPendingBadge');
    if (pendingRows.length > 0) {
        lblBadge.style.display = '';
        $('lblPendingCount').textContent = pendingRows.length;
    } else {
        lblBadge.style.display = 'none';
    }

    updatePendingButton();
}

function updatePendingButton() {
    const btn   = $('btnSimpanSemua');
    const count = $('spnPendingCount');
    if (!btn) return;

    if (pendingRows.length > 0 && editId === null) {
        btn.style.display = '';
        if (count) count.textContent = pendingRows.length;
    } else {
        btn.style.display = 'none';
    }

    const info = $('pendingInfo');
    if (info) {
        info.textContent = pendingRows.length > 0
            ? `${pendingRows.length} kantong belum disimpan`
            : '';
    }
}

/* ═══════════════════════════════════════════════════════════════════
   RENDER HELPERS
═══════════════════════════════════════════════════════════════════ */
function hasilBadge(h) {
    if (!h) return '<span class="text-muted">—</span>';
    const cls = h === 'Cocok' ? 'badge-compatible' : h === 'Tidak Cocok' ? 'badge-incompatible' : 'badge-pending';
    return `<span class="badge-pill ${cls}">${h}</span>`;
}
function skriningBadge(s) {
    if (!s || s === '-') return '—';
    return `<span class="badge-pill ${s === 'NEG' ? 'badge-compatible' : 'badge-incompatible'}">${s}</span>`;
}
function natIcon(n) {
    return n ? '<i class="bi bi-check-circle-fill text-success"></i>'
             : '<i class="bi bi-dash-circle text-muted"></i>';
}
function statusBadge(st) {
    const m = { selesai:'badge-compatible', proses:'badge-proses', batal:'badge-incompatible' };
    return `<span class="badge-pill ${m[st] || 'badge-pending'}">${st || 'pending'}</span>`;
}

function renderExistingRows(items) {
    return items.map((p, i) => `
        <tr class="${p.batas && isExpired(p.batas) ? 'row-expired' : ''}">
            <td class="mono text-muted text-center">${i + 1}</td>
            <td class="mono fw-semibold">${p.no_stock || '-'}</td>
            <td>${displayJnsDarah(p)}</td>
            <td class="mono">${displayGolRh(p)}</td>
            <td class="mono">${p.metode || '-'}</td>
            <td class="mono" style="white-space:nowrap;">${formatDateTime(p.tgl_periksa)}</td>
            <td style="white-space:nowrap;">${batasCell(p.batas)}</td>
            <td>${hasilBadge(p.hasil)}</td>
            <td class="text-center">${natIcon(p.nat)}</td>
            <td>${skriningBadge(p.skrining)}</td>
            <td style="font-size:.75rem;color:#6c757d;">${p.keterangan || '—'}</td>
            <td>${statusBadge(p.status)}</td>
            <td>
                <div class="d-flex gap-1">
                    <button class="btn-utd-outline py-1 px-2" onclick="editPelayanan(${p.id})" title="Edit">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn-utd-outline py-1 px-2" style="border-color:#e74c3c;color:#e74c3c"
                            onclick="deletePelayanan(${p.id},'${(p.no_stock||'').replace(/'/g,"\\'")}')">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
            </td>
        </tr>`).join('');
}

function renderPendingRows(rows) {
    return rows.map((p, i) => `
        <tr class="row-pending">
            <td class="text-center">
                <i class="bi bi-hourglass-split text-warning" title="Belum disimpan ke database"></i>
            </td>
            <td class="mono fw-semibold">
                ${p.no_stock || '-'}
                <span class="badge-pill badge-pending ms-1" style="font-size:.6rem;vertical-align:middle;">PENDING</span>
            </td>
            <td>${displayJnsDarah(p)}</td>
            <td class="mono">${displayGolRh(p)}</td>
            <td class="mono">${p.metode || '-'}</td>
            <td class="mono" style="white-space:nowrap;">${formatDateTime(p.tgl_periksa)}</td>
            <td style="white-space:nowrap;">${batasCell(p.batas)}</td>
            <td>${hasilBadge(p.hasil)}</td>
            <td class="text-center">${natIcon(p.nat)}</td>
            <td>${skriningBadge(p.skrining)}</td>
            <td style="font-size:.75rem;color:#6c757d;">${p.keterangan || '—'}</td>
            <td>${statusBadge(p.status)}</td>
            <td>
                <button class="btn-utd-outline py-1 px-2" style="border-color:#e74c3c;color:#e74c3c"
                        onclick="removePending(${p._tempId})" title="Hapus dari daftar pending">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        </tr>`).join('');
}

function renderBannerHTML(f) {
    return `
        <div class="fpup-top">
            <div>
                <div class="fpup-no-label">No. FPUP</div>
                <div class="fpup-no-value mono">${f.no_fpup || '-'}</div>
                <div class="fpup-pasien-sub mt-1"><i class="bi bi-calendar3"></i> ${f.tgl_fpup || '-'}</div>
            </div>
            <div>
                <div class="fpup-pasien-name">${f.nama_pasien || '-'}</div>
                <div class="fpup-pasien-sub">
                    ${f.jenis_kelamin || '-'} &bull; ${f.umur ? f.umur + ' thn' : '-'} &bull;
                    Gol/Rh <span class="mono">${f.gol_rh_os || '-'}</span>
                </div>
                <div class="fpup-pasien-sub">${f.diagnosa_klinis || '-'}</div>
            </div>
            <div class="text-end">
                <span class="badge-pill ${f.pasien_referal ? 'badge-incompatible' : 'badge-compatible'}">
                    ${f.pasien_referal ? 'REFERAL' : 'REGULER'}
                </span>
            </div>
        </div>
        <div class="fpup-grid">
            <div class="info-row"><span class="info-label">Rumah Sakit</span><span class="info-value">${f.nama_rs || '-'}</span></div>
            <div class="info-row"><span class="info-label">Bagian</span><span class="info-value">${f.bagian || '-'}</span></div>
            <div class="info-row"><span class="info-label">Kelas RS</span><span class="info-value">${f.kelas_rs || '-'}</span></div>
            <div class="info-row"><span class="info-label">Kelas Rawat</span><span class="info-value">${f.kelas_rawat || '-'}</span></div>
            <div class="info-row"><span class="info-label">No Reg</span><span class="info-value mono">${f.no_reg || '-'}</span></div>
            <div class="info-row"><span class="info-label">Dokter</span><span class="info-value">${f.nama_dokter || '-'}</span></div>
            <div class="info-row"><span class="info-label">Jns Biaya</span><span class="info-value">${f.jns_biaya || '-'}</span></div>
            <div class="info-row"><span class="info-label">Cara Bayar</span><span class="info-value">${f.cara_pembayaran || '-'}</span></div>
        </div>`;
}

function renderStatsHTML(s) {
    return `
        <div class="stat-card stat-minta">
            <i class="bi bi-droplet-half"></i>
            <div><div class="stat-value">${s.jml_minta   || 0}</div><div class="stat-label">Jml Minta</div></div>
        </div>
        <div class="stat-card stat-periksa">
            <i class="bi bi-clipboard2-check"></i>
            <div><div class="stat-value">${s.jml_periksa || 0}</div><div class="stat-label">Diperiksa</div></div>
        </div>
        <div class="stat-card stat-cocok">
            <i class="bi bi-check-circle"></i>
            <div><div class="stat-value">${s.jml_cocok   || 0}</div><div class="stat-label">Cocok</div></div>
        </div>
        <div class="stat-card stat-sisa">
            <i class="bi bi-hourglass-split"></i>
            <div><div class="stat-value">${s.sisa        || 0}</div><div class="stat-label">Sisa</div></div>
        </div>`;
}

/* ═══════════════════════════════════════════════════════════════════
   DOM READY
═══════════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {

    // Scan FPUP
    $('btnScanFpup').onclick = () => scanFpup($('inputNoFpup').value);
    $('inputNoFpup').onkeypress = e => { if (e.key === 'Enter') scanFpup(e.target.value); };

    // Scan No Stok (hanya isi form, tidak simpan)
    $('btnScanStock').onclick = () => scanStock($('inpNoStock').value);
    $('inpNoStock').onkeypress = e => { if (e.key === 'Enter') scanStock(e.target.value); };

    // Scan Petugas
    $('btnScanPetugas').onclick = () => scanPetugas($('inpPemeriksa').value);
    $('inpPemeriksa').onkeypress = e => { if (e.key === 'Enter') scanPetugas(e.target.value); };

    // Batas (kadaluarsa) → update badge status kantong realtime
    $('inpBatas')?.addEventListener('input', checkKadaluarsa);

    // Set default Tgl Periksa = sekarang saat halaman dibuka
    sv('inpTglPeriksa', nowDatetimeLocal());
    checkKadaluarsa();

    // Tambah ke Daftar Pending
    $('btnAddToPending').onclick = addToPending;

    // Simpan Semua Pending ke DB
    $('btnSimpanSemua').onclick = saveAllPending;

    // Update (mode edit)
    $('btnUpdateRecord').onclick = updateRecord;

    // Batal / Reset
    $('btnCancel').onclick = resetForm;

    // Prevent accidental form submit
    $('formPelayanan').onsubmit = e => e.preventDefault();

    // Konfirmasi hapus
    $('btnHapusConfirm').onclick = async function () {
        if (!deleteTargetId) return;
        try {
            const res = await apiFetch(`${ROUTES.base}/${deleteTargetId}`, { method: 'DELETE' });
            bootstrap.Modal.getInstance($('modalHapus'))?.hide();
            if (res.success) {
                toast(res.message || 'Berhasil dihapus', 'success');
                await refreshFpupSection();
            } else {
                toast(res.message || 'Gagal menghapus', 'danger');
            }
        } catch (e) {
            toast('Error: ' + e.message, 'danger');
        }
        deleteTargetId = null;
    };
});
</script>
@endpush