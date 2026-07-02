
@if(isset($pengajuan_pending) && $pengajuan_pending->count() > 0)

<div class="alert alert-warning d-flex align-items-center p-4 mb-5 border border-warning rounded">
    <i class="ki-duotone ki-information-5 fs-2hx text-warning me-3">
        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
    </i>
    <div>
        Ada <strong>{{ $pengajuan_pending->count() }} permintaan</strong> menunggu diproses.
        <a href="inventory/pengajuan_barang" class="fw-bold text-warning ms-1">Lihat pengajuan →</a>
    </div>
</div>

{{-- TABEL PENGAJUAN PENDING --}}
<div class="card border border-warning mb-6" id="section-pengajuan-pending">
    <div class="card-header bg-light-warning border-bottom border-warning min-h-50px">
        <h6 class="card-title text-warning fw-bold mb-0">
            <i class="ki-duotone ki-time fs-4 text-warning me-2">
                <span class="path1"></span><span class="path2"></span>
            </i>
            Pengajuan Pending ({{ $pengajuan_pending->count() }})
        </h6>
    </div>
    <div class="card-body p-0">
        
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-7 table-sm mb-0">
                <thead>
                    <tr class="text-start bg-light-warning text-dark fw-bold fs-7 text-uppercase border-bottom-0">
                        <th class="ps-4 w-10px">#</th>
                        <th>Kode Pengajuan</th>
                        <th>Barang</th>
                        <th>Pemohon</th>
                        <th>Tgl Pengajuan</th>
                        <th>Jumlah Minta</th>
                        <th>Stok Tersedia</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan_pending as $i => $pj)
                    @php
                        $barang     = $pj->barang;
                        $stok       = $barang->stok ?? 0;
                        $jmlMinta   = $pj->jml_minta ?? 0;
                        $stokKurang = $stok < $jmlMinta;
                    @endphp
                    <tr class="{{ $stokKurang ? 'bg-light-danger' : '' }}">
                        <td class="ps-4">{{ $i + 1 }}</td>

                        {{-- Kode --}}
                        <td class="fw-bold">{{ $pj->kode }}</td>

                        {{-- Barang --}}
                        <td>
                            <span class="fw-bold d-block">{{ $pj->nama_barang ?? ($barang->nama ?? '-') }}</span>
                            @if($stokKurang)
                                <span class="badge badge-light-danger fs-8 mt-1">
                                    <i class="ki-duotone ki-shield-cross fs-8 me-1">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                    Stok Tidak Mencukupi
                                </span>
                            @endif
                        </td>

                        {{-- Pemohon --}}
                        <td>{{ $pj->petugas->nama ?? $pj->user_input ?? '-' }}</td>

                        {{-- Tanggal --}}
                        <td>{{ $pj->tgl_pengajuan ?? \Carbon\Carbon::parse($pj->created_at)->format('d M Y') }}</td>

                        {{-- Jumlah Minta --}}
                        <td>
                            <span class="fw-bold {{ $stokKurang ? 'text-danger' : '' }}">
                                {{ $jmlMinta }} {{ $pj->satuan ?? '' }}
                            </span>
                        </td>

                        {{-- Stok Tersedia --}}
                        <td>
                            <span class="fw-bold {{ $stokKurang ? 'text-danger' : 'text-success' }}">
                                {{ $stok }} {{ $pj->satuan ?? '' }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="text-center pe-4">
                            <button class="btn btn-sm btn-danger px-4 py-1"
                                onclick="proses_pengeluaran({{ $pj->id }}, {{ $stokKurang ? 'true' : 'false' }})">
                                Proses
                                <i class="ki-duotone ki-arrow-right fs-6 ms-1">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif
{{-- ============================================================ --}}
{{-- TABEL PENGELUARAN BARANG (existing) --}}
{{-- ============================================================ --}}
<div class="d-flex justify-content-end mb-5">
    <a href="javascript:void(0)" onclick="cetakLaporan()" 
        class="btn btn-success btn-sm px-4 py-2">
        <i class="ki-duotone ki-printer fs-4 me-2"></i>
        Cetak Laporan
    </a>
</div>
<div class="table-responsive">
<table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
    <thead>
        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0">
            <th class="w-10px ps-4 rounded-start">#</th>
            <th>Barang</th>
            <th>Tgl Keluar</th>
            <th>Bagian</th>
            <th>No Lot</th>
            <th>Tgl Expired</th>
            <th>Qty Keluar</th>
            <th class="text-center w-50px pe-4 rounded-end">Opsi</th>
        </tr>
    </thead>
   <tbody>
@php
    $no = 1;
@endphp

@if($pengeluaran_barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
    @php
        $no = (($pengeluaran_barang->currentPage()-1) * $pengeluaran_barang->perPage()) + 1;
    @endphp
@endif

@foreach($pengeluaran_barang as $item)
@php
    $exp = !empty($item->tgl_expired) ? \Carbon\Carbon::parse($item->tgl_expired) : null;
    $isExpired = $exp && $exp->isPast();
    $isWarning = $exp && !$isExpired && $exp->diffInDays(now()) <= 7;
@endphp

<tr>

    <td class="ps-4">{{ $no++ }}</td>

    <td>
        {{ $item->nama_barang ?? ($item->barang->nama ?? '-') }}
    </td>

    <td>
        {{ $item->tgl_keluar ? \Carbon\Carbon::parse($item->tgl_keluar)->format('d-m-Y') : '-' }}
    </td>

    <td>
        {{ $item->bagian->nama ?? '-' }}
    </td>

    <td>
        <span class="badge badge-light-primary">
            {{ $item->no_lot ?? '-' }}
        </span>
    </td>

    <td>
        @if($exp)
            <div class="p-2 rounded {{ $isExpired ? 'bg-light-danger' : ($isWarning ? 'bg-light-warning' : 'bg-light') }}">

                <div class="fw-bold {{ $isExpired ? 'text-danger' : ($isWarning ? 'text-warning' : '') }}">
                    {{ $exp->format('d/m/Y') }}
                </div>

                @if($isExpired)
                    <div class="text-danger fw-bold">
                        ⚠ EXPIRED
                    </div>
                @elseif($isWarning)
                    <div class="text-warning fw-bold">
                        ⚠ Hampir Expired
                    </div>
                @endif

            </div>
        @else
            -
        @endif
    </td>

    <td>
        <span class="fw-bold text-danger">
            {{ $item->qty_keluar ?? 0 }} {{ $item->satuan ?? '' }}
        </span>
    </td>

    <td class="text-end text-nowrap">
        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="dropdown">
            Opsi
        </button>
        <div class="dropdown-menu">
            <a onclick="info('{{ $item->no_trans_keluar ?? ''}}')" href="javascript:void(0)" class="dropdown-item">Ubah</a>
            <a onclick="confirm_delete('{{ $item->no_trans_keluar }}')" href="javascript:void(0)" class="dropdown-item">Hapus</a>
        </div>
    </td>

</tr>
@endforeach

</tbody>
</table>
</div>

@if($pengeluaran_barang instanceof \Illuminate\Pagination\LengthAwarePaginator)
    {{ $pengeluaran_barang->links('vendor.pagination.custom') }}
@endif

<script>
function cetakLaporan() {
    let params = new URLSearchParams(window.location.search);

    // Tambahkan parameter pdf
    params.set("pdf", 1);

    // Buka di tab baru
    window.open(`/inventory/laporan/pengeluaran_barang?${params.toString()}`, "_blank");
}
</script>
<script>
function proses_pengeluaran(id, stokKurang) {
    if (stokKurang) {
        Swal.fire({
            title: 'Stok Tidak Mencukupi!',
            html: 'Stok barang tidak mencukupi permintaan.<br>Apakah tetap ingin memproses pengeluaran?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tetap Proses',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f1416c',
            cancelButtonColor: '#7e8299',
        }).then((result) => {
            if (result.isConfirmed) submitProses(id, true);
        });
    } else {
        Swal.fire({
            title: 'Konfirmasi Proses',
            text: 'Proses pengeluaran barang ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#f1416c',
        }).then((result) => {
            if (result.isConfirmed) submitProses(id, false);
        });
    }
}

function submitProses(id, paksa = false) {
    $.ajax({
        url: `/inventory/pengajuan_barang/${id}/proses`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            paksa: paksa ? 1 : 0
        },
        success: function(response) {
            Swal.fire('Berhasil!', response.message, 'success')
                .then(() => location.reload());
        },
        error: function(xhr) {
            const res = xhr.responseJSON;
            Swal.fire('Gagal!', res?.message ?? 'Terjadi kesalahan.', 'error');
        }
    });
}
</script>