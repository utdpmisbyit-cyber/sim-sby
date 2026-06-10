@extends('layouts.index')

@section('title', 'Pelayanan Darah')

@push('styles')
<style>
:root {
    --bd-red:#C0392B; --bd-red-lt:#FDEDEC; --bd-red-md:#E74C3C;
    --bd-navy:#1A2744; --bd-navy-lt:#EEF1F8;
    --bd-teal:#148F77; --bd-teal-lt:#E8F8F5;
    --bd-gold:#D4AC0D; --bd-gold-lt:#FEF9E7;
    --bd-muted:#6C7A8D; --bd-line:#E8ECF2; --bd-surface:#F5F7FA;
    --r-sm:6px; --r-md:10px; --r-lg:14px;
}
*{box-sizing:border-box}
.bd-wrap{padding:24px 28px;font-family:'Nunito','Segoe UI',sans-serif}
/* ── Header ── */
.bd-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.bd-title{display:flex;align-items:center;gap:12px}
.bd-title-icon{width:44px;height:44px;background:var(--bd-red);border-radius:var(--r-md);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;flex-shrink:0}
.bd-title h1{font-size:20px;font-weight:800;color:var(--bd-navy);margin:0;line-height:1.2}
.bd-title p{font-size:13px;color:var(--bd-muted);margin:0}
.bd-btn-new{background:var(--bd-red);color:#fff;border:none;padding:9px 18px;border-radius:var(--r-md);font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:7px;text-decoration:none;cursor:pointer;transition:.15s}
.bd-btn-new:hover{background:var(--bd-red-md);color:#fff}
/* ── Stats ── */
.bd-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:20px}
.bd-stat{background:#fff;border:1px solid var(--bd-line);border-radius:var(--r-lg);padding:16px 18px;border-left-width:4px}
.bd-stat.c-navy{border-left-color:var(--bd-navy)}.bd-stat.c-red{border-left-color:var(--bd-red)}
.bd-stat.c-teal{border-left-color:var(--bd-teal)}.bd-stat.c-gold{border-left-color:var(--bd-gold)}
.stat-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bd-muted);margin-bottom:5px}
.stat-val{font-size:26px;font-weight:800;color:var(--bd-navy);line-height:1}
.stat-val.sm{font-size:17px}.stat-sub{font-size:12px;color:var(--bd-muted);margin-top:4px}
/* ── Filter ── */
.bd-filter{background:#fff;border:1px solid var(--bd-line);border-radius:var(--r-lg);padding:14px 18px;margin-bottom:18px}
.bd-filter form{display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end}
.bd-filter label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bd-muted);display:block;margin-bottom:4px}
.bd-filter input,.bd-filter select{height:35px;border:1px solid var(--bd-line);border-radius:var(--r-sm);padding:0 10px;font-size:13px;color:var(--bd-navy);background:var(--bd-surface);outline:none;transition:border-color .15s}
.bd-filter input:focus,.bd-filter select:focus{border-color:var(--bd-red);background:#fff}
.f-search{flex:1;min-width:200px}
.btn-f{height:35px;padding:0 16px;border-radius:var(--r-sm);font-size:13px;font-weight:600;cursor:pointer;border:none;display:inline-flex;align-items:center;gap:6px;text-decoration:none}
.btn-f.apply{background:var(--bd-navy);color:#fff}.btn-f.reset{background:var(--bd-surface);color:var(--bd-muted);border:1px solid var(--bd-line)}
/* ── Table ── */
.bd-table-wrap{background:#fff;border:1px solid var(--bd-line);border-radius:var(--r-lg);overflow:hidden}
.bd-table-head{display:flex;align-items:center;justify-content:space-between;padding:13px 18px;border-bottom:1px solid var(--bd-line)}
.bd-table-head h3{font-size:14px;font-weight:700;color:var(--bd-navy);margin:0;display:flex;align-items:center;gap:7px}
.bd-count{font-size:12px;color:var(--bd-muted);background:var(--bd-surface);padding:3px 10px;border-radius:20px;border:1px solid var(--bd-line)}
table.bdt{width:100%;border-collapse:collapse;font-size:13px}
table.bdt thead tr{background:var(--bd-surface)}
table.bdt th{padding:10px 13px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bd-muted);white-space:nowrap}
table.bdt td{padding:11px 13px;border-bottom:1px solid var(--bd-line);vertical-align:middle;color:var(--bd-navy)}
table.bdt tbody tr:last-child td{border-bottom:none}
table.bdt tbody tr:hover td{background:#FAFBFD}
/* ── Badge ── */
.bs{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.bs::before{content:'';width:6px;height:6px;border-radius:50%;display:inline-block}
.bs-baru{background:var(--bd-navy-lt);color:var(--bd-navy)}.bs-baru::before{background:var(--bd-navy)}
.bs-lunas{background:var(--bd-teal-lt);color:var(--bd-teal)}.bs-lunas::before{background:var(--bd-teal)}
.bs-batal{background:var(--bd-red-lt);color:var(--bd-red)}.bs-batal::before{background:var(--bd-red)}
.bb{display:inline-block;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700}
.bb-tunai{background:var(--bd-gold-lt);color:#7D6608}.bb-kredit{background:var(--bd-navy-lt);color:var(--bd-navy)}
.dept-tag{background:var(--bd-navy-lt);color:var(--bd-navy);padding:1px 6px;border-radius:3px;font-size:10px;font-weight:700}
.nom{font-family:'Courier New',monospace;font-size:13px;font-weight:800;color:var(--bd-navy)}
.nom-sub{font-size:11px;color:var(--bd-muted);margin-top:2px}
/* ── Action buttons ── */
.td-acts{display:flex;align-items:center;gap:3px}
.act{width:29px;height:29px;border-radius:var(--r-sm);border:1px solid var(--bd-line);background:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:13px;color:var(--bd-muted);cursor:pointer;text-decoration:none;transition:.15s}
.act:hover{color:inherit}
.act.v:hover{background:var(--bd-navy-lt);color:var(--bd-navy);border-color:var(--bd-navy)}
.act.e:hover{background:var(--bd-gold-lt);color:var(--bd-gold);border-color:var(--bd-gold)}
.act.l:hover{background:var(--bd-teal-lt);color:var(--bd-teal);border-color:var(--bd-teal)}
.act.d:hover{background:var(--bd-red-lt);color:var(--bd-red);border-color:var(--bd-red)}
/* ── Pagination ── */
.bd-pag{padding:13px 18px;border-top:1px solid var(--bd-line);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.bd-pag-info{font-size:12px;color:var(--bd-muted)}
.bd-pag .pagination{margin:0;display:flex;list-style:none;gap:4px;padding:0}
.bd-pag .page-item .page-link{width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:var(--r-sm);border:1px solid var(--bd-line);font-size:13px;color:var(--bd-navy);background:#fff;text-decoration:none;transition:.15s}
.bd-pag .page-item.active .page-link{background:var(--bd-red);border-color:var(--bd-red);color:#fff;font-weight:700}
.bd-pag .page-item.disabled .page-link{color:var(--bd-muted);pointer-events:none}
/* ── Empty & Flash ── */
.bd-empty{padding:60px 20px;text-align:center}
.bd-empty-icon{font-size:40px;color:var(--bd-line);margin-bottom:14px}
.bd-empty h4{font-size:15px;font-weight:700;color:var(--bd-navy);margin-bottom:6px}
.bd-empty p{font-size:13px;color:var(--bd-muted);margin:0}
.bd-flash{padding:11px 16px;border-radius:var(--r-md);font-size:13px;display:flex;align-items:center;gap:10px;margin-bottom:16px}
.bd-flash.ok{background:var(--bd-teal-lt);color:var(--bd-teal);border:1px solid #A9DFBF}
.bd-flash.err{background:var(--bd-red-lt);color:var(--bd-red);border:1px solid #F5B7B1}
.nowrap{white-space:nowrap}

/* ══ MODAL ══════════════════════════════════════════════════════════ */
.bd-overlay{display:none;position:fixed;inset:0;background:rgba(15,25,50,.55);z-index:1040;align-items:flex-start;justify-content:center;padding:30px 16px;overflow-y:auto}
.bd-overlay.show{display:flex}
.bd-modal{background:#fff;border-radius:var(--r-lg);width:100%;max-width:920px;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:slideUp .2s ease}
@keyframes slideUp{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
.bd-modal-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--bd-line);background:var(--bd-surface);border-radius:var(--r-lg) var(--r-lg) 0 0}
.bd-modal-head h2{font-size:15px;font-weight:800;color:var(--bd-navy);margin:0;display:flex;align-items:center;gap:8px}
.bd-modal-head h2 i{color:var(--bd-red)}
.bd-modal-close{width:32px;height:32px;border-radius:var(--r-sm);border:1px solid var(--bd-line);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--bd-muted);font-size:15px;transition:.15s}
.bd-modal-close:hover{background:var(--bd-red-lt);color:var(--bd-red);border-color:var(--bd-red)}
.bd-modal-body{padding:20px}
.bd-modal-foot{padding:14px 20px;border-top:1px solid var(--bd-line);display:flex;justify-content:flex-end;gap:10px;background:var(--bd-surface);border-radius:0 0 var(--r-lg) var(--r-lg)}
/* Form inside modal */
.bd-form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.bd-col{display:flex;flex-direction:column;gap:14px}
.bd-card{background:#fff;border:1px solid var(--bd-line);border-radius:var(--r-md);overflow:hidden}
.bd-card-head{display:flex;align-items:center;gap:8px;padding:10px 14px;background:var(--bd-surface);border-bottom:1px solid var(--bd-line);font-size:12px;font-weight:700;color:var(--bd-navy);justify-content:space-between}
.bd-card-head i{color:var(--bd-red)}
.bd-card-body{padding:14px}
.bd-field{display:flex;flex-direction:column;gap:4px}
.bd-field label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--bd-muted)}
.bd-field .req{color:var(--bd-red)}
.bd-input{height:34px;border:1px solid var(--bd-line);border-radius:var(--r-sm);padding:0 10px;font-size:13px;color:var(--bd-navy);background:var(--bd-surface);outline:none;transition:border-color .15s;font-family:inherit;width:100%}
.bd-input:focus{border-color:var(--bd-red);background:#fff}
.bd-input[readonly]{background:#F0F3F8;color:var(--bd-muted);cursor:default}
textarea.bd-input{height:auto;padding:8px 10px;resize:vertical}
.bd-row2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.scan-row{display:flex;gap:8px}
.scan-row .bd-input{flex:1}
.scan-info{display:flex;align-items:center;gap:8px;margin-top:8px;padding:7px 12px;background:var(--bd-teal-lt);border:1px solid #A9DFBF;border-radius:var(--r-sm);font-size:12px;color:var(--bd-teal);font-weight:600}
.scan-err{display:flex;align-items:center;gap:8px;margin-top:8px;padding:7px 12px;background:var(--bd-red-lt);border:1px solid #F5B7B1;border-radius:var(--r-sm);font-size:12px;color:var(--bd-red);font-weight:600}
/* detail table */
.bd-detail-table{width:100%;border-collapse:collapse;font-size:12px}
.bd-detail-table thead tr{background:var(--bd-surface)}
.bd-detail-table th{padding:8px 10px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;color:var(--bd-muted);white-space:nowrap}
.bd-detail-table td{padding:6px 6px;border-bottom:1px solid var(--bd-line);vertical-align:middle}
.bd-detail-table tbody tr:last-child td{border-bottom:none}
.d-input{height:28px;border:1px solid var(--bd-line);border-radius:4px;padding:0 6px;font-size:12px;color:var(--bd-navy);background:var(--bd-surface);outline:none;width:100%;box-sizing:border-box}
.d-input:focus{border-color:var(--bd-red);background:#fff}
.btn-del-row{width:24px;height:24px;border-radius:4px;border:1px solid var(--bd-line);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--bd-muted);font-size:11px;transition:.15s}
.btn-del-row:hover{background:var(--bd-red-lt);color:var(--bd-red);border-color:var(--bd-red)}
.bd-total-val{font-family:'Courier New',monospace;font-size:13px;font-weight:800;color:var(--bd-red)}
/* nominal grid */
.bd-nominal-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.bd-nom-input{text-align:right;font-family:'Courier New',monospace;font-weight:700}
.bd-kembalian{color:var(--bd-gold) !important}
/* btn */
.bd-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:var(--r-md);font-size:13px;font-weight:700;cursor:pointer;border:none;text-decoration:none;transition:.15s}
.bd-btn-red{background:var(--bd-red);color:#fff}.bd-btn-red:hover{background:#A93226;color:#fff}
.bd-btn-navy{background:var(--bd-navy);color:#fff}.bd-btn-navy:hover{background:#0F1A2E;color:#fff}
.bd-btn-teal{background:var(--bd-teal);color:#fff}.bd-btn-teal:hover{background:#0E6B59;color:#fff}
.bd-btn-ghost{background:var(--bd-surface);color:var(--bd-muted);border:1px solid var(--bd-line)}.bd-btn-ghost:hover{background:var(--bd-line);color:var(--bd-navy)}
.bd-btn-danger{background:var(--bd-red-lt);color:var(--bd-red);border:1px solid #F5B7B1}.bd-btn-danger:hover{background:var(--bd-red);color:#fff}
.bd-btn-sm{padding:5px 11px;font-size:12px;border-radius:var(--r-sm)}
/* show view */
.bd-info-table{width:100%;border-collapse:collapse;font-size:13px}
.bd-info-table th{padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:var(--bd-muted);background:var(--bd-surface);white-space:nowrap;width:42%;border-bottom:1px solid var(--bd-line)}
.bd-info-table td{padding:9px 14px;color:var(--bd-navy);border-bottom:1px solid var(--bd-line)}
.bd-info-table tr:last-child th,.bd-info-table tr:last-child td{border-bottom:none}
.bd-gol{background:var(--bd-red-lt);color:var(--bd-red);padding:2px 8px;border-radius:4px;font-size:12px;font-weight:700}
.bd-nom-lg{font-family:'Courier New',monospace;font-weight:800;font-size:15px;color:var(--bd-navy)}
.m-no-badge{background:var(--bd-navy-lt);color:var(--bd-navy);font-size:12px;font-weight:800;padding:4px 12px;border-radius:20px;border:1px solid var(--bd-navy)}
@media(max-width:768px){
    .bd-wrap{padding:14px}
    .bd-stats{grid-template-columns:repeat(2,1fr)}
    table.bdt th:nth-child(n+5),table.bdt td:nth-child(n+5){display:none}
    .bd-form-grid{grid-template-columns:1fr}
    .bd-overlay{padding:10px}
}
</style>
@endpush

@section('content')
<div class="bd-wrap">

    {{-- Header --}}
    <div class="bd-header">
        <div class="bd-title">
            <div class="bd-title-icon"><i class="fas fa-tint"></i></div>
            <div>
                <h1>Pelayanan Darah</h1>
                <p>Manajemen kasir &amp; pembayaran darah</p>
            </div>
        </div>
        <button class="bd-btn-new" onclick="openCreate()">
            <i class="fas fa-plus"></i> Buat Pelayanan Baru
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bd-flash ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bd-flash err"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="bd-stats">
        <div class="bd-stat c-navy">
            <div class="stat-lbl">Total</div>
            <div class="stat-val">{{ $list->total() }}</div>
            <div class="stat-sub">semua pelayanan</div>
        </div>
        <div class="bd-stat c-red">
            <div class="stat-lbl">Baru</div>
            <div class="stat-val">{{ $list->getCollection()->where('status','baru')->count() }}</div>
            <div class="stat-sub">halaman ini</div>
        </div>
        <div class="bd-stat c-teal">
            <div class="stat-lbl">Lunas</div>
            <div class="stat-val">{{ $list->getCollection()->where('status','lunas')->count() }}</div>
            <div class="stat-sub">halaman ini</div>
        </div>
        <div class="bd-stat c-gold">
            <div class="stat-lbl">Total Biaya</div>
            <div class="stat-val sm">Rp {{ number_format($list->getCollection()->sum('total_biaya'),0,',','.') }}</div>
            <div class="stat-sub">halaman ini</div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bd-filter">
        <form method="GET" action="{{ route('unit.bank_darah.pelayanan_darah.index') }}">
            <div>
                <label>Cari</label>
                <input type="text" name="search" class="f-search"
                       placeholder="No. pelayanan, FPUP, nama pasien…"
                       value="{{ $filters['search'] ?? '' }}">
            </div>
            <div>
                <label>Status</label>
                <select name="status" style="width:120px">
                    <option value="">Semua</option>
                    @foreach(['baru'=>'Baru','lunas'=>'Lunas','batal'=>'Batal'] as $k=>$v)
                        <option value="{{ $k }}" {{ ($filters['status'] ?? '') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Dari</label>
                <input type="date" name="dari" value="{{ $filters['dari'] ?? '' }}" style="width:140px">
            </div>
            <div>
                <label>Sampai</label>
                <input type="date" name="sampai" value="{{ $filters['sampai'] ?? '' }}" style="width:140px">
            </div>
            <div style="display:flex;gap:6px;padding-top:18px">
                <button type="submit" class="btn-f apply"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('unit.bank_darah.pelayanan_darah.index') }}" class="btn-f reset"><i class="fas fa-times"></i> Reset</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bd-table-wrap">
        <div class="bd-table-head">
            <h3><i class="fas fa-tint" style="color:var(--bd-red)"></i> Daftar Pelayanan</h3>
            <span class="bd-count">{{ $list->total() }} data</span>
        </div>

        @if($list->isEmpty())
            <div class="bd-empty">
                <div class="bd-empty-icon"><i class="fas fa-tint-slash"></i></div>
                <h4>Belum ada data pelayanan</h4>
                <p>Klik "Buat Pelayanan Baru" untuk menambahkan transaksi pertama.</p>
            </div>
        @else
        <div style="overflow-x:auto">
            <table class="bdt">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Pelayanan</th>
                        <th>Tgl. Pelayanan</th>
                        <th>Pasien &amp; RS</th>
                        <th>No. FPUP</th>
                        <th>Cara Bayar</th>
                        <th>Total Biaya</th>
                        <th>Status</th>
                        <th style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($list as $i => $row)
                <tr>
                    <td class="nowrap" style="color:var(--bd-muted);font-size:11px">{{ $list->firstItem() + $i }}</td>
                    <td class="nowrap">
                        <a href="javascript:void(0)" onclick="openShow({{ $row->id }})"
                           style="font-weight:800;color:var(--bd-red);font-size:13px;text-decoration:none;cursor:pointer">
                            {{ $row->no_pelayanan }}
                        </a>
                        @if($row->no_pemberian)
                            <div style="font-size:10px;color:var(--bd-muted);margin-top:2px">
                                <i class="fas fa-link" style="font-size:9px"></i> {{ $row->no_pemberian }}
                            </div>
                        @endif
                    </td>
                    <td class="nowrap" style="font-size:12px">
                        <div style="font-weight:600;color:var(--bd-navy)">
                            {{ \Carbon\Carbon::parse($row->tgl_pelayanan)->format('d M Y') }}
                        </div>
                        @if($row->jam_pelayanan)
                            <div style="color:var(--bd-muted)">{{ \Carbon\Carbon::parse($row->jam_pelayanan)->format('H:i') }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13px">{{ $row->nama_pasien ?? '-' }}</div>
                        <div style="font-size:11px;color:var(--bd-muted);margin-top:2px;display:flex;flex-wrap:wrap;gap:4px;align-items:center">
                            @if($row->nama_rs)
                                <span><i class="fas fa-hospital" style="font-size:10px"></i> {{ Str::limit($row->nama_rs,28) }}</span>
                            @endif
                            @if($row->bagian_rs)
                                <span class="dept-tag">{{ $row->bagian_rs }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="nowrap" style="font-size:12px;color:var(--bd-muted)">{{ $row->no_fpup ?? '-' }}</td>
                    <td class="nowrap">
                        @if($row->cara_bayar)
                            <span class="bb {{ strtolower($row->cara_bayar)==='tunai'?'bb-tunai':'bb-kredit' }}">
                                {{ strtoupper($row->cara_bayar) }}
                            </span>
                        @endif
                        @if($row->jns_biaya)
                            <div style="font-size:11px;color:var(--bd-muted);margin-top:3px">{{ $row->jns_biaya }}</div>
                        @endif
                    </td>
                    <td class="nowrap">
                        <div class="nom">Rp {{ number_format($row->total_biaya,0,',','.') }}</div>
                        @if($row->diskon > 0)
                            <div class="nom-sub">diskon <span style="color:var(--bd-teal)">Rp {{ number_format($row->diskon,0,',','.') }}</span></div>
                        @endif
                    </td>
                    <td class="nowrap"><span class="bs bs-{{ $row->status }}">{{ ucfirst($row->status) }}</span></td>
                    <td>
                        <div class="td-acts">
                            <button class="act v" title="Detail" onclick="openShow({{ $row->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($row->status === 'baru')
                                <button class="act e" title="Edit" onclick="openEdit({{ $row->id }})">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('unit.bank_darah.pelayanan_darah.update-status', $row) }}"
                                      style="display:inline" onsubmit="return confirm('Tandai sebagai LUNAS?')">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="lunas">
                                    <button type="submit" class="act l" title="Tandai Lunas">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            @if($row->status !== 'lunas')
                                <form method="POST" action="{{ route('unit.bank_darah.pelayanan_darah.destroy', $row) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Hapus pelayanan ini?\nTidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="act d" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($list->hasPages())
            <div class="bd-pag">
                <span class="bd-pag-info">
                    Menampilkan {{ $list->firstItem() }}–{{ $list->lastItem() }} dari {{ $list->total() }} data
                </span>
                {{ $list->appends($filters)->links() }}
            </div>
        @endif
        @endif
    </div>
</div>

{{-- ══ MODAL CREATE ══════════════════════════════════════════════════ --}}
<div class="bd-overlay" id="modal-create">
  <div class="bd-modal">
    <div class="bd-modal-head">
        <h2><i class="fas fa-plus-circle"></i> Buat Pelayanan Darah</h2>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="m-no-badge" id="badge-no-create">—</span>
            <button class="bd-modal-close" onclick="closeModal('modal-create')"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <div class="bd-modal-body" style="max-height:75vh;overflow-y:auto">
        @if($errors->any())
            <div class="bd-flash err" style="margin-bottom:14px">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Kesalahan:</strong>
                    <ul style="margin:3px 0 0 14px;padding:0">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('unit.bank_darah.pelayanan_darah.store') }}" id="form-create">
            @csrf
            <div class="bd-form-grid">

                {{-- Kolom Kiri --}}
                <div class="bd-col">

                    {{-- Scan --}}
                    <div class="bd-card">
                        <div class="bd-card-head"><span><i class="fas fa-qrcode"></i> Scan / Cari No. FPUP</span></div>
                        <div class="bd-card-body">
                            <div class="scan-row">
                                <input type="text" id="scan-input-c" placeholder="Ketik No. FPUP / No. Pemberian…" class="bd-input"
                                       onkeydown="if(event.key==='Enter'){event.preventDefault();doScan('c')}">
                                <button type="button" class="bd-btn bd-btn-navy bd-btn-sm" onclick="doScan('c')">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                            <div class="scan-info" id="scan-info-c" style="display:none">
                                <i class="fas fa-check-circle"></i> <span id="scan-info-text-c"></span>
                            </div>
                            <div class="scan-err" id="scan-err-c" style="display:none">
                                <i class="fas fa-exclamation-circle"></i> <span id="scan-err-text-c"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Pasien --}}
                    <div class="bd-card">
                        <div class="bd-card-head"><span><i class="fas fa-user"></i> Informasi Pasien</span></div>
                        <div class="bd-card-body">
                            <input type="hidden" name="pemberian_darah_id" id="c_pemberian_darah_id">
                            <div class="bd-row2">
                                <div class="bd-field">
                                    <label>No. FPUP</label>
                                    <input type="text" name="no_fpup" id="c_no_fpup" class="bd-input" value="{{ old('no_fpup') }}" placeholder="c260512031">
                                </div>
                                <div class="bd-field">
                                    <label>No. Pemberian</label>
                                    <input type="text" name="no_pemberian" id="c_no_pemberian" class="bd-input" value="{{ old('no_pemberian') }}">
                                </div>
                            </div>
                            <div class="bd-field" style="margin-top:10px">
                                <label>Nama Pasien <span class="req">*</span></label>
                                <input type="text" name="nama_pasien" id="c_nama_pasien" class="bd-input" value="{{ old('nama_pasien') }}" required>
                            </div>
                            <div class="bd-row2" style="margin-top:10px">
                                <div class="bd-field">
                                    <label>Tgl. FPUP <span class="req">*</span></label>
                                    <input type="date" name="tgl_fpup" class="bd-input" value="{{ old('tgl_fpup', date('Y-m-d')) }}" required>
                                </div>
                                <div class="bd-field">
                                    <label>No. Register</label>
                                    <input type="text" name="no_register" id="c_no_register" class="bd-input" value="{{ old('no_register') }}">
                                </div>
                            </div>
                            <div class="bd-row2" style="margin-top:10px">
                                <div class="bd-field">
                                    <label>Gol. Darah</label>
                                    <select name="golongan_darah" id="c_golongan_darah" class="bd-input">
                                        <option value="">-</option>
                                        @foreach(['A','B','AB','O'] as $g)
                                            <option value="{{ $g }}" {{ old('golongan_darah')===$g?'selected':'' }}>{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="bd-field">
                                    <label>Rhesus</label>
                                    <select name="rhesus" id="c_rhesus" class="bd-input">
                                        <option value="">-</option>
                                        <option value="+" {{ old('rhesus')==='+' ?'selected':'' }}>Positif (+)</option>
                                        <option value="-" {{ old('rhesus')==='-' ?'selected':'' }}>Negatif (-)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bd-field" style="margin-top:10px">
                                <label>Alamat OS</label>
                                <input type="text" name="alamat_os" id="c_alamat_os" class="bd-input" value="{{ old('alamat_os') }}">
                            </div>
                        </div>
                    </div>

                    {{-- RS --}}
                    <div class="bd-card">
                        <div class="bd-card-head"><span><i class="fas fa-hospital"></i> Rumah Sakit</span></div>
                        <div class="bd-card-body">
                            <div class="bd-row2">
                                <div class="bd-field">
                                    <label>Kode RS</label>
                                    <input type="text" name="kode_rs" id="c_kode_rs" class="bd-input" value="{{ old('kode_rs') }}">
                                </div>
                                <div class="bd-field">
                                    <label>Jenis RS</label>
                                    <input type="text" name="jenis_rs" id="c_jenis_rs" class="bd-input" value="{{ old('jenis_rs') }}">
                                </div>
                            </div>
                            <div class="bd-field" style="margin-top:10px">
                                <label>Nama RS</label>
                                <input type="text" name="nama_rs" id="c_nama_rs" class="bd-input" value="{{ old('nama_rs') }}">
                            </div>
                            <div class="bd-row2" style="margin-top:10px">
                                <div class="bd-field">
                                    <label>Bagian / Ruang</label>
                                    <input type="text" name="bagian_rs" id="c_bagian_rs" class="bd-input" value="{{ old('bagian_rs') }}" placeholder="ICU, VIP…">
                                </div>
                                <div class="bd-field">
                                    <label>Kelas Rawat</label>
                                    <input type="text" name="kelas_rawat" id="c_kelas_rawat" class="bd-input" value="{{ old('kelas_rawat') }}">
                                </div>
                            </div>
                            <div class="bd-field" style="margin-top:10px">
                                <label>Nama Dokter</label>
                                <input type="text" name="nama_dokter" id="c_nama_dokter" class="bd-input" value="{{ old('nama_dokter') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="bd-col">
                    {{-- Pembayaran --}}
                    <div class="bd-card">
                        <div class="bd-card-head"><span><i class="fas fa-receipt"></i> Data Pembayaran</span></div>
                        <div class="bd-card-body">
                            <div class="bd-row2">
                                <div class="bd-field">
                                    <label>Tgl. Pelayanan <span class="req">*</span></label>
                                    <input type="date" name="tgl_pelayanan" class="bd-input" value="{{ old('tgl_pelayanan', date('Y-m-d')) }}" required>
                                </div>
                                <div class="bd-field">
                                    <label>Jam</label>
                                    <input type="time" name="jam_pelayanan" class="bd-input" value="{{ old('jam_pelayanan', date('H:i')) }}">
                                </div>
                            </div>
                            <div class="bd-row2" style="margin-top:10px">
                                <div class="bd-field">
                                    <label>Cara Bayar</label>
                                    <select name="cara_bayar" id="c_cara_bayar" class="bd-input">
                                        <option value="">-</option>
                                        @foreach(\App\Models\PermintaanFpup::CARA_BAYAR as $cb)
                                            <option value="{{ $cb }}" {{ old('cara_bayar')===$cb?'selected':'' }}>{{ $cb }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="bd-field">
                                    {{-- Jenis Biaya dari tabel jenis_biaya --}}
                                    <label>Jenis Biaya</label>
                                    <select name="jns_biaya" id="c_jns_biaya" class="bd-input">
                                        <option value="">- Pilih -</option>
                                        @foreach($jenisBiayaList as $jb)
                                            <option value="{{ $jb['nama'] }}" {{ old('jns_biaya')===$jb['nama']?'selected':'' }}>
                                                {{ $jb['nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="bd-row2" style="margin-top:10px">
                                <div class="bd-field">
                                    <label>Cara Pembayaran</label>
                                    <input type="text" name="cara_pembayaran" class="bd-input" value="{{ old('cara_pembayaran') }}" placeholder="Transfer, EDC…">
                                </div>
                                <div class="bd-field">
                                    <label>No. Faktur</label>
                                    <input type="text" name="no_faktur" class="bd-input" value="{{ old('no_faktur') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Detail Darah --}}
                    <div class="bd-card">
                        <div class="bd-card-head">
                            <span><i class="fas fa-tint"></i> Detail Darah</span>
                            <button type="button" class="bd-btn bd-btn-red bd-btn-sm" onclick="addRow('c')">
                                <i class="fas fa-plus"></i> Baris
                            </button>
                        </div>
                        <div class="bd-card-body" style="padding:0;overflow-x:auto">
                            <table class="bd-detail-table">
                                <thead><tr>
                                    <th>No.Stok</th><th>Jenis</th><th>Gol</th>
                                    <th>Jml</th><th>CC</th><th>Harga/Sat</th><th>Total</th><th></th>
                                </tr></thead>
                                <tbody id="detail-body-c"></tbody>
                                <tfoot><tr>
                                    <td colspan="6" style="text-align:right;padding:9px 10px;font-size:11px;font-weight:700;color:var(--bd-muted)">TOTAL BIAYA</td>
                                    <td style="padding:9px 10px"><span id="total-display-c" class="bd-total-val">Rp 0</span></td>
                                    <td></td>
                                </tr></tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- Nominal --}}
                    <div class="bd-card">
                        <div class="bd-card-head"><span><i class="fas fa-calculator"></i> Rincian Biaya</span></div>
                        <div class="bd-card-body">
                            <div class="bd-nominal-grid">
                                <div class="bd-field">
                                    <label>Total Biaya</label>
                                    <input type="number" name="total_biaya" id="c_total_biaya" class="bd-input bd-nom-input" value="{{ old('total_biaya',0) }}" readonly>
                                </div>
                                <div class="bd-field">
                                    <label>Diskon</label>
                                    <input type="number" name="diskon" id="c_diskon" class="bd-input bd-nom-input" value="{{ old('diskon',0) }}" oninput="hitungBayar('c')">
                                </div>
                                <div class="bd-field">
                                    <label>Total Bayar</label>
                                    <input type="number" name="total_bayar" id="c_total_bayar" class="bd-input bd-nom-input" value="{{ old('total_bayar',0) }}" readonly>
                                </div>
                                <div class="bd-field">
                                    <label>Terbayar</label>
                                    <input type="number" name="terbayar" id="c_terbayar" class="bd-input bd-nom-input" value="{{ old('terbayar',0) }}" oninput="hitungKembalian('c')">
                                </div>
                                <div class="bd-field" style="grid-column:1/-1">
                                    <label>Kembalian</label>
                                    <input type="number" name="kembalian" id="c_kembalian" class="bd-input bd-nom-input bd-kembalian" value="{{ old('kembalian',0) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bd-card">
                        <div class="bd-card-head"><span><i class="fas fa-sticky-note"></i> Keterangan</span></div>
                        <div class="bd-card-body">
                            <textarea name="keterangan" class="bd-input" rows="2" placeholder="Catatan…">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>{{-- /kol kanan --}}
            </div>
        </form>
    </div>
    <div class="bd-modal-foot">
        <button class="bd-btn bd-btn-ghost" onclick="closeModal('modal-create')"><i class="fas fa-times"></i> Batal</button>
        <button class="bd-btn bd-btn-red" onclick="document.getElementById('form-create').submit()">
            <i class="fas fa-save"></i> Simpan Pelayanan
        </button>
    </div>
  </div>
</div>

{{-- ══ MODAL EDIT ═════════════════════════════════════════════════════ --}}
<div class="bd-overlay" id="modal-edit">
  <div class="bd-modal">
    <div class="bd-modal-head">
        <h2><i class="fas fa-pen"></i> Edit Pelayanan Darah</h2>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="m-no-badge" id="badge-no-edit">—</span>
            <button class="bd-modal-close" onclick="closeModal('modal-edit')"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <div class="bd-modal-body" style="max-height:75vh;overflow-y:auto" id="edit-body">
        <div style="text-align:center;padding:40px;color:var(--bd-muted)">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat data…
        </div>
    </div>
    <div class="bd-modal-foot">
        <button class="bd-btn bd-btn-ghost" onclick="closeModal('modal-edit')"><i class="fas fa-times"></i> Batal</button>
        <button class="bd-btn bd-btn-red" onclick="submitEdit()">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </div>
  </div>
</div>

{{-- ══ MODAL SHOW ══════════════════════════════════════════════════════ --}}
<div class="bd-overlay" id="modal-show">
  <div class="bd-modal">
    <div class="bd-modal-head">
        <h2><i class="fas fa-eye"></i> Detail Pelayanan Darah</h2>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="m-no-badge" id="badge-no-show">—</span>
            <button class="bd-modal-close" onclick="closeModal('modal-show')"><i class="fas fa-times"></i></button>
        </div>
    </div>
    <div class="bd-modal-body" style="max-height:75vh;overflow-y:auto" id="show-body">
        <div style="text-align:center;padding:40px;color:var(--bd-muted)">
            <i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat data…
        </div>
    </div>
    <div class="bd-modal-foot" id="show-foot">
        <button class="bd-btn bd-btn-ghost" onclick="closeModal('modal-show')"><i class="fas fa-times"></i> Tutup</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// ── Route URLs ────────────────────────────────────────────────────
const URL_STORE       = "{{ route('unit.bank_darah.pelayanan_darah.store') }}";
const URL_SCAN        = "{{ route('unit.bank_darah.pelayanan_darah.scan-pemberian') }}";
const URL_NEXT_NO     = "{{ route('unit.bank_darah.pelayanan_darah.next-no-pelayanan') }}";
const URL_BASE        = "{{ url('/') }}/{{ ltrim(parse_url(route('unit.bank_darah.pelayanan_darah.index'), PHP_URL_PATH), '/') }}";
const CSRF            = "{{ csrf_token() }}";

// Jenis biaya list dari server (untuk populate dropdown di modal edit)
const JENIS_BIAYA_LIST = @json($jenisBiayaList);

// ── Modal helpers ─────────────────────────────────────────────────
function openModal(id){ document.getElementById(id).classList.add('show'); document.body.style.overflow='hidden'; }
function closeModal(id){ document.getElementById(id).classList.remove('show'); document.body.style.overflow=''; }
document.querySelectorAll('.bd-overlay').forEach(el=>{
    el.addEventListener('click', e=>{ if(e.target===el){ el.classList.remove('show'); document.body.style.overflow=''; } });
});
document.addEventListener('keydown', e=>{
    if(e.key==='Escape') document.querySelectorAll('.bd-overlay.show').forEach(m=>{ m.classList.remove('show'); document.body.style.overflow=''; });
});

// ── CREATE ────────────────────────────────────────────────────────
function openCreate(){
    resetRows('c');
    addRow('c');
    openModal('modal-create');
    fetch(URL_NEXT_NO).then(r=>r.json()).then(d=>{
        document.getElementById('badge-no-create').textContent = d.no_pelayanan;
    });
}

// ── SCAN ──────────────────────────────────────────────────────────
/**
 * doScan: cari data berdasarkan no_fpup / no_pemberian.
 * - Data pasien   → dari permintaan_fpup (via service)
 * - Detail darah  → dari pemberian_darah_detail
 * - Jenis biaya   → dari tabel jenis_biaya (sudah di-embed sebagai JENIS_BIAYA_LIST)
 */
function doScan(pfx){
    const q = document.getElementById('scan-input-'+pfx).value.trim();
    if(!q) return;

    // Reset feedback
    hideScanFeedback(pfx);

    fetch(URL_SCAN + '?q=' + encodeURIComponent(q))
        .then(r => {
            if(!r.ok) throw new Error('not_found');
            return r.json();
        })
        .then(d => {
            // ── Identitas pemberian ──────────────────────────────────
            svField(pfx, 'pemberian_darah_id', d.id || '');
            svField(pfx, 'no_fpup',            d.no_fpup || '');
            svField(pfx, 'no_pemberian',        d.no_pemberian || '');

            // ── Data pasien (dari permintaan_fpup) ───────────────────
            svField(pfx, 'nama_pasien',   d.nama_pasien || '');
            svField(pfx, 'no_register',   d.no_register || '');
            svField(pfx, 'golongan_darah', d.golongan_darah || '');
            svField(pfx, 'rhesus',        d.rhesus || '');
            svField(pfx, 'alamat_os',     d.alamat_os || '');

            // ── Data RS (dari permintaan_fpup) ───────────────────────
            svField(pfx, 'nama_rs',    d.nama_rs    || '');
            svField(pfx, 'kode_rs',    d.kode_rs    || '');
            svField(pfx, 'jenis_rs',   d.jenis_rs   || '');
            svField(pfx, 'bagian_rs',  d.bagian_rs  || '');
            svField(pfx, 'kelas_rawat', d.kelas_rawat || '');
            svField(pfx, 'nama_dokter', d.nama_dokter || '');

            // ── Pembayaran ────────────────────────────────────────────
            svField(pfx, 'cara_bayar', d.cara_bayar || '');
            // jns_biaya: set berdasarkan nama (value di select adalah nama)
            svField(pfx, 'jns_biaya', d.jns_biaya || '');

            // ── Detail darah dari pemberian_darah_detail ─────────────
            if(d.details && d.details.length > 0){
                resetRows(pfx);
                d.details.forEach(det => addRow(pfx, {
                    pemberian_darah_detail_id : det.id,
                    no_stok                   : det.no_stok,
                    jns_darah                 : det.jns_darah,
                    gol                       : det.gol,
                    rhesus                    : det.rhesus,
                    jumlah                    : det.jumlah,
                    cc                        : det.cc,
                    harga_satuan              : det.harga_satuan,
                }));
                hitungTotal(pfx);
            }

            // Tampilkan feedback sukses
            const si = document.getElementById('scan-info-' + pfx);
            const st = document.getElementById('scan-info-text-' + pfx);
            if(si && st){
                st.textContent = 'Data ditemukan: ' + (d.nama_pasien || d.no_fpup || q);
                si.style.display = 'flex';
            }
        })
        .catch(err => {
            const se = document.getElementById('scan-err-' + pfx);
            const st = document.getElementById('scan-err-text-' + pfx);
            if(se && st){
                st.textContent = 'Data tidak ditemukan untuk: ' + q;
                se.style.display = 'flex';
            }
        });
}

function hideScanFeedback(pfx){
    const si = document.getElementById('scan-info-' + pfx);
    const se = document.getElementById('scan-err-' + pfx);
    if(si) si.style.display = 'none';
    if(se) se.style.display = 'none';
}

/**
 * Set value ke input atau select berdasarkan id = pfx + '_' + field.
 * Untuk <select>: set .value langsung (pilih option yang cocok).
 */
function svField(pfx, field, val){
    const el = document.getElementById(pfx + '_' + field);
    if(!el) return;
    el.value = val;
}

// ── SHOW ──────────────────────────────────────────────────────────
function openShow(id){
    document.getElementById('show-body').innerHTML = loadingHtml();
    document.getElementById('show-foot').innerHTML = '<button class="bd-btn bd-btn-ghost" onclick="closeModal(\'modal-show\')"><i class="fas fa-times"></i> Tutup</button>';
    openModal('modal-show');

    fetch(URL_BASE + '/' + id, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
        .then(r => r.json())
        .then(d => {
            document.getElementById('badge-no-show').textContent = d.no_pelayanan;
            renderShow(d);
        })
        .catch(() => { document.getElementById('show-body').innerHTML='<p style="color:red;padding:20px">Gagal memuat data.</p>'; });
}

function renderShow(d){
    const fmt    = n => 'Rp ' + Math.round(n||0).toLocaleString('id-ID');
    const tgl    = s => s ? new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-';
    const bSt    = {'baru':'bs-baru','lunas':'bs-lunas','batal':'bs-batal'};
    const bByr   = s => s==='tunai' ? '<span class="bb bb-tunai">TUNAI</span>' : (s ? '<span class="bb bb-kredit">'+s.toUpperCase()+'</span>' : '-');

    let detailRows = '';
    (d.details||[]).forEach((r,i)=>{
        detailRows += `<tr>
            <td style="color:var(--bd-muted)">${i+1}</td>
            <td>${r.no_stok||'-'}</td>
            <td>${r.jns_darah||'-'}</td>
            <td>${r.gol ? '<span class="bd-gol">'+r.gol+(r.rhesus||'')+'</span>' : '-'}</td>
            <td>${r.jumlah}</td>
            <td>${r.cc||'-'}</td>
            <td>Rp ${Math.round(r.harga_satuan||0).toLocaleString('id-ID')}</td>
            <td style="font-weight:700">Rp ${Math.round(r.total_harga||0).toLocaleString('id-ID')}</td>
        </tr>`;
    });

    document.getElementById('show-body').innerHTML = `
    <div class="bd-form-grid">
        <div class="bd-col">
            <div class="bd-card">
                <div class="bd-card-head"><span><i class="fas fa-id-card"></i> Identitas Pelayanan</span></div>
                <div class="bd-card-body" style="padding:0">
                    <table class="bd-info-table">
                        <tr><th>No. Pelayanan</th><td style="font-weight:800;color:var(--bd-red)">${d.no_pelayanan}</td></tr>
                        <tr><th>Status</th><td><span class="bs ${bSt[d.status]||''}">${d.status||''}</span></td></tr>
                        <tr><th>No. FPUP</th><td>${d.no_fpup||'-'}</td></tr>
                        <tr><th>No. Pemberian</th><td>${d.no_pemberian||'-'}</td></tr>
                        <tr><th>Tgl. FPUP</th><td>${tgl(d.tgl_fpup)}</td></tr>
                        <tr><th>Tgl. Pelayanan</th><td>${tgl(d.tgl_pelayanan)}${d.jam_pelayanan?' <span style="color:var(--bd-muted);font-size:12px">'+d.jam_pelayanan.slice(0,5)+'</span>':''}</td></tr>
                        <tr><th>Petugas Kasir</th><td>${d.petugas_kasir||'-'}</td></tr>
                        <tr><th>No. Faktur</th><td>${d.no_faktur||'-'}</td></tr>
                    </table>
                </div>
            </div>
            <div class="bd-card">
                <div class="bd-card-head"><span><i class="fas fa-user"></i> Data Pasien</span></div>
                <div class="bd-card-body" style="padding:0">
                    <table class="bd-info-table">
                        <tr><th>Nama Pasien</th><td style="font-weight:700">${d.nama_pasien||'-'}</td></tr>
                        <tr><th>No. Register</th><td>${d.no_register||'-'}</td></tr>
                        <tr><th>Gol. Darah</th><td>${d.golongan_darah ? '<span class="bd-gol">'+d.golongan_darah+(d.rhesus||'')+'</span>' : '-'}</td></tr>
                        <tr><th>Alamat</th><td>${d.alamat_os||'-'}</td></tr>
                    </table>
                </div>
            </div>
            <div class="bd-card">
                <div class="bd-card-head"><span><i class="fas fa-hospital"></i> Rumah Sakit</span></div>
                <div class="bd-card-body" style="padding:0">
                    <table class="bd-info-table">
                        <tr><th>Kode RS</th><td>${d.kode_rs||'-'}</td></tr>
                        <tr><th>Nama RS</th><td style="font-weight:700">${d.nama_rs||'-'}</td></tr>
                        <tr><th>Jenis RS</th><td>${d.jenis_rs||'-'}</td></tr>
                        <tr><th>Bagian</th><td>${d.bagian_rs||'-'}</td></tr>
                        <tr><th>Kelas Rawat</th><td>${d.kelas_rawat||'-'}</td></tr>
                        <tr><th>Dokter</th><td>${d.nama_dokter||'-'}</td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="bd-col">
            <div class="bd-card">
                <div class="bd-card-head"><span><i class="fas fa-receipt"></i> Pembayaran</span></div>
                <div class="bd-card-body" style="padding:0">
                    <table class="bd-info-table">
                        <tr><th>Cara Bayar</th><td>${bByr(d.cara_bayar)}</td></tr>
                        <tr><th>Jenis Biaya</th><td>${d.jns_biaya||'-'}</td></tr>
                        <tr><th>Cara Pembayaran</th><td>${d.cara_pembayaran||'-'}</td></tr>
                    </table>
                </div>
            </div>
            <div class="bd-card">
                <div class="bd-card-head"><span><i class="fas fa-calculator"></i> Rincian Biaya</span></div>
                <div class="bd-card-body" style="padding:0">
                    <table class="bd-info-table">
                        <tr><th>Total Biaya</th><td><span class="bd-nom-lg">${fmt(d.total_biaya)}</span></td></tr>
                        <tr><th>Diskon</th><td style="color:var(--bd-teal);font-weight:600">${fmt(d.diskon)}</td></tr>
                        <tr style="border-top:2px solid var(--bd-line)"><th>Total Bayar</th><td><span class="bd-nom-lg" style="font-size:17px">${fmt(d.total_bayar)}</span></td></tr>
                        <tr><th>Terbayar</th><td><span class="bd-nom-lg">${fmt(d.terbayar)}</span></td></tr>
                        <tr><th>Kembalian</th><td><span class="bd-nom-lg" style="color:var(--bd-gold)">${fmt(d.kembalian)}</span></td></tr>
                    </table>
                </div>
            </div>
            <div class="bd-card">
                <div class="bd-card-head"><span><i class="fas fa-tint"></i> Detail Darah</span></div>
                <div class="bd-card-body" style="padding:0;overflow-x:auto">
                    <table class="bd-detail-table" style="font-size:12px">
                        <thead><tr><th>#</th><th>No.Stok</th><th>Jenis</th><th>Gol</th><th>Jml</th><th>CC</th><th>Harga/Sat</th><th>Total</th></tr></thead>
                        <tbody>${detailRows||'<tr><td colspan="8" style="text-align:center;padding:14px;color:var(--bd-muted)">Tidak ada detail</td></tr>'}</tbody>
                    </table>
                </div>
            </div>
            ${d.keterangan ? `<div class="bd-card"><div class="bd-card-head"><span><i class="fas fa-sticky-note"></i> Keterangan</span></div><div class="bd-card-body"><p style="font-size:13px;margin:0">${d.keterangan}</p></div></div>` : ''}
        </div>
    </div>`;

    // Footer buttons
    let foot = '<button class="bd-btn bd-btn-ghost" onclick="closeModal(\'modal-show\')"><i class="fas fa-times"></i> Tutup</button>';
    if(d.status === 'baru'){
        foot += `
        <form method="POST" action="${URL_BASE}/${d.id}/status" style="display:inline" onsubmit="return confirm('Tandai LUNAS?')">
            <input type="hidden" name="_token" value="${CSRF}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="status" value="lunas">
            <button type="submit" class="bd-btn bd-btn-teal"><i class="fas fa-check-circle"></i> Tandai Lunas</button>
        </form>
        <form method="POST" action="${URL_BASE}/${d.id}/status" style="display:inline" onsubmit="return confirm('Batalkan pelayanan ini?')">
            <input type="hidden" name="_token" value="${CSRF}">
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="status" value="batal">
            <button type="submit" class="bd-btn bd-btn-ghost"><i class="fas fa-ban"></i> Batalkan</button>
        </form>
        <button class="bd-btn bd-btn-navy" onclick="closeModal('modal-show');openEdit(${d.id})"><i class="fas fa-pen"></i> Edit</button>`;
    }
    if(d.status !== 'lunas'){
        foot += `
        <form method="POST" action="${URL_BASE}/${d.id}" style="display:inline" onsubmit="return confirm('Hapus pelayanan ini? Tidak dapat dibatalkan.')">
            <input type="hidden" name="_token" value="${CSRF}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="bd-btn bd-btn-danger"><i class="fas fa-trash"></i> Hapus</button>
        </form>`;
    }
    document.getElementById('show-foot').innerHTML = foot;
}

// ── EDIT ──────────────────────────────────────────────────────────
let currentEditId = null;
function openEdit(id){
    currentEditId = id;
    document.getElementById('edit-body').innerHTML = loadingHtml();
    openModal('modal-edit');

    fetch(URL_BASE + '/' + id, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} })
        .then(r => r.json())
        .then(d => {
            document.getElementById('badge-no-edit').textContent = d.no_pelayanan;
            renderEditForm(d);
        });
}

function renderEditForm(d){
    // Build jenis_biaya options
    const jbOptions = JENIS_BIAYA_LIST.map(jb =>
        `<option value="${escHtml(jb.nama)}" ${d.jns_biaya === jb.nama ? 'selected' : ''}>${escHtml(jb.nama)}</option>`
    ).join('');

    // Build cara_bayar options (dari konstanta PermintaanFpup::CARA_BAYAR)
    const caraBayarOptions = @json(\App\Models\PermintaanFpup::CARA_BAYAR)
        .map(cb => `<option value="${escHtml(cb)}" ${d.cara_bayar === cb ? 'selected' : ''}>${escHtml(cb)}</option>`)
        .join('');

    document.getElementById('edit-body').innerHTML = `
    <form method="POST" action="${URL_BASE}/${d.id}" id="form-edit">
        <input type="hidden" name="_token" value="${CSRF}">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="pemberian_darah_id" value="${d.pemberian_darah_id||''}">
        <div class="bd-form-grid">
            <div class="bd-col">
                <div class="bd-card">
                    <div class="bd-card-head"><span><i class="fas fa-user"></i> Informasi Pasien</span></div>
                    <div class="bd-card-body">
                        <div class="bd-row2">
                            <div class="bd-field"><label>No. FPUP</label><input type="text" name="no_fpup" class="bd-input" value="${escHtml(d.no_fpup||'')}"></div>
                            <div class="bd-field"><label>No. Pemberian</label><input type="text" name="no_pemberian" class="bd-input" value="${escHtml(d.no_pemberian||'')}"></div>
                        </div>
                        <div class="bd-field" style="margin-top:10px">
                            <label>Nama Pasien <span class="req">*</span></label>
                            <input type="text" name="nama_pasien" class="bd-input" value="${escHtml(d.nama_pasien||'')}" required>
                        </div>
                        <div class="bd-row2" style="margin-top:10px">
                            <div class="bd-field"><label>Tgl. FPUP <span class="req">*</span></label><input type="date" name="tgl_fpup" class="bd-input" value="${(d.tgl_fpup||'').slice(0,10)}" required></div>
                            <div class="bd-field"><label>No. Register</label><input type="text" name="no_register" class="bd-input" value="${escHtml(d.no_register||'')}"></div>
                        </div>
                        <div class="bd-row2" style="margin-top:10px">
                            <div class="bd-field"><label>Gol. Darah</label>
                                <select name="golongan_darah" class="bd-input">
                                    <option value="">-</option>
                                    ${['A','B','AB','O'].map(g=>`<option value="${g}" ${d.golongan_darah===g?'selected':''}>${g}</option>`).join('')}
                                </select>
                            </div>
                            <div class="bd-field"><label>Rhesus</label>
                                <select name="rhesus" class="bd-input">
                                    <option value="">-</option>
                                    <option value="+" ${d.rhesus==='+'?'selected':''}>Positif (+)</option>
                                    <option value="-" ${d.rhesus==='-'?'selected':''}>Negatif (-)</option>
                                </select>
                            </div>
                        </div>
                        <div class="bd-field" style="margin-top:10px"><label>Alamat OS</label><input type="text" name="alamat_os" class="bd-input" value="${escHtml(d.alamat_os||'')}"></div>
                    </div>
                </div>
                <div class="bd-card">
                    <div class="bd-card-head"><span><i class="fas fa-hospital"></i> Rumah Sakit</span></div>
                    <div class="bd-card-body">
                        <div class="bd-row2">
                            <div class="bd-field"><label>Kode RS</label><input type="text" name="kode_rs" class="bd-input" value="${escHtml(d.kode_rs||'')}"></div>
                            <div class="bd-field"><label>Jenis RS</label><input type="text" name="jenis_rs" class="bd-input" value="${escHtml(d.jenis_rs||'')}"></div>
                        </div>
                        <div class="bd-field" style="margin-top:10px"><label>Nama RS</label><input type="text" name="nama_rs" class="bd-input" value="${escHtml(d.nama_rs||'')}"></div>
                        <div class="bd-row2" style="margin-top:10px">
                            <div class="bd-field"><label>Bagian</label><input type="text" name="bagian_rs" class="bd-input" value="${escHtml(d.bagian_rs||'')}"></div>
                            <div class="bd-field"><label>Kelas Rawat</label><input type="text" name="kelas_rawat" class="bd-input" value="${escHtml(d.kelas_rawat||'')}"></div>
                        </div>
                        <div class="bd-field" style="margin-top:10px"><label>Nama Dokter</label><input type="text" name="nama_dokter" class="bd-input" value="${escHtml(d.nama_dokter||'')}"></div>
                    </div>
                </div>
            </div>
            <div class="bd-col">
                <div class="bd-card">
                    <div class="bd-card-head"><span><i class="fas fa-receipt"></i> Data Pembayaran</span></div>
                    <div class="bd-card-body">
                        <div class="bd-row2">
                            <div class="bd-field"><label>Tgl. Pelayanan <span class="req">*</span></label><input type="date" name="tgl_pelayanan" class="bd-input" value="${(d.tgl_pelayanan||'').slice(0,10)}" required></div>
                            <div class="bd-field"><label>Jam</label><input type="time" name="jam_pelayanan" class="bd-input" value="${(d.jam_pelayanan||'').slice(0,5)}"></div>
                        </div>
                        <div class="bd-row2" style="margin-top:10px">
                            <div class="bd-field"><label>Cara Bayar</label>
                                <select name="cara_bayar" class="bd-input">
                                    <option value="">-</option>${caraBayarOptions}
                                </select>
                            </div>
                            <div class="bd-field"><label>Jenis Biaya</label>
                                <select name="jns_biaya" class="bd-input">
                                    <option value="">- Pilih -</option>${jbOptions}
                                </select>
                            </div>
                        </div>
                        <div class="bd-row2" style="margin-top:10px">
                            <div class="bd-field"><label>Cara Pembayaran</label><input type="text" name="cara_pembayaran" class="bd-input" value="${escHtml(d.cara_pembayaran||'')}"></div>
                            <div class="bd-field"><label>No. Faktur</label><input type="text" name="no_faktur" class="bd-input" value="${escHtml(d.no_faktur||'')}"></div>
                        </div>
                    </div>
                </div>
                <div class="bd-card">
                    <div class="bd-card-head">
                        <span><i class="fas fa-tint"></i> Detail Darah</span>
                        <button type="button" class="bd-btn bd-btn-red bd-btn-sm" onclick="addRow('e')"><i class="fas fa-plus"></i> Baris</button>
                    </div>
                    <div class="bd-card-body" style="padding:0;overflow-x:auto">
                        <table class="bd-detail-table">
                            <thead><tr><th>No.Stok</th><th>Jenis</th><th>Gol</th><th>Jml</th><th>CC</th><th>Harga/Sat</th><th>Total</th><th></th></tr></thead>
                            <tbody id="detail-body-e"></tbody>
                            <tfoot><tr>
                                <td colspan="6" style="text-align:right;padding:9px 10px;font-size:11px;font-weight:700;color:var(--bd-muted)">TOTAL BIAYA</td>
                                <td style="padding:9px 10px"><span id="total-display-e" class="bd-total-val">Rp 0</span></td>
                                <td></td>
                            </tr></tfoot>
                        </table>
                    </div>
                </div>
                <div class="bd-card">
                    <div class="bd-card-head"><span><i class="fas fa-calculator"></i> Rincian Biaya</span></div>
                    <div class="bd-card-body">
                        <div class="bd-nominal-grid">
                            <div class="bd-field"><label>Total Biaya</label><input type="number" name="total_biaya" id="e_total_biaya" class="bd-input bd-nom-input" value="${d.total_biaya||0}" readonly></div>
                            <div class="bd-field"><label>Diskon</label><input type="number" name="diskon" id="e_diskon" class="bd-input bd-nom-input" value="${d.diskon||0}" oninput="hitungBayar('e')"></div>
                            <div class="bd-field"><label>Total Bayar</label><input type="number" name="total_bayar" id="e_total_bayar" class="bd-input bd-nom-input" value="${d.total_bayar||0}" readonly></div>
                            <div class="bd-field"><label>Terbayar</label><input type="number" name="terbayar" id="e_terbayar" class="bd-input bd-nom-input" value="${d.terbayar||0}" oninput="hitungKembalian('e')"></div>
                            <div class="bd-field" style="grid-column:1/-1"><label>Kembalian</label><input type="number" name="kembalian" id="e_kembalian" class="bd-input bd-nom-input bd-kembalian" value="${d.kembalian||0}" readonly></div>
                        </div>
                    </div>
                </div>
                <div class="bd-card">
                    <div class="bd-card-head"><span><i class="fas fa-sticky-note"></i> Keterangan</span></div>
                    <div class="bd-card-body"><textarea name="keterangan" class="bd-input" rows="2">${escHtml(d.keterangan||'')}</textarea></div>
                </div>
            </div>
        </div>
    </form>`;

    // Populate detail rows
    rowIdx.e = 0;
    (d.details||[]).forEach(r => addRow('e', {
        pemberian_darah_detail_id : r.pemberian_darah_detail_id,
        no_stok      : r.no_stok,
        jns_darah    : r.jns_darah,
        gol          : r.gol,
        rhesus       : r.rhesus,
        jumlah       : r.jumlah,
        cc           : r.cc,
        harga_satuan : r.harga_satuan,
    }));
    if(!d.details || !d.details.length) addRow('e');
    hitungTotal('e');
}

function submitEdit(){
    const f = document.getElementById('form-edit');
    if(f) f.submit();
}

// ── Detail rows ───────────────────────────────────────────────────
const rowIdx = {c:0, e:0};

function resetRows(pfx){
    rowIdx[pfx] = 0;
    const tb = document.getElementById('detail-body-'+pfx);
    if(tb) tb.innerHTML = '';
}

function addRow(pfx, data={}){
    const i     = rowIdx[pfx]++;
    const tbody = document.getElementById('detail-body-'+pfx);
    if(!tbody) return;

    const gols = ['','A','B','AB','O']
        .map(g => `<option value="${g}" ${(data.gol||'')===g?'selected':''}>${g||'-'}</option>`)
        .join('');

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="hidden" name="details[${i}][pemberian_darah_detail_id]" value="${data.pemberian_darah_detail_id||''}">
            <input type="text" name="details[${i}][no_stok]" class="d-input" value="${escHtml(data.no_stok||'')}" style="width:90px" placeholder="No.Stok">
        </td>
        <td><input type="text" name="details[${i}][jns_darah]" class="d-input" value="${escHtml(data.jns_darah||'')}" style="width:80px" placeholder="WB,PRC…"></td>
        <td><select name="details[${i}][gol]" class="d-input" style="width:52px">${gols}</select></td>
        <td><input type="number" name="details[${i}][jumlah]" class="d-input row-jumlah" value="${data.jumlah||1}" min="1" style="width:46px" oninput="hitungRow(this,'${pfx}')"></td>
        <td><input type="number" name="details[${i}][cc]" class="d-input" value="${data.cc||''}" style="width:50px"></td>
        <td><input type="number" name="details[${i}][harga_satuan]" class="d-input row-harga" value="${data.harga_satuan||0}" min="0" step="500" style="width:100px" oninput="hitungRow(this,'${pfx}')"></td>
        <td><span class="row-total" style="font-family:'Courier New',monospace;font-weight:700;font-size:12px;white-space:nowrap">Rp ${fmtN((data.jumlah||1)*(data.harga_satuan||0))}</span></td>
        <td><button type="button" class="btn-del-row" onclick="this.closest('tr').remove();hitungTotal('${pfx}')"><i class="fas fa-times"></i></button></td>`;
    tbody.appendChild(tr);
    hitungTotal(pfx);
}

function hitungRow(inp, pfx){
    const tr = inp.closest('tr');
    const j  = parseFloat(tr.querySelector('.row-jumlah').value)||0;
    const h  = parseFloat(tr.querySelector('.row-harga').value)||0;
    tr.querySelector('.row-total').textContent = 'Rp '+fmtN(j*h);
    hitungTotal(pfx);
}

function hitungTotal(pfx){
    let total = 0;
    const tbody = document.getElementById('detail-body-'+pfx);
    if(!tbody) return;
    tbody.querySelectorAll('tr').forEach(tr=>{
        const j = parseFloat(tr.querySelector('.row-jumlah')?.value)||0;
        const h = parseFloat(tr.querySelector('.row-harga')?.value)||0;
        total  += j * h;
    });
    const td = document.getElementById('total-display-'+pfx);
    if(td) td.textContent = 'Rp '+fmtN(total);
    const tb = document.getElementById(pfx+'_total_biaya');
    if(tb){ tb.value = total; hitungBayar(pfx); }
}

function hitungBayar(pfx){
    const b  = parseFloat(document.getElementById(pfx+'_total_biaya')?.value)||0;
    const d  = parseFloat(document.getElementById(pfx+'_diskon')?.value)||0;
    const el = document.getElementById(pfx+'_total_bayar');
    if(el){ el.value = Math.max(0, b-d); hitungKembalian(pfx); }
}

function hitungKembalian(pfx){
    const b  = parseFloat(document.getElementById(pfx+'_total_bayar')?.value)||0;
    const t  = parseFloat(document.getElementById(pfx+'_terbayar')?.value)||0;
    const el = document.getElementById(pfx+'_kembalian');
    if(el) el.value = Math.max(0, t-b);
}

function fmtN(n){ return Math.round(n).toLocaleString('id-ID'); }

function escHtml(s){
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function loadingHtml(){
    return '<div style="text-align:center;padding:40px;color:var(--bd-muted)"><i class="fas fa-spinner fa-spin fa-2x"></i><br><br>Memuat data…</div>';
}

// ── Auto-open modal jika validasi gagal saat store ────────────────
@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modal-create'));
@endif
</script>
@endpush