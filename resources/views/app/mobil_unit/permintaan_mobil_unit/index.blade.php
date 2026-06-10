@extends('layouts.index')

@section('title', 'Permintaan Mobile Unit')

@push('styles')
<style>
    :root {
        --primary: #c0392b;
        --primary-dark: #96281b;
        --primary-light: #fadbd8;
        --accent: #e74c3c;
        --surface: #ffffff;
        --surface-2: #f8f9fa;
        --surface-3: #f1f3f4;
        --border: #e0e3e7;
        --text: #1a1d23;
        --text-muted: #6b7280;
        --text-light: #9ca3af;
        --shadow-sm: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 12px rgba(0,0,0,.10), 0 2px 4px rgba(0,0,0,.05);
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
    .page-header h4 {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--text);
        margin: 0;
    }
    .page-header .breadcrumb {
        font-size: .78rem;
        color: var(--text-muted);
        margin: 0;
    }

    .main-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin: 24px;
        overflow: hidden;
    }

    .card-header-bar {
        padding: 18px 22px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface);
    }
    .card-header-bar h6 {
        font-weight: 700;
        font-size: .9rem;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-header-bar h6 i { color: var(--primary); font-size: .85rem; }

    /* Form Section */
    .form-section {
        padding: 22px;
        background: linear-gradient(135deg, #fef9f9 0%, #fff5f5 100%);
        border-bottom: 1px solid var(--border);
    }
    .form-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
    .form-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px 24px; }
    .form-group  { display: flex; flex-direction: column; gap: 5px; }
    .form-label  {
        font-size: .73rem; font-weight: 700;
        color: var(--text-muted); text-transform: uppercase; letter-spacing: .06em;
    }
    .form-control, .form-select {
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 9px 12px;
        font-size: .87rem;
        color: var(--text);
        background: var(--surface);
        transition: border-color .18s, box-shadow .18s;
        height: 40px;
        outline: none;
        width: 100%;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(192,57,43,.12);
    }
    .form-control[readonly] {
        background: var(--surface-3);
        color: var(--text-muted);
        cursor: not-allowed;
    }
    textarea.form-control { height: auto; resize: vertical; }

    /* Qty */
    .qty-group {
        display: flex; align-items: center;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden; height: 40px; background: var(--surface);
    }
    .qty-group:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(192,57,43,.12); }
    .qty-btn {
        width: 36px; height: 38px; border: none;
        background: var(--surface-3); color: var(--text);
        font-size: 1rem; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s, color .15s;
        flex-shrink: 0; font-weight: 700; user-select: none;
    }
    .qty-btn:hover { background: var(--primary); color: #fff; }
    .qty-input {
        width: 60px; text-align: center; border: none; outline: none;
        font-size: .9rem; font-weight: 600; color: var(--text); background: transparent;
    }

    /* Buttons */
    .btn-add {
        background: var(--primary); color: #fff;
        border: none; border-radius: var(--radius-sm);
        padding: 0 18px; height: 40px; font-size: .85rem; font-weight: 700;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: background .18s, transform .1s, box-shadow .18s;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(192,57,43,.25);
    }
    .btn-add:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-add:disabled { background: var(--text-light); box-shadow: none; cursor: not-allowed; transform: none; }

    .btn-save {
        background: var(--primary); color: #fff;
        border: none; border-radius: var(--radius-sm);
        padding: 10px 24px; font-size: .88rem; font-weight: 700;
        cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        transition: background .18s, transform .1s, box-shadow .18s;
        box-shadow: 0 2px 10px rgba(192,57,43,.3);
    }
    .btn-save:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-save:disabled { background: #ccc; box-shadow: none; cursor: not-allowed; transform: none; }

    .btn-outline {
        background: transparent; color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 9px 16px; font-size: .85rem; font-weight: 600;
        cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        transition: all .18s;
    }
    .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

    /* Detail Items Table (input rows) */
    .detail-section { padding: 22px; border-bottom: 1px solid var(--border); }
    .detail-section .section-title {
        font-size: .73rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: var(--text-muted); margin-bottom: 12px;
        display: flex; align-items: center; gap: 8px;
    }
    .detail-table-wrap { overflow-x: auto; }
    table.detail-input-table {
        width: 100%; border-collapse: collapse; font-size: .83rem; min-width: 700px;
    }
    table.detail-input-table thead tr { background: #f8f0f0; border-bottom: 2px solid #e8d0d0; }
    table.detail-input-table thead th {
        padding: 9px 10px; font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--primary-dark); white-space: nowrap;
    }
    table.detail-input-table tbody tr { border-bottom: 1px solid var(--border); }
    table.detail-input-table tbody td { padding: 7px 8px; vertical-align: middle; }
    table.detail-input-table .form-control,
    table.detail-input-table .form-select { height: 34px; padding: 5px 8px; font-size: .82rem; }
    table.detail-input-table .qty-group   { height: 34px; }
    table.detail-input-table .qty-btn     { height: 32px; width: 28px; }
    table.detail-input-table .qty-input   { width: 44px; font-size: .82rem; }

    .btn-delete-detail {
        width: 28px; height: 28px; border: none;
        background: transparent; color: var(--text-light);
        border-radius: 4px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background .15s, color .15s; font-size: .85rem;
    }
    .btn-delete-detail:hover { background: #fee2e2; color: var(--primary); }

    /* Main items table (history) */
    .table-section { padding: 0; }
    .table-wrap { overflow-x: auto; }
    table.items-table { width: 100%; border-collapse: collapse; font-size: .85rem; }
    table.items-table thead tr { background: #f8f0f0; border-bottom: 2px solid #e8d0d0; }
    table.items-table thead th {
        padding: 12px 16px; font-size: .72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .06em;
        color: var(--primary-dark); text-align: left; white-space: nowrap;
    }
    table.items-table tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
    table.items-table tbody tr:hover { background: #fef9f9; }
    table.items-table tbody td { padding: 12px 16px; color: var(--text); vertical-align: middle; }
    table.items-table tbody tr.empty-row td {
        text-align: center; padding: 40px; color: var(--text-light);
    }
    table.items-table tbody tr.empty-row .empty-icon {
        font-size: 2rem; display: block; margin-bottom: 8px; opacity: .4;
    }

    .badge-status {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: .72rem; font-weight: 700; letter-spacing: .03em;
    }
    .badge-draft      { background: #e5e7eb; color: #374151; }
    .badge-diajukan   { background: #fef3cd; color: #856404; }
    .badge-diverifikasi { background: #dbeafe; color: #1e40af; }
    .badge-selesai    { background: #d1fae5; color: #065f46; }

    /* Footer */
    .card-footer-bar {
        padding: 16px 22px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        background: var(--surface-2);
    }
    .item-count { font-size: .82rem; color: var(--text-muted); font-weight: 600; }
    .item-count span { color: var(--primary); font-weight: 800; }

    .no-badge {
        display: inline-block; font-weight: 700; font-size: .85rem;
        color: var(--primary); background: var(--primary-light);
        padding: 4px 12px; border-radius: 20px; letter-spacing: .02em;
    }

    .alert-success-bar {
        padding: 12px 22px; background: #d1fae5;
        border-bottom: 1px solid #a7f3d0;
        display: flex; align-items: center; gap: 10px;
        font-size: .85rem; font-weight: 600; color: #065f46;
    }

    .history-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin: 0 24px 24px;
        overflow: hidden;
    }

    .spinner {
        display: inline-block; width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,.4); border-top-color: #fff;
        border-radius: 50%; animation: spin .6s linear infinite; margin-right: 4px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    @keyframes rowIn {
        from { opacity: 0; transform: translateX(-12px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .row-new { animation: rowIn .3s ease; }

    .section-divider {
        padding: 0 0 16px;
        display: flex; align-items: center; gap: 12px;
    }
    .section-divider span {
        font-size: .72rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .08em; color: var(--text-light); white-space: nowrap;
    }
    .section-divider hr { border: none; border-top: 1px solid var(--border); flex: 1; }

    @media (max-width: 768px) {
        .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
        .main-card, .history-card { margin: 12px; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h4><i class="fas fa-ambulance me-2" style="color:var(--primary)"></i>Permintaan Mobile Unit</h4>
        <div class="breadcrumb">Unit / Mobile Unit / Permintaan</div>
    </div>
</div>

<div class="main-card mt-0" style="margin-top: 20px !important;">

    {{-- Alert --}}
    <div id="alertSuccess" class="alert-success-bar" style="display:none">
        <i class="fas fa-check-circle"></i>
        <span id="alertMsg">Data berhasil disimpan.</span>
    </div>

    {{-- Header --}}
    <div class="card-header-bar">
        <h6><i class="fas fa-plus-circle"></i> Form Permintaan Baru</h6>
        <span class="no-badge" id="displayNoPermintaan">—</span>
    </div>

    {{-- Form Section: Informasi Umum --}}
    <div class="form-section">
        <input type="hidden" id="id_edit">

        {{-- Baris 1: Nomor & Tanggal --}}
        <div class="form-grid mb-3">
            <div class="form-group">
                <label class="form-label">No Permintaan</label>
                <input type="text" id="noPermintaan" class="form-control" readonly placeholder="Auto generate…">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal</label>
                <input type="date" id="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
        </div>

        {{-- Baris 2: Bagian, Petugas, Verifikator --}}
        <div class="form-grid-3 mb-3">
            <div class="form-group">
                <label class="form-label">Bagian Petugas</label>
                <select id="bagianPetugas" class="form-select">
                    <option value="">— Pilih Bagian —</option>
                    @foreach(\App\Models\BagianPetugas::orderBy('nama')->get() as $bp)
                        <option value="{{ $bp->id }}">{{ $bp->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Petugas</label>
                <select id="petugas" class="form-select">
                    <option value="">— Pilih Petugas —</option>
                    @foreach(\App\Models\Petugas::orderBy('nama')->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Verifikator</label>
                <select id="verifikator" class="form-select">
                    <option value="">— Belum Ditentukan —</option>
                    @foreach(\App\Models\Petugas::orderBy('nama')->get() as $p)
                        <option value="{{ $p->id }}">{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Baris 3: Status & Keterangan --}}
        <div class="form-grid mb-3">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select id="flag" class="form-select">
                    <option value="0">Draft</option>
                    <option value="1">Diajukan</option>
                    <option value="2">Diverifikasi</option>
                    <option value="3">Selesai</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <input type="text" id="keterangan" class="form-control" placeholder="Catatan tambahan…">
            </div>
        </div>

        {{-- Divider Detail Items --}}
        <div class="section-divider">
            <span>Detail Item</span><hr>
        </div>

        {{-- Input Row Tambah Item --}}
        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr auto; gap:12px; align-items:end;" class="mb-3">
            <div class="form-group">
                <label class="form-label">Tipe Kantong</label>
                <select id="tipeKantong" class="form-select">
                    <option value="">— Pilih —</option>
                    @foreach(\App\Models\TipeKantong::orderBy('nama')->get() as $tk)
                        <option value="{{ $tk->id }}" data-nama="{{ $tk->nama }}">{{ $tk->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Merk Kantong</label>
                <select id="detailMerk" class="form-select">
                    <option value="">— Pilih Merk —</option>
                    @foreach(\App\Models\PermintaanAftap::MERK_KANTONG as $merk)
                        <option value="{{ $merk }}">{{ $merk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Kantong</label>
                <select id="detailJenis" class="form-select">
                    <option value="">— Pilih Jenis —</option>
                    @foreach(\App\Models\PermintaanAftap::JENIS_KANTONG as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ukuran</label>
                <select id="detailUkuran" class="form-select">
                    @foreach(\App\Models\PermintaanAftap::UKURAN as $ukuran)
                        <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah</label>
                <div class="qty-group">
                    <button class="qty-btn" onclick="changeQty(-1)" type="button">−</button>
                    <input class="qty-input" type="number" id="detailJumlah" value="1" min="1">
                    <button class="qty-btn" onclick="changeQty(1)"  type="button">+</button>
                </div>
            </div>
            <div>
                <button class="btn-add" type="button" onclick="addDetail()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>

        {{-- Tabel detail items yang sudah ditambah --}}
        <div class="detail-table-wrap">
            <table class="detail-input-table">
                <thead>
                    <tr>
                        <th width="32">#</th>
                        <th>Tipe Kantong</th>
                        <th>Merk</th>
                        <th>Jenis</th>
                        <th>Ukuran</th>
                        <th width="80">Jumlah</th>
                        <th width="40"></th>
                    </tr>
                </thead>
                <tbody id="detailBody">
                    <tr id="emptyDetailRow">
                        <td colspan="7" style="text-align:center;padding:20px;color:var(--text-light)">
                            Belum ada item detail. Tambahkan di atas.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer --}}
    <div class="card-footer-bar">
        <div class="item-count">Total: <span id="itemCount">0</span> item</div>
        <div style="display:flex;gap:10px">
            <button class="btn-outline" type="button" onclick="resetForm()">
                <i class="fas fa-undo"></i> Reset
            </button>
            <button class="btn-save" id="btnSave" type="button" onclick="savePermintaan()" disabled>
                <i class="fas fa-save"></i> Simpan Permintaan
            </button>
        </div>
    </div>
</div>

{{-- Riwayat --}}
<div class="history-card">
    <div class="card-header-bar">
        <h6><i class="fas fa-history"></i> Riwayat Permintaan</h6>
        <div style="display:flex;gap:8px;align-items:center">
            <input type="text" id="searchInput" class="form-control"
                style="width:220px;height:34px;font-size:.83rem"
                placeholder="Cari nomor / bagian…" oninput="filterHistory()">
        </div>
    </div>
    <div class="table-wrap">
        <table class="items-table" id="historyTable">
            <thead>
                <tr>
                    <th>No Permintaan</th>
                    <th>Tanggal</th>
                    <th>Bagian</th>
                    <th>Petugas</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th width="90">Aksi</th>
                </tr>
            </thead>
            <tbody id="historyBody">
                <tr>
                    <td colspan="7" style="text-align:center;padding:32px;color:var(--text-light)">
                        <i class="fas fa-circle-notch fa-spin me-2"></i> Memuat data…
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ─── State ──────────────────────────────────────────────────────────────────
let details    = [];   // item detail yang akan disimpan
let noPermintaan = '';

// ─── Init ────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fetchNoPermintaan();
    loadHistory();
});

// ─── Auto No ─────────────────────────────────────────────────────────────────
async function fetchNoPermintaan() {
    try {
        const res  = await fetch('{{ route("mobil_unit.permintaan_mobil_unit.generate_nomor") }}', {
            headers: { "Accept": "application/json" }
        });
        const json = await res.json();
        noPermintaan = json.no;
        document.getElementById('noPermintaan').value          = noPermintaan;
        document.getElementById('displayNoPermintaan').textContent = noPermintaan;
    } catch (e) {
        console.error(e);
    }
}

// ─── Qty ─────────────────────────────────────────────────────────────────────
function changeQty(delta) {
    const el = document.getElementById('detailJumlah');
    el.value = Math.max(1, (parseInt(el.value) || 1) + delta);
}

// ─── Tambah Detail Item ───────────────────────────────────────────────────────
function addDetail() {
    const tipeEl   = document.getElementById('tipeKantong');
    const tipeId   = tipeEl.value;
    const tipeNama = tipeEl.options[tipeEl.selectedIndex]?.dataset.nama ?? '';
    const merk     = document.getElementById('detailMerk').value;
    const jenis    = document.getElementById('detailJenis').value;
    const ukuran   = document.getElementById('detailUkuran').value;
    const jumlah   = parseInt(document.getElementById('detailJumlah').value) || 1;

    if (!tipeId)  { showFlash('Tipe kantong wajib dipilih', 'error'); return; }
    if (!merk)    { showFlash('Merk wajib dipilih', 'error'); return; }
    if (!jenis)   { showFlash('Jenis kantong wajib dipilih', 'error'); return; }

    details.push({ tipe_kantong_id: tipeId, tipe_kantong_nama: tipeNama, merk, jenis, ukuran, jumlah });
    renderDetailTable();

    // reset field tambah
    document.getElementById('tipeKantong').value  = '';
    document.getElementById('detailMerk').value   = '';
    document.getElementById('detailJenis').value  = '';
    // ukuran: kembalikan ke pilihan pertama (bukan kosong, karena tidak ada option kosong)
    document.getElementById('detailUkuran').selectedIndex = 0;
    document.getElementById('detailJumlah').value = 1;
}

// ─── Render Tabel Detail ──────────────────────────────────────────────────────
function renderDetailTable() {
    const tbody   = document.getElementById('detailBody');
    const btnSave = document.getElementById('btnSave');
    document.getElementById('itemCount').textContent = details.length;

    if (details.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyDetailRow">
                <td colspan="7" style="text-align:center;padding:20px;color:var(--text-light)">
                    Belum ada item detail. Tambahkan di atas.
                </td>
            </tr>`;
        btnSave.disabled = true;
        return;
    }

    btnSave.disabled = false;
    tbody.innerHTML = details.map((d, i) => `
        <tr class="row-new">
            <td style="color:var(--text-muted);font-weight:700">${i + 1}</td>
            <td><strong>${d.tipe_kantong_nama || '—'}</strong></td>
            <td>${d.merk || '<span style="color:var(--text-light);font-style:italic">—</span>'}</td>
            <td>${d.jenis || '<span style="color:var(--text-light);font-style:italic">—</span>'}</td>
            <td><span style="background:#f0f0f0;padding:2px 8px;border-radius:4px;font-size:.8rem;font-weight:600">${d.ukuran || '—'}</span></td>
            <td><span style="font-weight:800;font-size:1rem;color:var(--primary)">${d.jumlah}</span></td>
            <td>
                <button class="btn-delete-detail" onclick="removeDetail(${i})" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function removeDetail(idx) {
    details.splice(idx, 1);
    renderDetailTable();
}

// ─── Save ─────────────────────────────────────────────────────────────────────
async function savePermintaan() {
    if (details.length === 0) return;
    if (!noPermintaan) { showFlash('No permintaan kosong!', 'error'); return; }

    const idEdit = document.getElementById('id_edit').value;
    const btn    = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner"></span> Menyimpan…`;

    let url    = '{{ route("mobil_unit.permintaan_mobil_unit.store") }}';
    let method = 'POST';

    if (idEdit && idEdit !== '') {
        url    = `{{ url('mobil_unit/permintaan_mobil_unit') }}/${idEdit}`;
        method = 'PUT';
    }

    try {
        const res = await fetch(url, {
            method,
            headers: {
                "Content-Type" : "application/json",
                "X-CSRF-TOKEN" : document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                nomor              : noPermintaan,
                tanggal            : document.getElementById('tanggal').value,
                bagian_petugas_id  : document.getElementById('bagianPetugas').value  || null,
                petugas_id         : document.getElementById('petugas').value         || null,
                verifikator_id     : document.getElementById('verifikator').value     || null,
                flag               : document.getElementById('flag').value,
                keterangan         : document.getElementById('keterangan').value,
                details,
            })
        });

        const data = await res.json();

        if (res.ok) {
            showFlash(idEdit ? 'Permintaan berhasil diperbarui!' : 'Permintaan berhasil disimpan!', 'success');
            details = [];
            renderDetailTable();
            loadHistory();
            fetchNoPermintaan();
            document.getElementById('id_edit').value = '';
            // reset header fields
            document.getElementById('bagianPetugas').value = '';
            document.getElementById('petugas').value       = '';
            document.getElementById('verifikator').value   = '';
            document.getElementById('flag').value          = '0';
            document.getElementById('keterangan').value    = '';
        } else {
            showFlash(data.message || 'Terjadi kesalahan saat menyimpan.', 'error');
        }
    } catch (e) {
        console.error(e);
        showFlash('Koneksi error. Coba lagi.', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = `<i class="fas fa-save"></i> Simpan Permintaan`;
}

// ─── Reset ────────────────────────────────────────────────────────────────────
function resetForm() {
    if (details.length > 0 && !confirm('Reset form? Semua item akan dihapus.')) return;
    details = [];
    renderDetailTable();
    document.getElementById('bagianPetugas').value = '';
    document.getElementById('petugas').value       = '';
    document.getElementById('verifikator').value   = '';
    document.getElementById('flag').value          = '0';
    document.getElementById('keterangan').value    = '';
    document.getElementById('tipeKantong').value   = '';
    document.getElementById('detailMerk').value    = '';
    document.getElementById('detailJenis').value   = '';
    document.getElementById('detailUkuran').selectedIndex = 0;
    document.getElementById('detailJumlah').value  = 1;
    document.getElementById('tanggal').value       = new Date().toISOString().split('T')[0];
    fetchNoPermintaan();
}

// ─── History ──────────────────────────────────────────────────────────────────
async function loadHistory() {
    try {
        const res  = await fetch('{{ route("mobil_unit.permintaan_mobil_unit.index") }}?t=' + Date.now(), {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        });
        const json = await res.json();
        renderHistory(json.data ?? []);
    } catch (e) {
        document.getElementById('historyBody').innerHTML = `
            <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text-light)">
                Gagal memuat data…
            </td></tr>`;
    }
}

function formatTanggal(tgl) {
    if (!tgl) return '—';
    return new Date(tgl).toLocaleDateString('id-ID');
}

function renderHistory(rows) {
    const tbody = document.getElementById('historyBody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada data permintaan.</td></tr>`;
        return;
    }

    const flagMap = {
        0: { cls: 'badge-draft',        label: 'Draft'        },
        1: { cls: 'badge-diajukan',     label: 'Diajukan'     },
        2: { cls: 'badge-diverifikasi', label: 'Diverifikasi' },
        3: { cls: 'badge-selesai',      label: 'Selesai'      },
    };

    tbody.innerHTML = rows.map(r => {
        const f = flagMap[r.flag] ?? flagMap[0];
        return `
        <tr>
            <td><span class="no-badge" style="font-size:.78rem">${r.nomor ?? '—'}</span></td>
            <td style="color:var(--text-muted);font-size:.82rem">${formatTanggal(r.tanggal)}</td>
            <td>${r.bagian_petugas ?? '—'}</td>
            <td>${r.petugas ?? '—'}</td>
            <td><span style="background:#e0e7ff;color:#3730a3;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:700">
                ${r.details_count ?? (r.details ? r.details.length : 0)} item
            </span></td>
            <td><span class="badge-status ${f.cls}">${f.label}</span></td>
            <td>
                <button onclick="editData('${r.id ?? ''}')"
                    class="btn-outline" style="padding:5px 10px;font-size:.75rem">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteData(${r.id})"
                    class="btn-outline" style="padding:5px 10px;font-size:.75rem;color:#b91c1c;border-color:#b91c1c;margin-left:4px">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

// ─── Edit ─────────────────────────────────────────────────────────────────────
async function editData(id) {
    if (!id) { showFlash('ID tidak valid!', 'error'); return; }

    try {
        const res  = await fetch(`{{ url('mobil_unit/permintaan_mobil_unit') }}/${id}/edit`, {
            headers: { "Accept": "application/json" }
        });
        const json = await res.json();

        if (!json.success) { showFlash('Gagal memuat data!', 'error'); return; }

        const d = json.data;

        // isi header
        noPermintaan = d.nomor;
        document.getElementById('noPermintaan').value              = d.nomor;
        document.getElementById('displayNoPermintaan').textContent = d.nomor;
        document.getElementById('tanggal').value                   = d.tanggal ?? new Date().toISOString().split('T')[0];
        document.getElementById('bagianPetugas').value             = d.bagian_petugas_id ?? '';
        document.getElementById('petugas').value                   = d.petugas_id ?? '';
        document.getElementById('verifikator').value               = d.verifikator_id ?? '';
        document.getElementById('flag').value                      = d.flag ?? 0;
        document.getElementById('keterangan').value                = d.keterangan ?? '';
        document.getElementById('id_edit').value                   = d.id;

        // isi details
        details = (d.details ?? []).map(item => ({
            tipe_kantong_id  : item.tipe_kantong_id,
            tipe_kantong_nama: item.tipe_kantong ?? '',
            merk             : item.merk   ?? '',
            jenis            : item.jenis  ?? '',
            ukuran           : item.ukuran ?? '',
            jumlah           : parseInt(item.jumlah) || 1,
        }));
        renderDetailTable();

        document.getElementById('btnSave').disabled = false;
        window.scrollTo({ top: 0, behavior: 'smooth' });
        showFlash('Mode edit aktif', 'success');

    } catch (e) {
        console.error(e);
        showFlash('Error mengambil data!', 'error');
    }
}

// ─── Delete ───────────────────────────────────────────────────────────────────
function deleteData(id) {
    if (!confirm('Yakin ingin menghapus permintaan ini?')) return;

    fetch(`{{ url('mobil_unit/permintaan_mobil_unit') }}/${id}`, {
        method : 'DELETE',
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept"      : "application/json"
        }
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (res.ok) {
            showFlash('Data berhasil dihapus', 'success');
            loadHistory();
        } else {
            showFlash(data.message || 'Gagal menghapus', 'error');
        }
    })
    .catch(() => showFlash('Server error', 'error'));
}

// ─── Filter History ───────────────────────────────────────────────────────────
function filterHistory() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#historyBody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ─── Flash ────────────────────────────────────────────────────────────────────
function showFlash(msg, type) {
    const el  = document.getElementById('alertSuccess');
    const txt = document.getElementById('alertMsg');
    el.style.display          = 'flex';
    txt.textContent           = msg;
    el.style.background       = type === 'error' ? '#fee2e2' : '#d1fae5';
    el.style.color            = type === 'error' ? '#991b1b' : '#065f46';
    el.style.borderBottomColor= type === 'error' ? '#fca5a5' : '#a7f3d0';
    clearTimeout(el._t);
    el._t = setTimeout(() => el.style.display = 'none', 3500);
}
</script>
@endpush