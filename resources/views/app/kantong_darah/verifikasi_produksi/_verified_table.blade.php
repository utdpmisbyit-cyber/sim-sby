<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
            <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                <th class="w-10px ps-4 rounded-start">#</th>
                <th>Kode</th>
                <th>Barcode (Kantong + Satelit)</th>
                <th>Jenis Darah</th>
                <th class="text-end">Berat (Gram)</th>
                <th class="text-end">Volume (ML)</th>
                <th>Status</th>
                <th class="ps-4 rounded-end">Tanggal Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @php($no = 1)
            @if($verified_items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @php($no = (($verified_items->currentPage()-1) * $verified_items->perPage()) + 1)
            @endif
            @forelse($verified_items as $item)
                <tr>
                    <td class="ps-4">{{ $no++ }}</td>
                    <td class="fw-bold text-gray-800">{{ $item->kode }}</td>
                    <td class="fw-semibold text-gray-600">{{ $item->barcode }}</td>
                    <td>
                        <span class="badge badge-light-info fw-bold fs-8 border border-info border-opacity-50 text-uppercase">{{ $item->jenis_darah }}</span>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->gram, 1) }} g</td>
                    <td class="text-end fw-bold text-primary">{{ number_format($item->volume, 1) }} ml</td>
                    <td>
                        <span class="badge badge-light-success fw-bold fs-8 border border-success border-opacity-50">VERIFIED</span>
                    </td>
                    <td class="ps-4">{{ formatDate($item->updated_at) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-8">
                        <i class="fa fa-info-circle fs-4 me-2"></i> Belum ada data produksi terverifikasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($verified_items instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
        <div class="text-muted fs-7">
            Menampilkan {{ $verified_items->firstItem() ?? 0 }} sampai {{ $verified_items->lastItem() ?? 0 }} dari {{ $verified_items->total() }} data
        </div>
        <div>
            {{ $verified_items->links('vendor.pagination.custom') }}
        </div>
    </div>
@endif
