<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
            <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                <th class="w-10px ps-4 rounded-start">#</th>
                <th>No. Kantong</th>
                <th>Satelit</th>
                <th>Jenis Darah</th>
                <th class="text-end">Berat (Gram)</th>
                <th class="text-end">Volume (ML)</th>
                <th>Rencana Produksi</th>
                <th class="ps-4 rounded-end">Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            @php($no = 1)
            @if($saved_items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @php($no = (($saved_items->currentPage()-1) * $saved_items->perPage()) + 1)
            @endif
            @forelse($saved_items as $item)
                <tr>
                    <td class="ps-4">{{ $no++ }}</td>
                    <td class="fw-bold text-gray-800">{{ $item->no_kantong }}</td>
                    <td class="fw-semibold text-gray-600">Satelit {{ $item->no_satelit ?? '-' }}</td>
                    <td>
                        <span class="badge badge-light-info fw-bold fs-8 border border-info border-opacity-50 text-uppercase">{{ $item->jenis_darah ?? '-' }}</span>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->gram, 1) }} g</td>
                    <td class="text-end fw-bold text-primary">{{ number_format($item->volume, 1) }} ml</td>
                    <td>
                        <div class="fs-8">
                            <span class="fw-bold">No. Pengiriman:</span> {{ $item->rencanaProduksi->pengirimanAftap->no_pengiriman ?? '-' }}
                        </div>
                        <div class="text-muted fs-9">
                            Tanggal: {{ formatDate($item->rencanaProduksi->tanggal) }}
                        </div>
                    </td>
                    <td class="ps-4">{{ formatDate($item->updated_at) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-8">
                        <i class="fa fa-info-circle fs-4 me-2"></i> Belum ada data detail rencana yang terisi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($saved_items instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
        <div class="text-muted fs-7">
            Menampilkan {{ $saved_items->firstItem() ?? 0 }} sampai {{ $saved_items->lastItem() ?? 0 }} dari {{ $saved_items->total() }} data
        </div>
        <div>
            {{ $saved_items->links('vendor.pagination.custom') }}
        </div>
    </div>
@endif
