@if($riwayats->count() > 0)
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-200 table-sm align-middle">
            <thead>
            <tr class="fs-7 fw-bold bg-secondary text-gray-400 border-bottom-0 text-uppercase">
                <th class="w-30px ps-4 rounded-start">No.</th>
                <th class="min-w-100px">Kode</th>
                <th class="min-w-120px">Barcode</th>
                <th class="min-w-100px">Merk</th>
                <th class="min-w-100px">Jenis</th>
                <th class="min-w-80px">Type</th>
                <th class="min-w-80px">Ukuran</th>
                <th class="min-w-100px">No. Lot</th>
                <th class="min-w-60px text-center">Duplikat</th>
                <th class="min-w-80px">Status</th>
                <th class="min-w-120px text-end pe-4 rounded-end">Tanggal Generate</th>
            </tr>
            </thead>
            <tbody>
            @php($no = ($riwayats->currentPage() - 1) * $riwayats->perPage() + 1)
            @foreach($riwayats as $riwayat)
                <tr>
                    <td class="fw-semibold text-muted fs-7 ps-4">{{ $no++ }}</td>
                    <td><span class="fw-bold text-dark fs-7">{{ $riwayat->kode }}</span></td>
                    <td><span class="text-dark fs-7 font-monospace">{{ $riwayat->barcode ?? '-' }}</span></td>
                    <td class="fs-7">{{ $riwayat->merk_kantong ?? '-' }}</td>
                    <td class="fs-7">{{ $riwayat->jenis_kantong ?? '-' }}</td>
                    <td class="fs-7">{{ $riwayat->type_kantong ?? '-' }}</td>
                    <td class="fs-7">{{ $riwayat->ukuran ?? '-' }}</td>
                    <td class="fs-7">{{ $riwayat->no_lot ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-light-info">{{ $riwayat->duplikat }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $riwayat->status === 'aktif' ? 'badge-light-success' : 'badge-light-secondary' }}">
                            {{ ucfirst($riwayat->status) }}
                        </span>
                    </td>
                    <td class="text-end pe-4 fs-7">{{ formatDate($riwayat->created_at) }}, {{ formatTime($riwayat->created_at) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="fs-8 text-muted">
            Menampilkan {{ $riwayats->firstItem() }}–{{ $riwayats->lastItem() }} dari {{ $riwayats->total() }} data
        </div>
        <div>
            {{ $riwayats->links() }}
        </div>
    </div>
@else
    <div class="text-center py-10">
        <i class="ki-duotone ki-scan-barcode fs-3x text-muted mb-3">
            <span class="path1"></span><span class="path2"></span>
        </i>
        <div class="fs-6 text-muted">Belum ada riwayat barcode yang digenerate.</div>
    </div>
@endif