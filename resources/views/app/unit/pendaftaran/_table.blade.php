<div class="d-flex gap-3 mb-5 flex-wrap">
    @foreach($steps as $step)
        <div class="badge badge-light-primary d-flex gap-1 fs-8 px-3 py-2">
            <span class="bullet bullet-dot bg-warning me-1"></span>
            <span>{{ $count_steps[$step] }}</span> {{ $step }}
        </div>
    @endforeach
</div>
@if(count($log_donors) > 0)
    <div class="table-responsive">
        <table class="table table-row-dashed table-row-gray-200 table-sm align-middle">
            <thead>
            <tr class="fs-7 fw-bold bg-secondary text-gray-400 border-bottom-0 text-uppercase">
                <th class="w-30px ps-4 rounded-start">No.</th>
                <th class="min-w-100px">Kode Reg.</th>
                <th class="min-w-100px">Kode Donor</th>
                <th class="min-w-150px">Nama Pasien</th>
                <th class="min-w-100px">Tanggal, Waktu Daftar</th>
                <th class="min-w-80px">Petugas</th>
                <th class="min-w-80px">Status</th>
                <th class="min-w-80px text-end pe-4 rounded-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @php($no = 1)
            @foreach($log_donors as $log_donor)
                <tr>
                    <td class="fw-semibold text-muted fs-7 ps-4">{{ $no++ }}</td>
                    <td>
                        <span class="fw-bold text-dark fs-7">{{ $log_donor->kode }}</span>
                    </td>
                    <td>
                        <span class="text-dark fs-7">{{ $log_donor->donor->kode }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold text-dark fs-7">{{ $log_donor->donor->nama }}</span>
                        </div>
                    </td>
                    <td>{{ formatDate($log_donor->created_at) }}, {{ formatTime($log_donor->created_at) }}</td>
                    <td>{{ $log_donor->petugasRegistrasi->kode ?? '' }}</td>
                    <td>{{ $log_donor->step }}</td>
                   <td class="text-end pe-4">
                    <button class="btn btn-icon btn-light-danger btn-sm h-25px w-25px me-1"
                            title="Cetak Tiket"
                            onclick="cetak_tiket_from_row(
                                '{{ $log_donor->kode }}',
                                '{{ addslashes($log_donor->donor->nama) }}',
                                '{{ $log_donor->donor->kode }}',
                                '{{ $log_donor->donor->golongan_darah }} {{ $log_donor->donor->rhesus_caption }}',
                                '{{ $log_donor->petugasRegistrasi->nama ?? '-' }}'
                            )">
                        <i class="ki-duotone ki-printer fs-6">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                    </button>
                    <button class="btn btn-icon btn-light-primary btn-sm h-25px w-25px me-1"
                        title="Cetak Kartu Donor"
                        onclick="cetak_kartu_donor(
                            '{{ $log_donor->donor->nama }}',
                            '{{ $log_donor->donor->kode }}',
                            '{{ $log_donor->donor->tanggal_lahir ? $log_donor->donor->tanggal_lahir->format('d/m/Y') : '-' }}',
                            '{{ $log_donor->donor->jenis_kelamin }}',
                            '{{ $log_donor->donor->alamat_1 }}',
                            '{{ $log_donor->donor->no_telp }}',
                            '{{ $log_donor->donor->golongan_darah }}',
                            '{{ $log_donor->donor->rhesus }}',
                            '{{ $log_donor->donor->foto 
                                ? asset('storage/'.$log_donor->donor->foto) 
                                : asset('assets/media/avatars/blank.jpg') }}'
                        )">
                    <i class="ki-duotone ki-credit-cart fs-6">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
                <button class="btn btn-icon btn-light-success btn-sm h-25px w-25px me-1"
    title="Cetak Form Pendaftaran"
    onclick='cetak_form_pendaftaran({
        kode: @json($log_donor->kode),
        tanggal_daftar: @json(formatDate($log_donor->created_at)),
        jam_daftar: @json(formatTime($log_donor->created_at)),
        kode_donor: @json($log_donor->donor->kode),
        nama: @json($log_donor->donor->nama),
        jenis_kelamin: @json($log_donor->donor->jenis_kelamin),
        tanggal_lahir: @json($log_donor->donor->tanggal_lahir ? $log_donor->donor->tanggal_lahir->format("d/m/Y") : "-"),
        golongan: @json($log_donor->donor->golongan_darah),
        rhesus: @json($log_donor->donor->rhesus_caption),
        donor_ke: @json($log_donor->donor->donor_ke ?? 0),
        alamat: @json($log_donor->donor->alamat_1),
        no_hp: @json($log_donor->donor->no_telp)
    })'>
    
    <i class="ki-duotone ki-document fs-6">
        <span class="path1"></span>
        <span class="path2"></span>
    </i>
</button>

                    @if($log_donor->step == 'Dokter')
                        <button class="btn btn-icon btn-light-danger btn-sm h-25px w-25px"
                                title="Delete" onclick="confirm_delete({{ $log_donor->id }})">
                            <i class="ki-duotone ki-cross fs-6">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </button>
                    @endif
                </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(count($log_donors) == 0)
    <div class="text-center py-10">
        <i class="ki-duotone ki-people fs-3x text-muted mb-3">
            <span class="path1"></span><span class="path2"></span>
            <span class="path3"></span><span class="path4"></span>
            <span class="path5"></span>
        </i>
        <div class="fs-6 text-muted">Belum ada antrian hari ini.</div>
    </div>
@endif

<script>
    $('#total_log_donor').html('{{ count($log_donors) }}')
</script>