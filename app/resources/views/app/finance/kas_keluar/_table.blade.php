<table class="table table-row-dashed table-hover fs-7 table-sm align-middle">
    <thead>
        <tr class="bg-secondary text-dark fw-bold fs-7 text-uppercase">
            <th>#</th>
            <th>Kode</th>
            <th>Program Kerja</th>
            <th>Dokumen</th>
            <th>Ref AN</th>
            <th>Rekening Kas</th>
            <th>Transaksi</th>
            <th>Nominal</th>
            <th>Tgl</th>
            <th>Akun</th>
            <th>Keterangan</th>
            <th width="120" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($kas_keluar as $item)
            <tr>
                <td>{{ ($kas_keluar->currentPage()-1)*$kas_keluar->perPage() + $loop->iteration }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->programKerja->nama ?? $item->program_kerja }}</td>
                <td>{{ $item->dokumen }}</td>
                <td>{{ $item->ref_an }}</td>
                <td>{{ $item->rekning_kas }}</td>
                <td>{{ $item->transaksi }}</td>
                <td>{{ number_format($item->nominal, 0, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}</td>
                <td>{{ $item->coa?->nama_akun ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>

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
                <td colspan="11" class="text-center text-muted py-5">Tidak ada data ditemukan</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-3">{{ $kas_keluar->links() }}</div>