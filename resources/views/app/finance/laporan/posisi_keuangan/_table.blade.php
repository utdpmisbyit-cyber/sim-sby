{{-- resources/views/finance/laporan/posisi_keuangan/laporan.blade.php --}}

@extends('layouts.index')

@section('title', 'Laporan Posisi Keuangan')

@section('content')
<div class="p-4">

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- =================== LEFT CARD (Chart) =================== --}}
        <div class="bg-white shadow rounded-xl border p-4">

            <h2 class="text-center text-xl font-bold">
                PALANG MERAH INDONESIA KOTA SURABAYA
            </h2>
            <p class="text-center">LAPORAN POSISI KEUANGAN</p>
            <p class="text-center text-sm mt-1" id="periodeLabel"></p>

            {{-- FILTER --}}
            <div class="flex flex-wrap justify-center items-center gap-3 my-4">
                <input id="tglAwal" type="date" class="border px-3 py-2 rounded" value="{{ date('Y-01-01') }}">
                <input id="tglAkhir" type="date" class="border px-3 py-2 rounded" value="{{ date('Y-m-d') }}">
                <button id="btnFilter" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
                <button id="btnReset" class="border px-4 py-2 rounded">Reset</button>
            </div>

            {{-- CHART TYPE --}}
            <div class="flex justify-center gap-3 mb-4">
                <button data-type="bar" class="chart-btn bg-blue-600 text-white px-3 py-1 rounded">Bar</button>
                <button data-type="line" class="chart-btn border px-3 py-1 rounded">Line</button>
                <button data-type="pie" class="chart-btn border px-3 py-1 rounded">Pie</button>
            </div>

            {{-- CHART --}}
            <div class="max-w-md mx-auto">
                <canvas id="laporanChart" height="260"></canvas>
            </div>
        </div>

        {{-- =================== RIGHT CARD (Detail Table + Export) =================== --}}
        <div class="bg-white shadow rounded-xl border p-4">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-center text-xl font-bold">Detail Laporan</h3>

                <div class="flex gap-3">
                    <button id="btnPdf" class="bg-green-600 text-white px-4 py-2 rounded">Export PDF</button>
                    <button id="btnExcel" class="bg-blue-600 text-white px-4 py-2 rounded">Export Excel</button>
                </div>
            </div>

            {{-- WRAPPER UNTUK EXPORT --}}
            <div id="exportPDFArea">
                <div id="laporanContainer" class="max-h-[70vh] overflow-y-auto"></div>
            </div>
        </div>

    </div>
</div>

{{-- ====================== SCRIPT ====================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<script>
let chart;
let chartType = "bar";
let laporan = [];
const baseURL = "{{ route('laporan.posisi_keuangan.search') }}";

// ====================== FETCH DATA =======================
async function fetchLaporan() {
    const start = document.getElementById("tglAwal").value;
    const end   = document.getElementById("tglAkhir").value;
    document.getElementById("periodeLabel").innerHTML = `Periode: ${start} - ${end}`;

    const res = await fetch(baseURL, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
            "Accept": "application/json",
        },
        body: JSON.stringify({ start, end }),
    });

    const json = await res.json();

    if (!json.data || json.data.length === 0) {
        document.getElementById("laporanContainer").innerHTML =
            `<p class='text-center text-red-600 p-4'>Tidak ada data laporan.</p>`;
        laporan = [];
        renderChart();
        return;
    }

    laporan = groupData(json.data);
    renderTable();
    renderChart();
}

// ====================== GROUP DATA =======================
function groupData(items) {
    const grouped = {};
    items.forEach(item => {
        const coa = item.coa ?? {};
        const kategori = coa.kategori_1 ?? "Lainnya";
        const tahun = new Date(item.tgl).getFullYear();
        const saldo = (parseFloat(item.nominal_debit) || 0) - (parseFloat(item.nominal_kredit) || 0);

        if (!grouped[kategori]) grouped[kategori] = [];
        grouped[kategori].push({
            kode: coa.kd_coa || item.kode || "-",
            akun: item.nama_akun || coa.nama_akun || "-",
            deskripsi: item.keterangan || "-",
            tahun2025: tahun === 2025 ? saldo : 0,
            tahun2024: tahun === 2024 ? saldo : 0,
        });
    });

    return Object.entries(grouped).map(([title, rows]) => ({
        title,
        rows,
        total2025: rows.reduce((s,r) => s + r.tahun2025, 0),
        total2024: rows.reduce((s,r) => s + r.tahun2024, 0),
    }));
}

// ====================== RENDER TABLE =======================
function renderTable() {
    const c = document.getElementById("laporanContainer");
    c.innerHTML = "";

    laporan.forEach(section => {
        let html = `
        <div class="mb-6 border-b pb-4">
            <h3 class="font-semibold text-lg mb-2 text-red-600">${section.title}</h3>
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">Kode</th>
                        <th class="p-2 border">Akun</th>
                        <th class="p-2 border">Deskripsi</th>
                        <th class="p-2 border text-right">2025</th>
                        <th class="p-2 border text-right">2024</th>
                    </tr>
                </thead>
                <tbody>
        `;
        section.rows.forEach(r => {
            html += `
                <tr>
                    <td class="p-2 border">${r.kode}</td>
                    <td class="p-2 border">${r.akun}</td>
                    <td class="p-2 border">${r.deskripsi}</td>
                    <td class="p-2 border text-right">${formatRupiah(r.tahun2025)}</td>
                    <td class="p-2 border text-right">${formatRupiah(r.tahun2024)}</td>
                </tr>
            `;
        });
        html += `
                </tbody>
            </table>
            <div class="flex justify-end mt-2 pt-2 border-t font-semibold">
                <div class="w-64">Total ${section.title}</div>
                <div class="w-32 text-right">${formatRupiah(section.total2025)}</div>
                <div class="w-32 text-right">${formatRupiah(section.total2024)}</div>
            </div>
        </div>`;
        c.innerHTML += html;
    });
}

// ======================= FORMAT RUPIAH =======================
function formatRupiah(val) {
    if (!val) return "0";
    return val.toLocaleString("id-ID");
}

// ======================= RENDER CHART =======================
function renderChart() {
    if (!laporan || laporan.length === 0) {
        if (chart) chart.destroy();
        return;
    }

    const ctx = document.getElementById("laporanChart").getContext("2d");
    if (chart) chart.destroy();

    const labels = laporan.map(s => s.title);

    const barOrLineData = {
        labels,
        datasets: [
            { label: "Tahun 2025", data: laporan.map(s => s.total2025) },
            { label: "Tahun 2024", data: laporan.map(s => s.total2024) },
        ]
    };

    const pieData = { labels, datasets: [{ data: laporan.map(s => s.total2025) }] };

    chart = new Chart(ctx, {
        type: chartType,
        data: chartType === "pie" ? pieData : barOrLineData,
        options: { responsive:true, maintainAspectRatio:false }
    });
}

// ====================== EVENT LISTENER =======================
document.getElementById("btnFilter").addEventListener("click", fetchLaporan);
document.getElementById("btnReset").addEventListener("click", () => {
    document.getElementById("tglAwal").value = "{{ date('Y-01-01') }}";
    document.getElementById("tglAkhir").value = "{{ date('Y-m-d') }}";
    fetchLaporan();
});
document.querySelectorAll(".chart-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        chartType = btn.dataset.type;
        renderChart();
    });
});

// ====================== EXPORT PDF =======================
document.getElementById("btnPdf").addEventListener("click", () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("p", "pt", "a4");
    doc.html(document.getElementById("exportPDFArea"), {
        callback(doc){ doc.save("laporan-posisi-keuangan.pdf"); },
        margin: [20,20,20,20],
        autoPaging:"text",
        html2canvas:{ scale: 0.75 }
    });
});

// ====================== EXPORT EXCEL =======================
document.getElementById("btnExcel").addEventListener("click", () => {
    const ws = XLSX.utils.table_to_sheet(document.getElementById("laporanContainer"));
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Laporan");
    XLSX.writeFile(wb, "laporan-posisi-keuangan.xlsx");
});

// ====================== AUTO LOAD FIRST TIME =======================
fetchLaporan();
</script>
@endpush