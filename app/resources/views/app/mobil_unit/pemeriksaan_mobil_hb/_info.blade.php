<div class="card card-flush rounded-4 border-0 shadow-xs h-100" id="form_pemeriksaan_section">
    <div class="card-body">
        <form id="form_info">
            @csrf
            <h4 class="fs-6 fw-bold text-gray-800 mb-5">
                <i class="ki-duotone ki-pulse fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                Hasil Uji Hematologi
            </h4>

            <div class="row g-5">
                <div class="col-3">
                    <div class="row">
                        <div class="col-6">
                            <x-io-select :viewtype="2" name="golongan_darah" caption="Golongan Darah" placeholder="-Pilih-" :options="array_combine($golongan_darah_options, $golongan_darah_options)" :value="$pemeriksaan_mobil_hb->golongan_darah ?? ''" />
                        </div>
                        <div class="col-6">
                            <x-io-select :viewtype="2" name="rhesus" caption="Rhesus"  placeholder="-Pilih-" :options="array_combine($rhesus_options, $rhesus_options)" :value="$pemeriksaan_mobil_hb->rhesus ?? ''" />
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <x-io-select :viewtype="2" name="metode" caption="Metode" :options="array_combine($metode_options, $metode_options)" :value="$pemeriksaan_mobil_hb->metode ?? ''" />
                </div>
                <div class="col-3">
                    <x-io-select :viewtype="2" name="lengan" caption="Lengan Ambil Sebelah" :options="array_combine($lengan_options, $lengan_options)" :value="$pemeriksaan_mobil_hb->lengan ?? ''" />
                </div>
                <div class="col-3">
                    <x-io-input :viewtype="2" name="hb_meter" caption="HB Meter (gr/dL)" :value="$pemeriksaan_mobil_hb->hb_meter ?? ''" />
                </div>
            </div>
            <div class="row g-5">
                <div class="col-3">
                    <x-io-input :viewtype="2" name="trombosit" caption="Trombosit" :value="$pemeriksaan_mobil_hb->trombosit ?? 0" />
                </div>
                <div class="col-3">
                    <x-io-input :viewtype="2" name="lecosit" caption="Lecosit" :value="$pemeriksaan_mobil_hb->lecosit ?? 0" />
                </div>
                <div class="col-3">
                    <x-io-input :viewtype="2" name="eritrosit" caption="Eritrosit" :value="$pemeriksaan_mobil_hb->eritrosit ?? 0" />
                </div>
                <div class="col-3">
                    <x-io-input :viewtype="2" name="hematocrit" caption="Hematocrit (%)" :value="$pemeriksaan_mobil_hb->hematocrit ?? 0" />
                </div>
            </div>
            <div class="row g-5 mb-3">
                <div class="col-md-3">
                    <x-io-select name="status" caption="Status" :options="$list_status" :value="$pemeriksaan_mobil_hb->status ?? ''" :viewtype="2" />
                </div>
                <div class="col-md-9">
                    <x-io-select name="alasan" caption="Alasan" placeholder="-Pilih-" :options="array_combine($list_alasan, $list_alasan)" :value="$pemeriksaan_mobil_hb->alasan ?? ''" :viewtype="2" />
                </div>
            </div>

            <div class="separator separator-dashed mb-6"></div>

            <div class="d-flex justify-content-end gap-3">
                <button type="button" class="btn btn-light btn-sm" onclick="init()">
                    <i class="ki-duotone ki-arrow-left fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    Kembali
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="ki-duotone ki-check fs-5"><span class="path1"></span><span class="path2"></span></i>
                    Simpan Pemeriksaan
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    init_form_element();
    init_form({{ $id }});

    // batas HB dari blade
    let batasHb = @json(
        optional(optional($pemeriksaan_mobil_hb->logDonor)->donor)->jenis_kelamin === 'Wanita'
            ? 12.0
            : 12.5
    );

    function toggleAlasan() {

        let status = $('[name="status"]').val();
        let $alasan = $('[name="alasan"]');

        // cari text option terima
        let textStatus = $('[name="status"] option:selected')
            .text()
            .trim()
            .toLowerCase();

    
        if (
            status === 'Approved' ||
            textStatus === 'terima'
        ) {

            // kosongkan alasan
            $alasan.val('').trigger('change');

            // readonly select2
            $alasan.prop('disabled', true);

            // style abu
            $alasan.closest('.form-group, .fv-row, .col-md-9')
                .find('.select2-selection')
                .css({
                    'background': '#EFF2F5',
                    'cursor': 'not-allowed'
                });

        } else {

            $alasan.prop('disabled', false);

            $alasan.closest('.form-group, .fv-row, .col-md-9')
                .find('.select2-selection')
                .css({
                    'background': '',
                    'cursor': ''
                });
        }

        $alasan.trigger('change.select2');
    }

    // jalankan saat load
    toggleAlasan();

    // jalankan saat status berubah
    $('[name="status"]').on('change', function () {
        toggleAlasan();
    });

    // =========================================
    // AUTO STATUS BERDASARKAN HB
    // =========================================

    $('#hb_meter').off('keyup change input').on('keyup change input', function () {

        let val = parseFloat($(this).val());

        let $status = $('[name="status"]');
        let $alasan = $('[name="alasan"]');

        $('#hb-warn').remove();

        // cari option berdasarkan TEXT
        let optionTolak = $status.find('option').filter(function () {
            return $(this).text().trim().toLowerCase() === 'tolak';
        });

        let optionTerima = $status.find('option').filter(function () {
            return $(this).text().trim().toLowerCase() === 'terima';
        });

        // ========================
        // HB RENDAH → TOLAK
        // ========================
        if (!isNaN(val) && val < batasHb) {

            if (optionTolak.length) {

                $status.val(optionTolak.val());
                $status.trigger('change.select2');
                $status.trigger('change');
            }

            let optionHbRendah = $alasan.find('option').filter(function () {
                return $(this).text().trim() === 'HB Rendah';
            });

            if (optionHbRendah.length) {
                $alasan.val(optionHbRendah.val())
                        .trigger('change.select2')
                        .trigger('change');
            }

            $(this).closest('.col-3').append(`
                <div id="hb-warn" class="text-danger fs-8 mt-1">
                    ⚠ HB rendah (${val} gr/dL), status otomatis Tolak
                </div>
            `);

        } else {

            $('#hb-warn').remove();

            if (optionTerima.length) {

                $status.val(optionTerima.val());
                $status.trigger('change.select2');
                $status.trigger('change');
            }

            $alasan.val('')
                    .trigger('change.select2')
                    .trigger('change');
        }

        // update readonly alasan
        toggleAlasan();
    });

    // trigger pertama kali
    $('#hb_meter').trigger('change');

</script>