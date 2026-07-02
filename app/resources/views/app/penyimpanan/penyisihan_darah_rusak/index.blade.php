@extends('layouts.index')

@section('title', 'Penyisihan Darah Rusak / Kadaluarsa')

@push('styles')
<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

  :root {
    --red-deep:   #8B1A1A;
    --red-mid:    #C0392B;
    --red-light:  #F8E8E8;
    --red-accent: #E74C3C;
    --slate-900:  #1A1F2E;
    --slate-700:  #2D3448;
    --slate-500:  #4A5270;
    --slate-300:  #8892B0;
    --slate-100:  #F4F5F8;
    --white:      #FFFFFF;
    --border:     #E2E6EF;
    --success:    #27AE60;
    --warning:    #F39C12;
    --info:       #2980B9;
    --shadow-sm:  0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
    --shadow-md:  0 4px 16px rgba(0,0,0,.10);
    --shadow-lg:  0 12px 32px rgba(0,0,0,.14);
    --radius:     10px;
  }

  * { box-sizing: border-box; }

  body { font-family: 'DM Sans', sans-serif; background: var(--slate-100); color: var(--slate-900); }

  /* ── Page Header ─────────────────────────────── */
  .page-header {
    background: linear-gradient(135deg, var(--red-deep) 0%, var(--red-mid) 60%, #D35400 100%);
    padding: 24px 32px;
    display: flex;
    align-items: center;
    gap: 20px;
    border-radius: 0 0 var(--radius) var(--radius);
    box-shadow: var(--shadow-md);
    margin-bottom: 28px;
  }
  .page-header .icon-wrap {
    width: 52px; height: 52px;
    background: rgba(255,255,255,.15);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
  }
  .page-header h1 { font-size: 1.35rem; font-weight: 700; color: #fff; margin: 0; letter-spacing: -.3px; }
  .page-header p  { margin: 2px 0 0; font-size: .82rem; color: rgba(255,255,255,.75); }

  /* ── Cards ───────────────────────────────────── */
  .card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
  }
  .card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }
  .card-header .title { font-weight: 600; font-size: .95rem; color: var(--slate-900); }
  .card-body { padding: 20px; }

  /* ── Toolbar ─────────────────────────────────── */
  .toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
  .btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 16px;
    border-radius: 7px;
    font-family: 'DM Sans', sans-serif;
    font-size: .84rem; font-weight: 600;
    border: none; cursor: pointer;
    transition: all .18s ease;
    text-decoration: none;
  }
  .btn-primary   { background: var(--red-mid); color: #fff; }
  .btn-primary:hover { background: var(--red-deep); transform: translateY(-1px); box-shadow: var(--shadow-md); }
  .btn-outline   { background: transparent; border: 1.5px solid var(--border); color: var(--slate-700); }
  .btn-outline:hover { border-color: var(--red-mid); color: var(--red-mid); }
  .btn-success   { background: var(--success); color: #fff; }
  .btn-success:hover { filter: brightness(1.1); }
  .btn-danger    { background: transparent; border: 1.5px solid #E74C3C; color: var(--red-accent); }
  .btn-danger:hover  { background: var(--red-light); }
  .btn-sm { padding: 5px 11px; font-size: .78rem; }
  .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 7px; }

  /* ── Search Input ────────────────────────────── */
  .search-box {
    position: relative; flex: 1; min-width: 220px;
  }
  .search-box input {
    width: 100%; padding: 8px 12px 8px 36px;
    border: 1.5px solid var(--border); border-radius: 7px;
    font-family: 'DM Sans', sans-serif; font-size: .84rem;
    background: var(--slate-100); color: var(--slate-900);
    transition: border .18s;
  }
  .search-box input:focus { outline: none; border-color: var(--red-mid); background: #fff; }
  .search-box .icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--slate-300); font-size: .9rem; }

  /* ── Table ───────────────────────────────────── */
  .table-wrapper { overflow-x: auto; border-radius: 0 0 var(--radius) var(--radius); }
  table { width: 100%; border-collapse: collapse; font-size: .84rem; }
  thead tr { background: var(--slate-100); }
  thead th {
    padding: 11px 14px; text-align: left;
    font-weight: 600; font-size: .78rem; text-transform: uppercase;
    letter-spacing: .5px; color: var(--slate-500);
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
  }
  tbody tr { transition: background .15s; border-bottom: 1px solid var(--border); }
  tbody tr:hover { background: var(--red-light); }
  tbody td { padding: 11px 14px; vertical-align: middle; }

  /* ── Badge ───────────────────────────────────── */
  .badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 20px;
    font-size: .74rem; font-weight: 600; letter-spacing: .3px;
  }
  .badge-draft     { background: #EBF5FB; color: var(--info); }
  .badge-disetujui { background: #EAFAF1; color: var(--success); }
  .badge-ditolak   { background: var(--red-light); color: var(--red-accent); }

  /* ── Modal ───────────────────────────────────── */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(26,31,46,.55); backdrop-filter: blur(3px);
    z-index: 1000; align-items: center; justify-content: center;
  }
  .modal-overlay.open { display: flex; }
  .modal-box {
    background: #fff; border-radius: 14px;
    box-shadow: var(--shadow-lg);
    width: 720px; max-width: 97vw;
    max-height: 92vh; overflow-y: auto;
    animation: slideUp .22s ease;
  }
  @keyframes slideUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
  .modal-header {
    padding: 18px 24px;
    background: linear-gradient(135deg, var(--red-deep), var(--red-mid));
    border-radius: 14px 14px 0 0;
    display: flex; align-items: center; justify-content: space-between;
  }
  .modal-header h2 { font-size: 1rem; font-weight: 700; color: #fff; margin: 0; }
  .modal-close { background: rgba(255,255,255,.2); border: none; color: #fff; border-radius: 6px; width: 28px; height: 28px; cursor: pointer; font-size: 1rem; display: flex; align-items: center; justify-content: center; }
  .modal-body { padding: 24px; }
  .modal-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; gap: 10px; justify-content: flex-end; }

  /* ── Form ─────────────────────────────────────── */
  .form-row { display: grid; gap: 16px; }
  .form-row.cols-2 { grid-template-columns: 1fr 1fr; }
  .form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
  .form-group { display: flex; flex-direction: column; gap: 5px; }
  .form-group label { font-size: .78rem; font-weight: 600; color: var(--slate-500); text-transform: uppercase; letter-spacing: .4px; }
  .form-group input,
  .form-group select,
  .form-group textarea {
    padding: 9px 12px;
    border: 1.5px solid var(--border); border-radius: 7px;
    font-family: 'DM Sans', sans-serif; font-size: .88rem;
    color: var(--slate-900); background: #fff;
    transition: border .18s;
  }
  .form-group input:focus,
  .form-group select:focus,
  .form-group textarea:focus { outline: none; border-color: var(--red-mid); box-shadow: 0 0 0 3px rgba(192,57,43,.1); }
  .form-group input[readonly] { background: var(--slate-100); color: var(--slate-500); }

  /* ── Detail table inside modal ───────────────── */
  .detail-section { margin-top: 20px; }
  .detail-section .sec-title {
    font-size: .8rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--slate-500); margin-bottom: 10px;
  }
  .detail-add-row { display: flex; gap: 8px; margin-bottom: 10px; }
  .detail-add-row input {
    flex: 1; padding: 8px 12px; border: 1.5px solid var(--border); border-radius: 7px;
    font-family: 'DM Sans', sans-serif; font-size: .85rem;
  }
  .detail-add-row input:focus { outline: none; border-color: var(--red-mid); }

  #detailTable { width: 100%; border-collapse: collapse; font-size: .82rem; border-radius: 8px; overflow: hidden; border: 1px solid var(--border); }
  #detailTable thead th { background: var(--slate-100); padding: 9px 12px; font-weight: 600; font-size: .75rem; text-transform: uppercase; color: var(--slate-500); border-bottom: 1px solid var(--border); }
  #detailTable tbody td { padding: 9px 12px; border-bottom: 1px solid var(--border); }
  #detailTable tbody tr:last-child td { border-bottom: none; }
  #detailTable tbody tr:hover { background: var(--red-light); }

  /* ── Mono number ─────────────────────────────── */
  .mono { font-family: 'DM Mono', monospace; }

  /* ── Summary cards ───────────────────────────── */
  .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
  .summary-card {
    background: #fff; border-radius: var(--radius); padding: 16px 18px;
    border: 1px solid var(--border); box-shadow: var(--shadow-sm);
    display: flex; flex-direction: column; gap: 4px;
  }
  .summary-card .label { font-size: .74rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--slate-300); }
  .summary-card .value { font-size: 1.6rem; font-weight: 700; color: var(--slate-900); line-height: 1; }
  .summary-card .sub   { font-size: .75rem; color: var(--slate-500); }
  .summary-card.red-accent .value { color: var(--red-mid); }
  .summary-card.green-accent .value { color: var(--success); }

  /* ── Alasan badge ────────────────────────────── */
  .alasan-rusak     { background: #FFF3E0; color: #E65100; border-radius: 4px; padding: 2px 8px; font-size: .75rem; font-weight: 600; }
  .alasan-kadaluarsa{ background: #F3E5F5; color: #7B1FA2; border-radius: 4px; padding: 2px 8px; font-size: .75rem; font-weight: 600; }
  .alasan-lainnya   { background: #E8F5E9; color: #2E7D32; border-radius: 4px; padding: 2px 8px; font-size: .75rem; font-weight: 600; }

  /* ── Spinner ─────────────────────────────────── */
  .spinner { display: inline-block; width: 18px; height: 18px; border: 2.5px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: spin .6s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── Toast ───────────────────────────────────── */
  #toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 9999;
    padding: 12px 20px; border-radius: 9px; font-size: .86rem; font-weight: 600;
    color: #fff; box-shadow: var(--shadow-lg);
    transform: translateY(20px); opacity: 0;
    transition: all .3s ease; pointer-events: none;
  }
  #toast.show { transform: translateY(0); opacity: 1; }
  #toast.success { background: var(--success); }
  #toast.error   { background: var(--red-accent); }

  @media (max-width: 640px) {
    .form-row.cols-2, .form-row.cols-3 { grid-template-columns: 1fr; }
    .modal-box { width: 100vw; max-width: 100vw; border-radius: 14px 14px 0 0; }
    .page-header { padding: 16px 20px; }
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div class="icon-wrap">🩸</div>
  <div>
    <h1>Penyisihan Darah Rusak / Kadaluarsa</h1>
    <p>Manajemen penyisihan kantong darah yang rusak atau telah kadaluarsa</p>
  </div>
</div>

<div style="padding: 0 24px 24px;">

  {{-- Summary Cards --}}
  <div class="summary-grid" id="summaryCards">
    <div class="summary-card">
      <div class="label">Total Penyisihan</div>
      <div class="value" id="sumTotal">—</div>
      <div class="sub">Semua waktu</div>
    </div>
    <div class="summary-card red-accent">
      <div class="label">Draft</div>
      <div class="value" id="sumDraft">—</div>
      <div class="sub">Belum disetujui</div>
    </div>
    <div class="summary-card green-accent">
      <div class="label">Disetujui</div>
      <div class="value" id="sumDisetujui">—</div>
      <div class="sub">Sudah diproses</div>
    </div>
    <div class="summary-card">
      <div class="label">Kantong Terpisihkan</div>
      <div class="value" id="sumKantong">—</div>
      <div class="sub">Total kantong</div>
    </div>
  </div>

  {{-- Main Table Card --}}
  <div class="card">
    <div class="card-header">
      <span class="title">Daftar Penyisihan</span>
      <div class="toolbar">
        <div class="search-box">
          <span class="icon">🔍</span>
          <input type="text" id="searchInput" placeholder="Cari no. penyisihan…" oninput="debounceLoad()">
        </div>
        <select id="filterStatus" class="btn btn-outline" onchange="loadData()" style="padding:8px 12px;">
          <option value="">Semua Status</option>
          <option value="draft">Draft</option>
          <option value="disetujui">Disetujui</option>
          <option value="ditolak">Ditolak</option>
        </select>
        <button class="btn btn-primary" onclick="openModal()">
          <span>＋</span> Tambah
        </button>
      </div>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>No. Penyisihan</th>
            <th>Tgl Penyisihan</th>
            <th>Alasan</th>
            <th>Jml Kantong</th>
            <th>Status</th>
            <th>Petugas</th>
            <th style="text-align:center;">Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--slate-300);">Memuat data…</td></tr>
        </tbody>
      </table>
    </div>

    <div style="padding:14px 20px; display:flex; align-items:center; justify-content:space-between; border-top:1px solid var(--border);">
      <div id="paginationInfo" style="font-size:.8rem;color:var(--slate-500);"></div>
      <div id="paginationLinks" style="display:flex;gap:6px;"></div>
    </div>
  </div>
</div>

{{-- ═══════════════ MODAL FORM ═══════════════ --}}
<div class="modal-overlay" id="modalOverlay" onclick="closeOnBackdrop(event)">
  <div class="modal-box">
    <div class="modal-header">
      <h2 id="modalTitle">Tambah Penyisihan Darah</h2>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="editId">

      <div class="form-row cols-2" style="margin-bottom:16px;">
        <div class="form-group">
          <label>No. Penyisihan</label>
          <input type="text" id="noPenyisihan" readonly class="mono" placeholder="Auto-generate">
        </div>
        <div class="form-group">
          <label>Tanggal Penyisihan</label>
          <input type="date" id="tglPenyisihan">
        </div>
      </div>

      <div class="form-row cols-2" style="margin-bottom:16px;">
        <div class="form-group">
          <label>Alasan Penyisihan</label>
          <select id="alasan">
            <option value="">— Pilih Alasan —</option>
            <option value="tidak-terserap">Darah Tidak Terserap</option>
            <option value="uji-mutu">Uji Mutu</option>
            <option value="keruh">Keruh</option>
            <option value="DCT-Positif">DCT Positif / Mayor Positif</option>
            <option value="rusak">Darah Rusak</option>
            <option value="HBc-positif">HBc Positif</option>
            <option value="Bocor">Bocor</option>
            <option value="kadaluarsa">Kadaluarsa</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <input type="text" id="keterangan" placeholder="Opsional…">
        </div>
      </div>

      {{-- Detail Kantong --}}
      <div class="detail-section">
        <div class="sec-title">Detail Kantong Darah</div>
        <div class="detail-add-row">
          <input type="text" id="inputNoStok" placeholder="Masukkan No. Stok, tekan Enter atau klik Cari…"
                 onkeydown="if(event.key==='Enter'){cariStok();}">
          <button class="btn btn-outline btn-sm" onclick="cariStok()">🔍 Cari</button>
        </div>
        <div id="stokInfo" style="display:none; background:var(--red-light); border:1.5px solid #E8B4B8; border-radius:8px; padding:12px 14px; margin-bottom:10px; font-size:.83rem;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div id="stokInfoContent"></div>
            <button class="btn btn-primary btn-sm" onclick="addDetail()">＋ Tambahkan</button>
          </div>
        </div>
        <table id="detailTable">
          <thead>
            <tr>
              <th>No</th>
              <th>No Stok</th>
              <th>Jenis</th>
              <th>Gol</th>
              <th>Rhesus</th>
              <th>Tgl Aftap</th>
              <th>Tgl Expired</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="detailBody">
            <tr id="detailEmpty"><td colspan="8" style="text-align:center;padding:20px;color:var(--slate-300);">Belum ada kantong ditambahkan</td></tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal()">Batal</button>
      <button class="btn btn-primary" id="btnSimpan" onclick="simpanData()">
        💾 Simpan
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════ DETAIL VIEW MODAL ═══════════════ --}}
<div class="modal-overlay" id="modalDetail" onclick="closeOnBackdrop(event)">
  <div class="modal-box">
    <div class="modal-header">
      <h2>Detail Penyisihan</h2>
      <button class="modal-close" onclick="closeDetail()">✕</button>
    </div>
    <div class="modal-body" id="detailViewContent">
      <div style="text-align:center;padding:30px;color:var(--slate-300);">Memuat…</div>
    </div>
    <div class="modal-footer" id="detailViewFooter"></div>
  </div>
</div>

<div id="toast"></div>

@endsection

@push('scripts')
<script>
const BASE = '{{ route("penyimpanan.penyisihan_darah_rusak.index") }}';
const CSRF = '{{ csrf_token() }}';

let currentPage = 1;
let debounceTimer;
let detailList = []; // array of stok objects
let currentStok = null;

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('tglPenyisihan').value = today;
  loadData();
  loadSummary();
  fetchNomor();
});

function debounceLoad() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(loadData, 380);
}

// ── Load Table Data ───────────────────────────────────────────
async function loadData(page = 1) {
  currentPage = page;
  const search = document.getElementById('searchInput').value;
  const status = document.getElementById('filterStatus').value;
  const params = new URLSearchParams({ page, search, status, per_page: 10 });

  const res  = await fetch(`${BASE}/data?${params}`);
  const json = await res.json();
  renderTable(json);
}

function renderTable(json) {
  const tbody = document.getElementById('tableBody');
  const rows  = json.data;

  if (!rows.length) {
    tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--slate-300);">Tidak ada data.</td></tr>`;
    document.getElementById('paginationInfo').textContent = '';
    document.getElementById('paginationLinks').innerHTML = '';
    return;
  }

  tbody.innerHTML = rows.map((r, i) => {
    const offset = (json.current_page - 1) * json.per_page;
    const alasanBadge = `<span class="alasan-${r.alasan}">${r.alasan}</span>`;
    const statusBadge = `<span class="badge badge-${r.status}">${r.status}</span>`;

    return `<tr>
      <td style="color:var(--slate-300);">${offset + i + 1}</td>
      <td><span class="mono" style="font-weight:600;">${r.no_penyisihan}</span></td>
      <td>${formatDate(r.tgl_penyisihan)}</td>
      <td>${alasanBadge}</td>
      <td style="text-align:center;font-weight:600;">${r.details_count ?? r.details?.length ?? 0}</td>
      <td>${statusBadge}</td>
      <td style="color:var(--slate-500);font-size:.81rem;">${r.petugas?.name ?? '—'}</td>
      <td style="text-align:center;">
        <div style="display:flex;gap:6px;justify-content:center;">
          <button class="btn btn-outline btn-icon btn-sm" title="Detail" onclick="showDetail(${r.id})">👁</button>
          ${r.status === 'draft' ? `
          <button class="btn btn-outline btn-icon btn-sm" title="Edit" onclick="editData(${r.id})">✏️</button>
          <button class="btn btn-success btn-icon btn-sm" title="Setujui" onclick="approve(${r.id})">✔</button>
          <button class="btn btn-danger btn-icon btn-sm" title="Hapus" onclick="hapus(${r.id})">🗑</button>
          ` : ''}
        </div>
      </td>
    </tr>`;
  }).join('');

  // Pagination info
  document.getElementById('paginationInfo').textContent =
    `Menampilkan ${json.from ?? 0}–${json.to ?? 0} dari ${json.total} data`;

  // Pagination links
  const links = document.getElementById('paginationLinks');
  links.innerHTML = '';
  for (let p = 1; p <= json.last_page; p++) {
    const btn = document.createElement('button');
    btn.className = `btn btn-sm ${p === json.current_page ? 'btn-primary' : 'btn-outline'}`;
    btn.textContent = p;
    btn.onclick = () => loadData(p);
    links.appendChild(btn);
  }
}

// ── Summary ───────────────────────────────────────────────────
async function loadSummary() {
  // Kita hitung dari data (bisa dibuat endpoint tersendiri di controller)
  const res  = await fetch(`${BASE}/data?per_page=1000`);
  const json = await res.json();
  const all  = json.data;
  document.getElementById('sumTotal').textContent     = json.total ?? all.length;
  document.getElementById('sumDraft').textContent     = all.filter(r => r.status === 'draft').length;
  document.getElementById('sumDisetujui').textContent = all.filter(r => r.status === 'disetujui').length;
  document.getElementById('sumKantong').textContent   =
    all.reduce((acc, r) => acc + (r.details_count ?? r.details?.length ?? 0), 0);
}

// ── Nomor ─────────────────────────────────────────────────────
async function fetchNomor() {
  const res  = await fetch(`${BASE}/next-nomor`);
  const json = await res.json();
  document.getElementById('noPenyisihan').value = json.no_penyisihan;
}

// ── Cari Stok ─────────────────────────────────────────────────
async function cariStok() {
  const no = document.getElementById('inputNoStok').value.trim();
  if (!no) return;
  const res  = await fetch(`${BASE}/cari-stok?no_stok=${encodeURIComponent(no)}`);
  if (!res.ok) { showToast('Stok tidak ditemukan atau tidak tersedia.', 'error'); return; }
  currentStok = await res.json();
  const s = currentStok;
  document.getElementById('stokInfoContent').innerHTML = `
    <strong class="mono">${s.no_stok}</strong>
    &nbsp;|&nbsp; ${s.jenis_darah ?? '—'}
    &nbsp;|&nbsp; ${s.golongan_darah ?? '—'} ${s.rhesus ?? ''}
    &nbsp;|&nbsp; Aftap: ${formatDate(s.tgl_aftap)}
    &nbsp;|&nbsp; Expired: <span style="color:var(--red-mid);font-weight:600;">${formatDate(s.tgl_expired)}</span>
  `;
  document.getElementById('stokInfo').style.display = 'block';
}

function addDetail() {
  if (!currentStok) return;
  const dup = detailList.find(d => d.no_stok === currentStok.no_stok);
  if (dup) { showToast('Kantong sudah ditambahkan.', 'error'); return; }
  detailList.push(currentStok);
  renderDetailTable();
  document.getElementById('inputNoStok').value = '';
  document.getElementById('stokInfo').style.display = 'none';
  currentStok = null;
}

function removeDetail(noStok) {
  detailList = detailList.filter(d => d.no_stok !== noStok);
  renderDetailTable();
}

function renderDetailTable() {
  const tbody = document.getElementById('detailBody');
  const empty = document.getElementById('detailEmpty');
  if (!detailList.length) {
    tbody.innerHTML = `<tr id="detailEmpty"><td colspan="8" style="text-align:center;padding:20px;color:var(--slate-300);">Belum ada kantong ditambahkan</td></tr>`;
    return;
  }
  tbody.innerHTML = detailList.map((s, i) => `
    <tr>
      <td style="color:var(--slate-300);">${i + 1}</td>
      <td class="mono" style="font-weight:600;">${s.no_stok}</td>
      <td>${s.jenis_darah ?? '—'}</td>
      <td>${s.golongan_darah ?? '—'}</td>
      <td>${s.rhesus ?? '—'}</td>
      <td>${formatDate(s.tgl_aftap)}</td>
      <td style="color:var(--red-mid);font-weight:600;">${formatDate(s.tgl_expired)}</td>
      <td><button class="btn btn-danger btn-icon btn-sm" onclick="removeDetail('${s.no_stok}')">✕</button></td>
    </tr>
  `).join('');
}

// ── Modal Open / Close ────────────────────────────────────────
function openModal(reset = true) {
  if (reset) {
    document.getElementById('editId').value = '';
    document.getElementById('modalTitle').textContent = 'Tambah Penyisihan Darah';
    document.getElementById('alasan').value = '';
    document.getElementById('keterangan').value = '';
    detailList = [];
    renderDetailTable();
    document.getElementById('stokInfo').style.display = 'none';
    document.getElementById('inputNoStok').value = '';
    fetchNomor();
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tglPenyisihan').value = today;
  }
  document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('open');
}

function closeDetail() {
  document.getElementById('modalDetail').classList.remove('open');
}

function closeOnBackdrop(e) {
  if (e.target === e.currentTarget) {
    e.currentTarget.classList.remove('open');
  }
}

// ── Edit ──────────────────────────────────────────────────────
async function editData(id) {
  const res  = await fetch(`${BASE}/${id}`);
  const data = await res.json();
  document.getElementById('editId').value           = data.id;
  document.getElementById('noPenyisihan').value     = data.no_penyisihan;
  document.getElementById('tglPenyisihan').value    = data.tgl_penyisihan;
  document.getElementById('alasan').value           = data.alasan;
  document.getElementById('keterangan').value       = data.keterangan ?? '';
  document.getElementById('modalTitle').textContent = 'Edit Penyisihan Darah';

  detailList = (data.details ?? []).map(d => ({
    no_stok:        d.no_stok,
    jenis_darah:    d.jenis_darah,
    golongan_darah: d.golongan_darah,
    rhesus:         d.rhesus,
    tgl_aftap:      d.tgl_aftap,
    tgl_expired:    d.tgl_expired,
  }));
  renderDetailTable();
  openModal(false);
}

// ── Show Detail ───────────────────────────────────────────────
async function showDetail(id) {
  document.getElementById('modalDetail').classList.add('open');
  document.getElementById('detailViewContent').innerHTML =
    '<div style="text-align:center;padding:30px;color:var(--slate-300);">Memuat…</div>';

  const res  = await fetch(`${BASE}/${id}`);
  const d    = await res.json();

  const alasanBadge = `<span class="alasan-${d.alasan}">${d.alasan}</span>`;
  const statusBadge = `<span class="badge badge-${d.status}">${d.status}</span>`;

  document.getElementById('detailViewContent').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
      <div><div style="font-size:.74rem;color:var(--slate-300);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">No. Penyisihan</div>
           <div class="mono" style="font-weight:700;font-size:1rem;">${d.no_penyisihan}</div></div>
      <div><div style="font-size:.74rem;color:var(--slate-300);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Tanggal</div>
           <div>${formatDate(d.tgl_penyisihan)}</div></div>
      <div><div style="font-size:.74rem;color:var(--slate-300);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Alasan</div>
           <div>${alasanBadge}</div></div>
      <div><div style="font-size:.74rem;color:var(--slate-300);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Status</div>
           <div>${statusBadge}</div></div>
      <div><div style="font-size:.74rem;color:var(--slate-300);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Petugas</div>
           <div>${d.petugas?.name ?? '—'}</div></div>
      <div><div style="font-size:.74rem;color:var(--slate-300);text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;">Keterangan</div>
           <div>${d.keterangan ?? '—'}</div></div>
    </div>
    <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--slate-500);margin-bottom:8px;">Detail Kantong</div>
    <table id="detailTable">
      <thead>
        <tr>
          <th>No</th><th>No Stok</th><th>Jenis</th><th>Gol</th><th>Rhesus</th><th>Tgl Aftap</th><th>Tgl Expired</th>
        </tr>
      </thead>
      <tbody>
        ${(d.details ?? []).map((r, i) => `
          <tr>
            <td style="color:var(--slate-300);">${i+1}</td>
            <td class="mono" style="font-weight:600;">${r.no_stok}</td>
            <td>${r.jenis_darah ?? '—'}</td>
            <td>${r.golongan_darah ?? '—'}</td>
            <td>${r.rhesus ?? '—'}</td>
            <td>${formatDate(r.tgl_aftap)}</td>
            <td style="color:var(--red-mid);font-weight:600;">${formatDate(r.tgl_expired)}</td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  `;

  const footer = document.getElementById('detailViewFooter');
  footer.innerHTML = '';
  if (d.status === 'draft') {
    const btnApprove = document.createElement('button');
    btnApprove.className = 'btn btn-success';
    btnApprove.innerHTML = '✔ Setujui Penyisihan';
    btnApprove.onclick = () => { closeDetail(); approve(d.id); };
    footer.appendChild(btnApprove);
  }
  const btnClose = document.createElement('button');
  btnClose.className = 'btn btn-outline';
  btnClose.textContent = 'Tutup';
  btnClose.onclick = closeDetail;
  footer.appendChild(btnClose);
}

// ── Simpan ────────────────────────────────────────────────────
async function simpanData() {
  if (!detailList.length) { showToast('Tambahkan minimal 1 kantong.', 'error'); return; }
  const alasan = document.getElementById('alasan').value;
  if (!alasan) { showToast('Pilih alasan penyisihan.', 'error'); return; }

  const editId = document.getElementById('editId').value;
  const body   = {
    tgl_penyisihan: document.getElementById('tglPenyisihan').value,
    alasan,
    keterangan: document.getElementById('keterangan').value,
    details:    detailList.map(s => ({ no_stok: s.no_stok })),
  };

  const btn = document.getElementById('btnSimpan');
  btn.innerHTML = '<span class="spinner"></span> Menyimpan…';
  btn.disabled = true;

  const method  = editId ? 'PUT' : 'POST';
  const url     = editId ? `${BASE}/${editId}` : BASE;
  const res     = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify(body),
  });
  const json = await res.json();

  btn.innerHTML = '💾 Simpan';
  btn.disabled  = false;

  if (res.ok) {
    showToast(json.message, 'success');
    closeModal();
    loadData(currentPage);
    loadSummary();
  } else {
    showToast(json.message ?? 'Gagal menyimpan.', 'error');
  }
}

// ── Approve ───────────────────────────────────────────────────
async function approve(id) {
  if (!confirm('Setujui penyisihan ini? Stok akan dikurangi.')) return;
  const res  = await fetch(`${BASE}/${id}/approve`, {
    method: 'PUT', headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
  });
  const json = await res.json();
  showToast(json.message, res.ok ? 'success' : 'error');
  if (res.ok) { loadData(currentPage); loadSummary(); }
}

// ── Hapus ─────────────────────────────────────────────────────
async function hapus(id) {
  if (!confirm('Hapus data penyisihan ini?')) return;
  const res  = await fetch(`${BASE}/${id}`, {
    method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF },
  });
  const json = await res.json();
  showToast(json.message, res.ok ? 'success' : 'error');
  if (res.ok) { loadData(currentPage); loadSummary(); }
}

// ── Helpers ───────────────────────────────────────────────────
function formatDate(str) {
  if (!str) return '—';
  const d = new Date(str);
  return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' });
}

function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className   = `show ${type}`;
  setTimeout(() => { t.className = ''; }, 3200);
}
</script>
@endpush