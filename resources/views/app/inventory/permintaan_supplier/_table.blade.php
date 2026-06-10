<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
    <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th>Barang</th>
            <th>Tgl Permintaan</th>
            <th>Satuan</th>
            <th>Jumlah</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
    </thead>
    <tbody>
        @php($no = 1)
        @if($permintaan_supplier instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($permintaan_supplier->currentPage()-1) * $permintaan_supplier->perPage()) + 1)
        @endif

        @foreach($permintaan_supplier as $item)
        <tr>
            <td class="ps-4">{{ $no++ }}</td>

            {{-- Nama Barang --}}
            <td>{{ $item->barang->nama ?? $item->barang_id }}</td>
            <td>{{ $item->tgl_permintaan }}</td>
            <td>{{ $item->satuan }}</td>
            {{-- Jumlah --}}
            <td>{{ $item->qty }}</td>

            {{-- Proses --}}
            <td>
              {{ $item->keterangan }}
            </td>

            {{-- Status --}}
            <td>
                @if($item->status == 1)
                    <span class="badge badge-primary">Aktif</span>
                @else
                    <span class="badge badge-secondary">Nonaktif</span>
                @endif
            </td>

            {{-- Opsi --}}
            <td class="text-end text-nowrap">
                <button class="btn btn-sm btn-primary ps-4 pe-2 py-1" type="button" data-bs-toggle="dropdown">Opsi</button>
                <div class="dropdown-menu">
                    {{-- Pastikan kirim id/UUID supaya form load data --}}
                    <a onclick="info('{{ $item->id }}')" href="javascript:void(0)" class="dropdown-item">Ubah</a>
                    <a onclick="confirm_delete('{{ $item->id }}')" href="javascript:void(0)" class="dropdown-item">Hapus</a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>

{{-- Pagination --}}
@if($permintaan_supplier instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $permintaan_supplier->links('vendor.pagination.custom') }}
@endif