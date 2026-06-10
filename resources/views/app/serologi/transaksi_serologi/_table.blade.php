<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th class="w-100px">Nomor</th>
            <th>Group</th>
            <th>Tanggal</th>
            <th>Jenis Periksa</th>
            <th>Metode Periksa</th>
            <th>Reagen</th>
            <th>Petugas</th>
            <th>Pemeriksa</th>
            <th>Diputar</th>
            <th>Diperiksa</th>
            <th>Disahkan</th>
            <th class="text-center">Jumlah Detail</th>
            <th class="text-center">Selesai</th>
            <th class="text-center">Status</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($serologis instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($serologis->currentPage()-1) * $serologis->perPage()) + 1)
        @endif
        @foreach($serologis as $item)
            @php($total = $item->details->count())
            @php($done = $item->details->where('status', '!=', 'pending')->count())
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="fw-bold">{{ $item->nomor }}</td>
                <td>
                    <span class="badge badge-light-info">{{ $item->group ?? '-' }}</span>
                </td>
                <td>{{ formatDate($item->tanggal) }}</td>
                <td>{{ $item->jenisPeriksaSerologi->nama ?? '-' }}</td>
                <td>{{ $item->metodeSerologi->nama ?? '-' }}</td>
                <td>{{ $item->reagenSerologi->nama ?? '-' }}</td>
                <td>{{ $item->petugas->nama ?? '-' }}</td>
                <td>{{ $item->pemeriksaSerologi->nama ?? '-' }}</td>
                <td>{{ $item->diputarOleh->nama ?? '-' }}</td>
                <td>{{ $item->diperiksaOleh->nama ?? '-' }}</td>
                <td>{{ $item->disahkanOleh->nama ?? '-' }}</td>
                <td class="text-center">{{ $total }}</td>
                <td class="text-center">{{ $done }}</td>
                <td class="text-center">
                    @php($badge = $item->status === 'selesai' ? 'success' : ($item->status === 'proses' ? 'warning' : 'secondary'))
                    <span class="badge badge-light-{{ $badge }}">{{ strtoupper($item->status) }}</span>
                </td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-primary ps-4 pe-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                    </button>
                    <div class="menu menu-sub menu-sub-dropdown dropdown-menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true">
                        <div class="menu-item px-3"><a onclick="info({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Detail / Edit</a></div>
                        <div class="menu-item px-3"><a onclick="duplicate_quick({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Buat Serupa (Cepat)</a></div>
                        <div class="menu-item px-3"><a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Hapus</a></div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($serologis instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $serologis->links('vendor.pagination.custom') }}
@endif
