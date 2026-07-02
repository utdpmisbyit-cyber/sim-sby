<form id="form_info">
    @csrf
    <div class="modal-header">
        <h3 class="modal-title">{{ !empty($user) ? 'Ubah' : 'Tambah' }} User</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>
    <div class="modal-body">
        <x-io-select name="role" caption="Akses" :options="array_combine($roles, $roles)" :value="$user->role ?? ''" data-dropdown-parent="#modal_info" required />
        <x-io-input name="name" caption="Nama" :value="$user->name ?? ''" required />
        <x-io-input name="email" caption="Username" :value="$user->email ?? ''" required />
        <x-io-input type="password" name="password" caption="Password" placeholder="{{ !empty($user) ? 'Leave blank if no change' : '' }}" />
        <x-io-input type="password" name="password_confirmation" caption="Repeat Password" placeholder="{{ !empty($user) ? 'Leave blank if no change' : '' }}" />
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary me-6" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
    init_form_element();
    init_form({{ $user->id ?? '' }});
</script>
