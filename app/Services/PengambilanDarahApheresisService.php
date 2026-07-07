<?php

namespace App\Services;

use App\Models\PengambilanDarahApheresis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengambilanDarahApheresisService
{
    /** Sama seperti modul sampling pra donor - lihat catatan di SamplingPraDonorService. */
    protected string $donorTable = 'donor';

    public function list(?string $search, int $perPage = 15)
    {
        return PengambilanDarahApheresis::search($search)
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): PengambilanDarahApheresis
    {
        return PengambilanDarahApheresis::with('siklus')->findOrFail($id);
    }

    /**
     * Generate No Transaksi baru, format: DIS-YYMMDD + kode petugas + running number.
     * Contoh dari screenshot: DIS-2606301C05
     */
    public function generateKode(): string
    {
        $tanggal = now()->format('ymd');
        $kodePetugas = Auth::user()->kode_petugas ?? 'C05';

        $prefix = "DIS-{$tanggal}{$kodePetugas}";

        $last = PengambilanDarahApheresis::where('no_transaksi', 'like', "{$prefix}%")
            ->orderByDesc('no_transaksi')
            ->value('no_transaksi');

        $urut = 1;
        if ($last) {
            $urut = ((int) substr($last, -3)) + 1;
        }

        return $prefix . str_pad($urut, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan header + baris-baris siklus sekaligus dalam satu transaksi.
     * $data['siklus'] berupa array asosiatif per baris, contoh:
     * [ ['siklus_ke' => 1, 'jam' => '09:10', 'draw_return_ml' => 120, ...], ... ]
     */
    public function create(array $data): PengambilanDarahApheresis
    {
        return DB::transaction(function () use ($data) {
            $siklusRows = $data['siklus'] ?? [];
            unset($data['siklus']);

            $data['petugas_id'] = $data['petugas_id'] ?? Auth::id();
            $data['server_date'] = $data['server_date'] ?? now();

            $header = PengambilanDarahApheresis::create($data);
            $this->syncSiklus($header, $siklusRows);

            return $header->load('siklus');
        });
    }

    public function update(PengambilanDarahApheresis $header, array $data): PengambilanDarahApheresis
    {
        return DB::transaction(function () use ($header, $data) {
            $siklusRows = $data['siklus'] ?? [];
            unset($data['siklus']);

            $header->update($data);
            $this->syncSiklus($header, $siklusRows);

            return $header->fresh('siklus');
        });
    }

    /**
     * Ganti seluruh baris siklus milik header ini dengan data baru dari form
     * (hapus lalu insert ulang - lebih sederhana daripada diff per baris).
     */
    protected function syncSiklus(PengambilanDarahApheresis $header, array $rows): void
    {
        $header->siklus()->delete();

        $toInsert = [];
        foreach ($rows as $i => $row) {
            // Lewati baris kosong (semua field null/kosong)
            if (empty(array_filter($row, fn ($v) => $v !== null && $v !== ''))) {
                continue;
            }

            $toInsert[] = [
                'pengambilan_darah_id' => $header->id,
                'siklus_ke'            => $row['siklus_ke'] ?? ($i + 1),
                'jam'                  => $row['jam'] ?? null,
                'draw_return_ml'       => $row['draw_return_ml'] ?? null,
                'draw_return_menit'    => $row['draw_return_menit'] ?? null,
                'plasma_vol'           => $row['plasma_vol'] ?? null,
                'platelet_yield'       => $row['platelet_yield'] ?? null,
                'plasma_vol_2'         => $row['plasma_vol_2'] ?? null,
                'nacl_sitrat'          => $row['nacl_sitrat'] ?? null,
                'keterangan'           => $row['keterangan'] ?? null,
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }

        if ($toInsert) {
            DB::table('apheresis_pengambilan_darah_siklus')->insert($toInsert);
        }
    }

    public function delete(PengambilanDarahApheresis $header): bool
    {
        return (bool) $header->delete();
    }

    /**
     * Cari data donor dari tabel `donor` berdasarkan Kode Donor (hasil scan barcode/manual)
     * untuk auto-fill form. Sama seperti SamplingPraDonorService::findDonorReference().
     */
    public function findDonorReference(string $kodeDonor): ?array
    {
        $donor = DB::table($this->donorTable)
            ->where('kode', $kodeDonor)
            ->orWhere('no_ktp', $kodeDonor)
            ->whereNull('deleted_at')
            ->latest('id')
            ->first();

        if (!$donor) {
            return null;
        }

        return [
            'donor_id'       => $donor->id,
            'no_donor'       => $donor->kode,
            'nama_donor'     => $donor->nama,
            'tgl_lahir'      => $donor->tanggal_lahir,
            'jenis_kelamin'  => $this->normalizeJenisKelamin($donor->jenis_kelamin),
            'golongan_darah' => $donor->golongan_darah,
            'rhesus'         => $donor->rhesus,
            'donor_ke'       => $donor->donor_ke ?? null,
        ];
    }

    /**
     * Cari data dari tabel `apheresis_sampling_pra_donors` berdasarkan No Sampling
     * (no_transaksi hasil input di modul Sampling Pra Donor) untuk auto-fill form.
     */
    public function findSamplingReference(string $noSampling): ?array
    {
        $sampling = DB::table('apheresis_sampling_pra_donors')
            ->where('no_transaksi', $noSampling)
            ->latest('id')
            ->first();

        if (!$sampling) {
            return null;
        }

        return [
            'no_sampling'    => $sampling->no_transaksi,
            'no_donor'       => $sampling->no_donor,
            'nama_donor'     => $sampling->nama_donor,
            'tgl_lahir'      => $sampling->tgl_lahir,
            'jenis_kelamin'  => $sampling->jenis_kelamin, // sudah tersimpan sbg 'pria'/'wanita' dari modul sampling
            'golongan_darah' => $sampling->golongan_darah,
            'rhesus'         => $sampling->rhesus,
            'hct'            => $sampling->hct,
            'status_lulus'   => $sampling->status_lulus,
        ];
    }

    protected function normalizeJenisKelamin(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $v = strtolower(trim($value));

        return match (true) {
            in_array($v, ['l', 'laki-laki', 'laki laki', 'pria', 'male', 'm']) => 'pria',
            in_array($v, ['p', 'perempuan', 'wanita', 'female', 'f']) => 'wanita',
            default => null,
        };
    }
}