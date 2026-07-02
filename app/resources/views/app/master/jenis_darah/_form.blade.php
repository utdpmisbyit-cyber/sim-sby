<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($jenis_darah) ? 'Ubah' : 'Tambah' }} Jenis Darah</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="nama" caption="Nama" :value="$jenis_darah->nama ?? ''" required />
        <x-io-input name="nama_pendek" caption="Nama Pendek" :value="$jenis_darah->nama_pendek ?? ''" />
        <x-io-input name="umur_darah" caption="Umur Darah" :value="$jenis_darah->umur_darah ?? ''" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $jenis_darah->id ?? '' }});
</script>
