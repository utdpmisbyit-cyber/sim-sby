
<div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
    <div class="card-header pt-6">
        <div class="card-title">
            <h3 class="fw-bold fs-4 text-dark mb-0">Antrian Pendaftaran</h3>
        </div>
    </div>
    <div class="card-body pt-0">
        @if(count($log_donors) == 0)
            <div class="text-center py-10">
                <i class="ki-duotone ki-people fs-3x text-muted mb-3">
                    <span class="path1"></span><span class="path2"></span>
                    <span class="path3"></span><span class="path4"></span>
                    <span class="path5"></span>
                </i>
                <div class="fs-6 text-muted">Antrian pendaftar hari ini kosong.</div>
            </div>
        @endif


        @if(count($log_donors) > 0)
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 table-sm align-middle">
                    <thead>
                    <tr class="fs-7 fw-bold bg-secondary text-gray-400 border-bottom-0 text-uppercase">
                        <th class="ps-3 rounded-start min-w-150px">Nama Pendaftar</th>
                        <th class="min-w-80px text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($no = 1)
                    @foreach($log_donors as $log_donor)
                        <tr>
                            <td class="lh-1 ps-3">
                                <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->nama }}</span> <br>
                                <span class="text-dark fs-7">Nomor : <span class="fw-bolder">{{ substr($log_donor->kode, 7) }}</span></span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-primary btn-sm py-1" title="Detail" onclick="select_log({{ $log_donor->id }})">
                                    Pilih Pendaftar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
