@extends('layouts.index')

@section('title', 'Laporan Perubahan Aset Netto')

@push('styles')
<style>
    .report-wrapper {
        background: #f0f4ff;
        min-height: 100vh;
        padding: 24px;
        font-family: 'Segoe UI', sans-serif;
    }

    .report-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
    }

    /* Header */
    .report-header {
        background: #fff;
        text-align: center;
        padding: 28px 24px 16px;
        border-bottom: 1px solid #e8eaf0;
    }
    .report-header .org-name {
        color: #2563eb;
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .report-header .report-title {
        color: #2563eb;
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .report-header .report-period {
        color: #6b7280;
        font-size: 0.85rem;
    }

    /* Filter */
    .filter-section {
        padding: 16px 24px;
        border-bottom: 1px solid #e8eaf0;
    }
    .filter-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #374151;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .filter-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
        min-width: 160px;
    }
    .filter-group label {
        font-size: 0.78rem;
        color: #6b7280;
        font-weight: 500;
    }
    .filter-group input[type="date"] {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 7px 12px;
        font-size: 0.85rem;
        color: #374151;
        outline: none;
        transition: border-color 0.2s;
        width: 100%;
    }
    .filter-group input[type="date"]:focus {
        border-color: #2563eb;
    }
    .btn-reset {
        background: #9ca3af;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 28px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        height: 36px;
        white-space: nowrap;
    }
    .btn-reset:hover { background: #6b7280; }
    .btn-filter {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 28px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        height: 36px;
        white-space: nowrap;
    }
    .btn-filter:hover { background: #1d4ed8; }

    /* Section Header */
    .section-header {
        background: #3730a3;
        color: #fff;
        padding: 13px 24px;
        font-size: 0.88rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
        text-transform: uppercase;
    }
    .section-header .toggle-icon {
        font-size: 1rem;
        transition: transform 0.2s;
    }
    .section-header.collapsed .toggle-icon {
        transform: rotate(180deg);
    }

    /* Section Body */
    .section-body {
        border-bottom: 1px solid #e8eaf0;
    }
    .section-row {
        display: flex;
        align-items: center;
        padding: 13px 24px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.88rem;
    }
    .section-row:last-child { border-bottom: none; }
    .row-label {
        flex: 1;
        color: #374151;
    }
    .row-val {
        width: 150px;
        text-align: right;
        color: #111827;
        font-weight: 500;
    }
    .row-val.green { color: #16a34a; font-weight: 700; }
    .row-val.dash { color: #9ca3af; }
    .row-val.year-2025 {
        width: 120px;
        text-align: right;
        color: #9ca3af;
    }

    /* Total Row */
    .total-row {
        background: #eff6ff;
        border-left: 4px solid #2563eb;
        padding: 13px 24px 13px 20px;
        display: flex;
        align-items: center;
        font-size: 0.88rem;
    }
    .total-row .row-label {
        font-weight: 700;
        color: #1e3a8a;
    }
    .total-row .row-val {
        color: #1e3a8a;
        font-weight: 700;
    }

    /* Grand Total */
    .grand-total-row {
        background: #3730a3;
        color: #fff;
        padding: 15px 24px;
        display: flex;
        align-items: center;
        font-size: 0.92rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .grand-total-row .row-label { flex: 1; color: #fff; }
    .grand-total-row .row-val { color: #fff; width: 150px; text-align: right; }
    .grand-total-row .row-val.year-2025 { width: 120px; color: #c7d2fe; }

    /* Footer keterangan */
    .footer-row {
        display: flex;
        padding: 10px 24px;
        font-size: 0.78rem;
        color: #9ca3af;
        border-top: 1px solid #e8eaf0;
        background: #fafafa;
    }
    .footer-row .row-label { flex: 1; }
    .footer-row .col-year { width: 150px; text-align: right; }
    .footer-row .col-year2 { width: 120px; text-align: right; }

    /* Collapsible */
    .collapsible-body { overflow: hidden; transition: max-height 0.3s ease; }
    .collapsible-body.hidden { max-height: 0; }

    /* Loading */
    #loading-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(255,255,255,0.6);
        z-index: 999;
        align-items: center;
        justify-content: center;
    }
    #loading-overlay.show { display: flex; }
    .spinner {
        width: 40px; height: 40px;
        border: 4px solid #e0e7ff;
        border-top-color: #3730a3;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="report-wrapper">
    <div id="loading-overlay"><div class="spinner"></div></div>

    <div class="report-card">
        {{-- Header --}}
        <div class="report-header">
            <div class="org-name">Palang Merah Indonesia Kota Surabaya</div>
            <div class="report-title">Laporan Perubahan Aset Netto</div>
            <div class="report-period" id="period-label">
                Periode sampai {{ \Carbon\Carbon::now()->format('d F Y') }}
            </div>
        </div>

        {{-- Filter --}}
        <div class="filter-section">
            <div class="filter-label">Filter Periode</div>
            <form id="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal" value="{{ request('tanggal_awal') }}">
                    </div>
                    <div class="filter-group">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                    </div>
                    <button type="submit" class="btn-filter">Tampilkan</button>
                    <button type="button" class="btn-reset" id="btn-reset">Reset</button>
                </div>
            </form>
        </div>

       {{-- Report Body --}}
        <div id="report-body">
            @include('app.finance.laporan.aset_netto._table', [
                'tidakTerikat' => $tidakTerikat,
                'terikat'      => $terikat,
                'totalNetto'   => $totalNetto,
                'tahunIni'     => $tahunIni,
                'tahunLalu'    => $tahunLalu,
            ])
</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Toggle collapsible sections
    document.querySelectorAll('.section-header').forEach(function (header) {
        header.addEventListener('click', function () {
            const body = this.nextElementSibling;
            if (body && body.classList.contains('collapsible-body')) {
                body.classList.toggle('hidden');
                this.classList.toggle('collapsed');
            }
        });
    });

    // Filter form submit
    document.getElementById('filter-form').addEventListener('submit', function (e) {
        e.preventDefault();
        loadReport();
    });

    // Reset
    document.getElementById('btn-reset').addEventListener('click', function () {
        document.getElementById('tanggal_awal').value = '';
        document.getElementById('tanggal_akhir').value = '';
        loadReport();
    });

    function loadReport() {
        const awal = document.getElementById('tanggal_awal').value;
        const akhir = document.getElementById('tanggal_akhir').value;
        const params = new URLSearchParams();
        if (awal) params.append('tanggal_awal', awal);
        if (akhir) params.append('tanggal_akhir', akhir);

        // Update period label
        if (akhir) {
            const d = new Date(akhir);
            const opts = { day: '2-digit', month: 'long', year: 'numeric' };
            document.getElementById('period-label').textContent =
                'Periode sampai ' + d.toLocaleDateString('id-ID', opts);
        }

        document.getElementById('loading-overlay').classList.add('show');

        fetch('{{ route("finance.laporan.aset_netto.search") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            document.getElementById('report-body').innerHTML = html;
            // Re-bind toggles
            document.querySelectorAll('.section-header').forEach(function (header) {
                header.addEventListener('click', function () {
                    const body = this.nextElementSibling;
                    if (body && body.classList.contains('collapsible-body')) {
                        body.classList.toggle('hidden');
                        this.classList.toggle('collapsed');
                    }
                });
            });
        })
        .finally(() => {
            document.getElementById('loading-overlay').classList.remove('show');
        });
    }
});
</script>
@endpush