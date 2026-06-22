@extends('layouts.index')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --red:      #C8102E;
    --red-d:    #a00d24;
    --red-glow: rgba(200,16,46,.18);
    --navy:     #ebf5ff;
    --navy-2:   #dbe7fe;
    --teal:     #00c9b1;
    --amber:    #f59e0b;
    --sky:      #38bdf8;
    --muted:    #7a8fa6;
    --border:   #1e2f42;
    --card:     #f3f7fa;
    --card-2:   #e8f1ff;
    --text:     #0f1318;
    --text-dim: #8fa3bc;
    --mono: 'JetBrains Mono', monospace;
    --sans: 'Plus Jakarta Sans', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--sans); background: var(--navy); color: var(--text); margin: 0; }

/* ── Header ── */
.fp-header {
    background: linear-gradient(135deg, #0d1b2a 0%, #1a2f45 100%);
    border-bottom: 2px solid var(--red);
    padding: 1.1rem 2rem;
    display: flex; align-items: center; gap: 1.25rem;
    box-shadow: 0 4px 24px rgba(0,0,0,.4);
    position: sticky; top: 0; z-index: 100;
}
.fp-header .logo-badge {
    background: var(--red); color: #fff;
    font-family: var(--mono); font-size: .6rem; font-weight: 700;
    letter-spacing: .12em; padding: .3rem .65rem;
    border-radius: 4px; text-transform: uppercase; flex-shrink: 0;
}
.fp-header h1 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #fff; }
.fp-header p  { margin: 0; font-size: .73rem; color: var(--muted); }
.fp-header .ms-auto { margin-left: auto; display: flex; gap: .5rem; }

/* ── No FPUP Bar ── */
.no-fpup-bar {
    background: linear-gradient(90deg, var(--card-2), var(--navy-2));
    border: 1px solid var(--border);
    border-left: 4px solid var(--teal);
    border-radius: 10px;
    padding: .85rem 1.25rem;
    display: flex; align-items: center; gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.no-fpup-label { font-size: .68rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
.no-fpup-val   { font-family: var(--mono); font-size: 1.3rem; font-weight: 600; color: var(--teal); letter-spacing: .05em; }
.meta-item { border-left: 1px solid var(--border); padding-left: 1.25rem; }

/* ── Section Card ── */
.sec-card {
    background   : var(--card);
    border       : 1px solid var(--border);
    border-radius: 14px;
    margin-bottom: 1.25rem;
    overflow     : hidden;
}
.sec-head {
    background    : var(--card-2);
    border-bottom : 1px solid var(--border);
    padding       : .8rem 1.25rem;
    display       : flex; align-items: center; gap: .65rem;
    font-size     : .78rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
}
.sec-head .ico { font-size: .95rem; }
.sec-head .ico.teal  { color: var(--teal); }
.sec-head .ico.amber { color: var(--amber); }
.sec-head .ico.sky   { color: var(--sky); }
.sec-head .ico.red   { color: var(--red); }
.sec-body { padding: 1.25rem; }

/* ── Form Grid ── */
.fg { display: grid; gap: 1rem; margin-bottom: .9rem; }
.fg-4 { grid-template-columns: repeat(4, 1fr); }
.fg-3 { grid-template-columns: repeat(3, 1fr); }
.fg-2 { grid-template-columns: repeat(2, 1fr); }
.fg-1 { grid-template-columns: 1fr; }
@media(max-width:900px) { .fg-4,.fg-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:600px) { .fg-4,.fg-3,.fg-2 { grid-template-columns: 1fr; } }

.fgroup label {
    display: block; font-size: .67rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted); margin-bottom: .35rem;
}
.fgroup label .req { color: var(--red); }
.fgroup input,
.fgroup select,
.fgroup textarea {
    width: 100%;
    background: var(--navy-2);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: .5rem .85rem;
    font-size: .85rem;
    color: var(--text);
    font-family: var(--sans);
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.fgroup input:focus,
.fgroup select:focus,
.fgroup textarea:focus {
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(0,201,177,.12);
}
.fgroup input[readonly] {
    background: rgba(255,255,255,.03);
    color: var(--text-dim);
    cursor: default;
}
.fgroup .hint { font-size: .68rem; color: var(--muted); margin-top: .25rem; }
.fgroup textarea { resize: vertical; min-height: 64px; }

/* ── Autocomplete dropdown ── */
.ac-wrap { position: relative; }
.ac-dropdown {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: var(--card-2);
    border: 1.5px solid var(--teal);
    border-radius: 8px;
    z-index: 200;
    max-height: 240px;
    overflow-y: auto;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    display: none;
}
.ac-dropdown.open { display: block; }
.ac-item {
    padding: .55rem 1rem;
    cursor: pointer;
    font-size: .82rem;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.ac-item:last-child { border-bottom: none; }
.ac-item:hover, .ac-item.focused { background: rgba(0,201,177,.12); }
.ac-item .ac-kode { font-family: var(--mono); font-size: .72rem; color: var(--teal); margin-right: .5rem; }
.ac-item .ac-sub  { font-size: .7rem; color: var(--muted); margin-top: .1rem; }
.ac-loading { padding: .6rem 1rem; font-size: .8rem; color: var(--muted); }

/* ── Radio group ── */
.radio-group { display: flex; gap: .75rem; flex-wrap: wrap; padding-top: .2rem; }
.radio-group label {
    display: flex; align-items: center; gap: .4rem;
    font-size: .82rem; font-weight: 500;
    text-transform: none; letter-spacing: 0;
    color: var(--text); cursor: pointer;
}
.radio-group input[type=radio] { accent-color: var(--teal); cursor: pointer; }

/* ── Toggle switch ── */
.toggle-wrap { display: flex; align-items: center; gap: .6rem; padding-top: .4rem; }
.toggle { position: relative; display: inline-block; width: 38px; height: 20px; flex-shrink: 0; }
.toggle input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute; inset: 0; cursor: pointer;
    background: var(--border); border-radius: 20px; transition: .25s;
}
.slider:before {
    position: absolute; content: "";
    height: 14px; width: 14px; left: 3px; bottom: 3px;
    background: #fff; border-radius: 50%; transition: .25s;
}
input:checked + .slider { background: var(--teal); }
input:checked + .slider:before { transform: translateX(18px); }
.toggle-label { font-size: .82rem; color: var(--text-dim); }

/* ── Conditional block ── */
.cond-block {
    background: rgba(0,201,177,.05);
    border: 1px solid rgba(0,201,177,.2);
    border-radius: 10px;
    padding: .9rem 1rem;
    margin-top: .5rem;
}
.cond-block.danger {
    background: rgba(200,16,46,.05);
    border-color: rgba(200,16,46,.2);
}

/* ── Detail Darah Table ── */
.detail-tbl-wrap { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; margin-bottom: 1rem; }
.detail-tbl { width: 100%; border-collapse: collapse; }
.detail-tbl thead tr { background: var(--card-2); }
.detail-tbl thead th {
    padding: .65rem .9rem;
    font-size: .64rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted); text-align: left;
    border-bottom: 1px solid var(--border);
}
.detail-tbl tbody tr { border-bottom: 1px solid rgba(251, 251, 251, 0.7); }
.detail-tbl tbody td { padding: .5rem .6rem; vertical-align: middle; }
.detail-tbl input,
.detail-tbl select {
    background: var(--navy-2); border: 1px solid var(--border);
    border-radius: 6px; padding: .38rem .6rem;
    font-size: .8rem; color: var(--text); font-family: var(--sans);
    width: 100%; outline: none;
}
.detail-tbl input:focus,
.detail-tbl select:focus { border-color: var(--teal); }

/* ── Btn ── */
.btn-fp {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1.2rem; border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .2s;
    font-family: var(--sans); text-decoration: none;
}
.btn-fp:active { transform: scale(.97); }
.btn-primary { background: var(--red); color: #fff; }
.btn-primary:hover { background: var(--red-d); color: #fff; }
.btn-ghost   { background: rgba(255,255,255,.06); color: var(--text); border: 1px solid var(--border); }
.btn-ghost:hover { background: rgba(255,255,255,.12); }
.btn-teal    { background: rgba(0,201,177,.15); color: var(--teal); border: 1px solid rgba(0,201,177,.3); }
.btn-teal:hover { background: rgba(0,201,177,.28); }
.btn-sm      { padding: .32rem .7rem; font-size: .74rem; }
.btn-danger  { background: rgba(200,16,46,.15); color: #f87171; border: 1px solid rgba(200,16,46,.3); }
.btn-danger:hover { background: rgba(200,16,46,.28); }

/* ── Footer action ── */
.form-footer {
    background: var(--card-2); border: 1px solid var(--border); border-radius: 14px;
    padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
    gap: 1rem; flex-wrap: wrap;
}
.form-footer .left  { display: flex; gap: .5rem; align-items: center; }
.form-footer .right { display: flex; gap: .6rem; align-items: center; }

/* ── Validation errors ── */
.err-msg { color: #f87171; font-size: .72rem; margin-top: .25rem; }

/* ── Modal Pasien ── */
.modal-content { background: var(--card); color: var(--text); border: 1px solid var(--border); }
.nav-tabs .nav-link { color: var(--text-dim); }
.nav-tabs .nav-link.active { color: var(--red); font-weight: 700; }
.list-group-item { background: var(--navy-2); border-color: var(--border); color: var(--text); cursor: pointer; }
.list-group-item:hover { background: rgba(0,201,177,.12); }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="fp-header">
    <span class="logo-badge">PMI</span>
    <div>
        <h1>{{ $mode === 'create' ? 'Tambah' : 'Edit' }} Permintaan Darah (FPUP)</h1>
        <p>Gudang › Permintaan FPUP › {{ $mode === 'create' ? 'Formulir Baru' : $fpup->no_fpup }}</p>
    </div>
    <div class="ms-auto">
        <a href="{{ route('crossmatch.permintaan_fpup.index') }}" class="btn-fp btn-ghost">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="container-fluid px-4 py-4">

    @if($errors->any())
    <div style="background:rgba(200,16,46,.12);border:1px solid rgba(200,16,46,.3);border-radius:10px;padding:.85rem 1.25rem;margin-bottom:1.25rem;font-size:.82rem;color:#f87171;">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Ada {{ $errors->count() }} kesalahan:</strong>
        <ul style="margin:.4rem 0 0 1rem;padding:0;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form id="form-fpup"
          method="POST"
          action="{{ $mode === 'create' ? route('crossmatch.permintaan_fpup.store') : route('crossmatch.permintaan_fpup.update', $fpup) }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        {{-- fpup_id (master pasien terpilih dari modal pencarian/tambah baru) --}}
        <input type="hidden" name="fpup_id" id="fpup_id_hidden" value="{{ old('fpup_id', $fpup?->fpup_id ?? '') }}">

        {{-- No FPUP Bar --}}
        <div class="no-fpup-bar">
            <div>
                <div class="no-fpup-label">No FPUP</div>
                <div class="no-fpup-val" id="display-no-fpup">
                    {{ $mode === 'edit' ? $fpup->no_fpup : '—' }}
                </div>
                <input type="hidden" name="no_fpup" id="no_fpup_hidden" value="{{ $mode === 'edit' ? $fpup->no_fpup : '' }}">
            </div>
            <div class="meta-item">
                <div class="no-fpup-label">Tgl & Jam</div>
                <div style="font-family:var(--mono);font-size:.88rem;">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="meta-item">
                <div class="no-fpup-label">Status</div>
                <div style="font-size:.88rem;">{{ $mode === 'create' ? 'BARU' : strtoupper($fpup->status) }}</div>
            </div>
            @if($mode === 'create')
            <div class="meta-item" style="margin-left:auto;">
                <button type="button" id="btn-gen-no" class="btn-fp btn-teal btn-sm">
                    <i class="fas fa-sync-alt"></i> Generate No FPUP
                </button>
            </div>
            @endif
        </div>

        {{-- ① Data Rumah Sakit --}}
        <div class="sec-card">
            <div class="sec-head">
                <i class="fas fa-hospital ico sky"></i> Data Rumah Sakit / Instansi
            </div>
            <div class="sec-body">
                <div class="fg fg-4">
                    <div class="fgroup">
                        <label>Kode RS</label>
                        <input type="text" name="kode_rs" id="kode_rs"
                               value="{{ old('kode_rs', $fpup->kode_rs ?? '') }}"
                               placeholder="500018"
                               autocomplete="off">
                    </div>

                    <div class="fgroup ac-wrap" style="grid-column: span 2;">
                        <label>Nama RS / Instansi</label>
                        <input type="text" name="nama_rs" id="nama_rs"
                               value="{{ old('nama_rs', $fpup->nama_rs ?? '') }}"
                               placeholder="Ketik nama rumah sakit..."
                               autocomplete="off">
                        <div class="ac-dropdown" id="ac-rs-dropdown"></div>
                    </div>

                    <div class="fg fg-3">
                        <div class="fgroup">
                            <label>No Registrasi</label>
                            <input type="text"
                                name="no_reg"
                                value="{{ old('no_reg', $fpup->no_reg ?? ($nextNoReg ?? '')) }}">
                        </div>

                        <div class="fgroup">
                            <label>No Registrasi </label>
                            <input type="text"
                                name="no_reg_online"
                                value="{{ old('no_reg_online', $fpup->no_reg_online ?? ($nextNoRegOnline ?? '')) }}">
                        </div>

                        <div class="fgroup">
                            <label>Tanggal Registrasi </label>
                            <input type="date"
                                name="tgl_registrasi_online"
                                value="{{ old('tgl_registrasi_online',
                                        isset($fpup->tgl_registrasi_online)
                                        ? $fpup->tgl_registrasi_online->format('Y-m-d')
                                        : now()->format('Y-m-d')) }}">
                        </div>
                    </div>
                </div>
                <div class="fg fg-4">
                    <div class="fgroup">
                        <label>Jenis RS</label>
                        <select name="jenis_rs" id="jenis_rs">
                            <option value="">— Pilih —</option>
                            @foreach($jenis_rs as $j)
                                <option value="{{ $j }}" @selected(old('jenis_rs', $fpup->jenis_rs ?? '') === $j)>{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Kategori RS</label>
                        <select name="kategori_rs" id="kategori_rs">
                            <option value="">— Pilih —</option>
                            @foreach($kategori_rs as $k)
                                <option value="{{ $k }}" @selected(old('kategori_rs', $fpup->kategori_rs ?? '') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Bagian / Ruangan</label>
                        <select name="bagian">
                            <option value="">— Pilih —</option>
                            @foreach($bagian as $b)
                                <option value="{{ $b }}" @selected(old('bagian', $fpup->bagian ?? '') === $b)>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Kelas Rawat</label>
                        <select name="kelas_rawat">
                            <option value="">— Pilih —</option>
                            @foreach($kelas_rawat as $kr)
                                <option value="{{ $kr }}" @selected(old('kelas_rawat', $fpup->kelas_rawat ?? '') === $kr)>{{ $kr }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fg fg-3">
                    <div class="fgroup">
                        <label>Kelompok RS</label>
                        <input type="text" id="kelompok_rs_display" readonly placeholder="— otomatis —"
                               value="{{ old('kelompok_rs', $fpup->kelompok_rs ?? '') }}"
                               style="background:rgba(255,255,255,.03);color:var(--text-dim);">
                    </div>
                    <div class="fgroup">
                        <label>Nama Dokter</label>
                        <input type="text" name="nama_dokter" value="{{ old('nama_dokter', $fpup->nama_dokter ?? '') }}" placeholder="dr. ...">
                    </div>
                    <div class="fgroup">
                        <label>Nama O.S (Alias)</label>
                        <input type="text" name="nama_os" value="{{ old('nama_os', $fpup->nama_os ?? '') }}" placeholder="Nama pasien di catatan">
                    </div>
                </div>
            </div>
        </div>

        {{-- ② Data Pasien --}}
        <div class="sec-card">
            <div class="sec-head" style="justify-content:space-between;">
                <span><i class="fas fa-user ico amber"></i> Data Pasien</span>
                <button type="button" class="btn-fp btn-teal btn-sm" data-bs-toggle="modal" data-bs-target="#modalPasien">
                    <i class="fas fa-search"></i> Cari / Tambah Pasien
                </button>
            </div>
            <div class="sec-body">
                <div class="fg fg-4">
                    <div class="fgroup" style="grid-column:span 2;">
                        <label>Nama Pasien <span class="req">*</span></label>
                        <input type="text" name="nama_pasien" id="nama_pasien_utama"
                               value="{{ old('nama_pasien', $fpup->nama_pasien ?? '') }}" required placeholder="Nama lengkap pasien">
                        @error('nama_pasien')<div class="err-msg">{{ $message }}</div>@enderror
                    </div>
                    <div class="fgroup">
                        <label>Nama Suami/Istri</label>
                        <input type="text" name="nama_suami_istri" value="{{ old('nama_suami_istri', $fpup->nama_suami_istri ?? '') }}">
                    </div>
                    <div class="fgroup">
                        <label>Kebangsaan</label>
                        <select name="kebangsaan" id="kebangsaan_utama">
                            <option value="">— Pilih —</option>
                            @foreach($kebangsaan as $k)
                                <option value="{{ $k }}" @selected(old('kebangsaan', $fpup->kebangsaan ?? '') === $k)>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="fg fg-4">
                    <div class="fgroup">
                        <label>Tgl Lahir</label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir"
                               value="{{ old('tgl_lahir', isset($fpup->tgl_lahir) ? $fpup->tgl_lahir->format('Y-m-d') : '') }}">
                    </div>
                    <div class="fgroup">
                        <label>Umur (Tahun)</label>
                        <input type="number" name="umur" id="umur" value="{{ old('umur', $fpup->umur ?? '') }}" min="0" max="150">
                    </div>
                    <div class="fgroup">
                        <label>Jenis Kelamin</label>
                        <div class="radio-group" style="padding-top:.55rem;">
                            <label><input type="radio" name="jenis_kelamin" value="Pria"   @checked(old('jenis_kelamin', $fpup->jenis_kelamin ?? '') === 'Pria')> Pria</label>
                            <label><input type="radio" name="jenis_kelamin" value="Wanita" @checked(old('jenis_kelamin', $fpup->jenis_kelamin ?? '') === 'Wanita')> Wanita</label>
                        </div>
                    </div>
                    <div class="fgroup">
                        <label>Alamat</label>
                        <input type="text" name="alamat" id="alamat_utama" value="{{ old('alamat', $fpup->alamat ?? '') }}" placeholder="Alamat lengkap">
                    </div>
                </div>

                {{-- Foto KTP terpasang (terisi otomatis dari modal pasien) --}}
                <div class="fg fg-1" id="fotoKtpTerpasangWrap" style="display:{{ ($fpup?->fpup?->foto_ktp_path ?? null) ? 'block' : 'none' }};margin-top:-.3rem;">
                    <div style="display:flex;align-items:center;gap:.6rem;background:var(--navy-2);border:1px solid var(--border);border-radius:8px;padding:.5rem .75rem;">
                        <img id="fotoKtpTerpasangImg"
                             src="{{ ($fpup?->fpup?->foto_ktp_path ?? null) ? \Illuminate\Support\Facades\Storage::url($fpup->fpup->foto_ktp_path) : '' }}"
                             style="height:46px;border-radius:6px;border:1px solid var(--border);">
                        <span style="font-size:.78rem;color:var(--text-dim);">Foto KTP terlampir</span>
                        <span id="fotoKtpVerifBadge">
                            @if($fpup?->fpup?->ocr_terverifikasi ?? false)
                                <span class="badge badge-selesai" style="background:rgba(0,168,150,.15);color:var(--teal);padding:.2rem .55rem;border-radius:5px;font-size:.68rem;">Terverifikasi</span>
                            @elseif($fpup?->fpup ?? null)
                                <span class="badge badge-baru" style="background:rgba(2,132,199,.15);color:var(--sky);padding:.2rem .55rem;border-radius:5px;font-size:.68rem;">Belum Diverifikasi</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Khusus Wanita --}}
                <div id="section-wanita" style="display:none;">
                    <div style="font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--amber);margin-bottom:.65rem;padding-top:.25rem;">
                        <i class="fas fa-venus"></i> Khusus Wanita
                    </div>
                    <div class="fg fg-3">
                        <div class="fgroup">
                            <label>Jml Kehamilan Sebelumnya</label>
                            <input type="number" name="jumlah_kehamilan" value="{{ old('jumlah_kehamilan', $fpup->jumlah_kehamilan ?? '') }}" min="0">
                        </div>
                        <div class="fgroup">
                            <label>Abortus</label>
                            <select name="abortus">
                                <option value="">—</option>
                                @foreach(['0','1','2','3','4','5+'] as $ab)
                                    <option value="{{ $ab }}" @selected(old('abortus', $fpup->abortus ?? '') === $ab)>{{ $ab }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="fgroup">
                            <label>Penyakit Hemolitik Bayi (HDN)?</label>
                            <div class="toggle-wrap" style="padding-top:.55rem;">
                                <label class="toggle">
                                    <input type="hidden" name="hdn" value="0">
                                    <input type="checkbox" name="hdn" value="1" @checked(old('hdn', $fpup->hdn ?? false))>
                                    <span class="slider"></span>
                                </label>
                                <span class="toggle-label">Ya / Tidak</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ③ Data Permintaan --}}
        <div class="sec-card">
            <div class="sec-head">
                <i class="fas fa-clipboard-list ico red"></i> Data Permintaan Transfusi
            </div>
            <div class="sec-body">
                <div class="fg fg-4">
                    <div class="fgroup">
                        <label>Jenis Permintaan</label>
                        <select name="jns_permintaan">
                            <option value="">— Pilih —</option>
                            @foreach($jns_permintaan as $jp)
                                <option value="{{ $jp }}" @selected(old('jns_permintaan', $fpup->jns_permintaan ?? '') === $jp)>{{ $jp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup ac-wrap" style="grid-column:span 2;">
                        <label>Diagnosa Klinis</label>
                        <input type="text" name="diagnosa_klinis" id="diagnosa_klinis"
                               value="{{ old('diagnosa_klinis', $fpup->diagnosa_klinis ?? '') }}"
                               placeholder="Ketik atau pilih diagnosa..."
                               autocomplete="off">
                        <div class="ac-dropdown" id="ac-diagnosa-dropdown"></div>
                    </div>
                    <div class="fgroup">
                        <label>Hb</label>
                        <input type="text" name="hb" value="{{ old('hb', $fpup->hb ?? '') }}" placeholder="g/dL">
                    </div>
                </div>
                <div class="fg fg-1">
                    <div class="fgroup">
                        <label>Alasan Transfusi</label>
                        <textarea name="alasan_transfusi" rows="2">{{ old('alasan_transfusi', $fpup->alasan_transfusi ?? '') }}</textarea>
                    </div>
                </div>

                <div class="fg fg-3" style="margin-bottom:.25rem;">
                    <div class="fgroup">
                        <label>Transfusi Sebelumnya?</label>
                        <div class="radio-group" style="padding-top:.55rem;">
                            <label>
                                <input type="radio" name="transfusi_sebelumnya" value="1"
                                    @checked(old('transfusi_sebelumnya', $fpup->transfusi_sebelumnya ?? null) == '1')>
                                Ya
                            </label>
                            <label>
                                <input type="radio" name="transfusi_sebelumnya" value="0"
                                    @checked(old('transfusi_sebelumnya', $fpup->transfusi_sebelumnya ?? null) == '0')>
                                Tidak
                            </label>
                        </div>
                    </div>
                </div>
                <div id="wrap-kapan-transfusi" style="display:none; margin-bottom:.9rem;">
                    <div class="cond-block">
                        <div class="fg fg-2" style="margin-bottom:0;">
                            <div class="fgroup">
                                <label>Kapan Terakhir Transfusi</label>
                                <input type="date" name="transfusi_kapan"
                                       value="{{ old('transfusi_kapan', isset($fpup->transfusi_kapan) ? $fpup->transfusi_kapan->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fg fg-3" style="margin-bottom:.25rem;">
                    <div class="fgroup">
                        <label>Reaksi Transfusi?</label>
                        <div class="radio-group" style="padding-top:.55rem;">
                            <label>
                                <input type="radio" name="reaksi_transfusi" value="1"
                                    @checked(old('reaksi_transfusi', $fpup->reaksi_transfusi ?? null) == '1')>
                                Ya
                            </label>
                            <label>
                                <input type="radio" name="reaksi_transfusi" value="0"
                                    @checked(old('reaksi_transfusi', $fpup->reaksi_transfusi ?? null) == '0')>
                                Tidak
                            </label>
                        </div>
                    </div>
                </div>
                <div id="wrap-gejala" style="display:none; margin-bottom:.9rem;">
                    <div class="cond-block danger">
                        <div class="fgroup" style="margin-bottom:0;">
                            <label>Gejala Reaksi Transfusi</label>
                            <textarea name="reaksi_gejala" rows="2">{{ old('reaksi_gejala', $fpup->reaksi_gejala ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="fg fg-4" style="margin-bottom:.25rem;">
                    <div class="fgroup">
                        <label>Pernah Serologi Gol Darah?</label>
                        <div class="radio-group" style="padding-top:.55rem;">
                            <label>
                                <input type="radio" name="pernah_serologi" value="1"
                                    @checked(old('pernah_serologi', $fpup->pernah_serologi ?? null) == '1')>
                                Ya
                            </label>
                            <label>
                                <input type="radio" name="pernah_serologi" value="0"
                                    @checked(old('pernah_serologi', $fpup->pernah_serologi ?? null) == '0')>
                                Tidak
                            </label>
                        </div>
                    </div>
                </div>
                <div id="wrap-serologi" style="display:none; margin-bottom:.9rem;">
                    <div class="cond-block">
                        <div class="fg fg-3" style="margin-bottom:0;">
                            <div class="fgroup">
                                <label>Kapan</label>
                                <input type="date" name="serologi_kapan"
                                       value="{{ old('serologi_kapan', isset($fpup->serologi_kapan) ? $fpup->serologi_kapan->format('Y-m-d') : '') }}">
                            </div>
                            <div class="fgroup">
                                <label>Dimana</label>
                                <input type="text" name="serologi_dimana"
                                       value="{{ old('serologi_dimana', $fpup->serologi_dimana ?? '') }}"
                                       placeholder="Nama RS / Lab">
                            </div>
                            <div class="fgroup">
                                <label>Hasil</label>
                                <input type="text" name="serologi_hasil"
                                       value="{{ old('serologi_hasil', $fpup->serologi_hasil ?? '') }}"
                                       placeholder="mis. A+, B-, dll">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ④ Detail Darah --}}
        <div class="sec-card">
            <div class="sec-head">
                <i class="fas fa-tint ico red"></i> Detail Permintaan Darah
                <button type="button" id="btn-add-row" class="btn-fp btn-teal btn-sm" style="margin-left:auto;">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>
            <div class="sec-body" style="padding:0;">
                <div class="detail-tbl-wrap" style="border-radius:0;border:none;">
                    <table class="detail-tbl" id="detail-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Jenis Darah</th>
                                <th>Gol Darah</th>
                                <th>Rhesus</th>
                                <th>Jumlah</th>
                                <th>CC</th>
                                <th>Tgl Perlu</th>
                                <th>Keterangan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="detail-tbody"></tbody>
                    </table>
                </div>
                <div id="empty-detail" style="text-align:center;padding:1.5rem;color:var(--muted);font-size:.82rem;">
                    <i class="fas fa-tint" style="opacity:.3;font-size:1.4rem;display:block;margin-bottom:.4rem;"></i>
                    Belum ada baris. Klik <strong>Tambah Baris</strong>.
                </div>
            </div>
        </div>

        {{-- ⑤ Cara Pembayaran & Donor --}}
        <div class="sec-card">
            <div class="sec-head">
                <i class="fas fa-wallet ico teal"></i> Cara Pembayaran & Donor
            </div>
            <div class="sec-body">
                <div class="fg fg-4">
                    <div class="fgroup">
                        <label>Cara Pembayaran</label>
                        <select name="cara_pembayaran">
                            <option value="">— Pilih —</option>
                            @foreach($cara_bayar as $cb)
                                <option value="{{ $cb }}" @selected(old('cara_pembayaran', $fpup->cara_pembayaran ?? '') === $cb)>{{ $cb }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup" style="grid-column:span 2;">
                        <label>Jenis Biaya</label>
                        <select name="jns_biaya">
                            <option value="">— Pilih —</option>
                            @foreach($jns_biaya as $jb)
                                <option value="{{ $jb }}" @selected(old('jns_biaya', $fpup->jns_biaya ?? '') === $jb)>{{ $jb }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Jml Donor</label>
                        <input type="number" name="jml_donor" value="{{ old('jml_donor', $fpup->jml_donor ?? 0) }}" min="0">
                    </div>
                </div>
                <div class="fg fg-2">
                    <div class="fgroup">
                        <label>Jenis Donor</label>
                        <div class="radio-group" style="padding-top:.55rem;">
                            @foreach($jns_donor as $jd)
                                <label>
                                    <input type="radio" name="jns_donor" value="{{ $jd }}"
                                        @checked(old('jns_donor', $fpup->jns_donor ?? 'Sukarela') === $jd)>
                                    {{ $jd }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="fgroup">
                        <label>Cetak Barcode?</label>
                        <div class="toggle-wrap" style="padding-top:.55rem;">
                            <label class="toggle">
                                <input type="hidden" name="cetak_barcode" value="0">
                                <input type="checkbox" name="cetak_barcode" value="1" @checked(old('cetak_barcode', $fpup->cetak_barcode ?? false))>
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label">Ya</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="form-footer">
            <div class="left">
                <i class="fas fa-info-circle" style="color:var(--muted);"></i>
                <span style="font-size:.78rem;color:var(--muted);">Field bertanda <span style="color:var(--red);">*</span> wajib diisi.</span>
            </div>
            <div class="right">
                <a href="{{ route('crossmatch.permintaan_fpup.index') }}" class="btn-fp btn-ghost">
                    <i class="fas fa-times"></i> Batal
                </a>
                <button type="submit" class="btn-fp btn-primary">
                    <i class="fas fa-save"></i> {{ $mode === 'create' ? 'Simpan FPUP' : 'Update FPUP' }}
                </button>
            </div>
        </div>

    </form>

    {{-- ══════════════════════════════════════════════════════
         MODAL PASIEN — Cari Pasien & Tambah Baru (dengan OCR)
         Di luar <form id="form-fpup"> agar form modal tidak
         bersarang dengan form utama.
         ══════════════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalPasien" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header" style="background:linear-gradient(90deg,var(--red),#e74c3c);color:#fff;">
            <h5 class="modal-title"><i class="fas fa-id-card me-1"></i> Data Pasien FPUP</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <ul class="nav nav-tabs mb-3" id="pasienTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-cari-btn" data-bs-toggle="tab" data-bs-target="#tab-cari" type="button">
                    <i class="fas fa-search me-1"></i>Cari Pasien
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-baru-btn" data-bs-toggle="tab" data-bs-target="#tab-baru" type="button">
                    <i class="fas fa-user-plus me-1"></i>Tambah Baru
                </button>
              </li>
            </ul>

            <div class="tab-content">

              {{-- TAB CARI --}}
              <div class="tab-pane fade show active" id="tab-cari" role="tabpanel">
                <div class="fgroup mb-2">
                    <label>Cari berdasarkan Nama / No KTP</label>
                    <input type="text" id="inputCariPasien" class="form-control" placeholder="Ketik minimal 3 huruf..." autocomplete="off">
                </div>
                <div id="hasilCariPasien" class="list-group" style="max-height:320px;overflow:auto"></div>
                <div id="cariPasienEmpty" class="text-muted text-center py-3" style="display:none">
                    <i class="fas fa-inbox"></i> Tidak ada data ditemukan.
                </div>
              </div>

              {{-- TAB TAMBAH BARU --}}
              <div class="tab-pane fade" id="tab-baru" role="tabpanel">
                <div class="cond-block mb-3">
                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;color:var(--red);margin-bottom:.6rem;">
                        <i class="fas fa-camera"></i> Upload Foto KTP (Opsional, untuk OCR)
                    </div>
                    <div class="d-flex gap-2 align-items-end">
                        <input type="file" id="inputFotoKtp" class="form-control" accept="image/png,image/jpeg" style="max-width:280px;">
                        <button type="button" id="btnProsesOcr" class="btn-fp btn-teal btn-sm"><i class="fas fa-magic"></i> Proses OCR</button>
                        <img id="previewFotoKtp" src="" style="display:none;height:46px;border-radius:6px;border:1px solid var(--border);">
                    </div>
                    <div class="hint">Format JPG/PNG, maks 5MB. Pastikan foto jelas & tidak buram.</div>
                    <div id="ocrStatus" class="mt-2" style="display:none;font-size:.8rem;"></div>
                </div>

                <form id="formPasienBaru">
                    <input type="hidden" id="pb_foto_ktp_path" name="foto_ktp_path" value="">
                    <input type="hidden" id="pb_ocr_raw_result" name="ocr_raw_result" value="">

                    <div class="fg fg-2">
                        <div class="fgroup">
                            <label>Nama Pasien <span class="req">*</span></label>
                            <input type="text" id="pb_nama_pasien" name="nama_pasien" class="form-control" required>
                        </div>
                        <div class="fgroup">
                            <label>No KTP / NIK</label>
                            <input type="text" id="pb_no_ktp" name="no_ktp" class="form-control" maxlength="16">
                        </div>
                    </div>
                    <div class="fg fg-3">
                        <div class="fgroup">
                            <label>Tgl Lahir</label>
                            <input type="date" id="pb_tgl_lahir" name="tgl_lahir" class="form-control">
                        </div>
                        <div class="fgroup">
                            <label>Umur</label>
                            <input type="number" id="pb_umur" name="umur" class="form-control" min="0">
                        </div>
                        <div class="fgroup">
                            <label>Jenis Kelamin</label>
                            <select id="pb_jenis_kelamin" name="jenis_kelamin" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="Pria">Pria</option>
                                <option value="Wanita">Wanita</option>
                            </select>
                        </div>
                    </div>
                    <div class="fg fg-2">
                        <div class="fgroup">
                            <label>Kebangsaan</label>
                            <input type="text" id="pb_kebangsaan" name="kebangsaan" class="form-control" value="INDONESIA">
                        </div>
                        <div class="fgroup">
                            <label>No Telp</label>
                            <input type="text" id="pb_no_telp" name="no_telp" class="form-control">
                        </div>
                    </div>
                    <div class="fg fg-1">
                        <div class="fgroup">
                            <label>Alamat</label>
                            <textarea id="pb_alamat" name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="fg fg-2">
                        <div class="fgroup">
                            <label>Nama Dokter</label>
                            <input type="text" id="pb_nama_dokter" name="nama_dokter" class="form-control">
                        </div>
                        <div class="fgroup">
                            <label>Nama Instansi / RS Perujuk</label>
                            <input type="text" id="pb_nama_instansi" name="nama_instansi" class="form-control">
                        </div>
                    </div>

                    <div class="fgroup" style="margin-top:.3rem;">
                        <label style="text-transform:none;font-weight:500;display:flex;align-items:center;gap:.4rem;">
                            <input type="checkbox" id="pb_ocr_terverifikasi" name="ocr_terverifikasi" value="1">
                            Saya telah memverifikasi foto KTP sesuai dengan data di atas
                        </label>
                    </div>

                    <div id="pasienBaruAlert" class="err-msg" style="display:none;margin-top:.5rem;"></div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn-fp btn-primary"><i class="fas fa-save"></i> Simpan & Gunakan Pasien Ini</button>
                    </div>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ════════════════════════════════════════════
       URL API (dari route Laravel)
    ════════════════════════════════════════════ */
    const URL_GEN_NO    = '{{ route("crossmatch.permintaan_fpup.next-no-fpup") }}';
    const URL_SEARCH_RS = '{{ route("crossmatch.permintaan_fpup.search-rs") }}';
    const URL_RS_KODE   = '{{ route("crossmatch.permintaan_fpup.rs-by-kode") }}';
    const URL_DIAGNOSA  = '{{ route("crossmatch.permintaan_fpup.diagnosa") }}';

    /* ════════════════════════════════════════════
       Constants dari PHP
    ════════════════════════════════════════════ */
    const JNS_DARAH = @json($jns_darah);
    const GOL_DARAH = @json($gol_darah);
    const RHESUS    = @json($rhesus);

    const existingDetails = @json(isset($fpup) && $fpup ? $fpup->details->toArray() : []);

    function debounce(fn, ms) {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    }

    /* ════════════════════════════════════════════
       Generate No FPUP
    ════════════════════════════════════════════ */
    const btnGen = document.getElementById('btn-gen-no');
    if (btnGen) {
        btnGen.addEventListener('click', async () => {
            btnGen.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            try {
                const res  = await fetch(URL_GEN_NO);
                const data = await res.json();
                document.getElementById('display-no-fpup').textContent = data.no_fpup;
                document.getElementById('no_fpup_hidden').value = data.no_fpup;
            } catch { alert('Gagal generate nomor'); }
            finally { btnGen.innerHTML = '<i class="fas fa-sync-alt"></i> Generate No FPUP'; }
        });
        btnGen.click();
    }

    /* ════════════════════════════════════════════
       Auto hitung umur dari tgl lahir
    ════════════════════════════════════════════ */
    document.getElementById('tgl_lahir').addEventListener('change', function () {
        if (!this.value) return;
        const birth = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        document.getElementById('umur').value = age;
    });

    /* ════════════════════════════════════════════
       Tampil/sembunyi section wanita
    ════════════════════════════════════════════ */
    function toggleWanita() {
        const checked = document.querySelector('input[name=jenis_kelamin]:checked');
        document.getElementById('section-wanita').style.display =
            (checked && checked.value === 'Wanita') ? 'block' : 'none';
    }
    document.querySelectorAll('input[name=jenis_kelamin]').forEach(el =>
        el.addEventListener('change', toggleWanita));
    toggleWanita();

    function setSelectValue(sel, val) {
        if (!sel || !val) return;
        const opt = [...sel.options].find(o => o.value === val);
        if (opt) sel.value = val;
    }

    /* ════════════════════════════════════════════
       Fill RS fields dari data API
    ════════════════════════════════════════════ */
    function fillRsFields(data) {
        document.getElementById('kode_rs').value = data.kode_rs  || '';
        document.getElementById('nama_rs').value = data.nama_rs  || '';
        setSelectValue(document.getElementById('jenis_rs'),    data.jenis_rs    || '');
        setSelectValue(document.getElementById('kategori_rs'), data.kategori_rs || '');
        document.getElementById('kelompok_rs_display').value = data.kelompok_nama || '';
    }

    /* ════════════════════════════════════════════
       AUTOCOMPLETE: Nama RS
    ════════════════════════════════════════════ */
    const inputNamaRs   = document.getElementById('nama_rs');
    const dropdownRs    = document.getElementById('ac-rs-dropdown');
    let rsResults       = [];
    let rsFocusIndex    = -1;

    function renderRsDropdown(items) {
        if (!items.length) { dropdownRs.classList.remove('open'); return; }
        dropdownRs.innerHTML = items.map((r, i) => `
            <div class="ac-item" data-idx="${i}">
                <span class="ac-kode">${r.kode_rs}</span>${r.nama_rs}
                <div class="ac-sub">${r.jenis_rs || ''} ${r.kategori_rs ? '· ' + r.kategori_rs : ''} ${r.kelompok_nama ? '· ' + r.kelompok_nama : ''}</div>
            </div>`).join('');
        dropdownRs.classList.add('open');
        rsFocusIndex = -1;
    }

    async function fetchRs(q) {
        if (q.length < 2) { dropdownRs.classList.remove('open'); return; }
        dropdownRs.innerHTML = '<div class="ac-loading"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';
        dropdownRs.classList.add('open');
        try {
            const res  = await fetch(`${URL_SEARCH_RS}?q=${encodeURIComponent(q)}`);
            rsResults  = await res.json();
            renderRsDropdown(rsResults);
        } catch { dropdownRs.classList.remove('open'); }
    }

    inputNamaRs.addEventListener('input', debounce(e => fetchRs(e.target.value), 300));

    inputNamaRs.addEventListener('keydown', e => {
        const items = dropdownRs.querySelectorAll('.ac-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); rsFocusIndex = Math.min(rsFocusIndex + 1, items.length - 1); items.forEach((el, i) => el.classList.toggle('focused', i === rsFocusIndex)); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); rsFocusIndex = Math.max(rsFocusIndex - 1, 0); items.forEach((el, i) => el.classList.toggle('focused', i === rsFocusIndex)); }
        else if (e.key === 'Enter' && rsFocusIndex >= 0) { e.preventDefault(); fillRsFields(rsResults[rsFocusIndex]); dropdownRs.classList.remove('open'); }
        else if (e.key === 'Escape') { dropdownRs.classList.remove('open'); }
    });

    dropdownRs.addEventListener('click', e => {
        const item = e.target.closest('.ac-item');
        if (!item) return;
        fillRsFields(rsResults[parseInt(item.dataset.idx)]);
        dropdownRs.classList.remove('open');
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.ac-wrap')) dropdownRs.classList.remove('open');
    });

    const inputKodeRs = document.getElementById('kode_rs');
    inputKodeRs.addEventListener('blur', async function () {
        const kode = this.value.trim();
        if (!kode || inputNamaRs.value) return;
        try {
            const res  = await fetch(`${URL_RS_KODE}?kode=${encodeURIComponent(kode)}`);
            const data = await res.json();
            if (data.found) fillRsFields(data);
        } catch {}
    });

    /* ════════════════════════════════════════════
       AUTOCOMPLETE: Diagnosa Klinis
    ════════════════════════════════════════════ */
    const inputDiagnosa    = document.getElementById('diagnosa_klinis');
    const dropdownDiagnosa = document.getElementById('ac-diagnosa-dropdown');
    let diagnosaResults    = [];
    let diagnosaFocusIdx   = -1;

    function renderDiagnosaDropdown(items) {
        if (!items.length) { dropdownDiagnosa.classList.remove('open'); return; }
        dropdownDiagnosa.innerHTML = items.map((d, i) => `
            <div class="ac-item" data-idx="${i}">
                ${d.kode ? `<span class="ac-kode">${d.kode}</span>` : ''}${d.nama}
            </div>`).join('');
        dropdownDiagnosa.classList.add('open');
        diagnosaFocusIdx = -1;
    }

    async function fetchDiagnosa(q) {
        dropdownDiagnosa.innerHTML = '<div class="ac-loading"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>';
        dropdownDiagnosa.classList.add('open');
        try {
            const res     = await fetch(`${URL_DIAGNOSA}?q=${encodeURIComponent(q)}`);
            diagnosaResults = await res.json();
            renderDiagnosaDropdown(diagnosaResults);
        } catch { dropdownDiagnosa.classList.remove('open'); }
    }

    inputDiagnosa.addEventListener('focus', () => fetchDiagnosa(inputDiagnosa.value));
    inputDiagnosa.addEventListener('input', debounce(e => fetchDiagnosa(e.target.value), 250));

    inputDiagnosa.addEventListener('keydown', e => {
        const items = dropdownDiagnosa.querySelectorAll('.ac-item');
        if (!items.length) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); diagnosaFocusIdx = Math.min(diagnosaFocusIdx + 1, items.length - 1); items.forEach((el, i) => el.classList.toggle('focused', i === diagnosaFocusIdx)); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); diagnosaFocusIdx = Math.max(diagnosaFocusIdx - 1, 0); items.forEach((el, i) => el.classList.toggle('focused', i === diagnosaFocusIdx)); }
        else if (e.key === 'Enter' && diagnosaFocusIdx >= 0) { e.preventDefault(); inputDiagnosa.value = diagnosaResults[diagnosaFocusIdx].nama; dropdownDiagnosa.classList.remove('open'); }
        else if (e.key === 'Escape') { dropdownDiagnosa.classList.remove('open'); }
    });

    dropdownDiagnosa.addEventListener('click', e => {
        const item = e.target.closest('.ac-item');
        if (!item) return;
        inputDiagnosa.value = diagnosaResults[parseInt(item.dataset.idx)].nama;
        dropdownDiagnosa.classList.remove('open');
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('#diagnosa_klinis') && !e.target.closest('#ac-diagnosa-dropdown'))
            dropdownDiagnosa.classList.remove('open');
    });

    /* ════════════════════════════════════════════
       CONDITIONAL FIELDS
    ════════════════════════════════════════════ */
    function toggleKapanTransfusi() {
        const val = document.querySelector('input[name=transfusi_sebelumnya]:checked')?.value;
        const wrap = document.getElementById('wrap-kapan-transfusi');
        wrap.style.display = (val === '1') ? 'block' : 'none';
        const inp = wrap.querySelector('input[name=transfusi_kapan]');
        if (val !== '1') inp.value = '';
    }
    document.querySelectorAll('input[name=transfusi_sebelumnya]').forEach(el =>
        el.addEventListener('change', toggleKapanTransfusi));
    toggleKapanTransfusi();

    function toggleGejala() {
        const val  = document.querySelector('input[name=reaksi_transfusi]:checked')?.value;
        const wrap = document.getElementById('wrap-gejala');
        wrap.style.display = (val === '1') ? 'block' : 'none';
        if (val !== '1') wrap.querySelector('textarea[name=reaksi_gejala]').value = '';
    }
    document.querySelectorAll('input[name=reaksi_transfusi]').forEach(el =>
        el.addEventListener('change', toggleGejala));
    toggleGejala();

    function toggleSerologi() {
        const val  = document.querySelector('input[name=pernah_serologi]:checked')?.value;
        const wrap = document.getElementById('wrap-serologi');
        wrap.style.display = (val === '1') ? 'block' : 'none';
        if (val !== '1') {
            wrap.querySelectorAll('input').forEach(i => i.value = '');
        }
    }
    document.querySelectorAll('input[name=pernah_serologi]').forEach(el =>
        el.addEventListener('change', toggleSerologi));
    toggleSerologi();

    /* ════════════════════════════════════════════
       DETAIL DARAH
    ════════════════════════════════════════════ */
    const tbody     = document.getElementById('detail-tbody');
    const emptyDiv  = document.getElementById('empty-detail');
    const detailTbl = document.getElementById('detail-table');
    let   detailRows = [];

    function renderDetailRows() {
        tbody.innerHTML = '';
        const show = detailRows.length > 0;
        emptyDiv.style.display  = show ? 'none'  : 'block';
        detailTbl.style.display = show ? 'table' : 'none';

        detailRows.forEach((row, i) => {
            const jnsOpts = Array.isArray(JNS_DARAH)
                ? JNS_DARAH.map(k => `<option value="${k}" ${row.jns_darah === k ? 'selected' : ''}>${k}</option>`).join('')
                : Object.entries(JNS_DARAH).map(([k, v]) => `<option value="${k}" ${row.jns_darah === k ? 'selected' : ''}>${k} – ${v}</option>`).join('');

            const golOpts = ['', ...GOL_DARAH].map(g =>
                `<option value="${g}" ${row.gol_darah === g ? 'selected' : ''}>${g || '—'}</option>`).join('');
            const rhsOpts = ['', ...RHESUS].map(r =>
                `<option value="${r}" ${row.rhesus === r ? 'selected' : ''}>${r || '—'}</option>`).join('');

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td style="color:var(--muted);font-family:var(--mono);font-size:.75rem;">${i+1}</td>
                    <td><select name="details[${i}][jns_darah]" onchange="updateDetail(${i},'jns_darah',this.value)" style="min-width:130px;">
                        <option value="">—</option>${jnsOpts}
                    </select></td>
                    <td><select name="details[${i}][gol_darah]" onchange="updateDetail(${i},'gol_darah',this.value)" style="width:70px;">${golOpts}</select></td>
                    <td><select name="details[${i}][rhesus]" onchange="updateDetail(${i},'rhesus',this.value)" style="width:100px;">${rhsOpts}</select></td>
                    <td><input type="number" name="details[${i}][jumlah]" value="${row.jumlah||1}" min="1" style="width:60px;" onchange="updateDetail(${i},'jumlah',this.value)"></td>
                    <td><input type="number" name="details[${i}][cc]" value="${row.cc||''}" min="0" style="width:70px;" onchange="updateDetail(${i},'cc',this.value)" placeholder="cc"></td>
                    <td><input type="date" name="details[${i}][tgl_perlu]" value="${row.tgl_perlu||''}" onchange="updateDetail(${i},'tgl_perlu',this.value)" style="width:140px;"></td>
                    <td><input type="text" name="details[${i}][keterangan]" value="${row.keterangan||''}" placeholder="Opsional..." onchange="updateDetail(${i},'keterangan',this.value)" style="min-width:110px;"></td>
                    <td><button type="button" onclick="removeDetail(${i})" class="btn-fp btn-danger btn-sm"><i class="fas fa-times"></i></button></td>
                </tr>`);
        });
    }

    window.updateDetail = (i, key, val) => { detailRows[i][key] = val; };
    window.removeDetail = (i) => { detailRows.splice(i, 1); renderDetailRows(); };

    document.getElementById('btn-add-row').addEventListener('click', () => {
        detailRows.push({ jns_darah:'', gol_darah:'', rhesus:'', jumlah:1, cc:'', tgl_perlu:'', keterangan:'' });
        renderDetailRows();
    });

    if (existingDetails.length > 0) {
        detailRows = existingDetails.map(d => ({
            jns_darah  : d.jns_darah  || '',
            gol_darah  : d.gol_darah  || '',
            rhesus     : d.rhesus     || '',
            jumlah     : d.jumlah     || 1,
            cc         : d.cc         || '',
            tgl_perlu  : d.tgl_perlu  ? d.tgl_perlu.substring(0, 10) : '',
            keterangan : d.keterangan || '',
        }));
    }
    renderDetailRows();
    detailTbl.style.display = detailRows.length > 0 ? 'table' : 'none';

    /* ════════════════════════════════════════════
       MODAL PASIEN — Cari / Tambah Baru + OCR KTP
    ════════════════════════════════════════════ */
    (function () {
        const modalEl = document.getElementById('modalPasien');
        if (!modalEl) return;

        const urlCari  = '{{ route("crossmatch.permintaan_fpup.pasien.cari") }}';
        const urlShow  = '{{ route("crossmatch.permintaan_fpup.pasien.show", ["id" => "__ID__"]) }}';
        const urlOcr   = '{{ route("crossmatch.permintaan_fpup.pasien.ocr-preview") }}';
        const urlStore = '{{ route("crossmatch.permintaan_fpup.pasien.store") }}';
        const csrfToken = '{{ csrf_token() }}';

        let timerCari = null;

        document.getElementById('inputCariPasien').addEventListener('input', function () {
            clearTimeout(timerCari);
            const q = this.value.trim();
            const hasilEl = document.getElementById('hasilCariPasien');
            const emptyEl = document.getElementById('cariPasienEmpty');
            if (q.length < 3) { hasilEl.innerHTML = ''; emptyEl.style.display = 'none'; return; }

            timerCari = setTimeout(() => {
                fetch(`${urlCari}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(json => {
                        hasilEl.innerHTML = '';
                        const data = json.data || [];
                        emptyEl.style.display = data.length ? 'none' : 'block';
                        data.forEach(p => {
                            const item = document.createElement('button');
                            item.type = 'button';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML = `<div style="display:flex;justify-content:space-between;">
                                    <strong>${p.nama_pasien}</strong>
                                    ${p.ocr_terverifikasi
                                        ? '<span style="background:rgba(0,168,150,.15);color:var(--teal);padding:.15rem .5rem;border-radius:5px;font-size:.68rem;">Terverifikasi</span>'
                                        : '<span style="background:rgba(2,132,199,.15);color:var(--sky);padding:.15rem .5rem;border-radius:5px;font-size:.68rem;">Belum Verifikasi</span>'}
                                </div>
                                <div style="font-size:.78rem;color:var(--text-dim);">
                                    NIK: ${p.no_ktp ?? '—'} · ${p.jenis_kelamin ?? '—'} · ${p.alamat ? p.alamat.substring(0,40) : '—'}
                                </div>`;
                            item.addEventListener('click', () => pilihPasien(p.id));
                            hasilEl.appendChild(item);
                        });
                    });
            }, 350);
        });

        function pilihPasien(id) {
            fetch(urlShow.replace('__ID__', id))
                .then(r => r.json())
                .then(json => {
                    if (!json.success) { alert(json.message || 'Pasien tidak ditemukan'); return; }
                    isiFormUtama(json.data);
                    bootstrap.Modal.getInstance(modalEl)?.hide();
                });
        }

        document.getElementById('inputFotoKtp').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const preview = document.getElementById('previewFotoKtp');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'inline-block';
        });

        document.getElementById('btnProsesOcr').addEventListener('click', function () {
            const file = document.getElementById('inputFotoKtp').files[0];
            const statusEl = document.getElementById('ocrStatus');
            if (!file) { alert('Pilih foto KTP terlebih dahulu.'); return; }

            const fd = new FormData();
            fd.append('foto_ktp', file);
            fd.append('_token', csrfToken);

            statusEl.style.display = 'block';
            statusEl.style.color = 'var(--muted)';
            statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses OCR...';
            this.disabled = true;
            const btn = this;

            fetch(urlOcr, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(json => {
                    btn.disabled = false;
                    if (!json.success) {
                        statusEl.style.color = 'var(--red)';
                        statusEl.innerHTML = `<i class="fas fa-times-circle"></i> ${json.message}`;
                        return;
                    }
                    document.getElementById('pb_foto_ktp_path').value = json.foto_path;
                    document.getElementById('pb_ocr_raw_result').value = json.raw_text;

                    const p = json.parsed || {};
                    if (p.nama_pasien) document.getElementById('pb_nama_pasien').value = p.nama_pasien;
                    if (p.no_ktp)      document.getElementById('pb_no_ktp').value = p.no_ktp;
                    if (p.alamat)      document.getElementById('pb_alamat').value = p.alamat;
                    if (p.kebangsaan)  document.getElementById('pb_kebangsaan').value = p.kebangsaan;

                    statusEl.style.color = json.low_confidence ? 'var(--amber)' : 'var(--teal)';
                    statusEl.innerHTML = `<i class="fas fa-${json.low_confidence ? 'exclamation-triangle' : 'check-circle'}"></i> ${json.message}`;
                })
                .catch(() => {
                    btn.disabled = false;
                    statusEl.style.color = 'var(--red)';
                    statusEl.innerHTML = '<i class="fas fa-times-circle"></i> Terjadi kesalahan saat memproses OCR.';
                });
        });

        document.getElementById('formPasienBaru').addEventListener('submit', function (e) {
            e.preventDefault();
            const alertEl = document.getElementById('pasienBaruAlert');
            alertEl.style.display = 'none';

            const payload = {
                nama_pasien:       document.getElementById('pb_nama_pasien').value,
                no_ktp:            document.getElementById('pb_no_ktp').value || null,
                tgl_lahir:         document.getElementById('pb_tgl_lahir').value || null,
                umur:              document.getElementById('pb_umur').value || null,
                jenis_kelamin:     document.getElementById('pb_jenis_kelamin').value || null,
                kebangsaan:        document.getElementById('pb_kebangsaan').value || null,
                no_telp:           document.getElementById('pb_no_telp').value || null,
                alamat:            document.getElementById('pb_alamat').value || null,
                nama_dokter:       document.getElementById('pb_nama_dokter').value || null,
                nama_instansi:     document.getElementById('pb_nama_instansi').value || null,
                foto_ktp_path:     document.getElementById('pb_foto_ktp_path').value || null,
                ocr_raw_result:    document.getElementById('pb_ocr_raw_result').value || null,
                ocr_terverifikasi: document.getElementById('pb_ocr_terverifikasi').checked ? 1 : 0,
            };

            fetch(urlStore, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            })
                .then(res => res.json().then(json => ({ status: res.status, json })))
                .then(({ status, json }) => {
                    if (status === 422) {
                        const msgs = Object.values(json.errors || {}).flat().join('<br>') || json.message;
                        alertEl.innerHTML = msgs;
                        alertEl.style.display = 'block';
                        return;
                    }
                    if (!json.success) { alertEl.innerHTML = json.message; alertEl.style.display = 'block'; return; }

                    isiFormUtama(json.data);
                    bootstrap.Modal.getInstance(modalEl)?.hide();
                })
                .catch(() => {
                    alertEl.innerHTML = 'Terjadi kesalahan saat menyimpan data pasien.';
                    alertEl.style.display = 'block';
                });
        });

        function isiFormUtama(p) {
            setVal('#nama_pasien_utama', p.nama_pasien);
            setVal('#tgl_lahir',         p.tgl_lahir ? String(p.tgl_lahir).substring(0, 10) : '');
            setVal('#umur',              p.umur);
            setVal('#alamat_utama',      p.alamat);

            const selKb = document.getElementById('kebangsaan_utama');
            if (selKb && p.kebangsaan) selKb.value = p.kebangsaan;

            if (p.jenis_kelamin === 'Pria' || p.jenis_kelamin === 'Wanita') {
                const r = document.querySelector(`input[name="jenis_kelamin"][value="${p.jenis_kelamin}"]`);
                if (r) { r.checked = true; r.dispatchEvent(new Event('change')); }
            }

            document.getElementById('fpup_id_hidden').value = p.id;

            const wrap = document.getElementById('fotoKtpTerpasangWrap');
            if (p.foto_ktp_path) {
                document.getElementById('fotoKtpTerpasangImg').src = '/storage/' + p.foto_ktp_path;
                document.getElementById('fotoKtpVerifBadge').innerHTML = p.ocr_terverifikasi
                    ? '<span style="background:rgba(0,168,150,.15);color:var(--teal);padding:.2rem .55rem;border-radius:5px;font-size:.68rem;">Terverifikasi</span>'
                    : '<span style="background:rgba(2,132,199,.15);color:var(--sky);padding:.2rem .55rem;border-radius:5px;font-size:.68rem;">Belum Diverifikasi</span>';
                wrap.style.display = 'block';
            } else {
                wrap.style.display = 'none';
            }
        }

        function setVal(sel, val) {
            const el = document.querySelector(sel);
            if (el && val !== undefined && val !== null) el.value = val;
        }
    })();

});
/* ════════════════════════════════════════════
   Auto hitung umur dari tgl lahir (reusable)
════════════════════════════════════════════ */
function hitungUmurDariTanggal(tglLahirValue) {
    if (!tglLahirValue) return null;
    const birth = new Date(tglLahirValue);
    if (isNaN(birth)) return null;

    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;

    return age >= 0 ? age : null;
}

// Form utama
document.getElementById('tgl_lahir').addEventListener('change', function () {
    const age = hitungUmurDariTanggal(this.value);
    if (age !== null) document.getElementById('umur').value = age;
});

// Modal "Tambah Baru" pasien
document.getElementById('pb_tgl_lahir')?.addEventListener('change', function () {
    const age = hitungUmurDariTanggal(this.value);
    if (age !== null) document.getElementById('pb_umur').value = age;
});
</script>
@endpush