
<div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
    <div class="card-header pt-6">
        <div class="card-title">
            <h3 class="fw-bold fs-4 text-dark mb-0">Antrian Pendaftaran</h3>
        </div>
        {{-- Info petugas login & ruangannya --}}
        <div class="card-toolbar">
            @php
                $petugasLogin  = \App\Models\Petugas::where('user_id', auth()->id())->first();
                $ruanganSesi   = session('nomor_ruangan', '-');
            @endphp
            @if($petugasLogin)
                <span class="badge badge-light-primary rounded-3 fs-8 px-3 py-2">
                    <i class="ki-duotone ki-user-tick fs-6 me-1">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    </i>
                    {{ $petugasLogin->nama }}
                    &nbsp;·&nbsp;
                    <span class="fw-bold">Ruangan {{ $ruanganSesi }}</span>
                </span>
            @endif
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
                        <th class="ps-3 rounded-start">#</th>
                        <th class="min-w-120px">Nama Pendaftar</th>
                        <th>Dokter</th>
                        <th>Ruangan</th>
                        <th>Status</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($log_donors as $i => $log_donor)
                    @php
                        $pemDokter  = optional($log_donor->pemeriksaanDokter);
                        $step       = $log_donor->step ?? 'Registrasi';
                        $noRuangan  = $log_donor->nomor_ruangan; // dari log_donor
                        $dokterNama = optional($pemDokter->dokter)->nama;
                    @endphp
                    <tr id="row-{{ $log_donor->id }}">
                        <td class="ps-3 fw-bold text-muted fs-7">{{ $i + 1 }}</td>

                        <td class="lh-1">
                            <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->nama }}</span><br>
                            <span class="text-muted fs-8">{{ substr($log_donor->kode, 7) }}</span>
                        </td>

                        {{-- Dokter --}}
                        <td class="fs-8 lh-1" id="dokter-col-{{ $log_donor->id }}">
                            {{ $dokterNama ?? '-' }}
                        </td>

                        {{-- Ruangan --}}
                        <td id="ruangan-col-{{ $log_donor->id }}">
                            @php
                                $ruanganClass = match((int)$noRuangan) {
                                    1 => 'badge-light-primary',
                                    2 => 'badge-light-info',
                                    3 => 'badge-light-danger',
                                    default => 'badge-light-secondary'
                                };
                            @endphp
                            @if($noRuangan)
                                <span class="badge fs-8 {{ $ruanganClass  == 2 ? 'badge-light-info' : 'badge-light-danger' }}">
                                    <i class="ki-duotone ki-home fs-8 me-1">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                    Ruangan {{ $noRuangan }}
                                </span>
                            @else
                                {{-- Belum assign ruangan: tampilkan tombol pilih --}}
                                <div class="d-flex gap-1" id="assign-ruangan-{{ $log_donor->id }}">
                                    <button class="btn btn-xs btn-light-primary py-1 px-2 fs-8 fw-bold"
                                        onclick="assign_ruangan({{ $log_donor->id }}, 1)">
                                        R1
                                    </button>
                                    <button class="btn btn-xs btn-light-danger py-1 px-2 fs-8 fw-bold"
                                        onclick="assign_ruangan({{ $log_donor->id }}, 2)">R2</button>
                                    <button class="btn btn-xs btn-light-info py-1 px-2 fs-8 fw-bold"
                                        onclick="assign_ruangan({{ $log_donor->id }}, 3)">R3</button>
                                </div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="badge fs-8 badge-status-{{ $log_donor->id }}
                                @if($step === 'Terpanggil')                             badge-light-warning
                                @elseif(in_array($step, ['Kesehatan','HB','Aftap']))    badge-light-primary
                                @elseif($step === 'Approved')                           badge-light-success
                                @elseif($step === 'Rejected')                           badge-light-danger
                                @else                                                   badge-light-secondary
                                @endif">
                                @if($step === 'Terpanggil')     &#x1F50A; Dipanggil
                                @elseif($step === 'Kesehatan')  &#x2699; Diproses
                                @elseif($step === 'Approved')   &#x2713; Selesai
                                @elseif($step === 'Rejected')   &#x2715; Ditolak
                                @else                           Menunggu
                                @endif
                            </span>
                        </td>

                        <td class="text-end text-nowrap">
                            <button class="btn btn-sm btn-secondary py-1 me-1"
                                onclick="call_donor(
                                    {{ $pemDokter->id ?? 'null' }},
                                    {{ $log_donor->id }},
                                    '{{ addslashes($log_donor->donor->nama) }}',
                                    {{ (int) substr($log_donor->kode, -3) }}
                                )">
                                <i class="ki-duotone ki-volume-up fs-7 me-1">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                Panggil
                            </button>
                            <button class="btn btn-sm btn-primary py-1"
                                onclick="select_log({{ $log_donor->id }})">
                                Pilih
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