<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">{{ !empty($rencana_produksi) ? 'Ubah' : 'Tambah' }} Rencana Produksi</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="card card-flush border mb-4">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Informasi Rencana</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <x-io-input :viewtype="2" name="tanggal" caption="Tanggal" :value="formatDate($rencana_produksi->tanggal ?? date('d-m-Y'))" class="datepicker" required />

                        <div class="my-4">
                            <x-io-select :viewtype="2" name="tipe_kantong_id" caption="Tipe Kantong" :options="$tipe_kantong_options" :value="$rencana_produksi->tipe_kantong_id ?? ''" data-dropdown-parent="#modal_info" required />
                        </div>

                        <div class="my-4">
                            <x-io-select :viewtype="2" name="pengiriman_sample_id" caption="Pengiriman Sample (No. FPD)" :options="$pengiriman_sample_options" :value="$rencana_produksi->pengiriman_sample_id ?? ''" required />
                        </div>

                        <div class="my-4">
                            <label class="form-label required">Petugas Input</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" name="petugas_id" id="petugas_id" value="{{ $rencana_produksi->petugas_id ?? '' }}">
                                    <x-input name="petugas_kode" caption="" :value="$rencana_produksi->petugas->kode ?? ''" placeholder="Kode petugas" autocomplete="off" required />
                                </div>
                                <div class="col-8">
                                    <div class="form-text fs-5 text-dark" id="petugas_nama">{{ $rencana_produksi->petugas->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($rencana_produksi))
                    <div class="card card-flush border">
                        <div class="card-header min-h-40px px-4 pt-3 pb-0">
                            <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Status Ringkas</h6>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4">
                            @php($total = $rencana_produksi->details->count())
                            <div class="d-flex justify-content-between mb-2"><span>Total Detail</span><span class="fw-bold">{{ $total }}</span></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                @if(!empty($rencana_produksi))
                    <div class="card card-flush border mb-4">
                        <div class="card-header min-h-40px px-4 pt-3 pb-0">
                            <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Input Detail (Scan Barcode)</h6>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4">
                            @if($satelit_options->isNotEmpty())
                                <div class="bg-light p-4 rounded mb-4 border border-secondary">
                                    <h6 class="text-primary fw-bold mb-3"><i class="fa fa-info-circle me-2"></i>Konfigurasi Jenis Darah Per Satelit (Tipe: {{ $rencana_produksi->tipeKantong->nama }})</h6>
                                    <div class="row g-3">
                                        @foreach($satelit_options as $sat)
                                                <?php $satelit_rules = $aturan_satelits->where('satelit', $sat); ?>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold text-dark fs-7">Satelit {{ $sat }} :</label>
                                                <select name="satelit_jenis_darah[{{ $sat }}]" id="satelit_jenis_darah_{{ $sat }}" class="form-select form-select-sm border border-primary bg-white" required>
                                                    @foreach($satelit_rules as $rule)
                                                        <option value="{{ $rule->jenisdarah }}">{{ $rule->jenisdarah }} (KD: {{ $rule->kdtype }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-lg-9">
                                    <x-io-input :viewtype="2" name="no_kantong" caption="No Kantong (Single)" />
                                </div>
                                <div class="col-lg-3">
                                    <button class="btn btn-sm btn-primary w-100 mt-7" type="button" onclick="save_single_detail()">Tambahkan Kantong</button>
                                </div>
                            </div>
                            <div id="aftap_info_box" class="alert alert-light-info mt-4 d-none">
                                <div class="fw-bold mb-2">Info Donor & Aftap</div>
                                <div class="row fs-7">
                                    <div class="col-md-6 mb-2"><span class="text-muted">No Kantong:</span> <span id="aftap_no_kantong">-</span></div>
                                    <div class="col-md-6 mb-2"><span class="text-muted">Kode Aftap:</span> <span id="aftap_kode">-</span></div>
                                    <div class="col-md-6 mb-2"><span class="text-muted">Donor:</span> <span id="aftap_donor_nama">-</span></div>
                                    <div class="col-md-6 mb-2"><span class="text-muted">Kode Donor:</span> <span id="aftap_donor_kode">-</span></div>
                                    <div class="col-md-6 mb-2"><span class="text-muted">Gol. Darah:</span> <span id="aftap_donor_goldar">-</span></div>
                                    <div class="col-md-6 mb-2"><span class="text-muted">Jenis Donor:</span> <span id="aftap_jenis_donor">-</span></div>
                                </div>
                            </div>

                            <div class="table-responsive mt-4">
                                <table class="table align-middle table-row-dashed fs-7 table-sm">
                                    <thead>
                                    <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                        <th class="w-10px ps-4 rounded-start">#</th>
                                        <th>No. Kantong</th>
                                        <th>Pilih Satelit</th>
                                        <th class="text-center w-50px pe-4 rounded-end"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no = 1)
                                    @foreach($rencana_produksi->details as $detail)
                                        <tr>
                                            <td class="ps-4">{{ $no++ }}</td>
                                            <td>{{ $detail->no_kantong }}</td>
                                            <td class="p-0">
                                                <table class="table table-borderless border-bottom border-dark">
                                                    <tr>
                                                        @foreach($satelit_options as $satelit)
                                                            <td class="p-0 pe-2 border border-solid border-dark">
                                                                @foreach($mapped_satelit[$satelit] as $aturan)
                                                                    <input
                                                                        type="radio"
                                                                        class="btn-check"
                                                                        name="satelit_{{ $detail->id }}_{{ $satelit }}"
                                                                        id="satelit_{{ $detail->id }}_{{ $satelit }}_{{ $aturan->id }}"
                                                                        value="{{ $aturan->id }}"
                                                                        autocomplete="off"
                                                                    >
                                                                    <label
                                                                        class="btn btn-sm btn-outline-primary me-1 mb-1"
                                                                        for="satelit_{{ $detail->id }}_{{ $satelit }}_{{ $aturan->id }}"
                                                                    >
                                                                        {{ $aturan->jenisdarah }}
                                                                    </label>
                                                                @endforeach
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light-info">
                        Simpan data header terlebih dahulu, lalu input detail no kantong dari barcode scanner.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal-footer py-3 px-6">
        <button type="button" class="btn btn-sm btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">{{ empty($rencana_produksi) ? 'Simpan Header' : 'Update Header' }}</button>
    </div>
</form>

<script>
    init_form_element();
    $tanggalInput = $('#tanggal');
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
    init_form({{ $rencana_produksi->id ?? '' }});

    petugasLookupUrl = '{{ route('kantong_darah.rencana_produksi.petugas_by_kode') }}';

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

    setupPetugasLookup('petugas', true);

    sampleInfoUrl = '{{ route('kantong_darah.rencana_produksi.pengiriman_sample_info', ['id' => ':id']) }}';

    $('#pengiriman_sample_id').on('change', function () {
        const sampleId = $(this).val();
        if (!sampleId) {
            $('#tipe_kantong_id').val('').trigger('change');
            return;
        }

        const url = sampleInfoUrl.replace('%3Aid', sampleId).replace(':id', sampleId);
        $.get(url, (res) => {
            if (res.tipe_kantong_id) {
                $('#tipe_kantong_id').val(res.tipe_kantong_id).trigger('change');
            } else {
                $('#tipe_kantong_id').val('').trigger('change');
            }
        }).fail(() => {
            $('#tipe_kantong_id').val('').trigger('change');
        });
    });

    $('#form_info').on('submit', function (e) {
        if (!$('#petugas_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Kode Petugas Input tidak valid'});
            return;
        }
    });

    @if(!empty($rencana_produksi))
        rencana_produksi_id = '{{ $rencana_produksi->id }}';
    aftapInfoUrl = `{{ route('kantong_darah.rencana_produksi.detail.aftap_info', ['rencana_produksi' => $rencana_produksi->id]) }}`;

    clear_aftap_info = () => {
        $('#aftap_info_box').addClass('d-none');
        $('#aftap_no_kantong, #aftap_kode, #aftap_donor_nama, #aftap_donor_kode, #aftap_donor_goldar, #aftap_jenis_donor').text('-');
    }

    load_aftap_info = (no_kantong) => {
        if (!no_kantong) {
            clear_aftap_info();
            return;
        }

        $.get(aftapInfoUrl, { no_kantong }, (res) => {
            if (res.error) {
                clear_aftap_info();
                return;
            }
            $('#aftap_info_box').removeClass('d-none');
            $('#aftap_no_kantong').text(res.no_kantong || '-');
            $('#aftap_kode').text(res.kode_aftap || '-');
            $('#aftap_donor_nama').text(res.donor?.nama || '-');
            $('#aftap_donor_kode').text(res.donor?.kode || '-');
            const goldar = res.donor ? `${res.donor.golongan_darah || ''}${res.donor.rhesus || ''}` : '-';
            $('#aftap_donor_goldar').text(goldar || '-');
            $('#aftap_jenis_donor').text(res.jenis_donor || '-');
        }).fail(() => {
            clear_aftap_info();
        });
    }

    save_single_detail = () => {
        const no_kantong = $('#no_kantong').val();
        if (!no_kantong) {
            Swal.fire({icon: 'warning', title: 'No kantong wajib diisi'});
            return;
        }

        const satelit_jenis_darah = {};
        $('select[name^="satelit_jenis_darah"]').each(function() {
            const name = $(this).attr('name');
            const match = name.match(/\\[(\\d+)\\]/);
            if (match) {
                const sat = match[1];
                satelit_jenis_darah[sat] = $(this).val();
            }
        });

        $.post(base_url + '/' + rencana_produksi_id + '/detail', {
            _token,
            no_kantong,
            satelit_jenis_darah
        }, (result) => {
            if (result.error) {
                Swal.fire({icon: 'warning', title: result.error});
                return;
            }
            info(rencana_produksi_id);
        }).fail((xhr) => error_handle(xhr.responseText));
    }

    $('#no_kantong').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            save_single_detail();
        }
    });
    $('#no_kantong').on('blur', function () {
        load_aftap_info($(this).val());
    });

    save_result = (detailId) => {
        const no_satelit = $('#no_satelit_' + detailId).val();
        const jenis_darah = $('#jenis_darah_' + detailId).val();

        $.post(base_url + '/' + rencana_produksi_id + '/detail/' + detailId, {
            _token,
            _method: 'put',
            no_satelit,
            jenis_darah
        }, () => info(rencana_produksi_id)).fail((xhr) => error_handle(xhr.responseText));
    }

    confirm_delete_detail = (id) => {
        Swal.fire(swal_delete_params).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + '/' + rencana_produksi_id + '/detail/' + id, {_token, _method: 'delete'}, () => info(rencana_produksi_id))
                    .fail((xhr) => error_handle(xhr.responseText));
            }
        });
    }
    @endif
</script>
