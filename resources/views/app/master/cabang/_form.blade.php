<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($cabang) ? 'Ubah' : 'Tambah' }} Cabang</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="kode" caption="Kode" :value="$cabang->kode ?? ''" required />
        <x-io-input name="nama" caption="Nama" :value="$cabang->nama ?? ''" required />
        <x-io-input name="alamat_1" caption="Alamat 1" :value="$cabang->alamat_1 ?? ''" />
        <x-io-input name="alamat_2" caption="Alamat 2" :value="$cabang->alamat_2 ?? ''" />
        <x-io-input name="kode_pos" caption="Kode Pos" :value="$cabang->kode_pos ?? ''" />
        <x-io-input name="no_telp" caption="No. Telp" :value="$cabang->no_telp ?? ''" />
        <x-io-select name="status" caption="Status" :options="[1 => 'Aktif', 0 => 'Non-Aktif']" :value="$cabang->status ?? 1" data-dropdown-parent="#modal_info" required />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $cabang->id ?? '' }});
</script>
