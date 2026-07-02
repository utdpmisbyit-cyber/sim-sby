<form id="form_info" enctype="multipart/form-data">
@csrf

<div class="modal-header">
    <h3 class="modal-title">
        {{ isset($pembelian_qc_masuk) ? 'Ubah' : 'Tambah' }} QC Barang
    </h3>
    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
        <i class="ki-duotone ki-cross fs-1"></i>
    </div>
</div>

<div class="modal-body">
    <div class="row g-4">

        {{-- Row 1 --}}
        <div class="col-md-3">
            <x-io-input name="no_trans_qc" caption="No Trans QC (Auto)"
                :value="$pembelian_qc_masuk->no_trans_qc ?? $next_no"
                :viewtype="2" readonly />
        </div>
        <div class="col-md-3">
            <x-io-input name="tgl_qc" type="date" caption="Tgl QC"
                :value="$pembelian_qc_masuk->tgl_qc ?? date('Y-m-d')"
                :viewtype="2" required />
        </div>
        <div class="col-md-3">
            <x-io-input name="tgl_beli" type="date" caption="Tgl Beli"
                :value="$pembelian_qc_masuk->tgl_beli ?? date('Y-m-d')"
                :viewtype="2" required />
        </div>
        <div class="col-md-3">
            <x-io-select name="status_qc" caption="Status"
                :options="['0'=>'⏳ PENDING','1'=>'✅ Approve','2'=>'❌ Reject']"
                :value="$pembelian_qc_masuk->status_qc ?? 'pending'" />
        </div>

        {{-- Row 2 --}}
        <div class="col-md-3">
            <x-io-input name="no_faktur" caption="No Faktur"
                :value="$pembelian_qc_masuk->no_faktur ?? ''"
                :viewtype="2" required />
        </div>
        <div class="col-md-3">
            <x-io-select name="purchase_order_id" caption="No PO" :options="$purchase_orders ?? []" :value="$pembelian_qc_masuk->purchase_order_id ?? ''" :viewtype="2" required />
        </div>
        <div class="col-md-3">
            <x-io-input name="supplier_id" caption="Kode Supplier (Auto)"
                :value="$pembelian_qc_masuk->supplier_id ?? ''"
                :viewtype="2" readonly />
        </div>
        <div class="col-md-3">
            <x-io-input name="user_proses" caption="User Proses"
                :value="$pembelian_qc_masuk->user_proses ?? auth()->user()->name ?? ''"
                :viewtype="2" readonly />
        </div>

    </div>

    {{-- Detail Lot Barang --}}
    <div class="d-flex justify-content-between align-items-center mt-6 mb-3">
        <h5 class="fw-bold mb-0">Detail Lot Barang</h5>
        <button type="button" class="btn btn-sm btn-success" onclick="tambah_baris()">
            + Tambah Baris
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle fs-7 table-sm" id="table_detail">
            <thead>
                <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase">
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>No Lot</th>
                    <th>Jenis Barang</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Exp Date</th>
                    <th>Suhu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody_detail">
                @if(isset($pembelian_qc_masuk) && $pembelian_qc_masuk->qcDetailLot->count())
                    @foreach($pembelian_qc_masuk->qcDetailLot as $i => $lot)
                        <tr>
                            <td><input type="text" name="detail[{{ $i }}][barang_id]" class="form-control form-control-sm" value="{{ $lot->barang_id }}" onchange="fetch_barang(this, {{ $i }})"></td>
                            <td><input type="text" name="detail[{{ $i }}][nama_barang]" class="form-control form-control-sm" value="{{ $lot->barang->nama ?? '-' }}" readonly></td>
                            <td><input type="text" name="detail[{{ $i }}][no_lot]" class="form-control form-control-sm" value="{{ $lot->no_lot }}"></td>
                            <td><input type="text" name="detail[{{ $i }}][jenis_barang]" class="form-control form-control-sm" value="{{ $lot->jenis_barang }}"></td>
                            <td><input type="number" name="detail[{{ $i }}][qty_terima]" class="form-control form-control-sm qty" value="{{ $lot->qty_terima }}" onchange="hitung_subtotal(this)"></td>
                            <td><input type="number" name="detail[{{ $i }}][harga]" class="form-control form-control-sm harga" value="{{ $lot->harga }}" onchange="hitung_subtotal(this)"></td>
                            <td><input type="text" name="detail[{{ $i }}][subtotal_harga]" class="form-control form-control-sm subtotal" value="{{ $lot->subtotal_harga }}" readonly></td>
                            <td><input type="date" name="detail[{{ $i }}][tgl_exp_date]" class="form-control form-control-sm" value="{{ $lot->tgl_exp_date }}"></td>
                            <td><input type="number" name="detail[{{ $i }}][suhu]" class="form-control form-control-sm" value="{{ $lot->suhu }}"></td>
                            <td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_baris(this)"><i class="fa fa-trash"></i></button></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary me-2" onclick="init()">Batal</button>
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>

</form>

<script>
    // ===============================
    // GLOBAL STATE
    // ===============================
    window.row_index = window.row_index ?? 0;

    row_index = {{ isset($pembelian_qc_masuk) ? $pembelian_qc_masuk->qcDetailLot->count() : 0 }};

    init_form_element();
    init_form(@json($pembelian_qc_masuk->id ?? ''));

    function reset_table() {
        row_index = 0;
        $('#tbody_detail').empty();
    }

    // ===============================
    // LOAD PO
    // ===============================
  // ===============================
// LOAD PO (FIX SELECT2 + AUTO LOAD)
// ===============================
$(document)
    .off('change', '#purchase_order_id')
    .on('change', '#purchase_order_id', function () {

        // console.log("CHANGE OK");
        let isEdit = {{ isset($pembelian_qc_masuk) ? 'true' : 'false' }};
        if (isEdit) return;
        let po_id = $(this).val();
        if (!po_id) return;

        $.ajax({
            url: "/inventory/purchase_order/" + po_id + "/show_json",
            type: "GET",
            success: function(data) {

                // console.log("DATA MASUK:", data);

                $('[name=supplier_id]').val(data.supplier_id ?? '');

                reset_table();

                let details = data.detail ?? [];

                if (details.length === 0) return;

                details.forEach(d => {
                    let i = row_index++;

                    let qty = parseFloat(d.qty_po) || 0;
                    let harga = parseFloat(d.harga_po) || 0;

                    let nama_barang = d.barang?.nama_barang ?? '-';
                    let jenis_barang = d.barang?.jenis_barang ?? '-';

                    let row = `
                    <tr>
                        <td><input type="text" name="detail[${i}][barang_id]" value="${d.barang_id}" class="form-control form-control-sm"></td>
                        <td><input type="text" name="detail[${i}][nama_barang]" value="${nama_barang}" class="form-control form-control-sm" readonly></td>
                        <td><input type="text" name="detail[${i}][no_lot]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="detail[${i}][jenis_barang]" value="${jenis_barang}" class="form-control form-control-sm"></td>
                        <td><input type="number" name="detail[${i}][qty_terima]" value="${qty}" class="form-control form-control-sm qty" onchange="hitung_subtotal(this)"></td>
                        <td><input type="number" name="detail[${i}][harga]" value="${harga}" class="form-control form-control-sm harga" onchange="hitung_subtotal(this)"></td>
                        <td><input type="text" name="detail[${i}][subtotal_harga]" value="${qty * harga}" class="form-control form-control-sm subtotal" readonly></td>
                        <td><input type="date" name="detail[${i}][tgl_exp_date]" class="form-control form-control-sm"></td>
                        <td><input type="number" name="detail[${i}][suhu]" value="0" class="form-control form-control-sm"></td>
                        <td><button type="button" onclick="hapus_baris(this)" class="btn btn-danger btn-sm">X</button></td>
                    </tr>`;

                    $('#tbody_detail').append(row);
                });
            },
            error: function(err) {
                console.error("ERROR AJAX:", err);
            }
        });
    });


// ===============================
// AUTO LOAD SAAT FORM DIBUKA
// ===============================
$(document).ready(function () {
    setTimeout(() => {
        $('#purchase_order_id').trigger('change');
    }, 300); // delay biar select2 ready
});


// ===============================
// TAMBAH BARIS
// ===============================
function tambah_baris() {
    let i = row_index++;

    let row = `
    <tr>
        <td><input type="text" name="detail[${i}][barang_id]" class="form-control form-control-sm" onchange="fetch_barang(this, ${i})"></td>
        <td><input type="text" name="detail[${i}][nama]" class="form-control form-control-sm" readonly></td>
        <td><input type="text" name="detail[${i}][no_lot]" class="form-control form-control-sm"></td>
        <td><input type="text" name="detail[${i}][jenis_barang]" class="form-control form-control-sm"></td>
        <td><input type="number" name="detail[${i}][qty_terima]" class="form-control form-control-sm qty" value="0" onchange="hitung_subtotal(this)"></td>
        <td><input type="number" name="detail[${i}][harga]" class="form-control form-control-sm harga" value="0" onchange="hitung_subtotal(this)"></td>
        <td><input type="text" name="detail[${i}][subtotal_harga]" class="form-control form-control-sm subtotal" value="0" readonly></td>
        <td><input type="date" name="detail[${i}][tgl_exp_date]" class="form-control form-control-sm"></td>
        <td><input type="number" name="detail[${i}][suhu]" class="form-control form-control-sm" value="0"></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="hapus_baris(this)">X</button></td>
    </tr>`;

    $('#tbody_detail').append(row);
}


// ===============================
// HAPUS BARIS
// ===============================
function hapus_baris(btn) {
    $(btn).closest('tr').remove();
}


// ===============================
// HITUNG SUBTOTAL
// ===============================
function hitung_subtotal(el) {
    let $tr = $(el).closest('tr');
    let qty = parseFloat($tr.find('.qty').val()) || 0;
    let harga = parseFloat($tr.find('.harga').val()) || 0;
    $tr.find('.subtotal').val(qty * harga);
}


// ===============================
// FETCH BARANG
// ===============================
function fetch_barang(el, i) {
    let kode = $(el).val();
    if (!kode) return;

    $.get('/inventory/barang/' + kode + '/show_json', function(data) {
        $(`[name="detail[${i}][nama_barang]"]`).val(data.nama ?? '-');
    });
}
</script>