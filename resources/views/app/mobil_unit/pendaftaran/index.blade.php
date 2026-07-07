@php($active_cabang = session('active_cabang', []))

@extends('layouts.index')

@section('title')
    pendaftaran_mobil - mobil_unit Transfusi Darah -
@endsection

@section('content')
    <div class="content flex-column-fluid" id="kt_content">
        <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
            <div class="card-body py-5">
                <div class="d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h1 class="d-flex align-items-center my-1">
                            <span class="text-dark fw-bold fs-1">pendaftaran_mobil Pasien</span>
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

        <div class="row g-6">
            <div class="col-xl-5">
                <div class="card card-flush rounded-4 border-0 shadow-xs mb-6">
                    <div class="card-header pt-6">
                        <div class="card-title">
                            <h3 class="fw-bold fs-4 text-dark mb-0">Cari Donor</h3>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        @if(empty($active_cabang))
                            <h5 class="text-center my-10">- Cabang Aktif Kosong -</h5>
                        @endif
                        @if(!empty($active_cabang))
                            <div class="notice d-flex bg-light-primary rounded-3 border border-primary border-dashed p-4 mb-5">
                                <i class="ki-duotone ki-information-5 fs-2tx text-primary me-4">
                                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <div class="fs-7 text-gray-700">Cari berdasarkan nama, kode donor, nomor ktp, atau nomor telfon. Jika donor belum terdaftar, tambahkan sebagai donor baru.</div>
                                    </div>
                                </div>
                            </div>

                            <form class="input-group mb-4" id="form_search_donor">
                                @csrf
                                <x-input name="search" prefix="donor_" class="rounded-start" caption="Nama, Kode Donor, No.KTP, No.Telp" />
                                <button class="btn btn-primary ps-6" type="submit">
                                    <i class="ki-duotone ki-magnifier fs-4"><span class="path1"></span><span class="path2"></span></i>
                                    Cari
                                </button>
                                <button class="btn btn-success pe-3" type="button" onclick="new_donor()">
                                    Donor Baru
                                    <i class="ki-duotone ki-plus fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </button>
                            </form>
                        @endif

                        <div id="search_donor_table"></div>
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="card card-flush rounded-4 border-0 shadow-xs h-100">
                    <form class="card-header pt-6" id="form_search">
                        @csrf
                        <div class="card-title flex-column">
                            <h3 class="fw-bold fs-4 text-dark mb-1">Antrian Hari Ini</h3>
                            <div class="fs-7 text-muted">
                                <span id="total_log_donor">0</span> donor terdaftar
                            </div>
                        </div>
                        <div class="card-toolbar d-flex flex-row gap-3">
                            <div class="d-flex align-items-center position-relative">
                                <i class="ki-duotone ki-magnifier fs-5 position-absolute ms-3 text-muted"><span class="path1"></span><span class="path2"></span></i>
                                <x-input name="search" prefix="log_donor_" class="form-control-sm ps-9 w-175px" caption="Filter pendaftaran_mobil ..." />
                            </div>
                            <div class="w-150px"><x-select name="step" class="form-select-sm" caption="Semua Status" :options="array_combine($steps, $steps)" data-control="select2" onchange="search_data()" /></div>
                        </div>
                    </form>
                    <div class="card-body pt-4">
                        <div id="table"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade modal-slide-right" tabindex="-1" id="modal_donor">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="modal_donor_content"></div>
        </div>
    </div>

    {{-- ★ Modal Tiket Antrian --}}
    <div class="modal fade" tabindex="-1" id="modal_tiket">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Nomor Antrian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pt-2">
                    <div id="tiket_preview">
                        {{-- diisi via JS --}}
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center gap-3">
                    <button class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                     <button class="btn btn-success btn-sm fw-bold" onclick="kirim_wa()">
                        <i class="ki-duotone ki-whatsapp fs-5 me-1"></i>
                        Kirim WA
                    </button>
                    <button class="btn btn-danger btn-sm fw-bold" onclick="print_tiket()">
                        <i class="ki-duotone ki-printer fs-5 me-1">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                        Cetak
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        let tiket_global = null;

        let $form_search_donor = $('#form_search_donor'), $search_donor_table = $('#search_donor_table'), $modal_donor = $('#modal_donor'), $modal_donor_content = $('#modal_donor_content');
        let $form_search = $('#form_search'), $table = $('#table');
        let _token = '{{ csrf_token() }}';
        let base_url = '{{ route('mobil_unit.pendaftaran_mobil.index') }}', donor_url = '{{ route('mobil_unit.donor.index') }}';
        let search_result = [];

        // ★ Batas usia layak donor (disamakan dengan backend)
        const USIA_MIN = 17;
        const USIA_MAX = 65;

        let search_donor = () => {
            let data = get_form_data($form_search_donor);
            $.post(base_url + '/search_donor', data, (result) => $search_donor_table.html(result)).fail((xhr) => $search_donor_table.html(xhr.responseText));
        }

        let search_data = () => {
            let data = get_form_data($form_search);
            $.post(base_url + '/search', data, (result) => $table.html(result)).fail((xhr) => $table.html(xhr.responseText));
        }

        let display_modal_donor = (item) => {
            $modal_donor_content.html(item);
            $modal_donor.modal('show');
        }

        let new_donor = () => {
            $.get(donor_url + '/create', (result) => display_modal_donor(result)).fail((xhr) => display_modal_donor(xhr.responseText));
        }

        let init = () => {
            $modal_donor_content.html('');
            $modal_donor.modal('hide');
        }

        // ═══════════════════════════════════════════════════════════════════
        // select_donor — cek "sudah daftar hari ini" DULU, baru umur
        // ═══════════════════════════════════════════════════════════════════
        let select_donor = (donor_id) => {
            $.get(donor_url + '/' + donor_id, (donor) => {
                const umur = donor.umur; // dikirim dari backend (DonorController@show)
                const sudahDaftarHariIni = donor.sudah_daftar_hari_ini; // dikirim dari backend

                // 1. Cek dulu apakah donor ini sudah didaftarkan hari ini
                if (sudahDaftarHariIni) {
                    Swal.fire({
                        title: 'Perhatian: Sudah Terdaftar Hari Ini',
                        html: `Donor <b>${donor.nama}</b> sudah didaftarkan pada antrian hari ini.<br>Apakah tetap ingin mendaftarkan ulang?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Tetap Daftarkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cek_umur_lalu_daftar(donor_id, umur, true); // forceDuplikat = true
                        }
                        // jika batal -> tidak lanjut apapun
                    });
                } else {
                    cek_umur_lalu_daftar(donor_id, umur, false);
                }
            }).fail((xhr) => {
                Swal.fire('Error', 'Gagal mengambil data donor', 'error');
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // cek batas umur 17-65 tahun, baru lanjut proses simpan
        // ═══════════════════════════════════════════════════════════════════
        let cek_umur_lalu_daftar = (donor_id, umur, forceDuplikat = false) => {
            if (umur !== null && umur !== undefined && (umur < USIA_MIN || umur > USIA_MAX)) {
                Swal.fire({
                    title: 'Perhatian: Batasan Umur',
                    text: `Umur donor adalah ${umur} tahun (di luar rentang ${USIA_MIN}-${USIA_MAX} tahun). Apakah tetap layak dan ingin lanjut?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Tidak, Batalkan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        proses_pendaftaran_mobil(donor_id, forceDuplikat, true); // forceUmur = true
                    } else {
                        Swal.fire('Dibatalkan', 'pendaftaran_mobil donor tidak dilanjutkan karena batasan umur.', 'info');
                    }
                });
            } else {
                proses_pendaftaran_mobil(donor_id, forceDuplikat, false);
            }
        }

        // ═══════════════════════════════════════════════════════════════════
        // proses_pendaftaran_mobil — kirim flag force/force_umur,
        //   dan handle 409 dari backend sebagai fallback/safety net
        // ═══════════════════════════════════════════════════════════════════
        let proses_pendaftaran_mobil = (donor_id, forceDuplikat = false, forceUmur = false) => {
            $.post(base_url, {
                _token,
                donor_id,
                force: forceDuplikat ? 1 : 0,
                force_umur: forceUmur ? 1 : 0
            }, (result) => {
                if (result.error) {
                    Swal.fire({ icon: 'error', title: result.error });
                    return;
                }
                $('#donor_search').html('');
                search_donor();
                search_data();

                if (result.tiket) {
                    cetak_tiket(result.tiket);
                }
            }).fail((xhr) => {
                // 409 dari backend = butuh konfirmasi tambahan (safety net)
                if (xhr.status === 409 && xhr.responseJSON) {
                    const jenis = xhr.responseJSON.need_confirmation;
                    const pesan = xhr.responseJSON.message;

                    if (jenis === 'sudah_daftar_hari_ini') {
                        Swal.fire({
                            title: 'Perhatian: Sudah Terdaftar Hari Ini',
                            text: pesan,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Tetap Daftarkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) proses_pendaftaran_mobil(donor_id, true, forceUmur);
                        });
                    } else if (jenis === 'umur_di_luar_rentang') {
                        Swal.fire({
                            title: 'Perhatian: Batasan Umur',
                            text: pesan,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) proses_pendaftaran_mobil(donor_id, forceDuplikat, true);
                        });
                    } else {
                        Swal.fire('Perhatian', pesan, 'warning');
                    }
                    return;
                }
                $('#error_log').html(xhr.responseText);
            });
        }

        // ★ Isi preview tiket dan tampilkan modal
        let cetak_tiket = (tiket) => {
            // ★ Ambil 4 digit terakhir dari kode
            tiket_global = tiket; 
            const no_antrian = String(tiket.kode).slice(-4);
            
            
            $('#tiket_preview').html(`
                <div id="tiket_print_area" style="
                    font-family: 'Courier New', monospace;
                    border: 2px dashed #dee2e6;
                    border-radius: 10px;
                    padding: 16px 14px;
                    max-width: 260px;
                    margin: 0 auto;
                    background: #fff;
                    color: #000;
                    font-size: 11px;
                ">
                    <div style="text-align:center; margin-bottom:10px;">
                        <div style="font-size:10px; font-weight:700; letter-spacing:2px; text-transform:uppercase;">
                            mobil_unit Transfusi Darah
                        </div>
                        <div style="font-size:9px; color:#888;">${tiket.cabang}</div>
                        <div style="border-top:1px dashed #ccc; margin:8px 0;"></div>
                    </div>

                    <div style="text-align:center; margin-bottom:12px;">
                        <div style="font-size:9px; color:#888; text-transform:uppercase; letter-spacing:1px;">
                            Nomor Antrian
                        </div>
                        <div style="
                            font-size:2.8rem;
                            font-weight:900;
                            line-height:1;
                            color:#c0392b;
                            letter-spacing:4px;
                            margin: 6px 0 4px;
                        ">${no_antrian}</div>
                        <div style="font-size:9px; color:#aaa;">${tiket.kode}</div>
                        <div style="font-size:9px; color:#888; margin-top:2px;">${tiket.waktu}</div>
                    </div>

                    <div style="border-top:1px dashed #ccc; margin:8px 0;"></div>

                    <table style="width:100%; font-size:10px; border-collapse:collapse;">
                        <tr>
                            <td style="color:#888; padding:2px 0; width:42%;">Nama</td>
                            <td style="font-weight:700; padding:2px 0;">${tiket.nama}</td>
                        </tr>
                        <tr>
                            <td style="color:#888; padding:2px 0;">Kode Donor</td>
                            <td style="font-weight:700; padding:2px 0;">${tiket.kode_donor}</td>
                        </tr>
                        <tr>
                            <td style="color:#888; padding:2px 0;">Gol. Darah</td>
                            <td style="font-weight:700; padding:2px 0;">${tiket.golongan}</td>
                        </tr>
                        <tr>
                            <td style="color:#888; padding:2px 0;">Petugas</td>
                            <td style="font-weight:700; padding:2px 0;">${tiket.petugas}</td>
                        </tr>
                    </table>

                    <div style="border-top:1px dashed #ccc; margin:8px 0;"></div>
                    <div style="text-align:center; font-size:9px; color:#aaa;">
                        Harap simpan nomor ini.<br>Terima kasih telah mendonor.
                    </div>
                </div>
            `);
            $('#modal_tiket').modal('show');
        }
        let cetak_kartu_donor = (
            nama,
            kode,
            tanggal_lahir,
            jenis_kelamin,
            alamat,
            no_telp,
            golongan,
            rhesus,
            foto
        ) => {

            const barcodeValue = kode;

            const html = `
            <div id="kartu_donor_print" style="
                width: 323px;
                height: 204px;
                border-radius: 12px;
                overflow: hidden;
                font-family: Arial, sans-serif;
                background: #fff;
                border: 1px solid #ccc;
                position: relative;
            ">

                <!-- HEADER -->
                <div style="
                    background: linear-gradient(90deg,#c62828,#ef5350);
                    height: 38px;
                    color: white;
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    padding:0 10px;
                    font-weight:bold;
                    font-size:11px;
                ">
                    <div>KARTU DONOR DARAH</div>
                    <div>PMI</div>
                </div>

                <!-- CONTENT -->
                <div style="display:flex;padding:8px;gap:8px;">

                    <!-- FOTO -->
                    <div>
                        <img src="${foto}"
                             style="
                                width:72px;
                                height:90px;
                                object-fit:cover;
                                border-radius:4px;
                                border:1px solid #ddd;
                             ">
                    </div>

                    <!-- INFO -->
                    <div style="flex:1;font-size:10px;line-height:1.4;">
                        <div><b>No. ID</b> : ${kode}</div>
                        <div><b>Nama</b> : ${nama}</div>
                        <div><b>Tgl. Lahir</b> : ${tanggal_lahir}</div>
                        <div><b>Jns. Kelamin</b> : ${jenis_kelamin}</div>
                        <div><b>Alamat</b> : ${alamat ?? '-'}</div>
                        <div><b>Telp/HP</b> : ${no_telp ?? '-'}</div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div style="
                    position:absolute;
                    bottom:8px;
                    left:10px;
                    right:10px;
                    display:flex;
                    align-items:end;
                    justify-content:space-between;
                ">
                    <div style="
                        font-size:42px;
                        font-weight:900;
                        line-height:1;
                        color:#222;
                    ">
                        ${golongan}${rhesus}
                    </div>

                    <div style="text-align:center;">
                        <svg id="barcode"></svg>
                        <div style="font-size:9px;">${barcodeValue}</div>
                    </div>
                </div>
            </div>
            `;

            const win = window.open('', '', 'width=400,height=300');

            win.document.write(`
                <html>
                <head>
                    <title>Kartu Donor</title>

                    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>

                    <style>
                        body{
                            margin:0;
                            padding:10px;
                            background:white;
                            display:flex;
                            justify-content:center;
                            align-items:center;
                        }

                        @media print{
                            @page{
                                size: 86mm 54mm;
                                margin:0;
                            }

                            body{
                                padding:0;
                            }
                        }
                    </style>
                </head>

                <body>
                    ${html}

                    <script>
                        JsBarcode("#barcode", "${barcodeValue}", {
                            format: "CODE128",
                            width: 1.2,
                            height: 32,
                            displayValue: false,
                            margin: 0
                        });

                        window.onload = () => {
                            setTimeout(() => {
                                window.print();
                                window.close();
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);

            win.document.close();
        }
        let cetak_form_pendaftaran_mobil = (data) => {

            let nextDate = new Date();
            nextDate.setDate(nextDate.getDate() + 75);

            let kembali = nextDate.toLocaleDateString('id-ID');

            const html = `
            <div style="
                width:100%;
                font-family:Arial,sans-serif;
                font-size:12px;
                color:#000;
            ">

                <table style="
                    width:100%;
                    border-collapse:collapse;
                    border:1px solid #000;
                ">
                    <tr>
                        <td style="padding:15px;">

                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        <div style="font-size:20px;font-weight:700;">
                                            PALANG MERAH INDONESIA
                                        </div>

                                        <div style="font-size:14px;">
                                            mobil_unit DONOR DARAH
                                        </div>
                                    </td>

                                    <td style="text-align:center;width:220px;">

                                        <div style="
                                            font-size:46px;
                                            font-weight:900;
                                            line-height:1;
                                        ">
                                            ${String(data.kode).slice(-4)}
                                        </div>

                                        <svg id="barcode"></svg>

                                        <div style="font-size:10px;">
                                            ${data.kode}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <hr>

                            <div style="
                                font-size:18px;
                                font-weight:700;
                                margin-bottom:10px;
                            ">
                                FORM pendaftaran_mobil DONOR
                            </div>

                            <table style="
                                width:100%;
                                border-collapse:collapse;
                                font-size:12px;
                            ">
                                <tr>
                                    <td width="200">Nomor pendaftaran_mobil</td>
                                    <td>: ${data.kode}</td>
                                </tr>

                                <tr>
                                    <td>Tanggal Daftar</td>
                                    <td>: ${data.tanggal_daftar} ${data.jam_daftar}</td>
                                </tr>

                                <tr>
                                    <td>Kode Donor</td>
                                    <td>: ${data.kode_donor}</td>
                                </tr>

                                <tr>
                                    <td>Nama Donor</td>
                                    <td>: ${data.nama}</td>
                                </tr>

                                <tr>
                                    <td>Jenis Kelamin</td>
                                    <td>: ${data.jenis_kelamin}</td>
                                </tr>

                                <tr>
                                    <td>Tanggal Lahir</td>
                                    <td>: ${data.tanggal_lahir}</td>
                                </tr>

                                <tr>
                                    <td>Golongan Darah</td>
                                    <td>: ${data.golongan} ${data.rhesus}</td>
                                </tr>

                                <tr>
                                    <td>Donor Ke</td>
                                    <td>: ${data.donor_ke}</td>
                                </tr>

                                <tr>
                                    <td>Alamat</td>
                                    <td>: ${data.alamat ?? '-'}</td>
                                </tr>

                               <tr>
            <td>No HP</td>
            <td>: ${data.no_hp ?? ''}</td>
        </tr>
        </table>

        <div style="
            border-top:1px dashed #000;
            margin:10px 0;
        "></div>

        <div style="
            font-size:12px;
            line-height:1.8;
        ">

            <b style="text-decoration-line: underline;">
                Pemeriksaan Darah Awal
            </b>
            <br>

            Gol Darah - Rh :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            HB :
            <br>

            Petugas Periksa :
            <br>

            Tanggal / Jam :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            BB :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            HCT :
            <br>

            <div style="border-top:1px dashed #000;margin:8px 0;"></div>

            <b style="text-decoration-line: underline;">
                Pemeriksaan Kesehatan
            </b>
            <br>

            Hasil Periksa :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Tensi :
            <br>

            Petugas Periksa :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            Jns Kantong :
            <br>

            Tanggal / Jam :
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            CC diambil :
            <br>

            <div style="border-top:1px dashed #000;margin:8px 0;"></div>

            <b style="text-decoration-line: underline;">
                Aftaper
            </b>
            <br>

            Petugas Aftap :
            <br>

            Tanggal / Jam :
            <br>

            <div style="border-top:1px dashed #000;margin:8px 0;"></div>

        </div>

        <table style="
            width:100%;
            border-collapse:collapse;
            font-size:12px;
        ">

                            <hr>

                            <div style="
                                margin-top:15px;
                                font-size:12px;
                            ">
                                Harap kembali donor pada tanggal:
                                <b>${kembali}</b>
                            </div>

                        </td>
                    </tr>
                </table>
            </div>
            `;

            const win = window.open('', '_blank', 'width=900,height=1000');

            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Form pendaftaran_mobil</title>

                    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>

                    <style>
                        body{
                            margin:0;
                            padding:20px;
                            background:#fff;
                            font-family:Arial,sans-serif;
                        }

                        @media print{
                            @page{
                                size:A4;
                                margin:10mm;
                            }

                            body{
                                padding:0;
                            }
                        }
                    </style>
                </head>

                <body>
                    ${html}

                    <script>

                        window.onload = function(){

                            JsBarcode("#barcode", "${data.kode}", {
                                format: "CODE128",
                                width: 1.5,
                                height: 40,
                                displayValue: false,
                                margin: 0
                            });

                            setTimeout(() => {
                                window.print();
                            }, 700);

                        }

                    <\/script>

                </body>
                </html>
            `);

            win.document.close();
        }


        let kirim_wa = () => {
            if (!tiket_global) return;

            // ⚠️ pastikan ada nomor HP dari backend
            let nomor = tiket_global.no_hp || '';

            if (!nomor) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nomor HP kosong',
                    text: 'Nomor WhatsApp donor tidak tersedia'
                });
                return;
            }

            // format nomor (08 → 628)
            nomor = nomor.replace(/^0/, '62');

            const no_antrian = String(tiket_global.kode).slice(-4);

            let pesan = `*mobil_unit TRANSFUSI DARAH*\n\n` +
                `Halo *${tiket_global.nama}*\n\n` +
                `Nomor Antrian Anda:\n` +
                `*${no_antrian}*\n\n` +
                `Kode: ${tiket_global.kode}\n` +
                `Tanggal: ${tiket_global.waktu}\n\n` +
                `Silakan menunggu panggilan.\nTerima kasih 🙏`;

            let url = `https://wa.me/${nomor}?text=${encodeURIComponent(pesan)}`;

            window.open(url, '_blank');
        }
        // ★ Cetak hanya area tiket (tanpa header/footer browser)
        let print_tiket = () => {
            const konten = document.getElementById('tiket_print_area').outerHTML;
            const win = window.open('', '_blank', 'width=320,height=500');
            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Tiket Antrian</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body {
                            display: flex;
                            justify-content: center;
                            align-items: flex-start;
                            padding: 10px;
                            background: #fff;
                        }
                        @media print {
                            @page {
                                size: 80mm auto;
                                margin: 4mm;
                            }
                            body { padding: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${konten}
                    <script>
                        window.onload = () => {
                            window.print();
                            setTimeout(() => window.close(), 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            win.document.close();
        }
        let cetak_tiket_from_row = (kode, nama, kode_donor, golongan, petugas) => {
            cetak_tiket({
                kode:       kode,
                nama:       nama,
                kode_donor: kode_donor,
                golongan:   golongan,
                petugas:    petugas,
                waktu:      '', // tidak perlu waktu saat cetak ulang
                cabang:     '{{ session("active_cabang.nama") ?? "" }}',
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // ★ FIXED: init_form (form tambah donor baru di modal pendaftaran_mobil)
        //   sekarang kirim force_umur ke backend + handle 409
        // ═══════════════════════════════════════════════════════════════════
        let init_form = () => {
            let $form_info = $('#form_info');
            
            $form_info.submit((e) => {
                e.preventDefault();

                let usia = parseInt($('input[name="usia"]').val()); 

                if (usia < USIA_MIN || usia > USIA_MAX) {
                    Swal.fire({
                        title: 'Konfirmasi Usia',
                        text: `Usia pendonor ${usia} tahun (di luar rentang ${USIA_MIN}-${USIA_MAX} thn). Apakah pendonor tetap layak dan ingin melanjutkan pendaftaran_mobil?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Tetap Simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            execute_save_donor($form_info, true); // ★ forceUmur = true
                        }
                    });
                } else {
                    execute_save_donor($form_info, false);
                }
            });
        }

        // ═══════════════════════════════════════════════════════════════════
        // ★ FIXED: execute_save_donor — kirim force_umur ke backend,
        //   dan tangani 409 sebagai fallback/safety net
        // ═══════════════════════════════════════════════════════════════════
        let execute_save_donor = ($form, forceUmur = false) => {
            let url = donor_url;
            let data = new FormData($form.get(0));
            if (forceUmur) data.append('force_umur', 1); // ★ kirim flag ke backend

            $.ajax({
                url,
                type: 'post',
                data,
                cache: false,
                processData: false,
                contentType: false,
                success: (result) => {
                    init(); // Tutup modal
                    select_donor(result.id); // Pilih donor -> otomatis lewat pengecekan duplikat & umur lagi
                    Swal.fire('Berhasil', 'Data donor berhasil disimpan', 'success');
                }
            }).fail((xhr) => {
                // ★ 409 dari backend = butuh konfirmasi umur (safety net kalau flag belum terkirim)
                if (xhr.status === 409 && xhr.responseJSON) {
                    const pesan = xhr.responseJSON.message;
                    Swal.fire({
                        title: 'Konfirmasi Usia',
                        text: pesan,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Tetap Simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) execute_save_donor($form, true);
                    });
                    return;
                }
                // Jika ada error lain (misal validasi backend gagal), tampilkan di sini
                if(typeof error_handle === 'function') {
                    error_handle(xhr.responseText);
                } else {
                    Swal.fire('Error', 'Gagal menyimpan data', 'error');
                }
            });
        }

        let confirm_delete = (id) => {
            Swal.fire(swal_delete_params).then((result) => {
                if (result.isConfirmed) $.post(base_url + '/' + id, {_method: 'delete', _token}, (data) => {
                    if (data.error) swal.fire({icon: 'error', title: data.error}).then(() => search_data());
                    else swal.fire('Berhasil Dihapus').then(() => search_data());
                }).fail((xhr) => $table.html(xhr.responseText));
            });
        }

        $form_search_donor.submit((e) => {
            e.preventDefault();

            console.log(search_result.length);
            if (search_result.length > 0) {
                console.log(search_result[0].id);
                select_donor(search_result[0].id);
            }

            search_donor();
        });

        $form_search.submit((e) => {
            e.preventDefault();
            search_data();
        });

        init_form_element();
        search_data();
    </script>
@endpush