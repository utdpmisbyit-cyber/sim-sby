<form method="POST"
      action="{{ isset($anggaran) 
                  ? route('finance.anggaran.update', $anggaran->id) 
                  : route('finance.anggaran.store') }}">
    @csrf

    @if(isset($anggaran))
        @method('PUT')
    @endif

    <div class="modal-body">

        {{-- Kode Anggaran --}}
        <div class="mb-3">
            <label for="kode" class="form-label">Kode</label>
            <input type="text" name="kode" id="kode" class="form-control"
                value="{{ old('kode', $anggaran->kode ?? $kodeOtomatis) }}" readonly>
        </div>

        {{-- Tanggal Input --}}
        <div class="mb-3">
            <label for="tgl_input" class="form-label">Tanggal</label>
            <input type="date" name="tgl_input" id="tgl_input" class="form-control"
                   value="{{ old('tgl_input', isset($anggaran) ? $anggaran->tgl_input->format('Y-m-d') : now()->format('Y-m-d')) }}"
                   required>
        </div>

        {{-- Tahun Anggaran --}}
        <div class="mb-3">
            <label for="tahun_anggaran" class="form-label">Tahun Anggaran</label>
            <input type="number" name="tahun_anggaran" id="tahun_anggaran" class="form-control"
                   value="{{ old('tahun_anggaran', $anggaran->tahun_anggaran ?? now()->year) }}" required>
        </div>

        {{-- Keterangan --}}
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $anggaran->keterangan ?? '') }}</textarea>
        </div>

        {{-- Nilai Anggaran --}}
        <div class="mb-3">
            <label for="nilai_anggaran" class="form-label">Nilai Anggaran</label>
            <input type="number" name="nilai_anggaran" id="nilai_anggaran" class="form-control"
                   value="{{ old('nilai_anggaran', $anggaran->nilai_anggaran ?? '') }}" step="0.01" required>
        </div>

        {{-- Petugas (user_input) --}}
        <div class="mb-3">
            <label for="user_input" class="form-label">Petugas</label>
            <select name="user_input" id="user_input" class="form-select" required>
                <option value="">-- Pilih Petugas --</option>
                @foreach($petugasList as $petugas)
                    <option value="{{ $petugas->id }}"
                        {{ old('user_input', $anggaran->user_input ?? '') == $petugas->id ? 'selected' : '' }}>
                        {{ $petugas->nama }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ isset($anggaran) ? 'Update' : 'Simpan' }}</button>
    </div>
</form>