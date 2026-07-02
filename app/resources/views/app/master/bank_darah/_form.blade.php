<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($bank_darah) ? 'Ubah' : 'Tambah' }} Bank Darah</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="nama" caption="Nama" :value="$bank_darah->nama ?? ''" required />
        <x-io-select name="jenis" caption="Jenis" :options="$list_jenis" :value="$bank_darah->jenis ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-textarea name="alamat" caption="Alamat" :value="$bank_darah->alamat ?? ''" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $bank_darah->id ?? '' }});
</script>
