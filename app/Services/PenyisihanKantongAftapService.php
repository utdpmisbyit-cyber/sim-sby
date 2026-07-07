<?php

namespace App\Services;

use App\Models\PenyisihanKantongAftap;
use App\Models\PenyisihanKantongAftapDetail;
use App\Models\StokKantongPenerimaanDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenyisihanKantongAftapService
{
    /**
     * Daftar alasan penyisihan baku (dipakai untuk dropdown "Alasan" di form).
     */
    public const DAFTAR_ALASAN = [
        'Darah Tidak Terserap',
        'Uji Mutu',
        'Keruh',
        'DCT Positif / Mayor Positif',
        'Expired Date',
        'Bocor',
        'HBc Positif',
        'Rusak',
        'Lain-Lain'

    ];

    /**
     * Cari kantong berdasarkan no_kantong, hanya yang statusnya masih 'tersedia'
     * (belum disisihkan / belum dikirim sample / serologi).
     *
     * Data yang dikembalikan sudah termasuk jenis, gol_darah, dan rhesus
     * untuk langsung ditampilkan di form.
     */
    public function scanKantong(string $noKantong): StokKantongPenerimaanDetail
    {
        $kantong = StokKantongPenerimaanDetail::with('penerimaan')
            ->where('no_kantong', $noKantong)
            ->tersedia()
            ->orderByDesc('id')
            ->first();

        if (! $kantong) {
            throw ValidationException::withMessages([
                'no_kantong' => "Kantong {$noKantong} tidak ditemukan atau statusnya sudah tidak tersedia.",
            ]);
        }

        return $kantong;
    }

    /**
     * Simpan transaksi penyisihan beserta detail kantongnya (masing-masing
     * kantong punya alasan sendiri), sekaligus mengupdate status_kirim di
     * stok_kantong_penerimaan_detail menjadi 'disisihkan'.
     *
     * @param  array  $data  [
     *     'tanggal'    => 'YYYY-MM-DD',
     *     'keterangan' => string|null,
     *     'kantong'    => [
     *         ['id' => 1, 'alasan' => 'Bocor'],
     *         ['id' => 2, 'alasan' => 'Uji Mutu'],
     *         ...
     *     ],
     * ]
     */
    public function store(array $data): PenyisihanKantongAftap
    {
        return DB::transaction(function () use ($data) {
            $tanggal = $data['tanggal'];
            $kantongInput = collect($data['kantong'] ?? [])->keyBy('id');

            if ($kantongInput->isEmpty()) {
                throw ValidationException::withMessages([
                    'kantong' => 'Tambahkan minimal satu kantong sebelum menyimpan.',
                ]);
            }

            $penyisihan = PenyisihanKantongAftap::create([
                'no_transaksi' => PenyisihanKantongAftap::generateNoTransaksi(
                    $tanggal instanceof \DateTimeInterface ? $tanggal : new \DateTime($tanggal)
                ),
                'tanggal'    => $tanggal,
                'keterangan' => $data['keterangan'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $kantongList = StokKantongPenerimaanDetail::whereIn('id', $kantongInput->keys())
                ->tersedia()
                ->get();

            if ($kantongList->isEmpty()) {
                throw ValidationException::withMessages([
                    'kantong' => 'Tidak ada kantong valid (tersedia) yang dipilih untuk disisihkan.',
                ]);
            }

            foreach ($kantongList as $kantong) {
                $alasan = $kantongInput->get($kantong->id)['alasan'] ?? null;

                $detail = PenyisihanKantongAftapDetail::create([
                    'penyisihan_id'        => $penyisihan->id,
                    'penerimaan_detail_id' => $kantong->id,
                    'no_kantong'           => $kantong->no_kantong,
                    'no_lot'               => $kantong->no_lot,
                    'merk'                 => $kantong->merk,
                    'jenis'                => $kantong->jenis,
                    'ukuran'               => $kantong->ukuran,
                    'gol_darah'            => $kantong->gol_darah,
                    'rhesus'               => $kantong->rhesus,
                    'status'               => $kantong->status_kirim,
                    'alasan'               => $alasan,
                ]);

                $kantong->update([
                    'status_kirim'         => StokKantongPenerimaanDetail::STATUS_DISISIHKAN,
                    'info_kirim'           => $alasan ?? 'Disisihkan',
                    'penyisihan_detail_id' => $detail->id,
                ]);
            }

            return $penyisihan->load('details');
        });
    }

    /**
     * Tambahkan satu kantong ke transaksi penyisihan yang SUDAH tersimpan
     * (dipakai di mode edit, saat scan kantong baru langsung disimpan live).
     */
    public function addDetail(PenyisihanKantongAftap $penyisihan, int $kantongId, string $alasan): PenyisihanKantongAftapDetail
    {
        return DB::transaction(function () use ($penyisihan, $kantongId, $alasan) {
            $kantong = StokKantongPenerimaanDetail::where('id', $kantongId)
                ->tersedia()
                ->first();

            if (! $kantong) {
                throw ValidationException::withMessages([
                    'kantong' => 'Kantong tidak ditemukan atau statusnya sudah tidak tersedia.',
                ]);
            }

            $detail = PenyisihanKantongAftapDetail::create([
                'penyisihan_id'        => $penyisihan->id,
                'penerimaan_detail_id' => $kantong->id,
                'no_kantong'           => $kantong->no_kantong,
                'no_lot'               => $kantong->no_lot,
                'merk'                 => $kantong->merk,
                'jenis'                => $kantong->jenis,
                'ukuran'               => $kantong->ukuran,
                'gol_darah'            => $kantong->gol_darah,
                'rhesus'               => $kantong->rhesus,
                'status'               => $kantong->status_kirim,
                'alasan'               => $alasan,
            ]);

            $kantong->update([
                'status_kirim'         => StokKantongPenerimaanDetail::STATUS_DISISIHKAN,
                'info_kirim'           => $alasan,
                'penyisihan_detail_id' => $detail->id,
            ]);

            return $detail;
        });
    }

    /**
     * Update data header (tanggal, keterangan) - tidak mengubah daftar kantong.
     */
    public function update(PenyisihanKantongAftap $penyisihan, array $data): PenyisihanKantongAftap
    {
        $penyisihan->update([
            'tanggal'    => $data['tanggal'] ?? $penyisihan->tanggal,
            'keterangan' => $data['keterangan'] ?? $penyisihan->keterangan,
        ]);

        return $penyisihan;
    }

    /**
     * Update alasan satu baris kantong dalam transaksi penyisihan ("Ubah Alasan").
     */
    public function updateAlasanDetail(PenyisihanKantongAftapDetail $detail, string $alasan): PenyisihanKantongAftapDetail
    {
        $detail->update(['alasan' => $alasan]);

        StokKantongPenerimaanDetail::where('penyisihan_detail_id', $detail->id)
            ->update(['info_kirim' => $alasan]);

        return $detail;
    }

    /**
     * Hapus satu baris kantong dari transaksi penyisihan, kembalikan status ke 'tersedia'.
     */
    public function removeDetail(PenyisihanKantongAftapDetail $detail): void
    {
        DB::transaction(function () use ($detail) {
            StokKantongPenerimaanDetail::where('penyisihan_detail_id', $detail->id)
                ->update([
                    'status_kirim'         => StokKantongPenerimaanDetail::STATUS_TERSEDIA,
                    'info_kirim'           => null,
                    'penyisihan_detail_id' => null,
                ]);

            $detail->delete();
        });
    }

    /**
     * Hapus transaksi penyisihan dan kembalikan status semua kantongnya ke 'tersedia'.
     */
    public function destroy(PenyisihanKantongAftap $penyisihan): void
    {
        DB::transaction(function () use ($penyisihan) {
            foreach ($penyisihan->details as $detail) {
                StokKantongPenerimaanDetail::where('penyisihan_detail_id', $detail->id)
                    ->update([
                        'status_kirim'         => StokKantongPenerimaanDetail::STATUS_TERSEDIA,
                        'info_kirim'           => null,
                        'penyisihan_detail_id' => null,
                    ]);
            }

            $penyisihan->details()->delete();
            $penyisihan->delete();
        });
    }
}