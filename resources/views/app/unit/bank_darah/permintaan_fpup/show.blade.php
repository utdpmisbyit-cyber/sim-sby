@extends('layouts.index')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>
:root {
    --red:      #C8102E;
    --red-d:    #a00d24;
    --red-glow: rgba(200,16,46,.12);
    --navy:     #ffffff;
    --navy-2:   #f7faff;
    --teal:     #00a896;
    --amber:    #d97706;
    --sky:      #0284c7;
    --muted:    #64748b;
    --border:   #cbd5e1;
    --card:     #ffffff;
    --card-2:   #eef4ff;
    --text:     #1e293b;
    --text-dim: #64748b;
    --mono: 'JetBrains Mono', monospace;
    --sans: 'Plus Jakarta Sans', sans-serif;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: var(--sans); background: #f1f5f9; color: var(--text); margin: 0; }

/* ── Header ── */
.fp-header {
    background: linear-gradient(135deg, #0d1b2a 0%, #1a2f45 100%);
    border-bottom: 2px solid var(--red);
    padding: 1.1rem 2rem;
    display: flex; align-items: center; gap: 1.25rem;
    box-shadow: 0 4px 24px rgba(0,0,0,.18);
    position: sticky; top: 0; z-index: 100;
}
.fp-header .logo-badge {
    background: var(--red); color: #fff;
    font-family: var(--mono); font-size: .6rem; font-weight: 700;
    letter-spacing: .12em; padding: .3rem .65rem;
    border-radius: 4px; text-transform: uppercase; flex-shrink: 0;
}
.fp-header h1 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #fff; }
.fp-header p  { margin: 0; font-size: .73rem; color: #94a3b8; }
.fp-header .ms-auto { margin-left: auto; display: flex; gap: .5rem; }

/* ── Btn ── */
.btn-fp {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .55rem 1.2rem; border: none; border-radius: 8px;
    font-size: .82rem; font-weight: 600; cursor: pointer; transition: all .2s;
    font-family: var(--sans); text-decoration: none;
}
.btn-fp:active { transform: scale(.97); }
.btn-primary { background: var(--red); color: #fff; }
.btn-primary:hover { background: var(--red-d); color: #fff; }
.btn-ghost   { background: #fff; color: var(--text); border: 1px solid var(--border); }
.btn-ghost:hover { background: #f1f5f9; }
.btn-amber   { background: rgba(217,119,6,.12); color: var(--amber); border: 1px solid rgba(217,119,6,.3); }
.btn-amber:hover { background: rgba(217,119,6,.22); }
.btn-danger  { background: rgba(200,16,46,.1); color: #dc2626; border: 1px solid rgba(200,16,46,.25); }
.btn-danger:hover { background: rgba(200,16,46,.2); }
.btn-sm { padding: .35rem .85rem; font-size: .76rem; }

/* ── Section Card ── */
.sec-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 14px;
    margin-bottom: 1.25rem;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.sec-head {
    background: var(--card-2);
    border-bottom: 1px solid var(--border);
    padding: .8rem 1.25rem;
    display: flex; align-items: center; gap: .65rem;
    font-size: .78rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    color: var(--text);
}
.sec-head .ico.teal  { color: var(--teal); }
.sec-head .ico.amber { color: var(--amber); }
.sec-head .ico.sky   { color: var(--sky); }
.sec-head .ico.red   { color: var(--red); }
.sec-body { padding: 1.25rem; }

/* ── Info Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.info-grid.col-3 { grid-template-columns: repeat(3, 1fr); }
.info-grid.col-2 { grid-template-columns: repeat(2, 1fr); }
@media(max-width:900px) { .info-grid,.info-grid.col-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:600px) { .info-grid,.info-grid.col-3,.info-grid.col-2 { grid-template-columns: 1fr; } }

.info-item {}
.info-label {
    font-size: .65rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted); margin-bottom: .3rem;
}
.info-val {
    font-size: .88rem; color: var(--text); font-weight: 500;
    word-break: break-word;
}
.info-val.mono { font-family: var(--mono); }
.info-val.empty { color: var(--muted); font-style: italic; font-weight: 400; }

/* ── Badge ── */
.badge {
    display: inline-flex; align-items: center; gap: .3rem;
    border-radius: 5px; padding: .22rem .6rem;
    font-size: .7rem; font-weight: 700; letter-spacing: .04em; white-space: nowrap;
}
.badge-baru    { background: rgba(2,132,199,.12);  color: #0369a1; border: 1px solid rgba(2,132,199,.2); }
.badge-proses  { background: rgba(217,119,6,.12);  color: #b45309; border: 1px solid rgba(217,119,6,.2); }
.badge-selesai { background: rgba(0,168,150,.12);  color: #00766a; border: 1px solid rgba(0,168,150,.2); }
.badge-batal   { background: rgba(200,16,46,.1);   color: #dc2626; border: 1px solid rgba(200,16,46,.2); }
.badge-cito    { background: var(--red); color: #fff; }
.badge-ya      { background: rgba(0,168,150,.12); color: #00766a; border: 1px solid rgba(0,168,150,.2); }
.badge-tidak   { background: #f1f5f9; color: var(--muted); border: 1px solid var(--border); }

/* ── Detail Darah Table ── */
.detail-tbl-wrap { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.detail-tbl { width: 100%; border-collapse: collapse; }
.detail-tbl thead tr { background: var(--card-2); }
.detail-tbl thead th {
    padding: .65rem 1rem;
    font-size: .64rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted); text-align: left;
    border-bottom: 1px solid var(--border);
}
.detail-tbl tbody tr { border-bottom: 1px solid #e2e8f0; transition: background .15s; }
.detail-tbl tbody tr:hover { background: #f8faff; }
.detail-tbl tbody td { padding: .7rem 1rem; font-size: .82rem; color: var(--text); vertical-align: middle; }
.detail-tbl tbody tr:last-child { border-bottom: none; }

/* ── No FPUP Bar ── */
.fpup-bar {
    background: linear-gradient(90deg, var(--card-2), #fff);
    border: 1px solid var(--border);
    border-left: 4px solid var(--teal);
    border-radius: 10px;
    padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: 2rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.fpup-bar .no-label { font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
.fpup-bar .no-val   { font-family: var(--mono); font-size: 1.4rem; font-weight: 700; color: var(--teal); }
.fpup-bar .meta-item { border-left: 1px solid var(--border); padding-left: 1.5rem; }

/* ── Status selector ── */
.status-select {
    background: var(--navy-2); border: 1.5px solid var(--border);
    border-radius: 8px; padding: .45rem .85rem;
    font-size: .82rem; color: var(--text);
    font-family: var(--sans); font-weight: 600; outline: none;
    cursor: pointer; transition: border-color .2s;
}
.status-select:focus { border-color: var(--teal); }

/* ── Divider ── */
.divider { border: none; border-top: 1px solid var(--border); margin: 1rem 0; }

/* ── Conditional info box ── */
.cond-box {
    background: rgba(0,168,150,.05);
    border: 1px solid rgba(0,168,150,.2);
    border-radius: 8px;
    padding: .75rem 1rem;
    margin-top: .5rem;
}
.cond-box.danger {
    background: rgba(200,16,46,.04);
    border-color: rgba(200,16,46,.15);
}
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="fp-header">
    <span class="logo-badge">PMI</span>
    <div>
        <h1>Detail Permintaan Darah (FPUP)</h1>
        <p>Unit › Bank Darah › Permintaan FPUP › {{ $fpup->no_fpup }}</p>
    </div>
    <div class="ms-auto">
        <a href="{{ route('unit.bank_darah.permintaan_fpup.edit', $fpup) }}" class="btn-fp btn-amber">
            <i class="fas fa-edit"></i> Edit
        </a>
        <a href="{{ route('unit.bank_darah.permintaan_fpup.index') }}" class="btn-fp btn-ghost">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="container-fluid px-4 py-4">

    @if(session('success'))
    <div style="background:rgba(0,168,150,.08);border:1px solid rgba(0,168,150,.25);color:#00766a;padding:.75rem 1.25rem;border-radius:10px;margin-bottom:1rem;font-size:.85rem;display:flex;align-items:center;gap:.5rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- No FPUP Bar --}}
    <div class="fpup-bar">
        <div>
            <div class="no-label">No FPUP</div>
            <div class="no-val">{{ $fpup->no_fpup }}</div>
        </div>
        <div class="meta-item">
            <div class="no-label">Tgl & Jam Minta</div>
            <div style="font-family:var(--mono);font-size:.9rem;font-weight:600;">
                {{ $fpup->tgl_minta?->format('d/m/Y') }}
                <span style="color:var(--muted);">{{ $fpup->jam_minta ? substr($fpup->jam_minta,0,5) : '' }}</span>
            </div>
        </div>
        <div class="meta-item">
            <div class="no-label">Status</div>
            <div style="margin-top:.25rem;">
                <span class="badge badge-{{ $fpup->status }}">{{ ucfirst($fpup->status) }}</span>
            </div>
        </div>
        <div class="meta-item" style="margin-left:auto;">
            <div class="no-label" style="margin-bottom:.35rem;">Ubah Status</div>
            <select class="status-select" id="status-select" data-id="{{ $fpup->id }}">
                @foreach($status_list as $s)
                    <option value="{{ $s }}" @selected($fpup->status === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <form method="POST"
                  action="{{ route('unit.bank_darah.permintaan_fpup.destroy', $fpup) }}"
                  onsubmit="return confirm('Hapus FPUP {{ $fpup->no_fpup }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-fp btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- ① Data Rumah Sakit --}}
    <div class="sec-card">
        <div class="sec-head">
            <i class="fas fa-hospital ico sky"></i> Data Rumah Sakit / Instansi
        </div>
        <div class="sec-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Kode RS</div>
                    <div class="info-val mono">{{ $fpup->kode_rs ?: '—' }}</div>
                </div>
                <div class="info-item" style="grid-column:span 2;">
                    <div class="info-label">Nama RS / Instansi</div>
                    <div class="info-val">{{ $fpup->nama_rs ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">No Reg</div>
                    <div class="info-val mono">{{ $fpup->no_reg ?: '—' }}</div>
                </div>
            </div>
            <hr class="divider">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Jenis RS</div>
                    <div class="info-val">{{ $fpup->jenis_rs ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kategori RS</div>
                    <div class="info-val">{{ $fpup->kategori_rs ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Bagian / Ruangan</div>
                    <div class="info-val">{{ $fpup->bagian ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kelas Rawat</div>
                    <div class="info-val">{{ $fpup->kelas_rawat ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Dokter</div>
                    <div class="info-val">{{ $fpup->nama_dokter ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama O.S</div>
                    <div class="info-val">{{ $fpup->nama_os ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ② Data Pasien --}}
    <div class="sec-card">
        <div class="sec-head">
            <i class="fas fa-user ico amber"></i> Data Pasien
        </div>
        <div class="sec-body">
            <div class="info-grid">
                <div class="info-item" style="grid-column:span 2;">
                    <div class="info-label">Nama Pasien</div>
                    <div class="info-val" style="font-size:1rem;font-weight:700;">{{ $fpup->nama_pasien }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nama Suami/Istri</div>
                    <div class="info-val">{{ $fpup->nama_suami_istri ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kebangsaan</div>
                    <div class="info-val">{{ $fpup->kebangsaan ?: '—' }}</div>
                </div>
            </div>
            <div class="info-grid" style="margin-top:1rem;">
                <div class="info-item">
                    <div class="info-label">Tgl Lahir</div>
                    <div class="info-val mono">{{ $fpup->tgl_lahir?->format('d/m/Y') ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Umur</div>
                    <div class="info-val">{{ $fpup->umur ? $fpup->umur.' tahun' : '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Kelamin</div>
                    <div class="info-val">{{ $fpup->jenis_kelamin ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Alamat</div>
                    <div class="info-val">{{ $fpup->alamat ?: '—' }}</div>
                </div>
            </div>

            @if($fpup->jenis_kelamin === 'Wanita')
            <hr class="divider">
            <div style="font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--amber);margin-bottom:.75rem;">
                <i class="fas fa-venus"></i> Khusus Wanita
            </div>
            <div class="info-grid col-3">
                <div class="info-item">
                    <div class="info-label">Jml Kehamilan</div>
                    <div class="info-val">{{ $fpup->jumlah_kehamilan ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Abortus</div>
                    <div class="info-val">{{ $fpup->abortus ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">HDN</div>
                    <div class="info-val">
                        <span class="badge {{ $fpup->hdn ? 'badge-ya' : 'badge-tidak' }}">
                            {{ $fpup->hdn ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ③ Data Permintaan --}}
    <div class="sec-card">
        <div class="sec-head">
            <i class="fas fa-clipboard-list ico red"></i> Data Permintaan Transfusi
        </div>
        <div class="sec-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Jenis Permintaan</div>
                    <div class="info-val">
                        @if($fpup->jns_permintaan === 'CITO')
                            <span class="badge badge-cito"><i class="fas fa-bolt"></i> CITO</span>
                        @else
                            {{ $fpup->jns_permintaan ?: '—' }}
                        @endif
                    </div>
                </div>
                <div class="info-item" style="grid-column:span 2;">
                    <div class="info-label">Diagnosa Klinis</div>
                    <div class="info-val">{{ $fpup->diagnosa_klinis ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Hb</div>
                    <div class="info-val mono">{{ $fpup->hb ? $fpup->hb.' g/dL' : '—' }}</div>
                </div>
            </div>
            <div style="margin-top:.75rem;">
                <div class="info-label">Alasan Transfusi</div>
                <div class="info-val" style="margin-top:.3rem;line-height:1.6;">
                    {{ $fpup->alasan_transfusi ?: '—' }}
                </div>
            </div>

            <hr class="divider">

            {{-- Transfusi Sebelumnya --}}
            <div class="info-grid col-3" style="margin-bottom:.5rem;">
                <div class="info-item">
                    <div class="info-label">Transfusi Sebelumnya</div>
                    <div class="info-val">
                        <span class="badge {{ $fpup->transfusi_sebelumnya ? 'badge-ya' : 'badge-tidak' }}">
                            {{ $fpup->transfusi_sebelumnya ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                </div>
                @if($fpup->transfusi_sebelumnya)
                <div class="info-item">
                    <div class="info-label">Kapan Terakhir</div>
                    <div class="info-val mono">{{ $fpup->transfusi_kapan?->format('d/m/Y') ?: '—' }}</div>
                </div>
                @endif
            </div>

            {{-- Reaksi Transfusi --}}
            <div class="info-grid col-3" style="margin-bottom:.5rem;">
                <div class="info-item">
                    <div class="info-label">Reaksi Transfusi</div>
                    <div class="info-val">
                        <span class="badge {{ $fpup->reaksi_transfusi ? 'badge-ya' : 'badge-tidak' }}">
                            {{ $fpup->reaksi_transfusi ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                </div>
                @if($fpup->reaksi_transfusi)
                <div class="info-item" style="grid-column:span 2;">
                    <div class="info-label">Gejala Reaksi</div>
                    <div class="info-val" style="line-height:1.6;">{{ $fpup->reaksi_gejala ?: '—' }}</div>
                </div>
                @endif
            </div>

            {{-- Pernah Serologi --}}
            <div class="info-item" style="margin-bottom:.5rem;">
                <div class="info-label">Pernah Serologi Gol Darah</div>
                <div class="info-val" style="margin-top:.25rem;">
                    <span class="badge {{ $fpup->pernah_serologi ? 'badge-ya' : 'badge-tidak' }}">
                        {{ $fpup->pernah_serologi ? 'Ya' : 'Tidak' }}
                    </span>
                </div>
            </div>
            @if($fpup->pernah_serologi)
            <div class="cond-box">
                <div class="info-grid col-3" style="margin-bottom:0;">
                    <div class="info-item">
                        <div class="info-label">Kapan</div>
                        <div class="info-val mono">{{ $fpup->serologi_kapan?->format('d/m/Y') ?: '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dimana</div>
                        <div class="info-val">{{ $fpup->serologi_dimana ?: '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Hasil</div>
                        <div class="info-val mono" style="font-weight:700;color:var(--teal);">{{ $fpup->serologi_hasil ?: '—' }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ④ Detail Darah --}}
    <div class="sec-card">
        <div class="sec-head">
            <i class="fas fa-tint ico red"></i> Detail Permintaan Darah
            <span style="margin-left:auto;font-size:.72rem;font-weight:500;color:var(--muted);text-transform:none;letter-spacing:0;">
                {{ $fpup->details->count() }} item
            </span>
        </div>
        <div class="sec-body" style="padding:0;">
            @if($fpup->details->count() > 0)
            <div class="detail-tbl-wrap" style="border-radius:0;border:none;">
                <table class="detail-tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jenis Darah</th>
                            <th>Gol Darah</th>
                            <th>Rhesus</th>
                            <th>Jumlah</th>
                            <th>CC</th>
                            <th>Tgl Perlu</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fpup->details as $i => $d)
                        <tr>
                            <td style="color:var(--muted);font-family:var(--mono);font-size:.75rem;">{{ $i+1 }}</td>
                            <td>
                                <span style="font-family:var(--mono);font-weight:700;color:var(--red);">{{ $d->jns_darah }}</span>
                            </td>
                            <td style="font-family:var(--mono);font-weight:600;">
                                {{ $d->gol_darah ?: '—' }}
                            </td>
                            <td>
                                @if($d->rhesus === 'Positif')
                                    <span style="color:#00766a;font-weight:600;">+ Positif</span>
                                @elseif($d->rhesus === 'Negatif')
                                    <span style="color:#dc2626;font-weight:600;">− Negatif</span>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                            <td style="font-family:var(--mono);font-weight:700;">{{ $d->jumlah ?? '—' }}</td>
                            <td style="font-family:var(--mono);">{{ $d->cc ? $d->cc.' cc' : '—' }}</td>
                            <td style="font-family:var(--mono);">{{ $d->tgl_perlu?->format('d/m/Y') ?: '—' }}</td>
                            <td style="color:var(--muted);">{{ $d->keterangan ?: '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align:center;padding:2rem;color:var(--muted);font-size:.84rem;">
                <i class="fas fa-tint" style="font-size:1.5rem;opacity:.2;display:block;margin-bottom:.5rem;"></i>
                Tidak ada detail darah.
            </div>
            @endif
        </div>
    </div>

    {{-- ⑤ Pembayaran & Donor --}}
    <div class="sec-card">
        <div class="sec-head">
            <i class="fas fa-wallet ico teal"></i> Cara Pembayaran & Donor
        </div>
        <div class="sec-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Cara Pembayaran</div>
                    <div class="info-val">{{ $fpup->cara_pembayaran ?: '—' }}</div>
                </div>
                <div class="info-item" style="grid-column:span 2;">
                    <div class="info-label">Jenis Biaya</div>
                    <div class="info-val">{{ $fpup->jns_biaya ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jml Donor</div>
                    <div class="info-val mono">{{ $fpup->jml_donor ?? '0' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Jenis Donor</div>
                    <div class="info-val">{{ $fpup->jns_donor ?: '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cetak Barcode</div>
                    <div class="info-val">
                        <span class="badge {{ $fpup->cetak_barcode ? 'badge-ya' : 'badge-tidak' }}">
                            {{ $fpup->cetak_barcode ? 'Ya' : 'Tidak' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sel    = document.getElementById('status-select');
    const UPDATE = '{{ route("unit.bank_darah.permintaan_fpup.update-status", $fpup) }}';
    const TOKEN  = '{{ csrf_token() }}';

    sel.addEventListener('change', async function () {
        const status = this.value;
        try {
            const res = await fetch(UPDATE, {
                method : 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                body   : JSON.stringify({ status }),
            });
            const data = await res.json();
            if (data.success) {
                // Update badge di bar
                const badge = document.querySelector('.fpup-bar .badge');
                if (badge) {
                    badge.className = `badge badge-${status}`;
                    badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                }
                showToast('Status diubah ke ' + status, false);
            }
        } catch { showToast('Gagal mengubah status', true); }
    });

    function showToast(msg, err) {
        let t = document.getElementById('fp-toast');
        if (!t) {
            t = document.createElement('div');
            t.id = 'fp-toast';
            t.style.cssText = 'position:fixed;top:1.5rem;right:1.5rem;background:#fff;color:#1e293b;padding:.75rem 1.25rem;border-radius:10px;font-size:.84rem;box-shadow:0 8px 32px rgba(0,0,0,.15);z-index:9999;opacity:0;transform:translateY(-12px);transition:all .3s;border-left:4px solid #00a896;font-family:var(--sans);';
            document.body.appendChild(t);
        }
        t.style.borderLeftColor = err ? '#C8102E' : '#00a896';
        t.textContent = msg;
        t.style.opacity = '1'; t.style.transform = 'translateY(0)';
        setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateY(-12px)'; }, 3000);
    }
});
</script>
@endpush