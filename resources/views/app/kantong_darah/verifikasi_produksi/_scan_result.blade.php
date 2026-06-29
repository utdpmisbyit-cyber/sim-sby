<div class="row g-5">
    {{-- Left Column: Info Cards --}}
    <div class="col-lg-7">
        {{-- Produksi Darah Info --}}
        <div class="card card-flush border mb-4">
            <div class="card-header min-h-40px px-4 pt-3 pb-0">
                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">
                    <i class="fa fa-flask text-primary me-2"></i>Produksi Darah
                </h6>
                @if($produksi_darah->gram && $produksi_darah->volume)
                    <span class="badge badge-light-success fw-bold fs-8 border border-success border-opacity-50">
                        <i class="fa fa-check-circle me-1"></i> Sudah Diverifikasi
                    </span>
                @else
                    <span class="badge badge-light-warning fw-bold fs-8 border border-warning border-opacity-50">
                        <i class="fa fa-clock me-1"></i> Belum Diverifikasi
                    </span>
                @endif
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                <div class="row">
                    <div class="col-6 mb-2">
                        <span class="text-muted fs-8">Kode</span>
                        <div class="fw-bold text-dark">{{ $produksi_darah->kode }}</div>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted fs-8">Barcode</span>
                        <div class="fw-bold text-primary">{{ $produksi_darah->barcode }}</div>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted fs-8">Status</span>
                        <div>
                            @if($produksi_darah->status === 'SENDING')
                                <span class="badge badge-light-warning fw-bold fs-8">SENDING</span>
                            @elseif($produksi_darah->status === 'QUEUED')
                                <span class="badge badge-light-primary fw-bold fs-8">QUEUED</span>
                            @elseif($produksi_darah->status === 'ONGOING')
                                <span class="badge badge-light-info fw-bold fs-8">ONGOING</span>
                            @elseif($produksi_darah->status === 'COMPLETED')
                                <span class="badge badge-light-success fw-bold fs-8">COMPLETED</span>
                            @else
                                <span class="badge badge-light-secondary fw-bold fs-8">{{ $produksi_darah->status }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 mb-2">
                        <span class="text-muted fs-8">Jenis Darah</span>
                        <div class="fw-bold text-dark">{{ $jenis_darah ?? '-' }}</div>
                    </div>
                    @if($produksi_darah->gram)
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Berat (gram)</span>
                            <div class="fw-bold text-success">{{ $produksi_darah->gram }} gram</div>
                        </div>
                    @endif
                    @if($produksi_darah->volume)
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Volume (ml)</span>
                            <div class="fw-bold text-success">{{ $produksi_darah->volume }} ml</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Jenis Kantong Info --}}
        <div class="card card-flush border mb-4">
            <div class="card-header min-h-40px px-4 pt-3 pb-0">
                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">
                    <i class="fa fa-box text-info me-2"></i>Jenis Kantong
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                @if($jenis_kantong)
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center w-40px h-40px rounded bg-light-info">
                            <i class="fa fa-box text-info fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $jenis_kantong }}</div>
                            <div class="text-muted fs-8">No. Kantong: {{ $no_kantong }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-muted fst-italic fs-7">Data jenis kantong tidak ditemukan</div>
                @endif
            </div>
        </div>

        {{-- Aftap Info --}}
        <div class="card card-flush border mb-4">
            <div class="card-header min-h-40px px-4 pt-3 pb-0">
                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">
                    <i class="fa fa-syringe text-danger me-2"></i>Data Aftap
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                @if($aftap)
                    <div class="row">
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Kode Aftap</span>
                            <div class="fw-bold text-dark">{{ $aftap->kode }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">No. Kantong</span>
                            <div class="fw-bold text-dark">{{ $aftap->no_kantong }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Jenis Donor</span>
                            <div class="fw-bold text-dark">{{ $aftap->jenis_donor ?? '-' }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Status</span>
                            <div>
                                @if($aftap->status === 'Approved')
                                    <span class="badge badge-light-success fw-bold fs-8">{{ $aftap->status }}</span>
                                @elseif($aftap->status === 'Pending' || $aftap->status === 'Ongoing')
                                    <span class="badge badge-light-warning fw-bold fs-8">{{ $aftap->status }}</span>
                                @else
                                    <span class="badge badge-light-danger fw-bold fs-8">{{ $aftap->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Jam Mulai</span>
                            <div class="fw-bold text-dark">{{ $aftap->jam_mulai ? formatTime($aftap->jam_mulai) : '-' }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Jam Selesai</span>
                            <div class="fw-bold text-dark">{{ $aftap->jam_selesai ? formatTime($aftap->jam_selesai) : '-' }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">CC Ambil</span>
                            <div class="fw-bold text-dark">{{ $aftap->cc_ambil ?? '-' }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Satelit</span>
                            <div class="fw-bold text-dark">{{ $aftap->satelit ?? '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-muted fst-italic fs-7">Data aftap tidak ditemukan untuk no. kantong ini</div>
                @endif
            </div>
        </div>

        {{-- Donor Info --}}
        <div class="card card-flush border mb-4">
            <div class="card-header min-h-40px px-4 pt-3 pb-0">
                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">
                    <i class="fa fa-user text-success me-2"></i>Data Donor
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                @if($donor)
                    <div class="row">
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Kode Donor</span>
                            <div class="fw-bold text-dark">{{ $donor->kode }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Nama</span>
                            <div class="fw-bold text-dark">{{ $donor->nama }}</div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Golongan Darah</span>
                            <div class="fw-bold text-dark fs-5">
                                <span class="badge badge-light-danger fw-bolder fs-6 px-3 py-1">{{ $donor->golongan_darah }}{{ $donor->rhesus }}</span>
                            </div>
                        </div>
                        <div class="col-6 mb-2">
                            <span class="text-muted fs-8">Jenis Kelamin</span>
                            <div class="fw-bold text-dark">{{ $donor->jenis_kelamin }}</div>
                        </div>
                    </div>
                @else
                    <div class="text-muted fst-italic fs-7">Data donor tidak ditemukan</div>
                @endif
            </div>
        </div>

        {{-- Serologi Info --}}
        <div class="card card-flush border mb-4">
            <div class="card-header min-h-40px px-4 pt-3 pb-0">
                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">
                    <i class="fa fa-vial text-warning me-2"></i>Data Serologi
                </h6>
            </div>
            <div class="card-body px-4 pt-3 pb-4">
                @if(!empty($serologi_details) && count($serologi_details) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-row-dashed align-middle fs-7 mb-0">
                            <thead>
                                <tr class="text-muted fw-bold fs-8 text-uppercase">
                                    <th>No. Serologi</th>
                                    <th>Status</th>
                                    <th>Hasil</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($serologi_details as $sd)
                                    <tr>
                                        <td class="fw-semibold">{{ $sd->serologi->nomor ?? '-' }}</td>
                                        <td>
                                            @if($sd->status === 'selesai')
                                                <span class="badge badge-light-success fw-bold fs-9">Selesai</span>
                                            @else
                                                <span class="badge badge-light-warning fw-bold fs-9">{{ ucfirst($sd->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sd->hasil === 'Non Reaktif' || $sd->hasil === 'NR' || $sd->hasil === 'Negatif')
                                                <span class="badge badge-light-success fw-bold fs-9">{{ $sd->hasil }}</span>
                                            @elseif($sd->hasil === 'Reaktif' || $sd->hasil === 'R' || $sd->hasil === 'Positif')
                                                <span class="badge badge-light-danger fw-bold fs-9">{{ $sd->hasil }}</span>
                                            @elseif($sd->hasil)
                                                <span class="badge badge-light-info fw-bold fs-9">{{ $sd->hasil }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $sd->keterangan ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-muted fst-italic fs-7">Belum ada data serologi untuk no. kantong ini</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Column: Verification Form --}}
    <div class="col-lg-5">
        <div class="card card-flush border border-primary shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header min-h-50px px-5 pt-4 pb-0 bg-light-primary">
                <h6 class="card-title fw-bold fs-6 text-primary m-0">
                    <i class="fa fa-weight-hanging text-primary me-2"></i>Verifikasi Berat Kantong
                </h6>
            </div>
            <div class="card-body px-5 pt-4 pb-5">
                <form id="form_verify">
                    <input type="hidden" id="produksi_darah_id" value="{{ $produksi_darah->id }}">
                    <input type="hidden" id="jenis_darah_value" value="{{ $jenis_darah ?? '' }}">

                    {{-- Jenis Darah Display --}}
                    <div class="mb-5">
                        <label class="form-label text-muted fs-8 text-uppercase fw-bold">Jenis Darah (dari Rencana Produksi)</label>
                        <div class="d-flex align-items-center gap-2">
                            @if($jenis_darah)
                                <span class="badge badge-primary fw-bold fs-5 px-4 py-2">{{ $jenis_darah }}</span>
                            @else
                                <span class="text-muted fst-italic">Tidak ditemukan</span>
                            @endif
                        </div>
                    </div>

                    @if($rencana_produksi_detail)
                        <div class="mb-5">
                            <label class="form-label text-muted fs-8 text-uppercase fw-bold">Detail Rencana</label>
                            <div class="bg-light rounded p-3 fs-7">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">No. Kantong</span>
                                    <span class="fw-bold">{{ $rencana_produksi_detail->no_kantong }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">No. Satelit</span>
                                    <span class="fw-bold">{{ $rencana_produksi_detail->no_satelit ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Previously saved values --}}
                    @if($produksi_darah->gram)
                        <div class="alert alert-light-success border border-success border-dashed py-3 px-4 mb-5">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fa fa-check-circle text-success"></i>
                                <strong class="text-success fs-7">Data Sebelumnya</strong>
                            </div>
                            <div class="d-flex justify-content-between fs-7">
                                <span>Berat</span>
                                <span class="fw-bold">{{ $produksi_darah->gram }} gram</span>
                            </div>
                            <div class="d-flex justify-content-between fs-7">
                                <span>Volume</span>
                                <span class="fw-bold">{{ $produksi_darah->volume ?? '-' }} ml</span>
                            </div>
                        </div>
                    @endif

                    {{-- Weight Input --}}
                    <div class="mb-4">
                        <label class="form-label required fw-bold">Berat Kantong (gram)</label>
                        <input type="number" class="form-control form-control-lg" id="gram_input"
                               placeholder="Masukkan berat dalam gram"
                               value="{{ $produksi_darah->gram ?? '' }}"
                               step="0.01" min="0" required autofocus>
                        <div class="form-text text-muted fs-8 mt-1">
                            Timbang kantong darah, lalu masukkan berat total dalam gram
                        </div>
                    </div>

                    {{-- Calculated Volume Display --}}
                    <div class="mb-5">
                        <label class="form-label fw-bold text-muted">Volume (hasil konversi)</label>
                        <div class="bg-light-primary border border-primary border-dashed rounded py-4 px-4 text-center">
                            <div class="fs-1 fw-bolder text-primary" id="volume_display">
                                @if($produksi_darah->volume)
                                    {{ $produksi_darah->volume }} ml
                                @else
                                    -
                                @endif
                            </div>
                            <div class="text-muted fs-8 mt-1">ml (mililiter)</div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-3">
                        <i class="fa fa-save me-2"></i> Simpan Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
