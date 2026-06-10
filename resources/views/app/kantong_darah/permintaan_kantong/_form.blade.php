<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">{{ !empty($permintaan_kantong) ? 'Ubah' : 'Tambah' }} Permintaan Kantong</h3>
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
                        <div class="row">
                            <div class="col-lg-6">
                                <x-io-input :viewtype="2" name="nomor" caption="No.Permintaan" :value="$permintaan_kantong->nomor ?? ''" required />
                            </div>
                            <div class="col-lg-6">
                                <x-io-input :viewtype="2" name="tanggal" caption="Tanggal" :value="formatDate($permintaan_kantong->tanggal ?? date('d-m-Y'))" class="datepicker" required />
                            </div>
                        </div>

                        <x-io-select :viewtype="2" name="petugas_id" caption="Pertugas" :options="$petugas_options" :value="$permintaan_kantong->petugas_id ?? ''" required />
                        <x-io-textarea :viewtype="2" name="keterangan" caption="Keterangan" :value="$permintaan_kantong->keterangan ?? ''" />
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
                            <div class="row">
                                <div class="col-lg-3">
                                    <x-io-select :viewtype="2" name="jenis_kantong_id" :options="$jenis_kantong_options" caption="Jenis Kantong" />
                                </div>
                                <div class="col-lg-3">
                                    <x-io-select :viewtype="2" name="tipe_kantong_id" caption="Tipe Kantong" />
                                </div>
                                <div class="col-lg-3">
                                    <x-io-input type="number" :viewtype="2" name="jumlah" caption="Jumlah" />
                                </div>
                                <div class="col-lg-3">
                                    <button class="btn btn-sm btn-success w-100 mt-7" type="button" onclick="save_detail()">Tambahkan Detail</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
                                    <thead>
                                    <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                        <th class="w-10px ps-4 rounded-start">#</th>
                                        <th>Jenis Kantong</th>
                                        <th>Tipe Kantong</th>
                                        <th>Jumlah</th>
                                        <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($no = 1)
                                    @foreach($permintaan_kantong->details as $item)
                                        <tr>
                                            <td class="ps-4">{{ $no++ }}</td>
                                            <td>{{ $item->tipeKantong->jenisKantong->nama ?? '-' }}</td>
                                            <td>{{ $item->tipeKantong->nama ?? '-' }}</td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td class="text-end text-nowrap">
                                                <button class="btn btn-sm btn-primary px-4 py-1" type="button" onclick="confirm_delete_detail({{ $item->id }})">
                                                    Hapus
                                                </button>
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
        <button type="submit" class="btn btn-sm btn-primary">{{ empty($permintaan_kantong) ? 'Lanjutkan' : 'Update' }}</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $permintaan_kantong->id ?? '' }});

    $jenis_kantong_id = $('#jenis_kantong_id');
    $tipe_kantong_id = $('#tipe_kantong_id');
    list_tipe_jenis_kantong = JSON.parse(`@json($tipe_kantong_options)`);
    $jenis_kantong_id.change(() => {
        let jenis_kantong_id = $jenis_kantong_id.find('option:selected').val();
        let list_selected_tipe = list_tipe_jenis_kantong.filter(val =>
            val.jenis_kantong_id.toString() === jenis_kantong_id.toString()
        );
        $tipe_kantong_id.html('<option value="">-Pilih-</option>');
        $.each(list_selected_tipe, (i, val) => {
            $tipe_kantong_id.append('<option value="'+ val.id +'" >'+ val.nama +'</option>');
        });
        $tipe_kantong_id.change();
    });
    $jenis_kantong_id.change();

    @if(!empty($permintaan_kantong))
        $detail_table = $('#detail_table');
        permintaan_kantong_id = '{{ $permintaan_kantong->id ?? '' }}';
        save_detail = () => {
            let tipe_kantong_id = $tipe_kantong_id.find('option:selected').val();
            let jumlah = $('#jumlah').val();
            if (tipe_kantong_id === '' || jumlah === '') {
                Swal.fire({icon: 'error', title: 'Lengkapi data!'});
                return;
            }
            $.post(base_url + '/' + permintaan_kantong_id + '/detail', {_token, tipe_kantong_id, jumlah}, () => info(permintaan_kantong_id)).fail((xhr) => $detail_table.html(xhr.responseText));
        }

        confirm_delete_detail = (id) => {
            Swal.fire(swal_delete_params).then((result) => {
                if (result.isConfirmed) {
                    $.post(base_url + '/' + permintaan_kantong_id + '/detail/' + id, {_token, _method: 'delete'}, () => info(permintaan_kantong_id)).fail((xhr) => $detail_table.html(xhr.responseText));
                }
            });
        }
    @endif
</script>
