<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($tipe_kantong) ? 'Ubah' : 'Tambah' }} Tipe Kantong</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-select name="jenis_kantong_id" caption="Jenis Kantong" :options="$jenis_kantong_options" :value="$tipe_kantong->jenis_kantong_id ?? ''" required />
        <x-io-input name="nama" caption="Nama" :value="$tipe_kantong->nama ?? ''" required />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $tipe_kantong->id ?? '' }});
</script>
