@extends('layouts.index')

@section('title', 'Stock Opname Darah')

@push('styles')
<style>
    :root {
        --red-deep:    #b91c1c;
        --red-main:    #dc2626;
        --red-light:   #fca5a5;
        --red-pale:    #fff1f2;
        --red-border:  #fecaca;
        --ink:         #1e1b1b;
        --ink-soft:    #57534e;
        --ink-muted:   #a8a29e;
        --surface:     #ffffff;
        --surface-2:   #fafaf9;
        --surface-3:   #f5f5f4;
        --border:      #e7e5e4;
        --shadow-sm:   0 1px 3px rgba(0,0,0,.07);
        --shadow-md:   0 4px 16px rgba(0,0,0,.09);
        --shadow-lg:   0 12px 40px rgba(0,0,0,.12);
        --radius:      10px;
        --radius-lg:   16px;
    }

    /* ── Layout ── */
    .opname-wrap { display:flex; flex-direction:column; gap:1.25rem; padding:1.5rem; }

    /* ── Header bar ── */
    .opname-header {
        background: linear-gradient(135deg, var(--red-deep) 0%, var(--red-main) 100%);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.75rem;
        display: flex; align-items: center; justify-content: space-between;
        box-shadow: 0 6px 24px rgba(185,28,28,.35);
        color: #fff;
    }
    .opname-header-title { display:flex; align-items:center; gap:.75rem; }
    .opname-header-title .icon-wrap {
        width:42px; height:42px; border-radius:10px;
        background:rgba(255,255,255,.18);
        display:flex; align-items:center; justify-content:center;
        font-size:1.3rem;
    }
    .opname-header-title h1 { font-size:1.2rem; font-weight:700; margin:0; letter-spacing:.3px; }
    .opname-header-title span { font-size:.8rem; opacity:.78; }

    /* ── Stats cards ── */
    .stats-row { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
    @media(max-width:900px){ .stats-row{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:500px){ .stats-row{ grid-template-columns:1fr; } }

    .stat-card {
        background:var(--surface);
        border:1.5px solid var(--border);
        border-radius:var(--radius);
        padding:1rem 1.2rem;
        display:flex; align-items:center; gap:.9rem;
        box-shadow:var(--shadow-sm);
        transition: transform .15s, box-shadow .15s;
    }
    .stat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
    .stat-icon {
        width:44px; height:44px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:1.2rem; flex-shrink:0;
    }
    .stat-icon.red   { background:var(--red-pale); color:var(--red-main); }
    .stat-icon.blue  { background:#eff6ff; color:#2563eb; }
    .stat-icon.green { background:#f0fdf4; color:#16a34a; }
    .stat-icon.amber { background:#fffbeb; color:#d97706; }
    .stat-label { font-size:.72rem; color:var(--ink-muted); font-weight:500; text-transform:uppercase; letter-spacing:.5px; }
    .stat-value { font-size:1.55rem; font-weight:700; color:var(--ink); line-height:1; }

    /* ── Card ── */
    .card {
        background:var(--surface);
        border:1.5px solid var(--border);
        border-radius:var(--radius-lg);
        box-shadow:var(--shadow-sm);
        overflow:hidden;
    }
    .card-head {
        padding:.9rem 1.25rem;
        border-bottom:1.5px solid var(--border);
        display:flex; align-items:center; justify-content:space-between;
        background:var(--surface-2);
        gap:.75rem; flex-wrap:wrap;
    }
    .card-head-title { font-size:.9rem; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:.5rem; }
    .card-body { padding:1.25rem; }

    /* ── Filter bar ── */
    .filter-bar { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; }
    .filter-bar input,
    .filter-bar select {
        border:1.5px solid var(--border);
        border-radius:8px;
        padding:.45rem .75rem;
        font-size:.82rem;
        color:var(--ink);
        background:var(--surface);
        outline:none;
        transition:border-color .15s;
    }
    .filter-bar input:focus,
    .filter-bar select:focus { border-color:var(--red-main); }
    .filter-bar input { min-width:180px; }

    /* ── Buttons ── */
    .btn {
        display:inline-flex; align-items:center; gap:.4rem;
        padding:.45rem 1rem; border-radius:8px; font-size:.82rem;
        font-weight:600; cursor:pointer; border:none; transition:all .15s;
        white-space:nowrap;
    }
    .btn-primary { background:var(--red-main); color:#fff; }
    .btn-primary:hover { background:var(--red-deep); box-shadow:0 3px 10px rgba(220,38,38,.35); }
    .btn-outline { background:transparent; color:var(--ink); border:1.5px solid var(--border); }
    .btn-outline:hover { background:var(--surface-3); border-color:var(--ink-muted); }
    .btn-success { background:#16a34a; color:#fff; }
    .btn-success:hover { background:#15803d; }
    .btn-sm { padding:.3rem .7rem; font-size:.76rem; }
    .btn-icon { width:30px; height:30px; padding:0; justify-content:center; }

    /* ── Table ── */
    .tbl-wrap { overflow-x:auto; }
    table.opname-tbl { width:100%; border-collapse:collapse; font-size:.82rem; }
    .opname-tbl thead tr { background:var(--surface-3); }
    .opname-tbl th {
        padding:.65rem 1rem; text-align:left;
        font-size:.7rem; font-weight:700; color:var(--ink-soft);
        text-transform:uppercase; letter-spacing:.5px;
        border-bottom:2px solid var(--border); white-space:nowrap;
    }
    .opname-tbl td {
        padding:.7rem 1rem; border-bottom:1px solid var(--border);
        color:var(--ink); vertical-align:middle;
    }
    .opname-tbl tbody tr:last-child td { border-bottom:none; }
    .opname-tbl tbody tr:hover td { background:var(--red-pale); }

    /* ── Badge ── */
    .badge {
        display:inline-flex; align-items:center; gap:.3rem;
        padding:.2rem .65rem; border-radius:99px;
        font-size:.72rem; font-weight:600; letter-spacing:.3px;
    }
    .badge-draft   { background:#fef3c7; color:#92400e; }
    .badge-selesai { background:#d1fae5; color:#065f46; }
    .badge::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }

    /* ── Action buttons ── */
    .actions { display:flex; gap:.3rem; }

    /* ── Empty state ── */
    .empty-state {
        text-align:center; padding:3.5rem 1rem; color:var(--ink-muted);
    }
    .empty-state .empty-icon { font-size:3rem; margin-bottom:.75rem; opacity:.4; }
    .empty-state p { margin:.25rem 0 0; font-size:.85rem; }

    /* ── Pagination ── */
    .pagination-wrap { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-top:1.5px solid var(--border); flex-wrap:wrap; gap:.5rem; }
    .page-info { font-size:.78rem; color:var(--ink-muted); }
    .page-btns { display:flex; gap:.3rem; }
    .page-btns button { width:32px; height:32px; border-radius:7px; border:1.5px solid var(--border); background:var(--surface); color:var(--ink); font-size:.8rem; cursor:pointer; transition:all .15s; }
    .page-btns button:hover:not(:disabled) { border-color:var(--red-main); color:var(--red-main); }
    .page-btns button.active { background:var(--red-main); color:#fff; border-color:var(--red-main); }
    .page-btns button:disabled { opacity:.4; cursor:not-allowed; }

    /* ── Modal backdrop ── */
    .modal-backdrop {
        position:fixed; inset:0; background:rgba(0,0,0,.45);
        backdrop-filter:blur(3px); z-index:1000;
        display:none; align-items:center; justify-content:center; padding:1rem;
    }
    .modal-backdrop.open { display:flex; }
    .modal {
        background:var(--surface); border-radius:var(--radius-lg);
        width:100%; max-width:780px; max-height:90vh;
        display:flex; flex-direction:column;
        box-shadow:var(--shadow-lg);
        animation:modalIn .2s ease;
    }
    @keyframes modalIn { from{ opacity:0; transform:translateY(20px) scale(.97); } }
    .modal-head {
        padding:1.1rem 1.5rem; border-bottom:1.5px solid var(--border);
        display:flex; align-items:center; justify-content:space-between;
        background: linear-gradient(135deg,var(--red-deep),var(--red-main));
        border-radius:var(--radius-lg) var(--radius-lg) 0 0; color:#fff;
    }
    .modal-head h2 { font-size:1rem; font-weight:700; margin:0; }
    .modal-close { background:rgba(255,255,255,.2); border:none; color:#fff; width:30px; height:30px; border-radius:7px; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center; }
    .modal-close:hover { background:rgba(255,255,255,.35); }
    .modal-body { padding:1.5rem; overflow-y:auto; flex:1; }
    .modal-foot { padding:1rem 1.5rem; border-top:1.5px solid var(--border); display:flex; justify-content:flex-end; gap:.6rem; background:var(--surface-2); border-radius:0 0 var(--radius-lg) var(--radius-lg); }

    /* ── Form ── */
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:560px){ .form-grid{ grid-template-columns:1fr; } }
    .form-grid.full { grid-template-columns:1fr; }
    .form-group { display:flex; flex-direction:column; gap:.35rem; }
    .form-group label { font-size:.75rem; font-weight:600; color:var(--ink-soft); text-transform:uppercase; letter-spacing:.4px; }
    .form-group input,
    .form-group select,
    .form-group textarea {
        border:1.5px solid var(--border); border-radius:8px;
        padding:.5rem .75rem; font-size:.84rem; color:var(--ink);
        background:var(--surface); outline:none; transition:border-color .15s;
        width:100%; box-sizing:border-box;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color:var(--red-main); }
    .form-group input[readonly] { background:var(--surface-3); color:var(--ink-muted); }

    /* ── Detail table in modal ── */
    .detail-section { margin-top:1.25rem; }
    .detail-section-head {
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:.75rem;
    }
    .detail-section-head h3 { font-size:.85rem; font-weight:700; color:var(--ink); margin:0; }

    .detail-tbl-wrap { overflow-x:auto; border:1.5px solid var(--border); border-radius:var(--radius); }
    table.detail-tbl { width:100%; border-collapse:collapse; font-size:.78rem; }
    .detail-tbl th {
        padding:.5rem .75rem; background:var(--surface-3);
        font-size:.68rem; font-weight:700; color:var(--ink-soft);
        text-transform:uppercase; letter-spacing:.4px;
        border-bottom:1.5px solid var(--border); white-space:nowrap;
    }
    .detail-tbl td { padding:.55rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; }
    .detail-tbl tbody tr:last-child td { border-bottom:none; }
    .detail-tbl input {
        border:1.5px solid var(--border); border-radius:6px;
        padding:.3rem .5rem; font-size:.78rem; width:70px; text-align:center;
    }
    .detail-tbl input:focus { border-color:var(--red-main); outline:none; }

    .selisih-pos { color:#16a34a; font-weight:700; }
    .selisih-neg { color:var(--red-main); font-weight:700; }
    .selisih-zero{ color:var(--ink-muted); }

    /* ── Stok search popover ── */
    .stok-search-wrap { position:relative; }
    .stok-dropdown {
        position:absolute; top:100%; left:0; right:0; z-index:200;
        background:var(--surface); border:1.5px solid var(--border);
        border-radius:var(--radius); box-shadow:var(--shadow-md);
        max-height:200px; overflow-y:auto; display:none;
    }
    .stok-dropdown.open { display:block; }
    .stok-opt {
        padding:.55rem .75rem; cursor:pointer; font-size:.78rem;
        border-bottom:1px solid var(--border); display:flex; justify-content:space-between;
    }
    .stok-opt:last-child { border-bottom:none; }
    .stok-opt:hover { background:var(--red-pale); }
    .stok-opt-label { color:var(--ink); font-weight:600; }
    .stok-opt-meta  { color:var(--ink-muted); font-size:.72rem; }

    /* ── Loader ── */
    .spinner { display:inline-block; width:16px; height:16px; border:2.5px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:spin .7s linear infinite; }
    @keyframes spin{ to{ transform:rotate(360deg); } }

    /* ── Toast ── */
    #toast-container { position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
    .toast {
        background:var(--ink); color:#fff; padding:.65rem 1.1rem;
        border-radius:10px; font-size:.82rem; font-weight:500;
        box-shadow:var(--shadow-lg); display:flex; align-items:center; gap:.5rem;
        animation: toastIn .25s ease; min-width:220px;
    }
    .toast.success { background:#15803d; }
    .toast.error   { background:var(--red-deep); }
    @keyframes toastIn { from{ opacity:0; transform:translateY(10px); } }

    /* ── View modal detail grid ── */
    .view-grid { display:grid; grid-template-columns:1fr 1fr; gap:.6rem 1.5rem; margin-bottom:1rem; }
    .view-row label { font-size:.7rem; text-transform:uppercase; letter-spacing:.4px; color:var(--ink-muted); font-weight:600; }
    .view-row span  { font-size:.85rem; color:var(--ink); font-weight:500; }
</style>
@endpush

@section('content')
<div class="opname-wrap">

    {{-- Header --}}
    <div class="opname-header">
        <div class="opname-header-title">
            <div class="icon-wrap">🩸</div>
            <div>
                <h1>Stock Opname Darah</h1>
                <span>Pencatatan & Verifikasi Fisik Stok Darah</span>
            </div>
        </div>
        <button class="btn btn-outline" style="color:#fff;border-color:rgba(255,255,255,.4);background:rgba(255,255,255,.12)" onclick="openModal('modal-tambah')">
            <span>＋</span> Tambah Opname
        </button>
    </div>

    {{-- Stats --}}
    <div class="stats-row" id="stats-row">
        <div class="stat-card">
            <div class="stat-icon red">📋</div>
            <div><div class="stat-label">Total Opname</div><div class="stat-value" id="stat-total">–</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">⏳</div>
            <div><div class="stat-label">Draft</div><div class="stat-value" id="stat-draft">–</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div><div class="stat-label">Selesai</div><div class="stat-value" id="stat-selesai">–</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">📅</div>
            <div><div class="stat-label">Bulan Ini</div><div class="stat-value" id="stat-bulan">–</div></div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="card">
        <div class="card-head">
            <span class="card-head-title">🗂️ Daftar Stock Opname</span>
            <div class="filter-bar">
                <input type="text" id="filter-search" placeholder="🔍  Cari no. opname / lokasi…" oninput="debounceLoad()">
                <select id="filter-status" onchange="loadData()">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="selesai">Selesai</option>
                </select>
                <input type="date" id="filter-dari" onchange="loadData()" title="Dari tanggal">
                <input type="date" id="filter-sampai" onchange="loadData()" title="Sampai tanggal">
            </div>
        </div>

        <div class="tbl-wrap">
            <table class="opname-tbl">
                <thead>
                    <tr>
                        <th>No. Opname</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Petugas</th>
                        <th>Jumlah Item</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbl-body">
                    <tr><td colspan="8"><div class="empty-state"><div class="empty-icon">⏳</div><p>Memuat data…</p></div></td></tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">
            <span class="page-info" id="page-info">–</span>
            <div class="page-btns" id="page-btns"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════ MODAL TAMBAH / EDIT ═══════════════════════════ --}}
<div class="modal-backdrop" id="modal-tambah">
    <div class="modal" style="max-width:860px">
        <div class="modal-head">
            <h2 id="modal-title">➕ Tambah Stock Opname</h2>
            <button class="modal-close" onclick="closeModal('modal-tambah')">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="edit-id">
            <div class="form-grid">
                <div class="form-group">
                    <label>No. Opname</label>
                    <input type="text" id="f-no-opname" readonly placeholder="Auto-generate">
                </div>
                <div class="form-group">
                    <label>Tanggal Opname <span style="color:var(--red-main)">*</span></label>
                    <input type="date" id="f-tgl-opname">
                </div>
                <div class="form-group">
                    <label>Lokasi Opname (Bagian)</label>
                    <div class="stok-search-wrap" id="wrap-bagian">
                        <input type="hidden" id="f-lokasi-id">
                        <input type="text" id="f-lokasi" placeholder="🔍  Ketik nama bagian…"
                            autocomplete="off"
                            style="border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:.82rem;width:100%;box-sizing:border-box;outline:none"
                            oninput="searchBagian(this.value)"
                            onfocus="if(this.value) searchBagian(this.value)">
                        <div class="stok-dropdown" id="dd-bagian"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Petugas Opname</label>
                    <div class="stok-search-wrap" id="wrap-petugas">
                        <input type="hidden" id="f-petugas-id">
                        <input type="text" id="f-petugas" placeholder="🔍  Ketik nama petugas…"
                            autocomplete="off"
                            style="border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:.82rem;width:100%;box-sizing:border-box;outline:none"
                            oninput="searchPetugas(this.value)"
                            onfocus="if(this.value) searchPetugas(this.value)">
                        <div class="stok-dropdown" id="dd-petugas"></div>
                    </div>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label>Keterangan</label>
                    <textarea id="f-keterangan" rows="2" placeholder="Catatan tambahan…" style="resize:vertical"></textarea>
                </div>
            </div>

            {{-- Detail stok --}}
            <div class="detail-section">
                <div class="detail-section-head">
                    <h3>📦 Detail Stok Darah</h3>
                    <button class="btn btn-outline btn-sm" onclick="addDetailRow()">＋ Tambah Stok</button>
                </div>

                {{-- Cari stok --}}
                <div class="stok-search-wrap" style="margin-bottom:.75rem">
                    <input type="text" id="stok-search-input" placeholder="🔍  Ketik No. Stok untuk mencari…"
                        style="border:1.5px solid var(--border);border-radius:8px;padding:.5rem .75rem;font-size:.82rem;width:100%;box-sizing:border-box;outline:none"
                        oninput="searchStok(this.value)">
                    <div class="stok-dropdown" id="stok-dropdown"></div>
                </div>

                <div class="detail-tbl-wrap">
                    <table class="detail-tbl">
                        <thead>
                            <tr>
                                <th>No. Stok</th>
                                <th>Jenis Darah</th>
                                <th>Gol/Rh</th>
                                <th>Tgl Kadaluarsa</th>
                                <th>Jml Sistem</th>
                                <th>Jml Fisik</th>
                                <th>Selisih</th>
                                <th>Ket</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="detail-tbody"></tbody>
                    </table>
                </div>
                <div id="detail-empty" style="text-align:center;padding:1.5rem;color:var(--ink-muted);font-size:.8rem;display:none">
                    Belum ada stok ditambahkan. Cari dan pilih stok di atas.
                </div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-outline" onclick="closeModal('modal-tambah')">Batal</button>
            <button class="btn btn-primary" id="btn-save" onclick="saveOpname()">
                <span>💾</span> Simpan Opname
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════ MODAL VIEW ═══════════════════════════ --}}
<div class="modal-backdrop" id="modal-view">
    <div class="modal" style="max-width:860px">
        <div class="modal-head">
            <h2 id="view-title">📋 Detail Stock Opname</h2>
            <button class="modal-close" onclick="closeModal('modal-view')">✕</button>
        </div>
        <div class="modal-body" id="view-body">
            <div style="text-align:center;padding:2rem;color:var(--ink-muted)">Memuat…</div>
        </div>
        <div class="modal-foot" id="view-foot"></div>
    </div>
</div>

{{-- Toast --}}
<div id="toast-container"></div>
@endsection

@push('scripts')
<script>
// ══ Config ══════════════════════════════════════════════════════════════
const ROUTES = {
    data:       '{{ route("penyimpanan.opname_darah.data") }}',
    nextNomor:  '{{ route("penyimpanan.opname_darah.nextNomor") }}',
    cariStok:   '{{ route("penyimpanan.opname_darah.cariStok") }}',
    store:      '{{ route("penyimpanan.opname_darah.store") }}',
    cariBagian:  '{{ route("penyimpanan.opname_darah.cariBagian") }}',
    cariPetugas: '{{ route("penyimpanan.opname_darah.cariPetugas") }}',
    base:       '/penyimpanan/opname_darah',
};
const csrfToken = '{{ csrf_token() }}';

// ══ State ════════════════════════════════════════════════════════════════
let currentPage  = 1;
let detailRows   = [];   // { no_stok, jenis_darah, golongan_darah, rhesus, tgl_kadaluarsa, jumlah_sistem, jumlah_fisik, keterangan }
let stokSearchTimeout = null;
let editId = null;
let bagianTimeout, petugasTimeout;

function searchBagian(val) {
    clearTimeout(bagianTimeout);
    const dd = document.getElementById('dd-bagian');
    if (!val.trim()) { dd.classList.remove('open'); return; }

    bagianTimeout = setTimeout(async () => {
        const res  = await fetch(`${ROUTES.cariBagian}?q=${encodeURIComponent(val)}`);
        const list = await res.json();

        dd.innerHTML = list.length
            ? list.map(b => `
                <div class="stok-opt" onclick="pickBagian(${b.id}, '${b.kode} — ${b.nama}')">
                    <span class="stok-opt-label">${b.nama}</span>
                    <span class="stok-opt-meta">${b.kode}</span>
                </div>`).join('')
            : '<div class="stok-opt"><span class="stok-opt-label">Tidak ditemukan</span></div>';

        dd.classList.add('open');
    }, 300);
}

function pickBagian(id, label) {
    document.getElementById('f-lokasi-id').value = id;
    document.getElementById('f-lokasi').value    = label;
    document.getElementById('dd-bagian').classList.remove('open');
}
function searchPetugas(val) {
    clearTimeout(petugasTimeout);
    const dd = document.getElementById('dd-petugas');
    if (!val.trim()) { dd.classList.remove('open'); return; }

    petugasTimeout = setTimeout(async () => {
        const res  = await fetch(`${ROUTES.cariPetugas}?q=${encodeURIComponent(val)}`);
        const list = await res.json();

        dd.innerHTML = list.length
            ? list.map(p => `
                <div class="stok-opt" onclick="pickPetugas(${p.id}, '${p.nama}')">
                    <span class="stok-opt-label">${p.nama}</span>
                    <span class="stok-opt-meta">${p.kode}</span>
                </div>`).join('')
            : '<div class="stok-opt"><span class="stok-opt-label">Tidak ditemukan</span></div>';

        dd.classList.add('open');
    }, 300);
}

function pickPetugas(id, label) {
    document.getElementById('f-petugas-id').value = id;
    document.getElementById('f-petugas').value    = label;
    document.getElementById('dd-petugas').classList.remove('open');
}



// ══ Init ═════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('f-tgl-opname').value = today;
    loadData();
    loadStats();
});

// ══ Load table ═══════════════════════════════════════════════════════════
let debounceTimer;
function debounceLoad(){ clearTimeout(debounceTimer); debounceTimer = setTimeout(loadData, 400); }

async function loadData(page = 1){
    currentPage = page;
    const params = new URLSearchParams({
        page,
        per_page: 15,
        search:      document.getElementById('filter-search').value,
        status:      document.getElementById('filter-status').value,
        tgl_dari:    document.getElementById('filter-dari').value,
        tgl_sampai:  document.getElementById('filter-sampai').value,
    });

    const tbody = document.getElementById('tbl-body');
    tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="empty-icon">⏳</div><p>Memuat data…</p></div></td></tr>`;

    try {
        const res  = await fetch(`${ROUTES.data}?${params}`);
        const json = await res.json();
        renderTable(json);
    } catch(e) {
        tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="empty-icon">⚠️</div><p>Gagal memuat data</p></div></td></tr>`;
    }
}

function renderTable(json){
    const tbody = document.getElementById('tbl-body');
    const rows  = json.data ?? [];

    if(!rows.length){
        tbody.innerHTML = `<tr><td colspan="8"><div class="empty-state"><div class="empty-icon">📭</div><p>Tidak ada data opname ditemukan</p></div></td></tr>`;
        renderPagination(json);
        return;
    }

    tbody.innerHTML = rows.map(r => {
        const lokasiNama = (typeof r.lokasi_opname === 'object' && r.lokasi_opname !== null)
        ? r.lokasi_opname.nama
        : (r.lokasi_opname ?? null);

    return `
        <tr>
            <td><strong style="color:var(--red-deep)">${r.no_opname}</strong></td>
            <td>${fmt(r.tgl_opname)}</td>
            <td>${lokasiNama ?? '<span style="color:var(--ink-muted)">—</span>'}</td>
            <td>${r.petugas?.nama ?? '<span style="color:var(--ink-muted)">—</span>'}</td>
            <td style="text-align:center">
                <span style="background:var(--red-pale);color:var(--red-deep);padding:.15rem .55rem;border-radius:99px;font-size:.75rem;font-weight:700">
                    ${r.detail_count ?? 0}
                </span>
            </td>
            <td>${badgeHtml(r.status)}</td>
            <td style="color:var(--ink-muted);font-size:.76rem">${fmtDt(r.created_at)}</td>
            <td>
                <div class="actions" style="justify-content:center">
                    <button class="btn btn-outline btn-sm btn-icon" title="Lihat" onclick="viewOpname(${r.id})">👁</button>
                    ${r.status==='draft' ? `<button class="btn btn-outline btn-sm btn-icon" title="Edit" onclick="editOpname(${r.id})">✏️</button>` : ''}
                    ${r.status==='draft' ? `<button class="btn btn-success btn-sm" onclick="selesaiOpname(${r.id},'${r.no_opname}')">✔ Selesai</button>` : ''}
                    ${r.status==='draft' ? `<button class="btn btn-outline btn-sm btn-icon" onclick="deleteOpname(${r.id},'${r.no_opname}')" style="color:var(--red-main)">🗑</button>` : ''}
                </div>
            </td>
        </tr>`;
}).join('');

    renderPagination(json);
}

function renderPagination(json){
    const info  = document.getElementById('page-info');
    const btns  = document.getElementById('page-btns');
    const { from, to, total, last_page, current_page } = json;

    info.textContent = total ? `Menampilkan ${from}–${to} dari ${total} data` : 'Tidak ada data';
    btns.innerHTML = '';

    const addBtn = (label, page, active=false, disabled=false) => {
        const b = document.createElement('button');
        b.innerHTML = label;
        if(active)   b.classList.add('active');
        if(disabled) b.disabled = true;
        b.onclick = () => loadData(page);
        btns.appendChild(b);
    };

    addBtn('‹', current_page-1, false, current_page===1);
    const start = Math.max(1, current_page-2);
    const end   = Math.min(last_page, current_page+2);
    for(let p=start; p<=end; p++) addBtn(p, p, p===current_page);
    addBtn('›', current_page+1, false, current_page===last_page);
}

// ══ Stats ════════════════════════════════════════════════════════════════
async function loadStats(){
    try {
        const res  = await fetch(`${ROUTES.data}?per_page=1&page=1`);
        const json = await res.json();
        document.getElementById('stat-total').textContent = json.total ?? '0';

        const [rDraft, rSelesai, rBulan] = await Promise.all([
            fetch(`${ROUTES.data}?per_page=1&status=draft`).then(r=>r.json()),
            fetch(`${ROUTES.data}?per_page=1&status=selesai`).then(r=>r.json()),
            fetch(`${ROUTES.data}?per_page=1&tgl_dari=${firstOfMonth()}`).then(r=>r.json()),
        ]);
        document.getElementById('stat-draft').textContent   = rDraft.total   ?? '0';
        document.getElementById('stat-selesai').textContent = rSelesai.total ?? '0';
        document.getElementById('stat-bulan').textContent   = rBulan.total   ?? '0';
    } catch(e){}
}

// ══ Modal helpers ════════════════════════════════════════════════════════
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }

// ══ Add opname ───────────────────────────────────────────────────────────
async function openTambah(){
    editId = null;
    document.getElementById('modal-title').textContent = '➕ Tambah Stock Opname';
    document.getElementById('edit-id').value = '';
    document.getElementById('f-lokasi-id').value  = '';
    document.getElementById('f-lokasi').value     = '';
    document.getElementById('f-petugas-id').value = '';
    document.getElementById('f-petugas').value    = '';
    document.getElementById('f-keterangan').value = '';
    document.getElementById('f-tgl-opname').value = new Date().toISOString().split('T')[0];
    detailRows = [];
    renderDetailRows();

    const res  = await fetch(ROUTES.nextNomor);
    const json = await res.json();
    document.getElementById('f-no-opname').value = json.no_opname;

    openModal('modal-tambah');
}
document.querySelector('[onclick="openModal(\'modal-tambah\')"]').onclick = openTambah;

// ══ Edit opname ──────────────────────────────────────────────────────────
async function editOpname(id){
    editId = id;
    document.getElementById('modal-title').textContent = '✏️ Edit Stock Opname';

    try {
        const res  = await fetch(`${ROUTES.base}/${id}`);
        const json = await res.json();

        document.getElementById('f-no-opname').value   = json.no_opname;
        document.getElementById('f-tgl-opname').value  = json.tgl_opname?.split('T')[0] ?? json.tgl_opname;
        document.getElementById('f-lokasi-id').value  = json.lokasi_opname_id ?? '';
        const lokasiObj = json.lokasiOpname ?? 
    (typeof json.lokasi_opname === 'object' ? json.lokasi_opname : null);

    document.getElementById('f-lokasi-id').value = json.lokasi_opname_id ?? lokasiObj?.id ?? '';
    document.getElementById('f-lokasi').value    = lokasiObj
        ? `${lokasiObj.kode} — ${lokasiObj.nama}`
        : (typeof json.lokasi_opname === 'string' ? json.lokasi_opname : '');
            
        document.getElementById('f-petugas-id').value = json.petugas_id ?? '';
        document.getElementById('f-petugas').value    = json.petugas?.nama ?? '';
        document.getElementById('f-keterangan').value  = json.keterangan ?? '';

        detailRows = (json.detail ?? []).map(d => ({
            no_stok:        d.no_stok,
            jenis_darah:    d.jenis_darah,
            golongan_darah: d.golongan_darah,
            rhesus:         d.rhesus,
            tgl_kadaluarsa: d.tgl_kadaluarsa?.split('T')[0] ?? d.tgl_kadaluarsa,
            jumlah_sistem:  d.jumlah_sistem,
            jumlah_fisik:   d.jumlah_fisik,
            keterangan:     d.keterangan ?? '',
        }));
        renderDetailRows();
        openModal('modal-tambah');
    } catch(e){ toast('Gagal memuat data opname', 'error'); }
}

// ══ Detail rows ──────────────────────────────────────────────────────────
function addDetailRow(stok = null){
    detailRows.push({
        no_stok:        stok?.no_stok ?? '',
        jenis_darah:    stok?.jenis_darah ?? '',
        golongan_darah: stok?.golongan_darah ?? '',
        rhesus:         stok?.rhesus ?? '',
        tgl_kadaluarsa: stok?.tgl_expired ?? '',
        jumlah_sistem:  stok?.saldo ?? 0,
        jumlah_fisik:   0,
        keterangan:     '',
    });
    renderDetailRows();
}

function removeDetailRow(idx){
    detailRows.splice(idx, 1);
    renderDetailRows();
}

function renderDetailRows(){
    const tbody = document.getElementById('detail-tbody');
    const empty = document.getElementById('detail-empty');

    if(!detailRows.length){
        tbody.innerHTML = '';
        empty.style.display = 'block';
        return;
    }
    empty.style.display = 'none';

    tbody.innerHTML = detailRows.map((r, i) => {
        const selisih = (parseInt(r.jumlah_fisik)||0) - (parseInt(r.jumlah_sistem)||0);
        const cls = selisih>0 ? 'selisih-pos' : selisih<0 ? 'selisih-neg' : 'selisih-zero';
        return `
        <tr id="drow-${i}">
            <td><strong style="font-size:.78rem">${r.no_stok || '—'}</strong></td>
            <td>${r.jenis_darah || '—'}</td>
            <td>${(r.golongan_darah||'') + ' ' + (r.rhesus||'')}</td>
            <td style="font-size:.74rem;color:var(--ink-muted)">${r.tgl_kadaluarsa ? fmt(r.tgl_kadaluarsa) : '—'}</td>
            <td style="text-align:center;color:var(--ink-soft)">${r.jumlah_sistem}</td>
            <td>
                <input type="number" min="0" value="${r.jumlah_fisik}"
                    onchange="updateFisik(${i}, this.value)"
                    oninput="updateFisik(${i}, this.value)">
            </td>
            <td class="${cls}" style="text-align:center" id="sel-${i}">${selisih >= 0 ? '+'+selisih : selisih}</td>
            <td>
                <input type="text" value="${r.keterangan||''}" placeholder="ket…" style="width:80px"
                    onchange="detailRows[${i}].keterangan = this.value">
            </td>
            <td>
                <button class="btn btn-outline btn-sm btn-icon" style="color:var(--red-main)" onclick="removeDetailRow(${i})">✕</button>
            </td>
        </tr>`;
    }).join('');
}

function updateFisik(idx, val){
    detailRows[idx].jumlah_fisik = parseInt(val) || 0;
    const selisih = detailRows[idx].jumlah_fisik - (detailRows[idx].jumlah_sistem || 0);
    const cell = document.getElementById(`sel-${idx}`);
    if(cell){
        cell.textContent = selisih >= 0 ? '+'+selisih : selisih;
        cell.className   = selisih>0 ? 'selisih-pos' : selisih<0 ? 'selisih-neg' : 'selisih-zero';
    }
}

// ══ Stok search ──────────────────────────────────────────────────────────
function searchStok(val){
    clearTimeout(stokSearchTimeout);
    const dd = document.getElementById('stok-dropdown');
    if(!val.trim()){ dd.classList.remove('open'); return; }

    stokSearchTimeout = setTimeout(async () => {
        const res  = await fetch(`${ROUTES.cariStok}?no_stok=${encodeURIComponent(val)}`);
        const list = await res.json();

        if(!list.length){ dd.innerHTML = '<div class="stok-opt"><span class="stok-opt-label">Tidak ditemukan</span></div>'; }
        else {
            dd.innerHTML = list.map(s => `
                <div class="stok-opt" onclick="pickStok(${JSON.stringify(s).replace(/"/g,'&quot;')})">
                    <span class="stok-opt-label">${s.no_stok}</span>
                    <span class="stok-opt-meta">${s.jenis_darah ?? ''} ${s.golongan_darah ?? ''}${s.rhesus ?? ''} · Saldo: ${s.saldo}</span>
                </div>
            `).join('');
        }
        dd.classList.add('open');
    }, 300);
}

function pickStok(stok){
    // prevent duplicate
    if(detailRows.some(r => r.no_stok === stok.no_stok)){
        toast('Stok ini sudah ditambahkan', 'error'); return;
    }
    addDetailRow(stok);
    document.getElementById('stok-search-input').value = '';
    document.getElementById('stok-dropdown').classList.remove('open');
}

document.addEventListener('click', e => {
    if (!e.target.closest('.stok-search-wrap')) {
        document.getElementById('stok-dropdown').classList.remove('open');
        document.getElementById('dd-bagian').classList.remove('open');
        document.getElementById('dd-petugas').classList.remove('open');
    }
});

// ══ Save ─────────────────────────────────────────────────────────────────
async function saveOpname(){
    const tgl = document.getElementById('f-tgl-opname').value;
    if(!tgl){ toast('Tanggal opname wajib diisi', 'error'); return; }

    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Menyimpan…';

    const payload = {
        tgl_opname:        tgl,
        lokasi_opname_id:  document.getElementById('f-lokasi-id').value  || null,
        lokasi_opname_nama: document.getElementById('f-lokasi').value    || null,
        petugas_id:        document.getElementById('f-petugas-id').value || null,
        keterangan:        document.getElementById('f-keterangan').value,
        detail: detailRows.map(r => ({
            no_stok:        r.no_stok,
            jenis_darah:    r.jenis_darah,
            golongan_darah: r.golongan_darah,
            rhesus:         r.rhesus,
            tgl_kadaluarsa: r.tgl_kadaluarsa,
            jumlah_fisik:   r.jumlah_fisik,
            keterangan:     r.keterangan,
        })),
    };

    try {
        const isEdit = !!editId;
        const url    = isEdit ? `${ROUTES.base}/${editId}` : ROUTES.store;
        const method = isEdit ? 'PUT' : 'POST';

        const res  = await fetch(url, {
            method,
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
            body: JSON.stringify(payload),
        });
        const json = await res.json();

        if(!res.ok) throw new Error(json.message ?? 'Gagal menyimpan');

        toast(json.message ?? 'Berhasil disimpan', 'success');
        closeModal('modal-tambah');
        loadData(currentPage);
        loadStats();
    } catch(e){
        toast(e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span>💾</span> Simpan Opname';
    }
}

// ══ View ─────────────────────────────────────────────────────────────────
async function viewOpname(id){
    document.getElementById('view-body').innerHTML = '<div style="text-align:center;padding:2rem;color:var(--ink-muted)">Memuat…</div>';
    document.getElementById('view-foot').innerHTML = '';
    openModal('modal-view');

    const res  = await fetch(`${ROUTES.base}/${id}`);
    const d    = await res.json();

    document.getElementById('view-title').textContent = `📋 ${d.no_opname}`;

    const det = (d.detail ?? []).map(r => {
        const sel = r.selisih ?? (r.jumlah_fisik - r.jumlah_sistem);
        const cls = sel>0 ? 'selisih-pos' : sel<0 ? 'selisih-neg' : 'selisih-zero';
        return `
        <tr>
            <td><strong>${r.no_stok}</strong></td>
            <td>${r.jenis_darah??'—'}</td>
            <td>${(r.golongan_darah??'')+' '+(r.rhesus??'')}</td>
            <td>${r.tgl_kadaluarsa ? fmt(r.tgl_kadaluarsa) : '—'}</td>
            <td style="text-align:center">${r.jumlah_sistem}</td>
            <td style="text-align:center"><strong>${r.jumlah_fisik}</strong></td>
            <td class="${cls}" style="text-align:center">${sel>=0?'+'+sel:sel}</td>
            <td style="color:var(--ink-muted);font-size:.74rem">${r.keterangan??'—'}</td>
        </tr>`;
    }).join('');

    document.getElementById('view-body').innerHTML = `
        <div class="view-grid">
            <div class="view-row"><label>No. Opname</label><br><span>${d.no_opname}</span></div>
            <div class="view-row"><label>Tanggal</label><br><span>${fmt(d.tgl_opname)}</span></div>
            <div class="view-row"><label>Lokasi</label><br><span>${d.lokasi_opname?.nama ?? d.lokasi_opname.nama ?? '—' }</span></div>
            <div class="view-row"><label>Petugas</label><br><span>${d.petugas?.nama??'—'}</span></div>
            <div class="view-row"><label>Status</label><br>${badgeHtml(d.status)}</div>
            <div class="view-row"><label>Keterangan</label><br><span>${d.keterangan??'—'}</span></div>
        </div>
        <div class="detail-tbl-wrap">
            <table class="detail-tbl">
                <thead><tr>
                    <th>No. Stok</th><th>Jenis</th><th>Gol/Rh</th><th>Kadaluarsa</th>
                    <th>Jml Sistem</th><th>Jml Fisik</th><th>Selisih</th><th>Ket</th>
                </tr></thead>
                <tbody>${det || '<tr><td colspan="8" style="text-align:center;color:var(--ink-muted);padding:1.5rem">Tidak ada detail</td></tr>'}</tbody>
            </table>
        </div>
    `;

    document.getElementById('view-foot').innerHTML = d.status==='draft'
        ? `<button class="btn btn-success" onclick="selesaiOpname(${d.id},'${d.no_opname}',true)">✔ Selesaikan Opname</button>
           <button class="btn btn-outline" onclick="closeModal('modal-view')">Tutup</button>`
        : `<button class="btn btn-outline" onclick="closeModal('modal-view')">Tutup</button>`;
}

// ══ Selesai ──────────────────────────────────────────────────────────────
async function selesaiOpname(id, noOpname, fromView=false){
    if(!confirm(`Selesaikan opname ${noOpname}?\nData tidak dapat diubah setelah diselesaikan.`)) return;

    try {
        const res  = await fetch(`${ROUTES.base}/${id}/selesai`, {
            method:'PUT', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        });
        const json = await res.json();
        if(!res.ok) throw new Error(json.message);
        toast(json.message ?? 'Opname diselesaikan', 'success');
        if(fromView) closeModal('modal-view');
        loadData(currentPage);
        loadStats();
    } catch(e){ toast(e.message, 'error'); }
}

// ══ Delete ───────────────────────────────────────────────────────────────
async function deleteOpname(id, noOpname){
    if(!confirm(`Hapus opname ${noOpname}?`)) return;
    try {
        const res  = await fetch(`${ROUTES.base}/${id}`, {
            method:'DELETE', headers:{'X-CSRF-TOKEN':csrfToken},
        });
        const json = await res.json();
        if(!res.ok) throw new Error(json.message);
        toast(json.message ?? 'Berhasil dihapus', 'success');
        loadData(currentPage);
        loadStats();
    } catch(e){ toast(e.message, 'error'); }
}

// ══ Helpers ──────────────────────────────────────────────────────────────
function fmt(d){
    if(!d) return '—';
    const dt = new Date(d);
    return dt.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});
}
function fmtDt(d){
    if(!d) return '—';
    const dt = new Date(d);
    return dt.toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'});
}
function badgeHtml(status){
    const map = {
        draft:   ['badge-draft',   '⏳ Draft'],
        selesai: ['badge-selesai', '✅ Selesai'],
    };
    const [cls, lbl] = map[status] ?? ['','—'];
    return `<span class="badge ${cls}">${lbl}</span>`;
}
function firstOfMonth(){
    const d = new Date(); d.setDate(1);
    return d.toISOString().split('T')[0];
}

// ══ Toast ────────────────────────────────────────────────────────────────
function toast(msg, type='success'){
    const c  = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = (type==='success'?'✅':'❌') + ' ' + msg;
    c.appendChild(el);
    setTimeout(()=>{ el.style.opacity='0'; el.style.transition='opacity .3s'; setTimeout(()=>el.remove(),300); }, 3200);
}
</script>
@endpush