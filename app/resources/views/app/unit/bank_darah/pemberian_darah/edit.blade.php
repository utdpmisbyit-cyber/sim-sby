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
  body{background:var(--bd-gray);}
  .fbd-topbar{background:linear-gradient(135deg,var(--bd-blue) 0%,#154360 100%);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;border-radius:0 0 12px 12px;box-shadow:var(--bd-shadow);}
  .fbd-topbar h1{color:#fff;font-size:1.1rem;font-weight:700;margin:0;display:flex;align-items:center;gap:10px;}
  .fbd-topbar p{color:rgba(255,255,255,.65);font-size:.8rem;margin:2px 0 0;}
  .fbd-card{background:var(--bd-white);border-radius:var(--bd-radius);box-shadow:0 1px 6px rgba(0,0,0,.07);margin-bottom:18px;overflow:hidden;}
  .fbd-card-head{background:#f0f4fa;border-bottom:1px solid var(--bd-border);padding:11px 20px;font-size:.88rem;font-weight:700;color:var(--bd-blue);display:flex;align-items:center;gap:8px;}
  .fbd-card-body{padding:18px 20px;}
  .fbd-grid{display:grid;gap:14px;}
  .fbd-grid-2{grid-template-columns:repeat(2,1fr);}
  .fbd-grid-3{grid-template-columns:repeat(3,1fr);}
  .fbd-grid-4{grid-template-columns:repeat(4,1fr);}
  @media(max-width:768px){.fbd-grid-2,.fbd-grid-3,.fbd-grid-4{grid-template-columns:1fr;}}
  .fbd-group{display:flex;flex-direction:column;gap:5px;}
  .fbd-label{font-size:.74rem;font-weight:700;color:var(--bd-muted);text-transform:uppercase;letter-spacing:.5px;}
  .fbd-input{border:1.5px solid var(--bd-border);border-radius:var(--bd-radius-sm);padding:8px 12px;font-size:.87rem;color:var(--bd-text);background:var(--bd-white);outline:none;transition:border-color .2s,box-shadow .2s;width:100%;}
  .fbd-input:focus{border-color:var(--bd-blue);box-shadow:0 0 0 3px rgba(26,82,118,.1);}
  .fbd-input[readonly]{background:#f7f9fc;color:var(--bd-muted);}
  .fbd-input.filled{background:#edfbf5;border-color:var(--bd-teal);}
  .fbd-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:var(--bd-radius-sm);font-size:.88rem;font-weight:700;border:none;cursor:pointer;transition:filter .15s;text-decoration:none;}
  .fbd-btn:hover{filter:brightness(1.08);}
  .fbd-btn-save{background:var(--bd-red);color:#fff;}
  .fbd-btn-back{background:transparent;border:1.5px solid var(--bd-border);color:var(--bd-text);}
  .fbd-btn-add-row{background:var(--bd-teal);color:#fff;font-size:.82rem;padding:7px 14px;}
  .fbd-btn-del-row{background:#fdecea;color:var(--bd-red);border:none;border-radius:var(--bd-radius-sm);padding:5px 9px;cursor:pointer;font-size:.82rem;}
  .fbd-btn-del-row:hover{background:#f1948a;color:#fff;}
  .detail-table-wrap{overflow-x:auto;}
  .detail-table{width:100%;border-collapse:collapse;font-size:.83rem;}
  .detail-table thead th{background:var(--bd-blue);color:#fff;padding:9px 11px;font-size:.77rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;white-space:nowrap;}
  .detail-table tbody tr{border-bottom:1px solid var(--bd-border);}
  .detail-table tbody tr:hover{background:#f5faff;}
  .detail-table tbody td{padding:8px 10px;vertical-align:middle;}
  .detail-table .fbd-input{padding:6px 9px;font-size:.82rem;}
  .hasil-cocok{background:var(--bd-teal-light);color:var(--bd-teal);}
  .hasil-tidak{background:#fdecea;color:var(--bd-red);}
  .scan-row{display:flex;gap:8px;align-items:flex-end;}
  .scan-row .fbd-input{flex:1;}
  .scan-btn{padding:8px 14px;border-radius:var(--bd-radius-sm);font-size:.84rem;font-weight:700;border:none;cursor:pointer;transition:filter .15s;white-space:nowrap;display:flex;align-items:center;gap:6px;}
  .scan-btn-stok{background:var(--bd-teal);color:#fff;}
  .scan-btn:hover{filter:brightness(1.1);}
  .scan-btn:disabled{opacity:.5;cursor:not-allowed;}
  #bd-toast{position:fixed;bottom:28px;right:28px;z-index:9999;padding:12px 20px;border-radius:var(--bd-radius-sm);font-size:.87rem;font-weight:600;display:none;box-shadow:var(--bd-shadow);min-width:240px;}
  #bd-toast.success{background:var(--bd-teal-light);color:var(--bd-teal);border:1px solid #a9dfbf;}
  #bd-toast.error{background:#fdecea;color:var(--bd-red);border:1px solid #f1948a;}
  .spinner{display:none;width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;}
  @keyframes spin{to{transform:rotate(360deg);}}

  .is-invalid{border-color:#e74c3c !important;}
  .invalid-feedback{font-size:.75rem;color:#e74c3c;margin-top:3px;}
</style>
@endpush

@section('content')
<div id="bd-toast"></div>

<div class="fbd-topbar">
  <div>
    <h1>✏️ Edit Pemberian Darah</h1>
    <p>{{ $pemberian->no_pemberian }}</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('unit.bank_darah.pemberian_darah.show', $pemberian) }}"
       class="fbd-btn fbd-btn-back" style="color:#fff;border-color:rgba(255,255,255,.3)">← Kembali</a>
  </div>
</div>

<div class="px-3 px-md-4">

@if($errors->any())
  <div style="background:#fdecea;border:1px solid #f1948a;border-radius:6px;padding:12px 16px;margin-bottom:16px;font-size:.87rem;color:var(--bd-red)">
    <strong>⚠ Perhatikan kesalahan berikut:</strong>
    <ul style="margin:6px 0 0;padding-left:18px">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('unit.bank_darah.pemberian_darah.update', $pemberian) }}" id="form-edit">
@csrf @method('PUT')

{{-- ── 1. Nomor & Tanggal ──────────────────────────────────────────────── --}}
<div class="fbd-card">
  <div class="fbd-card-head">📋 Nomor Pemberian &amp; Waktu</div>
  <div class="fbd-card-body">
    <div class="fbd-grid fbd-grid-4">
      <div class="fbd-group">
        <label class="fbd-label">No Pemberian</label>
        <input type="text" class="fbd-input" value="{{ $pemberian->no_pemberian }}" readonly>
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Tanggal Keluar <span style="color:red">*</span></label>
        <input type="date" name="tgl_keluar" class="fbd-input {{ $errors->has('tgl_keluar') ? 'is-invalid' : '' }}"
               value="{{ old('tgl_keluar', \Carbon\Carbon::parse($pemberian->tgl_keluar)->format('Y-m-d')) }}" required>
        @error('tgl_keluar')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Jam Keluar</label>
        <input type="time" name="jam_keluar" class="fbd-input"
               value="{{ old('jam_keluar', $pemberian->jam_keluar ? substr($pemberian->jam_keluar,0,5) : '') }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Petugas</label>
        <input type="text" name="petugas" class="fbd-input" value="{{ old('petugas', $pemberian->petugas) }}">
      </div>
    </div>
  </div>
</div>

{{-- ── 2. Data Pasien & RS ──────────────────────────────────────────────── --}}
<div class="fbd-card">
  <div class="fbd-card-head">🏥 Data Pasien &amp; Rumah Sakit</div>
  <div class="fbd-card-body">
    <div style="margin-bottom:12px">
      <label class="fbd-label" style="display:block;margin-bottom:4px">No FPUP</label>
      <input type="text" name="no_fpup" class="fbd-input" style="max-width:300px"
             value="{{ old('no_fpup', $pemberian->no_fpup) }}" placeholder="No FPUP">
      <input type="hidden" name="permintaan_fpup_id" value="{{ old('permintaan_fpup_id', $pemberian->permintaan_fpup_id) }}">
    </div>

    <div class="fbd-grid fbd-grid-3">
      <div class="fbd-group">
        <label class="fbd-label">Nama Pasien</label>
        <input type="text" name="nama_pasien" class="fbd-input" value="{{ old('nama_pasien', $pemberian->nama_pasien) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Nama Dokter</label>
        <input type="text" name="nama_dokter" class="fbd-input" value="{{ old('nama_dokter', $pemberian->nama_dokter) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Gol / Rh Pasien</label>
        <input type="text" name="gol_rh_pasien" class="fbd-input" style="font-weight:700;color:var(--bd-red)"
               value="{{ old('gol_rh_pasien', $pemberian->gol_rh_pasien) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Nama RS</label>
        <input type="text" name="nama_rs" class="fbd-input" value="{{ old('nama_rs', $pemberian->nama_rs) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Jenis RS</label>
        <input type="text" name="jenis_rs" class="fbd-input" value="{{ old('jenis_rs', $pemberian->jenis_rs) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Kelas Rawat</label>
        <input type="text" name="kelas_rawat" class="fbd-input" value="{{ old('kelas_rawat', $pemberian->kelas_rawat) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Cara Pembayaran</label>
        <select name="cara_pembayaran" class="fbd-input">
          <option value="">— Pilih —</option>
          @foreach(['TAGIHAN','TUNAI','BPJS','JKN','JAMKESDA','GRATIS'] as $opt)
            <option value="{{ $opt }}" {{ old('cara_pembayaran', $pemberian->cara_pembayaran) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Jenis Biaya</label>
        <input type="text" name="jns_biaya" class="fbd-input" value="{{ old('jns_biaya', $pemberian->jns_biaya) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Kurir RS</label>
        <input type="text" name="kurir_rs" class="fbd-input" value="{{ old('kurir_rs', $pemberian->kurir_rs) }}">
      </div>
    </div>

    <div class="fbd-grid fbd-grid-3" style="margin-top:14px">
      <div class="fbd-group">
        <label class="fbd-label">No Registrasi Online</label>
        <input type="text" name="no_reg_online" class="fbd-input" value="{{ old('no_reg_online', $pemberian->no_reg_online) }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Tgl Registrasi Online</label>
        <input type="date" name="tgl_registrasi_online" class="fbd-input"
               value="{{ old('tgl_registrasi_online', $pemberian->tgl_registrasi_online ? \Carbon\Carbon::parse($pemberian->tgl_registrasi_online)->format('Y-m-d') : '') }}">
      </div>
      <div class="fbd-group" style="justify-content:flex-end;padding-top:20px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.87rem">
          <input type="checkbox" name="pasien_referal" value="1"
                 {{ old('pasien_referal', $pemberian->pasien_referal) ? 'checked' : '' }}
                 style="width:16px;height:16px;accent-color:var(--bd-red)">
          <span style="font-weight:600;color:var(--bd-text)">Pasien REFERAL</span>
        </label>
      </div>
    </div>

    <div class="fbd-group" style="margin-top:14px">
      <label class="fbd-label">Nama Penerima / Alamat</label>
      <div class="fbd-grid fbd-grid-2">
        <input type="text" name="nama_penerima" class="fbd-input" placeholder="Nama penerima"
               value="{{ old('nama_penerima', $pemberian->nama_penerima) }}">
        <input type="text" name="alamat_penerima" class="fbd-input" placeholder="Alamat penerima"
               value="{{ old('alamat_penerima', $pemberian->alamat_penerima) }}">
      </div>
    </div>
  </div>
</div>

{{-- ── 3. Detail Darah ──────────────────────────────────────────────────── --}}
<div class="fbd-card">
  <div class="fbd-card-head" style="justify-content:space-between">
    <span>🩸 Jenis Darah yang Diberikan</span>
    <div style="display:flex;gap:8px;align-items:center">
      <div class="scan-row" style="margin-bottom:0">
        <input type="text" id="input-no-stok" class="fbd-input" style="width:200px" placeholder="Scan no stok…">
        <button type="button" class="scan-btn scan-btn-stok" id="btn-scan-stok"
                data-url="{{ route('unit.bank_darah.pemberian_darah.scan-stok') }}">
          <span id="scan-stok-icon">📦</span>
          <span class="spinner" id="scan-stok-spinner"></span>
          Scan Stok
        </button>
      </div>
      <button type="button" class="fbd-btn fbd-btn-add-row" id="btn-add-row">＋ Tambah Baris</button>
    </div>
  </div>
  <div class="fbd-card-body" style="padding:0">
    <div class="detail-table-wrap">
      <table class="detail-table">
        <thead>
          <tr>
            <th>#</th><th>No Stok</th><th>Jenis Darah</th>
            <th>Gol</th><th>Rh</th><th>Tgl Expired</th>
            <th>Metode</th><th>Hasil</th>
            <th>Jml</th><th>CC</th><th>Ket</th><th>Hapus</th>
          </tr>
        </thead>
        <tbody id="detail-tbody">
          {{-- Diisi dari data existing via JS --}}
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ── Actions ──────────────────────────────────────────────────────────── --}}
<div style="display:flex;gap:12px;justify-content:flex-end;margin-bottom:30px">
  <a href="{{ route('unit.bank_darah.pemberian_darah.show', $pemberian) }}" class="fbd-btn fbd-btn-back">↩ Batal</a>
  <button type="submit" class="fbd-btn fbd-btn-save">💾 Simpan Perubahan</button>
</div>

</form>
</div>

@push('scripts')
<script>
(function () {
  'use strict';

  const SCAN_STOK_URL = document.getElementById('btn-scan-stok').dataset.url;

  // Data existing dikirim dari controller sebagai $detailRows (sudah di-map & format)
  const existingRows = @json($detailRows);

  function toast(msg, type = 'success') {
    const el = document.getElementById('bd-toast');
    el.textContent = (type === 'success' ? '✔ ' : '✘ ') + msg;
    el.className = type; el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3200);
  }

  let rowIndex = 0;

  function buildRow(data = {}) {
    const i   = rowIndex++;
    const n   = name => `detail[${i}][${name}]`;
    const val = (key, def = '') => data[key] ?? def;

    const hasilOpts = ['', 'Cocok', 'Tidak Cocok', 'Minor', 'Ragu-ragu']
      .map(v => `<option value="${v}"${val('hasil') === v ? ' selected' : ''}>${v || '— Hasil —'}</option>`).join('');
    const metOpts = ['', 'GEL', 'Tabung', 'Mikro']
      .map(v => `<option value="${v}"${val('metode') === v ? ' selected' : ''}>${v || '— Metode —'}</option>`).join('');
    const hasilClass = val('hasil') === 'Cocok' ? ' hasil-cocok' : val('hasil') === 'Tidak Cocok' ? ' hasil-tidak' : '';

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="text-align:center;color:var(--bd-muted);font-size:.78rem" class="row-num">—</td>
      <td>
        <input type="hidden" name="${n('stok_darah_id')}" value="${val('stok_darah_id')}">
        <input type="text" name="${n('no_stok')}" class="fbd-input${val('no_stok') ? ' filled' : ''}"
               value="${val('no_stok')}" placeholder="—" style="width:130px">
      </td>
      <td><input type="text"   name="${n('jns_darah')}"  class="fbd-input" value="${val('jns_darah')}"  style="width:90px"></td>
      <td><input type="text"   name="${n('gol')}"         class="fbd-input" value="${val('gol')}"        style="width:50px" maxlength="5"></td>
      <td><input type="text"   name="${n('rhesus')}"      class="fbd-input" value="${val('rhesus')}"     style="width:55px" maxlength="10"></td>
      <td><input type="date"   name="${n('tgl_expired')}" class="fbd-input" value="${val('tgl_expired')}" style="width:130px"></td>
      <td><select              name="${n('metode')}"      class="fbd-input" style="width:90px">${metOpts}</select></td>
      <td><select              name="${n('hasil')}"       class="fbd-input inp-hasil${hasilClass}" style="width:110px">${hasilOpts}</select></td>
      <td><input type="number" name="${n('jumlah')}"      class="fbd-input" value="${val('jumlah', 1)}"  min="1" style="width:60px"></td>
      <td><input type="number" name="${n('cc')}"          class="fbd-input" value="${val('cc')}"         style="width:65px"></td>
      <td><input type="text"   name="${n('keterangan')}"  class="fbd-input" value="${val('keterangan')}" style="width:100px"></td>
      <td><button type="button" class="fbd-btn-del-row btn-del-row">🗑</button></td>
    `;
    return tr;
  }

  function reindex() {
    document.querySelectorAll('#detail-tbody tr').forEach((tr, i) => {
      tr.querySelector('.row-num').textContent = i + 1;
    });
  }

  // Load existing rows
  const tbody = document.getElementById('detail-tbody');
  existingRows.forEach(row => tbody.appendChild(buildRow(row)));
  if (existingRows.length === 0) tbody.appendChild(buildRow());
  reindex();

  document.getElementById('btn-add-row').addEventListener('click', () => {
    tbody.appendChild(buildRow()); reindex();
  });

  tbody.addEventListener('click', e => {
    if (e.target.closest('.btn-del-row')) { e.target.closest('tr').remove(); reindex(); }
  });

  tbody.addEventListener('change', e => {
    if (e.target.classList.contains('inp-hasil')) {
      const v = e.target.value;
      e.target.className = 'fbd-input inp-hasil' +
        (v === 'Cocok' ? ' hasil-cocok' : v === 'Tidak Cocok' ? ' hasil-tidak' : '');
    }
  });

  // Scan stok
  async function doScanStok(noStok) {
    const btn  = document.getElementById('btn-scan-stok');
    const icon = document.getElementById('scan-stok-icon');
    const spin = document.getElementById('scan-stok-spinner');
    btn.disabled = true; icon.style.display = 'none'; spin.style.display = 'inline-block';
    try {
      const res  = await fetch(SCAN_STOK_URL + '?no_stok=' + encodeURIComponent(noStok), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (!res.ok) { toast(data.message || 'Stok tidak ditemukan', 'error'); return; }
      tbody.appendChild(buildRow({
        stok_darah_id: data.stok_darah_id,
        no_stok: data.no_stok,
        jns_darah: data.jns_darah,
        gol: data.gol,
        rhesus: data.rhesus,
        tgl_expired: data.tgl_expired ?? '',
        cc: data.cc ?? '',
        jumlah: 1,
      }));
      reindex();
      document.getElementById('input-no-stok').value = '';
      toast(`Stok ${data.no_stok} ditambahkan`);
    } catch (err) {
      toast('Gagal menghubungi server', 'error');
    } finally {
      btn.disabled = false; icon.style.display = ''; spin.style.display = 'none';
    }
  }

  document.getElementById('btn-scan-stok').addEventListener('click', () => {
    const v = document.getElementById('input-no-stok').value.trim();
    if (v) doScanStok(v); else toast('Masukkan nomor stok', 'error');
  });
  document.getElementById('input-no-stok').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); const v = e.target.value.trim(); if (v) doScanStok(v); }
  });
})();
</script>
@endpush
@endsection