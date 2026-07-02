<form method="POST"
      action="{{ isset($penyesuaian) 
                  ? route('finance.penyesuaian.update', $penyesuaian->id) 
                  : route('finance.penyesuaian.store') }}">
    @csrf
    @if(isset($penyesuaian))
        @method('PUT')
    @endif

    <div class="modal-body">
        <div class="mb-3">
            <label for="kode">Kode</label>
            <input type="text" name="kode" id="kode" class="form-control"
                   value="{{ old('kode', $penyesuaian->kode ?? $kodeOtomatis) }}" readonly>
        </div>

        <div class="mb-3">
            <label for="tgl">Tanggal</label>
            <input type="date" name="tgl" id="tgl" class="form-control"
                   value="{{ old('tgl', $penyesuaian?->tgl ?? date('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="program_kerja_id">Program Kerja</label>
            <select name="program_kerja_id" id="program_kerja_id" class="form-select" required>
                <option value="">-- Pilih Program Kerja --</option>
                @foreach($programKerjaList as $p)
                    <option value="{{ $p->id }}"
                        {{ old('program_kerja_id', $penyesuaian->program_kerja_id ?? '') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_program }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="dokumen">Dokumen</label>
            <input type="text" name="dokumen" id="dokumen" class="form-control"
                   value="{{ old('dokumen', $penyesuaian->dokumen ?? '') }}">
        </div>

        <div class="mb-3">
            <label for="ref_bayar">Ref Bayar</label>
            <input type="text" name="ref_bayar" id="ref_bayar" class="form-control"
                   value="{{ old('ref_bayar', $penyesuaian->ref_bayar ?? '') }}">
        </div>

        <div class="mb-3">
            <label for="transaksi_coa">Transaksi COA</label>
            <select name="transaksi_coa" id="transaksi_coa" class="form-select" required>
                <option value="">-- Pilih COA --</option>
                @foreach($coaList as $c)
                    <option value="{{ $c->nama_akun }}"
                        {{ old('transaksi_coa', $penyesuaian->transaksi_coa ?? '') == $c->nama_akun ? 'selected' : '' }}>
                        {{ $c->nama_akun }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nominal_debit">Nominal Debit</label>
            <input type="number" name="nominal_debit" id="nominal_debit" class="form-control"
                   value="{{ old('nominal_debit', $penyesuaian->nominal_debit ?? '') }}" step="0.01">
        </div>

        <div class="mb-3">
            <label for="nominal_kredit">Nominal Kredit</label>
            <input type="number" name="nominal_kredit" id="nominal_kredit" class="form-control"
                   value="{{ old('nominal_kredit', $penyesuaian->nominal_kredit ?? '') }}" step="0.01">
        </div>

        <div class="mb-3">
            <label for="jenis_saldo">Jenis Saldo</label>
            <select name="jenis_saldo" id="jenis_saldo" class="form-select" required>
                <option value="">-- Pilih Jenis Saldo --</option>
                <option value="saldo_awal" {{ old('jenis_saldo', $penyesuaian->jenis_saldo ?? '') == 'saldo_awal' ? 'selected' : '' }}>Saldo Awal</option>
                <option value="lainnya" {{ old('jenis_saldo', $penyesuaian->jenis_saldo ?? '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
        </div>

        

        <div class="mb-3">
            <label for="keterangan">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control"
                   value="{{ old('keterangan', $penyesuaian->keterangan ?? '') }}" required>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">{{ isset($penyesuaian) ? 'Update' : 'Simpan' }}</button>
    </div>
</form>