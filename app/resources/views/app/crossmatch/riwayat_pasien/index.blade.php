@extends('layouts.index')

@section('title', 'Riwayat Pasien Crossmatch')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-history"></i> Riwayat Pasien Crossmatch</h5>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('crossmatch.riwayat_pasien_crossmatch.index') }}" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="q" value="{{ $keyword }}" class="form-control"
                           placeholder="Cari nama pasien / no KTP / no FPUP">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-search"></i> Cari
                    </button>
                </div>
                @if($keyword)
                <div class="col-md-2">
                    <a href="{{ route('crossmatch.riwayat_pasien_crossmatch.index') }}" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama Pasien</th>
                            <th>No. KTP</th>
                            <th class="text-center">Total Permintaan FPUP</th>
                            <th>Permintaan Terakhir</th>
                            <th class="text-center" style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasienList as $i => $pasien)
                            <tr>
                                <td>{{ $pasienList->firstItem() + $i }}</td>
                                <td>{{ $pasien->nama_pasien }}</td>
                                <td>{{ $pasien->no_ktp ?? '-' }}</td>
                                <td class="text-center">{{ $pasien->total_permintaan }}</td>
                                <td>
                                    {{ $pasien->tgl_terakhir ? \Carbon\Carbon::parse($pasien->tgl_terakhir)->format('d-m-Y') : '-' }}
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-info btn-riwayat"
                                            data-no-ktp="{{ $pasien->no_ktp }}"
                                            data-nama-pasien="{{ $pasien->nama_pasien }}">
                                        <i class="fa fa-history"></i> Lihat Riwayat
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Tidak ada data pasien yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $pasienList->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Riwayat Pasien -->
<div class="modal fade" id="modalRiwayat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Riwayat Crossmatch - <span id="modalNamaPasien"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="riwayatLoading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat riwayat...</p>
                </div>

                <ul class="timeline list-unstyled mb-0" id="riwayatTimeline"></ul>

                <p id="riwayatEmpty" class="text-muted text-center py-4 d-none">
                    Belum ada riwayat untuk pasien ini.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
        border-left: 2px solid #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.25rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.6rem;
        top: 0.25rem;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #0d6efd;
    }
    .timeline-item .badge {
        font-size: 0.7rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl       = document.getElementById('modalRiwayat');
    var modal         = new bootstrap.Modal(modalEl);
    var timelineEl    = document.getElementById('riwayatTimeline');
    var loadingEl     = document.getElementById('riwayatLoading');
    var emptyEl       = document.getElementById('riwayatEmpty');
    var namaPasienEl  = document.getElementById('modalNamaPasien');

    document.querySelectorAll('.btn-riwayat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var noKtp      = btn.dataset.noKtp || '';
            var namaPasien = btn.dataset.namaPasien || '';

            namaPasienEl.textContent = namaPasien;
            timelineEl.innerHTML = '';
            emptyEl.classList.add('d-none');
            emptyEl.textContent = 'Belum ada riwayat untuk pasien ini.';
            loadingEl.classList.remove('d-none');
            modal.show();

            var params = new URLSearchParams();
            if (noKtp) params.append('no_ktp', noKtp);
            params.append('nama_pasien', namaPasien);

            fetch("{{ route('crossmatch.riwayat_pasien_crossmatch.detail') }}?" + params.toString(), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    loadingEl.classList.add('d-none');

                    if (!data.timeline || data.timeline.length === 0) {
                        emptyEl.classList.remove('d-none');
                        return;
                    }

                    data.timeline.forEach(function (item) {
                        var li = document.createElement('li');
                        li.className = 'timeline-item';
                        li.innerHTML =
                            '<div class="d-flex justify-content-between">' +
                                '<strong>' + (item.jenis || '') + '</strong>' +
                                '<span class="badge bg-secondary">' + (item.status || '-') + '</span>' +
                            '</div>' +
                            '<div class="small text-muted">' +
                                (item.tanggal || '-') + ' &middot; No. FPUP: ' + (item.no_fpup || '-') +
                            '</div>' +
                            '<div>' + (item.keterangan || '') + '</div>';
                        timelineEl.appendChild(li);
                    });
                })
                .catch(function () {
                    loadingEl.classList.add('d-none');
                    emptyEl.textContent = 'Gagal memuat riwayat. Silakan coba lagi.';
                    emptyEl.classList.remove('d-none');
                });
        });
    });
});
</script>
@endpush