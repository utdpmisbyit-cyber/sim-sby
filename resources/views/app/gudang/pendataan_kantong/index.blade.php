@extends('layouts.index')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --pmi-red: #C8102E;
        --pmi-dark: #f23928;
        --pmi-panel: #fcfdff;
        --pmi-card: #0f3460;
        --pmi-accent: #e94560;
        --pmi-teal: #00b4d8;
        --pmi-light: #f0f4f8;
        --pmi-muted: #8892a4;
        --mono: 'IBM Plex Mono', monospace;
        --sans: 'IBM Plex Sans', sans-serif;
    }

    body { font-family: var(--sans); background: var(--pmi-light); }

    /* ── Page Header ── */
    .pk-header {
        background: linear-gradient(135deg, var(--pmi-dark) 0%, var(--pmi-panel) 100%);
        color: #fff;
        padding: 1.25rem 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 3px solid var(--pmi-red);
        box-shadow: 0 4px 20px rgba(0,0,0,.25);
    }
    .pk-header .badge-pmi {
        background: var(--pmi-red);
        color: #fff;
        font-family: var(--mono);
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .1em;
        padding: .25rem .6rem;
        border-radius: 3px;
        text-transform: uppercase;
    }
    .pk-header h1 { margin: 0; font-size: 1.15rem; font-weight: 600; letter-spacing: .02em; }
    .pk-header p  { margin: 0; font-size: .78rem; color: var(--pmi-muted); }

    /* ── Main Card ── */
    .label-inner {
        width: 37mm;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .pk-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 16px rgba(0,0,0,.08);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .pk-card-header {
        background: var(--pmi-dark);
        color: #fff;
        padding: .75rem 1.25rem;
        font-size: .8rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .pk-card-header i { color: var(--pmi-teal); }
    .pk-card-body { padding: 1.5rem; }

    /* ── Form Controls ── */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .form-grid-3 { grid-template-columns: repeat(3, 1fr); }
    @media(max-width:768px) {
        .form-grid, .form-grid-3 { grid-template-columns: 1fr 1fr; }
    }

    .fgroup label {
        display: block;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--pmi-muted);
        margin-bottom: .35rem;
    }
    .fgroup select,
    .fgroup input {
        width: 100%;
        border: 1.5px solid #dde3ee;
        border-radius: 7px;
        padding: .5rem .75rem;
        font-size: .88rem;
        font-family: var(--sans);
        background: #f8fafc;
        color: var(--pmi-dark);
        transition: border-color .2s, box-shadow .2s;
        outline: none;
    }
    .fgroup select:focus,
    .fgroup input:focus {
        border-color: var(--pmi-teal);
        box-shadow: 0 0 0 3px rgba(0,180,216,.15);
        background: #fff;
    }
    .fgroup input[readonly] {
        background: #eef2f7;
        color: #6b7a99;
        cursor: not-allowed;
    }
    .fgroup .hint {
        font-size: .68rem;
        color: var(--pmi-muted);
        margin-top: .2rem;
    }

    /* ── Action Buttons ── */
    .btn-action-row {
        display: flex;
        align-items: center;
        gap: .6rem;
        justify-content: flex-end;
        padding-top: 1rem;
        border-top: 1px solid #eef2f7;
        margin-top: 1rem;
    }
    .btn-pk {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.2rem;
        border: none;
        border-radius: 7px;
        font-size: .83rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .2s;
        font-family: var(--sans);
    }
    .btn-pk:active { transform: scale(.97); }
    .btn-run    { background: #00b4d8; color: #fff; }
    .btn-run:hover { background: #0096b7; }
    .btn-stop   { background: #64748b; color: #fff; }
    .btn-stop:hover { background: #475569; }
    .btn-save   { background: #f59e0b; color: #fff; }
    .btn-save:hover { background: #d97706; }
    .btn-save:disabled,
    .btn-print:disabled { opacity: .5; cursor: not-allowed; }
    .btn-print  { background: var(--pmi-red); color: #fff; }
    .btn-print:hover { background: #a30c24; }

    /* ── Jumlah Badge ── */
    .jumlah-badge {
        background: var(--pmi-dark);
        color: var(--pmi-teal);
        font-family: var(--mono);
        font-size: 1.6rem;
        font-weight: 600;
        border-radius: 10px;
        padding: .6rem 1.5rem;
        text-align: center;
        min-width: 100px;
        border: 1.5px solid var(--pmi-card);
    }
    .jumlah-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--pmi-muted);
        text-align: center;
        margin-bottom: .35rem;
    }

    /* ── Table ── */
    .pk-table-wrap {
        overflow-x: auto;
        border-radius: 10px;
        border: 1px solid #dde3ee;
    }
    .pk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .83rem;
    }
    .pk-table thead tr {
        background: var(--pmi-dark);
        color: #fff;
    }
    .pk-table thead th {
        padding: .75rem 1rem;
        text-align: left;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pk-table tbody tr {
        border-bottom: 1px solid #f0f4f8;
        transition: background .15s;
    }
    .pk-table tbody tr:hover { background: #f8fafc; }
    .pk-table tbody td {
        padding: .7rem 1rem;
        color: #334155;
        font-family: var(--mono);
        font-size: .8rem;
    }
    .pk-table tbody td.text-normal { font-family: var(--sans); }
    .badge-nat {
        background: #dcfce7;
        color: #166534;
        border-radius: 4px;
        padding: .15rem .45rem;
        font-size: .7rem;
        font-weight: 700;
    }
    .badge-no {
        background: var(--pmi-dark);
        color: var(--pmi-teal);
        border-radius: 4px;
        padding: .15rem .5rem;
        font-size: .75rem;
        font-weight: 600;
        font-family: var(--mono);
    }
    .td-action { text-align: center; }
    .btn-del {
        background: none;
        border: 1.5px solid #fca5a5;
        color: #ef4444;
        border-radius: 5px;
        padding: .25rem .55rem;
        font-size: .75rem;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-del:hover { background: #fef2f2; }

    /* ── Duplicate Warning ── */
    .pk-table tbody tr.row-bentrok { background: #fef2f2 !important; }
    .badge-bentrok {
        background: #fee2e2;
        color: #b91c1c;
        border-radius: 4px;
        padding: .15rem .5rem;
        font-size: .72rem;
        font-weight: 700;
        font-family: var(--mono);
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        white-space: nowrap;
    }
    .save-warning {
        font-size: .75rem;
        color: var(--pmi-red);
        font-weight: 600;
        margin-right: auto;
        display: none;
        align-items: center;
        gap: .4rem;
    }
    .save-warning.show { display: inline-flex; }

    /* ── Loading Spinner ── */
    #loading-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(26,26,46,.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 1rem;
        color: #fff;
        font-size: .9rem;
        font-family: var(--sans);
    }
    .spinner {
        width: 42px; height: 42px;
        border: 4px solid rgba(255,255,255,.2);
        border-top-color: var(--pmi-teal);
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Toast ── */
    #toast {
        position: fixed;
        top: 1.5rem; right: 1.5rem;
        background: var(--pmi-dark);
        color: #fff;
        padding: .75rem 1.25rem;
        border-radius: 8px;
        font-size: .85rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.2);
        z-index: 10000;
        opacity: 0;
        transform: translateY(-10px);
        transition: all .3s;
        display: flex;
        align-items: center;
        gap: .5rem;
        border-left: 4px solid var(--pmi-teal);
    }
    #toast.show { opacity: 1; transform: translateY(0); }
    #toast.error { border-left-color: var(--pmi-red); }

    /* ════════════════════════════════════════════
       PRINT STYLES — SATU SUMBER KEBENARAN
       Ukuran fisik label: 40mm x 20mm (sesuai fisik di lapangan)
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

    /* ── Page-break holder ──
       FIX GARIS PUTUS (v3): dua percobaan sebelumnya salah asumsi.
       v1: .label-cut absolute bottom:0 -- nempel di ujung PALING BAWAH
           label-page (19mm), padahal konten aslinya jauh lebih pendek
           -> muncul gap kosong besar antara footer & garis.
       v2: .label-gap (flex:1, center) -- nge-center garis di TENGAH
           sisa ruang kosong itu, tapi ternyata yang dibutuhkan (lihat
           foto referensi fisik) bukan di tengah, melainkan RAPAT
           langsung di bawah footer, jarak kecil & fixed.
       v3 (sekarang): height:19mm DIHAPUS dari .label-page -- tinggi
       kertas mengikuti konten asli (.label-wrap) + jarak kecil ke
       .label-cut, bukan dipaksa fixed. Garis jadi rapat seperti foto. */
    .label-page {
        width: 40mm;
        overflow: hidden;
        page-break-after: always;
        break-after: page;
        page-break-inside: avoid;
        break-inside: avoid;
        display: flex;
        flex-direction: column;
    }

    /* ── Layout visual konten label (title+barcode+footer) saja ── */
    .label-wrap {
        width: 100%;
        /* height:100% tetap dihapus -- tinggi wrap = tinggi konten asli
           saja (title+barcode+footer), tidak dipaksa mengisi label-page. */
        flex: 0 0 auto;
        padding: 1.2mm 1.5mm 0.8mm 1.5mm;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        background: #fff;
    }

    .label-title {
        font-size: 5.5pt;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-align: left;
        line-height: 1;
        flex-shrink: 0;
        margin-bottom: 0.5mm;
        padding: 0 0.5mm;
        font-family: 'IBM Plex Mono', monospace;
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
        font-family: 'IBM Plex Mono', monospace;
        margin-top: 0.3mm;
        padding: 0 0.5mm;
        line-height: 1;
        flex-shrink: 0;
    }
    .label-foot .left { text-align: left; }
    .label-foot .right { text-align: right; }

    /* Garis putus — dipakai HANYA setelah semua duplikat satu no_kantong
       selesai (lihat JS), dan tidak pernah pada label paling akhir di
       seluruh batch. Sekarang ditempel LANGSUNG sebagai child .label-page
       setelah .label-wrap, dengan jarak KECIL & FIXED (margin-top) --
       BUKAN di-center di ruang kosong besar (lihat catatan v3 di atas).
       Sesuaikan margin-top di bawah kalau hasil cetak fisik masih
       kurang/lebih rapat dibanding foto referensi. */
    .label-cut {
        width: 40mm;
        height: 0;
        margin: 1mm 0 0 0;
        border-top: 1.5px dashed #555;
    }
    @media print {
        .label-cut { border-top: 1.5px dashed #000; }
    }
</style>
@endpush

@section('content')
<div id="loading-overlay">
    <div class="spinner"></div>
    <span>Memproses data...</span>
</div>
<div id="toast"></div>

{{-- Header --}}
<div class="pk-header">
    <div>
        <span class="badge-pmi">PMI</span>
    </div>
    <div>
        <h1>Pendataan Kantong Darah</h1>
        <p>Gudang &rsaquo; Pendataan Kantong &rsaquo; Generate Barcode & Cetak Label</p>
    </div>
</div>

<div class="container-fluid px-4 py-4">

    {{-- Form Card --}}
    <div class="pk-card">
        <div class="pk-card-header">
            <i class="fas fa-tags"></i> Form Generate Kantong
        </div>
        <div class="pk-card-body">
            <form id="form-kantong" autocomplete="off">
                @csrf
                {{-- Row 1: dropdowns --}}
                <div class="form-grid">
                    <div class="fgroup">
                        <label>Merk Kantong</label>
                        <select name="merk_kantong" id="merk_kantong">
                            <option value="">— Pilih Merk —</option>
                            @foreach($merk_kantong as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Jenis Kantong</label>
                        <select name="jenis_kantong" id="jenis_kantong" required>
                            <option value="">— Pilih Jenis —</option>
                            @foreach($jenis_kantong as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Type Kantong</label>
                        <select name="type_kantong" id="type_kantong" required>
                            <option value="">— Pilih Type —</option>
                            @foreach($type_kantong as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fgroup">
                        <label>Ukuran</label>
                        <select name="ukuran" id="ukuran" required>
                            <option value="">— Pilih Ukuran —</option>
                            @foreach($ukuran_kantong as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Row 2: inputs --}}
                <div class="form-grid">
                    <div class="fgroup">
                        <label>No. Lot</label>
                        <input type="text" name="no_lot" id="no_lot" placeholder="Contoh: 254222" required>
                    </div>
                    <div class="fgroup">
                        <label>Duplikat (lembar/kantong)</label>
                        <input type="number" name="duplikat" id="duplikat" min="1" max="10" value="1" required>
                        <div class="hint">Jumlah label per nomor kantong</div>
                    </div>
                    <div class="fgroup">
                        <label>No Kantong (auto)</label>
                        <input type="text" id="no_kantong_preview" readonly placeholder="Otomatis">
                        <div class="hint">Format: YYMMSEQ (ex: 2604<strong>0001</strong>)</div>
                    </div>
                    <div class="fgroup">
                        <label>Jumlah Cetak</label>
                        <input type="number" name="jumlah_cetak" id="jumlah_cetak" min="0" max="500" value="0" required>
                        <div class="hint">Total nomor kantong unik</div>
                    </div>
                </div>

                {{-- Summary + Buttons --}}
                <div style="display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
                    <div>
                        <div class="jumlah-label">Total Label Dicetak</div>
                        <div class="jumlah-badge" id="total-label">0</div>
                    </div>
                    <div class="btn-action-row" style="border:none; padding:0; margin:0;">
                        <span class="save-warning" id="save-warning">
                            <i class="fas fa-triangle-exclamation"></i>
                            Ada nomor kantong bentrok — hapus baris itu sebelum Simpan
                        </span>
                        <button type="button" class="btn-pk btn-run" id="btn-run">
                            <i class="fas fa-running"></i> Run
                        </button>
                        <button type="button" class="btn-pk btn-print" id="btn-print-direct" disabled>
                            <i class="fas fa-print"></i> Print Sato
                        </button>
                        <button type="button" class="btn-pk btn-save" id="btn-save" disabled>
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <button type="button" class="btn-pk btn-print" id="btn-print" disabled>
                            <i class="fas fa-eye"></i> Preview
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Preview Table --}}
    <div class="pk-card">
        <div class="pk-card-header">
            <i class="fas fa-table"></i> Preview Data Kantong
            <span id="row-count" style="margin-left:auto; background:rgba(255,255,255,.1); border-radius:4px; padding:.15rem .6rem; font-size:.75rem;">0 baris</span>
        </div>
        <div class="pk-card-body" style="padding:0;">
            <div class="pk-table-wrap">
                <table class="pk-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Merk</th>
                            <th>Jns Kantong</th>
                            <th>Vol</th>
                            <th>Tgl Barcode</th>
                            <th>Jumlah</th>
                            <th>No Lot</th>
                            <th>NAT</th>
                            <th>UK KTG</th>
                            <th>No Kantong</th>
                            <th>Duplikat</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tbl-body">
                        <tr id="empty-row">
                            <td colspan="12" style="text-align:center; padding:2rem; color:var(--pmi-muted); font-family:var(--sans);">
                                <i class="fas fa-inbox" style="font-size:1.5rem; display:block; margin-bottom:.5rem; opacity:.4;"></i>
                                Belum ada data. Klik <strong>Run</strong> untuk generate.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>{{-- /container --}}

{{-- Hidden print area --}}
<div id="print-area"></div>
@endsection

@push('scripts')
{{-- JsBarcode CDN --}}
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ───── State ───── */
    let generatedRows = [];
    let running = false;
    let lastUsedSeq = 0;
    let lastUsedPrefix = '';

    /* ───── Helpers ───── */
    const $ = id => document.getElementById(id);
    const pad  = (n, len) => String(n).padStart(len, '0');

    /* ───── Mapping Jenis → Type (dari PHP) ───── */
    const jenisTypeMap = @json($jenis_type_map);

    /* ───── Simpan semua option type asli ───── */
    const allTypeOptions = Array.from(
        document.getElementById('type_kantong').options
    ).map(o => ({ value: o.value, text: o.text }));

    /* ───── Filter type saat jenis berubah ───── */
    document.getElementById('jenis_kantong').addEventListener('change', function () {
        const selected  = this.value.trim();
        const typeSelect = document.getElementById('type_kantong');
        const allowed   = jenisTypeMap[selected] || null;

        typeSelect.innerHTML = '<option value="">— Pilih Type —</option>';
        typeSelect.value = '';

        allTypeOptions.forEach(opt => {
            if (!opt.value) return;
            const match = !allowed || allowed.some(
                a => a.toLowerCase() === opt.value.toLowerCase()
            );
            if (match) {
                const o = document.createElement('option');
                o.value = opt.value;
                o.text  = opt.text;
                typeSelect.appendChild(o);
            }
        });

        if (typeSelect.options.length === 2) {
            typeSelect.selectedIndex = 1;
        }
    });

    function today() {
        const d = new Date();
        return {
            dd: pad(d.getDate(), 2),
            mm: pad(d.getMonth() + 1, 2),
            yyyy: d.getFullYear(),
            display: `${pad(d.getDate(),2)}-${pad(d.getMonth()+1,2)}-${d.getFullYear()} ${pad(d.getHours(),2)}:${pad(d.getMinutes(),2)}:${pad(d.getSeconds(),2)}`
        };
    }

    function makeNoKantong(yy, mm, seq) {
        return `${yy}${mm}${pad(seq, 4)}`;
    }

    function showToast(msg, isError = false) {
        const t = $('toast');
        t.innerHTML = `<i class="fas fa-${isError ? 'exclamation-circle' : 'check-circle'}"></i> ${msg}`;
        t.className = 'show' + (isError ? ' error' : '');
        setTimeout(() => t.className = '', 3000);
    }

    function setLoading(on) {
        $('loading-overlay').style.display = on ? 'flex' : 'none';
    }

    /* ───── Live total label counter ───── */
    function updateTotal() {
        const jumlah = parseInt($('jumlah_cetak').value) || 0;
        const dup    = parseInt($('duplikat').value) || 1;
        $('total-label').textContent = jumlah * dup;
    }
    $('jumlah_cetak').addEventListener('input', updateTotal);
    $('duplikat').addEventListener('input', updateTotal);
    updateTotal();

    /* ───── Cek nomor kantong yang sudah pernah dipakai di DB ───── */
    async function checkBentrok(kodeList) {
        if (kodeList.length === 0) return [];
        try {
            const res = await fetch('{{ route("gudang.pendataan_kantong.check-duplicate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ kodes: kodeList })
            });
            const data = await res.json();
            return data.bentrok || [];
        } catch (e) {
            console.error('Gagal cek bentrok:', e);
            return [];
        }
    }

    /* ───── Render table row ───── */
    function renderTable() {
        const tbody = $('tbl-body');
        if (generatedRows.length === 0) {
            tbody.innerHTML = `<tr id="empty-row"><td colspan="12" style="text-align:center;padding:2rem;color:var(--pmi-muted);font-family:var(--sans);"><i class="fas fa-inbox" style="font-size:1.5rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>Belum ada data. Klik <strong>Run</strong> untuk generate.</td></tr>`;
            $('row-count').textContent = '0 baris';
            return;
        }
        $('row-count').textContent = generatedRows.length + ' baris';
        tbody.innerHTML = generatedRows.map((r, i) => `
            <tr class="${r.isBentrok ? 'row-bentrok' : ''}">
                <td>${i + 1}</td>
                <td class="text-normal">${r.merk || '—'}</td>
                <td class="text-normal">${r.jenis}</td>
                <td>${r.vol}</td>
                <td>${r.tgl}</td>
                <td>${r.jumlah}</td>
                <td>${r.no_lot}</td>
                <td><span class="badge-nat">T</span></td>
                <td>${r.type}</td>
                <td>${r.isBentrok
                    ? `<span class="badge-bentrok" title="Nomor ini sudah ada di database"><i class="fas fa-triangle-exclamation"></i> ${r.no_kantong}</span>`
                    : `<span class="badge-no">${r.no_kantong}</span>`}</td>
                <td>${r.duplikat}</td>
                <td class="td-action">
                    <button class="btn-del" onclick="deleteRow(${i})" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    window.deleteRow = function(i) {
        generatedRows.splice(i, 1);
        renderTable();
        toggleButtons();
    };

    function toggleButtons() {
        const has        = generatedRows.length > 0;
        const hasBentrok = generatedRows.some(r => r.isBentrok);

        $('btn-save').disabled         = !has || hasBentrok;
        $('btn-print').disabled        = !has;
        $('btn-print-direct').disabled = !has;

        $('save-warning').className = hasBentrok ? 'save-warning show' : 'save-warning';
    }

    /* ───── RUN ───── */
    $('btn-run').addEventListener('click', async function () {
        const jenis  = $('jenis_kantong').value;
        const type   = $('type_kantong').value;
        const vol    = $('ukuran').value;
        const no_lot = $('no_lot').value.trim();
        const jumlah = parseInt($('jumlah_cetak').value);
        const dup    = parseInt($('duplikat').value);
        const merk   = $('merk_kantong').value;

        if (!jenis || !type || !vol || !no_lot || !jumlah) {
            showToast('Lengkapi semua field wajib!', true); return;
        }

        setLoading(true);
        running = true;
        $('btn-run').disabled  = true;

        try {
            const dt = today();
            const currentPrefix = dt.yyyy.toString().slice(2) + dt.mm; // YYMM

            let seq;
            if (lastUsedSeq > 0 && lastUsedPrefix === currentPrefix) {
                seq = lastUsedSeq + 1;
            } else {
                const res  = await fetch('{{ route("gudang.pendataan_kantong.next-seq") }}');
                const data = await res.json();
                seq = data.next_seq || 1;
            }

            for (let i = 0; i < jumlah; i++) {
                if (!running) break;
                const noKantong = makeNoKantong(currentPrefix.slice(0, 2), currentPrefix.slice(2), seq + i);
                generatedRows.push({
                    merk, jenis, type, vol,
                    tgl: dt.display,
                    jumlah,
                    no_lot,
                    no_kantong: noKantong,
                    duplikat: dup,
                    isBentrok: false,
                });
                $('no_kantong_preview').value = noKantong;
                renderTable();
                updateTotal();
                await new Promise(r => setTimeout(r, 30));
            }

            lastUsedSeq    = seq + jumlah - 1;
            lastUsedPrefix = currentPrefix;

            const batchBaru     = generatedRows.slice(-jumlah);
            const kodeBatchBaru = batchBaru.map(r => r.no_kantong);
            const bentrok       = await checkBentrok(kodeBatchBaru);

            if (bentrok.length > 0) {
                batchBaru.forEach(r => {
                    if (bentrok.includes(r.no_kantong)) r.isBentrok = true;
                });
                showToast(`⚠️ ${bentrok.length} no kantong SUDAH PERNAH dipakai: ${bentrok.join(', ')}. Hapus baris ini sebelum Simpan!`, true);
            } else {
                showToast(`${jumlah} nomor kantong berhasil ditambahkan! Total sekarang: ${generatedRows.length}`);
            }

        } catch(e) {
            showToast('Gagal generate: ' + e.message, true);
        } finally {
            setLoading(false);
            running = false;
            $('btn-run').disabled  = false;
            toggleButtons();
            renderTable();
        }
    });

    /* ───── SAVE ───── */
    $('btn-save').addEventListener('click', async function () {
        if (generatedRows.length === 0) return;

        if (generatedRows.some(r => r.isBentrok)) {
            showToast('Masih ada nomor kantong yang bentrok. Hapus dulu sebelum Simpan!', true);
            return;
        }

        setLoading(true);
        try {
            const res = await fetch('{{ route("gudang.pendataan_kantong.store-batch") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ rows: generatedRows })
            });
            const data = await res.json();
            if (data.success) {
                showToast(`${data.saved} data berhasil disimpan!`);
                generatedRows  = [];
                lastUsedSeq    = 0;
                lastUsedPrefix = '';
                renderTable();
                toggleButtons();
            } else {
                if (data.message) {
                    const match = data.message.match(/:\s*(.+)$/);
                    if (match) {
                        const kodeBentrok = match[1].split(',').map(s => s.trim());
                        generatedRows.forEach(r => {
                            if (kodeBentrok.includes(r.no_kantong)) r.isBentrok = true;
                        });
                        renderTable();
                        toggleButtons();
                    }
                }
                showToast(data.message || 'Gagal menyimpan.', true);
            }
        } catch(e) {
            showToast('Error: ' + e.message, true);
        } finally {
            setLoading(false);
        }
    });

    /* ════════════════════════════════════════════════════════
       BUILD PRINT AREA — SATU SUMBER KEBENARAN UNTUK SEMUA PRINT

       FIX GARIS PUTUS (v3): dua percobaan sebelumnya (absolute bottom:0,
       lalu flex:1+center via .label-gap) salah asumsi soal SEBERAPA JAUH
       garis dari konten. Sesuai foto referensi fisik, garis putus harus
       RAPAT langsung di bawah footer (.label-wrap) dengan jarak kecil &
       fixed (CSS margin-top pada .label-cut) -- bukan di tengah ruang
       kosong besar. .label-gap dihapus; .label-cut sekarang ditempel
       LANGSUNG sebagai child .label-page, persis setelah .label-wrap.
       ════════════════════════════════════════════════════════ */
    function buildPrintArea() {
        const area = $('print-area');
        area.innerHTML = '';

        generatedRows.forEach((row, rowIndex) => {
            const isLastRow = rowIndex === generatedRows.length - 1;

            for (let d = 0; d < row.duplikat; d++) {
                const isLastDuplikat = d === row.duplikat - 1;

                // Pembungkus luar — flex column, pemegang page-break.
                const page = document.createElement('div');
                page.className = 'label-page';

                // Konten label asli (title + barcode + footer).
                const wrap = document.createElement('div');
                wrap.className = 'label-wrap';

                const title = document.createElement('div');
                title.className = 'label-title';
                title.textContent = 'UDD PMI KOTA SBY';
                wrap.appendChild(title);

                const barcodeDiv = document.createElement('div');
                barcodeDiv.className = 'label-barcode';
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                barcodeDiv.appendChild(svg);
                wrap.appendChild(barcodeDiv);

                const foot = document.createElement('div');
                foot.className = 'label-foot';
                foot.innerHTML = `<span class="left">${row.no_kantong}</span><span class="right">${row.type}</span>`;
                wrap.appendChild(foot);

                page.appendChild(wrap);

                // Garis putus ditempel LANGSUNG sebagai child .label-page,
                // tepat setelah .label-wrap -- jarak kecil & fixed dari
                // CSS margin-top, bukan di-center di ruang kosong besar.
                // HANYA disisipkan setelah duplikat TERAKHIR untuk 1
                // no_kantong, dan TIDAK pada label paling akhir di
                // seluruh batch.
                if (isLastDuplikat && !isLastRow) {
                    const cut = document.createElement('div');
                    cut.className = 'label-cut';
                    page.appendChild(cut);
                }

                area.appendChild(page);

                JsBarcode(svg, row.no_kantong, {
                    format: 'CODE128',
                    width: 1.6,
                    height: 28,
                    displayValue: false,
                    margin: 0,
                    lineColor: '#000'
                });
            }
        });

        // Matikan break-after pada kertas paling akhir, supaya tidak
        // nyangkut 1 halaman kosong di ujung dokumen.
        const allPages = area.querySelectorAll('.label-page');
        if (allPages.length > 0) {
            const lastPage = allPages[allPages.length - 1];
            lastPage.style.pageBreakAfter = 'avoid';
            lastPage.style.breakAfter = 'avoid';
        }
    }

    /* ───── PREVIEW / PRINT (browser print dialog) ───── */
    $('btn-print').addEventListener('click', function () {
        if (generatedRows.length === 0) return;
        buildPrintArea();
        $('print-area').style.display = 'block';
        setTimeout(() => window.print(), 150);
    });

    /* ───── PRINT SATO ───── */
    $('btn-print-direct').addEventListener('click', function () {
        if (generatedRows.length === 0) {
            showToast("Tidak ada data untuk dicetak", true);
            return;
        }
        buildPrintArea();
        $('print-area').style.display = 'block';
        setTimeout(() => window.print(), 150);
    });

    /* ───── After print cleanup ───── */
    window.addEventListener('afterprint', function () {
        $('print-area').style.display = 'none';
    });

});
</script>
@endpush