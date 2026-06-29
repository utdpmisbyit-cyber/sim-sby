@extends('layouts.index')

@section('title')
    Detail Rencana Produksi - Kantong Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 h-100 shadow-xs">
            <div class="card-body">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1"><span class="text-dark fw-bold fs-1">Detail Rencana Produksi</span></h1>
                        @include('layouts._breadcrumb')
                    </div>
                </div>

                {{-- Barcode Scanner Input --}}
                <div class="card card-flush border border-primary border-dashed bg-light-primary mt-6">
                    <div class="card-body py-6 px-6">
                        <div class="d-flex align-items-center gap-4">
                            <div class="d-flex align-items-center justify-content-center w-50px h-50px rounded-circle bg-primary">
                                <i class="fa fa-barcode text-white fs-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold text-primary mb-1">Scan / Input No. Kantong</h5>
                                <p class="text-muted fs-7 mb-3">Scan atau ketik no. kantong darah untuk menyimpan berat dan volume</p>
                                <div class="d-flex gap-3">
                                    <input type="text" class="form-control form-control-sm w-350px" id="barcode_input" placeholder="Scan no. kantong di sini..." autocomplete="off" autofocus>
                                    <button type="button" class="btn btn-sm btn-primary fw-bold px-6" id="btn_scan">
                                        <i class="fa fa-search me-1"></i> Cari
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Scan Result Area --}}
                <div id="scan_result" class="mt-6"></div>

                {{-- Loading Indicator --}}
                <div id="scan_loading" class="text-center py-10 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-muted mt-3">Mencari data no. kantong...</div>
                </div>

                {{-- Saved List Card --}}
                <div class="card card-flush border mt-8">
                    <div class="card-header min-h-50px px-6 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark fs-5">Daftar Detail Rencana Terisi</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Menampilkan data detail rencana produksi yang sudah diisi berat dan volumenya</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="fa fa-search position-absolute ms-3 text-gray-500"></i>
                                <input type="text" id="search_saved_keyword" class="form-control form-control-solid form-control-sm w-250px ps-9" placeholder="Cari No. Kantong atau Jenis Darah...">
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-6 pb-6">
                        <div id="saved_table_container">
                            {{-- Table loaded via AJAX --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let _token = '{{ csrf_token() }}';
        let scan_url = '{{ route('kantong_darah.detail_rencana_produksi.scan') }}';
        let save_base_url = '{{ route('kantong_darah.detail_rencana_produksi.save', ['id' => '__ID__']) }}';
        let calculate_url = '{{ route('kantong_darah.detail_rencana_produksi.calculate') }}';
        let list_url = '{{ route('kantong_darah.detail_rencana_produksi.list') }}';
        
        let $barcode_input = $('#barcode_input');
        let $scan_result = $('#scan_result');
        let $scan_loading = $('#scan_loading');
        let $saved_table_container = $('#saved_table_container');
        let $search_keyword = $('#search_saved_keyword');
        let current_page = 1;

        let loadSavedList = (page = 1) => {
            let keyword = $search_keyword.val().trim();
            $.post(list_url, {_token, keyword, page, paginate: 10}, (result) => {
                $saved_table_container.html(result);
                // bind pagination links
                $saved_table_container.find('.pagination a').on('click', function(e) {
                    e.preventDefault();
                    let pageUrl = $(this).attr('href');
                    if (pageUrl) {
                        let pageNum = new URL(pageUrl).searchParams.get('page');
                        if (pageNum) {
                            current_page = pageNum;
                            loadSavedList(pageNum);
                        }
                    }
                });
            });
        };

        let doScan = () => {
            let barcode = $barcode_input.val().trim();
            if (barcode === '') {
                Swal.fire({icon: 'warning', title: 'No. Kantong kosong', text: 'Silakan scan atau ketik no. kantong terlebih dahulu'});
                return;
            }

            $scan_result.html('');
            $scan_loading.removeClass('d-none');

            $.post(scan_url, {_token, barcode}, (result) => {
                $scan_loading.addClass('d-none');
                $scan_result.html(result);
                initVerifyForms();
            }).fail((xhr) => {
                $scan_loading.addClass('d-none');
                let errorMsg = 'Terjadi kesalahan';
                try {
                    let resp = JSON.parse(xhr.responseText);
                    errorMsg = resp.error || errorMsg;
                } catch (e) {}
                $scan_result.html(`
                    <div class="alert alert-danger d-flex align-items-center py-4 px-5 rounded">
                        <i class="fa fa-exclamation-triangle text-danger fs-3 me-3"></i>
                        <div>
                            <strong>No. Kantong tidak ditemukan!</strong><br>
                            <span class="text-muted">${errorMsg}</span>
                        </div>
                    </div>
                `);
            });
        };

        let initVerifyForms = () => {
            $('.form-verify-detail').each(function() {
                let $form = $(this);
                let detailId = $form.data('id');
                let jenisDarah = $form.data('jenis-darah');
                let $gram_input = $form.find('.gram-input');
                let $volume_display = $form.find('.volume-display');

                // Auto-calculate volume on gram input
                let calcTimeout = null;
                $gram_input.on('input', function() {
                    clearTimeout(calcTimeout);
                    calcTimeout = setTimeout(() => {
                        let gram = parseFloat($(this).val()) || 0;

                        if (gram <= 0 || !jenisDarah) {
                            $volume_display.text('-');
                            return;
                        }

                        $.post(calculate_url, {_token, gram, jenis_darah: jenisDarah}, (result) => {
                            if (result.volume !== null && result.volume !== undefined) {
                                $volume_display.text(result.volume + ' ml');
                            } else {
                                $volume_display.text('Tidak dapat dihitung');
                            }
                        });
                    }, 300);
                });

                // Submit verify form
                $form.on('submit', function(e) {
                    e.preventDefault();
                    let gram = parseFloat($gram_input.val()) || 0;

                    if (gram <= 0) {
                        Swal.fire({icon: 'warning', title: 'Berat tidak valid', text: 'Masukkan berat kantong dalam gram'});
                        return;
                    }

                    let url = save_base_url.replace('__ID__', detailId);

                    $.post(url, {_token, gram}, (result) => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Detail Rencana Berhasil Disimpan!',
                            html: `<div class="text-start">
                                <p><strong>Satelit:</strong> ${result.no_satelit || '-'}</p>
                                <p><strong>Berat:</strong> ${result.gram} gram</p>
                                <p><strong>Volume:</strong> ${result.volume !== null ? result.volume + ' ml' : 'Tidak dapat dihitung'}</p>
                                <p><strong>Jenis Darah:</strong> ${result.jenis_darah || '-'}</p>
                            </div>`,
                        }).then(() => {
                            // Reload the table
                            loadSavedList(current_page);
                            // Highlight the row/form as saved or do simple scan refous
                            $form.find('button[type="submit"]').removeClass('btn-primary').addClass('btn-success').html('<i class="fa fa-check me-2"></i> Tersimpan');
                            setTimeout(() => {
                                $form.find('button[type="submit"]').removeClass('btn-success').addClass('btn-primary').html('<i class="fa fa-save me-2"></i> Simpan Detail');
                            }, 2000);
                        });
                    }).fail((xhr) => {
                        let errorMsg = 'Terjadi kesalahan';
                        try {
                            let resp = JSON.parse(xhr.responseText);
                            errorMsg = resp.error || errorMsg;
                        } catch (e) {}
                        Swal.fire({icon: 'error', title: 'Gagal', text: errorMsg});
                    });
                });
            });
        };

        // Scan on Enter
        $barcode_input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                doScan();
            }
        });

        // Scan on button click
        $('#btn_scan').on('click', doScan);

        // Search keyup with debounce
        let searchTimeout = null;
        $search_keyword.on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                current_page = 1;
                loadSavedList(1);
            }, 300);
        });

        // Auto-focus barcode input
        $barcode_input.focus();

        // Load saved items list initially
        loadSavedList();
    </script>
@endpush
