@extends('layouts.index')

@section('title')
Inventory - Pengeluaran Barang
@endsection

@section('content')
<div class="content flex-column-fluid" id="kt_content">
    <div class="card card-flush rounded-4 border-0 h-100 shadow-xs">
        <div class="card-body">
            <div class="d-flex flex-row justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <h1 class="d-flex align-items-center my-1">
                        <span class="text-dark fw-bold fs-1">Pengeluaran Barang</span>
                    </h1>
                    @include('layouts._breadcrumb')
                </div>
                <!-- <button type="button" onclick="info()" class="btn btn-sm btn-primary fw-bold border-0 fs-7">Tambah Permintaan</button> -->
            </div>

            <form id="form_search" class="w-100 mt-4 bg-gray-200 border-solid border-2 border-gray-300 rounded-2 px-5 py-4">
                @csrf
                <div class="d-flex flex-lg-row flex-column align-items-lg-center justify-content-lg-between gap-4">
                    <div class="d-flex flex-row gap-6 flex-grow-1">
                        <x-input name="nama" prefix="search_" caption="Cari Nama" />
                        <x-input name="kode" prefix="search_" caption="Cari Kode" />
                    </div>
                    <button type="submit" class="btn btn-sm btn-success fw-bold border-0 fs-7 btn-flex gap-4 pe-5 justify-content-between">
                        Cari <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
         
            <div id="table" class="mt-4"></div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade modal-slide-right" tabindex="-1" id="modal_info">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="modal_info_item"></div>
    </div>
</div>
@endpush

@push('scripts')
<script>
let $form_search = $('#form_search'), $table = $('#table'), $modal_info = $('#modal_info'), $modal_info_item = $('#modal_info_item');
let selected_page = 1, _token = '{{ csrf_token() }}', base_url = '{{ route('inventory.pengeluaran_barang.index') }}', params_url = '{{ $params ?? '' }}';

// Init list
let init = () => { $modal_info_item.html(''); try { $modal_info.modal('hide'); } catch (e) { } search_data(selected_page); }

// Search / load data
let search_data = (page = 1) => {
    let data = get_form_data($form_search);
    data.paginate = 10;
    data.page = selected_page = get_selected_page(page, selected_page);
    $.post(base_url + '/search?' + params_url, data, (result) => $table.html(result)).fail((xhr) => $table.html(xhr.responseText));
}

// Show modal
let display_modal_info = (item) => { $modal_info_item.html(item); $modal_info.modal('show'); }
let info = (id = '') => { $.get(base_url + '/' + (id === '' ? 'create' : (id + '/edit')) + '?' + params_url, (result) => display_modal_info(result)).fail((xhr) => display_modal_info(xhr.responseText)); }

// Delete / restore
let confirm_delete = (id) => {
    Swal.fire(swal_delete_params).then((result) => {
        if (result.isConfirmed) $.post(base_url + '/' + id, {_method: 'delete', _token}, (data) => {
            if (data.error) swal.fire({icon: 'error', title: data.error}).then(() => init());
            else swal.fire('Berhasil Dihapus').then(() => init());
        }).fail((xhr) => $table.html(xhr.responseText));
    });
}

let confirm_restore = (id) => {
    Swal.fire(swal_restore_params).then((result) => {
        if (result.isConfirmed) $.post(base_url + '/' + id + '/restore', {_method: 'put', _token}, () => swal.fire('Berhasil Dikembalikan').then(() => init())).fail((xhr) => $table.html(xhr.responseText));
    });
}

// Form submit
let init_form = (id = '') => {
    let $form_info = $('#form_info');
    $form_info.submit((e) => {
        e.preventDefault();
        let url = base_url;
        let data = new FormData($form_info.get(0));
        if (id !== '') url += '/' + id + '?_method=put';
        $.ajax({ url, type: 'post', data, cache: false, processData: false, contentType: false, success: () => init() }).fail((xhr) => error_handle(xhr.responseText));
    });
}

$form_search.submit((e) => { e.preventDefault(); search_data(); });

init_form_element();
init();
</script>
@endpush