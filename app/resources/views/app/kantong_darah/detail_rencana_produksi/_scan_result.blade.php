<div class="row g-5">
    {{-- Left Column: Info Cards --}}
    <div class="col-lg-7">
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
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center w-40px h-40px rounded bg-light-secondary">
                            <i class="fa fa-box text-gray-600 fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">Detail Metadata Tidak Ditemukan</div>
                            <div class="text-muted fs-8">No. Kantong: <span class="fw-bold text-primary">{{ $no_kantong }}</span></div>
                        </div>
                    </div>
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

    {{-- Right Column: Verification Forms for each Satelit --}}
    <div class="col-lg-5">
        <div class="sticky-top" style="top: 20px;">
            @foreach($details as $detail)
                <div class="card card-flush border border-primary shadow-sm mb-5">
                    <div class="card-header min-h-50px px-5 pt-4 pb-0 bg-light-primary d-flex justify-content-between align-items-center">
                        <h6 class="card-title fw-bold fs-6 text-primary m-0">
                            <i class="fa fa-weight-hanging text-primary me-2"></i>Satelit {{ $detail->no_satelit ?? '-' }}
                        </h6>
                        @if($detail->gram && $detail->volume)
                            <span class="badge badge-light-success fw-bold fs-9 border border-success border-opacity-50">
                                <i class="fa fa-check-circle text-success me-1"></i> Terisi
                            </span>
                        @else
                            <span class="badge badge-light-warning fw-bold fs-9 border border-warning border-opacity-50">
                                <i class="fa fa-clock text-warning me-1"></i> Belum Terisi
                            </span>
                        @endif
                    </div>
                    <div class="card-body px-5 pt-4 pb-5">
                        <form class="form-verify-detail" data-id="{{ $detail->id }}" data-jenis-darah="{{ $detail->jenis_darah ?? '' }}">
                            {{-- Jenis Darah Display --}}
                            <div class="mb-4">
                                <label class="form-label text-muted fs-8 text-uppercase fw-bold">Jenis Darah</label>
                                <div>
                                    @if($detail->jenis_darah)
                                        <span class="badge badge-primary fw-bold fs-6 px-3 py-1.5">{{ $detail->jenis_darah }}</span>
                                    @else
                                        <span class="badge badge-light-danger fw-bold fs-6 px-3 py-1.5">Belum Dipilih di Rencana</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Previously saved values --}}
                            @if($detail->gram)
                                <div class="alert alert-light-success border border-success border-dashed py-2 px-3 mb-4 fs-7">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="fa fa-check-circle text-success fs-8"></i>
                                        <strong class="text-success fs-8">Data Sebelumnya</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Berat:</span>
                                        <span class="fw-bold">{{ $detail->gram }} gram</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Volume:</span>
                                        <span class="fw-bold">{{ $detail->volume ?? '-' }} ml</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Weight Input --}}
                            <div class="mb-4">
                                <label class="form-label required fw-bold fs-7">Berat Kantong (gram)</label>
                                <input type="number" class="form-control form-control-sm gram-input"
                                       placeholder="Masukkan berat dalam gram"
                                       value="{{ $detail->gram ?? '' }}"
                                       step="0.01" min="0" required {{ !$detail->jenis_darah ? 'disabled' : '' }}>
                                <div class="form-text text-muted fs-9 mt-1">
                                    Masukkan berat total dalam gram
                                </div>
                            </div>

                            {{-- Calculated Volume Display --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted fs-8">Volume (hasil konversi)</label>
                                <div class="bg-light-primary border border-primary border-dashed rounded py-3 px-3 text-center">
                                    <div class="fs-3 fw-bolder text-primary volume-display">
                                        @if($detail->volume)
                                            {{ $detail->volume }} ml
                                        @else
                                            -
                                        @endif
                                    </div>
                                    <div class="text-muted fs-9 mt-0.5">ml (mililiter)</div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn btn-sm btn-primary w-100 fw-bold py-2" {{ !$detail->jenis_darah ? 'disabled' : '' }}>
                                <i class="fa fa-save me-2"></i> Simpan Detail
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
