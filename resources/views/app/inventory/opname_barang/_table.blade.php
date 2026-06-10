<div class="table-responsive">
<table class="table table-row-dashed table-hover fs-7 table-sm align-middle">

<thead>
<tr class="bg-secondary text-dark fw-bold fs-7 text-uppercase">
    <th>#</th>
    <th>No Opname</th>
    <th>Tanggal</th>
    <th>Barang</th>
    <th>Qty Sistem</th>
    <th>Qty Fisik</th>
    <th>Selisih</th>
    <th>Lokasi Stok</th>
    <th>Petugas</th>
    <th>Status</th>
    <th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>

@php($no = $opname_barang instanceof \Illuminate\Pagination\LengthAwarePaginator 
    ? (($opname_barang->currentPage()-1) * $opname_barang->perPage()) + 1 
    : 1)

@foreach($opname_barang as $item)
<tr>
    <td>{{ $no++ }}</td>
    <td>{{ $item->no_opname }}</td>

    <td>{{ date('d-m-Y', strtotime($item->tgl_opname)) }}</td>

    <td>{{ $item->barang->nama ?? '-' }}</td>

    <td>{{ $item->qty_sistem }}</td>
    <td>{{ $item->qty_fisik }}</td>
    <td class="{{ $item->selisih < 0 ? 'text-danger' : 'text-success' }}">
        {{ $item->selisih }}
    </td>
    <td>{{ $item->lokasiBagian->nama ?? '-' }}</td>
    <td>{{ $item->petugas->nama ?? '-' }}</td>

    <td>
        <span class="badge badge-light-{{ $item->status == 'selesai' ? 'success':'warning' }}">
            {{ ucfirst($item->status) }}
        </span>
    </td>

   <td class="text-end text-nowrap">

    @if(!$item->deleted_at)

        <button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
            type="button"
            data-bs-toggle="dropdown">
            Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
        </button>

        <div class="dropdown-menu">

            {{-- EDIT --}}
            <div class="dropdown-item">
                <a onclick="info('{{ $item->no_opname }}')" href="javascript:void(0)">
                    Ubah
                </a>
            </div>

            {{-- DELETE --}}
            <div class="dropdown-item">
                <a onclick="confirm_delete('{{ $item->no_opname }}')" href="javascript:void(0)">
                    Hapus
                </a>
            </div>

        </div>

    @else
        <span class="badge badge-light-danger">Data Terhapus</span>
    @endif

</td>

</tr>
@endforeach

</tbody>
</table>

</div>

@if($opname_barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $opname_barang->links('vendor.pagination.custom') }}
@endif
