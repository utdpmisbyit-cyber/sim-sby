<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($petugas) ? 'Ubah' : 'Tambah' }} Petugas</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="kode" caption="Kode" :value="$petugas->kode ?? ''" required />
        <x-io-input name="nama" caption="Nama" :value="$petugas->nama ?? ''" required />

        <x-io-select name="cabang_id" caption="Cabang" :options="$cabang_options ?? []" :value="$petugas->cabang_id ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-select name="jabatan_id" caption="Jabatan" :options="$jabatan_options ?? []" :value="$petugas->jabatan_id ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-select name="bagian_id" caption="Bagian" :options="$bagian_options ?? []" :value="$petugas->bagian_id ?? ''" data-dropdown-parent="#modal_info" required />

        <x-io-select name="hak_akses_id" caption="Hak Akses" :options="$hak_akses_options ?? []" :value="$petugas->hakAkses->hak_akses_id ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-input type="password" name="password" caption="Password" placeholder="{{ !empty($petugas) ? 'Kosongkan jika tidak diubah' : '' }}"  />

        <x-io-input name="no_telp" caption="No. Telp" :value="$petugas->no_telp ?? ''"  />
        <x-io-input name="alamat_1" caption="Alamat 1" :value="$petugas->alamat_1 ?? ''" />
        <x-io-input name="alamat_2" caption="Alamat 2" :value="$petugas->alamat_2 ?? ''" />
        <x-io-input name="kode_pos" caption="Kode Pos" :value="$petugas->kode_pos ?? ''" />
        <x-io-input name="file_tanda_tangan" type="file" caption="Tanda Tangan" :value="$petugas->tanda_tangan ?? ''" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $petugas->id ?? '' }});
</script>
