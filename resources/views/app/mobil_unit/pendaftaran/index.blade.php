@php($active_cabang = session('active_cabang', []))

@extends('layouts.index')

@section('title')
    Pendaftaran Mobile unit- Unit Transfusi Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
            <div class="card-body py-5">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1">
                            <span class="text-dark fw-bold fs-1">Pendaftaran Mobil Unit</span>
                        </h1>
                        @include('layouts._breadcrumb')
                    </div>
                    <div class="d-flex gap-3">
                        <span class="badge badge-light-primary rounded-3 fs-7 text-dark fw-semibold px-4 py-3">
                            <i class="ki-duotone ki-calendar fs-5 me-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            {{ fulldate(date('Y-m-d'), " ") }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-6">
            <div class="col-xl-5">
                <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <h3 class="fw-bold fs-4 text-dark mb-0">Cari Donor</h3>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if(empty($active_cabang))
                            <h5 class="text-center my-10">- Cabang Aktif Kosong -</h5>
                        @endif
                        @if(!empty($active_cabang))
                            <div class="notice d-flex bg-light-primary rounded-3 border border-primary border-dashed p-4 mb-5">
                                <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-7 text-gray-700">Cari berdasarkan nama, kode donor, nomor ktp, atau nomor telfon. Jika donor belum terdaftar, tambahkan sebagai donor baru.</div>
                                    </div>
                                </div>
                            </div>

                            <form class="input-group mb-4" id="form_search_donor">
                                @csrf
                                <x-input name="search" prefix="donor_" class="rounded-start" caption="Nama, Kode Donor, No.KTP, No.Telp" />
                                <button class="btn btn-primary ps-6" type="submit">
                                    <i class="ki-duotone ki-magnifier fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    Cari
                                </button>
                                <button class="btn btn-success pe-3" type="button" onclick="new_donor()">
                                    Donor Baru
                                    <i class="ki-duotone ki-plus fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                            </form>
                        @endif

                        <div id="search_donor_table"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card card-flush rounded-4 border-0 shadow-xs h-100">
                    <form class="card-header pt-6" id="form_search">
                        @csrf
                        <div class="card-title flex-column">
                            <h3 class="fw-bold fs-4 text-dark mb-1">Antrian Hari Ini</h3>
                            <div class="fs-7 text-muted">
                                <span id="total_log_donor">0</span> donor terdaftar
                            </div>
                        </div>
                        <div class="card-toolbar d-flex flex-row gap-3">
                            <div class="d-flex align-items-center position-relative">
                                <i class="ki-duotone ki-magnifier fs-5 position-absolute ms-3 text-muted"><span class="path1"></span><span class="path2"></span></i>
                                <x-input name="search" prefix="log_donor_" class="form-control-sm ps-9 w-175px" caption="Filter Pendaftaran ..." />
                            </div>
                            <div class="w-150px"><x-select name="step" class="form-select-sm" caption="Semua Status" :options="array_combine($steps, $steps)" data-control="select2" onchange="search_data()" /></div>
                        </div>
                    </form>
                    <div class="card-body pt-4">
                        <div id="table"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade modal-slide-right" tabindex="-1" id="modal_donor">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="modal_donor_content"></div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        let $form_search_donor = $('#form_search_donor'), $search_donor_table = $('#search_donor_table'), $modal_donor = $('#modal_donor'), $modal_donor_content = $('#modal_donor_content');
        let $form_search = $('#form_search'), $table = $('#table');
        let _token = '{{ csrf_token() }}';
        let base_url = '{{ route('mobil_unit.pendaftaran_mobil.index') }}', donor_url = '{{ route('mobil_unit.donor.index') }}';

        let search_donor = () => {
            let data = get_form_data($form_search_donor);
            $.post(base_url + '/search_donor', data, (result) => $search_donor_table.html(result)).fail((xhr) => $search_donor_table.html(xhr.responseText));
        }

        let search_data = () => {
            let data = get_form_data($form_search);
            $.post(base_url + '/search', data, (result) => $table.html(result)).fail((xhr) => $table.html(xhr.responseText));
        }

        let display_modal_donor = (item) => {
            $modal_donor_content.html(item);
            $modal_donor.modal('show');
        }

        let new_donor = () => {
            $.get(donor_url + '/create', (result) => display_modal_donor(result)).fail((xhr) => display_modal_donor(xhr.responseText));
        }

        let init = () => {
            $modal_donor_content.html('');
            $modal_donor.modal('hide');
        }

        let select_donor = (donor_id) => {
            $.post(base_url, {_token, donor_id}, (result) => {
                if (result.error) {
                    swal.fire({icon: 'error', title: result.error});
                    return;
                }
                $('#donor_search').html('');
                search_donor();
                search_data();
            }).fail((xhr) => {
                $('#error_log').html(xhr.responseText);
            })
        }

        let init_form = () => {
            let $form_info = $('#form_info');
            $form_info.submit((e) => {
                e.preventDefault();
                let url = donor_url;
                let data = new FormData($form_info.get(0));
                $.ajax({
                    url,
                    type: 'post',
                    data,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: (result) => {
                        init();
                        select_donor(result.id);
                    }
                }).fail((xhr) => error_handle(xhr.responseText));
            });
        }

        let confirm_delete = (id) => {
            Swal.fire(swal_delete_params).then((result) => {
                if (result.isConfirmed) $.post(base_url + '/' + id, {_method: 'delete', _token}, (data) => {
                    if (data.error) swal.fire({icon: 'error', title: data.error}).then(() => search_data());
                    else swal.fire('Berhasil Dihapus').then(() => search_data());
                }).fail((xhr) => $table.html(xhr.responseText));
            });
        }

        $form_search_donor.submit((e) => {
            e.preventDefault();
            search_donor();
        });

        $form_search.submit((e) => {
            e.preventDefault();
            search_data();
        });

        init_form_element();
        search_data();
    </script>
@endpush
