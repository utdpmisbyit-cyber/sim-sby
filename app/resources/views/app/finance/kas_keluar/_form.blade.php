<form method="POST"
      action="{{ isset($kas_keluar) 
                  ? route('finance.kas_keluar.update', $kas_keluar->id) 
                  : route('finance.kas_keluar.store') }}">
    @csrf
    @if(isset($kas_keluar))
        @method('PUT')
    @endif

    <div class="modal-body">
        <!-- Kode -->
        <div class="mb-3">
            <label for="kode" class="form-label">Kode</label>
            <input type="text" name="kode" id="kode" class="form-control"
                   value="{{ old('kode', $kas_keluar->kode ?? $kodeOtomatis) }}" readonly>
        </div>

        <!-- Tanggal -->
        <div class="mb-3">
            <label for="tgl" class="form-label">Tanggal</label>
            <input type="date" name="tgl" id="tgl" class="form-control"
                   value="{{ old('tgl', $kas_keluar?->tgl ?? date('Y-m-d')) }}" required>
        </div>

        <!-- Program Kerja -->
        <div class="mb-3">
            <label for="program_kerja_id" class="form-label">Program Kerja</label>
            <select name="program_kerja_id" id="program_kerja_id" class="form-select" required>
                <option value="">-- Pilih Program Kerja --</option>
                @foreach($programKerjaList as $p)
                    <option value="{{ $p->id }}"
                        {{ old('program_kerja_id', $kas_keluar->program_kerja_id ?? '') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_program }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Dokumen -->
        <div class="mb-3">
            <label for="dokumen" class="form-label">Nomor Dokumen / Bukti</label>
            <input type="text" name="dokumen" id="dokumen" class="form-control"
                   value="{{ old('dokumen', $kas_keluar->dokumen ?? '') }}">
        </div>

        <!-- Ref AN -->
        <div class="mb-3">
            <label for="ref_an" class="form-label">Ref AN</label>
            <input type="text" name="ref_an" id="ref_an" class="form-control"
                   value="{{ old('ref_an', $kas_keluar->ref_an ?? '') }}">
        </div>

        <!-- Dibayar Ke (Rekanan) -->
        <div class="mb-3">
            <label for="dibayar_ke" class="form-label">Dibayar Ke</label>
            <select name="dibayar_ke" id="dibayar_ke" class="form-select" required>
                <option value="">-- Pilih Rekanan --</option>
                @foreach($rekananList as $r)
                    <option value="{{ $r->nama_rekanan }}"
                        {{ old('dibayar_ke', $kas_keluar->dibayar_ke ?? '') == $r->nama_rekanan ? 'selected' : '' }}>
                        {{ $r->nama_rekanan }} ({{ $r->kategori }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Rekening Kas -->
        <div class="mb-3">
            <label for="rekning_kas" class="form-label">Rekening Kas</label>
            <select name="rekning_kas" id="rekning_kas" class="form-select" required>
                <option value="">-- Pilih Akun --</option>
                @foreach($coaList as $c)
                    <option value="{{ $c->nama_akun }}"
                        {{ old('rekning_kas', $kas_keluar->rekning_kas ?? '') == $c->nama_akun ? 'selected' : '' }}>
                        {{ $c->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Nama Akun / Transaksi -->
        <div class="mb-3">
            <label for="nama_akun" class="form-label">Nama Transaksi</label>
            <select name="nama_akun" id="nama_akun" class="form-select" required>
                <option value="">-- Pilih Akun --</option>
                @foreach($coaList as $c)
                    <option value="{{ $c->nama_akun }}"
                        {{ old('nama_akun', $kas_keluar->nama_akun ?? '') == $c->nama_akun ? 'selected' : '' }}>
                        {{ $c->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Nominal -->
        <div class="mb-3">
            <label for="nominal" class="form-label">Nominal</label>
            <input type="number" name="nominal" id="nominal" class="form-control"
                   value="{{ old('nominal', $kas_keluar->nominal ?? '') }}" step="0.01" required>
        </div>

        <!-- Keterangan -->
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control"
                   value="{{ old('keterangan', $kas_keluar->keterangan ?? '') }}" required>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ isset($kas_keluar) ? 'Update' : 'Simpan' }}</button>
    </div>
</form>