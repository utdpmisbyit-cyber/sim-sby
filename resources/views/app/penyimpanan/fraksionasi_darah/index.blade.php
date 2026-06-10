@extends('layouts.index')

@section('title', 'Fraksionasi Darah')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
    --red:      #dc2626; --red-d: #b91c1c; --red-bg: #fef2f2; --red-bdr: #fecaca;
    --sl9: #0f172a; --sl7: #334155; --sl5: #64748b; --sl2: #e2e8f0; --sl1: #f1f5f9; --sl0: #f8fafc;
    --grn: #16a34a; --grn-bg: #f0fdf4; --amb: #d97706; --amb-bg: #fffbeb;
    --wht: #ffffff; --mono: 'JetBrains Mono',monospace; --sans: 'DM Sans',sans-serif;
    --sh: 0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.05);
}
*{font-family:var(--sans)}

.page-hdr{background:linear-gradient(135deg,var(--sl9) 0%,#1e1b4b 100%);border-radius:14px;padding:22px 26px;margin-bottom:18px;position:relative;overflow:hidden}
.page-hdr::before{content:'';position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:radial-gradient(circle,rgba(220,38,38,.3) 0%,transparent 70%);border-radius:50%}
.page-hdr h4{color:#fff;font-weight:700;font-size:1.15rem;margin:0}
.page-hdr p{color:#94a3b8;font-size:.8rem;margin:3px 0 0}

.sum-card{background:var(--wht);border-radius:11px;padding:16px 18px;border:1px solid var(--sl2);display:flex;align-items:center;gap:12px;transition:.2s}
.sum-card:hover{box-shadow:0 6px 18px rgba(0,0,0,.08);transform:translateY(-2px)}
.sum-card .ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.sum-card .val{font-size:1.5rem;font-weight:700;color:var(--sl9);line-height:1}
.sum-card .lbl{font-size:.72rem;color:var(--sl5);margin-top:2px;font-weight:500}

.tab-panel{background:var(--wht);border-radius:13px;border:1px solid var(--sl2);overflow:hidden}
.tab-panel .nav-tabs{border-bottom:1px solid var(--sl2);padding:0 18px;background:var(--sl0);gap:2px}
.tab-panel .nav-tabs .nav-link{border:none;border-bottom:3px solid transparent;border-radius:0;padding:13px 17px;color:var(--sl5);font-weight:600;font-size:.84rem;transition:.2s}
.tab-panel .nav-tabs .nav-link.active{color:var(--red);border-bottom-color:var(--red);background:transparent}
.tab-panel .tab-content{padding:18px}

.filter-bar{background:var(--sl0);border-radius:9px;padding:12px 14px;margin-bottom:14px;border:1px solid var(--sl2);display:flex;flex-wrap:wrap;gap:9px;align-items:flex-end}
.filter-bar label{font-size:.72rem;font-weight:600;color:var(--sl5);text-transform:uppercase;letter-spacing:.4px;display:block;margin-bottom:3px}
.filter-bar .form-control,.filter-bar .form-select{font-size:.81rem;border-color:var(--sl2);border-radius:7px;height:34px;background:var(--wht)}
.filter-bar .form-control:focus,.filter-bar .form-select:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(220,38,38,.1)}

/* ── Custom table (no DataTable plugin) ── */
.tbl-wrap{overflow-x:auto}
#tbl-fraksionasi{font-size:.8rem;width:100%;border-collapse:collapse}
#tbl-fraksionasi thead th{background:var(--sl9);color:#fff;font-weight:600;font-size:.72rem;text-transform:uppercase;letter-spacing:.4px;padding:10px 11px;border:none;white-space:nowrap}
#tbl-fraksionasi tbody tr{transition:background .12s;border-bottom:1px solid var(--sl1)}
#tbl-fraksionasi tbody tr:hover{background:var(--red-bg)}
#tbl-fraksionasi tbody td{padding:8px 11px;vertical-align:middle}
#tbl-fraksionasi .mono{font-family:var(--mono);font-size:.76rem;color:var(--sl7)}
.tbl-empty{text-align:center;padding:40px;color:var(--sl5);font-size:.85rem}
.tbl-footer{display:flex;justify-content:space-between;align-items:center;padding:10px 4px;font-size:.78rem;color:var(--sl5)}
.tbl-footer .pager{display:flex;gap:4px}
.tbl-footer .pager button{border:1px solid var(--sl2);background:var(--wht);border-radius:6px;padding:4px 10px;font-size:.76rem;cursor:pointer;transition:.15s}
.tbl-footer .pager button:hover{background:var(--sl0)}
.tbl-footer .pager button.active{background:var(--red);color:#fff;border-color:var(--red)}
.tbl-footer .pager button:disabled{opacity:.4;cursor:not-allowed}

.badge{font-size:.7rem;font-weight:600;padding:3px 8px;border-radius:5px}
.bdg-w{background:var(--amb-bg)!important;color:var(--amb)!important;border:1px solid #fde68a}
.bdg-s{background:var(--grn-bg)!important;color:var(--grn)!important;border:1px solid #bbf7d0}
.bdg-d{background:var(--red-bg)!important;color:var(--red-d)!important;border:1px solid var(--red-bdr)}

.gol{display:inline-flex;align-items:center;background:var(--red-bg);color:var(--red-d);border:1px solid var(--red-bdr);border-radius:5px;padding:1px 7px;font-family:var(--mono);font-size:.75rem;font-weight:700}

.btn-add{background:var(--red);color:#fff;border:none;border-radius:8px;padding:8px 18px;font-weight:600;font-size:.84rem;display:inline-flex;align-items:center;gap:5px;cursor:pointer;transition:.2s}
.btn-add:hover{background:var(--red-d);color:#fff;transform:translateY(-1px)}
.btn-flt{background:var(--sl9);color:#fff;border:none;border-radius:7px;padding:6px 14px;font-size:.8rem;font-weight:600;display:inline-flex;align-items:center;gap:4px;cursor:pointer;height:34px}
.btn-flt:hover{background:var(--sl7);color:#fff}

.form-section{margin-bottom:18px}
.fsec-title{font-size:.7rem;font-weight:700;color:var(--red);text-transform:uppercase;letter-spacing:.8px;padding-bottom:7px;border-bottom:2px solid var(--red-bdr);margin-bottom:13px;display:flex;align-items:center;gap:5px}
.form-label{font-size:.76rem;font-weight:600;color:var(--sl7);margin-bottom:4px}
.form-control,.form-select{font-size:.82rem;border-color:var(--sl2);border-radius:7px}
.form-control:focus,.form-select:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(220,38,38,.1)}
.form-control[readonly]{background:var(--sl0);color:var(--sl5)}

/* ── Stok item card ── */
.stok-item{padding:9px 11px;border-radius:7px;border:1px solid var(--sl2);margin-bottom:5px;cursor:pointer;background:var(--wht);transition:.12s;user-select:none}
.stok-item:hover{background:var(--red-bg);border-color:var(--red-bdr)}
.stok-item.selected{background:var(--red-bg);border-color:var(--red);box-shadow:0 0 0 2px rgba(220,38,38,.15)}
.stok-item .si-no{font-family:var(--mono);font-size:.78rem;font-weight:700;color:var(--sl9)}
.stok-item .si-sub{font-size:.72rem;color:var(--sl5);margin-top:2px}

/* ── Dropping table ── */
#tbl-dropping{font-size:.78rem;width:100%;border-collapse:collapse}
#tbl-dropping thead th{background:var(--sl9);color:#fff;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.3px;padding:8px 9px;border:none;white-space:nowrap}
#tbl-dropping tbody tr{border-bottom:1px solid var(--sl1);transition:.12s}
#tbl-dropping tbody tr:hover{background:var(--red-bg)}
#tbl-dropping tbody td{padding:7px 9px;vertical-align:middle;font-family:var(--mono);font-size:.75rem}
#tbl-dropping tfoot td{padding:8px 9px;font-weight:700;font-size:.78rem;background:var(--sl0);border-top:2px solid var(--sl2)}
.btn-rm{background:none;border:1px solid var(--red-bdr);color:var(--red);border-radius:5px;padding:2px 7px;font-size:.72rem;cursor:pointer;transition:.12s}
.btn-rm:hover{background:var(--red-bg)}

/* ── Suhu ── */
.suhu{display:inline-flex;align-items:center;gap:4px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;border-radius:5px;padding:2px 8px;font-family:var(--mono);font-size:.75rem;font-weight:600}

/* ── Modal ── */
.modal-content{border:none;border-radius:14px;overflow:hidden}
.modal-header{background:var(--sl9);color:#fff;padding:16px 22px;border:none}
.modal-header .modal-title{font-weight:700;font-size:.95rem}
.modal-header .btn-close{filter:invert(1);opacity:.7}
.modal-body{padding:22px}
.modal-footer{border-top:1px solid var(--sl2);padding:14px 22px}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.di .k{font-size:.7rem;color:var(--sl5);font-weight:600;text-transform:uppercase;letter-spacing:.4px}
.di .v{font-size:.87rem;color:var(--sl9);font-weight:600;margin-top:2px}

/* ── Toast ── */
#toast-cnt{position:fixed;top:18px;right:18px;z-index:9999}
.toast-i{background:var(--wht);border-radius:9px;padding:12px 16px;margin-bottom:7px;box-shadow:0 8px 22px rgba(0,0,0,.12);display:flex;align-items:center;gap:9px;font-size:.82rem;font-weight:500;color:var(--sl9);border-left:4px solid;animation:sIn .22s ease;min-width:260px}
.toast-i.ok{border-color:var(--grn)}.toast-i.err{border-color:var(--red)}
@keyframes sIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}

/* ── Loading ── */
.tbl-loading{text-align:center;padding:36px;color:var(--sl5);font-size:.83rem}
.spin{display:inline-block;width:20px;height:20px;border:2px solid var(--sl2);border-top-color:var(--red);border-radius:50%;animation:spin .7s linear infinite;margin-right:8px;vertical-align:middle}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
@endpush

@section('content')
<div id="toast-cnt"></div>

{{-- Header --}}
<div class="page-hdr">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4><i class="ri-drop-line me-2" style="color:#fca5a5"></i>Fraksionasi Darah</h4>
            <p>Manajemen proses fraksionasi kantong darah dari stok penyimpanan</p>
        </div>
        <button class="btn-add" id="btnTambah">
            <i class="ri-add-line"></i> Tambah Dropping Fraksionasi
        </button>
    </div>
</div>

{{-- Summary --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="sum-card">
            <div class="ico" style="background:#fef2f2;color:#dc2626"><i class="ri-drop-fill"></i></div>
            <div><div class="val" id="s-total">—</div><div class="lbl">Total Fraksionasi</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sum-card">
            <div class="ico" style="background:#fffbeb;color:#d97706"><i class="ri-loader-4-line"></i></div>
            <div><div class="val" id="s-proses">—</div><div class="lbl">Sedang Proses</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sum-card">
            <div class="ico" style="background:#f0fdf4;color:#16a34a"><i class="ri-checkbox-circle-line"></i></div>
            <div><div class="val" id="s-selesai">—</div><div class="lbl">Selesai</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="sum-card">
            <div class="ico" style="background:#f0f9ff;color:#0284c7"><i class="ri-calendar-today-line"></i></div>
            <div><div class="val" id="s-hari">—</div><div class="lbl">Hari Ini</div></div>
        </div>
    </div>
</div>

{{-- Tab Panel --}}
<div class="tab-panel">
    <ul class="nav nav-tabs" id="mainTab">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-booking">
                <i class="ri-bookmark-line me-1"></i>Booking Fraksionasi
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-dropping" id="tabDropBtn">
                <i class="ri-arrow-down-circle-line me-1"></i>Dropping Fraksionasi
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ══ TAB BOOKING ══ --}}
        <div class="tab-pane fade show active" id="tab-booking">
            <div class="filter-bar">
                <div>
                    <label>Status</label>
                    <select class="form-select" id="f-status" style="min-width:120px">
                        <option value="">Semua</option>
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
                <div>
                    <label>Golongan</label>
                    <select class="form-select" id="f-gol" style="min-width:90px">
                        <option value="">Semua</option>
                        @foreach(['A','B','AB','O'] as $g)
                        <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Tgl Dari</label>
                    <input type="date" class="form-control" id="f-dari" style="min-width:130px">
                </div>
                <div>
                    <label>Tgl Sampai</label>
                    <input type="date" class="form-control" id="f-sampai" style="min-width:130px">
                </div>
                <div style="flex:1;min-width:180px">
                    <label>Cari</label>
                    <input type="text" class="form-control" id="f-search" placeholder="No stok / kantong / transaksi…">
                </div>
                <div><button class="btn-flt" id="btnFilter"><i class="ri-search-line"></i> Filter</button></div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="btnReset" style="height:34px;border-radius:7px;font-size:.8rem">
                        <i class="ri-refresh-line"></i>
                    </button>
                </div>
            </div>

            <div class="tbl-wrap">
                <table id="tbl-fraksionasi">
                    <thead>
                        <tr>
                            <th width="36">No</th>
                            <th>No Stok</th>
                            <th>Jenis</th>
                            <th>Gol</th>
                            <th>Rh</th>
                            <th>NAT</th>
                            <th>Tgl Dropping</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Kadaluarsa</th>
                            <th>Suhu</th>
                            <th>Petugas</th>
                            <th>Status</th>
                            <th width="110">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbl-body">
                        <tr><td colspan="13" class="tbl-loading"><span class="spin"></span>Memuat data…</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="tbl-footer">
                <span id="tbl-info">—</span>
                <div class="pager" id="tbl-pager"></div>
            </div>
        </div>

        {{-- ══ TAB DROPPING ══ --}}
        <div class="tab-pane fade" id="tab-dropping">
            <div class="row g-3">

                {{-- Kiri: scan / cari stok ─────────────────────── --}}
                <div class="col-lg-4">
                    <div style="background:var(--sl0);border:1px solid var(--sl2);border-radius:11px;padding:15px;height:100%">
                        <div style="font-size:.75rem;font-weight:700;color:var(--sl5);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
                            <i class="ri-barcode-line me-1"></i>Scan / Cari No Stok
                        </div>
                        <input type="text" class="form-control mb-2" id="stok-scan"
                               placeholder="Scan barcode atau ketik no stok…"
                               style="font-family:var(--mono);font-size:.83rem"
                               autofocus>
                        <div id="stok-list" style="max-height:400px;overflow-y:auto">
                            <div style="text-align:center;padding:28px;color:var(--sl5);font-size:.8rem">
                                <i class="ri-search-line" style="font-size:1.5rem;display:block;margin-bottom:8px;color:var(--sl2)"></i>
                                Ketik atau scan untuk mencari stok
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: form + tabel dropping ─────────────────── --}}
                <div class="col-lg-8">
                    <div style="background:var(--wht);border:1px solid var(--sl2);border-radius:11px;padding:18px">

                        {{-- Header Nomor + Tanggal --}}
                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <label class="form-label">No Dropping</label>
                                <input type="text" id="d-no-fraksionasi" class="form-control" readonly
                                       style="font-family:var(--mono);font-weight:700;color:var(--sl9)">
                            </div>
                            <div class="col-7">
                                <label class="form-label">Tgl Dropping</label>
                                <input type="datetime-local" id="d-tgl-dropping" class="form-control">
                            </div>
                        </div>

                        {{-- Petugas --}}
                        
                            <div class="form-section">
                                <div class="fsec-title"><i class="ri-user-line"></i>Petugas & Transaksi</div>
                                <div class="row g-2">
                                    <div class="col-3">
                                        <label class="form-label">Kode Petugas</label>
                                        <div style="position:relative">
                                            <input type="text" id="d-pet-kode" class="form-control"
                                                placeholder="Ketik kode…"
                                                style="font-family:var(--mono)"
                                                autocomplete="off">
                                            <div id="pet-dropdown"
                                                style="display:none;position:absolute;top:100%;left:0;right:0;
                                                        background:#fff;border:1px solid var(--sl2);border-radius:7px;
                                                        box-shadow:0 6px 18px rgba(0,0,0,.1);z-index:999;
                                                        max-height:200px;overflow-y:auto;margin-top:3px">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-9">
                                        <label class="form-label">Nama Petugas</label>
                                        <input type="text" id="d-pet-nama" class="form-control" readonly
                                            placeholder="— pilih kode petugas —">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">No Transaksi</label>
                                        <input type="text" id="d-no-transaksi" class="form-control" readonly
                                            style="font-family:var(--mono)">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">No Stok (terpilih)</label>
                                        <input type="text" id="d-no-stok-show" class="form-control" readonly
                                            style="font-family:var(--mono);color:var(--red);font-weight:700"
                                            placeholder="—">
                                    </div>
                                </div>
                            </div>

                        {{-- Kantong defaults --}}
                        <div class="form-section">
                            <div class="fsec-title"><i class="ri-drop-line"></i>Informasi Kantong</div>
                            <div class="row g-2">
                                <div class="col-3">
                                    <label class="form-label">Ukuran (cc)</label>
                                    <select id="d-ukuran" class="form-select">
                                        <option value="350">350</option>
                                        <option value="450">450</option>
                                        <option value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label">Suhu Box (°C)</label>
                                    <input type="number" id="d-suhu" class="form-control" value="0">
                                </div>
                                <div class="col-3">
                                    <label class="form-label">No Transaksi</label>
                                    <input type="text" id="d-no-transaksi-kantong" class="form-control" readonly
                                           style="font-family:var(--mono);font-size:.76rem">
                                </div>
                                <div class="col-3">
                                    <label class="form-label">No Kantong</label>
                                    <input type="text" id="d-no-kantong" class="form-control" readonly
                                           style="font-family:var(--mono)">
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Jenis Kantong</label>
                                    <input type="text" id="d-jenis-kantong" class="form-control" readonly>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Merk Kantong</label>
                                    <input type="text" id="d-merk" class="form-control" readonly>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Tipe Kantong</label>
                                    <input type="text" id="d-tipe-kantong" class="form-control" readonly>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel stok ter-dropping --}}
                        <div class="form-section">
                            <div class="fsec-title d-flex justify-content-between align-items-center">
                                <span><i class="ri-list-check-2"></i> Daftar Stok Dropping</span>
                                <span id="drop-count-badge" style="background:var(--red);color:#fff;padding:2px 10px;border-radius:12px;font-size:.7rem">0 item</span>
                            </div>
                            <div style="overflow-x:auto;border:1px solid var(--sl2);border-radius:8px;max-height:240px;overflow-y:auto">
                                <table id="tbl-dropping">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tgl Kadaluarsa</th>
                                            <th>No Kantong</th>
                                            <th>gr</th>
                                            <th>mL</th>
                                            <th>Jenis Ktg</th>
                                            <th>Tipe</th>
                                            <th>Ukuran</th>
                                            <th>Umur Hari</th>
                                            <th>Suhu</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="drop-tbody">
                                        <tr><td colspan="11" class="tbl-empty">Belum ada stok ditambahkan</td></tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">Jumlah</td>
                                            <td id="sum-gr">0</td>
                                            <td id="sum-ml">0</td>
                                            <td colspan="6" class="text-end">Total: <span id="sum-total" style="color:var(--red)">0</span> kantong</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Rak, Box, Keterangan --}}
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label">Nomor Rak</label>
                                <input type="text" id="d-rak" class="form-control" placeholder="—">
                            </div>
                            <div class="col-4">
                                <label class="form-label">Nomor Box</label>
                                <input type="text" id="d-box" class="form-control" placeholder="—">
                            </div>
                            <div class="col-4">
                                <label class="form-label">Keterangan</label>
                                <input type="text" id="d-keterangan" class="form-control" placeholder="Opsional">
                            </div>
                        </div>

                        <input type="hidden" id="d-stok-id">
                        <input type="hidden" id="d-pet-id">

                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-outline-secondary" id="btnResetForm"
                                    style="border-radius:7px;font-size:.82rem;padding:7px 16px">
                                <i class="ri-refresh-line"></i> Reset
                            </button>
                            <button class="btn-add" id="btnSimpanDropping" style="padding:8px 22px">
                                <i class="ri-save-line"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ── Modal Detail ── --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-drop-fill me-2" style="color:#fca5a5"></i>Detail Fraksionasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detail-body">
                <div class="text-center py-4"><div class="spin" style="width:24px;height:24px;border-width:3px"></div></div>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal Edit ── --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-edit-2-line me-2" style="color:#fca5a5"></i>Edit Fraksionasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-id">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Ukuran Kantong</label>
                        <select id="edit-ukuran" class="form-select">
                            <option value="350">350 cc</option><option value="450">450 cc</option><option value="1000">1000 cc</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Suhu Box (°C)</label>
                        <input type="number" id="edit-suhu" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Tgl Dropping</label>
                        <input type="datetime-local" id="edit-tgl-dropping" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Status</label>
                        <select id="edit-status" class="form-select">
                            <option value="proses">Proses</option><option value="selesai">Selesai</option><option value="batal">Batal</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Nomor Rak</label>
                        <input type="text" id="edit-rak" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Nomor Box</label>
                        <input type="text" id="edit-box" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Keterangan</label>
                        <textarea id="edit-keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn-add" id="btnSimpanEdit" style="padding:7px 20px"><i class="ri-save-line"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Constants ────────────────────────────────────────────────────────────────
const BASE = '{{ rtrim(route("penyimpanan.fraksionasi_darah.index"), "/") }}';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
// ── Petugas Search ────────────────────────────────────────────────────────────
let petTimer;
const petInput    = () => document.getElementById('d-pet-kode');
const petDropdown = () => document.getElementById('pet-dropdown');

function renderPetDropdown(list) {
    const dd = petDropdown();
    if (!list.length) {
        dd.innerHTML = '<div style="padding:10px 12px;font-size:.78rem;color:var(--sl5)">Petugas tidak ditemukan</div>';
        dd.style.display = 'block';
        return;
    }
    dd.innerHTML = list.map(p => `
        <div class="pet-opt" data-id="${p.id}" data-kode="${p.kode}" data-nama="${p.nama}"
             style="padding:9px 12px;cursor:pointer;border-bottom:1px solid var(--sl1);
                    font-size:.82rem;transition:.12s"
             onmouseenter="this.style.background='var(--red-bg)'"
             onmouseleave="this.style.background=''">
            <span style="font-family:var(--mono);font-weight:700;color:var(--sl9)">${p.kode}</span>
            <span style="color:var(--sl5);margin-left:8px">${p.nama}</span>
        </div>
    `).join('');
    dd.style.display = 'block';

    dd.querySelectorAll('.pet-opt').forEach(el => {
        el.addEventListener('click', () => {
            pilihPetugas(el.dataset.id, el.dataset.kode, el.dataset.nama);
        });
    });
}

function pilihPetugas(id, kode, nama) {
    document.getElementById('d-pet-id').value   = id;
    document.getElementById('d-pet-kode').value = kode;
    document.getElementById('d-pet-nama').value = nama;
    petDropdown().style.display = 'none';
}

async function cariPetugas(q) {
    try {
        const r = await fetch(`${BASE}/search-petugas?q=${encodeURIComponent(q)}`);
        if (!r.ok) return;
        const data = await r.json();
        renderPetDropdown(data);
    } catch {}
}

// Event: typing di kode petugas
document.addEventListener('DOMContentLoaded', () => {
    const inp = petInput();
    if (!inp) return;

    inp.addEventListener('input', function () {
        clearTimeout(petTimer);
        const v = this.value.trim();
        if (!v) { petDropdown().style.display = 'none'; return; }
        petTimer = setTimeout(() => cariPetugas(v), 250);
    });

    // Tutup dropdown klik luar
    document.addEventListener('click', e => {
        if (!e.target.closest('#d-pet-kode') && !e.target.closest('#pet-dropdown')) {
            petDropdown().style.display = 'none';
        }
    });
});
// ── Toast ────────────────────────────────────────────────────────────────────
function toast(msg, type = 'ok') {
    const icon = type === 'ok' ? 'ri-checkbox-circle-fill' : 'ri-error-warning-fill';
    const color = type === 'ok' ? '#16a34a' : '#dc2626';
    const el = Object.assign(document.createElement('div'), {
        className: `toast-i ${type}`,
        innerHTML: `<i class="${icon}" style="color:${color};font-size:1.05rem"></i><span>${msg}</span>`
    });
    document.getElementById('toast-cnt').appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// ── Summary ──────────────────────────────────────────────────────────────────
function loadSummary() {
    fetch(`${BASE}/summary`)
        .then(r => r.json())
        .then(d => {
            document.getElementById('s-total').textContent   = d.total    ?? 0;
            document.getElementById('s-proses').textContent  = d.proses   ?? 0;
            document.getElementById('s-selesai').textContent = d.selesai  ?? 0;
            document.getElementById('s-hari').textContent    = d.hari_ini ?? 0;
        }).catch(() => {});
}

// ── Pure-JS Table ─────────────────────────────────────────────────────────────
let allRows = [], curPage = 1, perPage = 15, filteredRows = [];

function fmt(v) { return v ?? '-'; }
function fmtDate(v) {
    if (!v) return '-';
    // Handle both "2025-06-05 10:00:00" and ISO
    const d = new Date(v.replace(' ', 'T'));
    if (isNaN(d)) return v;
    return d.toLocaleDateString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric' })
        + ' ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' });
}
function statusBadge(s) {
    const map = { proses: ['bdg-w','Proses'], selesai: ['bdg-s','Selesai'], batal: ['bdg-d','Batal'] };
    const [cls, lbl] = map[s] ?? ['','—'];
    return `<span class="badge ${cls}">${lbl}</span>`;
}

async function loadTableData() {
    const body = document.getElementById('tbl-body');
    body.innerHTML = '<tr><td colspan="13" class="tbl-loading"><span class="spin"></span>Memuat…</td></tr>';

    const params = new URLSearchParams({
        status:         document.getElementById('f-status').value,
        golongan_darah: document.getElementById('f-gol').value,
        tgl_dari:       document.getElementById('f-dari').value,
        tgl_sampai:     document.getElementById('f-sampai').value,
        search:         document.getElementById('f-search').value,
        per_page: 500,   // load all, paginate client-side
    });

    try {
        const res = await fetch(`${BASE}/data?${params}`);
        const json = await res.json();
        // Support both DataTables-style {data:[]} and plain array
        allRows = Array.isArray(json) ? json : (json.data ?? []);
        filteredRows = [...allRows];
        curPage = 1;
        renderPage();
    } catch (e) {
        body.innerHTML = '<tr><td colspan="13" class="tbl-empty">Gagal memuat data</td></tr>';
    }
}

function renderPage() {
    const tbody = document.getElementById('tbl-body');
    const start = (curPage - 1) * perPage;
    const rows  = filteredRows.slice(start, start + perPage);
    const total = filteredRows.length;

    if (!rows.length) {
        tbody.innerHTML = '<tr><td colspan="13" class="tbl-empty">Tidak ada data</td></tr>';
    } else {
        tbody.innerHTML = rows.map((r, i) => `
            <tr>
                <td class="text-center" style="color:var(--sl5)">${start + i + 1}</td>
                <td class="mono">${fmt(r.no_stok)}</td>
                <td>${fmt(r.jenis_darah)}</td>
                <td><span class="gol">${fmt(r.golongan_darah)}</span></td>
                <td>${fmt(r.rhesus)}</td>
                <td>N</td>
                <td style="font-size:.75rem">${fmtDate(r.tgl_dropping)}</td>
                <td style="font-size:.75rem">${fmtDate(r.tgl_produksi)}</td>
                <td style="font-size:.75rem">${fmtDate(r.tgl_kadaluarsa)}</td>
                <td>${r.suhu_box != null ? `<span class="suhu"><i class="ri-temp-cold-line"></i>${r.suhu_box}°C</span>` : '-'}</td>
                <td style="font-size:.76rem">${r.petugas_nama ?? '-'}</td>
                <td>${statusBadge(r.status)}</td>
                <td>
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-info btn-detail" data-id="${r.id}" title="Detail" style="padding:3px 8px;font-size:.72rem;border-radius:5px">
                            <i class="ri-eye-line"></i>
                        </button>
                        ${r.status === 'proses' ? `
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${r.id}" title="Edit" style="padding:3px 8px;font-size:.72rem;border-radius:5px">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-sm btn-success btn-selesai" data-id="${r.id}" data-no="${r.no_fraksionasi}" title="Selesai" style="padding:3px 8px;font-size:.72rem;border-radius:5px">
                            <i class="ri-check-double-line"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-hapus" data-id="${r.id}" data-no="${r.no_fraksionasi}" title="Hapus" style="padding:3px 8px;font-size:.72rem;border-radius:5px">
                            <i class="ri-delete-bin-line"></i>
                        </button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    // Footer info
    const from = total ? start + 1 : 0;
    const to   = Math.min(start + perPage, total);
    document.getElementById('tbl-info').textContent = `Menampilkan ${from}–${to} dari ${total} data`;
    renderPager(total);
}

function renderPager(total) {
    const pages = Math.ceil(total / perPage);
    const pg    = document.getElementById('tbl-pager');
    if (pages <= 1) { pg.innerHTML = ''; return; }

    let html = `<button ${curPage===1?'disabled':''} onclick="goPage(${curPage-1})">‹</button>`;
    for (let p = 1; p <= pages; p++) {
        if (pages > 7 && Math.abs(p - curPage) > 2 && p !== 1 && p !== pages) {
            if (p === 2 || p === pages - 1) html += '<button disabled>…</button>';
            continue;
        }
        html += `<button class="${p===curPage?'active':''}" onclick="goPage(${p})">${p}</button>`;
    }
    html += `<button ${curPage===pages?'disabled':''} onclick="goPage(${curPage+1})">›</button>`;
    pg.innerHTML = html;
}

function goPage(p) { curPage = p; renderPage(); }

// Filter
document.getElementById('btnFilter').addEventListener('click', loadTableData);
document.getElementById('f-search').addEventListener('keydown', e => { if (e.key === 'Enter') loadTableData(); });
document.getElementById('btnReset').addEventListener('click', () => {
    ['f-status','f-gol','f-dari','f-sampai','f-search'].forEach(id => document.getElementById(id).value = '');
    loadTableData();
});

// ── Tab switching ─────────────────────────────────────────────────────────────
document.querySelectorAll('#mainTab button').forEach(btn => {
    btn.addEventListener('shown.bs.tab', e => {
        if (e.target.getAttribute('data-bs-target') === '#tab-dropping') {
            initDropping();
        }
    });
});
document.getElementById('btnTambah').addEventListener('click', () => {
    const t = document.getElementById('tabDropBtn');
    bootstrap.Tab.getOrCreateInstance(t).show();
    initDropping();
});

// ── Init Dropping tab ─────────────────────────────────────────────────────────
async function initDropping() {
    // Load next nomor
    try {
        const r = await fetch(`${BASE}/next-nomor`);
        const d = await r.json();
        document.getElementById('d-no-fraksionasi').value    = d.no_fraksionasi ?? '';
        document.getElementById('d-no-transaksi').value       = d.no_transaksi   ?? '';
        document.getElementById('d-no-transaksi-kantong').value = d.no_transaksi ?? '';
    } catch {}

    // Set tgl dropping = now
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    document.getElementById('d-tgl-dropping').value = now.toISOString().slice(0, 16);

    // Load current petugas from session
    loadPetugasSession();

    // Focus scan input
    setTimeout(() => document.getElementById('stok-scan').focus(), 300);
}

async function loadPetugasSession() {
    // Data dari Blade (server-side) — sudah tersedia
    const petugasId   = '{{ auth()->user()?->petugas?->id ?? "" }}';
    const petugasKode = '{{ auth()->user()?->petugas?->kode ?? "" }}';
    const petugasNama = '{{ auth()->user()?->petugas?->nama ?? "" }}';

    document.getElementById('d-pet-kode').value = petugasKode;
    document.getElementById('d-pet-nama').value = petugasNama;
    document.getElementById('d-pet-id').value   = petugasId;
    
    // Jika kosong, coba fetch dari API
    if (!petugasKode) {
        try {
            const r = await fetch('/api/user/petugas'); // sesuaikan endpoint
            const d = await r.json();
            if (d.kode) {
                document.getElementById('d-pet-kode').value = d.kode;
                document.getElementById('d-pet-nama').value = d.nama;
                document.getElementById('d-pet-id').value   = d.id;
            }
        } catch {}
    }
}

// ── Stok Scan / Search ────────────────────────────────────────────────────────
let scanTimer;
document.getElementById('stok-scan').addEventListener('input', function () {
    clearTimeout(scanTimer);
    const v = this.value.trim();
    if (!v || v.length < 2) {
        document.getElementById('stok-list').innerHTML =
            '<div style="text-align:center;padding:24px;color:var(--sl5);font-size:.78rem"><i class="ri-search-line" style="font-size:1.4rem;display:block;margin-bottom:6px;color:var(--sl2)"></i>Ketik atau scan untuk mencari stok</div>';
        return;
    }
    scanTimer = setTimeout(() => doStokSearch(v), 250);
});

// Enter = langsung pilih hasil pertama (simulasi scan barcode)
document.getElementById('stok-scan').addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
        const first = document.querySelector('.stok-item');
        if (first) first.click();
    }
});

async function doStokSearch(q) {
    const list = document.getElementById('stok-list');
    list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--sl5);font-size:.78rem"><span class="spin"></span>Mencari…</div>';
    try {
        const r = await fetch(`${BASE}/cari-stok?q=${encodeURIComponent(q)}`);
        if (!r.ok) {
            const err = await r.json().catch(() => ({}));
            list.innerHTML = `<div style="text-align:center;padding:20px;color:var(--red);font-size:.78rem">
                <i class="ri-error-warning-line" style="font-size:1.4rem;display:block;margin-bottom:6px"></i>
                ${err.message || 'Gagal mencari stok (Error ' + r.status + ')'}
            </div>`;
            return;
        }
        const data = await r.json();
        if (data.error) {
            list.innerHTML = `<div style="text-align:center;padding:20px;color:var(--red);font-size:.78rem">${data.message}</div>`;
            return;
        }
        renderStokList(data);
    } catch (e) {
        list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--red);font-size:.78rem">Koneksi gagal</div>';
    }
}

function renderStokList(items) {
    const list = document.getElementById('stok-list');
    if (!items.length) {
        list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--sl5);font-size:.78rem"><i class="ri-inbox-line" style="font-size:1.4rem;display:block;margin-bottom:6px;color:var(--sl2)"></i>Stok tidak ditemukan</div>';
        return;
    }
    list.innerHTML = items.map(s => {
        const age = s.tgl_aftap ? Math.floor((Date.now() - new Date(s.tgl_aftap)) / 86400000) : '?';
        return `<div class="stok-item" data-stok='${JSON.stringify(s).replace(/'/g,"&#39;")}'>
            <div class="si-no">${s.no_stok}</div>
            <div class="si-sub">
                <span class="gol">${s.golongan_darah ?? '?'}${s.rhesus === 'Positif' ? '+' : '-'}</span>
                &nbsp;${s.jenis_darah ?? '-'}
                &nbsp;·&nbsp;Kantong: ${s.no_kantong ?? '-'}
                &nbsp;·&nbsp;${age} hari
            </div>
        </div>`;
    }).join('');
    //  console.log('Stok data:', items);

    list.querySelectorAll('.stok-item').forEach(el => {
        el.addEventListener('click', () => {
            const s = JSON.parse(el.getAttribute('data-stok').replace(/&#39;/g, "'"));
            pilihStok(s, el);
        });
    });
}

// ── Pilih Stok → auto-fill form + tambah ke tabel ─────────────────────────────
function pilihStok(s, el) {
    if (droppingRows.some(r => r.stok_id === s.id)) {
        toast('Stok ini sudah ditambahkan', 'err');
        return;
    }

    document.querySelectorAll('.stok-item').forEach(x => x.classList.remove('selected'));
    el?.classList.add('selected');

    // Header stok
    document.getElementById('d-no-stok-show').value = s.no_stok  ?? '';
    document.getElementById('d-stok-id').value      = s.id;
    document.getElementById('d-no-kantong').value   = s.no_kantong ?? '';

    // ── Langsung dari root — sudah dimap service ──
    document.getElementById('d-jenis-kantong').value = s.jenis_kantong ?? '';  // "Single"
    document.getElementById('d-merk').value          = s.merk          ?? '';  // "Amicore"
    document.getElementById('d-tipe-kantong').value  = s.tipe_kantong  ?? '';  // "SG"

    // Ukuran: "350 CC" → ambil angkanya saja untuk select
    if (s.ukuran) {
        const angka = String(s.ukuran).replace(/\D/g, ''); // "350 CC" → "350"
        const opt   = document.querySelector(`#d-ukuran option[value="${angka}"]`);
        if (opt) document.getElementById('d-ukuran').value = angka;
    }

    addToDropTable(s);

    document.getElementById('stok-scan').value = '';
    document.getElementById('stok-scan').focus();
}

// ── Dropping table rows ───────────────────────────────────────────────────────
let droppingRows = [];

function addToDropTable(s) {
    const exp    = s.tgl_expired;
    const age    = s.tgl_aftap
        ? Math.floor((Date.now() - new Date(s.tgl_aftap)) / 86400000)
        : 0;
    const suhu   = document.getElementById('d-suhu').value || -20;
    const ukuran = String(s.ukuran ?? '').replace(/\D/g, '') || 
                   document.getElementById('d-ukuran').value || 450;

    droppingRows.push({
        stok_id:     s.id,
        no_stok:     s.no_stok,
        no_kantong:  s.no_kantong  ?? '-',
        gr:          s.gr          ?? 0,
        ml:          s.ml          ?? 0,
        jenis:       s.jenis_kantong ?? '-',   // ← dari root
        tipe:        s.tipe_kantong  ?? '-',   // ← dari root
        ukuran:      ukuran,
        usia:        age,
        suhu:        suhu,
        tgl_expired: exp,
    });

    renderDropTable();
}

function removeFromDropTable(idx) {
    droppingRows.splice(idx, 1);
    renderDropTable();
}

function renderDropTable() {
    const tbody = document.getElementById('drop-tbody');
    const badge = document.getElementById('drop-count-badge');
    const n     = droppingRows.length;

    badge.textContent = `${n} item`;

    if (!n) {
        tbody.innerHTML = '<tr><td colspan="11" class="tbl-empty">Belum ada stok ditambahkan</td></tr>';
        document.getElementById('sum-gr').textContent    = 0;
        document.getElementById('sum-ml').textContent    = 0;
        document.getElementById('sum-total').textContent = 0;
        return;
    }

    let totalGr = 0, totalMl = 0;
    tbody.innerHTML = droppingRows.map((r, i) => {
        totalGr += Number(r.gr) || 0;
        totalMl += Number(r.ml) || 0;
        const expFmt = r.tgl_expired ? new Date(r.tgl_expired).toLocaleDateString('id-ID') : '-';
        return `<tr>
            <td>${i + 1}</td>
            <td>${expFmt}</td>
            <td>${r.no_kantong}</td>
            <td>${r.gr ?? 0}</td>
            <td>${r.ml ?? 0}</td>
            <td>${r.jenis}</td>
            <td>${r.tipe}</td>
            <td>${r.ukuran} cc</td>
            <td>${r.usia}</td>
            <td><span class="suhu"><i class="ri-temp-cold-line"></i>${r.suhu}°C</span></td>
            <td><button class="btn-rm" onclick="removeFromDropTable(${i})"><i class="ri-delete-bin-line"></i></button></td>
        </tr>`;
    }).join('');

    document.getElementById('sum-gr').textContent    = totalGr;
    document.getElementById('sum-ml').textContent    = totalMl;
    document.getElementById('sum-total').textContent = n;
}

// ── Reset form ────────────────────────────────────────────────────────────────
document.getElementById('btnResetForm').addEventListener('click', resetForm);
function resetForm() {
    droppingRows = [];
    renderDropTable();
    ['d-no-stok-show','d-stok-id','d-no-kantong','d-jenis-kantong','d-merk','d-tipe-kantong'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('d-ukuran').value = '450';
    document.getElementById('d-suhu').value   = '-20';
    document.getElementById('d-rak').value    = '';
    document.getElementById('d-box').value    = '';
    document.getElementById('d-keterangan').value = '';
    document.getElementById('stok-scan').value = '';
    document.querySelectorAll('.stok-item').forEach(x => x.classList.remove('selected'));
}

// ── Simpan Dropping ───────────────────────────────────────────────────────────
document.getElementById('btnSimpanDropping').addEventListener('click', async () => {
    if (!droppingRows.length) { toast('Tambahkan minimal 1 stok terlebih dahulu', 'err'); return; }

    const btn = document.getElementById('btnSimpanDropping');
    btn.disabled = true;
    btn.innerHTML = '<span class="spin" style="width:16px;height:16px;border-width:2px;margin-right:6px"></span>Menyimpan…';

    let saved = 0, failed = 0;
    for (const row of droppingRows) {
        try {
            const res = await fetch(BASE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({
                    no_stok:        row.no_stok,
                    ukuran_kantong: row.ukuran,
                    suhu_box:       document.getElementById('d-suhu').value || null,
                    tgl_dropping:   document.getElementById('d-tgl-dropping').value || null,
                    jenis_kantong:  row.jenis,
                    merk:           document.getElementById('d-merk').value || null,
                    tipe_kantong:   row.tipe,
                    nomor_rak:      document.getElementById('d-rak').value || null,
                    nomor_box:      document.getElementById('d-box').value || null,
                    keterangan:     document.getElementById('d-keterangan').value || null,
                }),
            });
            const d = await res.json();
            if (d.success) saved++; else failed++;
        } catch { failed++; }
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="ri-save-line"></i> Simpan';

    if (saved > 0) {
        toast(`${saved} fraksionasi berhasil disimpan!${failed ? ' (' + failed + ' gagal)' : ''}`, failed ? 'err' : 'ok');
        resetForm();
        loadTableData();
        loadSummary();
        initDropping();
        const t = document.querySelector('[data-bs-target="#tab-booking"]');
        if (t) bootstrap.Tab.getOrCreateInstance(t).show();
    } else {
        toast('Semua data gagal disimpan', 'err');
    }
});

// ── Detail ────────────────────────────────────────────────────────────────────
document.getElementById('tbl-body').addEventListener('click', async e => {
    const btn = e.target.closest('.btn-detail');
    if (!btn) return;
    const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
    document.getElementById('detail-body').innerHTML =
        '<div class="text-center py-4"><span class="spin" style="width:24px;height:24px;border-width:3px"></span></div>';
    modal.show();

    const r = await fetch(`${BASE}/${btn.dataset.id}`);
    const d = await r.json();

    const di = (k, v) => `<div class="di"><div class="k">${k}</div><div class="v">${v ?? '-'}</div></div>`;
    document.getElementById('detail-body').innerHTML = `
        <div class="detail-grid">
            ${di('No Fraksionasi', `<span style="font-family:var(--mono);font-weight:700;color:var(--sl9)">${d.no_fraksionasi}</span>`)}
            ${di('No Transaksi',   `<span style="font-family:var(--mono)">${d.no_transaksi}</span>`)}
            ${di('No Stok',        `<span style="font-family:var(--mono)">${d.no_stok}</span>`)}
            ${di('No Kantong',      d.no_kantong)}
            ${di('Jenis Darah',     d.jenis_darah)}
            ${di('Gol / Rh',        `<span class="gol">${d.golongan_darah ?? '?'}</span> ${d.rhesus ?? ''}`)}
            ${di('Ukuran',          d.ukuran_kantong ? d.ukuran_kantong + ' cc' : '-')}
            ${di('Suhu Box',        d.suhu_box !== null ? `<span class="suhu"><i class="ri-temp-cold-line"></i>${d.suhu_box}°C</span>` : '-')}
            ${di('Tgl Dropping',    fmtDate(d.tgl_dropping))}
            ${di('Tgl Produksi',    fmtDate(d.tgl_produksi))}
            ${di('Tgl Kadaluarsa',  fmtDate(d.tgl_kadaluarsa))}
            ${di('Rak / Box',       `${d.nomor_rak ?? '-'} / ${d.nomor_box ?? '-'}`)}
            ${di('Status',          statusBadge(d.status))}
            ${di('Petugas',         d.petugas?.nama)}
            ${di('Keterangan',      d.keterangan)}
        </div>`;
});

// ── Edit ──────────────────────────────────────────────────────────────────────
document.getElementById('tbl-body').addEventListener('click', async e => {
    const btn = e.target.closest('.btn-edit');
    if (!btn) return;
    const r = await fetch(`${BASE}/${btn.dataset.id}`);
    const d = await r.json();

    document.getElementById('edit-id').value           = d.id;
    document.getElementById('edit-ukuran').value       = d.ukuran_kantong ?? '450';
    document.getElementById('edit-suhu').value         = d.suhu_box ?? '';
    document.getElementById('edit-status').value       = d.status;
    document.getElementById('edit-tgl-dropping').value = d.tgl_dropping?.substring(0, 16) ?? '';
    document.getElementById('edit-rak').value          = d.nomor_rak ?? '';
    document.getElementById('edit-box').value          = d.nomor_box ?? '';
    document.getElementById('edit-keterangan').value   = d.keterangan ?? '';

    new bootstrap.Modal(document.getElementById('modalEdit')).show();
});

document.getElementById('btnSimpanEdit').addEventListener('click', async () => {
    const id = document.getElementById('edit-id').value;
    const res = await fetch(`${BASE}/${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            _method:        'PUT',
            ukuran_kantong: document.getElementById('edit-ukuran').value,
            suhu_box:       document.getElementById('edit-suhu').value || null,
            status:         document.getElementById('edit-status').value,
            tgl_dropping:   document.getElementById('edit-tgl-dropping').value || null,
            nomor_rak:      document.getElementById('edit-rak').value || null,
            nomor_box:      document.getElementById('edit-box').value || null,
            keterangan:     document.getElementById('edit-keterangan').value || null,
        }),
    });
    const data = await res.json();
    if (data.success) {
        toast('Data berhasil diperbarui!');
        bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
        loadTableData();
        loadSummary();
    } else {
        toast(data.message || 'Gagal', 'err');
    }
});

// ── Selesai ───────────────────────────────────────────────────────────────────
document.getElementById('tbl-body').addEventListener('click', async e => {
    const btn = e.target.closest('.btn-selesai');
    if (!btn) return;
    if (!confirm(`Selesaikan fraksionasi ${btn.dataset.no}?`)) return;
    const res = await fetch(`${BASE}/${btn.dataset.id}/selesai`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
        body: '{}',
    });
    const data = await res.json();
    if (data.success) { toast('Fraksionasi diselesaikan!'); loadTableData(); loadSummary(); }
    else toast(data.message || 'Gagal', 'err');
});

// ── Hapus ─────────────────────────────────────────────────────────────────────
document.getElementById('tbl-body').addEventListener('click', async e => {
    const btn = e.target.closest('.btn-hapus');
    if (!btn) return;
    if (!confirm(`Hapus fraksionasi ${btn.dataset.no}? Stok akan dikembalikan.`)) return;
    const res = await fetch(`${BASE}/${btn.dataset.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF },
    });
    const data = await res.json();
    if (data.success) { toast('Data berhasil dihapus!'); loadTableData(); loadSummary(); }
    else toast(data.message || 'Gagal hapus', 'err');
});

// ── Init ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    loadTableData();
    loadSummary();
});
</script>
@endpush