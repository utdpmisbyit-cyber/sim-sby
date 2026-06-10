@extends('layouts.index')

@push('styles')
<style>
  :root {
    --bd-red:#c0392b;--bd-red-light:#f9ebea;
    --bd-blue:#1a5276;--bd-blue-light:#d6eaf8;
    --bd-teal:#148f77;--bd-teal-light:#d1f2eb;
    --bd-amber:#d68910;--bd-amber-light:#fef9e7;
    --bd-gray:#f5f6fa;--bd-border:#dce3ed;
    --bd-text:#1c2833;--bd-muted:#7f8c8d;--bd-white:#ffffff;
    --bd-shadow:0 3px 14px rgba(0,0,0,.09);
    --bd-radius:10px;--bd-radius-sm:6px;
  }
  .show-header {
    background:linear-gradient(135deg,var(--bd-blue) 0%,#154360 100%);
    padding:18px 26px;display:flex;align-items:center;justify-content:space-between;
    border-radius:0 0 14px 14px;box-shadow:var(--bd-shadow);margin-bottom:22px;
  }
  .show-header h1{color:#fff;font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:10px;}
  .show-header p{color:rgba(255,255,255,.65);font-size:.8rem;margin:2px 0 0;}
  .show-actions{display:flex;gap:8px;}

  .sd-card{background:var(--bd-white);border-radius:var(--bd-radius);box-shadow:0 1px 6px rgba(0,0,0,.07);margin-bottom:16px;overflow:hidden;}
  .sd-card-head{background:#f0f4fa;border-bottom:1px solid var(--bd-border);padding:10px 18px;font-size:.87rem;font-weight:700;color:var(--bd-blue);display:flex;align-items:center;gap:8px;}
  .sd-card-body{padding:16px 18px;}

  .sd-grid{display:grid;gap:10px 20px;}
  .sd-grid-2{grid-template-columns:repeat(2,1fr);}
  .sd-grid-3{grid-template-columns:repeat(3,1fr);}
  .sd-grid-4{grid-template-columns:repeat(4,1fr);}
  @media(max-width:768px){.sd-grid-2,.sd-grid-3,.sd-grid-4{grid-template-columns:1fr 1fr;}}

  .sd-field{}
  .sd-label{font-size:.7rem;font-weight:700;color:var(--bd-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;}
  .sd-value{font-size:.88rem;color:var(--bd-text);font-weight:500;}
  .sd-value.mono{font-family:'Courier New',monospace;font-weight:700;color:var(--bd-red);}
  .sd-value.gol{display:inline-block;padding:2px 10px;border-radius:4px;font-weight:700;background:var(--bd-red-light);color:var(--bd-red);border:1px solid #f1948a;}
  .sd-value.pay{display:inline-block;padding:2px 8px;border-radius:4px;font-size:.8rem;font-weight:600;background:var(--bd-blue-light);color:var(--bd-blue);}
  .sd-value.empty{color:var(--bd-muted);font-style:italic;}

  .bd-badge{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:700;text-transform:uppercase;}
  .badge-baru{background:var(--bd-amber-light);color:var(--bd-amber);}
  .badge-selesai{background:var(--bd-teal-light);color:var(--bd-teal);}
  .badge-batal{background:#fdecea;color:var(--bd-red);}
  .badge-dot{width:6px;height:6px;border-radius:50%;background:currentColor;}

  .bd-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--bd-radius-sm);font-size:.84rem;font-weight:600;border:none;cursor:pointer;transition:filter .15s;text-decoration:none;white-space:nowrap;}
  .bd-btn:hover{filter:brightness(1.08);}
  .bd-btn-primary{background:var(--bd-red);color:#fff;}
  .bd-btn-secondary{background:var(--bd-blue);color:#fff;}
  .bd-btn-outline{background:transparent;border:1.5px solid var(--bd-border);color:var(--bd-text);}
  .bd-btn-danger{background:#fdecea;color:var(--bd-red);border:1.5px solid #f1948a;}

  /* Detail table */
  .dt-wrap{overflow-x:auto;}
  .dt-table{width:100%;border-collapse:collapse;font-size:.83rem;}
  .dt-table thead th{background:var(--bd-blue);color:#fff;padding:9px 12px;font-size:.77rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;}
  .dt-table tbody tr{border-bottom:1px solid var(--bd-border);}
  .dt-table tbody tr:last-child{border-bottom:none;}
  .dt-table tbody tr:hover{background:#f5faff;}
  .dt-table tbody td{padding:9px 12px;vertical-align:middle;}
  .dt-no{font-family:'Courier New',monospace;font-size:.82rem;font-weight:700;color:var(--bd-blue);}
  .hasil-cocok{display:inline-block;padding:2px 8px;border-radius:4px;background:var(--bd-teal-light);color:var(--bd-teal);font-weight:700;font-size:.78rem;}
  .hasil-tidak{display:inline-block;padding:2px 8px;border-radius:4px;background:#fdecea;color:var(--bd-red);font-weight:700;font-size:.78rem;}
  .hasil-lain{display:inline-block;padding:2px 8px;border-radius:4px;background:var(--bd-amber-light);color:var(--bd-amber);font-weight:700;font-size:.78rem;}
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="show-header">
  <div>
    <h1>🩸 Detail Pemberian Darah</h1>
    <p>{{ $pemberian->no_pemberian }} &mdash; {{ \Carbon\Carbon::parse($pemberian->tgl_keluar)->format('d/m/Y') }}</p>
  </div>
  <div class="show-actions">
    <a href="{{ route('unit.bank_darah.pemberian_darah.index') }}" class="bd-btn bd-btn-outline" style="color:#fff;border-color:rgba(255,255,255,.35)">← Kembali</a>
    <a href="{{ route('unit.bank_darah.pemberian_darah.edit', $pemberian) }}" class="bd-btn bd-btn-primary">✏️ Edit</a>
    <form method="POST" action="{{ route('unit.bank_darah.pemberian_darah.destroy', $pemberian) }}"
          onsubmit="return confirm('Hapus pemberian {{ $pemberian->no_pemberian }}?')" style="margin:0">
      @csrf @method('DELETE')
      <button type="submit" class="bd-btn bd-btn-danger">🗑 Hapus</button>
    </form>
  </div>
</div>

<div class="px-3 px-md-4">

  {{-- ── Ringkasan Header ──────────────────────────────────────────────────── --}}
  <div class="sd-card">
    <div class="sd-card-head">📋 Informasi Pemberian</div>
    <div class="sd-card-body">
      <div class="sd-grid sd-grid-4">
        <div class="sd-field">
          <div class="sd-label">No Pemberian</div>
          <div class="sd-value mono">{{ $pemberian->no_pemberian }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">No FPUP</div>
          <div class="sd-value mono" style="color:var(--bd-blue)">{{ $pemberian->no_fpup ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Tanggal Keluar</div>
          <div class="sd-value">{{ \Carbon\Carbon::parse($pemberian->tgl_keluar)->format('d/m/Y') }}
            @if($pemberian->jam_keluar)
              <span style="color:var(--bd-muted);font-size:.8rem"> 🕐 {{ substr($pemberian->jam_keluar,0,5) }}</span>
            @endif
          </div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Status</div>
          <div>
            <span class="bd-badge badge-{{ $pemberian->status }}">
              <span class="badge-dot"></span>{{ ucfirst($pemberian->status) }}
            </span>
            @if($pemberian->export_dropping)
              <span style="font-size:.72rem;color:var(--bd-teal);margin-left:6px">📤 Dropped</span>
            @endif
          </div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Petugas</div>
          <div class="sd-value">{{ $pemberian->petugas ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Kurir RS</div>
          <div class="sd-value">{{ $pemberian->kurir_rs ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Pasien Referal</div>
          <div>
            @if($pemberian->pasien_referal)
              <span style="background:#e8f4fd;color:#1a5276;padding:2px 8px;border-radius:4px;font-size:.78rem;font-weight:700">YA</span>
            @else
              <span class="sd-value empty">Tidak</span>
            @endif
          </div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Cara Pembayaran</div>
          <div>
            @if($pemberian->cara_pembayaran)
              <span class="sd-value pay">{{ $pemberian->cara_pembayaran }}</span>
              @if($pemberian->jns_biaya)
                <div style="font-size:.76rem;color:var(--bd-muted)">{{ $pemberian->jns_biaya }}</div>
              @endif
            @else
              <span class="sd-value empty">—</span>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Data Pasien ───────────────────────────────────────────────────────── --}}
  <div class="sd-card">
    <div class="sd-card-head">🏥 Data Pasien &amp; Rumah Sakit</div>
    <div class="sd-card-body">
      <div class="sd-grid sd-grid-3">
        <div class="sd-field">
          <div class="sd-label">Nama Pasien</div>
          <div class="sd-value" style="font-weight:700">{{ $pemberian->nama_pasien ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Nama Dokter</div>
          <div class="sd-value">{{ $pemberian->nama_dokter ? 'dr. '.$pemberian->nama_dokter : '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Gol / Rh Pasien</div>
          <div>
            @if($pemberian->gol_rh_pasien)
              <span class="sd-value gol">{{ $pemberian->gol_rh_pasien }}</span>
            @else
              <span class="sd-value empty">—</span>
            @endif
          </div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Nama RS</div>
          <div class="sd-value">{{ $pemberian->nama_rs ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Jenis RS</div>
          <div class="sd-value">{{ $pemberian->jenis_rs ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Kelas Rawat</div>
          <div class="sd-value">{{ $pemberian->kelas_rawat ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Nama Penerima</div>
          <div class="sd-value">{{ $pemberian->nama_penerima ?? '—' }}</div>
        </div>
        <div class="sd-field" style="grid-column:span 2">
          <div class="sd-label">Alamat Penerima</div>
          <div class="sd-value">{{ $pemberian->alamat_penerima ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Registrasi Online ─────────────────────────────────────────────────── --}}
  @if($pemberian->no_reg_online || $pemberian->tgl_registrasi_online)
  <div class="sd-card">
    <div class="sd-card-head">🌐 Registrasi Online</div>
    <div class="sd-card-body">
      <div class="sd-grid sd-grid-3">
        <div class="sd-field">
          <div class="sd-label">No Registrasi Online</div>
          <div class="sd-value mono" style="color:var(--bd-blue)">{{ $pemberian->no_reg_online ?? '—' }}</div>
        </div>
        <div class="sd-field">
          <div class="sd-label">Tgl Registrasi Online</div>
          <div class="sd-value">
            {{ $pemberian->tgl_registrasi_online
                ? \Carbon\Carbon::parse($pemberian->tgl_registrasi_online)->format('d/m/Y')
                : '—' }}
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  {{-- ── Detail Darah ──────────────────────────────────────────────────────── --}}
  <div class="sd-card">
    <div class="sd-card-head" style="justify-content:space-between">
      <span>🩸 Darah yang Diberikan ({{ $pemberian->detail->count() }} kantong)</span>
    </div>
    <div class="dt-wrap">
      <table class="dt-table">
        <thead>
          <tr>
            <th>#</th>
            <th>No Stok</th>
            <th>Jenis Darah</th>
            <th>Gol</th>
            <th>Rh</th>
            <th>Tgl Expired</th>
            <th>Metode</th>
            <th>Hasil</th>
            <th>Jml</th>
            <th>CC</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pemberian->detail as $i => $d)
          <tr>
            <td style="text-align:center;color:var(--bd-muted)">{{ $i + 1 }}</td>
            <td><span class="dt-no">{{ $d->no_stok ?? '—' }}</span></td>
            <td>{{ $d->jns_darah ?? '—' }}</td>
            <td style="font-weight:700;color:var(--bd-red)">{{ $d->gol ?? '—' }}</td>
            <td>{{ $d->rhesus ?? '—' }}</td>
            <td style="white-space:nowrap">
              {{ $d->tgl_expired ? \Carbon\Carbon::parse($d->tgl_expired)->format('d/m/Y') : '—' }}
              @if($d->tgl_expired && \Carbon\Carbon::parse($d->tgl_expired)->isPast())
                <span style="font-size:.7rem;color:var(--bd-red)"> ⚠ Kadaluarsa</span>
              @endif
            </td>
            <td>{{ $d->metode ?? '—' }}</td>
            <td>
              @if($d->hasil === 'Cocok')
                <span class="hasil-cocok">✓ Cocok</span>
              @elseif($d->hasil === 'Tidak Cocok')
                <span class="hasil-tidak">✗ Tidak Cocok</span>
              @elseif($d->hasil)
                <span class="hasil-lain">{{ $d->hasil }}</span>
              @else
                <span style="color:var(--bd-muted)">—</span>
              @endif
            </td>
            <td style="text-align:center;font-weight:600">{{ $d->jumlah }}</td>
            <td style="text-align:center">{{ $d->cc ? $d->cc.' ml' : '—' }}</td>
            <td style="color:var(--bd-muted);font-size:.82rem">{{ $d->keterangan ?? '—' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="11" style="text-align:center;padding:28px;color:var(--bd-muted)">
              Belum ada data detail darah.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ── Timestamps ────────────────────────────────────────────────────────── --}}
  <div style="font-size:.76rem;color:var(--bd-muted);text-align:right;margin-bottom:24px;padding:0 4px">
    Dibuat: {{ $pemberian->created_at->format('d/m/Y H:i') }}
    &nbsp;|&nbsp;
    Diperbarui: {{ $pemberian->updated_at->format('d/m/Y H:i') }}
  </div>

</div>
@endsection