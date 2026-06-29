<div class="card card-flush rounded-4 border-0 shadow-xs h-100" id="form_pemeriksaan_section">
    <div class="card-header pt-6 pb-0 border-0">
        <div class="card-title">
            <div class="d-flex align-items-center gap-3">
                <div class="w-40px h-40px rounded-2 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center">
                    <i class="ki-duotone ki-pulse fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <div>
                    <h4 class="fs-5 fw-bold text-gray-800 mb-0">Form Aftap</h4>
                    <span class="text-muted fs-7">Pengisian data pengambilan darah donor</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body pt-6">
        <form id="form_info">
            @csrf

            <input type="hidden" name="status" id="status" value="Approved">
            <input type="hidden" name="asal_darah"
                   value="{{ optional($aftap->logDonor->pemeriksaanDokter)->asal_darah ?? '' }}">
            <input type="hidden" name="log_donor_id"
                   value="{{ $aftap->log_donor_id }}">
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

            <div class="separator separator-dashed mb-5"></div>

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

        $.post("{{ route('aftap.aftap.scan_kantong') }}", {
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

    _stopPadaTimer = null; // debounce timer

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
