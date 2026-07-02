@extends('layouts.index')

@section('content')
<div class="container-fluid">
    {{-- Header Card --}}
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-header border-0 py-5 bg-transparent">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="ki-duotone ki-shield-tick fs-2x me-2"></i>
                        Pengiriman Bank Darah Internal
                    </h2>
                    <span class="text-muted fs-6">
                        <i class="ki-duotone ki-map fs-7"></i>
                        Distribusi darah internal bank darah
                    </span>
                </div>
                <div class="card-toolbar">
                    <button class="btn btn-danger rounded-pill px-5 py-3 shadow-sm" onclick="openForm()">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Pengiriman Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card rounded-4 border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <input type="hidden" id="pengiriman_id">
                <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0" id="tablePengiriman">
                    <thead class="bg-light">
                        <tr class="fw-bold text-muted border-bottom">
                            <th class="ps-4">No Pengiriman</th>
                            <th>Tanggal</th>
                            <th>No Permintaan</th>
                            <th>Bank Darah</th>
                            <th>Petugas</th>
                            <th>Status</th>
                            <th class="pe-4" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Form --}}
<div class="modal fade" id="modalForm" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 px-8 pt-8 pb-0">
                <h2 class="fw-bold text-danger">
                    <i class="ki-duotone ki-paper-plane fs-1 me-2"></i>
                    Pengiriman Darah Internal
                </h2>
                <button type="button" class="btn btn-icon btn-sm rounded-circle" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"></i>
                </button>
            </div>

            <div class="modal-body px-8 pb-8">

                {{-- Baris 1: No Permintaan + Bank Darah + Tgl Permintaan --}}
                <div class="row mb-5 g-4">
                    <div class="col-md-5 position-relative">
                        <label class="form-label fw-semibold text-dark">
                            <i class="ki-duotone ki-note-2 fs-5 me-1"></i>
                            No Permintaan
                        </label>
                        <div class="input-group">
                            <input type="text" id="no_permintaan_search" class="form-control form-control-lg"
                                placeholder="Ketik / scan no permintaan..." autocomplete="off">
                            <button class="btn btn-primary px-4" onclick="cariPermintaan()">
                                <i class="ki-duotone ki-magnifier fs-3"></i> Cari
                            </button>
                        </div>
                        <div id="dropdownPermintaan" class="dropdown-menu w-100 shadow-sm"
                            style="display:none; max-height:220px; overflow-y:auto; position:absolute; z-index:9999;"></div>
                        <input type="hidden" id="permintaan_id">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Bank Darah</label>
                        <input type="text" id="bank_darah_nama" class="form-control form-control-lg" readonly placeholder="-">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark">Tgl Permintaan</label>
                        <input type="text" id="tgl_permintaan" class="form-control form-control-lg" readonly placeholder="-">
                    </div>

                    {{-- Baris 2: Petugas Minta + Petugas Kirim (search) + Tgl Pengiriman + Keterangan --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">
                            <i class="ki-duotone ki-profile-circle fs-5 me-1"></i>
                            Petugas Minta
                        </label>
                        <input type="text" id="petugas_minta" class="form-control form-control-lg" readonly placeholder="-">
                    </div>

                    {{-- Petugas Kirim: search input --}}
                    <div class="col-md-4 position-relative">
                        <label class="form-label fw-semibold text-dark">
                            <i class="ki-duotone ki-profile-circle fs-5 me-1"></i>
                            Petugas Kirim
                        </label>
                        <input type="text" id="petugas_kirim_search" class="form-control form-control-lg"
                            placeholder="Cari nama / kode petugas..." autocomplete="off">
                        <div id="dropdownPetugas" class="dropdown-menu w-100 shadow-sm"
                            style="display:none; max-height:200px; overflow-y:auto; position:absolute; z-index:9999;"></div>
                        <input type="hidden" id="petugas_kirim_id">
                        {{-- tampilkan yang terpilih --}}
                        <small id="petugasTerpilihLabel" class="text-success fw-semibold mt-1 d-block" style="min-height:18px;"></small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">
                            <i class="ki-duotone ki-calendar fs-5 me-1"></i>
                            Tgl Pengiriman
                        </label>
                        <input type="text" id="tgl_pengiriman" class="form-control form-control-lg"
                            readonly placeholder="-" value="{{ now()->format('d/m/Y H:i') }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">Keterangan</label>
                        <textarea id="keterangan" class="form-control" rows="2"
                            placeholder="Masukkan keterangan pengiriman..."></textarea>
                    </div>
                </div>

                {{-- Row: Detail Permintaan + Ringkasan Jenis Darah --}}
                <div class="row mb-5 g-4">
                    {{-- Permintaan Detail --}}
                    <div class="col-md-8">
                        <div class="card border rounded-4 overflow-hidden h-100">
                            <div class="card-header bg-light py-3">
                                <h6 class="mb-0 fw-bold">
                                    <i class="ki-duotone ki-information-2 fs-5 me-2"></i>
                                    Pilih Jenis Darah yang Diminta
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered align-middle mb-0 fs-7" id="tablePermintaanDetail">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis</th>
                                            <th>Gol</th>
                                            <th>RH</th>
                                            <th>Jumlah</th>
                                            <th>CC</th>
                                            <th>Tgl Perlu</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyPermintaanDetail">
                                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada permintaan dipilih</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Ringkasan Jenis Darah Dikirim --}}
                    <div class="col-md-4">
                        <div class="card border rounded-4 overflow-hidden h-100">
                            <div class="card-header bg-light-danger py-3">
                                <h6 class="mb-0 fw-bold text-danger">
                                    <i class="ki-duotone ki-drop fs-5 me-2"></i>
                                    Jenis Darah yang Dikirim
                                </h6>
                            </div>
                            <div class="card-body p-2">
                                <table class="table table-sm table-bordered mb-0 fs-8" id="tableRingkasan">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Jns</th>
                                            <th>Gol</th>
                                            <th>Rhesus</th>
                                            <th>Dilayani</th>
                                            <th>Diberikan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyRingkasan">
                                        <tr><td colspan="5" class="text-center text-muted py-3">-</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-light py-2 text-center">
                                <span class="fw-bold text-dark fs-6">Jumlah di Beri: </span>
                                <span class="badge bg-danger fs-5 px-4" id="totalDiberi">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scan No Stok --}}
                <div class="card border rounded-4 overflow-hidden mb-5">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ki-duotone ki-barcode fs-5 me-2"></i>
                            Scan / Input No Stok
                        </h6>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" id="inputNoStok" class="form-control form-control-sm"
                                style="width:220px" placeholder="Scan barcode no_stok..." autocomplete="off">
                            <button class="btn btn-sm btn-danger" onclick="tambahStokManual()">
                                <i class="ki-duotone ki-plus fs-4"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Table Stok Detail --}}
                <div class="card border rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-light py-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="ki-duotone ki-element-11 fs-5 me-2"></i>
                            Rincian Stok Darah
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="tableDetail">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>No Stok</th>
                                        <th>Jenis</th>
                                        <th>Gol</th>
                                        <th>Rh</th>
                                        <th>CC</th>
                                        <th>Tgl Kadaluarsa</th>
                                        <th>NAT</th>
                                        <th>FPUP/BDL</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyDetail">
                                    <tr><td colspan="11" class="text-center text-muted py-4">Belum ada stok</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="text-end mt-5">
                    <button class="btn btn-light me-2 px-5 py-2 rounded-pill" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-arrow-left fs-2 me-1"></i> Tutup
                    </button>
                    <button class="btn btn-danger px-6 py-2 rounded-pill shadow-sm" onclick="saveData()">
                        <i class="ki-duotone ki-check fs-2 me-1"></i> Proses Pengiriman
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE_URL = '/penyimpanan/pengiriman_bank_darah_internal';

let modal;
let permintaanList  = [];   // cache semua permintaan
let petugasList     = [];   // cache semua petugas
let stokTerpilih    = [];   // stok yang sudah di-scan/ditambah
let permintaanDetails = []; // detail dari permintaan terpilih
let editMode        = false;

// ─── Load Petugas ke cache (untuk search) ────────────────────────────────────
function loadPetugas() {
    $.get('/master/petugas/data', function (res) {
        let data = [];
        if (Array.isArray(res))            data = res;
        else if (Array.isArray(res.data))  data = res.data;
        else if (Array.isArray(res.data?.data)) data = res.data.data;
        petugasList = data;
    }).fail(function () {
        console.warn('Gagal memuat data petugas');
    });
}

// ─── Render dropdown petugas ─────────────────────────────────────────────────
function renderDropdownPetugas(q) {
    if (!q) { $('#dropdownPetugas').hide(); return; }
    let filtered = petugasList.filter(p => {
        let nama  = (p.nama ?? p.nama_petugas ?? '').toLowerCase();
        let kode  = (p.kode ?? p.kode_petugas ?? '').toLowerCase();
        return nama.includes(q) || kode.includes(q);
    });
    if (!filtered.length) { $('#dropdownPetugas').hide(); return; }
    let html = filtered.map(p => {
        let id   = p.id;
        let kode = p.kode ?? p.kode_petugas ?? '-';
        let nama = (p.nama ?? p.nama_petugas ?? '-').replace(/'/g, "\\'");
        return `<a class="dropdown-item py-2 px-3" href="#"
            onclick="pilihPetugas(${id},'${kode}','${nama}'); return false;">
            <strong>${kode}</strong>
            <span class="text-muted ms-2">${p.nama ?? p.nama_petugas ?? '-'}</span>
        </a>`;
    }).join('');
    $('#dropdownPetugas').html(html).show();
}

function pilihPetugas(id, kode, nama) {
    $('#petugas_kirim_id').val(id);
    $('#petugas_kirim_search').val('');
    $('#petugasTerpilihLabel').text(`✓ ${kode} – ${nama}`);
    $('#dropdownPetugas').hide();
}

$(document).ready(function () {
    modal = new bootstrap.Modal(document.getElementById('modalForm'));
    loadData();
    loadPermintaan();
    loadPetugas();

    // Scan enter otomatis tambah
    $('#inputNoStok').on('keydown', function (e) {
        if (e.key === 'Enter') tambahStokManual();
    });

    // Live search permintaan
    $('#no_permintaan_search').on('input', function () {
        let q = $(this).val().toLowerCase().trim();
        if (!q) { $('#dropdownPermintaan').hide(); return; }
        let filtered = permintaanList.filter(p =>
            p.no_permintaan.toLowerCase().includes(q) ||
            (p.bank_darah_nama || '').toLowerCase().includes(q)
        );
        if (!filtered.length) { $('#dropdownPermintaan').hide(); return; }
        let html = filtered.map(p => `
            <a class="dropdown-item py-2 px-3" href="#"
                onclick="pilihPermintaan(
                    ${p.id},
                    '${p.no_permintaan}',
                    '${(p.bank_darah_nama ?? '').replace(/'/g, "\\'")}',
                    '${p.tanggal_minta}',
                    '${(p.petugas_nama ?? '').replace(/'/g, "\\'")}'
                ); return false;">
                <strong>${p.no_permintaan}</strong>
                <small class="text-muted ms-2">${p.bank_darah_nama ?? ''}</small>
                ${p.petugas_nama ? `<small class="text-primary ms-2">${p.petugas_nama}</small>` : ''}
            </a>
        `).join('');
        $('#dropdownPermintaan').html(html).show();
    });

    // Live search petugas kirim
    $('#petugas_kirim_search').on('input', function () {
        let q = $(this).val().toLowerCase().trim();
        // Jika search dikosongkan, reset pilihan
        if (!q) {
            $('#petugas_kirim_id').val('');
            $('#petugasTerpilihLabel').text('');
            $('#dropdownPetugas').hide();
            return;
        }
        renderDropdownPetugas(q);
    });

    // Klik luar tutup dropdown
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#no_permintaan_search, #dropdownPermintaan').length) {
            $('#dropdownPermintaan').hide();
        }
        if (!$(e.target).closest('#petugas_kirim_search, #dropdownPetugas').length) {
            $('#dropdownPetugas').hide();
        }
    });
});

// ─── Load permintaan ke cache ─────────────────────────────────────────────────
function loadPermintaan() {
    $.get(`${BASE_URL}/permintaan`, function (res) {
        permintaanList = res.data ?? [];
    });
}

// ─── Validasi stok sesuai permintaan ─────────────────────────────────────────
function isStokSesuaiPermintaan(stok) {
    return permintaanDetails.some(d => {
        let detailRh = normalizeRh(d.rhesus);
        let stokRh   = normalizeRh(stok.rhesus);
        return (
            (d.jenis_darah ?? '').toLowerCase().trim()   === (stok.jenis_darah ?? '').toLowerCase().trim()
            && (d.golongan_darah ?? '').toUpperCase().trim() === (stok.golongan_darah ?? '').toUpperCase().trim()
            && detailRh === stokRh
        );
    });
}

function normalizeRh(rh) {
    let r = (rh ?? '').toLowerCase().trim();
    return ['+', 'positif', 'positive'].includes(r) ? 'positif' : 'negatif';
}

// ─── Reset & buka form ────────────────────────────────────────────────────────
function openForm() {
    editMode = false;
    resetForm();
    modal.show();
}

function resetForm() {
    stokTerpilih      = [];
    permintaanDetails = [];

    $('#pengiriman_id').val('');
    $('#no_permintaan_search').val('');
    $('#permintaan_id').val('');
    $('#bank_darah_nama').val('');
    $('#tgl_permintaan').val('');
    $('#petugas_minta').val('');
    $('#petugas_kirim_id').val('');
    $('#petugas_kirim_search').val('');
    $('#petugasTerpilihLabel').text('');
    $('#keterangan').val('');
    $('#tgl_pengiriman').val("{{ now()->format('d/m/Y H:i') }}");

    $('#tbodyPermintaanDetail').html('<tr><td colspan="7" class="text-center text-muted py-4">Belum ada permintaan dipilih</td></tr>');
    $('#tbodyRingkasan').html('<tr><td colspan="5" class="text-center text-muted py-3">-</td></tr>');
    $('#tbodyDetail').html('<tr><td colspan="11" class="text-center text-muted py-4">Belum ada stok</td></tr>');
    $('#totalDiberi').text('0');
    $('#dropdownPermintaan').hide();
    $('#dropdownPetugas').hide();
}

// ─── Cari tombol ──────────────────────────────────────────────────────────────
function cariPermintaan() {
    let q = $('#no_permintaan_search').val().trim();
    if (!q) return;
    let found = permintaanList.find(p => p.no_permintaan.toLowerCase() === q.toLowerCase());
    if (found) {
        pilihPermintaan(found.id, found.no_permintaan, found.bank_darah_nama, found.tanggal_minta, found.petugas_nama);
    } else {
        Swal.fire({ icon: 'warning', title: 'Tidak Ditemukan', text: `No permintaan "${q}" tidak ditemukan.`, confirmButtonColor: '#dc3545' });
    }
}

// ─── Pilih permintaan ─────────────────────────────────────────────────────────
function pilihPermintaan(id, no, bank, tgl, petugas) {
    $('#permintaan_id').val(id);
    $('#no_permintaan_search').val(no);
    $('#bank_darah_nama').val(bank ?? '');
    $('#tgl_permintaan').val(tgl ? moment(tgl).format('DD/MM/YYYY HH:mm') : '');
    // petugas_minta: langsung dari data permintaan (kolom petugas_nama di tabel permintaan_darah_penyimpanan)
    $('#petugas_minta').val(petugas ?? '');
    $('#dropdownPermintaan').hide();

    stokTerpilih = [];
    renderTableDetail();
    renderRingkasan();
    loadDetailPermintaan(id);
}

// ─── Load detail permintaan ───────────────────────────────────────────────────
function loadDetailPermintaan(id) {
    $('#tbodyPermintaanDetail').html('<tr><td colspan="7" class="text-center py-3"><span class="spinner-border spinner-border-sm text-danger"></span> Memuat...</td></tr>');
    $.get(`${BASE_URL}/permintaan/${id}`, function (res) {
        permintaanDetails = res.data.details ?? [];
        renderTablePermintaanDetail();

        // Auto-load stok rekomendasi
        permintaanDetails.forEach(d => {
            if (d.stok && d.stok.length > 0) {
                d.stok.forEach(s => {
                    if (!stokTerpilih.find(x => x.no_stok === s.no_stok)) {
                        stokTerpilih.push(s);
                    }
                });
            }
        });
        renderTableDetail();
        renderRingkasan();
    }).fail(function () {
        $('#tbodyPermintaanDetail').html('<tr><td colspan="7" class="text-center text-danger py-3">Gagal memuat detail</td></tr>');
    });
}

// ─── Render tabel permintaan detail ──────────────────────────────────────────
function renderTablePermintaanDetail() {
    if (!permintaanDetails.length) {
        $('#tbodyPermintaanDetail').html('<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada detail</td></tr>');
        return;
    }
    let html = '';
    permintaanDetails.forEach((d, i) => {
        html += `<tr>
            <td class="text-center">${i + 1}</td>
            <td class="fw-bold">${d.jenis_darah}</td>
            <td class="text-center">${d.golongan_darah}</td>
            <td class="text-center">${d.rhesus}</td>
            <td class="text-center"><span class="badge bg-primary">${d.jumlah_kantong}</span></td>
            <td class="text-center">${d.jumlah_cc ?? 0} ml</td>
            <td class="text-center text-muted fs-8">-</td>
        </tr>`;
    });
    $('#tbodyPermintaanDetail').html(html);
}

// ─── Render tabel stok terpilih ───────────────────────────────────────────────
function renderTableDetail() {
    if (!stokTerpilih.length) {
        $('#tbodyDetail').html('<tr><td colspan="11" class="text-center text-muted py-4">Belum ada stok</td></tr>');
        return;
    }
    let html = '';
    stokTerpilih.forEach((s, i) => {
        let expired  = moment(s.tgl_expired);
        let isExp    = expired.isBefore(moment());
        let natBadge = s.nat
            ? `<span class="badge bg-success">${s.nat}</span>`
            : `<span class="text-muted">-</span>`;
        let fpupVal  = s.fpup_bdl ?? s.fpup ?? s.bdl ?? null;
        let fpupBadge = fpupVal
            ? `<span class="badge bg-info text-dark">${fpupVal}</span>`
            : `<span class="text-muted">-</span>`;
        html += `<tr>
            <td class="text-center fw-bold">${i + 1}</td>
            <td><span class="badge badge-light-primary fs-8">${s.no_stok}</span></td>
            <td class="fw-bold">${s.jenis_darah}</td>
            <td class="text-center">${s.golongan_darah}</td>
            <td class="text-center">${s.rhesus}</td>
            <td class="text-end">${s.ml ?? 0} ml</td>
            <td class="text-center ${isExp ? 'text-danger fw-bold' : ''}">
                ${expired.format('DD/MM/YYYY')}
                ${isExp ? '<i class="ki-duotone ki-warning-2 fs-6 text-danger ms-1"></i>' : ''}
            </td>
            <td class="text-center">${natBadge}</td>
            <td class="text-center">${fpupBadge}</td>
            <td class="text-muted fs-8">${s.keterangan ?? s.no_kantong ?? '-'}</td>
            <td class="text-center">
                <button class="btn btn-icon btn-sm btn-light-danger btn-hover-danger rounded-circle"
                    onclick="hapusStok('${s.no_stok}')" title="Hapus">
                    <i class="ki-duotone ki-trash fs-4"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </td>
        </tr>`;
    });
    $('#tbodyDetail').html(html);
}

// ─── Render ringkasan ─────────────────────────────────────────────────────────
function renderRingkasan() {
    let groups = {};
    stokTerpilih.forEach(s => {
        let key = `${s.jenis_darah}|${s.golongan_darah}|${normalizeRh(s.rhesus)}`;
        groups[key] = (groups[key] ?? 0) + 1;
    });

    if (!permintaanDetails.length) {
        $('#tbodyRingkasan').html('<tr><td colspan="5" class="text-center text-muted py-3">-</td></tr>');
        $('#totalDiberi').text(stokTerpilih.length);
        return;
    }

    let html = '';
    permintaanDetails.forEach(d => {
        let key      = `${d.jenis_darah}|${d.golongan_darah}|${normalizeRh(d.rhesus)}`;
        let diberikan = groups[key] ?? 0;
        let dilayani  = d.jumlah_kantong;
        let ok        = diberikan >= dilayani;
        html += `<tr class="${ok ? '' : 'table-warning'}">
            <td class="fw-bold">${d.jenis_darah}</td>
            <td class="text-center">${d.golongan_darah}</td>
            <td class="text-center">${d.rhesus}</td>
            <td class="text-center"><span class="badge bg-secondary">${dilayani}</span></td>
            <td class="text-center"><span class="badge ${ok ? 'bg-success' : 'bg-danger'}">${diberikan}</span></td>
        </tr>`;
    });
    $('#tbodyRingkasan').html(html);
    $('#totalDiberi').text(stokTerpilih.length);
}

// ─── Hapus stok ───────────────────────────────────────────────────────────────
function hapusStok(noStok) {
    stokTerpilih = stokTerpilih.filter(s => s.no_stok !== noStok);
    renderTableDetail();
    renderRingkasan();
}

// ─── Tambah stok manual / scan ────────────────────────────────────────────────
function tambahStokManual() {
    let noStok = $('#inputNoStok').val().trim();
    if (!noStok) return;

    if (!$('#permintaan_id').val()) {
        Swal.fire({ icon: 'warning', title: 'Pilih Permintaan', text: 'Silakan pilih no permintaan terlebih dahulu.', confirmButtonColor: '#dc3545' });
        $('#inputNoStok').val('').focus();
        return;
    }

    if (stokTerpilih.find(s => s.no_stok === noStok)) {
        Swal.fire({ icon: 'warning', title: 'Duplikat', text: `No Stok ${noStok} sudah ada di daftar.`, confirmButtonColor: '#dc3545' });
        $('#inputNoStok').val('').focus();
        return;
    }

    const validateStok = (stok) => {
        if (!isStokSesuaiPermintaan(stok)) {
            let detailText = permintaanDetails.map(d =>
                `${d.jenis_darah} ${d.golongan_darah} ${d.rhesus}`
            ).join(', ');
            Swal.fire({
                icon: 'error',
                title: 'Jenis Darah Tidak Sesuai',
                html: `<div class="text-start">
                    <b>Stok Scan:</b><br>${stok.jenis_darah} ${stok.golongan_darah} ${stok.rhesus}
                    <hr><b>Permintaan:</b><br>${detailText}
                </div>`,
                confirmButtonColor: '#dc3545'
            });
            $('#inputNoStok').val('').focus();
            return false;
        }
        return true;
    };

    // Cari di stok rekomendasi permintaan dulu
    let found = null;
    permintaanDetails.forEach(d => {
        if (!found && d.stok) {
            found = d.stok.find(s => s.no_stok === noStok);
        }
    });

    if (found) {
        if (!validateStok(found)) return;
        stokTerpilih.push(found);
        renderTableDetail();
        renderRingkasan();
        $('#inputNoStok').val('').focus();
        return;
    }

    // Cari via AJAX
    $.get(`${BASE_URL}/cari_stok`, { no_stok: noStok }, function (res) {
        if (!res.data) {
            Swal.fire({ icon: 'error', title: 'Tidak Ditemukan', text: `No Stok ${noStok} tidak ditemukan atau tidak tersedia.`, confirmButtonColor: '#dc3545' });
            $('#inputNoStok').val('').focus();
            return;
        }
        const stok = res.data;
        if (!validateStok(stok)) return;
        stokTerpilih.push(stok);
        renderTableDetail();
        renderRingkasan();
        $('#inputNoStok').val('').focus();
    }).fail(function () {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mencari no stok.', confirmButtonColor: '#dc3545' });
    });
}

// ─── Edit data ────────────────────────────────────────────────────────────────
function editData(id) {
    editMode = true;
    resetForm();

    $.get(`${BASE_URL}/${id}`, function (res) {
        const data = res.data;

        $('#pengiriman_id').val(data.id);

        // isi field header
        $('#permintaan_id').val(data.permintaan_id);
        $('#no_permintaan_search').val(data.no_permintaan);
        $('#bank_darah_nama').val(data.bank_darah_nama ?? '');
        $('#tgl_permintaan').val(data.tanggal_minta ? moment(data.tanggal_minta).format('DD/MM/YYYY HH:mm') : '');
        // petugas minta: dari permintaan (sudah dikembalikan service->findById)
        $('#petugas_minta').val(data.petugas_minta ?? '');
        $('#tgl_pengiriman').val(data.tanggal_pengiriman ? moment(data.tanggal_pengiriman).format('DD/MM/YYYY HH:mm') : '');
        $('#keterangan').val(data.keterangan ?? '');

        // petugas kirim: set hidden id & tampilkan label
        if (data.petugas_id) {
            $('#petugas_kirim_id').val(data.petugas_id);
            // cari di cache
            let p = petugasList.find(x => x.id == data.petugas_id);
            if (p) {
                let kode = p.kode ?? p.kode_petugas ?? '-';
                let nama = p.nama ?? p.nama_petugas ?? '-';
                $('#petugasTerpilihLabel').text(`✓ ${kode} – ${nama}`);
            } else {
                // fallback: tampilkan nama dari data
                $('#petugasTerpilihLabel').text(`✓ ${data.petugas_nama ?? ''}`);
            }
        }

        // load detail permintaan terlebih dahulu, lalu isi stok
        $.get(`${BASE_URL}/permintaan/${data.permintaan_id}`, function (res2) {
            permintaanDetails = res2.data.details ?? [];
            renderTablePermintaanDetail();

            // stok dari data edit (bukan rekomendasi otomatis)
            stokTerpilih = data.details ?? [];
            renderTableDetail();
            renderRingkasan();
        }).fail(function () {
            // Jika gagal load detail permintaan, tetap isi stok
            stokTerpilih = data.details ?? [];
            renderTableDetail();
            renderRingkasan();
        });

        modal.show();
    }).fail(function () {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data pengiriman gagal dimuat' });
    });
}

// ─── Load data tabel utama ────────────────────────────────────────────────────
function loadData() {
    $.get(`${BASE_URL}/data`, function (res) {
        let html = '';
        (res.data ?? []).forEach(item => {
            html += `<tr>
                <td class="ps-4 fw-bold">${item.no_pengiriman}</td>
                <td>${moment(item.tanggal_pengiriman).format('DD/MM/YYYY HH:mm')}</td>
                <td><span class="badge badge-light-primary">${item.no_permintaan}</span></td>
                <td><i class="ki-duotone ki-building fs-5 me-1"></i> ${item.bank_darah_nama}</td>
                <td><i class="ki-duotone ki-profile-circle fs-5 me-1"></i> ${item.petugas_nama}</td>
                <td><span class="badge badge-success py-2 px-3 rounded-pill">${item.status}</span></td>
                <td class="pe-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light-primary rounded-pill px-3" onclick="editData(${item.id})">
                            <i class="ki-duotone ki-pencil fs-5"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-light-danger rounded-pill px-3" onclick="deleteData(${item.id})">
                            <i class="ki-duotone ki-trash fs-5"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>`;
        });
        $('#tablePengiriman tbody').html(html || '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data</td></tr>');
    });
}

// ─── Save / proses pengiriman ─────────────────────────────────────────────────
function saveData() {
    let permintaanId = $('#permintaan_id').val();
    if (!permintaanId) {
        Swal.fire({ icon: 'warning', title: 'Pilih Permintaan', text: 'Silakan cari dan pilih no permintaan terlebih dahulu.', confirmButtonColor: '#dc3545' });
        return;
    }
    if (!stokTerpilih.length) {
        Swal.fire({ icon: 'warning', title: 'Stok Kosong', text: 'Belum ada stok yang dipilih.', confirmButtonColor: '#dc3545' });
        return;
    }

    Swal.fire({
        title: 'Proses Pengiriman?',
        text: `Total ${stokTerpilih.length} kantong akan dikirim.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then(result => {
        if (!result.isConfirmed) return;

        const pengirimanId = $('#pengiriman_id').val();
        const payload = {
            _token:        "{{ csrf_token() }}",
            permintaan_id: permintaanId,
            petugas_id:    $('#petugas_kirim_id').val(),
            keterangan:    $('#keterangan').val(),
            stok_ids:      stokTerpilih.map(s => s.id)
        };

        $.ajax({
            url:  editMode ? `${BASE_URL}/${pengirimanId}` : BASE_URL,
            type: editMode ? 'PUT' : 'POST',
            data: payload,
            success: function (res) {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
                modal.hide();
                loadData();
                loadPermintaan(); // refresh cache permintaan
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.', confirmButtonColor: '#dc3545' });
            }
        });
    });
}

// ─── Delete ────────────────────────────────────────────────────────────────────
function deleteData(id) {
    Swal.fire({
        title: 'Hapus Data?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url:  `${BASE_URL}/${id}`,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function (res) {
                    Swal.fire({ icon: 'success', title: 'Terhapus!', text: res.message, confirmButtonColor: '#dc3545' });
                    loadData();
                    loadPermintaan();
                },
                error: function (xhr) {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message ?? 'Terjadi kesalahan.', confirmButtonColor: '#dc3545' });
                }
            });
        }
    });
}
</script>
@endpush