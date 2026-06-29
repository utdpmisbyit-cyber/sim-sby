<form id="form_info" enctype="multipart/form-data">
    <style>
        .satelit-btn {
            border: 2px dashed #cbd5e1 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
            transition: all 0.15s ease-in-out;
        }
        .satelit-btn:hover {
            border-color: #94a3b8 !important;
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }
        .satelit-radio-btn:checked + .satelit-btn {
            background-color: #009ef7 !important;
            color: #ffffff !important;
            border-style: solid !important;
            border-color: #0076bd !important;
            font-weight: 800 !important;
            box-shadow: 0 8px 15px rgba(0, 158, 247, 0.25) !important;
            transform: scale(1.04);
        }
    </style>
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

                        @if(!empty($rencana_produksi->tipeKantong))
                            <div class="my-4">
                                <label class="form-label text-muted">Tipe Kantong</label>
                                <div class="fw-bold text-dark fs-5">{{ $rencana_produksi->tipeKantong->nama }}</div>
                                <input type="hidden" name="tipe_kantong_id" value="{{ $rencana_produksi->tipe_kantong_id }}">
                            </div>
                        @endif

                        <div class="my-4">
                            <x-io-select :viewtype="2" name="pengiriman_aftap_id" caption="Pengiriman Aftap (No. Pengiriman)" :options="$pengiriman_aftap_options" :value="$rencana_produksi->pengiriman_aftap_id ?? ''" required />
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
                            <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Konfigurasi Satelit & Detail Rencana</h6>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4">
                            @if($satelit_options->isNotEmpty())
                                <div class="card card-flush border border-primary border-dashed bg-light-primary p-5 rounded mb-5 shadow-xs">
                                    <h6 class="text-primary fw-bolder mb-4 d-flex align-items-center gap-2">
                                        <i class="fa fa-info-circle fs-4 text-primary"></i>
                                        Konfigurasi Satelit (Wajib Dipilih)
                                    </h6>
                                    <div class="row g-4">
                                        @foreach($satelit_options as $sat)
                                            <?php
                                                $currentJenisDarah = null;
                                                if (!empty($rencana_produksi)) {
                                                    $existingDetail = $rencana_produksi->details->where('no_satelit', $sat)->first();
                                                    $currentJenisDarah = $existingDetail ? $existingDetail->jenis_darah : null;
                                                }
                                            ?>
                                            <div class="col-md-6">
                                                <div class="bg-white border border-gray-300 rounded p-4 shadow-xs h-100">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="fs-6 fw-bold text-gray-800">
                                                            Satelit {{ $sat }} <span class="text-danger fw-bold">*</span>
                                                        </span>
                                                        <span class="badge badge-light-danger fw-bold fs-9 px-2 py-1 satelit-status-badge-{{ $sat }}">Belum Dipilih</span>
                                                    </div>
                                                    <div class="d-flex flex-wrap gap-2.5">
                                                        @foreach($mapped_satelit[$sat] as $aturan)
                                                            <input
                                                                type="radio"
                                                                class="btn-check satelit-radio-btn"
                                                                name="satelit_jenis_darah[{{ $sat }}]"
                                                                id="satelit_jenis_darah_{{ $sat }}_{{ $aturan->id }}"
                                                                value="{{ $aturan->jenisdarah }}"
                                                                data-satelit="{{ $sat }}"
                                                                autocomplete="off"
                                                                {{ $currentJenisDarah === $aturan->jenisdarah ? 'checked' : '' }}
                                                            >
                                                            <label
                                                                class="btn satelit-btn fw-bold py-2.5 px-4 fs-6 min-w-80px text-center shadow-xs cursor-pointer rounded-2"
                                                                for="satelit_jenis_darah_{{ $sat }}_{{ $aturan->id }}"
                                                            >
                                                                {{ $aturan->jenisdarah }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="table-responsive mt-4">
                                <table class="table align-middle table-row-dashed fs-7 table-sm">
                                    <thead>
                                    <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                        <th class="w-10px ps-4 rounded-start">#</th>
                                        <th>No. Kantong</th>
                                        <th>Satelit & Jenis Darah</th>
                                        <th class="text-center w-80px pe-4 rounded-end">Opsi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no = 1)
                                    @foreach($rencana_produksi->details->groupBy('no_kantong') as $no_kantong => $details_group)
                                        <tr>
                                            <td class="ps-4">{{ $no++ }}</td>
                                            <td class="fw-bold text-gray-800">{{ $no_kantong }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 py-1">
                                                    @foreach($details_group as $detail)
                                                        <span class="badge badge-light-primary border border-primary fs-8 px-2 py-1">
                                                            Satelit {{ $detail->no_satelit }}: <strong class="text-dark">{{ $detail->jenis_darah }}</strong>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="text-center pe-4">
                                                <button type="button" class="btn btn-icon btn-sm btn-light-danger border border-danger border-opacity-25" onclick="confirm_delete_detail('{{ $no_kantong }}')" title="Hapus Kantong">
                                                    <i class="fa fa-trash fs-6"></i>
                                                </button>
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
        <button type="submit" class="btn btn-sm btn-primary">{{ empty($rencana_produksi) ? 'Simpan' : 'Update' }}</button>
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

    $('.satelit-radio-btn').on('change', function() {
        const sat = $(this).data('satelit');
        if ($(this).is(':checked')) {
            $(`.satelit-status-badge-${sat}`)
                .removeClass('badge-light-danger')
                .addClass('badge-light-success')
                .text('Terpilih');
        }
    });
    $('.satelit-radio-btn:checked').trigger('change');

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

    $('#form_info').on('submit', function (e) {
        if (!$('#petugas_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Kode Petugas Input tidak valid'});
            return;
        }

        @if(!empty($rencana_produksi))
            const satelit_jenis_darah = {};
            $('input[name^="satelit_jenis_darah"]:checked').each(function() {
                const name = $(this).attr('name');
                const match = name.match(/\[(\d+)\]/);
                if (match) {
                    const sat = match[1];
                    satelit_jenis_darah[sat] = $(this).val();
                }
            });

            const satelit_keys = [];
            @if(!empty($satelit_options))
                @foreach($satelit_options as $sat)
                    satelit_keys.push('{{ $sat }}');
                @endforeach
            @endif

            for (const sat of satelit_keys) {
                if (!satelit_jenis_darah[sat]) {
                    e.preventDefault();
                    Swal.fire({icon: 'warning', title: `Satelit ${sat} belum dipilih`});
                    return;
                }
            }
        @endif
    });

    @if(!empty($rencana_produksi))
    rencana_produksi_id = '{{ $rencana_produksi->id }}';

    confirm_delete_detail = (no_kantong) => {
        Swal.fire(swal_delete_params).then((result) => {
            if (result.isConfirmed) {
                $.post(base_url + '/' + rencana_produksi_id + '/detail/' + encodeURIComponent(no_kantong), {_token, _method: 'delete'}, () => info(rencana_produksi_id))
                    .fail((xhr) => error_handle(xhr.responseText));
            }
        });
    }
    @endif
</script>
