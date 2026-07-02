@extends('layouts.index')

@push('styles')
<style>
  :root {
    --bd-red: #c0392b; --bd-red-dark: #96281b; --bd-red-light: #f9ebea;
    --bd-blue: #1a5276; --bd-blue-light: #d6eaf8;
    --bd-teal: #148f77; --bd-teal-light: #d1f2eb;
    --bd-amber: #d68910; --bd-amber-light: #fef9e7;
    --bd-gray: #f5f6fa; --bd-border: #dce3ed;
    --bd-text: #1c2833; --bd-muted: #7f8c8d;
    --bd-white: #ffffff;
    --bd-shadow: 0 3px 14px rgba(0,0,0,.09);
    --bd-radius: 10px; --bd-radius-sm: 6px;
  }
  body { background: var(--bd-gray); }

  .fbd-topbar {
    background: linear-gradient(135deg, var(--bd-blue) 0%, #154360 100%);
    padding: 16px 24px; display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 20px; border-radius: 0 0 12px 12px; box-shadow: var(--bd-shadow);
  }
  .fbd-topbar h1 { color:#fff; font-size:1.15rem; font-weight:700; margin:0; display:flex; align-items:center; gap:10px; }
  .fbd-topbar p  { color:rgba(255,255,255,.65); font-size:.8rem; margin:2px 0 0; }

  .fbd-card {
    background: var(--bd-white); border-radius: var(--bd-radius);
    box-shadow: 0 1px 6px rgba(0,0,0,.07); margin-bottom: 18px; overflow: hidden;
  }
  .fbd-card-head {
    background: #f0f4fa; border-bottom: 1px solid var(--bd-border);
    padding: 11px 20px; font-size: .88rem; font-weight: 700;
    color: var(--bd-blue); display: flex; align-items: center; gap: 8px;
  }
  .fbd-card-body { padding: 18px 20px; }

  .fbd-grid { display: grid; gap: 14px; }
  .fbd-grid-2 { grid-template-columns: repeat(2, 1fr); }
  .fbd-grid-3 { grid-template-columns: repeat(3, 1fr); }
  .fbd-grid-4 { grid-template-columns: repeat(4, 1fr); }
  @media(max-width:768px) {
    .fbd-grid-2,.fbd-grid-3,.fbd-grid-4 { grid-template-columns: 1fr; }
  }

  .fbd-group { display: flex; flex-direction: column; gap: 5px; }
  .fbd-label { font-size: .74rem; font-weight: 700; color: var(--bd-muted); text-transform: uppercase; letter-spacing: .5px; }
  .fbd-input {
    border: 1.5px solid var(--bd-border); border-radius: var(--bd-radius-sm);
    padding: 8px 12px; font-size: .87rem; color: var(--bd-text);
    background: var(--bd-white); outline: none;
    transition: border-color .2s, box-shadow .2s; width: 100%;
  }
  .fbd-input:focus { border-color: var(--bd-blue); box-shadow: 0 0 0 3px rgba(26,82,118,.1); }
  .fbd-input[readonly] { background: #f7f9fc; color: var(--bd-muted); }
  .fbd-input.filled { background: #edfbf5; border-color: var(--bd-teal); }

  .scan-row { display: flex; gap: 8px; align-items: flex-end; }
  .scan-row .fbd-input { flex: 1; }
  .scan-btn {
    padding: 8px 14px; border-radius: var(--bd-radius-sm);
    font-size: .84rem; font-weight: 700; border: none; cursor: pointer;
    transition: filter .15s; white-space: nowrap;
    display: flex; align-items: center; gap: 6px;
  }
  .scan-btn-fpup  { background: var(--bd-red);   color: #fff; }
  .scan-btn-stok  { background: var(--bd-teal);  color: #fff; }
  .scan-btn:hover { filter: brightness(1.1); }
  .scan-btn:disabled { opacity: .5; cursor: not-allowed; }

  /* ── Info panel hasil scan FPUP ──────────────────────────────────────── */
  .fpup-info-panel {
    background: var(--bd-blue-light); border: 1.5px solid #85c1e9;
    border-radius: var(--bd-radius-sm); padding: 14px 16px; margin-top: 10px;
    display: none;
  }
  .fpup-info-panel.show { display: block; }
  .fpup-info-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px;
  }
  .fpup-info-item { font-size: .82rem; }
  .fpup-info-item span  { display: block; font-size: .7rem; color: var(--bd-muted); font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
  .fpup-info-item strong { color: var(--bd-text); }
  /* Highlight fields penting */
  .fpup-info-item.highlight strong {
    display: inline-block; padding: 2px 8px; border-radius: 4px;
    background: var(--bd-red-light); color: var(--bd-red);
    font-size: .85rem; border: 1px solid #f1948a;
  }

  .detail-table-wrap { overflow-x: auto; }
  .detail-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
  .detail-table thead th {
    background: var(--bd-blue); color: #fff;
    padding: 9px 11px; font-size: .77rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
  }
  .detail-table tbody tr { border-bottom: 1px solid var(--bd-border); }
  .detail-table tbody tr:hover { background: #f5faff; }
  .detail-table tbody td { padding: 8px 10px; vertical-align: middle; }
  .detail-table .fbd-input { padding: 6px 9px; font-size: .82rem; }

  .hasil-cocok { background: var(--bd-teal-light); color: var(--bd-teal); }
  .hasil-tidak { background: #fdecea; color: var(--bd-red); }

  .fbd-btn { display: inline-flex; align-items: center; gap: 7px; padding: 10px 20px; border-radius: var(--bd-radius-sm); font-size: .88rem; font-weight: 700; border: none; cursor: pointer; transition: filter .15s; text-decoration: none; }
  .fbd-btn:hover { filter: brightness(1.08); }
  .fbd-btn-save    { background: var(--bd-red);  color: #fff; }
  .fbd-btn-back    { background: transparent; border: 1.5px solid var(--bd-border); color: var(--bd-text); }
  .fbd-btn-add-row { background: var(--bd-teal); color: #fff; font-size: .82rem; padding: 7px 14px; }
  .fbd-btn-del-row { background: #fdecea; color: var(--bd-red); border: none; border-radius: var(--bd-radius-sm); padding: 5px 9px; cursor: pointer; font-size: .82rem; }
  .fbd-btn-del-row:hover { background: #f1948a; color: #fff; }

  #bd-toast {
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    padding: 12px 20px; border-radius: var(--bd-radius-sm);
    font-size: .87rem; font-weight: 600; display: none;
    box-shadow: var(--bd-shadow); min-width: 240px;
  }
  #bd-toast.success { background: var(--bd-teal-light); color: var(--bd-teal); border: 1px solid #a9dfbf; }
  #bd-toast.error   { background: #fdecea; color: var(--bd-red); border: 1px solid #f1948a; }

  .spinner { display: none; width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; animation: spin .6s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div id="bd-toast"></div>

<div class="fbd-topbar">
  <div>
    <h1>🩸 Pemberian Darah Baru</h1>
    <p>Tambah data pengeluaran darah dari stok UTD</p>
  </div>
  <a href="{{ route('unit.bank_darah.pemberian_darah.index') }}" class="fbd-btn fbd-btn-back" style="color:#fff;border-color:rgba(255,255,255,.3)">
    ← Kembali
  </a>
</div>

<div class="px-3 px-md-4">
<form method="POST" action="{{ route('unit.bank_darah.pemberian_darah.store') }}" id="form-pemberian">
@csrf

{{-- ── 1. Nomor & Tanggal ──────────────────────────────────────────────── --}}
<div class="fbd-card">
  <div class="fbd-card-head">📋 Nomor Pemberian &amp; Waktu</div>
  <div class="fbd-card-body">
    <div class="fbd-grid fbd-grid-4">
      <div class="fbd-group">
        <label class="fbd-label">No Pemberian</label>
        <input type="text" class="fbd-input" value="{{ $noPemberian }}" readonly>
        <input type="hidden" name="kode_rs" id="fill_kode_rs">
        
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Tanggal Keluar <span style="color:red">*</span></label>
        <input type="date" name="tgl_keluar" class="fbd-input" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Jam Keluar</label>
        <input type="time" name="jam_keluar" class="fbd-input" value="{{ date('H:i') }}">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Petugas</label>
        <input type="text" name="petugas" class="fbd-input" value="{{ auth()->user()->name ?? '' }}">
      </div>
    </div>
  </div>
</div>

{{-- ── 2. Scan FPUP ─────────────────────────────────────────────────────── --}}
<div class="fbd-card">
  <div class="fbd-card-head">🔍 Scan / Cari No FPUP</div>
  <div class="fbd-card-body">

    {{-- Input scan --}}
    <div class="fbd-group">
      <label class="fbd-label">No FPUP (Scan Barcode / Ketik Manual)</label>
      <div class="scan-row">
        <input type="text" id="input-no-fpup" name="no_fpup"
               class="fbd-input" placeholder="Scan atau ketik no FPUP…"
               autocomplete="off" style="max-width:340px">
        <input type="hidden" name="permintaan_fpup_id" id="permintaan_fpup_id">
        <button type="button" class="scan-btn scan-btn-fpup" id="btn-scan-fpup"
                data-url="{{ route('unit.bank_darah.pemberian_darah.scan-fpup') }}">
          <span id="scan-fpup-icon">🔍</span>
          <span class="spinner" id="scan-fpup-spinner"></span>
          Cari FPUP
        </button>
      </div>
    </div>

    {{-- Info panel hasil scan --}}
    <div class="fpup-info-panel" id="fpup-info-panel">
      <div style="font-size:.8rem;font-weight:700;color:var(--bd-blue);margin-bottom:10px">
        ✅ Data FPUP Ditemukan
      </div>
      <div class="fpup-info-grid" id="fpup-info-grid"></div>
    </div>

    {{-- Auto-fill fields dari FPUP --}}
    <div class="fbd-grid fbd-grid-3" style="margin-top:16px">
      <div class="fbd-group">
        <label class="fbd-label">Nama Pasien</label>
        <input type="text" name="nama_pasien" id="fill_nama_pasien" class="fbd-input" placeholder="Otomatis dari FPUP">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Nama Dokter</label>
        <input type="text" name="nama_dokter" id="fill_nama_dokter" class="fbd-input" placeholder="Otomatis dari FPUP">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Gol / Rh Pasien</label>
        {{-- Field ini diisi dari data.gol_rh_pasien --}}
        <input type="text" name="gol_rh_pasien" id="fill_gol_rh_pasien" class="fbd-input"
               placeholder="Otomatis dari FPUP" style="font-weight:700;color:var(--bd-red)">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Rumah Sakit</label>
        <input type="text" name="nama_rs" id="fill_nama_rs" class="fbd-input" placeholder="Otomatis dari FPUP">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Jenis RS</label>
        <input type="text" name="jenis_rs" id="fill_jenis_rs" class="fbd-input">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Kelas Rawat</label>
        <input type="text" name="kelas_rawat" id="fill_kelas_rawat" class="fbd-input">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Cara Pembayaran</label>
        <select name="cara_pembayaran" id="fill_cara_pembayaran" class="fbd-input">
          <option value="">— Pilih —</option>
          <option value="TAGIHAN">TAGIHAN</option>
          <option value="TUNAI">TUNAI</option>
          <option value="BPJS">BPJS</option>
        </select>
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Jenis Biaya</label>
        <input type="text" name="jns_biaya" id="fill_jns_biaya" class="fbd-input" placeholder="NATBPPD, dll">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Kurir RS</label>
        <input type="text" name="kurir_rs" class="fbd-input">
      </div>
    </div>

    <div class="fbd-grid fbd-grid-3" style="margin-top:14px">
      <div class="fbd-group">
        <label class="fbd-label">No Registrasi Online</label>
        <input type="text" name="no_reg_online" id="fill_no_reg_online" class="fbd-input">
      </div>
      <div class="fbd-group">
        <label class="fbd-label">Tgl Registrasi Online</label>
        {{-- Diisi otomatis dari data.tgl_registrasi_online (format Y-m-d) --}}
        <input type="date" name="tgl_registrasi_online" id="fill_tgl_registrasi_online" class="fbd-input">
      </div>
      <div class="fbd-group" style="justify-content:flex-end;padding-top:20px">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.87rem">
          <input type="checkbox" name="pasien_referal" id="fill_pasien_referal" value="1"
                 style="width:16px;height:16px;accent-color:var(--bd-red)">
          <span style="font-weight:600;color:var(--bd-text)">Pasien REFERAL</span>
        </label>
      </div>
    </div>

    <div class="fbd-group" style="margin-top:14px">
      <label class="fbd-label">Nama Penerima / Alamat</label>
      <div class="fbd-grid fbd-grid-2">
        <input type="text" name="nama_penerima" class="fbd-input" placeholder="Nama penerima darah">
        <input type="text" name="alamat_penerima" class="fbd-input" placeholder="Alamat penerima">
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
        <input type="text" id="input-no-stok" class="fbd-input"
               style="width:200px" placeholder="Scan no stok…">
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
        <tbody id="detail-tbody"></tbody>
      </table>
    </div>
  </div>
</div>

{{-- ── Actions ──────────────────────────────────────────────────────────── --}}
<div style="display:flex;gap:12px;justify-content:flex-end;margin-bottom:30px">
  <a href="{{ route('unit.bank_darah.pemberian_darah.index') }}" class="fbd-btn fbd-btn-back">↩ Batal</a>
  <button type="submit" class="fbd-btn fbd-btn-save">💾 Simpan Pemberian Darah</button>
</div>

</form>
</div>

@push('scripts')
<script>
(function () {
  'use strict';

  // URL diambil dari data-url attribute — aman terhadap perubahan prefix route
  const ROUTES = {
    scanFpup : document.getElementById('btn-scan-fpup').dataset.url,
    scanStok : document.getElementById('btn-scan-stok').dataset.url,
  };

  // ── Toast ────────────────────────────────────────────────────────────────
  function toast(msg, type = 'success') {
    const el = document.getElementById('bd-toast');
    el.textContent = (type === 'success' ? '✔ ' : '✘ ') + msg;
    el.className = type;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 3200);
  }

  // ── Isi satu input/select by id, tambahkan class filled jika ada nilai ───
  function fillField(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    if (el.tagName === 'SELECT') {
      // cari option yang cocok (case-insensitive)
      const opts = Array.from(el.options);
      const match = opts.find(o => o.value.toLowerCase() === String(value ?? '').toLowerCase());
      el.value = match ? match.value : '';
    } else {
      el.value = value ?? '';
    }
    if (value) {
      el.classList.add('filled');
    } else {
      el.classList.remove('filled');
    }
  }

  // ── Scan FPUP ─────────────────────────────────────────────────────────────
  async function doScanFpup(noFpup) {
    const btn  = document.getElementById('btn-scan-fpup');
    const icon = document.getElementById('scan-fpup-icon');
    const spin = document.getElementById('scan-fpup-spinner');
    btn.disabled = true; icon.style.display = 'none'; spin.style.display = 'inline-block';

    try {
      const res  = await fetch(ROUTES.scanFpup + '?no_fpup=' + encodeURIComponent(noFpup), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (!res.ok) {
        toast(data.message || 'No FPUP tidak ditemukan', 'error');
        return;
      }

      // ── Auto-fill semua field dari response JSON ──────────────────────────
      // hidden
      fillField('permintaan_fpup_id',        data.permintaan_fpup_id);
      fillField('fill_kode_rs', data.kode_rs);
      fillField('fill_nama_pasien',           data.nama_pasien);
      fillField('fill_nama_dokter',           data.nama_dokter);
      fillField('fill_gol_rh_pasien',         data.gol_rh_pasien);  
      fillField('fill_nama_rs',               data.nama_rs);
      fillField('fill_jenis_rs',              data.jenis_rs);
      fillField('fill_kelas_rawat',           data.kelas_rawat);
      fillField('fill_cara_pembayaran',       data.cara_pembayaran); 
      fillField('fill_jns_biaya',             data.jns_biaya);
      fillField('fill_no_reg_online', data.no_reg_online);
      fillField('fill_gol_rh_pasien', data.gol_rh_pasien);
      fillField('fill_tgl_registrasi_online', data.tgl_registrasi_online);

      // Pasien referal checkbox
      const cbReferal = document.getElementById('fill_pasien_referal');
      if (cbReferal) cbReferal.checked = !!data.pasien_referal;

      // ── Tampilkan info panel ringkasan ────────────────────────────────────
      const panel = document.getElementById('fpup-info-panel');
      const grid  = document.getElementById('fpup-info-grid');
      const items = [
        { label: 'No FPUP',       value: data.no_fpup,               highlight: false },
        { label: 'Pasien',        value: data.nama_pasien,            highlight: false },
        { label: 'Dokter',        value: data.nama_dokter,            highlight: false },
        { label: 'Gol / Rh',      value: data.gol_rh_pasien,         highlight: true  }, // merah
        { label: 'RS',            value: data.nama_rs,                highlight: false },
        { label: 'Kelas',         value: data.kelas_rawat,            highlight: false },
        { label: 'Bayar',         value: data.cara_pembayaran,        highlight: false },
        { label: 'Tgl Reg Online',value: data.tgl_registrasi_online,  highlight: true  }, // merah
        { label: 'Referal',       value: data.pasien_referal ? 'YA' : 'Tidak', highlight: false },
      ];
      grid.innerHTML = items.map(item =>
        `<div class="fpup-info-item${item.highlight ? ' highlight' : ''}">
           <span>${item.label}</span>
           <strong>${item.value ?? '—'}</strong>
         </div>`
      ).join('');
      panel.classList.add('show');

      toast('Data FPUP berhasil dimuat ✓');
    } catch (err) {
      console.error(err);
      toast('Gagal menghubungi server', 'error');
    } finally {
      btn.disabled = false; icon.style.display = ''; spin.style.display = 'none';
    }
  }

  document.getElementById('btn-scan-fpup').addEventListener('click', () => {
    const v = document.getElementById('input-no-fpup').value.trim();
    if (v) doScanFpup(v);
    else toast('Masukkan nomor FPUP terlebih dahulu', 'error');
  });
  document.getElementById('input-no-fpup').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); const v = e.target.value.trim(); if (v) doScanFpup(v); }
  });

  // ── Detail rows ───────────────────────────────────────────────────────────
  let rowIndex = 0;

  function buildRow(data = {}) {
    const i   = rowIndex++;
    const n   = name => `detail[${i}][${name}]`;
    const val = (key, def = '') => data[key] ?? def;

    const hasilOpts = ['', 'Cocok', 'Tidak Cocok', 'Minor', 'Ragu-ragu']
      .map(v => `<option value="${v}"${val('hasil') === v ? ' selected' : ''}>${v || '— Hasil —'}</option>`).join('');
    const metOpts = ['', 'GEL', 'Tabung', 'Mikro']
      .map(v => `<option value="${v}"${val('metode') === v ? ' selected' : ''}>${v || '— Metode —'}</option>`).join('');

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="text-align:center;color:var(--bd-muted);font-size:.78rem" class="row-num">—</td>
      <td>
        <input type="hidden" name="${n('stok_darah_id')}" value="${val('stok_darah_id')}">
        <input type="text"   name="${n('no_stok')}" class="fbd-input${val('no_stok') ? ' filled' : ''}"
               value="${val('no_stok')}" placeholder="—" style="width:130px">
      </td>
      <td><input type="text"   name="${n('jns_darah')}"   class="fbd-input" value="${val('jns_darah')}"  style="width:90px"></td>
      <td><input type="text"   name="${n('gol')}"          class="fbd-input" value="${val('gol')}"        style="width:50px" maxlength="5"></td>
      <td><input type="text"   name="${n('rhesus')}"       class="fbd-input" value="${val('rhesus')}"     style="width:55px" maxlength="10"></td>
      <td><input type="date"   name="${n('tgl_expired')}"  class="fbd-input" value="${val('tgl_expired')}" style="width:130px"></td>
      <td><select              name="${n('metode')}"       class="fbd-input" style="width:90px">${metOpts}</select></td>
      <td><select              name="${n('hasil')}"        class="fbd-input inp-hasil" style="width:110px">${hasilOpts}</select></td>
      <td><input type="number" name="${n('jumlah')}"       class="fbd-input" value="${val('jumlah', 1)}"  min="1" style="width:60px"></td>
      <td><input type="number" name="${n('cc')}"           class="fbd-input" value="${val('cc')}"         style="width:65px"></td>
      <td><input type="text"   name="${n('keterangan')}"   class="fbd-input" value="${val('keterangan')}" style="width:100px"></td>
      <td><button type="button" class="fbd-btn-del-row btn-del-row">🗑</button></td>
    `;
    return tr;
  }

  function reindex() {
    document.querySelectorAll('#detail-tbody tr').forEach((tr, i) => {
      tr.querySelector('.row-num').textContent = i + 1;
    });
  }

  document.getElementById('btn-add-row').addEventListener('click', () => {
    document.getElementById('detail-tbody').appendChild(buildRow());
    reindex();
  });

  document.getElementById('detail-tbody').addEventListener('click', e => {
    if (e.target.closest('.btn-del-row')) { e.target.closest('tr').remove(); reindex(); }
  });

  document.getElementById('detail-tbody').addEventListener('change', e => {
    if (e.target.classList.contains('inp-hasil')) {
      const v = e.target.value;
      e.target.className = 'fbd-input inp-hasil' +
        (v === 'Cocok' ? ' hasil-cocok' : v === 'Tidak Cocok' ? ' hasil-tidak' : '');
    }
  });

  // ── Scan Stok ─────────────────────────────────────────────────────────────
  async function doScanStok(noStok) {
    const btn  = document.getElementById('btn-scan-stok');
    const icon = document.getElementById('scan-stok-icon');
    const spin = document.getElementById('scan-stok-spinner');
    btn.disabled = true; icon.style.display = 'none'; spin.style.display = 'inline-block';

    try {
      const res  = await fetch(ROUTES.scanStok + '?no_stok=' + encodeURIComponent(noStok), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (!res.ok) {
        toast(data.message || 'Stok tidak ditemukan atau tidak tersedia', 'error');
        return;
      }

      // tgl_expired sudah dalam format Y-m-d dari service → langsung pakai
      document.getElementById('detail-tbody').appendChild(buildRow({
        stok_darah_id : data.stok_darah_id,
        no_stok       : data.no_stok,
        jns_darah     : data.jns_darah,
        gol           : data.gol,
        rhesus        : data.rhesus,
        tgl_expired   : data.tgl_expired ?? '',
        cc            : data.cc ?? '',
        jumlah        : 1,
      }));
      reindex();
      document.getElementById('input-no-stok').value = '';
      toast(`Stok ${data.no_stok} — ${data.jns_darah} ${data.gol}${data.rhesus} ditambahkan`);
    } catch (err) {
      console.error(err);
      toast('Gagal menghubungi server', 'error');
    } finally {
      btn.disabled = false; icon.style.display = ''; spin.style.display = 'none';
    }
  }

  document.getElementById('btn-scan-stok').addEventListener('click', () => {
    const v = document.getElementById('input-no-stok').value.trim();
    if (v) doScanStok(v);
    else toast('Masukkan nomor stok terlebih dahulu', 'error');
  });
  document.getElementById('input-no-stok').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); const v = e.target.value.trim(); if (v) doScanStok(v); }
  });

  // Inisialisasi satu baris kosong
  document.getElementById('detail-tbody').appendChild(buildRow());
  reindex();
})();
</script>
@endpush
@endsection