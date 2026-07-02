<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($return_supplier) ? 'Ubah' : 'Tambah' }} Pengembalian Barang</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="no_return" caption="No.Pengembalian" :value="$return_supplier->no_return ?? ''" readonly required />
        <x-io-input name="tgl_return" caption="Tanggal Pengembalian" :value="formatDate($return_supplier->tgl_return ?? '')" required />
        <x-io-select name="supplier_id" caption="Supplier" :options="$supplier_options" :value="$return_supplier->supplier_id ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-select name="barang_id" caption="Barang" :options="$barang_options" :value="$return_supplier->barang_id ?? ''" data-dropdown-parent="#modal_info" required />

        <x-io-input type="number" name="qty" caption="Qty" :value="$return_supplier->qty ?? ''" required />
        <x-io-input name="satuan" caption="Satuan" :value="$return_supplier->satuan ?? ''" />
        <x-io-textarea name="keterangan" caption="Keterangan" :value="$return_supplier->keterangan ?? ''" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $return_supplier->id ?? '' }});
</script>
