<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th>#</th>
    <th>Kode</th>
    <th>Tanggal Pinjam</th>
    <th>Barang</th>
    <th>Petugas</th>
    <th>Jumlah</th>
    <th>Diserahkan Ke</th>
    <th>Status</th>
    <th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>

@php($no = 1)
@if($retur_pinjam instanceof \Illuminate\Pagination\LengthAwarePaginator)
    @php($no = (($retur_pinjam->currentPage()-1) * $retur_pinjam->perPage()) + 1)
@endif

@foreach($retur_pinjam as $item)
<tr>

    {{-- NOMOR --}}
    <td>{{ $no++ }}</td>

    {{-- KODE RETUR --}}
    <td>{{ $item->kode }}</td>

    {{-- TANGGAL RETUR --}}
    <td>{{ $item->tanggal_retur ? date('d-m-Y', strtotime($item->tanggal_retur)) : '-' }}</td>

    {{-- NAMA BARANG RELASI --}}
    <td>{{ $item->barang->nama ?? '-' }}</td>

    {{-- PETUGAS --}}
    <td>{{ $item->petugas->nama ?? '-' }}</td>

    {{-- JUMLAH RETUR --}}
    <td>{{ $item->jumlah_retur }}</td>

    {{-- DISERAHKAN KE (bagian petugas) --}}
    <td>{{ $item->bagianPetugas->nama ?? '-' }}</td>

    {{-- STATUS PINJAM (dari relasi pinjam barang) --}}
    <td>
        {{ $item->pinjamBarang->status ?? '-' }}
    </td>

    {{-- OPSI --}}
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
                    <a onclick="info({{ $item->id }})" href="javascript:void(0)">
                        Ubah
                    </a>
                </div>

                {{-- DELETE --}}
                <div class="dropdown-item">
                    <a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)">
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

@if($retur_pinjam instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $retur_pinjam->links('vendor.pagination.custom') }}
@endif