<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th class="w-100px">No.Permintaan</th>
            <th>Tanggal</th>
            <th>Bagian</th>
            <th>Petugas</th>
            <th class="text-center">Jumlah</th>
            <th class="text-center">Status</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($permintaan_kantongs instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($permintaan_kantongs->currentPage()-1) * $permintaan_kantongs->perPage()) + 1)
        @endif
        @foreach($permintaan_kantongs as $item)
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="text-nowrap fw-bold">{{ $item->nomor }}</td>
                <td class="text-nowrap">{{ formatDate($item->tanggal) }}</td>
                <td>{{ $item->bagianPetugas->nama ?? '-' }}</td>
                <td>{{ $item->petugas->nama ?? '-' }}</td>
                <td class="text-center">{{ $item->details->sum('jumlah') }}</td>
                <td class="text-center">{{ $item->flag == 0 ? 'Pending' : 'Verified' }}</td>
                <td class="text-end text-nowrap">
                    @if($item->deleted_at === null)
                        @if($item->flag === 0)
                            <button class="btn btn-sm btn-primary px-3 py-1" type="button" onclick="info({{ $item->id }})">
                                Konfirmasi
                            </button>
                        @endif
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
@if($permintaan_kantongs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $permintaan_kantongs->links('vendor.pagination.custom') }}
@endif
