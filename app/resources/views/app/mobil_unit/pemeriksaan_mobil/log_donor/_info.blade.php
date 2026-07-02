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
                <span class="fw-bold text-dark fs-7">{{ $log_donor->kode }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Tanggal Daftar</span>
                <span class="fw-bold text-dark fs-7">{{ fulldate($log_donor->created_at) }}, {{ formatTime($log_donor->created_at) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Nama Lengkap</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->nama }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Kode Donor</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->kode }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">NIK</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->no_ktp }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Jenis Kelamin</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->jenis_kelamin }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Umur</span>
                <span class="fw-bold text-dark fs-7">{{ calculateAge($log_donor->donor->tanggal_lahir) }} Tahun</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">No. Telepon</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->no_telp }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Gol. Darah</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->golongan_darah }} {{ $log_donor->donor->rhesus_caption }}</span>
            </div>
        </div>

        <div class="separator separator-dashed my-6"></div>

        <div class="fs-7 text-dark fw-semibold mb-3 text-uppercase ls-1">Riwayat Donor</div>
        @if($log_donor->donor->logDonorAftap->count() == 0)
            <div class="text-center py-10">
                <i class="ki-duotone ki-heart fs-3x text-muted mb-3">
                    <span class="path1"></span><span class="path2"></span>
                    <span class="path3"></span><span class="path4"></span>
                    <span class="path5"></span>
                </i>
                <div class="fs-6 text-muted">Belum ada riwayat donor.</div>
            </div>
        @endif

        @if($log_donor->donor->logDonorAftap->count() > 0)
            <div class="d-flex flex-column gap-3 p-4 bg-secondary rounded-3">
                <div class="d-flex justify-content-between">
                    <span class="text-dark fs-7">Total Donor</span>
                    <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->logDonorAftap->count() }} kali</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-dark fs-7">Donor Terakhir</span>
                    <span class="fw-bold text-dark fs-7">{{ fulldate($log_donor->donor->logDonorAftap->last()->created_at ?? '', ' ') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    info({{ $log_donor->id }});
</script>
