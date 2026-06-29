<div class="modal-header py-3 px-6">
    <h3 class="modal-title fs-5">Detail Persediaan: {{ $tipe_kantong->nama }}</h3>
    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
    </div>
</div>

<div class="modal-body py-5 px-6">
    {{-- Search Form --}}
    <form id="form_detail_search" class="mb-5">
        <div class="d-flex align-items-center gap-3">
            <div class="position-relative w-100">
                <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-4 text-gray-500"></i>
                <input type="text" id="detail_keyword" class="form-control form-control-solid ps-12 fs-7" 
                       placeholder="Cari Nomor Kantong..." value="{{ $keyword ?? '' }}">
            </div>
            <button type="submit" class="btn btn-sm btn-primary fs-7 px-5">Cari</button>
            @if(!empty($keyword))
                <button type="button" id="btn_clear_detail" class="btn btn-sm btn-light fs-7 px-5">Reset</button>
            @endif
        </div>
    </form>

    {{-- Tabs --}}
    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-7 fw-bold" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab_kantong_aktif" role="tab">
                Kantong Aktif <span class="badge badge-light-success ms-1">{{ count($active_list) }}</span>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#tab_kantong_riwayat" role="tab">
                Riwayat Tidak Aktif <span class="badge badge-light-danger ms-1">{{ count($inactive_list) }}</span>
            </a>
        </li>
    </ul>

    <div class="tab-content" id="pouchDetailTabContent">
        {{-- Active Pouches --}}
        <div class="tab-pane fade show active" id="tab_kantong_aktif" role="tabpanel">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
                    <thead>
                        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0 sticky-top">
                            <th class="w-10px ps-4 rounded-start bg-secondary">#</th>
                            <th class="bg-secondary">No. Kantong</th>
                            <th class="bg-secondary">Ukuran</th>
                            <th class="bg-secondary">No. Lot</th>
                            <th class="bg-secondary">Status</th>
                            <th class="text-end pe-4 rounded-end bg-secondary">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($active_list as $index => $item)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td class="fw-bold text-gray-800">{{ $item->no_kantong }}</td>
                                <td>
                                    @if(!empty($item->ukuran))
                                        <span class="badge badge-secondary fs-8 px-2 py-0.5">{{ $item->ukuran }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->no_lot ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-light-success fw-bold fs-8 px-2.5 py-1">Aktif</span>
                                </td>
                                <td class="text-end fw-bold pe-4">{{ formatNumber($item->jumlah) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Tidak ada data kantong aktif.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Inactive Pouches History --}}
        <div class="tab-pane fade" id="tab_kantong_riwayat" role="tabpanel">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table align-middle table-row-dashed table-hover fs-7 table-sm">
                    <thead>
                        <tr class="text-start bg-secondary text-dark fw-bold fs-7 text-uppercase border-bottom-0 sticky-top">
                            <th class="w-10px ps-4 rounded-start bg-secondary">#</th>
                            <th class="bg-secondary">No. Kantong</th>
                            <th class="bg-secondary">Ukuran</th>
                            <th class="bg-secondary">No. Lot</th>
                            <th class="bg-secondary">Status</th>
                            <th class="text-end pe-4 rounded-end bg-secondary">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inactive_list as $index => $item)
                            <tr>
                                <td class="ps-4">{{ $index + 1 }}</td>
                                <td class="fw-bold text-gray-800">{{ $item->no_kantong }}</td>
                                <td>
                                    @if(!empty($item->ukuran))
                                        <span class="badge badge-secondary fs-8 px-2 py-0.5">{{ $item->ukuran }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->no_lot ?? '-' }}</td>
                                <td>
                                    @if($item->flag == -1)
                                        <span class="badge badge-light-danger fw-bold fs-8 px-2.5 py-1">Dikembalikan</span>
                                    @elseif($item->flag == 2)
                                        <span class="badge badge-light-primary fw-bold fs-8 px-2.5 py-1">Digunakan</span>
                                    @elseif($item->flag == 3)
                                        <span class="badge badge-light-warning fw-bold fs-8 px-2.5 py-1">Kirim Serologi</span>
                                    @else
                                        <span class="badge badge-light-secondary fw-bold fs-8 px-2.5 py-1">Lainnya ({{ $item->flag }})</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold pe-4">{{ formatNumber($item->jumlah) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">Tidak ada data riwayat kantong.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer py-3 px-6">
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>

<script>
    $(document).ready(function() {
        let base_url = '{{ route("aftap.persediaan_kantong.show", $tipe_kantong->id) }}';

        $('#form_detail_search').submit(function(e) {
            e.preventDefault();
            let keyword = $('#detail_keyword').val();
            let activeTabId = $('.modal-body .nav-tabs .nav-link.active').attr('href');
            
            $.get(base_url, { keyword: keyword }, function(result) {
                $('#modal_info_item').html(result);
                if (activeTabId) {
                    let tabEl = document.querySelector('.modal-body .nav-tabs a[href="' + activeTabId + '"]');
                    if (tabEl) {
                        let tab = new bootstrap.Tab(tabEl);
                        tab.show();
                    }
                }
            });
        });

        $('#btn_clear_detail').click(function() {
            let activeTabId = $('.modal-body .nav-tabs .nav-link.active').attr('href');
            $.get(base_url, { keyword: '' }, function(result) {
                $('#modal_info_item').html(result);
                if (activeTabId) {
                    let tabEl = document.querySelector('.modal-body .nav-tabs a[href="' + activeTabId + '"]');
                    if (tabEl) {
                        let tab = new bootstrap.Tab(tabEl);
                        tab.show();
                    }
                }
            });
        });
    });
</script>
