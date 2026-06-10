{{--
    Variables tersedia:
    $tidakTerikat  → array/collection dengan keys: saldo_awal, pendapatan_netto, saldo_akhir (tahun ini & tahun lalu)
    $terikat       → array/collection sama
    $totalNetto    → array: ['current' => ..., 'previous' => ...]
    $tahunIni      → int
    $tahunLalu     → int
--}}

@php
    use App\Helpers\FormatAngka;

    function fmtAng($val) {
        if (is_null($val) || $val == 0) return '-';
        return number_format($val, 0, ',', '.');
    }
@endphp

{{-- ====== ASET NETTO TIDAK TERIKAT ====== --}}
<div class="section-body">
    <div class="section-header">
        <span>Aset Netto Tidak Terikat</span>
        <span class="toggle-icon">&#8679;</span>
    </div>
    <div class="collapsible-body">
        <div class="section-row">
            <div class="row-label">Saldo Awal</div>
            <div class="row-val {{ $tidakTerikat['saldo_awal']['current'] > 0 ? 'green' : 'dash' }}">
                {{ fmtAng($tidakTerikat['saldo_awal']['current']) }}
            </div>
            <div class="row-val year-2025 {{ $tidakTerikat['saldo_awal']['previous'] > 0 ? '' : 'dash' }}">
                {{ fmtAng($tidakTerikat['saldo_awal']['previous']) }}
            </div>
        </div>
        <div class="section-row">
            <div class="row-label">Pendapatan Netto Periode Berjalan</div>
            <div class="row-val {{ $tidakTerikat['pendapatan_netto']['current'] > 0 ? 'green' : 'dash' }}">
                {{ fmtAng($tidakTerikat['pendapatan_netto']['current']) }}
            </div>
            <div class="row-val year-2025 {{ $tidakTerikat['pendapatan_netto']['previous'] > 0 ? '' : 'dash' }}">
                {{ fmtAng($tidakTerikat['pendapatan_netto']['previous']) }}
            </div>
        </div>
        <div class="total-row">
            <div class="row-label">Saldo Akhir Aset Netto Tidak Terikat</div>
            <div class="row-val {{ $tidakTerikat['saldo_akhir']['current'] > 0 ? 'green' : 'dash' }}">
                {{ fmtAng($tidakTerikat['saldo_akhir']['current']) }}
            </div>
            <div class="row-val year-2025 {{ $tidakTerikat['saldo_akhir']['previous'] > 0 ? '' : 'dash' }}">
                {{ fmtAng($tidakTerikat['saldo_akhir']['previous']) }}
            </div>
        </div>
    </div>
</div>

{{-- ====== ASET NETTO TERIKAT ====== --}}
<div class="section-body">
    <div class="section-header">
        <span>Aset Netto Terikat</span>
        <span class="toggle-icon">&#8679;</span>
    </div>
    <div class="collapsible-body">
        <div class="section-row">
            <div class="row-label">Saldo Awal</div>
            <div class="row-val {{ $terikat['saldo_awal']['current'] > 0 ? 'green' : 'dash' }}">
                {{ fmtAng($terikat['saldo_awal']['current']) }}
            </div>
            <div class="row-val year-2025 {{ $terikat['saldo_awal']['previous'] > 0 ? '' : 'dash' }}">
                {{ fmtAng($terikat['saldo_awal']['previous']) }}
            </div>
        </div>
        <div class="section-row">
            <div class="row-label">Pendapatan Netto Periode Berjalan</div>
            <div class="row-val {{ $terikat['pendapatan_netto']['current'] > 0 ? 'green' : 'dash' }}">
                {{ fmtAng($terikat['pendapatan_netto']['current']) }}
            </div>
            <div class="row-val year-2025 {{ $terikat['pendapatan_netto']['previous'] > 0 ? '' : 'dash' }}">
                {{ fmtAng($terikat['pendapatan_netto']['previous']) }}
            </div>
        </div>
        <div class="total-row">
            <div class="row-label">Saldo Akhir Aset Netto Terikat</div>
            <div class="row-val {{ $terikat['saldo_akhir']['current'] > 0 ? 'green' : 'dash' }}">
                {{ fmtAng($terikat['saldo_akhir']['current']) }}
            </div>
            <div class="row-val year-2025 {{ $terikat['saldo_akhir']['previous'] > 0 ? '' : 'dash' }}">
                {{ fmtAng($terikat['saldo_akhir']['previous']) }}
            </div>
        </div>
    </div>
</div>

{{-- ====== GRAND TOTAL ====== --}}
<div class="grand-total-row">
    <div class="row-label">Total Assets Netto</div>
    <div class="row-val {{ $totalNetto['current'] > 0 ? '' : 'dash' }}">
        {{ fmtAng($totalNetto['current']) }}
    </div>
    <div class="row-val year-2025 {{ $totalNetto['previous'] > 0 ? '' : 'dash' }}">
        {{ fmtAng($totalNetto['previous']) }}
    </div>
</div>

{{-- Footer label tahun --}}
<div class="footer-row">
    <div class="row-label">Keterangan</div>
    <div class="col-year">{{ $tahunIni }}</div>
    <div class="col-year2">{{ $tahunLalu }}</div>
</div>