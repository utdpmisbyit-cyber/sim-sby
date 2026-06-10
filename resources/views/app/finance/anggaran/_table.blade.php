<table class="table table-row-dashed table-hover fs-7 table-sm align-middle">
    <thead>
        <tr class="bg-secondary text-dark fw-bold fs-7 text-uppercase">
            <th>#</th>
            <th>Kode</th>
            <th>Tgl Input</th>
            <th>Tahun Anggaran</th>
            <th>Keterangan</th>
            <th>Nilai Anggaran</th>
            <th>User Input</th>
            <th width="120" class="text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        @forelse($anggaran as $item)
            <tr>
                <td>{{ ($anggaran->currentPage() - 1) * $anggaran->perPage() + $loop->iteration }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->tgl_input?->format('Y-m-d') }}</td>
                <td>{{ $item->tahun_anggaran }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ number_format($item->nilai_anggaran, 0, ',', '.') }}</td>
                <td>{{ $item->petugas?->nama ?? '-' }}</td>

                <td class="text-end text-nowrap">

                    <button class="btn btn-sm btn-primary ps-4 pe-2 py-1"
                        type="button"
                        data-bs-toggle="dropdown">
                        Opsi <i class="ki-duotone ki-down fs-5 ms-1"></i>
                    </button>

                    <div class="dropdown-menu">
                        <div class="dropdown-item">
                            <a onclick="info('{{ $item->id }}')" href="javascript:void(0)">
                                Ubah
                            </a>
                        </div>

                        <div class="dropdown-item">
                            <a onclick="confirm_delete('{{ $item->id }}')" href="javascript:void(0)">
                                Hapus
                            </a>
                        </div>
                    </div>

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
    {{ $anggaran->links() }}
</div>