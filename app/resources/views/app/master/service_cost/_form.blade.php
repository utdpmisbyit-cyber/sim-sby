<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($service_cost) ? 'Ubah' : 'Tambah' }} Service Cost</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="nama" caption="Nama" :value="$service_cost->nama ?? ''" required />
        <x-io-select name="jenis" caption="Jenis" :options="$list_jenis" :value="$service_cost->jenis ?? ''" data-dropdow-parent="#modal_info" required />
        <x-io-select name="service_cost_id" caption="Jenis Biaya" :options="$list_jenis_biaya" :value="$service_cost->service_cost_id ?? ''" data-dropdow-parent="#modal_info" required />
        <x-io-select name="kelompok_biaya_id" caption="Kelompok Biaya" :options="$list_kelompok_biaya" :value="$service_cost->kelompok_biaya_id ?? ''" data-dropdow-parent="#modal_info" required />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $service_cost->id ?? '' }});
</script>
