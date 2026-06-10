<?php
namespace App\Services;

use App\Models\PengirimanDarahProlis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PengirimanDarahService
{
    public function getAll($perPage = 15)
    {
        return PengirimanDarahProlis::orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getByNoPengiriman($noPengiriman)
    {
        return PengirimanDarahProlis::where('no_pengiriman', $noPengiriman)
            ->orderBy('tgl_aftap')
            ->get();
    }

    public function getDistinctNoPengiriman()
    {
        return PengirimanDarahProlis::select('no_pengiriman', 'tgl_pengiriman')
            ->distinct()
            ->orderBy('tgl_pengiriman', 'desc')
            ->get();
    }

    public function generateNoPengiriman()
    {
        $prefix = 'K' . date('ymd');
        $lastRecord = PengirimanDarahProlis::where('no_pengiriman', 'like', $prefix . '%')
            ->orderBy('no_pengiriman', 'desc')
            ->first();
        
        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord->no_pengiriman, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['created_by'] = Auth::id();
            return PengirimanDarahProlis::create($data);
        });
    }

    public function createBatch(array $dataBatch)
    {
        return DB::transaction(function () use ($dataBatch) {
            $noPengiriman = $this->generateNoPengiriman();
            $createdData = [];
            
            foreach ($dataBatch as $data) {
                $data['no_pengiriman'] = $noPengiriman;
                $data['tgl_pengiriman'] = now();
                $data['created_by'] = Auth::id();
                $createdData[] = PengirimanDarahProlis::create($data);
            }
            
            return $createdData;
        });
    }

    public function update($id, array $data)
    {
        $record = PengirimanDarahProlis::findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = PengirimanDarahProlis::findOrFail($id);
        return $record->delete();
    }

    public function deleteByNoPengiriman($noPengiriman)
    {
        return PengirimanDarahProlis::where('no_pengiriman', $noPengiriman)->delete();
    }

    public function getStatistics()
    {
        return [
            'total_pengiriman' => PengirimanDarahProlis::distinct('no_pengiriman')->count('no_pengiriman'),
            'total_kantong' => PengirimanDarahProlis::count(),
            'total_by_golongan' => PengirimanDarahProlis::select('golongan_darah', DB::raw('count(*) as total'))
                ->groupBy('golongan_darah')
                ->get(),
            'recent_pengiriman' => PengirimanDarahProlis::with('petugas')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];
    }
}