@php($active_cabang = session('active_cabang', []))
@php($nomor_ruangan = session('nomor_ruangan'))

@extends('layouts.index')

@section('title')
    Pemeriksaan Kesehatan - Unit Tranfusi Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
            <div class="card-body py-5">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1">
                            <span class="text-dark fw-bold fs-1">Pemeriksaan Kesehatan</span>
                        </h1>
                        @include('layouts._breadcrumb')
                    </div>
                    <div class="d-flex gap-3">
                        <span class="badge badge-light-primary rounded-3 fs-7 text-dark fw-semibold px-4 py-3 cursor-pointer" onclick="setNomorRuangan()">
                            <i class="ki-duotone ki-folder fs-5 me-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            No.Ruangan : &nbsp;<b>{{ session('nomor_ruangan', '-Kosong-') }}</b>
                        </span>
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

        @if(empty($active_cabang))
            <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
                <div class="card-body">
                    <h5 class="text-center my-10">- Cabang Aktif Kosong -</h5>
                </div>
            </div>
        @endif

        @if(!empty($active_cabang))
            <div class="row g-6">
                <div class="col-md-4" id="table_log_donor"></div>
                <div class="col-md-8">
                    <div class="card card-flush rounded-4 border-0 shadow-xs h-100" id="card_search">
                        <form class="card-header pt-6" id="form_search">
                            @csrf
                            <div class="card-title flex-column">
                                <h3 class="fw-bold fs-4 text-dark mb-1">Antrian Hari Ini</h3>
                                <div class="fs-7 text-muted">
                                    <span id="total_pemeriksaan_dokter">0</span> riwayat pemeriksaan
                                </div>
                            </div>
                            <div class="card-toolbar d-flex flex-row gap-3">
                                <div class="d-flex align-items-center position-relative">
                                    <i class="ki-duotone ki-magnifier fs-5 position-absolute ms-3 text-muted"><span class="path1"></span><span class="path2"></span></i>
                                    <x-input name="nama_pasien" prefix="pemeriksaan_dokter_" class="form-control-sm ps-9 w-175px" caption="Cari Nama Donor ..." />
                                </div>
                            </div>
                        </form>
                        <div class="card-body pt-4">
                            <div id="table"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-3" id="info_log_donor"></div>
                <div class="col-md-9" id="info"></div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        let _token = '{{ csrf_token() }}', base_url = '{{ route('mobil_unit.pemeriksaan_mobil.index') }}';
        let $table_log_donor = $('#table_log_donor'), $info_log_donor = $('#info_log_donor'), $info = $('#info'), $table = $('#table');
        let $form_search = $('#form_search'), $card_search = $('#card_search');

        let init = () => {
            $info_log_donor.html('');
            $info.html('');
            $table_log_donor.show();
            $card_search.show();
            search_pemeriksaan();
            search_log_donor();
        }

        let search_log_donor = () => {
            $.post(base_url + '/log_donor/search', {_token}, (result) => $table_log_donor.html(result)).fail((xhr) => $table_log_donor.html(xhr.responseText));
        }

        let select_log = (id) => {
            $.get(base_url + '/log_donor/' + id, {_token}, (result) => {
                $info_log_donor.html(result)
            }).fail((xhr) => {
                $info_log_donor.html(xhr.responseText)
            });
        }

        let info = (id) => {
            $table_log_donor.hide();
            $card_search.hide();
            $.get(base_url + '/' + id, {_token}, (result) => {
                $info.html(result)
            }).fail((xhr) => {
                $info.html(xhr.responseText)
            });
        }

        let init_form = (id) => {
            let $form_info = $('#form_info');
            $form_info.submit((e) => {
                e.preventDefault();
                let url = base_url + '/' + id + '?_method=put';
                let data = new FormData($form_info.get(0));
                $.ajax({
                    url, type: 'post', data, cache: false, processData: false, contentType: false,
                    success: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pemeriksaan Berhasil Disimpan'
                        }).then(() => init())
                    },
                }).fail((xhr) => error_handle(xhr.responseText));
            });
        }

        let search_pemeriksaan = () => {
            let data = get_form_data($form_search);
            $.post(base_url + '/search', data, (result) => $table.html(result)).fail((xhr) => $table.html(xhr.responseText));
        }

        let setNomorRuangan = () => {
            Swal.fire({
                title: 'Isi Nomor Ruangan',
                input: 'number',
                showDenyButton: true,
                confirmButtonText: 'Simpan',
                denyButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    let nomor_ruangan = result.value;
                    $.post(base_url + '/nomor_ruangan', {_token, nomor_ruangan}, () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Disimpan'
                        }).then(() => {
                            window.location.reload();
                        })
                    }).fail((xhr) => $table_log_donor.html(xhr.responseText));
                }
            });
        }

        $form_search.submit((e) => {
            e.preventDefault();
            search_pemeriksaan();
        });

        init_form_element();
        init();
    </script>
@endpush
