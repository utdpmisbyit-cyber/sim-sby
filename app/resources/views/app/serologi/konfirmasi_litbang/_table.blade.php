<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th>No. Kantong</th>
            <th>Tanggal Kirim</th>
            <th>Nama Donor</th>
            <th>Gol. Darah Awal</th>
            <th>Gol. Darah Konfirmasi</th>
            <th>Rhesus Konfirmasi</th>
            <th>Tanggal Konfirmasi</th>
            <th>Petugas Konfirmasi</th>
            <th class="text-center">Status</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($konfirmasi_litbangs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($konfirmasi_litbangs->currentPage()-1) * $konfirmasi_litbangs->perPage()) + 1)
        @endif
        @foreach($konfirmasi_litbangs as $item)
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="fw-bold">{{ $item->no_kantong }}</td>
                <td class="text-nowrap">{{ formatDate($item->tanggal_kirim) }}</td>
                <td>{{ $item->donor->nama ?? '-' }}</td>
                <td>
                    @if($item->donor)
                        <span class="badge badge-light-secondary">{{ $item->donor->golongan_darah }}{{ $item->donor->rhesus }}</span>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($item->golongan_darah)
                        <span class="badge badge-light-primary fw-bold">{{ $item->golongan_darah }}</span>
                    @else
                        <span class="text-muted italic">-</span>
                    @endif
                </td>
                <td>
                    @if($item->rhesus)
                        <span class="badge badge-light-info fw-bold">{{ $item->rhesus }}</span>
                    @else
                        <span class="text-muted italic">-</span>
                    @endif
                </td>
                <td class="text-nowrap">{{ $item->tanggal_konfirmasi ? formatDate($item->tanggal_konfirmasi) : '-' }}</td>
                <td>{{ $item->petugasKonfirmasi->nama ?? '-' }}</td>
                <td class="text-center">
                    @php($badge = $item->status === 'selesai' ? 'success' : 'warning')
                    <span class="badge badge-light-{{ $badge }}">{{ $item->status === 'selesai' ? 'KONFIRMASI SELESAI' : 'MENUNGGU KONFIRMASI' }}</span>
                </td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-primary ps-4 pe-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown dropdown-menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true">
                        <div class="menu-item px-3"><a onclick="info({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Konfirmasi</a></div>
                        <div class="menu-item px-3"><a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Hapus</a></div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($konfirmasi_litbangs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $konfirmasi_litbangs->links('vendor.pagination.custom') }}
@endif
