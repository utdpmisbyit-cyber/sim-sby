<form id="form_info">
@csrf 

<div class="modal-header">
<h3 class="modal-title">
{{ !empty($barang) ? 'Ubah' : 'Tambah' }} Barang
</h3>

<div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
data-bs-dismiss="modal">
<i class="ki-duotone ki-cross fs-1"></i>
</div>
</div>

<div class="modal-body">

<div class="row g-4">

<div class="col-md-6">
<x-io-input
name="kode"
caption="Kode Barang"
:value="$barang->kode ?? ''"
:viewtype="2"
required
/>
</div>

<div class="col-md-6">
<x-io-input
name="nama"
caption="Nama Barang"
:value="$barang->nama ?? ''"
:viewtype="2"
required
/>
</div>

<div class="col-md-6">
<x-io-input
name="satuan"
caption="Satuan"
:value="$barang->satuan ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-input
type="number"
name="stok"
caption="Stok"
:value="$barang->stok ?? 0"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-input
type="number"
name="harga_satuan"
caption="Harga Satuan"
:value="$barang->harga_satuan ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-input
type="number"
name="min_stok"
caption="Minimal Stok"
:value="$barang->min_stok ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-select
name="jenis_barang"
caption="Jenis Barang"
:options="$jenis_barang"
:value="$barang->jenis_barang ?? ''"
/>
</div>

<div class="col-md-6">
<x-io-select
name="cabang_id"
caption="Cabang"
:options="$cabang_options"
:value="$barang->cabang_id ?? ''"
/>
</div>

</div>
</div>

<div class="modal-footer">

<button type="button"
class="btn btn-secondary me-6"
onclick="init()">
Batal
</button>

<button type="submit"
class="btn btn-primary">
Simpan
</button>

</div>

</form>

<script>
init_form_element();
init_form(@json($barang->id ?? ''));
</script>