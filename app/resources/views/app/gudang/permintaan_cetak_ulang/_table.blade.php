<div class="pcu-table-wrap">
<table class="pcu-table">

<thead>
<tr>
    <th>No Surat</th>
    <th>Pemohon</th>
    <th>Bagian</th>
    <th>No Barcode</th>
    <th>Jml</th>
    <th>Status</th>
    <th class="text-end">Aksi</th>
</tr>
</thead>

<tbody>

@if($permintaans->count() === 0)
<tr>
    <td colspan="7">
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            Belum ada permintaan cetak ulang.
        </div>
    </td>
</tr>
@endif

@foreach($permintaans as $item)
@php
    $initial = strtoupper(substr($item->nama_pemohon ?? '-', 0, 1));
    $statusIcon = [
        'diajukan'  => 'fa-hourglass-half',
        'disetujui' => 'fa-thumbs-up',
        'ditolak'   => 'fa-ban',
        'selesai'   => 'fa-flag-checkered',
    ][$item->status] ?? 'fa-circle';
@endphp
<tr>

<td>
    @if($item->deleted_at !== null)
        <span class="badge badge-light-danger">Terhapus</span>
    @endif
    <div class="no-surat">{{ $item->no_surat }}</div>
    <div class="sub">{{ \Illuminate\Support\Carbon::parse($item->tanggal_permohonan)->translatedFormat('d M Y') }}</div>
</td>

<td>
    <div class="pemohon-cell">
        <div class="avatar-circle">{{ $initial }}</div>
        <div>
            <div class="fw-bold">{{ $item->nama_pemohon }}</div>
            <div class="sub">{{ $item->jabatan_pemohon ?? '-' }}</div>
        </div>
    </div>
</td>

<td>{{ $item->bagian->nama ?? '-' }}</td>

<td>
    <span class="badge-kode">{{ $item->pendataanKantong->kode ?? '-' }}</span>
    <div class="sub">{{ $item->pendataanKantong->jenis_kantong ?? '' }}</div>
</td>

<td class="text-center">{{ $item->jumlah_cetak }}</td>

<td>
    <span class="badge-status {{ $item->status }}">
        <i class="fas {{ $statusIcon }}"></i> {{ ucfirst($item->status) }}
    </span>
</td>

<td>
    <div class="action-icons">

        <button class="icon-btn view" title="Lihat Detail" onclick="info({{ $item->id }})">
            <i class="fas fa-eye"></i>
        </button>

        @if($item->deleted_at === null)

            @if($item->status === 'diajukan')
            <button class="icon-btn approve" title="Setujui" onclick="approve_permintaan({{ $item->id }})">
                <i class="fas fa-check"></i>
            </button>
            <button class="icon-btn reject" title="Tolak" onclick="reject_permintaan({{ $item->id }})">
                <i class="fas fa-times"></i>
            </button>
            @endif

            @if($item->status === 'disetujui')
            <button class="icon-btn done" title="Tandai Selesai" onclick="selesaikan_permintaan({{ $item->id }})">
                <i class="fas fa-flag-checkered"></i>
            </button>
            @endif

            <button class="icon-btn del" title="Hapus" onclick="confirm_delete({{ $item->id }})">
                <i class="fas fa-trash"></i>
            </button>

        @endif

    </div>
</td>

</tr>
@endforeach

</tbody>

</table>
</div>

<div class="px-4 py-3">
@if($permintaans instanceof \Illuminate\Pagination\LengthAwarePaginator)
{{ $permintaans->links('vendor.pagination.custom') }}
@endif
</div>