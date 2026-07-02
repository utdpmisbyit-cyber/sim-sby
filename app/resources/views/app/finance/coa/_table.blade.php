<table class="table table-row-dashed table-hover fs-7 table-sm align-middle">
    <thead>
        <tr class="bg-secondary text-dark fw-bold fs-7 text-uppercase">
            <th width="50">#</th>
            <th>Kode COA</th>
            <th>Nama Akun</th>
            <th>Kategori 1</th>
            <th>Kategori 2</th>
            <th>Pos Saldo</th>
            <th>Pos Laporan</th>
            <th width="120" class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse($coa as $item)
            <tr>
                <td>{{ ($coa->currentPage() - 1) * $coa->perPage() + $loop->iteration }}</td>
                <td>{{ $item->kd_coa }}</td>
                <td>{{ $item->nama_akun }}</td>
                <td>{{ $item->kategori_1 }}</td>
                <td>{{ $item->kategori_2 }}</td>
                <td>{{ $item->possaldo }}</td>
                <td>{{ $item->poslaporan }}</td>

                <td class="text-end text-nowrap">

                    @if(!$item->deleted_at)

                        <button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
                            type="button"
                            data-bs-toggle="dropdown">
                            Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                        </button>

                        <div class="dropdown-menu">

                            <div class="dropdown-item">
                                <a onclick="info('{{ $item->kd_coa }}')" href="javascript:void(0)">
                                    Ubah
                                </a>
                            </div>

                            <div class="dropdown-item">
                                <a onclick="confirm_delete('{{ $item->kd_coa }}')" href="javascript:void(0)">
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
                <td colspan="8" class="text-center text-muted py-5">
                    Tidak ada data ditemukan
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $coa->links() }}
</div>