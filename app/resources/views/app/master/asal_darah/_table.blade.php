<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th class="w-100px">Kode</th>
            <th>Nama</th>
            <th>Alamat 1</th>
            <th>Alamat 2</th>
            <th>Kodepos</th>
            <th>No.Telp</th>
            <th>Nama Sponsor</th>
            <th>No.Telp Sponsor</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($asal_darahs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($asal_darahs->currentPage()-1) * $asal_darahs->perPage()) + 1)
        @endif
        @foreach($asal_darahs as $item)
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="text-nowrap">{{ $item->kode }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->alamat_1 }}</td>
                <td>{{ $item->alamat_2 }}</td>
                <td>{{ $item->kodepos }}</td>
                <td>{{ $item->no_telp }}</td>
                <td>{{ $item->nama_sponsor }}</td>
                <td>{{ $item->no_telp_sponsor }}</td>
                <td class="text-end text-nowrap">
                    @if($item->deleted_at === null)
                        <button class="btn btn-sm btn-primary ps-4 pe-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown dropdown-menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true">
                            <div class="menu-item px-3"><a onclick="info({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Ubah</a></div>
                            <div class="menu-item px-3"><a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Hapus</a></div>
                        </div>
                    @else
                        <button class="btn btn-sm btn-primary px-2 py-1" type="button" onclick="confirm_restore({{ $item->id }})">
                            Batal Hapus
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($asal_darahs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $asal_darahs->links('vendor.pagination.custom') }}
@endif
