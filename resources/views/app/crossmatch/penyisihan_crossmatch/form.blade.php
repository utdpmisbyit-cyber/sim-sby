@extends('layouts.index')

@section('title', $item ? 'Edit Penyisihan Darah Rusak' : 'Tambah Penyisihan Darah Rusak')

@section('content')
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js"></script>

<div class="pny-wrap"
     x-data="penyisihanForm({
        scanUrl: '{{ route('crossmatch.penyisihan_crossmatch.scan-stock') }}',
        csrf: '{{ csrf_token() }}',
        initialItems: {{ $item ? $item->details->map(fn ($d) => [
                'cross_test_id'  => $d->cross_test_id,
                'no_stock'       => $d->no_stock,
                'jns_darah'      => $d->jns_darah,
                'gol_rh_kantong' => $d->gol_rh_kantong,
                'gol'            => $d->gol,
                'rhesus'         => $d->rhesus,
                'tgl_aftap'      => optional($d->tgl_aftap)->format('Y-m-d'),
                'tgl_kadaluarsa' => optional($d->tgl_kadaluarsa)->format('Y-m-d'),
                'status_kantong' => $d->status_kantong,
                'alasan'         => $d->alasan,
            ])->values()->toJson() : '[]' }},
     })">

    <div class="pny-header">
        <div>
            <h1 class="pny-title">{{ $item ? 'Edit Penyisihan Darah Rusak' : 'Tambah Penyisihan Darah Rusak' }}</h1>
            <p class="pny-subtitle">Scan / input No Stock kantong darah rusak untuk disisihkan</p>
        </div>
        <a href="{{ route('crossmatch.penyisihan_crossmatch.index') }}" class="pny-btn pny-btn-ghost">&larr; Kembali</a>
    </div>

    @if ($errors->any())
        <div class="pny-alert pny-alert-danger">
            <strong>Periksa kembali data Anda:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $item ? route('crossmatch.penyisihan_crossmatch.update', $item->id) : route('crossmatch.penyisihan_crossmatch.store') }}"
          @submit="return items.length > 0 || (alert('Tambahkan minimal 1 kantong darah ke daftar.'), false)">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="pny-card pny-pad">
            <div class="pny-grid-3">
                <div class="pny-field">
                    <label>Nomor Penyisihan</label>
                    <input type="text" value="{{ $noPenyisihan }}" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Tanggal Penyisihan</label>
                    <input type="date" name="tanggal_penyisihan" value="{{ $item ? $item->tanggal_penyisihan->format('Y-m-d') : date('Y-m-d') }}" required>
                </div>
                <div class="pny-field">
                    <label>Petugas</label>
                    <input type="text" name="petugas" value="{{ $item->petugas ?? '' }}" placeholder="Nama petugas">
                </div>
            </div>
        </div>

        <div class="pny-card pny-pad" style="margin-top:16px;">
            <h3 class="pny-section-title">Scan Kantong Darah</h3>

            <div class="pny-grid-3">
                <div class="pny-field" style="grid-column: span 1;">
                    <label>No Stock</label>
                    <input type="text"
                           x-model="noStock"
                           @keydown.enter.prevent="scan()"
                           placeholder="Scan / ketik No Stock lalu Enter"
                           class="pny-highlight"
                           autofocus>
                    <p class="pny-hint" x-show="loading">Mencari data...</p>
                    <p class="pny-hint pny-hint-error" x-show="error" x-text="error"></p>
                </div>
                <div class="pny-field">
                    <label>Jenis Darah</label>
                    <input type="text" :value="current ? current.jns_darah : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Gol / Rh Kantong</label>
                    <input type="text" :value="current ? current.gol_rh_kantong : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Golongan</label>
                    <input type="text" :value="current ? current.gol : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Rhesus</label>
                    <input type="text" :value="current ? current.rhesus : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Status</label>
                    <input type="text" :value="current ? current.status_kantong : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Tgl Aftap</label>
                    <input type="text" :value="current ? formatDate(current.tgl_aftap) : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Tgl Kadaluarsa</label>
                    <input type="text" :value="current ? formatDate(current.tgl_kadaluarsa) : ''" readonly class="pny-readonly">
                </div>
                <div class="pny-field">
                    <label>Alasan</label>
                    <select x-model="alasan">
                        <option value="">- Pilih Alasan -</option>
                        @foreach ($alasanOptions as $alasanOption)
                            <option value="{{ $alasanOption }}">{{ $alasanOption }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top:14px;">
                <button type="button" class="pny-btn pny-btn-primary" @click="addItem()" :disabled="!current || !alasan">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tambah ke Daftar
                </button>
            </div>
        </div>

        <div class="pny-card" style="margin-top:16px;">
            <table class="pny-table">
                <thead>
                    <tr>
                        <th style="width:36px;">No.</th>
                        <th>No Stock</th>
                        <th>Jns Darah</th>
                        <th>Gol</th>
                        <th>Rhesus</th>
                        <th>Tgl Aftap</th>
                        <th>Tgl Expired</th>
                        <th>Alasan</th>
                        <th style="width:60px;" class="pny-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="items.length === 0">
                        <tr>
                            <td colspan="9">
                                <div class="pny-empty">
                                    <p>Belum ada kantong darah yang ditambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-for="(itm, index) in items" :key="index">
                        <tr>
                            <td x-text="index + 1"></td>
                            <td><span class="pny-mono" x-text="itm.no_stock"></span></td>
                            <td x-text="itm.jns_darah"></td>
                            <td x-text="itm.gol"></td>
                            <td x-text="itm.rhesus"></td>
                            <td x-text="formatDate(itm.tgl_aftap)"></td>
                            <td x-text="formatDate(itm.tgl_kadaluarsa)"></td>
                            <td x-text="itm.alasan"></td>
                            <td class="pny-center">
                                <button type="button" class="pny-icon-btn pny-icon-btn-danger" @click="removeItem(index)" title="Hapus dari daftar">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </td>

                            <!-- Hidden inputs supaya array items terkirim sebagai form data biasa -->
                            <input type="hidden" :name="`items[${index}][no_stock]`" :value="itm.no_stock">
                            <input type="hidden" :name="`items[${index}][cross_test_id]`" :value="itm.cross_test_id">
                            <input type="hidden" :name="`items[${index}][alasan]`" :value="itm.alasan">
                            <input type="hidden" :name="`items[${index}][jns_darah]`" :value="itm.jns_darah">
                            <input type="hidden" :name="`items[${index}][gol_rh_kantong]`" :value="itm.gol_rh_kantong">
                            <input type="hidden" :name="`items[${index}][gol]`" :value="itm.gol">
                            <input type="hidden" :name="`items[${index}][rhesus]`" :value="itm.rhesus">
                            <input type="hidden" :name="`items[${index}][tgl_aftap]`" :value="itm.tgl_aftap">
                            <input type="hidden" :name="`items[${index}][tgl_kadaluarsa]`" :value="itm.tgl_kadaluarsa">
                            <input type="hidden" :name="`items[${index}][status_kantong]`" :value="itm.status_kantong">
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8" style="text-align:right; font-weight:600;">Jumlah Kantong</td>
                        <td class="pny-center" style="font-weight:700;" x-text="items.length"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="pny-card pny-pad" style="margin-top:16px;">
            <div class="pny-field">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)">{{ $item->keterangan ?? '' }}</textarea>
            </div>
        </div>

        <div class="pny-form-actions">
            <a href="{{ route('crossmatch.penyisihan_crossmatch.index') }}" class="pny-btn pny-btn-outline">Batal</a>
            <button type="submit" class="pny-btn pny-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan
            </button>
        </div>
    </form>
</div>

<style>
    .pny-wrap { max-width: 1100px; margin: 0 auto; padding: 24px 16px 64px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #1f2937; }
    .pny-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom: 20px; flex-wrap: wrap; }
    .pny-title { font-size: 22px; font-weight: 700; margin: 0 0 2px; color:#111827; }
    .pny-subtitle { font-size: 13.5px; color:#6b7280; margin:0; }

    .pny-btn { display:inline-flex; align-items:center; gap:6px; padding: 9px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration:none; border:1px solid transparent; cursor:pointer; transition: background .15s ease, transform .05s ease; }
    .pny-btn:active { transform: scale(0.98); }
    .pny-btn:disabled { opacity:.5; cursor:not-allowed; }
    .pny-btn-primary { background:#7c2d92; color:#fff; box-shadow: 0 1px 2px rgba(124,45,146,.3); }
    .pny-btn-primary:hover:not(:disabled) { background:#6a2680; }
    .pny-btn-outline { background:#fff; color:#374151; border-color:#d1d5db; }
    .pny-btn-outline:hover { background:#f9fafb; }
    .pny-btn-ghost { background:transparent; color:#6b7280; text-decoration:none; }
    .pny-btn-ghost:hover { background:#f3f4f6; }

    .pny-alert { padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 16px; }
    .pny-alert-danger { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; }
    .pny-alert-danger ul { margin: 6px 0 0; padding-left: 18px; }

    .pny-card { background:#fff; border:1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 2px rgba(0,0,0,.03); }
    .pny-pad { padding: 18px; }
    .pny-section-title { font-size: 14px; font-weight:700; margin: 0 0 14px; color:#111827; }

    .pny-grid-3 { display:grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    @media (max-width: 720px) { .pny-grid-3 { grid-template-columns: 1fr; } }

    .pny-field { display:flex; flex-direction:column; gap:5px; }
    .pny-field label { font-size: 11.5px; font-weight:600; color:#6b7280; text-transform: uppercase; letter-spacing:.03em; }
    .pny-field input, .pny-field select, .pny-field textarea { border:1px solid #d1d5db; border-radius:7px; padding: 9px 10px; font-size: 13.5px; outline:none; font-family: inherit; background:#fff; }
    .pny-field input:focus, .pny-field select:focus, .pny-field textarea:focus { border-color:#7c2d92; box-shadow: 0 0 0 3px rgba(124,45,146,.12); }
    .pny-readonly { background:#f9fafb !important; color:#6b7280; }
    .pny-highlight { background:#fffbeb; border-color:#fcd34d !important; font-weight:600; }

    .pny-hint { font-size: 12px; color:#6b7280; margin:2px 0 0; }
    .pny-hint-error { color:#dc2626; font-weight:600; }

    .pny-table { width:100%; border-collapse: collapse; font-size: 13.5px; }
    .pny-table thead th { text-align:left; background:#f9fafb; color:#6b7280; font-size: 11.5px; text-transform: uppercase; letter-spacing:.03em; font-weight:700; padding: 11px 14px; border-bottom: 1px solid #e5e7eb; }
    .pny-table td { padding: 11px 14px; border-bottom: 1px solid #f1f2f4; vertical-align: middle; }
    .pny-table tfoot td { border-top: 1px solid #e5e7eb; border-bottom:none; padding: 11px 14px; background:#fafafa; }
    .pny-center { text-align:center; }
    .pny-mono { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight:600; color:#111827; }

    .pny-icon-btn { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border-radius:7px; border:1px solid #e5e7eb; background:#fff; color:#4b5563; cursor:pointer; }
    .pny-icon-btn-danger:hover { background:#fef2f2; color:#dc2626; border-color:#fecaca; }

    .pny-empty { display:flex; flex-direction:column; align-items:center; gap:6px; padding: 32px 0; color:#9ca3af; }
    .pny-empty p { margin:0; font-size: 13.5px; }

    .pny-form-actions { display:flex; justify-content:flex-end; gap:10px; margin-top: 20px; }
</style>

<script>
    function penyisihanForm({ scanUrl, csrf, initialItems }) {
        return {
            noStock: '',
            current: null,
            alasan: '',
            loading: false,
            error: '',
            items: initialItems || [],

            async scan() {
                if (!this.noStock) return;
                this.loading = true;
                this.error = '';
                this.current = null;

                if (this.items.some(i => i.no_stock === this.noStock)) {
                    this.error = 'No Stock ini sudah ada di daftar.';
                    this.loading = false;
                    return;
                }

                try {
                    const res = await fetch(scanUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ no_stock: this.noStock }),
                    });
                    const json = await res.json();

                    if (!res.ok || !json.success) {
                        this.error = json.message || 'No Stock tidak ditemukan.';
                        return;
                    }

                    this.current = json.data;
                } catch (e) {
                    this.error = 'Gagal menghubungi server, coba lagi.';
                } finally {
                    this.loading = false;
                }
            },

            addItem() {
                if (!this.current || !this.alasan) return;

                this.items.push({
                    ...this.current,
                    alasan: this.alasan,
                });

                // reset area scan untuk kantong berikutnya
                this.noStock = '';
                this.current = null;
                this.alasan = '';
                this.error = '';
                this.$nextTick(() => {
                    const input = this.$root.querySelector('input[x-model="noStock"]');
                    if (input) input.focus();
                });
            },

            removeItem(index) {
                this.items.splice(index, 1);
            },

            formatDate(value) {
                if (!value) return '-';
                const d = new Date(value);
                if (isNaN(d)) return value;
                return d.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
            },
        };
    }
</script>
@endsection