@extends('layouts.index')

@section('title', $isEdit ? 'Edit Pengembalian Darah' : 'Tambah Pengembalian Darah')

@push('styles')
<style>
    :root {
        --clr-primary:    #1a56a4;
        --clr-primary-dk: #133f80;
        --clr-accent:     #e63946;
        --clr-success:    #2d8a4e;
        --clr-warning:    #c08a00;
        --clr-surface:    #f0f4fa;
        --clr-border:     #c7d4e8;
        --clr-text:       #1e2533;
        --clr-muted:      #6b7a99;
        --radius:         6px;
        --shadow-sm:      0 1px 4px rgba(0,0,0,.10);
        --shadow-md:      0 3px 12px rgba(0,0,0,.13);
    }
    .page-header {
        background: linear-gradient(135deg, var(--clr-primary), var(--clr-primary-dk));
        color: #fff; padding: 12px 18px; border-radius: var(--radius);
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; box-shadow: var(--shadow-md);
    }
    .page-header h4 { margin:0; font-size:.95rem; font-weight:700; display:flex; align-items:center; gap:8px; }
    .form-section {
        background: #fff; border: 1px solid var(--clr-border);
        border-radius: var(--radius); margin-bottom: 14px;
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .form-section .sec-header {
        background: var(--clr-surface); border-bottom: 1px solid var(--clr-border);
        padding: 8px 14px; font-size: .78rem; font-weight: 700;
        color: var(--clr-primary); letter-spacing: .4px; text-transform: uppercase;
        display: flex; align-items: center; gap: 7px;
    }
    .form-section .sec-body { padding: 14px 16px; }
    label.form-label { font-size:.76rem; font-weight:600; color:var(--clr-muted); margin-bottom:3px; }
    .form-control, .form-select {
        font-size: .82rem; padding: 5px 9px; border-color: var(--clr-border);
        border-radius: 4px; transition: border-color .15s, box-shadow .15s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--clr-primary); box-shadow: 0 0 0 2.5px rgba(26,86,164,.2);
    }
    .invalid-feedback { font-size: .74rem; }
    .scan-group { position: relative; }
    .scan-group .form-control { padding-right: 90px; font-family:'Courier New',monospace; letter-spacing:.5px; }
    .btn-scan {
        position: absolute; right: 0; top: 0; bottom: 0;
        background: var(--clr-primary); color: #fff; border: none;
        border-radius: 0 4px 4px 0; padding: 0 12px; font-size: .76rem;
        font-weight: 600; display: flex; align-items: center; gap: 5px;
        cursor: pointer; white-space: nowrap; transition: background .15s;
    }
    .btn-scan:hover { background: var(--clr-primary-dk); }
    .btn-scan:disabled { opacity: .55; cursor: not-allowed; }
    .info-box {
        background: var(--clr-surface); border: 1px solid var(--clr-border);
        border-radius: 4px; padding: 6px 10px; font-size: .8rem;
        color: var(--clr-text); min-height: 33px; display: flex; align-items: center;
    }
    .info-box.found { border-color: #9ddab8; background: #ecf9f2; color: var(--clr-success); font-weight: 600; }
    .info-box.empty { color: var(--clr-muted); font-style: italic; }
    .detail-wrap {
        background: #fff; border: 1px solid var(--clr-border);
        border-radius: var(--radius); margin-bottom: 14px;
        box-shadow: var(--shadow-sm); overflow: hidden;
    }
    .detail-wrap .sec-header {
        background: var(--clr-surface); border-bottom: 1px solid var(--clr-border);
        padding: 8px 14px; font-size: .78rem; font-weight: 700;
        color: var(--clr-primary); letter-spacing:.4px; text-transform:uppercase;
        display: flex; align-items: center; justify-content: space-between;
    }
    .stock-scan-row { padding: 10px 14px; background: #f7f9fd; border-bottom: 1px solid var(--clr-border); }
    .tbl-detail { font-size: .78rem; margin: 0; }
    .tbl-detail thead th {
        background: #2c4a7c; color: #fff; font-size:.73rem; font-weight:600;
        padding: 7px 8px; border:none; white-space:nowrap;
    }
    .tbl-detail tbody td { padding: 5px 7px; vertical-align:middle; border-color:#e5ecf7; }
    .tbl-detail tbody tr:nth-child(even) td { background:#f7f9fd; }
    .tbl-detail .form-control, .tbl-detail .form-select { padding: 3px 6px; font-size:.76rem; border-radius:3px; }
    .tbl-detail .btn-del-row {
        background: #fdeaea; color: var(--clr-accent); border:1px solid #f5b0b0;
        border-radius: 3px; padding: 3px 7px; font-size:.74rem; cursor: pointer;
    }
    .tbl-detail .btn-del-row:hover { background: var(--clr-accent); color:#fff; }
    .form-footer {
        background: var(--clr-surface); border: 1px solid var(--clr-border);
        border-radius: var(--radius); padding: 12px 16px;
        display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    }
    .btn-save {
        background: var(--clr-primary); color: #fff; border:none;
        border-radius: 5px; padding: 7px 22px; font-size:.83rem; font-weight:600;
        display:flex; align-items:center; gap:6px; cursor:pointer; transition: background .15s;
    }
    .btn-save:hover { background: var(--clr-primary-dk); }
    .btn-cancel {
        background: #fff; color: var(--clr-muted); border: 1px solid var(--clr-border);
        border-radius: 5px; padding: 7px 18px; font-size:.83rem; font-weight:600;
        text-decoration:none; display:flex; align-items:center; gap:6px; transition: background .15s;
    }
    .btn-cancel:hover { background: #f0f4fa; color: var(--clr-text); }
    .nomor-display {
        font-family: 'Courier New', monospace; font-size: .9rem; font-weight: 700;
        color: var(--clr-primary-dk); background: #eef3fc; border: 1px solid var(--clr-border);
        border-radius: 4px; padding: 5px 12px; letter-spacing: .5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" style="font-size:.83rem">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" style="font-size:.83rem">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" style="font-size:.83rem">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Validasi gagal:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="page-header">
        <h4><i class="fas fa-exchange-alt"></i> {{ $isEdit ? 'Edit' : 'Tambah' }} Pengembalian Darah</h4>
        <a href="{{ route('referal.pengembalian_darah.index') }}" class="btn btn-sm btn-light" style="font-size:.78rem">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Form --}}
    <form method="POST"
          action="{{ $isEdit
              ? route('referal.pengembalian_darah.update', $pengembalian)
              : route('referal.pengembalian_darah.store') }}"
          id="mainForm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Informasi Dokumen --}}
        <div class="form-section">
            <div class="sec-header"><i class="fas fa-hashtag"></i> Informasi Dokumen</div>
            <div class="sec-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nomor Kembali</label>
                        <div class="nomor-display">{{ $nomorBaru }}</div>
                        <input type="hidden" name="nomor_kembali" value="{{ $nomorBaru }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_kembali"
                               class="form-control @error('tanggal_kembali') is-invalid @enderror"
                               value="{{ old('tanggal_kembali', $pengembalian?->tanggal_kembali?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_kembali')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Yang Mengembalikan</label>
                        <input type="text" name="yang_mengembalikan" class="form-control"
                               value="{{ old('yang_mengembalikan', $pengembalian?->yang_mengembalikan) }}"
                               placeholder="Nama pengirim / pengantar">
                    </div>
                </div>
            </div>
        </div>

        {{-- Petugas --}}
        <div class="form-section">
            <div class="sec-header"><i class="fas fa-user-md"></i> Petugas Penerima</div>
            <div class="sec-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Kode Petugas (Scan)</label>
                        <div class="scan-group">
                            <input type="text" id="inputPetugas" class="form-control"
                                   placeholder="Scan barcode petugas…" autocomplete="off"
                                   value="{{ old('kode_petugas', $pengembalian?->kode_petugas) }}">
                            <button type="button" class="btn-scan" id="btnScanPetugas">
                                <i class="fas fa-barcode"></i> Scan
                            </button>
                        </div>
                        <input type="hidden" name="kode_petugas" id="kodePetugas"
                               value="{{ old('kode_petugas', $pengembalian?->kode_petugas) }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Nama Petugas</label>
                        <div class="info-box {{ $pengembalian?->nama_petugas ? 'found' : 'empty' }}" id="namaPetugasBox">
                            {{ $pengembalian?->nama_petugas ?? 'Scan petugas untuk mengisi otomatis…' }}
                        </div>
                        <input type="hidden" name="nama_petugas" id="namaPetugas"
                               value="{{ old('nama_petugas', $pengembalian?->nama_petugas) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- FPUP / Pasien --}}
        <div class="form-section">
            <div class="sec-header"><i class="fas fa-file-medical"></i> Data FPUP / Pasien</div>
            <div class="sec-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">No. FPUP (Scan/Ketik)</label>
                        <div class="scan-group">
                            {{-- inputFpup: visible scan input (tidak disubmit) --}}
                            <input type="text" id="inputFpup" class="form-control"
                                   placeholder="Scan atau ketik no. FPUP…" autocomplete="off"
                                   value="{{ old('no_fpup', $pengembalian?->no_fpup) }}">
                            <button type="button" class="btn-scan" id="btnScanFpup">
                                <i class="fas fa-qrcode"></i> F4-Cari
                            </button>
                        </div>
                        {{-- noFpup: hidden field yang DISUBMIT --}}
                        <input type="hidden" name="no_fpup" id="noFpup"
                               value="{{ old('no_fpup', $pengembalian?->no_fpup) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tgl. FPUP</label>
                        <input type="date" name="tgl_fpup" id="tglFpup" class="form-control"
                               value="{{ old('tgl_fpup', $pengembalian?->tgl_fpup?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No. Stock</label>
                        <input type="text" name="no_stock" id="noStockHeader" class="form-control"
                               value="{{ old('no_stock', $pengembalian?->no_stock) }}"
                               placeholder="No. stock utama">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Rumah Sakit</label>
                        <div class="input-group" style="gap:6px">
                            <input type="text" name="kode_rumah_sakit" id="kodeRS" class="form-control"
                                   style="max-width:90px"
                                   value="{{ old('kode_rumah_sakit', $pengembalian?->kode_rumah_sakit) }}"
                                   placeholder="Kode RS">
                            <input type="text" name="nama_rumah_sakit" id="namaRS" class="form-control"
                                   value="{{ old('nama_rumah_sakit', $pengembalian?->nama_rumah_sakit) }}"
                                   placeholder="Nama Rumah Sakit">
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-2">
                        <label class="form-label">Tgl. Pemberian</label>
                        <input type="date" name="tgl_pemberian" id="tglPemberian" class="form-control"
                               value="{{ old('tgl_pemberian', $pengembalian?->tgl_pemberian?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Umur (Hari) Pemberian</label>
                        <input type="number" name="umur_hari_pemberian" id="umurHariPemberian"
                               class="form-control" min="0"
                               value="{{ old('umur_hari_pemberian', $pengembalian?->umur_hari_pemberian) }}"
                               placeholder="0">
                    </div>
                </div>
            </div>
        </div>

        {{-- Alasan & Status --}}
        <div class="form-section">
            <div class="sec-header"><i class="fas fa-clipboard-list"></i> Alasan & Status Pengembalian</div>
            <div class="sec-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Alasan Kembali</label>
                        <input type="text" name="alasan_kembali" id="mainAlasan"
                               class="form-control @error('alasan_kembali') is-invalid @enderror"
                               value="{{ old('alasan_kembali', $pengembalian?->alasan_kembali) }}"
                               placeholder="Masukkan alasan pengembalian…">
                        @error('alasan_kembali')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Kembali <span class="text-danger">*</span></label>
                        <select name="status_kembali" id="mainStatus"
                                class="form-select @error('status_kembali') is-invalid @enderror" required>
                            @foreach(['Baik','Rusak','Kadaluarsa'] as $st)
                                <option value="{{ $st }}"
                                    {{ old('status_kembali', $pengembalian?->status_kembali ?? 'Baik') === $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_kembali')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                               value="{{ old('keterangan', $pengembalian?->keterangan) }}"
                               placeholder="Catatan tambahan…">
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Stock Kantong --}}
        <div class="detail-wrap">
            <div class="sec-header">
                <span><i class="fas fa-tint me-1"></i> Detail Stock Kantong Darah</span>
                <span class="text-muted" style="font-size:.72rem;font-weight:400">Scan atau tambah manual</span>
            </div>
            <div class="stock-scan-row">
                <div class="row align-items-end g-2">
                    <div class="col-md-4">
                        <label class="form-label mb-1">No. Stock (Scan Barcode)</label>
                        <div class="scan-group">
                            <input type="text" id="inputStock" class="form-control"
                                   placeholder="Scan barcode kantong darah…" autocomplete="off">
                            <button type="button" class="btn-scan" id="btnScanStock">
                                <i class="fas fa-barcode"></i> Cari
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" id="btnAddRow"
                                style="font-size:.78rem;padding:5px 0">
                            <i class="fas fa-plus me-1"></i> Tambah Manual
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered tbl-detail" id="detailTable">
                    <thead>
                        <tr>
                            <th style="width:35px">No</th>
                            <th style="min-width:120px">No. Stock</th>
                            <th style="min-width:100px">Jns Darah</th>
                            <th style="width:55px">Gol</th>
                            <th style="width:55px">Rhesus</th>
                            <th style="width:70px">Sts</th>
                            <th style="width:100px">Stat. Kembali</th>
                            <th style="min-width:140px">Alasan Kembali</th>
                            <th style="width:110px">Tgl. Aftap</th>
                            <th style="width:110px">Kadaluarsa</th>
                            <th style="width:50px">Jml</th>
                            <th style="width:38px"></th>
                        </tr>
                    </thead>
                    <tbody id="detailBody">
                        @if($isEdit && $pengembalian->details->count())
                            @foreach($pengembalian->details as $idx => $d)
                            <tr>
                                <td class="text-center row-no">{{ $idx + 1 }}</td>
                                <td><input type="text" name="details[{{ $idx }}][no_stock]" class="form-control" value="{{ $d->no_stock }}" required></td>
                                <td><input type="text" name="details[{{ $idx }}][jenis_darah]" class="form-control" value="{{ $d->jenis_darah }}"></td>
                                <td><input type="text" name="details[{{ $idx }}][gol_darah]" class="form-control text-center" value="{{ $d->gol_darah }}" maxlength="10"></td>
                                <td><input type="text" name="details[{{ $idx }}][rhesus]" class="form-control text-center" value="{{ $d->rhesus }}" maxlength="10"></td>
                                <td><input type="text" name="details[{{ $idx }}][sts]" class="form-control text-center" value="{{ $d->sts }}" maxlength="50"></td>
                                <td>
                                    <select name="details[{{ $idx }}][status_kembali]" class="form-select">
                                        @foreach(['Baik','Rusak','Kadaluarsa'] as $st)
                                            <option value="{{ $st }}" {{ $d->status_kembali === $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" name="details[{{ $idx }}][alasan_kembali]" class="form-control" value="{{ $d->alasan_kembali }}"></td>
                                <td><input type="date" name="details[{{ $idx }}][tgl_aftap]" class="form-control" value="{{ $d->tgl_aftap?->format('Y-m-d') }}"></td>
                                <td><input type="date" name="details[{{ $idx }}][kadaluarsa]" class="form-control" value="{{ $d->kadaluarsa?->format('Y-m-d') }}"></td>
                                <td><input type="number" name="details[{{ $idx }}][jumlah]" class="form-control text-center" value="{{ $d->jumlah }}" min="1"></td>
                                <td class="text-center">
                                    <button type="button" class="btn-del-row" onclick="delRow(this)">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center"
                 style="background:#f7f9fd;font-size:.78rem;color:var(--clr-muted)">
                <span>Total Item: <strong id="totalItems" class="text-primary">{{ $isEdit ? $pengembalian->details->count() : 0 }}</strong></span>
                <span>Jumlah: <strong id="totalJumlah" class="text-primary">{{ $isEdit ? $pengembalian->details->sum('jumlah') : 0 }}</strong></span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="form-footer">
            <a href="{{ route('referal.pengembalian_darah.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="submit" class="btn-save" id="btnSave">
                <i class="fas fa-save"></i> {{ $isEdit ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
const ROUTES = {
    scanFpup:    "{{ route('referal.pengembalian_darah.scan_fpup') }}",
    scanStock:   "{{ route('referal.pengembalian_darah.scan_stock') }}",
    scanPetugas: "{{ route('referal.pengembalian_darah.scan_petugas') }}",
};
const CSRF = "{{ csrf_token() }}";
let rowIndex = {{ $isEdit ? $pengembalian->details->count() : 0 }};

// ── Utilities ─────────────────────────────────────────────────────────────────
function reindex() {
    document.querySelectorAll('#detailBody tr').forEach((tr, i) => {
        tr.querySelector('.row-no').textContent = i + 1;
        tr.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/details\[\d+\]/, 'details[' + i + ']');
        });
    });
    updateTotals();
}

function updateTotals() {
    const rows = document.querySelectorAll('#detailBody tr');
    document.getElementById('totalItems').textContent = rows.length;
    let jml = 0;
    rows.forEach(tr => { jml += parseInt(tr.querySelector('input[name*="jumlah"]')?.value || 0); });
    document.getElementById('totalJumlah').textContent = jml;
}

function delRow(btn) { btn.closest('tr').remove(); reindex(); }

// ── Add row ───────────────────────────────────────────────────────────────────
function addRow(data = {}) {
    const idx = rowIndex++;

    // Auto-sync alasan & status dari field utama
    if (!data.alasan_kembali) {
        data.alasan_kembali = document.getElementById('mainAlasan')?.value || '';
    }
    if (!data.status_kembali) {
        data.status_kembali = document.getElementById('mainStatus')?.value || 'Baik';
    }

    const esc = v => String(v || '').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;');
    const opts = ['Baik','Rusak','Kadaluarsa']
        .map(s => '<option value="' + s + '" ' + (data.status_kembali === s ? 'selected' : '') + '>' + s + '</option>')
        .join('');

    const tr = document.createElement('tr');
    tr.innerHTML =
        '<td class="text-center row-no">' + (document.querySelectorAll('#detailBody tr').length + 1) + '</td>' +
        '<td><input type="text"   name="details['+idx+'][no_stock]"       class="form-control" value="'+esc(data.no_stock)+'" required></td>' +
        '<td><input type="text"   name="details['+idx+'][jenis_darah]"    class="form-control" value="'+esc(data.jenis_darah)+'"></td>' +
        '<td><input type="text"   name="details['+idx+'][gol_darah]"      class="form-control text-center" value="'+esc(data.gol_darah)+'" maxlength="10"></td>' +
        '<td><input type="text"   name="details['+idx+'][rhesus]"         class="form-control text-center" value="'+esc(data.rhesus)+'" maxlength="10"></td>' +
        '<td><input type="text"   name="details['+idx+'][sts]"            class="form-control text-center" value="'+esc(data.sts)+'" maxlength="50"></td>' +
        '<td><select name="details['+idx+'][status_kembali]" class="form-select">'+opts+'</select></td>' +
        '<td><input type="text"   name="details['+idx+'][alasan_kembali]" class="form-control" value="'+esc(data.alasan_kembali)+'"></td>' +
        '<td><input type="date"   name="details['+idx+'][tgl_aftap]"      class="form-control" value="'+esc(data.tgl_aftap)+'"></td>' +
        '<td><input type="date"   name="details['+idx+'][kadaluarsa]"     class="form-control" value="'+esc(data.kadaluarsa)+'"></td>' +
        '<td><input type="number" name="details['+idx+'][jumlah]"         class="form-control text-center" value="'+(data.jumlah || 1)+'" min="1"></td>' +
        '<td class="text-center"><button type="button" class="btn-del-row" onclick="delRow(this)"><i class="fas fa-times"></i></button></td>';
    document.getElementById('detailBody').appendChild(tr);
    tr.querySelector('input[name*="jumlah"]').addEventListener('input', updateTotals);
    updateTotals();
}

// ── AJAX helper ───────────────────────────────────────────────────────────────
async function doScan(url, payload) {
    let res;
    try {
        res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
    } catch (e) {
        return { ok: false, data: { found: false, message: 'Tidak dapat terhubung ke server.' } };
    }
    const text = await res.text();
    let data;
    try { data = JSON.parse(text); }
    catch (_) { data = { found: false, message: 'Server error (' + res.status + ')' }; }
    return { ok: res.ok, data };
}

// ── Toast ─────────────────────────────────────────────────────────────────────
function showToast(msg, type = 'danger') {
    const el = document.createElement('div');
    el.className = 'alert alert-' + type + ' alert-dismissible fade show py-2 px-3';
    el.style.cssText = 'font-size:.81rem;position:fixed;top:14px;right:14px;z-index:9999;min-width:260px;max-width:440px;box-shadow:0 4px 18px rgba(0,0,0,.18)';
    const icon = type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle';
    el.innerHTML = '<i class="fas fa-' + icon + ' me-1"></i>' + msg +
        '<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>';
    document.body.appendChild(el);
    setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 300); }, 5000);
}

// ── Warning modal (duplikat FPUP / Stock) ────────────────────────────────────
let _warningCallback = null;

function showWarning(title, message, onProceed, onCancel) {
    // Hapus modal lama jika ada
    document.getElementById('_warnModal')?.remove();

    const m = document.createElement('div');
    m.id = '_warnModal';
    m.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9998;display:flex;align-items:center;justify-content:center';
    m.innerHTML =
        '<div style="background:#fff;border-radius:8px;overflow:hidden;max-width:440px;width:94%;box-shadow:0 8px 32px rgba(0,0,0,.22)">' +
            '<div style="background:#fff3cd;padding:12px 16px;border-bottom:1px solid #f0d080;display:flex;align-items:center;gap:8px">' +
                '<i class="fas fa-exclamation-triangle" style="color:#c08a00;font-size:1.1rem"></i>' +
                '<strong style="font-size:.88rem;color:#7d5a00">' + title + '</strong>' +
            '</div>' +
            '<div style="padding:14px 16px;font-size:.83rem;color:#1e2533;line-height:1.5">' + message + '</div>' +
            '<div style="padding:10px 16px;border-top:1px solid #eee;display:flex;gap:8px;justify-content:flex-end">' +
                '<button id="_warnCancel" style="background:#fff;border:1px solid #c7d4e8;border-radius:5px;padding:6px 16px;font-size:.81rem;cursor:pointer">Batalkan</button>' +
                '<button id="_warnProceed" style="background:#1a56a4;color:#fff;border:none;border-radius:5px;padding:6px 16px;font-size:.81rem;font-weight:600;cursor:pointer">Tetap Lanjutkan</button>' +
            '</div>' +
        '</div>';
    document.body.appendChild(m);

    document.getElementById('_warnProceed').addEventListener('click', () => { m.remove(); if (onProceed) onProceed(); });
    document.getElementById('_warnCancel').addEventListener('click', () => { m.remove(); if (onCancel) onCancel(); });
    m.addEventListener('click', e => { if (e.target === m) { m.remove(); if (onCancel) onCancel(); } });
}

function setBtnLoading(btn, on) {
    if (on) { btn._orig = btn.innerHTML; btn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem"></span>'; btn.disabled = true; }
    else { btn.innerHTML = btn._orig || btn.innerHTML; btn.disabled = false; }
}

function toInputDate(raw) {
    if (!raw) return '';
    const s = String(raw).trim();
    if (/^\d{4}-\d{2}-\d{2}/.test(s)) return s.slice(0, 10);
    const m = s.match(/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})/);
    if (m) return m[3] + '-' + m[2] + '-' + m[1];
    return s;
}

function setField(el, value) {
    if (!el) return;
    el.value = value ?? '';
    if (value) {
        el.style.transition = 'background .15s';
        el.style.background = '#d4f5e2';
        setTimeout(() => { el.style.background = ''; }, 1800);
    }
}

// ── Auto hitung umur ──────────────────────────────────────────────────────────
function hitungUmur() {
    const tglEl  = document.getElementById('tglPemberian');
    const umurEl = document.getElementById('umurHariPemberian');
    if (!tglEl || !umurEl || !tglEl.value) return;
    const d    = new Date(tglEl.value + 'T00:00:00');
    const n    = new Date(); n.setHours(0,0,0,0);
    const diff = Math.floor((n - d) / 86400000);
    if (diff >= 0) setField(umurEl, diff);
}
document.getElementById('tglPemberian').addEventListener('change', hitungUmur);
document.getElementById('tglPemberian').addEventListener('input',  hitungUmur);

// ══════════════════════════════════════════════════════════════════════════════
// ── Form submit: pastikan no_fpup terisi sebelum kirim ───────────────────────
// ══════════════════════════════════════════════════════════════════════════════
document.getElementById('mainForm').addEventListener('submit', function(e) {
    const noFpupEl    = document.getElementById('noFpup');
    const inputFpupEl = document.getElementById('inputFpup');

    // Jika hidden field kosong tapi visible input ada nilainya → salin
    if (!noFpupEl.value.trim() && inputFpupEl.value.trim()) {
        noFpupEl.value = inputFpupEl.value.trim();
    }

    // Tombol simpan → loading state
    const btnSave = document.getElementById('btnSave');
    if (btnSave) {
        btnSave._orig   = btnSave.innerHTML;
        btnSave.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:.75rem;height:.75rem"></span> Menyimpan…';
        btnSave.disabled  = true;
    }
});

// ── Scan Petugas ──────────────────────────────────────────────────────────────
document.getElementById('btnScanPetugas').addEventListener('click', async () => {
    const val = document.getElementById('inputPetugas').value.trim();
    if (!val) return;
    const btn = document.getElementById('btnScanPetugas');
    setBtnLoading(btn, true);
    const { data } = await doScan(ROUTES.scanPetugas, { kode_petugas: val });
    setBtnLoading(btn, false);
    const box = document.getElementById('namaPetugasBox');
    if (data.found) {
        document.getElementById('kodePetugas').value = data.data.kode || val;
        document.getElementById('namaPetugas').value = data.data.nama || '';
        box.textContent = data.data.nama || val;
        box.className   = 'info-box found';
    } else {
        box.textContent = data.message || 'Petugas tidak ditemukan.';
        box.className   = 'info-box';
        showToast(data.message || 'Kode petugas tidak ditemukan.');
    }
});
document.getElementById('inputPetugas').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('btnScanPetugas').click(); }
});

// ── Scan FPUP ─────────────────────────────────────────────────────────────────
document.getElementById('btnScanFpup').addEventListener('click', async () => {
    const val = document.getElementById('inputFpup').value.trim();
    if (!val) { showToast('Masukkan No. FPUP terlebih dahulu.', 'warning'); return; }

    const btn = document.getElementById('btnScanFpup');
    setBtnLoading(btn, true);
    const { data } = await doScan(ROUTES.scanFpup, { no_fpup: val });
    setBtnLoading(btn, false);

    console.log('[scan-fpup]', data);

    if (data.found) {
        const d = data.data;

        // ── Cek warning duplikat FPUP ─────────────────────────────────────
        if (d.warning) {
            showWarning(
                'No. FPUP Sudah Pernah Dikembalikan',
                d.warning_message + '<br><br><span style="color:#6b7a99;font-size:.8rem">Apakah Anda tetap ingin melanjutkan pengembalian untuk No. FPUP ini?</span>',
                () => _isiFieldFpup(d, val),  // lanjutkan → isi field
                () => { /* batalkan → bersihkan input */ document.getElementById('inputFpup').value = ''; }
            );
            return;
        }

        _isiFieldFpup(d, val);
    } else {
        showToast(data.message || 'No FPUP tidak ditemukan.');
    }
});

// ── Helper: isi semua field FPUP setelah konfirmasi ───────────────────────
function _isiFieldFpup(d, val) {
    // no_fpup hidden field
    document.getElementById('noFpup').value = d.no_fpup || val;

        // tgl_fpup
        const tglFpupEl = document.getElementById('tglFpup');
        if (tglFpupEl) {
            tglFpupEl.value = toInputDate(d.tgl_fpup);
            if (d.tgl_fpup) { tglFpupEl.style.background='#d4f5e2'; setTimeout(()=>tglFpupEl.style.background='',1800); }
        }

        // no_stock
        setField(document.getElementById('noStockHeader'), d.no_stock || '');

        // RS
        setField(document.getElementById('kodeRS'), d.kode_rumah_sakit || '');
        setField(document.getElementById('namaRS'), d.nama_rumah_sakit || '');

        // tgl_pemberian
        const tglPbrEl = document.getElementById('tglPemberian');
        if (tglPbrEl) {
            tglPbrEl.value = toInputDate(d.tgl_pemberian);
            if (d.tgl_pemberian) { tglPbrEl.style.background='#d4f5e2'; setTimeout(()=>tglPbrEl.style.background='',1800); }
        }

        // umur
        const umurEl = document.getElementById('umurHariPemberian');
        if (umurEl) {
            if (d.umur_hari_pemberian !== null && d.umur_hari_pemberian !== undefined) {
                setField(umurEl, d.umur_hari_pemberian);
            } else {
                setTimeout(hitungUmur, 80);
            }
        }

        const terisi = [];
        if (d.tgl_fpup)         terisi.push('Tgl.FPUP');
        if (d.no_stock)         terisi.push('No.Stock');
        if (d.nama_rumah_sakit) terisi.push('RS');
        if (d.tgl_pemberian)    terisi.push('Tgl.Pemberian');
        if (d.tgl_pemberian || d.umur_hari_pemberian != null) terisi.push('Umur');

        showToast('Data FPUP ditemukan' + (terisi.length ? '. <strong>' + terisi.join(', ') + '</strong> terisi.' : '.'), 'success');
}
document.getElementById('inputFpup').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('btnScanFpup').click(); }
});
document.addEventListener('keydown', e => {
    if (e.key === 'F4') { e.preventDefault(); document.getElementById('inputFpup').focus(); }
});

// ── Scan Stock ────────────────────────────────────────────────────────────────
document.getElementById('btnScanStock').addEventListener('click', async () => {
    const val = document.getElementById('inputStock').value.trim();
    if (!val) return;

    // ── Cek duplikat di tabel detail yang sedang dibuka (client-side) ────────
    const existingInTable = Array.from(
        document.querySelectorAll('#detailBody input[name*="[no_stock]"]')
    ).some(el => el.value.trim() === val);

    if (existingInTable) {
        showWarning(
            'No. Stock Sudah Ada di Tabel',
            'No. Stock <strong>' + val + '</strong> sudah ada di daftar detail di bawah.<br><br>' +
            '<span style="color:#6b7a99;font-size:.8rem">Apakah Anda ingin menambahkannya lagi?</span>',
            () => _doScanStock(val),
            null
        );
        return;
    }

    _doScanStock(val);
});

async function _doScanStock(val) {
    const btn = document.getElementById('btnScanStock');
    setBtnLoading(btn, true);
    const { data } = await doScan(ROUTES.scanStock, { no_stock: val });
    setBtnLoading(btn, false);

    if (data.found) {
        const d = data.data;

        // ── Cek warning duplikat dari database ───────────────────────────
        if (d.warning) {
            showWarning(
                'No. Stock Sudah Pernah Dikembalikan',
                d.warning_message + '<br><br>' +
                '<span style="color:#6b7a99;font-size:.8rem">Apakah Anda tetap ingin menambahkan stock ini?</span>',
                () => {
                    addRow(d);
                    document.getElementById('inputStock').value = '';
                    document.getElementById('inputStock').focus();
                    showToast('Stock ditambahkan (ada riwayat pengembalian).', 'warning');
                },
                () => {
                    document.getElementById('inputStock').value = '';
                    document.getElementById('inputStock').focus();
                }
            );
            return;
        }

        addRow(d);
        document.getElementById('inputStock').value = '';
        document.getElementById('inputStock').focus();
        showToast('Stock ditambahkan ke tabel.', 'success');
    } else {
        showToast(data.message || 'No Stock tidak ditemukan.');
    }
}
document.getElementById('inputStock').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('btnScanStock').click(); }
});

document.getElementById('btnAddRow').addEventListener('click', () => addRow());

// ── Sync Alasan & Status → detail rows ───────────────────────────────────────
document.getElementById('mainAlasan').addEventListener('input', function() {
    document.querySelectorAll('#detailBody input[name*="[alasan_kembali]"]').forEach(el => el.value = this.value);
});
document.getElementById('mainStatus').addEventListener('change', function() {
    document.querySelectorAll('#detailBody select[name*="[status_kembali]"]').forEach(el => el.value = this.value);
});

// ── Init ──────────────────────────────────────────────────────────────────────
updateTotals();
hitungUmur();
</script>
@endpush