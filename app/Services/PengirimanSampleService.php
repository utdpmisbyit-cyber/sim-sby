<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\PengirimanSample;
use App\Models\PengirimanSampleDetail;
use App\Models\PenerimaanKantong;
use App\Models\PenerimaanKantongDetail;
use App\Models\PengirimanSerologi;
use App\Models\PengirimanSerologiDetail;
use App\Models\PengirimanProduksi;


class PengirimanSampleService
{
    /**
     * Generate no_fpd unik: FPD-YYMMDD-XXXX
     */
    public function generateNoFpd(): string
    {
        $prefix = 'FPD-' . date('ymd') . '-';
        $urut   = PengirimanSample::where('no_fpd', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate kode unik untuk pengiriman serologi: SRL-YYMMDD-XXXX
     */
    public function generateKodeSerologi(): string
    {
        $prefix = 'SRL-' . date('ymd') . '-';
        $urut   = PengirimanSerologi::where('kode', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

  
        public function getKantongByScan(string $no_kantong): object
    {
        $data = DB::table('aftap as a')
            ->leftJoin('log_donor as ld', 'ld.id', '=', 'a.log_donor_id')
            ->leftJoin('donor as d', 'd.id', '=', 'a.donor_id')
            ->leftJoin('cabang as c', 'c.id', '=', 'a.asal_darah_id')
            ->select([
                'a.id as aftap_id',
                'a.no_kantong',
                'a.no_selang',
                'a.jenis_donor as jenis_kantong',
                'a.jam_mulai as tanggal_aftap',
                'a.asal_darah_id',
                'a.dokter_id as petugas_id',
                'a.jenis_donor',
                'd.id as donor_id',
                'd.kode as no_donor',
                'd.nama as nama_donor',
                'd.golongan_darah as gol_darah',
                'd.rhesus',
                'c.id as cabang_id',
                'c.kode as kode_asal_darah',
                'c.nama as asal_darah',      // ← asal_darah dari nama cabang
            ])
            ->where('a.no_kantong', $no_kantong)
            ->whereNull('a.deleted_at')
            ->first();

        if (! $data) {
            throw new \Exception("No. kantong [{$no_kantong}] tidak ditemukan.");
        }

        return $data;
    }
    /**
     * Ambil daftar dengan filter + pagination untuk tab riwayat.
     */
    public function getList(array $filters, int $page = 1, int $per = 10): array
    {
        $q = PengirimanSample::withCount('detail')
            ->when($filters['dari']    ?? null, fn($q) => $q->whereDate('tanggal_fpd', '>=', $filters['dari']))
            ->when($filters['sampai']  ?? null, fn($q) => $q->whereDate('tanggal_fpd', '<=', $filters['sampai']))
            ->when($filters['keyword'] ?? null, fn($q) => $q->where('no_fpd', 'like', "%{$filters['keyword']}%"))
            ->latest('tanggal_fpd');

        $total = $q->count();
        $data  = $q->skip(($page - 1) * $per)->take($per)->get();

        return ['total' => $total, 'data' => $data];
    }

    /**
     * Simpan header + detail pengiriman_sample dalam satu transaksi.
     */
    public function simpan(array $data): PengirimanSample
    {
        DB::beginTransaction();

        try {
            $header = PengirimanSample::create([
                'no_fpd'            => $data['no_fpd'],
                'tanggal_fpd'       => $data['tanggal_fpd'],
                'total'             => count($data['items'] ?? []),
                'keterangan'        => $data['keterangan']        ?? null,
                'type_kantong'      => $data['type_kantong']      ?? null,
                'suhu'              => $data['suhu']              ?? null,
                'is_nat'            => (bool) ($data['is_nat']    ?? false),
                'petugas_pemeriksa' => $data['petugas_pemeriksa'] ?? null,
                'id_logger'         => $data['id_logger']         ?? null,
                'id_coolbox'        => $data['id_coolbox']        ?? null,
            ]);

            foreach (($data['items'] ?? []) as $urut => $item) {
                PengirimanSampleDetail::create([
                    'pengiriman_sample_id' => $header->id,
                    'urut'                 => $urut + 1,
                    'no_kantong'           => $item['no_kantong']      ?? null,
                    'jenis_kantong'        => $item['jenis_kantong']   ?? null,
                    'aftap_id'             => $item['aftap_id']        ?? null,
                    'tanggal_aftap'        => $item['tanggal_aftap']   ?? null,
                    'donor_id'             => $item['donor_id']        ?? null,
                    'no_donor'             => $item['no_donor']        ?? null,
                    'nama_donor'           => $item['nama_donor']      ?? null,
                    'asal_darah_id'        => $item['asal_darah_id']   ?? null,
                    'kode_asal_darah'      => $item['kode_asal_darah'] ?? null,
                    'gol_darah'            => $item['gol_darah']       ?? null,
                    'rhesus'               => $item['rhesus']          ?? null,
                    'tolak'                => (bool) ($item['tolak']   ?? false),
                    'keterangan'           => $item['keterangan']      ?? null,
                    'petugas_id'           => $item['petugas_id']      ?? null,
                    'cabang_id'            => $item['cabang_id']       ?? null,
                    'perkiraan'            => $item['perkiraan']       ?? null,
                    'jenis_donor'          => $item['jenis_donor']     ?? null,
                    'suhu'                 => $item['suhu']            ?? $data['suhu'] ?? null,
                    'id_logger'            => $item['id_logger']       ?? $data['id_logger'] ?? null,
                    'id_coolbox'           => $item['id_coolbox']      ?? $data['id_coolbox'] ?? null,
                    'asal_darah'           => $item['asal_darah']      ?? null,  
                    'status'               => $item['status']           ?? 'pending',
                ]);
            }

            DB::commit();
            return $header;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

   /**
 * Kirim FPD ke serologi:
 *  1. Simpan ke pengiriman_serologi (header)
 *  2. Simpan ke pengiriman_serologi_detail (per kantong)
 *  3. Kurangi stok di penerimaan_kantong_detail (hapus baris no_kantong)
 *
 * @param  int   $pengirimanSampleId  ID record pengiriman_sample
 * @param  array $extra               Data tambahan: pengirim_id, penerima_id, dokumen
 * @return PengirimanSerologi
 *
 * @throws \Exception
 */
    public function kirimFpd(int $pengirimanSampleId, array $extra = []): PengirimanSerologi
{
    $sample = PengirimanSample::with('detail')->findOrFail($pengirimanSampleId);

    $pengirimId = $extra['pengirim_id'] ?? auth()->id() ?? null;

    if (is_null($pengirimId)) {
        throw new \Exception('Pengirim tidak diketahui. Pastikan Anda sudah login.');
    }

    DB::beginTransaction();

    try {
        // ── 1. Buat header pengiriman_serologi ──────────────────────
        $serologi = PengirimanSerologi::create([
            'kode'        => $this->generateKodeSerologi(),
            'pengirim_id' => $pengirimId,
            'penerima_id' => $extra['penerima_id'] ?? null,
            'dokumen'     => $extra['dokumen']     ?? $sample->no_fpd,
        ]);

        // ── 2. Buat header pengiriman_produksi (tanpa detail) ───────
        PengirimanProduksi::create([
            'kode'        => $this->generateKodeProduksi(),
            'pengirim_id' => $pengirimId,
            'penerima_id' => $extra['penerima_id'] ?? null,
            'dokumen'     => $sample->no_fpd,
        ]);

        // ── 3. Salin detail ke serologi + kurangi stok ──────────────
        foreach ($sample->detail as $det) {

            PengirimanSerologiDetail::create([
                'pengiriman_serologi_id' => $serologi->id,
                'no_kantong'             => $det->no_kantong,
                'no_selang'              => $det->id_coolbox,
                'jenis_kantong'          => $det->jenis_kantong,
                'no_donor'               => $det->no_donor,
                'nama_donor'             => $det->nama_donor,
                'gol_darah'              => $det->gol_darah,
                'rhesus'                 => $det->rhesus,
                'asal_darah'             => $det->kode_asal_darah,
                'tanggal_aftap'          => $det->tanggal_aftap,
                'tolak'                  => $det->tolak,
                'is_nat'                 => $sample->is_nat,
            ]);

            PenerimaanKantongDetail::where('no_kantong', $det->no_kantong)->delete();
        }

        DB::commit();
        return $serologi;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

    /**
     * Update header saja (tanpa ubah detail).
     */
    public function update(PengirimanSample $header, array $data): PengirimanSample
    {
        $header->update([
            'tanggal_fpd'       => $data['tanggal_fpd']       ?? $header->tanggal_fpd,
            'keterangan'        => $data['keterangan']        ?? $header->keterangan,
            'type_kantong'      => $data['type_kantong']      ?? $header->type_kantong,
            'suhu'              => $data['suhu']              ?? $header->suhu,
            'is_nat'            => (bool) ($data['is_nat']    ?? $header->is_nat),
            'petugas_pemeriksa' => $data['petugas_pemeriksa'] ?? $header->petugas_pemeriksa,
            'id_logger'         => $data['id_logger']         ?? $header->id_logger,
            'id_coolbox'        => $data['id_coolbox']        ?? $header->id_coolbox,
        ]);

        return $header;
    }
    public function generateKodeProduksi(): string
    {
        $prefix = 'PRD-' . date('ymd') . '-';
        $urut   = PengirimanProduksi::where('kode', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }
    public function hapus(PengirimanSample $header): void
    {
        $header->delete();
    }

    /**
     * Toggle tolak pada satu detail.
     */
    public function toggleTolak(PengirimanSampleDetail $detail): PengirimanSampleDetail
    {
        $detail->update(['tolak' => ! $detail->tolak]);
        return $detail;
    }
}