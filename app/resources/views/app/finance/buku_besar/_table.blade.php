@if($buku_besar->isEmpty())
    <div class="alert alert-warning text-center py-6">
        <i class="fa fa-info-circle"></i> Tidak ada data ditemukan.
    </div>
@else
<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm align-middle">
        <thead class="bg-primary text-white fw-bold">
            <tr>
                <th width="5%">#</th>
                <th>Kode</th>
                <th>Nama Akun</th>
                <th width="10%">Debit</th>
                <th width="10%">Kredit</th>
                <th width="10%">Saldo</th>
                <th width="10%">Posisi</th>
                <th width="13%">Tanggal</th>
            </tr>
        </thead>

        <tbody>
            @php
                $running_saldo = 0;
                $total_debit = 0;
                $total_kredit = 0;
            @endphp

            @foreach($buku_besar as $i => $row)

                @php
                    $total_debit  += $row->debet ?? 0;
                    $total_kredit += $row->kredit ?? 0;

                    $running_saldo += ($row->debet ?? 0) - ($row->kredit ?? 0);

                    $badge = $row->debet > 0
                        ? '<span class="badge bg-success">DEBIT</span>'
                        : '<span class="badge bg-danger">KREDIT</span>';
                @endphp

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="fw-bold">{{ $row->kode }}</td>
                    <td>
                        {{ $row->nama_akun ?? ($row->coa->nama_akun ?? '-') }}
                        <br>
                        {!! $badge !!}
                    </td>

                    <td class="text-end">{{ number_format($row->debet, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($row->kredit, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($running_saldo, 0, ',', '.') }}</td>
                    <td>{{ $row->pos_laporan ?? '-' }}</td>
                    <td>{{ $row->created_at?->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot class="bg-light fw-bold">
            <tr>
                <td colspan="3" class="text-end">TOTAL :</td>
                <td class="text-end text-success">{{ number_format($total_debit, 0, ',', '.') }}</td>
                <td class="text-end text-danger">{{ number_format($total_kredit, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($running_saldo, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif