<div class="modal-header">
    <h3 class="fw-bold mb-0">
        {{ isset($coa) ? 'Edit COA' : 'Tambah COA' }}
    </h3>
    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">X</button>
</div>

<form id="form_info">
    @csrf

    <div class="modal-body">

        {{-- KODE COA --}}
        <div class="mb-5">
            <label class="form-label required">Kode COA</label>
            <input type="text" name="kd_coa" class="form-control"
                value="{{ $coa->kd_coa ?? $kode_otomatis ?? '' }}" required>
        </div>

        {{-- NAMA AKUN --}}
        <div class="mb-5">
            <label class="form-label required">Nama Akun</label>
            <input type="text" name="nama_akun" class="form-control"
                value="{{ $coa->nama_akun ?? '' }}" required>
        </div>

        {{-- KATEGORI --}}
        <div class="mb-5">
            <label class="form-label">Kategori 1</label>
            <input type="text" name="kategori_1" class="form-control"
                value="{{ $coa->kategori_1 ?? '' }}">
        </div>

        <div class="mb-5">
            <label class="form-label">Kategori 2</label>
            <input type="text" name="kategori_2" class="form-control"
                value="{{ $coa->kategori_2 ?? '' }}">
        </div>

        {{-- POS --}}
        <div class="mb-5">
            <label class="form-label">Pos Saldo</label>
          <select name="possaldo" class="form-control" required>
            <option value="">-- Pilih Pos Saldo --</option>
            <option value="Debit"
                {{ old('possaldo', $coa->possaldo ?? '') == 'Debit' ? 'selected' : '' }}>
                Debit
            </option>
            <option value="Kredit"
                {{ old('possaldo', $coa->possaldo ?? '') == 'Kredit' ? 'selected' : '' }}>
                Kredit
            </option>
        </select>
        </div>
        <div class="mb-5">
            <label class="form-label">Pos Laporan</label>
            <select name="poslaporan" class="form-control" required>
                <option value="">-- Pilih Pos Laporan --</option>
                <option value="Neraca"
                    {{ old('poslaporan', $coa->poslaporan ?? '') == 'Neraca' ? 'selected' : '' }}>
                    Neraca
                </option>

                <option value="Laba Rugi"
                    {{ old('poslaporan', $coa->poslaporan ?? '') == 'Laba Rugi' ? 'selected' : '' }}>
                    Laba Rugi
                </option>
            </select>
        </div>

    </div>

    <div class="modal-footer d-flex justify-content-end">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form(`{{ $coa->kd_coa ?? '' }}`);
</script>