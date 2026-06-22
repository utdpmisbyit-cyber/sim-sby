
@extends('layouts.index')

@section('title', 'Pembayaran Dropping External')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
<style>
    :root{
        --pde-maroon:#A11D33;
        --pde-maroon-dark:#7E1527;
        --pde-ink:#1F2430;
        --pde-bg:#F7F5F3;
        --pde-line:#E7E1DC;
        --pde-success:#1F8A57;
        --pde-amber:#B5750A;
        --pde-muted:#6B7280;
    }
    .pde-wrap{ font-family:'Inter',sans-serif; color:var(--pde-ink); }
    .pde-head{ display:flex; align-items:flex-end; justify-content:space-between; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap; }
    .pde-head h1{ font-family:'Poppins',sans-serif; font-weight:700; font-size:1.5rem; margin:0; }
    .pde-head .eyebrow{ font-size:.72rem; letter-spacing:.12em; text-transform:uppercase; color:var(--pde-maroon); font-weight:600; }
    .pde-card{ background:#fff; border:1px solid var(--pde-line); border-radius:14px; box-shadow:0 1px 2px rgba(31,36,48,.04); }
    .pde-card .pde-card-head{ padding:1rem 1.25rem; border-bottom:1px dashed var(--pde-line); display:flex; align-items:center; justify-content:space-between; }
    .pde-card .pde-card-head h2{ font-family:'Poppins',sans-serif; font-size:1rem; font-weight:600; margin:0; }
    .pde-card-body{ padding:1.25rem; }
    .pde-label{ font-size:.78rem; font-weight:600; color:var(--pde-muted); margin-bottom:.3rem; display:block; }
    .pde-input, .pde-select{ border:1px solid var(--pde-line); border-radius:10px; padding:.55rem .8rem; width:100%; font-size:.92rem; }
    .pde-input:focus, .pde-select:focus{ outline:none; border-color:var(--pde-maroon); box-shadow:0 0 0 3px rgba(161,29,51,.12); }
    .pde-input[readonly]{ background:#FAFAF9; color:var(--pde-muted); }
    .pde-scan-row{ display:flex; gap:.6rem; }
    .pde-scan-row .pde-input{ font-family:'JetBrains Mono', monospace; letter-spacing:.02em; }
    .btn-pde-primary{ background:var(--pde-maroon); border-color:var(--pde-maroon); color:#fff; border-radius:10px; font-weight:600; }
    .btn-pde-primary:hover{ background:var(--pde-maroon-dark); border-color:var(--pde-maroon-dark); color:#fff; }
    .btn-pde-ghost{ background:#fff; border:1px solid var(--pde-line); border-radius:10px; font-weight:600; color:var(--pde-ink); }
    .btn-pde-ghost:hover{ background:#FBFAF8; }
    .drop-dot{ display:inline-block; width:9px; height:9px; border-radius:50% 50% 50% 0; background:var(--pde-maroon); transform:rotate(45deg); }
    table.pde-items{ width:100%; border-collapse:collapse; }
    table.pde-items thead th{
        font-size:.7rem; text-transform:uppercase; letter-spacing:.06em; color:var(--pde-muted);
        font-weight:600; border-bottom:1px solid var(--pde-line); padding:.7rem .9rem; white-space:nowrap; background:#FBFAF8;
    }
    table.pde-items tbody td{ padding:.65rem .9rem; border-bottom:1px solid var(--pde-line); font-size:.86rem; }
    .mono{ font-family:'JetBrains Mono', monospace; }
    .pde-empty-items{ padding:2.5rem 1rem; text-align:center; color:var(--pde-muted); font-size:.9rem; }
    .pde-summary-card{ position:sticky; top:1rem; }
    .pde-due{ font-family:'Poppins',sans-serif; font-weight:700; font-size:1.9rem; color:var(--pde-maroon); }
    .pde-due-label{ font-size:.78rem; color:var(--pde-muted); }
    .pde-radio-group{ display:flex; gap:.6rem; }
    .pde-radio-group label{
        flex:1; border:1px solid var(--pde-line); border-radius:10px; padding:.55rem .8rem; text-align:center;
        cursor:pointer; font-weight:600; font-size:.86rem; transition:.12s;
    }
    .pde-radio-group input{ display:none; }
    .pde-radio-group input:checked + span{ color:var(--pde-maroon); }
    .pde-radio-group label:has(input:checked){ border-color:var(--pde-maroon); background:#FBF1F3; }
    .pde-hint{ font-size:.78rem; margin-top:.5rem; }
    .pde-hint.ok{ color:var(--pde-success); }
    .pde-hint.warn{ color:var(--pde-amber); }
    .pde-footer-actions{ display:flex; gap:.6rem; justify-content:flex-end; padding:1rem 1.25rem; border-top:1px solid var(--pde-line); }
    .kbd{ font-family:'JetBrains Mono',monospace; font-size:.72rem; background:#F1EFEC; border:1px solid var(--pde-line); border-radius:6px; padding:.05rem .35rem; margin-right:.3rem; }
    .field-error{ color:#C0392B; font-size:.78rem; margin-top:.25rem; }
</style>
@endpush

@section('content')
<div class="pde-wrap container-fluid py-4">

    <div class="pde-head">
        <div>
            <div class="eyebrow">Keuangan &middot; Bank Darah Eksternal</div>
            <h1>{{ $isEdit ? 'Detail Pembayaran Dropping External' : 'Penerimaan Pembayaran Dropping External' }}</h1>
        </div>
        <a href="{{ route('finance.pembayaran_dropping_external.index') }}" class="btn btn-pde-ghost">
            <span class="kbd">Esc</span> Keluar
        </a>
    </div>

    <form id="pde-form"
          action="{{ $isEdit ? route('finance.pembayaran_dropping_external.update', $pembayaran) : route('finance.pembayaran_dropping_external.store') }}"
          method="POST">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <input type="hidden" name="pengiriman_id" id="pengiriman_id" value="{{ $pembayaran->pengiriman_id ?? '' }}">
       <input type="hidden" name="jenis_biaya" id="jenis_biaya_hidden" value="{{ $scan['jenis_biaya'] ?? $pembayaran->jenis_biaya ?? '' }}">

        <div class="row g-3">
            {{-- KOLOM KIRI: scan + item --}}
            <div class="col-lg-8">

                <div class="pde-card mb-3">
                    <div class="pde-card-head">
                        <h2><span class="drop-dot"></span> &nbsp;Nomor Kirim</h2>
                    </div>
                    <div class="pde-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="pde-label">Nomor Kirim</label>
                                <div class="pde-scan-row">
                                    <input type="text" id="nomor_kirim" class="pde-input"
                                           placeholder="Scan / ketik nomor kirim..."
                                           value="{{ $pembayaran->nomor_kirim ?? '' }}"
                                           {{ $isEdit ? 'readonly' : '' }} autofocus>
                                    @unless($isEdit)
                                        <button type="button" id="btn-scan" class="btn btn-pde-primary px-3">Scan</button>
                                    @endunless
                                </div>
                                <div id="nomor_kirim_error" class="field-error d-none"></div>
                            </div>
                            <div class="col-md-3">
                                <label class="pde-label">Tgl Kirim</label>
                                <input type="text" id="tanggal_kirim" class="pde-input" readonly
                                       value="{{ optional($pembayaran->tanggal_kirim)->format('d/m/Y') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="pde-label">Jenis Biaya</label>
                                <input type="text" id="jenis_biaya_display" class="pde-input" readonly
                                    value="{{ $scan['jenis_biaya'] ?? $pembayaran->jenis_biaya ?? '' }}">
                            </div>
                            <div class="col-md-12">
                                <label class="pde-label">Tujuan Darah (Bank Darah / RS Tujuan)</label>
                                <input type="text" id="institusi_tujuan" class="pde-input" readonly
                                       value="{{ $pembayaran->institusi_tujuan ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pde-card">
                    <div class="pde-card-head">
                        <h2>Tabel Penagihan Biaya</h2>
                        <span class="text-muted small" id="items-count"></span>
                    </div>
                    <div class="table-responsive">
                        <table class="pde-items">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tgl Kirim</th>
                                    <th>No Kirim</th>
                                    <th>No Stock</th>
                                    <th>Jns Darah</th>
                                    <th>Gol/Rhs</th>
                                    <th>Nama Tujuan</th>
                                    <th class="text-end">Tarif</th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                @forelse(($scan['items'] ?? []) as $i => $it)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $it['tgl_kirim'] }}</td>
                                        <td class="mono">{{ $it['no_kirim'] }}</td>
                                        <td class="mono">{{ $it['no_stock'] }}</td>
                                        <td>{{ $it['jenis_darah'] }}</td>
                                        <td>{{ $it['gol_rhesus'] }}</td>
                                        <td>{{ $it['nama_tujuan'] }}</td>
                                        <td class="text-end mono">{{ number_format($it['tarif'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                        @if(empty($scan['items'] ?? []))
                            <div class="pde-empty-items" id="items-empty">
                                Belum ada data. Scan nomor kirim untuk menampilkan daftar kantong darah yang ditagihkan.
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: ringkasan pembayaran --}}
            <div class="col-lg-4">
                <div class="pde-card pde-summary-card">
                    <div class="pde-card-head">
                        <h2>Pembayaran</h2>
                    </div>
                    <div class="pde-card-body">

                        <div class="text-center mb-3">
                            <div class="pde-due-label">Harus Dibayar</div>
                            <div class="pde-due" id="harus_dibayar_display">
                                Rp {{ number_format($pembayaran->harus_dibayar ?? 0, 0, ',', '.') }}
                            </div>
                            <input type="hidden" name="harus_dibayar" id="harus_dibayar" value="{{ $pembayaran->harus_dibayar ?? 0 }}">
                        </div>

                        <div class="mb-3">
                            <label class="pde-label">Jumlah Pembayaran</label>
                            <input type="text" name="pembayaran" id="pembayaran" class="pde-input mono"
                                   value="{{ $pembayaran->pembayaran ?? '' }}" inputmode="numeric"
                                   {{ $isEdit ? 'readonly' : '' }}>
                            <div class="pde-hint" id="pembayaran_hint"></div>
                        </div>

                        <div class="mb-3">
                            <label class="pde-label">Metode</label>
                            <div class="pde-radio-group">
                                <label>
                                    <input type="radio" name="metode_bayar" value="tunai"
                                           {{ old('metode_bayar', $pembayaran->metode_bayar ?? 'tunai') === 'tunai' ? 'checked' : '' }}
                                           {{ $isEdit ? 'disabled' : '' }}>
                                    <span>Tunai</span>
                                </label>
                                <label>
                                    <input type="radio" name="metode_bayar" value="kredit"
                                           {{ old('metode_bayar', $pembayaran->metode_bayar ?? '') === 'kredit' ? 'checked' : '' }}
                                           {{ $isEdit ? 'disabled' : '' }}>
                                    <span>Kredit</span>
                                </label>
                            </div>
                            @if($isEdit)
                                {{-- pertahankan nilai saat field disabled --}}
                                <input type="hidden" name="metode_bayar" value="{{ $pembayaran->metode_bayar }}">
                            @endif
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <label class="pde-label">Tanggal Bayar</label>
                                <input type="datetime-local" name="tanggal_bayar" class="pde-input"
                                       value="{{ optional($pembayaran->tanggal_bayar ?? now())->format('Y-m-d\TH:i') }}"
                                       {{ $isEdit ? 'readonly' : '' }}>
                            </div>
                            <div class="col-5">
                                <label class="pde-label">Kode Kasir</label>
                                <input type="text" class="pde-input" readonly
                                       value="{{ $pembayaran->kode_kasir ?? auth()->user()?->kode ?? 'ADM' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="pde-label">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="pde-input">{{ $pembayaran->keterangan ?? '' }}</textarea>
                        </div>

                        @if($isEdit)
                            <a href="#" class="btn btn-pde-ghost w-100 mb-2" onclick="window.print(); return false;">
                                Cetak Ulang
                            </a>
                        @else
                            <button type="submit" id="btn-simpan" class="btn btn-pde-primary w-100" disabled>
                                <span class="kbd">F8</span> Simpan Pembayaran
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @unless($isEdit)
        <div class="pde-footer-actions">
            <button type="button" id="btn-batal" class="btn btn-pde-ghost"><span class="kbd">F9</span> Batal</button>
        </div>
        @endunless
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script>
(function () {
    const isEdit = @json($isEdit);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const elJenisBiayaDisplay = document.getElementById('jenis_biaya_display');
    const elJenisBiayaHidden   = document.getElementById('jenis_biaya_hidden');
    const elNomorKirim   = document.getElementById('nomor_kirim');
    const elTanggalKirim = document.getElementById('tanggal_kirim');
    const elInstitusi    = document.getElementById('institusi_tujuan');
    const elPengirimanId = document.getElementById('pengiriman_id');
    const elHarusDisplay = document.getElementById('harus_dibayar_display');
    const elHarusHidden  = document.getElementById('harus_dibayar');
    const elPembayaran   = document.getElementById('pembayaran');
    const elItemsBody    = document.getElementById('items-body');
    const elItemsEmpty   = document.getElementById('items-empty');
    const elItemsCount   = document.getElementById('items-count');
    const elBtnSimpan    = document.getElementById('btn-simpan');
    const elBtnScan      = document.getElementById('btn-scan');
    const elHint         = document.getElementById('pembayaran_hint');
    const elNomorError   = document.getElementById('nomor_kirim_error');

    const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

    function setItems(items) {
        elItemsBody.innerHTML = '';
        if (!items || items.length === 0) {
            if (elItemsEmpty) elItemsEmpty.classList.remove('d-none');
            if (elItemsCount) elItemsCount.textContent = '';
            return;
        }
        if (elItemsEmpty) elItemsEmpty.classList.add('d-none');
        if (elItemsCount) elItemsCount.textContent = items.length + ' kantong';

        items.forEach((it, i) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i + 1}</td>
                <td>${it.tgl_kirim ?? ''}</td>
                <td class="mono">${it.no_kirim ?? ''}</td>
                <td class="mono">${it.no_stock ?? ''}</td>
                <td>${it.jenis_darah ?? ''}</td>
                <td>${it.gol_rhesus ?? ''}</td>
                <td>${it.nama_tujuan ?? ''}</td>
                <td class="text-end mono">${Number(it.tarif || 0).toLocaleString('id-ID')}</td>
            `;
            elItemsBody.appendChild(tr);
        });
    }

    function checkHint() {
        const harus = parseFloat(elHarusHidden.value || 0);
        const bayar = parseFloat((elPembayaran.value || '0').replace(/\./g, '').replace(/,/g, '.')) || 0;
        if (!harus) { elHint.textContent = ''; return; }

        if (bayar >= harus) {
            elHint.textContent = 'Lunas — sisa Rp 0';
            elHint.className = 'pde-hint ok';
        } else {
            elHint.textContent = 'Kurang bayar: ' + rupiah(harus - bayar) + ' (akan tercatat Kredit)';
            elHint.className = 'pde-hint warn';
        }
    }

    if (elPembayaran) {
        elPembayaran.addEventListener('input', checkHint);
        if (isEdit) checkHint();
    }

    async function doScan() {
        const nomor = elNomorKirim.value.trim();
        elNomorError.classList.add('d-none');

        if (!nomor) {
            elNomorError.textContent = 'Masukkan / scan nomor kirim terlebih dahulu.';
            elNomorError.classList.remove('d-none');
            return;
        }

        elBtnScan.disabled = true;
        elBtnScan.textContent = 'Mencari...';

        try {
            const res = await fetch(@json(route('finance.pembayaran_dropping_external.cari_kiriman')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ nomor_kirim: nomor }),
            });
            const json = await res.json();

            if (!json.success) {
                elNomorError.textContent = json.message || 'Nomor kirim tidak ditemukan.';
                elNomorError.classList.remove('d-none');
                setItems([]);
                elPengirimanId.value = '';
                elJenisBiayaDisplay.value = '';   // <-- tambahan
                elJenisBiayaHidden.value = ''; 
                elHarusHidden.value = 0;
                elHarusDisplay.textContent = rupiah(0);
                elBtnSimpan.disabled = true;
                return;
            }

            const d = json.data;
            elPengirimanId.value = d.pengiriman_id;
            elTanggalKirim.value = d.tanggal_kirim;
            elInstitusi.value = d.institusi_tujuan;
            elJenisBiayaDisplay.value = d.jenis_biaya;  
            elJenisBiayaHidden.value = d.jenis_biaya;
            elHarusHidden.value = d.harus_dibayar;
            elHarusDisplay.textContent = rupiah(d.harus_dibayar);
            setItems(d.items);

            // default jumlah pembayaran = harus dibayar (kasir bisa ubah jika kredit/parsial)
            elPembayaran.value = d.harus_dibayar;
            checkHint();

            elBtnSimpan.disabled = false;
        } catch (err) {
            elNomorError.textContent = 'Terjadi kesalahan saat menghubungi server.';
            elNomorError.classList.remove('d-none');
        } finally {
            elBtnScan.disabled = false;
            elBtnScan.textContent = 'Scan';
        }
    }

    if (elBtnScan) {
        elBtnScan.addEventListener('click', doScan);
        elNomorKirim.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); doScan(); }
        });
    }

    // Shortcut keyboard mengikuti aplikasi asal: F8 simpan, F9 batal, Esc keluar
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F8') {
            e.preventDefault();
            if (elBtnSimpan && !elBtnSimpan.disabled) document.getElementById('pde-form').requestSubmit();
        }
        if (e.key === 'F9') {
            e.preventDefault();
            document.getElementById('btn-batal')?.click();
        }
        if (e.key === 'Escape') {
            window.location.href = @json(route('finance.pembayaran_dropping_external.index'));
        }
    });

    document.getElementById('btn-batal')?.addEventListener('click', function () {
        Swal.fire({
            title: 'Batalkan input?',
            text: 'Data yang belum disimpan akan hilang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, batalkan',
            cancelButtonText: 'Tetap di sini',
            confirmButtonColor: '#A11D33',
        }).then((res) => {
            if (res.isConfirmed) window.location.href = @json(route('finance.pembayaran_dropping_external.index'));
        });
    });
})();
</script>
@endpush
