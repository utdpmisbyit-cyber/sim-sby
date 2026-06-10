@extends('layouts.index')

@section('title', 'Permintaan Kantong Aftap')

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
        --shadow-lg: 0 10px 30px rgba(0,0,0,.12);
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
        letter-spacing: -.01em;
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

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 24px;
    }
    .form-grid-4 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr auto;
        gap: 16px;
        align-items: end;
    }

    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-label {
        font-size: .73rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
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

    /* Quantity Controls */
    .qty-group {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        height: 40px;
        background: var(--surface);
    }
    .qty-group:focus-within { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(192,57,43,.12); }
    .qty-btn {
        width: 36px;
        height: 38px;
        border: none;
        background: var(--surface-3);
        color: var(--text);
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s, color .15s;
        flex-shrink: 0;
        font-weight: 700;
        user-select: none;
    }
    .qty-btn:hover { background: var(--primary); color: #fff; }
    .qty-input {
        width: 60px;
        text-align: center;
        border: none;
        outline: none;
        font-size: .9rem;
        font-weight: 600;
        color: var(--text);
        background: transparent;
    }

    /* Buttons */
    .btn-add {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 0 18px;
        height: 40px;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background .18s, transform .1s, box-shadow .18s;
        white-space: nowrap;
        letter-spacing: .01em;
        box-shadow: 0 2px 8px rgba(192,57,43,.25);
    }
    .btn-add:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(192,57,43,.35); }
    .btn-add:active { transform: translateY(0); }
    .btn-add:disabled { background: var(--text-light); box-shadow: none; cursor: not-allowed; transform: none; }

    .btn-save {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: var(--radius-sm);
        padding: 10px 24px;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background .18s, transform .1s, box-shadow .18s;
        letter-spacing: .01em;
        box-shadow: 0 2px 10px rgba(192,57,43,.3);
    }
    .btn-save:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-save:disabled { background: #ccc; box-shadow: none; cursor: not-allowed; transform: none; }

    .btn-outline {
        background: transparent;
        color: var(--text-muted);
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 9px 16px;
        font-size: .85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .18s;
    }
    .btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

    /* Table */
    .table-section { padding: 0; }
    .table-wrap { overflow-x: auto; }

    table.items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .85rem;
    }
    table.items-table thead tr {
        background: #f8f0f0;
        border-bottom: 2px solid #e8d0d0;
    }
    table.items-table thead th {
        padding: 12px 16px;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--primary-dark);
        text-align: left;
        white-space: nowrap;
    }
    table.items-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .12s;
    }
    table.items-table tbody tr:hover { background: #fef9f9; }
    table.items-table tbody td {
        padding: 12px 16px;
        color: var(--text);
        vertical-align: middle;
    }
    table.items-table tbody tr.empty-row td {
        text-align: center;
        padding: 40px;
        color: var(--text-light);
    }
    table.items-table tbody tr.empty-row .empty-icon {
        font-size: 2rem;
        display: block;
        margin-bottom: 8px;
        opacity: .4;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .03em;
    }
    .badge-pending { background: #fef3cd; color: #856404; }
    .badge-accepted { background: #d1fae5; color: #065f46; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }

    .btn-delete-row {
        width: 28px; height: 28px;
        border: none;
        background: transparent;
        color: var(--text-light);
        border-radius: 4px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background .15s, color .15s;
        font-size: .85rem;
    }
    .btn-delete-row:hover { background: #fee2e2; color: var(--primary); }

    /* Footer actions */
    .card-footer-bar {
        padding: 16px 22px;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--surface-2);
    }
    .item-count {
        font-size: .82rem;
        color: var(--text-muted);
        font-weight: 600;
    }
    .item-count span { color: var(--primary); font-weight: 800; }

    /* No Permintaan badge */
    .no-badge {
        display: inline-block;
        font-weight: 700;
        font-size: .85rem;
        color: var(--primary);
        background: var(--primary-light);
        padding: 4px 12px;
        border-radius: 20px;
        letter-spacing: .02em;
    }

    /* Alert */
    .alert-success-bar {
        padding: 12px 22px;
        background: #d1fae5;
        border-bottom: 1px solid #a7f3d0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: .85rem;
        font-weight: 600;
        color: #065f46;
    }

    /* History table below */
    .history-card {
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        margin: 0 24px 24px;
        overflow: hidden;
    }

    /* Spinner */
    .spinner {
        display: inline-block;
        width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .6s linear infinite;
        margin-right: 4px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Row enter animation */
    @keyframes rowIn {
        from { opacity: 0; transform: translateX(-12px); background: #fef9f9; }
        to   { opacity: 1; transform: translateX(0); }
    }
    .row-new { animation: rowIn .3s ease; }

    /* Divider */
    .section-divider {
        padding: 0 22px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .section-divider span {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--text-light);
        white-space: nowrap;
    }
    .section-divider hr { border: none; border-top: 1px solid var(--border); flex: 1; }

    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-grid-4 { grid-template-columns: 1fr 1fr; }
        .main-card, .history-card { margin: 12px; }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h4><i class="fas fa-boxes me-2" style="color:var(--primary)"></i>Permintaan Kantong Aftap</h4>
        <div class="breadcrumb">aftap / Permintaan Kantong</div>
    </div>
</div>

<div class="main-card mt-0" style="margin-top: 20px !important;">

    {{-- Alert Success --}}
    <div id="alertSuccess" class="alert-success-bar" style="display:none">
        <i class="fas fa-check-circle"></i>
        <span id="alertMsg">Data berhasil disimpan.</span>
    </div>

    {{-- Card Header --}}
    <div class="card-header-bar">
        <h6><i class="fas fa-plus-circle"></i> Form Permintaan Baru</h6>
        <span class="no-badge" id="displayNoPermintaan">—</span>
    </div>

    {{-- Form Section --}}
    <div class="form-section">
        {{-- Row 1: No & Tanggal --}}
        <div class="form-grid mb-3">
            <div class="form-group">
                <input type="hidden" id="id_edit">
                <label class="form-label">No Permintaan</label>
                <input type="text" id="noPermintaan" class="form-control" readonly placeholder="Auto generate…">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Minta</label>
                <input type="date" id="tanggalMinta" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
        </div>

        {{-- Row 2: Item Input --}}
        <div class="section-divider">
            <span>Tambah Item</span><hr>
        </div>

        <div class="form-grid-4">
            <div class="form-group">
                <label class="form-label">Merk Kantong</label>
                <select id="merkKantong" class="form-select">
                    <option value="">— Pilih Merk —</option>
                    @foreach(\App\Models\PermintaanAftap::MERK_KANTONG as $merk)
                        <option value="{{ $merk }}">{{ $merk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jenis Kantong</label>
                <select id="jenisKantong" class="form-select">
                    <option value="">— Pilih Jenis —</option>
                    @foreach(\App\Models\PermintaanAftap::JENIS_KANTONG as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ukuran</label>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px;">
                    <select id="ukuranKantong" class="form-select">
                        @foreach(\App\Models\PermintaanAftap::UKURAN as $ukuran)
                            <option value="{{ $ukuran }}">{{ $ukuran }}</option>
                        @endforeach
                    </select>
                    <div class="form-group" style="gap:5px">
                        <label class="form-label">Jumlah</label>
                        <div class="qty-group">
                            <button class="qty-btn" onclick="changeQty(-1)" type="button">−</button>
                            <input class="qty-input" type="number" id="jumlahKantong" value="1" min="1">
                            <button class="qty-btn" onclick="changeQty(1)" type="button">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group" style="padding-top: 20px">
                <button class="btn-add w-100" type="button" onclick="addItem()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-section">
        <div class="table-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Merk Kantong</th>
                        <th>Jenis Kantong</th>
                        <th>Ukuran</th>
                        <th width="90">Jumlah</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="empty-row" id="emptyRow">
                        <td colspan="6">
                            <span class="empty-icon">📦</span>
                            Belum ada item. Tambahkan item di atas.
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

{{-- History --}}
<div class="history-card">
    <div class="card-header-bar">
        <h6><i class="fas fa-history"></i> Riwayat Permintaan</h6>
        <div style="display:flex;gap:8px;align-items:center">
            <input type="text" id="searchInput" class="form-control" style="width:220px;height:34px;font-size:.83rem"
                placeholder="Cari no permintaan…" oninput="filterHistory()">
        </div>
    </div>
    <div class="table-wrap">
        <table class="items-table" id="historyTable">
            <thead>
                <tr>
                    <th>No Permintaan</th>
                    <th>Tanggal</th>
                    <th>Merk</th>
                    <th>Jenis</th>
                    <th>Ukuran</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody id="historyBody">
                <tr>
                    <td colspan="8" style="text-align:center;padding:32px;color:var(--text-light)">
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
// ─── State ─────────────────────────────────────────────────────────────────
let items = [];
let noPermintaan = '';

// ─── Init ───────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    fetchNoPermintaan();
    loadHistory();
});

// ─── Auto No ────────────────────────────────────────────────────────────────
async function fetchNoPermintaan() {
    try {
        const res = await fetch('{{ route("aftap.permintaan_kantong.next_no") }}', {
            headers: { "Accept": "application/json" }
        });
        const json = await res.json();

        noPermintaan = json.no;

        document.getElementById('noPermintaan').value = noPermintaan;
        document.getElementById('displayNoPermintaan').textContent = noPermintaan;
    } catch (e) {
        console.error(e);
    }
}

// ─── Qty ────────────────────────────────────────────────────────────────────
function changeQty(delta) {
    const el = document.getElementById('jumlahKantong');
    let v = parseInt(el.value) || 1;
    v = Math.max(1, v + delta);
    el.value = v;
}

// ─── Add Item ───────────────────────────────────────────────────────────────
function addItem() {
    const merk   = document.getElementById('merkKantong').value;
    const jenis  = document.getElementById('jenisKantong').value;
    const ukuran = document.getElementById('ukuranKantong').value;
    const jumlah = parseInt(document.getElementById('jumlahKantong').value) || 1;
    

    if (!merk)  { showFlash('Merk wajib dipilih', 'error'); return; }
    if (!jenis) { showFlash('Jenis wajib dipilih', 'error'); return; }
    if (!ukuran){ showFlash('Ukuran wajib dipilih', 'error'); return; }
    if (!jenis) { showFlash('Pilih jenis kantong terlebih dahulu', 'error'); return; }

    items.push({ merk, jenis, ukuran, jumlah });
    renderTable();
    // reset item fields
    document.getElementById('merkKantong').value = '';
    document.getElementById('jenisKantong').value = '';
    document.getElementById('jumlahKantong').value = 1;
}

// ─── Render Table ───────────────────────────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById('itemsBody');
    const empty = document.getElementById('emptyRow');
    const btnSave = document.getElementById('btnSave');
    document.getElementById('itemCount').textContent = items.length;

    if (items.length === 0) {
        tbody.innerHTML = `
            <tr class="empty-row" id="emptyRow">
                <td colspan="6">
                    <span class="empty-icon">📦</span>
                    Belum ada item. Tambahkan item di atas.
                </td>
            </tr>`;
        btnSave.disabled = true;
        return;
    }

    btnSave.disabled = false;
    tbody.innerHTML = items.map((item, i) => `
        <tr class="row-new">
            <td style="color:var(--text-muted);font-weight:700">${i+1}</td>
            <td>${item.merk || '<span style="color:var(--text-light);font-style:italic">—</span>'}</td>
            <td><strong>${item.jenis}</strong></td>
            <td><span style="background:#f0f0f0;padding:2px 8px;border-radius:4px;font-size:.8rem;font-weight:600">${item.ukuran}</span></td>
            <td><span style="font-weight:800;font-size:1rem;color:var(--primary)">${item.jumlah}</span></td>
            <td>
                <button class="btn-delete-row" onclick="removeItem(${i})" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function removeItem(idx) {
    items.splice(idx, 1);
    renderTable();
}

// ─── Save ───────────────────────────────────────────────────────────────────
async function savePermintaan() {
    if (items.length === 0) return;

    const idEdit = document.getElementById('id_edit').value;
    console.log('id_edit:', idEdit);        // cek apakah ada nilainya
    console.log('items:', items);           // cek items
    console.log('noPermintaan:', noPermintaan);
    if (!noPermintaan) {
    showFlash("No permintaan kosong!", "error");
    return;
}
    const btn = document.getElementById('btnSave');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner"></span> Menyimpan…`;

    let url    = '{{ route("aftap.permintaan_kantong.store") }}';
    let method = 'POST';

    if (idEdit && idEdit !== '') {
        url    = `{{ url('aftap/permintaan_kantong') }}/${idEdit}`;
        method = 'PUT';
    }

    console.log('method:', method, 'url:', url);  // cek url & method

    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                kode:  noPermintaan,
                 tanggal_minta: document.getElementById('tanggalMinta').value,
                items: items
            })
        });

        console.log('response status:', res.status);  // cek status response
        const data = await res.json();
        console.log('response data:', data);           // cek response body

        if (res.ok) {
            showFlash(idEdit ? 'Permintaan berhasil diperbarui!' : 'Permintaan berhasil disimpan!', 'success');
            items = [];
            renderTable();
            loadHistory();
            fetchNoPermintaan();
            document.getElementById('id_edit').value = '';
        } else {
            showFlash(data.message || 'Terjadi kesalahan saat menyimpan.', 'error');
        }
    } catch(e) {
        console.error('fetch error:', e);
        showFlash('Koneksi error. Coba lagi.', 'error');
    }

    btn.disabled = false;
    btn.innerHTML = `<i class="fas fa-save"></i> Simpan Permintaan`;
}

// ─── Reset ──────────────────────────────────────────────────────────────────
function resetForm() {
    if (items.length > 0 && !confirm('Reset form? Semua item akan dihapus.')) return;
    items = [];
    renderTable();
    document.getElementById('merkKantong').value = '';
    document.getElementById('jenisKantong').value = '';
    document.getElementById('jumlahKantong').value = 1;
    document.getElementById('tanggalMinta').value = new Date().toISOString().split('T')[0];
    fetchNoPermintaan();
}

// ─── History ─────────────────────────────────────────────────────────────────
async function loadHistory() {
    try {
        // Tambah timestamp untuk hindari cache
        const res = await fetch('{{ route("aftap.permintaan_kantong.index") }}?t=' + Date.now(), {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        });
        const json = await res.json();
        renderHistory(json.data ?? []);
    } catch (e) {
        document.getElementById("historyBody").innerHTML = `
            <tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text-light)">
                Gagal memuat data…
            </td></tr>`;
    }
}
function formatTanggal(tgl) {
    if (!tgl) return '—';
    const d = new Date(tgl);
    return d.toLocaleDateString('id-ID');
}
function renderHistory(rows) {
    const tbody = document.getElementById('historyBody');
    if (!rows || rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-light)">Belum ada data permintaan.</td></tr>`;
        return;
    }
    const statusClass = { 'PENDING':'badge-pending', 'ACCEPTED':'badge-accepted', 'REJECTED':'badge-rejected' };
    tbody.innerHTML = rows.map(r => `
        <tr>
            <td><span class="no-badge" style="font-size:.78rem">${r.kode ?? '—'}</span></td>
            <td style="color:var(--text-muted);font-size:.82rem">
                ${formatTanggal(r.tanggal_minta)}
            </td>
            <td>${r.merk ?? '—'}</td>
            <td>${r.jenis ?? '—'}</td>
            <td><span style="background:#f0f0f0;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:600">${r.ukuran ?? '—'}</span></td>
            <td style="font-weight:700">${r.jumlah ?? '—'}</td>
            <td><span class="badge-status ${statusClass[r.status] ?? 'badge-pending'}">${r.status ?? 'PENDING'}</span></td>
            <td>
           

                <button onclick="editData('${r.id ?? ""}')"
                    class="btn-outline" style="padding:5px 10px;font-size:.75rem;margin-left:4px">
                    <i class="fas fa-edit"></i>
                </button>

                <button onclick="deleteData(${r.id})"
                    class="btn-outline" style="padding:5px 10px;font-size:.75rem;color:#b91c1c;border-color:#b91c1c;margin-left:4px">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

async function editData(id) {
    if (!id) {
        showFlash("Kode tidak valid!", "error");
        return;
    }

    try {
        const res  = await fetch(`{{ url('aftap/permintaan_kantong') }}/${id}/edit`, {
            headers: { "Accept": "application/json" }
        });

        const json = await res.json();

        if (!json.success) {
            showFlash("Gagal memuat data!", "error");
            return;
        }

        const d = json.data;

        // reset items dulu
        items = [];

        items = (d.items || []).map(item => ({
            merk:   item.merk,
            jenis:  item.jenis,
            ukuran: item.ukuran,
            jumlah: parseInt(item.jumlah) || 1,
        }));

        renderTable();

        // isi form
        noPermintaan = d.kode;
        document.getElementById('noPermintaan').value = d.kode;
        document.getElementById('displayNoPermintaan').textContent = d.kode;
        document.getElementById('tanggalMinta').value = d.tanggal_minta ?? new Date().toISOString().split('T')[0];

        // penting!
        document.getElementById('id_edit').value = d.id;

        document.getElementById('btnSave').disabled = false;

        window.scrollTo({ top: 0, behavior: 'smooth' });

        showFlash("Mode edit aktif", "success");

    } catch (e) {
        console.error(e);
        showFlash("Error mengambil data!", "error");
    }
}
function deleteData(id) {
    if (!confirm("Yakin ingin menghapus permintaan ini?")) return;

    fetch(`{{ url('aftap/permintaan_kantong') }}/${id}`, {
    method: "DELETE",
    headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        "Accept": "application/json"
        }
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));

        if (res.ok) {
            showFlash("Data berhasil dihapus", "success");
            loadHistory();
        } else {
            showFlash(data.message || "Gagal menghapus", "error");
        }
    })
.catch(() => showFlash("Server error", "error"));
}
function filterHistory() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#historyBody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

// ─── Flash ───────────────────────────────────────────────────────────────────
function showFlash(msg, type) {
    const el  = document.getElementById('alertSuccess');
    const txt = document.getElementById('alertMsg');
    el.style.display = 'flex';
    txt.textContent  = msg;
    el.style.background = type === 'error' ? '#fee2e2' : '#d1fae5';
    el.style.color      = type === 'error' ? '#991b1b' : '#065f46';
    el.style.borderBottomColor = type === 'error' ? '#fca5a5' : '#a7f3d0';
    clearTimeout(el._t);
    el._t = setTimeout(() => el.style.display = 'none', 3500);
}
</script>
@endpush