@extends('layouts.index')

@section('content')

<div class="d-flex flex-wrap align-items-center mb-6">
    <div class="symbol symbol-50px symbol-circle bg-light-primary me-4">
        <span class="symbol-label">
            <i class="ki-duotone ki-scan-barcode fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
        </span>
    </div>
    <div>
        <h1 class="fw-bold fs-2 text-dark mb-0">Riwayat Barcode Kantong</h1>
        <div class="fs-7 text-muted">Daftar barcode kantong yang sudah digenerate, beserta rekap jumlah per merk dan jenis.</div>
    </div>
</div>

{{-- ═══════════════════════ RINGKASAN ═══════════════════════ --}}
<div class="row g-4 mb-6">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex flex-column justify-content-center py-6 px-6">
                <div class="symbol symbol-45px symbol-circle bg-light-primary mb-4">
                    <span class="symbol-label">
                        <i class="ki-duotone ki-scan-barcode fs-3 text-primary"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                </div>
                <div class="fs-2x fw-bold text-dark" id="summary_total">0</div>
                <div class="fs-7 text-muted">Total Barcode Digenerate</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body py-6 px-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="symbol symbol-40px symbol-circle bg-light-success me-3">
                        <span class="symbol-label">
                            <i class="ki-duotone ki-data fs-4 text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </span>
                    </div>
                    <div class="fs-6 fw-bold text-dark">Jumlah per Merk</div>
                </div>
                <div id="summary_merk" class="d-flex flex-column gap-2 fs-7" style="max-height:160px; overflow-y:auto;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body py-6 px-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="symbol symbol-40px symbol-circle bg-light-warning me-3">
                        <span class="symbol-label">
                            <i class="ki-duotone ki-category fs-4 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                    </div>
                    <div class="fs-6 fw-bold text-dark">Jumlah per Jenis</div>
                </div>
                <div id="summary_jenis" class="d-flex flex-column gap-2 fs-7" style="max-height:160px; overflow-y:auto;"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body py-6 px-6">
                <div class="d-flex align-items-center mb-4">
                    <div class="symbol symbol-40px symbol-circle bg-light-info me-3">
                        <span class="symbol-label">
                            <i class="ki-duotone ki-element-2 fs-4 text-info"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                    <div class="fs-6 fw-bold text-dark">Jumlah per Type</div>
                </div>
                <div id="summary_type" class="d-flex flex-column gap-2 fs-7" style="max-height:160px; overflow-y:auto;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ FILTER ═══════════════════════ --}}
<div class="card border-0 shadow-sm rounded-4 mb-6">
    <div class="card-body py-5 px-6">
        <div class="d-flex align-items-center mb-4">
            <i class="ki-duotone ki-filter fs-4 text-muted me-2"><span class="path1"></span><span class="path2"></span></i>
            <span class="fs-7 fw-bold text-muted text-uppercase">Filter Pencarian</span>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Cari Kode / Barcode / No. Lot</label>
                <div class="position-relative">
                    <i class="ki-duotone ki-magnifier fs-5 text-muted position-absolute top-50 translate-middle-y ms-3"><span class="path1"></span><span class="path2"></span></i>
                    <input type="text" id="f_q" class="form-control form-control-sm ps-9" placeholder="Ketik untuk mencari…" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Merk Kantong</label>
                <select id="f_merk" class="form-select form-select-sm">
                    <option value="">Semua Merk</option>
                    @foreach($merk_options as $merk)
                        <option value="{{ $merk }}">{{ $merk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Jenis Kantong</label>
                <select id="f_jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    @foreach($jenis_options as $jenis)
                        <option value="{{ $jenis }}">{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Type Kantong</label>
                <select id="f_type" class="form-select form-select-sm">
                    <option value="">Semua Type</option>
                    @foreach($type_options as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Status</label>
                <select id="f_status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach($status_options as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Tanggal Dari</label>
                <input type="text" id="f_date_from" class="form-control form-control-sm datepicker" placeholder="Pilih tanggal" autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label fs-8 fw-bold text-muted mb-1">Tanggal Sampai</label>
                <input type="text" id="f_date_to" class="form-control form-control-sm datepicker" placeholder="Pilih tanggal" autocomplete="off">
            </div>
            <div class="col-md-3 d-flex justify-content-end">
                <button type="button" class="btn btn-sm btn-light-secondary" id="btn_reset_filter">
                    <i class="ki-duotone ki-arrows-circle fs-6 me-1"><span class="path1"></span><span class="path2"></span></i>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════ TABEL ═══════════════════════ --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body py-5 px-6">
        <div id="riwayat_table_container">
            <div class="text-center text-muted py-10">Memuat data…</div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {

    var jenisTypeMap = @json($jenis_type_map);
    var allTypeOptions = @json($type_options);

    function currentFilters(page) {
        return {
            q: $('#f_q').val(),
            merk_kantong: $('#f_merk').val(),
            jenis_kantong: $('#f_jenis').val(),
            type_kantong: $('#f_type').val(),
            status: $('#f_status').val(),
            date_from: $('#f_date_from').val(),
            date_to: $('#f_date_to').val(),
            page: page || 1
        };
    }

    function refreshTypeOptions() {
        let jenis = $('#f_jenis').val();
        let options = (jenis && jenisTypeMap[jenis]) ? jenisTypeMap[jenis] : allTypeOptions;
        let currentVal = $('#f_type').val();

        $('#f_type').empty().append('<option value="">Semua Type</option>');
        options.forEach(function (type) {
            $('#f_type').append('<option value="' + type + '">' + type + '</option>');
        });

        if (options.includes(currentVal)) {
            $('#f_type').val(currentVal);
        }
    }

    function loadTable(page) {
        $.get('{{ route("gudang.riwayat_barcode.data") }}', currentFilters(page), function (html) {
            $('#riwayat_table_container').html(html);
        });
    }

    function loadSummary() {
        $.get('{{ route("gudang.riwayat_barcode.summary") }}', currentFilters(), function (res) {
            $('#summary_total').text(res.total);

            let $merk = $('#summary_merk').empty();
            if (!res.per_merk.length) {
                $merk.append('<div class="text-muted">Tidak ada data</div>');
            }
            res.per_merk.forEach(function (row) {
                $merk.append(
                    '<div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-dashed">' +
                        '<span class="text-gray-700">' + (row.merk_kantong ?? '-') + '</span>' +
                        '<span class="badge badge-light-success fs-7">' + row.jumlah + '</span>' +
                    '</div>'
                );
            });

            let $jenis = $('#summary_jenis').empty();
            if (!res.per_jenis.length) {
                $jenis.append('<div class="text-muted">Tidak ada data</div>');
            }
            res.per_jenis.forEach(function (row) {
                $jenis.append(
                    '<div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-dashed">' +
                        '<span class="text-gray-700">' + (row.jenis_kantong ?? '-') + '</span>' +
                        '<span class="badge badge-light-warning fs-7">' + row.jumlah + '</span>' +
                    '</div>'
                );
            });

            let $type = $('#summary_type').empty();
            if (!res.per_type.length) {
                $type.append('<div class="text-muted">Tidak ada data</div>');
            }
            res.per_type.forEach(function (row) {
                $type.append(
                    '<div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-dashed">' +
                        '<span class="text-gray-700">' + (row.type_kantong ?? '-') + '</span>' +
                        '<span class="badge badge-light-info fs-7">' + row.jumlah + '</span>' +
                    '</div>'
                );
            });
        });
    }

    function reload() {
        loadTable(1);
        loadSummary();
    }

    $(document).ready(function () {
        $('.datepicker').flatpickr({ dateFormat: 'Y-m-d', allowInput: true });

        let _timer = null;
        $('#f_q').on('input', function () {
            clearTimeout(_timer);
            _timer = setTimeout(reload, 350);
        });

        $('#f_merk, #f_type, #f_status, #f_date_from, #f_date_to').on('change', reload);

        $('#f_jenis').on('change', function () {
            refreshTypeOptions();
            reload();
        });

        $('#btn_reset_filter').on('click', function () {
            $('#f_q').val('');
            $('#f_merk, #f_jenis, #f_status, #f_type').val('');
            $('#f_date_from, #f_date_to').val('');
            refreshTypeOptions();
            reload();
        });

        $(document).on('click', '#riwayat_table_container .pagination a', function (e) {
            e.preventDefault();
            let url = new URL(this.href);
            let page = url.searchParams.get('page') || 1;
            loadTable(page);
        });

        reload();
    });

})();
</script>
@endpush

@endsection