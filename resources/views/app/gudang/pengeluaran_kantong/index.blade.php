@extends('layouts.index')

@section('title', 'Pengeluaran Kantong Darah')

@push('styles')
<style>
    :root {
        --primary: #c0392b;
        --primary-dark: #96281b;
        --primary-light: #fadbd8;
        --accent: #e74c3c;
        --teal: #0d9488;
        --teal-light: #ccfbf1;
        --surface: #ffffff;
        --surface-2: #f8f9fa;
        --surface-3: #f1f3f4;
        --border: #e0e3e7;
        --text: #1a1d23;
        --text-muted: #6b7280;
        --text-light: #9ca3af;
        --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
        --radius: 10px;
        --radius-sm: 6px;
    }
    body { background: #f4f6f9; }

    .page-header {
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        padding: 18px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .page-header h4 { font-weight: 700; font-size: 1.1rem; color: var(--text); margin: 0; }
    .page-header .breadcrumb { font-size: .78rem; color: var(--text-muted); margin: 0; }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 20px;
        padding: 20px 24px;
    }

    .card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .card-header-bar {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header-bar h6 {
        font-weight: 700; font-size: .88rem; color: var(--text); margin: 0;
        display: flex; align-items: center; gap: 8px;
    }
    .card-header-bar h6 i { color: var(--primary); }

    .form-body { padding: 18px 20px; }
    .form-row { display: grid; gap: 14px; margin-bottom: 14px; }
    .form-row-2 { grid-template-columns: 1fr 1fr; }

    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-label {
        font-size: .71rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted);
    }
    .form-control, .form-select {
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        padding: 9px 12px; font-size: .86rem; color: var(--text);
        background: var(--surface); transition: border-color .18s, box-shadow .18s;
        height: 40px; outline: none; width: 100%;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.12);
    }
    .form-control[readonly] { background: var(--surface-3); color: var(--text-muted); }

    .scan-bar { display: flex; gap: 8px; align-items: center; }
    .scan-bar .form-control { flex: 1; font-family: monospace; font-size: .9rem; font-weight: 700; }

    .btn-scan {
        background: var(--teal); color: #fff; border: none; border-radius: var(--radius-sm);
        padding: 0 16px; height: 40px; font-size: .83rem; font-weight: 700; cursor: pointer;
        display: inline-flex; align-items: center; gap: 6px;
        transition: background .18s, transform .1s; white-space: nowrap;
    }
    .btn-scan:hover { background: #0f766e; transform: translateY(-1px); }

    .select-section {
        background: linear-gradient(135deg, #f0fffe 0%, #e6faf8 100%);
        border-bottom: 1px solid #b2dfdb;
    }
    .select-section .section-title {
        padding: 12px 20px 0; font-size: .75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em; color: var(--teal);
    }
    .select-table-wrap { overflow-x: auto; max-height: 220px; overflow-y: auto; }

    table.select-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
    table.select-table thead tr { background: rgba(13,148,136,.12); }
    table.select-table thead th {
        padding: 10px 14px; font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em; color: var(--teal);
        text-align: left; white-space: nowrap; position: sticky; top: 0; background: #ddf6f4; z-index: 1;
    }
    table.select-table tbody tr {
        border-bottom: 1px solid #e0f2f1; cursor: pointer; transition: background .12s;
    }
    table.select-table tbody tr:hover { background: #e0f9f7; }
    table.select-table tbody tr.selected-row { background: #b2f0eb; }
    table.select-table tbody td { padding: 10px 14px; vertical-align: middle; }
    table.select-table tbody td.empty-cell {
        text-align: center; padding: 28px; color: var(--text-light); font-size: .83rem;
    }

    /* Status badge */
    .badge-status {
        display: inline-block; font-size: .7rem; font-weight: 700;
        padding: 2px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: .05em;
    }
    .badge-pending  { background: #fef3c7; color: #92400e; }
    .badge-proses   { background: #dbeafe; color: #1e40af; }
    .badge-selesai  { background: #d1fae5; color: #065f46; }

    .info-panel { display: flex; flex-direction: column; gap: 14px; }
    .info-card {
        background: linear-gradient(135deg, #f8f0f0 0%, #fef5f5 100%);
        border: 1px solid #f0d0d0; border-radius: var(--radius); overflow: hidden;
    }
    .info-card-header {
        padding: 12px 18px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        color: #fff; font-size: .78rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em;
        display: flex; align-items: center; gap: 8px;
    }
    .info-grid {
        padding: 16px 18px; display: grid;
        grid-template-columns: 1fr 1fr; gap: 12px;
    }
    .info-field { display: flex; flex-direction: column; gap: 4px; }
    .info-field.full { grid-column: 1 / -1; }
    .info-field label {
        font-size: .68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .07em; color: var(--text-muted);
    }
    .info-value {
        border: 1.5px solid var(--border); border-radius: var(--radius-sm);
        padding: 8px 12px; font-size: .88rem; font-weight: 600; color: var(--text);
        background: var(--surface); min-height: 38px; display: flex; align-items: center;
    }
    .info-value.highlight { border-color: var(--primary); color: var(--primary); font-weight: 800; font-size: .95rem; }
    .info-value input {
        border: none; outline: none; width: 100%;
        font-size: .88rem; font-weight: 600; color: var(--text); background: transparent;
    }
    .info-value input::-webkit-inner-spin-button { -webkit-appearance: none; }

    .btn-save {
        background: var(--primary); color: #fff; border: none; border-radius: var(--radius-sm);
        padding: 10px 20px; font-size: .88rem; font-weight: 700; cursor: pointer;
        width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background .18s, transform .1s, box-shadow .18s;
        box-shadow: 0 2px 10px rgba(192,57,43,.3); margin-top: 4px;
    }
    .btn-save:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-save:disabled { background: #ccc; box-shadow: none; cursor: not-allowed; transform: none; }

    .no-badge {
        display: inline-block; font-weight: 700; font-size: .82rem;
        color: var(--teal); background: var(--teal-light); padding: 4px 12px; border-radius: 20px;
    }
    .edit-badge {
        display: none; font-weight: 700; font-size: .78rem;
        color: #92400e; background: #fef3c7; padding: 4px 12px; border-radius: 20px;
        align-items: center; gap: 6px;
    }
    .edit-badge.visible { display: inline-flex; }

    .alert-bar {
        padding: 11px 20px; border-radius: var(--radius-sm); font-size: .84rem; font-weight: 600;
        display: flex; align-items: center; gap: 9px; margin: 0 20px 14px;
    }
    .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

    .history-wrap { padding: 0 24px 24px; }
    .history-card { background: var(--surface); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }

    table.history-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
    table.history-table thead tr { background: #f8f0f0; border-bottom: 2px solid #f0d0d0; }
    table.history-table thead th {
        padding: 11px 14px; font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em; color: var(--primary-dark);
        text-align: left; white-space: nowrap;
    }
    table.history-table tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
    table.history-table tbody tr:hover { background: #fef9f9; }
    table.history-table tbody td { padding: 11px 14px; vertical-align: middle; }

    .btn-icon {
        border: none; background: transparent; cursor: pointer;
        padding: 4px 8px; border-radius: 6px; font-size: .85rem;
        transition: background .12s;
    }
    .btn-icon:hover { background: #f0f0f0; }
    .btn-icon.edit  { color: #1d4ed8; }
    .btn-icon.del   { color: #b91c1c; }

    .spinner {
        display: inline-block; width: 13px; height: 13px;
        border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
        border-radius: 50%; animation: spin .6s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes rowIn { from { opacity: 0; background: #e0f9f7; } to { opacity: 1; } }
    .row-in { animation: rowIn .3s ease; }

    @media (max-width: 960px) {
        .content-grid { grid-template-columns: 1fr; }
        .form-row-2 { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h4><i class="fas fa-sign-out-alt me-2" style="color:var(--primary)"></i>Pengeluaran Kantong Darah</h4>
        <div class="breadcrumb">Gudang / Pengeluaran Kantong</div>
    </div>
</div>

<div class="content-grid">

    {{-- ── KIRI ──────────────────────────────────────────── --}}
    <div class="card">

        <div class="card-header-bar">
            <h6><i class="fas fa-file-medical-alt"></i> Form Pengeluaran</h6>
            <div style="display:flex;gap:8px;align-items:center">
                <span class="edit-badge" id="editBadge"><i class="fas fa-pencil-alt"></i> Mode Edit</span>
                <span class="no-badge" id="displayNoTransaksi">—</span>
                <button id="btnCancelEdit" onclick="cancelEdit()"
                    style="display:none;background:#fee2e2;color:#991b1b;border:none;
                           border-radius:6px;padding:4px 12px;font-size:.78rem;font-weight:700;cursor:pointer">
                    Batal Edit
                </button>
            </div>
        </div>

        <div id="alertBar" style="display:none; margin-top:14px"></div>

        <div class="form-body">
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label class="form-label">No Transaksi</label>
                    <input type="text" id="noTransaksi" class="form-control" readonly placeholder="Auto…">
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Transaksi</label>
                    <input type="date" id="tanggalTransaksi" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
            </div>
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label class="form-label">Petugas</label>
                    <input type="text" class="form-control" readonly value="{{ auth()->user()->name ?? 'admin' }}">
                </div>
                <div class="form-group">
                    <label class="form-label">No Permintaan</label>
                    <select id="noMinta" class="form-select" onchange="loadPermintaan()">
                        <option value="">— Pilih No Permintaan —</option>
                        @foreach($permintaanList ?? [] as $pm)
                            <option value="{{ $pm->id }}">
                                {{ $pm->nomor }} — {{ $pm->status }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Scan No Kantong</label>
                <div class="scan-bar">
                    <input type="text" id="scanInput" class="form-control"
                        placeholder="Scan / ketik no kantong lalu Enter…"
                        onkeydown="if(event.key==='Enter') doScan()">
                    <button class="btn-scan" type="button" onclick="doScan()">
                        <i class="fas fa-barcode"></i> Scan
                    </button>
                </div>
            </div>
        </div>

        {{-- Tabel Detail Permintaan --}}
        <div class="select-section">
            <div class="section-title">Detail Permintaan — Pilih Baris</div>
            <div class="select-table-wrap">
                <table class="select-table">
                    <thead>
                        <tr>
                            <th>Merk</th>
                            <th>Jenis Kantong</th>
                            <th>Ukuran</th>
                            <th>Jml Minta</th>
                            <th>Dilayani</th>
                            <th>Sisa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="selectBody">
                        <tr><td class="empty-cell" colspan="7">Pilih No Permintaan untuk memuat item.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── KANAN ─────────────────────────────────────────── --}}
    <div class="info-panel">

        <div class="info-card">
            <div class="info-card-header">
                <i class="fas fa-tint"></i> Jenis Darah Dikirim
            </div>
            <div class="info-grid">
                <div class="info-field full">
                    <label>Merk</label>
                    <div class="info-value"><input type="text" id="infMerk" placeholder="—" readonly></div>
                </div>
                <div class="info-field full">
                    <label>Jenis</label>
                    <div class="info-value"><input type="text" id="infJenis" placeholder="—" readonly></div>
                </div>
                <div class="info-field full">
                    <label>Ukuran CC</label>
                    <div class="info-value"><input type="text" id="infUkuran" placeholder="—" readonly></div>
                </div>
                <div class="info-field">
                    <label>Jml Minta</label>
                    <div class="info-value highlight" id="infJmlMinta">0</div>
                </div>
                <div class="info-field">
                    <label>Sudah Dilayani</label>
                    <div class="info-value highlight" id="infSudah">0</div>
                </div>
                <div class="info-field">
                    <label>Scan Sesi Ini</label>
                    <div class="info-value highlight" id="infScanCount">0</div>
                </div>
                <div class="info-field">
                    <label>Sisa</label>
                    <div class="info-value highlight" id="infSisa">0</div>
                </div>
                <div class="info-field full">
                    <label>No Kantong (Hasil Scan)</label>
                    <div class="info-value" id="listKantong" style="flex-wrap:wrap;gap:6px;min-height:42px">
                        <span style="color:#999">Belum ada scan</span>
                    </div>
                </div>
            </div>
            <div style="padding: 0 18px 18px">
                <button class="btn-save" id="btnSave" type="button" onclick="savePengeluaran()" disabled>
                    <i class="fas fa-save"></i> Simpan Pengeluaran
                </button>
            </div>
        </div>

        <div class="card" style="padding:16px 18px">
            <div style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--text-muted);margin-bottom:10px">
                Ringkasan Permintaan
            </div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <div style="display:flex;justify-content:space-between;font-size:.84rem">
                    <span style="color:var(--text-muted)">Total Item Permintaan</span>
                    <strong id="sumItem">0</strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.84rem">
                    <span style="color:var(--text-muted)">Total Jumlah Minta</span>
                    <strong id="sumJumlah" style="color:var(--teal)">0</strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:.84rem">
                    <span style="color:var(--text-muted)">Sudah Dilayani</span>
                    <strong id="sumDilayani" style="color:var(--primary)">0</strong>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── RIWAYAT ──────────────────────────────────────────── --}}
<div class="history-wrap">
    <div class="history-card">
        <div class="card-header-bar">
            <h6><i class="fas fa-history"></i> Riwayat Pengeluaran</h6>
            <input type="text" id="histSearch" class="form-control"
                style="width:220px;height:34px;font-size:.82rem"
                placeholder="Cari no keluar / kantong…" oninput="filterHist()">
        </div>
        <div style="overflow-x:auto">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>No Keluar</th>
                        <th>Tgl Keluar</th>
                        <th>No Kantong</th>
                        <th>Merk</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th>Tujuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="histBody">
                    <tr>
                        <td colspan="9" style="text-align:center;padding:28px;color:var(--text-light)">
                            <i class="fas fa-circle-notch fa-spin me-2"></i> Memuat…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   STATE
   ═══════════════════════════════════════════════════════════ */
let scanHistory     = [];    // no_kantong yang di-scan sesi ini
let selectedRowData = null;  // data baris detail yang aktif
let editId          = null;  // id record yang sedang diedit

/* ═══════════════════════════════════════════════════════════
   INIT
   ═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    generateNoTransaksi();
    loadHistory();
    document.getElementById('scanInput').focus();
});

/* ═══════════════════════════════════════════════════════════
   NO TRANSAKSI — auto generate
   ═══════════════════════════════════════════════════════════ */
function generateNoTransaksi() {
    const now = new Date();
    const yy  = String(now.getFullYear()).slice(2);
    const mm  = String(now.getMonth() + 1).padStart(2, '0');
    const seq = String(Math.floor(Math.random() * 9000) + 1000);
    const no  = `G${yy}${mm}${seq}`;
    document.getElementById('noTransaksi').value            = no;
    document.getElementById('displayNoTransaksi').textContent = no;
}

/* ═══════════════════════════════════════════════════════════
   LOAD PERMINTAAN — saat pilih No Minta
   ═══════════════════════════════════════════════════════════ */
async function loadPermintaan() {
    const id = document.getElementById('noMinta').value;

    document.getElementById('selectBody').innerHTML =
        `<tr><td class="empty-cell" colspan="7">Pilih No Permintaan untuk memuat item.</td></tr>`;
    resetPanel();

    if (!id) return;

    try {
        const res  = await fetch(`{{ url('gudang/permintaan_kantong') }}/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const items = await res.json();

        renderSelectItems(Array.isArray(items) ? items : [items]);
        updateSummary(Array.isArray(items) ? items : [items]);

    } catch (e) {
        showAlert('Gagal memuat detail permintaan.', 'error');
    }
}

/* ═══════════════════════════════════════════════════════════
   RENDER TABEL DETAIL PERMINTAAN
   ═══════════════════════════════════════════════════════════ */
function renderSelectItems(items) {
    const tbody = document.getElementById('selectBody');

    if (!items.length) {
        tbody.innerHTML = `<tr><td class="empty-cell" colspan="7">Tidak ada item permintaan.</td></tr>`;
        return;
    }

    tbody.innerHTML = items.map(item => {
        const jumlah    = parseInt(item.jumlah)          || 0;
        const dilayani  = parseInt(item.jumlah_dilayani) || 0;
        const sisa      = Math.max(0, jumlah - dilayani);
        const st        = (item.status ?? 'PENDING').toUpperCase();

        // Baris selesai tidak bisa dipilih
        const disabled  = st === 'SELESAI';
        const rowStyle  = disabled ? 'opacity:.5;pointer-events:none;' : 'cursor:pointer';

        const badgeClass = st === 'SELESAI' ? 'badge-selesai'
                         : st === 'PROSES'  ? 'badge-proses'
                         : 'badge-pending';

        return `
        <tr class="row-in"
            style="${rowStyle}"
            data-id="${item.id ?? ''}"
            data-merk="${item.merk ?? ''}"
            data-jenis="${item.jenis ?? ''}"
            data-ukuran="${item.ukuran ?? ''}"
            data-jumlah="${jumlah}"
            data-dilayani="${dilayani}"
            data-status="${st}"
            onclick="${disabled ? '' : 'selectRow(this)'}">
            <td><strong>${item.merk ?? '—'}</strong></td>
            <td>${item.jenis ?? '—'}</td>
            <td>
                <span style="background:#e0f2f1;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:600">
                    ${item.ukuran ?? '—'}
                </span>
            </td>
            <td style="font-weight:700;color:var(--teal)">${jumlah}</td>
            <td style="font-weight:700;color:var(--primary)">${dilayani}</td>
            <td style="font-weight:700">${sisa}</td>
            <td><span class="badge-status ${badgeClass}">${st}</span></td>
        </tr>`;
    }).join('');
}

/* ═══════════════════════════════════════════════════════════
   RINGKASAN — hitung total dari semua baris
   ═══════════════════════════════════════════════════════════ */
function updateSummary(items) {
    const total    = items.reduce((s, i) => s + (parseInt(i.jumlah) || 0), 0);
    const dilayani = items.reduce((s, i) => s + (parseInt(i.jumlah_dilayani) || 0), 0);
    document.getElementById('sumItem').textContent     = items.length;
    document.getElementById('sumJumlah').textContent   = total;
    document.getElementById('sumDilayani').textContent = dilayani;
}

/* ═══════════════════════════════════════════════════════════
   SELECT BARIS DETAIL
   ═══════════════════════════════════════════════════════════ */
function selectRow(tr) {
    document.querySelectorAll('#selectBody tr').forEach(r => r.classList.remove('selected-row'));
    tr.classList.add('selected-row');
    tr.scrollIntoView({ block: 'nearest', behavior: 'smooth' });

    selectedRowData = {
        detailId  : tr.dataset.id,
        merk      : tr.dataset.merk,
        jenis     : tr.dataset.jenis,
        ukuran    : tr.dataset.ukuran,
        jumlah    : parseInt(tr.dataset.jumlah)    || 0,
        dilayani  : parseInt(tr.dataset.dilayani)  || 0,
    };

    // Reset scan saat ganti baris
    scanHistory = [];
    renderKantongList();

    fillPanelFromRow(selectedRowData);
    recalc();

    document.getElementById('btnSave').disabled = true;
    document.getElementById('scanInput').focus();
}

function fillPanelFromRow(d) {
    document.getElementById('infMerk').value   = d.merk   ?? '';
    document.getElementById('infJenis').value  = d.jenis  ?? '';
    document.getElementById('infUkuran').value = d.ukuran ?? '';
    document.getElementById('infJmlMinta').textContent = d.jumlah;
    document.getElementById('infSudah').textContent    = d.dilayani;
    document.getElementById('infSisa').textContent     = Math.max(0, d.jumlah - d.dilayani);
}

/* ═══════════════════════════════════════════════════════════
   REKALKUKASI sisi kanan
   ═══════════════════════════════════════════════════════════ */
function recalc() {
    if (!selectedRowData) return;

    const minta    = selectedRowData.jumlah;
    const sudahDb  = selectedRowData.dilayani;
    const scan     = scanHistory.length;
    const total    = sudahDb + scan;
    const sisa     = Math.max(0, minta - total);

    document.getElementById('infScanCount').textContent = scan;
    document.getElementById('infSudah').textContent     = total;
    document.getElementById('infSisa').textContent      = sisa;
}

/* ═══════════════════════════════════════════════════════════
   SCAN — verifikasi kantong ke server
   ═══════════════════════════════════════════════════════════ */
function doScan() {
    const raw = document.getElementById('scanInput').value.trim();

    if (!raw) {
        showAlert('No kantong tidak boleh kosong', 'error');
        return;
    }

    if (!selectedRowData) {
        showAlert('Pilih baris permintaan dahulu!', 'error');
        return;
    }

    // Cek sisa
    const sisa = Math.max(0, selectedRowData.jumlah - selectedRowData.dilayani - scanHistory.length);
    if (sisa <= 0) {
        showAlert('Permintaan sudah terpenuhi untuk baris ini!', 'error');
        return;
    }

    if (scanHistory.includes(raw)) {
        showAlert(`Kantong ${raw} sudah di-scan`, 'error');
        document.getElementById('scanInput').value = '';
        return;
    }

    fetch(`{{ route('gudang.pengeluaran_kantong.find') }}?no_kantong=${encodeURIComponent(raw)}`)
        .then(res => res.json())
        .then(data => {
            if (data.status !== 'ok') {
                showAlert(data.message || 'Kantong tidak valid', 'error');
                return;
            }

            const d = data.data;

            // Validasi: harus sesuai merk & ukuran permintaan
            const merkOk   = (d.merk   ?? '').toLowerCase() === (selectedRowData.merk   ?? '').toLowerCase();
            const ukuranOk = (d.ukuran ?? '').toLowerCase() === (selectedRowData.ukuran ?? '').toLowerCase();

            if (!merkOk || !ukuranOk) {
                showAlert(
                    `Kantong tidak sesuai! Merk: ${d.merk} / Ukuran: ${d.ukuran} — dibutuhkan ${selectedRowData.merk} / ${selectedRowData.ukuran}`,
                    'error'
                );
                return;
            }

            scanHistory.push(d.no_kantong);
            renderKantongList();
            recalc();

            document.getElementById('btnSave').disabled = false;
            showAlert(`✓ Scan berhasil: ${d.no_kantong}`, 'success');
        })
        .catch(() => showAlert('Koneksi error saat scan', 'error'))
        .finally(() => {
            document.getElementById('scanInput').value = '';
            document.getElementById('scanInput').focus();
        });
}

/* ═══════════════════════════════════════════════════════════
   RENDER KANTONG TAGS
   ═══════════════════════════════════════════════════════════ */
function renderKantongList() {
    const wrap = document.getElementById('listKantong');

    if (!scanHistory.length) {
        wrap.innerHTML = '<span style="color:#999">Belum ada scan</span>';
        return;
    }

    wrap.innerHTML = scanHistory.map(k => `
        <span style="
            background:#0d9488;color:#fff;
            padding:4px 8px;border-radius:6px;
            font-size:.75rem;font-family:monospace;
        ">${k}
            <span onclick="removeKantong('${k}')"
                style="margin-left:6px;cursor:pointer;opacity:.7">✕</span>
        </span>
    `).join('');
}

function removeKantong(no) {
    scanHistory = scanHistory.filter(k => k !== no);
    renderKantongList();
    recalc();
    if (!scanHistory.length) {
        document.getElementById('btnSave').disabled = true;
    }
}

/* ═══════════════════════════════════════════════════════════
   SIMPAN
   ═══════════════════════════════════════════════════════════ */
async function savePengeluaran() {
    if (!scanHistory.length) {
        showAlert('Scan no kantong terlebih dahulu', 'error');
        return;
    }
    if (!document.getElementById('noMinta').value) {
        showAlert('Pilih No Permintaan terlebih dahulu', 'error');
        return;
    }
    if (!selectedRowData?.detailId) {
        showAlert('Pilih baris detail permintaan terlebih dahulu', 'error');
        return;
    }

    const btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner"></span> Menyimpan…`;

    const payload = {
        id          : editId,
        no_keluar   : document.getElementById('noTransaksi').value,
        tgl_keluar  : document.getElementById('tanggalTransaksi').value,
        no_kantong  : scanHistory,
        no_minta    : document.getElementById('noMinta').value,
        detail_id   : selectedRowData.detailId,   // ← wajib untuk update status
        merk        : selectedRowData.merk,
        jenis       : selectedRowData.jenis,
        ukuran      : selectedRowData.ukuran,
        tujuan      : 'Pengeluaran',
    };

    try {
        const res  = await fetch('{{ route("gudang.pengeluaran_kantong.save") }}', {
            method : 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            showAlert('Pengeluaran berhasil disimpan!', 'success');
            cancelEdit();             // reset state & form
            await loadHistory();
            await loadPermintaan();   // refresh tabel detail (status terupdate)
        } else {
            showAlert(data.message || 'Terjadi kesalahan.', 'error');
            btn.disabled = false;
        }
    } catch (e) {
        showAlert('Koneksi error.', 'error');
        btn.disabled = false;
    }

    btn.innerHTML = `<i class="fas fa-save"></i> Simpan Pengeluaran`;
}

/* ═══════════════════════════════════════════════════════════
   EDIT
   ═══════════════════════════════════════════════════════════ */
async function editData(id) {
    try {
        const res  = await fetch(`{{ url('gudang/pengeluaran_kantong/get') }}/${id}`);

        if (!res.ok) throw new Error('Data tidak ditemukan');

        const d = await res.json();

        // Isi form header
        document.getElementById('noTransaksi').value       = d.no_keluar  ?? '';
        document.getElementById('tanggalTransaksi').value  = d.tgl_keluar ?? '';
        document.getElementById('infMerk').value           = d.merk       ?? '';
        document.getElementById('infJenis').value          = d.jenis      ?? '';
        document.getElementById('infUkuran').value         = d.ukuran     ?? '';

        // Set permintaan jika ada
        if (d.permintaan_kantong_id) {
            document.getElementById('noMinta').value = d.permintaan_kantong_id;
            await loadPermintaan();

            // Highlight baris detail yang sesuai
            if (d.detail_id) {
                const tr = document.querySelector(`#selectBody tr[data-id="${d.detail_id}"]`);
                if (tr) selectRow(tr);
            }
        }

        // Mode edit
        editId = id;
        scanHistory = [d.no_kantong]; // isi dengan kantong lama
        renderKantongList();
        recalc();

        document.getElementById('editBadge').classList.add('visible');
        document.getElementById('btnCancelEdit').style.display = 'inline-block';
        document.getElementById('btnSave').disabled = false;

        showAlert(`Mode edit aktif — No Kantong: ${d.no_kantong}`, 'success');
        window.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (err) {
        showAlert(err.message, 'error');
    }
}

function cancelEdit() {
    editId = null;
    resetPanel();
    clearTableSelection();
    generateNoTransaksi();
    document.getElementById('noMinta').value = '';
    document.getElementById('selectBody').innerHTML =
        `<tr><td class="empty-cell" colspan="7">Pilih No Permintaan untuk memuat item.</td></tr>`;
    document.getElementById('editBadge').classList.remove('visible');
    document.getElementById('btnCancelEdit').style.display = 'none';

    // Reset ringkasan
    ['sumItem','sumJumlah','sumDilayani'].forEach(id => {
        document.getElementById(id).textContent = '0';
    });
}

/* ═══════════════════════════════════════════════════════════
   DELETE
   ═══════════════════════════════════════════════════════════ */
async function deleteData(id) {
    if (!confirm('Yakin hapus data ini? Stok kantong akan dikembalikan.')) return;

    try {
        const res  = await fetch(`{{ url('gudang/pengeluaran_kantong/delete') }}/${id}`, {
            method : 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        const data = await res.json();

        if (data.success) {
            showAlert('Data berhasil dihapus', 'success');
            loadHistory();
            // Refresh tabel permintaan jika sedang tampil
            if (document.getElementById('noMinta').value) loadPermintaan();
        } else {
            showAlert(data.message, 'error');
        }
    } catch {
        showAlert('Gagal menghapus data', 'error');
    }
}

/* ═══════════════════════════════════════════════════════════
   RESET PANEL KANAN
   ═══════════════════════════════════════════════════════════ */
function resetPanel() {
    scanHistory     = [];
    selectedRowData = null;

    ['infMerk','infJenis','infUkuran'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    ['infJmlMinta','infSudah','infSisa','infScanCount'].forEach(id => {
        document.getElementById(id).textContent = '0';
    });

    document.getElementById('listKantong').innerHTML =
        '<span style="color:#999">Belum ada scan</span>';
    document.getElementById('scanInput').value = '';
    document.getElementById('btnSave').disabled = true;
}

function clearTableSelection() {
    document.querySelectorAll('#selectBody tr').forEach(r => r.classList.remove('selected-row'));
}

/* ═══════════════════════════════════════════════════════════
   RIWAYAT
   ═══════════════════════════════════════════════════════════ */
async function loadHistory() {
    try {
        const res  = await fetch('{{ route("gudang.pengeluaran_kantong.list") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();
        renderHistory(data.data ?? data);
    } catch (e) {
        document.getElementById('histBody').innerHTML =
            `<tr><td colspan="9" style="text-align:center;padding:24px;color:var(--text-light)">Gagal memuat riwayat.</td></tr>`;
    }
}

function renderHistory(rows) {
    const tbody = document.getElementById('histBody');

    if (!rows?.length) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;padding:28px;color:var(--text-light)">Belum ada data pengeluaran.</td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map(r => `
        <tr>
            <td><span style="font-weight:700;color:var(--teal);font-size:.82rem">${r.no_keluar ?? '—'}</span></td>
            <td style="color:var(--text-muted);font-size:.82rem">${r.tgl_keluar ?? r.created_at?.split('T')[0] ?? '—'}</td>
            <td style="font-family:monospace;font-weight:700">${r.no_kantong ?? '—'}</td>
            <td>${r.merk ?? '—'}</td>
            <td>${r.jenis ?? '—'}</td>
            <td>
                <span style="background:#f0f0f0;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:600">
                    ${r.ukuran ?? '—'}
                </span>
            </td>
            <td>${r.tujuan ?? '—'}</td>
            <td style="color:var(--text-muted);font-size:.8rem">${r.keterangan ?? '—'}</td>
            <td style="white-space:nowrap">
                <button class="btn-icon edit" onclick="editData(${r.id})" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="btn-icon del" onclick="deleteData(${r.id})" title="Hapus">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function filterHist() {
    const q = document.getElementById('histSearch').value.toLowerCase();
    document.querySelectorAll('#histBody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

/* ═══════════════════════════════════════════════════════════
   ALERT
   ═══════════════════════════════════════════════════════════ */
function showAlert(msg, type) {
    const wrap = document.getElementById('alertBar');
    wrap.style.display = 'block';
    wrap.innerHTML = `
        <div class="alert-bar alert-${type}">
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i> ${msg}
        </div>`;
    clearTimeout(wrap._t);
    wrap._t = setTimeout(() => { wrap.style.display = 'none'; }, 3500);
}
</script>
@endpush