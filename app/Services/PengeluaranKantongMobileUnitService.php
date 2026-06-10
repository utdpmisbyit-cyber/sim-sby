<?php

namespace App\Services;

use App\Models\AsalDarah;
use App\Models\MobilUnit;
use App\Models\PenerimaanKantongDetail;
use App\Models\PengeluaranKantongMobileUnit;
use App\Models\PermintaanMobileUnit;
use App\Models\Petugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranKantongMobileUnitService
{
    public function getPermintaanMobileUnit()
    {
        return PermintaanMobileUnit::query()
            ->select('id', 'nomor', 'tanggal')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }
    public function getMobilUnits()
    {
        return MobilUnit::orderBy('merk_mobil')->get(['id', 'kode', 'merk_mobil', 'no_polisi']);
    }

    public function getAsalDarah()
    {
        return AsalDarah::orderBy('nama')->get(['id', 'kode', 'nama']);
    }

    public function getPetugas()
    {
        return Petugas::orderBy('nama')->get(['id', 'kode', 'nama']);
    }

    public function findKantong(string $noKantong): ?array
    {
        $detail = PenerimaanKantongDetail::where('no_kantong', $noKantong)->first();
        if (! $detail) {
            return null;
        }

        // Cek apakah sudah pernah dikeluarkan
        $sudahKeluar = PengeluaranKantongMobileUnit::where('no_kantong', $noKantong)
            ->whereNull('deleted_at')
            ->exists();

        return [
            'penerimaan_kantong_id' => $detail->penerimaan_id,
            'no_kantong'            => $detail->no_kantong,
            'merk'                  => $detail->merk,
            'jenis'                 => $detail->jenis,
            'ukuran'                => $detail->ukuran,
            'no_lot'                => $detail->no_lot,
            'sudah_keluar'          => $sudahKeluar,
        ];
    }

    public function getKantongItems(): array
    {
        return session('pengeluaran_kantong_items', []);
    }

    public function addKantongItem(array $item): void
    {
        $items = $this->getKantongItems();

        // Cegah duplikat
        foreach ($items as $existing) {
            if ($existing['no_kantong'] === $item['no_kantong']) {
                return;
            }
        }

        $items[] = $item;
        session(['pengeluaran_kantong_items' => $items]);
    }

    public function removeKantongItem(string $noKantong): void
    {
        $items = array_filter(
            $this->getKantongItems(),
            fn($i) => $i['no_kantong'] !== $noKantong
        );
        session(['pengeluaran_kantong_items' => array_values($items)]);
    }

    public function clearKantongItems(): void
    {
        session()->forget('pengeluaran_kantong_items');
    }


    public function store(array $data): PengeluaranKantongMobileUnit
    {
        return DB::transaction(function () use ($data) {
            $noKeluar = PengeluaranKantongMobileUnit::generateNomorKeluar();

            foreach ($data['kantong_items'] as $item) {
                PengeluaranKantongMobileUnit::create([
                    'no_keluar'                  => $noKeluar,
                    'tgl_keluar'                 => $data['tgl_keluar'],
                    'no_kantong'                 => $item['no_kantong'],
                    'no_lot'                     => $item['no_lot'] ?? null,
                    'merk'                       => $item['merk'] ?? null,
                    'jenis'                      => $item['jenis'] ?? null,
                    'ukuran'                     => $item['ukuran'] ?? null,
                    'jumlah'                     => $item['jumlah'] ?? 1,
                    'mobile_unit_id'             => $data['mobile_unit_id'],
                    'asal_darah_id'              => $data['asal_darah_id'],
                    'petugas_id'                 => $data['petugas_id'],
                    'penerimaan_kantong_id'      => $item['penerimaan_kantong_id'] ?? null,
                    'tujuan'                     => $data['tujuan'] ?? null,
                    'keterangan'                 => $data['keterangan'] ?? null,
                    'permintaan_mobile_unit_id'   => $data['permintaan_mobile_unit_id'] ?? null,
                    'no_permintaan'               => $data['no_permintaan'] ?? null,
                    'created_by'                 => Auth::id(),
                ]);
            }

            // Bersihkan session setelah simpan
            $this->clearKantongItems();

            return PengeluaranKantongMobileUnit::where('no_keluar', $noKeluar)->first();
        });
    }

    
    public function findById($id)
{
    return PengeluaranKantongMobileUnit::with(['petugas', 'asalDarah', 'mobilUnit'])
        ->findOrFail($id);
}

// ── Get kantong items by pengeluaran ID ──────────────────────────────────────
public function getKantongItemsByPengeluaran($pengeluaranId)
{
    $pengeluaran = PengeluaranKantongMobileUnit::find($pengeluaranId);
    
    if (!$pengeluaran) {
        return [];
    }
    
    $items = PengeluaranKantongMobileUnit::where('no_keluar', $pengeluaran->no_keluar)->get();
    
    return $items->map(function($item) {
        return [
            'penerimaan_kantong_id' => $item->penerimaan_kantong_id,
            'no_kantong'            => $item->no_kantong,
            'merk'                  => $item->merk,
            'jenis'                 => $item->jenis,
            'ukuran'                => $item->ukuran,
            'no_lot'                => $item->no_lot,
            'jumlah'                => $item->jumlah,
            'sudah_keluar'          => true,
        ];
    })->toArray();
}

// ── Set edit mode kantong items ──────────────────────────────────────────────
public function setEditModeKantongItems(array $items): void
{
    // Format ulang items agar sesuai dengan format yang diharapkan
    $formattedItems = [];
    foreach ($items as $item) {
        $formattedItems[] = [
            'penerimaan_kantong_id' => $item['penerimaan_kantong_id'] ?? null,
            'no_kantong'            => $item['no_kantong'],
            'merk'                  => $item['merk'] ?? null,
            'jenis'                 => $item['jenis'] ?? null,
            'ukuran'                => $item['ukuran'] ?? null,
            'no_lot'                => $item['no_lot'] ?? null,
            'jumlah'                => $item['jumlah'] ?? 1,
            'sudah_keluar'          => true,
        ];
    }
    session(['pengeluaran_kantong_items' => $formattedItems]);
}

// ── Update pengeluaran (Versi dengan Validasi) ────────────────────────────────
public function update($id, array $data): PengeluaranKantongMobileUnit
{
    return DB::transaction(function () use ($id, $data) {
        // Cari data pengeluaran
        $pengeluaran = PengeluaranKantongMobileUnit::find($id);
        
        if (!$pengeluaran) {
            throw new \Exception('Data pengeluaran dengan ID ' . $id . ' tidak ditemukan');
        }
        
        $noKeluar = $pengeluaran->no_keluar;
        
        // Hapus semua data dengan no_keluar yang sama
        $deleted = PengeluaranKantongMobileUnit::where('no_keluar', $noKeluar)->delete();
        
        // Insert data baru
        $inserted = 0;
        foreach ($data['kantong_items'] as $item) {
            $newData = [
                'no_keluar'                  => $noKeluar,
                'tgl_keluar'                 => $data['tgl_keluar'],
                'no_kantong'                 => $item['no_kantong'],
                'no_lot'                     => $item['no_lot'] ?? null,
                'merk'                       => $item['merk'] ?? null,
                'jenis'                      => $item['jenis'] ?? null,
                'ukuran'                     => $item['ukuran'] ?? null,
                'jumlah'                     => 1,
                'mobile_unit_id'             => $data['mobile_unit_id'],
                'asal_darah_id'              => $data['asal_darah_id'],
                'petugas_id'                 => $data['petugas_id'],
                'penerimaan_kantong_id'      => $item['penerimaan_kantong_id'] ?? null,
                'tujuan'                     => $data['tujuan'] ?? null,
                'keterangan'                 => $data['keterangan'] ?? null,
                'permintaan_mobile_unit_id'  => $data['permintaan_mobile_unit_id'] ?? null,
                'no_permintaan'              => $data['no_permintaan'] ?? null,
                'created_by'                 => Auth::id(),
            ];
            
            PengeluaranKantongMobileUnit::create($newData);
            $inserted++;
        }
        
        // Bersihkan session
        $this->clearKantongItems();
        
        // Ambil data yang baru saja diupdate
        $result = PengeluaranKantongMobileUnit::where('no_keluar', $noKeluar)->first();
        
        if (!$result) {
            throw new \Exception('Gagal menyimpan data pengeluaran');
        }
        
        return $result;
    });
}

public function delete($id): bool
{
    return DB::transaction(function () use ($id) {
        $pengeluaran = PengeluaranKantongMobileUnit::findOrFail($id);
        $noKeluar = $pengeluaran->no_keluar;
        
        // Hapus semua record dengan no_keluar yang sama
        $deleted = PengeluaranKantongMobileUnit::where('no_keluar', $noKeluar)->delete();
        
        return $deleted > 0;
    });
}

public function getPaginatedList(array $filters = [])
    {
        return PengeluaranKantongMobileUnit::query()
            ->leftJoin(
                'permintaan_mobile_unit',
                'pengeluaran_kantong_mobile_unit.permintaan_mobile_unit_id',
                '=',
                'permintaan_mobile_unit.id'
            )
            ->with([
                'petugas',
                'asalDarah',
                'mobilUnit'
            ])
            ->select(
                'pengeluaran_kantong_mobile_unit.*',
                'permintaan_mobile_unit.nomor as nomor_permintaan'
            )
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];

                $q->where(function ($qq) use ($search) {
                    $qq->where('pengeluaran_kantong_mobile_unit.no_keluar', 'like', "%{$search}%")
                    ->orWhere('permintaan_mobile_unit.nomor', 'like', "%{$search}%");
                });
            })
            ->when(!empty($filters['tgl_dari']), function ($q) use ($filters) {
                $q->whereDate(
                    'pengeluaran_kantong_mobile_unit.tgl_keluar',
                    '>=',
                    $filters['tgl_dari']
                );
            })
            ->when(!empty($filters['tgl_sampai']), function ($q) use ($filters) {
                $q->whereDate(
                    'pengeluaran_kantong_mobile_unit.tgl_keluar',
                    '<=',
                    $filters['tgl_sampai']
                );
            })
            ->orderByDesc('pengeluaran_kantong_mobile_unit.id')
            ->paginate(10);
    }



}