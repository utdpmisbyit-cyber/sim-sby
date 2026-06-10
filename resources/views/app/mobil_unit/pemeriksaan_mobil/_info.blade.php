<div class="card card-flush rounded-4 border-0 shadow-xs h-100" id="form_pemeriksaan_section">
    <div class="card-body position-relative">
        <button type="button" class="btn btn-light btn-sm position-absolute top-0 end-0 me-9 mt-5" onclick="init()">
            <i class="ki-duotone ki-arrow-left fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
            Kembali
        </button>
        <form id="form_info">
            @csrf
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_info">Pemeriksaan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_kuisioner">Kuisioner</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_hematologi">Hematologi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab_aftap">Aftap</a>
                </li>
            </ul>

            <div class="tab-content" id="tab_content">
                <div class="tab-pane fade show active" id="tab_info" role="tabpanel">
                    <h4 class="fs-6 fw-bold text-gray-800 mb-5">
                        <i class="ki-duotone ki-pulse fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        Kondisi Kesehatan
                    </h4>

                    <div class="row g-5 mb-3">
                        <div class="col-md-3">
                            <x-io-input type="number" name="sistole" caption="Sistole (mmHg)" placeholder="120" :value="$pemeriksaan_dokter->sistole ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-input type="number" name="diastole" caption="Diastole (mmHg)" placeholder="80" :value="$pemeriksaan_dokter->diastole ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-input type="number" name="nadi" caption="Nadi (x/menit)"  placeholder="75" :value="$pemeriksaan_dokter->nadi ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-input type="number" step="0.1" name="suhu" caption="Suhu (°C)" placeholder="36.5" :value="$pemeriksaan_dokter->suhu ?? ''" :viewtype="2" />
                        </div>
                    </div>

                    <div class="row g-5 mb-3">
                        <div class="col-md-3">
                            <x-io-select name="ecg" caption="ECG (Elektrokardiogram)" :options="array_combine($list_ecg, $list_ecg)" :value="$pemeriksaan_dokter->ecg ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-input type="number" step="0.1" name="tinggi_badan" caption="Tinggi Badan (cm)"  placeholder="105" :value="$pemeriksaan_dokter->tinggi_badan ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-input type="number" step="0.1" name="berat_badan" caption="Berat Badan (kg)"  placeholder="65" :value="$pemeriksaan_dokter->berat_badan ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-select name="puasa" caption="Status Puasa" placeholder="-Pilih-" :options="$list_puasa" :value="$pemeriksaan_dokter->puasa ?? ''" :viewtype="2" />
                        </div>
                    </div>

                    <div class="separator separator-dashed mb-6"></div>

                    <h4 class="fs-6 fw-bold text-gray-800 mb-5">
                        <i class="ki-duotone ki-user-tick fs-4 text-danger me-2">
                            <span class="path1"></span><span class="path2"></span>
                            <span class="path3"></span><span class="path4"></span>
                            <span class="path5"></span><span class="path6"></span>
                        </i>
                        Hasil Pemeriksaan
                    </h4>

                    <div class="row g-5">
                        <div class="col-md-3">
                            <x-io-select name="status" caption="Status" :options="$list_status" :value="$pemeriksaan_dokter->status ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-9">
                            <x-io-select name="alasan" caption="Alasan" placeholder="-Pilih-" :options="array_combine($list_alasan, $list_alasan)" :value="$pemeriksaan_dokter->alasan ?? ''" :viewtype="2" />
                        </div>
                    </div>
                    <div class="row g-5">
                        <div class="col-md-3">
                            <x-io-select name="jenis_kantong" placeholder="-Pilih-" caption="Jenis Kantong" :options="array_combine($list_jenis_kantong, $list_jenis_kantong)" :value="$pemeriksaan_dokter->jenis_kantong ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-select name="tipe_jenis_kantong" placeholder="-Pilih-" caption="Tipe Jenis Kantong" :options="[]" :value="$pemeriksaan_dokter->tipe_jenis_kantong ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-6">
                            <x-io-input name="sampling" caption="Sampling" :value="$pemeriksaan_dokter->sampling ?? ''" :viewtype="2" />
                        </div>
                    </div>
                    <div class="row g-5">
                        <div class="col-md-12">
                            <x-io-textarea name="keterangan" caption="Keterangan" :value="$pemeriksaan_dokter->keterangan ?? ''" :viewtype="2" />
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab_kuisioner" role="tabpanel">
                    <h4 class="fs-6 fw-bold text-gray-800 mb-5">
                        <i class="ki-duotone ki-message-question fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        Kuisioner Pemeriksaan
                    </h4>

                    @php($perColumn = ceil(count($questions) / 3))
                    @php($columns = array_chunk($questions, $perColumn))
                    <div class="row">
                        @foreach($columns as $column)
                            <div class="col-md-4">
                                @foreach($column as $question)
                                    @if($question['section'])
                                        <h6 class="fw-bold fs-7 fst-italic mb-3">{{ $question['section'] }}</h6>
                                    @endif

                                    <div class="form-group mb-4 d-flex flex-column align-items-start">
                                        <label class="form-label fs-7 fw-semibold text-gray-700">
                                            {{ $question['label'] }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex gap-4">
                                            <x-radio
                                                name="{{ $question['name'] }}"
                                                caption="Ya"
                                                value="Ya"
                                                :checked="($data_kuisioner[$question['name']] ?? '') === 'Ya'"
                                            />
                                            <x-radio
                                                name="{{ $question['name'] }}"
                                                caption="Tidak"
                                                value="Tidak"
                                                :checked="($data_kuisioner[$question['name']] ?? 'Tidak') === 'Tidak'"
                                            />
                                            <x-radio
                                                name="{{ $question['name'] }}"
                                                caption="Diisi Petugas"
                                                value="Diisi Petugas"
                                                :checked="($data_kuisioner[$question['name']] ?? '') === 'Diisi Petugas'"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="tab_hematologi" role="tabpanel">
                    <h4 class="fs-6 fw-bold text-gray-800 mb-5">
                        <i class="ki-duotone ki-pulse fs-4 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        Hasil Uji Hematologi
                    </h4>
                    <div class="row g-5">
                <div class="col-3">
                    <div class="row">
                        <div class="col-6">
                            <x-io-select :viewtype="2" name="golongan_darah" caption="Golongan Darah" placeholder="-Pilih-" :options="array_combine($golongan_darah_options, $golongan_darah_options)" :value="$pemeriksaan_hb->golongan_darah ?? ''" />
                        </div>
                        <div class="col-6">
                            <x-io-select :viewtype="2" name="rhesus" caption="Rhesus"  placeholder="-Pilih-" :options="array_combine($rhesus_options, $rhesus_options)" :value="$pemeriksaan_hb->rhesus ?? ''" />
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <x-io-select :viewtype="2" name="metode" caption="Metode" :options="array_combine($metode_options, $metode_options)" :value="$pemeriksaan_hb->metode ?? ''" />
                </div>
                <div class="col-3">
                    <x-io-select :viewtype="2" name="lengan" caption="Lengan Ambil Sebelah" :options="array_combine($lengan_options, $lengan_options)" :value="$pemeriksaan_hb->lengan ?? ''" />
                </div>
                <div class="col-3">
                    <x-io-input :viewtype="2" name="hb_meter" caption="HB Meter (gr/dL)" :value="$pemeriksaan_hb->hb_meter ?? ''" />
                </div>
                </div>
                <div class="row g-5">
                    <div class="col-3">
                        <x-io-input :viewtype="2" name="trombosit" caption="Trombosit" :value="$pemeriksaan_hb->trombosit ?? 0" />
                    </div>
                    <div class="col-3">
                        <x-io-input :viewtype="2" name="lecosit" caption="Lecosit" :value="$pemeriksaan_hb->lecosit ?? 0" />
                    </div>
                    <div class="col-3">
                        <x-io-input :viewtype="2" name="eritrosit" caption="Eritrosit" :value="$pemeriksaan_hb->eritrosit ?? 0" />
                    </div>
                    <div class="col-3">
                        <x-io-input :viewtype="2" name="hematocrit" caption="Hematocrit (%)" :value="$pemeriksaan_hb->hematocrit ?? 0" />
                    </div>
                </div>
                <div class="row g-5 mb-3">
                    <div class="col-md-3">
                        <x-io-select name="status" caption="Status" :options="$list_status" :value="$pemeriksaan_hb->status ?? ''" :viewtype="2" />
                    </div>
                    <div class="col-md-9">
                        <x-io-select name="alasan" caption="Alasan" placeholder="-Pilih-" :options="array_combine($list_alasan, $list_alasan)" :value="$pemeriksaan_hb->alasan ?? ''" :viewtype="2" />
                    </div>
                </div>
                </div>
                <div class="tab-pane fade" id="tab_aftap" role="tabpanel">
                     <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="bullet bullet-dot bg-primary h-8px w-8px"></span>
                    <span class="fs-7 fw-bold text-primary text-uppercase ls-1">Data Aftap</span>
                </div>
                <div class="row g-4">                  
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="no_kantong" id="no_kantong" caption="No. Kantong" :value="$aftap->no_kantong ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="no_selang" caption="No. Selang" :value="$aftap->no_selang ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-select :viewtype="2" name="id_hemoscale" caption="ID Hemoscale" :options="array_combine(arrayNumber(15), arrayNumber(15))" :value="$aftap->id_hemoscale ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="jam_mulai" caption="Jam Mulai" :value="$aftap->jam_mulai ?? ''" class="timepicker" />
                    </div>
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="jam_selesai" caption="Jam Selesai" :value="$aftap->jam_selesai ?? ''" class="timepicker" />
                    </div>
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="stop_pada"  id="stop_pada" caption="Stop Pada (cc)" :value="$aftap->stop_pada ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-select :viewtype="2" name="cara_ambil" caption="Cara Ambil" :options="$cara_ambil_options" :value="$aftap->cara_ambil ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-select :viewtype="2" name="jenis_donor" caption="Jenis Donor" :options="$jenis_donor_options" :value="$aftap->jenis_donor ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-select :viewtype="2" name="reaksi_donor" caption="Reaksi Donor" :options="$reaksi_donor_options" :value="$aftap->reaksi_donor ?? ''" />
                    </div>
                </div>
            </div>

            <div class="separator separator-dashed mb-5"></div>

            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="bullet bullet-dot bg-primary h-8px w-8px"></span>
                    <span class="fs-7 fw-bold text-primary text-uppercase ls-1">Kondisi & Keterangan</span>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Sample Darah <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <x-radio name="sample_darah" caption="Ya" :value="true" :checked="($aftap->sample_darah ?? true) == true" />
                            <x-radio name="sample_darah" caption="Tidak" :value="false" :checked="($aftap->sample_darah ?? true) == false" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Darah Lancar <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <x-radio name="darah_lancar" caption="Ya" :value="true" :checked="($aftap->darah_lancar ?? true) == true" />
                            <x-radio name="darah_lancar" caption="Tidak" :value="false" :checked="($aftap->darah_lancar ?? true) == false" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Cuci Lengan <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <x-radio name="cuci_tangan" caption="Ya" :value="true" :checked="($aftap->cuci_tangan ?? true) == true" />
                            <x-radio name="cuci_tangan" caption="Tidak" :value="false" :checked="($aftap->cuci_tangan ?? true) == false" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Penusukan Sulit <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <x-radio name="penusukan_sulit" caption="Ya" :value="true" :checked="($aftap->penusukan_sulit ?? true) == true" />
                            <x-radio name="penusukan_sulit" caption="Tidak" :value="false" :checked="($aftap->penusukan_sulit ?? true) == false" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Donor Sewaktu-waktu <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <x-radio name="donor_sewaktu_waktu" caption="Ya" :value="true" :checked="($aftap->donor_sewaktu_waktu ?? true) == true" />
                            <x-radio name="donor_sewaktu_waktu" caption="Tidak" :value="false" :checked="($aftap->donor_sewaktu_waktu ?? true) == false" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-2">Bersedia Dikirim Surat <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <x-radio name="bersedia_dikirim_surat" caption="Ya" :value="true" :checked="($aftap->bersedia_dikirim_surat ?? true) == true" onclick="toggle_alamat(false)" />
                            <x-radio name="bersedia_dikirim_surat" caption="Tidak" :value="false" :checked="($aftap->bersedia_dikirim_surat ?? true) == false" onclick="toggle_alamat(true)" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <x-io-input 
                            :viewtype="2"
                            name="cc_ambil"
                            id="cc_ambil"
                            caption="Cc Ambil"
                            :value="$aftap->cc_ambil ?? ''"
                            
                        />
                    </div>
                    <div class="col-md-8 d-none" id="catatan_container">
                        <x-io-input 
                            :viewtype="2"
                            name="catatan_gagal"
                            id="catatan_gagal"
                            caption="Catatan Kegagalan"
                        />
                    </div>

                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="satelit" id="satelit" caption="Satelit" :value="$aftap->satelit ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" readonly name="durasi" caption="Durasi" :value="$aftap->durasi ?? ''" />
                    </div>
                    <div class="col-md-4">
                        <x-io-input :viewtype="2" name="lain_lain" caption="Lain-lain" :value="$aftap->lain_lain ?? ''" />
                    </div>
                    <div class="col-md-8">
                        <x-io-input :viewtype="2" name="alamat_surat" caption="Alamat Surat" :value="$aftap->alamat_surat ?? ''" />
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Jenis Kantong</label>

                    <select name="tipe_kantong_id" id="tipe_kantong_id"
                        class="form-select form-select-solid">

                        @foreach(\App\Models\TipeKantong::with('jenisKantong')->get() as $item)
                            <option value="{{ $item->id }}"
                                {{ optional($aftap->logDonor->pemeriksaanDokter)->tipe_kantong_id == $item->id ? 'selected' : '' }}>
                                
                                {{ optional($item->jenisKantong)->nama }}
                                - {{ $item->nama }}
                            </option>
                        @endforeach

                    </select>
                </div>
            </div>

                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="ki-duotone ki-check fs-5"><span class="path1"></span><span class="path2"></span></i>
                        Simpan Pemeriksaan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
     init_form_element();
    init_form({{ $id }});

    var $status = $('#status');
    var $jenis_kantong = $('#jenis_kantong');
    var $tipe_jenis_kantong = $('#tipe_jenis_kantong');

    var list_tipe_jenis_kantong =
        JSON.parse(`@json($list_tipe_jenis_kantong)`);

    // batas HB
    var batasHb = @json(
        optional(optional($pemeriksaan_hb->logDonor)->donor)->jenis_kelamin === 'Wanita'
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

    // aftap 
     toggle_alamat = (status) => {
        $('#alamat_surat').attr('disabled', status);
    }

    $('.timepicker').flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    // =====================================================
    // HITUNG DURASI
    // =====================================================

    function hitungDurasi() {

        let start = $('#jam_mulai').val();
        let end   = $('#jam_selesai').val();

        if (!start || !end) return;

        let [sh, sm] = start.split(':').map(Number);
        let [eh, em] = end.split(':').map(Number);

        let startMenit = (sh * 60) + sm;
        let endMenit   = (eh * 60) + em;

        if (endMenit < startMenit) {
            endMenit += 24 * 60;
        }

        let selisih = endMenit - startMenit;

        let jam   = Math.floor(selisih / 60);
        let menit = selisih % 60;

        $('#durasi').val(jam + ' Jam ' + menit + ' Menit');
    }

    $('#jam_mulai').on('change', hitungDurasi);
    $('#jam_selesai').on('change', hitungDurasi);

    toggle_alamat(true);

    // =====================================================
    // SCAN KANTONG
    // =====================================================

    $('#no_kantong').on('change', function () {

        let no_kantong = $(this).val();

        if (!no_kantong) return;

        $.post("{{ route('unit.aftap.scan_kantong') }}", {
            _token: "{{ csrf_token() }}",
            no_kantong: no_kantong

        }, function (res) {

            if (!res.success) {

                Swal.fire('Error', res.message, 'error');
                return;
            }

            // reset form gagal
            $('#manual_cc_container').addClass('d-none');
            $('#catatan_container').addClass('d-none');

            $('#cc_manual').val('');
            $('#catatan_gagal').val('').removeAttr('required');

            // default
            $('#status').val('Approved');

            // auto isi
            $('#cc_ambil').val(res.data.cc);
            $('#stop_pada').val(res.data.cc);
            $('#satelit').val(res.data.satelit);

            // =========================================
            // JIKA STOP
            // =========================================

            if (res.data.is_stop) {

                $('#status').val('Gagal');

                $('#manual_cc_container').removeClass('d-none');
                $('#catatan_container').removeClass('d-none');

                $('#catatan_gagal').attr('required', true);

                $('#cc_ambil').val('');
                $('#stop_pada').val('STOP');

                Swal.fire({
                    icon: 'warning',
                    title: 'Kantong STOP',
                    text: 'Isi CC manual dan catatan'
                });

            } else {

                Swal.fire({
                    icon: 'success',
                    title: 'Kantong ditemukan',
                    text: `CC ${res.data.cc} | Satelit ${res.data.satelit}`
                });
            }

        }).fail(function (xhr) {

            Swal.fire('Error', xhr.responseText, 'error');

        });

    });
 // =====================================================
// VALIDASI STOP PADA — input manual langsung
// =====================================================
// =====================================================
// VALIDASI STOP PADA — input manual langsung
// =====================================================

let _stopPadaTimer = null; // debounce timer

$('#stop_pada').on('keyup change', function () {

    // Debounce — tunggu user selesai ketik (600ms)
    clearTimeout(_stopPadaTimer);
    _stopPadaTimer = setTimeout(function () {

        let raw = $('#stop_pada').val().trim();

        // Jika kosong, reset semua
        if (raw === '') {
            $('#catatan_container').addClass('d-none');
            $('#catatan_gagal').removeAttr('required').val('');
            $('#status').val('Approved');
            return;
        }

        // Jika STOP (dari scan kantong), jangan proses ulang
        if (raw.toUpperCase() === 'STOP') return;

        let cc = parseInt(raw);

        // Bukan angka valid
        if (isNaN(cc)) return;

        // cc < 250 = Gagal, tampilkan catatan SAJA (tanpa Swal)
        if (cc > 0 && cc < 250) {

            $('#status').val('Gagal');
            $('#catatan_container').removeClass('d-none');
            $('#catatan_gagal').attr('required', true);

        // cc >= 250 = Approved, sembunyikan catatan
        } else if (cc >= 250) {

            $('#status').val('Approved');
            $('#catatan_container').addClass('d-none');
            $('#catatan_gagal').removeAttr('required').val('');

        } else {

            $('#status').val('Gagal');
        }

       
    }, 600);
});

</script>
