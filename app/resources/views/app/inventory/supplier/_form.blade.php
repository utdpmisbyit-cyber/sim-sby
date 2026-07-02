<form id="form_info">
@csrf 

<div class="modal-header">
<h3 class="modal-title">
{{ !empty($supplier) ? 'Ubah' : 'Tambah' }} Supplier
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
caption="Kode Supplier"
:value="$supplier->kode ?? ''"
:viewtype="2"
required
/>
</div>

<div class="col-md-6">
<x-io-input
name="nama"
caption="Nama Supplier"
:value="$supplier->nama ?? ''"
:viewtype="2"
required
/>
</div>

<div class="col-md-12">
<x-io-input
name="alamat"
caption="Alamat"
:value="$supplier->alamat ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-input
name="no_telp"
caption="No Telepon"
:value="$supplier->no_telp ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-select
name="status"
caption="Status"
:options="[
    '1' => 'Aktif',
    '0' => 'Tidak Aktif'
]"
:value="$supplier->status ?? '1'"
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
init_form(@json($supplier->id ?? ''));
</script>