<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th style="width: 40px;">#</th>
            <th>No Transaksi</th>
            <th>No Donor</th>
            <th>Nama Donor</th>
            <th>Golongan Darah</th>
            <th>Mesin</th>
            <th>Server Date</th>
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
                <td>{{ $row->type_mesin ?? '-' }} {{ $row->no_mesin }}</td>
                <td>{{ optional($row->server_date)->format('d M Y H:i') }}</td>
                <td class="text-center">
                    <a href="{{ route('apheresis.pengambilan_darah.edit', $row->id) }}"
                       class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="fa fa-pen"></i>
                    </a>
                    <form action="{{ route('apheresis.pengambilan_darah.destroy', $row->id) }}"
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
                <td colspan="8" class="text-center text-muted py-4">Belum ada data lembar kerja pengambilan darah.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="px-3 py-2">
    {{ $items->links() }}
</div>