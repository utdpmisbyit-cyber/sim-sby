<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">Konfirmasi Hasil Litbang</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">
            <div class="col-lg-6">
                <h6 class="fw-bold fs-7 text-uppercase text-muted mb-4">Informasi Pengiriman</h6>
                <table class="table table-sm table-borderless fs-7 align-middle mb-4">
                    <tr>
                        <td class="text-muted w-150px">No. Kantong</td>
                        <td class="fw-bold text-dark">: {{ $konfirmasi_litbang->no_kantong }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kode Aftap</td>
                        <td class="fw-bold text-dark">: {{ $konfirmasi_litbang->aftap->kode ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama Donor</td>
                        <td class="fw-bold text-dark">: {{ $konfirmasi_litbang->donor->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gol. Darah Awal</td>
                        <td class="fw-bold text-dark">: {{ $konfirmasi_litbang->donor ? ($konfirmasi_litbang->donor->golongan_darah . $konfirmasi_litbang->donor->rhesus) : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Kirim</td>
                        <td class="fw-bold text-dark">: {{ formatDate($konfirmasi_litbang->tanggal_kirim) }}</td>
                    </tr>
                </table>

                <div class="mb-4">
                    <x-io-input name="tanggal_konfirmasi" caption="Tanggal Konfirmasi" :value="formatDate($konfirmasi_litbang->tanggal_konfirmasi ?? date('d-m-Y'))" class="datepicker" required />
                </div>

                <div class="mb-4 row">
                    <label class="form-label fs-7 fw-semibold text-gray-700 required col-md-3">Petugas Konfirmasi</label>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-4">
                                <input type="hidden" name="petugas_konfirmasi_id" id="petugas_konfirmasi_id" value="{{ $konfirmasi_litbang->petugas_konfirmasi_id ?? '' }}">
                                <x-input name="petugas_konfirmasi_kode" caption="" :value="$konfirmasi_litbang->petugasKonfirmasi->kode ?? ''" placeholder="Kode petugas" autocomplete="off" required />
                            </div>
                            <div class="col-8">
                                <div class="form-text fs-5 text-dark" id="petugas_konfirmasi_nama">{{ $konfirmasi_litbang->petugasKonfirmasi->nama ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <h6 class="fw-bold fs-7 text-uppercase text-muted mb-4">Hasil Konfirmasi Litbang</h6>
                <div class="mb-4">
                    <x-io-select name="golongan_darah" caption="Golongan Darah Hasil" :options="$golongan_darah_options" :value="$konfirmasi_litbang->golongan_darah ?? ''" required />
                </div>

                <div class="mb-4">
                    <x-io-select name="rhesus" caption="Rhesus Hasil" :options="$rhesus_options" :value="$konfirmasi_litbang->rhesus ?? ''" required />
                </div>

                <div class="mb-4">
                    <x-io-textarea name="keterangan" caption="Keterangan Tambahan" :value="$konfirmasi_litbang->keterangan ?? ''" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer py-3 px-6">
        <button type="button" class="btn btn-sm btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Simpan Konfirmasi</button>
    </div>
</form>

<script>
    init_form_element();
    $tanggalInput = $('#tanggal_konfirmasi');
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

    petugasLookupUrl = '{{ route('serologi.konfirmasi_litbang.petugas_by_kode') }}';

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

    setupPetugasLookup('petugas_konfirmasi', true);

    $('#form_info').on('submit', function (e) {
        if (!$('#petugas_konfirmasi_id').val()) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Kode Petugas Konfirmasi tidak valid'});
            return;
        }
    });

    init_form({{ $konfirmasi_litbang->id ?? '' }});
</script>
