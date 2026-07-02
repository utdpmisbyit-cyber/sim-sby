<?php

namespace App\Services;

use App\Models\PenerimaanProlisPenyimpanan;
use App\Models\PengirimanDarahProlis;
use App\Services\StokDarahService; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PenerimaanProlisPenyimpananService
{
   
    const RUANGAN = ['A-1200', 'B-1000', 'C-800'];

    const KAPASITAS_RUANGAN = [
        'A-1200' => 1200,
        'B-1000' => 1000,
        'C-800'  => 800,
    ];
     public function __construct(
        protected StokDarahService $stokService
    ) {}

   
    public function getAll(array $filters = [])
    {
        $query = PenerimaanProlisPenyimpanan::with(['petugas', 'asalDarah']);

        if (!empty($filters['no_penerimaan'])) {
            $query->where('no_penerimaan', $filters['no_penerimaan']);
        }
        if (!empty($filters['golongan_darah'])) {
            $query->where('golongan_darah', $filters['golongan_darah']);
        }
        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['rhesus'])) {
            $query->where('rhesus', $filters['rhesus']);
        }

        return $query->orderByDesc('id')->get();
    }

    public function getByNoPengiriman(string $noPengiriman)
    {
         return PengirimanDarahProlis::query()
            ->where('no_pengiriman', 'like', '%' . trim($noPengiriman) . '%')
            ->orderBy('id')
            ->get();
    }

    public function getByRuangan(string $ruangan, array $filters = [])
    {
        $query = PenerimaanProlisPenyimpanan::where('status', $ruangan);

        if (!empty($filters['golongan_darah'])) {
            $query->where('golongan_darah', $filters['golongan_darah']);
        }
        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }
        if (!empty($filters['rhesus'])) {
            $query->where('rhesus', $filters['rhesus']);
        }

        return $query->orderByDesc('id')->get();
    }

    public function store(array $data): PenerimaanProlisPenyimpanan
    {
        return DB::transaction(function () use ($data) {
            $data['no_penerimaan'] = PenerimaanProlisPenyimpanan::generateNoPenerimaan();
            $data['tgl_penerimaan'] = now()->toDateString();
            $data['created_by']   = Auth::id();
            $data['petugas_id']   = Auth::id();

            $record = PenerimaanProlisPenyimpanan::create($data);

            $record->refresh();

            $this->stokService->masuk($record);

            return $record;
        });
    }

    public function update(int $id, array $data): PenerimaanProlisPenyimpanan
    {
        return DB::transaction(function () use ($id, $data) {
            $record = PenerimaanProlisPenyimpanan::findOrFail($id);

            $jumlahLama = $record->jumlah ?? 1;
            $record->update($data);
            $record->refresh();

            // Jika jumlah atau ruang berubah, update stok
            $jumlahBaru = $record->jumlah ?? 1;
            if ($jumlahLama !== $jumlahBaru) {
                // Rollback dulu jumlah lama, lalu masukkan jumlah baru
                $this->stokService->rollbackMasuk(
                    tap(clone $record, fn($r) => $r->jumlah = $jumlahLama)
                );
                $this->stokService->masuk($record);
            }

            return $record->fresh();
        });
    }

   public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = PenerimaanProlisPenyimpanan::findOrFail($id);

            // Rollback stok sebelum hapus
            $this->stokService->rollbackMasuk($record);

            return $record->delete();
        });
    }

    public function getKapasitas(string $ruangan): array
    {
        $kapasitas = self::KAPASITAS_RUANGAN[$ruangan] ?? 0;
        $isi = PenerimaanProlisPenyimpanan::where('status', $ruangan)->sum('jumlah');
        $sisaMax = $kapasitas - $isi;

        return [
            'ruangan'   => $ruangan,
            'kapasitas' => $kapasitas,
            'isi'       => $isi,
            'sisa_max'  => max(0, $sisaMax),
        ];
    }

    public function cekKapasitas(string $ruangan, array $filters = []): array
    {
        $query = PenerimaanProlisPenyimpanan::where('status', $ruangan);

        if (!empty($filters['golongan_darah'])) {
            $query->where('golongan_darah', $filters['golongan_darah']);
        }
        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        $items = $query->orderByDesc('id')->get();
        $kapasitasInfo = $this->getKapasitas($ruangan);

        return [
            'kapasitas_info' => $kapasitasInfo,
            'items'          => $items,
        ];
    }

      public function getByNoStock(string $noStock)
    {
        return PengirimanDarahProlis::query()
            ->where('no_stok', 'like', '%' . trim($noStock) . '%')
            ->get();
    }
    public function getRuanganOptions(): array
    {
        return self::RUANGAN;
    }

    public function getGolonganDarahOptions(): array
    {
        return ['A', 'B', 'AB', 'O'];
    }

    public function getJenisOptions(): array
    {
        return ['PCLs', 'WB', 'PRC', 'FFP', 'TC', 'Cryo'];
    }

    public function getRhesusOptions(): array
    {
        return ['Positif', 'Negatif'];
    }
}