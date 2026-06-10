@extends('layouts.index')

@section('title')
    Riwayat Pengiriman Sample – Unit Transfusi Darah
@endsection

@push('styles')
<style>
/* Toast styling */
.ps-toast-area {
    position: fixed; top: 18px; right: 18px; z-index: 99999;
    display: flex; flex-direction: column; gap: 8px; pointer-events: none;
}
.ps-toast {
    display: inline-flex; align-items: center; gap: 9px;
    padding: 11px 15px; border-radius: 8px;
    font-size: .82rem; font-weight: 600; color: #fff;
    min-width: 250px; pointer-events: auto;
    animation: psToastIn .22s ease; font-family: 'Inter', sans-serif;
    border: 1px solid rgba(255,255,255,.10);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.ps-toast i { font-size: 15px; flex-shrink: 0; }
.ps-toast.t-ok   { background: #14532D; border-color: rgba(34,197,94,.20); }
.ps-toast.t-err  { background: #7F1D1D; border-color: rgba(239,68,68,.25); }
.ps-toast.t-warn { background: #78350F; border-color: rgba(245,158,11,.20); }
@keyframes psToastIn { from { opacity: 0; transform: translateX(18px); } to { opacity: 1; transform: translateX(0); } }

/* Spinner & row transitions */
.ps-spin { animation: psSpin .85s linear infinite; display: inline-block; }
@keyframes psSpin { to { transform: rotate(360deg); } }
.ps-row-in { animation: psRowIn .28s ease forwards; }
@keyframes psRowIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

/* Monospace */
.ps-mono { font-family: 'JetBrains Mono', monospace; font-size: .78rem; font-weight: 500; }

/* Dashboard card gradients */
.stat-card-blue { background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; }
.stat-card-green { background: linear-gradient(135deg, #064e3b, #10b981); color: white; }
.stat-card-amber { background: linear-gradient(135deg, #78350f, #f59e0b); color: white; }
.stat-card-red { background: linear-gradient(135deg, #7f1d1d, #ef4444); color: white; }
</style>
@endpush

@section('content')

<div class="ps-toast-area" id="psToastArea"></div>

<div class="content flex-column-fluid" id="kt_content">

    {{-- ── PAGE HEADER ── --}}
    <div class="d-flex flex-row justify-content-between align-items-center mb-6">
        <div class="d-flex flex-column">
            <h1 class="d-flex align-items-center my-1"><span class="text-dark fw-bold fs-1">Riwayat Pengiriman Sample</span></h1>
            @include('layouts._breadcrumb')
        </div>
        <div>
            <a href="{{ route('aftap.pengiriman_sample.index') }}" class="btn btn-sm btn-primary fw-bold">
                <i class="fas fa-plus me-2"></i> Buat Pengiriman Baru
            </a>
        </div>
    </div>

    {{-- ── MAIN HISTORY TABLE & FILTERS ── --}}
    <div class="card card-flush rounded-4 border-0 shadow-xs">
        <div class="card-header bg-light-secondary px-6 py-4 border-bottom d-flex align-items-center gap-3 flex-wrap">
            <div class="position-relative" style="flex:1;min-width:150px">
                <label class="form-label fw-bold fs-8 text-uppercase text-muted mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> Dari Tanggal</label>
                <input type="date" id="ps_hist_dari" class="form-control form-control-sm form-control-solid" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
            </div>
            <div class="position-relative" style="flex:1;min-width:150px">
                <label class="form-label fw-bold fs-8 text-uppercase text-muted mb-1"><i class="fas fa-calendar-alt text-primary me-1"></i> Sampai Tanggal</label>
                <input type="date" id="ps_hist_sampai" class="form-control form-control-sm form-control-solid" value="{{ date('Y-m-d') }}">
            </div>
            <div class="position-relative" style="flex:2;min-width:180px">
                <label class="form-label fw-bold fs-8 text-uppercase text-muted mb-1"><i class="fas fa-search text-primary me-1"></i> Keyword / No FPD</label>
                <input type="text" id="ps_hist_keyword" class="form-control form-control-sm form-control-solid" placeholder="No FPD...">
            </div>
            <div class="d-flex align-items-end" style="height: 100%">
                <button class="btn btn-sm btn-success fw-bold px-5 py-2 mt-5" onclick="psLoadHistory()">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed table-hover fs-7 table-sm mb-0">
                    <thead>
                        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                            <th class="ps-6 rounded-start" style="width:38px">#</th>
                            <th>No FPD</th>
                            <th>Tanggal FPD</th>
                            <th>Type Kantong</th>
                            <th>Suhu</th>
                            <th>NAT status</th>
                            <th>ID Logger</th>
                            <th>ID Coolbox</th>
                            <th class="text-center">Jml Kantong</th>
                            <th>Petugas Pemeriksa</th>
                            <th class="text-center w-120px pe-6 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ps_hist_tbody">
                        <tr class="ps-no-data">
                            <td colspan="11" class="text-center text-muted py-10 fs-6">
                                <i class="fas fa-spinner ps-spin fs-4 text-primary"></i> Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer d-flex flex-row justify-content-between align-items-center px-6 py-4 border-top" id="ps_hist_foot" style="display:none">
            <span class="badge badge-light-dark fs-7 py-2 px-3" id="ps_hist_info">–</span>
            <div class="d-flex gap-2">
                <button class="btn btn-icon btn-sm btn-light-primary" id="ps_hist_prev" onclick="psHistPage(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-icon btn-sm btn-light-primary" id="ps_hist_next" onclick="psHistPage(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ── DRILL-DOWN DETAIL MODAL ── --}}
<div class="modal fade" tabindex="-1" id="ps_modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header py-3 px-6">
                <div>
                    <h3 class="modal-title fs-5" id="ps_modal_title">Detail FPD</h3>
                    <p class="text-muted fs-8 mb-0" id="ps_modal_sub"></p>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body py-5 px-6">
                <div class="table-responsive" style="max-height:400px;overflow-y:auto">
                    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm mb-0">
                        <thead>
                            <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                <th class="ps-4 rounded-start" style="width:36px">#</th>
                                <th>No Kantong</th>
                                <th>No Selang</th>
                                <th>Jenis</th>
                                <th>No Donor</th>
                                <th>Nama Donor</th>
                                <th>Gol. Darah</th>
                                <th>Asal Darah</th>
                                <th class="text-center w-80px pe-4 rounded-end">Tolak</th>
                            </tr>
                        </thead>
                        <tbody id="ps_modal_tbody">
                            <tr class="ps-no-data">
                                <td colspan="9" class="text-center py-6">
                                    <i class="fas fa-spinner ps-spin fs-4 text-primary"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ══════════════════════════════════════
   CONFIG & STATE
   ══════════════════════════════════════ */
const ROUTE_INDEX    = "{{ route('aftap.riwayat_pengiriman_sample') }}";
const ROUTE_SHOW     = "{{ url('/aftap/pengiriman_sample') }}"; // URL base to fetch detail
const CSRF           = "{{ csrf_token() }}";

let histPage     = 1;
let histTotal    = 0;
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
   LOAD DATA HISTORY (AJAX)
   ══════════════════════════════════════ */
async function psLoadHistory() {
    const dari    = document.getElementById('ps_hist_dari').value;
    const sampai  = document.getElementById('ps_hist_sampai').value;
    const keyword = document.getElementById('ps_hist_keyword').value;
    const tbody   = document.getElementById('ps_hist_tbody');

    tbody.innerHTML = `
        <tr>
            <td colspan="11" class="text-center py-10 text-muted fs-6">
                <i class="fas fa-spinner ps-spin fs-4 text-primary mb-2 d-block"></i>
                Memuat riwayat pengiriman...
            </td>
        </tr>`;

    try {
        const u = new URL(ROUTE_INDEX);
        if (dari)    u.searchParams.append('dari', dari);
        if (sampai)  u.searchParams.append('sampai', sampai);
        if (keyword) u.searchParams.append('keyword', keyword);
        u.searchParams.append('page', histPage);
        u.searchParams.append('per', HIST_PER);

        const res  = await fetch(u.toString(), { headers: { 'X-Requested-With':'XMLHttpRequest' } });
        const json = await res.json();

        histTotal = json.total;
        tbody.innerHTML = '';

        if (!json.data || json.data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="11" class="text-center py-10 text-muted fs-6">
                        <i class="fas fa-search fs-1 d-block mb-3 opacity-25"></i>
                        Tidak ada riwayat pengiriman sample
                    </td>
                </tr>`;
            document.getElementById('ps_hist_foot').style.display = 'none';
            return;
        }

        json.data.forEach((x, i) => {
            const num = (histPage - 1) * HIST_PER + (i + 1);
            const tgl = x.tanggal_fpd
                ? new Date(x.tanggal_fpd).toLocaleDateString('id-ID',
                    {day:'2-digit', month:'short', year:'numeric'})
                : '–';
            const natBadge = x.is_nat
                ? '<span class="badge badge-light-warning">NAT</span>'
                : '<span class="badge badge-light-primary">BIASA</span>';

            const tr = document.createElement('tr');
            tr.className = 'ps-row-in';
            tr.innerHTML = `
                <td class="ps-6" style="color:var(--text-3)">${num}</td>
                <td><span class="ps-mono fw-bold text-primary" style="cursor:pointer" onclick="psViewDetail(${x.id})">${x.no_fpd}</span></td>
                <td>${tgl}</td>
                <td><span class="badge badge-light-info">${x.type_kantong ?? 'BIASA'}</span></td>
                <td>${x.suhu ?? '–'}</td>
                <td>${natBadge}</td>
                <td><span class="ps-mono">${x.id_logger ?? '–'}</span></td>
                <td><span class="ps-mono">${x.id_coolbox ?? '–'}</span></td>
                <td class="text-center"><span class="badge badge-light-success fs-7 fw-bold">${x.detail_count ?? 0}</span></td>
                <td class="fw-semibold text-dark">${x.petugas_pemeriksa ?? '–'}</td>
                <td class="text-center pe-6">
                    <button class="btn btn-icon btn-sm btn-light-primary me-1" onclick="psViewDetail(${x.id})" title="Lihat detail">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-icon btn-sm btn-light-success me-1" onclick="psCetak(${x.id})" title="Cetak FPD">
                        <i class="fas fa-print"></i>
                    </button>
                </td>`;
            tbody.appendChild(tr);
        });

        // Pagination info
        document.getElementById('ps_hist_foot').style.display = '';
        const pageMax = Math.ceil(histTotal / HIST_PER) || 1;
        document.getElementById('ps_hist_info').textContent = `Halaman ${histPage} dari ${pageMax} (Total ${histTotal} data)`;
        document.getElementById('ps_hist_prev').disabled = histPage <= 1;
        document.getElementById('ps_hist_next').disabled = histPage >= pageMax;

    } catch(e) {
        tbody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center py-10 text-danger fs-6">
                    <i class="fas fa-exclamation-triangle fs-1 d-block mb-3 opacity-25"></i>
                    Gagal memuat data: ${e.message}
                </td>
            </tr>`;
        psToast('Gagal memuat data', 'err');
    }
}

function psHistPage(dir) {
    histPage += dir;
    psLoadHistory();
}

/* ══════════════════════════════════════
   VIEW DETAIL MODAL
   ══════════════════════════════════════ */
async function psViewDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('ps_modal'));
    document.getElementById('ps_modal_title').textContent = 'Memuat...';
    document.getElementById('ps_modal_sub').textContent   = '';
    document.getElementById('ps_modal_tbody').innerHTML   = `
        <tr>
            <td colspan="9" class="text-center py-6">
                <i class="fas fa-spinner ps-spin fs-4 text-primary"></i>
            </td>
        </tr>`;

    modal.show();

    try {
        const res  = await fetch(`${ROUTE_SHOW}/${id}`);
        const json = await res.json();

        document.getElementById('ps_modal_title').textContent = `Detail FPD: ${json.header.no_fpd}`;
        const tgl = json.header.tanggal_fpd
            ? new Date(json.header.tanggal_fpd).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})
            : '–';
        document.getElementById('ps_modal_sub').textContent =
            `Tanggal: ${tgl} | Type: ${json.header.type_kantong ?? 'BIASA'} | Suhu: ${json.header.suhu ?? '–'} | Logger: ${json.header.id_logger ?? '–'} | Coolbox: ${json.header.id_coolbox ?? '–'}`;

        const tbody = document.getElementById('ps_modal_tbody');
        tbody.innerHTML = '';

        if (!json.detail || json.detail.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Tidak ada kantong</td></tr>';
            return;
        }

        json.detail.forEach((x, i) => {
            const golRhesus = [x.gol_darah, x.rhesus].filter(Boolean).join(' ');
            const badgeColor = (x.rhesus ?? '').includes('-') ? 'danger' : 'success';
            const tolakStyle = x.tolak ? 'style="opacity: 0.5"' : '';
            const tolakBadge = x.tolak
                ? '<span class="badge badge-light-danger"><i class="fas fa-ban me-1 text-danger"></i> DITOLAK</span>'
                : '<span class="badge badge-light-success"><i class="fas fa-check me-1 text-success"></i> DITERIMA</span>';

            const tr = document.createElement('tr');
            if (x.tolak) tr.style.opacity = '0.5';
            tr.innerHTML = `
                <td class="ps-4" style="color:var(--text-3)">${i + 1}</td>
                <td><span class="ps-mono">${x.no_kantong ?? '–'}</span></td>
                <td><span class="ps-mono">${x.id_coolbox ?? '–'}</span></td>
                <td><span class="badge badge-light-info">${x.jenis_kantong ?? '–'}</span></td>
                <td><span class="ps-mono">${x.no_donor ?? '–'}</span></td>
                <td class="fw-semibold text-dark">${x.nama_donor ?? '–'}</td>
                <td><span class="badge badge-light-${badgeColor}">${golRhesus || '–'}</span></td>
                <td>${x.kode_asal_darah ?? '–'}</td>
                <td class="text-center pe-4">${tolakBadge}</td>`;
            tbody.appendChild(tr);
        });

    } catch (e) {
        document.getElementById('ps_modal_title').textContent = 'Error';
        document.getElementById('ps_modal_tbody').innerHTML =
            `<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat detail: ${e.message}</td></tr>`;
    }
}

/* ══════════════════════════════════════
   PRINT FPD Logic (Same design standard)
   ══════════════════════════════════════ */
async function psCetak(id) {
    try {
        const res  = await fetch(`${ROUTE_SHOW}/${id}`);
        const json = await res.json();

        const header = json.header;
        const details = json.detail;

        const noFpd      = header.no_fpd ?? '–';
        const tglFpd     = header.tanggal_fpd
            ? new Date(header.tanggal_fpd).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' })
            : '–';

        const isNat      = header.is_nat ?? false;
        const suhu       = header.suhu || '';
        const idLogger   = header.id_logger || '';
        const noSelang   = header.id_coolbox || '';
        const petugas    = header.petugas_pemeriksa || '';
        const keterangan = header.keterangan || '';

        const golMap = { A: { pos: 0, neg: 0 }, B: { pos: 0, neg: 0 }, AB: { pos: 0, neg: 0 }, O: { pos: 0, neg: 0 } };
        details.forEach(it => {
            if (!it.tolak) {
                const gol = (it.gol_darah ?? '').toUpperCase().trim();
                const rh  = (it.rhesus   ?? '').trim();
                if (golMap[gol] !== undefined) {
                    if (rh === '+') golMap[gol].pos++;
                    else            golMap[gol].neg++;
                }
            }
        });

        const totalDiterima = details.filter(x => !x.tolak).length;
        const totalDitolak  = details.filter(x =>  x.tolak).length;

        const rowsHtml = details.map((it, i) => {
            const tgl = it.tanggal_aftap
                ? (() => {
                    const d = new Date(it.tanggal_aftap);
                    const hh = String(d.getHours()).padStart(2,'0');
                    const mm = String(d.getMinutes()).padStart(2,'0');
                    return hh + ':' + mm;
                  })()
                : '–';

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
                <td>${it.keterangan ?? keterangan}</td>
                <td style="text-align:center">${it.id_logger ?? idLogger}</td>
                <td style="text-align:center">${tgl}</td>
            </tr>`;
        }).join('');

        const golKeys   = ['A', 'B', 'AB', 'O'];
        let totAll = {};
        let grandAll = 0;
        golKeys.forEach(g => {
            totAll[g] = golMap[g].pos + golMap[g].neg;
            grandAll += golMap[g].pos + golMap[g].neg;
        });

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

        const now      = new Date();
        const printTgl = now.toLocaleDateString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric' });
        const printJam = now.toLocaleTimeString('id-ID', { hour12: false });
        const operator = petugas || 'ADM';

        const html = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>FPD – ${noFpd}</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');
body { font-family: 'Courier Prime', Courier, monospace; font-size: 11px; color: #111; margin: 20px; line-height: 1.25; }
.header-tbl { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
.header-tbl td { padding: 2px 4px; vertical-align: top; }
.logo-title { font-size: 13px; font-weight: bold; margin-bottom: 2px; }
.doc-title { font-size: 15px; font-weight: bold; text-align: center; margin: 8px 0; border: 1px double #222; padding: 4px; }
.meta-tbl { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.meta-tbl td { font-size: 11px; padding: 2px 4px; }
.detail-tbl { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
.detail-tbl th, .detail-tbl td { border: 1px solid #111; padding: 3px 5px; font-size: 10px; text-align: left; }
.detail-tbl th { background: #e5e5e5; font-weight: bold; text-align: center; }
.summary-tbl { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
.summary-tbl th, .summary-tbl td { border: 1px solid #111; padding: 4px 6px; font-size: 10px; }
.summary-tbl th { background: #e5e5e5; font-weight: bold; text-align: center; }
.footer-tbl { width: 100%; border-collapse: collapse; margin-top: 15px; }
.footer-tbl td { text-align: center; width: 33%; font-size: 11px; padding: 5px; }
.sig-box { height: 50px; }
.no-print-bar { background: #f5f5f5; padding: 8px 12px; border-bottom: 1px solid #ddd; margin-bottom: 15px; text-align: right; }
.no-print-bar button { padding: 4px 10px; font-weight: bold; cursor: pointer; }
@media print { .no-print-bar { display: none; } body { margin: 0; } }
</style>
</head>
<body>
<div class="no-print-bar">
    <button onclick="window.print()">Cetak Dokumen</button>
    <button onclick="window.close()" style="margin-left:8px">Tutup</button>
</div>

<table class="header-tbl">
    <tr>
        <td style="width:12%"><img src="/assets/media/logos/pmi_logo.png" style="height:48px;display:block" onerror="this.style.display='none'"></td>
        <td>
            <div class="logo-title">PALANG MERAH INDONESIA</div>
            <div class="logo-title" style="font-size:12px">UNIT DONOR DARAH KOTA SURABAYA</div>
            <div style="font-size:10px;opacity:0.8">Jl. Embong Ploso No. 7-15 Surabaya | Telp. 031-5313563</div>
        </td>
        <td style="text-align:right;width:30%;font-size:10px">
            <div>Formulir: <b>UTDSBY-FS005-PDK-L4-28-2019</b></div>
            <div>Revisi: 02 | Halaman 1 dari 1</div>
        </td>
    </tr>
</table>

<div class="doc-title">FORMULIR PENGIRIMAN DOKUMEN & KANTONG DARAH (FPD)</div>

<table class="meta-tbl">
    <tr>
        <td style="width:12%">No FPD</td><td style="width:1%">:</td><td style="width:37%"><b>${noFpd}</b></td>
        <td style="width:12%">Metode</td><td style="width:1%">:</td><td style="width:37%"><b>${isNat ? 'NAT' : 'Biasa'}</b></td>
    </tr>
    <tr>
        <td>Tanggal FPD</td><td>:</td><td>${tglFpd}</td>
        <td>Suhu Pengiriman</td><td>:</td><td>${suhu ? suhu + ' &deg;C' : '–'}</td>
    </tr>
    <tr>
        <td>Petugas</td><td>:</td><td>${petugas}</td>
        <td>ID Logger / Coolbox</td><td>:</td><td>${idLogger || '–'} / ${noSelang || '–'}</td>
    </tr>
</table>

<table class="detail-tbl">
    <thead>
        <tr>
            <th style="width:3%">No</th>
            <th>No Aftap</th>
            <th>No Kantong</th>
            <th>Jenis Ktg</th>
            <th>Vol</th>
            <th>Pnh</th>
            <th>Smpl</th>
            <th>Jenis Donor</th>
            <th>Asal Drh</th>
            <th>Petugas</th>
            <th>No Donor</th>
            <th style="width:4%">Gol</th>
            <th style="width:3%">Rh</th>
            <th style="width:5%">Cek</th>
            <th>Keterangan</th>
            <th style="width:5%">Log</th>
            <th style="width:6%">Jam</th>
        </tr>
    </thead>
    <tbody>
        ${rowsHtml}
    </tbody>
</table>

<div style="font-weight:bold;margin-bottom:6px">RINGKASAN PENGIRIMAN SAMPLE DARAH</div>
<table class="summary-tbl">
    <thead>
        <tr>
            <th rowspan="2" style="width:16%">Jenis Kantong</th>
            <th colspan="2">A</th>
            <th colspan="2">B</th>
            <th colspan="2">AB</th>
            <th colspan="2">O</th>
            <th rowspan="2" style="width:10%">Total</th>
        </tr>
        <tr>
            <th style="width:9%">Positif</th><th style="width:9%">Total</th>
            <th style="width:9%">Positif</th><th style="width:9%">Total</th>
            <th style="width:9%">Positif</th><th style="width:9%">Total</th>
            <th style="width:9%">Positif</th><th style="width:9%">Total</th>
        </tr>
    </thead>
    <tbody>
        ${summaryDataRow}
    </tbody>
</table>

<table class="meta-tbl" style="margin-top:10px">
    <tr>
        <td style="width:12%">Keterangan</td><td style="width:1%">:</td>
        <td>${keterangan || '–'}</td>
    </tr>
    <tr>
        <td>Ringkasan</td><td>:</td>
        <td>Total Kantong: <b>${details.length}</b> (Diterima: <b>${totalDiterima}</b> | Ditolak: <b style="color:red">${totalDitolak}</b>)</td>
    </tr>
</table>

<table class="footer-tbl">
    <tr>
        <td>
            <div>Diserahkan Oleh,</div>
            <div style="font-size:10px;opacity:0.6">Petugas Aftap</div>
            <div class="sig-box"></div>
            <div style="text-decoration:underline;font-weight:bold">${operator}</div>
        </td>
        <td>
            <div>Mengetahui,</div>
            <div style="font-size:10px;opacity:0.6">Supervisor / PJ</div>
            <div class="sig-box"></div>
            <div>( _____________________ )</div>
        </td>
        <td>
            <div>Diterima Oleh,</div>
            <div style="font-size:10px;opacity:0.6">Petugas Serologi</div>
            <div class="sig-box"></div>
            <div>( _____________________ )</div>
        </td>
    </tr>
</table>

<div style="font-size:9px;opacity:0.5;margin-top:15px;border-top:1px dashed #bbb;padding-top:4px">
    Printed: ${printTgl} ${printJam} | Op: ${operator} | System: Antigravity UTD
</div>

<script>
window.onload = function() {
    // Auto trigger print in 500ms
    setTimeout(function() { window.print(); }, 400);
};
<\/script>
</body>
</html>`;

        const w = window.open('about:blank', '_blank', 'width=1024,height=768,scrollbars=yes');
        w.document.write(html);
        w.document.close();

    } catch (e) {
        psToast(`Gagal menyiapkan print: ${e.message}`, 'err');
    }
}

// Auto load data on load
document.addEventListener('DOMContentLoaded', () => {
    psLoadHistory();
});
</script>
@endpush
