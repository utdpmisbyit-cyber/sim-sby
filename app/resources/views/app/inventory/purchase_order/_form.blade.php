<form id="form_info">
    @csrf

    <div class="modal-header">
        <h3 class="modal-title">
            {{ !empty($purchase_order) ? 'Ubah' : 'Tambah' }} Purchase Order
        </h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
            <i class="ki-duotone ki-cross fs-1"></i>
        </div>
    </div>
        @if(isset($purchase_order))
        <input type="hidden" name="id" value="{{ $purchase_order->id }}">
    @endif
    <div class="modal-body">

        {{-- ===================== DETAIL UTAMA ===================== --}}
        <div class="card card-flush border border-gray-200 rounded-3 mb-5">
            <div class="card-header min-h-50px">
                <h4 class="card-title fw-bold text-dark fs-6">Detail Utama</h4>
            </div>
            <div class="card-body pt-3">
                <div class="row g-4">

                    {{-- Nomor PO --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-7 text-gray-700">Nomor PO</label>
                        <input
                            type="text"
                            name="no_po"
                            class="form-control form-control-sm bg-light"
                            value="{{ $purchase_order->no_po ?? 'PO-'.date('Y').str_pad(rand(1,9999999), 7, '0', STR_PAD_LEFT) }}"
                            readonly
                        />
                    </div>

                    {{-- Tanggal PO --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-7 text-gray-700">Tanggal PO</label>
                        <input
                            type="date"
                            name="tgl_po"
                            class="form-control form-control-sm"
                            value="{{ isset($purchase_order) ? \Carbon\Carbon::parse($purchase_order->tgl_po)->format('Y-m-d') : date('Y-m-d') }}"
                            required
                        />
                    </div>

                    {{-- Supplier --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-7 text-gray-700">Supplier</label>
                        <select name="supplier_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($supplier_options ?? [] as $id => $nama)
                                <option value="{{ $id }}" {{ (isset($purchase_order) && $purchase_order->supplier_id == $id) ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status PO --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-7 text-gray-700">Status PO</label>
                        <select name="status_po" class="form-select form-select-sm">
                            <option value="0" {{ (!isset($purchase_order) || $purchase_order->status_po == 0) ? 'selected' : '' }}>Draft</option>
                            <option value="1" {{ (isset($purchase_order) && $purchase_order->status_po == 1) ? 'selected' : '' }}>Proses</option>
                            <option value="2" {{ (isset($purchase_order) && $purchase_order->status_po == 2) ? 'selected' : '' }}>Selesai</option>
                            <option value="3" {{ (isset($purchase_order) && $purchase_order->status_po == 3) ? 'selected' : '' }}>Batal</option>
                        </select>
                    </div>

                    {{-- Total PO (readonly, dihitung otomatis) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold fs-7 text-gray-700">Total PO</label>
                        <input
                            type="text"
                            id="display_total_po"
                            class="form-control form-control-sm bg-light"
                            value="{{ isset($purchase_order) ? 'Rp '.number_format($purchase_order->total_po, 0, ',', '.') : 'Rp 0' }}"
                            readonly
                        />
                        <input type="hidden" name="total_po" id="total_po" value="{{ $purchase_order->total_po ?? 0 }}" />
                    </div>

                </div>
            </div>
        </div>

        {{-- ===================== DETAIL BARANG ===================== --}}
        <div class="card card-flush border border-gray-200 rounded-3">
            <div class="card-header min-h-50px">
                <h4 class="card-title fw-bold text-dark fs-6">Detail Barang</h4>
            </div>
            <div class="card-body pt-3">

                {{-- Input row tambah barang --}}
                <div class="border border-dashed border-primary rounded-3 p-4 mb-4 bg-light-primary">
                    <div class="row g-3 align-items-end">

                        {{-- Dropdown: value=id, data-kode, data-nama, data-harga --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold fs-7 text-gray-700">Kode Barang</label>
                            <select id="input_barang_id" class="form-select form-select-sm">
                                <option value="">Pilih Barang</option>
                                @foreach($barang_list ?? [] as $b)
                                    <option
                                        value="{{ $b->id }}"
                                        data-kode="{{ $b->kode }}"
                                        data-nama="{{ $b->nama }}"
                                        data-harga="{{ $b->harga_satuan ?? 0 }}"
                                    >{{ $b->kode }} — {{ $b->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-gray-700">Jumlah Order</label>
                            <input type="number" id="input_qty" class="form-control form-control-sm" min="1" placeholder="0" />
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold fs-7 text-gray-700">Harga</label>
                            <input type="number" id="input_harga" class="form-control form-control-sm" min="0" placeholder="0" />
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm w-100 fw-bold" onclick="tambah_barang()">
                                Tambah Barang
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tabel detail barang --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle fs-7">
                        <thead>
                            <tr class="fw-bold text-dark fs-7 text-uppercase">
                                <th width="40">#</th>
                                <th width="110">Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="70" class="text-center">Qty</th>
                                <th width="130">Harga</th>
                                <th width="130">Subtotal</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detail">

                           @php
                                $details = $purchase_order->details ?? collect();
                            @endphp

                            @if($details->count() > 0)
                                @foreach($details as $i => $det)
                                <tr data-index="{{ $i }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-bold">
                                        {{ $det->barang->kode ?? '-' }}
                                        <input type="hidden" name="details[{{ $i }}][barang_id]"   value="{{ $det->barang_id }}" />
                                        <input type="hidden" name="details[{{ $i }}][kode]"        value="{{ $det->barang->kode ?? '' }}" />
                                        <input type="hidden" name="details[{{ $i }}][nama_barang]" value="{{ $det->barang->nama ?? '' }}" />
                                    </td>
                                    <td>{{ $det->barang->nama ?? '-' }}</td>
                                    <td class="text-center">
                                        {{ $det->qty_po }}
                                        <input type="hidden" name="details[{{ $i }}][qty_po]"      value="{{ $det->qty_po }}" />
                                    </td>
                                    <td>
                                        Rp {{ number_format($det->harga_po, 0, ',', '.') }}
                                        <input type="hidden" name="details[{{ $i }}][harga_po]"    value="{{ $det->harga_po }}" />
                                    </td>
                                    <td>
                                        Rp {{ number_format($det->subtotal_po, 0, ',', '.') }}
                                        <input type="hidden" name="details[{{ $i }}][subtotal_po]" value="{{ $det->subtotal_po }}" />
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <a href="javascript:void(0)" class="text-primary me-2" onclick="edit_barang(this)">Edit</a>
                                        <a href="javascript:void(0)" class="text-danger" onclick="hapus_barang(this)">Hapus</a>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr id="row_empty">
                                    <td colspan="7" class="text-center text-muted py-4">Belum ada barang ditambahkan</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>{{-- end modal-body --}}

    <div class="modal-footer">
        <button type="button" class="btn btn-light me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="ki-duotone ki-check fs-2"></i> Simpan
        </button>
    </div>

</form>

<script>
   let detail_index = {{ isset($purchase_order) ? ($purchase_order->details->count() ?? 0) : 0 }};
    const fmt_rp = (val) => 'Rp ' + Number(val).toLocaleString('id-ID');

    // Hitung ulang total PO dari semua baris
    const hitung_total = () => {
        let total = 0;
        $('#tbody_detail tr[data-index]').each(function () {
            total += parseFloat($(this).find('input[name$="[subtotal_po]"]').val()) || 0;
        });
        $('#total_po').val(total);
        $('#display_total_po').val(fmt_rp(total));
    };

    // Nomor urut baris
    const renumber_rows = () => {
        $('#tbody_detail tr[data-index]').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    };

    // Isi harga otomatis saat pilih barang
    $('#input_barang_id').on('change', function () {
        let harga = $(this).find('option:selected').data('harga') || 0;
        $('#input_harga').val(harga > 0 ? harga : '');
    });

    // Tambah baris ke tabel
    const tambah_barang = () => {
        let $sel      = $('#input_barang_id');
        let barang_id = $sel.val();
        let kode      = $sel.find('option:selected').data('kode') || '';
        let nama      = $sel.find('option:selected').data('nama') || '';
        let qty       = parseFloat($('#input_qty').val()) || 0;
        let harga     = parseFloat($('#input_harga').val()) || 0;

        if (!barang_id) { alert('Pilih barang terlebih dahulu.'); return; }
        if (qty <= 0)   { alert('Jumlah order harus lebih dari 0.'); return; }

        let subtotal = qty * harga;
        let idx      = detail_index++;

        $('#row_empty').remove();

        let row = `
        <tr data-index="${idx}">
            <td></td>
            <td class="fw-bold">
                ${kode}
                <input type="hidden" name="details[${idx}][barang_id]"   value="${barang_id}" />
                <input type="hidden" name="details[${idx}][kode]"        value="${kode}" />
                <input type="hidden" name="details[${idx}][nama_barang]" value="${nama}" />
            </td>
            <td>${nama}</td>
            <td class="text-center">
                ${qty}
                <input type="hidden" name="details[${idx}][qty_po]"      value="${qty}" />
            </td>
            <td>
                ${fmt_rp(harga)}
                <input type="hidden" name="details[${idx}][harga_po]"    value="${harga}" />
            </td>
            <td>
                ${fmt_rp(subtotal)}
                <input type="hidden" name="details[${idx}][subtotal_po]" value="${subtotal}" />
            </td>
            <td class="text-center text-nowrap">
                <a href="javascript:void(0)" class="text-primary me-2" onclick="edit_barang(this)">Edit</a>
                <a href="javascript:void(0)" class="text-danger" onclick="hapus_barang(this)">Hapus</a>
            </td>
        </tr>`;

        $('#tbody_detail').append(row);
        renumber_rows();
        hitung_total();

        // Reset input
        $sel.val('');
        $('#input_qty').val('');
        $('#input_harga').val('');
    };

    // Edit: kembalikan data ke input row
    const edit_barang = (el) => {
        let $row      = $(el).closest('tr');
        let barang_id = $row.find('input[name$="[barang_id]"]').val();
        let qty       = $row.find('input[name$="[qty_po]"]').val();
        let harga     = $row.find('input[name$="[harga_po]"]').val();

        $('#input_barang_id').val(barang_id);
        $('#input_qty').val(qty);
        $('#input_harga').val(harga);

        $row.remove();
        renumber_rows();
        hitung_total();

        if ($('#tbody_detail tr[data-index]').length === 0) {
            $('#tbody_detail').append('<tr id="row_empty"><td colspan="7" class="text-center text-muted py-4">Belum ada barang ditambahkan</td></tr>');
        }
    };

    // Hapus baris
    const hapus_barang = (el) => {
        $(el).closest('tr').remove();
        renumber_rows();
        hitung_total();
        if ($('#tbody_detail tr[data-index]').length === 0) {
            $('#tbody_detail').append('<tr id="row_empty"><td colspan="7" class="text-center text-muted py-4">Belum ada barang ditambahkan</td></tr>');
        }
    };

    init_form_element();
    init_form(@json($purchase_order->id ?? ''));
</script>