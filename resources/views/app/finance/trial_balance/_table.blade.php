@php
    // Pastikan trial_balance sudah collection
    $grouped = $trial_balance->groupBy('kode');
@endphp

{{-- Loop Akun --}}
@foreach($grouped as $kode => $items)
    @php
        $first      = $items->first();
        $saldoAbs   = abs(($first->sa_debet ?? 0) - ($first->sa_kredit ?? 0));
        $posSaldo   = $first->pos_saldo ?? 'DEBET';
        $totalDebet = $items->sum('debet');
        $totalKredit= $items->sum('kredit');
    @endphp

    <div class="mb-3 rounded overflow-hidden border" style="box-shadow:0 1px 4px rgba(0,0,0,.08)">
        <div class="d-flex align-items-center gap-2 px-3 py-2 akun-header"
             style="background:#1565C0;cursor:pointer;user-select:none"
             onclick="toggleAkun('akun-{{ $loop->index }}', 'chev-{{ $loop->index }}')">
            <span id="chev-{{ $loop->index }}"
                  style="color:#fff;font-size:13px;transition:transform .25s;display:inline-block">▼</span>
            <span class="text-white fw-bold" style="font-size:13px">
                {{ $kode }} - {{ $first->nama_akun }}
            </span>
            <span class="text-white" style="font-size:12px;opacity:.85">
                Saldo Akhir: Rp {{ number_format($saldoAbs, 0, ',', '.') }}
            </span>
            <span class="badge {{ $posSaldo === 'DEBET' ? 'bg-success' : 'bg-danger' }}"
                  style="font-size:10px">
                {{ $posSaldo }}
            </span>
            @if($first->kategori1)
                <small class="ms-auto" style="color:rgba(255,255,255,.65)">
                    {{ $first->kategori1 }}
                </small>
            @endif
        </div>

        <div id="akun-{{ $loop->index }}">
            <table class="table table-bordered table-sm align-middle mb-0" style="font-size:12px">
                <thead>
                    <tr class="bg-light text-uppercase fw-semibold" style="font-size:11px">
                        <th style="min-width:90px">Tanggal</th>
                        <th>Keterangan</th>
                        <th>Referensi</th>
                        <th class="text-end" style="min-width:120px">Debit</th>
                        <th class="text-end" style="min-width:120px">Kredit</th>
                        <th class="text-end" style="min-width:120px">Saldo</th>
                        <th class="text-center" style="min-width:75px">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                 @php $running = ($first->sa_debet ?? 0) - ($first->sa_kredit ?? 0); @endphp
                        @foreach($items as $item)
                            @php
                                $d = $item->debet  ?? 0;
                                $k = $item->kredit ?? 0;

                                // Tentukan tipe transaksi
                                $rowTipe = $d > 0 ? 'DEBET' : ($k > 0 ? 'KREDIT' : '-');

                                // Update saldo running
                                $running += ($d - $k);
                            @endphp
                            <tr>
                                <td>{{ $item->created_at?->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $item->kategori2 ?? '-' }}</td>
                                <td style="font-size:11px;color:#888">REF-{{ $item->kode }}</td>
                                <td class="text-end {{ $d > 0 ? 'text-success' : 'text-muted' }}">
                                    Rp {{ number_format($d, 0, ',', '.') }}
                                </td>
                                <td class="text-end {{ $k > 0 ? 'text-danger' : 'text-muted' }}">
                                    Rp {{ number_format($k, 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format(abs($running), 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $rowTipe === 'KREDIT' ? 'bg-danger' : ($rowTipe === 'DEBET' ? 'bg-success' : 'bg-secondary') }}"
                                        style="font-size:10px">
                                        {{ $rowTipe }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light fw-semibold" style="font-size:11px">
                        <td colspan="3" class="text-end pe-3">SUBTOTAL</td>
                        <td class="text-end text-success">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                        <td class="text-end text-danger">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endforeach

{{-- ===== NERACA ===== --}}
@php $grandD = $grandK = 0; @endphp
<div class="card mt-4" style="border-radius:10px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.08)">
    <div class="card-header fw-bold fs-6">📋 Neraca</div>
    <div class="card-body p-0">
        <table class="table table-bordered table-sm align-middle mb-0" style="font-size:12px">
            <thead>
                <tr class="bg-light fw-semibold text-uppercase" style="font-size:11px">
                    <th style="width:120px">Kode COA</th>
                    <th>Nama Akun</th>
                    <th class="text-center" style="width:110px">Posisi Saldo</th>
                    <th class="text-end" style="width:160px">Debit</th>
                    <th class="text-end" style="width:160px">Kredit</th>
                </tr>
            </thead>
            <tbody>
               @foreach($grouped as $kode => $items)
                @php
                    $first  = $items->first();
                    $pos    = $first->pos_saldo ?? 'DEBET';

                    // HITUNG SALDO AKHIR
                    $sa   = ($first->sa_debet ?? 0) - ($first->sa_kredit ?? 0);
                    $mut  = $items->sum('debet') - $items->sum('kredit');
                    $saldoAkhir = $sa + $mut;

                    // MASUKKAN KE NERACA
                    if ($saldoAkhir >= 0) {
                        $nd = $saldoAkhir;
                        $nk = 0;
                    } else {
                        $nd = 0;
                        $nk = abs($saldoAkhir);
                    }

                    $grandD += $nd;
                    $grandK += $nk;
                @endphp

                <tr>
                    <td class="fw-semibold">{{ $kode }}</td>
                    <td>{{ $first->nama_akun ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $saldoAkhir >= 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $saldoAkhir >= 0 ? 'DEBET' : 'KREDIT' }}
                        </span>
                    </td>
                    <td class="text-end text-success">
                        {{ $nd > 0 ? 'Rp '.number_format($nd, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-end text-danger">
                        {{ $nk > 0 ? 'Rp '.number_format($nk, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#1565C0;" class="text-white fw-bold">
                    <td colspan="3" class="text-end pe-3" style="font-size:12px">TOTAL</td>
                    <td class="text-end">Rp {{ number_format($grandD, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($grandK, 0, ',', '.') }}</td>
                </tr>
                @php $selisih = abs($grandD - $grandK); $seimbang = $grandD === $grandK; @endphp
                <tr>
                    <td colspan="3" class="text-end pe-3 fw-semibold" style="font-size:12px">STATUS</td>
                    <td colspan="2" class="text-end fw-bold {{ $seimbang ? 'text-success' : 'text-danger' }}">
                        @if($seimbang)
                            ✓ SEIMBANG
                        @else
                            ✗ TIDAK SEIMBANG: Rp {{ number_format($selisih, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ===== JAVASCRIPT ACCORDION ===== --}}
<script>
function toggleAkun(bodyId, chevId) {
    const body = document.getElementById(bodyId);
    const chev = document.getElementById(chevId);
    const isOpen = body.style.display !== 'none';

    body.style.display = isOpen ? 'none' : '';
    body.style.transition = 'all .25s';
    chev.style.transform  = isOpen ? 'rotate(-90deg)' : 'rotate(0deg)';
    chev.textContent      = isOpen ? '▶' : '▼';
}
</script>