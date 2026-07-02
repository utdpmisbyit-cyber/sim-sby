<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($jenis_biaya) ? 'Ubah' : 'Tambah' }} Jenis Biaya</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="kode" caption="Kode" :value="$jenis_biaya->kode ?? ''" required />
        <div class="row mb-4" id="form_group_harga">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Harga</label>
            <div class="col-lg-9">
                <x-input name="harga" caption="Harga" :value="$jenis_biaya->harga ?? ''" class="autonumeric" required />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $jenis_biaya->id ?? '' }});
</script>
