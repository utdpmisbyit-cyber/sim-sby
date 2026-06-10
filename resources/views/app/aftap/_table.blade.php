@if(count($aftaps) > 0)
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-200 table-sm align-middle">
            <thead>
            <tr class="fs-7 fw-bold bg-secondary text-gray-400 border-bottom-0 text-uppercase">
                <th class="w-30px ps-4 rounded-start">No.</th>
                <th class="min-w-100px">Kode Reg.</th>
                <th class="min-w-150px">Nama Pasien</th>
                <th class="min-w-100px">Golongan Darah</th>
                <th class="min-w-100px">Tanggal, Waktu</th>
                <th class="min-w-80px">Nama Petugas</th>
                <th class="min-w-80px text-end pe-4 rounded-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @php($no = 1)
            @foreach($aftaps as $aftap)
                <tr>
                    <td class="fw-semibold text-muted fs-7 ps-4">{{ $no++ }}</td>
                    <td>
                        <span class="fw-bold text-dark fs-7">{{ $aftap->kode }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold text-dark fs-7">{{ $aftap->donor->nama }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-dark fs-7">{{ $aftap->donor->golongan_darah }} {{ $aftap->donor->rhesus_caption }}</span>
                        </div>
                    </td>
                    <td>{{ formatDate($aftap->created_at) }}, {{ formatTime($aftap->created_at) }}</td>
                    <td>{{ $aftap->dokter->nama ?? '' }}</td>
                    <td class="text-end">
                        <button class="btn btn-icon btn-light-primary btn-sm h-25px w-25px" title="Detail" onclick="select_log({{ $aftap->log_donor_id }})">
                            <i class="ki-duotone ki-eye fs-6"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(count($aftaps) == 0)
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
    $('#total_aftap').html('{{ count($aftaps) }}')
</script>
