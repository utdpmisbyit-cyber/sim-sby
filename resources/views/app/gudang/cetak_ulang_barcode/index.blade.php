@extends('layouts.index')

@section('title')
    Cetak Ulang Barcode
@endsection

@push('styles')
<style>
:root{
    --pmi-red:#C8102E;
    --pmi-dark:#f23928;
    --pmi-light:#f8fafc;
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
                    data-type="${row.type_kantong}">
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

document.getElementById('btn-print')
.addEventListener('click', function(){

    const checked = document.querySelectorAll(
        '.barcode-check:checked'
    );

    if (!checked.length) {
        alert('Pilih barcode terlebih dahulu');
        return;
    }

    const labels = [];

    checked.forEach(el => {

        labels.push(`
        <div class="label">
            <div class="row header">
                <div>UDD PMI KOTA SBY</div>
                <div>${el.dataset.type}</div>
            </div>

            <div class="barcode-wrap">
                <svg class="barcode"></svg>
            </div>

            <div class="row footer">
                <div>${el.dataset.barcode}</div>
                <div>${el.dataset.type}</div>
            </div>
        </div>
        `);
    });

    const printWin = window.open('', '_blank');

    printWin.document.write(`
    <html>
    <head>
    <style>
    @page {
        size: 50mm 25mm;
        margin:0;
    }

    body{
        margin:0;
        font-family:monospace;
    }

    .label{
        width:50mm;
        height:25mm;
        padding:2mm;
        page-break-after:always;
        box-sizing:border-box;
        display:flex;
        flex-direction:column;
        justify-content:space-between;
    }

    .row{
        display:flex;
        justify-content:space-between;
        font-size:9pt;
        font-weight:bold;
    }

    svg{
        width:42mm;
        height:10mm;
    }

    .barcode-wrap{
        display:flex;
        justify-content:center;
    }
    </style>
    </head>
    <body>
        ${labels.join('')}
    </body>
    </html>
    `);

    const script = printWin.document.createElement('script');
    script.src =
'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js';

    script.onload = function () {

        const svgs =
            printWin.document.querySelectorAll('.barcode');

        svgs.forEach((svg,index)=>{

            const code =
                checked[index].dataset.barcode;

            JsBarcode(svg, code,{
                format:'CODE128',
                displayValue:false,
                width:1.8,
                height:40,
                margin:0
            });

        });

        printWin.print();
        printWin.close();
    };

    printWin.document.body.appendChild(script);
});
</script>

@endsection