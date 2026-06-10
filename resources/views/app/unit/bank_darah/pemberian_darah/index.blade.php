@extends('layouts.index')

@push('styles')
<style>
  :root {
    --bd-red:#c0392b; --bd-red-light:#f9ebea;
    --bd-blue:#1a5276; --bd-blue-light:#d6eaf8;
    --bd-teal:#148f77; --bd-teal-light:#d1f2eb;
    --bd-amber:#d68910; --bd-amber-light:#fef9e7;
    --bd-gray:#f5f6fa; --bd-border:#dce3ed;
    --bd-text:#1c2833; --bd-muted:#7f8c8d; --bd-white:#ffffff;
    --bd-shadow-sm:0 1px 4px rgba(0,0,0,.06);
    --bd-shadow:0 3px 14px rgba(0,0,0,.09);
    --bd-radius:10px; --bd-radius-sm:6px;
  }
  .bd-header {
    background: linear-gradient(135deg,var(--bd-blue) 0%,#154360 100%);
    padding:20px 28px; display:flex; align-items:center; justify-content:space-between;
    border-radius:0 0 14px 14px; box-shadow:var(--bd-shadow); margin-bottom:22px;
  }
  .bd-header-title { display:flex; align-items:center; gap:14px; }
  .bd-header-icon {
    width:44px; height:44px; border-radius:50%;
    background:rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:center; font-size:20px;
  }
  .bd-header h1 { color:#fff; font-size:1.25rem; font-weight:700; margin:0; }
  .bd-header p  { color:rgba(255,255,255,.7); font-size:.8rem; margin:2px 0 0; }

  .bd-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px; margin-bottom:22px; }
  .bd-stat-card {
    background:var(--bd-white); border-radius:var(--bd-radius); padding:16px 18px;
    border-left:4px solid var(--bd-red); box-shadow:var(--bd-shadow-sm);
    display:flex; align-items:center; gap:14px; transition:box-shadow .2s;
  }
  .bd-stat-card:hover { box-shadow:var(--bd-shadow); }
  .bd-stat-card.teal  { border-color:var(--bd-teal); }
  .bd-stat-card.amber { border-color:var(--bd-amber); }
  .bd-stat-card.blue  { border-color:var(--bd-blue); }
  .bd-stat-icon { font-size:1.6rem; min-width:38px; text-align:center; }
  .bd-stat-label { font-size:.72rem; color:var(--bd-muted); text-transform:uppercase; letter-spacing:.5px; }
  .bd-stat-value { font-size:1.5rem; font-weight:700; color:var(--bd-text); line-height:1.1; }

  .bd-card { background:var(--bd-white); border-radius:var(--bd-radius); box-shadow:var(--bd-shadow-sm); margin-bottom:18px; overflow:hidden; }
  .bd-card-header {
    padding:14px 20px; border-bottom:1px solid var(--bd-border);
    display:flex; align-items:center; justify-content:space-between; background:#fafbfd;
  }
  .bd-card-header h5 { margin:0; font-size:.93rem; font-weight:600; color:var(--bd-text); display:flex; align-items:center; gap:8px; }

  .bd-filters {
    display:flex; flex-wrap:wrap; gap:10px; align-items:flex-end;
    padding:16px 20px; border-bottom:1px solid var(--bd-border); background:#fafbfd;
  }
  .bd-filters .form-group { display:flex; flex-direction:column; gap:4px; }
  .bd-filters label { font-size:.75rem; color:var(--bd-muted); font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
  .bd-input {
    border:1.5px solid var(--bd-border); border-radius:var(--bd-radius-sm);
    padding:7px 12px; font-size:.84rem; color:var(--bd-text); background:var(--bd-white); outline:none;
    transition:border-color .2s,box-shadow .2s;
  }
  .bd-input:focus { border-color:var(--bd-blue); box-shadow:0 0 0 3px rgba(26,82,118,.12); }
  .bd-input-search { min-width:220px; }

  .bd-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:8px 16px; border-radius:var(--bd-radius-sm);
    font-size:.84rem; font-weight:600; border:none; cursor:pointer;
    transition:filter .15s,box-shadow .15s; text-decoration:none; white-space:nowrap;
  }
  .bd-btn:hover { filter:brightness(1.08); box-shadow:0 2px 8px rgba(0,0,0,.14); }
  .bd-btn-primary   { background:var(--bd-red);  color:#fff; }
  .bd-btn-secondary { background:var(--bd-blue); color:#fff; }
  .bd-btn-outline   { background:transparent; border:1.5px solid var(--bd-border); color:var(--bd-text); }

  .bd-table-wrap { overflow-x:auto; }
  .bd-table { width:100%; border-collapse:collapse; font-size:.84rem; }
  .bd-table thead th {
    background:var(--bd-blue); color:#fff;
    padding:11px 14px; font-weight:600; font-size:.79rem;
    text-transform:uppercase; letter-spacing:.5px; white-space:nowrap;
    position:sticky; top:0; z-index:2;
  }
  .bd-table tbody tr { border-bottom:1px solid var(--bd-border); transition:background .14s; }
  .bd-table tbody tr:hover { background:#f0f6ff; }
  .bd-table tbody td { padding:10px 14px; vertical-align:middle; color:var(--bd-text); }
  .bd-table tbody tr:last-child { border-bottom:none; }
  .td-no { width:44px; text-align:center; color:var(--bd-muted); font-size:.78rem; }
  .td-nopem { font-family:'Courier New',monospace; font-weight:700; color:var(--bd-red); font-size:.88rem; }

  .bd-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.73rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
  .badge-baru    { background:var(--bd-amber-light); color:var(--bd-amber); }
  .badge-selesai { background:var(--bd-teal-light);  color:var(--bd-teal); }
  .badge-batal   { background:#fdecea; color:#c0392b; }
  .badge-dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

  .bd-gol { display:inline-block; padding:2px 9px; border-radius:4px; font-weight:700; font-size:.82rem; background:var(--bd-red-light); color:var(--bd-red); border:1px solid #f1948a; }
  .bd-pay { display:inline-block; padding:2px 8px; border-radius:4px; font-size:.76rem; font-weight:600; background:var(--bd-blue-light); color:var(--bd-blue); }

  .bd-actions { display:flex; gap:5px; flex-wrap:nowrap; }
  .bd-action-btn {
    width:30px; height:30px; border-radius:var(--bd-radius-sm);
    border:1.5px solid var(--bd-border); background:var(--bd-white);
    display:flex; align-items:center; justify-content:center;
    font-size:.88rem; cursor:pointer; text-decoration:none;
    transition:background .15s,border-color .15s; color:var(--bd-text);
  }
  .bd-action-btn.view  { border-color:var(--bd-blue);  color:var(--bd-blue);  }
  .bd-action-btn.edit  { border-color:var(--bd-amber); color:var(--bd-amber); }
  .bd-action-btn.del   { border-color:var(--bd-red);   color:var(--bd-red);   }
  .bd-action-btn:hover { opacity:.75; background:var(--bd-gray); }

  .bd-pagination {
    padding:14px 20px; display:flex; align-items:center; justify-content:space-between;
    border-top:1px solid var(--bd-border); font-size:.82rem; color:var(--bd-muted);
  }
  .bd-pagination .pagination { margin:0; }
  .bd-pagination .page-link { border-radius:var(--bd-radius-sm) !important; font-size:.82rem; padding:5px 11px; color:var(--bd-blue); border-color:var(--bd-border); }
  .bd-pagination .page-item.active .page-link { background:var(--bd-blue); border-color:var(--bd-blue); color:#fff; }

  .bd-empty { padding:56px 20px; text-align:center; color:var(--bd-muted); }
  .bd-empty-icon { font-size:3rem; margin-bottom:12px; opacity:.4; }
  .bd-empty p { margin:0; font-size:.9rem; }

  .bd-alert { padding:12px 18px; border-radius:var(--bd-radius-sm); margin-bottom:16px; font-size:.87rem; display:flex; align-items:center; gap:10px; }
  .bd-alert-success { background:var(--bd-teal-light); color:var(--bd-teal); border:1px solid #a9dfbf; }
  .bd-alert-danger  { background:#fdecea; color:var(--bd-red); border:1px solid #f1948a; }

  @media(max-width:768px) {
    .bd-header { flex-direction:column; gap:12px; align-items:flex-start; }
    .bd-stats  { grid-template-columns:repeat(2,1fr); }
    .bd-filters { flex-direction:column; }
    .bd-input-search { min-width:100%; }
    .td-hide-sm { display:none; }
  }
</style>
@endpush

@section('content')
<div class="bd-header">
  <div class="bd-header-title">
    <div class="bd-header-icon">🩸</div>
    <div>
      <h1>Pemberian Darah</h1>
      <p>Bank Darah — Keluar Stok &amp; Crossmatch</p>
    </div>
  </div>
  <a href="{{ route('unit.bank_darah.pemberian_darah.create') }}" class="bd-btn bd-btn-primary">
    <span>＋</span> Pemberian Baru
  </a>
</div>

<div class="px-3 px-md-4">

  @if(session('success'))
    <div class="bd-alert bd-alert-success">✔ {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="bd-alert bd-alert-danger">✘ {{ session('error') }}</div>
  @endif

  {{-- Stat cards: gunakan $stats yang dikirim controller, bukan $list->where() --}}
  <div class="bd-stats">
    <div class="bd-stat-card">
      <div class="bd-stat-icon">📋</div>
      <div>
        <div class="bd-stat-label">Total Hari Ini</div>
        <div class="bd-stat-value">{{ $stats['hari_ini'] }}</div>
      </div>
    </div>
    <div class="bd-stat-card amber">
      <div class="bd-stat-icon">⏳</div>
      <div>
        <div class="bd-stat-label">Status Baru</div>
        <div class="bd-stat-value">{{ $stats['baru'] }}</div>
      </div>
    </div>
    <div class="bd-stat-card teal">
      <div class="bd-stat-icon">✅</div>
      <div>
        <div class="bd-stat-label">Selesai</div>
        <div class="bd-stat-value">{{ $stats['selesai'] }}</div>
      </div>
    </div>
    <div class="bd-stat-card blue">
      <div class="bd-stat-icon">🗂</div>
      <div>
        <div class="bd-stat-label">Total Record</div>
        <div class="bd-stat-value">{{ $list->total() }}</div>
      </div>
    </div>
  </div>

  <div class="bd-card">
    <div class="bd-card-header">
      <h5>🔍 Filter &amp; Cari</h5>
    </div>

    <form method="GET" action="{{ route('unit.bank_darah.pemberian_darah.index') }}">
      <div class="bd-filters">
        <div class="form-group">
          <label>Cari</label>
          <input type="text" name="search" class="bd-input bd-input-search"
            placeholder="No pemberian, FPUP, pasien, RS…" value="{{ request('search') }}">
        </div>
        <div class="form-group">
          <label>Tanggal Dari</label>
          <input type="date" name="tgl_dari" class="bd-input" value="{{ request('tgl_dari') }}">
        </div>
        <div class="form-group">
          <label>Tanggal S/D</label>
          <input type="date" name="tgl_sampai" class="bd-input" value="{{ request('tgl_sampai') }}">
        </div>
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="bd-input">
            <option value="">Semua Status</option>
            <option value="baru"    {{ request('status')==='baru'    ? 'selected':'' }}>Baru</option>
            <option value="selesai" {{ request('status')==='selesai' ? 'selected':'' }}>Selesai</option>
            <option value="batal"   {{ request('status')==='batal'   ? 'selected':'' }}>Batal</option>
          </select>
        </div>
        <button type="submit" class="bd-btn bd-btn-secondary">🔍 Cari</button>
        <a href="{{ route('unit.bank_darah.pemberian_darah.index') }}" class="bd-btn bd-btn-outline">↺ Reset</a>
      </div>
    </form>

    <div class="bd-table-wrap">
      <table class="bd-table">
        <thead>
          <tr>
            <th class="td-no">#</th>
            <th>No Pemberian</th>
            <th>No FPUP</th>
            <th>Tgl Keluar</th>
            <th>Nama Pasien</th>
            <th class="td-hide-sm">RS / Dokter</th>
            <th class="td-hide-sm">Gol/Rh</th>
            <th class="td-hide-sm">Bayar</th>
            <th>Status</th>
            <th style="width:110px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($list as $i => $row)
          <tr>
            <td class="td-no">{{ $list->firstItem() + $i }}</td>
            <td>
              <div class="td-nopem">{{ $row->no_pemberian }}</div>
              @if($row->petugas)
                <div style="font-size:.73rem;color:var(--bd-muted)">👤 {{ $row->petugas }}</div>
              @endif
            </td>
            <td>
              @if($row->no_fpup)
                <code style="font-size:.8rem;background:var(--bd-red-light);color:var(--bd-red);padding:2px 6px;border-radius:4px">{{ $row->no_fpup }}</code>
              @else
                <span style="color:var(--bd-muted)">—</span>
              @endif
            </td>
            <td style="white-space:nowrap">
              <div style="font-weight:600">{{ \Carbon\Carbon::parse($row->tgl_keluar)->format('d/m/Y') }}</div>
              @if($row->jam_keluar)
                <div style="font-size:.75rem;color:var(--bd-muted)">🕐 {{ substr($row->jam_keluar,0,5) }}</div>
              @endif
            </td>
            <td>
              <div style="font-weight:600">{{ $row->nama_pasien ?? '—' }}</div>
              @if($row->pasien_referal)
                <span style="font-size:.7rem;background:#e8f4fd;color:#1a5276;padding:1px 6px;border-radius:4px;font-weight:600">REFERAL</span>
              @endif
            </td>
            <td class="td-hide-sm">
              <div style="font-size:.82rem">{{ $row->nama_rs ?? '—' }}</div>
              @if($row->nama_dokter)
                <div style="font-size:.75rem;color:var(--bd-muted)">dr. {{ $row->nama_dokter }}</div>
              @endif
            </td>
            <td class="td-hide-sm">
              @if($row->gol_rh_pasien)
                <span class="bd-gol">{{ $row->gol_rh_pasien }}</span>
              @else
                <span style="color:var(--bd-muted)">—</span>
              @endif
            </td>
            <td class="td-hide-sm">
              @if($row->cara_pembayaran)
                <span class="bd-pay">{{ $row->cara_pembayaran }}</span>
                @if($row->jns_biaya)
                  <div style="font-size:.72rem;color:var(--bd-muted)">{{ $row->jns_biaya }}</div>
                @endif
              @else
                <span style="color:var(--bd-muted)">—</span>
              @endif
            </td>
            <td>
              <span class="bd-badge badge-{{ $row->status }}">
                <span class="badge-dot"></span>{{ ucfirst($row->status) }}
              </span>
              @if($row->export_dropping)
                <div style="font-size:.7rem;color:var(--bd-teal);margin-top:3px">📤 Dropped</div>
              @endif
            </td>
            <td>
              <div class="bd-actions">
                <a href="{{ route('unit.bank_darah.pemberian_darah.show', $row) }}"
                   class="bd-action-btn view" title="Detail">👁</a>
                <a href="{{ route('unit.bank_darah.pemberian_darah.edit', $row) }}"
                   class="bd-action-btn edit" title="Edit">✏️</a>
                <form method="POST"
                      action="{{ route('unit.bank_darah.pemberian_darah.destroy', $row) }}"
                      onsubmit="return confirm('Hapus pemberian {{ $row->no_pemberian }}?')"
                      style="display:inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="bd-action-btn del" title="Hapus">🗑</button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10">
              <div class="bd-empty">
                <div class="bd-empty-icon">🩸</div>
                <p>Belum ada data pemberian darah.</p>
                <a href="{{ route('unit.bank_darah.pemberian_darah.create') }}"
                   class="bd-btn bd-btn-primary" style="margin-top:12px;display:inline-flex">
                  ＋ Tambah Pemberian Pertama
                </a>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($list->hasPages())
    <div class="bd-pagination">
      <span>Menampilkan {{ $list->firstItem() }}–{{ $list->lastItem() }} dari {{ $list->total() }} data</span>
      {{ $list->links() }}
    </div>
    @endif
  </div>
</div>
@endsection