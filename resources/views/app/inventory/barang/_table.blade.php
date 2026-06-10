<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
<th class="w-10px ps-4 rounded-start">#</th>
<th class="w-100px">Kode</th>
<th>Nama</th>
<th>Jenis</th>
<th>Stok</th>
<th>Min Stok</th>
<th>Warning</th>
<th>Satuan</th>
<th>Harga</th>
<th class="text-center w-50px pe-4 rounded-end">Opsi</th>
</tr>
</thead>

<tbody>

@php $no = 1; @endphp

@if($barangs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    @php $no = (($barangs->currentPage()-1) * $barangs->perPage()) + 1; @endphp
@endif

@forelse($barangs as $item)

@php
    $stok = $item->stok ?? 0;
    $min  = $item->min_stok ?? 0;

    $rowClass = '';
    if ($stok <= 0) {
        $rowClass = 'table-danger';
    } elseif ($stok <= $min) {
        $rowClass = 'table-warning';
    }
@endphp

<tr class="{{ $rowClass }}">

<td class="ps-4">{{ $no++ }}</td>

<td class="fw-bold text-nowrap">{{ $item->kode }}</td>

<td class="text-nowrap">{{ $item->nama }}</td>

<td>{{ $item->jenis_barang }}</td>

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

<td>{{ $item->satuan }}</td>

<td>Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>

<td class="text-end text-nowrap">

@if($item->deleted_at === null)

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

@else

<button class="btn btn-sm btn-primary px-2 py-1"
    type="button"
    onclick="confirm_restore({{ $item->id }})">
    Batal Hapus
</button>

@endif

</td>

</tr>

@empty
<tr>
    <td colspan="10" class="text-center">Data tidak ditemukan</td>
</tr>
@endforelse

</tbody>

</table>
</div>

@if($barangs instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $barangs->links('vendor.pagination.custom') }}
@endif