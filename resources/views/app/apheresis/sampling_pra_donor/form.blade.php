@extends('layouts.index')

@section('title', 'Input Sampling Pra Donor')

@push('styles')
<style>
    .sml-card { border: 1px solid #d9d9d9; border-radius: 6px; }
    .sml-header {
        background: linear-gradient(90deg,#7a1f2b,#a12d3a);
        color: #fff; padding: .6rem 1rem; border-radius: 6px 6px 0 0;
        font-weight: 600; font-size: 1.05rem;
    }
    .sml-tabs { background:#3d3d3d; }
    .sml-tabs .nav-link { color:#ddd; border-radius:0; }
    .sml-tabs .nav-link.active { background:#7a1f2b; color:#fff; }
    .lab-group-title { font-weight:700; color:#7a1f2b; margin-bottom:.25rem; }
    .lab-row { display:flex; align-items:center; gap:.5rem; margin-bottom:.35rem; }
    .lab-row label { width:60px; font-size:.85rem; margin-bottom:0; flex-shrink:0; }
    .lab-row input.value { width:90px; }
    .lab-row .range { font-size:.75rem; color:#888; white-space:nowrap; }
    .form-label-sm { font-size:.85rem; color:#333; margin-bottom:.15rem; }
    .alasan-list .form-check { margin-bottom:.25rem; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    <div class="sml-tabs d-flex mb-2 rounded-top">
        <div class="nav-link active px-3 py-2"><i class="fa fa-flask me-1"></i> Input Sampling Pra Donor</div>
        <div class="nav-link px-3 py-2"><i class="fa fa-file-alt me-1"></i> Lembar Kerja Donor Sampling Apheresis</div>
        <div class="nav-link px-3 py-2"><i class="fa fa-chart-bar me-1"></i> Laporan</div>
        <div class="nav-link px-3 py-2 ms-auto"><i class="fa fa-times me-1"></i> Exit</div>
    </div>

    <div class="sml-card">
        <div class="sml-header">Input Sampling Pra Donor</div>

        <form id="form-sampling"
              action="{{ $isEdit ? route('apheresis.sampling_pra_donor.update', $sampling->id) : route('apheresis.sampling_pra_donor.store') }}"
              method="POST">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <div class="p-3">
                {{-- ===================== HEADER ===================== --}}
                <fieldset class="border rounded p-3 mb-3">
                    <legend class="float-none w-auto px-2 fs-6 fw-semibold text-secondary">Header</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label-sm">No Transaksi</label>
                            <div class="input-group">
                                <input type="text" name="no_transaksi" id="no_transaksi" class="form-control fw-semibold"
                                       value="{{ old('no_transaksi', $sampling->no_transaksi) }}" readonly>
                                <button type="button" id="btn-generate" class="btn btn-outline-secondary" title="Generate No Transaksi">
                                    <i class="fa fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-sm">Server Date</label>
                            <input type="text" id="server_date" class="form-control"
                                   value="{{ optional($sampling->server_date ?? now())->format('Y-m-d H:i') }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-sm">Petugas</label>
                            <input type="text" class="form-control"
                                   value="{{ $sampling->petugas->kode_petugas ?? auth()->user()->kode_petugas ?? '' }} - {{ $sampling->petugas->name ?? auth()->user()->name ?? '' }}"
                                   readonly>
                        </div>
                    </div>
                </fieldset>

                <div class="row g-3">
                    {{-- ===================== DATA DONOR ===================== --}}
                    <div class="col-lg-4">
                        <fieldset class="border rounded p-3 h-100">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-secondary">Data Donor</legend>

                            <div class="mb-2">
                                <label class="form-label-sm">No Donor</label>
                                <input type="text" name="no_donor" id="no_donor" class="form-control"
                                       value="{{ old('no_donor', $sampling->no_donor) }}" autocomplete="off">
                            </div>
                            <div class="mb-2">
                                <label class="form-label-sm">Nama Donor</label>
                                <input type="text" name="nama_donor" id="nama_donor" class="form-control @error('nama_donor') is-invalid @enderror"
                                       value="{{ old('nama_donor', $sampling->nama_donor) }}" required>
                                @error('nama_donor')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-2">
                                <label class="form-label-sm">Tgl Lahir</label>
                                <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control"
                                       value="{{ old('tgl_lahir', optional($sampling->tgl_lahir)->format('Y-m-d')) }}">
                            </div>

                            <div class="mb-2">
                                <label class="form-label-sm d-block">Rhesus</label>
                                @foreach(['positif' => 'Positif', 'negatif' => 'Negatif'] as $val => $label)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rhesus" id="rhesus_{{ $val }}"
                                               value="{{ $val }}" {{ old('rhesus', $sampling->rhesus) === $val ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rhesus_{{ $val }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <label class="form-label-sm d-block">Jenis Kelamin</label>
                                    @foreach(['pria' => 'Pria', 'wanita' => 'Wanita'] as $val => $label)
                                        <div class="form-check">
                                            <input class="form-check-input jk-radio" type="radio" name="jenis_kelamin"
                                                   id="jk_{{ $val }}" value="{{ $val }}"
                                                   {{ old('jenis_kelamin', $sampling->jenis_kelamin) === $val ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="jk_{{ $val }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-6">
                                    <label class="form-label-sm d-block">Golongan Darah</label>
                                    @foreach(['A','B','AB','O'] as $val)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="golongan_darah"
                                                   id="gol_{{ $val }}" value="{{ $val }}"
                                                   {{ old('golongan_darah', $sampling->golongan_darah) === $val ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gol_{{ $val }}">{{ $val }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- ===================== HASIL LABORATORIUM ===================== --}}
                    <div class="col-lg-6">
                        <fieldset class="border rounded p-3 h-100">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-secondary">Hasil Pemeriksaan</legend>
                            <div class="row">
                                {{-- WBC panel --}}
                                <div class="col-6">
                                    <div class="lab-group-title">WBC</div>
                                    @php
                                        $wbcSub = [
                                            'neut'  => ['NEUT', 'text-primary'],
                                            'lymph' => ['LYMPH', 'text-danger'],
                                            'mono'  => ['MONO', 'text-info'],
                                            'eo'    => ['EO', 'text-success'],
                                            'baso'  => ['BASO', 'text-secondary'],
                                            'ig'    => ['IG', 'text-dark'],
                                        ];
                                    @endphp
                                    <div class="lab-row">
                                        <label>WBC</label>
                                        <input type="number" step="0.01" name="wbc" class="form-control form-control-sm value"
                                               value="{{ old('wbc', $sampling->wbc) }}">
                                        <span class="range">[{{ $ranges['wbc']['M'][0] }}-{{ $ranges['wbc']['M'][1] }}]</span>
                                    </div>
                                    @foreach($wbcSub as $field => [$label, $color])
                                        <div class="lab-row">
                                            <label class="{{ $color }}">{{ $label }}</label>
                                            <input type="number" step="0.01" name="{{ $field }}" class="form-control form-control-sm value"
                                                   value="{{ old($field, $sampling->$field) }}">
                                            <span class="range">[{{ $ranges[$field][0] }}-{{ $ranges[$field][1] }}]</span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- RBC panel --}}
                                <div class="col-6">
                                    <div class="lab-group-title">RBC</div>
                                    <div class="lab-row">
                                        <label>RBC</label>
                                        <input type="number" step="0.01" name="rbc" class="form-control form-control-sm value"
                                               value="{{ old('rbc', $sampling->rbc) }}">
                                        <span class="range">[{{ $ranges['rbc']['M'][0] }}-{{ $ranges['rbc']['M'][1] }}]</span>
                                    </div>
                                    <div class="lab-row">
                                        <label>HGB</label>
                                        <input type="number" step="0.01" name="hgb" class="form-control form-control-sm value"
                                               value="{{ old('hgb', $sampling->hgb) }}">
                                        <span class="range">[{{ $ranges['hgb']['M'][0] }}-{{ $ranges['hgb']['M'][1] }}]</span>
                                    </div>
                                    <div class="lab-row">
                                        <label>HCT</label>
                                        <input type="number" step="0.01" name="hct" class="form-control form-control-sm value"
                                               value="{{ old('hct', $sampling->hct) }}">
                                        <span class="range">[{{ $ranges['hct']['M'][0] }}-{{ $ranges['hct']['M'][1] }}]</span>
                                    </div>

                                    @foreach(['mcv' => 'MCV', 'mch' => 'MCH', 'mchc' => 'MCHC', 'rdw_sd' => 'RDW-SD', 'rdw_cv' => 'RDW-CV'] as $field => $label)
                                        <div class="lab-row">
                                            <label>{{ $label }}</label>
                                            <input type="number" step="0.01" name="{{ $field }}" class="form-control form-control-sm value"
                                                   value="{{ old($field, $sampling->$field) }}">
                                            <span class="range">[{{ $ranges[$field][0] }}-{{ $ranges[$field][1] }}]</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-6">
                                    <div class="lab-group-title">PLT</div>
                                    @foreach(['plt' => 'PLT', 'pdw' => 'PDW', 'mpv' => 'MPV', 'p_lcr' => 'P-LCR', 'pct' => 'PCT'] as $field => $label)
                                        <div class="lab-row">
                                            <label>{{ $label }}</label>
                                            <input type="number" step="0.01" name="{{ $field }}" class="form-control form-control-sm value"
                                                   value="{{ old($field, $sampling->$field) }}">
                                            <span class="range">[{{ $ranges[$field][0] }}-{{ $ranges[$field][1] }}]</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- ===================== KETERANGAN LULUS / TIDAK ===================== --}}
                    <div class="col-lg-2">
                        <fieldset class="border rounded p-3 h-100">
                            <legend class="float-none w-auto px-2 fs-6 fw-semibold text-secondary">Keterangan</legend>

                            <label class="form-label-sm d-block mb-2">Lulus / Tidak</label>
                            @foreach(['lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus'] as $val => $label)
                                <div class="form-check">
                                    <input class="form-check-input status-radio" type="radio" name="status_lulus"
                                           id="status_{{ $val }}" value="{{ $val }}"
                                           {{ old('status_lulus', $sampling->status_lulus) === $val ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="status_{{ $val }}">{{ $label }}</label>
                                </div>
                            @endforeach

                            <hr>

                            <label class="form-label-sm d-block mb-1">Alasan</label>
                            <div class="alasan-list" id="alasan-list">
                                @foreach($alasanOptions as $val => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="alasan_tidak_lulus[]"
                                               id="alasan_{{ $val }}" value="{{ $val }}" disabled
                                               {{ in_array($val, old('alasan_tidak_lulus', $sampling->alasan_tidak_lulus ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="alasan_{{ $val }}">{{ $label }}</label>
                                    </div>
                                @endforeach
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
                <a href="{{ route('apheresis.sampling_pra_donor.index') }}" class="btn btn-danger px-4">
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

    // Toggle alasan checkboxes berdasarkan status lulus / tidak
    function toggleAlasan() {
        const tidakLulus = document.getElementById('status_tidak_lulus').checked;
        document.querySelectorAll('#alasan-list input[type=checkbox]').forEach(cb => {
            cb.disabled = !tidakLulus;
            if (!tidakLulus) cb.checked = false;
        });
    }
    document.querySelectorAll('.status-radio').forEach(r => r.addEventListener('change', toggleAlasan));
    toggleAlasan();

    // Generate No Transaksi baru
    document.getElementById('btn-generate').addEventListener('click', function () {
        fetch(`{{ route('apheresis.sampling_pra_donor.generate_kode') }}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('no_transaksi').value = data.no_transaksi;
                document.getElementById('server_date').value = data.server_date;
            });
    });

    // Auto-fill data donor berdasarkan No Donor (input manual maupun hasil scan barcode)
    const noDonorInput = document.getElementById('no_donor');

    function cariDataDonor() {
        const noDonor = noDonorInput.value.trim();
        if (!noDonor) return;

        fetch(`{{ route('apheresis.sampling_pra_donor.search_donor') }}`, {
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

            // Pindah fokus ke field berikutnya (Nama Donor) supaya alur input tetap cepat
            document.getElementById('nama_donor').focus();
        })
        .catch(() => noDonorInput.classList.add('is-invalid'));
    }

    // Barcode scanner umumnya mengetik cepat lalu mengirim keystroke Enter
    noDonorInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // cegah form ter-submit tidak sengaja
            cariDataDonor();
        }
    });

    // Fallback untuk input manual: tetap cari saat field kehilangan fokus
    noDonorInput.addEventListener('blur', cariDataDonor);

    @if($isEdit)
    document.getElementById('btn-cetak').addEventListener('click', function () {
        window.print();
    });
    @endif
})();
</script>
@endpush