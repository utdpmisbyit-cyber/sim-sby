<form method="POST"
action="{{ isset($pinjam_barang) 
    ? route('inventory.pinjam_barang.update', $pinjam_barang->id) 
    : route('inventory.pinjam_barang.store') }}">
@csrf

@if(isset($pinjam_barang))
    <input type="hidden" name="_method" value="PUT">
@endif

<div class="modal-header">
    <h3 class="modal-title">
        {{ isset($pinjam_barang) ? 'Ubah' : 'Tambah' }} Pinjam Barang
    </h3>
</div>

<div class="modal-body">
<div class="row">

    {{-- KODE --}}
    <div class="col-md-6 mb-3">
        <label>Kode</label>
        <input type="text" name="kode" class="form-control"
        value="{{ $pinjam_barang->kode ?? $kode_otomatis }}" readonly>
    </div>

    {{-- TANGGAL PEMAKAIAN (controller pakai tgl_pemakaian) --}}
    <div class="col-md-6 mb-3">
        <label>Tanggal Pinjam <span class="text-danger">*</span></label>
        <input type="date" name="tgl_pemakaian" class="form-control"
        value="{{ $pinjam_barang->tgl_pemakaian ?? date('Y-m-d') }}" required>
    </div>

    {{-- BAGIAN --}}
    <div class="col-md-6 mb-3">
        <label>Bagian <span class="text-danger">*</span></label>
        <select name="bagian_id" id="bagian_id" class="form-control" required>
            <option value="">-- Pilih Bagian --</option>
            @foreach($bagian as $b)
                <option value="{{ $b->id }}"
                    data-nama="{{ $b->nama }}"
                    {{ isset($pinjam_barang) && $pinjam_barang->bagian_id == $b->id ? 'selected' : '' }}>
                    {{ $b->kode }} - {{ $b->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- NAMA BAGIAN (auto) --}}
    <div class="col-md-6 mb-3">
        <label>Nama Bagian</label>
        <input type="text" id="nama_bagian" class="form-control" readonly>
    </div>

    {{-- BARANG --}}
    <div class="col-md-6 mb-3">
        <label>Barang <span class="text-danger">*</span></label>
        <select name="barang_id" id="barang_id" class="form-control" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($barang as $br)
                <option value="{{ $br->id }}"
                    data-nama="{{ $br->nama }}"
                    {{ isset($pinjam_barang) && $pinjam_barang->barang_id == $br->id ? 'selected' : '' }}>
                    {{ $br->kode }} - {{ $br->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- HIDDEN NAMA BARANG untuk controller --}}
    <input type="hidden" name="nama_barang" id="nama_barang"
    value="{{ $pinjam_barang->nama_barang ?? '' }}">

    {{-- PETUGAS --}}
    <div class="col-md-6 mb-3">
        <label>Petugas <span class="text-danger">*</span></label>
        <select name="petugas_id" class="form-control" required>
            <option value="">-- Pilih Petugas --</option>
            @foreach($petugas as $pt)
                <option value="{{ $pt->id }}"
                    {{ isset($pinjam_barang) && $pinjam_barang->petugas_id == $pt->id ? 'selected' : '' }}>
                    {{ $pt->nama }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- JUMLAH PAKAI (controller pakai jumlah_pakai) --}}
    <div class="col-md-6 mb-3">
        <label>Jumlah Pinjam <span class="text-danger">*</span></label>
        <input type="number" name="jumlah_pinjam" class="form-control"
        value="{{ $pinjam_barang->jumlah_pinjam ?? 1 }}" required>
    </div>

    {{-- DISERAHKAN KE --}}
    <div class="col-md-6 mb-3">
        <label>Diserahkan Ke</label>
        <input type="text" name="diserahkan_ke" class="form-control"
        value="{{ $pinjam_barang->diserahkan_ke ?? '' }}">
    </div>

    {{-- STATUS --}}
    <div class="col-md-6 mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="dipinjam" {{ isset($pinjam_barang) && $pinjam_barang->status == 'dipinjam' ? 'selected' : '' }}>
                Dipinjam
            </option>
            <option value="dikembalikan" {{ isset($pinjam_barang) && $pinjam_barang->status == 'dikembalikan' ? 'selected' : '' }}>
                Dikembalikan
            </option>
        </select>
    </div>

    {{-- KETERANGAN --}}
    <div class="col-md-12 mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control">{{ $pinjam_barang->keterangan ?? '' }}</textarea>
    </div>

</div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>

</form>

<script>
$(document).ready(function(){

    function setNamaBarang(){
        let selected = $('#barang_id option:selected');
        $('#nama_barang').val(selected.data('nama') ?? '');
    }

    function setNamaBagian(){
        let selected = $('#bagian_id option:selected');
        $('#nama_bagian').val(selected.data('nama') ?? '');
    }

    $('#barang_id').on('change', setNamaBarang);
    $('#bagian_id').on('change', setNamaBagian);

    setNamaBarang();
    setNamaBagian();
});
</script>