@extends('layouts.index')

@section('title', 'Manajemen Permintaan Darah')

@push('styles')
<style>
/* ── Google Font ─────────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

/* ── Root Variables ──────────────────────────────────── */
:root {
    --red-50:   #FCEBEB;
    --red-100:  #F7C1C1;
    --red-600:  #A32D2D;
    --red-800:  #791F1F;
    --blue-50:  #E6F1FB;
    --blue-600: #185FA5;
    --amber-50: #FAEEDA;
    --amber-600:#854F0B;
    --green-50: #EAF3DE;
    --green-600:#3B6D11;
    --gray-bg:  #F8FAFC;
    --border:   rgba(0,0,0,.08);
    --radius-md:8px;
    --radius-lg:14px;
    --font-main:'Plus Jakarta Sans', -apple-system, sans-serif;
}

* { font-family: var(--font-main); }

/* ── Page Header ─────────────────────────────────────── */
.pdh-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
}
.pdh-title-wrap {
    display: flex; align-items: center; gap: 12px;
}
.pdh-icon-circle {
    width: 42px; height: 42px; border-radius: 12px;
    background: var(--red-50);
    display: flex; align-items: center; justify-content: center;
    color: var(--red-600); font-size: 22px;
}
.pdh-title { font-size: 17px; font-weight: 700; color: #111827; margin: 0; }
.pdh-subtitle { font-size: 12px; color: #6B7280; margin: 2px 0 0; }

/* ── Button New ──────────────────────────────────────── */
.btn-new-req {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--red-600);
    color: #fff;
    border: none; border-radius: var(--radius-md);
    padding: 9px 18px;
    font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background .15s, transform .1s;
}
.btn-new-req:hover {
    background: var(--red-800);
    transform: translateY(-1px);
    color: #fff; text-decoration: none;
}
.btn-new-req i { font-size: 17px; }

/* ── Filter Bar ──────────────────────────────────────── */
.filter-bar-new {
    background: #F9FAFB;
    border: 0.5px solid rgba(0,0,0,.10);
    border-radius: var(--radius-lg);
    padding: 12px 16px;
    margin-bottom: 1.25rem;
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
}
.filter-group-new {
    display: flex; align-items: center; gap: 6px;
}
.filter-group-new .fi-label {
    font-size: 10.5px; font-weight: 700;
    color: #6B7280; text-transform: uppercase; letter-spacing: .4px;
    white-space: nowrap;
}
.filter-group-new i { font-size: 15px; color: #9CA3AF; }
.filter-inp-new {
    background: #fff;
    border: 0.5px solid rgba(0,0,0,.15);
    border-radius: var(--radius-md);
    padding: 7px 11px;
    font-size: 13px; color: #111827;
    font-family: var(--font-main);
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    min-width: 130px;
}
.filter-inp-new:focus {
    border-color: var(--red-600);
    box-shadow: 0 0 0 3px rgba(163,45,45,.12);
}
.btn-reset-new {
    display: inline-flex; align-items: center; gap: 5px;
    background: transparent;
    border: 0.5px solid rgba(0,0,0,.15);
    border-radius: var(--radius-md);
    padding: 7px 13px;
    font-size: 12px; font-weight: 500; color: #6B7280;
    cursor: pointer; font-family: var(--font-main);
    text-decoration: none;
    transition: background .15s;
    margin-left: auto;
}
.btn-reset-new:hover { background: #F3F4F6; color: #374151; }

/* ── Table Card ──────────────────────────────────────── */
.table-card-new {
    background: #fff;
    border: 0.5px solid rgba(0,0,0,.10);
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.table-card-new .table {
    margin-bottom: 0;
}
.table-card-new .table thead th {
    background: #F9FAFB;
    font-size: 10.5px; font-weight: 700;
    color: #6B7280;
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 0.5px solid rgba(0,0,0,.10);
    border-top: none;
    padding: 11px 14px;
    white-space: nowrap;
}
.table-card-new .table tbody td {
    padding: 12px 14px;
    font-size: 13px; color: #111827;
    vertical-align: middle;
    border-bottom: 0.5px solid rgba(0,0,0,.06);
    border-top: none;
}
.table-card-new .table tbody tr:last-child td { border-bottom: none; }
.table-card-new .table tbody tr:hover td { background: #FFF5F5; }

/* ── No. Permintaan cell ─────────────────────────────── */
.no-req-cell strong { font-weight: 700; font-size: 13px; color: #111827; display: block; }
.no-req-cell small  { font-size: 11px; color: #9CA3AF; }

/* ── Bank Darah cell ─────────────────────────────────── */
.bank-cell { display: flex; align-items: center; gap: 8px; }
.bank-icon {
    width: 30px; height: 30px; border-radius: 8px;
    background: var(--red-50); color: var(--red-600);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.bank-name { font-weight: 500; font-size: 13px; }

/* ── Status Badges ───────────────────────────────────── */
.status-badge-new {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700; letter-spacing: .3px;
    white-space: nowrap;
}
.status-badge-new i { font-size: 12px; }
.sbadge-permintaan { background: var(--blue-50);  color: var(--blue-600); }
.sbadge-proses     { background: var(--amber-50); color: var(--amber-600); }
.sbadge-selesai    { background: var(--green-50); color: var(--green-600); }
.sbadge-batal      { background: var(--red-50);   color: var(--red-600); }

/* ── Jumlah Chip ─────────────────────────────────────── */
.jml-chip {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    background: var(--red-50); color: var(--red-600);
    font-size: 14px; font-weight: 700;
}

/* ── Officer Cell ────────────────────────────────────── */
.officer-cell { display: flex; align-items: center; gap: 8px; }
.officer-avatar {
    width: 30px; height: 30px; border-radius: 8px;
    background: var(--blue-50); color: var(--blue-600);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.officer-name { font-size: 13px; font-weight: 500; }

/* ── Action Buttons ──────────────────────────────────── */
.actions-cell { display: flex; align-items: center; justify-content: center; gap: 5px; }
.act-btn-new {
    width: 32px; height: 32px; border-radius: 8px; border: none;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: 15px;
    transition: transform .1s, opacity .1s;
    flex-shrink: 0; padding: 0;
}
.act-btn-new:hover { transform: scale(1.12); opacity: .85; }
.abn-detail { background: var(--blue-50);  color: var(--blue-600); }
.abn-edit   { background: var(--amber-50); color: var(--amber-600); }
.abn-status { background: var(--green-50); color: var(--green-600); }
.abn-delete { background: var(--red-50);   color: var(--red-600); }

/* ── Empty state ─────────────────────────────────────── */
.empty-state {
    padding: 48px 0;
    text-align: center;
    color: #9CA3AF;
}
.empty-state i { font-size: 36px; display: block; margin-bottom: 8px; }
.empty-state span { font-size: 13px; }

/* ── Pagination ──────────────────────────────────────── */
.pagination .page-item .page-link {
    border-radius: 8px !important;
    border: 0.5px solid rgba(0,0,0,.12);
    color: var(--red-600);
    font-size: 12px;
    margin: 0 2px;
}
.pagination .page-item.active .page-link {
    background: var(--red-600);
    border-color: var(--red-600);
    color: #fff;
}

/* ── Loading Overlay ─────────────────────────────────── */
.loading-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 9999; display: none;
    justify-content: center; align-items: center;
    flex-direction: column; gap: 12px;
}
.loading-overlay.active { display: flex; }
.loading-overlay span { color: #fff; font-size: 13px; font-weight: 500; }

/* ── Modal improvements ──────────────────────────────── */
.modal-content  { border: none; border-radius: var(--radius-lg); box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-header   { padding: 14px 20px; }
.modal-header.bg-primary { background: linear-gradient(135deg,#1D4ED8,#3B82F6) !important; }
.modal-header.bg-info    { background: linear-gradient(135deg,#0284C7,#38BDF8) !important; }
.modal-header.bg-warning { background: linear-gradient(135deg,#D97706,#F59E0B) !important; }
.modal-header.bg-danger  { background: linear-gradient(135deg, var(--red-800), var(--red-600)) !important; }
.modal-title { font-size: 14px; font-weight: 700; }
.modal-xl { max-width: 92% !important; }
.close { opacity: .8; } .close:hover { opacity: 1; }

/* ── Form inside modal ───────────────────────────────── */
.form-label-sm {
    font-size: 10.5px; font-weight: 700; color: #6B7280;
    text-transform: uppercase; letter-spacing: .35px;
    margin-bottom: 4px; display: block;
}
.form-control-sm-custom {
    width: 100%; padding: 7px 10px;
    border: 0.5px solid rgba(0,0,0,.15);
    border-radius: var(--radius-md);
    font-size: 13px; font-family: var(--font-main);
    transition: border-color .15s, box-shadow .15s;
    background: #fff; color: #111827;
}
.form-control-sm-custom:focus {
    outline: none; border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59,130,246,.15);
}
.form-control-sm-custom:disabled,
.form-control-sm-custom[disabled] { background: #F3F4F6; color: #6B7280; }

.section-divider { border: none; border-top: 1.5px dashed rgba(0,0,0,.08); margin: 16px 0; }
.section-title-new {
    font-size: 11.5px; font-weight: 700;
    color: var(--red-600); text-transform: uppercase; letter-spacing: .5px;
    margin-bottom: 12px; display: flex; align-items: center; gap: 6px;
}
.section-title-new::after { content:''; flex:1; height:1px; background: var(--red-50); }

/* ── Detail table inside modal ───────────────────────── */
.detail-table thead { background: #FEF2F2; }
.detail-table thead th { font-size: 10.5px; font-weight: 700; color: #6B7280; text-transform: uppercase; letter-spacing: .4px; padding: 8px 10px; }
.detail-table tbody td { font-size: 12px; padding: 7px 10px; vertical-align: middle; }
.tfoot-total td { background: #F9FAFB; font-size: 12px; font-weight: 600; }

/* ── Golongan Darah Pills ────────────────────────────── */
.goldar-pill {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 50%;
    font-size: 12px; font-weight: 700;
}
.gol-a  { background: #FAECE7; color: #7B241C; }
.gol-b  { background: var(--blue-50); color: #154360; }
.gol-ab { background: #F3EFFC; color: #4A235A; }
.gol-o  { background: var(--green-50); color: #145A32; }
.rhesus-pos { color: var(--green-600); font-weight: 700; }
.rhesus-neg { color: var(--red-600);   font-weight: 700; }

/* ── FPUP ────────────────────────────────────────────── */
.fpup-detail-box {
    display: none;
    background: #F8FAFC;
    border: 0.5px solid #CBD5E1;
    padding: 12px; border-radius: 8px;
}
#fpupSearchResult {
    position: absolute; top: 100%; left: 0; right: 0;
    background: #fff; border: 0.5px solid #D1D5DB;
    border-top: none; z-index: 9999;
    max-height: 250px; overflow: auto; display: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,.1);
}
.fpup-item {
    padding: 10px 12px;
    border-bottom: 0.5px solid #F3F4F6;
    cursor: pointer; transition: background .1s;
}
.fpup-item:hover { background: var(--red-50); }
.fpup-item:last-child { border-bottom: none; }

/* ── Info strip ──────────────────────────────────────── */
.info-strip { background: #F0F9FF; border-left: 3px solid #0284C7; padding: 10px 14px; border-radius: 0 8px 8px 0; }
.info-strip th { font-size: 11px; color: #6B7280; font-weight: 600; padding: 3px 10px 3px 0; white-space: nowrap; }
.info-strip td { font-size: 13px; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner-border text-light" role="status" style="width:2.5rem;height:2.5rem"></div>
        <span id="loadingText">Memproses...</span>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- Page Header --}}
            <div class="pdh-header">
                <div class="pdh-title-wrap">
                    <div class="pdh-icon-circle">
                        <i class="ti ti-droplet-half-2"></i>
                    </div>
                    <div>
                        <h3 class="pdh-title">Daftar Permintaan Darah</h3>
                        <p class="pdh-subtitle">Manajemen permintaan &amp; stok darah unit</p>
                    </div>
                </div>
                <button type="button" class="btn-new-req" onclick="showCreateModal()">
                    <i class="ti ti-circle-plus"></i> Permintaan Baru
                </button>
            </div>

            {{-- Filter --}}
            <form method="GET" id="filterForm">
                <div class="filter-bar-new">
                    <div class="filter-group-new">
                        <i class="ti ti-adjustments-horizontal"></i>
                        <span class="fi-label">Status</span>
                        <select name="status" class="filter-inp-new" onchange="this.form.submit()" style="min-width:150px">
                            <option value="">Semua Status</option>
                            @foreach($statusList as $key => $label)
                                <option value="{{ $key }}" {{ ($filters['status'] ?? '') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group-new">
                        <i class="ti ti-calendar"></i>
                        <span class="fi-label">Tanggal</span>
                        <input type="date" name="tanggal_minta" class="filter-inp-new"
                               value="{{ $filters['tanggal_minta'] ?? '' }}" onchange="this.form.submit()">
                    </div>
                    <div class="filter-group-new">
                        <i class="ti ti-search"></i>
                        <input type="text" name="search" class="filter-inp-new"
                               value="{{ $filters['search'] ?? '' }}"
                               placeholder="Cari no. permintaan / bank darah..."
                               style="min-width:230px"
                               onkeypress="if(event.keyCode==13) this.form.submit()">
                    </div>
                    <a href="{{ url('permintaan_darah_penyimpanan') }}" class="btn-reset-new">
                        <i class="ti ti-refresh"></i> Reset
                    </a>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-card-new">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:42px">No</th>
                                <th style="width:170px">No. Permintaan</th>
                                <th style="width:110px">Tanggal</th>
                                <th>Bank Darah</th>
                                <th class="text-center" style="width:130px">Status</th>
                                <th class="text-center" style="width:72px">Jumlah</th>
                                <th style="width:170px">Petugas</th>
                                <th class="text-center" style="width:140px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $index => $row)
                            @php
                                $initials = collect(explode(' ', $row->user->name ?? 'U'))
                                    ->map(fn($w) => strtoupper(substr($w,0,1)))
                                    ->take(2)->implode('');

                                $avatarColors = [
                                    'style="background:var(--blue-50);color:var(--blue-600)"',
                                    'style="background:var(--amber-50);color:var(--amber-600)"',
                                    'style="background:var(--green-50);color:var(--green-600)"',
                                ];
                                $avatarStyle = $avatarColors[$row->id % 3];

                                $statusIcons = [
                                    'permintaan' => 'ti-clock',
                                    'proses'     => 'ti-loader',
                                    'selesai'    => 'ti-circle-check',
                                    'batal'      => 'ti-circle-x',
                                ];
                                $statusIcon = $statusIcons[$row->status] ?? 'ti-point';
                            @endphp
                            <tr>
                                <td class="text-center" style="color:#9CA3AF;font-size:12px">
                                    {{ $rows->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="no-req-cell">
                                        <strong>{{ $row->no_permintaan }}</strong>
                                        <small>{{ \Carbon\Carbon::parse($row->tanggal_minta)->isoFormat('DD MMM YYYY') }}</small>
                                    </div>
                                </td>
                                <td style="color:#6B7280;font-size:13px">
                                    {{ \Carbon\Carbon::parse($row->tanggal_minta)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="bank-cell">
                                        <div class="bank-icon">
                                            <i class="ti ti-building-hospital"></i>
                                        </div>
                                        <span class="bank-name">{{ $row->bank_darah_nama }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="status-badge-new sbadge-{{ $row->status }}">
                                        <i class="ti {{ $statusIcon }}"></i>
                                        {{ $row->status_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="jml-chip">{{ $row->details->sum('jumlah_kantong') }}</span>
                                </td>
                                <td>
                                    <div class="officer-cell">
                                        <div class="officer-avatar" {!! $avatarStyle !!}>{{ $initials }}</div>
                                        <span class="officer-name">{{ $row->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="act-btn-new abn-detail" onclick="showDetail({{ $row->id }})" title="Detail">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <button class="act-btn-new abn-edit" onclick="showEditModal({{ $row->id }})" title="Edit">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <button class="act-btn-new abn-status" onclick="showStatusModal({{ $row->id }}, '{{ $row->status }}')" title="Update Status">
                                            <i class="ti ti-refresh"></i>
                                        </button>
                                        <button class="act-btn-new abn-delete" onclick="deletePermintaan({{ $row->id }})" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="ti ti-inbox"></i>
                                        <span>Tidak ada data permintaan darah</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($rows, 'links'))
                <div class="mt-3">{{ $rows->appends(request()->query())->links() }}</div>
            @endif

        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: Form Create / Edit
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="formModal" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="formModalTitle">
                    <i class="ti ti-droplet mr-1"></i> Form Permintaan Darah
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body py-3">
                <input type="hidden" id="editId">

                {{-- Header info --}}
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label-sm">Nomor Permintaan</label>
                        <input type="text" id="no_permintaan_display" class="form-control-sm-custom" readonly disabled
                               placeholder="(Auto-generate)">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Tanggal Minta <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal_minta" class="form-control-sm-custom" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Bank Darah <span class="text-danger">*</span></label>
                        <select id="bank_darah_kode" class="form-control-sm-custom" required>
                            <option value="">Pilih Bank Darah...</option>
                        </select>
                        <input type="hidden" id="bank_darah_nama">
                    </div>
                </div>

                <hr class="section-divider">
                <div class="section-title-new"><i class="ti ti-list"></i> Detail Permintaan</div>

                {{-- Input baris detail --}}
                <div class="row" style="gap:0">
                    <div class="col-md-3 pr-1">
                        <label class="form-label-sm">Jenis Darah</label>
                        <select id="jenis_darah" class="form-control-sm-custom">
                            <option value="">Pilih Jenis Darah...</option>
                        </select>
                    </div>
                    <div class="col-md-2 px-1">
                        <label class="form-label-sm">Golongan</label>
                        <select id="golongan_darah" class="form-control-sm-custom">
                            <option value="">Pilih</option>
                            <option>A</option><option>B</option>
                            <option>AB</option><option>O</option>
                        </select>
                    </div>
                    <div class="col-md-2 px-1">
                        <label class="form-label-sm">Rhesus</label>
                        <select id="rhesus" class="form-control-sm-custom">
                            <option value="Positif">Positif (+)</option>
                            <option value="Negatif">Negatif (−)</option>
                        </select>
                    </div>
                    <div class="col-md-1 px-1">
                        <label class="form-label-sm">Jumlah</label>
                        <input type="number" id="jumlah_kantong" class="form-control-sm-custom" value="1" min="1">
                    </div>
                    <div class="col-md-2 px-1">
                        <label class="form-label-sm">Jumlah CC</label>
                        <input type="number" id="jumlah_cc" class="form-control-sm-custom" value="200">
                    </div>
                    <div class="col-md-2 px-1">
                        <label class="form-label-sm">Tgl Perlu</label>
                        <input type="date" id="tanggal_perlu" class="form-control-sm-custom">
                    </div>
                    <div class="col-md-2 px-1">
                        <label class="form-label-sm">No. FPUP</label>
                        <div style="position:relative">
                            <input type="text" id="no_fpup_search" class="form-control-sm-custom"
                                   placeholder="Cari No FPUP / Nama Pasien...">
                            <input type="hidden" id="no_fpup">
                            <div id="fpupSearchResult"></div>
                        </div>
                    </div>
                    <div class="col-md-2 pl-1 d-flex align-items-end">
                        <button type="button" class="btn btn-success btn-block btn-sm"
                                onclick="addDetail()"
                                style="height:38px;border-radius:8px;font-weight:600">
                            <i class="ti ti-plus mr-1"></i> Tambah Detail
                        </button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div id="fpupDetailBox" class="fpup-detail-box"></div>
                    </div>
                </div>

                {{-- Detail table --}}
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-sm detail-table mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:32px">#</th>
                                <th>Jenis</th>
                                <th class="text-center">Gol</th>
                                <th>Rhesus</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">CC</th>
                                <th>Tgl Perlu</th>
                                <th>No FPUP</th>
                                <th class="text-center" style="width:80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                            <tr id="emptyDetailRow">
                                <td colspan="9" class="text-center py-3" style="color:#9CA3AF;font-size:12px">
                                    Belum ada detail — tambahkan di atas
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="tfoot-total">
                            <tr>
                                <td colspan="4" class="text-right">Total:</td>
                                <td class="text-center" id="totalKantong">0</td>
                                <td class="text-center" id="totalCc">0</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light" style="padding:10px 20px">
                <small class="text-muted mr-auto"><span class="text-danger">*</span> Wajib diisi</small>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success btn-sm" onclick="savePermintaan()">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: Update Status
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="ti ti-refresh mr-1"></i>Update Status</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="statusPermintaanId">
                <label class="form-label-sm">Status Baru</label>
                <select id="newStatus" class="form-control-sm-custom">
                    <option value="permintaan">Permintaan</option>
                    <option value="proses">Proses</option>
                    <option value="selesai">Selesai</option>
                    <option value="batal">Batal</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="updateStatus()">
                    <i class="ti ti-check mr-1"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: Detail View
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="ti ti-eye mr-1"></i>Detail Permintaan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-info" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ══════════════════════════════════════════════════════
   STATE
══════════════════════════════════════════════════════ */
var details = [];
var nextId  = 1;
var BASE    = "{{ url('penyimpanan/permintaan_darah_penyimpanan') }}";
var CSRF    = '{{ csrf_token() }}';

/* ══════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════ */
function fmtDate(d) {
    if (!d) return '-';
    var p = d.split('-');
    return p.length === 3 ? (p[2]+'/'+p[1]+'/'+p[0]) : d;
}
function golClass(g) {
    return {A:'gol-a',B:'gol-b',AB:'gol-ab',O:'gol-o'}[g]||'gol-o';
}
function showLoading(msg) {
    msg = msg || 'Memproses...';
    document.getElementById('loadingText').textContent = msg;
    document.getElementById('loadingOverlay').classList.add('active');
}
function hideLoading() {
    document.getElementById('loadingOverlay').classList.remove('active');
}

/* ══════════════════════════════════════════════════════
   DETAIL TABLE
══════════════════════════════════════════════════════ */
function renderDetailTable() {
    var tbody      = document.getElementById('detailTableBody');
    var emptyRow   = document.getElementById('emptyDetailRow');
    var totKantong = 0, totCc = 0;

    Array.from(tbody.querySelectorAll('tr.data-row')).forEach(function(r){ r.remove(); });

    if (details.length === 0) {
        if (emptyRow) emptyRow.style.display = '';
        document.getElementById('totalKantong').textContent = '0';
        document.getElementById('totalCc').textContent      = '0';
        return;
    }
    if (emptyRow) emptyRow.style.display = 'none';

    details.forEach(function(d, i) {
        totKantong += +d.jumlah_kantong || 0;
        totCc      += +d.jumlah_cc      || 0;
        var tr = document.createElement('tr');
        tr.className = 'data-row';
        var rhClass = d.rhesus === 'Positif' ? 'rhesus-pos' : 'rhesus-neg';
        var rhText  = d.rhesus === 'Positif' ? '+ Positif' : '&minus; Negatif';
        tr.innerHTML =
            '<td class="text-center">'+(i+1)+'</td>'+
            '<td>'+d.jenis_darah+'</td>'+
            '<td class="text-center"><span class="goldar-pill '+golClass(d.golongan_darah)+'">'+d.golongan_darah+'</span></td>'+
            '<td class="'+rhClass+'">'+rhText+'</td>'+
            '<td class="text-center">'+d.jumlah_kantong+'</td>'+
            '<td class="text-center">'+(d.jumlah_cc||0)+'</td>'+
            '<td>'+fmtDate(d.tanggal_perlu)+'</td>'+
            '<td>'+(d.no_fpup||'-')+'</td>'+
            '<td class="text-center">'+
                '<div class="actions-cell">'+
                    '<button type="button" class="act-btn-new abn-edit" onclick="editDetail('+d.id+')" title="Edit">'+
                        '<i class="ti ti-pencil"></i>'+
                    '</button>'+
                    '<button type="button" class="act-btn-new abn-delete" onclick="deleteDetail('+d.id+')" title="Hapus">'+
                        '<i class="ti ti-trash"></i>'+
                    '</button>'+
                '</div>'+
            '</td>';
        tbody.appendChild(tr);
    });

    document.getElementById('totalKantong').textContent = totKantong;
    document.getElementById('totalCc').textContent      = totCc;
}

function addDetail() {
    var jenis    = document.getElementById('jenis_darah').value;
    var golongan = document.getElementById('golongan_darah').value;
    var kantong  = parseInt(document.getElementById('jumlah_kantong').value);

    if (!jenis)    { alert('Pilih jenis darah!'); return; }
    if (!golongan) { alert('Pilih golongan darah!'); return; }
    if (kantong < 1) { alert('Jumlah kantong minimal 1!'); return; }

    details.push({
        id:             nextId++,
        jenis_darah:    jenis,
        golongan_darah: golongan,
        rhesus:         document.getElementById('rhesus').value,
        jumlah_kantong: kantong,
        jumlah_cc:      parseInt(document.getElementById('jumlah_cc').value)||0,
        tanggal_perlu:  document.getElementById('tanggal_perlu').value,
        no_fpup:        document.getElementById('no_fpup').value,
    });
    renderDetailTable();
    document.getElementById('jenis_darah').value    = '';
    document.getElementById('golongan_darah').value = '';
    document.getElementById('jumlah_kantong').value = '1';
    document.getElementById('jumlah_cc').value      = '200';
    document.getElementById('tanggal_perlu').value  = '';
    document.getElementById('no_fpup').value        = '';
    document.getElementById('no_fpup_search').value = '';
    document.getElementById('fpupDetailBox').style.display = 'none';
    document.getElementById('fpupDetailBox').innerHTML     = '';
}

function editDetail(id) {
    var d = details.find(function(x){ return x.id===id; });
    if (!d) return;
    document.getElementById('jenis_darah').value    = d.jenis_darah;
    document.getElementById('golongan_darah').value = d.golongan_darah;
    document.getElementById('rhesus').value         = d.rhesus;
    document.getElementById('jumlah_kantong').value = d.jumlah_kantong;
    document.getElementById('jumlah_cc').value      = d.jumlah_cc;
    document.getElementById('tanggal_perlu').value  = d.tanggal_perlu;
    document.getElementById('no_fpup').value        = d.no_fpup;
    document.getElementById('no_fpup_search').value = d.no_fpup;
    details = details.filter(function(x){ return x.id!==id; });
    renderDetailTable();
}

function deleteDetail(id) {
    if (confirm('Hapus detail ini?')) {
        details = details.filter(function(x){ return x.id!==id; });
        renderDetailTable();
    }
}

/* ══════════════════════════════════════════════════════
   LOAD BANK DARAH
══════════════════════════════════════════════════════ */
async function loadBankDarah(selectedKode) {
    selectedKode = selectedKode || '';
    try {
        var res  = await fetch(BASE+'/search-bank-darah?q=');
        var data = await res.json();
        var sel  = document.getElementById('bank_darah_kode');
        sel.innerHTML = '<option value="">Pilih Bank Darah...</option>';
        data.forEach(function(rs) {
            var opt = new Option(rs.kode+' \u2014 '+rs.nama, rs.kode);
            if (rs.kode === selectedKode) opt.selected = true;
            sel.appendChild(opt);
        });
    } catch(e) { console.error('loadBankDarah:', e); }
}

/* ══════════════════════════════════════════════════════
   LOAD JENIS DARAH
══════════════════════════════════════════════════════ */
async function loadJenisDarah(selected) {
    selected = selected || '';
    try {
        var res    = await fetch(BASE+'/jenis-darah');
        var data   = await res.json();
        var select = document.getElementById('jenis_darah');
        select.innerHTML = '<option value="">Pilih Jenis Darah...</option>';
        data.forEach(function(item) {
            var value = item.nama_pendek || item.jenis_darah || item.keterangan;
            var opt   = new Option(value, value);
            if (selected === value) opt.selected = true;
            select.appendChild(opt);
        });
    } catch(e) { console.error('loadJenisDarah:', e); }
}

/* ══════════════════════════════════════════════════════
   GENERATE NO PERMINTAAN
══════════════════════════════════════════════════════ */
async function generateNoPermintaan() {
    try {
        var res  = await fetch(BASE+'/next-no-permintaan');
        var data = await res.json();
        document.getElementById('no_permintaan_display').value = data.no_permintaan;
    } catch(e) { console.error('generateNoPermintaan:', e); }
}

/* ══════════════════════════════════════════════════════
   SHOW CREATE MODAL
══════════════════════════════════════════════════════ */
function showCreateModal() {
    document.getElementById('editId').value             = '';
    document.getElementById('formModalTitle').innerHTML = '<i class="ti ti-droplet mr-1"></i> Tambah Permintaan Darah';
    document.getElementById('tanggal_minta').value      = '{{ date("Y-m-d") }}';
    document.getElementById('bank_darah_kode').value    = '';
    document.getElementById('bank_darah_nama').value    = '';
    details = []; nextId = 1;
    renderDetailTable();
    generateNoPermintaan();
    $('#formModal').modal('show');
}

/* ══════════════════════════════════════════════════════
   SHOW EDIT MODAL
══════════════════════════════════════════════════════ */
async function showEditModal(id) {
    try {
        showLoading('Memuat data...');
        var res    = await fetch(BASE+'/'+id);
        var result = await res.json();
        if (!result.success) throw new Error(result.message);
        var data = result.data;

        document.getElementById('editId').value             = data.id;
        document.getElementById('formModalTitle').innerHTML = '<i class="ti ti-pencil mr-1"></i> Edit Permintaan Darah';
        document.getElementById('no_permintaan_display').value = data.no_permintaan;
        document.getElementById('tanggal_minta').value         = data.tanggal_minta ? data.tanggal_minta.substring(0,10) : '';
        document.getElementById('bank_darah_nama').value       = data.bank_darah_nama;

        await loadBankDarah(data.bank_darah_kode);

        details = (data.details||[]).map(function(d, idx) {
            return {
                id:             idx+1,
                jenis_darah:    d.jenis_darah,
                golongan_darah: d.golongan_darah,
                rhesus:         d.rhesus,
                jumlah_kantong: d.jumlah_kantong,
                jumlah_cc:      d.jumlah_cc,
                tanggal_perlu:  d.tanggal_perlu ? d.tanggal_perlu.substring(0,10) : '',
                no_fpup:        d.no_fpup,
            };
        });
        nextId = details.length + 1;
        renderDetailTable();
        $('#formModal').modal('show');
    } catch(e) {
        alert('Error: '+e.message);
    } finally {
        hideLoading();
    }
}

/* ══════════════════════════════════════════════════════
   SAVE (Create / Update)
══════════════════════════════════════════════════════ */
async function savePermintaan() {
    var bankKode = document.getElementById('bank_darah_kode').value;
    var bankNama = document.getElementById('bank_darah_nama').value;
    var tgl      = document.getElementById('tanggal_minta').value;

    if (!bankKode) { alert('Pilih bank darah!'); return; }
    if (!tgl)      { alert('Isi tanggal minta!'); return; }
    if (details.length === 0) { alert('Minimal tambahkan 1 detail!'); return; }

    var id      = document.getElementById('editId').value;
    var url     = id ? (BASE+'/'+id) : BASE;
    var payload = {
        bank_darah_kode: bankKode,
        bank_darah_nama: bankNama,
        tanggal_minta:   tgl,
        detail:          details,
        _method:         id ? 'PUT' : 'POST',
    };

    showLoading('Menyimpan...');
    try {
        var res    = await fetch(url, {
            method:  'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body:    JSON.stringify(payload),
        });
        var result = await res.json();
        if (result.success) {
            $('#formModal').modal('hide');
            setTimeout(function(){ alert(result.message); location.reload(); }, 200);
        } else {
            alert('Gagal: '+result.message);
        }
    } catch(e) {
        alert('Error: '+e.message);
    } finally {
        hideLoading();
    }
}

/* ══════════════════════════════════════════════════════
   STATUS MODAL
══════════════════════════════════════════════════════ */
function showStatusModal(id, currentStatus) {
    document.getElementById('statusPermintaanId').value = id;
    document.getElementById('newStatus').value          = currentStatus;
    $('#statusModal').modal('show');
}

async function updateStatus() {
    var id     = document.getElementById('statusPermintaanId').value;
    var status = document.getElementById('newStatus').value;
    showLoading('Mengubah status...');
    try {
        var res    = await fetch(BASE+'/'+id+'/status', {
            method:  'PATCH',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body:    JSON.stringify({ status: status }),
        });
        var result = await res.json();
        if (result.success) {
            $('#statusModal').modal('hide');
            setTimeout(function(){ alert('Status berhasil diubah: '+result.status); location.reload(); }, 200);
        } else {
            alert('Gagal: '+result.message);
        }
    } catch(e) {
        alert('Error: '+e.message);
    } finally {
        hideLoading();
    }
}

/* ══════════════════════════════════════════════════════
   DETAIL VIEW MODAL
══════════════════════════════════════════════════════ */
async function showDetail(id) {
    $('#detailModal').modal('show');
    document.getElementById('detailContent').innerHTML =
        '<div class="text-center py-4"><div class="spinner-border text-info" role="status"></div></div>';
    try {
        var res    = await fetch(BASE+'/'+id);
        var result = await res.json();
        if (!result.success) throw new Error(result.message);
        var d = result.data;

        var rows = (d.details||[]).map(function(item, i) {
            var rhClass  = item.rhesus==='Positif' ? 'rhesus-pos' : 'rhesus-neg';
            var rhText   = item.rhesus==='Positif' ? '+ Positif' : '&minus; Negatif';
            var tglPerlu = item.tanggal_perlu ? item.tanggal_perlu.substring(0,10) : '';
            return '<tr>'+
                '<td class="text-center">'+(i+1)+'</td>'+
                '<td>'+item.jenis_darah+'</td>'+
                '<td class="text-center"><span class="goldar-pill '+golClass(item.golongan_darah)+'">'+item.golongan_darah+'</span></td>'+
                '<td class="'+rhClass+'">'+rhText+'</td>'+
                '<td class="text-center">'+item.jumlah_kantong+'</td>'+
                '<td class="text-center">'+(item.jumlah_cc||0)+'</td>'+
                '<td>'+fmtDate(tglPerlu)+'</td>'+
                '<td>'+(item.no_fpup||'-')+'</td>'+
            '</tr>';
        }).join('');

        var tglMinta  = d.tanggal_minta ? d.tanggal_minta.substring(0,10) : '';
        var userName  = (d.user && d.user.name) ? d.user.name : '-';
        var createdAt = d.created_at ? new Date(d.created_at).toLocaleString('id-ID') : '-';

        var statusIcons = { permintaan:'ti-clock', proses:'ti-loader', selesai:'ti-circle-check', batal:'ti-circle-x' };
        var sIcon = statusIcons[d.status] || 'ti-point';

        document.getElementById('detailContent').innerHTML =
            '<div class="row mb-3">'+
                '<div class="col-md-6">'+
                    '<table class="table table-sm info-strip">'+
                        '<tr><th>No. Permintaan</th><td><strong>'+d.no_permintaan+'</strong></td></tr>'+
                        '<tr><th>Tanggal Minta</th><td>'+fmtDate(tglMinta)+'</td></tr>'+
                        '<tr><th>Bank Darah</th><td>'+d.bank_darah_nama+'</td></tr>'+
                    '</table>'+
                '</div>'+
                '<div class="col-md-6">'+
                    '<table class="table table-sm info-strip">'+
                        '<tr><th>Status</th><td><span class="status-badge-new sbadge-'+d.status+'"><i class="ti '+sIcon+'"></i> '+d.status_label+'</span></td></tr>'+
                        '<tr><th>Petugas</th><td>'+userName+'</td></tr>'+
                        '<tr><th>Dibuat</th><td>'+createdAt+'</td></tr>'+
                    '</table>'+
                '</div>'+
            '</div>'+
            '<div class="section-title-new"><i class="ti ti-list"></i> Rincian Darah</div>'+
            '<div class="table-responsive">'+
                '<table class="table table-bordered table-sm detail-table">'+
                    '<thead><tr>'+
                        '<th class="text-center">#</th><th>Jenis</th>'+
                        '<th class="text-center">Gol</th><th>Rhesus</th>'+
                        '<th class="text-center">Kantong</th><th class="text-center">CC</th>'+
                        '<th>Tgl Perlu</th><th>No FPUP</th>'+
                    '</tr></thead>'+
                    '<tbody>'+(rows||'<tr><td colspan="8" class="text-center">Tidak ada detail</td></tr>')+'</tbody>'+
                '</table>'+
            '</div>';
    } catch(e) {
        document.getElementById('detailContent').innerHTML =
            '<div class="alert alert-danger"><i class="ti ti-alert-circle mr-1"></i> Error: '+e.message+'</div>';
    }
}

/* ══════════════════════════════════════════════════════
   DELETE
══════════════════════════════════════════════════════ */
function deletePermintaan(id) {
    if (!confirm('Yakin ingin menghapus permintaan ini?')) return;
    showLoading('Menghapus...');
    fetch(BASE+'/'+id, {
        method:  'DELETE',
        headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
    })
    .then(function(r){ return r.json(); })
    .then(function(result) {
        if (result.success) { alert(result.message); location.reload(); }
        else alert('Gagal: '+result.message);
    })
    .catch(function(e){ alert('Error: '+e.message); })
    .finally(hideLoading);
}

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {
    loadBankDarah();
    loadJenisDarah();

    document.getElementById('bank_darah_kode').addEventListener('change', function() {
        var opt      = this.options[this.selectedIndex];
        var fullText = opt.text;
        var dashIdx  = fullText.indexOf(' — ');
        document.getElementById('bank_darah_nama').value =
            dashIdx >= 0 ? fullText.substring(dashIdx+3) : fullText;
    });

    /* ── FPUP Search ── */
    var fpupInput  = document.getElementById('no_fpup_search');
    var fpupResult = document.getElementById('fpupSearchResult');

    fpupInput.addEventListener('keyup', async function() {
        var keyword = this.value;
        if (keyword.length < 1) { fpupResult.style.display = 'none'; return; }
        try {
            var res  = await fetch(BASE+'/search-fpup?q='+encodeURIComponent(keyword));
            var data = await res.json();
            if (data.length === 0) {
                fpupResult.innerHTML = '<div style="padding:10px 12px;font-size:13px;color:#6B7280">Data tidak ditemukan</div>';
                fpupResult.style.display = 'block'; return;
            }
            var html = '';
            data.forEach(function(item) {
                html += '<div class="fpup-item"'+
                    ' data-no="'+item.no_fpup+'"'+
                    ' data-nama="'+item.nama_pasien+'"'+
                    ' data-goldar="'+item.golongan_darah+'"'+
                    ' data-rhesus="'+item.rhesus+'"'+
                    ' data-diagnosa="'+(item.diagnosa||'')+'"'+
                    '>'+
                    '<strong style="font-size:13px">'+item.no_fpup+'</strong><br>'+
                    '<small style="color:#6B7280">'+item.nama_pasien+'</small>'+
                    '</div>';
            });
            fpupResult.innerHTML = html;
            fpupResult.style.display = 'block';
        } catch(e) { console.error(e); }
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.fpup-item')) {
            var item     = e.target.closest('.fpup-item');
            var noFpup   = item.dataset.no;
            var nama     = item.dataset.nama;
            var goldar   = item.dataset.goldar;
            var rhesus   = item.dataset.rhesus;
            var diagnosa = item.dataset.diagnosa;

            document.getElementById('no_fpup').value        = noFpup;
            document.getElementById('no_fpup_search').value = noFpup+' - '+nama;
            document.getElementById('golongan_darah').value = goldar;
            document.getElementById('rhesus').value         = rhesus;
            fpupResult.style.display = 'none';

            document.getElementById('fpupDetailBox').innerHTML =
                '<div class="row">'+
                    '<div class="col-md-3"><strong style="font-size:11px;color:#6B7280;text-transform:uppercase">No FPUP</strong><br><span style="font-size:13px;font-weight:600">'+noFpup+'</span></div>'+
                    '<div class="col-md-4"><strong style="font-size:11px;color:#6B7280;text-transform:uppercase">Pasien</strong><br><span style="font-size:13px">'+nama+'</span></div>'+
                    '<div class="col-md-2"><strong style="font-size:11px;color:#6B7280;text-transform:uppercase">Gol Darah</strong><br><span class="goldar-pill '+golClass(goldar)+'" style="margin-top:4px">'+goldar+'</span></div>'+
                    '<div class="col-md-3"><strong style="font-size:11px;color:#6B7280;text-transform:uppercase">Rhesus</strong><br><span class="'+(rhesus==='Positif'?'rhesus-pos':'rhesus-neg')+'" style="font-size:14px">'+(rhesus==='Positif'?'+ Positif':'&minus; Negatif')+'</span></div>'+
                    '<div class="col-md-12 mt-2"><strong style="font-size:11px;color:#6B7280;text-transform:uppercase">Diagnosa</strong><br><span style="font-size:13px">'+(diagnosa||'-')+'</span></div>'+
                '</div>';
            document.getElementById('fpupDetailBox').style.display = 'block';
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#fpupSearchResult') && !e.target.closest('#no_fpup_search')) {
            fpupResult.style.display = 'none';
        }
    });
});

function golClass(g) {
    return {A:'gol-a',B:'gol-b',AB:'gol-ab',O:'gol-o'}[g]||'gol-o';
}
</script>
@endpush