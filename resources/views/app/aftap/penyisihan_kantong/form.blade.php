@extends('layouts.index')

@php
    $isEdit = isset($penyisihan) && $penyisihan;
@endphp

@section('title', $isEdit ? 'Detail Penyisihan Kantong AFTAP' : 'Tambah Penyisihan Kantong AFTAP')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            {{ $isEdit ? 'Detail / Edit Penyisihan Kantong AFTAP' : 'Tambah Penyisihan Kantong AFTAP' }}
        </h4>
        <a href="{{ route('aftap.penyisihan_kantong_aftap.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="form-penyisihan"
          action="{{ $isEdit ? route('aftap.penyisihan_kantong_aftap.update', $penyisihan->id) : route('aftap.penyisihan_kantong_aftap.store') }}"
          method="POST">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        {{-- ============ HEADER ============ --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Nomor Penyisihan</label>
                        <input type="text" class="form-control bg-light"
                               value="{{ $isEdit ? $penyisihan->no_transaksi : 'Otomatis saat disimpan' }}" disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Penyisihan <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ old('tanggal', $isEdit ? \Carbon\Carbon::parse($penyisihan->tanggal)->format('Y-m-d') : date('Y-m-d')) }}"
                               required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                               value="{{ old('keterangan', $isEdit ? $penyisihan->keterangan : null) }}"
                               placeholder="Catatan umum transaksi (opsional)">
                    </div>
                    @if ($isEdit)
                        <div class="col-md-12 text-muted small">
                            Dibuat oleh {{ $penyisihan->creator->name ?? '-' }}
                            pada {{ $penyisihan->created_at?->format('d-m-Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============ SCAN KANTONG ============ --}}
        <div class="card mb-3">
            <div class="card-header">Scan Kantong</div>
            <div class="card-body">

                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">No. Kantong</label>
                        <input type="text" id="input-no-kantong" class="form-control"
                               placeholder="Scan / ketik no. kantong" autofocus>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Jenis - Gol - Rh</label>
                        <input type="text" id="display-jenis-gol-rh" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Status</label>
                        <input type="text" id="display-status" class="form-control bg-light" readonly>
                    </div>
                </div>

                <div class="row g-3 align-items-end mt-1">
                    <div class="col-md-4">
                        <label class="form-label">Alasan <span class="text-danger">*</span></label>
                        <select id="select-alasan" class="form-select">
                            <option value="">-- Pilih Alasan --</option>
                            @foreach ($daftarAlasan as $alasan)
                                <option value="{{ $alasan }}">{{ $alasan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="button" id="btn-scan" class="btn btn-outline-primary">
                            <i class="fa fa-barcode"></i> Cari
                        </button>
                        <button type="button" id="btn-tambah" class="btn btn-primary" disabled>
                            <i class="fa fa-plus"></i> Tambah ke Daftar
                        </button>
                    </div>
                    <div class="col-md-auto">
                        <span id="scan-status" class="text-muted"></span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============ GRID KANTONG ============ --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Daftar Kantong Disisihkan</span>
                @if ($isEdit)
                    <span class="badge bg-secondary" id="badge-jumlah">{{ $penyisihan->details->count() }} kantong</span>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>No. Kantong</th>
                                <th>Jns</th>
                                <th>Gol</th>
                                <th>Rhesus</th>
                                <th>Status</th>
                                <th>Alasan</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-kantong-body">
                            @if ($isEdit)
                                @forelse ($penyisihan->details as $index => $detail)
                                    <tr data-detail-id="{{ $detail->id }}">
                                        <td class="col-no">{{ $index + 1 }}</td>
                                        <td>{{ $detail->no_kantong }}</td>
                                        <td>{{ $detail->jenis ?? '-' }}</td>
                                        <td>{{ $detail->gol_darah ?? '-' }}</td>
                                        <td>{{ $detail->rhesus ?? '-' }}</td>
                                        <td>{{ $detail->status ?? '-' }}</td>
                                        <td class="cell-alasan">{{ $detail->alasan ?? '-' }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-ubah-alasan"
                                                    data-id="{{ $detail->id }}"
                                                    data-alasan="{{ $detail->alasan }}"
                                                    data-no-kantong="{{ $detail->no_kantong }}" title="Ubah Alasan">
                                                <i class="fa fa-pen"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove"
                                                    data-id="{{ $detail->id }}"
                                                    data-no-kantong="{{ $detail->no_kantong }}" title="Hapus">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="row-empty">
                                        <td colspan="8" class="text-center text-muted py-3">
                                            Belum ada kantong yang ditambahkan.
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr id="row-empty">
                                    <td colspan="8" class="text-center text-muted py-3">
                                        Belum ada kantong yang ditambahkan.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @unless ($isEdit)
                    <div id="kantong-ids-container"></div>
                @endunless
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <div>
                @if ($isEdit)
                    <button type="button" id="btn-hapus-transaksi" class="btn btn-outline-danger">
                        <i class="fa fa-trash"></i> Hapus Transaksi
                    </button>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('aftap.penyisihan_kantong_aftap.index') }}" class="btn btn-secondary">
                    {{ $isEdit ? 'Tutup' : 'Batal' }}
                </a>
                <button type="submit" id="btn-submit" class="btn btn-success" {{ $isEdit ? '' : 'disabled' }}>
                    <i class="fa fa-save"></i> {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Penyisihan' }}
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ============ MODAL UBAH ALASAN ============ --}}
<div class="modal fade" id="modal-ubah-alasan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Alasan Kantong <span id="modal-no-kantong"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Alasan</label>
                <select id="modal-select-alasan" class="form-select">
                    <option value="">-- Pilih Alasan --</option>
                    @foreach ($daftarAlasan as $alasan)
                        <option value="{{ $alasan }}">{{ $alasan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-simpan-alasan" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

@if ($isEdit)
<form id="form-hapus-transaksi" action="{{ route('aftap.penyisihan_kantong_aftap.destroy', $penyisihan->id) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endif

<script>
(function () {
    const isEdit = @json($isEdit);

    const inputNoKantong    = document.getElementById('input-no-kantong');
    const displayJenisGolRh = document.getElementById('display-jenis-gol-rh');
    const displayStatus     = document.getElementById('display-status');
    const selectAlasan      = document.getElementById('select-alasan');
    const btnScan           = document.getElementById('btn-scan');
    const btnTambah         = document.getElementById('btn-tambah');
    const scanStatus        = document.getElementById('scan-status');
    const tableBody         = document.getElementById('table-kantong-body');
    const btnSubmit         = document.getElementById('btn-submit');
    const idsContainer      = document.getElementById('kantong-ids-container');
    const badgeJumlah       = document.getElementById('badge-jumlah');

    const scanUrl        = @json(route('aftap.penyisihan_kantong_aftap.scan_kantong'));
    const addDetailUrl   = @json($isEdit ? route('aftap.penyisihan_kantong_aftap.detail.add', $penyisihan->id) : null);
    const detailBaseUrl  = @json(url('aftap/penyisihan_kantong_aftap/detail'));
    const csrfToken      = @json(csrf_token());

    let currentKantong = null;

    // Hanya dipakai di mode CREATE: Map<kantong_id, {no_kantong, jenis, gol_darah, rhesus, status_kirim, alasan}>
    const daftarKantong = new Map();

    function resetScanDisplay() {
        currentKantong = null;
        displayJenisGolRh.value = '';
        displayStatus.value = '';
        selectAlasan.value = '';
        btnTambah.disabled = true;
    }

    function removeEmptyRow() {
        document.getElementById('row-empty')?.remove();
    }

    function renumberRows() {
        tableBody.querySelectorAll('tr[data-detail-id], tr[data-kantong-id]').forEach((tr, index) => {
            const cellNo = tr.querySelector('.col-no');
            if (cellNo) cellNo.textContent = index + 1;
        });
    }

    function checkEmptyState() {
        const hasRows = tableBody.querySelector('tr[data-detail-id], tr[data-kantong-id]');
        if (!hasRows && !document.getElementById('row-empty')) {
            const tr = document.createElement('tr');
            tr.id = 'row-empty';
            tr.innerHTML = `<td colspan="8" class="text-center text-muted py-3">Belum ada kantong yang ditambahkan.</td>`;
            tableBody.appendChild(tr);
        }
        if (badgeJumlah) {
            badgeJumlah.textContent = tableBody.querySelectorAll('tr[data-detail-id]').length + ' kantong';
        }
    }

    /**
     * Render baris untuk MODE EDIT (sudah tersimpan di database, punya detail id asli).
     */
    function renderSavedRow(detail) {
        const tr = document.createElement('tr');
        tr.dataset.detailId = detail.id;
        tr.innerHTML = `
            <td class="col-no"></td>
            <td>${detail.no_kantong}</td>
            <td>${detail.jenis ?? '-'}</td>
            <td>${detail.gol_darah ?? '-'}</td>
            <td>${detail.rhesus ?? '-'}</td>
            <td>${detail.status ?? '-'}</td>
            <td class="cell-alasan">${detail.alasan}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-warning btn-ubah-alasan"
                        data-id="${detail.id}" data-alasan="${detail.alasan}" data-no-kantong="${detail.no_kantong}" title="Ubah Alasan">
                    <i class="fa fa-pen"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove"
                        data-id="${detail.id}" data-no-kantong="${detail.no_kantong}" title="Hapus">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        `;
        return tr;
    }

    /**
     * Render ulang seluruh grid untuk MODE CREATE (masih di memori browser,
     * dikirim sebagai hidden input saat submit).
     */
    function renderPendingTable() {
        tableBody.innerHTML = '';
        idsContainer.innerHTML = '';

        if (daftarKantong.size === 0) {
            checkEmptyState();
            btnSubmit.disabled = true;
            return;
        }

        btnSubmit.disabled = false;

        let no = 1;
        daftarKantong.forEach((item, id) => {
            const tr = document.createElement('tr');
            tr.dataset.kantongId = id;
            tr.innerHTML = `
                <td class="col-no">${no}</td>
                <td>${item.no_kantong}</td>
                <td>${item.jenis ?? '-'}</td>
                <td>${item.gol_darah ?? '-'}</td>
                <td>${item.rhesus ?? '-'}</td>
                <td>${item.status_kirim ?? '-'}</td>
                <td class="cell-alasan">${item.alasan}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-warning btn-ubah-alasan"
                            data-id="${id}" data-alasan="${item.alasan}" data-no-kantong="${item.no_kantong}" title="Ubah Alasan">
                        <i class="fa fa-pen"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove"
                            data-id="${id}" data-no-kantong="${item.no_kantong}" title="Hapus">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);

            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = `kantong[${id}][id]`;
            hiddenId.value = id;
            idsContainer.appendChild(hiddenId);

            const hiddenAlasan = document.createElement('input');
            hiddenAlasan.type = 'hidden';
            hiddenAlasan.name = `kantong[${id}][alasan]`;
            hiddenAlasan.value = item.alasan;
            idsContainer.appendChild(hiddenAlasan);

            no++;
        });
    }

    async function scanKantong() {
        const noKantong = inputNoKantong.value.trim();
        if (!noKantong) return;

        scanStatus.textContent = 'Mencari...';
        scanStatus.className = 'text-muted';
        resetScanDisplay();

        try {
            const response = await fetch(scanUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ no_kantong: noKantong }),
            });

            const result = await response.json();

            if (!response.ok) {
                const message = result?.errors?.no_kantong?.[0] ?? result?.message ?? 'Kantong tidak ditemukan.';
                scanStatus.textContent = message;
                scanStatus.className = 'text-danger';
                inputNoKantong.select();
                return;
            }

            const data = result.data;
            const sudahAda = isEdit
                ? tableBody.querySelector(`tr[data-detail-id] td:nth-child(2)`) && [...tableBody.querySelectorAll('tr[data-detail-id]')].some(tr => tr.dataset.noKantong === data.no_kantong || tr.children[1].textContent === data.no_kantong)
                : daftarKantong.has(String(data.id));

            if (sudahAda) {
                scanStatus.textContent = `Kantong ${data.no_kantong} sudah ada di daftar.`;
                scanStatus.className = 'text-warning';
                return;
            }

            currentKantong = data;
            displayJenisGolRh.value = [data.jenis, data.gol_darah, data.rhesus].filter(Boolean).join(' / ');
            displayStatus.value = data.status_kirim ?? '-';
            btnTambah.disabled = false;

            scanStatus.textContent = `Kantong ${data.no_kantong} ditemukan. Pilih alasan lalu klik "Tambah ke Daftar".`;
            scanStatus.className = 'text-success';
            selectAlasan.focus();
        } catch (e) {
            scanStatus.textContent = 'Terjadi kesalahan saat mencari kantong.';
            scanStatus.className = 'text-danger';
        }
    }

    async function tambahKeDaftar() {
        if (!currentKantong) {
            scanStatus.textContent = 'Scan kantong terlebih dahulu.';
            scanStatus.className = 'text-danger';
            return;
        }

        const alasan = selectAlasan.value;
        if (!alasan) {
            scanStatus.textContent = 'Pilih alasan terlebih dahulu.';
            scanStatus.className = 'text-danger';
            selectAlasan.focus();
            return;
        }

        if (isEdit) {
            btnTambah.disabled = true;
            try {
                const response = await fetch(addDetailUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ kantong_id: currentKantong.id, alasan }),
                });

                const result = await response.json();

                if (!response.ok) {
                    scanStatus.textContent = result?.errors?.kantong?.[0] ?? 'Gagal menambahkan kantong.';
                    scanStatus.className = 'text-danger';
                    btnTambah.disabled = false;
                    return;
                }

                removeEmptyRow();
                tableBody.appendChild(renderSavedRow(result.data));
                renumberRows();
                checkEmptyState();

                scanStatus.textContent = `Kantong ${currentKantong.no_kantong} berhasil ditambahkan & tersimpan.`;
                scanStatus.className = 'text-success';
                resetScanDisplay();
                inputNoKantong.value = '';
                inputNoKantong.focus();
            } catch (e) {
                scanStatus.textContent = 'Terjadi kesalahan saat menambahkan kantong.';
                scanStatus.className = 'text-danger';
                btnTambah.disabled = false;
            }
        } else {
            daftarKantong.set(String(currentKantong.id), {
                no_kantong: currentKantong.no_kantong,
                jenis: currentKantong.jenis,
                gol_darah: currentKantong.gol_darah,
                rhesus: currentKantong.rhesus,
                status_kirim: currentKantong.status_kirim,
                alasan: alasan,
            });

            renderPendingTable();

            scanStatus.textContent = `Kantong ${currentKantong.no_kantong} ditambahkan ke daftar.`;
            scanStatus.className = 'text-success';
            resetScanDisplay();
            inputNoKantong.value = '';
            inputNoKantong.focus();
        }
    }

    btnScan.addEventListener('click', scanKantong);
    inputNoKantong.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            scanKantong();
        }
    });
    btnTambah.addEventListener('click', tambahKeDaftar);

    // ---------- Ubah Alasan (modal, dipakai kedua mode) ----------
    const modalEl = document.getElementById('modal-ubah-alasan');
    // Modal manual (tidak bergantung pada file JS Bootstrap yang mungkin
    // belum dimuat oleh layout). Cukup toggle class + backdrop sendiri.
    let backdropEl = null;

    function showModal(el) {
        el.style.display = 'block';
        el.classList.add('show');
        el.setAttribute('aria-modal', 'true');
        el.removeAttribute('aria-hidden');
        document.body.classList.add('modal-open');

        if (!backdropEl) {
            backdropEl = document.createElement('div');
            backdropEl.className = 'modal-backdrop fade show';
            document.body.appendChild(backdropEl);
        }
    }

    function hideModal(el) {
        el.style.display = 'none';
        el.classList.remove('show');
        el.setAttribute('aria-hidden', 'true');
        el.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');

        if (backdropEl) {
            backdropEl.remove();
            backdropEl = null;
        }
    }

    const modal = modalEl ? { show: () => showModal(modalEl), hide: () => hideModal(modalEl) } : null;

    modalEl?.querySelectorAll('[data-bs-dismiss="modal"]').forEach((btn) => {
        btn.addEventListener('click', () => hideModal(modalEl));
    });
    const modalNoKantong = document.getElementById('modal-no-kantong');
    const modalSelectAlasan = document.getElementById('modal-select-alasan');
    const btnSimpanAlasan = document.getElementById('btn-simpan-alasan');
    let idAktif = null;

    tableBody.addEventListener('click', function (e) {
        const btnUbah = e.target.closest('.btn-ubah-alasan');
        const btnHapus = e.target.closest('.btn-remove');

        if (btnUbah) {
            idAktif = btnUbah.dataset.id;
            modalNoKantong.textContent = btnUbah.dataset.noKantong;
            modalSelectAlasan.value = btnUbah.dataset.alasan ?? '';
            modal.show();
        }

        if (btnHapus) {
            hapusBaris(btnHapus.dataset.id, btnHapus.dataset.noKantong);
        }
    });

    btnSimpanAlasan.addEventListener('click', async function () {
        const alasanBaru = modalSelectAlasan.value;
        if (!alasanBaru) {
            alert('Pilih alasan terlebih dahulu.');
            return;
        }

        if (isEdit) {
            try {
                const response = await fetch(`${detailBaseUrl}/${idAktif}/alasan`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ alasan: alasanBaru }),
                });

                if (!response.ok) {
                    alert('Gagal menyimpan alasan.');
                    return;
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menyimpan alasan.');
                return;
            }
        } else {
            const item = daftarKantong.get(idAktif);
            if (item) {
                item.alasan = alasanBaru;
                daftarKantong.set(idAktif, item);
            }
        }

        const row = tableBody.querySelector(`tr[data-detail-id="${idAktif}"] .cell-alasan, tr[data-kantong-id="${idAktif}"] .cell-alasan`);
        if (row) row.textContent = alasanBaru;

        const btn = tableBody.querySelector(`.btn-ubah-alasan[data-id="${idAktif}"]`);
        if (btn) btn.dataset.alasan = alasanBaru;

        if (!isEdit) {
            // sinkronkan hidden input alasan
            const hiddenAlasan = idsContainer.querySelector(`input[name="kantong[${idAktif}][alasan]"]`);
            if (hiddenAlasan) hiddenAlasan.value = alasanBaru;
        }

        modal.hide();
    });

    async function hapusBaris(id, noKantong) {
        if (!confirm(`Hapus kantong ${noKantong} dari daftar?`)) return;

        if (isEdit) {
            try {
                const response = await fetch(`${detailBaseUrl}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                });

                if (!response.ok) {
                    alert('Gagal menghapus kantong.');
                    return;
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menghapus kantong.');
                return;
            }

            tableBody.querySelector(`tr[data-detail-id="${id}"]`)?.remove();
            renumberRows();
            checkEmptyState();
        } else {
            daftarKantong.delete(id);
            renderPendingTable();
        }
    }

    // ---------- Hapus transaksi (mode edit) ----------
    const btnHapusTransaksi = document.getElementById('btn-hapus-transaksi');
    if (btnHapusTransaksi) {
        btnHapusTransaksi.addEventListener('click', function () {
            if (confirm('Hapus seluruh transaksi ini? Semua kantong akan dikembalikan ke status tersedia.')) {
                document.getElementById('form-hapus-transaksi').submit();
            }
        });
    }

    // ---------- Validasi submit (mode create) ----------
    document.getElementById('form-penyisihan').addEventListener('submit', function (e) {
        if (!isEdit && daftarKantong.size === 0) {
            e.preventDefault();
            alert('Tambahkan minimal satu kantong sebelum menyimpan.');
        }
    });
})();
</script>
@endsection