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
            </ul>

            <div class="tab-content" id="tab_content">
                <div class="tab-pane fade show active" id="tab_info" role="tabpanel">
                    <h3 class="fw-bold fs-4 text-dark">Form Pemeriksaan Dokter</h3>
                    <div class="separator separator-dashed mb-6"></div>
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
                           <x-io-input
                                type="number"
                                step="0.1"
                                name="cc_ambil"
                                caption="Cc Ambil"
                                placeholder="350"
                                :value="$pemeriksaan_dokter->cc_ambil ?? ''"
                                :viewtype="2"
                            />
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
                            <x-io-select name="jenis_kantong_id" placeholder="-Pilih-" caption="Jenis Kantong" :options="$jenis_kantong_options" :value="$pemeriksaan_dokter->tipeKantong->jenis_kantong_id ?? ''" :viewtype="2" />
                        </div>
                        <div class="col-md-3">
                            <x-io-select name="tipe_kantong_id" placeholder="-Pilih-" caption="Tipe Jenis Kantong" :options="[]" :viewtype="2" />
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

                    <div class="d-flex justify-content-end gap-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ki-duotone ki-check fs-5"><span class="path1"></span><span class="path2"></span></i>
                            Simpan Pemeriksaan
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    init_form_element();
    init_form({{ $id }});

    $status = $('#status');
    $jenis_kantong_id = $('#jenis_kantong_id');
    $tipe_kantong_id = $('#tipe_kantong_id');
    list_tipe_jenis_kantong = JSON.parse(`@json($tipe_kantong_options)`);

    $status.change(() => {
        let $alasan = $('#alasan')

        let status = $status.find('option:selected').val();
        if (status === 'Approved') {
            $alasan.val('').change();
            $alasan.attr('disabled', true);

            $jenis_kantong_id.attr('disabled', false);
            $tipe_kantong_id.attr('disabled', false);
        } else {
            $alasan.attr('disabled', false);

            $jenis_kantong_id.val('').change();
            $tipe_kantong_id.val('').change();
            $jenis_kantong_id.attr('disabled', true);
            $tipe_kantong_id.attr('disabled', true);
        }
    });
    $status.change();

    $jenis_kantong_id.change(() => {
        let jenis_kantong_id = $jenis_kantong_id.find('option:selected').val();
        let list_selected_tipe = list_tipe_jenis_kantong.filter(val =>
            val.jenis_kantong_id.toString() === jenis_kantong_id.toString()
        );
        let selected_tipe_kantong_id = '{{ $pemeriksaan_dokter->tipe_kantong_id ?? '' }}'

        $tipe_kantong_id.html('<option value="">-Pilih-</option>');
        $.each(list_selected_tipe, (i, val) => {
            $tipe_kantong_id.append('<option value="'+ val.id +'" '+ (selected_tipe_kantong_id.toString() === val.id.toString() ? 'selected' : '') +'>'+ val.nama +'</option>');
        });
        $tipe_kantong_id.change();
    });
    $jenis_kantong_id.change();
</script>
