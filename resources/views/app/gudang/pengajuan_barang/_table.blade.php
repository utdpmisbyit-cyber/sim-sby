<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th>#</th>
    <th>Kode</th>
    <th>Barang</th>
    <th>Tanggal</th>
    <th>Jenis Permintaan Barang </th>
    <th>Bagian Pengajuan</th>
    <th>Jumlah Minta</th>
    <th>Status Logistik</th>
    <!-- <th>Status</th> -->
    <th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>

@php($no = 1)

@if($pengajuans instanceof \Illuminate\Pagination\LengthAwarePaginator)
@php($no = (($pengajuans->currentPage()-1) * $pengajuans->perPage()) + 1)
@endif

@foreach($pengajuans as $item)

<tr>

<td>{{ $no++ }}</td>

<td class="fw-bold">
{{ $item->kode }}
</td>
<td>
{{ $item->nama_barang ?? '-' }}
</td>
<td>
{{ $item->tgl_pengajuan }}
</td>

<td>
{{ $item->jenis_pengajuan }}
</td>
<td>
{{ $item->bagian->nama ?? '-' }}
</td>
<td>
{{ ($item->jml_minta ?? 0).' '.($item->satuan ?? '') }}
</td>
<td>
    @if($item->permintaanLogistik)
        @php($badge = ['diterima'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success','ditolak'=>'danger'])
        <span class="badge badge-light-{{ $badge[$item->permintaanLogistik->status] ?? 'secondary' }}">
            {{ ucfirst($item->permintaanLogistik->status) }}
        </span>
    @else
        <span class="badge badge-light-secondary">Belum Diproses</span>
    @endif
</td>
<!-- <td>
@if($item->status == 0)
<span class="badge badge-light-warning">Draft</span>

@elseif($item->status == 1)
<span class="badge badge-light-success">Proses</span>

@elseif($item->status == 2)
<span class="badge badge-light-danger">Selesai</span>
@elseif($item->status == 3)
<span class="badge badge-light-primary">Batal</span>

@endif -->

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

@if($pengajuans instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $pengajuans->links('vendor.pagination.custom') }}
@endif