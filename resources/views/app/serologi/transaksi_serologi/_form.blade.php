<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">{{ !empty($serologi) ? 'Ubah' : 'Tambah' }} Transaksi Serologi</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="card card-flush border mb-4">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Informasi Serologi</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <x-io-input :viewtype="2" name="tanggal" caption="Tanggal" :value="formatDate($serologi->tanggal ?? date('d-m-Y'))" class="datepicker" required />
                        <input type="hidden" name="group" value="{{ $serologi->group ?? ($generated_group ?? '') }}">
                        @if(empty($serologi))
                            <div class="separator separator-dashed my-5"></div>
                            <h6 class="fw-bold fs-8 text-uppercase text-muted mb-3">Batch Nomor & Master Periksa</h6>
                            <div id="batch_rows"></div>
                            <button type="button" class="btn btn-sm btn-light-primary mt-2" onclick="add_batch_row()">+ Tambah Baris</button>
                        @else
                        <div class="row">
                            <div class="col-lg-12">
                                <x-io-input :viewtype="2" name="nomor" caption="Nomor" :value="$serologi->nomor ?? ''" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4">
                                <x-io-select :viewtype="2" name="jenis_periksa_serologi_id" caption="Jenis Periksa" :options="$jenis_periksa_serologi_options" :value="$serologi->jenis_periksa_serologi_id ?? ''" required />
                            </div>
                            <div class="col-lg-4">
                                <x-io-select :viewtype="2" name="metode_serologi_id" caption="Metode Periksa" :options="$metode_serologi_options" :value="$serologi->metode_serologi_id ?? ''" required />
                            </div>
                            <div class="col-lg-4">
                                <x-io-select :viewtype="2" name="reagen_serologi_id" caption="Reagen" :options="$reagen_serologi_options" :value="$serologi->reagen_serologi_id ?? ''" required />
                            </div>
                        </div>
                        @endif
                        <div class="my-4">
                            <label class="form-label required">Petugas Input</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" name="petugas_id" id="petugas_id" value="{{ $serologi->petugas_id ?? '' }}">
                                    <x-input name="petugas_kode" caption="" :value="$serologi->petugas->kode ?? ''" placeholder="Kode petugas" autocomplete="off" required />
                                </div>
                                <div class="col-8">
                                    <div class="form-text fs-5 text-dark" id="petugas_nama">{{ $serologi->petugas->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label required">Pemeriksa Serologi</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" name="pemeriksa_serologi_id" id="pemeriksa_serologi_id" value="{{ $serologi->pemeriksa_serologi_id ?? '' }}">
                                    <x-input name="pemeriksa_serologi_kode" caption="" :value="$serologi->pemeriksaSerologi->kode ?? ''" placeholder="Kode petugas" autocomplete="off" required />
                                </div>
                                <div class="col-8">
                                    <div class="form-text fs-5 text-dark" id="pemeriksa_serologi_nama">{{ $serologi->pemeriksaSerologi->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Diputar Oleh</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" name="diputar_oleh_id" id="diputar_oleh_id" value="{{ $serologi->diputar_oleh_id ?? '' }}">
                                    <x-input name="diputar_oleh_kode" caption="" :value="$serologi->diputarOleh->kode ?? ''" placeholder="Kode petugas" autocomplete="off" />
                                </div>
                                <div class="col-8">
                                    <div class="form-text fs-5 text-dark" id="diputar_oleh_nama">{{ $serologi->diputarOleh->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Diperiksa Oleh</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" name="diperiksa_oleh_id" id="diperiksa_oleh_id" value="{{ $serologi->diperiksa_oleh_id ?? '' }}">
                                    <x-input name="diperiksa_oleh_kode" caption="" :value="$serologi->diperiksaOleh->kode ?? ''" placeholder="Kode petugas" autocomplete="off" />
                                </div>
                                <div class="col-8">
                                    <div class="form-text fs-5 text-dark" id="diperiksa_oleh_nama">{{ $serologi->diperiksaOleh->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Disahkan Oleh</label>
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" name="disahkan_oleh_id" id="disahkan_oleh_id" value="{{ $serologi->disahkan_oleh_id ?? '' }}">
                                    <x-input name="disahkan_oleh_kode" caption="" :value="$serologi->disahkanOleh->kode ?? ''" placeholder="Kode petugas" autocomplete="off" />
                                </div>
                                <div class="col-8">
                                    <div class="form-text fs-5 text-dark" id="disahkan_oleh_nama">{{ $serologi->disahkanOleh->nama ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                
                    </div>
                </div>

                @if(!empty($serologi))
                    <div class="card card-flush border">
                        <div class="card-header min-h-40px px-4 pt-3 pb-0">
                            <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Status Ringkas</h6>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4">
                            @php($total = $serologi->details->count())
                            @php($done = $serologi->details->where('status', '!=', 'pending')->count())
                            <div class="d-flex justify-content-between mb-2"><span>Total Detail</span><span class="fw-bold">{{ $total }}</span></div>
                            <div class="d-flex justify-content-between mb-2"><span>Sudah Hasil</span><span class="fw-bold">{{ $done }}</span></div>
                            <div class="d-flex justify-content-between"><span>Status Header</span><span class="badge badge-light-primary">{{ strtoupper($serologi->status) }}</span></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-8">
                @if(!empty($serologi))
                    @if(!empty($group_serologis) && count($group_serologis) > 0)
                        <div class="card card-flush border mb-4">
                            <div class="card-header min-h-40px px-4 pt-3 pb-0">
                                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Serologi Dalam Group Yang Sama</h6>
                            </div>
                            <div class="card-body px-4 pt-3 pb-4">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-hover fs-7 table-sm align-middle">
                                        <thead>
                                        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                            <th class="ps-3">Nomor</th>
                                            <th>Jenis Periksa</th>
                                            <th>Metode</th>
                                            <th>Reagen</th>
                                            <th class="text-center">Detail</th>
                                            <th class="text-center">Selesai</th>
                                            <th class="text-end pe-3">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($group_serologis as $group_item)
                                            @php($gTotal = $group_item->details->count())
                                            @php($gDone = $group_item->details->where('status', '!=', 'pending')->count())
                                            <tr class="{{ $group_item->id == $serologi->id ? 'bg-light-primary' : '' }}">
                                                <td class="ps-3 fw-bold">{{ $group_item->nomor }}</td>
                                                <td>{{ $group_item->jenisPeriksaSerologi->nama ?? '-' }}</td>
                                                <td>{{ $group_item->metodeSerologi->nama ?? '-' }}</td>
                                                <td>{{ $group_item->reagenSerologi->nama ?? '-' }}</td>
                                                <td class="text-center">{{ $gTotal }}</td>
                                                <td class="text-center">{{ $gDone }}</td>
                                                <td class="text-end pe-3">
                                                    @if($group_item->id == $serologi->id)
                                                        <span class="badge badge-light-primary">Aktif</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-light-primary" onclick="info({{ $group_item->id }})">Buka</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="card card-flush border mb-4">
                        <div class="card-header min-h-40px px-4 pt-3 pb-0">
                            <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Input Detail (Scan Barcode)</h6>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4">
                        
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

                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
                                    <thead>
                                    <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                        <th class="w-10px ps-4 rounded-start">#</th>
                                        <th>Status</th>
                                        <th>Hasil</th>
                                        <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no = 1)
                                    @foreach($serologi->details as $detail)
                                        <tr>
                                            <td class="ps-4">{{ $no++ }}</td>
                                            <td>
                                                @php($detailBadge = $detail->status === 'selesai' ? 'success' : ($detail->status === 'proses' ? 'warning' : 'secondary'))
                                                <span class="badge badge-light-{{ $detailBadge }}">{{ strtoupper($detail->status) }}</span>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" id="hasil_{{ $detail->id }}" value="{{ $detail->hasil }}" placeholder="Hasil lab">
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <button class="btn btn-sm btn-light-success px-2 py-1" type="button" onclick="save_result({{ $detail->id }})">Simpan</button>
                                                <button class="btn btn-sm btn-light-danger px-2 py-1" type="button" onclick="confirm_delete_detail({{ $detail->id }})">Hapus</button>
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
        <button type="submit" class="btn btn-sm btn-primary">{{ empty($serologi) ? 'Simpan Batch' : 'Update Header' }}</button>
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
    init_form({{ $serologi->id ?? '' }});

    petugasLookupUrl = '{{ route('serologi.transaksi_serologi.petugas_by_kode') }}';

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
    setupPetugasLookup('pemeriksa_serologi', true);
    setupPetugasLookup('diputar_oleh', false);
    setupPetugasLookup('diperiksa_oleh', false);
    setupPetugasLookup('disahkan_oleh', false);

    $('#form_info').on('submit', function (e) {
        if (!$('#petugas_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Kode Petugas Input tidak valid'});
            return;
        }
        if (!$('#pemeriksa_serologi_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Kode Pemeriksa Serologi tidak valid'});
        }
    });

    @if(empty($serologi))
        batchIdx = 0;

        jenisPeriksaOptions = @json($jenis_periksa_serologi_options ?? []);
        metodeOptions = @json($metode_serologi_options ?? []);
        reagenOptions = @json($reagen_serologi_options ?? []);

        buildOptionHtml = (options) => {
            let html = '<option value="">-- Pilih --</option>';
            Object.keys(options).forEach((key) => {
                html += `<option value="${key}">${options[key]}</option>`;
            });
            return html;
        }

        add_batch_row = (nomor = null, jenis_periksa_serologi_id = '', metode_serologi_id = '', reagen_serologi_id = '') => {
            batchIdx++;
            let defaultNomor = nomor;
            if (defaultNomor === null) {
                defaultNomor = '{{ $generated_nomor ?? '' }}';
            }

            $('#batch_rows').append(`
            <div class="batch-row" data-row="${batchIdx}">
                <div class="row g-3 align-items-end mb-3 batch-row">
                    <div class="col-lg-8">
                        <label class="form-label fs-7 fw-semibold">Nomor</label>
                        <input type="text" name="nomor_list[]" class="form-control form-control-sm" value="${defaultNomor}" required>
                    </div>
                    <div class="col-lg-4">
                        <button type="button" class="btn btn-sm btn-light-danger w-100" onclick="remove_batch_row(${batchIdx})">Hapus</button>
                    </div>
                </div>
                <div class="row g-3 align-items-end mb-3 batch-row">
                    <div class="col-lg-4">
                        <label class="form-label fs-7 fw-semibold">Jenis Periksa</label>
                        <select name="jenis_periksa_serologi_id_list[]" class="form-select form-select-sm" required>${buildOptionHtml(jenisPeriksaOptions)}</select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label fs-7 fw-semibold">Metode</label>
                        <select name="metode_serologi_id_list[]" class="form-select form-select-sm" required>${buildOptionHtml(metodeOptions)}</select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label fs-7 fw-semibold">Reagen</label>
                        <select name="reagen_serologi_id_list[]" class="form-select form-select-sm" required>${buildOptionHtml(reagenOptions)}</select>
                    </div>
                    
                </div>
            </div>
            `);

            const $row = $(`.batch-row[data-row="${batchIdx}"]`);
            $row.find('select[name="jenis_periksa_serologi_id_list[]"]').val(jenis_periksa_serologi_id);
            $row.find('select[name="metode_serologi_id_list[]"]').val(metode_serologi_id);
            $row.find('select[name="reagen_serologi_id_list[]"]').val(reagen_serologi_id);
        }

        remove_batch_row = (id) => {
            $(`.batch-row[data-row="${id}"]`).remove();
        }

        add_batch_row('{{ $generated_nomor ?? '' }}');
    @endif

    @if(!empty($serologi))
        serologi_id = '{{ $serologi->id }}';
        aftapInfoUrl = `{{ route('serologi.transaksi_serologi.detail.aftap_info', ['serologi' => $serologi->id]) }}`;

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

            $.post(base_url + '/' + serologi_id + '/detail', {_token, no_kantong}, () => info(serologi_id))
                .fail((xhr) => error_handle(xhr.responseText));
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
            const hasil = $('#hasil_' + detailId).val();
            const status = hasil ? 'selesai' : 'pending';

            $.post(base_url + '/' + serologi_id + '/detail/' + detailId, {
                _token,
                _method: 'put',
                hasil,
                status
            }, () => info(serologi_id)).fail((xhr) => error_handle(xhr.responseText));
        }

        confirm_delete_detail = (id) => {
            Swal.fire(swal_delete_params).then((result) => {
                if (result.isConfirmed) {
                    $.post(base_url + '/' + serologi_id + '/detail/' + id, {_token, _method: 'delete'}, () => info(serologi_id))
                        .fail((xhr) => error_handle(xhr.responseText));
                }
            });
        }
    @endif
</script>
