<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
    <th>#</th>
    <th>Kode Permintaan</th>
    <th>Kode Pengajuan</th>
    <th>Barang</th>
    <th>Cabang</th>
    <th>Jml Minta</th>
    <th>Jml Acc</th>
    <th>Tgl Terima</th>
    <th>Status</th>
    <th class="text-center">Opsi</th>
</tr>
</thead>
<tbody>
@php($no = 1)
@if($permintaans instanceof \Illuminate\Pagination\LengthAwarePaginator)
    @php($no = (($permintaans->currentPage()-1) * $permintaans->perPage()) + 1)
@endif

@foreach($permintaans as $item)
<tr>
    <td>{{ $no++ }}</td>
    <td class="fw-bold">{{ $item->kode }}</td>
    <td>{{ $item->pengajuanBarang->kode ?? '-' }}</td>
    <td>{{ $item->pengajuanBarang->nama_barang ?? '-' }}</td>
    <td>{{ $item->pengajuanBarang->cabang->nama ?? '-' }}</td>
    <td>{{ $item->pengajuanBarang->jml_minta ?? '-' }}</td>
    <td>{{ $item->jml_acc ?? '-' }}</td>
    <td>{{ $item->tgl_terima }}</td>
    <td>
        @php($badge = ['diterima'=>'warning','diproses'=>'info','dikirim'=>'primary','selesai'=>'success','ditolak'=>'danger'])
        <span class="badge badge-light-{{ $badge[$item->status] ?? 'secondary' }}">{{ ucfirst($item->status) }}</span>
    </td>
    <td class="text-end text-nowrap">
        @if($item->deleted_at === null)
        <button class="btn btn-sm btn-primary ps-4 pe-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
        </button>
        <div class="menu menu-sub menu-sub-dropdown dropdown-menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-auto py-4" data-kt-menu="true">
            <div class="menu-item px-3"><a onclick="info({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Ubah</a></div>
            <div class="menu-item px-3"><a onclick="confirm_delete({{ $item->id }})" href="javascript:void(0)" class="menu-link px-3">Hapus</a></div>
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

@if($permintaans instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $permintaans->links('vendor.pagination.custom') }}
@endif