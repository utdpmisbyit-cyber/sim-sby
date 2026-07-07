@extends('layouts.index')

@php
    $isEdit      = isset($pengembalian);
    $formTitle   = $isEdit ? 'Edit Pengembalian Kantong' : 'Tambah Pengembalian Kantong';
    $formAction  = $isEdit
        ? route('aftap.pengembalian_kantong.update', $pengembalian->id)
        : route('aftap.pengembalian_kantong.store');
    $noKembaliVal = $isEdit ? $pengembalian->no_kembali : $no_kembali;
@endphp

@section('title', $formTitle)

@push('styles')
<style>
    /* ─── SEARCH DROPDOWN (samakan dengan modul Pendaftaran Donor) ────────── */
    .search-wrap { position: relative; }
    .search-wrap .form-control-sm {
        border-radius: 8px;
        border: 1.5px solid #e0e0e0;
        font-size: .82rem;
        height: 33px;
        transition: border-color .15s, box-shadow .15s;
        padding-right: 28px;
    }
    .search-wrap .form-control-sm:focus {
        border-color: #3e97ff;
        box-shadow: 0 0 0 3px rgba(62,151,255,.12);
        outline: none;
    }
    .search-wrap .search-clear {
        position: absolute;
        right: 7px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
        font-size: .75rem;
        display: none;
        background: none;
        border: none;
        padding: 0;
        line-height: 1;
    }
    .search-wrap .search-clear.visible { display: block; }
    .search-wrap .search-clear:hover { color: #e74c3c; }

    .search-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 10px;
        box-shadow: 0 12px 28px rgba(0,0,0,.10), 0 2px 6px rgba(0,0,0,.06);
        z-index: 9999;
        max-height: 230px;
        overflow-y: auto;
        display: none;
        padding: 4px;
    }
    .search-dropdown.open { display: block; }

    .search-dropdown .sd-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: background .12s;
        font-size: .82rem;
    }
    .search-dropdown .sd-item + .sd-item { margin-top: 1px; }
    .search-dropdown .sd-item:hover, .search-dropdown .sd-item.active { background: #fdeeee; }

    .search-dropdown .sd-code {
        flex-shrink: 0;
        min-width: 34px;
        text-align: center;
        font-size: .68rem;
        font-weight: 700;
        font-family: monospace;
        color: #e74c3c;
        background: #fdeeee;
        border-radius: 5px;
        padding: 3px 5px;
    }
    .search-dropdown .sd-item:hover .sd-code, .search-dropdown .sd-item.active .sd-code {
        background: #e74c3c;
        color: #fff;
    }

    .search-dropdown .sd-text { color: #1a1a2e; flex: 1; }
    .search-dropdown .sd-empty, .search-dropdown .sd-loading {
        padding: 12px 10px;
        font-size: .8rem;
        color: #999;
        text-align: center;
    }
    .search-dropdown .sd-loading i { color: #3e97ff; }

    /* ── Scan feedback ── */
    .scan-status { transition: all .25s ease; }
    .scan-success { border-color: #198754 !important; background: #f0fdf4 !important; }
    .scan-error   { border-color: #dc3545 !important; background: #fff5f5 !important; }

    /* ── Detail table ── */
    #detail-rows td { vertical-align: middle; }
    .total-row td   { background: #f8f9fa; font-weight: 600; }

    /* ── Card kantong detail ── */
    #card-detail-kantong { border-left: 4px solid #198754; }

    /* ── Info badges di card detail ── */
    .info-badge {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 6px;
        padding: 6px 10px;
    }

    /* ── Highlight jumlah stok ── */
    .stok-jumlah {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- ── Header ── --}}
    <div class="d-flex align-items-center gap-2 mb-3 mt-3">
        <a href="{{ route('aftap.pengembalian_kantong.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">{{ $formTitle }}</h4>
            <small class="text-muted">
                @if($isEdit) {{ $pengembalian->no_kembali }}
                @else Form pengembalian kantong darah
                @endif
            </small>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-1"></i> <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" id="form-pengembalian">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="row g-3">

            {{-- ══ Kolom Kiri ══════════════════════════════════════════════════ --}}
            <div class="col-lg-8">

                {{-- ── Card: Info Pengembalian ── --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header py-2 {{ $isEdit ? 'bg-warning text-dark' : 'bg-primary text-white' }}">
                        <i class="fas fa-{{ $isEdit ? 'edit' : 'info-circle' }} me-1"></i>
                        Informasi Pengembalian
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">No. Kembali</label>
                                <input type="text" class="form-control form-control-sm bg-light"
                                       value="{{ $noKembaliVal }}" readonly>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">
                                    Tgl Kembali <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tgl_kembali"
                                       class="form-control form-control-sm @error('tgl_kembali') is-invalid @enderror"
                                       value="{{ old('tgl_kembali', $isEdit ? $pengembalian->tgl_kembali->format('Y-m-d') : date('Y-m-d')) }}"
                                       required>
                                @error('tgl_kembali')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── Asal Darah (searchable, sama seperti modul Pendaftaran Donor) ── --}}
                            <div class="col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Asal Darah</label>
                                @php
                                    $asalDarahIdVal   = old('asal_darah_id', $isEdit ? $pengembalian->asal_darah_id : '');
                                    $asalDarahNamaVal = old('asal_darah_nama', $isEdit && $pengembalian->asalDarah ? $pengembalian->asalDarah->nama : '');
                                @endphp
                                <input type="hidden" name="asal_darah_id" id="asal_darah_id" value="{{ $asalDarahIdVal }}">
                                <div class="search-wrap" id="wrap_asal_darah">
                                    <input type="text" id="search_asal_darah"
                                           class="form-control form-control-sm @error('asal_darah_id') is-invalid @enderror"
                                           placeholder="Cari asal darah..."
                                           value="{{ $asalDarahNamaVal }}"
                                           autocomplete="off">
                                    <button type="button" class="search-clear" id="clear_asal_darah">✕</button>
                                    <div class="search-dropdown" id="dd_asal_darah"></div>
                                </div>
                                @error('asal_darah_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── Scan / readonly ── --}}
                            <div class="col-12">
                                <label class="form-label form-label-sm fw-semibold">
                                    No. Kantong <span class="text-danger">*</span>
                                </label>
                                @if($isEdit)
                                    <input type="text" class="form-control form-control-sm bg-light"
                                           value="{{ $pengembalian->no_kantong }}" readonly>
                                    <input type="hidden" name="no_kantong"      value="{{ $pengembalian->no_kantong }}">
                                    <input type="hidden" name="stok_kantong_id" value="{{ $pengembalian->stok_kantong_id }}">
                                    <small class="text-muted">No. kantong tidak dapat diubah.</small>
                                @else
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                        <input type="text" id="scan_kantong_input"
                                               class="form-control scan-status @error('no_kantong') is-invalid @enderror"
                                               placeholder="Scan barcode atau ketik no. kantong, lalu Enter"
                                               autocomplete="off">
                                        <button type="button" class="btn btn-outline-primary" id="btn-scan">
                                            <i class="fas fa-search me-1"></i> Cari
                                        </button>
                                        {{-- spinner --}}
                                        <span class="input-group-text d-none" id="scan-spinner">
                                            <span class="spinner-border spinner-border-sm text-primary"></span>
                                        </span>
                                    </div>
                                    <input type="hidden" name="no_kantong"      id="no_kantong"      value="{{ old('no_kantong') }}">
                                    <input type="hidden" name="stok_kantong_id" id="stok_kantong_id" value="{{ old('stok_kantong_id') }}">
                                    <div id="scan-feedback" class="mt-1"></div>
                                    @error('no_kantong')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Card: Detail Kantong (hasil scan) ── --}}
                <div class="card shadow-sm mb-3 border-success"
                     id="card-detail-kantong"
                     style="{{ ($isEdit || old('no_kantong')) ? '' : 'display:none' }}">
                    <div class="card-header bg-success text-white py-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-check-circle me-1"></i> Detail Kantong Ditemukan</span>
                        {{-- Jumlah stok besar di kanan header --}}
                        <span class="badge bg-white text-success fs-6 px-3 py-1" id="badge-jumlah-stok" style="display:none">
                            Stok: <span id="val-jumlah-stok">0</span>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-stretch">
                            {{-- Merk, Jenis, Tipe, Ukuran --}}
                            <div class="col-sm-6">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Merk</label>
                                        <input type="text" name="merk" id="merk"
                                               class="form-control form-control-sm"
                                               value="{{ old('merk', $isEdit ? $pengembalian->merk : '') }}"
                                               {{ $isEdit ? '' : 'readonly' }}>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Jenis</label>
                                        <input type="text" name="jenis" id="jenis"
                                               class="form-control form-control-sm"
                                               value="{{ old('jenis', $isEdit ? $pengembalian->jenis : '') }}"
                                               {{ $isEdit ? '' : 'readonly' }}>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Tipe</label>
                                        <input type="text" name="tipe" id="tipe"
                                               class="form-control form-control-sm"
                                               value="{{ old('tipe', $isEdit ? $pengembalian->tipe : '') }}"
                                               {{ $isEdit ? '' : 'readonly' }}>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label form-label-sm text-muted mb-1">Ukuran</label>
                                        <input type="text" name="ukuran" id="ukuran"
                                               class="form-control form-control-sm"
                                               value="{{ old('ukuran', $isEdit ? $pengembalian->ukuran : '') }}"
                                               {{ $isEdit ? '' : 'readonly' }}>
                                    </div>
                                </div>
                            </div>

                            {{-- Box jumlah stok --}}
                            <div class="col-sm-6 d-flex align-items-center justify-content-center" id="box-jumlah-stok" style="display:none!important">
                                <div class="text-center info-badge w-100 py-3">
                                    <div class="text-muted small mb-1">Jumlah Stok Tersedia</div>
                                    <div class="stok-jumlah" id="big-jumlah-stok">0</div>
                                    <div class="text-muted small">kantong</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Card: Detail Tipe Kantong ── --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                        <span><i class="fas fa-list me-1 text-primary"></i> Detail Tipe Kantong</span>
                        <div class="d-flex align-items-center gap-2">
                            {{-- Total keseluruhan --}}
                            <span class="badge bg-primary" id="badge-total-keseluruhan" style="display:none">
                                Total: <span id="val-total-keseluruhan">0</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-detail">
                                <i class="fas fa-plus me-1"></i>Tambah Baris
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40%">Tipe Kantong</th>
                                        <th style="width:20%">Jumlah</th>
                                        <th style="width:20%">Flag</th>
                                        <th style="width:10%" class="text-center">Sub Total</th>
                                        <th style="width:10%" class="text-center">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-rows"></tbody>
                                <tfoot id="detail-footer" style="display:none">
                                    <tr class="total-row">
                                        <td colspan="2" class="text-end pe-3 text-muted">Total Keseluruhan:</td>
                                        <td colspan="2" class="text-center text-primary fw-bold" id="tfoot-total">0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="detail-empty" class="text-center text-muted py-3">
                            <small><i class="fas fa-info-circle me-1"></i>
                                Belum ada baris detail. Klik "+ Tambah Baris".
                            </small>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ══ Kolom Kanan ═════════════════════════════════════════════════ --}}
            <div class="col-lg-4">

                {{-- ── Ringkasan Stok (muncul setelah scan) ── --}}
                <div class="card shadow-sm mb-3 border-primary" id="card-ringkasan-stok" style="display:none">
                    <div class="card-header py-2 bg-primary text-white">
                        <i class="fas fa-cubes me-1"></i> Ringkasan Stok
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="text-muted small mb-1">Stok Tersedia</div>
                        <div class="display-5 fw-bold text-primary" id="card-stok-val">0</div>
                        <div class="text-muted small">kantong</div>
                        <hr class="my-2">
                        <div class="text-muted small mb-1">Total Dikembalikan</div>
                        <div class="fs-4 fw-bold text-success" id="card-kembali-val">0</div>
                        <div class="text-muted small">kantong</div>
                        <hr class="my-2">
                        <div class="text-muted small mb-1">Sisa Stok</div>
                        <div class="fs-4 fw-bold" id="card-sisa-val">0</div>
                        <div class="text-muted small">kantong</div>
                    </div>
                </div>

                <div class="card shadow-sm mb-3">
                    <div class="card-header py-2">
                        <i class="fas fa-clipboard-check me-1"></i> Kondisi & Catatan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label form-label-sm fw-semibold">
                                Kondisi <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                @php $kondisiVal = old('kondisi', $isEdit ? $pengembalian->kondisi : 'baik'); @endphp
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi"
                                           id="kondisi_baik" value="baik"
                                           {{ $kondisiVal === 'baik' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kondisi_baik">
                                        <span class="badge bg-success">Baik</span>
                                    </label>
                                </div>
                                <!-- <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kondisi"
                                           id="kondisi_rusak" value="rusak"
                                           {{ $kondisiVal === 'rusak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="kondisi_rusak">
                                        <span class="badge bg-danger">Rusak</span>
                                    </label>
                                </div> -->
                            </div>
                            @error('kondisi')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="form-label form-label-sm fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="4"
                                      class="form-control form-control-sm @error('keterangan') is-invalid @enderror"
                                      placeholder="Catatan tambahan...">{{ old('keterangan', $isEdit ? $pengembalian->keterangan : '') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn {{ $isEdit ? 'btn-warning' : 'btn-primary' }}">
                        <i class="fas fa-save me-1"></i>
                        {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pengembalian' }}
                    </button>
                    <a href="{{ route('aftap.pengembalian_kantong.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

@push('scripts')
<script>
    // ─── SEARCH DROPDOWN (pola registerSearchDropdown, sama seperti modul Pendaftaran Donor) ───
    (function () {
        'use strict';

        function closeAll(exceptId) {
            document.querySelectorAll('.search-dropdown').forEach(function (dd) {
                if (dd.id !== exceptId) dd.classList.remove('open');
            });
        }

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        // Guard: daftarkan listener klik-luar HANYA SEKALI walau file ini
        // ikut ke-load lagi di halaman lain yang juga pakai pola yang sama.
        if (!window._searchDropdownOutsideClickBound) {
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.search-wrap')) closeAll('');
            });
            window._searchDropdownOutsideClickBound = true;
        }

        window.registerSearchDropdown = window.registerSearchDropdown || function (cfg) {
            var $input  = $('#' + cfg.inputId);
            var $hidden = $('#' + cfg.hiddenId);
            var $dd     = $('#' + cfg.dropdownId);
            var $clear  = $('#' + cfg.clearId);
            var _timer  = null;
            var _xhr    = null;
            var _active = -1;

            function toggleClear() {
                if ($input.val().trim()) $clear.addClass('visible');
                else                     $clear.removeClass('visible');
            }

            function renderItems(items) {
                $dd.empty();
                _active = -1;

                if (!items || !items.length) {
                    $dd.html('<div class="sd-empty">Tidak ada hasil</div>').addClass('open');
                    return;
                }

                items.forEach(function (item, idx) {
                    var code = item.code ?? String(item.id).padStart(4, '0');
                    var $item = $('<div class="sd-item" tabindex="-1">')
                        .attr('data-idx', idx)
                        .html(
                            '<span class="sd-code">' + escapeHtml(code) + '</span>' +
                            '<span class="sd-text">'  + escapeHtml(item.text) + '</span>'
                        )
                        .on('mousedown', function (e) {
                            e.preventDefault();
                            selectItem(item);
                        });
                    $dd.append($item);
                });

                $dd.addClass('open');
            }

            function selectItem(item) {
                $input.val(item.text);
                $hidden.val(item.id);
                toggleClear();
                $dd.removeClass('open').empty();
                $input.closest('.search-wrap').removeClass('is-invalid');
                if (typeof cfg.onSelect === 'function') cfg.onSelect(item);
            }

            function fetchItems(q) {
                if (_xhr) _xhr.abort();
                $dd.html('<div class="sd-loading"><i class="fas fa-spinner fa-spin me-1"></i>Mencari...</div>').addClass('open');

                var params = { q: q };
                if (typeof cfg.extraParams === 'function') {
                    $.extend(params, cfg.extraParams());
                }

                _xhr = $.ajax({
                    url     : cfg.ajaxUrl,
                    data    : params,
                    success : function (res) {
                        renderItems(res.results || []);
                    },
                    error   : function (xhr) {
                        if (xhr.statusText !== 'abort') {
                            $dd.html('<div class="sd-empty">Gagal memuat data</div>').addClass('open');
                        }
                    }
                });
            }

            function openDropdown() {
                closeAll($dd.attr('id'));
                var q = $input.val().trim();
                fetchItems(q);
            }

            $input.on('focus click', function () {
                openDropdown();
            });

            $input.on('keydown', function (e) {
                var $items = $dd.find('.sd-item');
                var total  = $items.length;
                if (!total) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    _active = Math.min(_active + 1, total - 1);
                    $items.removeClass('active').eq(_active).addClass('active');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    _active = Math.max(_active - 1, 0);
                    $items.removeClass('active').eq(_active).addClass('active');
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (_active >= 0) $items.eq(_active).trigger('mousedown');
                } else if (e.key === 'Escape') {
                    $dd.removeClass('open');
                }
            });

            $input.on('input', function () {
                var q = $(this).val().trim();
                toggleClear();

                if (!q) {
                    $hidden.val('');
                    clearTimeout(_timer);
                    _timer = setTimeout(function () { fetchItems(''); }, 100);
                    if (typeof cfg.onSelect === 'function') cfg.onSelect(null);
                    return;
                }

                clearTimeout(_timer);
                _timer = setTimeout(function () { fetchItems(q); }, 280);
            });

            $clear.on('click', function () {
                $input.val('');
                $hidden.val('');
                toggleClear();
                if (typeof cfg.onSelect === 'function') cfg.onSelect(null);
                $input.trigger('focus').focus();
                openDropdown();
            });

            toggleClear();
        };

    })();

    // ── Daftarkan field Asal Darah ──────────────────────────────────────────
    registerSearchDropdown({
        inputId    : 'search_asal_darah',
        hiddenId   : 'asal_darah_id',
        dropdownId : 'dd_asal_darah',
        clearId    : 'clear_asal_darah',
        ajaxUrl    : '{{ url('aftap/pengembalian_kantong/select2/asal-darah') }}',
    });
</script>
<script>
@php
    $tipeKantongJson = $tipe_kantong->map(fn($t) => ['id' => $t->id, 'nama' => $t->nama])->values();

    $existingDetailsJson = $isEdit
        ? $pengembalian->details->map(fn($d) => [
            'tipe_kantong_id' => $d->tipe_kantong_id,
            'jumlah'          => $d->jumlah,
            'flag'            => $d->flag,
        ])->values()
        : collect([]);
@endphp

(function () {
    'use strict';

    const IS_EDIT         = @json($isEdit);
    const TIPE_OPTS       = @json($tipeKantongJson);
    const EXISTING        = @json($existingDetailsJson);
    const SCAN_URL        = '{{ route('aftap.pengembalian_kantong.scan_kantong') }}';
    const CSRF            = '{{ csrf_token() }}';

    let stokJumlah = 0;   // jumlah stok dari hasil scan
    let rowIdx     = 0;

    // ── Build <option> ────────────────────────────────────────────────────────
    function buildOptions(selected = '') {
        return `<option value="">-- Pilih Tipe --</option>` +
            TIPE_OPTS.map(t =>
                `<option value="${t.id}" ${String(t.id) === String(selected) ? 'selected' : ''}>${t.nama}</option>`
            ).join('');
    }

    // ── Hitung ulang total & update ringkasan ─────────────────────────────────
    function recalc() {
        const tbody   = document.getElementById('detail-rows');
        const rows    = tbody.querySelectorAll('tr');
        let   total   = 0;

        rows.forEach(row => {
            const jumlahInput = row.querySelector('input[name$="[jumlah]"]');
            const subtotalEl  = row.querySelector('.subtotal-val');
            const jml = parseInt(jumlahInput?.value || 0, 10);
            total += isNaN(jml) ? 0 : jml;
            if (subtotalEl) subtotalEl.textContent = isNaN(jml) ? 0 : jml.toLocaleString('id-ID');
        });

        // footer tabel
        const footer = document.getElementById('detail-footer');
        const tfoot  = document.getElementById('tfoot-total');
        if (rows.length > 1) {
            footer.style.display = '';
            tfoot.textContent = total.toLocaleString('id-ID');
        } else {
            footer.style.display = 'none';
        }

        // badge header
        const badge = document.getElementById('badge-total-keseluruhan');
        const badgeVal = document.getElementById('val-total-keseluruhan');
        badge.style.display  = rows.length ? '' : 'none';
        badgeVal.textContent = total.toLocaleString('id-ID');

        // kartu ringkasan kanan
        updateRingkasan(total);
    }

    function updateRingkasan(totalKembali) {
        const cardSisa    = document.getElementById('card-sisa-val');
        const cardKembali = document.getElementById('card-kembali-val');
        const sisa        = stokJumlah - totalKembali;

        cardKembali.textContent = totalKembali.toLocaleString('id-ID');
        cardSisa.textContent    = sisa.toLocaleString('id-ID');
        cardSisa.className      = 'fs-4 fw-bold ' + (sisa < 0 ? 'text-danger' : sisa === 0 ? 'text-success' : 'text-dark');
    }

    // ── Tambah baris ─────────────────────────────────────────────────────────
    function addRow(data = {}) {
        const i   = rowIdx++;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="details[${i}][tipe_kantong_id]" class="form-select form-select-sm">
                    ${buildOptions(data.tipe_kantong_id)}
                </select>
            </td>
            <td>
                <input type="number" min="1"
                       class="form-control form-control-sm jumlah-input"
                       name="details[${i}][jumlah]"
                       value="${data.jumlah || ''}"
                       placeholder="0">
            </td>
            <td>
                <input type="number" min="0"
                       class="form-control form-control-sm"
                       name="details[${i}][flag]"
                       value="${data.flag ?? 0}">
            </td>
            <td class="text-center">
                <span class="badge bg-secondary subtotal-val">
                    ${(data.jumlah || 0).toLocaleString('id-ID')}
                </span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;

        row.querySelector('.btn-remove-row').addEventListener('click', () => {
            row.remove();
            syncEmpty();
            recalc();
        });

        row.querySelector('.jumlah-input').addEventListener('input', recalc);

        document.getElementById('detail-rows').appendChild(row);
        syncEmpty();
        recalc();
    }

    function syncEmpty() {
        const tbody = document.getElementById('detail-rows');
        document.getElementById('detail-empty').style.display =
            tbody.children.length ? 'none' : '';
    }

    // ── Scan ─────────────────────────────────────────────────────────────────
    function setScanState(state, msg = '') {
        const input    = document.getElementById('scan_kantong_input');
        const feedback = document.getElementById('scan-feedback');
        const spinner  = document.getElementById('scan-spinner');
        const btnScan  = document.getElementById('btn-scan');

        input.classList.remove('scan-success', 'scan-error');
        spinner.classList.add('d-none');
        btnScan.disabled = false;

        if (state === 'loading') {
            spinner.classList.remove('d-none');
            btnScan.disabled = true;
            feedback.innerHTML = '';
        } else if (state === 'success') {
            input.classList.add('scan-success');
            feedback.innerHTML = `<small class="text-success"><i class="fas fa-check-circle me-1"></i>${msg}</small>`;
        } else if (state === 'error') {
            input.classList.add('scan-error');
            feedback.innerHTML = `<small class="text-danger"><i class="fas fa-times-circle me-1"></i>${msg}</small>`;
        } else {
            feedback.innerHTML = '';
        }
    }

    function showStokInfo(jumlah) {
        stokJumlah = jumlah;

        // Box stok di kanan header card
        const boxJml  = document.getElementById('box-jumlah-stok');
        const bigJml  = document.getElementById('big-jumlah-stok');
        const badgeJml = document.getElementById('badge-jumlah-stok');
        const valJml  = document.getElementById('val-jumlah-stok');

        if (boxJml) { boxJml.style.removeProperty('display'); }
        if (bigJml)  bigJml.textContent  = jumlah.toLocaleString('id-ID');
        if (badgeJml) { badgeJml.style.display = ''; }
        if (valJml)   valJml.textContent  = jumlah.toLocaleString('id-ID');

        // Kartu ringkasan kanan
        const cardRingkasan = document.getElementById('card-ringkasan-stok');
        const cardStokVal   = document.getElementById('card-stok-val');
        cardRingkasan.style.display = '';
        cardStokVal.textContent = jumlah.toLocaleString('id-ID');

        recalc();
    }

    async function doScan() {
        const input = document.getElementById('scan_kantong_input');
        const val   = input.value.trim();
        if (!val) return;

        setScanState('loading');

        try {
            const res  = await fetch(SCAN_URL, {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body:    JSON.stringify({ no_kantong: val }),
            });
            const json = await res.json();

            if (!res.ok) {
                setScanState('error', json.message || 'Kantong tidak ditemukan.');
                // Sembunyikan card detail
                document.getElementById('card-detail-kantong').style.display = 'none';
                document.getElementById('card-ringkasan-stok').style.display = 'none';
                return;
            }

            // Isi hidden + field
            document.getElementById('no_kantong').value      = json.no_kantong;
            document.getElementById('stok_kantong_id').value = json.stok_kantong_id;
            document.getElementById('merk').value            = json.merk  || '';
            document.getElementById('jenis').value           = json.jenis || '';
            document.getElementById('tipe').value            = json.tipe  || '';
            document.getElementById('ukuran').value          = json.ukuran|| '';
            input.value                                      = json.no_kantong;

            // Tampilkan card detail
            const cardDetail = document.getElementById('card-detail-kantong');
            cardDetail.style.display = '';
            cardDetail.style.removeProperty('display');

            // Tampilkan info stok
            showStokInfo(json.jumlah ?? 0);

            setScanState('success', `Kantong ditemukan: ${json.no_kantong}`);

            // ── Auto-populate: jika belum ada baris, tambah 1 baris otomatis
            //    dengan jumlah diisi dari stok kantong
            const tbody = document.getElementById('detail-rows');
            if (tbody.children.length === 0 && json.jumlah > 0) {
                addRow({ jumlah: json.jumlah });
            } else if (tbody.children.length > 0) {
                // Update input jumlah pertama (opsional, bisa dihapus kalau tidak mau)
                const firstJumlah = tbody.querySelector('.jumlah-input');
                if (firstJumlah && !firstJumlah.value) {
                    firstJumlah.value = json.jumlah;
                    recalc();
                }
            }

        } catch (err) {
            console.error(err);
            setScanState('error', 'Terjadi kesalahan jaringan.');
        }
    }

    // ── Inisialisasi ─────────────────────────────────────────────────────────
    document.getElementById('btn-add-detail')
        .addEventListener('click', () => addRow());

    // Populate edit mode
    EXISTING.forEach(d => addRow(d));
    syncEmpty();

    if (!IS_EDIT) {
        document.getElementById('btn-scan')
            ?.addEventListener('click', doScan);
        document.getElementById('scan_kantong_input')
            ?.addEventListener('keydown', e => {
                if (e.key === 'Enter') { e.preventDefault(); doScan(); }
            });
    }

})(); // IIFE — hindari polusi global scope
</script>
@endpush
@endsection