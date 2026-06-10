<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th>#</th>
    <th>Kode</th>
    <th>Tanggal</th>
    <th>Jenis Permintaan Barang </th>
    <th>Status</th>
    <th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>

@php($no = 1)

@if($purchase_order instanceof \Illuminate\Pagination\LengthAwarePaginator)
@php($no = (($purchase_order->currentPage()-1) * $purchase_order->perPage()) + 1)
@endif

@foreach($purchase_order as $item)

<tr>

<td>{{ $no++ }}</td>

<td class="fw-bold">
{{ $item->no_po  }}
</td>

<td>
{{ $item->tgl_po  }}
</td>
<td>
{{ $item->supplier->nama ?? '-' }}
</td>

<td>
@if($item->status_po == 0)
<span class="badge badge-light-warning">Draft</span>

@elseif($item->status_po == 1)
<span class="badge badge-light-success">Proses</span>

@elseif($item->status_po == 2)
<span class="badge badge-light-danger">Selesai</span>
@elseif($item->status_po == 3)
<span class="badge badge-light-primary">Batal</span>

@endif

</td>

<td class="text-end text-nowrap">

@if($item->deleted_at === null)

<button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
type="button"
data-bs-toggle="dropdown"
aria-expanded="false">
Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
</button>

<div class="menu menu-sub menu-sub-dropdown dropdown-menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4"
data-kt-menu="true">

<div class="menu-item px-3">
<a onclick="info({{ $item->id }})"
href="javascript:void(0)"
class="menu-link px-3">
Ubah
</a>
</div>

<div class="menu-item px-3">
<a onclick="confirm_delete({{ $item->id }})"
href="javascript:void(0)"
class="menu-link px-3">
Hapus
</a>
</div>

</div>

@else

<span class="badge badge-light-danger">
Data Terhapus
</span>

@endif

</td>

</tr>

@endforeach

</tbody>

</table>
</div>

@if($purchase_order instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $purchase_order->links('vendor.pagination.custom') }}
@endif