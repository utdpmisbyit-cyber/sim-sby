<form id="form_info">
    @csrf

    @if(isset($pengeluaran_barang))
        <input type="hidden" name="_method" value="PUT">
    @endif

    <div class="modal-header">
        <h3 class="modal-title">
            {{ isset($pengeluaran_barang) ? 'Ubah' : 'Tambah' }} Pengeluaran Barang
        </h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
            <i class="ki-duotone ki-cross fs-1"></i>
        </div>
    </div>

    <div class="modal-body">

        {{-- ====== INFO TRANSAKSI ====== --}}
        <div class="row g-4 mb-2">
            <div class="col-md-4">
                <x-io-input name="no_trans_keluar" caption="No Transaksi"
                    :value="$pengeluaran_barang->no_trans_keluar ?? 'OUT-' . date('YmdHis')"
                    :viewtype="2" readonly />
            </div>

            <div class="col-md-4">
                <x-io-input name="tgl_keluar" type="date" caption="Tanggal"
                    :value="$pengeluaran_barang->tgl_keluar ?? date('Y-m-d')"
                    :viewtype="2" required />
            </div>

            <div class="col-md-4">
                <x-io-select name="status" caption="Status"
                    :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']"
                    :value="$pengeluaran_barang->status ?? 'aktif'" />
            </div>
        </div>

        <hr class="border-gray-200 my-4">

        {{-- ====== BARANG & TUJUAN ====== --}}
        <div class="row g-4 mb-2">
            <div class="col-md-6">
                <x-io-select name="barang_id" caption="Barang"
                    :options="$barang_options ?? []"
                    :value="old('barang_id', $pengeluaran_barang->barang_id ?? '')"
                    required />
            </div>

            <div class="col-md-6">
                <x-io-select name="bagian_id" caption="Bagian Tujuan"
                    :options="$bagian_options ?? []"
                    :value="old('bagian_id', $pengeluaran_barang->bagian_id ?? '')"
                    required />
            </div>
        </div>

        {{-- ====== JUMLAH & SATUAN ====== --}}
        <div class="row g-4 mb-2">
            <div class="col-md-6">
                <x-io-input type="number" name="qty_keluar" caption="Jumlah"
                    :value="$pengeluaran_barang->qty_keluar ?? ''"
                    :viewtype="2" required />
            </div>

            <div class="col-md-6">
                <x-io-input name="satuan" caption="Satuan"
                    :value="$pengeluaran_barang->satuan ?? ''"
                    :viewtype="2" required />
            </div>
        </div>

        <hr class="border-gray-200 my-4">

        {{-- ====== KETERANGAN ====== --}}
        <div class="row g-4">
            <div class="col-md-12">
                <x-io-input name="keterangan" caption="Keterangan"
                    :value="$pengeluaran_barang->keterangan ?? ''"
                    :viewtype="2" />
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
init_form_element();
init_form(@json($pengeluaran_barang->no_trans_keluar ?? ''));
</script>