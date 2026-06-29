<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th>No. Kantong</th>
            <th>Kode Aftap</th>
            <th>Tanggal Kirim</th>
            <th>Kode Donor</th>
            <th>Nama Donor</th>
            <th>Gol. Darah</th>
            <th>Rhesus</th>
            <th>Petugas Kirim</th>
            <th class="text-center">Status</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($kirim_litbangs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($kirim_litbangs->currentPage()-1) * $kirim_litbangs->perPage()) + 1)
        @endif
        @foreach($kirim_litbangs as $item)
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="fw-bold">{{ $item->no_kantong }}</td>
                <td>{{ $item->aftap->kode ?? '-' }}</td>
                <td class="text-nowrap">{{ formatDate($item->tanggal_kirim) }}</td>
                <td>{{ $item->donor->kode ?? '-' }}</td>
                <td>{{ $item->donor->nama ?? '-' }}</td>
                <td>{{ $item->donor->golongan_darah ?? '-' }}</td>
                <td>{{ $item->donor->rhesus ?? '-' }}</td>
                <td>{{ $item->petugasKirim->nama ?? '-' }}</td>
                <td class="text-center">
                    @php($badge = $item->status === 'selesai' ? 'success' : 'warning')
                    <span class="badge badge-light-{{ $badge }}">{{ $item->status === 'selesai' ? 'DIKONFIRMASI' : 'DIKIRIM' }}</span>
                </td>
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
@if($kirim_litbangs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $kirim_litbangs->links('vendor.pagination.custom') }}
@endif
