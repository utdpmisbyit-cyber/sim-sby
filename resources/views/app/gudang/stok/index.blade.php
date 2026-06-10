@extends('layouts.index')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap');

body, .card, .form-control, .btn, th, td { font-family: 'IBM Plex Sans', sans-serif; }

.page-wrapper { background: #f0f4f8; min-height: 100vh; padding: 24px; }

/* ── HEADER ── */
.header-card {
    background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 100%);
    border-radius: 16px; padding: 20px 28px; margin-bottom: 20px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 4px 20px rgba(43,108,176,.3);
}
.header-card h4 { color:#fff; font-weight:700; font-size:1.25rem; margin:0; }
.badge-no-terima {
    background:rgba(255,255,255,.15); color:#fff; border-radius:8px;
    padding:6px 14px; font-family:'IBM Plex Mono',monospace;
    font-size:.85rem; font-weight:600; letter-spacing:1px;
}

/* ── SUMMARY CARDS ── */
.summary-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:20px; }
.summary-card {
    background:#fff; border-radius:14px; padding:16px 18px;
    box-shadow:0 2px 10px rgba(0,0,0,.06); text-align:center;
    border-top:4px solid;
}
.summary-card.masuk    { border-color:#3182ce; }
.summary-card.tersedia { border-color:#38a169; }
.summary-card.keluar   { border-color:#d69e2e; }
.summary-card.kembali  { border-color:#805ad5; }
.summary-card.rusak    { border-color:#e53e3e; }
.summary-card .s-num {
    font-family:'IBM Plex Mono',monospace; font-size:2rem; font-weight:700; line-height:1;
}
.summary-card.masuk    .s-num { color:#3182ce; }
.summary-card.tersedia .s-num { color:#38a169; }
.summary-card.keluar   .s-num { color:#d69e2e; }
.summary-card.kembali  .s-num { color:#805ad5; }
.summary-card.rusak    .s-num { color:#e53e3e; }
.summary-card .s-label { font-size:.72rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#718096; margin-top:4px; }

/* ── FORM CARD ── */
.form-card {
    background:#fff; border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,.07); padding:24px 28px; margin-bottom:20px;
}
.form-label { font-weight:600; font-size:.72rem; letter-spacing:1px; text-transform:uppercase; color:#4a5568; margin-bottom:6px; }
.form-control {
    border:1.5px solid #e2e8f0; border-radius:10px; padding:10px 14px; font-size:.92rem;
    transition:border-color .2s,box-shadow .2s;
}
.form-control:focus { border-color:#2b6cb0; box-shadow:0 0 0 3px rgba(43,108,176,.12); outline:none; }
.form-control[readonly] { background:#f7fafc; color:#718096; }

/* ── SCAN INPUT ── */
.scan-input {
    font-family:'IBM Plex Mono',monospace; font-size:1.2rem; font-weight:700;
    letter-spacing:3px; border:2px solid #2b6cb0; background:#ebf8ff;
    color:#1a365d; text-align:center;
}
.scan-input:focus { border-color:#1a365d; box-shadow:0 0 0 4px rgba(43,108,176,.18); background:#e6fffa; }
.scan-input::placeholder { font-size:.8rem; letter-spacing:1px; color:#90cdf4; font-weight:400; }

/* ── COUNTER ── */
.counter-box {
    display:flex; align-items:center; gap:16px;
    background:#ebf8ff; border:1.5px solid #bee3f8; border-radius:12px; padding:12px 20px;
}
.counter-number { font-family:'IBM Plex Mono',monospace; font-size:2.4rem; font-weight:700; color:#2b6cb0; line-height:1; }
.counter-label  { font-size:.75rem; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:#4a5568; }
.counter-sub    { font-size:.7rem; color:#718096; margin-top:2px; }

/* ── BUTTONS ── */
.btn-save-main {
    background:linear-gradient(135deg,#2f855a,#38a169); border:none; color:#fff;
    font-weight:700; font-size:.92rem; padding:12px 20px; border-radius:10px;
    box-shadow:0 3px 10px rgba(56,161,105,.35); transition:transform .15s,box-shadow .15s;
}
.btn-save-main:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 6px 16px rgba(56,161,105,.4); color:#fff; }
.btn-save-main:disabled { opacity:.6; cursor:not-allowed; }
.btn-reset {
    background:#fff; border:1.5px solid #e2e8f0; color:#e53e3e;
    font-weight:600; font-size:.88rem; padding:12px 20px; border-radius:10px; transition:all .15s;
}
.btn-reset:hover { background:#fff5f5; border-color:#e53e3e; color:#e53e3e; }
.btn-kembali {
    background:linear-gradient(135deg,#553c9a,#805ad5); border:none; color:#fff;
    font-weight:700; font-size:.88rem; padding:10px 18px; border-radius:10px;
    box-shadow:0 3px 10px rgba(128,90,213,.3); transition:all .15s;
}
.btn-kembali:hover { transform:translateY(-1px); color:#fff; }

/* ── TOAST ── */
#toast-container { position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px; }
.toast-msg {
    background:#fff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,.15);
    padding:14px 18px; font-size:.88rem; font-weight:600;
    display:flex; align-items:center; gap:10px;
    animation:slideIn .3s ease; min-width:260px; border-left:4px solid;
}
.toast-msg.success { border-color:#38a169; color:#276749; }
.toast-msg.error   { border-color:#e53e3e; color:#c53030; }
.toast-msg.warning { border-color:#d69e2e; color:#975a16; }
@keyframes slideIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }

/* ── TABS ── */
.tab-wrapper { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
.nav-tabs-custom {
    background:#f7fafc; border-bottom:2px solid #e2e8f0; padding:0 24px; display:flex;
}
.nav-tabs-custom .nav-link {
    border:none; border-bottom:3px solid transparent; border-radius:0;
    padding:14px 22px; font-weight:700; font-size:.82rem; letter-spacing:.8px;
    text-transform:uppercase; color:#718096; transition:all .2s; margin-bottom:-2px;
}
.nav-tabs-custom .nav-link.active { color:#2b6cb0; border-bottom-color:#2b6cb0; background:transparent; }
.nav-tabs-custom .nav-link:hover:not(.active) { color:#4a5568; background:transparent; }
.tab-content-custom { padding:20px 24px; }

/* ── TABLE ── */
.table-custom { width:100%; border-collapse:separate; border-spacing:0; font-size:.87rem; }
.table-custom thead th {
    background:#f7fafc; color:#4a5568; font-weight:700;
    font-size:.72rem; letter-spacing:1px; text-transform:uppercase;
    padding:12px 14px; border-bottom:2px solid #e2e8f0;
}
.table-custom tbody td { padding:11px 14px; border-bottom:1px solid #f0f4f8; color:#2d3748; vertical-align:middle; }
.table-custom tbody tr:hover td { background:#ebf8ff; }
.table-custom .mono { font-family:'IBM Plex Mono',monospace; font-weight:600; font-size:.85rem; color:#1a365d; }
.table-custom .badge-type { display:inline-block; background:#ebf8ff; color:#2b6cb0; border-radius:6px; padding:2px 10px; font-size:.75rem; font-weight:600; }
.badge-status { display:inline-block; border-radius:6px; padding:2px 10px; font-size:.72rem; font-weight:700; letter-spacing:.5px; }
.badge-status.tersedia { background:#c6f6d5; color:#276749; }
.badge-status.keluar   { background:#fefcbf; color:#975a16; }
.badge-status.rusak    { background:#fed7d7; color:#c53030; }
.badge-status.baik     { background:#c6f6d5; color:#276749; }

/* ── EMPTY ── */
.empty-state { text-align:center; padding:48px 20px; color:#a0aec0; }
.empty-state i { font-size:2.5rem; margin-bottom:12px; display:block; }
.empty-state p { font-size:.88rem; margin:0; }

/* ── DIVIDER ── */
.section-divider { display:flex; align-items:center; gap:12px; margin-bottom:18px; }
.section-divider span { font-weight:700; font-size:.78rem; letter-spacing:1px; text-transform:uppercase; color:#4a5568; white-space:nowrap; }
.section-divider hr { flex:1; border-color:#e2e8f0; margin:0; }

/* ── MODAL KEMBALI ── */
.modal-kembali .modal-content { border-radius:16px; border:none; box-shadow:0 8px 40px rgba(0,0,0,.2); }
.modal-kembali .modal-header { background:linear-gradient(135deg,#553c9a,#805ad5); border-radius:16px 16px 0 0; padding:18px 24px; }
.modal-kembali .modal-title { color:#fff; font-weight:700; }
.modal-kembali .btn-close { filter:brightness(0) invert(1); }
.scan-kembali-input {
    font-family:'IBM Plex Mono',monospace; font-size:1.1rem; font-weight:700;
    letter-spacing:2px; border:2px solid #805ad5; background:#faf5ff;
    color:#553c9a; text-align:center;
}
.scan-kembali-input:focus { border-color:#553c9a; box-shadow:0 0 0 4px rgba(128,90,213,.18); background:#f3e8ff; }
</style>
@endpush

@section('content')
<div id="toast-container"></div>

<div class="page-wrapper">

    {{-- HEADER --}}
    <div class="header-card">
        <h4>📦 Pendataan Stok Kantong</h4>
        <div class="d-flex align-items-center gap-3">
            <span class="badge-no-terima" id="display_no_terima">No: {{ date('y').date('m').'000001' }}</span>
            <span style="color:rgba(255,255,255,.7); font-size:.85rem;">{{ date('d M Y') }}</span>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-grid" id="summaryGrid">
        <div class="summary-card masuk">
            <div class="s-num" id="sum-masuk">-</div>
            <div class="s-label">Total Masuk</div>
        </div>
        <div class="summary-card tersedia">
            <div class="s-num" id="sum-tersedia">-</div>
            <div class="s-label">Tersedia</div>
        </div>
        <div class="summary-card keluar">
            <div class="s-num" id="sum-keluar">-</div>
            <div class="s-label">Keluar</div>
        </div>
        <div class="summary-card kembali">
            <div class="s-num" id="sum-kembali">-</div>
            <div class="s-label">Dikembalikan</div>
        </div>
        <div class="summary-card rusak">
            <div class="s-num" id="sum-rusak">-</div>
            <div class="s-label">Rusak</div>
        </div>
    </div>

    {{-- FORM SCAN MASUK --}}
    <div class="form-card">
        <div class="section-divider"><span>📋 Informasi Penerimaan</span><hr></div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">No Terima</label>
                <input type="text" id="no_terima" class="form-control" value="{{ date('y').date('m').'000001' }}" readonly>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tgl Terima</label>
                <input type="date" id="tgl_terima" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">No Kantong (Scan)</label>
                <input type="text" id="scan_no_kantong" class="form-control scan-input"
                       placeholder="🔍  Scan / ketik No Kantong..." autofocus autocomplete="off">
            </div>
        </div>

        <div class="section-divider"><span>🏷 Detail Kantong</span><hr></div>
        <div class="row g-3 mb-4">
            <div class="col-md-3"><label class="form-label">No Lot</label><input type="text" id="no_lot" class="form-control" readonly></div>
            <div class="col-md-3"><label class="form-label">Merk</label><input type="text" id="merk" class="form-control" readonly></div>
            <div class="col-md-3"><label class="form-label">Jenis</label><input type="text" id="jenis" class="form-control" readonly></div>
            <div class="col-md-3"><label class="form-label">Type</label><input type="text" id="tipe" class="form-control" readonly></div>
            <div class="col-md-3"><label class="form-label">Ukuran</label><input type="text" id="ukuran" class="form-control" readonly></div>
        </div>

        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <div class="counter-box">
                    <div><div class="counter-number" id="counter-scan">0</div></div>
                    <div>
                        <div class="counter-label">Kantong Terscan</div>
                        <div class="counter-sub">Session ini — belum disimpan</div>
                    </div>
                </div>
            </div>
            <div class="col-md-7 d-flex gap-2 justify-content-end flex-wrap">
                <button class="btn btn-kembali" data-bs-toggle="modal" data-bs-target="#modalKembali">
                    <i class="fa fa-undo"></i> Pengembalian Kantong
                </button>
                <button class="btn btn-reset" id="btnReset"><i class="fa fa-trash-o"></i> Reset Session</button>
                <button class="btn btn-save-main" id="btnSave" disabled>
                    <i class="fa fa-cloud-upload"></i> Simpan ke Database
                </button>
            </div>
        </div>
    </div>

    {{-- SESSION TABLE --}}
    <div class="form-card" id="sessionTableCard" style="display:none;">
        <div class="section-divider">
            <span>📝 Antrian Scan (Belum Disimpan)</span><hr>
            <span id="queue-count" style="white-space:nowrap; color:#e53e3e;"></span>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-custom" id="tabelKantong">
                <thead><tr>
                    <th>#</th><th>No Kantong</th><th>Merk</th><th>Jenis</th>
                    <th>Type</th><th>Ukuran</th><th>No Lot</th><th></th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    {{-- STOK TABS --}}
    <div class="tab-wrapper">
        <ul class="nav-tabs-custom nav">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-masuk">📥 Stok Masuk</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-keluar">📤 Stok Keluar</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-kembali">🔄 Pengembalian</a></li>
        </ul>
        <div class="tab-content tab-content-custom">
            <div class="tab-pane fade show active" id="tab-masuk">
                <div id="stok-masuk-wrapper">
                    <div class="empty-state"><i class="fa fa-inbox"></i><p>Belum ada data stok masuk</p></div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-keluar">
                <div id="stok-keluar-wrapper">
                    <div class="empty-state"><i class="fa fa-sign-out"></i><p>Belum ada data stok keluar</p></div>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-kembali">
                <div id="stok-kembali-wrapper">
                    <div class="empty-state"><i class="fa fa-undo"></i><p>Belum ada data pengembalian</p></div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /page-wrapper --}}

{{-- ════════════════════════════════════════════════════════
     MODAL PENGEMBALIAN KANTONG
     ════════════════════════════════════════════════════════ --}}
<div class="modal fade modal-kembali" id="modalKembali" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">🔄 Pengembalian Kantong</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                {{-- Info Pengembalian --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">No Kembali</label>
                        <input type="text" id="no_kembali" class="form-control"
                               value="KB{{ date('ym') }}000001" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tgl Kembali</label>
                        <input type="date" id="tgl_kembali" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Keterangan</label>
                        <input type="text" id="keterangan_kembali" class="form-control" placeholder="Opsional...">
                    </div>
                </div>

                {{-- Scan --}}
                <div class="mb-3">
                    <label class="form-label">Scan No Kantong (yang dikembalikan)</label>
                    <input type="text" id="scan_kembali" class="form-control scan-kembali-input"
                           placeholder="🔍  Scan No Kantong yang kembali..." autocomplete="off">
                </div>

                {{-- Kondisi preview --}}
                <div class="row g-3 mb-3" id="kembali-preview" style="display:none!important;">
                    <div class="col-md-4">
                        <label class="form-label">Merk</label>
                        <input type="text" id="kb_merk" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis / Type</label>
                        <input type="text" id="kb_jenis" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kondisi Kembali</label>
                        <select id="kb_kondisi" class="form-control">
                            <option value="baik">✅ Baik</option>
                            <option value="rusak">❌ Rusak</option>
                        </select>
                    </div>
                </div>

                {{-- Antrian kembali --}}
                <div id="kembali-queue-card" style="display:none;">
                    <div class="section-divider">
                        <span>📝 Antrian Pengembalian</span><hr>
                        <span id="kembali-count" style="white-space:nowrap; color:#805ad5;"></span>
                    </div>
                    <div style="overflow-x:auto; max-height:260px; overflow-y:auto;">
                        <table class="table-custom" id="tabelKembali">
                            <thead><tr>
                                <th>#</th><th>No Kantong</th><th>Merk</th><th>Jenis</th><th>Kondisi</th><th></th>
                            </tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-reset" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-kembali" id="btnSaveKembali" disabled>
                    <i class="fa fa-cloud-upload"></i> Simpan Pengembalian
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ================================================================
   STOK KANTONG — JS
   ================================================================ */

// ── State ─────────────────────────────────────────────────────────
let sessionData      = [];
let scannedKodes     = new Set();
let kembaliData      = [];
let scannedKembali   = new Set();

// ── Toast ─────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const icons = { success:'✅', error:'❌', warning:'⚠️' };
    const el = $(`<div class="toast-msg ${type}">${icons[type]} ${msg}</div>`);
    $('#toast-container').append(el);
    setTimeout(() => el.fadeOut(300, () => el.remove()), 3500);
}

// ═══════════════════════════════════════════════════════
//  SUMMARY
// ═══════════════════════════════════════════════════════
function loadSummary() {
    $.get("{{ route('gudang.stok_kantong.summary') }}", res => {
        if (res.status !== 'ok') return;
        const d = res.data;
        $('#sum-masuk').text(d.masuk ?? 0);
        $('#sum-tersedia').text(d.tersedia ?? 0);
        $('#sum-keluar').text(d.keluar ?? 0);
        $('#sum-kembali').text(d.kembali ?? 0);
        $('#sum-rusak').text(d.rusak ?? 0);
    });
}

// ═══════════════════════════════════════════════════════
//  SCAN MASUK
// ═══════════════════════════════════════════════════════
function updateCounter() {
    const n = sessionData.length;
    $('#counter-scan').text(n);
    $('#btnSave').prop('disabled', n === 0);
    $('#queue-count').text(n > 0 ? `${n} item` : '');
    n > 0 ? $('#sessionTableCard').show() : $('#sessionTableCard').hide();
}

function renderSessionTable() {
    const tbody = $('#tabelKantong tbody').empty();
    sessionData.forEach((d, i) => {
        tbody.append(`
            <tr>
                <td style="color:#a0aec0;font-size:.8rem;">${i+1}</td>
                <td class="mono">${d.kode}</td>
                <td>${d.merk||'-'}</td>
                <td>${d.jenis||'-'}</td>
                <td><span class="badge-type">${d.tipe||'-'}</span></td>
                <td>${d.ukuran||'-'}</td>
                <td class="mono" style="color:#718096;">${d.no_lot||'-'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger py-0 px-2 btn-hapus" data-idx="${i}">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    updateCounter();
}

$(document).on('click', '.btn-hapus', function () {
    const idx = $(this).data('idx');
    scannedKodes.delete(sessionData[idx].kode);
    sessionData.splice(idx, 1);
    renderSessionTable();
});

$('#scan_no_kantong').on('change', function () {
    const kode = $.trim($(this).val());
    if (!kode) return;
    if (scannedKodes.has(kode)) {
        showToast(`No Kantong <b>${kode}</b> sudah terscan!`, 'warning');
        $(this).val('').focus(); return;
    }
    $.ajax({
        url: "{{ route('gudang.stok_kantong.find') }}",
        data: { kode },
        success(res) {
            if (res.status === 'found') {
                const d = res.data;
                $('#merk').val(d.merk); $('#jenis').val(d.jenis);
                $('#tipe').val(d.tipe); $('#ukuran').val(d.ukuran); $('#no_lot').val(d.no_lot);
                const item = { kode, merk:d.merk, jenis:d.jenis, tipe:d.tipe, ukuran:d.ukuran, no_lot:d.no_lot };
                sessionData.push(item); scannedKodes.add(kode);
                renderSessionTable();
                showToast(`${kode} ditambahkan (${sessionData.length} item)`, 'success');
            } else {
                showToast(`No Kantong tidak ditemukan: ${kode}`, 'error');
            }
            $('#scan_no_kantong').val('').focus();
            $('#merk,#jenis,#tipe,#ukuran,#no_lot').val('');
        },
        error() { showToast('Gagal terhubung ke server', 'error'); $('#scan_no_kantong').val('').focus(); }
    });
});

$('#btnReset').on('click', function () {
    if (!sessionData.length) return;
    if (!confirm(`Reset ${sessionData.length} item yang belum disimpan?`)) return;
    sessionData = []; scannedKodes.clear();
    renderSessionTable(); showToast('Session di-reset', 'warning');
});

$('#btnSave').on('click', function () {
    if (!sessionData.length) return;
    const payload = {
        no_terima : $('#no_terima').val(),
        tgl_terima: $('#tgl_terima').val(),
        items     : sessionData,
        _token    : "{{ csrf_token() }}",
    };
    $('#btnSave').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
    $.ajax({
        url: "{{ route('gudang.stok_kantong.save') }}", method:'POST',
        data: JSON.stringify(payload), contentType:'application/json',
        success(res) {
            if (res.status === 'ok') {
                showToast(`${sessionData.length} kantong berhasil disimpan!`, 'success');
                sessionData = []; scannedKodes.clear();
                renderSessionTable(); loadStokMasuk(); loadSummary();
            } else { showToast(res.message ?? 'Gagal menyimpan', 'error'); }
        },
        error() { showToast('Gagal menyimpan ke server', 'error'); },
        complete() {
            $('#btnSave').prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Simpan ke Database');
            updateCounter();
        }
    });
});

// ═══════════════════════════════════════════════════════
//  PENGEMBALIAN KANTONG
// ═══════════════════════════════════════════════════════
function updateKembaliCounter() {
    const n = kembaliData.length;
    $('#btnSaveKembali').prop('disabled', n === 0);
    $('#kembali-count').text(n > 0 ? `${n} item` : '');
    n > 0 ? $('#kembali-queue-card').show() : $('#kembali-queue-card').hide();
}

function renderKembaliTable() {
    const tbody = $('#tabelKembali tbody').empty();
    kembaliData.forEach((d, i) => {
        const badgeKondisi = d.kondisi === 'rusak'
            ? '<span class="badge-status rusak">RUSAK</span>'
            : '<span class="badge-status baik">BAIK</span>';
        tbody.append(`
            <tr>
                <td style="color:#a0aec0;font-size:.8rem;">${i+1}</td>
                <td class="mono">${d.no_kantong}</td>
                <td>${d.merk||'-'}</td>
                <td>${d.jenis||'-'}</td>
                <td>${badgeKondisi}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger py-0 px-2 btn-hapus-kb" data-idx="${i}">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `);
    });
    updateKembaliCounter();
}

$(document).on('click', '.btn-hapus-kb', function () {
    const idx = $(this).data('idx');
    scannedKembali.delete(kembaliData[idx].no_kantong);
    kembaliData.splice(idx, 1);
    renderKembaliTable();
});

// Scan pengembalian
$('#scan_kembali').on('change', function () {
    const kode = $.trim($(this).val());
    if (!kode) return;
    if (scannedKembali.has(kode)) {
        showToast(`${kode} sudah ada di antrian kembali!`, 'warning');
        $(this).val('').focus(); return;
    }
    $.ajax({
        url: "{{ route('gudang.stok_kantong.find_keluar') }}",
        data: { kode },
        success(res) {
            if (res.status === 'found') {
                const d = res.data;
                $('#kb_merk').val(d.merk);
                $('#kb_jenis').val((d.jenis||'') + (d.tipe ? ' / '+d.tipe : ''));
                $('#kembali-preview').css('display','flex');

                // Langsung tambah dengan kondisi default 'baik', user bisa ganti sebelum simpan
                // Atau kita tunggu user pilih kondisi lalu enter — implementasi sederhana: langsung tambah
                const kondisi = $('#kb_kondisi').val() || 'baik';
                const item = { no_kantong: kode, merk: d.merk, jenis: d.jenis, tipe: d.tipe, kondisi };
                kembaliData.push(item); scannedKembali.add(kode);
                renderKembaliTable();
                showToast(`${kode} ditambahkan ke antrian kembali`, 'success');
            } else if (res.status === 'invalid') {
                showToast(res.message, 'warning');
            } else {
                showToast(`Kantong tidak ditemukan atau bukan status keluar: ${kode}`, 'error');
            }
            $('#scan_kembali').val('').focus();
            $('#kb_merk,#kb_jenis').val('');
        },
        error() { showToast('Gagal terhubung ke server', 'error'); $('#scan_kembali').val('').focus(); }
    });
});

// Reset saat modal dibuka
$('#modalKembali').on('show.bs.modal', function () {
    kembaliData = []; scannedKembali.clear();
    renderKembaliTable();
    $('#scan_kembali').val(''); $('#kb_merk,#kb_jenis').val('');
    $('#kembali-preview').css('display','none');
    setTimeout(() => $('#scan_kembali').focus(), 400);
});

// Simpan pengembalian
$('#btnSaveKembali').on('click', function () {
    if (!kembaliData.length) return;

    // Update kondisi pada semua item dengan nilai kondisi saat ini (user bisa set kondisi sebelum scan per item)
    // Untuk bulk kondisi berbeda, kondisi sudah di-set per item saat scan
    const payload = {
        no_kembali  : $('#no_kembali').val(),
        tgl_kembali : $('#tgl_kembali').val(),
        keterangan  : $('#keterangan_kembali').val() || null,
        items       : kembaliData.map(d => ({ no_kantong: d.no_kantong, kondisi: d.kondisi })),
        _token      : "{{ csrf_token() }}",
    };

    $('#btnSaveKembali').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

    $.ajax({
        url: "{{ route('gudang.stok_kantong.save_kembali') }}", method:'POST',
        data: JSON.stringify(payload), contentType:'application/json',
        success(res) {
            if (res.status === 'ok') {
                let msg = `${res.berhasil} kantong berhasil dikembalikan`;
                if (res.gagal && res.gagal.length) {
                    msg += ` | ${res.gagal.length} gagal`;
                }
                showToast(msg, res.gagal?.length ? 'warning' : 'success');
                kembaliData = []; scannedKembali.clear();
                renderKembaliTable();
                // Refresh summary & tab kembali
                loadSummary(); loadStokKembali(); loadStokMasuk();
                $('#modalKembali').modal('hide');
            } else {
                showToast(res.message ?? 'Gagal menyimpan', 'error');
            }
        },
        error() { showToast('Gagal menyimpan ke server', 'error'); },
        complete() {
            $('#btnSaveKembali').prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Simpan Pengembalian');
        }
    });
});

// ═══════════════════════════════════════════════════════
//  LOAD TABS
// ═══════════════════════════════════════════════════════
function renderTable(wrapperId, rows, columns) {
    const wrapper = $(wrapperId);
    if (!rows || !rows.length) {
        wrapper.html(`<div class="empty-state"><i class="fa fa-inbox"></i><p>Belum ada data</p></div>`);
        return;
    }
    const ths = columns.map(c => `<th>${c.label}</th>`).join('');
    const trs = rows.map((d, i) => {
        const tds = columns.map(c => {
            const val = d[c.key] ?? '-';
            if (c.type === 'mono') return `<td class="mono">${val}</td>`;
            if (c.type === 'badge') return `<td><span class="badge-type">${val}</span></td>`;
            if (c.type === 'status') return `<td><span class="badge-status ${val}">${val.toUpperCase()}</span></td>`;
            if (c.type === 'idx') return `<td style="color:#a0aec0;font-size:.8rem;">${i+1}</td>`;
            return `<td>${val}</td>`;
        }).join('');
        return `<tr>${tds}</tr>`;
    }).join('');
    wrapper.html(`<div style="overflow-x:auto;"><table class="table-custom"><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table></div>`);
}

function loadStokMasuk() {
    $.get("{{ route('gudang.stok_kantong.list') }}?tipe=masuk", res => {
        renderTable('#stok-masuk-wrapper', res.data, [
            {key:'idx',       label:'#',          type:'idx'},
            {key:'no_terima', label:'No Terima',   type:'mono'},
            {key:'tgl_terima',label:'Tgl Terima',  type:''},
            {key:'no_kantong',label:'No Kantong',  type:'mono'},
            {key:'merk',      label:'Merk',        type:''},
            {key:'jenis',     label:'Jenis',       type:''},
            {key:'tipe',      label:'Type',        type:'badge'},
            {key:'ukuran',    label:'Ukuran',      type:''},
            {key:'no_lot',    label:'No Lot',      type:'mono'},
            {key:'status',    label:'Status',      type:'status'},
        ]);
    });
}

function loadStokKeluar() {
    $.get("{{ route('gudang.stok_kantong.list') }}?tipe=keluar", res => {
        renderTable('#stok-keluar-wrapper', res.data, [
            {key:'idx',       label:'#',          type:'idx'},
            {key:'no_terima', label:'No Terima',   type:'mono'},
            {key:'tgl_terima',label:'Tgl Terima',  type:''},
            {key:'no_kantong',label:'No Kantong',  type:'mono'},
            {key:'merk',      label:'Merk',        type:''},
            {key:'jenis',     label:'Jenis',       type:''},
            {key:'tipe',      label:'Type',        type:'badge'},
            {key:'ukuran',    label:'Ukuran',      type:''},
            {key:'status',    label:'Status',      type:'status'},
        ]);
    });
}

function loadStokKembali() {
    $.get("{{ route('gudang.stok_kantong.list') }}?tipe=kembali", res => {
        renderTable('#stok-kembali-wrapper', res.data, [
            {key:'idx',        label:'#',           type:'idx'},
            {key:'no_kembali', label:'No Kembali',  type:'mono'},
            {key:'tgl_kembali',label:'Tgl Kembali', type:''},
            {key:'no_kantong', label:'No Kantong',  type:'mono'},
            {key:'merk',       label:'Merk',        type:''},
            {key:'jenis',      label:'Jenis',       type:''},
            {key:'kondisi',    label:'Kondisi',     type:'status'},
            {key:'keterangan', label:'Keterangan',  type:''},
        ]);
    });
}

// ── Tab switch ──────────────────────────────────────────
$('a[href="#tab-keluar"]').on('shown.bs.tab', loadStokKeluar);
$('a[href="#tab-masuk"]').on('shown.bs.tab', loadStokMasuk);
$('a[href="#tab-kembali"]').on('shown.bs.tab', loadStokKembali);

// ── Init ────────────────────────────────────────────────
$(document).ready(function () {
    loadSummary();
    loadStokMasuk();
});
</script>
@endpush