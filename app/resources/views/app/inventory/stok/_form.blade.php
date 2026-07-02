<form id="form_info">
@csrf 

<div class="modal-header">
<h3 class="modal-title">
{{ isset($stok) ? 'Ubah' : 'Tambah' }} Stok
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
name="tgl_proses"
type="date"
caption="Tanggal Proses"
:value="$stok->tgl_proses ?? date('Y-m-d')"
:viewtype="2"
required
/>
</div>

<div class="col-md-6">
<x-io-select
name="proses"
caption="Jenis Proses"
:options="[
1 => 'Stok Masuk',
0 => 'Stok Keluar'
]"
:value="$stok->proses ?? 1"
/>
</div>

<div class="col-md-6">
<x-io-select
name="barang_id"
caption="Barang"
:options="$barang"
:value="$stok->barang_id ?? ''"
required
/>
</div>

<div class="col-md-6">
<x-io-input
type="number"
name="qty_in"
caption="Qty Masuk"
:value="$stok->qty_in ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-input
type="number"
name="qty_out"
caption="Qty Keluar"
:value="$stok->qty_out ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-input
type="number"
name="harga"
caption="Harga"
:value="$stok->harga ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-12">
<x-io-input
name="keterangan"
caption="Keterangan"
:value="$stok->keterangan ?? ''"
:viewtype="2"
/>
</div>

<div class="col-md-6">
<x-io-select
name="aktif"
caption="Status"
:options="[
'1' => 'Aktif',
'0' => 'Tidak Aktif'
]"
:value="$stok->aktif ?? '1'"
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
init_form(@json($stok->no_trans_stok ?? ''));

// 🔥 tambahan biar UX bagus
$(document).on('change', '[name="proses"]', function() {
    let val = $(this).val();

    if (val == 1) {
        $('[name="qty_in"]').prop('readonly', false);
        $('[name="qty_out"]').prop('readonly', true).val('');
    } else {
        $('[name="qty_in"]').prop('readonly', true).val('');
        $('[name="qty_out"]').prop('readonly', false);
    }
}).trigger('change');
</script>
<script id="data-barang">
let dataBarang = @json(\App\Models\Barang::select('id','stok','min_stok')->get()->keyBy('id'));
</script>