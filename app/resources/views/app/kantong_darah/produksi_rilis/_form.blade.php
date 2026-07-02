<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">Rilis Produksi Baru</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">
            <div class="col-12">
                <div class="card card-flush border">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Informasi Rencana Produksi</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="my-4">
                            <x-io-select :viewtype="2" name="rencana_produksi_id" caption="Pilih Rencana Produksi" :options="$rencana_produksi_options" value="" data-dropdown-parent="#modal_info" required />
                            <div class="form-text text-muted fs-7 mt-2">
                                <i class="fa fa-info-circle me-1 text-primary"></i> 
                                Semua detail kantong dari Rencana Produksi yang dipilih akan otomatis dirilis ke Produksi Darah dengan status <strong>SENDING</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer py-3 px-6">
        <button type="button" class="btn btn-sm btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Rilis Sekarang</button>
    </div>
</form>

<script>
    init_form_element();
    init_form();
</script>
