@php($active_cabang = session('active_cabang', []))
@php($nomor_ruangan = session('nomor_ruangan'))

@extends('layouts.index')

@section('title')
    Hematologi / Pra Lab - Unit Tranfusi Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
            <div class="card-body py-5">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1">
                            <span class="text-dark fw-bold fs-1">Hematologi / Pra Lab</span>
                        </h1>
                        @include('layouts._breadcrumb')
                    </div>
                    <div class="d-flex gap-3">
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
    let _token = '{{ csrf_token() }}', base_url = '{{ route('mobil_unit.pemeriksaan_mobil_hb.index') }}';
    let $table_log_donor = $('#table_log_donor'), $info_log_donor = $('#info_log_donor');
    let $info = $('#info'), $table = $('#table');
    let $form_search = $('#form_search'), $card_search = $('#card_search');

    let init = () => {
        $info_log_donor.html('');
        $info.html('');
        $table_log_donor.show();
        $card_search.show();
        search_pemeriksaan();
        search_log_donor();
    }

    let search_log_donor = () => {
        $.post(base_url + '/log_donor/search', {_token},
            (result) => $table_log_donor.html(result)
        ).fail((xhr) => $table_log_donor.html(xhr.responseText));
    }

    // ✅ FIX: select_log menampilkan info di panel kanan, TANPA menyembunyikan tabel
let select_log = (id) => {

    // update step jadi HB
    $.post(base_url + '/log_donor/' + id + '/panggil', { _token });

    // ambil detail log donor
    $.get(base_url + '/log_donor/' + id, (res) => {
        $info_log_donor.html(res);

        // ambil pemeriksaan id dari attribute
        let pemeriksaanId = $info_log_donor
            .find('[data-pemeriksaan-id]')
            .data('pemeriksaan-id');

        if (pemeriksaanId) {
            info(pemeriksaanId); // ✅ LOAD FORM HB
        } else {
            // $info.html(`
            //     <div class="alert alert-warning">
            //         Data pemeriksaan HB belum ada
            //     </div>
            // `);
        }
    });
}

    let info = (id) => {
    $table_log_donor.hide();
    $card_search.hide();

    $.get(base_url + '/' + id, (res) => {
        $info.html(res); // ✅ FORM HB MASUK SINI
    });
}

    let init_form = (id) => {
        let $form_info = $('#form_info');
        $form_info.submit((e) => {
            e.preventDefault();
            let url = base_url + '/' + id + '?_method=put';
            let data = new FormData($form_info.get(0));
            $.ajax({
                url, type: 'post', data, cache: false, processData: false, contentType: false,
                success: () => {
                    Swal.fire({ icon: 'success', title: 'Pemeriksaan Berhasil Disimpan' })
                        .then(() => init())
                        
                },
            }).fail((xhr) => error_handle(xhr.responseText));
        });
    }

    let search_pemeriksaan = () => {
        let data = get_form_data($form_search);
        $.post(base_url + '/search', data,
            (result) => $table.html(result)
        ).fail((xhr) => $table.html(xhr.responseText));
    }

    // ✅ FIX: call_donor terima 5 argumen (tambah noRuangan)
    let pemeriksaan_id = '', log_donor_id_call = '';
 // ✅ call_donor - tambah kodeLog & tanggal
let call_donor = (pemHbId, logDonorId, nama, kodeAftap, noRuangan, noAntrian) => {
    if (!logDonorId) {
        Swal.fire({ icon: 'warning', title: 'Data tidak lengkap' });
        return;
    }

    // ✅ speak pakai noAntrian angka, bukan kodeAftap
    speak_antrian(nama, noAntrian, 'meja pemeriksaan HB');

    $.post(base_url + '/log_donor/' + logDonorId + '/panggil', { _token }, () => {
        search_log_donor();
    });
};

let print_antrian = (nama, kodeAftap,lengan, kodeLog, tanggal) => {
    Swal.fire({
        title: 'Pilih Lengan',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Lengan Kanan',
        cancelButtonText: 'Lengan Kiri',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
    }).then((result) => {
        if (result.isDismissed && result.dismiss !== Swal.DismissReason.cancel) return;

        const isKanan = result.isConfirmed;
        const lengan = isKanan ? 'Kanan' : 'Kiri';

        // Generate nomor antrian berdasarkan lengan
        // Ambil angka dari kodeAftap (contoh: AF0001 → 1)
        const noAngka = parseInt(kodeAftap.replace('AF', '')) || 0;
        const nomorCetak = isKanan
            ? 'A' + String(noAngka).padStart(3, '0')   // a001
            : 'B' + String(noAngka).padStart(3, '0');  // b001

        do_print_antrian(nama, nomorCetak, lengan, kodeLog, tanggal);
    });
};

let do_print_antrian = (nama, nomorCetak, lengan, kodeLog, tanggal) => {
    let content = `
    <html>
    <head>
        <title>Cetak Antrian</title>
        <style>
            @page { size: 80mm auto; margin: 0; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Courier New', Courier, monospace;
                width: 80mm;
                padding: 4mm 5mm;
                font-size: 12px;
                color: #000;
            }
            .center  { text-align: center; }
            .bold    { font-weight: bold; }
            .header  { font-size: 14px; font-weight: bold; text-align: center; line-height: 1.4; }
            .divider { border-top: 1px dashed #000; margin: 4px 0; }
            .lengan-label {
                font-size: 13px;
                font-weight: bold;
                text-align: center;
                margin: 6px 0 2px;
                letter-spacing: 1px;
                text-transform: uppercase;
            }
            .label   { font-size: 11px; color: #333; text-align: center; margin-top: 4px; }
            .number  { font-size: 40px; font-weight: bold; text-align: center; letter-spacing: 4px; margin: 4px 0; line-height: 1; }
            .row     { display: flex; justify-content: space-between; font-size: 11px; margin: 2px 0; }
            .row span:last-child { text-align: right; font-weight: bold; }
            .footer  { font-size: 11px; text-align: center; margin-top: 4px; line-height: 1.6; }
        </style>
    </head>
    <body>
        <div class="header">&#x1F3E5; UNIT TRANSFUSI DARAH</div>
        <div class="divider"></div>

        <div class="label">Halo,</div>
        <div class="center bold" style="font-size:13px; margin: 2px 0 6px;">${nama}</div>

        <div class="divider"></div>

        <div class="lengan-label">&#9679; LENGAN ${lengan.toUpperCase()} &#9679;</div>
        <div class="label">Nomor Antrian Anda:</div>
        <div class="number">${nomorCetak}</div>

        <div class="divider"></div>

        <div class="row">
            <span>Kode</span>
            <span>${kodeLog}</span>
        </div>
        <div class="row">
            <span>Tanggal</span>
            <span>${tanggal}</span>
        </div>

        <div class="divider"></div>

        <div class="footer">
            Silakan menunggu panggilan.<br>
            Terima kasih &#x1F64F;
        </div>

        <div style="margin-top: 8mm;"></div>
    </body>
    </html>`;

    let w = window.open('', '', 'width=340,height=600');
    w.document.write(content);
    w.document.close();
    w.focus();
    setTimeout(() => { try { w.print(); w.close(); } catch(e) {} }, 500);
};

    let speak_antrian = (nama, noAntrian, tujuan) => {
        let text = `Nomor antrian ${noAntrian}, atas nama ${nama}, silakan menuju ${tujuan}`;
        let speech = new SpeechSynthesisUtterance(text);
        speech.lang  = 'id-ID';
        speech.rate  = 0.9;
        speech.pitch = 1;
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(speech);
    };


    let kirim_wa = (logDonorId, noHp, nama, kodeAftap, kodeLog, tanggal) => {
        let pesan = 
    `🏥 *UNIT TRANSFUSI DARAH*

    Halo *${nama}*
    Nomor Antrian Anda:

    *${kodeAftap}*
    Kode: ${kodeLog}
    Tanggal: ${tanggal}

    Silakan menunggu panggilan.
    Terima kasih 🙏`;

        let pesanEncoded = encodeURIComponent(pesan);
        let nomorBersih  = noHp.replace(/^0/, '62').replace(/\D/g, '');
        window.open(`https://wa.me/${nomorBersih}?text=${pesanEncoded}`, '_blank');
    };
    // ✅ FIX: assign_ruangan — setelah assign, refresh tabel
    let assign_ruangan = (logDonorId, nomor) => {
        $.post(base_url + '/log_donor/' + logDonorId + '/assign_ruangan',
            { _token, nomor_ruangan: nomor },
            () => search_log_donor()
        ).fail((xhr) => console.error(xhr.responseText));
    }

    let pilih_dokter = (dokter_id) => {
        if (!pemeriksaan_id) return;
        $.post(base_url + '/' + pemeriksaan_id, { _token, _method: 'put', dokter_id }, () => {
            $('#modal_call').modal('hide');
            search_log_donor();
        }).fail((xhr) => console.error(xhr.responseText));
    }

    let openDisplay = () => {
        window.open('{{ route('mobil_unit.pemeriksaan_mobil_hb.display_antrian') }}', '_blank',
            'width=1280,height=720,menubar=no,toolbar=no,scrollbars=no');
    }

    $form_search.submit((e) => {
        e.preventDefault();
        search_pemeriksaan();
    });

    init_form_element();
    init();
</script>
@endpush
@push('styles')
<style>
    @media print {
        @page { size: 80mm auto; margin: 0; }
        body  { margin: 0; padding: 0; }
    }
</style>
@endpush