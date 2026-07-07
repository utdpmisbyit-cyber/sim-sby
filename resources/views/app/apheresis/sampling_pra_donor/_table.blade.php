<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th style="width: 40px;">#</th>
            <th>No Transaksi</th>
            <th>No Donor</th>
            <th>Nama Donor</th>
            <th>Golongan Darah</th>
            <th>Petugas</th>
            <th>Server Date</th>
            <th class="text-center">Status</th>
            <th class="text-center" style="width: 110px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $i => $row)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td class="fw-semibold">{{ $row->no_transaksi }}</td>
                <td>{{ $row->no_donor ?? '-' }}</td>
                <td>{{ $row->nama_donor }}</td>
                <td>{{ $row->golongan_darah ?? '-' }}{{ $row->rhesus === 'negatif' ? ' (-)' : '' }}</td>
                <td>{{ $row->petugas->name ?? '-' }}</td>
                <td>{{ optional($row->server_date)->format('d M Y H:i') }}</td>
                <td class="text-center">
                    @if($row->status_lulus === 'lulus')
                        <span class="badge bg-success">Lulus</span>
                    @elseif($row->status_lulus === 'tidak_lulus')
                        <span class="badge bg-danger">Tidak Lulus</span>
                    @else
                        <span class="badge bg-secondary">-</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="{{ route('apheresis.sampling_pra_donor.edit', $row->id) }}"
                       class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="fa fa-pen"></i>
                    </a>
                    <form action="{{ route('apheresis.sampling_pra_donor.destroy', $row->id) }}"
                          method="POST" class="d-inline" onsubmit="return confirm('Hapus data ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center text-muted py-4">Belum ada data sampling pra donor.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="px-3 py-2">
    {{ $items->links() }}
</div>