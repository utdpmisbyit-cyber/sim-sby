<form id="form_info">
@csrf

<div class="modal-header">
    <h3 class="modal-title">
        {{ !empty($permintaan) ? 'Ubah' : 'Proses' }} Verifikasi Permintaan Barang Logistik
    </h3>
    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
        <i class="ki-duotone ki-cross fs-1"></i>
    </div>
</div>

<div class="modal-body">
<div class="row g-4">

    {{-- KODE --}}
    <div class="col-md-6">
        <x-io-input
            name="kode"
            caption="Kode Permintaan"
            :value="$permintaan->kode ?? ('PBL-'.date('Ymd').'-'.str_pad(rand(1,999),3,'0',STR_PAD_LEFT))"
            :viewtype="2"
            readonly
        />
    </div>

    {{-- PENGAJUAN BARANG (select2 ajax: find-pengajuan) --}}
    <div class="col-md-6">
        <x-io-select
            name="pengajuan_barang_id"
            caption="Pengajuan Barang (Cabang)"
            :options="$pengajuan_options ?? []"
            :value="$permintaan->pengajuan_barang_id ?? ''"
            class="form-select"
            required
            :disabled="!empty($permintaan)"
        />
    </div>

    {{-- TANGGAL TERIMA --}}
    <div class="col-md-6">
        <x-io-input
            type="date"
            name="tgl_terima"
            caption="Tanggal Terima Permintaan"
            :value="$permintaan->tgl_terima ?? date('Y-m-d')"
            :viewtype="2"
            required
        />
    </div>

    {{-- JUMLAH ACC --}}
    <div class="col-md-6">
        <x-io-input
            type="number"
            name="jml_acc"
            caption="Jumlah Disetujui"
            :value="$permintaan->jml_acc ?? ''"
            :viewtype="2"
            min="0"
            required
        />
    </div>

    {{-- PETUGAS GUDANG --}}
    <div class="col-md-6">
        <x-io-select
            name="petugas_gudang_id"
            caption="Petugas Gudang"
            :options="$petugas_options ?? []"
            :value="$permintaan->petugas_gudang_id ?? ''"
            class="form-select"
            required
        />
    </div>

    {{-- STATUS --}}
    <div class="col-md-6">
        <x-io-select
            name="status"
            caption="Status"
            :options="[
                'diterima' => 'Diterima',
                'diproses' => 'Diproses',
                'dikirim'  => 'Dikirim',
                'selesai'  => 'Selesai',
                'ditolak'  => 'Ditolak',
            ]"
            :value="$permintaan->status ?? 'diterima'"
            class="form-select"
            required
        />
    </div>

    {{-- CATATAN --}}
    <div class="col-md-12">
        <x-io-input
            name="catatan"
            caption="Catatan"
            :value="$permintaan->catatan ?? ''"
            type="textarea"
        />
    </div>

</div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light me-3" onclick="init()">Batal</button>
    <button type="submit" class="btn btn-primary">
        <i class="ki-duotone ki-check fs-2"></i> Simpan
    </button>
</div>

</form>

<script>
    init_form_element();
    init_form(@json($permintaan->id ?? ''));
    // jika menggunakan select2 ajax sendiri, init khusus dropdown pengajuan_barang_id di sini
</script>