<div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
    <div class="card-header pt-6">
        <div class="card-title">
            <h3 class="fw-bold fs-4 text-dark mb-0">Informasi Pendaftar</h3>
        </div>
    </div>
    <div class="card-body pt-0">
        @php
            $pemeriksaanDokter = optional($log_donor->pemeriksaanDokter);
            $pemeriksaanHb     = optional($log_donor->pemeriksaanHb);

            $tipeKantong  = optional($pemeriksaanDokter->tipeKantong);
            $jenisKantong = optional($tipeKantong->jenisKantong);

            /*
            |--------------------------------------------------------------------------
            | STATUS PEMERIKSAAN KESEHATAN
            |--------------------------------------------------------------------------
            */

            $statusDokter = strtolower(trim($pemeriksaanDokter->status ?? ''));
            $alasanDokter = $pemeriksaanDokter->alasan ?? null;

            if (in_array($statusDokter, ['approved', 'approve', 'terima'])) {

                $kesehatanText  = 'Diterima';
                $kesehatanColor = 'text-success';

            } elseif (in_array($statusDokter, ['rejected', 'reject', 'tolak'])) {

                $kesehatanText  = 'Ditolak';

                if ($alasanDokter) {
                    $kesehatanText .= ' — ' . $alasanDokter;
                }

                $kesehatanColor = 'text-danger';

            } else {

                $kesehatanText  = 'Belum Diperiksa';
                $kesehatanColor = 'text-muted';
            }

            /*
            |--------------------------------------------------------------------------
            | STATUS PEMERIKSAAN HB
            |--------------------------------------------------------------------------
            */

            $statusHb = strtolower(trim($pemeriksaanHb->status ?? ''));
            $alasanHb = $pemeriksaanHb->alasan ?? null;

            if (in_array($statusHb, ['approved', 'approve', 'terima'])) {

                $hematologiText  = 'Sudah Diperiksa';
                $hematologiColor = 'text-success';

            } elseif (in_array($statusHb, ['rejected', 'reject', 'tolak'])) {

                $hematologiText  = 'Ditolak';

                if ($alasanHb) {
                    $hematologiText .= ' — ' . $alasanHb;
                }

                $hematologiColor = 'text-danger';

            } else {

                $hematologiText  = 'Belum Diperiksa';
                $hematologiColor = 'text-muted';
            }
        @endphp

        <div class="d-flex flex-column gap-3 p-4 bg-secondary rounded-3 mb-4">

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Kode Pendaftaran</span>
                <span class="fw-bold text-dark fs-7">{{ $log_donor->kode }}</span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Tanggal Akhir Donor</span>
                <span class="fw-bold text-dark fs-7">
                    {{ fulldate($log_donor->created_at) }}, {{ formatTime($log_donor->created_at) }}
                </span>
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
                <span class="text-dark fs-7">Tgl Lahir</span>
                <span class="fw-bold text-dark fs-7">{{ fulldate($log_donor->donor->tanggal_lahir) }}</span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Umur</span>
                <span class="fw-bold text-dark fs-7">{{ calculateAge($log_donor->donor->tanggal_lahir) }} Tahun</span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">No. Telepon</span>
                <span class="fw-bold text-dark fs-7">
                    {{ $log_donor->no_telp ?? $log_donor->donor->no_telp ?? '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Gol. Darah</span>
                <span class="fw-bold text-dark fs-7">
                    {{ $log_donor->donor->golongan_darah }} {{ $log_donor->donor->rhesus_caption }}
                </span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Berat Badan</span>
                <span class="fw-bold text-dark fs-7">{{ $pemeriksaanDokter->berat_badan ?? '-' }} Kg</span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Lengan Ambil Sebelah</span>
                <span class="fw-bold text-dark fs-7">{{ $pemeriksaanHb->lengan ?? '-' }}</span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Asal Darah</span>
                <span class="fw-bold text-dark fs-7">
                    {{ $log_donor->donor->nama_asal_darah ?? '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">No FPUP</span>
                <span class="fw-bold text-dark fs-7">
                    {{ $pemeriksaanDokter->no_fpup ?? '-' }}
                </span>
            </div>

            <div class="d-flex justify-content-between">
                 <span class="text-dark fs-7">Pemeriksaan Kesehatan</span>
                    <span class="fw-bold fs-7 {{ $kesehatanColor }}">
                        {{ $kesehatanText }}
                    </span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Pemeriksaan Hematologi</span>
                <span class="fw-bold fs-7 {{ $hematologiColor }}">
                    {{ $hematologiText }}
                </span>
            </div>

            <div class="d-flex justify-content-between">
                  <span class="text-dark fs-7">Cc Yang Disarankan Ambil</span>
                    <span class="fw-bold text-dark fs-7">
                        {{ $pemeriksaanDokter->cc_ambil ?? '-' }}
                        @if($pemeriksaanDokter->cc_ambil) cc @endif
                    </span>
            </div>

            {{-- Jenis Kantong --}}
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Jenis Kantong Yang Digunakan</span>
                <span class="fw-bold text-dark fs-7">
                    {{ $jenisKantong->nama ?? '-' }}
                </span>
            </div>

            {{-- Type Jenis Kantong --}}
            <div class="d-flex justify-content-between">
                <span class="text-dark fs-7">Type Jenis Kantong</span>
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
    info({{ $log_donor->aftap->id }});
</script>