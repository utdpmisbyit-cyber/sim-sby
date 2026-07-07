<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\PengembalianBarang;
use App\Models\PengembalianBarangDetail;
use App\Models\Stok;
use Illuminate\Support\Facades\DB;

class PengembalianBarangService extends IoService
{
    public function __construct()
    {
        $this->model   = new PengembalianBarang();
        $this->sort_by = [
            'tgl_kembali' => 'desc',
            'no_kembali'  => 'desc',
        ];

        $this->filters = [
            'no_kembali',
            'tgl_kembali',
            'departemen',
            'barang',
            'no_kantong',
            'kondisi',
        ];

        $this->with = [
            'details.barang',
            'creator',
        ];
    }

    public function search($params = [])
    {
        $query = PengembalianBarang::query()->with($this->with);

        $query = $this->dynamic_search($query, $params);

        return $query
            ->orderBy('tgl_kembali', 'desc')
            ->orderBy('no_kembali', 'desc')
            ->paginate($params['per_page'] ?? 10)
            ->withQueryString();
    }

    public function dynamic_search($model, $params = [])
    {
        $no_kembali  = $params['no_kembali']  ?? '';
        $tgl_kembali = $params['tgl_kembali'] ?? '';
        $departemen  = $params['departemen']  ?? '';
        $barang      = $params['barang']      ?? '';
        $no_kantong  = $params['no_kantong']  ?? '';
        $kondisi     = $params['kondisi']     ?? '';

        if ($no_kembali !== '') {
            $model->where('no_kembali', 'like', '%' . $no_kembali . '%');
        }

        if ($tgl_kembali !== '') {
            $model->whereDate('tgl_kembali', $tgl_kembali);
        }

        if ($departemen !== '') {
            $model->where('departemen', 'like', '%' . $departemen . '%');
        }

        if ($barang !== '' || $no_kantong !== '' || $kondisi !== '') {
            $model->whereHas('details', function ($q) use ($barang, $no_kantong, $kondisi) {
                if ($barang !== '') {
                    $q->whereHas('barang', function ($qb) use ($barang) {
                        $qb->where('nama', 'like', '%' . $barang . '%')
                           ->orWhere('kode', 'like', '%' . $barang . '%');
                    });
                }
                if ($no_kantong !== '') {
                    $q->where('no_kantong', 'like', '%' . $no_kantong . '%');
                }
                if ($kondisi !== '') {
                    $q->where('kondisi', $kondisi);
                }
            });
        }

        return $model;
    }

    /**
     * Generate no_kembali
     * PB + YYMM + 6 digit sequence
     * ex: PB2507000001
     */
    public function generateNoKembali(): string
    {
        $prefix = 'PB' . now()->format('ym');

        $last = PengembalianBarang::where('no_kembali', 'like', $prefix . '%')
            ->orderByDesc('no_kembali')
            ->value('no_kembali');

        $seq = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad($seq, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate no_trans_stok untuk 1 baris detail. Formatnya dikaitkan ke
     * no_kembali + nomor urut baris supaya unik dan mudah ditelusuri balik
     * ke transaksi pengembalian asalnya.
     *
     * CATATAN: sesuaikan dengan konvensi no_trans_stok asli project Anda
     * kalau ternyata beda (mis. ada prefix khusus per jenis proses).
     */
    public function generateNoTransStok(string $noKembali, int $urutan): string
    {
        return $noKembali . '-' . str_pad($urutan, 2, '0', STR_PAD_LEFT);
    }

    /**
     * ──────────────────────────────────────────────────────────
     * PROSES DETAIL PENGEMBALIAN:
     * - Simpan baris pengembalian_barang_detail.
     * - Untuk kondisi 'baik': tambah baris stok (qty_in) dan tambah
     *   kolom barang.stok, karena barang kembali bisa dipakai lagi.
     * - Untuk kondisi 'rusak': hanya dicatat, TIDAK menambah stok pakai.
     *
     * Dipanggil di dalam DB::transaction() oleh controller.
     * ──────────────────────────────────────────────────────────
     */
    public function processDetails(PengembalianBarang $pengembalian, array $details): void
    {
        $urutan = 1;

        foreach ($details as $detail) {
            if (empty($detail['jumlah']) || empty($detail['barang_id'])) {
                continue;
            }

            $jumlah  = (int) $detail['jumlah'];
            $kondisi = $detail['kondisi'] ?? 'baik';

            $noTransStok = null;

            if ($kondisi === 'baik') {
                $noTransStok = $this->generateNoTransStok($pengembalian->no_kembali, $urutan);

                Stok::create([
                    'no_trans_stok' => $noTransStok,
                    'tgl_proses'    => $pengembalian->tgl_kembali,
                    'proses'        => 'pengembalian_barang',
                    'barang_id'     => $detail['barang_id'],
                    'qty_in'        => $jumlah,
                    'qty_out'       => 0,
                    'keterangan'    => 'Pengembalian barang ' . $pengembalian->no_kembali,
                    'aktif'         => 1,
                ]);

                Barang::whereKey($detail['barang_id'])->increment('stok', $jumlah);
            }

            PengembalianBarangDetail::create([
                'pengembalian_barang_id' => $pengembalian->id,
                'barang_id'              => $detail['barang_id'],
                'no_kantong'             => $detail['no_kantong'] ?? null,
                'jumlah'                 => $jumlah,
                'kondisi'                => $kondisi,
                'no_trans_stok'          => $noTransStok,
            ]);

            $urutan++;
        }
    }

    /**
     * ──────────────────────────────────────────────────────────
     * REVERT efek stok dari sebuah pengembalian (dipakai sebelum
     * update / saat delete), supaya barang.stok dan tabel stok tidak
     * dobel-hitung.
     * ──────────────────────────────────────────────────────────
     */
    public function revertDetails(PengembalianBarang $pengembalian): void
    {
        foreach ($pengembalian->details as $detail) {
            if ($detail->kondisi === 'baik' && $detail->no_trans_stok) {
                $stok = Stok::find($detail->no_trans_stok);

                if ($stok) {
                    Barang::whereKey($detail->barang_id)->decrement('stok', (int) $stok->qty_in);
                    $stok->delete();
                }
            }
        }

        $pengembalian->details()->delete();
    }
}