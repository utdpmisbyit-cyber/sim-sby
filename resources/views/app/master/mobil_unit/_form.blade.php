<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($mobil_unit) ? 'Ubah' : 'Tambah' }} Mobil Unit</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="merk_mobil" caption="Merk Mobil" :value="$mobil_unit->merk_mobil ?? ''" required />
        <x-io-input name="no_polisi" caption="No.Polisi" :value="$mobil_unit->no_polisi ?? ''" required />
        <x-io-input name="tahun_produksi" caption="Tahun Produksi" :value="$mobil_unit->tahun_produksi ?? ''" />
        <x-io-input name="tahun_beli" caption="Tahun Beli" :value="$mobil_unit->tahun_beli ?? ''" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $mobil_unit->id ?? '' }});
</script>
