<div class="modal-body">
    <div class="row mb-2">
        <div class="col-6">
            <strong>Kode</strong><br>
            {{ $general_ledge->kode ?? $kodeOtomatis }}
        </div>
        <div class="col-6">
            <strong>Tanggal</strong><br>
            {{ $general_ledge?->tgl ? \Carbon\Carbon::parse($general_ledge->tgl)->format('j F Y') : date('j F Y') }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <strong>Program Kerja</strong><br>
            {{ $general_ledge?->programKerja?->nama_program ?? '-' }}
        </div>
        <div class="col-6">
            <strong>COA Transaksi</strong><br>
            {{ $general_ledge?->coa?->nama_akun ?? '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <strong>Nominal Debit</strong><br>
            <span class="text-primary">
                {{ isset($general_ledge->nominal_debit) ? 'Rp ' . number_format($general_ledge->nominal_debit, 0, ',', '.') : '-' }}
            </span>
        </div>
        <div class="col-6">
            <strong>Nominal Kredit</strong><br>
            <span class="text-danger">
                {{ isset($general_ledge->nominal_kredit) ? 'Rp ' . number_format($general_ledge->nominal_kredit, 0, ',', '.') : '-' }}
            </span>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <strong>Rekening Kas</strong><br>
            {{ $general_ledge->rekening_kas ?? '-' }}
        </div>
        <div class="col-6">
            <strong>No Dokumen</strong><br>
            {{ $general_ledge->dokumen ?? '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <strong>Referensi</strong><br>
            {{ $general_ledge->ref_bayar ?? '-' }}
        </div>
        <div class="col-6">
            <strong>Saldo Awal</strong><br>
            {{ $general_ledge->saldo_awal ? 'Ya' : '-' }}
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-6">
            <strong>Dibayarkan Ke</strong><br>
            {{ $general_ledge->dibayarkan_ke ?? '-' }}
        </div>
        <div class="col-6">
            <strong>Lawan Transaksi</strong><br>
            {{ $general_ledge->lawan_transaksi ?? '-' }}
        </div>
    </div>

    <div class="mb-3">
        <strong>Keterangan</strong><br>
        <textarea class="form-control" rows="2" readonly>{{ $general_ledge->keterangan ?? '-' }}</textarea>
    </div>

    <div class="p-3 border rounded bg-light">
        <strong>Ringkasan Keuangan</strong>
        <div class="row text-center mt-2">
            <div class="col">
                <div>BS</div>
                <div>{{ isset($general_ledge->bs) ? 'Rp ' . number_format($general_ledge->bs, 0, ',', '.') : 'Rp 0' }}</div>
            </div>
            <div class="col">
                <div>PL</div>
                <div>{{ isset($general_ledge->pl) ? 'Rp ' . number_format($general_ledge->pl, 0, ',', '.') : 'Rp 0' }}</div>
            </div>
            <div class="col">
                <div>Inventory</div>
                <div>{{ $general_ledge->inventory ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>