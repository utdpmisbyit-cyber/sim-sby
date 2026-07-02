<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($hak_akses) ? 'Ubah' : 'Tambah' }} Hak Akses</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-input name="kode" caption="Kode" :value="$hak_akses->kode ?? ''" required />
        <x-io-input name="nama" caption="Nama" :value="$hak_akses->nama ?? ''" required />
        <div class="row mb-4">
            <label class="col-lg-3 col-form-label fw-bold fs-7 pt-0">List Akses</label>
            <div class="col-lg-9 d-flex flex-column gap-3">
                @php($description = json_decode($hak_akses->description ?? '[]', true))
                @foreach($option_modules as $key => $module)
                    <x-checkbox name="module_{{ $key }}" :caption="$module['caption']" :checked="empty($hak_akses) ? true : in_array($key, array_keys($description))" />
                    <div class="d-flex flex-column gap-2 ps-10">
                        @foreach($module['menus'] as $key2 => $menu)
                            <x-checkbox name="menu_{{ $key2 }}" :caption="$menu['caption']" :checked="empty($hak_akses) ? true : in_array($key2, ($description[$key] ?? []))" />
                            <div class="ps-6">
                                @foreach(($menu['sub_menus'] ?? []) as $key3 => $sub_menu)
                                    <x-checkbox name="sub_menu_{{ $key3 }}" :caption="$sub_menu['caption']" :checked="empty($hak_akses) ? true : in_array($key3, ($description[$key] ?? []))" />
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $hak_akses->id ?? '' }});
</script>
