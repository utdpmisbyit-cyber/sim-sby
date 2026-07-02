<form method="POST"
action="{{ isset($retur_pinjam) 
    ? route('inventory.retur_pinjam.update', $retur_pinjam->id) 
    : route('inventory.retur_pinjam.store') }}">

@csrf
@if(isset($retur_pinjam))
    <input type="hidden" name="_method" value="PUT">
@endif

<div class="modal-header">
    <h3 class="modal-title">
        {{ isset($retur_pinjam) ? 'Ubah' : 'Tambah' }} Retur Pinjam
    </h3>
</div>

<div class="modal-body">
<div class="row">

    {{-- KODE --}}
    <div class="col-md-6 mb-3">
        <label>Kode</label>
        <input type="text" name="kode" class="form-control"
        value="{{ $retur_pinjam->kode ?? $kode_otomatis }}" readonly>
    </div>

    {{-- PINJAM BARANG --}}
    <div class="col-md-6 mb-3">
        <label>No Trans Pinjam <span class="text-danger">*</span></label>
        <select name="pinjam_barang_id" class="form-control" required>
            <option value="">-- Pilih Peminjaman --</option>
            @foreach($pinjam as $p)
                <option value="{{ $p->id }}"
                    {{ isset($retur_pinjam) && $retur_pinjam->pinjam_barang_id == $p->id ? 'selected' : '' }}>
                    {{ $p->kode }} - {{ $p->barang->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BARANG --}}
    <div class="col-md-6 mb-3">
        <label>Barang <span class="text-danger">*</span></label>
        <select name="barang_id" class="form-control" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($barang as $br)
                <option value="{{ $br->id }}"
                    {{ isset($retur_pinjam) && $retur_pinjam->barang_id == $br->id ? 'selected' : '' }}>
                    {{ $br->kode }} - {{ $br->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PETUGAS --}}
    <div class="col-md-6 mb-3">
        <label>Petugas <span class="text-danger">*</span></label>
        <select name="petugas_id" class="form-control" required>
            @foreach($petugas as $pt)
                <option value="{{ $pt->id }}"
                    {{ isset($retur_pinjam) && $retur_pinjam->petugas_id == $pt->id ? 'selected' : '' }}>
                    {{ $pt->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BAGIAN --}}
    <div class="col-md-6 mb-3">
        <label>Bagian <span class="text-danger">*</span></label>
        <select name="bagian_petugas_id" class="form-control" required>
            @foreach($bagian as $b)
                <option value="{{ $b->id }}"
                    {{ isset($retur_pinjam) && $retur_pinjam->bagian_petugas_id == $b->id ? 'selected' : '' }}>
                    {{ $b->kode }} - {{ $b->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- JUMLAH RETUR --}}
    <div class="col-md-6 mb-3">
        <label>Jumlah Retur <span class="text-danger">*</span></label>
        <input type="number" min="1" name="jumlah_retur" class="form-control"
        value="{{ $retur_pinjam->jumlah_retur ?? 1 }}" required>
    </div>

    {{-- TANGGAL RETUR --}}
    <div class="col-md-6 mb-3">
        <label>Tanggal Retur <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_retur" class="form-control"
        value="{{ $retur_pinjam->tanggal_retur ?? date('Y-m-d') }}" required>
    </div>

    {{-- KONDISI --}}
    <div class="col-md-12 mb-3">
        <label>Kondisi Barang</label>
        <textarea name="kondisi_barang" class="form-control">{{ $retur_pinjam->kondisi_barang ?? '' }}</textarea>
    </div>
</div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>

</form>