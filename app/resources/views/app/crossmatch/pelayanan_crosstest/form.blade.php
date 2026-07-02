
<form id="formPelayanan" class="p-0" style="width:100%;" onsubmit="return false;">
    @csrf
    <input type="hidden" id="hidMethod"           value="POST">
    <input type="hidden" id="hidCrossTestId">
    <input type="hidden" id="hidPermintaanFpupId">
    <input type="hidden" id="hidNoFpup">

    <div class="row g-3 p-3">

        {{-- ── Identitas Kantong Darah ──────────────────────────────────── --}}
        <div class="col-12">
            <p class="section-label">
                <i class="bi bi-droplet-fill"></i> Identitas Kantong Darah
            </p>
        </div>

        <div class="col-md-3">
            <label class="form-label">No Stok <span class="text-danger">*</span></label>
            <div class="scan-wrap">
                <i class="bi bi-upc-scan scan-icon"></i>
                <input type="text" id="inpNoStock" class="form-control"
                       placeholder="Scan No Stok..." autocomplete="off">
                <button type="button" class="btn-utd-outline" id="btnScanStock"
                        style="padding:.5rem .75rem;" title="Cari stok">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label">Jns Darah</label>
            <input type="text" id="inpJnsDarah" class="form-control" placeholder="PRC, WB...">
        </div>

        <div class="col-md-2">
            <label class="form-label">Gol Darah</label>
            <input type="text" id="inpGol" class="form-control text-center" placeholder="O/A/B/AB">
        </div>

        <div class="col-md-2">
            <label class="form-label">Rhesus</label>
            <select id="inpRhesus" class="form-select">
                <option value="+">Positif (+)</option>
                <option value="-">Negatif (-)</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Metode</label>
            <select id="inpMetode" class="form-select">
                <option value="GEL">GEL</option>
                <option value="TUBE">TUBE</option>
                <option value="COLUMN">COLUMN</option>
            </select>
        </div>

        {{-- ── Hasil Pemeriksaan ────────────────────────────────────────── --}}
        <div class="col-12">
            <p class="section-label mt-1">
                <i class="bi bi-clipboard2-pulse-fill"></i> Hasil Pemeriksaan
            </p>
        </div>

        <div class="col-md-3">
            <label class="form-label">Hasil Crossmatch</label>
            <select id="inpHasil" class="form-select">
                <option value="">— Belum Diperiksa —</option>
                <option value="Cocok">✓ Cocok</option>
                <option value="Tidak Cocok">✕ Tidak Cocok</option>
                <option value="Doubtful">? Doubtful</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Skrining</label>
            <select id="inpSkrining" class="form-select">
                <option value="-">-</option>
                <option value="NEG">NEG</option>
                <option value="POS">POS</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label d-block">NAT</label>
            <div class="form-check form-switch mt-2">
                <input type="checkbox" id="inpNat" class="form-check-input" value="1">
                <label class="form-check-label" for="inpNat">Diperiksa</label>
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Status</label>
            <select id="inpStatus" class="form-select">
                <option value="pending">Pending</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
                <option value="batal">Batal</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" id="inpKeterangan" class="form-control" placeholder="Keterangan hasil...">
        </div>

        <div class="col-md-6">
            <label class="form-label">Catatan</label>
            <input type="text" id="inpCatatan" class="form-control" placeholder="Catatan tambahan...">
        </div>

        {{-- ── Petugas ──────────────────────────────────────────────────── --}}
        <div class="col-12">
            <p class="section-label">
                <i class="bi bi-person-badge-fill"></i> Petugas Pemeriksa
            </p>
        </div>

        <div class="col-md-6">
            <div class="scan-wrap">
                <i class="bi bi-person-badge scan-icon"></i>
                <input type="text" id="inpPemeriksa" class="form-control"
                       placeholder="Scan NIP atau ketik nama pemeriksa...">
                <button type="button" class="btn-utd-outline" id="btnScanPetugas">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>

        {{-- ── Tombol Aksi ──────────────────────────────────────────────── --}}
        <div class="col-12 pt-3 border-top">
            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- Simpan Semua Pending ke DB (TOMBOL UTAMA - muncul kalau ada pending) --}}
                <button type="button" class="btn-utd" id="btnSimpanSemua"
                        style="display:none; background:#27ae60; font-weight:700;" 
                        title="Simpan semua kantong yang sudah di-scan ke database">
                    <i class="bi bi-save-fill me-1"></i> Simpan Semua
                    <span id="spnPendingCount"
                          style="background:rgba(255,255,255,.3);border-radius:1rem;
                                 padding:0.2rem .5rem;font-size:.75rem;margin-left:.5rem;
                                 font-weight:600;">0</span>
                </button>

                {{-- MODE EDIT: Update record yang sudah ada di DB --}}
                <button type="button" class="btn-utd" id="btnUpdateRecord"
                        style="display:none; background:#2980b9;"
                        title="Perbarui data crossmatch yang sedang diedit">
                    <i class="bi bi-save me-1"></i> Update
                </button>

                {{-- MODE TAMBAH MANUAL: Alternatif untuk input tanpa scan --}}
                <button type="button" class="btn-utd-outline" id="btnAddToPending"
                        style="border-color:#aaa; color:#555;" 
                        title="Tambah kantong secara manual (alternatif tanpa scan)">
                    <i class="bi bi-plus-circle me-1"></i> + Manual
                </button>

                {{-- Batal / Reset --}}
                <button type="button" class="btn-utd-outline" id="btnCancel"
                        title="Batal dan reset form">
                    <i class="bi bi-x-lg me-1"></i> Batal
                </button>

                <span id="pendingInfo" class="ms-auto text-muted" style="font-size:.75rem;"></span>
            </div>
        </div>

    </div>
</form>