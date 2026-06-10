<table class="table table-row-dashed table-hover fs-7 table-sm align-middle">
    <thead>
        <tr class="bg-secondary text-dark fw-bold fs-7 text-uppercase">
            <th width="50">#</th>
            <th>Kode</th>
            <th>Nama Kelompok</th>
            <th width="120" class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse($kelompok_barang as $item)
            <tr>
                <td>{{ ($kelompok_barang->currentPage() - 1) * $kelompok_barang->perPage() + $loop->iteration }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->nama }}</td>
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
                                <a onclick="info('{{ $item->id }}')" href="javascript:void(0)">
                                    Ubah
                                </a>
                            </div>

                            {{-- DELETE --}}
                            <div class="dropdown-item">
                                <a onclick="confirm_delete('{{ $item->id }}')" href="javascript:void(0)">
                                    Hapus
                                </a>
                            </div>

                        </div>

                    @else
                        <span class="badge badge-light-danger">Data Terhapus</span>
                    @endif

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-5">Tidak ada data ditemukan</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
   {{ $kelompok_barang->links() }}
</div>