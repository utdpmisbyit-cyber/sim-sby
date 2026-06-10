<form method="POST"
action="{{ isset($opname_barang) 
    ? route('inventory.opname_barang.update', $opname_barang->no_opname) 
    : route('inventory.opname_barang.store') }}">

@csrf

@if(isset($opname_barang))
    <input type="hidden" name="_method" value="PUT">
@endif

<div class="modal-header">
    <h3 class="modal-title">
        {{ isset($opname_barang) ? 'Ubah' : 'Tambah' }} Opname Barang
    </h3>
</div>

<div class="modal-body">
<div class="row">

    {{-- NO OPNAME --}}
    <div class="col-md-6 mb-3">
        <label>No Opname</label>
        <input type="text" class="form-control" readonly
        value="{{ $opname_barang->no_opname ?? $kode_otomatis }}">
    </div>

    {{-- TANGGAL --}}
    <div class="col-md-6 mb-3">
        <label>Tanggal Opname <span class="text-danger">*</span></label>
        <input type="date" name="tgl_opname" class="form-control"
        value="{{ $opname_barang->tgl_opname ?? date('Y-m-d') }}" required>
    </div>

    {{-- BARANG --}}
    <div class="col-md-6 mb-3">
        <label>Barang <span class="text-danger">*</span></label>
        <select name="barang_id" id="barang_id" class="form-control" required>
            <option value="">-- Pilih Barang --</option>
          @foreach($barang as $br)
                <option value="{{ $br->id }}"
                    data-nama="{{ $br->nama }}"
                    data-satuan="{{ $br->satuan }}"
                    data-stok="{{ $br->stok_akhir  ?? 0}}"
                    {{ isset($opname_barang) && $opname_barang->barang_id == $br->id ? 'selected' : '' }}>
                    {{ $br->kode }} - {{ $br->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <input type="hidden" name="nama_barang" id="nama_barang"
    value="{{ $opname_barang->nama_barang ?? '' }}">
    <input type="hidden" name="status" value="opname selesai">

    <div class="col-md-6 mb-3">
        <label>Satuan</label>
        <input type="text" class="form-control" name="satuan" id="satuan"
        value="{{ $opname_barang->satuan ?? '' }}" readonly>
    </div>

    {{-- QTY SISTEM otomatis dari stok --}}
        <div class="col-md-6 mb-3">
            <label>Qty Sistem</label>
            <input type="number" name="qty_sistem" id="qty_sistem"
                class="form-control" readonly
                value="{{ $opname_barang->qty_sistem ?? '' }}">
        </div>

    <div class="col-md-6 mb-3">
        <label>Qty Fisik</label>
        <input type="number" name="qty_fisik" id="qty_fisik"
        class="form-control"
        value="{{ $opname_barang->qty_fisik ?? 0 }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label>Selisih</label>
        <input type="text" id="selisih" class="form-control" readonly
        value="{{ isset($opname_barang) ? $opname_barang->selisih : 0 }}">
    </div>

    {{-- BAGIAN --}}
    <div class="col-md-6 mb-3">
        <label>Lokasi Stok <span class="text-danger">*</span></label>
        <select name="lokasi" id="lokasi" class="form-control" required>
            <option value="">-- Pilih Lokasi --</option>
            @foreach($bagian as $b)
                <option value="{{ $b->id }}"
                    data-nama="{{ $b->nama }}"
                    {{ isset($opname_barang) && $opname_barang->bagian_id == $b->id ? 'selected' : '' }}>
                    {{ $b->kode }} - {{ $b->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label>Nama Bagian</label>
        <input type="text" class="form-control" id="nama_bagian" readonly>
    </div>

    {{-- PETUGAS --}}
    <div class="col-md-6 mb-3">
        <label>Petugas <span class="text-danger">*</span></label>
        <select name="petugas_id" class="form-control" required>
            <option value="">-- Pilih Petugas --</option>
            @foreach($petugas as $pt)
                <option value="{{ $pt->id }}"
                    {{ isset($opname_barang) && $opname_barang->petugas_id == $pt->id ? 'selected' : '' }}>
                    {{ $pt->nama }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-12 mb-3">
        <label>Keterangan</label>
        <textarea name="keterangan" class="form-control">{{ $opname_barang->keterangan ?? '' }}</textarea>
    </div>

</div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Simpan</button>
</div>

</form>
<script>
$(document).ready(function(){

    function setBarang(){
        let sel = $('#barang_id option:selected');

        // isi nama & satuan
        $('#nama_barang').val(sel.data('nama') || '');
        $('#satuan').val(sel.data('satuan') || '');

        // ambil stok akhir
        let stok = Number(sel.data('stok')) || 0;

        // jika create / saat ganti barang → update qty sistem
        $('#qty_sistem').val(stok);

        hitungSelisih();
    }

    function setBagian(){
        let sel = $('#lokasi option:selected');
        $('#nama_bagian').val(sel.data('nama') || '');
    }

    function hitungSelisih(){
        let s = parseFloat($('#qty_sistem').val()) || 0;
        let f = parseFloat($('#qty_fisik').val()) || 0;
        $('#selisih').val(f - s);
    }

    // Event listener
    $('#barang_id').on('change', setBarang);
    $('#lokasi').on('change', setBagian);
    $('#qty_fisik').on('input', hitungSelisih);

    // Load data saat edit
    setBarang();
    setBagian();
    hitungSelisih();
});
</script>