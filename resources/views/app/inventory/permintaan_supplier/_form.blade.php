<form id="form_info">
    @csrf
    <input type="hidden" name="no_permintaan" value="{{ $permintaan_supplier->no_permintaan ?? '' }}">

    <div class="modal-header">
        <h3 class="modal-title">
            {{ isset($permintaan_supplier) ? 'Ubah' : 'Tambah' }} Permintaan Supplier
        </h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
            <i class="ki-duotone ki-cross fs-1"></i>
        </div>
    </div>

    <div class="modal-body">
        <div class="row g-4">
            {{-- Tanggal Permintaan --}}
            <div class="col-md-6">
                <x-io-input name="tgl_permintaan" type="date" caption="Tanggal Permintaan"
                    :value="$permintaan_supplier->tgl_permintaan ?? date('Y-m-d')" :viewtype="2" required />
            </div>

            {{-- Supplier --}}
            <div class="col-md-6">
                <x-io-select name="supplier_id" caption="Supplier"
                    :options="$supplier_options ?? []"
                    :value="$permintaan_supplier->supplier_id ?? ''" />
            </div>

            {{-- Barang --}}
            <div class="col-md-6">
                <x-io-select name="barang_id" caption="Barang"
                    :options="$barang_options ?? []"
                    :value="$permintaan_supplier->barang_id ?? ''" />
            </div>

            {{-- Qty --}}
            <div class="col-md-6">
                <x-io-input type="number" name="qty" caption="Jumlah" :value="$permintaan_supplier->qty ?? ''" :viewtype="2" required />
            </div>

            {{-- Satuan --}}
            <div class="col-md-6">
                <x-io-input name="satuan" caption="Satuan" :value="$permintaan_supplier->satuan ?? ''" :viewtype="2" required />
            </div>

            {{-- Status --}}
            <div class="col-md-6">
                <x-io-select name="status" caption="Status"
                    :options="['1' => 'Aktif', '0' => 'Tidak Aktif']"
                    :value="$permintaan_supplier->status ?? '1'" />
            </div>

            {{-- Keterangan --}}
            <div class="col-md-12">
                <x-io-input name="keterangan" caption="Keterangan" :value="$permintaan_supplier->keterangan ?? ''" :viewtype="2" />
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
init_form(@json($permintaan_supplier->no_permintaan ?? ''));
</script>