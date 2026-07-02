@extends('layouts.index')

@section('title', $isEdit ? 'Ubah Pemberian Awal Referal' : 'Tambah Pemberian Awal Referal')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
<style>
    .par-shell {
        --par-bg: #f1f5f9;
        --par-surface: #ffffff;
        --par-border: #e2e8f0;
        --par-ink: #1e293b;
        --par-muted: #64748b;
        --par-primary: #0f766e;
        --par-primary-dark: #0d5c56;
        --par-primary-soft: #ccfbf1;
        --par-blood: #be123c;
        --par-blood-soft: #ffe4e6;
        --par-amber-soft: #fff7ed;
        --par-radius: 14px;
        --par-shadow: 0 1px 2px rgba(15,23,42,.04), 0 8px 24px -12px rgba(15,23,42,.10);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: var(--par-ink);
        background: var(--par-bg);
        padding: 28px 32px 56px;
        max-width: 1600px;
        margin: 0 auto;
        box-sizing: border-box;
    }
    .par-shell *, .par-shell *::before, .par-shell *::after { box-sizing: border-box; }

    .par-eyebrow { font-size: 12px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--par-primary); margin: 0 0 4px; }
    .par-title { font-size: 24px; font-weight: 800; margin: 0; letter-spacing: -.01em; }
    .par-headrow { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
    .par-back-link { font-size: 13.5px; font-weight: 600; color: var(--par-muted); text-decoration: none; }
    .par-back-link:hover { color: var(--par-ink); }

    .par-alert { border-radius: 10px; padding: 12px 16px; font-size: 14px; margin-bottom: 18px; border: 1px solid transparent; }
    .par-alert-error { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .par-alert-error ul { margin: 6px 0 0; padding-left: 18px; }

    .par-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
    @media (max-width: 980px) { .par-grid-2 { grid-template-columns: 1fr; } }
    .par-stack { display: flex; flex-direction: column; gap: 22px; }

    .par-card { background: var(--par-surface); border: 1px solid var(--par-border); border-radius: var(--par-radius); box-shadow: var(--par-shadow); position: relative; overflow: hidden; }
    .par-card::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--par-primary), #5eead4); }
    .par-card-pad { padding: 20px; }
    .par-card-title { font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--par-muted); margin: 0 0 16px; }
    .par-section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }

    .par-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .par-field-full { grid-column: 1 / -1; }
    .par-field label { display: block; font-size: 12px; font-weight: 600; color: var(--par-muted); margin-bottom: 6px; }
    .par-field input, .par-field select { width: 100%; border: 1px solid var(--par-border); border-radius: 8px; padding: 9px 11px; font-size: 13.5px; color: var(--par-ink); background: #fff; }
    .par-field input:focus, .par-field select:focus { outline: none; border-color: var(--par-primary); box-shadow: 0 0 0 3px var(--par-primary-soft); }
    .par-field input[disabled] { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }
    .par-field-hint { margin-top: 6px; font-size: 12px; color: var(--par-blood); }

    .par-search-row { display: flex; gap: 8px; }
    .par-search-row input { flex: 1; }
    .par-btn { display: inline-flex; align-items: center; gap: 6px; border: none; border-radius: 8px; padding: 9px 16px; font-size: 13.5px; font-weight: 600; cursor: pointer; white-space: nowrap; text-decoration: none; }
    .par-btn-dark { background: #1e293b; color: #fff; }
    .par-btn-dark:hover { background: #0f172a; }
    .par-btn-primary { background: var(--par-primary); color: #fff; }
    .par-btn-primary:hover { background: var(--par-primary-dark); }
    .par-btn-ghost { background: transparent; color: var(--par-muted); }
    .par-btn-ghost:hover { background: #f1f5f9; color: var(--par-ink); }

    .par-radio-group { display: flex; gap: 18px; align-items: center; padding-top: 6px; }
    .par-radio-group label, .par-checkbox-inline label { display: inline-flex; align-items: center; gap: 6px; font-size: 13.5px; color: var(--par-ink); cursor: pointer; margin: 0; }
    .par-radio-group input[type=radio], .par-checkbox-inline input[type=checkbox] { accent-color: var(--par-primary); width: 16px; height: 16px; margin: 0; }
    .par-inline-fields { display: flex; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
    .par-w-90 { width: 90px; flex: none; }

    .par-divider { border: none; border-top: 1px solid var(--par-border); margin: 18px 0; }

    .par-mini-table-wrap { border: 1px solid var(--par-border); border-radius: 10px; overflow: auto; max-height: 320px; }
    .par-mini-table-wrap-open { border: 1px solid var(--par-border); border-radius: 10px; overflow: visible; }
    .par-mini-table { width: 100%; border-collapse: collapse; font-size: 12.5px; min-width: 560px; }
    .par-mini-table thead th { position: sticky; top: 0; background: #f8fafc; color: var(--par-muted); text-transform: uppercase; letter-spacing: .04em; font-size: 10.5px; font-weight: 700; padding: 9px 10px; border-bottom: 1px solid var(--par-border); white-space: nowrap; text-align: left; }
    .par-mini-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; white-space: nowrap; }
    .par-mini-table tbody tr:last-child td { border-bottom: none; }
    .par-mini-table tbody tr.is-selected { background: var(--par-amber-soft); }
    .par-mini-empty { text-align: center; color: var(--par-muted); padding: 28px 12px; white-space: normal; }

    .par-row-input { width: 100%; border: 1px solid var(--par-border); border-radius: 6px; padding: 6px 8px; font-size: 12.5px; }
    .par-row-input:focus { outline: none; border-color: var(--par-primary); }
    .par-w-60 { width: 60px; } .par-w-70 { width: 70px; } .par-w-90b { width: 90px; }

    .par-summary-line { display: flex; justify-content: flex-end; gap: 6px; font-size: 14px; margin-top: 12px; }
    .par-summary-value { font-weight: 700; }
    .par-summary-accent { color: var(--par-primary); }

    .par-mini-btn { border: none; background: var(--par-primary-soft); color: var(--par-primary-dark); font-size: 12px; font-weight: 700; padding: 6px 12px; border-radius: 8px; cursor: pointer; }
    .par-mini-btn:hover { background: #99f6e4; }
    .par-mini-btn-ghost { border: none; background: transparent; color: var(--par-primary-dark); font-size: 12px; font-weight: 600; cursor: pointer; }

    .par-remove-btn { border: none; background: transparent; color: var(--par-blood); font-size: 16px; line-height: 1; cursor: pointer; padding: 2px 8px; border-radius: 6px; }
    .par-remove-btn:hover { background: var(--par-blood-soft); }

    .par-action-bar { margin-top: 22px; display: flex; justify-content: flex-end; gap: 10px; background: var(--par-surface); border: 1px solid var(--par-border); border-radius: var(--par-radius); padding: 16px 20px; box-shadow: var(--par-shadow); }

    .par-suggest-wrap { position: relative; }
    .par-suggest-box { position: absolute; z-index: 30; top: 100%; left: 0; min-width: 220px; background: #fff; border: 1px solid var(--par-border); border-radius: 8px; box-shadow: 0 10px 28px -8px rgba(15,23,42,.18); margin-top: 3px; max-height: 200px; overflow-y: auto; }
    .par-suggest-item { padding: 7px 10px; font-size: 12px; cursor: pointer; white-space: normal; border-bottom: 1px solid #f1f5f9; }
    .par-suggest-item:last-child { border-bottom: none; }
    .par-suggest-item:hover { background: var(--par-amber-soft); }
    .par-suggest-item small { display: block; color: var(--par-muted); font-size: 10.5px; margin-top: 1px; }
    .par-suggest-empty { padding: 8px 10px; font-size: 11.5px; color: var(--par-muted); }
</style>

<div class="par-shell" x-data="pemberianAwalForm()" x-init="init()">

    <div class="par-headrow">
        <div>
            <p class="par-eyebrow">Unit Donor Darah &middot; Referal</p>
            <h1 class="par-title">{{ $isEdit ? 'Ubah Pemberian Awal Referal' : 'Tambah Pemberian Awal Referal' }}</h1>
        </div>
        <a href="{{ route('referal.pemberian_awal_referal.index') }}" class="par-back-link">&larr; Kembali ke daftar</a>
    </div>

    @if ($errors->any())
        <div class="par-alert par-alert-error">
            <strong>Periksa kembali isian form:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form x-ref="formEl"
          action="{{ $isEdit ? route('referal.pemberian_awal_referal.update', $pemberian->id) : route('referal.pemberian_awal_referal.store') }}"
          method="POST">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="par-grid-2">

            {{-- ============ KOLOM KIRI ============ --}}
            <div class="par-stack">

                {{-- Card: Header FPUP & Pasien --}}
                <div class="par-card">
                    <div class="par-card-pad">
                        <p class="par-card-title">Data Permintaan FPUP</p>

                        <div class="par-form-grid">
                            <div class="par-field par-field-full">
                                <label>NOFPUP</label>
                                <div class="par-search-row">
                                    <input type="text" name="no_fpup" x-model="header.no_fpup"
                                           @keydown.f4.prevent="cariFpup()" @keydown.enter.prevent="cariFpup()"
                                           placeholder="Ketik No FPUP, lalu Enter atau F4">
                                    <button type="button" @click="cariFpup()" class="par-btn par-btn-dark">
                                        <span x-show="!loadingFpup">Cari [F4]</span>
                                        <span x-show="loadingFpup">Mencari&hellip;</span>
                                    </button>
                                </div>
                                <p class="par-field-hint" x-show="errorFpup" x-text="errorFpup"></p>
                            </div>

                            <div class="par-field">
                                <label>Tgl FPUP</label>
                                <input type="text" :value="header.tgl_fpup" disabled>
                            </div>
                            <div class="par-field">
                                <label>NOFPUP Dari CM</label>
                                <input type="text" name="nofpup_dari_cm" x-model="header.nofpup_dari_cm" disabled>
                            </div>

                            <div class="par-field">
                                <label>Cara Bayar</label>
                                <div class="par-radio-group">
                                    <label><input type="radio" name="cara_bayar" value="langsung_tunai" x-model="header.cara_bayar"> Langsung/Tunai</label>
                                    <label><input type="radio" name="cara_bayar" value="kredit" x-model="header.cara_bayar"> Kredit</label>
                                </div>
                            </div>
                            <div class="par-field">
                                <label>Identifikasi Antibodi</label>
                                <div class="par-radio-group">
                                    <label><input type="radio" name="identifikasi_antibodi" value="1" x-model="header.identifikasi_antibodi"> Ya</label>
                                    <label><input type="radio" name="identifikasi_antibodi" value="0" x-model="header.identifikasi_antibodi"> Tidak</label>
                                </div>
                            </div>
                        </div>

                        <hr class="par-divider">

                        <div class="par-form-grid">
                            <div class="par-field par-field-full">
                                <label>Nama Pasien</label>
                                <input type="text" name="nama_pasien" x-model="header.nama_pasien" required>
                            </div>
                            <div class="par-field">
                                <label>No KTP Pasien</label>
                                <input type="text" name="noktp_pasien" x-model="header.noktp_pasien">
                            </div>
                            <div class="par-field">
                                <label>Jenis Kelamin</label>
                                <div class="par-radio-group">
                                    <label><input type="radio" name="jenis_kelamin" value="pria" x-model="header.jenis_kelamin"> Pria</label>
                                    <label><input type="radio" name="jenis_kelamin" value="wanita" x-model="header.jenis_kelamin"> Wanita</label>
                                </div>
                            </div>
                            <div class="par-field par-field-full">
                                <label>Alamat Pasien</label>
                                <input type="text" name="alamat_pasien" x-model="header.alamat_pasien">
                            </div>
                            <div class="par-field">
                                <label>Nama RS</label>
                                <input type="text" name="kode_rs" :value="header.kode_rs" disabled placeholder="Kode RS" style="margin-bottom:8px;">
                                <input type="text" name="nama_rs" x-model="header.nama_rs" disabled placeholder="Nama RS">
                            </div>
                            <div class="par-field">
                                <label>No Reg</label>
                                <input type="text" name="no_reg" x-model="header.no_reg">
                            </div>
                            <div class="par-field">
                                <label>Gol - Rh</label>
                                <div class="par-search-row">
                                    <input type="text" name="gol_darah" x-model="header.gol_darah" required maxlength="3"
                                           style="flex: 0 0 64px; width:64px; text-align:center; font-weight:700; font-size:15px; text-transform:uppercase;">
                                    <select name="rhesus" x-model="header.rhesus" @change="cariStock()" style="flex: 1;">
                                        <option value="Positif">Positif</option>
                                        <option value="Negatif">Negatif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="par-field">
                                <label>&nbsp;</label>
                                <div class="par-inline-fields">
                                    <label class="par-checkbox-inline" style="display:inline-flex;">
                                        <input type="hidden" name="pasien_karier" value="0">
                                        <input type="checkbox" name="pasien_karier" value="1" x-model="header.pasien_karier">
                                        Pasien Karier
                                    </label>
                                    <div class="par-w-90">
                                        <label style="font-size:11px;">Seleksi</label>
                                        <select name="seleksi" x-model.number="header.seleksi">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="fpup_id" :value="header.fpup_id">
                        <input type="hidden" name="pasien_id" :value="header.pasien_id">
                    </div>
                </div>

                {{-- Card: Detail Pemeriksaan Awal (pilih stok kantong darah) --}}
                <div class="par-card">
                    <div class="par-card-pad">
                        <div class="par-section-head">
                            <p class="par-card-title" style="margin:0;">Detail Pemeriksaan Awal</p>
                            <button type="button" @click="cariStock()" class="par-mini-btn-ghost">
                                <span x-show="!loadingStock">Muat ulang stok</span>
                                <span x-show="loadingStock">Memuat&hellip;</span>
                            </button>
                        </div>
                        <p class="par-field-hint" x-show="errorStock" x-text="errorStock" style="margin-bottom:10px;"></p>

                        <div class="par-mini-table-wrap">
                            <table class="par-mini-table">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Nostock</th>
                                        <th>Jns Darah</th>
                                        <th>Gol</th>
                                        <th>Rhesus</th>
                                        <th>Tgl Aftap</th>
                                        <th>Tgl Produksi</th>
                                        <th>Kadaluarsa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="stock in stocks" :key="stock.nostock">
                                        <tr :class="stock.dipilih && 'is-selected'">
                                            <td><input type="checkbox" x-model="stock.dipilih"></td>
                                            <td style="font-weight:600;" x-text="stock.nostock"></td>
                                            <td x-text="stock.jns_darah"></td>
                                            <td x-text="stock.gol"></td>
                                            <td x-text="stock.rhesus"></td>
                                            <td x-text="stock.tgl_aftap"></td>
                                            <td x-text="stock.tgl_produksi"></td>
                                            <td x-text="stock.tgl_kadaluarsa"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="stocks.length === 0">
                                        <td colspan="8" class="par-mini-empty">
                                            Belum ada stok &mdash; cari FPUP terlebih dahulu, atau pastikan Gol/Rhesus sudah terisi.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="par-summary-line" style="justify-content:flex-start;">
                            Jumlah kantong per seleksi:&nbsp;<span class="par-summary-value par-summary-accent" x-text="jumlahKantongTerpilih"></span>
                        </p>

                        {{-- hidden inputs hanya untuk kantong yang dicentang --}}
                        <template x-for="(stock, index) in stocks.filter(s => s.dipilih)" :key="'h-'+stock.nostock">
                            <span>
                                <input type="hidden" :name="`stocks[${index}][nostock]`" :value="stock.nostock">
                                <input type="hidden" :name="`stocks[${index}][jns_darah]`" :value="stock.jns_darah">
                                <input type="hidden" :name="`stocks[${index}][gol]`" :value="stock.gol">
                                <input type="hidden" :name="`stocks[${index}][rhesus]`" :value="stock.rhesus">
                                <input type="hidden" :name="`stocks[${index}][tgl_aftap]`" :value="stock.tgl_aftap">
                                <input type="hidden" :name="`stocks[${index}][tgl_produksi]`" :value="stock.tgl_produksi">
                                <input type="hidden" :name="`stocks[${index}][tgl_kadaluarsa]`" :value="stock.tgl_kadaluarsa">
                                <input type="hidden" :name="`stocks[${index}][urutan_seleksi]`" :value="header.seleksi">
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ============ KOLOM KANAN ============ --}}
            <div class="par-stack">

                {{-- Card: Data Permintaan (read-only dari FPUP) --}}
                <div class="par-card">
                    <div class="par-card-pad">
                        <p class="par-card-title">Data Permintaan</p>
                        <div class="par-mini-table-wrap">
                            <table class="par-mini-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Darah</th>
                                        <th>Gol</th>
                                        <th>Rhesus</th>
                                        <th>Jumlah</th>
                                        <th>Tgl Perlu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in permintaan" :key="item.no">
                                        <tr>
                                            <td x-text="item.no"></td>
                                            <td style="font-weight:600;" x-text="item.jenis_darah"></td>
                                            <td x-text="item.gol"></td>
                                            <td x-text="item.rhesus"></td>
                                            <td x-text="item.jumlah"></td>
                                            <td x-text="item.tgl_perlu"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="permintaan.length === 0">
                                        <td colspan="6" class="par-mini-empty">
                                            Belum ada data &mdash; cari nomor FPUP di sebelah kiri.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Card: Rincian Biaya Lain --}}
                <div class="par-card">
                    <div class="par-card-pad">
                        <div class="par-section-head">
                            <p class="par-card-title" style="margin:0;">Rincian Biaya Lain</p>
                            <button type="button" @click="tambahBiaya()" class="par-mini-btn">+ Tambah Biaya</button>
                        </div>

                        <div class="par-mini-table-wrap-open">
                            <table class="par-mini-table">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Layanan</th>
                                        <th>QTY</th>
                                        <th>Harga</th>
                                        <th>Satuan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(biaya, index) in biayaLain" :key="index">
                                        <tr>
                                            <td class="par-suggest-wrap" @click.away="biaya.dropdownOpen = false">
                                                <input type="text" x-model="biaya.kode" @input.debounce.300ms="searchBarang(index, 'kode')" @focus="biaya.dropdownOpen = (biaya.suggestions || []).length > 0"
                                                       :name="`biaya_lain[${index}][kode]`" class="par-row-input par-w-70" autocomplete="off" placeholder="Kode">
                                                <div class="par-suggest-box" x-show="biaya.dropdownOpen">
                                                    <template x-for="item in (biaya.suggestions || [])" :key="item.id">
                                                        <div class="par-suggest-item" @click="pilihBarang(index, item)">
                                                            <span x-text="item.kode"></span>
                                                            <small x-text="item.nama"></small>
                                                        </div>
                                                    </template>
                                                    <div class="par-suggest-empty" x-show="(biaya.suggestions || []).length === 0">Tidak ditemukan.</div>
                                                </div>
                                            </td>
                                            <td class="par-suggest-wrap" @click.away="biaya.dropdownOpen = false">
                                                <input type="text" x-model="biaya.nama_layanan" @input.debounce.300ms="searchBarang(index, 'nama')" @focus="biaya.dropdownOpen = (biaya.suggestions || []).length > 0"
                                                       :name="`biaya_lain[${index}][nama_layanan]`" class="par-row-input" style="min-width:140px;" autocomplete="off" placeholder="Cari nama barang...">
                                                <div class="par-suggest-box" x-show="biaya.dropdownOpen">
                                                    <template x-for="item in (biaya.suggestions || [])" :key="item.id">
                                                        <div class="par-suggest-item" @click="pilihBarang(index, item)">
                                                            <span x-text="item.nama"></span>
                                                            <small x-text="item.kode"></small>
                                                        </div>
                                                    </template>
                                                    <div class="par-suggest-empty" x-show="(biaya.suggestions || []).length === 0">Tidak ditemukan.</div>
                                                </div>
                                            </td>
                                            <td><input type="number" min="1" x-model.number="biaya.qty" :name="`biaya_lain[${index}][qty]`" class="par-row-input par-w-60"></td>
                                            <td><input type="number" min="0" x-model.number="biaya.harga" :name="`biaya_lain[${index}][harga]`" class="par-row-input par-w-90b"></td>
                                            <td><input type="text" x-model="biaya.satuan" :name="`biaya_lain[${index}][satuan]`" class="par-row-input par-w-70" readonly></td>
                                            <td><button type="button" @click="hapusBiaya(index)" class="par-remove-btn">&times;</button></td>
                                        </tr>
                                    </template>
                                    <tr x-show="biayaLain.length === 0">
                                        <td colspan="6" class="par-mini-empty">Belum ada biaya tambahan.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="par-summary-line">
                            <span style="color:var(--par-muted);">Total Biaya:</span>
                            <span class="par-summary-value" x-text="'Rp ' + totalBiaya.toLocaleString('id-ID')"></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer aksi --}}
        <div class="par-action-bar">
            <a href="{{ route('referal.pemberian_awal_referal.index') }}" class="par-btn par-btn-ghost">Batal</a>
            <button type="submit" class="par-btn par-btn-primary">
                {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pemberian' }}
            </button>
        </div>
    </form>
</div>

<script>
function pemberianAwalForm() {
    return {
        header: {
            fpup_id: {{ $pemberian->fpup_id ?? 'null' }},
            no_fpup: @json($pemberian->no_fpup ?? ''),
            tgl_fpup: @json(optional($pemberian->tgl_fpup ?? null)->format('d-m-Y H:i')),
            nofpup_dari_cm: @json($pemberian->nofpup_dari_cm ?? ''),
            cara_bayar: @json($pemberian->cara_bayar ?? 'langsung_tunai'),
            identifikasi_antibodi: @json(($pemberian->identifikasi_antibodi ?? false) ? '1' : '0'),
            pasien_id: {{ $pemberian->pasien_id ?? 'null' }},
            nama_pasien: @json($pemberian->nama_pasien ?? ''),
            noktp_pasien: @json($pemberian->noktp_pasien ?? ''),
            jenis_kelamin: @json($pemberian->jenis_kelamin ?? 'wanita'),
            alamat_pasien: @json($pemberian->alamat_pasien ?? ''),
            kode_rs: @json($pemberian->kode_rs ?? ''),
            nama_rs: @json($pemberian->nama_rs ?? ''),
            no_reg: @json($pemberian->no_reg ?? ''),
            gol_darah: @json($pemberian->gol_darah ?? ''),
            rhesus: @json($pemberian->rhesus ?? 'Positif'),
            pasien_karier: @json($pemberian->pasien_karier ?? false),
            seleksi: {{ $pemberian->seleksi ?? 1 }},
        },
        permintaan: [],
        stocks: @json($pemberian->stocks ?? []),
        biayaLain: @json($pemberian->biaya_lain ?? []),
        loadingFpup: false,
        errorFpup: '',
        loadingStock: false,
        errorStock: '',

        init() {},

        get jumlahKantongTerpilih() {
            return this.stocks.filter(s => s.dipilih).length;
        },
        get totalBiaya() {
            return this.biayaLain.reduce((sum, b) => sum + (Number(b.qty) || 0) * (Number(b.harga) || 0), 0);
        },

        async cariFpup() {
            if (!this.header.no_fpup) return;
            this.loadingFpup = true;
            this.errorFpup = '';
            try {
                const url = `{{ route('referal.pemberian_awal_referal.cari_fpup') }}?no_fpup=${encodeURIComponent(this.header.no_fpup)}`;
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.errorFpup = err.message || 'Nomor FPUP tidak ditemukan.';
                    return;
                }
                const data = await res.json();
                Object.assign(this.header, {
                    fpup_id: data.fpup_id,
                    tgl_fpup: data.tgl_fpup,
                    nofpup_dari_cm: data.nofpup_dari_cm,
                    pasien_id: data.pasien_id,
                    nama_pasien: data.nama_pasien,
                    noktp_pasien: data.noktp_pasien,
                    jenis_kelamin: data.jenis_kelamin,
                    alamat_pasien: data.alamat_pasien,
                    kode_rs: data.kode_rs,
                    nama_rs: data.nama_rs,
                    no_reg: data.no_reg,
                    gol_darah: data.gol_darah,
                    rhesus: data.rhesus,
                });
                this.permintaan = data.permintaan || [];
                await this.cariStock();
            } finally {
                this.loadingFpup = false;
            }
        },

        async cariStock() {
            if (!this.header.gol_darah || !this.header.rhesus) return;
            this.loadingStock = true;
            this.errorStock = '';
            try {
                const params = new URLSearchParams({ gol: this.header.gol_darah, rhesus: this.header.rhesus });
                const res = await fetch(`{{ route('referal.pemberian_awal_referal.search_stock') }}?${params}`, { headers: { Accept: 'application/json' } });
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    this.errorStock = err.message || `Gagal memuat stok (HTTP ${res.status}).`;
                    this.stocks = [];
                    return;
                }
                const list = await res.json();
                this.stocks = list.map(s => ({ ...s, dipilih: false }));
            } catch (e) {
                this.errorStock = 'Tidak bisa terhubung ke server saat memuat stok.';
                this.stocks = [];
            } finally {
                this.loadingStock = false;
            }
        },

        tambahBiaya() {
            this.biayaLain.push({ kode: '', nama_layanan: '', qty: 1, harga: 0, satuan: '', suggestions: [], dropdownOpen: false });
        },
        hapusBiaya(index) {
            this.biayaLain.splice(index, 1);
        },

        async searchBarang(index, field) {
            const biaya = this.biayaLain[index];
            const query = field === 'kode' ? biaya.kode : biaya.nama_layanan;

            if (!query || query.length < 2) {
                biaya.suggestions = [];
                biaya.dropdownOpen = false;
                return;
            }

            try {
                const url = `{{ route('referal.pemberian_awal_referal.search_barang') }}?q=${encodeURIComponent(query)}`;
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!res.ok) {
                    biaya.suggestions = [];
                    biaya.dropdownOpen = false;
                    return;
                }
                biaya.suggestions = await res.json();
                biaya.dropdownOpen = true;
            } catch (e) {
                biaya.suggestions = [];
                biaya.dropdownOpen = false;
            }
        },

        pilihBarang(index, item) {
            const biaya = this.biayaLain[index];
            biaya.kode = item.kode;
            biaya.nama_layanan = item.nama;
            biaya.harga = item.harga_satuan;
            biaya.satuan = item.satuan;
            biaya.suggestions = [];
            biaya.dropdownOpen = false;
        },
    };
}
</script>
@endsection