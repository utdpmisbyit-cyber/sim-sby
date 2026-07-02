@if(count($pemeriksaan_dokters) > 0)
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-200 table-sm align-middle">
            <thead>
            <tr class="fs-7 fw-bold bg-secondary text-gray-400 border-bottom-0 text-uppercase">
                <th class="w-30px ps-4 rounded-start">No.</th>
                <th class="min-w-100px">Kode Reg.</th>
                <th class="min-w-150px">Nama Pasien</th>
                <th class="min-w-100px">Golongan Darah</th>
                <th class="min-w-100px">Tanggal, Waktu</th>
                <th class="min-w-80px">Nama Dokter</th>
                <th class="min-w-80px">No.Ruangan</th>
                <th class="min-w-80px text-end pe-4 rounded-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @php($no = 1)
            @foreach($pemeriksaan_dokters as $pemeriksaan_dokter)
                <tr>
                    <td class="fw-semibold text-muted fs-7 ps-4">{{ $no++ }}</td>
                    <td>
                        <span class="fw-bold text-dark fs-7">{{ $pemeriksaan_dokter->kode }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold text-dark fs-7">{{ $pemeriksaan_dokter->donor->nama }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-dark fs-7">{{ $pemeriksaan_dokter->donor->golongan_darah }} {{ $pemeriksaan_dokter->donor->rhesus_caption }}</span>
                        </div>
                    </td>
                    <td>{{ formatDate($pemeriksaan_dokter->created_at) }}, {{ formatTime($pemeriksaan_dokter->created_at) }}</td>
                    <td>{{ $pemeriksaan_dokter->dokter->nama ?? '' }}</td>
                    <td>{{ $pemeriksaan_dokter->nomor_ruangan }}</td>
                    <td class="text-end">
                        <button class="btn btn-icon btn-light-primary btn-sm h-25px w-25px" title="Detail" onclick="select_log({{ $pemeriksaan_dokter->log_donor_id }})">
                            <i class="ki-duotone ki-eye fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(count($pemeriksaan_dokters) == 0)
    <div class="text-center py-10">
        <i class="ki-duotone ki-people fs-3x text-muted mb-3">
            <span class="path1"></span><span class="path2"></span>
            <span class="path3"></span><span class="path4"></span>
            <span class="path5"></span>
        </i>
        <div class="fs-6 text-muted">Belum riwayat pemeriksaan hari ini.</div>
    </div>
@endif

<script>
    $('#total_pemeriksaan_dokter').html('{{ count($pemeriksaan_dokters) }}')
</script>
