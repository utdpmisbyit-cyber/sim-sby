<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($supplier) ? 'Ubah' : 'Tambah' }} Supplier</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="kode" caption="Kode" :value="$supplier->kode ?? ''" required />
        <x-io-input name="nama" caption="Nama" :value="$supplier->nama ?? ''" required />
        <x-io-input name="alamat" caption="Alamat" :value="$supplier->alamat ?? ''" />
        <x-io-input name="kode_pos" caption="Kode Pos" :value="$supplier->kode_pos ?? ''" />
        <x-io-input name="no_telp" caption="No. Telp" :value="$supplier->no_telp ?? ''" />
        <x-io-input name="status" caption="Status" :value="$supplier->status ?? ''" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $supplier->id ?? '' }});
</script>
