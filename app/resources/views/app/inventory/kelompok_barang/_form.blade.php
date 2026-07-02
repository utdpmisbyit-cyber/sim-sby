<div class="modal-header">
    <h3 class="fw-bold mb-0">
        {{ isset($kelompok_barang) ? 'Edit Kelompok Barang' : 'Tambah Kelompok Barang' }}
    </h3>
    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">X</button>
</div>

<form id="form_info">
    @csrf

    <div class="modal-body">

        {{-- KODE --}}
        <div class="mb-5">
            <label class="form-label required">Kode</label>
            <input type="text" name="kode" class="form-control"
                value="{{ $kelompok_barang->kode ?? $kode_otomatis ?? '' }}"
                {{ isset($kelompok_barang) ? '' : 'readonly' }}>
        </div>

        {{-- NAMA --}}
        <div class="mb-5">
            <label class="form-label required">Nama Kelompok</label>
            <input type="text" name="nama" class="form-control"
                value="{{ $kelompok_barang->nama ?? '' }}" required>
        </div>

    </div>

    <div class="modal-footer d-flex justify-content-end">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            Simpan
        </button>
    </div>
</form>

<script>
    init_form(`{{ $kelompok_barang->id ?? '' }}`);
</script>