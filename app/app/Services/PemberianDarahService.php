<?php

namespace App\Services;

use App\Models\PemberianDarah;
use App\Models\PemberianDarahDetail;
use App\Models\PermintaanFpup;
use App\Models\StokDarah;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PemberianDarahService
{
    // ─── Generate Nomor Pemberian ────────────────────────────────────────────────

    public function generateNoPemberian(): string
    {
        $prefix = 'B' . now()->format('Ymd');
        $last = PemberianDarah::withTrashed()
            ->where('no_pemberian', 'like', $prefix . '%')
            ->orderByDesc('no_pemberian')
            ->value('no_pemberian');

        $seq = $last ? ((int) substr($last, -6)) + 1 : 1;
        return $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    public function getDataByNoFpup(string $noFpup): ?array
    {
        // Nama relasi di model PermintaanFpup adalah details()
        $fpup = PermintaanFpup::with('details')->where('no_fpup', $noFpup)->first();

        if (! $fpup) {
            return null;
        }

        // Prioritas 1: gol_rh_os di header (sudah gabungan, contoh: "O+", "A-")
        // Prioritas 2: gabungkan gol_darah + rhesus dari baris pertama detail
        // Kolom di permintaan_fpup_detail: gol_darah (A/B/AB/O) + rhesus (Positif/Negatif)
        $golRh = null;
        if (! empty($fpup->gol_rh_os)) {
            $golRh = $fpup->gol_rh_os;
        } else {
            $firstDetail = $fpup->details->first(fn ($d) => ! empty($d->gol_darah));
            if ($firstDetail) {
                // Normalisasi rhesus: "Positif" -> "+" | "Negatif" -> "-"
                $rh = match (strtolower($firstDetail->rhesus ?? '')) {
                    'positif', 'positive', '+' => '+',
                    'negatif', 'negative', '-' => '-',
                    default                     => $firstDetail->rhesus ?? '',
                };
                $golRh = trim(($firstDetail->gol_darah ?? '') . $rh);
            }
        }

        // Format tanggal ke string Y-m-d agar JSON aman & input[type=date] bisa di-set
        $tglRegOnline = $fpup->tgl_registrasi_online
            ? Carbon::parse($fpup->tgl_registrasi_online)->format('Y-m-d')
            : null;

        return [
           'permintaan_fpup_id'    => $fpup->id,
            'no_fpup'               => $fpup->no_fpup,
            'nama_pasien'           => $fpup->nama_pasien,
            'nama_dokter'           => $fpup->nama_dokter,
            'nama_rs'               => $fpup->nama_rs,
            'kode_rs'               => $fpup->kode_rs,
            'jenis_rs'              => $fpup->jenis_rs,
            'kelas_rawat'           => $fpup->kelas_rawat,
            'gol_rh_pasien'         => $golRh,
            'cara_pembayaran'       => $fpup->cara_pembayaran,
            'jns_biaya'             => $fpup->jns_biaya,
            'no_reg_online'         => $fpup->no_reg_online,
            'tgl_registrasi_online' => $tglRegOnline,
            'pasien_referal'        => (bool) $fpup->pasien_referal,
            'alamat_penerima'       => $fpup->alamat_penerima ?? null,
        ];
    }

    // ─── Get Data Stok by no_stok (scan barcode) ────────────────────────────────

    public function getDataByNoStok(string $noStok): ?array
    {
        $stok = StokDarah::where('no_stok', $noStok)
            ->where('status_stok', 'tersedia')
            ->first();

        if (! $stok) {
            return null;
        }

        return [
            'stok_darah_id' => $stok->id,
            'no_stok'       => $stok->no_stok,
            'jns_darah'     => $stok->jenis_darah,
            'gol'           => $stok->golongan_darah,
            'rhesus'        => $stok->rhesus,
            // format d-m-Y agar konsisten dengan tampilan, JS akan konversi ke Y-m-d untuk input
            'tgl_expired'   => $stok->tgl_expired ? Carbon::parse($stok->tgl_expired)->format('Y-m-d') : null,
            'cc'            => $stok->ml,
            'saldo'         => $stok->saldo,
            'status_stok'   => $stok->status_stok,
        ];
    }

    // ─── Store ───────────────────────────────────────────────────────────────────

    public function store(array $data): PemberianDarah
    {
        return DB::transaction(function () use ($data) {
            $header = PemberianDarah::create([
                'no_pemberian'          => $this->generateNoPemberian(),
                'no_fpup'               => $data['no_fpup'] ?? null,
                'permintaan_fpup_id'    => $data['permintaan_fpup_id'] ?? null,
                'tgl_keluar'            => $data['tgl_keluar'],
                'jam_keluar'            => $data['jam_keluar'] ?? now()->format('H:i:s'),
                'nama_penerima'         => $data['nama_penerima'] ?? null,
                'alamat_penerima'       => $data['alamat_penerima'] ?? '',
                'nama_pasien'           => $data['nama_pasien'] ?? null,
                'nama_dokter'           => $data['nama_dokter'] ?? null,
                'nama_rs'               => $data['nama_rs'] ?? null,
                'kode_rs'               => $data['kode_rs'] ?? '',
                'jenis_rs'              => $data['jenis_rs'] ?? '',
                'kelas_rawat'           => $data['kelas_rawat'] ?? '',
                'gol_rh_pasien'         => $data['gol_rh_pasien'] ?? '',
                'cara_pembayaran'       => $data['cara_pembayaran'] ?? null,
                'jns_biaya'             => $data['jns_biaya'] ?? null,
                'no_reg_online'         => $data['no_reg_online'] ?? '',
                'tgl_registrasi_online' => $data['tgl_registrasi_online'] ?? now()->toDateString(),
                'petugas'               => $data['petugas'] ?? Auth::user()?->name,
                'kurir_rs'              => $data['kurir_rs'] ?? null,
                'pasien_referal'        => $data['pasien_referal'] ?? false,
                'keterangan'            => $data['keterangan'] ?? null,
                'status'                => 'baru',
            ]);

            foreach ($data['detail'] ?? [] as $row) {
                $detail = PemberianDarahDetail::create([
                    'pemberian_darah_id' => $header->id,
                    'stok_darah_id'      => $row['stok_darah_id'] ?? null,
                    'no_stok'            => $row['no_stok'] ?? null,
                    'jns_darah'          => $row['jns_darah'] ?? null,
                    'gol'                => $row['gol'] ?? null,
                    'rhesus'             => $row['rhesus'] ?? null,
                    'tgl_expired'        => $row['tgl_expired'] ?? null,
                    'metode'             => $row['metode'] ?? null,
                    'hasil'              => $row['hasil'] ?? null,
                    'keterangan'         => $row['keterangan'] ?? null,
                    'jumlah'             => $row['jumlah'] ?? 1,
                    'cc'                 => $row['cc'] ?? null,
                    'harga_satuan'       => $row['harga_satuan'] ?? 0,
                    'total_harga'        => ($row['harga_satuan'] ?? 0) * ($row['jumlah'] ?? 1),
                ]);

                if ($detail->stok_darah_id) {
                    StokDarah::where('id', $detail->stok_darah_id)->increment('jumlah_keluar', $detail->jumlah);
                    StokDarah::where('id', $detail->stok_darah_id)->decrement('saldo', $detail->jumlah);
                    $stok = StokDarah::find($detail->stok_darah_id);
                    if ($stok && $stok->saldo <= 0) {
                        $stok->update(['status_stok' => 'dipakai']);
                    }
                }
            }

            return $header->load('detail');
        });
    }

    // ─── Update ──────────────────────────────────────────────────────────────────

    public function update(PemberianDarah $pemberian, array $data): PemberianDarah
    {
        return DB::transaction(function () use ($pemberian, $data) {
            foreach ($pemberian->detail as $old) {
                if ($old->stok_darah_id) {
                    StokDarah::where('id', $old->stok_darah_id)->decrement('jumlah_keluar', $old->jumlah);
                    StokDarah::where('id', $old->stok_darah_id)->increment('saldo', $old->jumlah);
                    StokDarah::where('id', $old->stok_darah_id)->update(['status_stok' => 'tersedia']);
                }
            }

            $pemberian->detail()->delete();
            $pemberian->update([
                'no_fpup'               => $data['no_fpup'] ?? $pemberian->no_fpup,
                'permintaan_fpup_id'    => $data['permintaan_fpup_id'] ?? $pemberian->permintaan_fpup_id,
                'tgl_keluar'            => $data['tgl_keluar'],
                'jam_keluar'            => $data['jam_keluar'] ?? $pemberian->jam_keluar,
                'nama_penerima'         => $data['nama_penerima'] ?? null,
                'alamat_penerima'       => $data['alamat_penerima'] ?? null,
                'nama_pasien'           => $data['nama_pasien'] ?? null,
                'nama_dokter'           => $data['nama_dokter'] ?? null,
                'nama_rs'               => $data['nama_rs'] ?? null,
                'kode_rs'               => $data['kode_rs'] ?? null,
                'jenis_rs'              => $data['jenis_rs'] ?? null,
                'kelas_rawat'           => $data['kelas_rawat'] ?? null,
                'gol_rh_pasien'         => $data['gol_rh_pasien'] ?? null,
                'cara_pembayaran'       => $data['cara_pembayaran'] ?? null,
                'jns_biaya'             => $data['jns_biaya'] ?? null,
                'no_reg_online'         => $data['no_reg_online'] ?? null,
                'tgl_registrasi_online' => $data['tgl_registrasi_online'] ?? null,
                'petugas'               => $data['petugas'] ?? $pemberian->petugas,
                'kurir_rs'              => $data['kurir_rs'] ?? null,
                'pasien_referal'        => $data['pasien_referal'] ?? false,
                'keterangan'            => $data['keterangan'] ?? null,
            ]);

            foreach ($data['detail'] ?? [] as $row) {
                $detail = PemberianDarahDetail::create([
                    'pemberian_darah_id' => $pemberian->id,
                    'stok_darah_id'      => $row['stok_darah_id'] ?? null,
                    'no_stok'            => $row['no_stok'] ?? null,
                    'jns_darah'          => $row['jns_darah'] ?? null,
                    'gol'                => $row['gol'] ?? null,
                    'rhesus'             => $row['rhesus'] ?? null,
                    'tgl_expired'        => $row['tgl_expired'] ?? null,
                    'metode'             => $row['metode'] ?? null,
                    'hasil'              => $row['hasil'] ?? null,
                    'keterangan'         => $row['keterangan'] ?? null,
                    'jumlah'             => $row['jumlah'] ?? 1,
                    'cc'                 => $row['cc'] ?? null,
                    'harga_satuan'       => $row['harga_satuan'] ?? 0,
                    'total_harga'        => ($row['harga_satuan'] ?? 0) * ($row['jumlah'] ?? 1),
                ]);

                if ($detail->stok_darah_id) {
                    StokDarah::where('id', $detail->stok_darah_id)->increment('jumlah_keluar', $detail->jumlah);
                    StokDarah::where('id', $detail->stok_darah_id)->decrement('saldo', $detail->jumlah);
                    $stok = StokDarah::find($detail->stok_darah_id);
                    if ($stok && $stok->saldo <= 0) {
                        $stok->update(['status_stok' => 'dipakai']);
                    }
                }
            }

            return $pemberian->fresh('detail');
        });
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────────

    public function destroy(PemberianDarah $pemberian): void
    {
        DB::transaction(function () use ($pemberian) {
            foreach ($pemberian->detail as $d) {
                if ($d->stok_darah_id) {
                    StokDarah::where('id', $d->stok_darah_id)->decrement('jumlah_keluar', $d->jumlah);
                    StokDarah::where('id', $d->stok_darah_id)->increment('saldo', $d->jumlah);
                    StokDarah::where('id', $d->stok_darah_id)->update(['status_stok' => 'tersedia']);
                }
            }
            $pemberian->detail()->delete();
            $pemberian->delete();
        });
    }

    
    public function exportDropping(PemberianDarah $pemberian): PemberianDarah
    {
        $pemberian->update([
            'export_dropping'     => true,
            'tgl_export_dropping' => now(),
            'status'              => 'selesai',
        ]);
        return $pemberian;
    }
}