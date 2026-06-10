<div class="table-responsive">
    <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
        <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th class="w-100px">Kode</th>
            <th>Nama</th>
            <th>Tgl. Lahir</th>
            <th>Tgl Daftar</th>
            <th>Tempat Donor</th>
            <th>Jenis Kelamin</th>
            <th class="text-center">Gol. Darah</th>
            <th>No. Telp</th>
            <th>Wilayah</th>
            <th>Pekerjaan</th>
            <th class="text-center">Cekal</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
        </thead>
        <tbody>
        @php($no = 1)
        @if($donors instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @php($no = (($donors->currentPage()-1) * $donors->perPage()) + 1)
        @endif
        @foreach($donors as $item)
            <tr>
                <td class="ps-4">{{ $no++ }}</td>
                <td class="text-nowrap fw-bold">{{ $item->kode }}</td>
                <td class="text-nowrap">{{ $item->nama }}</td>
                <td class="text-nowrap">{{ $item->tanggal_lahir?->format('d/m/Y') ?? '-' }}</td>
                <td class="text-nowrap">
                    {{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}
                </td>

                <td>
                    {{ $item->asalDarah->nama ?? $item->nama_asal_darah ?? '-' }}
                </td>
                <td>{{ $item->jenis_kelamin ?? '-' }}</td>
                <td class="text-nowrap text-center">
                    @if($item->golongan_darah)
                        <span class="badge badge-danger fw-bold">
                            {{ $item->golongan_darah }}{{ $item->rhesus }}
                        </span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>{{ $item->no_telp ?? '-' }}</td>
                <td>{{ $item->wilayah->nama ?? '-' }}</td>
                <td>{{ $item->pekerjaan->nama ?? '-' }}</td>
                <td class="text-center">
                    @if($item->cekal == 1)
                        <span class="badge badge-danger fw-bold">Dicekal</span>
                    @else
                        <span class="badge badge-success fw-bold">Tidak</span>
                    @endif
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
                        <button class="btn btn-sm btn-primary px-2 py-1" type="button" onclick="confirm_restore({{ $item->id }})">
                            Batal Hapus
                        </button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if($donors instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $donors->links('vendor.pagination.custom') }}
@endif
