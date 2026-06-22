@extends('layouts.index')

@section('title', 'Pengiriman Darah External')

@push('styles')
<style>
    /* ── Palette ── */
    :root {
        --blood:      #c0392b;
        --blood-dark: #922b21;
        --blood-lite: #fadbd8;
        --ink:        #1a1a2e;
        --ink-mid:    #2c2c54;
        --steel:      #ecf0f1;
        --muted:      #7f8c8d;
        --green:      #1abc9c;
        --amber:      #f39c12;
        --red:        #e74c3c;
        --blue:       #2980b9;
        --card-bg:    #ffffff;
        --border:     #dce1e7;
    }

    /* ── Base ── */
    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }

    /* ── Page header ── */
    .page-header {
        background: linear-gradient(135deg, var(--blood-dark) 0%, var(--blood) 60%, #e74c3c 100%);
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 22px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 18px rgba(192,57,43,.35);
    }
    .page-header h4 { margin: 0; font-size: 1.25rem; font-weight: 700; letter-spacing: .4px; }
    .page-header small { opacity: .85; font-size: .8rem; }
    .page-header .btn-new {
        background: rgba(255,255,255,.2);
        border: 1.5px solid rgba(255,255,255,.6);
        color: #fff;
        font-weight: 600;
        border-radius: 8px;
        padding: 8px 20px;
        transition: background .2s;
    }
    .page-header .btn-new:hover { background: rgba(255,255,255,.35); color: #fff; }

    /* ── Search card ── */
    .search-card {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 18px 22px;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
        margin-bottom: 18px;
        border-left: 4px solid var(--blood);
    }
    .search-card label { font-weight: 600; font-size: .82rem; color: var(--ink-mid); text-transform: uppercase; letter-spacing: .4px; }

    /* ── DataTable card ── */
    .table-card {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 18px 22px;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
    }

    /* ── Table ── */
    #tblPengiriman thead th {
        background: var(--ink);
        color: #fff;
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .5px;
        border: none;
        padding: 11px 10px;
        white-space: nowrap;
    }
    #tblPengiriman tbody tr { transition: background .15s; }
    #tblPengiriman tbody tr:hover { background: #fdf3f2; }
    #tblPengiriman tbody td { vertical-align: middle; font-size: .87rem; padding: 9px 10px; border-color: #f0f0f0; }

    /* ── Badge ── */
    .badge { font-size: .75rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .badge-success { background: #d5f5e3; color: #1e8449; }
    .badge-warning { background: #fef9e7; color: #9a7d0a; }
    .badge-danger  { background: #fadbd8; color: #c0392b; }
    .badge-info    { background: #d6eaf8; color: #1a5276; }

    /* ── Action buttons ── */
    .btn-act {
        padding: 4px 10px;
        font-size: .78rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: opacity .15s;
        font-weight: 600;
    }
    .btn-act:hover { opacity: .8; }
    .btn-edit   { background: #d6eaf8; color: #1a5276; }
    .btn-delete { background: #fadbd8; color: #c0392b; }
    .btn-detail { background: #e8f8f5; color: #1e8449; }

    /* ── Modal ── */
    .modal-header { background: linear-gradient(135deg, var(--blood-dark), var(--blood)); color: #fff; border-radius: 10px 10px 0 0; }
    .modal-header .modal-title { font-weight: 700; font-size: 1rem; }
    .modal-header .close { color: #fff; opacity: .9; font-size: 1.3rem; }
    .modal-content { border-radius: 10px; border: none; box-shadow: 0 8px 40px rgba(0,0,0,.18); }
    .modal-footer { border-top: 1px solid #f0f0f0; }

    /* ── Form sections ── */
    .form-section {
        background: #fafbfc;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }
    .form-section-title {
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: var(--blood);
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 2px solid var(--blood-lite);
    }
    .form-control, .form-select {
        border-radius: 7px;
        font-size: .87rem;
        border: 1.5px solid var(--border);
        transition: border-color .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--blood);
        box-shadow: 0 0 0 3px rgba(192,57,43,.1);
    }
    label.form-label { font-size: .8rem; font-weight: 600; color: #555; margin-bottom: 4px; }

    /* ── No-Permintaan search box ── */
    .no-perm-wrap {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .no-perm-wrap .form-control { flex: 1; font-weight: 600; font-size: .95rem; }
    .btn-cari {
        background: var(--blood);
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 7px 16px;
        font-weight: 700;
        font-size: .85rem;
        transition: background .2s;
    }
    .btn-cari:hover { background: var(--blood-dark); }

    /* ── Permintaan info box ── */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 8px;
    }
    .info-item { background: #fff; border: 1px solid var(--border); border-radius: 6px; padding: 7px 10px; }
    .info-item .lbl { font-size: .72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; }
    .info-item .val { font-size: .88rem; font-weight: 700; color: var(--ink); }

    /* ── Permintaan detail list ── */
    #permintaanDetailTable thead th { background: #f8f9fa; font-size: .78rem; color: #666; font-weight: 700; }
    #permintaanDetailTable tbody td { font-size: .84rem; vertical-align: middle; }
    .btn-pilih-stok { background: var(--blood); color: #fff; border: none; border-radius: 5px; padding: 3px 10px; font-size: .78rem; font-weight: 600; cursor: pointer; }
    .btn-pilih-stok:hover { background: var(--blood-dark); }
    .fulfilled { opacity: .45; }

    /* ── Stok terpilih table ── */
    #stokTerpilihTable thead th { background: var(--ink); color: #fff; font-size: .78rem; }
    #stokTerpilihTable tbody td { font-size: .84rem; vertical-align: middle; }
    .btn-remove-stok { background: #fadbd8; color: var(--red); border: none; border-radius: 5px; padding: 2px 8px; font-size: .78rem; cursor: pointer; }

    /* ── Stok picker modal ── */
    #stokList .stok-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background .15s;
    }
    #stokList .stok-item:hover { background: #fdf3f2; }
    #stokList .stok-item .no-stok { font-weight: 700; font-size: .88rem; color: var(--ink); }
    #stokList .stok-item .stok-meta { font-size: .78rem; color: var(--muted); }
    #stokList .stok-item .btn-ambil { background: var(--blood); color: #fff; border: none; border-radius: 5px; padding: 3px 12px; font-size: .78rem; font-weight: 600; }

    /* ── Donor pengganti badge ── */
    .badge-donor-ya  { background: #d5f5e3; color: #1e8449; }
    .badge-donor-tdk { background: #f0f0f0; color: #888; }

    /* ── Spinner overlay ── */
    .spinner-overlay {
        position: absolute; inset: 0;
        background: rgba(255,255,255,.7);
        display: flex; align-items: center; justify-content: center;
        z-index: 10; border-radius: 8px;
    }

    /* ── Summary bar ── */
    .summary-bar {
        display: flex;
        gap: 10px;
        background: #fdf3f2;
        border: 1px solid var(--blood-lite);
        border-radius: 8px;
        padding: 10px 16px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .btn-page {
        padding: 3px 9px; font-size: .8rem; border-radius: 5px;
        border: 1px solid var(--border); background: var(--card-bg);
        cursor: pointer; transition: background .15s;
    }
    .btn-page:hover:not(:disabled) { background: var(--blood-lite); }
    .btn-page.active { background: var(--blood); color: #fff; border-color: var(--blood); }
    .btn-page:disabled { opacity: .4; cursor: default; }
    .summary-bar .s-item { font-size: .82rem; font-weight: 600; color: var(--ink-mid); }
    .summary-bar .s-item span { color: var(--blood); font-size: 1rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3">

    {{-- ── Page Header ── --}}
    <div class="page-header">
        <div>
            <h4>🩸 Pengiriman Darah External</h4>
            <small>Kelola pengiriman darah ke institusi eksternal berdasarkan permintaan</small>
        </div>
        <button class="btn btn-new" id="btnTambah">
            <i class="fas fa-plus me-1"></i> Pengiriman Baru
        </button>
    </div>

    {{-- ── Filter ── --}}
    <div class="search-card">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label>No. Permintaan</label>
                <input type="text" id="filterNoPermintaan" class="form-control form-control-sm" placeholder="Cari nomor permintaan…">
            </div>
            <div class="col-md-2">
                <label>Tgl Dari</label>
                <input type="date" id="filterTglDari" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label>Tgl Sampai</label>
                <input type="date" id="filterTglSampai" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label>Status</label>
                <select id="filterStatus" class="form-control form-control-sm">
                    <option value="">— Semua —</option>
                    <option>TERKIRIM</option>
                    <option>PROSES</option>
                    <option>BATAL</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary w-100" id="btnFilter">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
            </div>
            <div class="col-md-1">
                <button class="btn btn-sm btn-secondary w-100" id="btnReset">Reset</button>
            </div>
        </div>
    </div>

    {{-- ── Table ── --}}
    {{-- Di atas table-card, tambah search --}}
<div class="table-card">
    <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;gap:.5rem;align-items:center;justify-content:space-between">
        <span style="font-weight:700;font-size:.88rem">Data Pengiriman</span>
        <input type="text" id="tblSearch" placeholder="🔍 Cari…"
            style="border:1px solid var(--border);border-radius:7px;padding:5px 10px;font-size:.83rem;width:200px">
    </div>

    <table class="table table-bordered table-hover w-100">
        <thead>
            <tr>
                <th width="40">#</th>
                <th>No. Pengiriman</th>
                <th>No. Permintaan</th>
                <th>Tgl Kirim</th>
                <th>Institusi Tujuan</th>
                <th>Jenis Biaya</th>
                <th>Dropping</th>
                <th>Petugas</th>
                <th>Suhu</th>
                <th>Status</th>
                <th width="100">Aksi</th>
            </tr>
        </thead>
        <tbody id="tblBody">
            {{-- diisi JS --}}
        </tbody>
    </table>

    <div id="tblFooter" style="padding:10px 16px;border-top:1px solid var(--border)"></div>
</div>

</div>

{{-- ══════════════════════════════════════════════════
     MODAL FORM (Tambah / Edit)
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="modalTitle">Pengiriman Baru</span>
                <button type="button" class="close btn btn-sm" data-bs-dismiss="modal" style="color:#fff;font-size:1.4rem;line-height:1">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formPengiriman" novalidate>
                    <input type="hidden" id="editId">
                    <input type="hidden" id="fPermintaanId">
                    <input type="hidden" id="fNoPermintaan">

                    {{-- Step 1: Cari No. Permintaan --}}
                    <div class="form-section">
                        <div class="form-section-title">1. Cari No. Permintaan</div>
                        <div class="no-perm-wrap">
                            <input type="text" id="inputNoPermintaan" class="form-control"
                                   placeholder="Ketik nomor permintaan lalu tekan Cari / Enter"
                                   style="max-width:320px">
                            <button type="button" class="btn-cari" id="btnCariPermintaan">
                                <i class="fas fa-search me-1"></i>Cari
                            </button>
                            <span id="spinnerCari" style="display:none">
                                <i class="fas fa-spinner fa-spin text-danger"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Info permintaan --}}
                    <div id="sectionPermintaan" style="display:none">
                        <div class="form-section">
                            <div class="form-section-title">Info Permintaan</div>
                            <div class="info-grid" id="infoPermintaan"></div>
                        </div>

                        {{-- Daftar kebutuhan darah --}}
                        <div class="form-section">
                            <div class="form-section-title">2. Daftar Kebutuhan Darah (klik Pilih Stok)</div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="permintaanDetailTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Jenis</th>
                                            <th>Gol</th>
                                            <th>Rh</th>
                                            <th>Diminta</th>
                                            <th>Dipenuhi</th>
                                            <th>Sisa</th>
                                            <th>Tgl Perlu</th>
                                            <th>Donor Pengganti</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailRows"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Stok terpilih --}}
                        <div class="form-section">
                            <div class="form-section-title">3. Stok Darah Dikirim</div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="stokTerpilihTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>No Stock</th>
                                            <th>Jenis</th>
                                            <th>Gol</th>
                                            <th>Rh</th>
                                            <th>Tgl Kadaluarsa</th>
                                            <th>NAT</th>
                                            <th>Keterangan</th>
                                            <th>Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stokTerpilihRows">
                                        <tr id="emptyStok">
                                            <td colspan="9" class="text-center text-muted py-3">
                                                <i class="fas fa-box-open me-1"></i>Belum ada stok dipilih
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="summary-bar" id="summaryBar" style="display:none">
                                <div class="s-item">Total Kantong Dipilih: <span id="sumTotal">0</span></div>
                            </div>
                        </div>

                        {{-- Form header kirim --}}
                        <div class="form-section">
                            <div class="form-section-title">4. Data Pengiriman</div>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">No. Pengiriman</label>
                                    <input type="text" id="fNomorPengiriman" class="form-control" readonly
                                           placeholder="Auto-generate">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tgl Kirim <span class="text-danger">*</span></label>
                                    <input type="datetime-local" id="fTanggalKirim" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Petugas <span class="text-danger">*</span></label>
                                    <input type="text" id="fPetugas" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kode Petugas <span class="text-danger">*</span></label>
                                    <input type="text" id="fPetugasKode" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Penerima</label>
                                    <input type="text" id="fPenerima" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Institusi Tujuan</label>
                                    <input type="text" id="fInstitusi" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Jenis Biaya <span class="text-danger">*</span></label>
                                    <select class="form-control" id="fJenisBiaya">
                                        <option value="">Memuat...</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Dropping</label>
                                    <select id="fDropping" class="form-control">
                                        <option value="">— Pilih —</option>
                                        <option>AMBIL_SENDIRI</option>
                                        <option>DIANTAR</option>
                                        <option>KURIR</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Suhu Kirim (°C)</label>
                                    <input type="number" id="fSuhu" class="form-control" step="0.1" placeholder="e.g. 4.0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Keterangan</label>
                                    <textarea id="fKeterangan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>{{-- /sectionPermintaan --}}

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnSimpan" style="display:none">
                    <i class="fas fa-save me-1"></i>Simpan Pengiriman
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL PILIH STOK
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalStok" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Pilih Stok — <span id="stokLabel"></span></span>
                <button type="button" class="close btn btn-sm" data-bs-dismiss="modal" style="color:#fff;font-size:1.4rem;line-height:1">&times;</button>
            </div>
            <div class="modal-body p-0" style="position:relative; min-height:200px;">
                <div id="spinnerStok" class="spinner-overlay" style="display:none">
                    <i class="fas fa-spinner fa-spin fa-2x text-danger"></i>
                </div>
                <div id="stokList"></div>
                <div id="stokEmpty" class="text-center text-muted py-4" style="display:none">
                    <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                    Tidak ada stok tersedia untuk kriteria ini.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MODAL DETAIL (View only)
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title">Detail Pengiriman</span>
                <button type="button" class="close btn btn-sm" data-bs-dismiss="modal" style="color:#fff;font-size:1.4rem;line-height:1">&times;</button>
            </div>
            <div class="modal-body" id="detailBody"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   CONFIG
═══════════════════════════════════════════════════════════ */
const URL_DATA       = "{{ route('penyimpanan.pengiriman_darah_external.data') }}";
const URL_PERMINTAAN = "{{ route('penyimpanan.pengiriman_darah_external.permintaan') }}";
const URL_CARI_STOK  = "{{ route('penyimpanan.pengiriman_darah_external.cariStok') }}";
const URL_STORE      = "{{ route('penyimpanan.pengiriman_darah_external.store') }}";
const URL_NEXT_NO    = "{{ route('penyimpanan.pengiriman_darah_external.nextNomor') }}";
const URL_SHOW       = (id) => `/penyimpanan/pengiriman_darah_external/${id}`;
const URL_UPDATE     = (id) => `/penyimpanan/pengiriman_darah_external/${id}`;
const URL_DELETE     = (id) => `/penyimpanan/pengiriman_darah_external/${id}`;
const CSRF           = $('meta[name="csrf-token"]').attr('content');

/* STATE */
let currentPermintaan = null;
let selectedStok      = [];
let currentDetailRow  = null;
let allRows           = [];
let currentPage       = 1;
let perPage           = 10;
let searchQ           = '';
let filterState       = {};

/* ═══════════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════════ */
$(function () {
    loadTable();
    $('#btnFilter').on('click', function () {
        filterState = {
            no_permintaan  : $('#filterNoPermintaan').val(),
            tanggal_dari   : $('#filterTglDari').val(),
            tanggal_sampai : $('#filterTglSampai').val(),
            status         : $('#filterStatus').val(),
        };
        currentPage = 1;
        loadTable();
    });

    $('#btnReset').on('click', function () {
        $('#filterNoPermintaan,#filterTglDari,#filterTglSampai').val('');
        $('#filterStatus').val('');
        filterState = {};
        currentPage = 1;
        loadTable();
    });
});

/* ═══════════════════════════════════════════════════════════
   LOAD TABLE DATA
═══════════════════════════════════════════════════════════ */
function loadTable() {
    $('#tblBody').html(`
        <tr><td colspan="11" class="text-center py-3">
            <i class="fas fa-spinner fa-spin text-danger me-1"></i> Memuat data…
        </td></tr>`);

    $.ajax({
        url  : URL_DATA,
        data : filterState,
        success(r) {
            // response dari getData() controller — ambil data array
            allRows = r.data ?? [];
            renderTable();
        },
        error() {
            $('#tblBody').html(`<tr><td colspan="11" class="text-center text-danger py-3">Gagal memuat data.</td></tr>`);
        }
    });
}

/* ═══════════════════════════════════════════════════════════
   RENDER TABLE
═══════════════════════════════════════════════════════════ */
function renderTable() {
    // Client-side search
    let rows = allRows;
    if (searchQ) {
        rows = rows.filter(r =>
            Object.values(r).some(v => String(v).toLowerCase().includes(searchQ))
        );
    }

    // Pagination
    const total     = rows.length;
    const totalPage = Math.ceil(total / perPage) || 1;
    if (currentPage > totalPage) currentPage = totalPage;
    const start  = (currentPage - 1) * perPage;
    const paged  = rows.slice(start, start + perPage);

    if (!paged.length) {
        $('#tblBody').html(`<tr><td colspan="11" class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x d-block mb-2 opacity-50"></i>Belum ada data pengiriman.
        </td></tr>`);
        $('#tblFooter').html('');
        return;
    }

    const statusBadge = {
        'TERKIRIM': '<span class="badge badge-success">TERKIRIM</span>',
        'PROSES'  : '<span class="badge badge-warning">PROSES</span>',
        'BATAL'   : '<span class="badge badge-danger">BATAL</span>',
    };

    const html = paged.map((r, i) => `
        <tr>
            <td>${start + i + 1}</td>
            <td>${r.nomor_pengiriman ?? '-'}</td>
            <td>${r.no_permintaan ?? '-'}</td>
            <td>${r.tanggal_kirim ?? '-'}</td>
            <td>${r.institusi_tujuan ?? '-'}</td>
            <td><span class="badge badge-info">${r.jenis_biaya ?? '-'}</span></td>
            <td>${r.dropping ?? '-'}</td>
            <td>${r.petugas ?? '-'}</td>
            <td>${r.suhu_kirim ? r.suhu_kirim + ' °C' : '-'}</td>
            <td>${statusBadge[r.status] ?? r.status ?? '-'}</td>
            <td>
                <button class="btn-act btn-detail" data-id="${r.id}" title="Detail">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn-act btn-edit" data-id="${r.id}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-act btn-delete" data-id="${r.id}" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`).join('');

    $('#tblBody').html(html);
    renderPagination(total, totalPage);
}

/* ═══════════════════════════════════════════════════════════
   PAGINATION
═══════════════════════════════════════════════════════════ */
function renderPagination(total, totalPage) {
    const start = (currentPage - 1) * perPage + 1;
    const end   = Math.min(currentPage * perPage, total);

    let pages = '';
    for (let i = 1; i <= totalPage; i++) {
        if (
            i === 1 || i === totalPage ||
            (i >= currentPage - 1 && i <= currentPage + 1)
        ) {
            pages += `<button class="btn-page ${i === currentPage ? 'active' : ''}"
                onclick="goPage(${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            pages += `<span style="padding:0 4px;color:var(--muted)">…</span>`;
        }
    }

    $('#tblFooter').html(`
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
            <span style="font-size:.8rem;color:var(--muted)">
                Menampilkan ${start}–${end} dari ${total} data
            </span>
            <div style="display:flex;gap:.25rem;align-items:center">
                <button class="btn-page" onclick="goPage(${currentPage-1})" ${currentPage===1?'disabled':''}>‹</button>
                ${pages}
                <button class="btn-page" onclick="goPage(${currentPage+1})" ${currentPage===totalPage?'disabled':''}>›</button>
            </div>
            <select onchange="changePerPage(this.value)"
                style="font-size:.8rem;border:1px solid var(--border);border-radius:6px;padding:3px 8px;background:var(--card-bg)">
                <option value="10"  ${perPage===10 ?'selected':''}>10 / hal</option>
                <option value="25"  ${perPage===25 ?'selected':''}>25 / hal</option>
                <option value="50"  ${perPage===50 ?'selected':''}>50 / hal</option>
                <option value="100" ${perPage===100?'selected':''}>100 / hal</option>
            </select>
        </div>`);
}

function goPage(p) {
    const totalPage = Math.ceil(allRows.length / perPage) || 1;
    if (p < 1 || p > totalPage) return;
    currentPage = p;
    renderTable();
}

function changePerPage(v) {
    perPage     = parseInt(v);
    currentPage = 1;
    renderTable();
}

/* ═══════════════════════════════════════════════════════════
   SEARCH (client-side)
═══════════════════════════════════════════════════════════ */
$('#tblSearch').on('input', function () {
    searchQ     = $(this).val().toLowerCase();
    currentPage = 1;
    renderTable();
});
/* ═══════════════════════════════════════════════════════════
   BUKA MODAL TAMBAH
═══════════════════════════════════════════════════════════ */
$('#btnTambah').on('click', function () {
    resetForm();
    loadJenisBiaya();
    $('#modalTitle').text('Pengiriman Baru');
    $('#editId').val('');

    const now   = new Date();
    const local = new Date(now - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    $('#fTanggalKirim').val(local);

    $.get(URL_NEXT_NO, (r) => { if (r.success) $('#fNomorPengiriman').val(r.nomor); });

    $('#modalForm').modal('show');
    
});

/* ═══════════════════════════════════════════════════════════
   RESET FORM
═══════════════════════════════════════════════════════════ */
function resetForm() {
    currentPermintaan = null;
    selectedStok      = [];
    currentDetailRow  = null;

    $('#inputNoPermintaan').val('').prop('disabled', false);
    $('#sectionPermintaan').hide();
    $('#btnSimpan').hide();
    $('#detailRows').empty();
    $('#stokTerpilihRows').html(`
        <tr id="emptyStok">
            <td colspan="9" class="text-center text-muted py-3">
                <i class="fas fa-box-open me-1"></i>Belum ada stok dipilih
            </td>
        </tr>`);
    $('#summaryBar').hide();
    $('#fNomorPengiriman,#fTanggalKirim,#fPetugas,#fPetugasKode,#fPenerima,#fInstitusi,#fKeterangan,#fSuhu').val('');
    $('#fJenisBiaya').val('Dropping');
    $('#fDropping').val('');
}

/* ═══════════════════════════════════════════════════════════
   CARI PERMINTAAN
═══════════════════════════════════════════════════════════ */
$('#btnCariPermintaan').on('click', function (e) {
    e.preventDefault();
    cariPermintaan();
});

$('#inputNoPermintaan').on('keypress', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        cariPermintaan();
    }
});

function cariPermintaan() {
    const no = $('#inputNoPermintaan').val().trim();
    if (!no) { toastWarn('Masukkan nomor permintaan.'); return; }

    $('#spinnerCari').show();
    $('#btnCariPermintaan').prop('disabled', true);

    $.ajax({
        url  : URL_PERMINTAAN,
        data : { no_permintaan: no },
        success(r) {
            if (!r.success) { toastError(r.message); return; }
            currentPermintaan = r.data;
            renderPermintaanInfo(r.data);
            renderPermintaanDetail(r.data.details);
            $('#sectionPermintaan').slideDown(200);
            $('#btnSimpan').show();

            $('#fPermintaanId').val(r.data.id);
            $('#fNoPermintaan').val(r.data.nomor_permintaan);
            $('#fPetugas').val(r.data.petugas ?? '');
            $('#fPetugasKode').val(r.data.petugas_kode ?? '');
            $('#fInstitusi').val(r.data.institusi_lain ?? '');
            $('#fJenisBiaya').val(r.data.jenis_biaya ?? 'Dropping');
            if (r.data.dropping) $('#fDropping').val(r.data.dropping);
        },
        error(xhr) {
            toastError(xhr.responseJSON?.message || 'Terjadi kesalahan.');
        },
        complete() {
            $('#spinnerCari').hide();
            $('#btnCariPermintaan').prop('disabled', false);
        },
    });
}

/* ═══════════════════════════════════════════════════════════
   RENDER INFO PERMINTAAN
═══════════════════════════════════════════════════════════ */
function renderPermintaanInfo(d) {
    $('#infoPermintaan').html(`
        <div class="info-item"><div class="lbl">No. Permintaan</div><div class="val">${d.nomor_permintaan}</div></div>
        <div class="info-item"><div class="lbl">Tanggal</div><div class="val">${d.tanggal}</div></div>
        <div class="info-item"><div class="lbl">Nama Peminta</div><div class="val">${d.nama_peminta}</div></div>
        <div class="info-item"><div class="lbl">Institusi</div><div class="val">${d.institusi_lain || '-'}</div></div>
        <div class="info-item"><div class="lbl">Jenis Biaya</div><div class="val">${d.jenis_biaya}</div></div>
        <div class="info-item"><div class="lbl">Status</div><div class="val">${badgeStatus(d.status)}</div></div>
    `);
}

function badgeStatus(s) {
    const map = {
        SUDAH_DIPENUHI : '<span class="badge badge-success">SUDAH DIPENUHI</span>',
        SEBAGIAN       : '<span class="badge badge-warning">SEBAGIAN</span>',
        BELUM_DIPENUHI : '<span class="badge badge-danger">BELUM DIPENUHI</span>',
    };
    return map[s] || s;
}

/* ═══════════════════════════════════════════════════════════
   RENDER DETAIL PERMINTAAN
═══════════════════════════════════════════════════════════ */
function renderPermintaanDetail(details) {
    const tbody = $('#detailRows').empty();
    details.forEach((d, i) => {
        const sisa = d.sisa;
        const done = sisa <= 0;
        tbody.append(`
            <tr class="${done ? 'fulfilled' : ''}">
                <td>${i + 1}</td>
                <td>${d.jenis_darah || '-'}</td>
                <td><strong>${d.gol_darah}</strong></td>
                <td>${d.rhesus}</td>
                <td>${d.jumlah}</td>
                <td>${d.jumlah_dipenuhi}</td>
                <td><strong class="${sisa > 0 ? 'text-danger' : 'text-success'}">${sisa}</strong></td>
                <td>${d.tanggal_perlu || '-'}</td>
                <td>
                    <span class="badge ${d.donor_pengganti === 'Ya' ? 'badge-donor-ya' : 'badge-donor-tdk'}">
                        ${d.donor_pengganti || '-'}
                    </span>
                </td>
                <td>
                    ${!done
                        ? `<button type="button" class="btn-pilih-stok"
                                data-id="${d.id}"
                                data-jenis="${d.jenis_darah || ''}"
                                data-gol="${d.gol_darah}"
                                data-rhesus="${d.rhesus}"
                                data-sisa="${sisa}">
                                <i class="fas fa-search me-1"></i>Pilih Stok
                           </button>`
                        : '<span class="text-success"><i class="fas fa-check-circle"></i> Terpenuhi</span>'
                    }
                </td>
            </tr>`);
    });
}

async function loadJenisBiaya() {
    try {

        const response = await fetch(
            "{{ route('penyimpanan.pengiriman_darah_external.jenisBiaya') }}"
        );

        const json = await response.json();

        let html = '<option value="">-- Pilih Jenis Biaya --</option>';

        if (json.success) {

            json.data.forEach(item => {
                html += `
                    <option value="${item.kode}">
                        ${item.nama}
                    </option>
                `;
            });

        }

        $('#fJenisBiaya').html(html);

    } catch (e) {

        $('#fJenisBiaya').html(
            '<option value="">Gagal memuat data</option>'
        );

        console.log(e);
    }
}
$(document).on('click', '.btn-pilih-stok', function (e) {
    e.preventDefault();
    e.stopPropagation();

    currentDetailRow = {
        id    : $(this).data('id'),
        jenis : $(this).data('jenis'),
        gol   : $(this).data('gol'),
        rhesus    : $(this).data('rhesus'),
        sisa  : $(this).data('sisa'),
    };

    $('#stokLabel').text(`${currentDetailRow.jenis} — Gol ${currentDetailRow.gol} ${currentDetailRow.rhesus}`);
    $('#stokList').empty();
    $('#stokEmpty').hide();
    $('#spinnerStok').show();
    $('#modalStok').modal('show');

    $.ajax({
        url  : URL_CARI_STOK,
        data : {
            jenis_darah : currentDetailRow.jenis,
            gol_darah   : currentDetailRow.gol,
            rhesus          : currentDetailRow.rhesus,
        },
        success(r) {
            $('#spinnerStok').hide();
            if (!r.success || !r.data.length) {
                $('#stokEmpty').show();
                return;
            }
            const list = $('#stokList');
            r.data.forEach(s => {
                const alreadyPicked = selectedStok.some(x => x.no_stock === s.no_stock);
                list.append(`
                    <div class="stok-item">
                        <div>
                            <div class="no-stok">${s.no_stock}</div>
                            <div class="stok-meta">
                                ${s.jenis_darah} · Gol ${s.gol_darah}${s.rhesus} ·
                                Exp: ${s.tgl_kadaluarsa
                                    ? new Date(s.tgl_kadaluarsa).toLocaleDateString('id-ID')
                                    : '-'} ·
                                NAT: ${s.nat ? '✅' : '❌'}
                            </div>
                        </div>
                        <button type="button" class="btn-ambil"
                            data-no="${s.no_stock}"
                            data-jenis="${s.jenis_darah}"
                            data-gol="${s.gol_darah}"
                            data-rhesus="${s.rhesus}"
                            data-exp="${s.tgl_kadaluarsa || ''}"
                            data-nat="${s.nat ? '1' : '0'}"
                            ${alreadyPicked ? 'disabled style="opacity:.4"' : ''}>
                            ${alreadyPicked ? 'Dipilih' : 'Ambil'}
                        </button>
                    </div>`);
            });
        },
        error() {
            $('#spinnerStok').hide();
            toastError('Gagal memuat stok.');
        },
    });
});

/* ═══════════════════════════════════════════════════════════
   AMBIL STOK — FIX: parameter (e) ditambahkan
═══════════════════════════════════════════════════════════ */
$(document).on('click', '#stokList .btn-ambil', function (e) {
    e.preventDefault();

    const btn  = $(this);
    const stok = {
        permintaan_detail_id : currentDetailRow.id,
        no_stock             : String(btn.data('no')),
        jenis_darah          : String(btn.data('jenis')),
        gol_darah            : String(btn.data('gol')),
        rhesus               : String(btn.data('rhesus')),
        tgl_kadaluarsa       : btn.data('exp'),
        nat                  : btn.data('nat') === '1' || btn.data('nat') === 1,
        jumlah               : 1,
    };

    selectedStok.push(stok);
    renderStokTerpilih();

    btn.prop('disabled', true).text('Dipilih').css('opacity', .4);
    toastSuccess(`Stok ${stok.no_stock} ditambahkan.`);
});

/* ═══════════════════════════════════════════════════════════
   RENDER STOK TERPILIH
═══════════════════════════════════════════════════════════ */
function renderStokTerpilih() {
    const tbody = $('#stokTerpilihRows').empty();
    if (!selectedStok.length) {
        tbody.html(`<tr id="emptyStok"><td colspan="9" class="text-center text-muted py-3">
            <i class="fas fa-box-open me-1"></i>Belum ada stok dipilih</td></tr>`);
        $('#summaryBar').hide();
        return;
    }
    selectedStok.forEach((s, i) => {
        const exp = s.tgl_kadaluarsa
            ? new Date(s.tgl_kadaluarsa).toLocaleDateString('id-ID')
            : '-';
        tbody.append(`
            <tr>
                <td>${i + 1}</td>
                <td><strong>${s.no_stock}</strong></td>
                <td>${s.jenis_darah}</td>
                <td>${s.gol_darah}</td>
                <td>${s.rhesus}</td>
                <td>${exp}</td>
                <td>${s.nat ? '✅' : '❌'}</td>
                <td>
                    <input type="text" class="form-control form-control-sm stok-ket"
                        data-idx="${i}" value="${s.keterangan || ''}" placeholder="keterangan…">
                </td>
                <td>
                    <button type="button" class="btn-remove-stok" data-idx="${i}">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>`);
    });
    $('#sumTotal').text(selectedStok.length);
    $('#summaryBar').show();
}

$(document).on('input', '.stok-ket', function () {
    selectedStok[$(this).data('idx')].keterangan = $(this).val();
});

$(document).on('click', '.btn-remove-stok', function (e) {
    e.preventDefault();
    selectedStok.splice($(this).data('idx'), 1);
    renderStokTerpilih();
});

/* ═══════════════════════════════════════════════════════════
   SIMPAN
═══════════════════════════════════════════════════════════ */
$('#btnSimpan').on('click', function (e) {
    e.preventDefault();

    if (!currentPermintaan)    { toastWarn('Cari permintaan terlebih dahulu.'); return; }
    if (!selectedStok.length)  { toastWarn('Pilih minimal 1 stok darah.'); return; }
    if (!$('#fPetugas').val()) { toastWarn('Petugas wajib diisi.'); return; }
    if (!$('#fTanggalKirim').val()) { toastWarn('Tanggal kirim wajib diisi.'); return; }

    const isEdit  = !!$('#editId').val();
    const url     = isEdit ? URL_UPDATE($('#editId').val()) : URL_STORE;
    const method  = isEdit ? 'PUT' : 'POST';
    const payload = {
        permintaan_id    : $('#fPermintaanId').val(),
        no_permintaan    : $('#fNoPermintaan').val(),
        tanggal_kirim    : $('#fTanggalKirim').val(),
        petugas          : $('#fPetugas').val(),
        petugas_kode     : $('#fPetugasKode').val(),
        penerima         : $('#fPenerima').val(),
        institusi_tujuan : $('#fInstitusi').val(),
        jenis_biaya      : $('#fJenisBiaya').val(),
        dropping         : $('#fDropping').val() || null,
        suhu_kirim       : $('#fSuhu').val() || null,
        keterangan       : $('#fKeterangan').val(),
        details          : selectedStok,
        _token           : CSRF,
    };

    $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan…');

    $.ajax({
        url, method,
        contentType : 'application/json',
        data        : JSON.stringify(payload),
        success(r) {
            if (!r.success) { toastError(r.message); return; }
            toastSuccess(r.message);
            $('#modalForm').modal('hide');
            dt.draw();
        },
        error(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                Swal.fire({ icon: 'error', title: 'Validasi', html: Object.values(errors).flat().join('<br>') });
            } else {
                toastError(xhr.responseJSON?.message || 'Gagal menyimpan.');
            }
        },
        complete() {
            $('#btnSimpan').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan Pengiriman');
        },
    });
});

/* ═══════════════════════════════════════════════════════════
   DETAIL VIEW
═══════════════════════════════════════════════════════════ */
$(document).on('click', '.btn-detail', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    $('#detailBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-danger"></i></div>');
    $('#modalDetail').modal('show');

    $.get(URL_SHOW(id), (r) => {
        if (!r.success) { $('#detailBody').html('<p class="text-danger">Gagal memuat.</p>'); return; }
        const d    = r.data;
        const rows = d.details.map((s, i) => `
            <tr>
                <td>${i+1}</td>
                <td>${s.no_stock}</td>
                <td>${s.jenis_darah}</td>
                <td>${s.gol_darah}</td>
                <td>${s.rhesus}</td>
                <td>${s.tgl_kadaluarsa ? new Date(s.tgl_kadaluarsa).toLocaleDateString('id-ID') : '-'}</td>
                <td>${s.nat ? '✅' : '❌'}</td>
                <td>${s.keterangan || '-'}</td>
            </tr>`).join('');

        $('#detailBody').html(`
            <div class="info-grid mb-3">
                <div class="info-item"><div class="lbl">No. Pengiriman</div><div class="val">${d.nomor_pengiriman}</div></div>
                <div class="info-item"><div class="lbl">No. Permintaan</div><div class="val">${d.no_permintaan}</div></div>
                <div class="info-item"><div class="lbl">Tgl Kirim</div><div class="val">${d.tanggal_kirim}</div></div>
                <div class="info-item"><div class="lbl">Petugas</div><div class="val">${d.petugas}</div></div>
                <div class="info-item"><div class="lbl">Institusi Tujuan</div><div class="val">${d.institusi_tujuan || '-'}</div></div>
                <div class="info-item"><div class="lbl">Suhu Kirim</div><div class="val">${d.suhu_kirim ? d.suhu_kirim + ' °C' : '-'}</div></div>
            </div>
            <table class="table table-sm table-bordered">
                <thead><tr>
                    <th>#</th><th>No Stock</th><th>Jenis</th><th>Gol</th><th>Rh</th>
                    <th>Exp</th><th>NAT</th><th>Ket</th>
                </tr></thead>
                <tbody>${rows}</tbody>
            </table>`);
    });
});

/* ═══════════════════════════════════════════════════════════
   DELETE
═══════════════════════════════════════════════════════════ */
$(document).on('click', '.btn-delete', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    Swal.fire({
        title             : 'Hapus pengiriman?',
        text              : 'Stok darah akan dikembalikan ke penyimpanan.',
        icon              : 'warning',
        showCancelButton  : true,
        confirmButtonColor: '#c0392b',
        confirmButtonText : 'Ya, Hapus',
        cancelButtonText  : 'Batal',
    }).then(res => {
        if (!res.isConfirmed) return;
        $.ajax({
            url    : URL_DELETE(id),
            method : 'DELETE',
            data   : { _token: CSRF },
            success(r) { toastSuccess(r.message); dt.draw(); },
            error(xhr) { toastError(xhr.responseJSON?.message || 'Gagal menghapus.'); },
        });
    });
});

/* ═══════════════════════════════════════════════════════════
   EDIT
═══════════════════════════════════════════════════════════ */
$(document).on('click', '.btn-edit', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    resetForm();
    $('#modalTitle').text('Edit Pengiriman');
    $('#editId').val(id);

    $.get(URL_SHOW(id), (r) => {
        if (!r.success) { toastError('Gagal memuat data.'); return; }
        const d = r.data;

        $('#fNomorPengiriman').val(d.nomor_pengiriman);
        $('#fPermintaanId').val(d.permintaan_id);
        $('#fNoPermintaan').val(d.no_permintaan);
        $('#inputNoPermintaan').val(d.no_permintaan).prop('disabled', true);
        $('#fTanggalKirim').val(d.tanggal_kirim?.replace(' ', 'T').slice(0, 16));
        $('#fPetugas').val(d.petugas);
        $('#fPetugasKode').val(d.petugas_kode);
        $('#fPenerima').val(d.penerima);
        $('#fInstitusi').val(d.institusi_tujuan);
        $('#fJenisBiaya').val(d.jenis_biaya);
        $('#fDropping').val(d.dropping || '');
        $('#fSuhu').val(d.suhu_kirim);
        $('#fKeterangan').val(d.keterangan);

        if (d.permintaan) {
            currentPermintaan = d.permintaan;
            renderPermintaanInfo({ ...d.permintaan, nomor_permintaan: d.no_permintaan });
            if (d.permintaan.details) renderPermintaanDetail(d.permintaan.details);
        }

        selectedStok = (d.details || []).map(s => ({
            permintaan_detail_id : s.permintaan_detail_id,
            no_stock             : s.no_stock,
            jenis_darah          : s.jenis_darah,
            gol_darah            : s.gol_darah,
            rhesus               : s.rhesus,
            tgl_kadaluarsa       : s.tgl_kadaluarsa,
            nat                  : !!s.nat,
            jumlah               : s.jumlah,
            keterangan           : s.keterangan,
        }));
        renderStokTerpilih();

        $('#sectionPermintaan').show();
        $('#btnSimpan').show();
    });

    $('#modalForm').modal('show');
});

/* ═══════════════════════════════════════════════════════════
   TOAST HELPERS
═══════════════════════════════════════════════════════════ */
function toastSuccess(msg) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg,
        showConfirmButton: false, timer: 2500, timerProgressBar: true });
}
function toastError(msg) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg,
        showConfirmButton: false, timer: 3500, timerProgressBar: true });
}
function toastWarn(msg) {
    Swal.fire({ toast: true, position: 'top-end', icon: 'warning', title: msg,
        showConfirmButton: false, timer: 2500 });
}
</script>
@endpush