@extends('layouts.index')

@section('title')
    Cetak Ulang Barcode
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
    --pmi-red:#C8102E;
    --pmi-dark:#f23928;
    --pmi-light:#f8fafc;
    --mono: 'IBM Plex Mono', monospace;
}

.pk-card{
    background:#fff;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
    overflow:hidden;
    margin-bottom:1rem;
}

.pk-card-header{
    background:var(--pmi-dark);
    color:#fff;
    padding:1rem;
    font-weight:600;
}

.pk-card-body{
    padding:1rem;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:1rem;
}

.fgroup label{
    font-size:.75rem;
    font-weight:700;
    display:block;
    margin-bottom:.3rem;
}

.fgroup input{
    width:100%;
    border:1px solid #ddd;
    border-radius:8px;
    padding:.6rem;
}

.btn-pmi{
    border:none;
    border-radius:8px;
    padding:.7rem 1rem;
    font-weight:600;
    cursor:pointer;
}

.btn-search{
    background:#06b6d4;
    color:white;
}

.btn-print{
    background:var(--pmi-red);
    color:#fff;
}

.table{
    width:100%;
    border-collapse:collapse;
}

.table th{
    background:#f1f5f9;
    padding:.8rem;
}

.table td{
    padding:.7rem;
    border-bottom:1px solid #eee;
}

/* ════════════════════════════════════════════
   PRINT STYLES — selaras dengan modul Pendataan Kantong
   Ukuran fisik label: 40mm x 20mm
   ════════════════════════════════════════════ */
@media print {
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        width: 40mm !important;
        height: auto !important;
        overflow: visible !important;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    body * { visibility: hidden !important; }
    #print-area, #print-area * { visibility: visible !important; }
    #print-area {
        /* JANGAN pakai position:fixed -> merusak pagination multi-halaman */
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 40mm !important;
        background: #fff !important;
        padding: 0 !important;
        margin: 0 !important;
        z-index: 99999 !important;
    }
    @page {
        size: 40mm 20mm;
        margin: 0 !important;
    }
}

#print-area { display: none; }

/* Pembungkus luar — BLOCK, pemegang page-break.
   Jangan ubah jadi flex/grid: Chrome mengabaikan page-break-after
   pada elemen yang sekaligus display:flex. */
.label-page {
    width: 40mm;
    height: 19mm;
    overflow: hidden;
    page-break-after: always;
    break-after: page;
    page-break-inside: avoid;
    break-inside: avoid;
}

/* Layout visual saja, TIDAK memegang page-break */
.label-wrap {
    width: 100%;
    height: 100%;
    padding: 1.2mm 1.5mm 0.8mm 1.5mm;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #fff;
    font-family: var(--mono);
}

.label-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-shrink: 0;
}

.label-title {
    font-size: 5.5pt;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-align: left;
    line-height: 1;
}

/* Ukuran ditampilkan DI ATAS type_kantong (stacked, rata kanan) */
.label-ukuran-type {
    text-align: right;
    line-height: 1.3;
}
.label-ukuran-type .ukuran {
    font-size: 5pt;
    font-weight: 700;
    color: #444;
    display: block;
}
.label-ukuran-type .type {
    font-size: 4pt;
    font-weight: 700;
    display: block;
}

.label-barcode {
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 0.5mm 0;
    height: 11mm;
}

.label-barcode svg {
    display: block;
    width: 37mm !important;
    height: 11mm !important;
}

.label-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 6pt;
    font-weight: 700;
    letter-spacing: 0.05em;
    margin-top: 0.3mm;
    line-height: 1;
    flex-shrink: 0;
}
.label-foot .left { text-align: left; }
.label-foot .right { text-align: right; }
</style>
@endpush

@section('content')

<div class="container-fluid py-4">

    <div class="pk-card">
        <div class="pk-card-header">
         Cetak Ulang Barcode
        </div>

        <div class="pk-card-body">

            <div class="form-grid">
                <div class="fgroup">
                    <label>Tanggal</label>
                    <input type="date" id="tanggal">
                </div>

                <div class="fgroup">
                    <label>No Lot</label>
                    <input type="text" id="no_lot">
                </div>

                <div class="fgroup">
                    <label>Barcode</label>
                    <input type="text" id="barcode">
                </div>
            </div>

            <div style="margin-top:1rem;display:flex;gap:.5rem;">
                <button class="btn-pmi btn-search" id="btn-search">
                    Cari
                </button>

                <button class="btn-pmi btn-print" id="btn-print">
                    Print Barcode
                </button>
            </div>

        </div>
    </div>

    <div class="pk-card">
        <div class="pk-card-header">
            Data Barcode
        </div>

        <div class="pk-card-body">

            <table class="table">
                <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="check-all">
                    </th>
                    <th>Barcode</th>
                    <th>Merk</th>
                    <th>Jenis</th>
                    <th>Type</th>
                    <th>Ukuran</th>
                    <th>No Lot</th>
                </tr>
                </thead>

                <tbody id="tbl-body"></tbody>
            </table>

        </div>
    </div>

</div>

{{-- Hidden print area --}}
<div id="print-area"></div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
const tbody = document.getElementById('tbl-body');

document.getElementById('btn-search')
.addEventListener('click', loadData);

async function loadData() {

    const params = new URLSearchParams({
        tanggal: document.getElementById('tanggal').value,
        no_lot: document.getElementById('no_lot').value,
        barcode: document.getElementById('barcode').value,
    });

    const res = await fetch(
        `{{ route('gudang.cetak_barcode.data') }}?${params}`
    );

    const rows = await res.json();

    tbody.innerHTML = rows.map(row => `
        <tr>
            <td>
                <input type="checkbox"
                    class="barcode-check"
                    data-barcode="${row.barcode}"
                    data-type="${row.type_kantong}"
                    data-ukuran="${row.ukuran ?? ''}"
                    data-merk="${row.merk_kantong ?? ''}"
                    data-jenis="${row.jenis_kantong ?? ''}"
                    data-no_lot="${row.no_lot ?? ''}">
            </td>
            <td>${row.barcode}</td>
            <td>${row.merk_kantong}</td>
            <td>${row.jenis_kantong}</td>
            <td>${row.type_kantong}</td>
            <td>${row.ukuran}</td>
            <td>${row.no_lot}</td>
        </tr>
    `).join('');
}

document.getElementById('check-all')
.addEventListener('change', function(){

    document.querySelectorAll('.barcode-check')
    .forEach(el => el.checked = this.checked);

});

/* ════════════════════════════════════════════════════════
   BUILD PRINT AREA — sama persis arsitekturnya dengan modul
   Pendataan Kantong, supaya tidak kena bug yang sama:
   - .label-page (block, pemegang page-break) membungkus
     .label-wrap (flex, layout visual saja)
   - label TERAKHIR tidak memaksa break setelahnya (hindari
     halaman kosong nyangkut di ujung dokumen)
   ════════════════════════════════════════════════════════ */
function buildPrintArea(items) {
    const area = document.getElementById('print-area');
    area.innerHTML = '';

    items.forEach((item) => {
        const page = document.createElement('div');
        page.className = 'label-page';

        const wrap = document.createElement('div');
        wrap.className = 'label-wrap';

        // Title row: judul (kiri) + type (kanan, sudah dikecilkan)
        const titleRow = document.createElement('div');
        titleRow.className = 'label-title-row';

        const title = document.createElement('div');
        title.className = 'label-title';
        title.textContent = 'UDD PMI KOTA SBY';
        titleRow.appendChild(title);

        const ukuranType = document.createElement('div');
        ukuranType.className = 'label-ukuran-type';
        ukuranType.innerHTML = `
            <span class="type">${item.type || ''}</span>
        `;
        titleRow.appendChild(ukuranType);

        wrap.appendChild(titleRow);

        // Barcode
        const barcodeDiv = document.createElement('div');
        barcodeDiv.className = 'label-barcode';
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        barcodeDiv.appendChild(svg);
        wrap.appendChild(barcodeDiv);

        // Footer: no barcode (kiri) + type lagi (kanan, untuk konsistensi label fisik)
        const foot = document.createElement('div');
        foot.className = 'label-foot';
        foot.innerHTML = `<span class="left">${item.barcode}</span><span class="right">${item.type || ''}</span>`;
        wrap.appendChild(foot);

        page.appendChild(wrap);
        area.appendChild(page);

        JsBarcode(svg, item.barcode, {
            format: 'CODE128',
            width: 1.6,   // sedikit tebal supaya tegas/bold saat dicetak
            height: 28,
            displayValue: false,
            margin: 0,
            lineColor: '#000'
        });
    });

    // PENTING: matikan break-after pada label PALING AKHIR, supaya tidak
    // ada halaman kosong nyangkut di ujung dokumen.
    const allPages = area.querySelectorAll('.label-page');
    if (allPages.length > 0) {
        const lastPage = allPages[allPages.length - 1];
        lastPage.style.pageBreakAfter = 'avoid';
        lastPage.style.breakAfter = 'avoid';
    }
}

document.getElementById('btn-print')
.addEventListener('click', function () {

    const checked = document.querySelectorAll('.barcode-check:checked');

    if (!checked.length) {
        alert('Pilih barcode terlebih dahulu');
        return;
    }

    const items = Array.from(checked).map(el => ({
        barcode: el.dataset.barcode,
        type:    el.dataset.type,
        ukuran:  el.dataset.ukuran,
        merk:    el.dataset.merk,
        jenis:   el.dataset.jenis,
        no_lot:  el.dataset.no_lot,
    }));

    buildPrintArea(items);

    const area = document.getElementById('print-area');
    area.style.display = 'block';
    setTimeout(() => window.print(), 150); // beri waktu JsBarcode selesai render
});

window.addEventListener('afterprint', function () {
    document.getElementById('print-area').style.display = 'none';
});
</script>

@endsection