@extends('layouts.index')

@section('title', 'Pengembalian Darah External')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════ TOKENS ══════════════════════════════════ */
:root {
  --blood:      #c0392b; --blood-dark: #962d22;
  --blood-light:#f8e8e7; --blood-glow: rgba(192,57,43,.16);
  --ink:        #1a1a2e; --slate:      #6b7280;
  --border:     #e5e7eb; --surface:    #ffffff;
  --bg2:        #f9fafb; --bg3:        #f3f4f6;
  --success:    #059669; --danger:     #dc2626; --warn: #d97706;
  --r:  12px; --rs: 8px;
  --sh: 0 1px 3px rgba(0,0,0,.08);
  --sh2:0 4px 16px rgba(0,0,0,.10);
  --sh3:0 12px 40px rgba(0,0,0,.15);
  --font:'DM Sans',sans-serif; --mono:'DM Mono',monospace;
  --ease:all .2s cubic-bezier(.4,0,.2,1);
}
*{box-sizing:border-box}
.pk{font-family:var(--font);color:var(--ink)}

/* ─── BANNER ─── */
.pk-banner{
  background:linear-gradient(135deg,var(--blood-dark) 0%,var(--blood) 55%,#e74c3c 100%);
  border-radius:var(--r);padding:26px 30px;margin-bottom:22px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 8px 28px var(--blood-glow);position:relative;overflow:hidden;
}
.pk-banner::before,.pk-banner::after{content:'';position:absolute;border-radius:50%;background:rgba(255,255,255,.06)}
.pk-banner::before{width:200px;height:200px;right:-50px;top:-60px}
.pk-banner::after{width:140px;height:140px;right:80px;bottom:-70px}
.pk-banner h1{font-size:21px;font-weight:700;color:#fff;margin:0 0 3px;letter-spacing:-.3px}
.pk-banner p{color:rgba(255,255,255,.72);font-size:13px;margin:0}
.pk-banner-icon{width:50px;height:50px;border-radius:13px;background:rgba(255,255,255,.18);
  display:flex;align-items:center;justify-content:center;font-size:24px;position:relative;z-index:1}

/* ─── STATS ─── */
.pk-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);
  padding:16px 18px;box-shadow:var(--sh);transition:var(--ease)}
.stat:hover{box-shadow:var(--sh2);transform:translateY(-2px)}
.stat.accent{border-left:3px solid var(--blood)}
.stat .lbl{font-size:11px;font-weight:600;color:var(--slate);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px}
.stat .val{font-size:26px;font-weight:700;font-family:var(--mono);color:var(--ink)}
.stat.accent .val{color:var(--blood)}
.stat .sub{font-size:11px;color:var(--slate);margin-top:3px}

/* ─── CARD ─── */
.pk-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);
  box-shadow:var(--sh);overflow:hidden}
.pk-card-head{padding:16px 22px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;background:var(--bg2)}
.pk-card-head h5{font-size:14px;font-weight:600;margin:0}

/* ─── FILTER BAR ─── */
.filter-bar{display:flex;gap:8px;flex-wrap:wrap;padding:14px 22px;
  background:var(--bg3);border-bottom:1px solid var(--border);align-items:center}
.filter-bar input,.filter-bar select{
  font-size:13px;border-radius:var(--rs);border:1px solid var(--border);
  background:var(--surface);padding:7px 11px;height:36px;font-family:var(--font);color:var(--ink)}
.filter-bar input:focus,.filter-bar select:focus{
  border-color:var(--blood);box-shadow:0 0 0 3px var(--blood-glow);outline:none}

/* ─── TABLE ─── */
.pk-table-wrap{overflow-x:auto}
.pk-table{width:100%;border-collapse:collapse;font-size:13px}
.pk-table thead th{
  background:var(--bg3);color:var(--slate);font-weight:600;font-size:11px;
  text-transform:uppercase;letter-spacing:.5px;
  border-bottom:1px solid var(--border);padding:11px 14px;white-space:nowrap}
.pk-table tbody td{padding:11px 14px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
.pk-table tbody tr:hover td{background:var(--blood-light)}
.pk-table tbody tr:last-child td{border-bottom:none}
.pk-table .empty-row td{text-align:center;padding:40px;color:var(--slate);font-size:13px}

/* ─── BADGES ─── */
.badge{display:inline-flex;align-items:center;gap:4px;
  padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge.selesai{background:#d1fae5;color:#065f46}
.badge.draft{background:#fef3c7;color:#92400e}

/* ─── MONO CHIP ─── */
.chip-no{font-family:var(--mono);font-size:11px;color:var(--blood);font-weight:500}

/* ─── ACTION BUTTONS ─── */
.btn-act{width:28px;height:28px;border-radius:7px;border:none;cursor:pointer;
  font-size:12px;display:inline-flex;align-items:center;justify-content:center;
  transition:var(--ease)}
.btn-act.view{background:#e0f2fe;color:#0277bd}
.btn-act.edit{background:#fef3c7;color:#b45309}
.btn-act.del {background:#fee2e2;color:#b91c1c}
.btn-act:hover{filter:brightness(.88);transform:scale(1.1)}

/* ─── PAGINATION ─── */
.pk-pagination{display:flex;align-items:center;justify-content:space-between;
  padding:12px 22px;border-top:1px solid var(--border);background:var(--bg2);font-size:13px}
.pk-pagination .info{color:var(--slate)}
.pg-btns{display:flex;gap:4px}
.pg-btn{min-width:32px;height:32px;border:1px solid var(--border);background:var(--surface);
  border-radius:var(--rs);font-size:13px;cursor:pointer;font-family:var(--font);
  display:inline-flex;align-items:center;justify-content:center;transition:var(--ease);padding:0 8px}
.pg-btn:hover{background:var(--bg3)}
.pg-btn.active{background:var(--blood);color:#fff;border-color:var(--blood)}
.pg-btn:disabled{opacity:.4;cursor:not-allowed}

/* ─── BUTTONS ─── */
.btn-p{background:var(--blood);color:#fff;border:none;border-radius:var(--rs);
  padding:8px 16px;font-size:13px;font-weight:600;font-family:var(--font);cursor:pointer;
  transition:var(--ease);display:inline-flex;align-items:center;gap:6px}
.btn-p:hover{background:var(--blood-dark);box-shadow:0 4px 12px var(--blood-glow);transform:translateY(-1px)}
.btn-s{background:var(--surface);color:var(--ink);border:1px solid var(--border);
  border-radius:var(--rs);padding:8px 16px;font-size:13px;font-weight:500;
  font-family:var(--font);cursor:pointer;transition:var(--ease);display:inline-flex;align-items:center;gap:6px}
.btn-s:hover{background:var(--bg3)}

/* ─── MODAL ─── */
.modal-pke .modal-dialog{max-width:820px}
.modal-pke .modal-content{border:none;border-radius:var(--r);box-shadow:var(--sh3);font-family:var(--font)}
.modal-pke .modal-header{background:linear-gradient(135deg,var(--blood-dark),var(--blood));
  color:#fff;border-radius:var(--r) var(--r) 0 0;padding:16px 22px;border-bottom:none}
.modal-pke .modal-title{font-size:15px;font-weight:700}
.modal-pke .modal-header .btn-close{filter:invert(1) brightness(2)}
.modal-pke .modal-body{padding:22px}
.modal-pke .modal-footer{padding:14px 22px;border-top:1px solid var(--border);
  background:var(--bg2);border-radius:0 0 var(--r) var(--r)}

/* ─── FORM ─── */
.flabel{font-size:11px;font-weight:600;color:var(--slate);text-transform:uppercase;
  letter-spacing:.4px;margin-bottom:5px;display:block}
.finput,.fselect,.ftextarea{
  width:100%;border:1.5px solid var(--border);border-radius:var(--rs);
  padding:8px 11px;font-size:13px;font-family:var(--font);
  transition:var(--ease);background:var(--surface);color:var(--ink)}
.finput:focus,.fselect:focus,.ftextarea:focus{
  border-color:var(--blood);box-shadow:0 0 0 3px var(--blood-glow);outline:none}
.finput[readonly]{background:var(--bg3);cursor:default}
.finput[readonly]:focus{border-color:var(--border);box-shadow:none}

/* ─── AUTOCOMPLETE ─── */
.ac-wrap{position:relative}
.ac-drop{
  position:absolute;top:calc(100% + 4px);left:0;right:0;
  background:var(--surface);border:1.5px solid var(--blood);
  border-radius:var(--rs);box-shadow:var(--sh2);
  z-index:9999;max-height:220px;overflow-y:auto;
  display:none; /* hidden by default */
}
.ac-drop.open{display:block}
.ac-item{
  padding:9px 13px;cursor:pointer;font-size:13px;font-family:var(--font);
  border-bottom:1px solid #f3f4f6;display:flex;flex-direction:column;gap:2px;
  transition:background .12s;
}
.ac-item:last-child{border-bottom:none}
.ac-item:hover,.ac-item.focused{background:var(--blood-light)}
.ac-item .ac-name{font-weight:600;color:var(--ink)}
.ac-item .ac-sub{font-size:11px;color:var(--slate);font-family:var(--mono)}
.ac-item.ac-empty{color:var(--slate);font-size:12px;cursor:default;padding:12px 13px}
.ac-item.ac-loading{color:var(--slate);font-size:12px;cursor:default;padding:12px 13px;
  display:flex;align-items:center;gap:8px}
.ac-spinner{width:14px;height:14px;border:2px solid var(--border);
  border-top-color:var(--blood);border-radius:50%;
  animation:spin .6s linear infinite;flex-shrink:0}

/* ─── STOK SEARCH ─── */
.stok-row{display:flex;gap:8px;margin-bottom:10px}
.stok-row .finput{flex:1}

/* ─── SECTION DIVIDER ─── */
.sdiv{display:flex;align-items:center;gap:10px;margin:18px 0 12px}
.sdiv::after{content:'';flex:1;height:1px;background:var(--border)}
.sdiv span{font-size:11px;font-weight:700;color:var(--slate);text-transform:uppercase;
  letter-spacing:.5px;white-space:nowrap}

/* ─── DETAIL TABLE ─── */
.dtbl-wrap{border:1px solid var(--border);border-radius:var(--rs);overflow:hidden;max-height:300px;overflow-y:auto}
.dtbl{width:100%;border-collapse:collapse;font-size:12px}
.dtbl thead th{background:var(--bg3);padding:8px 10px;text-align:left;
  font-weight:600;color:var(--slate);font-size:10px;text-transform:uppercase;letter-spacing:.4px;
  position:sticky;top:0;z-index:1}
.dtbl tbody td{padding:8px 10px;border-top:1px solid #f0f0f0;vertical-align:middle}
.dtbl tbody tr:hover td{background:var(--blood-light)}
.dtbl select,.dtbl input[type=text]{
  border:1px solid var(--border);border-radius:6px;padding:4px 7px;
  font-size:12px;font-family:var(--font);width:100%}
.dtbl select:focus,.dtbl input:focus{border-color:var(--blood);outline:none;box-shadow:0 0 0 2px var(--blood-glow)}
.btn-rm{background:#fee2e2;color:var(--danger);border:none;border-radius:6px;
  width:24px;height:24px;cursor:pointer;font-size:15px;transition:var(--ease);
  display:inline-flex;align-items:center;justify-content:center;line-height:1}
.btn-rm:hover{background:var(--danger);color:#fff}
.empty-dtbl{text-align:center;padding:22px;color:var(--slate)}

/* ─── VIEW INFO ─── */
.vrow{display:grid;grid-template-columns:140px 1fr;gap:3px 10px;font-size:13px;margin-bottom:5px}
.vrow .vl{color:var(--slate);font-weight:500}
.vrow .vv{font-weight:600}

@keyframes spin{to{transform:rotate(360deg)}}
@media(max-width:768px){
  .pk-stats{grid-template-columns:repeat(2,1fr)}
  .pk-banner{flex-direction:column;gap:12px;text-align:center}
}
</style>
@endpush

@section('content')
<div class="pk">

  {{-- BANNER --}}
  <div class="pk-banner">
    <div>
      <h1>Pengembalian Darah External</h1>
      <p>Manajemen pengembalian kantong darah dari institusi eksternal</p>
    </div>
    <div class="pk-banner-icon">🩸</div>
  </div>

  {{-- STATS --}}
  <div class="pk-stats">
    <div class="stat accent">
      <div class="lbl">Total Pengembalian</div>
      <div class="val" id="sTotal">—</div>
      <div class="sub">Semua periode</div>
    </div>
    <div class="stat">
      <div class="lbl">Bulan Ini</div>
      <div class="val" id="sBulan">—</div>
      <div class="sub">{{ now()->translatedFormat('F Y') }}</div>
    </div>
    <div class="stat">
      <div class="lbl">Hari Ini</div>
      <div class="val" id="sHari">—</div>
      <div class="sub">{{ now()->translatedFormat('d F Y') }}</div>
    </div>
    <div class="stat">
      <div class="lbl">Total Kantong</div>
      <div class="val" id="sKantong">—</div>
      <div class="sub">Semua item dikembalikan</div>
    </div>
  </div>

  {{-- TABLE CARD --}}
  <div class="pk-card">
    <div class="pk-card-head">
      <h5>Daftar Pengembalian Darah</h5>
      <button class="btn-p" id="btnTambah">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah
      </button>
    </div>

    {{-- FILTER --}}
    <div class="filter-bar">
      <input type="date"  id="fDari"   style="width:150px" title="Dari tanggal">
      <input type="date"  id="fSampai" style="width:150px" title="Sampai tanggal">
      <input type="text"  id="fSearch" style="width:210px" placeholder="🔍 Cari nomor / tujuan…">
      <select id="fStatus" style="width:130px">
        <option value="">Semua Status</option>
        <option value="selesai">Selesai</option>
        <option value="draft">Draft</option>
      </select>
      <button class="btn-s" id="btnReset" style="height:36px">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M3 12a9 9 0 109-9 9 9 0 00-4 .8"/><polyline points="3 3 3 8 8 8"/>
        </svg>
        Reset
      </button>
      <select id="fPerPage" style="width:90px">
        <option value="10">10</option><option value="25">25</option>
        <option value="50">50</option><option value="100">100</option>
      </select>
    </div>

    {{-- TABLE --}}
    <div class="pk-table-wrap">
      <table class="pk-table">
        <thead>
          <tr>
            <th style="width:44px">No</th>
            <th>No Pengembalian</th>
            <th>Tgl Pengembalian</th>
            <th>Tujuan Darah</th>
            <th>Petugas Terima</th>
            <th>Petugas Kembali</th>
            <th style="text-align:center;width:80px">Jml Item</th>
            <th style="text-align:center;width:90px">Status</th>
            <th style="text-align:center;width:100px">Aksi</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <tr class="empty-row"><td colspan="9" style="text-align:center;padding:40px;color:var(--slate)">Memuat data…</td></tr>
        </tbody>
      </table>
    </div>

    {{-- PAGINATION --}}
    <div class="pk-pagination">
      <span class="info" id="pgInfo">—</span>
      <div class="pg-btns" id="pgBtns"></div>
    </div>
  </div>

</div>

{{-- ═══════════════ MODAL FORM ═══════════════ --}}
<div class="modal fade modal-pke" id="modalForm" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="mfTitle">Tambah Pengembalian Darah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">

          {{-- No. Pengembalian --}}
          <div class="col-md-6">
            <label class="flabel">No. Pengembalian</label>
            <input id="fNo" class="finput" readonly style="font-family:var(--mono);font-weight:600;color:var(--blood)">
          </div>

          {{-- Tanggal --}}
          <div class="col-md-6">
            <label class="flabel">Tanggal Pengembalian <span style="color:var(--blood)">*</span></label>
            <input type="date" id="fTgl" class="finput">
          </div>

          {{-- Tujuan Darah — AUTOCOMPLETE --}}
          <div class="col-md-6">
            <label class="flabel">Tujuan Darah</label>
            <div class="ac-wrap">
              <input type="text"   id="fTujuan"     class="finput" placeholder="Ketik nama institusi / kode…" autocomplete="off">
              <input type="hidden" id="fTujuanKode">
              <div class="ac-drop" id="acTujuan"></div>
            </div>
          </div>

          {{-- Petugas Terima — AUTOCOMPLETE --}}
          <div class="col-md-6">
            <label class="flabel">Petugas Terima</label>
            <div class="ac-wrap">
              <input type="text"   id="fPetugasTerima"   class="finput" placeholder="Ketik nama / kode petugas…" autocomplete="off">
              <input type="hidden" id="fPetugasTerimaId">
              <div class="ac-drop" id="acTerima"></div>
            </div>
          </div>

          {{-- Petugas Kembali — AUTOCOMPLETE --}}
          <div class="col-md-6">
            <label class="flabel">Petugas Kembali</label>
            <div class="ac-wrap">
              <input type="text"   id="fPetugasKembali"   class="finput" placeholder="Ketik nama / kode petugas…" autocomplete="off">
              <input type="hidden" id="fPetugasKembaliId">
              <div class="ac-drop" id="acKembali"></div>
            </div>
          </div>

          {{-- Keterangan --}}
          <div class="col-md-6">
            <label class="flabel">Keterangan</label>
            <input type="text" id="fKet" class="finput" placeholder="Catatan tambahan…">
          </div>

        </div>

        <div class="sdiv"><span>Detail Kantong Darah</span></div>

        <div class="stok-row">
          <input type="text" id="inputStok" class="finput" placeholder="Scan / ketik No. Stok lalu tekan Enter…">
          <button class="btn-p" id="btnCari" style="flex-shrink:0;white-space:nowrap">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            Cari Stok
          </button>
        </div>

        <div class="dtbl-wrap">
          <table class="dtbl">
            <thead>
              <tr>
                <th>#</th><th>No Stok</th><th>Jenis</th><th>Gol/Rh</th>
                <th>Aftap</th><th>Kadaluarsa</th><th>Stat Stok</th>
                <th>Stat Kembali *</th><th>Alasan Kembali</th><th></th>
              </tr>
            </thead>
            <tbody id="dtBody"></tbody>
          </table>
        </div>
        <div style="margin-top:7px;font-size:12px;color:var(--slate)">
          <span id="dtCount">0</span> kantong dipilih
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-s" data-bs-dismiss="modal">Batal</button>
        <button class="btn-p" id="btnSimpan">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
          </svg>
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════ MODAL VIEW ═══════════════ --}}
<div class="modal fade modal-pke" id="modalView" tabindex="-1">
  <div class="modal-dialog" style="max-width:900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Pengembalian Darah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-3" id="vHeader"></div>
        <div class="sdiv"><span>Detail Kantong</span></div>
        <div class="dtbl-wrap" style="max-height:360px">
          <table class="dtbl">
            <thead>
              <tr>
                <th>#</th><th>No Stok</th><th>Jenis</th><th>Gol/Rh</th>
                <th>Aftap</th><th>Kadaluarsa</th><th>Stat Stok</th>
                <th>Stat Kembali</th><th>Alasan</th>
              </tr>
            </thead>
            <tbody id="vBody"></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn-s" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════
   ROUTES
═══════════════════════════════════════════════════════ */
const R = {
  data         : '{{ route("penyimpanan.pengembalian_darah_external.data") }}',
  next         : '{{ route("penyimpanan.pengembalian_darah_external.nextNomor") }}',
  cari         : '{{ route("penyimpanan.pengembalian_darah_external.cariStok") }}',
  store        : '{{ route("penyimpanan.pengembalian_darah_external.store") }}',
  searchTujuan : '{{ route("penyimpanan.pengembalian_darah_external.searchTujuan") }}',
  searchPetugas: '{{ route("penyimpanan.pengembalian_darah_external.searchPetugas") }}',
  show         : id => `{{ url("/penyimpanan/pengembalian_darah_external") }}/${id}`,
  update       : id => `{{ url("/penyimpanan/pengembalian_darah_external") }}/${id}`,
  destroy      : id => `{{ url("/penyimpanan/pengembalian_darah_external") }}/${id}`,
};

/* ═══════════════════════════════════════════════════════
   STATE
═══════════════════════════════════════════════════════ */
let state = {
  page:1, perPage:10, search:'', dari:'', sampai:'', status:'', total:0, loading:false,
};
let editId = null;
let items  = [];

/* ═══════════════════════════════════════════════════════
   AUTOCOMPLETE ENGINE
   Membuat satu instance autocomplete yang reusable
   @param cfg = {
     inputId   : string   — id input text
     dropId    : string   — id div dropdown
     hiddenId  : string   — id hidden input (simpan id/kode)
     url       : string   — endpoint search (GET ?q=...)
     labelKey  : string   — key untuk label utama (default 'nama')
     subKey    : string   — key untuk sub-label  (default 'kode')
     valueKey  : string   — key untuk value hidden (default 'id')
   }
═══════════════════════════════════════════════════════ */
function makeAutocomplete(cfg) {
  const input  = document.getElementById(cfg.inputId);
  const drop   = document.getElementById(cfg.dropId);
  const hidden = document.getElementById(cfg.hiddenId);
  if (!input || !drop) return;

  const labelKey = cfg.labelKey  ?? 'nama';
  const subKey   = cfg.subKey    ?? 'kode';
  const valueKey = cfg.valueKey  ?? 'id';

  let debTimer, focusIdx = -1, lastResults = [];

  // ─── Highlight teks pencarian
  function highlight(text, q) {
    if (!q) return escHtml(text);
    const re = new RegExp(`(${q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi');
    return escHtml(text).replace(re, '<mark style="background:var(--blood-light);color:var(--blood);font-weight:700;border-radius:2px">$1</mark>');
  }

  function escHtml(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  // ─── Render dropdown
  function renderDrop(results, q) {
    lastResults = results;
    focusIdx    = -1;
    if (!results.length) {
      drop.innerHTML = `<div class="ac-item ac-empty">Tidak ada hasil untuk "<b>${escHtml(q)}</b>"</div>`;
    } else {
      drop.innerHTML = results.map((r, i) => `
        <div class="ac-item" data-idx="${i}" onmousedown="event.preventDefault()">
          <span class="ac-name">${highlight(r[labelKey], q)}</span>
          ${r[subKey] ? `<span class="ac-sub">${escHtml(r[subKey])}</span>` : ''}
        </div>`).join('');

      drop.querySelectorAll('.ac-item').forEach(el => {
        el.addEventListener('click', () => pick(+el.dataset.idx));
      });
    }
    drop.classList.add('open');
  }

  function showLoading() {
    drop.innerHTML = `<div class="ac-item ac-loading"><span class="ac-spinner"></span>Mencari…</div>`;
    drop.classList.add('open');
  }

  function closeDrop() {
    drop.classList.remove('open');
    focusIdx = -1;
  }

  // ─── Pilih item
  function pick(idx) {
    const r = lastResults[idx];
    if (!r) return;
    input.value  = r[labelKey] ?? '';
    if (hidden) hidden.value = r[valueKey] ?? '';
    closeDrop();
    // Trigger event supaya kode luar bisa hook
    input.dispatchEvent(new CustomEvent('ac:select', { detail: r, bubbles:true }));
  }

  // ─── Keyboard navigation
  function moveFocus(dir) {
    const items = drop.querySelectorAll('.ac-item:not(.ac-empty):not(.ac-loading)');
    if (!items.length) return;
    items[focusIdx]?.classList.remove('focused');
    focusIdx = (focusIdx + dir + items.length) % items.length;
    items[focusIdx]?.classList.add('focused');
    items[focusIdx]?.scrollIntoView({ block:'nearest' });
  }

  input.addEventListener('keydown', e => {
    if (!drop.classList.contains('open')) return;
    if (e.key === 'ArrowDown') { e.preventDefault(); moveFocus(1); }
    else if (e.key === 'ArrowUp')   { e.preventDefault(); moveFocus(-1); }
    else if (e.key === 'Enter') {
      e.preventDefault();
      if (focusIdx >= 0) pick(focusIdx);
    }
    else if (e.key === 'Escape') closeDrop();
  });

  // ─── Input event — debounce 300ms
  input.addEventListener('input', () => {
    const q = input.value.trim();
    if (hidden) hidden.value = ''; // reset pilihan saat mengetik ulang

    if (!q) { closeDrop(); return; }

    clearTimeout(debTimer);
    showLoading();
    debTimer = setTimeout(async () => {
      try {
        const res  = await fetch(`${cfg.url}?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        renderDrop(Array.isArray(data) ? data : (data.data ?? []), q);
      } catch {
        drop.innerHTML = `<div class="ac-item ac-empty" style="color:var(--danger)">Gagal memuat data</div>`;
      }
    }, 300);
  });

  // ─── Focus: tampilkan dropdown jika sudah ada teks
  input.addEventListener('focus', () => {
    const q = input.value.trim();
    if (q && !drop.classList.contains('open')) {
      input.dispatchEvent(new Event('input'));
    }
  });

  // ─── Blur: tutup dropdown setelah sedikit delay (beri waktu klik)
  input.addEventListener('blur', () => {
    setTimeout(closeDrop, 180);
  });

  // Expose fungsi set dari luar (untuk mode edit)
  return {
    setValue(label, value) {
      input.value  = label  ?? '';
      if (hidden) hidden.value = value ?? '';
    },
    clear() {
      input.value  = '';
      if (hidden) hidden.value = '';
      closeDrop();
    },
  };
}

/* ═══════════════════════════════════════════════════════
   LOAD TABLE
═══════════════════════════════════════════════════════ */
async function loadTable() {
  if (state.loading) return;
  state.loading = true;

  const tbody = document.getElementById('tableBody');
  tbody.innerHTML = `<tr class="empty-row"><td colspan="9" style="text-align:center;padding:36px">
    <div style="display:inline-flex;gap:8px;align-items:center;color:var(--slate)">
      <span style="width:16px;height:16px;border:2px solid var(--blood);border-top-color:transparent;
        border-radius:50%;display:inline-block;animation:spin .6s linear infinite"></span>
      Memuat data…
    </div></td></tr>`;

  const params = new URLSearchParams({
    page:state.page, length:state.perPage, search:state.search,
    tgl_dari:state.dari, tgl_sampai:state.sampai, status:state.status, draw:Date.now(),
  });

  try {
    const res  = await fetch(`${R.data}?${params}`);
    const data = await res.json();
    state.total   = data.recordsFiltered ?? 0;
    state.loading = false;
    renderTable(data.data ?? []);
    renderPagination();
    updateStats(data);
  } catch(e) {
    state.loading = false;
    tbody.innerHTML = `<tr class="empty-row"><td colspan="9" style="text-align:center;padding:36px;color:var(--danger)">
      Gagal memuat data. <button class="btn-s" onclick="loadTable()" style="margin-left:8px">Coba lagi</button>
    </td></tr>`;
  }
}

function renderTable(rows) {
  const tbody = document.getElementById('tableBody');
  if (!rows.length) {
    tbody.innerHTML = `<tr class="empty-row"><td colspan="9" style="text-align:center;padding:40px;color:var(--slate)">
      <div style="font-size:32px;margin-bottom:8px">📭</div>
      Tidak ada data pengembalian darah</td></tr>`;
    return;
  }
  const offset = (state.page - 1) * state.perPage;
  tbody.innerHTML = rows.map((r, i) => `
    <tr>
      <td style="color:var(--slate);font-size:12px">${offset + i + 1}</td>
      <td><span class="chip-no">${r.no_pengembalian}</span></td>
      <td>${r.tgl_pengembalian}</td>
      <td>${r.tujuan_darah ?? '<span style="color:var(--slate)">—</span>'}</td>
      <td>${r.petugas_terima ?? '<span style="color:var(--slate)">—</span>'}</td>
      <td>${r.petugas_kembali ?? '<span style="color:var(--slate)">—</span>'}</td>
      <td style="text-align:center">
        <span style="font-family:var(--mono);font-weight:600">${r.jumlah_item}</span>
      </td>
      <td style="text-align:center">
        <span class="badge ${r.status}">${cap(r.status)}</span>
      </td>
      <td style="text-align:center">
        <div style="display:flex;gap:4px;justify-content:center">
          <button class="btn-act view" onclick="openView(${r.id})" title="Lihat Detail">👁</button>
          <button class="btn-act edit" onclick="openEdit(${r.id})" title="Edit">✏️</button>
          <button class="btn-act del"  onclick="hapus(${r.id})"   title="Hapus">🗑</button>
        </div>
      </td>
    </tr>`).join('');
}

function renderPagination() {
  const totalPages = Math.ceil(state.total / state.perPage) || 1;
  document.getElementById('pgInfo').textContent =
    state.total
      ? `Menampilkan ${(state.page-1)*state.perPage+1}–${Math.min(state.page*state.perPage, state.total)} dari ${state.total} data`
      : 'Tidak ada data';

  const btns  = document.getElementById('pgBtns');
  const pages = paginate(state.page, totalPages);
  btns.innerHTML = `
    <button class="pg-btn" onclick="goPage(${state.page-1})" ${state.page<=1?'disabled':''}>‹</button>
    ${pages.map(p => p === '…'
      ? `<button class="pg-btn" disabled>…</button>`
      : `<button class="pg-btn ${p===state.page?'active':''}" onclick="goPage(${p})">${p}</button>`
    ).join('')}
    <button class="pg-btn" onclick="goPage(${state.page+1})" ${state.page>=totalPages?'disabled':''}>›</button>`;
}

function paginate(cur, total) {
  if (total <= 7) return Array.from({length:total},(_,i)=>i+1);
  if (cur <= 4)   return [1,2,3,4,5,'…',total];
  if (cur >= total-3) return [1,'…',total-4,total-3,total-2,total-1,total];
  return [1,'…',cur-1,cur,cur+1,'…',total];
}

function goPage(p) {
  const max = Math.ceil(state.total / state.perPage) || 1;
  if (p < 1 || p > max) return;
  state.page = p; loadTable();
}

function updateStats(data) {
  document.getElementById('sTotal').textContent   = data.recordsTotal  ?? '—';
  document.getElementById('sBulan').textContent   = data.bulan_ini     ?? '—';
  document.getElementById('sHari').textContent    = data.hari_ini      ?? '—';
  document.getElementById('sKantong').textContent = data.total_kantong ?? '—';
}

/* ═══════════════════════════════════════════════════════
   FILTERS
═══════════════════════════════════════════════════════ */
let debTimer;
document.getElementById('fSearch').addEventListener('input', e => {
  clearTimeout(debTimer);
  debTimer = setTimeout(() => { state.search = e.target.value; state.page = 1; loadTable(); }, 350);
});
['fDari','fSampai','fStatus'].forEach(id => {
  document.getElementById(id).addEventListener('change', () => {
    state.dari   = document.getElementById('fDari').value;
    state.sampai = document.getElementById('fSampai').value;
    state.status = document.getElementById('fStatus').value;
    state.page   = 1; loadTable();
  });
});
document.getElementById('fPerPage').addEventListener('change', e => {
  state.perPage = +e.target.value; state.page = 1; loadTable();
});
document.getElementById('btnReset').addEventListener('click', () => {
  ['fDari','fSampai','fSearch','fStatus'].forEach(id => document.getElementById(id).value = '');
  state = { ...state, page:1, search:'', dari:'', sampai:'', status:'' };
  loadTable();
});

/* ═══════════════════════════════════════════════════════
   INIT AUTOCOMPLETE INSTANCES
   (dibuat setelah DOM ready)
═══════════════════════════════════════════════════════ */
let acTujuan, acTerima, acKembali;

document.addEventListener('DOMContentLoaded', () => {
  acTujuan = makeAutocomplete({
    inputId  : 'fTujuan',
    dropId   : 'acTujuan',
    hiddenId : 'fTujuanKode',
    url      : R.searchTujuan,
    labelKey : 'nama',
    subKey   : 'kode',
    valueKey : 'kode',   // simpan kode ke kolom tujuan_darah (string)
  });

  acTerima = makeAutocomplete({
    inputId  : 'fPetugasTerima',
    dropId   : 'acTerima',
    hiddenId : 'fPetugasTerimaId',
    url      : R.searchPetugas,
    labelKey : 'nama',
    subKey   : 'kode',
    valueKey : 'id',
  });

  acKembali = makeAutocomplete({
    inputId  : 'fPetugasKembali',
    dropId   : 'acKembali',
    hiddenId : 'fPetugasKembaliId',
    url      : R.searchPetugas,
    labelKey : 'nama',
    subKey   : 'kode',
    valueKey : 'id',
  });

  loadTable();
});

/* ═══════════════════════════════════════════════════════
   MODAL FORM — TAMBAH
═══════════════════════════════════════════════════════ */
document.getElementById('btnTambah').addEventListener('click', async () => {
  editId = null; items = [];
  resetForm();
  document.getElementById('mfTitle').textContent = 'Tambah Pengembalian Darah';
  const r = await fetch(R.next);
  const d = await r.json();
  document.getElementById('fNo').value  = d.nomor;
  document.getElementById('fTgl').value = today();
  renderDtbl();
  new bootstrap.Modal('#modalForm').show();
});

/* ─── EDIT ─── */
async function openEdit(id) {
  editId = id; items = [];
  resetForm();
  document.getElementById('mfTitle').textContent = 'Edit Pengembalian Darah';
  const r = await fetch(R.show(id));
  const d = await r.json();

  set('fNo',  d.no_pengembalian);
  set('fTgl', d.tgl_pengembalian?.substring(0,10));
  set('fKet', d.keterangan ?? '');

  // Isi autocomplete dengan data dari server
  acTujuan?.setValue(d.tujuan_darah?.nama ?? d.tujuan_darah ?? '', d.tujuan_darah?.kode ?? d.tujuan_darah ?? '');
  acTerima?.setValue(d.petugas_terima?.nama ?? '', d.petugas_terima_id ?? '');
  acKembali?.setValue(d.petugas_kembali?.nama ?? '', d.petugas_kembali_id ?? '');

  items = (d.details ?? []).map(x => ({
    no_stok        : x.no_stok,
    jenis_darah    : x.jenis_darah,
    golongan_darah : x.golongan_darah,
    rhesus         : x.rhesus,
    tgl_aftap      : x.tgl_aftap?.substring(0,10),
    tgl_expired    : x.tgl_expired?.substring(0,10),
    status_stok    : x.status_stok,
    status_kembali : x.status_kembali ?? '',
    alasan_kembali : x.alasan_kembali ?? '',
  }));
  renderDtbl();
  new bootstrap.Modal('#modalForm').show();
}

/* ─── CARI STOK ─── */
document.getElementById('btnCari').addEventListener('click', cariStok);
document.getElementById('inputStok').addEventListener('keydown', e => {
  if (e.key === 'Enter') { e.preventDefault(); cariStok(); }
});

async function cariStok() {
  const no = document.getElementById('inputStok').value.trim();
  if (!no) return;
  if (items.find(x => x.no_stok === no))
    return swal('Info', 'No. Stok sudah ada di daftar.', 'info');

  try {
    const r = await fetch(`${R.cari}?no_stok=${encodeURIComponent(no)}`);
    if (!r.ok) {
      const e = await r.json();
      return swal('Tidak Ditemukan', e.message, 'warning');
    }
    const s = await r.json();
    items.push({
      no_stok        : s.no_stok,
      no_kantong: s.no_kantong,
      jenis_darah    : s.jenis_darah,
      golongan_darah : s.golongan_darah,
      rhesus         : s.rhesus,
      tgl_aftap      : s.tgl_aftap?.substring(0,10),
      tgl_expired    : s.tgl_expired?.substring(0,10),
      status_stok    : s.status_stok,
      status_kembali : '',
      alasan_kembali : '',
    });
    renderDtbl();
    document.getElementById('inputStok').value = '';
    document.getElementById('inputStok').focus();
  } catch { swal('Error', 'Gagal menghubungi server.', 'error'); }
}

/* ─── RENDER DETAIL TABLE ─── */
const STATUS_OPTS = ['baik','rusak','kadaluarsa','cacat','lainnya'];
const ALASAN_OPTS = ['Tidak terpakai','Salah kirim','Stok berlebih','Permintaan dibatalkan','Lainnya'];

function renderDtbl() {
  const tb = document.getElementById('dtBody');
  document.getElementById('dtCount').textContent = items.length;

  if (!items.length) {
    tb.innerHTML = `<tr><td colspan="10" class="empty-dtbl">
      <div style="font-size:28px;margin-bottom:6px">📋</div>
      Belum ada kantong darah ditambahkan</td></tr>`;
    return;
  }

  tb.innerHTML = items.map((it, i) => `
    <tr>
      <td style="font-family:var(--mono);font-size:11px;color:var(--slate)">${i+1}</td>
      <td><b class="chip-no">${it.no_stok}</b></td>
      <td>${it.jenis_darah ?? '—'}</td>
      <td><b>${(it.golongan_darah??'')+(it.rhesus??'')}</b></td>
      <td style="font-size:11px">${fmt(it.tgl_aftap)}</td>
      <td style="font-size:11px;color:${isExp(it.tgl_expired)?'var(--danger)':'inherit'}">${fmt(it.tgl_expired)}</td>
      <td><span style="font-size:11px;padding:2px 7px;border-radius:10px;background:var(--bg3)">${it.status_stok??'—'}</span></td>
      <td>
        <select onchange="upItem(${i},'status_kembali',this.value)" style="width:95px">
          <option value="">— Pilih —</option>
          ${STATUS_OPTS.map(o=>`<option value="${o}"${it.status_kembali===o?' selected':''}>${cap(o)}</option>`).join('')}
        </select>
      </td>
      <td>
        <select onchange="upItem(${i},'alasan_kembali',this.value)" style="width:130px">
          <option value="">— Pilih —</option>
          ${ALASAN_OPTS.map(o=>`<option value="${o}"${it.alasan_kembali===o?' selected':''}>${o}</option>`).join('')}
        </select>
      </td>
      <td><button class="btn-rm" onclick="rmItem(${i})">×</button></td>
    </tr>`).join('');
}

function upItem(i, f, v) { items[i][f] = v; }
function rmItem(i)       { items.splice(i,1); renderDtbl(); }

/* ─── SIMPAN ─── */
document.getElementById('btnSimpan').addEventListener('click', async () => {
  if (!document.getElementById('fTgl').value)
    return toast('Tanggal pengembalian wajib diisi.', 'warning');
  if (!items.length)
    return toast('Tambahkan minimal 1 kantong darah.', 'warning');
  if (items.some(x => !x.status_kembali))
    return toast('Status kembali wajib diisi untuk semua item.', 'warning');

  const payload = {
    tgl_pengembalian   : document.getElementById('fTgl').value,
    // tujuan_darah disimpan sebagai string (kode) sesuai skema tabel
    tujuan_darah       : document.getElementById('fTujuanKode').value || document.getElementById('fTujuan').value || null,
    petugas_terima_id  : document.getElementById('fPetugasTerimaId').value  || null,
    petugas_kembali_id : document.getElementById('fPetugasKembaliId').value || null,
    keterangan         : document.getElementById('fKet').value || null,
    details            : items,
  };

  const url = editId ? R.update(editId) : R.store;
  const met = editId ? 'PUT' : 'POST';

  setBtnLoading('btnSimpan', true);
  try {
    const r = await xfetch(url, met, payload);
    const b = await r.json();
    if (!r.ok) { swal('Gagal', b.message, 'error'); return; }
    bootstrap.Modal.getInstance('#modalForm').hide();
    loadTable();
    toast(b.message, 'success');
  } catch { swal('Error', 'Terjadi kesalahan sistem.', 'error'); }
  finally { setBtnLoading('btnSimpan', false); }
});

/* ═══════════════════════════════════════════════════════
   VIEW
═══════════════════════════════════════════════════════ */
async function openView(id) {
  const r = await fetch(R.show(id));
  const d = await r.json();

  document.getElementById('vHeader').innerHTML = `
    <div class="col-md-6">${vRow('No Pengembalian',`<span style="font-family:var(--mono);color:var(--blood);font-weight:700">${d.no_pengembalian}</span>`)}</div>
    <div class="col-md-6">${vRow('Tgl Pengembalian', fmt(d.tgl_pengembalian?.substring(0,10)))}</div>
    <div class="col-md-6">${vRow('Tujuan Darah', d.tujuan_darah?.nama ?? d.tujuan_darah ?? '—')}</div>
    <div class="col-md-6">${vRow('Status', `<span class="badge ${d.status}">${cap(d.status)}</span>`)}</div>
    <div class="col-md-6">${vRow('Petugas Terima',  d.petugas_terima?.nama ?? '—')}</div>
    <div class="col-md-6">${vRow('Petugas Kembali', d.petugas_kembali?.nama ?? '—')}</div>
    ${d.keterangan ? `<div class="col-12">${vRow('Keterangan', d.keterangan)}</div>` : ''}
  `;

  document.getElementById('vBody').innerHTML = (d.details ?? []).length
    ? (d.details).map((x,i) => `
        <tr>
          <td style="color:var(--slate);font-size:11px">${i+1}</td>
          <td><b class="chip-no">${x.no_stok}</b></td>
          <td>${x.jenis_darah??'—'}</td>
          <td><b>${(x.golongan_darah??'')+(x.rhesus??'')}</b></td>
          <td style="font-size:11px">${fmt(x.tgl_aftap?.substring(0,10))}</td>
          <td style="font-size:11px;color:${isExp(x.tgl_expired?.substring(0,10))?'var(--danger)':'inherit'}">${fmt(x.tgl_expired?.substring(0,10))}</td>
          <td>${x.status_stok??'—'}</td>
          <td><span class="badge" style="background:var(--bg3);color:var(--ink)">${x.status_kembali??'—'}</span></td>
          <td>${x.alasan_kembali??'—'}</td>
        </tr>`).join('')
    : `<tr><td colspan="9" class="empty-dtbl">Tidak ada detail</td></tr>`;

  new bootstrap.Modal('#modalView').show();
}

/* ═══════════════════════════════════════════════════════
   HAPUS
═══════════════════════════════════════════════════════ */
async function hapus(id) {
  if (!confirm('Hapus data pengembalian ini?\nSaldo stok akan di-rollback otomatis.')) return;
  const r = await xfetch(R.destroy(id), 'DELETE');
  const b = await r.json();
  if (!r.ok) return swal('Gagal', b.message, 'error');
  loadTable();
  toast(b.message, 'success');
}

/* ═══════════════════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════════════════ */
function resetForm() {
  ['fNo','fTgl','fKet','inputStok'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
  // Reset autocomplete
  acTujuan?.clear();
  acTerima?.clear();
  acKembali?.clear();
}

function set(id, v)  { const el = document.getElementById(id); if(el) el.value = v ?? ''; }
function today()     { return new Date().toISOString().substring(0,10); }
function fmt(d)      { if (!d) return '—'; const [y,m,dd]=d.split('-'); return `${dd}-${m}-${y}`; }
function isExp(d)    { return d && d < today(); }
function cap(s)      { return s ? s.charAt(0).toUpperCase()+s.slice(1) : ''; }
function vRow(l,v)   { return `<div class="vrow"><span class="vl">${l}</span><span class="vv">${v}</span></div>`; }

function toast(msg, type='info') {
  if (window.Swal) {
    Swal.fire({ icon:type, text:msg, toast:true, position:'top-end', timer:2500, showConfirmButton:false });
  } else { alert(msg); }
}
function swal(title, text, icon) {
  if (window.Swal) Swal.fire({title, text, icon});
  else alert(`${title}\n${text}`);
}
function setBtnLoading(id, loading) {
  const btn = document.getElementById(id);
  if (!btn) return;
  btn.disabled = loading;
  btn.style.opacity = loading ? '.7' : '1';
}
function xfetch(url, method='GET', body=null) {
  const opts = {
    method,
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN' : document.querySelector('meta[name=csrf-token]')?.content ?? '',
    },
  };
  if (body) opts.body = JSON.stringify(body);
  return fetch(url, opts);
}
</script>
@endpush