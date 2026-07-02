@extends('layouts.index')

@php
    $isEdit   = isset($pemberianDarah) && $pemberianDarah !== null;
    $isView   = $readonly ?? false;
    $title    = $isView ? 'Detail' : ($isEdit ? 'Edit' : 'Tambah');
    $old      = fn(string $field, $default = '') => old($field, $isEdit ? $pemberianDarah->$field : $default);
    $details  = $isEdit ? $pemberianDarah->details->toArray() : [];
@endphp

@section('title', $title . ' Pemberian Darah Referal')

@push('styles')
<style>
    :root {
        --bd-cyan:      #00b4d8;
        --bd-cyan-dark: #0077b6;
        --bd-yellow:    #fff3cd;
        --bd-section:   #caf0f8;
    }

    .form-wrapper        { width: 100%; padding: 0 2px; }
    .container-bd        { padding-left: 6px; padding-right: 6px; }

    .card-main  { border: 2px solid var(--bd-cyan-dark); border-radius: 4px; background:#fff; }
    .card-title { background: var(--bd-cyan-dark); color:#fff; padding:5px 14px; font-size:.88rem; font-weight:600; }

    .section-fpup  { background:#e0f7fa; border:1px solid var(--bd-cyan); border-radius:4px; padding:7px 10px; margin-bottom:8px; }
    .section-label { font-size:.7rem; font-weight:700; color:var(--bd-cyan-dark); text-transform:uppercase; letter-spacing:.5px; margin-bottom:3px; }

    .panel-kirim { background:#e8f4fd; border:1px solid #90caf9; border-radius:4px; padding:8px 10px; height:100%; }
    .panel-kirim .kirim-title { background:#1565c0; color:#fff; padding:3px 10px; border-radius:3px; font-size:.78rem; font-weight:600; margin-bottom:6px; }
    .darah-badge { background:#1565c0; color:#fff; padding:2px 8px; border-radius:3px; font-size:.78rem; display:inline-block; margin:2px; }

    .tbl-jenis thead th { background:var(--bd-cyan-dark); color:#fff; font-size:.73rem; padding:3px 5px; border:1px solid #0077b6; }
    .tbl-jenis tbody td { font-size:.76rem; padding:2px 5px; border:1px solid #cce5ff; cursor:pointer; }
    .tbl-jenis tbody tr:hover    { background:#d0eeff; }
    .tbl-jenis tbody tr.selected { background:#ffe082 !important; }

    .tbl-stok thead th { background:#37474f; color:#fff; font-size:.73rem; padding:3px 5px; border:1px solid #455a64; }
    .tbl-stok tbody td { font-size:.76rem; padding:2px 5px; border:1px solid #cfd8dc; vertical-align:middle; }
    .tbl-stok tbody input.inp-row { font-size:.76rem; padding:1px 4px; height:22px; border:1px solid #cfd8dc; width:100%; }

    .inp-scan { background:var(--bd-yellow); border:1px solid #ffc107; }
    .inp-ro   { background:#f5f5f5 !important; cursor:not-allowed; }
    .lbl-sm   { font-size:.7rem; font-weight:600; margin-bottom:1px; display:block; }

    .form-control-sm, .form-select-sm { font-size:.78rem; padding: 2px 6px; height:26px; }
    .input-group-sm .btn { font-size:.78rem; padding:2px 7px; }

    .footer-bar { background:#eceff1; border-top:1px solid #b0bec5; padding:7px 14px; }

    .mb-row { margin-bottom: 6px; }
</style>
@endpush

@section('content')
<div class="py-2 px-2 form-wrapper">

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show py-2 mb-2">
        <strong>Validasi Gagal:</strong>
        <ul class="mb-0 ps-3 mt-1">
            @foreach ($errors->all() as $e) <li style="font-size:.8rem">{{ $e }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form id="formPemberian"
      method="POST"
      action="{{ $isEdit ? route('referal.pemberian_darah.update', $pemberianDarah) : route('referal.pemberian_darah.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="card-main">

        {{-- ══ JUDUL ══ --}}
        <div class="card-title d-flex justify-content-between align-items-center">
            <span><i class="bi bi-droplet-fill me-2"></i>{{ strtoupper($title) }} PEMBERIAN DARAH REFERAL</span>
            <a href="{{ route('referal.pemberian_darah.index') }}" class="btn btn-sm btn-light py-0">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="p-2">

            {{-- ══ ROW 1: HEADER ══ --}}
            <div class="row g-1 mb-row">
                <div class="col-xl-2 col-lg-2 col-md-3">
                    <label class="lbl-sm">No. Pemberian</label>
                    <input type="text" class="form-control form-control-sm inp-ro fw-bold text-primary"
                           value="{{ $noPemberian }}" readonly>
                    <input type="hidden" name="no_pemberian" value="{{ $noPemberian }}">
                </div>
                <div class="col-xl-1 col-lg-2 col-md-2">
                    <label class="lbl-sm">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" id="tanggal"
                           class="form-control form-control-sm @error('tanggal') is-invalid @enderror {{ $isView ? 'inp-ro' : '' }}"
                           value="{{ $old('tanggal', now()->format('Y-m-d')) }}"
                           {{ $isView ? 'readonly' : 'required' }}>
                    @error('tanggal')<div class="invalid-feedback" style="font-size:.7rem">{{ $message }}</div>@enderror
                </div>
                <div class="col-xl-1 col-lg-1 col-md-2">
                    <label class="lbl-sm">Jam Keluar</label>
                    <input type="time" name="jam_keluar"
                           class="form-control form-control-sm inp-ro"
                           value="{{ $old('jam_keluar', now()->format('H:i')) }}" readonly>
                </div>
                <div class="col-xl-1 col-lg-2 col-md-2">
                    <label class="lbl-sm">Status</label>
                    <select name="status"
                            class="form-select form-select-sm {{ $isView ? 'inp-ro' : '' }}"
                            {{ $isView ? 'disabled' : '' }}>
                        @foreach(['draft'=>'Draft','proses'=>'Proses','selesai'=>'Selesai','batal'=>'Batal'] as $sv=>$sl)
                            <option value="{{ $sv }}" {{ $old('status','draft') === $sv ? 'selected' : '' }}>{{ $sl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3">
                    <label class="lbl-sm">Petugas</label>
                    <div class="d-flex gap-1">
                        <input type="text" id="petugasKode" name="petugas_kode"
                               class="form-control form-control-sm inp-scan {{ $isView ? 'inp-ro' : '' }}"
                               style="width:90px; flex-shrink:0"
                               placeholder="Kode"
                               value="{{ $old('petugas_kode') }}" {{ $isView ? 'readonly' : '' }}>
                        <input type="text" id="petugasNama" name="petugas_nama"
                               class="form-control form-control-sm inp-ro fw-semibold flex-grow-1"
                               value="{{ $old('petugas_nama') }}" readonly>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-2 col-md-6">
                    <label class="lbl-sm">Nama Penerima</label>
                    <input type="text" name="nama_penerima"
                           class="form-control form-control-sm {{ $isView ? 'inp-ro' : '' }}"
                           value="{{ $old('nama_penerima') }}" {{ $isView ? 'readonly' : '' }}>
                </div>
                <div class="col-xl-2 col-lg-3 col-md-6">
                    <label class="lbl-sm">Alamat Penerima</label>
                    <input type="text" name="alamat_penerima"
                           class="form-control form-control-sm {{ $isView ? 'inp-ro' : '' }}"
                           value="{{ $old('alamat_penerima') }}" {{ $isView ? 'readonly' : '' }}>
                </div>
                {{-- Kadaluarsa — checkbox sesuai tampilan, posisi pojok kanan atas --}}
                <div class="col-xl-12 d-flex justify-content-end">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="is_kadaluarsa" id="isKadaluarsa"
                               value="1" {{ $old('is_kadaluarsa', $isEdit ? $pemberianDarah->is_kadaluarsa : false) ? 'checked' : '' }}
                               {{ $isView ? 'disabled' : '' }}>
                        <label class="form-check-label" style="font-size:.78rem" for="isKadaluarsa">Kadaluarsa</label>
                    </div>
                </div>
            </div>

            {{-- ══ WARNING FPUP ══ --}}
            <div id="fpupWarning"></div>

            {{-- ══ SECTION FPUP ══ --}}
            <div class="section-fpup">
                <div class="section-label"><i class="bi bi-file-earmark-text me-1"></i>Data Permintaan (FPUP)</div>

                <div class="row g-1 mb-1">
                    <div class="col-xl-2 col-lg-2 col-md-3">
                        <label class="lbl-sm">No. FPUP</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="noFpup" name="no_fpup"
                                   class="form-control form-control-sm inp-scan {{ $isView ? 'inp-ro' : '' }}"
                                   placeholder="Scan / Ketik"
                                   value="{{ $old('no_fpup') }}" {{ $isView ? 'readonly' : '' }}>
                            @if(!$isView)
                            <button type="button" class="btn btn-sm btn-primary" onclick="doScanFpup()" title="Scan FPUP">
                                <i class="bi bi-upc-scan"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3">
                        <label class="lbl-sm">Tgl. FPUP</label>
                        <input type="datetime-local" name="tgl_fpup" id="tglFpup"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('tgl_fpup') ? \Carbon\Carbon::parse($old('tgl_fpup'))->format('Y-m-d\TH:i') : '' }}"
                               readonly>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="lbl-sm">Dokter</label>
                        <input type="text" name="dokter" id="dokter"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('dokter') }}" readonly>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2">
                        <label class="lbl-sm">Kode RS</label>
                        <input type="text" name="kode_rs" id="kodeRs"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('kode_rs') }}" readonly>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-10">
                        <label class="lbl-sm">Rumah Sakit</label>
                        <input type="text" name="nama_rs" id="namaRs"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('nama_rs') }}" readonly>
                    </div>
                    <div class="col-xl-2 col-lg-4 col-md-6">
                        <label class="lbl-sm">Pasien</label>
                        <input type="text" name="pasien" id="pasien"
                               class="form-control form-control-sm inp-ro fw-semibold"
                               value="{{ $old('pasien') }}" readonly>
                    </div>
                </div>

                <div class="row g-1">
                    <div class="col-xl-1 col-lg-2 col-md-2">
                        <label class="lbl-sm">Jenis RS</label>
                        <input type="text" name="jenis_rs" id="jenisRs"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('jenis_rs') }}" readonly>
                    </div>
                    <div class="col-xl-1 col-lg-2 col-md-2">
                        <label class="lbl-sm">Kelas Rawat</label>
                        <input type="text" name="kelas_rawat" id="kelasRawat"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('kelas_rawat') }}" readonly>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2">
                        <label class="lbl-sm">Gol/Rh</label>
                        <div class="d-flex gap-1">
                            <input type="text" name="gol_darah_pasien" id="golDarahPasien"
                                   class="form-control form-control-sm inp-ro text-center"
                                   style="width:42px; flex-shrink:0"
                                   value="{{ $old('gol_darah_pasien') }}" readonly>
                            <input type="text" name="rh_pasien" id="rhPasien"
                                   class="form-control form-control-sm inp-ro"
                                   style="width:65px; flex-shrink:0"
                                   value="{{ $old('rh_pasien') }}" readonly>
                        </div>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-1">
                        <label class="lbl-sm">Kategori</label>
                        <input type="text" name="kategori" id="kategori"
                               class="form-control form-control-sm inp-ro text-center"
                               value="{{ $old('kategori') }}" readonly>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3">
                        <label class="lbl-sm">UTDD Lain</label>
                        <input type="text" name="utdd_lain" id="utddLain"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('utdd_lain') }}" readonly>
                    </div>
                    <div class="col-xl-1 col-lg-1 col-md-2">
                        <label class="lbl-sm">Jns Biaya</label>
                        <input type="text" name="jns_biaya" id="jnsBiaya"
                               class="form-control form-control-sm inp-ro"
                               value="{{ $old('jns_biaya') }}" readonly>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3">
                        <label class="lbl-sm">No. Registrasi Online</label>
                        <input type="text" name="no_registrasi_online"
                               class="form-control form-control-sm {{ $isView ? 'inp-ro' : '' }}"
                               value="{{ $old('no_registrasi_online') }}" {{ $isView ? 'readonly' : '' }}>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-3">
                        <label class="lbl-sm">Tgl. Registrasi Online</label>
                        <input type="datetime-local" name="tgl_registrasi_online"
                               class="form-control form-control-sm {{ $isView ? 'inp-ro' : '' }}"
                               value="{{ $old('tgl_registrasi_online') ? \Carbon\Carbon::parse($old('tgl_registrasi_online'))->format('Y-m-d\TH:i') : '' }}"
                               {{ $isView ? 'readonly' : '' }}>
                    </div>
                </div>
            </div>{{-- /section-fpup --}}

            {{-- ══ PILIH DARAH + PANEL KIRIM ══ --}}
            <div class="row g-2 mb-row">

                <div class="col-xl-8 col-lg-7 col-md-7">
                    <div class="section-label">
                        Pilih Jenis Darah
                        <small class="text-muted fw-normal">(dari FPUP — klik baris untuk memilih)</small>
                    </div>
                    <div class="table-responsive"
                         style="max-height:150px; overflow-y:auto; border:1px solid #90caf9; border-radius:4px;">
                        <table class="table table-sm tbl-jenis mb-0" id="tblJenisDarah">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:30px">No</th>
                                    <th>Jns Darah</th>
                                    <th class="text-center" style="width:42px">Gol</th>
                                    <th style="width:68px">Rhesus</th>
                                    <th class="text-center" style="width:46px">CC</th>
                                    <th class="text-center" style="width:52px">Jumlah</th>
                                    <th class="text-center" style="width:62px">Dipenuhi</th>
                                </tr>
                            </thead>
                            <tbody id="bodyJenisDarah">
                                <tr id="rowJenisEmpty">
                                    <td colspan="7" class="text-center text-muted py-3">
                                        <i class="bi bi-arrow-up-circle me-1"></i>Scan FPUP untuk memuat data
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5 col-md-5">
                    <div class="panel-kirim">
                        <div class="kirim-title">Jns Darah yang Dikirim</div>
                        <div class="mb-2">
                            <span class="darah-badge" id="badgeJnsDarah">-</span>
                            <span class="darah-badge" id="badgeGolDarah">-</span>
                            <span class="darah-badge" id="badgeRhDarah">-</span>
                        </div>
                        <input type="hidden" name="jns_darah_kirim" id="jnsDarahKirim">
                        <input type="hidden" name="gol_darah_kirim" id="golDarahKirim">
                        <input type="hidden" name="rh_kirim"        id="rhKirim">

                        <div class="row g-2">
                            <div class="col-4">
                                <label class="lbl-sm">Jml Kantong</label>
                                <input type="number" name="jumlah_kantong" id="jumlahKantong"
                                       class="form-control form-control-sm inp-ro text-center"
                                       value="{{ $old('jumlah_kantong', 0) }}" readonly min="0">
                            </div>
                            <div class="col-4">
                                <label class="lbl-sm">Dilayani</label>
                                <input type="number" name="dilayani" id="dilayani"
                                       class="form-control form-control-sm {{ $isView ? 'inp-ro' : '' }} text-center"
                                       value="{{ $old('dilayani', 0) }}" {{ $isView ? 'readonly' : '' }} min="0">
                            </div>
                            <div class="col-4">
                                <label class="lbl-sm">Jml Stok</label>
                                <div class="fw-bold text-primary fs-6 pt-1 text-center" id="totalStok">
                                    {{ count($details) }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="lbl-sm">Kurir RS</label>
                            <input type="text" name="kurir_rs"
                                   class="form-control form-control-sm {{ $isView ? 'inp-ro' : '' }}"
                                   value="{{ $old('kurir_rs') }}" {{ $isView ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ WARNING STOCK ══ --}}
            <div id="stockWarning"></div>

            {{-- ══ OPSI CHECKBOX — sesuai tampilan: "Pasien Bayi" ══ --}}
            <div class="d-flex align-items-center gap-3 mb-row">
                <div class="form-check form-check-inline mb-0">
                    <input class="form-check-input" type="checkbox" name="is_pasien_bayi" id="isPasienBayi"
                           value="1" {{ $old('is_pasien_bayi', $isEdit ? $pemberianDarah->is_pasien_bayi : false) ? 'checked' : '' }}
                           {{ $isView ? 'disabled' : '' }}>
                    <label class="form-check-label" style="font-size:.78rem" for="isPasienBayi">Pasien Bayi</label>
                </div>
            </div>

            {{-- ══ TABEL STOK DARAH — kolom Metode/Hasil/Keterangan per kantong ══ --}}
            <div class="mb-row">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="section-label">Detail Stok Darah</div>
                    @if(!$isView)
                    <div class="d-flex gap-2">
                        <input type="text" id="inputNostock"
                               class="form-control form-control-sm inp-scan"
                               style="width:180px"
                               placeholder="Scan / Ketik Nostock"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();doScanStock();}">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="doScanStock()">
                            <i class="bi bi-upc-scan me-1"></i>Scan Stok
                        </button>
                    </div>
                    @endif
                </div>
                <div class="table-responsive" style="border:1px solid #90a4ae; border-radius:4px;">
                    <table class="table table-sm tbl-stok mb-0 w-100" id="tblStok">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:40px">No</th>
                                <th style="width:150px">Nostock</th>
                                <th style="width:80px">Jns</th>
                                <th class="text-center" style="width:55px">Gol</th>
                                <th style="width:70px">Rh</th>
                                <th style="width:100px">Tgl Expired</th>
                                <th style="width:110px">Metode</th>
                                <th style="width:110px">Hasil</th>
                                <th>Keterangan</th>
                                @if(!$isView)
                                <th class="text-center" style="width:50px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="bodyStok">
                            @if(count($details) === 0)
                            <tr id="rowStokEmpty">
                                <td colspan="{{ $isView ? 9 : 10 }}" class="text-center text-muted py-3">
                                    <i class="bi bi-box-seam me-1"></i>Belum ada stok dipilih
                                </td>
                            </tr>
                            @else
                                @foreach($details as $i => $d)
                                <tr>
                                    <td class="text-center row-num">{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $d['nostock'] ?? '-' }}</td>
                                    <td>{{ $d['jns_darah'] ?? '-' }}</td>
                                    <td class="text-center">{{ $d['gol'] ?? '-' }}</td>
                                    <td>{{ $d['rh'] ?? '-' }}</td>
                                    <td>{{ isset($d['tgl_expired']) ? \Carbon\Carbon::parse($d['tgl_expired'])->format('d-m-Y') : '-' }}</td>
                                    <td>
                                        @if($isView)
                                            {{ $d['metode'] ?? '-' }}
                                        @else
                                            <select class="inp-row" name="details[{{ $i }}][metode]">
                                                <option value="">-- Pilih --</option>
                                                @foreach(['GEL','Tube Method','Slide Method','Lainnya'] as $m)
                                                    <option value="{{ $m }}" {{ ($d['metode'] ?? '') === $m ? 'selected' : '' }}>{{ $m }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isView)
                                            {{ $d['hasil'] ?? '-' }}
                                        @else
                                            <select class="inp-row" name="details[{{ $i }}][hasil]">
                                                <option value="">-- Pilih --</option>
                                                @foreach(['Cocok','Tidak Cocok','Lainnya'] as $h)
                                                    <option value="{{ $h }}" {{ ($d['hasil'] ?? '') === $h ? 'selected' : '' }}>{{ $h }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isView)
                                            {{ $d['keterangan'] ?? '-' }}
                                        @else
                                            <input type="text" class="inp-row" name="details[{{ $i }}][keterangan]" value="{{ $d['keterangan'] ?? '' }}">
                                        @endif
                                    </td>
                                    @if(!$isView)
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1"
                                                onclick="removeStokRow(this)" title="Hapus Baris">
                                            <i class="bi bi-x-lg" style="font-size:.7rem"></i>
                                        </button>
                                    </td>
                                    @endif
                                    <input type="hidden" name="details[{{ $i }}][nostock]"     value="{{ $d['nostock'] ?? '' }}">
                                    <input type="hidden" name="details[{{ $i }}][jns_darah]"   value="{{ $d['jns_darah'] ?? '' }}">
                                    <input type="hidden" name="details[{{ $i }}][gol]"         value="{{ $d['gol'] ?? '' }}">
                                    <input type="hidden" name="details[{{ $i }}][rh]"          value="{{ $d['rh'] ?? '' }}">
                                    <input type="hidden" name="details[{{ $i }}][tgl_expired]" value="{{ $d['tgl_expired'] ?? '' }}">
                                    @if($isView)
                                    <input type="hidden" name="details[{{ $i }}][keterangan]"  value="{{ $d['keterangan'] ?? '' }}">
                                    @endif
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-1">
                    <strong class="me-2">Jumlah:</strong>
                    <span id="totalStokFooter">{{ count($details) }}</span>
                </div>
            </div>

        </div>{{-- /p-2 --}}

        {{-- ══ FOOTER BUTTONS ══ --}}
        @if(!$isView)
        <div class="footer-bar d-flex justify-content-between align-items-center">
            <a href="{{ route('referal.pemberian_darah.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-x-circle me-1"></i>Batal
            </a>
            <button type="submit" class="btn btn-primary btn-sm px-5">
                <i class="bi bi-save me-1"></i>{{ $isEdit ? 'Update' : 'Simpan' }}
            </button>
        </div>
        @else
        <div class="footer-bar d-flex gap-2">
            <a href="{{ route('referal.pemberian_darah.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('referal.pemberian_darah.edit', $pemberianDarah) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        </div>
        @endif

    </div>{{-- /card-main --}}
</form>
</div>{{-- /form-wrapper --}}
@endsection

@push('scripts')
<script>
/* ================================================================
   CONFIG
================================================================ */
const CSRF_TOKEN       = '{{ csrf_token() }}';
const URL_SCAN_FPUP    = '{{ route('referal.pemberian_darah.scan_fpup') }}';
const URL_SCAN_STOCK   = '{{ route('referal.pemberian_darah.scan_stock') }}';
const URL_SCAN_PETUGAS = '{{ route('referal.pemberian_darah.scan_petugas') }}';
const IS_VIEW = {{ $isView ? 'true' : 'false' }};

let detailIdx      = {{ count($details) }};
let jenisDarahList = [];

/* ================================================================
   SCAN FPUP
================================================================ */
async function doScanFpup() {
    const noFpup = document.getElementById('noFpup').value.trim();
    if (!noFpup) { alert('Masukkan No. FPUP terlebih dahulu.'); return; }

    try {
        const res  = await fetchPost(URL_SCAN_FPUP, { no_fpup: noFpup });
        const data = res.data;
        const warningBox = document.getElementById('fpupWarning');

        warningBox.innerHTML = data.warning
            ? `<div class="alert alert-warning alert-dismissible fade show py-2 mb-2">
                   <strong>Peringatan!</strong> ${data.warning_message}
                   <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>`
            : '';

        setVal('tglFpup',        data.tgl_fpup ? data.tgl_fpup.replace(' ', 'T').slice(0,16) : '');
        setVal('dokter',         data.dokter           || '');
        setVal('kodeRs',         data.kode_rs          || '');
        setVal('namaRs',         data.nama_rs          || '');
        setVal('pasien',         data.pasien           || '');
        setVal('jenisRs',        data.jenis_rs         || '');
        setVal('kelasRawat',     data.kelas_rawat      || '');
        setVal('golDarahPasien', data.gol_darah_pasien || '');
        setVal('rhPasien',       data.rh_pasien        || '');
        setVal('kategori',       data.kategori         || '');
        setVal('utddLain',       data.utdd_lain        || '');
        setVal('jnsBiaya',       data.jns_biaya        || '');

        document.querySelector('[name="no_registrasi_online"]').value =
            data.no_registrasi_online || '';
        document.querySelector('[name="tgl_registrasi_online"]').value =
            data.tgl_registrasi_online
                ? data.tgl_registrasi_online.replace(' ', 'T').substring(0, 16)
                : '';

        jenisDarahList = data.jenis_darah || [];
        renderJenisDarah(jenisDarahList);

    } catch (err) {
        alert('Gagal scan FPUP: ' + err.message);
    }
}

function renderJenisDarah(list) {
    const tbody = document.getElementById('bodyJenisDarah');
    if (!list.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-2">
            Tidak ada jenis darah pada FPUP ini</td></tr>`;
        return;
    }
    tbody.innerHTML = list.map((d, i) => `
        <tr onclick="selectJenisDarah(this, ${i})" style="cursor:pointer">
            <td class="text-center">${i + 1}</td>
            <td>${d.jns_darah || '-'}</td>
            <td class="text-center">${d.gol || '-'}</td>
            <td>${d.rh || '-'}</td>
            <td class="text-center">${d.cc || 0}</td>
            <td class="text-center">${d.jumlah || 0}</td>
            <td class="text-center dipenuhi-cell">${d.dipenuhi || 0}</td>
        </tr>
    `).join('');
}

function selectJenisDarah(row, idx) {
    document.querySelectorAll('#tblJenisDarah tbody tr')
            .forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');

    const d = jenisDarahList[idx];
    if (!d) return;

    document.getElementById('badgeJnsDarah').textContent = d.jns_darah || '-';
    document.getElementById('badgeGolDarah').textContent = d.gol       || '-';
    document.getElementById('badgeRhDarah').textContent  = d.rh        || '-';
    document.getElementById('jnsDarahKirim').value = d.jns_darah || '';
    document.getElementById('golDarahKirim').value = d.gol       || '';
    document.getElementById('rhKirim').value        = d.rh        || '';
    document.getElementById('jumlahKantong').value  = d.jumlah    || 0;
    document.getElementById('dilayani').value       = d.jumlah    || 0;
}

function updateDipenuhiTable() {
    const jenisDipilih = document.getElementById('jnsDarahKirim').value;
    let total = 0;
    document.querySelectorAll('#bodyStok tr').forEach(row => {
        const kolomJenis = row.children[2];
        if (kolomJenis && kolomJenis.innerText.trim() === jenisDipilih) total++;
    });
    const selectedRow = document.querySelector('#tblJenisDarah tbody tr.selected');
    if (selectedRow) {
        const cell = selectedRow.querySelector('.dipenuhi-cell');
        if (cell) cell.textContent = total;
    }
}

/* ================================================================
   SCAN STOCK
================================================================ */
async function doScanStock() {
    const noFpup  = document.getElementById('noFpup').value.trim();
    const jnsFpup = document.getElementById('jnsDarahKirim').value;
    const golFpup = document.getElementById('golDarahKirim').value;
    const rhFpup  = document.getElementById('rhKirim').value;
    const nostock = document.getElementById('inputNostock').value.trim();

    if (!noFpup)  { alert('Scan No. FPUP terlebih dahulu.'); return; }
    if (!jnsFpup) { alert('Pilih jenis darah dari tabel FPUP terlebih dahulu.'); return; }
    if (!nostock) { alert('Masukkan No Stock.'); return; }

    const existing = [...document.querySelectorAll('#bodyStok tr input[name*="[nostock]"]')]
        .map(i => i.value);
    if (existing.includes(nostock)) {
        alert('Nostock ' + nostock + ' sudah ada di tabel.'); return;
    }

    try {
        const res = await fetchPost(URL_SCAN_STOCK, { nostock });

        if (res.data.warning) {
            document.getElementById('stockWarning').innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show py-2 mb-2">
                    <strong>Peringatan!</strong> ${res.data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            return;
        }

        if (res.data.jns_darah !== jnsFpup) {
            alert('Jenis darah tidak sesuai.\nFPUP: ' + jnsFpup + '\nStok: ' + res.data.jns_darah);
            return;
        }
        if (res.data.gol !== golFpup) {
            alert('Golongan darah tidak sesuai.\nFPUP: ' + golFpup + '\nStok: ' + res.data.gol);
            return;
        }
        if (res.data.rh !== rhFpup) {
            alert('Rhesus tidak sesuai.\nFPUP: ' + rhFpup + '\nStok: ' + res.data.rh);
            return;
        }

        addStokRow(res.data);
        document.getElementById('inputNostock').value = '';
        document.getElementById('inputNostock').focus();

    } catch (err) {
        alert('Gagal scan stok: ' + err.message);
    }
}

function addStokRow(d) {
    const empty = document.getElementById('rowStokEmpty');
    if (empty) empty.remove();

    const tbody    = document.getElementById('bodyStok');
    const rowCount = tbody.querySelectorAll('tr').length + 1;
    const idx      = detailIdx++;

    const expFormatted = d.tgl_expired
        ? new Date(d.tgl_expired).toLocaleDateString('id-ID',
            {day:'2-digit', month:'2-digit', year:'numeric'})
        : '-';

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="text-center row-num">${rowCount}</td>
        <td class="fw-semibold">${d.nostock}</td>
        <td>${d.jns_darah || '-'}</td>
        <td class="text-center">${d.gol || '-'}</td>
        <td>${d.rh || '-'}</td>
        <td>${expFormatted}</td>
        <td>${selectOptionsHtml(idx, 'metode', ['GEL','Tube Method','Slide Method','Lainnya'], d.metode)}</td>
        <td>${selectOptionsHtml(idx, 'hasil', ['Cocok','Tidak Cocok','Lainnya'], d.hasil)}</td>
        <td><input type="text" class="inp-row" name="details[${idx}][keterangan]" value="${d.keterangan || ''}"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1"
                    onclick="removeStokRow(this)" title="Hapus">
                <i class="bi bi-x-lg" style="font-size:.7rem"></i>
            </button>
        </td>
        <input type="hidden" name="details[${idx}][nostock]"     value="${d.nostock}">
        <input type="hidden" name="details[${idx}][jns_darah]"   value="${d.jns_darah || ''}">
        <input type="hidden" name="details[${idx}][gol]"         value="${d.gol || ''}">
        <input type="hidden" name="details[${idx}][rh]"          value="${d.rh || ''}">
        <input type="hidden" name="details[${idx}][tgl_expired]" value="${d.tgl_expired || ''}">
    `;
    tbody.appendChild(tr);
    updateTotalStok();
    updateDipenuhiTable();
}

function selectOptionsHtml(idx, field, options, selected) {
    const opts = ['<option value="">-- Pilih --</option>']
        .concat(options.map(o => `<option value="${o}" ${o === selected ? 'selected' : ''}>${o}</option>`))
        .join('');
    return `<select class="inp-row" name="details[${idx}][${field}]">${opts}</select>`;
}

function removeStokRow(btn) {
    if (!confirm('Hapus baris stok ini?')) return;
    btn.closest('tr').remove();
    document.querySelectorAll('#bodyStok tr .row-num').forEach((el, i) => {
        el.textContent = i + 1;
    });
    if (document.querySelectorAll('#bodyStok tr').length === 0) {
        document.getElementById('bodyStok').innerHTML =
            `<tr id="rowStokEmpty"><td colspan="10" class="text-center text-muted py-3">
             <i class="bi bi-box-seam me-1"></i>Belum ada stok dipilih</td></tr>`;
    }
    updateTotalStok();
    updateDipenuhiTable();
}

function updateTotalStok() {
    const rows  = document.querySelectorAll('#bodyStok tr:not(#rowStokEmpty)');
    const total = rows.length;
    document.getElementById('totalStok').textContent       = total;
    document.getElementById('totalStokFooter').textContent = total;
    document.getElementById('dilayani').value               = total;
}

/* ================================================================
   SCAN PETUGAS (Enter di field kode)
================================================================ */
document.getElementById('petugasKode')?.addEventListener('keydown', async function(e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const kode = this.value.trim();
    if (!kode) return;
    try {
        const res = await fetchPost(URL_SCAN_PETUGAS, { kode });
        document.getElementById('petugasNama').value = res.data.nama || '';
    } catch(err) {
        alert('Gagal scan petugas: ' + err.message);
    }
});

/* ================================================================
   HELPER
================================================================ */
function setVal(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}

async function fetchPost(url, body) {
    const res = await fetch(url, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept':       'application/json',
        },
        body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!res.ok || !json.success) {
        throw new Error(json.message || 'Server error ' + res.status);
    }
    return json;
}

/* ================================================================
   JAM KELUAR — auto update tiap menit
================================================================ */
function updateJam() {
    const now = new Date();
    const hh  = String(now.getHours()).padStart(2,'0');
    const mm  = String(now.getMinutes()).padStart(2,'0');
    const el  = document.querySelector('[name="jam_keluar"]');
    if (el) el.value = hh + ':' + mm;
}
@if(!$isView)
updateJam();
setInterval(updateJam, 60000);
@endif

/* ================================================================
   INIT EDIT MODE
================================================================ */
@if($isEdit)
document.getElementById('badgeJnsDarah').textContent = '{{ $pemberianDarah->jns_darah_kirim ?? "-" }}';
document.getElementById('badgeGolDarah').textContent = '{{ $pemberianDarah->gol_darah_kirim ?? "-" }}';
document.getElementById('badgeRhDarah').textContent  = '{{ $pemberianDarah->rh_kirim ?? "-" }}';
document.getElementById('jnsDarahKirim').value = '{{ $pemberianDarah->jns_darah_kirim ?? "" }}';
document.getElementById('golDarahKirim').value = '{{ $pemberianDarah->gol_darah_kirim ?? "" }}';
document.getElementById('rhKirim').value        = '{{ $pemberianDarah->rh_kirim ?? "" }}';
@endif
</script>
@endpush