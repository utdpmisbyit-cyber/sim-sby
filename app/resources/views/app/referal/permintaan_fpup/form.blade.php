@extends('layouts.index')

@section('title',
    $record
        ? (($readOnly ?? false) ? 'Detail' : 'Edit')
        : 'Tambah'
)

@push('styles')
<style>
    /* ── CSS Variables hardcoded ── */
    :root {
        --utd-red      : #c0392b;
        --utd-red-light: #fdecea;
        --utd-dark     : #1a1a2e;
        --utd-blue     : #2980b9;
        --utd-gray     : #7f8c8d;
        --utd-border   : #e8ecef;
    }

    * { box-sizing: border-box; }
    body { background: #f4f6f9; font-size: .83rem; font-family: 'Segoe UI', system-ui, sans-serif; }

    /* ── Top Bar ── */
    .top-bar {
        height      : 56px;
        background  : linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #c0392b 100%);
        display     : flex;
        align-items : center;
        padding     : 0 1.25rem;
        gap         : .75rem;
        position    : sticky;
        top         : 0;
        z-index     : 200;
        box-shadow  : 0 2px 10px rgba(0,0,0,.3);
    }
    .back-btn {
        color          : rgba(255,255,255,.85);
        text-decoration: none;
        display        : flex;
        align-items    : center;
        gap            : .35rem;
        font-size      : .82rem;
        font-weight    : 500;
    }
    .back-btn:hover { color: #fff; }
    .top-bar .divider { width:1px; height:24px; background:rgba(255,255,255,.2); }
    .top-bar .ttl { color:#fff; font-weight:700; font-size:.92rem; }
    .top-bar .sub { color:rgba(255,255,255,.6); font-size:.71rem; letter-spacing:.3px; }
    .top-bar .spacer { flex:1; }
    .nfbdg {
        background   : rgba(255,255,255,.12);
        border       : 1px solid rgba(255,255,255,.25);
        color        : #fff;
        font-family  : 'Courier New', monospace;
        font-size    : .8rem;
        font-weight  : 700;
        padding      : .22rem .7rem;
        border-radius: 7px;
        letter-spacing: .5px;
        white-space  : nowrap;
    }

    /* ── Layout ── */
    .form-wrapper { max-width: 1200px; margin: 1.25rem auto; padding: 0 1.1rem 2rem; }

    /* ── Section Cards ── */
    .sec-card {
        background   : #fff;
        border       : 1px solid var(--utd-border);
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow     : hidden;
        box-shadow   : 0 1px 4px rgba(0,0,0,.04);
    }
    .sec-head {
        padding       : .55rem 1rem;
        font-weight   : 700;
        font-size     : .76rem;
        text-transform: uppercase;
        letter-spacing: .7px;
        display       : flex;
        align-items   : center;
        gap           : .4rem;
        color         : #fff;
    }
    .sec-head.red  { background: linear-gradient(90deg, #c0392b, #e74c3c); }
    .sec-head.blue { background: linear-gradient(90deg, #1a5276, #2980b9); }
    .sec-head.dark { background: linear-gradient(90deg, #1c2833, #2c3e50); }
    .sec-body      { padding: .9rem 1rem; }
    .sec-body-blue { padding: .9rem 1rem; background: #eaf4fb; }

    /* ── Form Fields ── */
    .form-label {
        font-size     : .74rem;
        font-weight   : 600;
        color         : #4a5568;
        margin-bottom : .22rem;
        letter-spacing: .15px;
        display       : block;
    }
    .form-control,
    .form-select {
        font-size    : .82rem;
        border       : 1px solid #dde1e7;
        border-radius: 7px;
        height       : 34px;
        padding      : .25rem .65rem;
        width        : 100%;
        color        : #2c3e50;
        background   : #fff;
        transition   : border-color .15s, box-shadow .15s;
    }
    textarea.form-control { height: auto; min-height: 58px; resize: vertical; }
    .form-control:focus,
    .form-select:focus {
        border-color: #c0392b;
        box-shadow  : 0 0 0 .18rem rgba(192,57,43,.14);
        outline     : none;
    }
    .form-control[readonly] { background: #f7f8fa; color: #6c757d; }
    .form-control.is-invalid,
    .form-select.is-invalid { border-color: #dc3545 !important; }
    .invalid-feedback { font-size: .72rem; color: #dc3545; margin-top: .2rem; display: block; }

    /* ── Radio Groups ── */
    .radio-group { display:flex; gap:.7rem; align-items:center; flex-wrap:wrap; padding-top:.15rem; }
    .radio-group .form-check { margin:0; display:flex; align-items:center; gap:.3rem; }
    .form-check-input { cursor:pointer; }
    .form-check-input:checked { background-color:#c0392b; border-color:#c0392b; }
    .form-check-label { font-size:.8rem; cursor:pointer; }

    /* ── Sub-section boxes ── */
    .sub-box {
        border-radius: 9px;
        padding      : .75rem;
        border       : 1px solid;
    }
    .sub-box-pink   { background:#fdf3f3; border-color:#f5c6cb; }
    .sub-box-danger { background:#fff3f3; border-color:#f5b7b1; }
    .sub-box-title  {
        font-size     : .72rem;
        font-weight   : 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom : .6rem;
        display       : flex;
        align-items   : center;
        gap           : .35rem;
    }

    /* ── Detail Darah Table ── */
    .detail-table { margin-bottom: 0; }
    .detail-table th {
        background    : #f8f9fa;
        font-size     : .71rem;
        text-transform: uppercase;
        color         : #6c757d;
        letter-spacing: .45px;
        font-weight   : 700;
        padding       : .48rem .5rem;
        white-space   : nowrap;
        border-bottom : 2px solid #eaecef;
    }
    .detail-table td { padding:.3rem .4rem; vertical-align:middle; border-bottom:1px solid #f0f2f4; }
    .detail-table .form-control,
    .detail-table .form-select { height:30px; font-size:.78rem; padding:.1rem .4rem; }

    .btn-del-row {
        width          : 26px;
        height         : 26px;
        border         : 1px solid #e0e0e0;
        border-radius  : 6px;
        background     : #fff;
        color          : #c0392b;
        display        : inline-flex;
        align-items    : center;
        justify-content: center;
        cursor         : pointer;
        font-size      : .82rem;
        transition     : all .15s;
    }
    .btn-del-row:hover { background:#fdecea; border-color:#c0392b; }

    /* ── Action Bar ── */
    .action-bar {
        background   : #fff;
        border       : 1px solid var(--utd-border);
        border-radius: 12px;
        padding      : .85rem 1rem;
        display      : flex;
        gap          : .5rem;
        flex-wrap    : wrap;
        align-items  : center;
        margin-bottom: 1.5rem;
        box-shadow   : 0 1px 4px rgba(0,0,0,.04);
    }
    .btn-save {
        background   : #c0392b;
        color        : #fff;
        border       : none;
        border-radius: 8px;
        padding      : .48rem 1.3rem;
        font-size    : .85rem;
        font-weight  : 700;
        display      : flex;
        align-items  : center;
        gap          : .4rem;
        cursor       : pointer;
        transition   : background .2s;
    }
    .btn-save:hover { background:#a93226; }
    .btn-cancel-link {
        border-radius  : 8px;
        padding        : .48rem 1.1rem;
        font-size      : .84rem;
        border         : 1px solid #dde1e7;
        background     : #fff;
        color          : #555;
        text-decoration: none;
        display        : inline-flex;
        align-items    : center;
        gap            : .3rem;
        transition     : background .15s;
    }
    .btn-cancel-link:hover { background:#f4f6f9; color:#333; }
    .btn-add-row {
        border-radius: 7px;
        padding      : .32rem .85rem;
        font-size    : .78rem;
        background   : #eaf4fb;
        color        : #1a5276;
        border       : 1px dashed #2980b9;
        display      : inline-flex;
        align-items  : center;
        gap          : .35rem;
        cursor       : pointer;
        margin-top   : .6rem;
        transition   : background .15s;
    }
    .btn-add-row:hover { background:#d4eaf7; }

    /* ── Read-only Banner ── */
    .readonly-banner {
        background   : #fffbeb;
        border       : 1px solid #fbbf24;
        border-radius: 9px;
        padding      : .6rem 1rem;
        font-size    : .82rem;
        display      : flex;
        align-items  : center;
        gap          : .5rem;
        margin-bottom: 1rem;
        color        : #92400e;
    }

    /* ── Modal Pasien ── */
    .btn-modal-pasien {
        background: rgba(255,255,255,.18);
        color: #fff;
        border: 1px solid rgba(255,255,255,.35);
        font-size: .74rem;
        border-radius: 7px;
        padding: .3rem .75rem;
    }
    .btn-modal-pasien:hover { background: rgba(255,255,255,.3); color:#fff; }

    @media (max-width: 576px) {
        .top-bar .nfbdg { display:none; }
        .form-wrapper   { padding: 0 .5rem 2rem; }
    }
</style>
@endpush

@section('header')
<header class="top-bar">
    <a href="{{ route('referal.permintaan_fpup.index') }}" class="back-btn">
        <i class="bi bi-chevron-left"></i> Kembali
    </a>
    <div class="divider"></div>
    <div>
        <div class="ttl">
            {{ $record ? (($readOnly ?? false) ? 'Detail' : 'Edit') : 'Tambah' }} FPUP Referal
        </div>
        <div class="sub">PASIEN SERVICE – PERMINTAAN DARAH (FPUP) REFERAL</div>
    </div>
    <div class="spacer"></div>
    <div class="nfbdg"><i class="bi bi-upc me-1"></i>{{ $noFpup }}</div>
</header>
@endsection

@section('content')

@php
    $isReadOnly = $readOnly ?? false;
    $ro  = $isReadOnly ? 'readonly' : '';
    $dis = $isReadOnly ? 'disabled' : '';

    $val = function (string $key, mixed $default = null) use ($record): mixed {
        return old($key, $record?->{$key} ?? $default);
    };

    $action = $record
        ? route('referal.permintaan_fpup.update', $record->id)
        : route('referal.permintaan_fpup.store');
    $method = $record ? 'PUT' : 'POST';
@endphp

<div class="form-wrapper">

    {{-- Flash / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger border-0">
            <strong><i class="bi bi-exclamation-circle me-1"></i>Ada kesalahan pada form:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $e)<li style="font-size:.81rem">{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if($isReadOnly)
        <div class="readonly-banner">
            <i class="bi bi-eye-fill"></i>
            Mode tampilan saja —
            <a href="{{ route('referal.permintaan_fpup.edit', $record->id) }}" style="color:#c0392b;font-weight:600">
                Klik di sini untuk mengedit
            </a>
        </div>
    @endif

    <form action="{{ $action }}" method="POST" id="fpupForm">
        @csrf
        @method($method)

        {{-- fpup_id (master pasien terpilih dari modal pencarian/tambah baru) --}}
        <input type="hidden" name="fpup_id" id="fpup_id_hidden" value="{{ $val('fpup_id') }}" />

        {{-- ══ SEKSI 1 – NOMOR & IDENTITAS ══ --}}
        <div class="sec-card">
            <div class="sec-head red"><i class="bi bi-hash"></i> Nomor & Identitas</div>
            <div class="sec-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label">No Referal</label>
                        <input type="text" class="form-control" readonly value="{{ $noFpup }}" />
                        <input type="hidden" name="no_referal" value="{{ $val('no_referal', $noFpup) }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No FPUP Asal</label>
                        <input type="text" class="form-control" readonly
                               value="{{ $val('no_fpup', $noFpup) }}" />
                        <input type="hidden" name="no_fpup" value="{{ $val('no_fpup', $noFpup) }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Reg</label>
                        <input type="text" id="no_reg_display" class="form-control" readonly
                               value="{{ $val('no_reg', 'Otomatis saat simpan') }}" />
                        <input type="hidden" name="no_reg" value="{{ $val('no_reg') }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tgl Minta <span class="text-danger">*</span></label>
                        <input type="date" name="tgl_minta"
                               class="form-control @error('tgl_minta') is-invalid @enderror"
                               {{ $ro }} value="{{ $val('tgl_minta', now()->toDateString()) }}" required />
                        @error('tgl_minta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jam Minta</label>
                        <input type="time" name="jam_minta" class="form-control"
                               {{ $ro }} value="{{ $val('jam_minta', now()->format('H:i')) }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Reg Online</label>
                        <input type="text" class="form-control" readonly
                               value="{{ $val('no_reg_online', 'Otomatis saat simpan') }}" />
                        <input type="hidden" name="no_reg_online" value="{{ $val('no_reg_online') }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tgl Reg Online</label>
                        <input type="date" class="form-control" readonly
                               value="{{ $val('tgl_registrasi_online', now()->toDateString()) }}" />
                        <input type="hidden" name="tgl_registrasi_online"
                               value="{{ $val('tgl_registrasi_online', now()->toDateString()) }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 2 – DATA RUMAH SAKIT ══ --}}
        <div class="sec-card">
            <div class="sec-head red"><i class="bi bi-hospital"></i> Data Rumah Sakit / Pengirim</div>
            <div class="sec-body">
                <div class="row g-2">
                    <div class="col-md-2" style="position:relative">
                        <label class="form-label">Kode RS</label>
                        <input type="text" id="kode_rs" name="kode_rs" class="form-control"
                            placeholder="Kode atau Nama RS" autocomplete="off"
                            value="{{ $val('kode_rs') }}" />
                        <input type="hidden" id="rumah_sakit_id" name="rumah_sakit_id" value="{{ $val('rumah_sakit_id') }}" />
                        <div id="list-rs" class="list-group position-absolute w-100 shadow"
                            style="z-index:9999;display:none;max-height:250px;overflow-y:auto;"></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama RS</label>
                        <input type="text" id="nama_rs" name="nama_rs" class="form-control"
                               placeholder="" readonly value="{{ $val('nama_rs') }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenis RS</label>
                        <select name="jenis_rs" id="jenis_rs" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                             @foreach($options['jenis_rs'] as $item)
                                <option value="{{ $item->nama }}" @selected($val('jenis_rs') === $item->nama)>
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kategori RS</label>
                        <select name="kategori_rs" id="kategori_rs" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                             @foreach($options['kategori_rs'] as $item)
                                    <option value="{{ $item->nama }}" @selected($val('kategori_rs') === $item->nama)>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenis Biaya</label>
                        <select name="jns_biaya" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                           @foreach($options['jenis_biaya'] as $o)
                            <option value="{{ $o->nama }}" @selected($val('jns_biaya') === $o->nama)>
                                {{ $o->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bagian</label>
                        <select name="bagian" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                            @foreach($options['bagian'] as $o)
                            <option value="{{ $o->nama }}" @selected($val('bagian') === $o->nama)>
                                {{ $o->nama }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kelas Rawat</label>
                        <select name="kelas_rawat" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                                @foreach($options['kelas_rawat'] as $item)
                                    <option value="{{ $item }}"
                                        {{ old('kelas_rawat', $record->kelas_rawat ?? '') == $item ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Dokter</label>
                        <input type="text" name="nama_dokter" id="nama_dokter_utama" class="form-control"
                               {{ $ro }} value="{{ $val('nama_dokter') }}" placeholder="dr. Nama Dokter, Sp." />
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 3 – DATA PASIEN ══ --}}
        <div class="sec-card">
            <div class="sec-head red" style="justify-content:space-between">
                <span><i class="bi bi-person"></i> Data Pasien</span>
                @unless($isReadOnly)
                <button type="button" class="btn-modal-pasien" data-bs-toggle="modal" data-bs-target="#modalPasien">
                    <i class="bi bi-search me-1"></i>Cari / Tambah Pasien
                </button>
                @endunless
            </div>
            <div class="sec-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Nama Pasien (OS) <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pasien" id="nama_pasien_utama"
                               class="form-control @error('nama_pasien') is-invalid @enderror"
                               {{ $ro }} value="{{ $val('nama_pasien') }}" required placeholder="Nama Lengkap" />
                        @error('nama_pasien')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">No KTP / NIK</label>
                        <input type="text" name="no_ktp" id="no_ktp_utama" class="form-control"
                               {{ $ro }} value="{{ $val('no_ktp') }}" maxlength="16" placeholder="16 digit NIK" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tgl Lahir</label>
                        <input type="date" name="tgl_lahir" id="tglLahir" class="form-control"
                               {{ $ro }} value="{{ $val('tgl_lahir') }}" />
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Umur</label>
                        <input type="number" name="umur" id="umur" class="form-control"
                               {{ $ro }} value="{{ $val('umur') }}" min="0" max="200" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_kelamin"
                                       id="jkPria" value="Pria" {{ $dis }}
                                       @checked($val('jenis_kelamin') === 'Pria') />
                                <label class="form-check-label" for="jkPria">Pria</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jenis_kelamin"
                                       id="jkWanita" value="Wanita" {{ $dis }}
                                       @checked($val('jenis_kelamin') === 'Wanita') />
                                <label class="form-check-label" for="jkWanita">Wanita</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Suami / Istri</label>
                        <input type="text" name="nama_suami_istri" class="form-control"
                               {{ $ro }} value="{{ $val('nama_suami_istri') }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kebangsaan</label>
                        <input type="text" name="kebangsaan" id="kebangsaan_utama" class="form-control"
                               {{ $ro }} value="{{ $val('kebangsaan', 'INDONESIA') }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Telp</label>
                        <input type="text" name="no_telp" id="no_telp_utama" class="form-control"
                               {{ $ro }} value="{{ $val('no_telp') }}" placeholder="08xxxxxxxxxx" />
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" id="alamat_utama" class="form-control"
                               {{ $ro }} value="{{ $val('alamat') }}" placeholder="Alamat lengkap" />
                    </div>

                    {{-- Data Keluarga / Penanggung Jawab (disimpan sebagai JSON) --}}
                    <div class="col-12">
                        <div class="sub-box" style="background:#f8f9fa;border-color:#e8ecef">
                            <div class="sub-box-title text-secondary">
                                <i class="bi bi-people"></i> Data Keluarga / Penanggung Jawab
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label">Nama Keluarga</label>
                                    <input type="text" id="keluarga_nama" class="form-control"
                                           {{ $ro }} value="{{ $val('keluarga')['nama'] ?? '' }}"
                                           placeholder="Nama keluarga / penanggung jawab" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hubungan</label>
                                    <input type="text" id="keluarga_hubungan" class="form-control"
                                           {{ $ro }} value="{{ $val('keluarga')['hubungan'] ?? '' }}"
                                           placeholder="Suami / Anak / dll" />
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">No Telp Keluarga</label>
                                    <input type="text" id="keluarga_telp" class="form-control"
                                           {{ $ro }} value="{{ $val('keluarga')['no_telp'] ?? '' }}" />
                                </div>
                                {{-- hidden JSON gabungan, disinkron via JS sebelum submit --}}
                                <input type="hidden" name="keluarga" id="keluarga_json"
                                       value="{{ $val('keluarga') ? json_encode($val('keluarga')) : '' }}" />
                            </div>
                        </div>
                    </div>

                    {{-- Foto KTP terpasang (jika pasien dipilih dari master / baru diupload) --}}
                    <div class="col-12" id="fotoKtpTerpasangWrap" style="display:none">
                        <div class="d-flex align-items-center gap-2 p-2" style="background:#f8f9fa;border-radius:8px;border:1px solid #eee">
                            <img id="fotoKtpTerpasangImg" src="" alt="Foto KTP" style="height:50px;border-radius:5px;border:1px solid #ddd" />
                            <div style="font-size:.78rem">
                                <span class="text-muted">Foto KTP terlampir</span>
                                <span id="fotoKtpVerifBadge"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Khusus Wanita --}}
                    <div class="col-12">
                        <div class="sub-box sub-box-pink">
                            <div class="sub-box-title text-danger">
                                <i class="bi bi-gender-female"></i> Khusus Wanita
                            </div>
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label class="form-label">Jml Kehamilan</label>
                                    <input type="number" name="jumlah_kehamilan" class="form-control"
                                           {{ $ro }} value="{{ $val('jumlah_kehamilan', 0) }}" min="0" />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Abortus</label>
                                    <input type="text" name="abortus" class="form-control"
                                           {{ $ro }} value="{{ $val('abortus', '0') }}" placeholder="0" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Penyakit Hemolitik Bayi (HDN)?</label>
                                    <div class="radio-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="hdn"
                                                   value="1" {{ $dis }}
                                                   @checked((bool) $val('hdn', false)) />
                                            <label class="form-check-label">Ya</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="hdn"
                                                   value="0" {{ $dis }}
                                                   @checked(! (bool) $val('hdn', false)) />
                                            <label class="form-check-label">Tidak</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 4 – DATA PERMINTAAN ══ --}}
        <div class="sec-card">
            <div class="sec-head red"><i class="bi bi-clipboard2-pulse"></i> Data Permintaan</div>
            <div class="sec-body">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label">Jns Permintaan</label>
                        <select name="jns_permintaan" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                            @foreach($options['jns_permintaan'] as $o)
                                <option value="{{ $o }}" @selected($val('jns_permintaan') === $o)>{{ $o }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Diagnosa Klinis</label>
                        <input type="text" list="diagnosa-list" name="diagnosa_klinis" class="form-control"
                               {{ $ro }} value="{{ $val('diagnosa_klinis') }}" />
                        <datalist id="diagnosa-list">
                        @foreach($options['diagnosa'] as $d)
                            <option value="{{ $d->nama }}">
                        @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hb (gr%)</label>
                        <input type="text" name="hb" class="form-control"
                               {{ $ro }} value="{{ $val('hb') }}" placeholder="8.5" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Alasan Transfusi</label>
                        <textarea name="alasan_transfusi" class="form-control" {{ $ro }}
                                  rows="1" placeholder="Alasan transfusi">{{ $val('alasan_transfusi') }}</textarea>
                    </div>

                    {{-- Transfusi Sebelumnya --}}
                    <div class="col-md-3">
                        <label class="form-label">Transfusi Sebelumnya?</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transfusi_sebelumnya"
                                       value="1" {{ $dis }} @checked((bool) $val('transfusi_sebelumnya', false)) />
                                <label class="form-check-label">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transfusi_sebelumnya"
                                       value="0" {{ $dis }} @checked(! (bool) $val('transfusi_sebelumnya', false)) />
                                <label class="form-check-label">Tidak</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kapan Transfusi</label>
                        <input type="date" name="transfusi_kapan" class="form-control"
                               {{ $ro }} value="{{ $val('transfusi_kapan') }}" />
                    </div>

                    {{-- Reaksi Transfusi --}}
                    <div class="col-md-3">
                        <label class="form-label">Reaksi Transfusi?</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reaksi_transfusi"
                                       value="1" {{ $dis }} @checked((bool) $val('reaksi_transfusi', false)) />
                                <label class="form-check-label">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="reaksi_transfusi"
                                       value="0" {{ $dis }} @checked(! (bool) $val('reaksi_transfusi', false)) />
                                <label class="form-check-label">Tidak</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Gejala Reaksi</label>
                        <input type="text" name="reaksi_gejala" class="form-control"
                               {{ $ro }} value="{{ $val('reaksi_gejala') }}" placeholder="Deskripsi gejala" />
                    </div>

                    {{-- Serologi --}}
                    <div class="col-md-3">
                        <label class="form-label">Pernah Serologi Golongan Darah?</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pernah_serologi"
                                       value="1" {{ $dis }} @checked((bool) $val('pernah_serologi', false)) />
                                <label class="form-check-label">Ya</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pernah_serologi"
                                       value="0" {{ $dis }} @checked(! (bool) $val('pernah_serologi', false)) />
                                <label class="form-check-label">Tidak</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dimana (Serologi)</label>
                        <input type="text" name="serologi_dimana" class="form-control"
                               {{ $ro }} value="{{ $val('serologi_dimana') }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Hasil Serologi</label>
                        <select name="serologi_hasil" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                            @foreach($options['serologi_hasil'] as $o)
                                <option value="{{ $o }}" @selected($val('serologi_hasil') === $o)>{{ $o }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kapan (Serologi)</label>
                        <input type="date" name="serologi_kapan" class="form-control"
                               {{ $ro }} value="{{ $val('serologi_kapan') }}" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 5 – UTDD & REFERAL ══ --}}
        <div class="sec-card">
            <div class="sec-head blue">
                <i class="bi bi-person-badge"></i> Diisi Petugas UTDD – Data Darah & Referal
            </div>
            <div class="sec-body-blue">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">Nama Darah O.S</label>
                        <input type="text" name="nama_darah_os" class="form-control"
                               {{ $ro }} value="{{ $val('nama_darah_os') }}" placeholder="Nama sesuai sampel" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gol / Rh O.S</label>
                        <select name="gol_rh_os" class="form-select" {{ $dis }}>
                            <option value="">-- Pilih --</option>
                            @foreach($options['gol_rh_os'] as $o)
                                <option value="{{ $o }}" @selected($val('gol_rh_os') === $o)>{{ $o }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tgl Terima</label>
                        <input type="date" name="tgl_terima" class="form-control"
                               {{ $ro }} value="{{ $val('tgl_terima', now()->toDateString()) }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jam Terima</label>
                        <input type="time" name="jam_terima" class="form-control"
                               {{ $ro }} value="{{ $val('jam_terima', now()->format('H:i')) }}" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pemeriksa</label>
                        <input type="text" name="pemeriksa" class="form-control"
                               {{ $ro }} value="{{ $val('pemeriksa') }}" placeholder="Nama petugas pemeriksa" />
                    </div>

                    {{-- Referal Box --}}
                    <div class="col-12">
                        <div class="sub-box sub-box-danger">
                            <div class="sub-box-title text-danger">
                                <i class="bi bi-arrow-up-right-circle-fill"></i> Pasien Referal
                            </div>
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Alasan Referal</label>
                                    <input type="text" name="alasan_referal" class="form-control"
                                           {{ $ro }} value="{{ $val('alasan_referal') }}" />
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Keterangan Alasan Referal</label>
                                    <input type="text" name="alasan_referal_utama" class="form-control"
                                           {{ $ro }} value="{{ $val('alasan_referal_utama') }}" />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status Referal</label>
                                    <select name="status_referal" class="form-select" {{ $dis }}>
                                        @foreach($options['status_referal'] as $s)
                                            <option value="{{ $s }}"
                                                @selected($val('status_referal', 'pending') === $s)>
                                                {{ ucfirst($s) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="checkbox"
                                               name="cetak_barcode" id="cetakBarcode"
                                               value="1" {{ $dis }}
                                               @checked((bool) $val('cetak_barcode', false)) />
                                        <label class="form-check-label fw-semibold" for="cetakBarcode" style="font-size:.8rem">
                                            <i class="bi bi-upc-scan"></i> Cetak Barcode
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 6 – PEMBAYARAN & DONOR ══ --}}
        <div class="sec-card">
            <div class="sec-head dark">
                <i class="bi bi-credit-card-2-front"></i> Cara Pembayaran & Donor
            </div>
            <div class="sec-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Cara Pembayaran</label>
                       <select name="cara_pembayaran" class="form-select" {{ $dis }}>
                            <option value="">Pilih Cara Pembayaran</option>
                             @foreach($options['cara_pembayaran'] as $o)
                                <option value="{{ $o }}" @selected($val('cara_pembayaran') === $o)>{{ $o }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenis Donor</label>
                        <div class="radio-group">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jns_donor"
                                       value="Sukarela" {{ $dis }}
                                       @checked($val('jns_donor', 'Sukarela') === 'Sukarela') />
                                <label class="form-check-label">Sukarela</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="jns_donor"
                                       value="Pengganti" {{ $dis }}
                                       @checked($val('jns_donor') === 'Pengganti') />
                                <label class="form-check-label">Pengganti</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Jml Donor</label>
                        <input type="number" id="jml_donor" name="jml_donor" class="form-control" readonly
                               value="{{ $val('jml_donor', 1) }}" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" {{ $dis }}>
                            @foreach($options['status'] as $s)
                                <option value="{{ $s }}" @selected($val('status', 'baru') === $s)>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nama O.S (Sistem)</label>
                        <input type="text" name="nama_os" id="nama_os_utama" class="form-control"
                               {{ $ro }} value="{{ $val('nama_os') }}" placeholder="Otomatis dari Nama Pasien" />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" class="form-control"
                               {{ $ro }} value="{{ $val('catatan') }}" placeholder="Catatan tambahan (opsional)" />
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ SEKSI 7 – DETAIL DARAH ══ --}}
        <div class="sec-card">
            <div class="sec-head red"><i class="bi bi-table"></i> Detail Permintaan Darah</div>
            <div class="sec-body">
                <div class="table-responsive">
                    <table class="table table-bordered detail-table" id="detailTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th style="min-width:120px">Jns Darah <span class="text-danger">*</span></th>
                                <th style="width:90px">Gol Darah</th>
                                <th style="width:100px">Rhesus</th>
                                <th style="width:70px">Jumlah</th>
                                <th style="width:70px">CC</th>
                                <th style="width:130px">Tgl Perlu</th>
                                <th>Keterangan</th>
                                @unless($isReadOnly)
                                    <th style="width:36px"></th>
                                @endunless
                            </tr>
                        </thead>
                        <tbody id="detailBody">
                            @php $detailRows = $record?->details->toArray() ?? [[]]; @endphp
                            @foreach($detailRows as $di => $d)
                            <tr>
                                <td class="text-center text-muted row-num">{{ $di + 1 }}</td>
                                <td>
                                    <select name="details[{{ $di }}][jns_darah]" class="form-select" {{ $dis }} required>
                                        <option value="">-- Pilih --</option>
                                       @foreach($options['jns_darah'] as $o)
                                        <option value="{{ $o->nama_pendek }}"
                                            @selected(($d['jns_darah'] ?? null) === $o->nama_pendek)>
                                            {{ $o->nama_pendek }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="details[{{ $di }}][gol_darah]" class="form-select" {{ $dis }}>
                                        <option value="">--</option>
                                       @foreach($options['gol_darah'] as $item)
                                            <option value="{{ $item }}" @selected(($d['gol_darah'] ?? null) === $item)>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="details[{{ $di }}][rhesus]" class="form-select" {{ $dis }}>
                                        <option value="">--</option>
                                         @foreach($options['rhesus'] as $item)
                                            <option value="{{ $item }}" @selected(($d['rhesus'] ?? null) === $item)>{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="details[{{ $di }}][jumlah]"
                                           class="form-control text-center"
                                           value="{{ $d['jumlah'] ?? 1 }}" min="1" {{ $ro }} required />
                                </td>
                                <td>
                                    <input type="number" name="details[{{ $di }}][cc]"
                                           class="form-control text-center"
                                           value="{{ $d['cc'] ?? 200 }}" min="0" {{ $ro }} placeholder="200" />
                                </td>
                                <td>
                                    <input type="date" name="details[{{ $di }}][tgl_perlu]"
                                           class="form-control" {{ $ro }}
                                           value="{{ isset($d['tgl_perlu']) && $d['tgl_perlu']
                                               ? \Carbon\Carbon::parse($d['tgl_perlu'])->format('Y-m-d')
                                               : now()->toDateString() }}" />
                                </td>
                                <td>
                                    <input type="text" name="details[{{ $di }}][keterangan]"
                                           class="form-control" value="{{ $d['keterangan'] ?? '-' }}"
                                           {{ $ro }} placeholder="Keterangan…" />
                                </td>
                                @unless($isReadOnly)
                                <td class="text-center">
                                    <button type="button" class="btn-del-row" onclick="deleteRow(this)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </td>
                                @endunless
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @unless($isReadOnly)
                <button type="button" class="btn-add-row" onclick="addDetailRow()">
                    <i class="bi bi-plus-circle"></i> Tambah Baris
                </button>
                @endunless
            </div>
        </div>

        {{-- ── ACTION BAR ── --}}
        @unless($isReadOnly)
        <div class="action-bar">
            <button type="submit" class="btn-save">
                <i class="bi bi-save2"></i>
                {{ $record ? 'Simpan Perubahan' : 'Simpan Data Referal' }}
            </button>
            <a href="{{ route('referal.permintaan_fpup.index') }}" class="btn-cancel-link">
                <i class="bi bi-x-lg"></i> Batal
            </a>
            @if($record)
                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('referal.permintaan_fpup.show', $record->id) }}"
                       class="btn-cancel-link" style="border-color:#adb5bd">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </a>
                    
                </div>
            @endif
        </div>
        @endunless

    </form>
   
</div>

{{-- ══════════════════════════════════════════════════════════════════
     MODAL PASIEN — Tab "Cari Pasien" & Tab "Tambah Baru" (dengan OCR KTP)
     Diletakkan di luar <form id="fpupForm"> agar form internal modal
     (formPasienBaru) tidak bersarang/konflik dengan form utama.
     ══════════════════════════════════════════════════════════════════ --}}
@unless($isReadOnly)
<div class="modal fade" id="modalPasien" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(90deg,#c0392b,#e74c3c);color:#fff">
        <h5 class="modal-title"><i class="bi bi-person-vcard me-1"></i> Data Pasien FPUP</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <ul class="nav nav-tabs mb-3" id="pasienTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-cari-btn" data-bs-toggle="tab" data-bs-target="#tab-cari" type="button">
              <i class="bi bi-search me-1"></i>Cari Pasien
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-baru-btn" data-bs-toggle="tab" data-bs-target="#tab-baru" type="button">
              <i class="bi bi-person-plus me-1"></i>Tambah Baru
            </button>
          </li>
        </ul>

        <div class="tab-content">

          {{-- ── TAB 1: CARI PASIEN ── --}}
          <div class="tab-pane fade show active" id="tab-cari" role="tabpanel">
            <div class="mb-2">
              <label class="form-label">Cari berdasarkan Nama / No KTP</label>
              <input type="text" id="inputCariPasien" class="form-control" placeholder="Ketik minimal 3 huruf..." autocomplete="off" />
            </div>
            <div id="hasilCariPasien" class="list-group" style="max-height:320px;overflow:auto"></div>
            <div id="cariPasienEmpty" class="text-muted text-center py-3" style="display:none">
              <i class="bi bi-inbox"></i> Tidak ada data ditemukan.
            </div>
          </div>

          {{-- ── TAB 2: TAMBAH BARU (dengan upload + OCR KTP) ── --}}
          <div class="tab-pane fade" id="tab-baru" role="tabpanel">

            <div class="sub-box sub-box-pink mb-3">
              <div class="sub-box-title text-danger"><i class="bi bi-camera"></i> Upload Foto KTP (Opsional, untuk OCR)</div>
              <div class="row g-2 align-items-end">
                <div class="col-md-7">
                  <input type="file" id="inputFotoKtp" class="form-control" accept="image/png,image/jpeg" />
                  <div class="form-text">Format JPG/PNG, maks 5MB. Pastikan foto jelas & tidak buram.</div>
                </div>
                <div class="col-md-3">
                  <button type="button" id="btnProsesOcr" class="btn btn-sm btn-cari w-100">
                    <i class="bi bi-magic me-1"></i>Proses OCR
                  </button>
                </div>
                <div class="col-md-2 text-center">
                  <img id="previewFotoKtp" src="" alt="" style="display:none;max-height:60px;border-radius:6px;border:1px solid #ddd" />
                </div>
              </div>
              <div id="ocrStatus" class="mt-2" style="display:none"></div>
              <div id="ocrDebugWrap" class="mt-2" style="display:none">
                <div class="text-muted" style="font-size:.72rem;margin-bottom:.25rem">
                  <i class="bi bi-eye"></i> Gambar yang dibaca sistem (setelah diolah) — jika gambar ini buram/gelap/miring, itu sebabnya bacaan gagal:
                </div>
                <img id="previewProcessed" src="" alt="" style="max-width:100%;max-height:220px;border-radius:6px;border:1px solid #ddd" />
                <details class="mt-1">
                  <summary style="font-size:.74rem;cursor:pointer;color:#7f8c8d">Lihat teks mentah hasil OCR</summary>
                  <pre id="ocrRawTextView" style="font-size:.7rem;background:#f8f9fa;padding:.5rem;border-radius:6px;white-space:pre-wrap;max-height:120px;overflow:auto"></pre>
                </details>
              </div>
            </div>

            <form id="formPasienBaru">
              <input type="hidden" id="pb_foto_ktp_path" name="foto_ktp_path" value="" />
              <input type="hidden" id="pb_ocr_raw_result" name="ocr_raw_result" value="" />

              <div class="row g-2">
                <div class="col-md-7">
                  <label class="form-label">Nama Pasien <span class="text-danger">*</span></label>
                  <input type="text" id="pb_nama_pasien" name="nama_pasien" class="form-control" required />
                </div>
                <div class="col-md-5">
                  <label class="form-label">No KTP / NIK</label>
                  <input type="text" id="pb_no_ktp" name="no_ktp" class="form-control" maxlength="16" />
                </div>
                <div class="col-md-3">
                  <label class="form-label">Tgl Lahir</label>
                  <input type="date" id="pb_tgl_lahir" name="tgl_lahir" class="form-control" />
                </div>
                <div class="col-md-2">
                  <label class="form-label">Umur</label>
                  <input type="number" id="pb_umur" name="umur" class="form-control" min="0" max="200" />
                </div>
                <div class="col-md-3">
                  <label class="form-label">Jenis Kelamin</label>
                  <select id="pb_jenis_kelamin" name="jenis_kelamin" class="form-select">
                    <option value="">-- Pilih --</option>
                    <option value="Pria">Pria</option>
                    <option value="Wanita">Wanita</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Kebangsaan</label>
                  <input type="text" id="pb_kebangsaan" name="kebangsaan" class="form-control" value="INDONESIA" />
                </div>
                <div class="col-md-5">
                  <label class="form-label">No Telp</label>
                  <input type="text" id="pb_no_telp" name="no_telp" class="form-control" />
                </div>
                <div class="col-12">
                  <label class="form-label">Alamat</label>
                  <textarea id="pb_alamat" name="alamat" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nama Dokter</label>
                  <input type="text" id="pb_nama_dokter" name="nama_dokter" class="form-control" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Nama Instansi / RS Perujuk</label>
                  <input type="text" id="pb_nama_instansi" name="nama_instansi" class="form-control" />
                </div>

                <div class="col-12">
                  <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="pb_ocr_terverifikasi" name="ocr_terverifikasi" value="1" />
                    <label class="form-check-label fw-semibold" for="pb_ocr_terverifikasi">
                      <i class="bi bi-patch-check"></i> Saya telah memverifikasi foto KTP sesuai dengan data di atas
                    </label>
                  </div>
                </div>
              </div>

              <div id="pasienBaruAlert" class="alert alert-danger mt-3" style="display:none"></div>

              <div class="text-end mt-3">
                <button type="submit" class="btn btn-cari">
                  <i class="bi bi-save2 me-1"></i> Simpan & Gunakan Pasien Ini
                </button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
@endunless

@endsection

@push('scripts')
<script>
let timerRs = null;
const urlSearchRs = "{{ route('referal.permintaan_fpup.search-rs') }}";

$('#kode_rs').on('input', function () {
    clearTimeout(timerRs);
    const keyword = $(this).val().trim();
    const listEl  = $('#list-rs');

    if (keyword.length < 2) {
        listEl.hide().empty();
        return;
    }

    timerRs = setTimeout(() => {
        $.get(urlSearchRs, { keyword: keyword }, function (data) {
            listEl.empty();

            if (!data.length) {
                listEl.append('<div class="list-group-item text-muted">Tidak ditemukan</div>');
            } else {
                data.forEach(item => {
                    const opt = $(`<button type="button" class="list-group-item list-group-item-action">
                        <strong>${item.kode}</strong> — ${item.nama}
                    </button>`);
                    opt.on('click', function () {
                        $('#kode_rs').val(item.kode);
                        $('#nama_rs').val(item.nama);
                        $('#rumah_sakit_id').val(item.id);
                        listEl.hide().empty();
                    });
                    listEl.append(opt);
                });
            }
            listEl.show();
        });
    }, 350);
});

$(document).on('click', function (e) {
    if (!$(e.target).closest('#kode_rs, #list-rs').length) {
        $('#list-rs').hide();
    }
});

function hitungDonor() {
    let total = 0;
    $('input[name$="[jumlah]"]').each(function () {
        let nilai = parseInt($(this).val()) || 0;
        total += nilai;
    });
    $('#jml_donor').val(total > 0 ? total : 1);
}

$(document).on('keyup change', 'input[name$="[jumlah]"]', hitungDonor);

$(document).ready(function () {
    hitungDonor();
});

/* Auto-hitung umur dari tgl lahir (dipakai di form utama & modal tambah pasien) */
function hitungUmurDariTanggal(tglLahirValue) {
    const tgl = new Date(tglLahirValue);
    if (isNaN(tgl)) return null;

    const today = new Date();
    let age = today.getFullYear() - tgl.getFullYear();
    const m = today.getMonth() - tgl.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < tgl.getDate())) age--;

    return age >= 0 ? age : null;
}

document.getElementById('tglLahir')?.addEventListener('change', function () {
    const age = hitungUmurDariTanggal(this.value);
    if (age !== null) document.getElementById('umur').value = age;
});

document.getElementById('pb_tgl_lahir')?.addEventListener('change', function () {
    const age = hitungUmurDariTanggal(this.value);
    if (age !== null) document.getElementById('pb_umur').value = age;
});

/* Sinkron Nama Pasien -> Nama O.S (Sistem) otomatis kalau field nama_os belum diisi manual */
(function () {
    const namaPasienEl = document.getElementById('nama_pasien_utama');
    const namaOsEl      = document.getElementById('nama_os_utama');
    let namaOsDiubahManual = false;

    namaOsEl?.addEventListener('input', () => { namaOsDiubahManual = true; });

    namaPasienEl?.addEventListener('input', function () {
        if (!namaOsDiubahManual && namaOsEl) {
            namaOsEl.value = this.value;
        }
    });
})();

/* Sinkron field keluarga (nama/hubungan/no_telp) menjadi JSON sebelum submit */
document.getElementById('fpupForm')?.addEventListener('submit', function () {
    const nama      = document.getElementById('keluarga_nama')?.value?.trim() || '';
    const hubungan  = document.getElementById('keluarga_hubungan')?.value?.trim() || '';
    const telpKel   = document.getElementById('keluarga_telp')?.value?.trim() || '';

    const jsonInput = document.getElementById('keluarga_json');
    if (jsonInput) {
        if (nama || hubungan || telpKel) {
            jsonInput.value = JSON.stringify({ nama, hubungan, no_telp: telpKel });
        } else {
            jsonInput.value = '';
        }
    }

    // Fallback terakhir: pastikan nama_os tidak pernah kosong saat submit
    const namaOsEl   = document.getElementById('nama_os_utama');
    const namaPasien = document.getElementById('nama_pasien_utama')?.value?.trim() || '';
    if (namaOsEl && !namaOsEl.value.trim() && namaPasien) {
        namaOsEl.value = namaPasien;
    }
});

/* Detail table */
let rowIndex = {{ count($record?->details ?? [[]]) }};

const jnsDarahOpts = @json($options['jns_darah']->pluck('nama_pendek'));
const golDarahOpts = @json($options['gol_darah']);
const rhesusOpts   = @json($options['rhesus']);

function buildSelect(name, opts, required = false) {
    let html = `<select name="${name}" class="form-select"${required ? ' required' : ''}><option value="">--</option>`;
    opts.forEach(o => { html += `<option value="${o}">${o}</option>`; });
    return html + '</select>';
}

function addDetailRow() {
    const idx   = rowIndex++;
    const tbody = document.getElementById('detailBody');
    const tr    = document.createElement('tr');
    const today = new Date().toISOString().split('T')[0];
    tr.innerHTML = `
        <td class="text-center text-muted row-num">–</td>
        <td>${buildSelect(`details[${idx}][jns_darah]`, jnsDarahOpts, true)}</td>
        <td>${buildSelect(`details[${idx}][gol_darah]`, golDarahOpts)}</td>
        <td>${buildSelect(`details[${idx}][rhesus]`, rhesusOpts)}</td>
        <td><input type="number" name="details[${idx}][jumlah]" class="form-control text-center" value="1" min="1" required/></td>
        <td><input type="number" name="details[${idx}][cc]" class="form-control text-center" value="200" min="0" placeholder="200"/></td>
        <td><input type="date" name="details[${idx}][tgl_perlu]" class="form-control" value="${today}"/></td>
        <td><input type="text" name="details[${idx}][keterangan]" class="form-control" value="-" placeholder="Keterangan…"/></td>
        <td class="text-center">
            <button type="button" class="btn-del-row" onclick="deleteRow(this)">
                <i class="bi bi-x"></i>
            </button>
        </td>`;
    tbody.appendChild(tr);
    renumberRows();
}

function deleteRow(btn) {
    const tbody = document.getElementById('detailBody');
    if (tbody.rows.length <= 1) {
        alert('Minimal 1 baris detail harus ada.');
        return;
    }
    btn.closest('tr').remove();
    renumberRows();
    hitungDonor();
}

function renumberRows() {
    document.querySelectorAll('#detailBody .row-num').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

/* ════════════════════════════════════════════════════════════════
   MODAL PASIEN — Cari, Tambah Baru, OCR KTP, Auto-fill ke Seksi 3
   ════════════════════════════════════════════════════════════════ */
(function () {
    const modalPasienEl = document.getElementById('modalPasien');
    if (!modalPasienEl) return; // mode readonly, modal tidak dirender

    const urlCariPasien  = "{{ route('referal.permintaan_fpup.pasien.cari') }}";
    const urlShowPasien  = "{{ route('referal.permintaan_fpup.pasien.show', ['id' => '__ID__']) }}";
    const urlOcrPreview  = "{{ route('referal.permintaan_fpup.pasien.ocr-preview') }}";
    const urlStorePasien = "{{ route('referal.permintaan_fpup.pasien.store') }}";
    const csrfToken      = "{{ csrf_token() }}";

    let timerCari = null;

    /* ── TAB 1: Cari Pasien (debounced autocomplete) ── */
    const inputCari = document.getElementById('inputCariPasien');
    inputCari?.addEventListener('input', function () {
        clearTimeout(timerCari);
        const q = this.value.trim();
        const hasilEl = document.getElementById('hasilCariPasien');
        const emptyEl = document.getElementById('cariPasienEmpty');

        if (q.length < 3) {
            hasilEl.innerHTML = '';
            emptyEl.style.display = 'none';
            return;
        }

        timerCari = setTimeout(() => {
            fetch(`${urlCariPasien}?q=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(json => {
                    hasilEl.innerHTML = '';
                    const data = json.data || [];
                    emptyEl.style.display = data.length ? 'none' : 'block';

                    data.forEach(p => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex justify-content-between">
                                <strong>${p.nama_pasien}</strong>
                                ${p.ocr_terverifikasi
                                    ? '<span class="bdg bdg-diterima"><i class="bi bi-patch-check"></i> Terverifikasi</span>'
                                    : '<span class="bdg bdg-pending">Belum Verifikasi</span>'}
                            </div>
                            <div class="text-muted" style="font-size:.78rem">
                                NIK: ${p.no_ktp ?? '–'} · ${p.jenis_kelamin ?? '–'} · ${p.alamat ? p.alamat.substring(0,40) : '–'}
                            </div>`;
                        item.addEventListener('click', () => pilihPasien(p.id));
                        hasilEl.appendChild(item);
                    });
                })
                .catch(() => {
                    hasilEl.innerHTML = '<div class="text-danger small">Gagal memuat data.</div>';
                });
        }, 350);
    });

    function pilihPasien(id) {
        fetch(urlShowPasien.replace('__ID__', id))
            .then(res => res.json())
            .then(json => {
                if (!json.success) {
                    alert(json.message || 'Pasien tidak ditemukan');
                    return;
                }
                isiFormUtamaDariPasien(json.data);
                bootstrap.Modal.getInstance(modalPasienEl)?.hide();
            });
    }

    /* ── TAB 2: Upload Foto + Proses OCR ── */
    const inputFoto = document.getElementById('inputFotoKtp');
    inputFoto?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const preview = document.getElementById('previewFotoKtp');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'inline-block';
    });

    document.getElementById('btnProsesOcr')?.addEventListener('click', function () {
        const file = inputFoto.files[0];
        const statusEl = document.getElementById('ocrStatus');

        if (!file) {
            alert('Pilih foto KTP terlebih dahulu.');
            return;
        }

        const fd = new FormData();
        fd.append('foto_ktp', file);
        fd.append('_token', csrfToken);

        statusEl.style.display = 'block';
        statusEl.className = 'mt-2 text-muted';
        statusEl.innerHTML = '<i class="bi bi-arrow-repeat"></i> Memproses OCR, mohon tunggu...';
        this.disabled = true;
        const btnSelf = this;

        fetch(urlOcrPreview, { method: 'POST', body: fd })
            .then(res => res.json())
            .then(json => {
                btnSelf.disabled = false;
                if (!json.success) {
                    statusEl.className = 'mt-2 text-danger';
                    statusEl.innerHTML = `<i class="bi bi-x-circle"></i> ${json.message}`;
                    return;
                }

                document.getElementById('pb_foto_ktp_path').value = json.foto_path;
                document.getElementById('pb_ocr_raw_result').value = json.raw_text;

                // Tampilkan gambar hasil preprocessing + teks mentah untuk debug
                const debugWrap = document.getElementById('ocrDebugWrap');
                if (json.processed_url) {
                    document.getElementById('previewProcessed').src = json.processed_url;
                    debugWrap.style.display = 'block';
                } else {
                    debugWrap.style.display = 'none';
                }
                document.getElementById('ocrRawTextView').textContent = json.raw_text || '(kosong)';

                const p = json.parsed || {};
                if (p.nama_pasien)   document.getElementById('pb_nama_pasien').value   = p.nama_pasien;
                if (p.no_ktp)        document.getElementById('pb_no_ktp').value        = p.no_ktp;
                if (p.tgl_lahir) {
                    document.getElementById('pb_tgl_lahir').value = p.tgl_lahir;
                    const age = hitungUmurDariTanggal(p.tgl_lahir);
                    if (age !== null) document.getElementById('pb_umur').value = age;
                }
                if (p.jenis_kelamin) document.getElementById('pb_jenis_kelamin').value  = p.jenis_kelamin;
                if (p.alamat)        document.getElementById('pb_alamat').value         = p.alamat;
                if (p.kebangsaan)    document.getElementById('pb_kebangsaan').value     = p.kebangsaan;

                if (json.low_confidence) {
                    statusEl.className = json.glare_detected ? 'mt-2 text-danger' : 'mt-2 text-warning';
                    const icon = json.glare_detected ? 'bi-brightness-high' : 'bi-exclamation-triangle';
                    statusEl.innerHTML = `<i class="bi ${icon}"></i> ${json.message}`;
                } else if (json.glare_detected) {
                    statusEl.className = 'mt-2 text-warning';
                    statusEl.innerHTML = `<i class="bi bi-brightness-high"></i> ${json.message}`;
                } else {
                    statusEl.className = 'mt-2 text-success';
                    statusEl.innerHTML = `<i class="bi bi-check-circle"></i> ${json.message}
                        <br><span class="text-muted" style="font-size:.75rem">Mohon periksa & koreksi manual jika ada bacaan yang kurang tepat, lalu centang verifikasi di bawah sebelum menyimpan.</span>`;
                }
            })
            .catch(() => {
                btnSelf.disabled = false;
                statusEl.className = 'mt-2 text-danger';
                statusEl.innerHTML = '<i class="bi bi-x-circle"></i> Terjadi kesalahan saat memproses OCR.';
            });
    });

    /* ── Submit Tambah Pasien Baru ── */
    document.getElementById('formPasienBaru')?.addEventListener('submit', function (e) {
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

        fetch(urlStorePasien, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        })
            .then(res => res.json().then(json => ({ status: res.status, json })))
            .then(({ status, json }) => {
                if (status === 422) {
                    const msgs = Object.values(json.errors || {}).flat().join('<br>');
                    alertEl.innerHTML = msgs || json.message;
                    alertEl.style.display = 'block';
                    return;
                }
                if (!json.success) {
                    alertEl.innerHTML = json.message;
                    alertEl.style.display = 'block';
                    return;
                }

                isiFormUtamaDariPasien(json.data);
                bootstrap.Modal.getInstance(modalPasienEl)?.hide();
            })
            .catch(() => {
                alertEl.innerHTML = 'Terjadi kesalahan saat menyimpan data pasien.';
                alertEl.style.display = 'block';
            });
    });

    /* ── Isi field-field di Seksi 3 form utama dari data pasien terpilih ── */
    function isiFormUtamaDariPasien(p) {
        setVal('#nama_pasien_utama', p.nama_pasien);
        setVal('#no_ktp_utama',      p.no_ktp);
        setVal('#tglLahir',          p.tgl_lahir ? String(p.tgl_lahir).substring(0, 10) : '');
        setVal('#umur',              p.umur);
        setVal('#kebangsaan_utama',  p.kebangsaan);
        setVal('#no_telp_utama',     p.no_telp);
        setVal('#alamat_utama',      p.alamat);
        setVal('#nama_dokter_utama', p.nama_dokter);
        setVal('#nama_os_utama',     p.nama_pasien);

        if (p.jenis_kelamin === 'Pria') {
            document.getElementById('jkPria').checked = true;
        } else if (p.jenis_kelamin === 'Wanita') {
            document.getElementById('jkWanita').checked = true;
        }

        document.getElementById('fpup_id_hidden').value = p.id;

        // Tampilkan badge foto KTP terlampir bila ada
        const wrap = document.getElementById('fotoKtpTerpasangWrap');
        if (p.foto_ktp_path) {
            document.getElementById('fotoKtpTerpasangImg').src = '/storage/' + p.foto_ktp_path;
            document.getElementById('fotoKtpVerifBadge').innerHTML = p.ocr_terverifikasi
                ? '<span class="bdg bdg-diterima ms-1"><i class="bi bi-patch-check"></i> Terverifikasi</span>'
                : '<span class="bdg bdg-pending ms-1">Belum Diverifikasi</span>';
            wrap.style.display = 'block';
        } else {
            wrap.style.display = 'none';
        }
    }

    function setVal(selector, value) {
        const el = document.querySelector(selector);
        if (el && value !== undefined && value !== null) el.value = value;
    }
})();
</script>
@endpush