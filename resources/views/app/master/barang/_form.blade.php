<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($barang) ? 'Ubah' : 'Tambah' }} Barang</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-select name="cabang_id" caption="Status" :options="$list_cabang" :value="$barang->cabang_id ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-select name="jenis_barang" caption="Jenis Barang" :options="$list_jenis" :value="$barang->jenis_barang ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-input name="kode" caption="Kode" :value="$barang->kode ?? ''" required />
        <x-io-input name="nama" caption="Nama" :value="$barang->nama ?? ''" required />
        <x-io-input type="number" name="stok" caption="Stok" :value="$barang->stok ?? ''" />
        <x-io-input type="number" name="min_stok" caption="Stok Minimal" :value="$barang->min_stok ?? ''" />
        <x-io-input name="satuan" caption="Satuan" :value="$barang->satuan ?? ''" />
        <div class="row mb-4" id="form_group_harga_satuan">
            <label class="col-lg-3 col-form-label required fw-bold fs-6">Harga</label>
            <div class="col-lg-9">
                <x-input name="harga_satuan" caption="Harga Satuan" :value="$barang->harga_satuan ?? ''" class="autonumeric" required />
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
    init_form({{ $barang->id ?? '' }});
</script>
