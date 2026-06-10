@extends('layouts.index')

@section('title')
   Inventory - Buku Besar 
@endsection

@section('content')
<div class="content flex-column-fluid" id="kt_content">
    <div class="card card-flush rounded-4 border-0 h-100 shadow-xs">
        <div class="card-body">

            <div class="d-flex flex-row justify-content-between align-items-center">
                <div class="d-flex flex-column">
                    <h1 class="d-flex align-items-center my-1">
                        <span class="text-dark fw-bold fs-1">Buku Besar</span>
                    </h1>
                    @include('layouts._breadcrumb')
                </div>
            </div>

            {{-- FILTER COA --}}
            <select id="filter_coa" class="form-control mt-4">
                <option value="">-- Semua COA --</option>
                @foreach(\App\Models\Coa::all() as $coa)
                    <option value="{{ $coa->id }}">
                        {{ $coa->nama_akun }} ({{ $coa->kd_coa }})
                    </option>
                @endforeach
            </select>

            {{-- FORM SEARCH --}}
            <form id="form_search" class="w-100 mt-4 bg-gray-200 border-solid border-2 border-gray-300 rounded-2 px-5 py-4">
                @csrf
                <div class="d-flex flex-lg-row flex-column align-items-lg-center justify-content-lg-between gap-4">
                    <div class="d-flex flex-row gap-6 flex-grow-1">
                        <x-input name="nama" prefix="search_" caption="Cari Nama" />
                        <x-input name="kode" prefix="search_" caption="Cari Kode" />
                    </div>
                    <button type="submit" class="btn btn-sm btn-success fw-bold border-0 fs-7">
                        Cari <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            {{-- TABLE AJAX --}}
            <div id="table" class="mt-4"></div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let $form_search = $('#form_search'),
    $table = $('#table'),
    $filter_coa = $('#filter_coa');

let selected_page = 1;
let base_url = '{{ route('finance.buku_besar.index') }}';

// =======================
// SEARCH AJAX
// =======================
let search_data = (page = 1) => {
    let data = get_form_data($form_search);

    data.search_nama = data.search_nama || '';
    data.search_kode = data.search_kode || '';

    // FILTER COA
    data.filter_coa = $filter_coa.val();

    $.post(base_url + '/search', data, (result) => {
        $table.html(result);
    }).fail((xhr) => {
        $table.html(xhr.responseText);
    });
};

// LISTENER FILTER COA
$filter_coa.on('change', () => search_data(1));

// LISTENER SEARCH FORM
$form_search.on('submit', (e) => {
    e.preventDefault();
    search_data(1);
});

// INIT
search_data();
</script>
@endpush