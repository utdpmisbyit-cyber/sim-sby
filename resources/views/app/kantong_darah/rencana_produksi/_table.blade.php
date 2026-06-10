<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th>Pengiriman Sample (No. FPD)</th>
            <th>Tipe Kantong</th>
            <th>Tanggal</th>
            <th>Petugas</th>
            <th class="text-center">Jumlah Detail</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($rencana_produksis instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($rencana_produksis->currentPage()-1) * $rencana_produksis->perPage()) + 1)
        @endif
        @foreach($rencana_produksis as $item)
            @php($total = $item->details->count())
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="fw-bold">{{ $item->pengirimanSample->no_fpd ?? '-' }}</td>
                <td>{{ $item->tipeKantong->nama ?? '-' }}</td>
                <td>{{ formatDate($item->tanggal) }}</td>
                <td>{{ $item->petugas->nama ?? '-' }}</td>
                <td class="text-center">{{ $total }}</td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-primary ps-4 pe-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown dropdown-menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true">
                        <div class="menu-item px-3"><a onclick="info({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Detail / Edit</a></div>
                        <div class="menu-item px-3"><a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Hapus</a></div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($rencana_produksis instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $rencana_produksis->links('vendor.pagination.custom') }}
@endif
