@extends('layouts.index')

@section('title')
    Persediaan Kantonng Darah - Kantong Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 h-100 shadow-xs">
            <div class="card-body">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1"><span class="text-dark fw-bold fs-1">Data Persediaan Kantonng Darah</span></h1>
                        @include('layouts._breadcrumb')
                    </div>
                </div>

                <div class="mt-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
                            <thead>
                            <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                                <th class="w-10px ps-4 rounded-start">#</th>
                                <th>Jenis Kantong</th>
                                <th>Nama</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-center w-50px pe-4 rounded-end"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($no = 1)
                            @foreach($list_tipe_kantong as $item)
                                <tr>
                                    <td class="ps-4">{{ $no++ }}</td>
                                    <td>{{ $item->jenisKantong->nama ?? '' }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td class="text-end">{{ formatNumber($item->stock) }}</td>
                                    <td class="text-end text-nowrap">

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
