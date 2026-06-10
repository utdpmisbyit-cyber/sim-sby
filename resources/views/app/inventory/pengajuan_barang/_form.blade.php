<form id="form_info">
@csrf 

<div class="modal-header">
    <h3 class="modal-title">
        {{ !empty($pengajuan) ? 'Ubah' : 'Tambah' }} Permintaan Barang
    </h3>

    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
         data-bs-dismiss="modal">
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
:value="$pengajuan->kode ?? ('REQ-'.date('Ymd').'-'.str_pad(rand(1,999),3,'0',STR_PAD_LEFT))"
:viewtype="2"
readonly
/>
</div>


{{-- TANGGAL --}}
<div class="col-md-6">
<x-io-input
type="date"
name="tgl_pengajuan"
caption="Tanggal Permintaan"
:value="$pengajuan->tgl_pengajuan ?? date('Y-m-d')"
:viewtype="2"
required
/>
</div>


{{-- JENIS PERMINTAAN --}}
<div class="col-md-6">
<x-io-select
name="jenis_pengajuan"
caption="Jenis Permintaan"
:options="[
'medis' => 'Barang Medis',
'perbaikan' => 'Perbaikan',
'pembelian' => 'Pembelian',
'non_medis' => 'Barang Non Medis'
]"
:value="$pengajuan->jenis_pengajuan ?? ''"
class="form-select"
required
/>
</div>


{{-- CABANG --}}
<div class="col-md-6">
<x-io-select
name="cabang_id"
caption="Cabang"
:options="$cabang_options ?? []"
:value="$pengajuan->cabang_id ?? ''"
class="form-select"
required
/>
</div>


{{-- BARANG --}}
<div class="col-md-6">
<x-io-select
name="barang_id"
caption="Barang"
:options="$barang_options ?? []"
:value="$pengajuan->barang_id ?? ''"
class="form-select"
required
/>
</div>


{{-- JUMLAH PERMINTAAN --}}
<div class="col-md-6">
<x-io-input
type="number"
name="jml_minta"
caption="Jumlah Permintaan"
:value="$pengajuan->jml_minta ?? 1"
:viewtype="2"
min="1"
required
/>
</div>


{{-- PETUGAS --}}
<div class="col-md-6">
<x-io-input
caption="Petugas"
:value="auth()->user()->name ?? 'System'"
:viewtype="2"
readonly
/>
</div>
{{-- BAGIAN PETUGAS AMBIL --}}
<div class="col-md-6">
<x-io-select
name="bagian_id"
caption="Bagian (Petugas Ambil)"
:options="$bagian_options ?? []"
:value="$pengajuan->bagian_id ?? ''"
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
0 => 'Draft',
1 => 'Proses',
2 => 'Selesai',
3 => 'Batal'
]"
:value="$pengajuan->status ?? 0"
class="form-select"
/>
</div>

</div>
</div>


<div class="modal-footer">
<button type="button"
class="btn btn-light me-3"
onclick="init()">
Batal
</button>

<button type="submit"
class="btn btn-primary">
<i class="ki-duotone ki-check fs-2"></i>
Simpan
</button>
</div>

</form>


<script>
init_form_element();
init_form(@json($pengajuan->id ?? ''));
</script>