@if(count($donors) > 0)
    <div class="separator separator-dashed mb-4"></div>
    <div class="fs-7 text-muted fw-semibold mb-3 text-uppercase ls-1">Hasil Pencarian</div>

    @foreach($donors as $donor)
        <div class="d-flex align-items-start justify-content-between rounded-3 p-3 bg-light-primary mb-2 border border-dashed border-gray-300">
            <div class="d-flex align-items-center gap-3">
                <div>
                    <div class="fw-bold text-dark fs-6">{{ $donor->nama }}</div>
                    <div class="text-gray-800 fs-7">NIK: {{ $donor->no_ktp }}, Kode : {{ $donor->kode }}</div>
                    <div class="text-gray-800 fs-7">Gol.Darah: {{ $donor->golongan_darah }} {{ $donor->rhesus_caption }}</div>
                    <div class="text-gray-800 fs-7">Jenis Kelamin: {{ $donor->jenis_kelamin }}</div>
                    <div class="text-gray-800 fs-7">Donor Ke: {{ $donor->donor_ke }}</div>
                    <div class="text-gray-800 fs-7">Tanggal Lahir: {{ fulldate($donor->tanggal_lahir) }} ({{ calculateAge($donor->tanggal_lahir) }} Tahun)</div>
                </div>
            </div>
            <button class="btn btn-sm btn-primary" onclick="select_donor({{ $donor->id }})">
                Daftarkan
            </button>
        </div>
    @endforeach
@endif

@if(count($donors) == 0)
    <div class="text-center py-4">
        <i class="ki-duotone ki-user-cross fs-3x text-muted mb-3">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
        </i>
        <div class="fs-6 text-muted">Donor tidak ditemukan.</div>
        <div class="fs-7 text-muted mt-1 mb-4">Silakan tambahkan sebagai donor baru.</div>
    </div>
@endif

<script>
    search_result = JSON.parse(`@json($donors)`);
</script>
