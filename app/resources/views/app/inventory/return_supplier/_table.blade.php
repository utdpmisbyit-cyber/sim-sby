<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th>#</th>
    <th>No Retur</th>
    <th>Tanggal</th>
    <th>Supplier</th>
    <th>Jenis Retur</th>
    <th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>

@php($no = 1)

@if($return_supplier instanceof \Illuminate\Pagination\LengthAwarePaginator)
@php($no = (($return_supplier->currentPage()-1) * $return_supplier->perPage()) + 1)
@endif

@foreach($return_supplier as $item)

<tr>
<td>{{ $no++ }}</td>

<td class="fw-bold">
    {{ $item->no_trans_retur }}
</td>

<td>
    {{ $item->tgl_retur }}
</td>

<td>
    {{ $item->supplier->nama ?? '-' }}
</td>

<td>
    {{ $item->jenis_retur }}
</td>

<td class="text-end text-nowrap">

@if($item->deleted_at === null)

<button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
type="button"
data-bs-toggle="dropdown">
Opsi
</button>

<div class="dropdown-menu">

<a onclick="info('{{ $item->id }}')"
href="javascript:void(0)"
class="dropdown-item">
Ubah
</a>

<a onclick="confirm_delete('{{ $item->id }}')"
href="javascript:void(0)"
class="dropdown-item">
Hapus
</a>

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

@if($return_supplier instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $return_supplier->links('vendor.pagination.custom') }}
@endif