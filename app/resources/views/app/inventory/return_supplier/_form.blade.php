<form id="form_info">
@csrf 
@if(!empty($return_supplier))
    <input type="hidden" name="id" value="{{ $return_supplier->id }}">
    <input type="hidden" name="_method" value="PUT">
@endif
<div class="modal-header">
    <h3 class="modal-title">
        {{ !empty($return_supplier) ? 'Ubah' : 'Tambah' }} Retur Supplier
    </h3>

    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
         data-bs-dismiss="modal">
        <i class="ki-duotone ki-cross fs-1"></i>
    </div>
</div>

<div class="modal-body">
<div class="row g-4">

{{-- NO RETUR --}}
<div class="col-md-6">
<label>No Retur</label>
<input type="text"
       name="no_trans_retur"
       class="form-control"
       value="{{ $return_supplier->no_trans_retur ?? 'RET-' . date('YmdHis') }}"
       readonly>
</div>


{{-- TANGGAL --}}
<div class="col-md-6">
<x-io-input
type="date"
name="tgl_retur"
caption="Tanggal Retur"
:value="$return_supplier->tgl_retur ?? date('Y-m-d')"
:viewtype="2"
required
/>
</div>

{{-- SUPPLIER --}}
<div class="col-md-6">
<x-io-select
name="supplier_id"
caption="Supplier"
:options="$supplier_options ?? []"
:value="$return_supplier->supplier_id ?? ''"
required
/>
</div>

{{-- JENIS RETUR --}}
<div class="col-md-6">
<x-io-select
name="jenis_retur"
caption="Jenis Retur"
:options="[
'rusak' => 'Barang Rusak',
'expired' => 'Expired',
'lebih' => 'Kelebihan'
]"
:value="$return_supplier->jenis_retur ?? ''"
required
/>
</div>

</div>
<div class="col-md-6">
<x-io-input
name="satuan"
caption="Satuan"
:value="$return_supplier->satuan ?? ''"
:viewtype="2"
required
/>
</div>
<hr>

<h5>Detail Barang</h5>

<div class="table-responsive">
<table class="table table-sm table-bordered" id="table_detail">
<thead class="bg-light">
<tr>
    <th>Barang</th>
    <th width="120">Qty</th>
    <th width="150">Harga</th>
    <th width="150">Subtotal</th>
    <th width="50">#</th>
</tr>
</thead>
  <tbody>
@if(!empty($details))
    @foreach($details as $d)
    <tr>
        <td>
            <select name="barang_id[]" class="form-control barang">
                @foreach($barang_options as $id => $nama)
                    <option value="{{ $id }}" 
                        {{ $id == $d->barang_id ? 'selected' : '' }}>
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="qty_retur[]" class="form-control qty" value="{{ $d->qty_retur }}"></td>
        <td><input type="number" name="harga_retur[]" class="form-control harga" value="{{ $d->harga_retur }}"></td>
        <td><input type="number" name="subtotal_retur[]" class="form-control subtotal" value="{{ $d->subtotal_retur }}" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
    </tr>
    @endforeach
@endif
</tbody>
</table>
<div class="mt-2">
    <b>Total: </b> <span id="grand_total">0</span>
</div>
</div>

<button type="button" class="btn btn-sm btn-primary mt-2" onclick="addRow()">
+ Tambah Barang
</button>

</div>

<div class="modal-footer">
<button type="button"
class="btn btn-light me-3"
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
$('#form_info').off('submit').on('submit', function(e){
    e.preventDefault();

    let id = $('input[name="id"]').val();
    let url = '';
    let method = 'POST';

    if(id){
        // ✅ UPDATE
        url = "{{ url('inventory/return_supplier') }}/" + id;
        method = 'POST'; // tetap POST karena pakai _method PUT
    } else {
        // ✅ CREATE
        url = "{{ url('inventory/return_supplier') }}";
    }

    $.ajax({
        url: url,
        type: method,
        data: $(this).serialize(),
        success: function(res){
            if(res.status){
                alert(res.message);
                init(); // reload table
            } else {
                alert(res.message);
            }
        }
    });
});

    $(document).ready(function(){
    let input = $('input[name="no_trans_retur"]');

  @if(empty($return_supplier))
    if(!input.val()){
        input.val('RET-' + new Date().getTime());
    }
  @endif
});
 function addRow() {
    let html = `
    <tr>
        <td>
            <select name="barang_id[]" class="form-control barang">
                @foreach($barang_options as $id => $nama)
                    @php $barang = \App\Models\Barang::find($id); @endphp
                    <option value="{{ $id }}" data-harga="{{ $barang->harga }}">
                        {{ $nama }}
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="qty_retur[]" class="form-control qty" value="1"></td>
        <td><input type="number" name="harga_retur[]" class="form-control harga" value="0"></td>
        <td><input type="number" name="subtotal_retur[]" class="form-control subtotal" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
    </tr>
    `;

    $('#table_detail tbody').append(html);
}
function removeRow(btn) {
    $(btn).closest('tr').remove();
}
// AUTO ISI HARGA SAAT PILIH BARANG
$(document).on('change', '.barang', function () {
    let row = $(this).closest('tr');
    let harga = $(this).find(':selected').data('harga') || 0;

    row.find('.harga').val(harga).trigger('keyup');
});

$(document).on('keyup change', '.qty, .harga', function () {
    let row = $(this).closest('tr');

    let qty   = parseFloat(row.find('.qty').val()) || 0;
    let harga = parseFloat(row.find('.harga').val()) || 0;

    let subtotal = qty * harga;

    row.find('.subtotal').val(subtotal);

    hitungTotal();
});
function hitungTotal() {
    let total = 0;

    $('.subtotal').each(function () {
        total += parseFloat($(this).val()) || 0;
    });

    $('#grand_total').text(total);
}

// LOAD BARANG DARI PO
$('select[name="po_id"]').change(function () {

    let po_id = $(this).val();

    $('#table_detail tbody').html('');

    $.get("{{ url('inventory/get-po-detail') }}/" + po_id, function(res){

        res.forEach(item => {
            addRow({
                qty: item.qty,
                harga: item.harga
            });
        });

    });

});
 init_form_element();
init_form(@json($return_supplier->id ?? ''));

// 🔥 FORCE SET NO RETUR (ANTI KE-RESET)
setTimeout(function(){
    let input = $('input[name="no_trans_retur"]');

    if(!input.val()){
        input.val('RET-' + new Date().getTime());
    }
}, 200);
</script>