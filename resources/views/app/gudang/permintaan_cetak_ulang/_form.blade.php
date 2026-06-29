<style>
    .pcu-modal-header {
        background: linear-gradient(135deg, #f23928 0%, #fcfdff 100%);
        padding: 1.25rem 1.5rem;
        border: none;
        position: relative;
    }
    .pcu-modal-header h3 { color: #fff; font-weight: 700; font-size: 1.05rem; margin: 0; }
    .pcu-modal-header .sub { color: rgba(255,255,255,.8); font-size: .78rem; margin-top: .15rem; }
    .pcu-modal-header .btn-close-modal {
        position: absolute; top: 1rem; right: 1rem;
        background: rgba(255,255,255,.25); color: #fff; border: none;
        width: 32px; height: 32px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
    }
    .pcu-section-title {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        margin: 1.25rem 0 .6rem;
        padding-bottom: .4rem;
        border-bottom: 1px solid #eef2f7;
    }
    .pcu-section-title:first-child { margin-top: 0; }
    .pcu-status-pill {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .8rem; border-radius: 20px;
        font-size: .72rem; font-weight: 700;
    }
    .pcu-status-pill.diajukan  { background:#fef3c7; color:#b45309; }
    .pcu-status-pill.disetujui { background:#cffafe; color:#0891b2; }
    .pcu-status-pill.ditolak   { background:#fee2e2; color:#dc2626; }
    .pcu-status-pill.selesai   { background:#dcfce7; color:#16a34a; }
    .pcu-approval-box {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
        padding: 1rem;
        margin-top: .5rem;
    }
    .pcu-approval-box.ditolak { background:#fef2f2; border-color:#fca5a5; }
    .pcu-approval-box.disetujui,
    .pcu-approval-box.selesai { background:#ecfeff; border-color:#67e8f9; }
</style>

<form id="form_info">
@csrf

<div class="pcu-modal-header">
    <h3>{{ !empty($permintaan) ? 'Detail Permintaan' : 'Buat Permintaan Cetak Ulang' }}</h3>
    <div class="sub">
        @if(!empty($permintaan))
            {{ $permintaan->no_surat }}
        @else
            Formulir Permohonan Cetak Ulang Label Barcode
        @endif
    </div>
    <button type="button" class="btn-close-modal" data-bs-dismiss="modal">
        <i class="ki-duotone ki-cross fs-3"></i>
    </button>
</div>

<div class="modal-body">

@if(!empty($permintaan))
<div class="d-flex justify-content-between align-items-center">
    <span class="pcu-status-pill {{ $permintaan->status }}">
        <i class="fas fa-circle" style="font-size:.5rem;"></i> {{ ucfirst($permintaan->status) }}
    </span>
    <span class="text-muted fs-8">Diajukan {{ \Illuminate\Support\Carbon::parse($permintaan->tanggal_permohonan)->translatedFormat('d M Y') }}</span>
</div>
@endif

<div class="pcu-section-title">Data Pemohon</div>
<div class="row g-4">

    <div class="col-md-6">
        <x-io-input
            type="date"
            name="tanggal_permohonan"
            caption="Tanggal Permohonan"
            :value="$permintaan->tanggal_permohonan ?? date('Y-m-d')"
            :viewtype="2"
            required
            :readonly="!empty($permintaan)"
        />
    </div>

    <div class="col-md-6">
        <x-io-select
            name="bagian_id"
            caption="Bagian / Seksi"
            :options="$bagian_options ?? []"
            :value="$permintaan->bagian_id ?? ''"
            class="form-select"
            :disabled="!empty($permintaan)"
        />
    </div>

    <div class="col-md-6">
        <x-io-input
            name="nama_pemohon"
            caption="Nama Pemohon"
            :value="$permintaan->nama_pemohon ?? ''"
            :viewtype="2"
            required
            :readonly="!empty($permintaan)"
        />
    </div>

    <div class="col-md-6">
        <x-io-input
            name="jabatan_pemohon"
            caption="Jabatan"
            :value="$permintaan->jabatan_pemohon ?? ''"
            :viewtype="2"
            :readonly="!empty($permintaan)"
        />
    </div>

</div>

<div class="pcu-section-title">Detail Permintaan</div>
<div class="row g-4">

    <div class="col-md-8">
        <x-io-select
            name="pendataan_kantong_id"
            caption="No Barcode (Kode Kantong)"
            :options="$barcode_options ?? []"
            :value="$permintaan->pendataan_kantong_id ?? ''"
            class="form-select"
            required
            :disabled="!empty($permintaan)"
        />
    </div>

    <div class="col-md-4">
        <x-io-input
            type="number"
            name="jumlah_cetak"
            caption="Jumlah Cetak"
            :value="$permintaan->jumlah_cetak ?? 1"
            :viewtype="2"
            min="1"
            required
            :readonly="!empty($permintaan)"
        />
    </div>

    <div class="col-md-12">
        <x-io-input
            name="alasan"
            caption="Alasan Permohonan"
            :value="$permintaan->alasan ?? ''"
            type="textarea"
            required
            :readonly="!empty($permintaan)"
        />
    </div>

</div>

@if(!empty($permintaan) && $permintaan->status !== 'diajukan')
<div class="pcu-section-title">Hasil Persetujuan</div>
<div class="pcu-approval-box {{ $permintaan->status }}">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="text-muted fs-8 fw-bold">PETUGAS YANG MELAYANI</div>
            <div class="fw-bold">{{ $permintaan->nama_petugas_melayani ?? '-' }}</div>
        </div>
        <div class="col-md-6">
            <div class="text-muted fs-8 fw-bold">MENGETAHUI (KASI)</div>
            <div class="fw-bold">{{ $permintaan->nama_kasi ?? '-' }}</div>
        </div>
        <div class="col-md-6">
            <div class="text-muted fs-8 fw-bold">TANGGAL DIPROSES</div>
            <div class="fw-bold">{{ $permintaan->tgl_disetujui ? \Illuminate\Support\Carbon::parse($permintaan->tgl_disetujui)->translatedFormat('d M Y') : '-' }}</div>
        </div>
        @if($permintaan->catatan)
        <div class="col-md-12">
            <div class="text-muted fs-8 fw-bold">CATATAN</div>
            <div>{{ $permintaan->catatan }}</div>
        </div>
        @endif
    </div>
</div>
@endif

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light me-3" onclick="init()">Tutup</button>
    @if(empty($permintaan) || $permintaan->status === 'diajukan')
        @if(empty($permintaan))
        <button type="submit" class="btn btn-primary">
            <i class="ki-duotone ki-check fs-2"></i> Kirim Permintaan
        </button>
        @else
        <button type="button" class="btn btn-success" onclick="approve_permintaan({{ $permintaan->id }})">
            <i class="fas fa-check"></i> Setujui
        </button>
        <button type="button" class="btn btn-danger" onclick="reject_permintaan({{ $permintaan->id }})">
            <i class="fas fa-times"></i> Tolak
        </button>
        @endif
    @elseif($permintaan->status === 'disetujui')
        <button type="button" class="btn btn-info text-white" onclick="selesaikan_permintaan({{ $permintaan->id }})">
            <i class="fas fa-flag-checkered"></i> Tandai Selesai
        </button>
    @endif
</div>

</form>

<script>
init_form_element();
init_form(@json($permintaan->id ?? ''));

// Aktifkan pencarian server-side (select2 ajax) untuk "No Barcode (Kode
// Kantong)" -- supaya bisa mencari ke SELURUH data, bukan cuma 20 opsi
// awal yang sudah di-load. destroy() dulu untuk menghindari konflik kalau
// init_form_element() di atas sudah otomatis menjadikan select ini select2
// versi default (tanpa ajax).
(function () {
    const $select = $('select[name="pendataan_kantong_id"]');
    if (!$select.length || !$.fn.select2) return;

    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }

    // Cari container modal terdekat. PENTING: tanpa dropdownParent, select2
    // merender kotak pencariannya menempel ke <body> -- lalu Bootstrap
    // modal otomatis "menarik" fokus keyboard balik ke dalam modal setiap
    // kali ada elemen di luar modal yang dicoba diketik, sehingga terlihat
    // ada kotak cari tapi tidak bisa diketik sama sekali. Dengan
    // dropdownParent, select2 dirender DI DALAM modal sehingga tidak kena
    // focus-trap tersebut.
    const $modalParent = $select.closest('.modal');

    $select.select2({
        width: '100%',
        placeholder: 'Ketik kode barcode untuk mencari...',
        minimumInputLength: 1,
        dropdownParent: $modalParent.length ? $modalParent : $(document.body),
        language: {
            inputTooShort: () => 'Ketik minimal 1 huruf untuk mencari',
            searching: () => 'Mencari...',
            noResults: () => 'Tidak ditemukan',
        },
        ajax: {
            url: '{{ route('gudang.permintaan_cetak_ulang.find_barcode') }}',
            dataType: 'json',
            delay: 300,
            data: (params) => ({ q: params.term }),
            processResults: (data) => ({ results: data }),
            cache: true,
            error: function (xhr) {
                console.error('Pencarian barcode gagal:', xhr.status, xhr.responseText);
            },
        },
    });
})();
</script>