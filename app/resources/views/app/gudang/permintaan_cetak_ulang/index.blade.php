@extends('layouts.index')

@section('title')
    Permintaan Cetak Ulang Barcode - Gudang
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --pmi-red: #C8102E;
        --pmi-dark: #f23928;
        --pmi-panel: #fcfdff;
        --pmi-teal: #00b4d8;
        --pmi-light: #f0f4f8;
        --pmi-muted: #8892a4;
        --mono: 'IBM Plex Mono', monospace;
        --sans: 'IBM Plex Sans', sans-serif;
    }

    body { font-family: var(--sans); background: var(--pmi-light); }

    /* ── Header ── */
    .pcu-header {
        background: linear-gradient(135deg, var(--pmi-dark) 0%, var(--pmi-panel) 100%);
        color: #fff;
        padding: 1.25rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border-bottom: 3px solid var(--pmi-red);
        box-shadow: 0 4px 20px rgba(0,0,0,.18);
        border-radius: 12px;
        margin-bottom: 1.25rem;
    }
    .pcu-header .badge-pmi {
        background: var(--pmi-red);
        color: #fff;
        font-family: var(--mono);
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .1em;
        padding: .25rem .6rem;
        border-radius: 3px;
        text-transform: uppercase;
        margin-bottom: .4rem;
        display: inline-block;
    }
    .pcu-header h1 { margin: 0; font-size: 1.2rem; font-weight: 600; letter-spacing: .02em; color: #fff; }
    .pcu-header p  { margin: 0; font-size: .78rem; color: rgba(255,255,255,.75); }
    .pcu-header .btn-new {
        background: #fff;
        color: var(--pmi-red);
        border: none;
        border-radius: 8px;
        padding: .6rem 1.1rem;
        font-weight: 700;
        font-size: .82rem;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        transition: all .15s;
        white-space: nowrap;
    }
    .pcu-header .btn-new:hover { background: #f1f5f9; transform: translateY(-1px); }

    /* ── Stat Cards ── */
    .stat-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    @media(max-width:768px) { .stat-row { grid-template-columns: repeat(2, 1fr); } }
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        border-left: 4px solid var(--pmi-muted);
        display: flex;
        align-items: center;
        gap: .9rem;
    }
    .stat-card .icon-box {
        width: 42px; height: 42px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .stat-card .num { font-size: 1.5rem; font-weight: 700; font-family: var(--mono); line-height: 1; }
    .stat-card .lbl { font-size: .72rem; color: var(--pmi-muted); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }

    .stat-card.diajukan  { border-left-color: #f59e0b; }
    .stat-card.diajukan .icon-box  { background:#fef3c7; color:#b45309; }
    .stat-card.disetujui { border-left-color: #00b4d8; }
    .stat-card.disetujui .icon-box { background:#cffafe; color:#0891b2; }
    .stat-card.ditolak    { border-left-color: #ef4444; }
    .stat-card.ditolak .icon-box    { background:#fee2e2; color:#dc2626; }
    .stat-card.selesai    { border-left-color: #22c55e; }
    .stat-card.selesai .icon-box    { background:#dcfce7; color:#16a34a; }

    /* ── Search Card ── */
    .pcu-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 16px rgba(0,0,0,.07);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .pcu-card-header {
        background: var(--pmi-dark);
        color: #fff;
        padding: .7rem 1.25rem;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .pcu-card-body { padding: 1.25rem; }

    /* ── Status badges ── */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .65rem;
        border-radius: 20px;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .03em;
    }
    .badge-status.diajukan  { background:#fef3c7; color:#b45309; }
    .badge-status.disetujui { background:#cffafe; color:#0891b2; }
    .badge-status.ditolak   { background:#fee2e2; color:#dc2626; }
    .badge-status.selesai   { background:#dcfce7; color:#16a34a; }
    .badge-status .dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

    /* ── Table ── */
    .pcu-table-wrap { overflow-x: auto; }
    .pcu-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
    .pcu-table thead tr { background: var(--pmi-dark); color: #fff; }
    .pcu-table thead th {
        padding: .75rem 1rem;
        text-align: left;
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pcu-table tbody tr { border-bottom: 1px solid #f0f4f8; transition: background .15s; }
    .pcu-table tbody tr:hover { background: #f8fafc; }
    .pcu-table tbody td { padding: .7rem 1rem; color: #334155; vertical-align: middle; }
    .pcu-table tbody td .no-surat { font-family: var(--mono); font-weight: 700; color: var(--pmi-dark); }
    .pcu-table tbody td .sub { font-size: .72rem; color: var(--pmi-muted); }

    .avatar-circle {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--pmi-dark);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .75rem;
        flex-shrink: 0;
    }
    .pemohon-cell { display: flex; align-items: center; gap: .6rem; }

    .badge-kode {
        background: #eef2ff;
        color: #4338ca;
        border-radius: 5px;
        padding: .2rem .55rem;
        font-size: .72rem;
        font-weight: 700;
        font-family: var(--mono);
    }

    /* ── Action icon buttons ── */
    .action-icons { display: flex; gap: .35rem; justify-content: flex-end; }
    .icon-btn {
        width: 30px; height: 30px;
        border-radius: 7px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s;
        font-size: .8rem;
        color: #64748b;
    }
    .icon-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0,0,0,.12); }
    .icon-btn.view    { color:#2563eb; border-color:#bfdbfe; }
    .icon-btn.approve { color:#16a34a; border-color:#bbf7d0; }
    .icon-btn.reject  { color:#dc2626; border-color:#fecaca; }
    .icon-btn.done    { color:#0891b2; border-color:#a5f3fc; }
    .icon-btn.del     { color:#dc2626; border-color:#fecaca; }
    .icon-btn.view:hover    { background:#eff6ff; }
    .icon-btn.approve:hover { background:#f0fdf4; }
    .icon-btn.reject:hover  { background:#fef2f2; }
    .icon-btn.done:hover    { background:#ecfeff; }
    .icon-btn.del:hover     { background:#fef2f2; }

    .empty-state { text-align: center; padding: 3rem 1rem; color: var(--pmi-muted); }
    .empty-state i { font-size: 2rem; opacity: .35; display: block; margin-bottom: .6rem; }
</style>
@endpush

@section('content')
    <div class="content flex-column-fluid" id="kt_content">

        {{-- Header --}}
        <div class="pcu-header">
            <div>
                <span class="badge-pmi">PMI &middot; Gudang</span>
                <h1>Permintaan Cetak Ulang Barcode</h1>
                <p>Formulir Permohonan Cetak Ulang Label Barcode &amp; Persetujuan</p>
            </div>
            <button type="button" onclick="info()" class="btn-new">
                <i class="ki-duotone ki-plus fs-4"></i> Buat Permintaan
            </button>
        </div>

        {{-- Stat Cards --}}
        <div class="stat-row">
            <div class="stat-card diajukan">
                <div class="icon-box"><i class="fas fa-hourglass-half"></i></div>
                <div>
                    <div class="num">{{ $counts['diajukan'] ?? 0 }}</div>
                    <div class="lbl">Diajukan</div>
                </div>
            </div>
            <div class="stat-card disetujui">
                <div class="icon-box"><i class="fas fa-thumbs-up"></i></div>
                <div>
                    <div class="num">{{ $counts['disetujui'] ?? 0 }}</div>
                    <div class="lbl">Disetujui</div>
                </div>
            </div>
            <div class="stat-card ditolak">
                <div class="icon-box"><i class="fas fa-ban"></i></div>
                <div>
                    <div class="num">{{ $counts['ditolak'] ?? 0 }}</div>
                    <div class="lbl">Ditolak</div>
                </div>
            </div>
            <div class="stat-card selesai">
                <div class="icon-box"><i class="fas fa-flag-checkered"></i></div>
                <div>
                    <div class="num">{{ $counts['selesai'] ?? 0 }}</div>
                    <div class="lbl">Selesai</div>
                </div>
            </div>
        </div>

        {{-- Search Card --}}
        <div class="pcu-card">
            <div class="pcu-card-header"><i class="fas fa-search"></i> Pencarian</div>
            <div class="pcu-card-body">
                <form id="form_search">
                    @csrf
                    <div class="d-flex flex-lg-row flex-column align-items-lg-end gap-4">
                        <div class="d-flex flex-row gap-4 flex-grow-1 flex-wrap">
                            <x-input name="no_surat" prefix="search_" caption="Cari No Surat" />
                            <x-input name="nama_pemohon" prefix="search_" caption="Cari Nama Pemohon" />
                            <x-io-select
                                name="status"
                                prefix="search_"
                                caption="Status"
                                :options="['' => 'Semua', 'diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'selesai' => 'Selesai']"
                                class="form-select"
                            />
                        </div>
                        <button type="submit" class="btn btn-sm btn-success fw-bold border-0 fs-7 btn-flex gap-3 px-4">
                            <i class="fa fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="pcu-card">
            <div class="pcu-card-header"><i class="fas fa-list"></i> Daftar Permintaan</div>
            <div class="pcu-card-body" style="padding:0;">
                <div id="table"></div>
            </div>
        </div>

    </div>
@endsection

@push('modals')
    <div class="modal fade modal-slide-right" tabindex="-1" id="modal_info">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modal_info_item"></div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        let $form_search = $('#form_search'), $table = $('#table'), $modal_info = $('#modal_info'), $modal_info_item = $('#modal_info_item');
        let selected_page = 1, _token = '{{ csrf_token() }}', base_url = '{{ route('gudang.permintaan_cetak_ulang.index') }}', params_url = '{{ $params ?? '' }}';

        let init = () => { $modal_info_item.html(''); try { $modal_info.modal('hide'); } catch (e) { } search_data(selected_page); }
        let search_data = (page = 1) => {
            let data = get_form_data($form_search);
            data.paginate = 10;
            data.page = selected_page = get_selected_page(page, selected_page);
            $.post(base_url + '/search?' + params_url, data, (result) => $table.html(result)).fail((xhr) => $table.html(xhr.responseText));
        }
        let display_modal_info = (item) => { $modal_info_item.html(item); $modal_info.modal('show'); }
        let info = (id = '') => { $.get(base_url + '/' + (id === '' ? 'create' : (id + '/edit')) + '?' + params_url, (result) => display_modal_info(result)).fail((xhr) => display_modal_info(xhr.responseText)); }

        let confirm_delete = (id) => {
            Swal.fire(swal_delete_params).then((result) => {
                if (result.isConfirmed) $.post(base_url + '/' + id, {_method: 'delete', _token}, (data) => {
                    if (data.error) swal.fire({icon: 'error', title: data.error}).then(() => init());
                    else swal.fire('Berhasil Dihapus').then(() => init());
                }).fail((xhr) => $table.html(xhr.responseText));
            });
        }

        let approve_permintaan = (id) => {
            Swal.fire({
                title: 'Setujui permintaan ini?',
                input: 'text',
                inputLabel: 'Nama Kasi (mengetahui)',
                showCancelButton: true,
                confirmButtonText: 'Setujui',
                confirmButtonColor: '#16a34a',
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.post(base_url + '/' + id + '/approve', { _token, nama_kasi: result.value }, () => {
                    swal.fire('Permintaan disetujui').then(() => init());
                }).fail((xhr) => swal.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Gagal menyetujui' }));
            });
        }

        let reject_permintaan = (id) => {
            Swal.fire({
                title: 'Tolak permintaan ini?',
                input: 'text',
                inputLabel: 'Alasan penolakan',
                inputValidator: (value) => !value && 'Alasan wajib diisi',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                confirmButtonColor: '#dc2626',
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.post(base_url + '/' + id + '/reject', { _token, catatan: result.value }, () => {
                    swal.fire('Permintaan ditolak').then(() => init());
                }).fail((xhr) => swal.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Gagal menolak' }));
            });
        }

        let selesaikan_permintaan = (id) => {
            Swal.fire({
                title: 'Tandai selesai?',
                text: 'Pastikan label sudah benar-benar dicetak ulang lewat menu Cetak Ulang Barcode.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai',
                confirmButtonColor: '#0891b2',
            }).then((result) => {
                if (!result.isConfirmed) return;
                $.post(base_url + '/' + id + '/selesai', { _token }, () => {
                    swal.fire('Permintaan ditandai selesai').then(() => init());
                }).fail((xhr) => swal.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Gagal memproses' }));
            });
        }

        let init_form = (id = '') => {
            let $form_info = $('#form_info');
            $form_info.submit((e) => {
                e.preventDefault();
                let url = base_url;
                let data = new FormData($form_info.get(0));
                if (id !== '') url += '/' + id + '?_method=put';
                $.ajax({ url, type: 'post', data, cache: false, processData: false, contentType: false, success: () => init() }).fail((xhr) => error_handle(xhr.responseText));
            });
        }
        $form_search.submit((e) => { e.preventDefault(); search_data(); });
        init_form_element();
        init();
    </script>
@endpush