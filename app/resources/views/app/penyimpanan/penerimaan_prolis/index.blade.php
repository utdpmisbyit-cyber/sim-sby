
@extends('layouts.index')

@section('title', 'Penerimaan Prolis – Penyimpanan Darah')

@push('styles')
<style>
    :root {
        --pmi-red      : #c0392b;
        --pmi-red-dark : #962d22;
        --pmi-red-light: #fdecea;
        --header-bg    : #d32f2f;
        --panel-border : #b0bec5;
        --row-hover    : #fff3f3;
        --row-expired  : #ffe0e0;
        --th-bg        : #eceff1;
    }

    .card-panel {
        background: #fff;
        border: 1px solid var(--panel-border);
        border-radius: 6px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
    }
    .card-panel-header {
        background: var(--pmi-red);
        color: #fff;
        padding: 6px 12px;
        border-radius: 5px 5px 0 0;
        font-weight: 600;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label-sm {
        font-size: 11px;
        font-weight: 600;
        color: #546e7a;
        margin-bottom: 2px;
        white-space: nowrap;
        display: block;
    }
    .form-control-sm, .form-select-sm {
        font-size: 12px;
        height: 28px;
        padding: 2px 8px;
        border-color: #b0bec5;
    }
    .form-control-sm:focus, .form-select-sm:focus {
        border-color: var(--pmi-red);
        box-shadow: 0 0 0 2px rgba(192,57,43,.15);
    }

    .meta-row {
        background: #fafafa;
        border-bottom: 1px solid #e0e0e0;
        padding: 8px 12px;
    }
    .meta-item { display: flex; align-items: center; gap: 6px; }

    .filter-row {
        background: #f5f5f5;
        border-bottom: 1px solid #e0e0e0;
        padding: 6px 12px;
    }

    .tbl-penyimpanan {
        font-size: 11.5px;
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }
    .tbl-penyimpanan thead tr th {
        background: var(--th-bg);
        border: 1px solid #cfd8dc;
        padding: 5px 6px;
        font-weight: 700;
        text-align: center;
        white-space: nowrap;
        color: #37474f;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .tbl-penyimpanan tbody tr td {
        border: 1px solid #eceff1;
        padding: 4px 6px;
        white-space: nowrap;
    }
    .tbl-penyimpanan tbody tr:hover       { background: var(--row-hover); cursor: pointer; }
    .tbl-penyimpanan tbody tr.selected    { background: #ffcdd2 !important; outline: 2px solid var(--pmi-red); }
    .tbl-penyimpanan tbody tr.expired-row { background: var(--row-expired); color: #c62828; }

    .tbl-wrapper {
        overflow-y: auto;
        max-height: 280px;
        border: 1px solid #cfd8dc;
    }

    .footer-jumlah {
        background: #eceff1;
        border-top: 1px solid #cfd8dc;
        padding: 5px 12px;
        text-align: right;
        font-weight: 700;
        font-size: 12px;
        color: #37474f;
    }
    .jumlah-badge {
        display: inline-block;
        min-width: 40px;
        text-align: center;
        background: var(--pmi-red);
        color: #fff;
        border-radius: 4px;
        padding: 1px 10px;
        font-size: 13px;
    }

    .btn-pmi {
        background: var(--pmi-red);
        color: #fff;
        border: none;
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 4px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-pmi:hover { background: var(--pmi-red-dark); color: #fff; }

    .btn-outline-pmi {
        background: transparent;
        color: var(--pmi-red);
        border: 1.5px solid var(--pmi-red);
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-outline-pmi:hover { background: var(--pmi-red-light); }

    .sts-badge {
        display: inline-block;
        padding: 1px 7px;
        border-radius: 10px;
        font-size: 10.5px;
        font-weight: 600;
    }
    .sts-3 { background: #e3f2fd; color: #1565c0; }

    .modal-header            { background: var(--header-bg); color: #fff; }
    .modal-header .btn-close { filter: invert(1); }

    .spinner-overlay {
        position: absolute; inset: 0;
        background: rgba(255,255,255,.7);
        display: flex; align-items: center; justify-content: center;
        z-index: 10; border-radius: 5px;
    }
    .spinner-overlay.d-none { display: none !important; }
    .panel-relative { position: relative; }
</style>
@endpush

@section('content')

<div class="container-fluid px-3 py-3">
<div class="row g-3">

    {{-- PANEL KIRI --}}
    <div class="col-12 col-xl-6">
        <div class="card-panel panel-relative" id="panelKiri">
            <div class="spinner-overlay d-none" id="spinnerKiri">
                <div class="spinner-border text-danger" style="width:2rem;height:2rem;"></div>
            </div>

            <div class="card-panel-header">
                <i class="bi bi-droplet-half"></i> Data Penerimaan Prolis
            </div>

            <div class="meta-row">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <label class="form-label-sm">No. Penerimaan</label>
                        <div class="meta-item">
                            <input type="text" class="form-control-sm form-control"
                                   id="noPenerimaan" value="{{ $noPenerimaan }}" readonly
                                   style="width:160px; background:#fff8e1; font-weight:700;" />
                            <i class="bi bi-lock-fill text-secondary" style="font-size:14px;"></i>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="form-label-sm">Tgl. Penerimaan</label>
                        <input type="text" class="form-control-sm form-control"
                               id="tglPenerimaan" value="{{ $tglPenerimaan }}" readonly
                               style="width:110px;" />
                    </div>
                    <div class="col-auto ms-auto">
                        <label class="form-label-sm">Petugas</label>
                        <div class="input-group" style="width:190px;">
                            <span class="input-group-text" style="font-size:11px;padding:2px 6px;height:28px;">ADM</span>
                            <input type="text" class="form-control form-control-sm"
                                   value="{{ $petugasNama }}" readonly />
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-row">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label-sm">No. Pengiriman</label>
                        <div class="input-group" style="width:215px;">
                            <input type="text" class="form-control form-control-sm"
                                   id="noPengiriman" placeholder="K26051816777 ..."
                                   style="font-weight:600;" />
                            <button class="btn btn-sm btn-secondary" type="button"
                                    id="btnCariBrowse" title="Browse">
                                <i class="bi bi-three-dots"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn-pmi" id="btnLoadPengiriman">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-auto ms-auto">
                        <!-- <button class="btn-outline-pmi"
                                id="btnTambahKiri">
                            <i class="bi bi-plus-circle"></i>
                            Tambah
                        </button> -->
                        <button class="btn-outline-pmi ms-1"
                                id="btnEditKiri">
                            <i class="bi bi-pencil-square"></i>
                            Edit
                        </button>
                        <button class="btn-pmi ms-1"
                                id="btnSimpanPenerimaan">
                            <i class="bi bi-save"></i>
                            Simpan
                        </button>
                        <button class="btn-outline-pmi ms-1"
                                id="btnHapusKiri">
                            <i class="bi bi-trash3"></i>
                            Hapus
                        </button>
                </div>
                </div>
            </div>

            <div class="tbl-wrapper">
                <table class="tbl-penyimpanan" id="tabelKiri">
                    <thead>
                        <tr>
                            <th style="width:35px;">No.</th>
                            <th>No Stock</th>
                            <th>Jns</th>
                            <th>Gol</th>
                            <th>RH</th>
                            <th>Tgl.Aftap</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Kadaluarsa</th>
                            <th style="width:40px;">STS</th>
                            <th style="width:45px;">mL</th>
                            <th style="width:45px;">flg</th>
                        </tr>
                    </thead>
                    <tbody id="bodyKiri">
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">
                                <i class="bi bi-inbox fs-5"></i><br/>Ketik No. Pengiriman lalu klik Cari
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="footer-jumlah">
                Jumlah : <span class="jumlah-badge" id="jumlahKiri">0</span>
            </div>
        </div>
    </div>

    {{-- PANEL KANAN --}}
    <div class="col-12 col-xl-6">
        <div class="card-panel panel-relative" id="panelKanan">
            <div class="spinner-overlay d-none" id="spinnerKanan">
                <div class="spinner-border text-danger" style="width:2rem;height:2rem;"></div>
            </div>

            <div class="card-panel-header">
                <i class="bi bi-archive"></i> Cek Kapasitas Penyimpanan
            </div>

            <div class="filter-row">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label-sm">Jns Darah</label>
                        <select class="form-select form-select-sm" id="filterJenis" style="width:90px;">
                            @foreach($jenisOptions as $j)
                                <option value="{{ $j }}" {{ $j === 'PCLs' ? 'selected' : '' }}>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label-sm">Gol</label>
                        <select class="form-select form-select-sm" id="filterGolongan" style="width:72px;">
                            @foreach($golonganOptions as $g)
                                <option value="{{ $g }}" {{ $g === 'AB' ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label-sm">Rhesus</label>
                        <select class="form-select form-select-sm" id="filterRhesus" style="width:100px;">
                            @foreach($rhesusOptions as $r)
                                <option value="{{ $r }}" {{ $r === 'Positif' ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn-pmi" id="btnCekKapasitas">
                            <i class="bi bi-check2-circle"></i> Cek Kapasitas Penyimpanan
                        </button>
                    </div>
                </div>

                <div class="row g-2 align-items-center mt-1">
                    <div class="col-auto">
                        <label class="form-label-sm">Ruang</label>
                        <select class="form-select form-select-sm" id="filterRuangan" style="width:100px;">
                            @foreach($ruanganOptions as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label-sm">Isi</label>
                        <input type="text" class="form-control form-control-sm text-center"
                               id="infoIsi" value="0" readonly style="width:55px; font-weight:700;" />
                    </div>
                    <div class="col-auto">
                        <label class="form-label-sm">Kapasitas</label>
                        <input type="text" class="form-control form-control-sm text-center"
                               id="infoKapasitas" value="1200" readonly
                               style="width:65px; font-weight:700; background:#e8f5e9;" />
                    </div>
                    <div class="col-auto">
                        <label class="form-label-sm">Sisa Max</label>
                        <input type="text" class="form-control form-control-sm text-center"
                               id="infoSisaMax" value="0" readonly
                               style="width:55px; font-weight:700; background:#fff3e0;" />
                    </div>
                </div>
            </div>

            <div class="px-3 py-2 border-bottom">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label-sm">No. Stock</label>
                        <input type="text" class="form-control form-control-sm"
                               id="noStockSearch" placeholder="Cari no stock..."
                               style="width:200px;" />
                    </div>
                    <div class="col-auto">
                        <button class="btn-outline-pmi" id="btnCariStock">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="tbl-wrapper">
                <table class="tbl-penyimpanan" id="tabelKanan">
                    <thead>
                        <tr>
                            <th style="width:35px;">No.</th>
                            <th>No Stock</th>
                            <th>Jns</th>
                            <th>Gol</th>
                            <th>RH</th>
                            <th>Tgl.Aftap</th>
                            <th>Tgl Produksi</th>
                            <th>Tgl Kadaluarsa</th>
                            <th>Ruang</th>
                        </tr>
                    </thead>
                    <tbody id="bodyKanan">
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                <i class="bi bi-archive fs-5"></i><br/>Klik "Cek Kapasitas Penyimpanan" untuk memuat data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="footer-jumlah">
                Jumlah : <span class="jumlah-badge" id="jumlahKanan">0</span>
            </div>
        </div>
    </div>

</div>
</div>

{{-- MODAL TAMBAH/ubah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="bi bi-plus-square me-2"></i>Data Penyimpanan
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-sm">No. Stock</label>
                        <input type="text" class="form-control form-control-sm" id="m_no_stok" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-sm">No. Kantong</label>
                        <input type="text" class="form-control form-control-sm" id="m_no_kantong" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Jenis Darah</label>
                        <select class="form-select form-select-sm" id="m_jenis">
                            @foreach($jenisOptions as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Golongan Darah</label>
                        <select class="form-select form-select-sm" id="m_golongan">
                            @foreach($golonganOptions as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Rhesus</label>
                        <select class="form-select form-select-sm" id="m_rhesus">
                            @foreach($rhesusOptions as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Tgl. Aftap</label>
                        <input type="date" class="form-control form-control-sm" id="m_tgl_aftap" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Tgl. Produksi</label>
                        <input type="date" class="form-control form-control-sm" id="m_tgl_produksi" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-sm">Tgl. Expired</label>
                        <input type="date" class="form-control form-control-sm" id="m_tgl_expired" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Volume (mL)</label>
                        <input type="number" class="form-control form-control-sm" id="m_ml" min="0" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Berat (gr)</label>
                        <input type="number" class="form-control form-control-sm" id="m_gr" min="0" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Jumlah</label>
                        <input type="number" class="form-control form-control-sm" id="m_jumlah" value="1" min="1" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-sm">Status / Ruang</label>
                        <select class="form-select form-select-sm" id="m_status">
                            @foreach($ruanganOptions as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-sm">No. FPD (Pengiriman)</label>
                        <input type="text" class="form-control form-control-sm" id="m_no_fpd" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-sm">Skrining</label>
                        <input type="text" class="form-control form-control-sm" id="m_skrining" />
                    </div>
                    <div class="col-12">
                        <label class="form-label-sm">Keterangan</label>
                        <textarea class="form-control form-control-sm" id="m_keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div id="modalAlert" class="alert alert-danger mt-3 d-none py-2 small"></div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <button type="button" class="btn-pmi" id="btnUpdateModal">
                    <i class="bi bi-save me-1"></i>Update
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div id="toastMsg" class="toast align-items-center text-white border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body" id="toastBody">Berhasil</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const BASE = '{{ route("penyimpanan.penerimaan_prolis.index") }}'.replace(/\/$/, '');

    // ─── Helpers ──────────────────────────────────────────────────────────────
    function formatDateForInput(dateStr) {
    if (!dateStr || dateStr === '-') {
        return '';
    }

    const parts = dateStr.split('/');

    if (parts.length !== 3) {
        return '';
    }

    return `${parts[2]}-${parts[1]}-${parts[0]}`;
}
    function showToast(msg, type = 'success') {
        const el = document.getElementById('toastMsg');
        el.className = 'toast align-items-center text-white border-0 bg-' + (type === 'success' ? 'success' : 'danger');
        document.getElementById('toastBody').textContent = msg;
        bootstrap.Toast.getOrCreateInstance(el, { delay: 3000 }).show();
    }

    function spinner(id, show) {
        document.getElementById(id).classList.toggle('d-none', !show);
    }

    async function fetchJson(url, opts = {}) {
        const res = await fetch(url, {
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            ...opts,
        });
        if (!res.ok) {
            const text = await res.text();
            console.error(text);
            try {
                const err = JSON.parse(text);
                throw new Error(
                    err.message ||
                    'Terjadi kesalahan pada server.'
                );

            } catch {
                throw new Error(text);
            }
        }
        return res.json();
    }
  function isAlreadyScanned(noStok) {
    return Array.from(
        document.querySelectorAll(
            '#bodyKiri tr[data-id]'
        )
    ).some(row => {
        return row.cells[1]
            .textContent
            .trim() === noStok;
    });
}
    // ─── Render Kiri ─────────────────────────────────────────────────────────

   function renderKiri(rows) {
    const today = new Date().toISOString().slice(0, 10);
    const tbody = document.getElementById('bodyKiri');

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="11" class="text-center text-muted py-3">
                    Tidak ada data ditemukan.
                </td>
            </tr>
        `;

        document.getElementById('jumlahKiri').textContent = 0;
        return;
    }

    // AUTO SET FILTER KANAN DARI DATA KIRI
    const first = rows[0];

    if (first.jenis_darah) {
        document.getElementById('filterJenis').value =
            first.jenis_darah;
    }

    if (first.golongan_darah) {
        document.getElementById('filterGolongan').value =
            first.golongan_darah;
    }

    if (first.rhesus) {
        document.getElementById('filterRhesus').value =
            first.rhesus;
    }

    tbody.innerHTML = rows.map((r, i) => {

        const exp = r.tgl_expired
            ? r.tgl_expired < today
            : false;

        return `
            <tr class="${exp ? 'expired-row' : ''}" 
            data-id="${r.id}"
            data-id="${r.id}"
            data-no-kantong="${r.no_kantong ?? ''}"
            data-gr="${r.gr ?? ''}"
            data-status="${r.status ?? ''}"
            data-no-fpd="${r.no_fpd ?? ''}"
            data-skrining="${r.skrining ?? ''}">
                <td class="text-center">${i + 1}</td>
                <td>${r.no_stok ?? '-'}</td>
                <td>${r.jenis_darah ?? '-'}</td>
                <td class="text-center fw-bold">
                    ${r.golongan_darah ?? '-'}
                </td>
                <td>${r.rhesus ?? '-'}</td>
                <td>${r.tgl_aftap ?? '-'}</td>
                <td>${r.tgl_produksi ?? '-'}</td>
                <td>${r.tgl_expired ?? '-'}</td>
                <td class="text-center">
                    <span class="sts-badge sts-3">
                        ${r.status ?? '-'}
                    </span>
                </td>
                <td class="text-end">${r.ml ?? 0}</td>
                <td class="text-center">${r.jumlah ?? 0}</td>
            </tr>
        `;
    }).join('');

    document.getElementById('jumlahKiri').textContent =
        rows.length;

    bindRowSelect('bodyKiri');

    // AUTO LOAD KAPASITAS SETELAH KIRI TAMPIL
    document.getElementById('btnCekKapasitas').click();
}

    // ─── Render Kanan ─────────────────────────────────────────────────────────

    function renderKanan(rows) {
        const today = new Date().toISOString().slice(0, 10);
        const tbody = document.getElementById('bodyKanan');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data di ruangan ini.</td></tr>';
            document.getElementById('jumlahKanan').textContent = 0;
            return;
        }
        tbody.innerHTML = rows.map((r, i) => {
            const exp = r.tgl_expired && r.tgl_expired < today;
            return '<tr class="' + (exp ? 'expired-row' : '') + '" data-id="' + r.id + '">'
                + '<td class="text-center">' + (i+1) + '</td>'
                + '<td>' + (r.no_stok ?? '-') + '</td>'
                + '<td>' + (r.jenis_darah ?? '-') + '</td>'
                + '<td class="text-center fw-bold">' + (r.golongan_darah ?? '-') + '</td>'
                + '<td>' + (r.rhesus ?? '-') + '</td>'
                + '<td>' + (r.tgl_aftap ?? '-') + '</td>'
                + '<td>' + (r.tgl_produksi ?? '-') + '</td>'
                + '<td>' + (r.tgl_expired ?? '-') + '</td>'
                + '<td>' + (r.status ?? '-') + '</td>'
                + '</tr>';
        }).join('');
        document.getElementById('jumlahKanan').textContent = rows.length;
        bindRowSelect('bodyKanan');
    }

    // ─── Row Select ───────────────────────────────────────────────────────────

    let selectedKiri = null;
    let editMode = false;
    let selectedData = null;
    let lastNoPengiriman = '';

    function bindRowSelect(tbodyId) {
        document.getElementById(tbodyId).querySelectorAll('tr[data-id]').forEach(tr => {
            tr.addEventListener('click', function () {
                if (tbodyId === 'bodyKiri') {
                    document.querySelectorAll('#bodyKiri tr').forEach(r => r.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedKiri = this.dataset.id;
                    selectedData = {
                      id: this.dataset.id,
                        no_stok: this.cells[1].textContent.trim(),
                        jenis_darah: this.cells[2].textContent.trim(),
                        golongan_darah: this.cells[3].textContent.trim(),
                        rhesus: this.cells[4].textContent.trim(),
                        tgl_aftap: this.cells[5].textContent.trim(),
                        tgl_produksi: this.cells[6].textContent.trim(),
                        tgl_expired: this.cells[7].textContent.trim(),
                        status: this.cells[8].textContent.trim(),
                        ml: this.cells[9].textContent.trim(),
                        jumlah: this.cells[10].textContent.trim(),
                        no_kantong: this.dataset.noKantong ?? '',
                        gr: this.dataset.gr ?? '',
                        no_fpd: this.dataset.noFpd ?? '',
                        skrining: this.dataset.skrining ?? '',
                    };
                }
            });
        });
    }
    document.getElementById('btnEditKiri')
        .addEventListener('click', function () {
            if (!selectedData) {
                showToast(
                    'Pilih data yang akan diedit',
                    'error'
                );
                return;
            }
             editMode = true;
                document.getElementById('m_no_stok').value =
                    selectedData.no_stok ?? '';
                document.getElementById('m_jenis').value =
                    selectedData.jenis_darah ?? '';
                document.getElementById('m_golongan').value =
                    selectedData.golongan_darah ?? '';
                document.getElementById('m_rhesus').value =
                    selectedData.rhesus ?? '';
                document.getElementById('m_tgl_aftap').value =
                    formatDateForInput(selectedData.tgl_aftap);
                document.getElementById('m_tgl_produksi').value =
                    formatDateForInput(selectedData.tgl_produksi);
                document.getElementById('m_tgl_expired').value =
                    formatDateForInput(selectedData.tgl_expired);
                document.getElementById('m_ml').value = selectedData.ml ?? '';
                document.getElementById('m_jumlah').value = selectedData.jumlah ?? 1;
                document.getElementById('m_status').value = selectedData.status ?? '';
                document.getElementById('m_no_fpd').value = selectedData.no_fpd ?? '';
                document.getElementById('m_skrining').value = selectedData.skrining ?? '';

                bootstrap.Modal
                    .getOrCreateInstance(
                        document.getElementById(
                            'modalTambah'
                        )
                    )
                    .show();
        });
    // ─── Load Pengiriman ──────────────────────────────────────────────────────

    document.getElementById('btnLoadPengiriman').addEventListener('click', async () => {
    const no = document.getElementById('noPengiriman').value.trim();
    if (!no) { showToast('Masukkan No. Pengiriman terlebih dahulu.', 'error'); return; }

    spinner('spinnerKiri', true);
    try {
        const data = await fetchJson(BASE + '/pengiriman/' + encodeURIComponent(no));

        if (!data.data.length) {
            showToast('Data pengiriman tidak ditemukan', 'error');
            return;
        }

        const duplicated = data.data.find(item => isAlreadyScanned(item.no_stok));
        if (duplicated) {
            showToast(`No Stock ${duplicated.no_stok} sudah discan`, 'error');
            document.getElementById('noPengiriman').value = '';
            document.getElementById('noPengiriman').focus();
            return;
        }

        lastNoPengiriman = no; // ✅ simpan sebelum dikosongkan

        renderKiri(data.data);

        document.getElementById('noPengiriman').value = '';
        document.getElementById('noPengiriman').focus();

    } catch (e) {
        showToast(e.message, 'error');
    } finally {
        spinner('spinnerKiri', false);
    }
});

    document.getElementById('noPengiriman').addEventListener('keydown', e => {
        if (e.key === 'Enter') document.getElementById('btnLoadPengiriman').click();
    });
    document.getElementById('btnUpdateModal')
        .addEventListener('click', async () => {

            const payload = {
                no_penerimaan: document.getElementById('noPenerimaan').value,
                no_stok: document.getElementById('m_no_stok').value,
                no_kantong: document.getElementById('m_no_kantong').value,
                jenis_darah: document.getElementById('m_jenis').value,
                golongan_darah: document.getElementById('m_golongan').value,
                rhesus: document.getElementById('m_rhesus').value,
                tgl_aftap: document.getElementById('m_tgl_aftap').value,
                tgl_produksi: document.getElementById('m_tgl_produksi').value,
                tgl_expired: document.getElementById('m_tgl_expired').value,
                ml: document.getElementById('m_ml').value,
                gr: document.getElementById('m_gr').value,
                jumlah: document.getElementById('m_jumlah').value,
                status: document.getElementById('m_status').value,
                no_fpd: document.getElementById('m_no_fpd').value,
                skrining: document.getElementById('m_skrining').value,
                keterangan: document.getElementById('m_keterangan').value
            };

            try {

                if (editMode && selectedKiri) {

                    await fetchJson(
                        BASE + '/' + selectedKiri,
                        {
                            method: 'PUT',
                            body: JSON.stringify(payload)
                        }
                    );

                    showToast('Data berhasil diupdate');

                } else {

                    await fetchJson(
                        BASE,
                        {
                            method: 'POST',
                            body: JSON.stringify(payload)
                        }
                    );

                    showToast('Data berhasil disimpan');
                }

                bootstrap.Modal
                    .getOrCreateInstance(
                        document.getElementById('modalTambah')
                    )
                    .hide();

                editMode = false;

                // reload data
                document.getElementById(
                    'btnLoadPengiriman'
                ).click();

            } catch (e) {
                showToast(e.message, 'error');
            }
        });
    // ─── Cek Kapasitas ────────────────────────────────────────────────────────

    document.getElementById('btnCekKapasitas').addEventListener('click', async () => {
        const payload = {
            ruangan       : document.getElementById('filterRuangan').value,
            golongan_darah: document.getElementById('filterGolongan').value,
            jenis_darah         : document.getElementById('filterJenis').value,
            rhesus        : document.getElementById('filterRhesus').value,
        };
        spinner('spinnerKanan', true);
        try {
            const data = await fetchJson(BASE + '/cek-kapasitas', { method: 'POST', body: JSON.stringify(payload) });
            renderKanan(data.data);
            document.getElementById('infoIsi').value       = data.kapasitas_info.isi;
            document.getElementById('infoKapasitas').value = data.kapasitas_info.kapasitas;
            document.getElementById('infoSisaMax').value   = data.kapasitas_info.sisa_max;
        } catch (e) {
            showToast(e.message, 'error');
        } finally {
            spinner('spinnerKanan', false);
        }
    });

    document.getElementById('filterRuangan').addEventListener('change', async function () {
        try {
            const data = await fetchJson(BASE + '/kapasitas?ruangan=' + this.value);
            document.getElementById('infoKapasitas').value = data.kapasitas;
            document.getElementById('infoIsi').value       = data.isi;
            document.getElementById('infoSisaMax').value   = data.sisa_max;
        } catch {}
    });

    // ─── Modal Tambah ─────────────────────────────────────────────────────────

    // document.getElementById('btnTambahKiri').addEventListener('click', () => {
    //     document.getElementById('modalAlert').classList.add('d-none');
    //     document.getElementById('m_no_fpd').value = document.getElementById('noPengiriman').value.trim();
    //     bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTambah')).show();
    // });

  document.getElementById('btnSimpanPenerimaan').addEventListener('click', async () => {
    if (!selectedData) {
        showToast('Pilih data di tabel kiri terlebih dahulu', 'error');
        return;
    }

    const allRows = document.querySelectorAll('#bodyKiri tr[data-id]');
    if (!allRows.length) {
        showToast('Tidak ada data untuk disimpan', 'error');
        return;
    }

    const payload = {
        no_stok        : selectedData.no_stok,
        jenis_darah    : selectedData.jenis_darah,
        golongan_darah : selectedData.golongan_darah,
        rhesus         : selectedData.rhesus,
        status         : 'Tersedia',
        tgl_aftap      : selectedData.tgl_aftap,
        tgl_produksi   : selectedData.tgl_produksi,
        tgl_expired    : selectedData.tgl_expired,
        jumlah         : allRows.length,
        ruang          : document.getElementById('filterRuangan').value,
        no_fpd         : lastNoPengiriman, 
    };

    try {
        if (editMode && selectedKiri) {
            await fetchJson(BASE + '/' + selectedKiri, {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
            showToast('Data berhasil diupdate');
        } else {
            const res = await fetchJson(BASE, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            showToast('Data berhasil disimpan');

            // ✅ Update no_penerimaan di UI dengan nomor baru dari server
            if (res.no_penerimaan) {
                document.getElementById('noPenerimaan').value = res.no_penerimaan;
            }
        }

        bootstrap.Modal.getOrCreateInstance(
            document.getElementById('modalTambah')
        ).hide();

        editMode = false;
        selectedData = null;
        selectedKiri = null;
        lastNoPengiriman = '';

        document.getElementById('btnCekKapasitas').click();

    } catch (e) {
        showToast(e.message, 'error');
    }
});

    // ─── Hapus ────────────────────────────────────────────────────────────────

    document.getElementById('btnHapusKiri').addEventListener('click', async () => {
        if (!selectedKiri) { showToast('Pilih baris yang akan dihapus.', 'error'); return; }
        if (!confirm('Hapus data ini?')) return;
        try {
            await fetchJson(BASE + '/' + selectedKiri, { method: 'DELETE' });
            showToast('Data berhasil dihapus.');
            selectedKiri = null;
            document.getElementById('btnLoadPengiriman').click();
        } catch (e) {
            showToast(e.message, 'error');
        }
    });

    // ─── Cari Stock ───────────────────────────────────────────────────────────

    document.getElementById('btnCariStock')
        .addEventListener('click', async () => {

            const noStock =
                document.getElementById('noStockSearch')
                .value
                .trim();

            if (!noStock) {
                showToast('Scan / input no stock dulu', 'error');
                return;
            }

            spinner('spinnerKanan', true);

            try {

                const data = await fetchJson(
                    BASE + '/stok/' +
                    encodeURIComponent(noStock)
                );

                renderKanan(data.data);

                // AUTO SYNC FILTER
                if (data.data.length > 0) {

                    const first = data.data[0];

                    document.getElementById('filterJenis').value =
                        first.jenis_darah ?? '';

                    document.getElementById('filterGolongan').value =
                        first.golongan_darah ?? '';

                    document.getElementById('filterRhesus').value =
                        first.rhesus ?? '';
                }

            } catch (e) {
                showToast(e.message, 'error');
            } finally {
                spinner('spinnerKanan', false);
            }
        });

   document.getElementById('noStockSearch').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('btnCariStock')
                .click();
        }
    });

    // ─── Init Kapasitas ───────────────────────────────────────────────────────

    (async () => {
        try {
            const ruangan = document.getElementById('filterRuangan').value;
            const data = await fetchJson(BASE + '/kapasitas?ruangan=' + ruangan);
            document.getElementById('infoKapasitas').value = data.kapasitas;
            document.getElementById('infoIsi').value       = data.isi;
            document.getElementById('infoSisaMax').value   = data.sisa_max;
        } catch {}
    })();
</script>
@endpush

