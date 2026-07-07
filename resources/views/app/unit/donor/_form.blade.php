<style>
/* ─── SEARCH DROPDOWN (ganti Select2 AJAX) ────────────────────────────── */
.search-wrap {
    position: relative;
}
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
.search-dropdown .sd-item:hover,
.search-dropdown .sd-item.active { background: #fdeeee; }

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
.search-dropdown .sd-item:hover .sd-code,
.search-dropdown .sd-item.active .sd-code {
    background: #e74c3c;
    color: #fff;
}

.search-dropdown .sd-text { color: #1a1a2e; flex: 1; }
.search-dropdown .sd-empty,
.search-dropdown .sd-loading {
    padding: 12px 10px;
    font-size: .8rem;
    color: #999;
    text-align: center;
}
.search-dropdown .sd-loading i { color: #3e97ff; }

/* ─── SELECTED BADGE ─────────────────────────────────────────────────── */
.selected-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e8f4fd;
    border: 1px solid #b3d9f5;
    border-radius: 20px;
    padding: 2px 8px 2px 6px;
    font-size: .77rem;
    font-weight: 600;
    color: #1a5276;
    margin-top: 3px;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.selected-badge .badge-remove {
    cursor: pointer;
    color: #c0392b;
    font-weight: 900;
    font-size: .8rem;
    line-height: 1;
    flex-shrink: 0;
}
.selected-badge .badge-remove:hover { color: #922b21; }

/* ─── REQUIRED FIELD MARK (untuk label custom, bukan x-io-*) ─────────── */
.required-mark { color: #e74c3c; margin-left: 2px; }
.search-wrap.is-invalid .form-control-sm,
.fv-row.is-invalid .form-control-sm {
    border-color: #e74c3c !important;
    box-shadow: 0 0 0 3px rgba(231,76,60,.12) !important;
}
</style>


<form id="form_info" enctype="multipart/form-data">
    @csrf
    <div class="modal-header py-3 px-6">
        <h3 class="modal-title fs-5">{{ !empty($donor) ? 'Ubah' : 'Tambah' }} Donor</h3>
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
        </div>
    </div>

    <div class="modal-body py-5 px-6">
        <div class="row g-5">

            {{-- ═══════════════════════════ LEFT COLUMN ═══════════════════════════ --}}
            <div class="col-lg-6">

                {{-- Foto Pendonor --}}
                <div class="card card-flush border mb-4">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Foto Pendonor</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="d-flex align-items-center gap-4">
                            <div id="foto_preview_wrapper" class="position-relative" style="width:90px;height:90px;flex-shrink:0;">
                                <img id="foto_preview"
                                     src="{{ !empty($donor->foto) ? asset('storage/'.$donor->foto) : asset('assets/media/avatars/blank.jpg') }}"
                                     class="rounded-2 border object-fit-cover w-100 h-100"
                                     alt="Foto Donor" />
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <label for="foto_upload" class="btn btn-sm btn-light-primary fw-bold fs-8 mb-0 cursor-pointer">
                                    <i class="ki-duotone ki-folder-up fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Upload Foto
                                </label>
                                <input type="file" id="foto_upload" name="foto" accept="image/*" class="d-none" />
                                <button type="button" class="btn btn-sm btn-light-info fw-bold fs-8" id="btn_capture">
                                    <i class="ki-duotone ki-camera fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Ambil Foto
                                </button>
                                <button type="button" class="btn btn-sm btn-light-danger fw-bold fs-8" id="btn_hapus_foto">
                                    <i class="ki-duotone ki-trash fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Hapus
                                </button>
                            </div>
                        </div>
                        <div id="camera_area" class="mt-3 d-none">
                            <video id="camera_video" class="w-100 rounded-2 border" autoplay playsinline style="max-height:200px;object-fit:cover;"></video>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-primary" id="btn_snap">
                                    <i class="ki-duotone ki-camera fs-5 me-1"><span class="path1"></span><span class="path2"></span></i>Ambil
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="btn_cancel_camera">Batal</button>
                            </div>
                        </div>
                        <canvas id="camera_canvas" class="d-none"></canvas>
                        <input type="hidden" name="foto_base64" id="foto_base64" value="" />
                    </div>
                </div>

                {{-- Identitas Pendonor --}}
                <div class="card card-flush border mb-4">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Identitas Pendonor</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        
                        <div class="row g-2">
                            <div class="col-8">
                                <x-io-input :viewtype="2" name="nama" caption="Nama Lengkap" :value="$donor->nama ?? ''" required />
                            </div>
                            <div class="card-header min-h-40px px-4 pt-3 pb-0 d-flex align-items-center justify-content-between">
                                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Alamat</h6>
                                <button type="button" class="btn btn-xs btn-light-primary fs-9 py-1 px-2" id="btn_copy_alamat"
                                        title="Salin Alamat 1 ke Alamat 2">
                                    <i class="ki-duotone ki-copy fs-7 me-1"><span class="path1"></span><span class="path2"></span></i>
                                    Salin ke Alamat 2
                                </button>
                            </div>
                            <div class="col-5">
                                <x-io-input :viewtype="2" name="alamat_1" id="alamat_1" caption="Alamat KTP" :value="$donor->alamat_1 ?? ''" required />
                            </div>
                            <div class="col-5">
                                <x-io-input :viewtype="2" name="alamat_2" id="alamat_2" caption="Alamat Domisili" :value="$donor->alamat_2 ?? ''" required />
                            </div>
                             <div class="col-3">
                                <x-io-input :viewtype="2" name="kode_pos" caption="Kode Pos" :value="$donor->kode_pos ?? ''" required />
                            </div>
                             <div class="col-4">
                                    <x-io-input :viewtype="2"
                                    name="tanggal_lahir"
                                    class="datepicker"
                                    caption="Tanggal Lahir"
                                    :value="!empty($donor->tanggal_lahir)
                                        ? \Carbon\Carbon::parse($donor->tanggal_lahir)->format('d/m/Y')
                                        : ''"
                                    required />
                            </div>
                           
                            <div class="col-4">
                                <x-io-select :viewtype="2" name="jenis_kelamin" caption="Jenis Kelamin"
                                    :options="array_combine($jenis_kelamin_options, $jenis_kelamin_options)"
                                    :value="$donor->jenis_kelamin ?? ''"
                                    data-dropdown-parent="#modal_info" required />
                            </div>
                            {{-- KEWARGANEGARAAN — custom search dropdown --}}
                            <div class="col-4">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">Kewarganegaraan <span class="required-mark">*</span></label>
                                    <input type="hidden" name="kewarganegaraan_id" id="kewarganegaraan_id"
                                           value="{{ $donor->kewarganegaraan_id ?? '' }}">
                                    <div class="search-wrap" id="wrap_kewarganegaraan">
                                        <input type="text" id="search_kewarganegaraan"
                                               class="form-control form-control-sm"
                                               placeholder="Cari kewarganegaraan…"
                                               value="{{ $donor->kewarganegaraan->nama ?? '' }}"
                                               autocomplete="off" required>
                                        <button type="button" class="search-clear" id="clear_kewarganegaraan">✕</button>
                                        <div class="search-dropdown" id="dd_kewarganegaraan"></div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- WILAYAH — custom search dropdown --}}
                            <div class="col-4">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">Wilayah / Kota <span class="required-mark">*</span></label>
                                    <input type="hidden" name="wilayah_id" id="wilayah_id"
                                           value="{{ $donor->wilayah_id ?? '' }}">
                                    <div class="search-wrap" id="wrap_wilayah">
                                        <input type="text" id="search_wilayah"
                                               class="form-control form-control-sm"
                                               placeholder="Cari wilayah…"
                                               value="{{ $donor->wilayah->nama ?? '' }}"
                                               autocomplete="off" required>
                                        <button type="button" class="search-clear" id="clear_wilayah">✕</button>
                                        <div class="search-dropdown" id="dd_wilayah"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- KECAMATAN — custom search dropdown --}}
                            <div class="col-4">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">Kecamatan <span class="required-mark">*</span></label>
                                    <input type="hidden" name="kecamatan_id" id="kecamatan_id"
                                           value="{{ $donor->kecamatan_id ?? '' }}">
                                    <div class="search-wrap" id="wrap_kecamatan">
                                        <input type="text" id="search_kecamatan"
                                               class="form-control form-control-sm"
                                               placeholder="Cari kecamatan…"
                                               value="{{ $donor->kecamatan->nama ?? '' }}"
                                               autocomplete="off" required>
                                        <button type="button" class="search-clear" id="clear_kecamatan">✕</button>
                                        <div class="search-dropdown" id="dd_kecamatan"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- PEKERJAAN — custom search dropdown --}}
                            <div class="col-4">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">Pekerjaan <span class="required-mark">*</span></label>
                                    <input type="hidden" name="pekerjaan_id" id="pekerjaan_id"
                                           value="{{ $donor->pekerjaan_id ?? '' }}">
                                    <div class="search-wrap" id="wrap_pekerjaan">
                                        <input type="text" id="search_pekerjaan"
                                               class="form-control form-control-sm"
                                               placeholder="Cari pekerjaan…"
                                               value="{{ $donor->pekerjaan->nama ?? '' }}"
                                               autocomplete="off" required>
                                        <button type="button" class="search-clear" id="clear_pekerjaan">✕</button>
                                        <div class="search-dropdown" id="dd_pekerjaan"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <x-io-select :viewtype="2" name="agama" caption="Agama"
                                    :options="array_combine($agama_options, $agama_options)"
                                    :value="$donor->agama ?? ''"
                                    data-dropdown-parent="#modal_info" />
                            </div>
                            <div class="col-4">
                                <x-io-input :viewtype="2" name="usia" id="usia" type="number" caption="Usia" :value="$donor->usia ?? ''" readonly required />
                            </div>
                              <div class="card-header min-h-40px px-4 pt-3 pb-0">
                                <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Kontak &amp; Identifikasi</h6>
                            </div>
                {{-- Kontak & Identifikasi --}}
                            <div class="card-body px-4 pt-3 pb-4">
                                <div class="row g-2">
                                    <div class="col-4">
                                        <x-io-input :viewtype="2" name="no_telp" caption="No. Telepon" :value="$donor->no_telp ?? ''" required/>
                                    </div>
                                    <div class="col-4">
                                        <x-io-input :viewtype="2" name="no_ktp" id="no_ktp" caption="No. KTP" :value="$donor->no_ktp ?? ''" required/>
                                    </div>
                                    <div class="col-4">
                                        <x-io-input :viewtype="2" name="no_sim" caption="No. SIM" :value="$donor->no_sim ?? ''" required/>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <div class="card card-flush border mb-4">
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="row g-2">
                                <div class="col-4">
                                    <x-io-input :viewtype="2" name="skrining" caption="Skrining Antibody"
                                        :value="!empty($donor) ? $donor->skrining : 'Negatif'" required />
                                </div>
                                <div class="col-4">
                                    <x-io-input :viewtype="2" name="no_fpup" caption="Permintaan FPUP" :value="$donor->no_fpup ?? ''" />
                                </div>
                                <div class="col-4">
                                    <input type="hidden" name="fpup_id" id="fpup_id" value="{{ $donor->fpup_id ?? '' }}">
                                </div>
                        </div>
                    </div>
                </div>

                {{-- Lainnya --}}
                <div class="card card-flush border">
                   
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="row g-2">
                           
                            
                        </div>
                    </div>
                </div>

            </div>{{-- /LEFT --}}

            {{-- ═══════════════════════════ RIGHT COLUMN ══════════════════════════ --}}
            <div class="col-lg-6">

                {{-- Kode Registrasi --}}
                <div class="card card-flush border mb-4">
                    <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Kode Registrasi</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">Kode <span class="text-muted fs-9">(otomatis)</span></label>
                                    <input type="text" name="kode" id="kode_display"
                                           class="form-control form-control-sm bg-light-secondary"
                                           value="{{ $donor->kode ?? '' }}" readonly placeholder="T2604001" />
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">No. Pendaftaran <span class="text-muted fs-9">(otomatis)</span></label>
                                    <input type="text" name="no_pendaftaran" id="no_pendaftaran_display"
                                           class="form-control form-control-sm bg-light-secondary"
                                           value="{{ $donor->no_pendaftaran ?? '' }}" readonly placeholder="A2604290001" />
                                </div>
                            </div>
                            <div class="col-6">
                                <x-io-input :viewtype="2" name="created_at_display" caption="Tanggal Daftar"
                                    :value="!empty($donor?->created_at)
                                        ? \Carbon\Carbon::parse($donor->created_at)->format('d-m-Y H:i')
                                        : now()->format('d-m-Y H:i')"
                                    readonly />
                            </div>

                            {{-- ASAL DARAH — custom search dropdown --}}
                            <div class="col-6">
                                <div class="fv-row">
                                    <label class="form-label fs-8 fw-bold text-muted mb-1">Tempat Donor</label>
                                    <input type="hidden" name="asal_darah_id" id="asal_darah_id"
                                           value="{{ $donor->asal_darah_id ?? 1 }}">
                                    <div class="search-wrap" id="wrap_asal_darah">
                                        <input type="text"
                                            id="search_asal_darah"
                                            class="form-control form-control-sm"
                                            placeholder="Cari tempat donor…"
                                            value="{{ $donor->asalDarah->nama ?? $donor->nama_asal_darah ?? 'UTD PMI Surabaya' }}"
                                            autocomplete="off">
                                        <button type="button" class="search-clear" id="clear_asal_darah">✕</button>
                                        <div class="search-dropdown" id="dd_asal_darah"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                             <x-io-input :viewtype="2"
                                name="nama_asal_darah"
                                caption="Nama Tempat Donor"
                                :value="$donor->nama_asal_darah ?? ($donor->asalDarah->nama ?? 'UTD PMI Surabaya')"
                                readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-flush border mb-4">
                      <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Golongan Darah</h6>
                      </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="row g-2">
                             <div class="col-3">
                                <label class="form-label fs-8 fw-bold text-muted mb-1">Golongan Darah</label>
                                <div class="d-flex flex-wrap gap-1" id="gol_darah_buttons">
                                    @foreach($golongan_darah_options as $gd)
                                        <button type="button"
                                            class="btn btn-sm btn-gol-darah {{ ($donor->golongan_darah ?? '') === $gd ? 'btn-danger' : 'btn-light' }} fw-bold fs-8 px-3 py-1"
                                            data-value="{{ $gd }}">{{ $gd }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="golongan_darah" id="golongan_darah_input"
                                       value="{{ $donor->golongan_darah ?? '' }}"
                                       {{ ($donor->is_golongan_darah_locked ?? false) ? 'disabled' : '' }} />
                            </div>
                            <div class="col-3">
                                <label class="form-label fs-8 fw-bold text-muted mb-1">Rhesus</label>
                                <div class="d-flex gap-1" id="rhesus_buttons">
                                    @foreach($rhesus_options as $rh)
                                        <button type="button"
                                            class="btn btn-sm btn-rhesus {{ ($donor->rhesus ?? '') === $rh ? 'btn-primary' : 'btn-light' }} fw-bold fs-8 px-3 py-1"
                                            data-value="{{ $rh }}">{{ $rh === '+' ? '+ Positif' : '- Negatif' }}</button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="rhesus" id="rhesus_input"
                                       value="{{ $donor->rhesus ?? '' }}"
                                       {{ ($donor->is_golongan_darah_locked ?? false) ? 'disabled' : '' }} />
                            </div>
                            <div class="col-3">
                                <x-io-select :viewtype="2" name="golongan_darah_lain" caption="Gol. Darah Lain"
                                    :options="array_combine($golongan_darah_lain_options, $golongan_darah_lain_options)"
                                    :value="$donor->golongan_darah_lain ?? ''" data-dropdown-parent="#modal_info" />
                            </div>
                            <div class="col-3">
                                <x-io-input :viewtype="2" name="golongan_rhesus" caption="Rhesus Lain" :value="$donor->golongan_rhesus ?? ''" />
                            </div>
                             @if(!empty($donor) && ($donor->is_golongan_darah_locked ?? false))
                            <div class="alert alert-warning d-flex align-items-center py-2 px-3 fs-8 mt-3 mb-0">
                                <i class="ki-duotone ki-information-5 fs-5 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                Golongan darah sudah terkunci dan tidak dapat diubah.
                            </div>
                        @endif
                        </div>
                    </div>
                </div>

                
                <div class="card card-flush border mb-4">
                  <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Status Cekal</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="row g-2">
                           <div class="col-4">
                                <x-io-input :viewtype="2" name="cekal" id="cekal"  readonly />
                            </div>
                            <!-- <div class="col-4">
                                <x-io-input :viewtype="2" name="no_cekal" caption="No. Cekal" :value="$donor->no_cekal ?? ''" />
                            </div>
                            <div class="col-4">
                                <x-io-input :viewtype="2" name="tanggal_cekal" type="datepicker" caption="Tanggal Cekal"
                                            :value="formatDate($donor->tanggal_cekal ?? '')" />
                            </div> -->
                        </div>
                       
                    </div>
                </div>

                <div class="card card-flush border">
                     <div class="card-header min-h-40px px-4 pt-3 pb-0">
                        <h6 class="card-title text-muted fw-bold fs-8 text-uppercase m-0">Lainnya</h6>
                    </div>
                    <div class="card-body px-4 pt-3 pb-4">
                        <div class="row g-2">
                             <div class="col-4">
                                <label class="form-label fs-8 fw-bold text-muted mb-1">Penghargaan</label>
                                @php $selected = explode(',', $donor->penghargaan ?? ''); @endphp
                                <div class="d-flex flex-column gap-1">
                                    @foreach([10,25,50,100] as $p)
                                        <label class="form-check form-check-sm">
                                            <input type="checkbox" name="penghargaan[]" value="{{ $p }}"
                                                class="form-check-input" {{ in_array($p, $selected) ? 'checked' : '' }}>
                                            <span class="form-check-label">{{ $p }} Kali</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-4">
                                <x-io-input :viewtype="2" name="donor_ke" id="donor_ke" type="number"
                                    caption="Donor Ke-" :value="$donor->donor_ke ?? 1" required />
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- /RIGHT --}}
        </div>
    </div>

    <div class="modal-footer py-3 px-6">
        <button type="button" class="btn btn-sm btn-secondary me-3" onclick="init()">Batal</button>
        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
    </div>
</form>

<script>
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

    // Guard: daftarkan listener klik-luar HANYA SEKALI,
    // walau partial ini di-load ulang berkali-kali via AJAX (modal Tambah/Ubah)
    if (!window._searchDropdownOutsideClickBound) {
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.search-wrap')) closeAll('');
        });
        window._searchDropdownOutsideClickBound = true;
    }

    window.registerSearchDropdown = function (cfg) {
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
            $dd.html('<div class="sd-loading"><i class="fas fa-spinner fa-spin me-1"></i>Mencari…</div>').addClass('open');

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

    /* ═══════════════════════════════════════════════════════════════════
       DAFTARKAN SEMUA FIELD
    ═══════════════════════════════════════════════════════════════════ */

    registerSearchDropdown({
        inputId    : 'search_pekerjaan',
        hiddenId   : 'pekerjaan_id',
        dropdownId : 'dd_pekerjaan',
        clearId    : 'clear_pekerjaan',
        ajaxUrl    : '{{ route("unit.donor.select2.pekerjaan") }}',
    });

    registerSearchDropdown({
        inputId    : 'search_kewarganegaraan',
        hiddenId   : 'kewarganegaraan_id',
        dropdownId : 'dd_kewarganegaraan',
        clearId    : 'clear_kewarganegaraan',
        ajaxUrl    : '{{ route("unit.donor.select2.kewarganegaraan") }}',
    });

    registerSearchDropdown({
        inputId    : 'search_asal_darah',
        hiddenId   : 'asal_darah_id',
        dropdownId : 'dd_asal_darah',
        clearId    : 'clear_asal_darah',
        ajaxUrl    : '{{ route("unit.donor.select2.asal_darah") }}',
        onSelect   : function (item) {
            $('[name="nama_asal_darah"]').val(item ? item.text : '');
        },
    });

    registerSearchDropdown({
        inputId    : 'search_wilayah',
        hiddenId   : 'wilayah_id',
        dropdownId : 'dd_wilayah',
        clearId    : 'clear_wilayah',
        ajaxUrl    : '{{ route("unit.donor.select2.wilayah") }}',
        onSelect   : function (item) {
            $('#search_kecamatan').val('');
            $('#kecamatan_id').val('');
            $('#dd_kecamatan').removeClass('open').empty();
            $('#clear_kecamatan').removeClass('visible');
        },
    });

    registerSearchDropdown({
        inputId      : 'search_kecamatan',
        hiddenId     : 'kecamatan_id',
        dropdownId   : 'dd_kecamatan',
        clearId      : 'clear_kecamatan',
        ajaxUrl      : '{{ route("unit.donor.select2.kecamatan") }}',
        extraParams  : function () {
            return { wilayah_id: $('#wilayah_id').val() || '' };
        },
    });

    /* ═══════════════════════════════════════════════════════════════════
       VALIDASI FIELD WAJIB UNTUK DROPDOWN CUSTOM SEBELUM SUBMIT
    ═══════════════════════════════════════════════════════════════════ */
    function validateRequiredDropdowns() {
        var requiredDropdowns = [
            { hidden: '#kewarganegaraan_id', wrap: '#wrap_kewarganegaraan', label: 'Kewarganegaraan' },
            { hidden: '#wilayah_id',         wrap: '#wrap_wilayah',         label: 'Wilayah / Kota' },
            { hidden: '#kecamatan_id',       wrap: '#wrap_kecamatan',       label: 'Kecamatan' },
            { hidden: '#pekerjaan_id',       wrap: '#wrap_pekerjaan',       label: 'Pekerjaan' },
        ];

        var firstInvalid = null;
        var messages = [];

        requiredDropdowns.forEach(function (item) {
            var val = $(item.hidden).val();
            var $wrap = $(item.wrap);
            if (!val) {
                $wrap.addClass('is-invalid');
                messages.push(item.label);
                if (!firstInvalid) firstInvalid = $wrap;
            } else {
                $wrap.removeClass('is-invalid');
            }
        });

        if (messages.length) {
            Swal.fire({
                icon: 'warning',
                title: 'Lengkapi Data Wajib',
                html: 'Field berikut wajib dipilih dari daftar pencarian:<br><b>' + messages.join(', ') + '</b>'
            });
            if (firstInvalid) firstInvalid.find('input[type="text"]').trigger('focus');
            return false;
        }

        return true;
    }

    $('#form_info').on('submit', function (e) {
        if (!validateRequiredDropdowns()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        }
    });

    /* ═══════════════════════════════════════════════════════════════════
       SISA SCRIPT (semua di-scope lokal via IIFE ini)
    ═══════════════════════════════════════════════════════════════════ */

    // ── AUTO VALIDASI FPUP ────────────────────────────────────────────────
    window.fpupLoaded = window.fpupLoaded || false;

    $('[name="no_fpup"]').on('change blur', function () {
        if (window.fpupLoaded) return;
        let no_fpup = $(this).val().trim();
        if (no_fpup === '') return;

        $.ajax({
            url  : '{{ route("unit.donor.check_fpup") }}',
            type : 'POST',
            data : { _token: '{{ csrf_token() }}', no_fpup },
            success: function (res) {
                if (!res.success) {
                    Swal.fire({ icon: 'error', title: 'FPUP Tidak Ditemukan', text: res.message });
                    return;
                }

                let fpup = res.data;
                let detailRows = '';
                if (fpup.details && fpup.details.length > 0) {
                    fpup.details.forEach(function (item) {
                        detailRows += `<tr>
                            <td>${item.jns_darah ?? '-'}</td>
                            <td>${item.gol_darah ?? '-'}</td>
                            <td>${item.rhesus ?? '-'}</td>
                            <td>${item.jumlah ?? 0}</td>
                            <td>${item.cc ?? 0}</td>
                        </tr>`;
                    });
                }

                Swal.fire({
                    title: 'Validasi Data FPUP', width: 900,
                    html: `<div class="text-start">
                        <div><b>No FPUP :</b> ${fpup.no_fpup ?? '-'}</div>
                        <div><b>Tanggal FPUP :</b> ${fpup.tgl_minta ?? '-'}</div>
                        <div><b>No Reg :</b> ${fpup.no_reg ?? '-'}</div>
                        <div><b>Kode RS :</b> ${fpup.kode_rs ?? '-'}</div>
                        <div><b>Nama RS :</b> ${fpup.nama_rs ?? '-'}</div>
                        <div><b>Jenis RS :</b> ${fpup.jenis_rs ?? '-'}</div>
                        <div><b>Bagian :</b> ${fpup.bagian ?? '-'}</div>
                        <div><b>Kelas :</b> ${fpup.kelas_rawat ?? '-'}</div>
                        <div><b>Dokter :</b> ${fpup.nama_dokter ?? '-'}</div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6"><b>Nama Pasien :</b><br>${fpup.nama_pasien ?? '-'}</div>
                            <div class="col-md-6"><b>Tanggal Lahir :</b><br>${fpup.tgl_lahir ?? '-'}</div>
                            <div class="col-md-6 mt-2"><b>Umur :</b><br>${fpup.umur ?? '-'} Tahun</div>
                            <div class="col-md-6 mt-2"><b>Alamat :</b><br>${fpup.alamat ?? '-'}</div>
                            <div class="col-md-6 mt-2"><b>Golongan Darah :</b><br>${fpup.gol_darah ?? '-'}</div>
                            <div class="col-md-6 mt-2"><b>Rhesus :</b><br>${fpup.rhesus ?? '-'}</div>
                        </div>
                        <table class="table table-bordered table-sm">
                            <thead><tr><th>Jenis Darah</th><th>Gol</th><th>Rhesus</th><th>Jumlah</th><th>CC</th></tr></thead>
                            <tbody>${detailRows}</tbody>
                        </table>
                        <div class="text-danger fw-bold mt-3">Klik YES jika data FPUP benar</div>
                    </div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'YES',
                    cancelButtonText : 'NO'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.fpupLoaded = true;
                        $('#fpup_id').val(fpup.fpup_id);
                        $('[name="nama"]').val(fpup.nama_pasien ?? '');
                        $('[name="tanggal_lahir"]').val(fpup.tgl_lahir ?? '');
                        $('[name="usia"]').val(fpup.umur ?? '');
                        updateUsiaHighlight();
                        $('[name="alamat_1"]').val(fpup.alamat ?? '');

                        let gol = fpup.gol_darah ?? '';
                        let rh  = fpup.rhesus ?? '';
                        $('#golongan_darah_input').val(gol);
                        $('#rhesus_input').val(rh);
                        $('#gol_darah_buttons .btn-gol-darah').removeClass('btn-danger').addClass('btn-light');
                        $('#gol_darah_buttons .btn-gol-darah[data-value="' + gol + '"]').removeClass('btn-light').addClass('btn-danger');
                        $('#rhesus_buttons .btn-rhesus').removeClass('btn-primary').addClass('btn-light');
                        $('#rhesus_buttons .btn-rhesus[data-value="' + rh + '"]').removeClass('btn-light').addClass('btn-primary');

                        Swal.fire({ icon: 'success', title: 'FPUP berhasil dipilih' });
                    } else {
                        $('[name="no_fpup"]').val('');
                        $('#fpup_id').val('');
                    }
                });
            },
            error: function (xhr) {
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: xhr.responseJSON?.message ?? 'Server error' });
            }
        });
    });

    // ── HIGHLIGHT POSITIF ─────────────────────────────────────────────────────
    function updatePositiveHighlight(selector) {
        let el  = $(selector);
        let val = (el.val() || '').toString().toLowerCase().trim();
        el.removeClass('border-danger bg-danger text-white fw-bold');
        if (['positif', '+', 'reactive', 'reaktif'].includes(val)) {
            el.addClass('border-danger bg-danger text-white fw-bold');
        }
    }
    $(document).on('keyup change', '[name="skrining"]',       function () { updatePositiveHighlight(this); });
    $(document).on('keyup change', '[name="golongan_rhesus"]',function () { updatePositiveHighlight(this); });
    updatePositiveHighlight('[name="skrining"]');
    updatePositiveHighlight('[name="golongan_rhesus"]');

    /* ═══════════════════════════════════════════════════════════════════
       ★ HIGHLIGHT WARNA USIA (const di-scope lokal, TIDAK bocor ke global)
    ═══════════════════════════════════════════════════════════════════ */
    const USIA_MIN = 17;
    const USIA_MAX = 65;

    function updateUsiaHighlight() {
        let el   = $('#usia');
        let usia = parseInt(el.val());

        el.removeClass('border-danger bg-danger text-white fw-bold');

        if (isNaN(usia)) return;

        if (usia < USIA_MIN || usia > USIA_MAX) {
            el.addClass('border-danger bg-danger text-white fw-bold');
        }
    }

    // ── HITUNG USIA ───────────────────────────────────────────────────────────
    function hitungUsia(tgl) {
        if (!tgl) return '';
        let d = new Date(tgl);
        if (isNaN(d)) return '';
        let today = new Date();
        let age   = today.getFullYear() - d.getFullYear();
        let m     = today.getMonth() - d.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < d.getDate())) age--;
        return age;
    }
    $('[name="tanggal_lahir"]').on('change input', function () {
        $('#usia').val(hitungUsia($(this).val()));
        updateUsiaHighlight();
    });
    let tgl = $('[name="tanggal_lahir"]').val();
    if (tgl) $('#usia').val(hitungUsia(tgl));
    updateUsiaHighlight();

    // ── AUTO CHECK PENGHARGAAN ────────────────────────────────────────────────
    function updatePenghargaan() {
        let ke = parseInt($('#donor_ke').val() || 0);
        $('input[name="penghargaan[]"]').prop('checked', false);
        if (ke >= 10)  $('input[name="penghargaan[]"][value="10"]').prop('checked', true);
        if (ke >= 25)  $('input[name="penghargaan[]"][value="25"]').prop('checked', true);
        if (ke >= 50)  $('input[name="penghargaan[]"][value="50"]').prop('checked', true);
        if (ke >= 100) $('input[name="penghargaan[]"][value="100"]').prop('checked', true);
    }
    $(document).on('keyup change input', '#donor_ke', function () { updatePenghargaan(); });
    updatePenghargaan();

    // ── DATEPICKER ────────────────────────────────────────────────────────────
    $('.datepicker').flatpickr({
        locale: "id",
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        allowInput: true
    });

    // ── COPY ALAMAT ───────────────────────────────────────────────────────────
    $('#btn_copy_alamat').on('click', function () {
        $('[name="alamat_2"]').val($('[name="alamat_1"]').val()).trigger('input');
    });

    // ── GOLONGAN DARAH BUTTON TOGGLE ─────────────────────────────────────────
    @if(!($donor->is_golongan_darah_locked ?? false))
    $('#gol_darah_buttons').on('click', '.btn-gol-darah', function () {
        $('#gol_darah_buttons .btn-gol-darah').removeClass('btn-danger').addClass('btn-light');
        $(this).removeClass('btn-light').addClass('btn-danger');
        $('#golongan_darah_input').val($(this).data('value'));
    });
    $('#rhesus_buttons').on('click', '.btn-rhesus', function () {
        $('#rhesus_buttons .btn-rhesus').removeClass('btn-primary').addClass('btn-light');
        $(this).removeClass('btn-light').addClass('btn-primary');
        $('#rhesus_input').val($(this).data('value'));
    });
    @else
    $('#gol_darah_buttons .btn-gol-darah, #rhesus_buttons .btn-rhesus').prop('disabled', true);
    @endif

    // ── AUTO DONOR KE dari NO KTP ─────────────────────────────────────────────
    window._donor_ke_timer = window._donor_ke_timer || null;
    $('[name="no_ktp"]').on('input change', function () {
        let no_ktp = $(this).val().trim();
        if (no_ktp.length < 16) return;
        clearTimeout(window._donor_ke_timer);
        window._donor_ke_timer = setTimeout(function () {
            $.get('{{ route("unit.donor.get_donor_ke") }}', { no_ktp }, function (res) {
                if (res.donor_ke !== undefined) {
                    $('[name="donor_ke"]').val(res.donor_ke).trigger('input');
                    if (res.golongan_darah) {
                        $('#golongan_darah_input').val(res.golongan_darah);
                        $('#gol_darah_buttons .btn-gol-darah').removeClass('btn-danger').addClass('btn-light');
                        $('#gol_darah_buttons .btn-gol-darah[data-value="' + res.golongan_darah + '"]').removeClass('btn-light').addClass('btn-danger');
                    }
                    if (res.rhesus) {
                        $('#rhesus_input').val(res.rhesus);
                        $('#rhesus_buttons .btn-rhesus').removeClass('btn-primary').addClass('btn-light');
                        $('#rhesus_buttons .btn-rhesus[data-value="' + res.rhesus + '"]').removeClass('btn-light').addClass('btn-primary');
                    }
                }
            });
        }, 600);
    });

    // ── FOTO: UPLOAD FILE ─────────────────────────────────────────────────────
    $('#foto_upload').on('change', function () {
        let file = this.files[0];
        if (!file) return;
        let reader = new FileReader();
        reader.onload = function (e) {
            $('#foto_preview').attr('src', e.target.result);
            $('#foto_base64').val('');
        };
        reader.readAsDataURL(file);
    });

    // ── FOTO: HAPUS ───────────────────────────────────────────────────────────
    $('#btn_hapus_foto').on('click', function () {
        $('#foto_preview').attr('src', '{{ asset('assets/media/avatars/blank.jpg') }}');
        $('#foto_upload').val('');
        $('#foto_base64').val('hapus');
    });

    // ── FOTO: KAMERA ──────────────────────────────────────────────────────────
    window._camera_stream = window._camera_stream || null;
    $('#btn_capture').on('click', function () {
        $('#camera_area').removeClass('d-none');
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
            .then(function (stream) {
                window._camera_stream = stream;
                $('#camera_video')[0].srcObject = stream;
            })
            .catch(function (err) {
                alert('Kamera tidak dapat diakses: ' + err.message);
                $('#camera_area').addClass('d-none');
            });
    });
    $('#btn_snap').on('click', function () {
        let video  = $('#camera_video')[0];
        let canvas = $('#camera_canvas')[0];
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        let dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        $('#foto_preview').attr('src', dataUrl);
        $('#foto_base64').val(dataUrl);
        $('#foto_upload').val('');
        stop_camera();
    });
    $('#btn_cancel_camera').on('click', function () { stop_camera(); });
    function stop_camera() {
        if (window._camera_stream) { window._camera_stream.getTracks().forEach(t=>t.stop()); window._camera_stream = null; }
        $('#camera_area').addClass('d-none');
    }

    // ── AUTO GENERATE KODE & NO PENDAFTARAN ──────────────────────────────────
    @if(empty($donor))
    $('#asal_darah_id').val(1);
    $('#search_asal_darah').val('UTD PMI Surabaya');
    $('[name="nama_asal_darah"]').val('UTD PMI Surabaya');

    $.get('{{ route('unit.donor.generate_kode') }}', function (res) {
        if (res.kode) $('#kode_display').val(res.kode);
        if (res.no_pendaftaran) $('#no_pendaftaran_display').val(res.no_pendaftaran);
    });
    @endif

    // ── INIT FORM SUBMIT ──────────────────────────────────────────────────────
    init_form({{ $donor->id ?? '' }});

})();
</script>