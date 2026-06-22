@extends('layouts.index')

@push('styles')
<style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',Tahoma,sans-serif;font-size:12px;background:#c8d4e0;color:#1e2533}

    .cm-titlebar{background:#1a3a6b;color:#fff;padding:3px 10px;display:flex;align-items:center;justify-content:space-between}
    .cm-titlebar span{font-size:11px;font-weight:600;letter-spacing:.03em}
    .badge-live{font-size:10px;background:#c0392b;padding:1px 7px;border-radius:10px;letter-spacing:.05em}

    .cm-toolbar{background:#e0e4ec;border-bottom:2px solid #a0a8c0;padding:3px 6px;display:flex;gap:3px;align-items:center}
    .btn-tb{background:linear-gradient(to bottom,#f5f5f5,#d8d8d8);border:1px solid #999;border-radius:2px;
        padding:3px 10px;font-size:11px;cursor:pointer;display:flex;flex-direction:column;align-items:center;
        gap:1px;min-width:60px;color:#222;line-height:1.2;transition:background .1s}
    .btn-tb i{font-size:15px}
    .btn-tb:hover{background:linear-gradient(to bottom,#dde8ff,#b8c8f0)}
    .btn-tb:active{background:#c8d4e8}
    .tb-sep{width:1px;height:40px;background:#aaa;margin:0 3px}

    .cm-scanbar{background:#c8d4e4;border-bottom:1px solid #a0a8c0;padding:4px 10px;display:flex;align-items:center;gap:6px;flex-wrap:wrap}
    .cm-scanbar label{font-size:11px;font-weight:700;color:#1a3a6b;white-space:nowrap}
    .cm-scanbar .si{border:1px solid #888;padding:2px 5px;font-size:11px;background:#fff;border-radius:1px;height:22px;outline:none}
    .cm-scanbar .si:focus{border-color:#1a3a6b;box-shadow:0 0 0 1px #1a3a6b}
    .btn-scan{background:linear-gradient(to bottom,#e8e8e8,#c8c8c8);border:1px solid #888;border-radius:2px;
        padding:2px 10px;font-size:11px;cursor:pointer;height:22px}
    .btn-scan:hover{background:linear-gradient(to bottom,#dde8ff,#b8c8f0)}
    .val-fpup{font-size:11px;font-weight:700;color:#1a3a6b;min-width:90px}

    .cm-main{display:grid;grid-template-columns:235px 1fr;height:calc(100vh - 115px);overflow:hidden}

    /* LEFT */
    .left-panel{background:#f0f4f8;border-right:2px solid #a0a8c0;overflow-y:auto;display:flex;flex-direction:column}
    .sect-head{background:#1a3a6b;color:#fff;padding:3px 7px;font-size:10.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}
    .field-grp{display:grid;grid-template-columns:90px 1fr;border-bottom:1px dotted #c8d0dc}
    .field-grp .lbl{background:#dde4ee;padding:3px 6px;font-size:10px;color:#4a5a70;border-right:1px solid #c8d0dc;
        display:flex;align-items:center;font-weight:600;text-transform:uppercase;letter-spacing:.03em;line-height:1.3}
    .field-grp .val{padding:3px 6px;font-size:11px;color:#1e2533;display:flex;align-items:center;background:#fff;flex-wrap:wrap;gap:2px}
    .field-grp .val.bold{font-weight:700;color:#1a3a6b}
    .gol-badge{background:#c0392b;color:#fff;font-weight:700;padding:1px 6px;border-radius:2px;font-size:11px}
    .check-row{padding:3px 7px;display:flex;align-items:center;gap:5px;font-size:11px;background:#f8fafc;border-bottom:1px dotted #c8d0dc}

    /* Scan no stok inline */
    .scan-stock-bar{background:#d8e8f8;border-bottom:1px solid #a0b8d0;padding:3px 8px;display:flex;align-items:center;gap:5px}
    .scan-stock-bar label{font-size:10.5px;font-weight:700;color:#1a3a6b;white-space:nowrap}
    .scan-stock-bar .ssi{border:1px solid #888;padding:2px 4px;font-size:11px;background:#fff;border-radius:1px;height:21px;outline:none;width:130px;font-family:monospace;font-weight:700}
    .scan-stock-bar .ssi:focus{border-color:#1a3a6b}
    .btn-scan-stok{background:linear-gradient(to bottom,#e8e8e8,#c8c8c8);border:1px solid #888;border-radius:2px;padding:1px 8px;font-size:11px;cursor:pointer;height:21px}
    .btn-scan-stok:hover{background:linear-gradient(to bottom,#dde8ff,#b8c8f0)}
    .stok-found-bar{background:#d4f5e5;border:1px solid #9fe0c0;border-radius:2px;padding:2px 6px;font-size:10.5px;color:#1a7f4b;display:none;align-items:center;gap:4px;margin-top:2px}
    .stok-found-bar.show{display:flex}
    .stok-warn-bar{background:#fef9e7;border:1px solid #f0d888;border-radius:2px;padding:2px 6px;font-size:10.5px;color:#7f6d22;display:none;align-items:center;gap:4px;margin-top:2px}
    .stok-warn-bar.show{display:flex}

    /* Petugas scan inline */
    .scan-ptgs-bar{background:#f0f4f8;border-bottom:1px dotted #c8d0dc;display:grid;grid-template-columns:90px 1fr;align-items:center}
    .scan-ptgs-bar .lbl{background:#dde4ee;padding:3px 6px;font-size:10px;color:#4a5a70;border-right:1px solid #c8d0dc;font-weight:600;text-transform:uppercase;letter-spacing:.03em;height:100%;display:flex;align-items:center}
    .scan-ptgs-bar .ptgs-inp-wrap{padding:2px 5px;background:#fff;display:flex;align-items:center;gap:3px}
    .ptgs-inp{border:1px solid #aaa;padding:1px 4px;font-size:11px;background:#fff;border-radius:1px;height:19px;outline:none;width:60px}
    .ptgs-inp:focus{border-color:#1a3a6b}
    .ptgs-nama{font-size:11px;font-weight:700;color:#1a3a6b;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

    /* RIGHT */
    .right-panel{display:flex;flex-direction:column;overflow:hidden;background:#f0f4f8}
    .right-top{display:grid;grid-template-columns:1fr 200px;border-bottom:2px solid #a0a8c0;background:#fff}
    .perm-wrap{padding:4px 6px}
    .perm-sect-head{font-size:10.5px;font-weight:700;color:#1a3a6b;margin-bottom:3px;border-bottom:1px solid #c8d4e4;padding-bottom:2px;text-transform:uppercase;letter-spacing:.04em}
    .perm-table{width:100%;border-collapse:collapse;font-size:11px}
    .perm-table th{background:#1a3a6b;color:#c8d8f0;padding:2px 6px;font-size:10px;font-weight:600;text-align:left;border:1px solid #2a4a7b;text-transform:uppercase;letter-spacing:.04em}
    .perm-table td{padding:2px 6px;border:1px solid #d0d8e8;vertical-align:middle}
    .perm-table tr.perm-sel td{background:#fff3b0}
    .perm-table tr:hover td{background:#e8f0ff}

    .kurir-wrap{padding:5px 8px;border-left:1px solid #c8d0dc;background:#f8fafc}
    .kurir-title{font-size:10.5px;font-weight:700;color:#1a3a6b;margin-bottom:5px}
    .si-row{display:flex;flex-direction:column;margin-bottom:5px}
    .si-lbl{font-size:10px;color:#6a7a90}
    .si-val{border:1px solid #bbb;padding:2px 4px;background:#fff9e6;font-size:11px;border-radius:1px;min-height:18px}

    .jml-bar{background:#d0d8e8;border-top:1px solid #a0a8c0;border-bottom:1px solid #a0a8c0;padding:3px 8px;display:flex;align-items:center;gap:8px}
    .jml-bar label{font-size:11px;color:#344}
    .jml-badge{background:#1a3a6b;color:#fff;padding:1px 10px;border-radius:2px;font-size:11px;font-weight:700;min-width:50px;text-align:center;transition:background .2s}

    /* Cross match table */
    .ct-wrap{flex:1;overflow-y:auto;background:#fff}
    .ct-table{width:100%;border-collapse:collapse;font-size:11px;table-layout:fixed}
    .ct-table thead tr{background:#1a3a6b;position:sticky;top:0;z-index:1}
    .ct-table th{color:#c8d8f0;padding:3px 5px;font-size:10px;font-weight:600;text-align:left;border:1px solid #2a4a7b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .ct-table td{padding:2px 5px;border:1px solid #d8e0ec;vertical-align:middle;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .ct-table tbody tr:hover td{background:#e8f0ff}
    .ct-table tbody tr.ct-sel td{background:#fff3b0}
    .ct-table .no-stok{font-family:monospace;font-weight:700;color:#1a3a6b;font-size:11px}
    .ct-table .empty-row td{text-align:center;padding:24px 10px;color:#9aacbf;font-size:12px}

    .bd{display:inline-block;padding:1px 5px;border-radius:2px;font-size:10px;font-weight:700;text-transform:uppercase}
    .bd-compat  {background:#d4f5e5;color:#1a7f4b}
    .bd-incompat{background:#fde8e8;color:#c0392b}
    .bd-pending {background:#fef9e7;color:#7f6d22}
    .bd-proses  {background:#e8f0fe;color:#1a56db}
    .bd-selesai {background:#e8f5f0;color:#1a7f4b}
    .hs-ok {color:#1a7f4b;font-weight:700}
    .hs-err{color:#c0392b;font-weight:700}
    .hs-na {color:#bbb}

    .btn-act{background:#f0f4f8;border:1px solid #b8c8d8;padding:1px 5px;border-radius:2px;cursor:pointer;font-size:11px;color:#4a5a70;transition:all .1s;line-height:1.4}
    .btn-act:hover{background:#dde8ff;border-color:#88a;color:#1a3a6b}
    .btn-del:hover{background:#fde8e8;border-color:#f99;color:#c0392b}

    /* Modal edit */
    .cm-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:center;justify-content:center}
    .cm-modal.open{display:flex}
    .cm-modal-box{background:#f0f4f8;border:2px solid #1a3a6b;border-radius:3px;width:520px;max-width:96vw;overflow:hidden;box-shadow:4px 4px 12px rgba(0,0,0,.3)}
    .cm-modal-head{background:#1a3a6b;color:#fff;padding:5px 10px;font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:space-between}
    .btn-close-modal{background:none;border:none;color:#fff;cursor:pointer;font-size:18px;line-height:1;padding:0 2px}
    .cm-modal-body{padding:10px 14px;display:flex;flex-direction:column;gap:6px}
    .form-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px}
    .form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:6px}
    .form-grp{display:flex;flex-direction:column;gap:2px}
    .form-grp label{font-size:10px;font-weight:700;color:#4a5a70;text-transform:uppercase;letter-spacing:.04em}
    .form-grp select,
    .form-grp input[type=text],
    .form-grp input[type=date]{border:1px solid #a0aab8;padding:2px 5px;font-size:11px;border-radius:2px;background:#fff;height:23px;outline:none}
    .form-grp select:focus,
    .form-grp input:focus{border-color:#1a3a6b;box-shadow:0 0 0 1px #1a3a6b}
    .cm-modal-foot{padding:6px 10px;border-top:1px solid #c0c8d8;display:flex;gap:6px;justify-content:flex-end;background:#e8edf5}
    .btn-danger{background:#c0392b;color:#fff;border:none;padding:4px 14px;border-radius:2px;font-size:11px;cursor:pointer}
    .btn-danger:hover{background:#a93226}
    .btn-sec{background:#e0e4ec;color:#333;border:1px solid #a0a8b8;padding:4px 12px;border-radius:2px;font-size:11px;cursor:pointer}

    #cmAlert{position:fixed;top:10px;right:10px;z-index:999;min-width:260px;display:none;padding:7px 14px;border-radius:3px;font-size:11px;border:1px solid transparent}
    .al-success{background:#d4f5e5;color:#1a7f4b;border-color:#9fe0c0}
    .al-danger {background:#fde8e8;color:#c0392b;border-color:#f5b8b8}
    .al-warning{background:#fef9e7;color:#7f6d22;border-color:#f0d888}

    .spinner{display:inline-block;width:12px;height:12px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle}
    @keyframes spin{to{transform:rotate(360deg)}}
    ::-webkit-scrollbar{width:5px;height:5px}
    ::-webkit-scrollbar-thumb{background:#b0bcd0;border-radius:3px}
</style>
@endpush

@section('content')
<div style="height:100vh;display:flex;flex-direction:column;overflow:hidden">

    {{-- Titlebar --}}
    <div class="cm-titlebar">
        <span><i class="bi bi-droplet-half me-1"></i> CROSS MATCH — Lembar Kerja Pelayanan Darah | BANK DARAH UTD</span>
        <span class="badge-live">● LIVE</span>
    </div>

    {{-- Toolbar --}}
    <div class="cm-toolbar">
        <button class="btn-tb" id="btnSimpan"><i class="bi bi-floppy"></i>Simpan (F8)</button>
        <div class="tb-sep"></div>
        <button class="btn-tb"><i class="bi bi-file-earmark-excel"></i>Excel (F6)</button>
        <button class="btn-tb" id="btnCetak"><i class="bi bi-printer"></i>Cetak (F10)</button>
        <div class="tb-sep"></div>
        <button class="btn-tb" id="btnFpup"><i class="bi bi-search"></i>Cari (F4)</button>
    </div>

    {{-- Scan FPUP bar --}}
    <div class="cm-scanbar">
        <label>NO FPUP</label>
        <input type="text" id="inputNoFpup" class="si" style="width:140px" placeholder="Scan / ketik No. FPUP…" autocomplete="off">
        <button class="btn-scan" id="btnScan">
            <span id="scanSpinner" style="display:none"><span class="spinner"></span></span>
            <i class="bi bi-upc-scan" id="scanIcon"></i>
            Cari (F4)
        </button>
        <label style="margin-left:8px">TGL FPUP</label>
        <span class="val-fpup" id="tglFpup">—</span>
        <label style="margin-left:8px">No. Form / No. Reg</label>
        <span class="val-fpup" id="noReg">—</span>
    </div>

    {{-- Main grid --}}
    <div class="cm-main">

        {{-- ── LEFT PANEL ── --}}
        <div class="left-panel">
            <div class="sect-head">Status Stock Dari Penyimpanan</div>

            <div class="field-grp">
                <span class="lbl">Rumah Sakit</span>
                <span class="val" id="namaRs">—</span>
            </div>
            <div class="field-grp">
                <span class="lbl">Bagian RS</span>
                <span class="val" id="bagianRs">—</span>
            </div>
            {{-- Kelas RS: tampil kelas + nama RS --}}
            <div class="field-grp">
                <span class="lbl">Kelas RS</span>
                <span class="val" id="kelasRawat">—</span>
            </div>
            <div class="field-grp">
                <span class="lbl">Nama Dokter</span>
                <span class="val" id="namaDokter">—</span>
            </div>
            <div class="field-grp">
                <span class="lbl">Nama Pasien</span>
                <span class="val bold" id="namaPasien">—</span>
            </div>
            {{-- Gol/Rh ambil dari detail permintaan --}}
            <div class="field-grp">
                <span class="lbl">Gol-Rh Pasien</span>
                <span class="val" id="golRhPasien">—</span>
            </div>
            <div class="field-grp">
                <span class="lbl">Diagnosa</span>
                <span class="val" id="diagnosa">—</span>
            </div>
            <div class="field-grp">
                <span class="lbl">Cara Bayar</span>
                <span class="val" id="caraBayar">—</span>
            </div>

            <div style="height:4px;background:#e8edf5"></div>

            {{-- Petugas Pemeriksa: kode + nama --}}
            <div class="scan-ptgs-bar">
                <span class="lbl">Petugas Prks</span>
                <div class="ptgs-inp-wrap">
                    <input type="text" id="inputKodePtgs" class="ptgs-inp" placeholder="Kode" autocomplete="off" title="Ketik kode petugas lalu Enter">
                    <span class="ptgs-nama" id="namaPtgs">—</span>
                </div>
            </div>

            <div class="check-row">
                <input type="checkbox" id="chkBarcode">
                <label for="chkBarcode" style="cursor:pointer">Cetak Barcode</label>
            </div>

            {{-- Pasien Referral checklist --}}
            <div class="check-row">
                <input type="checkbox" id="chkReferal" >
                <label for="chkReferal" style="cursor:default;color:#c0392b;font-weight:700">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Pasien REFERRAL
                </label>
            </div>

            <div style="height:6px;background:#e8edf5"></div>

            {{-- Scan No. Stock langsung di panel kiri --}}
            <div class="sect-head" style="background:#2a5090">Scan No. Stock Kantong</div>
            <div style="padding:5px 7px;background:#e8f0f8">
                <div class="scan-stock-bar" style="background:transparent;border:none;padding:0;gap:4px">
                    <label style="font-size:10.5px;font-weight:700;color:#1a3a6b;white-space:nowrap">No. Stock</label>
                    <input type="text" id="inputNoStock" class="ssi" placeholder="Scan / ketik…" autocomplete="off">
                    <button class="btn-scan-stok" id="btnScanStock">
                        <i class="bi bi-upc-scan"></i>
                    </button>
                </div>
                <div class="stok-found-bar" id="stokFoundBar">
                    <i class="bi bi-check-circle-fill"></i>
                    <span id="stokFoundText"></span>
                </div>
                <div class="stok-warn-bar" id="stokWarnBar">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span id="stokWarnText"></span>
                </div>
            </div>

        </div>

        {{-- ── RIGHT PANEL ── --}}
        <div class="right-panel">

            {{-- Permintaan + kurir --}}
            <div class="right-top">
                <div class="perm-wrap">
                    <div class="perm-sect-head">Data Permintaan</div>
                    <table class="perm-table">
                        <thead>
                            <tr>
                                <th style="width:24px">No</th>
                                <th>Jenis Darah</th>
                                <th style="width:38px">Gol</th>
                                <th style="width:60px">Rhesus</th>
                                <th style="width:50px">Jumlah</th>
                                <th style="width:90px">Tgl Perlu</th>
                            </tr>
                        </thead>
                        <tbody id="permBody">
                            <tr>
                                <td colspan="6" style="text-align:center;color:#9aacbf;padding:6px;font-size:11px">Belum ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="kurir-wrap">
                    <div class="kurir-title">Kurir Ptgs Online</div>
                    <div class="si-row">
                        <span class="si-lbl">No Registrasi Online</span>
                        <span class="si-val" id="noRegOnline"></span>
                    </div>
                    <div class="si-row">
                        <span class="si-lbl">Tgl Registrasi Online</span>
                        <span class="si-val" id="tglRegOnline"></span>
                    </div>
                </div>
            </div>

            {{-- Jumlah minta bar --}}
            <div class="jml-bar">
                <label>Jumlah Discan / Diminta</label>
                <span class="jml-badge" id="jumlahMinta">0 / 0</span>
                <span style="font-size:10.5px;color:#6a7a90;margin-left:6px" id="tglPerluInfo"></span>
            </div>

            {{-- Cross match table --}}
            <div class="ct-wrap">
                <table class="ct-table">
                    <thead>
                        <tr>
                            <th style="width:26px">No</th>
                            <th style="width:110px">No Stock</th>
                            <th style="width:55px">Jenis</th>
                            <th style="width:52px">Gol/Rh</th>
                            <th style="width:80px">Status</th>
                            <th style="width:82px">Tgl Aftap</th>
                            <th style="width:86px">Tgl Produksi</th>
                            <th style="width:82px">Kadaluarsa</th>
                            <th style="width:90px">Pemeriksa</th>
                            <th style="width:52px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="ctTableBody">
                        <tr class="empty-row">
                            <td colspan="10">
                                <i class="bi bi-upc-scan" style="font-size:1.6rem;display:block;margin-bottom:5px"></i>
                                Scan No. FPUP lalu scan No. Stock kantong untuk menambah data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modalCari" class="modal-search" style="display:none">
    <div class="modal-search-content">

        <div class="modal-header">
            Cari Data Cross Match
        </div>

        <input
            type="text"
            id="txtCari"
            class="form-control"
            placeholder="No FPUP / Nama Pasien / No Stock">

        <table class="table table-sm mt-2">
            <thead>
                <tr>
                    <th>No FPUP</th>
                    <th>Pasien</th>
                    <th>No Stock</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tblCariBody"></tbody>
        </table>

    </div>
</div>
{{-- ── Modal Edit ── --}}
<div class="cm-modal" id="cmModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="cm-modal-box">
        <div class="cm-modal-head">
            <span id="modalTitle"><i class="bi bi-pencil-fill me-1"></i> Edit Data Cross Match</span>
            <button class="btn-close-modal" id="btnCloseModal" aria-label="Tutup">&times;</button>
        </div>
        <div class="cm-modal-body">
            <div class="form-row-2">
                <div class="form-grp">
                    <label>No. Stock</label>
                    <input type="text" id="fNoStock" readonly style="background:#f5f5e8;color:#666;font-family:monospace;font-weight:700">
                </div>
                <div class="form-grp">
                    <label>Tgl. Ambil</label>
                    <input type="date" id="fTglAmbil">
                </div>
            </div>
            <div class="form-row">
                <div class="form-grp">
                    <label>Jenis Darah</label>
                    <select id="fJnsDarah">
                        <option value="">— Pilih —</option>
                        <option>WB</option><option>PRC</option><option>FFP</option>
                        <option>TC</option><option>Washed RBC</option><option>LP</option>
                    </select>
                </div>
                <div class="form-grp">
                    <label>Gol. Darah / Rh</label>
                    <select id="fGolRhKantong">
                        <option value="">— Pilih —</option>
                        @foreach(['O+','O-','A+','A-','B+','B-','AB+','AB-'] as $gol)
                            <option>{{ $gol }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grp">
                    <label>Status Stok</label>
                    <input type="text" id="fStatusStok" readonly style="background:#f5f5e8;color:#666">
                </div>
            </div>
            <div class="form-row">
                <div class="form-grp">
                    <label>Tgl. Produksi</label>
                    <input type="date" id="fTglProduksi">
                </div>
                <div class="form-grp">
                    <label>Tgl. Kadaluarsa</label>
                    <input type="date" id="fTglKadaluarsa">
                </div>
                <div class="form-grp">
                    <label>Tgl. Aftap</label>
                    <input type="date" id="fTglAftap">
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-grp">
                    <label>Pemeriksa</label>
                    <input type="text" id="fPemeriksa" readonly style="background:#f5f5e8;color:#444">
                </div>
                <div class="form-grp">
                    <label>Catatan</label>
                    <input type="text" id="fCatatan" placeholder="Catatan tambahan...">
                </div>
            </div>
        </div>
        <div class="cm-modal-foot">
            <button class="btn-sec" id="btnBatalModal">Batal</button>
            <button class="btn-danger" id="btnUpdateKantong">
                <i class="bi bi-floppy me-1"></i>Update Data
            </button>
        </div>
    </div>
</div>

<div id="cmAlert"></div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

let currentFpupId    = null;
let currentNoFpup    = null;
let currentPtgsId    = null;
let currentPtgsNama  = null;
let editingCtId      = null;
let tempRows = [];
// Counter Jumlah Minta vs Jumlah sudah di-scan
let totalJumlahMinta = 0;
let jumlahScan       = 0;

async function bukaCariData(){

    document.getElementById('modalCari').style.display='block';

    const res = await fetch('/cross_test/search');

    const data = await res.json();

    renderCari(data.data);
}
function renderCari(rows){

    const body = document.getElementById('tblCariBody');

    body.innerHTML = rows.map(r=>`

        <tr>
            <td>${r.no_fpup}</td>
            <td>${r.nama_pasien}</td>
            <td>${r.no_stock ?? '-'}</td>
            <td>${r.status}</td>

            <td>
                <button
                    class="btn btn-primary btn-sm"
                    onclick="editCrossTest('${r.no_fpup}')">

                    Pilih
                </button>
            </td>
        </tr>

    `).join('');
}
async function editCrossTest(noFpup){

    document.getElementById('modalCari').style.display='none';

    document.getElementById('txtNoFpup').value = noFpup;

    await scanFpup();
}

// ── Helpers ────────────────────────────────────────────────────
function showAlert(msg, type = 'success') {
    const b = document.getElementById('cmAlert');
    b.className = 'al-' + type;
    b.style.cssText = 'position:fixed;top:10px;right:10px;z-index:999;min-width:260px;padding:7px 14px;border-radius:3px;font-size:11px;border:1px solid transparent;display:block';
    b.innerHTML = msg;
    clearTimeout(b._t);
    b._t = setTimeout(() => b.style.display = 'none', 3500);
}

function fmtDate(d) {
    if (!d) return '—';
    try { return new Date(d).toLocaleDateString('id-ID', { day:'2-digit', month:'2-digit', year:'numeric' }); }
    catch { return d; }
}

function isoDate(d) {
    if (!d) return '';
    return typeof d === 'string' ? d.substring(0, 10) : '';
}

function setVal(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val || '';
}

// Ambil nilai pertama yang ada (tidak null/undefined/'') dari beberapa
// kemungkinan nama kolom — supaya tahan terhadap perbedaan nama field backend.
function pick(obj, keys, def = '') {
    if (!obj) return def;
    for (const k of keys) {
        const v = obj[k];
        if (v !== undefined && v !== null && v !== '') return v;
    }
    return def;
}

function statusBadge(st,isTemp=false){

    if(isTemp){
        return '<span class="bd bd-pending">BELUM DISIMPAN</span>';
    }

    const m = {
        compatible:'bd-compat',
        incompatible:'bd-incompat',
        pending:'bd-pending',
        proses:'bd-proses',
        selesai:'bd-selesai'
    };

    return `<span class="bd ${m[st] || 'bd-pending'}">${st}</span>`;
}

// ── Update badge Jumlah Discan / Diminta ───────────────────────
function updateJumlahBadge() {
    const badge = document.getElementById('jumlahMinta');
    badge.textContent = `${jumlahScan} / ${totalJumlahMinta}`;
    if (totalJumlahMinta > 0 && jumlahScan >= totalJumlahMinta) {
        badge.style.background = '#1a7f4b'; // hijau jika sudah cukup
    } else if (jumlahScan > totalJumlahMinta) {
        badge.style.background = '#c0392b'; // merah jika lebih
    } else {
        badge.style.background = '#1a3a6b'; // default navy
    }
}

// ── Scan FPUP ─────────────────────────────────────────────────
async function scanFpup() {
    const no = document.getElementById('inputNoFpup').value.trim();
    if (!no) return;

    document.getElementById('scanSpinner').style.display = 'inline';
    document.getElementById('scanIcon').style.display    = 'none';

    try {
        const res  = await fetch('/crossmatch/cross_test/scan', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ no_fpup: no }),
        });
        const data = await res.json();
        if(!res.ok || !data.success){

            Swal.fire({
                icon:'error',
                title:'FPUP Tidak Ditemukan',
                text:data.message || 'Data FPUP tidak ditemukan'
            });

            clearFpupForm();

            return;
        }
        if (!data.success) {
            showAlert('<i class="bi bi-exclamation-circle me-1"></i>' + data.message, 'danger');
            return;
        }

        fillInfo(data.fpup);
        renderTable(data.cross_tests);

        setTimeout(() => document.getElementById('inputNoStock').focus(), 200);
    } catch (e) {
        showAlert('<i class="bi bi-wifi-off me-1"></i>Gagal terhubung ke server.', 'danger');
    } finally {
        document.getElementById('scanSpinner').style.display = 'none';
        document.getElementById('scanIcon').style.display    = 'inline';
    }
}
function clearFpupForm(){

    $('#namaPasien').text('-');
    $('#golRhPasien').text('-');
    $('#namaDokter').text('-');
    $('#diagnosa').text('-');
    $('#caraBayar').text('-');

    $('#tbodyPermintaan').html('');
    $('#tbodyCrossTest').html('');

    $('#jumlahScan').text('0');
    $('#jumlahMinta').text('0');
}
// ── Fill info panel (Status Stock & Data Permintaan) ───────────
function fillInfo(fpup) {
    currentFpupId = fpup.id;
    currentNoFpup = fpup.no_fpup;

    document.getElementById('tglFpup').textContent  = fmtDate(pick(fpup, ['tgl_minta','tgl_fpup','created_at']));
    document.getElementById('noReg').textContent    = pick(fpup, ['no_reg','no_registrasi','no_form'], '—');
    document.getElementById('namaRs').textContent   = pick(fpup, ['nama_rs','rumah_sakit'], '—');
    document.getElementById('bagianRs').textContent = pick(fpup, ['bagian','bagian_rs'], '—');
    document.getElementById('namaDokter').textContent = pick(fpup, ['nama_dokter','dokter'], '—');
    document.getElementById('namaPasien').textContent = pick(fpup, ['nama_pasien','pasien'], '—');
    document.getElementById('diagnosa').textContent   = pick(fpup, ['diagnosa_klinis','diagnosa'], '—');
    const caraBayar = fpup.cara_pembayaran || '';
    const jnsBiaya  = fpup.jns_biaya || '';

    document.getElementById('caraBayar').textContent =
    [caraBayar, jnsBiaya].filter(Boolean).join(' / ') || '—';
    // Kelas RS: kelas + nama RS
    const kelas  = pick(fpup, ['kelas_rawat','kelas_rs','kelas'], '');
    const rsNama = pick(fpup, ['nama_rs','rumah_sakit'], '');
    document.getElementById('kelasRawat').textContent = kelas
        ? (rsNama ? `${kelas} — ${rsNama}` : kelas)
        : (rsNama || '—');

    // Pasien referral → checklist
    document.getElementById('chkReferal').disabled = false;
    document.getElementById('chkReferal').checked =
    Number(fpup.pasien_referal) === 1;
    // ── Data Permintaan ──
    // Ambil array detail dari berbagai kemungkinan nama relasi
    const details = fpup.details
        || fpup.detail
        || fpup.permintaan_fpup_detail
        || fpup.fpup_detail
        || [];

    const pb = document.getElementById('permBody');

    if (details && details.length) {
        pb.innerHTML = details.map((d, i) => {
            const jns    = pick(d, ['jns_darah','jenis_darah'], '—');
            const gol    = pick(d, ['gol_darah','golongan_darah','gol'], '');
            const rhesus = pick(d, ['rhesus','rh'], '');
            const jumlah = parseInt(pick(d, ['jumlah','jml','qty'], 0)) || 0;
            const tglPerlu = pick(d, ['tgl_perlu','tanggal_perlu','tgl_butuh'], null);

            return `
                <tr class="${i === 0 ? 'perm-sel' : ''}">
                    <td style="text-align:center">${i+1}</td>
                    <td><strong>${jns}</strong></td>
                    <td>${gol || '—'}</td>
                    <td>${rhesus || '—'}</td>
                    <td style="text-align:center">${jumlah}</td>
                    <td>${fmtDate(tglPerlu)}</td>
                </tr>`;
        }).join('');

        // Total jumlah minta dari semua baris detail
        totalJumlahMinta = details.reduce((s, d) => s + (parseInt(pick(d, ['jumlah','jml','qty'], 0)) || 0), 0);

        // Gol/Rh pasien diambil dari baris detail pertama
        const d0  = details[0];
        const g   = (pick(d0, ['gol_darah','golongan_darah','gol'], '')).toString().trim();
        let   r   = (pick(d0, ['rhesus','rh'], '')).toString().trim();
        if (/positif/i.test(r))      r = '+';
        else if (/negatif/i.test(r)) r = '-';
        const golRh = (g || r) ? `${g}${r}` : '';

        document.getElementById('golRhPasien').innerHTML = golRh
            ? `<span class="gol-badge">${golRh}</span>`
            : '—';

        // Info tanggal perlu (dari detail pertama)
        const tglPerlu0 = pick(d0, ['tgl_perlu','tanggal_perlu','tgl_butuh'], null);
        document.getElementById('tglPerluInfo').textContent = tglPerlu0
            ? `Diperlukan: ${fmtDate(tglPerlu0)}`
            : '';
    } else {
        pb.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#9aacbf;padding:6px">Tidak ada detail permintaan</td></tr>';
        totalJumlahMinta = 0;
        document.getElementById('golRhPasien').textContent = '—';
        document.getElementById('tglPerluInfo').textContent = '';
    }

    // Update badge (jumlahScan akan di-set ulang oleh renderTable setelah ini)
    updateJumlahBadge();
}

// ── Scan No Stock → cek data lalu simpan ke tabel ──────────────
async function scanNoStock() {
    const no = document.getElementById('inputNoStock').value.trim();
    if (!no) return;
    if (!currentFpupId) { showAlert('Scan No. FPUP terlebih dahulu.', 'warning'); return; }

    document.getElementById('stokFoundBar').classList.remove('show');
    document.getElementById('stokWarnBar').classList.remove('show');

    try {
        const res  = await fetch('/crossmatch/cross_test/scan_stock', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ no_stock: no }),
        });
        const data = await res.json();
        if(res.status === 404){
            Swal.fire({
                icon:'warning',
                title:'No Stok Tidak Ditemukan',
                text:'Nomor Stok tidak ada dalam database.'
            });

            clearFpupForm();

            return;
        }
        if (!data.success) {
            document.getElementById('stokWarnBar').classList.add('show');
            document.getElementById('stokWarnText').textContent = data.message || 'No. Stock tidak ditemukan.';
            return;
        }

        const s = data.stok;
        const exp = s.tgl_kadaluarsa && new Date(s.tgl_kadaluarsa) < new Date();

        if (exp) {
            document.getElementById('stokWarnBar').classList.add('show');
            document.getElementById('stokWarnText').textContent =
                `KADALUARSA! ${s.jns_darah || ''} ${s.gol_rh_kantong || ''} — Exp: ${fmtDate(s.tgl_kadaluarsa)}`;
        } else if (s.status_stok && s.status_stok !== 'tersedia') {
            document.getElementById('stokWarnBar').classList.add('show');
            document.getElementById('stokWarnText').textContent =
                `Status: ${s.status_stok} — ${s.jns_darah || ''} ${s.gol_rh_kantong || ''}`;
        } else {
            document.getElementById('stokFoundBar').classList.add('show');
            document.getElementById('stokFoundText').textContent =
                `${s.jns_darah || ''} ${s.gol_rh_kantong || ''} — Prod: ${fmtDate(s.tgl_produksi)} — Exp: ${fmtDate(s.tgl_kadaluarsa)}`;
        }

        // Peringatan jika sudah mencapai/lebih dari jumlah minta
        if (totalJumlahMinta > 0 && jumlahScan >= totalJumlahMinta) {
            const lanjut = confirm(
                `Jumlah kantong yang sudah di-scan (${jumlahScan}) sudah mencapai jumlah minta (${totalJumlahMinta}).\nTetap simpan kantong ini?`
            );
            if (!lanjut) {
                document.getElementById('inputNoStock').value = '';
                document.getElementById('inputNoStock').focus();
                return;
            }
        }

        window.tempStock = {
                no_stock: no,
                stok: s
            };
        tempRows.push({
            id: 'tmp_' + Date.now(),
            no_stock: no,
            jns_darah: s.jns_darah,
            gol_rh_kantong: s.gol_rh_kantong,
            tgl_ambil: new Date().toISOString().substring(0,10),
            tgl_produksi: s.tgl_produksi,
            tgl_kadaluarsa: s.tgl_kadaluarsa,
            pemeriksa: currentPtgsNama || '',
            status: 'proses',
            is_temp: true
        });

        renderTable(tempRows);

        jumlahScan = tempRows.length;
        updateJumlahBadge();
            document.getElementById('stokFoundBar').classList.add('show');

            showAlert(
                'Data kantong ditemukan. Klik tombol SIMPAN (F8) untuk menyimpan.',
                'success'
            );

    } catch (e) {
        showAlert('Gagal mengambil data stok: ' + e.message, 'danger');
    }
}
window.removeTempRow = function(id) {

    tempRows = tempRows.filter(r => r.id !== id);

    renderTable(tempRows);

    jumlahScan = tempRows.length;
    updateJumlahBadge();

    showAlert('Data scan dibatalkan.', 'warning');

    if (tempRows.length === 0) {
        window.tempStock = null;
    }
}
// ── Simpan kantong langsung (auto-save saat scan) ──────────────
async function simpanKantong(stok, noStock) {
    const payload = {
        permintaan_fpup_id: currentFpupId,
        no_fpup:            currentNoFpup,
        no_stock:           noStock,
        jns_darah:          stok.jns_darah        || '',
        gol_rh_kantong:     stok.gol_rh_kantong   || '',
        tgl_produksi:       isoDate(stok.tgl_produksi),
        tgl_kadaluarsa:     isoDate(stok.tgl_kadaluarsa),
        tgl_ambil:          isoDate(stok.tgl_aftap) || new Date().toISOString().substring(0,10),
        pemeriksa:          currentPtgsNama || '',
    };

    try {
        const res  = await fetch('/crossmatch/cross_test/store', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            showAlert('<i class="bi bi-check-circle me-1"></i>Kantong ' + noStock + ' ditambahkan.', 'success');
            document.getElementById('inputNoStock').value = '';
            document.getElementById('inputNoStock').focus();
            
            tempRows = [];
            window.tempStock = null;
            await scanFpup();
        } else {
            showAlert('<i class="bi bi-exclamation-circle me-1"></i>' + (data.message || 'Gagal menyimpan.'), 'danger');
        }
    } catch (e) {
        showAlert('Gagal menyimpan: ' + e.message, 'danger');
    }
}

// ── Scan Petugas (kode → nama) ─────────────────────────────────
async function scanPetugas() {
    const kode = document.getElementById('inputKodePtgs').value.trim();
    if (!kode) return;
    try {
        const res  = await fetch('/crossmatch/cross_test/petugas', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ kode }),
        });
        const data = await res.json();
        if (data.success) {
            currentPtgsId   = data.petugas.id;
            currentPtgsNama = data.petugas.nama;
            document.getElementById('namaPtgs').textContent = data.petugas.nama;
            showAlert('Petugas: ' + data.petugas.nama, 'success');
        } else {
            document.getElementById('namaPtgs').textContent = '— tidak ditemukan —';
            showAlert('Kode petugas tidak ditemukan.', 'warning');
        }
    } catch (e) {
        currentPtgsNama = kode;
        document.getElementById('namaPtgs').textContent = kode;
    }
}

// ── Render tabel cross match ───────────────────────────────────
function renderTable(rows) {
    const tbody = document.getElementById('ctTableBody');

    jumlahScan = rows ? rows.length : 0;
    updateJumlahBadge();

    if (!rows || !rows.length) {
        tbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="10">
                    <i class="bi bi-clipboard2-x" style="font-size:1.6rem;display:block;margin-bottom:5px"></i>
                    Belum ada data cross match — scan No. Stock untuk menambah
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = rows.map((r, i) => {
        const exp = r.tgl_kadaluarsa && new Date(r.tgl_kadaluarsa) < new Date();
        const kadTxt = exp
            ? `<span style="color:#c0392b;font-weight:700">${fmtDate(r.tgl_kadaluarsa)}</span>`
            : fmtDate(r.tgl_kadaluarsa);

        return `<tr>
            <td style="text-align:center;color:#9aacbf">${i+1}</td>
            <td><span class="no-stok">${r.no_stock || '—'}</span></td>
            <td>${r.jns_darah || '—'}</td>
            <td><strong>${r.gol_rh_kantong || '—'}</strong></td>
            <td>${statusBadge(r.status,r.is_temp)}</td>
            <td>${fmtDate(r.tgl_ambil)}</td>
            <td>${fmtDate(r.tgl_produksi)}</td>
            <td>${kadTxt}</td>
            <td>${r.pemeriksa || '—'}</td>
           <td style="white-space:nowrap">
            ${r.is_temp
                ? `
                    <button
                        class="btn-act btn-del"
                        onclick="removeTempRow('${r.id}')"
                        title="Batalkan Scan">
                        <i class="bi bi-trash3"></i>
                    </button>
                `
                : `
                    <button
                        class="btn-act"
                        onclick="editCt(${r.id})"
                        title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button
                        class="btn-act btn-del"
                        onclick="deleteCt(${r.id})"
                        title="Hapus">
                        <i class="bi bi-trash3"></i>
                    </button>
                `
            }
            </td>
        </tr>`;
    }).join('');
}

// ── Edit ───────────────────────────────────────────────────────
window.editCt = async function (id) {
    try {
        const res  = await fetch(`/crossmatch/cross_test/${id}`, {
            headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF },
        });
        const data = await res.json();
        if (!data.success) { showAlert('Gagal mengambil data.', 'danger'); return; }

        const r = data.data;
        editingCtId = id;

        setVal('fNoStock',       r.no_stock);
        setVal('fJnsDarah',      r.jns_darah);
        setVal('fGolRhKantong',  r.gol_rh_kantong);
        setVal('fTglProduksi',   isoDate(r.tgl_produksi));
        setVal('fTglKadaluarsa', isoDate(r.tgl_kadaluarsa));
        setVal('fTglAmbil',      isoDate(r.tgl_ambil));
        setVal('fTglAftap',      isoDate(r.tgl_ambil));
        setVal('fStatusStok',    r.status);
        setVal('fPemeriksa',     r.pemeriksa);
        setVal('fCatatan',       r.catatan_hasil);

        document.getElementById('cmModal').classList.add('open');
        setTimeout(() => document.getElementById('fTglAmbil').focus(), 200);
    } catch (e) {
        showAlert('Gagal memuat data: ' + e.message, 'danger');
    }
};

// ── Update ─────────────────────────────────────────────────────
document.getElementById('btnUpdateKantong').addEventListener('click', async () => {
    if (!editingCtId) return;
    const payload = {
        jns_darah:      document.getElementById('fJnsDarah').value,
        gol_rh_kantong: document.getElementById('fGolRhKantong').value,
        tgl_produksi:   document.getElementById('fTglProduksi').value,
        tgl_kadaluarsa: document.getElementById('fTglKadaluarsa').value,
        tgl_ambil:      document.getElementById('fTglAmbil').value,
        catatan_hasil:  document.getElementById('fCatatan').value,
        pemeriksa:      document.getElementById('fPemeriksa').value,
    };
    try {
        const res  = await fetch(`/crossmatch/cross_test/${editingCtId}`, {
            method: 'PUT',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) {
            showAlert('<i class="bi bi-check-circle me-1"></i>' + data.message);
            closeModal();
            scanFpup();
        } else {
            showAlert(data.message || 'Gagal memperbarui.', 'danger');
        }
    } catch (e) {
        showAlert('Gagal memperbarui data.', 'danger');
    }
});

// ── Delete ─────────────────────────────────────────────────────
window.deleteCt = async function (id) {
    if (!confirm('Hapus data cross match ini?')) return;
    try {
        const res  = await fetch(`/crossmatch/cross_test/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
        });
        const data = await res.json();
        if (data.success) { showAlert(data.message); scanFpup(); }
        else showAlert('Gagal menghapus data.', 'danger');
    } catch (e) {
        showAlert('Gagal menghapus data.', 'danger');
    }
};

// ── Modal helpers ──────────────────────────────────────────────
function closeModal() {
    document.getElementById('cmModal').classList.remove('open');
    editingCtId = null;
}

document.getElementById('btnCloseModal').addEventListener('click', closeModal);
document.getElementById('btnBatalModal').addEventListener('click', closeModal);
document.getElementById('cmModal').addEventListener('click', e => {
    if (e.target === document.getElementById('cmModal')) closeModal();
});

// ── Event bindings ─────────────────────────────────────────────
document.getElementById('btnScan').addEventListener('click', scanFpup);
document.getElementById('inputNoFpup').addEventListener('keydown', e => { if (e.key === 'Enter') scanFpup(); });

document.getElementById('btnScanStock').addEventListener('click', scanNoStock);
document.getElementById('inputNoStock').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); scanNoStock(); }
});

// Tombol toolbar "Simpan (F8)" → trigger simpan kantong yang sedang diketik
document.getElementById('btnSimpan')
.addEventListener('click', async () => {

    if (!window.tempStock) {
        showAlert('Scan No Stock terlebih dahulu.', 'warning');
        return;
    }

    await simpanKantong(
        window.tempStock.stok,
        window.tempStock.no_stock
    );

    window.tempStock = null;
});
document.getElementById('inputKodePtgs').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); scanPetugas(); }
});
document.getElementById('inputKodePtgs').addEventListener('blur', scanPetugas);

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
     if(e.key === 'F4'){
        e.preventDefault();
        bukaCariData();
    }
    if (e.key === 'F8') {
        e.preventDefault();
        document.getElementById('btnSimpan').click();
    }
});
</script>
@endpush