<form method="POST"
action="{{ isset($pemakaian_barang) 
    ? route('inventory.pemakaian_barang.update', $pemakaian_barang->id) 
    : route('inventory.pemakaian_barang.store') }}">
@csrf

@if(isset($pemakaian_barang))
<input type="hidden" name="_method" value="PUT">
@endif

<div class="modal-header">
    <h3 class="modal-title">
        {{ isset($pemakaian_barang) ? 'Ubah' : 'Tambah' }} Pemakaian Barang
    </h3>
</div>

<div class="modal-body">
<div class="row">

    {{-- KODE --}}
    <div class="col-md-6 mb-3">
        <label>Kode Pemakaian</label>
        <input type="text" name="kode" class="form-control"
        value="{{ $pemakaian_barang->kode ?? $kode_otomatis ?? '' }}" readonly>
    </div>

    {{-- TANGGAL --}}
    <div class="col-md-6 mb-3">
        <label>Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="tgl_pemakaian" class="form-control"
        value="{{ $pemakaian_barang->tgl_pemakaian ?? date('Y-m-d') }}" required>
    </div>

    {{-- KODE BAGIAN --}}
    <div class="col-md-6 mb-3">
        <label>Kode Bagian <span class="text-danger">*</span></label>
      {{-- BAGIAN --}}
        <select name="bagian_id" id="bagian_id" class="form-control" required>
            <option value="">-- Pilih Bagian --</option>
            @foreach($bagian as $b)
                <option value="{{ $b->id }}"
                    data-nama="{{ $b->nama }}"
                    {{ isset($pemakaian_barang) && $pemakaian_barang->bagian_id == $b->id ? 'selected' : '' }}>
                    {{ $b->kode }} -{{ $b->nama }} 
                </option>
            @endforeach
        </select>
    </div>

    {{-- NAMA BAGIAN --}}
    <div class="col-md-6 mb-3">
        <label>Nama Bagian</label>
        <input type="text" id="nama_bagian" class="form-control" readonly>
    </div>

    {{-- KODE BARANG --}}
    <div class="col-md-6 mb-3">
        <label>Kode Barang <span class="text-danger">*</span></label>
        {{-- BARANG --}}
        <select name="barang_id" id="barang_id" class="form-control" required>
            <option value="">-- Pilih Barang --</option>
            @foreach($barang as $br)
                <option value="{{ $br->id }}"
                    data-nama="{{ $br->nama }}"
                    {{ isset($pemakaian_barang) && $pemakaian_barang->barang_id == $br->id ? 'selected' : '' }}>
                    {{ $br->kode }} - {{ $br->nama }}
                </option>   
            @endforeach
        </select>
    </div>

    {{-- NAMA BARANG --}}
    <div class="col-md-6 mb-3">
        <label>Nama Barang</label>
        <input type="text" name="nama_barang" id="nama_barang" class="form-control"
        value="{{ $pemakaian_barang->nama_barang ?? '' }}" readonly>
    </div>
    <div class="col-md-6 mb-3">
     <label>Pengajuan Barang</label>
       <select name="pengajuan_barang_id" class="form-control" required>
    <option value="">-- Pilih Pengajuan --</option>
    @foreach($pengajuan ?? [] as $p)
        <option value="{{ $p->id }}"
            {{ isset($pemakaian_barang) && $pemakaian_barang->pengajuan_barang_id == $p->id ? 'selected' : '' }}>
            {{ $p->kode }}
        </option>
    @endforeach
</select>
    </div>
    {{-- JUMLAH --}}
    <div class="col-md-6 mb-3">
        <label>Jumlah Pakai <span class="text-danger">*</span></label>
        <input type="number" name="jumlah_pakai" class="form-control"
        value="{{ $pemakaian_barang->jumlah_pakai ?? 0 }}" required>
    </div>

    {{-- KETERANGAN --}}
    <div class="col-md-12 mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control"
        placeholder="Masukkan keterangan (opsional)">{{ $pemakaian_barang->keterangan ?? '' }}</textarea>
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
        let nama = selected.data('nama');
        $('#nama_barang').val(nama ? nama : '');
    }

    function setNamaBagian(){
        let selected = $('#bagian_id option:selected');
        let nama = selected.data('nama');
        $('#nama_bagian').val(nama ? nama : '');
    }

    $('#barang_id').on('change', setNamaBarang);
    $('#bagian_id').on('change', setNamaBagian);

    // 🔥 WAJIB untuk edit mode
    setNamaBarang();
    setNamaBagian();
});

</script>