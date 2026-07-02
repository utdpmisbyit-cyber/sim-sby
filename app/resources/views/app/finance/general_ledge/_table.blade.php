<table class="table table-row-dashed table-hover fs-7 table-sm align-middle"> 
    <thead>
        <tr class="bg-secondary text-dark fw-bold fs-7 text-uppercase">
            <th>#</th>
            <th>Kode</th>
            <th>Program Kerja</th>
            <th>Dokumen</th>
            <th>Ref AN</th>
            <th>Transaksi</th>
            <th>Nominal Debit</th>
            <th>Nominal Kredit</th>
            <th>Tgl</th>
            <th>Akun</th>
            <th>Keterangan</th>
            <th width="120" class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($general_ledge as $item)
            <tr>
                <td>{{ ($general_ledge->currentPage()-1)*$general_ledge->perPage() + $loop->iteration }}</td>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->program_kerja }}</td>
                <td>{{ $item->dokumen ?? '-' }}</td>
                <td>{{ $item->ref_bayar ?? '-' }}</td>
                <td>{{ $item->transaksi_coa }}</td>

                <!-- Nominal Debit -->
                <td>{{ isset($item->nominal_debit) ? number_format($item->nominal_debit, 0, ',', '.') : '-' }}</td>

                <!-- Nominal Kredit -->
                <td>{{ isset($item->nominal_kredit) ? number_format($item->nominal_kredit, 0, ',', '.') : '-' }}</td>

                <td>{{ \Carbon\Carbon::parse($item->tgl)->format('d-m-Y') }}</td>
                <td>{{ $item->nama_akun ?? '-' }}</td>
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
                                Lihat Detail
                            </a>
                        </div>
                        <!-- Hapus dihilangkan -->
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="text-center text-muted py-5">Tidak ada data ditemukan</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">{{ $general_ledge->links() }}</div>