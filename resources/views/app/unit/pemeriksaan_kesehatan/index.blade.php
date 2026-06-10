@php($active_cabang = session('active_cabang', []))
@php($nomor_ruangan = session('nomor_ruangan'))

@extends('layouts.index')

@section('title')
    Pemeriksaan Kesehatan - Unit Tranfusi Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
            <div class="card-body py-5">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1">
                            <span class="text-dark fw-bold fs-1">Pemeriksaan Kesehatan</span>
                        </h1>
                        @include('layouts._breadcrumb')
                    </div>
                    <div class="d-flex gap-3">
                        
                        <span class="badge badge-light-primary rounded-3 fs-7 text-dark fw-semibold px-4 py-3 cursor-pointer" onclick="setNomorRuangan()">
                            <i class="ki-duotone ki-folder fs-5 me-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            No.Ruangan : &nbsp;<b>{{ session('nomor_ruangan', '-Kosong-') }}</b>
                        </span>
                        <span class="badge badge-light-primary rounded-3 fs-7 text-dark fw-semibold px-4 py-3">
                            <i class="ki-duotone ki-calendar fs-5 me-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            {{ fulldate(date('Y-m-d'), " ") }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if(empty($active_cabang))
            <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
                <div class="card-body">
                    <h5 class="text-center my-10">- Cabang Aktif Kosong -</h5>
                </div>
            </div>
        @endif

        @if(!empty($active_cabang))
            <div class="row g-6">
                <div class="col-md-4" id="table_log_donor"></div>
                <div class="col-md-8">
                    <div class="card card-flush rounded-4 border-0 shadow-xs h-100" id="card_search">
                        <form class="card-header pt-6" id="form_search">
                            @csrf
                            <div class="card-title flex-column">
                                <h3 class="fw-bold fs-4 text-dark mb-1">Antrian Hari Ini</h3>
                                <div class="fs-7 text-muted">
                                    <span id="total_pemeriksaan_dokter">0</span> riwayat pemeriksaan
                                </div>
                            </div>
                            <div class="card-toolbar d-flex flex-row gap-3">
                                <div class="d-flex align-items-center position-relative">
                                    <i class="ki-duotone ki-magnifier fs-5 position-absolute ms-3 text-muted"><span class="path1"></span><span class="path2"></span></i>
                                    <x-input name="nama_pasien" prefix="pemeriksaan_dokter_" class="form-control-sm ps-9 w-175px" caption="Cari Nama Donor ..." />
                                </div>
{{--                                <div class="w-150px"><x-select name="step" class="form-select-sm" caption="Semua Status" :options="array_combine($steps, $steps)" data-control="select2" onchange="search_data()" /></div>--}}
                   
                                <button type="button" class="btn btn-light-danger btn-sm" onclick="openDisplay()">
                                    <i class="ki-duotone ki-screen fs-5 me-1">
                                        <span class="path1"></span><span class="path2"></span>
                                        <span class="path3"></span><span class="path4"></span>
                                    </i>
                                    Display Antrian
                                </button>
                            </div>
                        </form>
                        <div class="card-body pt-4">
                            <div id="table"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-6">
                <div class="col-md-3" id="info_log_donor"></div>
                <div class="col-md-9" id="info"></div>
            </div>
        @endif
    </div>
@endsection

@push('modals')
    <div class="modal fade modal-slide-right" tabindex="-1" id="modal_call">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Pilih Dokter</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless">
                        @foreach($dokter_options as $dokter)
                            <tr><td><button class="btn btn-primary w-100 btn-sm" type="button" onclick="pilih_dokter({{ $dokter->id }})">{{ $dokter->nama }}</button></td></tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
/* =========================================
   INIT AUDIO
========================================= */
let audioUnlocked = false;
let indoVoice = null;

function loadVoices() {
    let voices = window.speechSynthesis.getVoices();

    indoVoice =
        voices.find(v => v.lang === 'id-ID') ||
        voices.find(v => v.lang.includes('id')) ||
        voices[0];

    // console.log('VOICE READY:', indoVoice);
}

if ('speechSynthesis' in window) {

    loadVoices();

    window.speechSynthesis.onvoiceschanged = () => {
        loadVoices();
    };

    // unlock audio browser
    document.body.addEventListener('click', function unlock() {

        if (!audioUnlocked) {

            let dummy = new SpeechSynthesisUtterance('audio aktif');
            dummy.volume = 0;

            window.speechSynthesis.speak(dummy);

            audioUnlocked = true;

            // console.log('Audio unlocked');
        }

        document.body.removeEventListener('click', unlock);

    }, { once: true });
}

let _token   = '{{ csrf_token() }}';
let base_url = '{{ route('unit.pemeriksaan_kesehatan.index') }}';

let $table_log_donor = $('#table_log_donor'),
    $info_log_donor  = $('#info_log_donor'),
    $info            = $('#info'),
    $table           = $('#table');
let $form_search = $('#form_search'), $card_search = $('#card_search');

/* ─────────────────────────────────────
   INIT
───────────────────────────────────── */
let init = () => {
    $info_log_donor.html('');
    $info.html('');
    $table_log_donor.show();
    $card_search.show();

    search_pemeriksaan();
    search_log_donor();

    setTimeout(() => render_status_text(), 300);
}

let search_log_donor = () => {
    $.post(base_url + '/log_donor/search', { _token }, res => $table_log_donor.html(res))
     .fail(xhr => $table_log_donor.html(xhr.responseText));
}

/* ─────────────────────────────────────
   ASSIGN RUANGAN (R1 / R2)
   Dipanggil saat klik tombol R1 atau R2
───────────────────────────────────── */
let assign_ruangan = (log_donor_id, nomor_ruangan) => {
    $.post(base_url + '/log_donor/' + log_donor_id + '/assign_ruangan', {
        _token, nomor_ruangan
    }, () => {
        // Ganti tombol R1/R2 dengan badge ruangan
        let ruanganClass = 'badge-light-secondary';

            if (nomor_ruangan == 1) {
                ruanganClass = 'badge-light-primary';
            } else if (nomor_ruangan == 2) {
                ruanganClass = 'badge-light-info';
            } else if (nomor_ruangan == 3) {
                ruanganClass = 'badge-light-danger';
            }
        $('#assign-ruangan-' + log_donor_id).closest('td')
            .html(`<span class="badge fs-8 ${ruanganClass}">
                <i class="ki-duotone ki-home fs-8 me-1">
                    <span class="path1"></span><span class="path2"></span>
                </i>
                Ruangan ${nomor_ruangan}
            </span>`);
    }).fail(xhr => error_handle(xhr.responseText));
}

/* ─────────────────────────────────────
   PANGGIL — otomatis ambil dokter login
   + ruangan dari log_donor.nomor_ruangan
───────────────────────────────────── */
let call_donor = (pemeriksaan_id, log_donor_id, nama_donor, nomor_antrian) => {

    $.post(base_url + '/log_donor/' + log_donor_id + '/panggil', { _token }, (res) => {

        const ruangan    = parseInt(res.nomor_ruangan ?? 0);
        const dokterNama = res.dokter_nama ?? '';

        let ruanganClass = 'badge-light-secondary';

        if (ruangan == 1) {
            ruanganClass = 'badge-light-primary';
        } else if (ruangan == 2) {
            ruanganClass = 'badge-light-info';
        } else if (ruangan == 3) {
            ruanganClass = 'badge-light-danger';
        }

        // badge status
        $('.badge-status-' + log_donor_id)
            .removeClass(
                'badge-light-secondary badge-light-primary badge-light-success badge-light-danger'
            )
            .addClass('badge-light-warning')
            .html('🔊 Dipanggil');

        // update dokter
        if (dokterNama) {
            $('#dokter-col-' + log_donor_id).html(
                `<span class="text-dark fw-semibold">${dokterNama}</span>`
            );
        }

        // suara panggilan
        setTimeout(() => {

    let text =
        'Nomor antrian ' +
        nomor_antrian +
        '. Atas nama ' +
        nama_donor +
        '. Silakan menuju ruangan ' +
        ruangan;

    // console.log(text);

    speak(text);

}, 1000);

    }).fail(xhr => {

        Swal.fire({
            icon: 'warning',
            title: 'Ruangan Belum Dipilih',
            text: 'Silakan pilih ruangan terlebih dahulu sebelum memanggil.',
            confirmButtonText: 'OK'
        });

    });
}

/* ─────────────────────────────────────
   TTS
───────────────────────────────────── */
/* =========================================
   SPEAK FIX FINAL
========================================= */

let isSpeaking = false;

let speak = (text) => {

    if (!('speechSynthesis' in window)) {
        // console.log('Speech tidak support');
        return;
    }

    // kalau masih ngomong jangan panggil lagi
    if (isSpeaking) {
        // console.log('Masih speaking...');
        return;
    }

    isSpeaking = true;

    let utterance = new SpeechSynthesisUtterance(text);

    utterance.lang   = 'id-ID';
    utterance.rate   = 0.85;
    utterance.pitch  = 1;
    utterance.volume = 1;

    // ambil voice indonesia
    let voices = window.speechSynthesis.getVoices();

    let voice =
        voices.find(v => v.lang === 'id-ID') ||
        voices.find(v => v.lang.includes('id')) ||
        voices[0];

    if (voice) {
        utterance.voice = voice;
    }

    utterance.onstart = () => {
        // console.log('Suara mulai');
    };

    utterance.onend = () => {
        // console.log('Suara selesai');
        isSpeaking = false;
    };

    utterance.onerror = (e) => {
        // console.log('ERROR:', e);
        isSpeaking = false;
    };

    // JANGAN cancel lagi
    window.speechSynthesis.speak(utterance);
};
let availableVoices = [];

if ('speechSynthesis' in window) {
    const loadVoices = () => {
        availableVoices = speechSynthesis.getVoices();
        // console.log('Voices loaded:', availableVoices);
    };

    loadVoices();

    speechSynthesis.onvoiceschanged = loadVoices;
}

/* ─────────────────────────────────────
   PILIH DONOR → tampil info
───────────────────────────────────── */
let select_log = (id) => {
    $('.badge-status-' + id)
        .removeClass('badge-light-secondary badge-light-warning')
        .addClass('badge-light-primary')
        .html('&#x2699; Diproses');

    $table_log_donor.hide();
    $card_search.hide();

    $.get(base_url + '/log_donor/' + id, { _token }, result => {
        $info_log_donor.html(result);

        setTimeout(() => {
            let pemeriksaanId = $('#pemeriksaan_id').val();
            console.log('pemeriksaan_id:', pemeriksaanId); // CEK INI
            
            if (pemeriksaanId) {
                $.get(base_url + '/' + pemeriksaanId, { _token }, res => {
                    $info.html(res);
                }).fail(xhr => {
                    console.log('gagal load form:', xhr.responseText);
                    $info.html(xhr.responseText);
                });
            } else {
                console.log('pemeriksaan_id kosong atau tidak ditemukan');
            }
        }, 300);

    }).fail(xhr => {
        $table_log_donor.show();
        $card_search.show();
        $info_log_donor.html(xhr.responseText);
    });
}


let init_form = (id) => {
    let $form_info = $('#form_info');
    $form_info.submit(e => {
        e.preventDefault();
        $.ajax({
            url  : base_url + '/' + id + '?_method=put',
            type : 'post',
            data : new FormData($form_info.get(0)),
            cache: false, processData: false, contentType: false,
            success: () => Swal.fire({ icon: 'success', title: 'Pemeriksaan Berhasil Disimpan' })
                               .then(() => init()),
        }).fail(xhr => error_handle(xhr.responseText));
    });
}


let render_status_text = () => {
    $('.badge-status').each(function () {
        let status = $(this).data('status');

        let text = '';
        let cls  = '';

        if (status === 'Approved') {
            text = 'Sudah Terisi';
            cls  = 'badge-light-success';
        } else if (status === 'Rejected') {
            text = 'Batal';
            cls  = 'badge-light-danger';
        } else if (status === 'Pending' || status === 'Terpanggil') {
            text = 'Belum Terisi';
            cls  = 'badge-light-warning';
        } else {
            text = status;
            cls  = 'badge-light-secondary';
        }

        $(this)
            .removeClass('badge-light-success badge-light-danger badge-light-warning badge-light-secondary')
            .addClass(cls)
            .text(text);
    });
}
let search_pemeriksaan = () => {
    let data = get_form_data($form_search);

    $.post(base_url + '/search', data, (result) => {
        $table.html(result);

        // 🔥 WAJIB
        render_status_text();

    }).fail((xhr) => $table.html(xhr.responseText));
}

let setNomorRuangan = () => {
    Swal.fire({
    title: 'Pilih Nomor Ruangan',
    input: 'select',
    inputOptions: {
        1: 'R1',
        2: 'R2',
        3: 'R3'
    },
    inputPlaceholder: 'Pilih Ruangan',
    showCancelButton: true,
    confirmButtonText: 'Simpan',
    }).then(result => {
        if (result.isConfirmed) {
            $.post(base_url + '/nomor_ruangan', { _token, nomor_ruangan: result.value }, () => {
                Swal.fire({ icon: 'success', title: 'Berhasil Disimpan' })
                    .then(() => location.reload());
            });
        }
    });
}

let openDisplay = () => {
    window.open('{{ route('unit.pemeriksaan_kesehatan.display_antrian') }}', '_blank',
        'width=1280,height=720,menubar=no,toolbar=no,scrollbars=no');
}

$form_search.submit(e => { e.preventDefault(); search_pemeriksaan(); });
init_form_element();
init();
</script>
@endpush
