<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th>Kode</th>
            <th>Barcode (Kantong + Satelit)</th>
            <th>Status</th>
            <th>Tanggal Rilis</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($produksi_rilis instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($produksi_rilis->currentPage()-1) * $produksi_rilis->perPage()) + 1)
        @endif
        @foreach($produksi_rilis as $item)
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="fw-bold text-gray-800">{{ $item->kode }}</td>
                <td class="fw-semibold">{{ $item->barcode }}</td>
                <td>
                    @if($item->status === 'SENDING')
                        <span class="badge badge-light-warning fw-bold fs-8 border border-warning border-opacity-50">SENDING</span>
                    @elseif($item->status === 'QUEUED')
                        <span class="badge badge-light-primary fw-bold fs-8 border border-primary border-opacity-50">QUEUED</span>
                    @elseif($item->status === 'ONGOING')
                        <span class="badge badge-light-info fw-bold fs-8 border border-info border-opacity-50">ONGOING</span>
                    @elseif($item->status === 'COMPLETED')
                        <span class="badge badge-light-success fw-bold fs-8 border border-success border-opacity-50">COMPLETED</span>
                    @else
                        <span class="badge badge-light-secondary fw-bold fs-8">{{ $item->status }}</span>
                    @endif
                </td>
                <td>{{ formatDate($item->created_at) }}</td>
                <td class="text-end text-nowrap pe-4">
                    <button class="btn btn-sm btn-icon btn-light-danger btn-active-light-danger border border-danger border-opacity-25" type="button" onclick="confirm_delete({{ $item->id }})" title="Hapus">
                        <i class="fa fa-trash fs-6"></i>
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($produksi_rilis instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $produksi_rilis->links('vendor.pagination.custom') }}
@endif
