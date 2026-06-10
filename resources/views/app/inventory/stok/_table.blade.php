<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th class="w-10px ps-4 rounded-start">#</th>
    <th>Nama Barang</th>
    <th>Stok Sekarang</th>
    <th>Min Stok</th>
    <th>Warning</th>
    <th>Qty Masuk</th>
    <th>Qty Keluar</th>
    <th>Harga</th>
    <th>Proses</th>
    <th>Status</th>
    <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
</tr>
</thead>

<tbody>

@php
    $no = 1;
    if($stoks instanceof \Illuminate\Pagination\LengthAwarePaginator){
        $no = (($stoks->currentPage()-1) * $stoks->perPage()) + 1;
    }
@endphp

@forelse($stoks as $item)

@php
    $barang = $item->barang ?? null;
    $stok   = $barang->getAttribute('stok') ?? 0;
    $min    = $barang->min_stok ?? 0;

    $rowClass = '';
    if ($stok <= 0) {
        $rowClass = 'table-danger';
    } elseif ($stok <= $min) {
        $rowClass = 'table-warning';
    }
@endphp

<tr class="{{ $rowClass }}">

    <td class="ps-4">{{ $no++ }}</td>

    <td>{{ $barang->nama ?? '-' }}</td>

    <td>{{ $stok }}</td>

    <td>{{ $min }}</td>

    <td>
        @if($stok <= 0)
            <span class="badge badge-danger">❌ Habis</span>
        @elseif($stok <= $min)
            <span class="badge badge-warning">⚠️ Hampir Habis</span>
        @else
            <span class="badge badge-success">Aman</span>
        @endif
    </td>

    <td>{{ $item->qty_in ?? 0 }}</td>
    <td>{{ $item->qty_out ?? 0 }}</td>

    <td>Rp {{ number_format($item->harga ?? 0, 0, ',', '.') }}</td>

    <td>
        @if($item->proses == 1)
            <span class="badge badge-success">Masuk</span>
        @else
            <span class="badge badge-danger">Keluar</span>
        @endif
    </td>

    <td>
        @if($item->aktif == 1)
            <span class="badge badge-primary">Aktif</span>
        @else
            <span class="badge badge-secondary">Nonaktif</span>
        @endif
    </td>

    <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
            type="button"
            data-bs-toggle="dropdown">
            Opsi
        </button>

        <div class="dropdown-menu">
            <a onclick="info({{ $item->id }})"
                href="javascript:void(0)"
                class="dropdown-item">
                Ubah
            </a>

            <a onclick="confirm_delete({{ $item->id }})"
                href="javascript:void(0)"
                class="dropdown-item">
                Hapus
            </a>
        </div>
    </td>

</tr>

@empty
<tr>
    <td colspan="11" class="text-center text-muted">
        Data tidak ditemukan
    </td>
</tr>
@endforelse

</tbody>

</table>
</div>

@if($stoks instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $stoks->links('vendor.pagination.custom') }}
@endif