<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($program_kerja) ? 'Ubah' : 'Tambah' }} Program Kerja</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="kode" caption="Kode" :value="$program_kerja->kode ?? ''" required />
        <x-io-input name="nama_program" caption="Nama Program" :value="$program_kerja->nama_program ?? ''" required />
        <x-io-textarea name="keterangan" caption="Keterangan" :value="$program_kerja->keterangan ?? ''" />
        <x-io-select name="pic_id" caption="PIC (Penanggung Jawab)" placeholder="Kosong" :options="$petugas_options ?? []" :value="$program_kerja->pic_id ?? ''" data-dropdown-parent="#modal_info" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $program_kerja->id ?? '' }});
</script>
