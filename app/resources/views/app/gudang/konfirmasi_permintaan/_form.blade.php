<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">Konfirmasi Permintaan Kantong</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="card card-flush border mb-4">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Informasi Permintaan</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <table class="table table-borderless">
                            <tr><td>No.Permintaan</td><td>: {{ $permintaan_kantong->nomor }}</td></tr>
                            <tr><td>Tanggal</td><td>: {{ formatDate($permintaan_kantong->tanggal) }}</td></tr>
                            <tr><td>Petugas</td><td>: {{ ($permintaan_kantong->petugas->nama) }}</td></tr>
                            <tr><td>Keterangan</td><td>: {{ ($permintaan_kantong->keterangan) }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                @if(!empty($permintaan_kantong))
                    <div class="card card-flush border mb-4">
                        <div class="card-header min-h-40px px-4 pt-3 pb-0">
                            <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Detail Permintaan</h6>
                        </div>
                        <div class="card-body px-4 pt-3 pb-4">
                            <div class="table-responsive">
                                <table class="table align-top table-row-dashed table-hover fs-7 table-sm">
                                    <thead>
                                    <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                        <th class="w-10px ps-4 rounded-start">#</th>
                                        <th>Jenis Kantong</th>
                                        <th>Tipe Kantong</th>
                                        <th>Jumlah</th>
                                        <th class="text-center pe-4 rounded-end">Nomor Kantong</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no = 1)
                                    @foreach($permintaan_kantong->details ?? [] as $item)
                                        <tr>
                                            <td class="ps-4">{{ $no++ }}</td>
                                            <td>{{ $item->tipeKantong->jenisKantong->nama ?? '-' }}</td>
                                            <td>{{ $item->tipeKantong->nama ?? '-' }}</td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td class="text-end text-nowrap">
                                                <div class="row">
                                                    @for($i = 1; $i <= $item->jumlah; $i++)
                                                        <div class="col-3">
                                                            <x-input name="barcode_{{ $item->id }}_{{ $i }}" class="fs-8 py-0 mb-1 h-25px no-kantong" style="min-height: unset;" caption="No.Kantong {{ $i }}" required />
                                                        </div>
                                                    @endfor
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal-footer py-3 px-6">
        <button type="button" class="btn btn-sm btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Proses Konfirmasi</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $permintaan_kantong->id ?? '' }});

    $('.no-kantong').keypress(function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const currentInput = this;
            const no_kantong = $(currentInput).val().trim();
            if (no_kantong !== '') {
                check_no_kantong(no_kantong, currentInput, true);
            }
        }
    });

    $('.no-kantong').change(function() {
        const currentInput = this;
        const no_kantong = $(currentInput).val().trim();

        if (no_kantong !== '') {
            check_no_kantong(no_kantong, currentInput, false);
        }
    });

    check_no_kantong = (no_kantong, currentInput, moveNext) => {
        $.post("{{ route('gudang.pendataan_kantong.search') }}", {_token, barcode: no_kantong, ajax: 1}, (result) => {
            if (result.length === 0) {
                swal.fire({
                    icon: 'error',
                    title: 'No Kantong Tidak Ditemukan!'
                }).then(() => {
                    $(currentInput).val('').focus();
                });
            } else {
                if (moveNext) {
                    const $form = $(currentInput).closest('form');
                    const $inputs = $form.find('.no-kantong');
                    const currentIndex = $inputs.index(currentInput);

                    if (currentIndex + 1 < $inputs.length) {
                        $inputs.eq(currentIndex + 1).focus();
                    }
                }
            }
        });
    }
</script>
