<form method="POST"
      action="{{ isset($kas_masuk) 
                  ? route('finance.kas_masuk.update', $kas_masuk->id) 
                  : route('finance.kas_masuk.store') }}">
    @csrf
    @if(isset($kas_masuk))
        @method('PUT')
    @endif

    <div class="modal-body">
        <div class="mb-3">
            <label for="kode" class="form-label">Kode</label>
            <input type="text" name="kode" id="kode" class="form-control"
                   value="{{ old('kode', $kas_masuk->kode ?? $kodeOtomatis) }}" readonly>
        </div>

      <div class="mb-3">
            <label for="program_kerja_id" class="form-label">Program Kerja</label>
            <select name="program_kerja_id" id="program_kerja_id" class="form-select" required>
                <option value="">-- Pilih Program Kerja --</option>
                @foreach($programKerjaList as $p)
                    <option value="{{ $p->id }}"
                        {{ old('program_kerja_id', $kas_masuk->program_kerja_id ?? ($kas_masuk->programKerja->id ?? '')) == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_program }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="dokumen" class="form-label">Dokumen</label>
            <input type="text" name="dokumen" id="dokumen" class="form-control"
                   value="{{ old('dokumen', $kas_masuk->dokumen ?? '') }}">
        </div>
        <div class="mb-3">
            <label for="nama_akun" class="form-label">Terima Dari</label>
            <select name="nama_akun" id="nama_akun" class="form-select" required>
                <option value="">-- Pilih Akun --</option>
                @foreach($coaList as $c)
                    <option value="{{ $c->nama_akun }}"
                        {{ old('nama_akun', $kas_masuk->nama_akun ?? '') == $c->nama_akun ? 'selected' : '' }}>
                        {{ $c->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="ref_an" class="form-label">Ref AN</label>
            <input type="text" name="ref_an" id="ref_an" class="form-control"
                   value="{{ old('ref_an', $kas_masuk->ref_an ?? '') }}">
        </div>

        <div class="mb-3">
            <label for="rekning_kas" class="form-label">Rekening Kas</label>
             <select name="rekning_kas" id="rekning_kas" class="form-select" required>
                <option value="">-- Pilih Rekening --</option>
                @foreach($coaList as $c)
                    <option value="{{ $c->nama_akun }}"
                        {{ old('rekning_kas', $kas_masuk->nama_akun ?? '') == $c->nama_akun ? 'selected' : '' }}>
                        {{ $c->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="transaksi" class="form-label">Nama Transaksi</label>
            <select name="transaksi" id="transaksi" class="form-select" required>
                <option value="">-- Pilih Transaksi --</option>
                @foreach($coaList as $c)
                    <option value="{{ $c->nama_akun }}"
                        {{ old('transaksi', $kas_masuk->nama_akun ?? '') == $c->nama_akun ? 'selected' : '' }}>
                        {{ $c->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" name="nominal" id="nominal" class="form-control"
                   value="{{ old('nominal', $kas_masuk->nominal ?? '') }}" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="tgl" class="form-label">Tanggal</label>
            <input type="date" name="tgl" id="tgl" class="form-control"
                   value="{{ old('tgl', isset($kas_masuk) ? $kas_masuk->tgl->format('Y-m-d') : now()->format('Y-m-d')) }}" required>
        </div>
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control"
                   value="{{ old('keterangan', $kas_masuk->keterangan ?? '') }}" required>
        </div>
        
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ isset($kas_masuk) ? 'Update' : 'Simpan' }}</button>
    </div>
</form>