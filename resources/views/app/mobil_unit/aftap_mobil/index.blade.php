@php($active_cabang = session('active_cabang', []))
@php($nomor_ruangan = session('nomor_ruangan'))

@extends('layouts.index')

@section('title')
    Aftap - Unit Tranfusi Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
            <div class="card-body py-5">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1">
                            <span class="text-dark fw-bold fs-1">Aftap</span>
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
                        <a href="{{ route('unit.aftap.display_antrian') }}" target="_blank"
                           class="btn btn-sm btn-light-danger fw-semibold">
                            <i class="ki-duotone ki-screen fs-5 me-1">
                                <span class="path1"></span><span class="path2"></span>
                                <span class="path3"></span><span class="path4"></span>
                            </i>
                            Display Antrian
                        </a>
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
   {{-- Modal Pilih Bed --}}
<div class="modal fade" tabindex="-1" id="modal_pilih_bed">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Bed Aftap</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                     data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body">
                <p class="text-muted fs-7 mb-4">
                    Memanggil: <strong id="modal_bed_nama"></strong>
                </p>
                <div class="row g-3" id="bed_buttons">
                    @for($i = 1; $i <= 18; $i++)
                        <div class="col-4">
                            <button type="button"
                                    class="btn btn-light-primary btn-sm w-100 fw-bold"
                                    onclick="pilih_bed({{ $i }})">
                                Bed {{ $i }}
                            </button>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    let _token   = '{{ csrf_token() }}';
    let base_url = '{{ route('unit.aftap.index') }}';

    let $table_log_donor = $('#table_log_donor'),
        $info_log_donor  = $('#info_log_donor'),
        $info            = $('#info'),
        $table           = $('#table');
    let $form_search = $('#form_search'),
        $card_search = $('#card_search');

    /* ── init ── */
    let init = () => {
        $info_log_donor.html('');
        $info.html('');
        $table_log_donor.show();
        $card_search.show();
        search_pemeriksaan();
        search_log_donor();
    }

    /* ── antrian kiri ── */
    let search_log_donor = () => {
        $.post(base_url + '/log_donor/search', { _token }, (result) => {
            $table_log_donor.html(result);
        }).fail((xhr) => $table_log_donor.html(xhr.responseText));
    }

    let select_log = (id) => {
        $.get(base_url + '/log_donor/' + id, (result) => {
            $info_log_donor.html(result);
        }).fail((xhr) => $info_log_donor.html(xhr.responseText));
    }

    /* ── panel kanan (form aftap) ── */
    let info = (id) => {
        $table_log_donor.hide();
        $card_search.hide();
        $.get(base_url + '/' + id, (result) => {
            $info.html(result);
        }).fail((xhr) => $info.html(xhr.responseText));
    }

    let init_form = (id) => {
        let $form_info = $('#form_info');
        $form_info.submit((e) => {
            e.preventDefault();
            let url  = base_url + '/' + id + '?_method=put';
            let data = new FormData($form_info.get(0));
            $.ajax({
                url, type: 'post', data,
                cache: false, processData: false, contentType: false,
                success: () => {
                    Swal.fire({ icon: 'success', title: 'Aftap Berhasil Disimpan' })
                        .then(() => init());
                },
            }).fail((xhr) => error_handle(xhr.responseText));
        });
    }

    /* ── tabel riwayat ── */
    let search_pemeriksaan = () => {
        let data = get_form_data($form_search);
        $.post(base_url + '/search', data, (result) => {
            $table.html(result);
        }).fail((xhr) => $table.html(xhr.responseText));
    }

    $form_search.submit((e) => {
        e.preventDefault();
        search_pemeriksaan();
    });

    /* ══════════════════════════════════════════════════════
       PANGGIL DONOR — Pilih bed via SweetAlert (tanpa modal)
    ══════════════════════════════════════════════════════ */
    let _currentLengan = '';
    let call_donor = (log_donor_id, nama,lengan) => {
        _currentLengan = lengan; 
        // Build grid 10 bed (2 baris × 5 kolom)
        let bedHtml = '<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;padding:4px 0;">';
        for (let i = 1; i <= 18; i++) {
            bedHtml += `
                <button type="button" id="sbtn_${i}"
                    onclick="pilih_bed_confirm(${log_donor_id}, '${nama.replace(/'/g,"\\'")}', ${i})"
                    style="
                        padding: 14px 6px;
                        border: 2px solid #3b82f6;
                        background: rgba(59,130,246,0.12);
                        color: #93c5fd;
                        border-radius: 12px;
                        font-size: 1rem;
                        font-weight: 700;
                        cursor: pointer;
                        transition: all .15s;
                        line-height: 1.2;
                    "
                    onmouseover="this.style.background='rgba(59,130,246,0.3)'"
                    onmouseout="this.style.background='rgba(59,130,246,0.12)'"
                >
                    <div style="font-size:1.3rem;">🛏</div>
                    Bed ${i}
                </button>`;
        }
        bedHtml += '</div>';

        Swal.fire({
            title: `<span style="font-size:1rem;font-weight:600;">Pilih Bed untuk</span><br>
                    <span style="font-size:1.3rem;font-weight:800;color:#f87171;">${nama}</span>`,
            html: bedHtml,
            showConfirmButton: false,
            showCloseButton: true,
            background: '#1e293b',
            color: '#e2e8f0',
            width: 620,
            customClass: { popup: 'swal-dark-popup' }
        });
    }

    let pilih_bed_confirm = (log_donor_id, nama, bed,lengan) => {
    for (let i = 1; i <= 18; i++) {
        const el = document.getElementById(`sbtn_${i}`);
        if (!el) continue;
        if (i === bed) {
            el.style.background  = 'rgba(229,62,62,0.4)';
            el.style.borderColor = '#ef4444';
            el.style.color       = '#fca5a5';
        }
    }

    $.post(base_url + '/log_donor/' + log_donor_id + '/panggil',
        { _token, bed,lengan  },
        (res) => {
            
           speak_antrian(res.nama, res.nomor_antrian, `bed ${res.bed}`);

            Swal.close();

            Swal.fire({
                icon: 'success',
                title: `<span style="font-size:1rem;">${res.nama}</span>`,
                html: `
                    <div style="margin:10px 0;">
                        <div style="font-size:0.8rem;color:#94a3b8;margin-bottom:6px;">Nomor Antrian</div>
                        <div style="font-size:1.8rem;font-weight:900;color:#f87171;line-height:1.2;">
                            ${res.nomor_antrian}
                        </div>
                         <div style="font-size:0.85rem;color:#94a3b8;margin-top:6px;">
                            Lengan <strong style="color:#${res.lengan==='Kanan'?'60a5fa':'fbbf24'}">${res.lengan}</strong>
                        </div>
                    </div>
                    <div style="margin-top:12px;">
                        <span style="
                            background:rgba(29,78,216,0.3);
                            border:1px solid rgba(29,78,216,0.5);
                            color:#93c5fd;
                            border-radius:10px;
                            padding:8px 24px;
                            font-size:1.1rem;
                            font-weight:700;
                        ">&#x1F6CF; BED ${res.bed}</span>
                    </div>`,
                timer: 2500,
                showConfirmButton: false,
                background: '#1e293b',
                color: '#e2e8f0',
            });

            search_log_donor();
            search_pemeriksaan();
        }
    ).fail((xhr) => {
        Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseText });
    });
}

let speak_antrian = (nama, nomorAntrian, tujuan) => {
    let text = `Nomor antrian ${nomorAntrian}, atas nama ${nama}, silakan menuju ${tujuan}`;

    let speech = new SpeechSynthesisUtterance(text);
    speech.lang  = 'id-ID';
    speech.rate  = 0.9;
    speech.pitch = 1;

    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(speech);
};

// ✅ print_antrian thermal 80mm
let print_antrian = (nama, kodeAftap, kodeLog, tanggal) => {
    let content = `
    <html>
    <head>
        <title>Cetak Antrian</title>
        <style>
            @page { size: 80mm auto; margin: 0; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Courier New', Courier, monospace;
                width: 80mm; padding: 4mm 5mm;
                font-size: 12px; color: #000;
            }
            .center  { text-align: center; }
            .bold    { font-weight: bold; }
            .header  { font-size: 14px; font-weight: bold; text-align: center; line-height: 1.4; }
            .divider { border-top: 1px dashed #000; margin: 4px 0; }
            .label   { font-size: 11px; color: #333; text-align: center; margin-top: 4px; }
            .number  { font-size: 36px; font-weight: bold; text-align: center; letter-spacing: 3px; margin: 6px 0; line-height: 1; }
            .row     { display: flex; justify-content: space-between; font-size: 11px; margin: 2px 0; }
            .row span:last-child { text-align: right; font-weight: bold; }
            .footer  { font-size: 11px; text-align: center; margin-top: 4px; line-height: 1.6; }
        </style>
    </head>
    <body>
        <div class="header">&#x1F3E5; UNIT TRANSFUSI DARAH</div>
        <div class="divider"></div>
        <div class="label">Halo,</div>
        <div class="center bold" style="font-size:13px;margin:2px 0 6px;">${nama}</div>
        <div class="label">Nomor Antrian Anda:</div>
        <div class="number">${kodeAftap}</div>
        <div class="divider"></div>
        <div class="row"><span>Kode</span><span>${kodeLog}</span></div>
        <div class="row"><span>Tanggal</span><span>${tanggal}</span></div>
        <div class="divider"></div>
        <div class="footer">Silakan menunggu panggilan.<br>Terima kasih &#x1F64F;</div>
        <div style="margin-top:8mm;"></div>
    </body>
    </html>`;

    let w = window.open('', '', 'width=340,height=600');
    w.document.write(content);
    w.document.close();
    w.focus();
    w.onload = () => { w.print(); w.close(); };
    setTimeout(() => { try { w.print(); w.close(); } catch(e) {} }, 500);
};

// ✅ kirim WA via wa.me
let kirim_wa = (logDonorId, noHp, nama, kodeAftap, kodeLog, tanggal) => {
    let pesan =
`&#x1F3E5; *UNIT TRANSFUSI DARAH*

Halo *${nama}*
Nomor Antrian Anda:

*${kodeAftap}*
Kode: ${kodeLog}
Tanggal: ${tanggal}

Silakan menunggu panggilan.
Terima kasih &#x1F64F;`;

    let nomorBersih = noHp.replace(/^0/, '62').replace(/\D/g, '');
    window.open(`https://wa.me/${nomorBersih}?text=${encodeURIComponent(pesan)}`, '_blank');
};
    init_form_element();
    init();

    // Auto refresh antrian setiap 15 detik
    setInterval(() => search_log_donor(), 15000);
</script>
@endpush