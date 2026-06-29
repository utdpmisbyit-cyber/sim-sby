<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">{{ !empty($kirim_litbang) ? 'Ubah' : 'Kirim' }} Litbang Baru</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="mb-4">
                    <x-io-input name="no_kantong" caption="No. Kantong / Barcode" :value="$kirim_litbang->no_kantong ?? ''" required :readonly="!empty($kirim_litbang)" />
                    <input type="hidden" name="aftap_id" id="aftap_id" value="{{ $kirim_litbang->aftap_id ?? '' }}">
                    <input type="hidden" name="donor_id" id="donor_id" value="{{ $kirim_litbang->donor_id ?? '' }}">
                </div>

                <div class="mb-4">
                    <x-io-input name="tanggal_kirim" caption="Tanggal Kirim" :value="formatDate($kirim_litbang->tanggal_kirim ?? date('d-m-Y'))" class="datepicker" required />
                </div>

                <div class="mb-4 row">
                    <label class="form-label fs-7 fw-semibold text-gray-700 required col-md-3">Dikirim Oleh (Petugas)</label>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-4">
                                <input type="hidden" name="petugas_kirim_id" id="petugas_kirim_id" value="{{ $kirim_litbang->petugas_kirim_id ?? '' }}">
                                <x-input name="petugas_kirim_kode" caption="" :value="$kirim_litbang->petugasKirim->kode ?? ''" placeholder="Kode petugas" autocomplete="off" required />
                            </div>
                            <div class="col-8">
                                <div class="form-text fs-5 text-dark" id="petugas_kirim_nama">{{ $kirim_litbang->petugasKirim->nama ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <x-io-textarea name="keterangan" caption="Keterangan / Alasan" :value="$kirim_litbang->keterangan ?? ''" />
                </div>
            </div>

            <div class="col-lg-6">
                <div id="aftap_info_box" class="alert alert-light-info h-100 d-flex flex-column justify-content-center m-0 {{ empty($kirim_litbang) ? 'd-none' : '' }}">
                    <div class="fw-bold mb-3 fs-6">Informasi Aftap & Donor</div>
                    <div class="row fs-7 g-3">
                        <div class="col-md-12"><span class="text-muted fw-semibold">No Kantong:</span> <span id="aftap_no_kantong" class="fw-bold text-dark">{{ $kirim_litbang->no_kantong ?? '-' }}</span></div>
                        <div class="col-md-12"><span class="text-muted fw-semibold">Kode Aftap:</span> <span id="aftap_kode" class="fw-bold text-dark">{{ $kirim_litbang->aftap->kode ?? '-' }}</span></div>
                        <div class="col-md-12"><span class="text-muted fw-semibold">Kode Donor:</span> <span id="aftap_donor_kode" class="fw-bold text-dark">{{ $kirim_litbang->donor->kode ?? '-' }}</span></div>
                        <div class="col-md-12"><span class="text-muted fw-semibold">Nama Donor:</span> <span id="aftap_donor_nama" class="fw-bold text-dark">{{ $kirim_litbang->donor->nama ?? '-' }}</span></div>
                        <div class="col-md-12"><span class="text-muted fw-semibold">Golongan Darah Awal:</span> <span id="aftap_donor_goldar" class="badge badge-light-primary">{{ !empty($kirim_litbang) ? ($kirim_litbang->donor ? ($kirim_litbang->donor->golongan_darah . $kirim_litbang->donor->rhesus) : '-') : '' }}</span></div>
                        <div class="col-md-12"><span class="text-muted fw-semibold">Jenis Donor:</span> <span id="aftap_jenis_donor" class="fw-bold text-dark">{{ $kirim_litbang->aftap->jenis_donor ?? '-' }}</span></div>
                    </div>
                </div>
                <div id="aftap_placeholder" class="alert alert-light-warning h-100 d-flex flex-column justify-content-center align-items-center m-0 py-10 {{ empty($kirim_litbang) ? '' : 'd-none' }}">
                    <i class="ki-duotone ki-information fs-3x text-warning mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <span class="fs-7 text-muted text-center">Silakan input/scan No. Kantong untuk memuat informasi Aftap & Donor</span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer py-3 px-6">
        <button type="button" class="btn btn-sm btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    $tanggalInput = $('#tanggal_kirim');
    if ($tanggalInput.length) {
        $tanggalInput.datepicker('destroy').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            orientation: 'bottom',
            showOnFocus: false
        });
        $tanggalInput.on('click', function () {
            $(this).datepicker('show');
        });
    }

    petugasLookupUrl = '{{ route('serologi.kirim_litbang.petugas_by_kode') }}';
    aftapInfoUrl = '{{ route('serologi.kirim_litbang.aftap_info') }}';

    setupPetugasLookup = (key, required = false) => {
        const $kode = $('#' + key + '_kode');
        const $id = $('#' + key + '_id');
        const $nama = $('#' + key + '_nama');

        const clearValue = () => {
            $id.val('');
            $nama.text('-').removeClass('text-success').addClass('text-danger');
        };

        const resolveKode = () => {
            const kode = ($kode.val() || '').trim();
            if (kode === '') {
                if (required) {
                    clearValue();
                } else {
                    $id.val('');
                    $nama.text('-').removeClass('text-success text-danger').addClass('text-gray-700');
                }
                return;
            }

            $.get(petugasLookupUrl, { kode }, (result) => {
                $id.val(result.id);
                $kode.val(result.kode);
                $nama.text(result.nama).removeClass('text-danger text-gray-700').addClass('text-success');
            }).fail(() => {
                clearValue();
            });
        };

        $kode.on('blur', resolveKode);
        $kode.on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                resolveKode();
            }
        });
    };

    setupPetugasLookup('petugas_kirim', true);

    load_aftap_info = (no_kantong) => {
        if (!no_kantong) {
            $('#aftap_info_box').addClass('d-none');
            $('#aftap_placeholder').removeClass('d-none');
            return;
        }

        $.get(aftapInfoUrl, { no_kantong }, (res) => {
            $('#aftap_id').val(res.aftap_id);
            $('#donor_id').val(res.donor?.id || '');

            $('#aftap_info_box').removeClass('d-none');
            $('#aftap_placeholder').addClass('d-none');

            $('#aftap_no_kantong').text(res.no_kantong || '-');
            $('#aftap_kode').text(res.kode_aftap || '-');
            $('#aftap_donor_kode').text(res.donor?.kode || '-');
            $('#aftap_donor_nama').text(res.donor?.nama || '-');
            const goldar = res.donor ? `${res.donor.golongan_darah || ''}${res.donor.rhesus || ''}` : '-';
            $('#aftap_donor_goldar').text(goldar || '-');
            $('#aftap_jenis_donor').text(res.jenis_donor || '-');
        }).fail((xhr) => {
            $('#aftap_id').val('');
            $('#donor_id').val('');
            $('#aftap_info_box').addClass('d-none');
            $('#aftap_placeholder').removeClass('d-none');
            const errorMsg = xhr.responseJSON?.error || 'Gagal mencari nomor kantong';
            Swal.fire({icon: 'warning', title: errorMsg});
        });
    }

    $('#no_kantong').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            load_aftap_info($(this).val());
        }
    });

    $('#no_kantong').on('blur', function () {
        load_aftap_info($(this).val());
    });

    $('#form_info').on('submit', function (e) {
        if (!$('#petugas_kirim_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Kode Petugas Pengirim tidak valid'});
            return;
        }
        if (!$('#aftap_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Data Aftap / No Kantong tidak ditemukan'});
            return;
        }
    });

    init_form({{ $kirim_litbang->id ?? '' }});
</script>
