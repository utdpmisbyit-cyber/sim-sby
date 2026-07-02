<div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
    <div class="card-header pt-6">
        <div class="card-title">
            <h3 class="fw-bold fs-4 text-dark mb-0">Informasi Pendaftar</h3>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="d-flex flex-column gap-3 p-4 bg-secondary rounded-3 mb-4">
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Kode Pendaftaran</span>
                <span class="fw-bold text-dark fs-7">{{ optional($log_donor->donor)->kode ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Tanggal Daftar</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->created_at ? fulldate($log_donor->created_at) : '-' }},
                {{ $log_donor->created_at ? formatTime($log_donor->created_at) : '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Nama Lengkap</span>
                <span class="fw-bold text-dark fs-7">{{ optional($log_donor->donor)->nama ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Kode Donor</span>
                <span class="fw-bold text-dark fs-7"> {{ optional($log_donor->donor)->kode ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">NIK</span>
                <span class="fw-bold text-dark fs-7"> {{ optional($log_donor->donor)->no_ktp ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Jenis Kelamin</span>
                <span class="fw-bold text-dark fs-7"> {{ optional($log_donor->donor)->jenis_kelamin ?? '-' }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Umur</span>
                <span class="fw-bold text-dark fs-7">{{ optional($log_donor->donor)->tanggal_lahir ? calculateAge($log_donor->donor->tanggal_lahir) : '-' }} Tahun</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Tgl Lahir</span>
                <span class="fw-bold text-dark fs-7"> {{ fulldate($log_donor->donor->tanggal_lahir ?? '-' )}}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">No. Telepon</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->no_telp ?? '-'}}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Gol. Darah</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->golongan_darah ?? '-'}} {{ $log_donor->donor->rhesus_caption ?? '-'}}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Berat Badan</span>
                <span class="fw-bold text-dark fs-7"> {{ optional($log_donor->pemeriksaanDokter)->berat_badan ?? '-' }} Kg</span>
            </div>
        
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Skrining Antibody</span>
                <span class="fw-bold text-dark fs-7">
                    {{ optional($log_donor->donor)->skrining ?? '-' }}
                </span>
            </div>
           <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Jenis Kantong</span>

                @php
                    $pemeriksaanDokter = optional($log_donor->pemeriksaanDokter);
                    $tipeKantong       = optional($pemeriksaanDokter->tipeKantong);
                    $jenisKantong      = optional($tipeKantong->jenisKantong);
                @endphp

                <span class="fw-bold text-dark fs-7">
                    {{ $jenisKantong->nama ?? '-' }}
                    @if($tipeKantong->nama)
                        - {{ $tipeKantong->nama }}
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<script>
 @if(optional($log_donor->pemeriksaanHb)->id)
    info({{ $log_donor->pemeriksaanHb->id }});
@endif
</script>
