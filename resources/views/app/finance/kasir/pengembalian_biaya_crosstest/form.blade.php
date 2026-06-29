@extends('layouts.index')

@section('title', isset($pengembalian) ? 'Edit Retur Biaya Cross Test' : 'Retur Biaya Cross Test')

@php
    $isEdit = isset($pengembalian);

    $initialDataForJs = [
        'is_edit'            => $isEdit,
        'permintaan_fpup_id' => null,
        'kode_kasir'         => null,
        'nama_kasir'         => null,
        'no_nota'            => null,
        'keterangan'         => null,
        'total_retur'        => null,
        'items'              => [],
    ];

    if ($isEdit) {
        $initialDataForJs['permintaan_fpup_id'] = $pengembalian->permintaan_fpup_id;
        $initialDataForJs['kode_kasir']          = $pengembalian->kode_kasir;
        $initialDataForJs['nama_kasir']          = $pengembalian->nama_kasir;
        $initialDataForJs['no_nota']             = $pengembalian->no_nota;
        $initialDataForJs['keterangan']          = $pengembalian->keterangan;
        $initialDataForJs['total_retur']         = (float) $pengembalian->total_retur;

        $itemsForJs = [];
        foreach ($pengembalian->details as $d) {
            $itemsForJs[] = [
                'permintaan_fpup_detail_id' => $d->permintaan_fpup_detail_id,
                'nama_os'      => $d->nama_os,
                'jns_darah'    => $d->jns_darah,
                'gol_darah'    => $d->gol_darah,
                'rhesus'       => $d->rhesus,
                'jumlah'       => (int) $d->jumlah,
                'cc'           => $d->cc,
                'harga_satuan' => (float) $d->harga_satuan,
                'subtotal'     => (float) $d->subtotal,
            ];
        }
        $initialDataForJs['items'] = $itemsForJs;
    }
@endphp

@section('content')
<div class="container-fluid py-4">
    <form id="formRetur">
        @csrf

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $isEdit ? 'Edit Retur Biaya Cross Test' : 'Pengembalian Biaya Cross Test' }}</h5>
                <a href="{{ route('finance.pengembalian_biaya_crosstest.index') }}" id="btnKeluar"
                   class="btn btn-sm btn-light border">
                    <i class="bi bi-box-arrow-left"></i> Keluar (Esc)
                </a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">No FPUP</label>
                        <div class="input-group">
                            <input type="text" id="noFpup" class="form-control" placeholder="Scan / ketik No FPUP"
                                   value="{{ $isEdit ? $pengembalian->no_fpup : '' }}"
                                   @if($isEdit) readonly @else autofocus @endif>
                            <button type="button" id="btnScan" class="btn btn-outline-primary" @if($isEdit) disabled @endif>
                                <i class="bi bi-upc-scan"></i> Cari
                            </button>
                        </div>
                        @if($isEdit)
                            <small class="text-muted">No FPUP terkunci saat edit (tidak bisa di-scan ulang).</small>
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tgl FPUP</label>
                        <input type="text" id="tglFpup" class="form-control" readonly
                               value="{{ $isEdit ? optional($pengembalian->tgl_fpup)->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rumah Sakit</label>
                        <input type="text" id="namaRs" class="form-control" readonly
                               value="{{ $isEdit ? $pengembalian->nama_rs : '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Reg</label>
                        <input type="text" id="noReg" class="form-control" readonly
                               value="{{ $isEdit ? $pengembalian->no_reg : '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Kelas Rawat</label>
                        <input type="text" id="kelasRawat" class="form-control" readonly
                               value="{{ $isEdit ? $pengembalian->kelas_rawat : '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Nama O.S. (Pasien)</label>
                        <input type="text" id="namaPasien" class="form-control" readonly
                               value="{{ $isEdit ? $pengembalian->nama_pasien : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Dokter</label>
                        <input type="text" id="namaDokter" class="form-control" readonly
                               value="{{ $isEdit ? $pengembalian->nama_dokter : '' }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bagian</label>
                        <input type="text" id="bagian" class="form-control" readonly
                               value="{{ $isEdit ? $pengembalian->bagian : '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jenis Biaya</label>
                        <select id="jenisBiayaSelect" class="form-select" @if($isEdit) disabled @endif>
                            @if($isEdit && $pengembalian->kode_service_cost)
                                <option value="" selected>{{ $pengembalian->kode_service_cost }}</option>
                            @else
                                <option value="">-</option>
                            @endif
                        </select>
                        @if($isEdit)
                            <small class="text-muted">Dasar biaya terkunci saat edit.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <strong>Tabel Penagihan Biaya</strong>
                <small class="text-muted">(jumlah &amp; harga satuan diambil dari permintaan FPUP &amp; service cost — bisa disesuaikan)</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="tabelItem">
                        <thead class="table-light">
                            <tr>
                                <th>Nama O.S.</th>
                                <th>No Minta</th>
                                <th>Jenis Darah</th>
                                <th>Gol/Rh</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="bodyItem">
                            <tr id="rowEmpty">
                                <td colspan="7" class="text-center text-muted py-4">
                                    {{ $isEdit ? 'Memuat item penagihan...' : 'Scan No FPUP untuk menampilkan item penagihan.' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 position-relative">
                        <label class="form-label">Kode Kasir</label>
                        <input type="text" id="kodeKasirSearch" class="form-control"
                               placeholder="Cari nama / kode kasir..." autocomplete="off">
                        <input type="hidden" id="kodeKasir">
                        <div id="kasirSuggestions" class="list-group position-absolute w-100 shadow-sm"
                             style="z-index: 1050; display:none; max-height:220px; overflow-y:auto;"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nama Kasir</label>
                        <input type="text" id="namaKasir" class="form-control" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">No Nota</label>
                        <input type="text" id="noNota" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Keterangan</label>
                        <input type="text" id="keterangan" class="form-control">
                    </div>
                </div>
                <hr>
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Total Retur</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="totalRetur" class="form-control text-end fw-bold">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success" id="btnSimpan">
                <i class="bi bi-save"></i> {{ $isEdit ? 'Simpan Perubahan (F8)' : 'Simpan (F8)' }}
            </button>
            <button type="button" class="btn btn-outline-danger" id="btnBatal">
                <i class="bi bi-x-circle"></i> Batal (F9)
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Data awal kalau halaman ini dibuka untuk EDIT (kosong/null kalau mode create).
    window.__initialData = @json($initialDataForJs);

    window.__storeUrl = @if($isEdit)
        "{{ route('finance.pengembalian_biaya_crosstest.update', $pengembalian->id) }}"
    @else
        "{{ route('finance.pengembalian_biaya_crosstest.store') }}"
    @endif;
    window.__storeMethod = "{{ $isEdit ? 'PUT' : 'POST' }}";
</script>
<script>
(function () {
    const noFpupInput   = document.getElementById('noFpup');
    const bodyItem      = document.getElementById('bodyItem');
    const totalReturEl  = document.getElementById('totalRetur');
    const jenisBiayaSel = document.getElementById('jenisBiayaSelect');

    const kasirSearchInput = document.getElementById('kodeKasirSearch');
    const kasirSuggestions = document.getElementById('kasirSuggestions');
    const kodeKasirHidden  = document.getElementById('kodeKasir');
    const namaKasirInput   = document.getElementById('namaKasir');

    let currentFpupId       = null;
    let currentItems        = [];
    let jenisBiayaCandidates = [];

    const rupiah   = (n) => new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
    const unrupiah = (s) => Number(String(s).replace(/[^0-9]/g, '')) || 0;

    function setHeader(fpup) {
        document.getElementById('tglFpup').value    = fpup.tgl_fpup ?? '';
        document.getElementById('namaRs').value      = fpup.nama_rs ?? '';
        document.getElementById('noReg').value       = fpup.no_reg ?? '';
        document.getElementById('kelasRawat').value  = fpup.kelas_rawat ?? '';
        document.getElementById('namaPasien').value  = fpup.nama_pasien ?? '';
        document.getElementById('namaDokter').value  = fpup.nama_dokter ?? '';
        document.getElementById('bagian').value      = fpup.bagian ?? '';
        currentFpupId = fpup.id;
    }

    function renderItems(items) {
        currentItems = items;
        bodyItem.innerHTML = '';

        if (! items.length) {
            bodyItem.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada item penagihan untuk FPUP ini.</td></tr>';
            recalcTotal();
            return;
        }

        items.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.nama_os ?? ''}</td>
                <td>${noFpupInput.value}</td>
                <td>${item.jns_darah ?? '-'}</td>
                <td>${(item.gol_darah ?? '') + '/' + (item.rhesus ?? '')}</td>
                <td class="text-end">
                    <input type="number" min="1" class="form-control form-control-sm text-end item-jumlah"
                           value="${item.jumlah}" data-idx="${idx}" style="width:80px">
                </td>
                <td class="text-end">
                    <input type="text" class="form-control form-control-sm text-end item-harga"
                           value="${rupiah(item.harga_satuan)}" data-idx="${idx}" style="width:120px">
                </td>
                <td class="text-end item-subtotal">Rp ${rupiah(item.subtotal)}</td>
            `;
            bodyItem.appendChild(tr);
        });

        bodyItem.querySelectorAll('.item-jumlah, .item-harga').forEach((el) => {
            el.addEventListener('input', onItemChange);
        });

        recalcTotal();
    }

    function onItemChange(e) {
        const idx = Number(e.target.dataset.idx);
        const row = bodyItem.children[idx];
        const jumlah = Number(row.querySelector('.item-jumlah').value) || 0;
        const harga  = unrupiah(row.querySelector('.item-harga').value);

        currentItems[idx].jumlah       = jumlah;
        currentItems[idx].harga_satuan = harga;
        currentItems[idx].subtotal     = jumlah * harga;

        row.querySelector('.item-subtotal').textContent = 'Rp ' + rupiah(currentItems[idx].subtotal);
        recalcTotal();
    }

    function recalcTotal() {
        const total = currentItems.reduce((sum, i) => sum + (i.subtotal || 0), 0);
        totalReturEl.value = rupiah(total);
    }

    function renderJenisBiayaOptions(candidates, selected) {
        jenisBiayaCandidates = candidates ?? [];
        jenisBiayaSel.innerHTML = '<option value="">-</option>';

        jenisBiayaCandidates.forEach((c) => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = `${c.nama} — Rp ${rupiah(c.biaya)}`;
            if (selected && selected.id === c.id) opt.selected = true;
            jenisBiayaSel.appendChild(opt);
        });
    }

    jenisBiayaSel.addEventListener('change', function () {
        const selectedId = Number(this.value);
        const selected = jenisBiayaCandidates.find((c) => c.id === selectedId);
        if (! selected) return;

        currentItems = currentItems.map((i) => ({
            ...i,
            harga_satuan: selected.biaya,
            subtotal: selected.biaya * i.jumlah,
        }));
        renderItems(currentItems);
    });

    let kasirSearchTimeout = null;

    async function cariKasir(keyword) {
        try {
            const res = await fetch(`{{ route('finance.pengembalian_biaya_crosstest.cari-kasir') }}?q=${encodeURIComponent(keyword)}`, {
                headers: { Accept: 'application/json' },
            });
            const list = await res.json();
            renderKasirSuggestions(list);
        } catch (e) {
            console.error(e);
        }
    }

    function renderKasirSuggestions(list) {
        if (! list.length) {
            kasirSuggestions.innerHTML = '<div class="list-group-item text-muted small">Tidak ditemukan</div>';
            kasirSuggestions.style.display = 'block';
            return;
        }

        kasirSuggestions.innerHTML = list.map((p) => `
            <button type="button" class="list-group-item list-group-item-action kasir-option"
                    data-kode="${p.kode}" data-nama="${p.nama}">
                <strong>${p.kode}</strong> — ${p.nama}
            </button>
        `).join('');
        kasirSuggestions.style.display = 'block';

        kasirSuggestions.querySelectorAll('.kasir-option').forEach((btn) => {
            btn.addEventListener('click', function () {
                kodeKasirHidden.value   = this.dataset.kode;
                namaKasirInput.value    = this.dataset.nama;
                kasirSearchInput.value  = `${this.dataset.kode} — ${this.dataset.nama}`;
                kasirSuggestions.style.display = 'none';
            });
        });
    }

    kasirSearchInput.addEventListener('input', function () {
        const keyword = this.value.trim();

        // Kalau user mengetik ulang, reset pilihan kasir yang sudah tersimpan
        kodeKasirHidden.value = '';
        namaKasirInput.value  = '';

        clearTimeout(kasirSearchTimeout);

        if (keyword.length < 2) {
            kasirSuggestions.style.display = 'none';
            kasirSuggestions.innerHTML = '';
            return;
        }

        kasirSearchTimeout = setTimeout(() => cariKasir(keyword), 300);
    });

    document.addEventListener('click', (e) => {
        if (! kasirSearchInput.contains(e.target) && ! kasirSuggestions.contains(e.target)) {
            kasirSuggestions.style.display = 'none';
        }
    });

    async function scanFpup() {
        const noFpup = noFpupInput.value.trim();
        if (! noFpup) return;

        try {
            const res = await fetch(`{{ route('finance.pengembalian_biaya_crosstest.scan-fpup') }}?no_fpup=${encodeURIComponent(noFpup)}`, {
                headers: { Accept: 'application/json' },
            });

            if (! res.ok) {
                const err = await res.json();
                alert(err.message ?? 'No FPUP tidak ditemukan.');
                return;
            }

            const result = await res.json();
            setHeader(result.fpup);
            renderJenisBiayaOptions(result.jenis_biaya_candidates, result.service_cost_dipakai);
            renderItems(result.items);
        } catch (e) {
            console.error(e);
            alert('Gagal mengambil data FPUP.');
        }
    }

    // ===== Inisialisasi mode EDIT (kosong/no-op kalau mode create) =====
    const initial = window.__initialData || { is_edit: false };

    if (initial.is_edit) {
        currentFpupId = initial.permintaan_fpup_id;

        renderItems(initial.items || []);

        kodeKasirHidden.value = initial.kode_kasir || '';
        namaKasirInput.value  = initial.nama_kasir || '';
        if (initial.kode_kasir || initial.nama_kasir) {
            kasirSearchInput.value = `${initial.kode_kasir ?? ''} — ${initial.nama_kasir ?? ''}`;
        }

        document.getElementById('noNota').value     = initial.no_nota || '';
        document.getElementById('keterangan').value = initial.keterangan || '';

        // Total retur dipakai apa adanya dari data tersimpan (mungkin sudah disesuaikan
        // manual oleh kasir sebelumnya), JANGAN ditimpa hasil hitung ulang dari renderItems().
        if (initial.total_retur !== null && initial.total_retur !== undefined) {
            totalReturEl.value = rupiah(initial.total_retur);
        }
    }
    // ===== End inisialisasi mode EDIT =====

    document.getElementById('btnScan').addEventListener('click', scanFpup);
    noFpupInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); scanFpup(); }
    });

    document.getElementById('btnBatal').addEventListener('click', () => {
        window.location.href = "{{ route('finance.pengembalian_biaya_crosstest.index') }}";
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'F8') {
            e.preventDefault();
            if (! document.getElementById('btnSimpan').disabled) {
                document.getElementById('formRetur').requestSubmit();
            }
        }
        if (e.key === 'F9') { e.preventDefault(); document.getElementById('btnBatal').click(); }
        if (e.key === 'Escape') { e.preventDefault(); document.getElementById('btnKeluar').click(); }
    });

    document.getElementById('formRetur').addEventListener('submit', async function (e) {
        e.preventDefault();

        const btnSimpan = document.getElementById('btnSimpan');

        // Cegah submit dobel (double click / double-tap F8) yang bisa bikin
        // dua request hampir bersamaan dan menghasilkan no_retur duplikat.
        if (btnSimpan.disabled) {
            return;
        }

        if (! currentItems.length) {
            alert('Tidak ada item untuk diretur. Scan No FPUP terlebih dahulu.');
            return;
        }

        btnSimpan.disabled = true;
        const btnSimpanLabel = btnSimpan.innerHTML;
        btnSimpan.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';

        const selectedJenisBiayaId = jenisBiayaSel.value ? Number(jenisBiayaSel.value) : null;
        const selectedCandidate    = jenisBiayaCandidates.find((c) => c.id === selectedJenisBiayaId);

        const payload = {
            no_fpup:             noFpupInput.value.trim(),
            permintaan_fpup_id:  currentFpupId,
            kode_kasir:          document.getElementById('kodeKasir').value,
            nama_kasir:          document.getElementById('namaKasir').value,
            no_nota:             document.getElementById('noNota').value,
            keterangan:          document.getElementById('keterangan').value,
            total_retur:         unrupiah(totalReturEl.value),
            jenis_biaya_id:      selectedJenisBiayaId,
            kode_service_cost:   selectedCandidate ? selectedCandidate.kode : null,
            items: currentItems.map((i) => ({
                permintaan_fpup_detail_id: i.permintaan_fpup_detail_id,
                nama_os:      i.nama_os,
                jns_darah:    i.jns_darah,
                gol_darah:    i.gol_darah,
                rhesus:       i.rhesus,
                jumlah:       i.jumlah,
                cc:           i.cc,
                harga_satuan: i.harga_satuan,
            })),
        };

        try {
            const res = await fetch(window.__storeUrl, {
                method: window.__storeMethod,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });

            const out = await res.json();

            if (! res.ok) {
                alert(out.message ?? 'Gagal menyimpan data.');
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = btnSimpanLabel;
                return;
            }

            alert(out.message ?? 'Berhasil disimpan.');
            window.location.href = "{{ route('finance.pengembalian_biaya_crosstest.index') }}";
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan.');
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = btnSimpanLabel;
        }
    });
})();
</script>
@endpush