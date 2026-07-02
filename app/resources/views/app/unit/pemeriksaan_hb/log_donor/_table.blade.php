{{-- resources/views/app/unit/pemeriksaan_hb/log_donor/_table.blade.php --}}
<div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
    <div class="card-header pt-6">
        <div class="card-title">
            <h3 class="fw-bold fs-4 text-dark mb-0">Antrian Pendaftaran</h3>
        </div>
        <div class="card-toolbar">
            @php
                $petugasLogin = \App\Models\Petugas::where('user_id', auth()->id())->first();
                $ruanganSesi  = session('nomor_ruangan', '-');
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
                        <th>Petugas</th>
                        <th>Riwayat Ruangan</th>
                        <th>Status Panggil</th>
                        <th class="text-end pe-4 rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($log_donors as $i => $log_donor)
                   @php
                    $pemDokter  = optional($log_donor->pemeriksaanHb);
                    $step       = $log_donor->step ?? 'Registrasi';
                    $noRuangan  = $log_donor->nomor_ruangan;
                    $dokterNama = optional($pemDokter->dokter)->nama;
                    $noAntrian  = (int) substr($log_donor->kode, -3);
                    $kodeAftap  =  str_pad($noAntrian, 4, '0', STR_PAD_LEFT);
                    $tanggal    = \Carbon\Carbon::now()->format('d/m/Y H:i'); 
                    $kodeLog    = $log_donor->kode; 
                @endphp
                @if($step === 'Aftap')
                    @continue
                @endif
                    <tr id="row-{{ $log_donor->id }}">
                        <td class="ps-3 fw-bold text-muted fs-7">{{ $i + 1 }}</td>

                        <td class="lh-1">
                            <span class="fw-bold text-dark fs-7">{{ $log_donor->donor->nama }}</span><br>
                            <span class="text-muted fs-8">{{ $kodeAftap }}</span> 
                        </td>

                        <td class="fs-8 lh-1" id="dokter-col-{{ $log_donor->id }}">
                            {{ $dokterNama ?? '-' }}
                        </td>

                        <td id="ruangan-col-{{ $log_donor->id }}" data-ruangan="{{ $noRuangan }}">
                            @if($noRuangan)
                                <span class="badge fs-8 {{ $noRuangan == 2 ? 'badge-light-info' : 'badge-light-danger' }}">
                                    <i class="ki-duotone ki-home fs-8 me-1">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                    Ruangan {{ $noRuangan }}
                                </span>
                            @else
                                <div class="d-flex gap-1" id="assign-ruangan-{{ $log_donor->id }}">
                                    <button class="btn btn-xs btn-light-danger py-1 px-2 fs-8 fw-bold"
                                        onclick="assign_ruangan({{ $log_donor->id }}, 1)">R1</button>
                                    <button class="btn btn-xs btn-light-info py-1 px-2 fs-8 fw-bold"
                                        onclick="assign_ruangan({{ $log_donor->id }}, 2)">R2</button>
                                </div>
                            @endif
                        </td>

                        <td>
                            <span class="badge fs-8 badge-status-{{ $log_donor->id }}
                                @if($step === 'HB')                                      badge-light-warning
                                @elseif(in_array($step, ['Kesehatan','HB','Aftap']))     badge-light-primary
                                @elseif($step === 'Approved')                            badge-light-success
                                @elseif($step === 'Rejected')                            badge-light-danger
                                @else                                                    badge-light-secondary
                                @endif">
                                @if($step === 'HB')         &#x1F50A; Dipanggil
                                @elseif($step === 'Aftap')  &#x2699; Proses Aftap
                                @elseif($step === 'Approved') &#x2713; Selesai
                                @elseif($step === 'Rejected') &#x2715; Ditolak
                                @else                         Menunggu
                                @endif
                            </span>
                        </td>

                        <td class="text-end text-nowrap">
                           
                           <button class="btn btn-sm btn-secondary py-1 me-1"
                                onclick="call_donor(
                                    {{ $pemDokter->id ?? 'null' }},
                                    {{ $log_donor->id }},
                                    '{{ addslashes($log_donor->donor->nama) }}',
                                    '{{ $kodeAftap }}',
                                    {{ $noRuangan ?? 'null' }},
                                    {{ $noAntrian }}
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

                           <button class="btn btn-sm btn-icon btn-light-primary py-1 me-1"
                                title="Cetak Antrian"
                                onclick="print_antrian(
                                    '{{ addslashes($log_donor->donor->nama) }}',
                                    '{{ $kodeAftap }}',
                                    '{{ $kodeLog }}',
                                    '{{ $tanggal }}'
                                )">
                                <i class="ki-duotone ki-printer fs-5">
                                    <span class="path1"></span><span class="path2"></span>
                                    <span class="path3"></span><span class="path4"></span>
                                </i>
                            </button>

                           <button class="btn btn-sm btn-icon btn-light-success py-1"
                            title="Kirim WhatsApp"
                            onclick="kirim_wa(
                                {{ $log_donor->id }},
                                '{{ addslashes($log_donor->donor->no_telp) }}',
                                '{{ addslashes($log_donor->donor->nama) }}',
                                '{{ $kodeAftap }}',
                                '{{ $kodeLog }}',
                                '{{ $tanggal }}'
                            )">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/>
                            </svg>
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