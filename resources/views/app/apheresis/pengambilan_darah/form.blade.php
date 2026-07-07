@extends('layouts.index')

@section('title', 'Input Data Pengambilan Darah Apheresis')

@push('styles')
<style>
    .lk-card { border: 1px solid #d9d9d9; border-radius: 6px; }
    .lk-header {
        background: linear-gradient(90deg,#7a1f2b,#a12d3a);
        color: #fff; padding: .6rem 1rem; border-radius: 6px 6px 0 0;
        font-weight: 600; font-size: 1.05rem;
    }
    .form-label-sm { font-size:.85rem; color:#333; margin-bottom:.15rem; }
    fieldset.box { border:1px solid #ccc; border-radius:6px; }
    fieldset.box legend { font-size:.85rem; font-weight:700; color:#7a1f2b; width:auto; float:none; padding:0 .5rem; }
    .siklus-table input { min-width: 90px; }
    .siklus-table th { font-size:.75rem; white-space:nowrap; }
    .hint-zero { font-size:.8rem; color:#7a1f2b; font-weight:600; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    <div class="lk-card">
        <div class="lk-header">Lembar Kerja - Input Data Pengambilan Darah Apheresis</div>

        @if($errors->any())
            <div class="alert alert-danger m-3 mb-0">
                <strong><i class="fa fa-exclamation-triangle me-1"></i> Data belum tersimpan, ada {{ $errors->count() }} kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success m-3 mb-0 alert-dismissible fade show">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="form-lembar-kerja"
              action="{{ $isEdit ? route('apheresis.pengambilan_darah.update', $lembarKerja->id) : route('apheresis.pengambilan_darah.store') }}"
              method="POST">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="p-3">
                <div class="row g-3">

                    {{-- ===================== KOLOM KIRI: HEADER & DATA DONOR ===================== --}}
                    <div class="col-lg-5">

                        <fieldset class="box p-3 mb-3">
                            <legend>Header</legend>
                            <div class="row g-2">
                                <div class="col-md-7">
                                    <label class="form-label-sm">No Transaksi</label>
                                    <div class="input-group">
                                        <input type="text" name="no_transaksi" id="no_transaksi" class="form-control fw-semibold"
                                               value="{{ old('no_transaksi', $lembarKerja->no_transaksi) }}" readonly>
                                        <button type="button" id="btn-generate" class="btn btn-outline-secondary" title="Generate No Transaksi">
                                            <i class="fa fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label-sm">Server Date</label>
                                    <input type="text" id="server_date" class="form-control"
                                           value="{{ optional($lembarKerja->server_date ?? now())->format('Y-m-d H:i') }}" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-sm">Petugas</label>
                                    <select name="petugas_id" id="petugas_id" class="form-select" style="width:100%">
                                        @php
                                            $petugasTerpilih = $lembarKerja->petugas ?? auth()->user();
                                            $petugasIdTerpilih = old('petugas_id', $lembarKerja->petugas_id ?? auth()->id());
                                        @endphp
                                        @if($petugasTerpilih)
                                            <option value="{{ $petugasIdTerpilih }}" selected>
                                                {{ $petugasTerpilih->kode_petugas ?? '' }} - {{ $petugasTerpilih->name ?? '' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="box p-3 mb-3">
                            <legend>Data Donor</legend>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label-sm">No Sampling <span class="text-muted">(F1)</span></label>
                                    <input type="text" name="no_sampling" id="no_sampling" class="form-control" autocomplete="off"
                                           value="{{ old('no_sampling', $lembarKerja->no_sampling) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">No Donor <span class="text-muted">(scan di sini)</span></label>
                                    <input type="text" name="no_donor" id="no_donor" class="form-control" autocomplete="off"
                                           value="{{ old('no_donor', $lembarKerja->no_donor) }}">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label-sm">Nama Donor</label>
                                    <input type="text" name="nama_donor" id="nama_donor" class="form-control @error('nama_donor') is-invalid @enderror"
                                           value="{{ old('nama_donor', $lembarKerja->nama_donor) }}" required>
                                    @error('nama_donor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Tgl Lahir</label>
                                    <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control"
                                           value="{{ old('tgl_lahir', optional($lembarKerja->tgl_lahir)->format('Y-m-d')) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label-sm d-block">Golongan Darah</label>
                                    @foreach(['A','B','AB','O'] as $val)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="golongan_darah"
                                                   id="gol_{{ $val }}" value="{{ $val }}"
                                                   {{ old('golongan_darah', $lembarKerja->golongan_darah) === $val ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gol_{{ $val }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm d-block">Rhesus</label>
                                    @foreach(['positif' => 'Positif', 'negatif' => 'Negatif'] as $val => $label)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rhesus" id="rhesus_{{ $val }}"
                                                   value="{{ $val }}" {{ old('rhesus', $lembarKerja->rhesus) === $val ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rhesus_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm d-block">Jenis Kelamin</label>
                                    @foreach(['pria' => 'Pria', 'wanita' => 'Wanita'] as $val => $label)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin"
                                                   id="jk_{{ $val }}" value="{{ $val }}"
                                                   {{ old('jenis_kelamin', $lembarKerja->jenis_kelamin) === $val ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="jk_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-sm d-block">Riwayat Donor Sebelumnya</label>
                                    @foreach(['pernah' => 'Pernah', 'tidak_pernah' => 'Tidak Pernah'] as $val => $label)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="riwayat_donor_sebelumnya"
                                                   id="rds_{{ $val }}" value="{{ $val }}"
                                                   {{ old('riwayat_donor_sebelumnya', $lembarKerja->riwayat_donor_sebelumnya) === $val ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rds_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                    <input type="number" min="0" name="riwayat_donor_sebelumnya_kali" class="form-control form-control-sm mt-1" style="width:100px"
                                           placeholder="Kali" value="{{ old('riwayat_donor_sebelumnya_kali', $lembarKerja->riwayat_donor_sebelumnya_kali) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm d-block">Riwayat Donor Apheresis</label>
                                    @foreach(['pernah' => 'Pernah', 'tidak_pernah' => 'Tidak Pernah'] as $val => $label)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="riwayat_donor_apheresis"
                                                   id="rda_{{ $val }}" value="{{ $val }}"
                                                   {{ old('riwayat_donor_apheresis', $lembarKerja->riwayat_donor_apheresis) === $val ? 'checked' : '' }}>
                                            <label class="form-check-label" for="rda_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                    <input type="number" min="0" name="riwayat_donor_apheresis_kali" class="form-control form-control-sm mt-1" style="width:100px"
                                           placeholder="Kali" value="{{ old('riwayat_donor_apheresis_kali', $lembarKerja->riwayat_donor_apheresis_kali) }}">
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="box p-3">
                            <legend>Alat &amp; Bahan</legend>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label-sm">Type Mesin</label>
                                    <input type="text" name="type_mesin" class="form-control" value="{{ old('type_mesin', $lembarKerja->type_mesin) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">No Mesin</label>
                                    <input type="text" name="no_mesin" class="form-control" value="{{ old('no_mesin', $lembarKerja->no_mesin) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Operator</label>
                                    <select name="operator" id="operator" class="form-select operator-select" style="width:100%">
                                        @php $operatorVal = old('operator', $lembarKerja->operator); @endphp
                                        @if($operatorVal)
                                            <option value="{{ $operatorVal }}" selected>{{ $operatorVal }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Kode Disposable Kit</label>
                                    <input type="text" name="kode_disposable_kit" class="form-control" value="{{ old('kode_disposable_kit', $lembarKerja->kode_disposable_kit) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Type AC (Ratio)</label>
                                    <input type="text" name="type_ac_ratio" class="form-control" value="{{ old('type_ac_ratio', $lembarKerja->type_ac_ratio) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Cairan Saline</label>
                                    <input type="text" name="cairan_saline" class="form-control" value="{{ old('cairan_saline', $lembarKerja->cairan_saline) }}">
                                </div>

                                <div class="col-12 d-flex justify-content-between align-items-center mt-2">
                                    <label class="form-label-sm mb-0 fw-semibold">No Lot &amp; Kadaluarsa</label>
                                    @php $lot3Filled = filled(old('no_lot_3', $lembarKerja->no_lot_3)); @endphp
                                    <button type="button" id="btn-add-lot" class="btn btn-sm btn-outline-primary"
                                            style="{{ $lot3Filled ? 'display:none;' : '' }}">
                                        <i class="fa fa-plus"></i> Tambah No Lot
                                    </button>
                                </div>

                                @foreach([1, 2, 3] as $n)
                                    @php $isFilled = filled(old("no_lot_{$n}", $lembarKerja->{"no_lot_{$n}"})); @endphp
                                    <div class="col-md-7 lot-row" id="lot-row-{{ $n }}" style="{{ $n > 1 && !$isFilled ? 'display:none;' : '' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="form-label-sm mb-0">No Lot {{ $n }}</label>
                                            @if($n > 1)
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-lot"
                                                        data-lot="{{ $n }}" title="Batal, hapus baris ini">
                                                    <i class="fa fa-times"></i> Hapus
                                                </button>
                                            @endif
                                        </div>
                                        <input type="text" name="no_lot_{{ $n }}" class="form-control"
                                               value="{{ old("no_lot_{$n}", $lembarKerja->{"no_lot_{$n}"}) }}">
                                    </div>
                                    <div class="col-md-5 lot-row" id="lot-row-kadaluarsa-{{ $n }}" style="{{ $n > 1 && !$isFilled ? 'display:none;' : '' }}">
                                        <label class="form-label-sm">Kadaluarsa</label>
                                        <input type="date" name="kadaluarsa_lot_{{ $n }}" class="form-control"
                                               value="{{ old("kadaluarsa_lot_{$n}", optional($lembarKerja->{"kadaluarsa_lot_{$n}"})->format('Y-m-d')) }}">
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    </div>

                    {{-- ===================== KOLOM KANAN: HAEMOCALCULATOR, PROSEDUR, dst ===================== --}}
                    <div class="col-lg-7">

                        <fieldset class="box p-3 mb-3">
                            <legend>Haemocalculator (Sebelum Prosedur)</legend>
                            <div class="row g-2">
                                @php
                                    $haemoPre = [
                                        'tinggi_badan' => 'Tinggi Badan (cm)',
                                        'berat_badan' => 'Berat Badan (kg)',
                                        'hct' => 'HCT (%)',
                                        'platelet_precount' => 'Platelet Precount',
                                        'target_vol_plasma' => 'Target Vol Plasma (ml)',
                                        'target_platelet_yield' => 'Target Platelet (Yield)',
                                        'target_cycle' => 'Target Cycle',
                                        'target_waktu' => 'Target Waktu',
                                        'estimasi_vol_plt' => 'Estimasi Vol PLT (ml)',
                                    ];
                                @endphp
                                @foreach($haemoPre as $field => $label)
                                    <div class="col-md-4">
                                        <label class="form-label-sm">{{ $label }}</label>
                                        <input type="text" name="{{ $field }}" id="{{ $field }}" class="form-control form-control-sm"
                                               value="{{ old($field, $lembarKerja->$field) }}">
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>

                        <fieldset class="box p-3 mb-3">
                            <legend>Prosedur</legend>
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label-sm">Waktu Mulai</label>
                                    <input type="time" name="waktu_mulai" id="waktu_mulai" class="form-control form-control-sm"
                                           value="{{ old('waktu_mulai', $lembarKerja->waktu_mulai) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Waktu Selesai</label>
                                    <input type="time" name="waktu_selesai" id="waktu_selesai" class="form-control form-control-sm"
                                           value="{{ old('waktu_selesai', $lembarKerja->waktu_selesai) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label-sm">Durasi <span class="text-muted">(otomatis)</span></label>
                                    <input type="text" name="durasi" id="durasi" class="form-control form-control-sm" readonly
                                           value="{{ old('durasi', $lembarKerja->durasi) }}">
                                </div>
                                @php
                                    $prosedur = [
                                        'vol_wb_terproses' => 'Vol WB Terproses (ml)',
                                        'vol_ac_terpakai' => 'Vol AC Terpakai (ml)',
                                        'vol_saline_terpakai' => 'Vol Saline Terpakai (ml)',
                                        'draw_rate' => 'Draw Rate (ml/menit)',
                                        'return_rate' => 'Return Rate (ml/menit)',
                                        'plt_hct_postcount' => 'PLT/HCT Postcount',
                                    ];
                                @endphp
                                @foreach($prosedur as $field => $label)
                                    <div class="col-md-4">
                                        <label class="form-label-sm">{{ $label }}</label>
                                        <input type="text" name="{{ $field }}" class="form-control form-control-sm"
                                               value="{{ old($field, $lembarKerja->$field) }}">
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="hint-zero"><i class="fa fa-info-circle me-1"></i>Jika Hasilnya Kosong, Maka Isikan Angka 0 (Nol)</span>
                                <button type="button" id="btn-add-siklus" class="btn btn-sm btn-outline-primary">
                                    <i class="fa fa-plus"></i> Tambah Siklus
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm siklus-table mb-0" id="siklus-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px;">Siklus</th>
                                            <th>Jam</th>
                                            <th>Draw/Return (ml)</th>
                                            <th>Draw/Return (menit)</th>
                                            <th>Plasma Vol</th>
                                            <th>Platelet Yield</th>
                                            <th>Plasma Vol</th>
                                            <th>NaCl/Sitrat</th>
                                            <th>Keterangan</th>
                                            <th style="width:36px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="siklus-tbody">
                                        @forelse(old('siklus', optional($lembarKerja->siklus)->toArray() ?? []) as $i => $row)
                                            <tr>
                                                <td class="text-center align-middle">
                                                    <input type="number" name="siklus[{{ $i }}][siklus_ke]" class="form-control form-control-sm text-center"
                                                           value="{{ $row['siklus_ke'] ?? ($i + 1) }}" style="min-width:50px;">
                                                </td>
                                                <td><input type="time" name="siklus[{{ $i }}][jam]" class="form-control form-control-sm" value="{{ $row['jam'] ?? '' }}"></td>
                                                <td><input type="number" step="0.01" name="siklus[{{ $i }}][draw_return_ml]" class="form-control form-control-sm" value="{{ $row['draw_return_ml'] ?? '' }}"></td>
                                                <td><input type="number" step="0.01" name="siklus[{{ $i }}][draw_return_menit]" class="form-control form-control-sm" value="{{ $row['draw_return_menit'] ?? '' }}"></td>
                                                <td><input type="number" step="0.01" name="siklus[{{ $i }}][plasma_vol]" class="form-control form-control-sm" value="{{ $row['plasma_vol'] ?? '' }}"></td>
                                                <td><input type="number" step="0.01" name="siklus[{{ $i }}][platelet_yield]" class="form-control form-control-sm" value="{{ $row['platelet_yield'] ?? '' }}"></td>
                                                <td><input type="number" step="0.01" name="siklus[{{ $i }}][plasma_vol_2]" class="form-control form-control-sm" value="{{ $row['plasma_vol_2'] ?? '' }}"></td>
                                                <td><input type="number" step="0.01" name="siklus[{{ $i }}][nacl_sitrat]" class="form-control form-control-sm" value="{{ $row['nacl_sitrat'] ?? '' }}"></td>
                                                <td><input type="text" name="siklus[{{ $i }}][keterangan]" class="form-control form-control-sm" value="{{ $row['keterangan'] ?? '' }}"></td>
                                                <td class="text-center align-middle">
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-siklus"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </fieldset>

                        <fieldset class="box p-3 mb-3">
                            <legend>Haemocalculator (Setelah Prosedur)</legend>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="fw-semibold text-secondary mb-1">PLATELET</div>
                                    @php
                                        $haemoPlt = [
                                            'platelet_total_vol_aktual' => 'Total Vol Aktual (ml)',
                                            'platelet_vol_plt' => 'Vol. PLT (ml)',
                                            'platelet_vol_plasma_dlm_plt' => 'Vol Plasma dlm PLT (ml)',
                                            'platelet_ac_dlm_plt' => 'A/C dlm PLT (ml)',
                                            'platelet_yield_plt' => 'Yield PLT',
                                        ];
                                    @endphp
                                    @foreach($haemoPlt as $field => $label)
                                        <div class="mb-2">
                                            <label class="form-label-sm">{{ $label }}</label>
                                            <input type="text" name="{{ $field }}" class="form-control form-control-sm"
                                                   value="{{ old($field, $lembarKerja->$field) }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-semibold text-secondary mb-1">PLASMA</div>
                                    @php
                                        $haemoPlasma = [
                                            'plasma_total_vol_aktual' => 'Total Vol Aktual (ml)',
                                            'plasma_vol_plasma' => 'Vol Plasma (ml)',
                                            'plasma_ac_dlm_plasma' => 'A/C dlm Plasma (ml)',
                                        ];
                                    @endphp
                                    @foreach($haemoPlasma as $field => $label)
                                        <div class="mb-2">
                                            <label class="form-label-sm">{{ $label }}</label>
                                            <input type="text" name="{{ $field }}" class="form-control form-control-sm"
                                                   value="{{ old($field, $lembarKerja->$field) }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="box p-3">
                            <legend>Catatan</legend>
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $lembarKerja->catatan) }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-sm">Operator</label>
                                    <select name="operator_akhir" id="operator_akhir" class="form-select operator-select" style="width:100%">
                                        @php $operatorAkhirVal = old('operator_akhir', $lembarKerja->operator_akhir); @endphp
                                        @if($operatorAkhirVal)
                                            <option value="{{ $operatorAkhirVal }}" selected>{{ $operatorAkhirVal }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>

            {{-- ===================== FOOTER ACTIONS ===================== --}}
            <div class="d-flex justify-content-end gap-2 border-top p-3">
                @if($isEdit)
                    <button type="button" class="btn btn-secondary" id="btn-cetak">
                        <i class="fa fa-print me-1"></i> Cetak Ulang
                    </button>
                @endif
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('apheresis.pengambilan_darah.index') }}" class="btn btn-danger px-4">
                    <i class="fa fa-times me-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const csrf = '{{ csrf_token() }}';

    // ---------- Dropdown pencarian Petugas (select2) ----------
    if (window.jQuery && jQuery.fn.select2) {
        // Field "Petugas" di header - menyimpan ID user
        jQuery('#petugas_id').select2({
            width: '100%',
            placeholder: 'Cari nama / kode petugas...',
            minimumInputLength: 1,
            allowClear: false,
            ajax: {
                url: `{{ route('apheresis.pengambilan_darah.search_petugas') }}`,
                dataType: 'json',
                delay: 300,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data }),
            },
        });

        // Field "Operator" (Alat & Bahan) & "Operator" (Catatan) - menyimpan teks nama,
        // boleh pilih dari daftar petugas ATAU ketik bebas (tags: true) untuk operator yang belum jadi user sistem.
        jQuery('.operator-select').select2({
            width: '100%',
            placeholder: 'Cari atau ketik nama operator...',
            minimumInputLength: 1,
            allowClear: true,
            tags: true,
            ajax: {
                url: `{{ route('apheresis.pengambilan_darah.search_petugas') }}?as_text=1`,
                dataType: 'json',
                delay: 300,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data }),
            },
        });
    } else {
        console.warn('select2 tidak ditemukan - pastikan library jQuery + select2 sudah dimuat di layout.');
    }

    // ---------- Generate No Transaksi ----------
    document.getElementById('btn-generate').addEventListener('click', function () {
        fetch(`{{ route('apheresis.pengambilan_darah.generate_kode') }}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('no_transaksi').value = data.no_transaksi;
                document.getElementById('server_date').value = data.server_date;
            });
    });

    // ---------- Scan / cari data donor ----------
    const noDonorInput = document.getElementById('no_donor');

    function cariDataDonor() {
        const noDonor = noDonorInput.value.trim();
        if (!noDonor) return;

        fetch(`{{ route('apheresis.pengambilan_darah.search_donor') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ no_donor: noDonor }),
        })
        .then(res => res.ok ? res.json() : null)
        .then(result => {
            if (!result || !result.found) {
                noDonorInput.classList.add('is-invalid');
                return;
            }
            noDonorInput.classList.remove('is-invalid');

            const d = result.data;
            if (d.no_donor) noDonorInput.value = d.no_donor;
            if (d.nama_donor) document.getElementById('nama_donor').value = d.nama_donor;
            if (d.tgl_lahir) document.getElementById('tgl_lahir').value = d.tgl_lahir;
            if (d.jenis_kelamin) {
                const el = document.getElementById('jk_' + d.jenis_kelamin);
                if (el) el.checked = true;
            }
            if (d.golongan_darah) {
                const el = document.getElementById('gol_' + d.golongan_darah);
                if (el) el.checked = true;
            }
            if (d.rhesus) {
                const el = document.getElementById('rhesus_' + d.rhesus);
                if (el) el.checked = true;
            }

            document.getElementById('nama_donor').focus();
        })
        .catch(() => noDonorInput.classList.add('is-invalid'));
    }

    noDonorInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cariDataDonor();
        }
    });
    noDonorInput.addEventListener('blur', cariDataDonor);

    // ---------- Cari data dari modul Sampling Pra Donor (via No Sampling) ----------
    const noSamplingInput = document.getElementById('no_sampling');

    function cariDataSampling() {
        const noSampling = noSamplingInput.value.trim();
        if (!noSampling) return;

        fetch(`{{ route('apheresis.pengambilan_darah.search_sampling') }}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ no_sampling: noSampling }),
        })
        .then(res => res.ok ? res.json() : null)
        .then(result => {
            if (!result || !result.found) {
                noSamplingInput.classList.add('is-invalid');
                return;
            }
            noSamplingInput.classList.remove('is-invalid');

            const d = result.data;
            if (d.no_donor) {
                noDonorInput.value = d.no_donor;
                noDonorInput.classList.remove('is-invalid');
            }
            if (d.nama_donor) document.getElementById('nama_donor').value = d.nama_donor;
            if (d.tgl_lahir) document.getElementById('tgl_lahir').value = d.tgl_lahir;
            if (d.jenis_kelamin) {
                const el = document.getElementById('jk_' + d.jenis_kelamin);
                if (el) el.checked = true;
            }
            if (d.golongan_darah) {
                const el = document.getElementById('gol_' + d.golongan_darah);
                if (el) el.checked = true;
            }
            if (d.rhesus) {
                const el = document.getElementById('rhesus_' + d.rhesus);
                if (el) el.checked = true;
            }
            if (d.hct) document.getElementById('hct').value = d.hct;

            if (d.status_lulus === 'tidak_lulus') {
                alert('Perhatian: hasil Sampling Pra Donor untuk No Sampling ini berstatus TIDAK LULUS. Mohon periksa kembali sebelum melanjutkan.');
            }

            document.getElementById('nama_donor').focus();
        })
        .catch(() => noSamplingInput.classList.add('is-invalid'));
    }

    noSamplingInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cariDataSampling();
        }
    });
    noSamplingInput.addEventListener('blur', cariDataSampling);

    // ---------- Hitung Durasi otomatis dari Waktu Mulai & Waktu Selesai ----------
    const waktuMulaiInput = document.getElementById('waktu_mulai');
    const waktuSelesaiInput = document.getElementById('waktu_selesai');
    const durasiInput = document.getElementById('durasi');

    function hitungDurasi() {
        const mulai = waktuMulaiInput.value;
        const selesai = waktuSelesaiInput.value;
        if (!mulai || !selesai) {
            durasiInput.value = '';
            return;
        }

        const [jm, mm] = mulai.split(':').map(Number);
        const [js, ms] = selesai.split(':').map(Number);

        let menitMulai = (jm * 60) + mm;
        let menitSelesai = (js * 60) + ms;

        // Kalau waktu selesai lebih kecil, anggap lewat tengah malam
        let selisih = menitSelesai - menitMulai;
        if (selisih < 0) selisih += 24 * 60;

        const jam = Math.floor(selisih / 60);
        const menit = selisih % 60;
        durasiInput.value = `${String(jam).padStart(2, '0')}:${String(menit).padStart(2, '0')}`;
    }

    waktuMulaiInput.addEventListener('change', hitungDurasi);
    waktuSelesaiInput.addEventListener('change', hitungDurasi);
    hitungDurasi(); // hitung langsung kalau form edit sudah punya nilai

    // ---------- Tambah baris No Lot (maks 3, sesuai kolom yang tersedia) ----------
    const btnAddLot = document.getElementById('btn-add-lot');
    btnAddLot.addEventListener('click', function () {
        for (const n of [2, 3]) {
            const row1 = document.getElementById(`lot-row-${n}`);
            const row2 = document.getElementById(`lot-row-kadaluarsa-${n}`);
            if (row1.style.display === 'none') {
                row1.style.display = '';
                row2.style.display = '';
                if (n === 3) btnAddLot.style.display = 'none'; // sudah maksimal 3
                return;
            }
        }
    });

    // ---------- Batal / hapus baris No Lot 2 & 3 ----------
    document.querySelectorAll('.btn-remove-lot').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const n = this.dataset.lot;
            const row1 = document.getElementById(`lot-row-${n}`);
            const row2 = document.getElementById(`lot-row-kadaluarsa-${n}`);

            // Kosongkan nilainya supaya tidak ikut tersimpan
            row1.querySelector('input').value = '';
            row2.querySelector('input').value = '';

            row1.style.display = 'none';
            row2.style.display = 'none';

            // Tombol "Tambah No Lot" muncul lagi kalau sebelumnya disembunyikan
            btnAddLot.style.display = '';
        });
    });

    // ---------- Tabel siklus dinamis ----------
    const tbody = document.getElementById('siklus-tbody');
    let siklusIndex = tbody.querySelectorAll('tr').length;

    function buatBarisSiklus() {
        const idx = siklusIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-center align-middle">
                <input type="number" name="siklus[${idx}][siklus_ke]" class="form-control form-control-sm text-center" value="${idx + 1}" style="min-width:50px;">
            </td>
            <td><input type="time" name="siklus[${idx}][jam]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="siklus[${idx}][draw_return_ml]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="siklus[${idx}][draw_return_menit]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="siklus[${idx}][plasma_vol]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="siklus[${idx}][platelet_yield]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="siklus[${idx}][plasma_vol_2]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" name="siklus[${idx}][nacl_sitrat]" class="form-control form-control-sm"></td>
            <td><input type="text" name="siklus[${idx}][keterangan]" class="form-control form-control-sm"></td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-siklus"><i class="fa fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    }

    document.getElementById('btn-add-siklus').addEventListener('click', buatBarisSiklus);

    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-remove-siklus');
        if (btn) btn.closest('tr').remove();
    });

    // Kalau form baru (belum ada baris), langsung sediakan 1 baris kosong
    if (tbody.querySelectorAll('tr').length === 0) {
        buatBarisSiklus();
    }

    @if($isEdit)
    document.getElementById('btn-cetak').addEventListener('click', function () {
        window.print();
    });
    @endif
})();
</script>
@endpush