@extends('layouts.index')

@push('styles')
<style>
    *{box-sizing:border-box}
    body{font-family:'Inter','Segoe UI',Tahoma,sans-serif;font-size:13px;background:#f1f4f9;color:#1e2533}

    :root{
        --navy:#1a3a6b;
        --navy-dark:#10254a;
        --accent:#2563eb;
        --ref:#b8442e;
        --ref-bg:#fdf1ee;
        --ok:#0f9d58;
        --ok-bg:#e7f8ef;
        --warn:#b8860b;
        --warn-bg:#fdf6e3;
        --danger:#c0392b;
        --danger-bg:#fdebe9;
        --border:#dde3ee;
    }

    /* Header bar */
    .ctr-header{
        background:linear-gradient(135deg,var(--navy) 0%,var(--navy-dark) 100%);
        color:#fff;padding:10px 18px;display:flex;align-items:center;justify-content:space-between;
        box-shadow:0 2px 8px rgba(20,40,80,.15)
    }
    .ctr-header h1{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px;letter-spacing:.01em}
    .ctr-header .sub{font-size:11px;opacity:.75;font-weight:400}
    .badge-referal-mode{background:var(--ref);font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:.04em;display:flex;align-items:center;gap:5px}
    .badge-referal-mode .dot{width:6px;height:6px;border-radius:50%;background:#fff;animation:pulse 1.4s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}

    /* Toolbar */
    .ctr-toolbar{background:#fff;border-bottom:1px solid var(--border);padding:8px 18px;display:flex;gap:8px;align-items:center;flex-wrap:wrap}
    .btn-tool{background:#fff;border:1px solid #cfd8e6;border-radius:7px;padding:7px 14px;font-size:12px;font-weight:600;
        color:#33415c;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all .15s}
    .btn-tool:hover{background:#eef3fc;border-color:var(--accent);color:var(--accent)}
    .btn-tool.primary{background:var(--accent);border-color:var(--accent);color:#fff}
    .btn-tool.primary:hover{background:#1d4ed8}
    .btn-tool kbd{background:rgba(0,0,0,.08);border-radius:4px;padding:1px 5px;font-size:10px;font-weight:600;margin-left:2px}
    .btn-tool.primary kbd{background:rgba(255,255,255,.2)}
    .toolbar-sep{width:1px;height:26px;background:var(--border)}

    /* Scan bar */
    .ctr-scanbar{background:#fff;border-bottom:1px solid var(--border);padding:10px 18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
    .scan-group{display:flex;align-items:center;gap:6px;background:#f6f8fc;border:1px solid var(--border);border-radius:8px;padding:4px 4px 4px 12px}
    .scan-group label{font-size:11px;font-weight:700;color:var(--navy);white-space:nowrap;text-transform:uppercase;letter-spacing:.03em}
    .scan-group input{border:none;background:transparent;padding:4px 2px;font-size:13px;outline:none;width:170px;font-family:'JetBrains Mono',monospace;font-weight:600;color:#1e2533}
    .btn-go{background:var(--navy);color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:11px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px}
    .btn-go:hover{background:var(--navy-dark)}
    .info-chip{background:#fff;border:1px solid var(--border);border-radius:8px;padding:6px 12px;display:flex;flex-direction:column;gap:1px;min-width:110px}
    .info-chip .k{font-size:9.5px;text-transform:uppercase;letter-spacing:.04em;color:#8693ab;font-weight:700}
    .info-chip .v{font-size:12px;font-weight:700;color:var(--navy)}

    /* Main layout */
    .ctr-main{display:grid;grid-template-columns:280px 1fr;gap:14px;padding:14px 18px;height:calc(100vh - 188px);overflow:hidden}

    .card{background:#fff;border:1px solid var(--border);border-radius:10px;overflow:hidden;display:flex;flex-direction:column}
    .card-head{background:#f6f8fc;border-bottom:1px solid var(--border);padding:8px 12px;font-size:11px;font-weight:700;
        color:var(--navy);text-transform:uppercase;letter-spacing:.04em;display:flex;align-items:center;gap:6px}
    .card-head.ref{background:var(--ref-bg);color:var(--ref)}

    /* Left panel */
    .left-col{display:flex;flex-direction:column;gap:10px;overflow-y:auto;padding-right:2px}

    .info-row{display:grid;grid-template-columns:88px 1fr;border-bottom:1px solid #eef1f7;min-height:30px}
    .info-row:last-child{border-bottom:none}
    .info-row .lbl{font-size:10px;font-weight:700;color:#7c8aa3;text-transform:uppercase;letter-spacing:.03em;padding:6px 8px;display:flex;align-items:center;background:#fafbfd}
    .info-row .val{font-size:12px;color:#1e2533;padding:6px 8px;display:flex;align-items:center;flex-wrap:wrap;gap:3px;font-weight:500}
    .info-row .val.bold{font-weight:700;color:var(--navy)}
    .gol-badge{background:var(--danger);color:#fff;font-weight:700;padding:2px 8px;border-radius:4px;font-size:11.5px}

    .referal-banner{margin:8px 10px;background:var(--ref-bg);border:1px solid #f0c4b8;border-radius:8px;padding:8px 10px;display:flex;align-items:flex-start;gap:7px}
    .referal-banner i{color:var(--ref);font-size:15px;margin-top:1px}
    .referal-banner .t{font-size:11px;font-weight:700;color:var(--ref)}
    .referal-banner .s{font-size:10.5px;color:#8a5747;margin-top:1px;line-height:1.4}

    .petugas-box{padding:8px 10px;display:flex;align-items:center;gap:8px}
    .petugas-box label{font-size:10px;font-weight:700;color:#7c8aa3;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap}
    .petugas-input{border:1px solid #cfd8e6;border-radius:6px;padding:5px 8px;font-size:12px;width:64px;outline:none;font-weight:600}
    .petugas-input:focus{border-color:var(--accent);box-shadow:0 0 0 2px rgba(37,99,235,.12)}
    .petugas-nama{font-size:12px;font-weight:700;color:var(--navy);flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

    .check-line{padding:7px 10px;display:flex;align-items:center;gap:6px;font-size:12px;border-top:1px solid #eef1f7}
    .check-line input{width:15px;height:15px;cursor:pointer}

    /* Stock scan box */
    .stock-scan-box{padding:10px}
    .stock-input-row{display:flex;gap:6px}
    .stock-input-row input{flex:1;border:1px solid #cfd8e6;border-radius:6px;padding:7px 9px;font-size:13px;font-family:'JetBrains Mono',monospace;font-weight:600;outline:none}
    .stock-input-row input:focus{border-color:var(--accent);box-shadow:0 0 0 2px rgba(37,99,235,.12)}
    .btn-scan-icon{background:var(--navy);color:#fff;border:none;border-radius:6px;padding:0 12px;cursor:pointer;font-size:14px}
    .btn-scan-icon:hover{background:var(--navy-dark)}
    .status-pill{margin-top:7px;border-radius:7px;padding:6px 9px;font-size:11px;display:none;align-items:flex-start;gap:6px;line-height:1.4}
    .status-pill.show{display:flex}
    .status-pill.ok{background:var(--ok-bg);color:#0b7a44}
    .status-pill.warn{background:var(--warn-bg);color:#8a6d12}

    /* Right column */
    .right-col{display:flex;flex-direction:column;gap:10px;overflow:hidden}
    .top-row{display:grid;grid-template-columns:1fr 230px;gap:10px}

    .perm-table{width:100%;border-collapse:collapse;font-size:12px}
    .perm-table th{background:#f6f8fc;color:#7c8aa3;padding:6px 8px;font-size:10px;font-weight:700;text-align:left;
        border-bottom:1px solid var(--border);text-transform:uppercase;letter-spacing:.03em}
    .perm-table td{padding:6px 8px;border-bottom:1px solid #eef1f7}
    .perm-table tr.perm-sel td{background:#fff8e1}
    .perm-table tr:hover td{background:#f6f9ff}
    .jenis-darah-chip{background:#eaf0ff;color:var(--navy);font-weight:700;padding:2px 7px;border-radius:5px;font-size:11px}

    .kurir-box{padding:10px;display:flex;flex-direction:column;gap:8px}
    .kurir-item .k{font-size:10px;color:#8693ab;font-weight:700;text-transform:uppercase;letter-spacing:.03em}
    .kurir-item .v{font-size:12px;font-weight:600;color:#1e2533;background:#fffbe8;border:1px solid #f3e9b8;border-radius:5px;padding:4px 7px;margin-top:2px;min-height:16px}

    .jml-bar{background:#fff;border:1px solid var(--border);border-radius:10px;padding:8px 14px;display:flex;align-items:center;gap:10px}
    .jml-bar label{font-size:11.5px;color:#5a6a85;font-weight:600}
    .jml-badge{background:var(--navy);color:#fff;padding:3px 14px;border-radius:20px;font-size:12px;font-weight:700;min-width:56px;text-align:center;transition:background .2s}
    .perlu-info{font-size:11px;color:#8693ab;margin-left:auto;font-weight:600}

    /* Cross test table */
    .ct-card{flex:1;display:flex;flex-direction:column;overflow:hidden}
    .ct-scroll{flex:1;overflow-y:auto}
    .ct-table{width:100%;border-collapse:collapse;font-size:12px}
    .ct-table thead tr{background:var(--navy);position:sticky;top:0;z-index:1}
    .ct-table th{color:#cfe0ff;padding:8px 9px;font-size:10.5px;font-weight:700;text-align:left;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap}
    .ct-table td{padding:6px 9px;border-bottom:1px solid #eef1f7;vertical-align:middle;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .ct-table tbody tr:hover td{background:#f6f9ff}
    .no-stok{font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--navy);font-size:12px}
    .empty-state{text-align:center;padding:40px 10px;color:#a7b4c8}
    .empty-state i{font-size:2rem;display:block;margin-bottom:8px;opacity:.6}

    .bd{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.02em}
    .bd-compat  {background:var(--ok-bg);color:#0b7a44}
    .bd-incompat{background:var(--danger-bg);color:var(--danger)}
    .bd-pending {background:var(--warn-bg);color:#8a6d12}
    .bd-proses  {background:#e8f0fe;color:#1a56db}
    .bd-selesai {background:#e8f5f0;color:#0b7a44}

    .btn-act{background:#f6f8fc;border:1px solid var(--border);padding:4px 7px;border-radius:6px;cursor:pointer;font-size:12px;color:#5a6a85;transition:all .12s;margin-right:3px}
    .btn-act:hover{background:#eaf0ff;border-color:var(--accent);color:var(--accent)}
    .btn-act.btn-del:hover{background:var(--danger-bg);border-color:#f0a8a0;color:var(--danger)}

    /* Modal */
    .cm-modal{display:none;position:fixed;inset:0;background:rgba(15,25,45,.55);z-index:200;align-items:center;justify-content:center;backdrop-filter:blur(2px)}
    .cm-modal.open{display:flex}
    .cm-modal-box{background:#fff;border-radius:12px;width:540px;max-width:96vw;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.25)}
    .cm-modal-head{background:linear-gradient(135deg,var(--navy),var(--navy-dark));color:#fff;padding:14px 18px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:space-between}
    .btn-close-modal{background:rgba(255,255,255,.15);border:none;color:#fff;cursor:pointer;font-size:16px;line-height:1;padding:3px 8px;border-radius:6px}
    .btn-close-modal:hover{background:rgba(255,255,255,.28)}
    .cm-modal-body{padding:16px 18px;display:flex;flex-direction:column;gap:10px}
    .form-row{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px}
    .form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .form-grp{display:flex;flex-direction:column;gap:4px}
    .form-grp label{font-size:10px;font-weight:700;color:#7c8aa3;text-transform:uppercase;letter-spacing:.03em}
    .form-grp select,.form-grp input[type=text],.form-grp input[type=date]{
        border:1px solid #cfd8e6;padding:7px 9px;font-size:12.5px;border-radius:7px;background:#fff;outline:none}
    .form-grp select:focus,.form-grp input:focus{border-color:var(--accent);box-shadow:0 0 0 2px rgba(37,99,235,.12)}
    .cm-modal-foot{padding:12px 18px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end;background:#fafbfd}
    .btn-danger{background:var(--danger);color:#fff;border:none;padding:8px 18px;border-radius:7px;font-size:12px;font-weight:700;cursor:pointer}
    .btn-danger:hover{background:#a93226}
    .btn-sec{background:#f1f4f9;color:#33415c;border:1px solid #cfd8e6;padding:8px 16px;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer}
    .btn-sec:hover{background:#e7ecf5}

    /* Search modal */
    .modal-search{display:none;position:fixed;inset:0;background:rgba(15,25,45,.55);z-index:200;align-items:center;justify-content:center}
    .modal-search-content{background:#fff;border-radius:12px;width:600px;max-width:96vw;max-height:80vh;overflow-y:auto;padding:18px;box-shadow:0 10px 40px rgba(0,0,0,.25)}
    .modal-header{font-size:14px;font-weight:700;color:var(--navy);margin-bottom:10px}

    #cmAlert{position:fixed;top:14px;right:14px;z-index:999;min-width:280px;display:none;padding:12px 18px;border-radius:9px;font-size:13px;font-weight:600;box-shadow:0 6px 20px rgba(0,0,0,.18)}
    #cmAlert i{font-size:18px}
    @keyframes toastIn{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:translateX(0)}}
    .al-success{background:var(--ok-bg);color:#0b7a44;border:1px solid #b7e8cd}
    .al-danger {background:var(--danger-bg);color:var(--danger);border:1px solid #f3c4be}
    .al-warning{background:var(--warn-bg);color:#8a6d12;border:1px solid #f0dca0}

    .spinner{display:inline-block;width:12px;height:12px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;vertical-align:middle}
    @keyframes spin{to{transform:rotate(360deg)}}
    ::-webkit-scrollbar{width:6px;height:6px}
    ::-webkit-scrollbar-thumb{background:#c3cde0;border-radius:3px}
</style>
@endpush

@section('content')
<div style="height:100vh;display:flex;flex-direction:column;overflow:hidden;background:#f1f4f9">

    {{-- Header --}}
    <div class="ctr-header">
        <h1>
            <i class="bi bi-droplet-half"></i>
            CROSS MATCH REFERAL
            <span class="sub">&nbsp;— Lembar Kerja Pelayanan Darah Rujukan</span>
        </h1>
        <span class="badge-referal-mode"><span class="dot"></span> MODE REFERAL</span>
    </div>

    {{-- Toolbar --}}
    <div class="ctr-toolbar">
        <button class="btn-tool primary" id="btnSimpan"><i class="bi bi-floppy"></i> Simpan <kbd>F8</kbd></button>
        <div class="toolbar-sep"></div>
        <button class="btn-tool"><i class="bi bi-file-earmark-excel"></i> Excel <kbd>F6</kbd></button>
        <button class="btn-tool" id="btnCetak"><i class="bi bi-printer"></i> Cetak <kbd>F10</kbd></button>
        <div class="toolbar-sep"></div>
        <button class="btn-tool" id="btnFpup"><i class="bi bi-search"></i> Cari Data <kbd>F4</kbd></button>
    </div>

    {{-- Scan bar --}}
    <div class="ctr-scanbar">
        <div class="scan-group">
            <label>No. FPUP</label>
            <input type="text" id="inputNoFpup" placeholder="Scan / ketik No. FPUP…" autocomplete="off">
            <button class="btn-go" id="btnScan">
                <span id="scanSpinner" style="display:none"><span class="spinner"></span></span>
                <i class="bi bi-upc-scan" id="scanIcon"></i>
                Cari
            </button>
        </div>
        <div class="info-chip"><span class="k">Tgl FPUP</span><span class="v" id="tglFpup">—</span></div>
        <div class="info-chip"><span class="k">No. Reg</span><span class="v" id="noReg">—</span></div>
        <div class="info-chip" style="border-color:#f0c4b8;background:#fffaf8"><span class="k" style="color:var(--ref)">No. Referal</span><span class="v" id="noReferal" style="color:var(--ref)">—</span></div>
    </div>

    {{-- Main grid --}}
    <div class="ctr-main">

        {{-- ── LEFT COLUMN ── --}}
        <div class="left-col">

            <div class="card">
                <div class="card-head"><i class="bi bi-hospital"></i> Data Rumah Sakit & Pasien</div>
                <div class="info-row"><span class="lbl">Rumah Sakit</span><span class="val" id="namaRs">—</span></div>
                <div class="info-row"><span class="lbl">Bagian RS</span><span class="val" id="bagianRs">—</span></div>
                <div class="info-row"><span class="lbl">Kelas RS</span><span class="val" id="kelasRawat">—</span></div>
                <div class="info-row"><span class="lbl">Nama Dokter</span><span class="val" id="namaDokter">—</span></div>
                <div class="info-row"><span class="lbl">Nama Pasien</span><span class="val bold" id="namaPasien">—</span></div>
                <div class="info-row"><span class="lbl">Gol-Rh Pasien</span><span class="val" id="golRhPasien">—</span></div>
                <div class="info-row"><span class="lbl">Diagnosa</span><span class="val" id="diagnosa">—</span></div>
                <div class="info-row"><span class="lbl">Cara Bayar</span><span class="val" id="caraBayar">—</span></div>

                {{-- Banner referal --}}
                <div class="referal-banner" id="referalBanner" style="display:none">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <div class="t">Pasien Rujukan (Referal)</div>
                        <div class="s" id="alasanReferalText">—</div>
                    </div>
                </div>

                <div class="check-line">
                    <input type="checkbox" id="chkReferal" disabled>
                    <label for="chkReferal" style="color:var(--ref);font-weight:700">Tandai Pasien Referal</label>
                </div>
                <div class="check-line">
                    <input type="checkbox" id="chkBarcode">
                    <label for="chkBarcode">Cetak Barcode</label>
                </div>
            </div>

            <div class="card">
                <div class="card-head"><i class="bi bi-person-badge"></i> Petugas Pemeriksa</div>
                <div class="petugas-box">
                    <label>Kode</label>
                    <input type="text" id="inputKodePtgs" class="petugas-input" placeholder="Kode" autocomplete="off" title="Ketik kode petugas lalu Enter">
                    <span class="petugas-nama" id="namaPtgs">—</span>
                </div>
            </div>

            <div class="card">
                <div class="card-head ref"><i class="bi bi-upc-scan"></i> Scan No. Stock Kantong</div>
                <div class="stock-scan-box">
                    <div class="stock-input-row">
                        <input type="text" id="inputNoStock" placeholder="Scan / ketik No. Stock…" autocomplete="off">
                        <button class="btn-scan-icon" id="btnScanStock"><i class="bi bi-upc-scan"></i></button>
                    </div>
                    <div class="status-pill ok" id="stokFoundBar">
                        <i class="bi bi-check-circle-fill"></i>
                        <span id="stokFoundText"></span>
                    </div>
                    <div class="status-pill warn" id="stokWarnBar">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <span id="stokWarnText"></span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT COLUMN ── --}}
        <div class="right-col">

            <div class="top-row">
                <div class="card">
                    <div class="card-head"><i class="bi bi-clipboard2-pulse"></i> Data Permintaan Darah</div>
                    <div style="overflow-x:auto">
                        <table class="perm-table">
                            <thead>
                                <tr>
                                    <th style="width:26px">No</th>
                                    <th>Jenis Darah</th>
                                    <th style="width:40px">Gol</th>
                                    <th style="width:65px">Rhesus</th>
                                    <th style="width:55px">Jumlah</th>
                                    <th style="width:95px">Tgl Perlu</th>
                                </tr>
                            </thead>
                            <tbody id="permBody">
                                <tr><td colspan="6" style="text-align:center;color:#a7b4c8;padding:14px;font-size:12px">Belum ada data</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-head"><i class="bi bi-truck"></i> Kurir / Registrasi Online</div>
                    <div class="kurir-box">
                        <div class="kurir-item">
                            <div class="k">No Registrasi Online</div>
                            <div class="v" id="noRegOnline"></div>
                        </div>
                        <div class="kurir-item">
                            <div class="k">Tgl Registrasi Online</div>
                            <div class="v" id="tglRegOnline"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="jml-bar">
                <i class="bi bi-droplet-fill" style="color:var(--navy)"></i>
                <label>Jumlah Discan / Diminta</label>
                <span class="jml-badge" id="jumlahMinta">0 / 0</span>
                <span class="perlu-info" id="tglPerluInfo"></span>
            </div>

            <div class="card ct-card">
                <div class="card-head"><i class="bi bi-table"></i> Hasil Cross Match Kantong</div>
                <div class="ct-scroll">
                    <table class="ct-table">
                        <thead>
                            <tr>
                                <th style="width:26px">No</th>
                                <th style="width:115px">No Stock</th>
                                <th style="width:60px">Jenis</th>
                                <th style="width:55px">Gol/Rh</th>
                                <th style="width:90px">Status</th>
                                <th style="width:85px">Tgl Aftap</th>
                                <th style="width:88px">Tgl Produksi</th>
                                <th style="width:85px">Kadaluarsa</th>
                                <th style="width:95px">Pemeriksa</th>
                                <th style="width:60px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ctTableBody">
                            <tr>
                                <td colspan="10">
                                    <div class="empty-state">
                                        <i class="bi bi-upc-scan"></i>
                                        Scan No. FPUP lalu scan No. Stock kantong untuk menambah data
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Cari --}}
<div id="modalCari" class="modal-search">
    <div class="modal-search-content">
        <div class="modal-header"><i class="bi bi-search me-1"></i> Cari Data Cross Match Referal</div>
        <input type="text" id="txtCari" class="form-control" placeholder="No FPUP / Nama Pasien / No Stock"
            style="width:100%;border:1px solid #cfd8e6;border-radius:7px;padding:8px 10px;font-size:13px;outline:none">
        <table class="table table-sm mt-2" style="width:100%;font-size:12px;margin-top:10px">
            <thead>
                <tr style="background:#f6f8fc">
                    <th style="padding:6px">No FPUP</th>
                    <th style="padding:6px">Pasien</th>
                    <th style="padding:6px">No Stock</th>
                    <th style="padding:6px">Status</th>
                    <th style="padding:6px"></th>
                </tr>
            </thead>
            <tbody id="tblCariBody"></tbody>
        </table>
    </div>
</div>

{{-- Modal Edit --}}
<div class="cm-modal" id="cmModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="cm-modal-box">
        <div class="cm-modal-head">
            <span id="modalTitle"><i class="bi bi-pencil-fill me-1"></i> Edit Data Cross Match Referal</span>
            <button class="btn-close-modal" id="btnCloseModal" aria-label="Tutup">&times;</button>
        </div>
        <div class="cm-modal-body">
            <div class="form-row-2">
                <div class="form-grp">
                    <label>No. Stock</label>
                    <input type="text" id="fNoStock" readonly style="background:#fafbfd;color:#666;font-family:'JetBrains Mono',monospace;font-weight:700">
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
                    <input type="text" id="fStatusStok" readonly style="background:#fafbfd;color:#666">
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
                    <input type="text" id="fPemeriksa" readonly style="background:#fafbfd;color:#444">
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

// PERBAIKAN UTAMA:
// Sebelumnya hardcode "const BASE = '/cross_test_referal';" yang SALAH karena
// route ini sebenarnya terdaftar dengan prefix "/referal/cross_test_referal"
// (lihat routes/web.php -> Route::name('referal')->prefix('referal')->group(...)).
// Dengan route() helper, URL akan selalu otomatis benar walau prefix berubah lagi nanti.
const BASE = "{{ route('referal.cross_test_referal.index') }}";

let currentFpupId    = null;
let currentNoFpup    = null;
let currentPtgsId    = null;
let currentPtgsNama  = null;
let editingCtId      = null;
let tempRows = [];
let totalJumlahMinta = 0;
let jumlahScan       = 0;

async function bukaCariData(){
    document.getElementById('modalCari').style.display='flex';
    const kw = document.getElementById('txtCari').value.trim();
    const res = await fetch(`${BASE}/search?keyword=${encodeURIComponent(kw)}`);
    const data = await res.json();
    renderCari(data.data);
}
function renderCari(rows){
    const body = document.getElementById('tblCariBody');
    body.innerHTML = rows.map(r=>`
        <tr>
            <td style="padding:6px">${r.no_fpup}</td>
            <td style="padding:6px">${r.nama_pasien ?? '-'}</td>
            <td style="padding:6px">${r.no_stock ?? '-'}</td>
            <td style="padding:6px">${r.status}</td>
            <td style="padding:6px">
                <button class="btn-tool" style="padding:4px 10px" onclick="editCrossTest('${r.no_fpup}')">Pilih</button>
            </td>
        </tr>
    `).join('');
}
async function editCrossTest(noFpup){
    document.getElementById('modalCari').style.display='none';
    document.getElementById('inputNoFpup').value = noFpup;
    await scanFpup();
}

// ── Helpers ────────────────────────────────────────────────────
function showAlert(msg, type = 'success') {
    const b = document.getElementById('cmAlert');
    b.className = 'al-' + type;
    b.style.display = 'flex';
    b.style.alignItems = 'center';
    b.style.gap = '8px';
    b.innerHTML = msg;
    b.style.animation = 'none';
    requestAnimationFrame(() => { b.style.animation = 'toastIn .25s ease-out'; });
    clearTimeout(b._t);
    const duration = type === 'success' ? 4500 : 3500;
    b._t = setTimeout(() => b.style.display = 'none', duration);
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

function pick(obj, keys, def = '') {
    if (!obj) return def;
    for (const k of keys) {
        const v = obj[k];
        if (v !== undefined && v !== null && v !== '') return v;
    }
    return def;
}

function statusBadge(st, isTemp=false){
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

function updateJumlahBadge() {
    const badge = document.getElementById('jumlahMinta');
    badge.textContent = `${jumlahScan} / ${totalJumlahMinta}`;
    if (totalJumlahMinta > 0 && jumlahScan >= totalJumlahMinta) {
        badge.style.background = '#0f9d58';
    } else if (jumlahScan > totalJumlahMinta) {
        badge.style.background = '#c0392b';
    } else {
        badge.style.background = '#1a3a6b';
    }
}

// ── Scan FPUP Referal ───────────────────────────────────────────
async function scanFpup() {
    const no = document.getElementById('inputNoFpup').value.trim();
    if (!no) return;

    document.getElementById('scanSpinner').style.display = 'inline';
    document.getElementById('scanIcon').style.display    = 'none';

    try {
        const res  = await fetch(`${BASE}/scan`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ no_fpup: no }),
        });
        const data = await res.json();
        if(!res.ok || !data.success){
            Swal.fire({ icon:'error', title:'FPUP Referal Tidak Ditemukan', text:data.message || 'Data FPUP Referal tidak ditemukan' });
            clearFpupForm();
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
    document.getElementById('namaPasien').textContent = '—';
    document.getElementById('golRhPasien').textContent = '—';
    document.getElementById('namaDokter').textContent = '—';
    document.getElementById('diagnosa').textContent = '—';
    document.getElementById('caraBayar').textContent = '—';
    document.getElementById('namaRs').textContent = '—';
    document.getElementById('bagianRs').textContent = '—';
    document.getElementById('kelasRawat').textContent = '—';
    document.getElementById('noReferal').textContent = '—';
    document.getElementById('referalBanner').style.display = 'none';

    document.getElementById('permBody').innerHTML =
        '<tr><td colspan="6" style="text-align:center;color:#a7b4c8;padding:14px;font-size:12px">Belum ada data</td></tr>';

    totalJumlahMinta = 0;
    jumlahScan = 0;
    tempRows = [];
    updateJumlahBadge();
    renderTable([]);
}

// ── Fill info panel ──────────────────────────────────────────────
function fillInfo(fpup) {
    currentFpupId = fpup.id;
    currentNoFpup = fpup.no_fpup;

    document.getElementById('tglFpup').textContent  = fmtDate(pick(fpup, ['tgl_minta','tgl_fpup','created_at']));
    document.getElementById('noReg').textContent    = pick(fpup, ['no_reg','no_registrasi','no_form'], '—');
    document.getElementById('noReferal').textContent = pick(fpup, ['no_referal'], '—');
    document.getElementById('namaRs').textContent   = pick(fpup, ['nama_rs','rumah_sakit'], '—');
    document.getElementById('bagianRs').textContent = pick(fpup, ['bagian','bagian_rs'], '—');
    document.getElementById('namaDokter').textContent = pick(fpup, ['nama_dokter','dokter'], '—');
    document.getElementById('namaPasien').textContent = pick(fpup, ['nama_pasien','pasien'], '—');
    document.getElementById('diagnosa').textContent   = pick(fpup, ['diagnosa_klinis','diagnosa'], '—');

    const caraBayar = fpup.cara_pembayaran || '';
    const jnsBiaya  = fpup.jns_biaya || '';
    document.getElementById('caraBayar').textContent = [caraBayar, jnsBiaya].filter(Boolean).join(' / ') || '—';

    const kelas  = pick(fpup, ['kelas_rawat','kelas_rs','kelas'], '');
    const rsNama = pick(fpup, ['nama_rs','rumah_sakit'], '');
    document.getElementById('kelasRawat').textContent = kelas
        ? (rsNama ? `${kelas} — ${rsNama}` : kelas)
        : (rsNama || '—');

    // Banner & checkbox referal
    const isReferal = Number(fpup.pasien_referal) === 1;
    document.getElementById('chkReferal').checked = isReferal;
    document.getElementById('referalBanner').style.display = isReferal ? 'flex' : 'none';
    document.getElementById('alasanReferalText').textContent =
        pick(fpup, ['alasan_referal_utama','alasan_referal'], 'Pasien rujukan dari fasilitas lain.');

    // ── Data Permintaan (join jenis_darah) ──
    const details = fpup.details || [];
    const pb = document.getElementById('permBody');

    if (details && details.length) {
        pb.innerHTML = details.map((d, i) => {
            const jnsNama = pick(d, ['jenis_darah_nama_pendek','jenis_darah_nama','jenis_darah_kode'], '—');
            const gol     = pick(d, ['gol_darah','golongan_darah','gol'], '');
            const rhesus  = pick(d, ['rhesus','rh'], '');
            const jumlah  = parseInt(pick(d, ['jumlah','jml','qty'], 0)) || 0;
            const tglPerlu = pick(d, ['tgl_perlu','tanggal_perlu','tgl_butuh'], null);

            return `
                <tr class="${i === 0 ? 'perm-sel' : ''}" data-detail-id="${d.id ?? ''}">
                    <td style="text-align:center">${i+1}</td>
                    <td><span class="jenis-darah-chip">${jnsNama}</span></td>
                    <td>${gol || '—'}</td>
                    <td>${rhesus || '—'}</td>
                    <td style="text-align:center">${jumlah}</td>
                    <td>${fmtDate(tglPerlu)}</td>
                </tr>`;
        }).join('');

        totalJumlahMinta = details.reduce((s, d) => s + (parseInt(pick(d, ['jumlah','jml','qty'], 0)) || 0), 0);

        const d0  = details[0];
        const g   = (pick(d0, ['gol_darah','golongan_darah','gol'], '')).toString().trim();
        let   r   = (pick(d0, ['rhesus','rh'], '')).toString().trim();
        if (/positif/i.test(r))      r = '+';
        else if (/negatif/i.test(r)) r = '-';
        const golRh = (g || r) ? `${g}${r}` : '';

        document.getElementById('golRhPasien').innerHTML = golRh
            ? `<span class="gol-badge">${golRh}</span>`
            : '—';

        const tglPerlu0 = pick(d0, ['tgl_perlu','tanggal_perlu','tgl_butuh'], null);
        document.getElementById('tglPerluInfo').textContent = tglPerlu0
            ? `Diperlukan: ${fmtDate(tglPerlu0)}`
            : '';

        // simpan referensi detail pertama untuk dikirim saat simpan
        window.currentDetailId = d0.id ?? null;
    } else {
        pb.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#a7b4c8;padding:14px;font-size:12px">Tidak ada detail permintaan</td></tr>';
        totalJumlahMinta = 0;
        document.getElementById('golRhPasien').textContent = '—';
        document.getElementById('tglPerluInfo').textContent = '';
        window.currentDetailId = null;
    }

    document.getElementById('noRegOnline').textContent = pick(fpup, ['no_reg_online'], '—');
    document.getElementById('tglRegOnline').textContent = fmtDate(pick(fpup, ['tgl_registrasi_online'], null));

    updateJumlahBadge();
}

// ── Scan No Stock ─────────────────────────────────────────────
async function scanNoStock() {
    const no = document.getElementById('inputNoStock').value.trim();
    if (!no) return;
    if (!currentFpupId) { showAlert('Scan No. FPUP terlebih dahulu.', 'warning'); return; }

    document.getElementById('stokFoundBar').classList.remove('show');
    document.getElementById('stokWarnBar').classList.remove('show');

    try {
        const res  = await fetch(`${BASE}/scan_stock`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify({ no_stock: no }),
        });
        const data = await res.json();
        if(res.status === 404){
            Swal.fire({ icon:'warning', title:'No Stok Tidak Ditemukan', text:'Nomor Stok tidak ada dalam database.' });
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

        window.tempStock = { no_stock: no, stok: s };

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

        showAlert('Data kantong ditemukan. Klik tombol SIMPAN (F8) untuk menyimpan.', 'success');

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
    if (tempRows.length === 0) window.tempStock = null;
}

// ── Simpan kantong ───────────────────────────────────────────
async function simpanKantong(stok, noStock) {
    const payload = {
        permintaan_fpup_referal_id: currentFpupId,
        permintaan_fpup_referal_detail_id: window.currentDetailId || null,
        no_fpup:            currentNoFpup,
        no_stock:           noStock,
        jns_darah:          stok.jns_darah        || '',
        gol_rh_kantong:     stok.gol_rh_kantong   || '',
        tgl_produksi:       isoDate(stok.tgl_produksi),
        tgl_kadaluarsa:     isoDate(stok.tgl_kadaluarsa),
        // PERBAIKAN: stok.tgl_aftap baru ada jika service findStokByNoStock()
        // sudah ditambahkan field tgl_aftap (lihat catatan di Service).
        tgl_ambil:          isoDate(stok.tgl_aftap) || new Date().toISOString().substring(0,10),
        pemeriksa:          currentPtgsNama || '',
    };

    try {
        const res  = await fetch(`${BASE}/store`, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (data.success) {
            showAlert('<i class="bi bi-check-circle-fill me-1"></i><strong>Berhasil!</strong> Kantong ' + noStock + ' disimpan.', 'success');

            // PERBAIKAN: clear SEMUA input (No. FPUP, No. Stock, Kode Petugas)
            // setelah simpan berhasil, supaya siap untuk transaksi/scan baru.
            document.getElementById('inputNoFpup').value = '';
            document.getElementById('inputNoStock').value = '';
            document.getElementById('inputKodePtgs').value = '';
            document.getElementById('namaPtgs').textContent = '—';

            document.getElementById('stokFoundBar').classList.remove('show');
            document.getElementById('stokWarnBar').classList.remove('show');

            currentFpupId   = null;
            currentNoFpup   = null;
            currentPtgsId   = null;
            currentPtgsNama = null;
            tempRows        = [];
            window.tempStock = null;
            window.currentDetailId = null;

            clearFpupForm();
            document.getElementById('inputNoFpup').focus();
        } else {
            showAlert('<i class="bi bi-exclamation-circle me-1"></i>' + (data.message || 'Gagal menyimpan.'), 'danger');
        }
    } catch (e) {
        showAlert('Gagal menyimpan: ' + e.message, 'danger');
    }
}

// ── Scan Petugas ──────────────────────────────────────────────
async function scanPetugas() {
    const kode = document.getElementById('inputKodePtgs').value.trim();
    if (!kode) return;
    try {
        const res  = await fetch(`${BASE}/petugas`, {
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

// ── Render tabel cross match ────────────────────────────────────
function renderTable(rows) {
    const tbody = document.getElementById('ctTableBody');

    jumlahScan = rows ? rows.length : 0;
    updateJumlahBadge();

    if (!rows || !rows.length) {
        tbody.innerHTML = `
            <tr><td colspan="10">
                <div class="empty-state">
                    <i class="bi bi-clipboard2-x"></i>
                    Belum ada data cross match — scan No. Stock untuk menambah
                </div>
            </td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map((r, i) => {
        const exp = r.tgl_kadaluarsa && new Date(r.tgl_kadaluarsa) < new Date();
        const kadTxt = exp
            ? `<span style="color:#c0392b;font-weight:700">${fmtDate(r.tgl_kadaluarsa)}</span>`
            : fmtDate(r.tgl_kadaluarsa);

        return `<tr>
            <td style="text-align:center;color:#a7b4c8">${i+1}</td>
            <td><span class="no-stok">${r.no_stock || '—'}</span></td>
            <td>${r.jns_darah || '—'}</td>
            <td><strong>${r.gol_rh_kantong || '—'}</strong></td>
            <td>${statusBadge(r.status, r.is_temp)}</td>
            <td>${fmtDate(r.tgl_ambil)}</td>
            <td>${fmtDate(r.tgl_produksi)}</td>
            <td>${kadTxt}</td>
            <td>${r.pemeriksa || '—'}</td>
            <td style="white-space:nowrap">
                ${r.is_temp
                    ? `<button class="btn-act btn-del" onclick="removeTempRow('${r.id}')" title="Batalkan Scan"><i class="bi bi-trash3"></i></button>`
                    : `<button class="btn-act" onclick="editCt(${r.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                       <button class="btn-act btn-del" onclick="deleteCt(${r.id})" title="Hapus"><i class="bi bi-trash3"></i></button>`
                }
            </td>
        </tr>`;
    }).join('');
}

// ── Edit ───────────────────────────────────────────────────────
window.editCt = async function (id) {
    try {
        const res  = await fetch(`${BASE}/${id}`, { headers: { 'Accept':'application/json', 'X-CSRF-TOKEN':CSRF } });
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
        const res  = await fetch(`${BASE}/${editingCtId}`, {
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
        const res  = await fetch(`${BASE}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json' } });
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

document.getElementById('btnFpup').addEventListener('click', bukaCariData);
document.getElementById('txtCari').addEventListener('input', () => bukaCariData());
document.getElementById('modalCari').addEventListener('click', e => {
    if (e.target === document.getElementById('modalCari')) document.getElementById('modalCari').style.display = 'none';
});

document.getElementById('btnSimpan').addEventListener('click', async () => {
    if (!window.tempStock) { showAlert('Scan No Stock terlebih dahulu.', 'warning'); return; }
    await simpanKantong(window.tempStock.stok, window.tempStock.no_stock);
    window.tempStock = null;
});

document.getElementById('inputKodePtgs').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); scanPetugas(); }
});
document.getElementById('inputKodePtgs').addEventListener('blur', scanPetugas);

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
    if (e.key === 'F4') { e.preventDefault(); bukaCariData(); }
    if (e.key === 'F8') { e.preventDefault(); document.getElementById('btnSimpan').click(); }
});
</script>
@endpush