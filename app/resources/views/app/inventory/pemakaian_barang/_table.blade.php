<div class="table-responsive"> 
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th>#</th>
    <th>Kode</th>
    <th>Tanggal</th>
    <th>Barang</th>
    <th>Kode Pengajuan</th>
    <th>Jumlah</th>
    <th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>

@php($no = 1)

@if($pemakaian_barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
@php($no = (($pemakaian_barang->currentPage()-1) * $pemakaian_barang->perPage()) + 1)
@endif

@foreach($pemakaian_barang as $item)

<tr>

<td>{{ $no++ }}</td>

<td>{{ $item->kode }}</td>

<td>{{ date('d-m-Y', strtotime($item->tgl_pemakaian)) }}</td>

{{-- ✅ BARANG DARI RELASI --}}
<td>
    {{ $item->barang->nama ?? '-' }}
</td>

{{-- ✅ PENGAJUAN --}}
<td>
    {{ $item->pengajuanBarang->kode ?? '-' }}
</td>

{{-- ✅ JUMLAH --}}
<td>{{ $item->jumlah_pakai }}</td>

<td class="text-end text-nowrap">

@if($item->deleted_at === null)

<button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
type="button"
data-bs-toggle="dropdown">
Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
</button>

<div class="dropdown-menu">

<div class="dropdown-item">
<a onclick="info({{ $item->id }})" href="javascript:void(0)">
Ubah
</a>
</div>

<div class="dropdown-item">
<a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)">
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

@if($pemakaian_barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $pemakaian_barang->links('vendor.pagination.custom') }}
@endif