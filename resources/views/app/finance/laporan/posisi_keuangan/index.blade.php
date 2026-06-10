@extends('layouts.index')

@section('title')
   Inventory - Posisi Keuangan
@endsection

@section('content')
<div class="content flex-column-fluid p-4" id="kt_content">

    {{-- =================== HEADER =================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="text-dark fw-bold fs-2">Posisi Keuangan</h1>
            @include('layouts._breadcrumb')
        </div>
       
    </div>

    {{-- =================== FILTER FORM =================== --}}
    <div class="bg-light border rounded-xl p-4 mb-6">
        <form id="form_search" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="tgl_awal" class="form-control" value="{{ request('tgl_awal') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tgl_akhir" class="form-control" value="{{ request('tgl_akhir') }}">
            </div>
            <div class="col-md-6 text-end">
                <button type="submit" class="btn btn-success fw-bold">
                    <i class="fa fa-search me-2"></i>Cari
                </button>
            </div>
        </form>
    </div>

    {{-- =================== SUMMARY CARD =================== --}}
    <div class="row mb-6 g-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white rounded-xl shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold text-uppercase fs-7">Total Debit</div>
                        <div class="fw-bold fs-4" id="total_debit">Rp 0</div>
                    </div>
                    <i class="fa fa-arrow-down fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white rounded-xl shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold text-uppercase fs-7">Total Kredit</div>
                        <div class="fw-bold fs-4" id="total_kredit">Rp 0</div>
                    </div>
                    <i class="fa fa-arrow-up fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white rounded-xl shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold text-uppercase fs-7">Saldo</div>
                        <div class="fw-bold fs-4" id="saldo">Rp 0</div>
                    </div>
                    <i class="fa fa-wallet fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- =================== DETAIL TABLE =================== --}}
    <div class="bg-white border rounded-xl shadow-sm p-4">
        <div class="d-flex justify-between mb-3">
            <h3 class="fw-bold fs-5">Detail Transaksi</h3>
            <div class="d-flex gap-2">
                <button id="btnPdf" class="btn btn-success btn-sm">Export PDF</button>
                <button id="btnExcel" class="btn btn-primary btn-sm">Export Excel</button>
            </div>
        </div>

        <div id="table" class="table-responsive max-h-[60vh] overflow-y-auto"></div>
    </div>

</div>
@endsection

@push('modals')
<div class="modal fade modal-slide-right" tabindex="-1" id="modal_info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modal_info_item"></div>
    </div>
</div>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
let $form_search = $('#form_search'),
    $table = $('#table'),
    $modal_info = $('#modal_info'),
    $modal_info_item = $('#modal_info_item');

let selected_page = 1,
    _token = '{{ csrf_token() }}',
    base_url = '{{ route('finance.laporan.posisi_keuangan.index') }}';

let params_url = new URLSearchParams({
    tgl_awal: '{{ $tgl_awal }}',
    tgl_akhir: '{{ $tgl_akhir }}'
}).toString();

// ====================== INIT ======================
let init = () => {
    $modal_info_item.html('');
    try { $modal_info.modal('hide'); } catch (e) {}
    search_data(selected_page);
}

// ====================== SEARCH DATA ======================
let search_data = (page = 1) => {
    let data = get_form_data($form_search);
    data.paginate = 10;
    data.page = selected_page = get_selected_page(page, selected_page);

    $.post(base_url + '/search-json?' + params_url, data, (res) => {
        if (res.status) {
            render_table(res.data);
            render_summary(res.data);
            update_periode_label(data.tgl_awal, data.tgl_akhir);
        }
    }).fail((xhr) => $table.html(xhr.responseText));
}

// ====================== UPDATE PERIODE LABEL ======================
let update_periode_label = (awal, akhir) => {
    $('#periodeLabel').remove();
    $table.before(`<p class="text-center mt-2" id="periodeLabel">Periode: ${awal} - ${akhir}</p>`);
}

// ====================== RENDER TABLE ======================
let render_table = (rows) => {
    let html = `
        <table class="table table-striped table-hover table-bordered rounded-3">
            <thead class="table-light">
                <tr class="fw-bold text-uppercase text-dark">
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Nama Akun</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Kredit</th>
                </tr>
            </thead>
            <tbody>
    `;

    rows.forEach(r => {
        html += `
            <tr>
                <td>${r.tgl ?? '-'}</td>
                <td>${r.kode ?? '-'}</td>
                <td>${r.nama_akun ?? '-'}</td>
                <td class="text-end">${format_rp(r.nominal_debit)}</td>
                <td class="text-end">${format_rp(r.nominal_kredit)}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    $table.html(html);
}

// ====================== RENDER SUMMARY ======================
let total_debit_val = 0, total_kredit_val = 0, saldo_val = 0;
let render_summary = (rows) => {
    total_debit_val = 0; total_kredit_val = 0;
    rows.forEach(r => {
        total_debit_val += Number(r.nominal_debit ?? 0);
        total_kredit_val += Number(r.nominal_kredit ?? 0);
    });
    saldo_val = total_debit_val - total_kredit_val;

    $('#total_debit').text(format_rp(total_debit_val));
    $('#total_kredit').text(format_rp(total_kredit_val));
    $('#saldo').text(format_rp(saldo_val));
}

// ====================== FORMAT RUPIAH ======================
let format_rp = (v) => {
    v = v ?? 0;
    return new Intl.NumberFormat('id-ID').format(v);
}

// ====================== FORM SUBMIT ======================
$form_search.submit((e) => {
    e.preventDefault();
    params_url = new URLSearchParams(get_form_data($form_search)).toString();
    search_data();
});

// ====================== EXPORT PDF ======================
$('#btnPdf').click(() => {
    const doc = new window.jspdf.jsPDF("p", "pt", "a4");

    // HEADER
    doc.setFontSize(14);
    doc.setFont("helvetica", "bold");
    doc.text("PALANG MERAH INDONESIA KOTA SURABAYA", doc.internal.pageSize.getWidth()/2, 40, { align: "center" });
    doc.setFontSize(12);
    doc.setFont("helvetica", "normal");
    const periode = $('#periodeLabel').text() || "";
    doc.text("LAPORAN POSISI KEUANGAN", doc.internal.pageSize.getWidth()/2, 60, { align: "center" });
    doc.text(periode, doc.internal.pageSize.getWidth()/2, 75, { align: "center" });

    // SUMMARY TOTAL
    doc.setFont("helvetica", "bold");
    doc.text(`Total Debit: ${format_rp(total_debit_val)}`, 40, 95);
    doc.text(`Total Kredit: ${format_rp(total_kredit_val)}`, 200, 95);
    doc.text(`Saldo: ${format_rp(saldo_val)}`, 360, 95);

    // TABLE START AFTER SUMMARY
    doc.autoTable({
        html: '#table table',
        startY: 115,
        theme: 'grid',
        headStyles: { fillColor: [240,240,240] }
    });

    doc.save("laporan-posisi-keuangan.pdf");
});

// ====================== EXPORT EXCEL ======================
$('#btnExcel').click(() => {
    const tableEl = document.querySelector("#table table");
    if (!tableEl) return alert("Tidak ada data untuk di-export!");
    const ws = XLSX.utils.table_to_sheet(tableEl);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Laporan");

    // Tambahkan total di bawah tabel
    XLSX.utils.sheet_add_aoa(ws, [["", "", "Total Debit", total_debit_val]], { origin: -1 });
    XLSX.utils.sheet_add_aoa(ws, [["", "", "Total Kredit", total_kredit_val]], { origin: -1 });
    XLSX.utils.sheet_add_aoa(ws, [["", "", "Saldo", saldo_val]], { origin: -1 });

    XLSX.writeFile(wb, "laporan-posisi-keuangan.xlsx");
});

// ====================== INIT ======================
init_form_element();
init();
</script>
@endpush