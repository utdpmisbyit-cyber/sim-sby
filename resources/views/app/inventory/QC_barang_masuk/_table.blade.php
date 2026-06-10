<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">

<thead>
<tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
<th>#</th>
<th>No QC</th>
<th>Tanggal QC</th>
<th>No Faktur</th>
<th>Barang & Expired </th>
<th>Status</th>
<th class="text-center">Opsi</th>
</tr>
</thead>

<tbody>
@foreach($pembelian_qc_masuk as $item)

    @foreach($item->qcDetailLot as $lot)

    @php
        $today = \Carbon\Carbon::today();
        $exp   = $lot->tgl_exp_date ? \Carbon\Carbon::parse($lot->tgl_exp_date) : null;

        $diff = $exp ? $today->diffInDays($exp, false) : null;

        $status_exp = 'aman';
        if ($exp) {
            if ($diff < 0) {
                $status_exp = 'expired';
            } elseif ($diff <= 30) {
                $status_exp = 'warning';
            }
        }
    @endphp

    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->no_trans_qc }}</td>
        <td>{{ $item->tgl_qc }}</td>
        <td>{{ $item->no_faktur }}</td>

        {{-- BARANG + EXP --}}
        <td>
            <div class="p-2 rounded 
                @if($status_exp == 'expired') bg-danger text-white
                @elseif($status_exp == 'warning') bg-warning
                @else bg-light
                @endif
            ">

                <strong>{{ $lot->barang->nama ?? '-' }}</strong><br>

                Exp: {{ $lot->tgl_exp_date ?? '-' }}

                @if($status_exp == 'expired')
                    <div class="text-danger fw-bold">
                        ⚠ EXPIRED
                    </div>
                @elseif($status_exp == 'warning')
                    <div class="text-dark fw-bold">
                        ⚠ Mendekati Expired ({{ $diff }} hari)
                    </div>
                @endif

            </div>
        </td>

        {{-- STATUS QC --}}
        <td>
            @if($item->status_qc == 1)
                <span class="badge bg-success">LULUS</span>
            @elseif($item->status_qc == 2)
                <span class="badge bg-danger">REJECT</span>
            @else
                <span class="badge bg-warning">PENDING</span>
            @endif
        </td>

        <td class="text-end">
            <button class="btn btn-sm btn-primary" onclick="info('{{ $item->id }}')">
                Detail
            </button>
        </td>
    </tr>

    @endforeach

@endforeach
</tbody>
</table>
</div>

@if($pembelian_qc_masuk instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $pembelian_qc_masuk->links('vendor.pagination.custom') }}
@endif