<?php

namespace App\Services;

use App\Models\PermintaanDarahExternal;
use App\Models\PermintaanDarahExternalDetail;
use App\Models\JenisBiaya;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermintaanDarahExternalService
{
    public function getAll(array $filters = [])
    {
        $query = PermintaanDarahExternal::with('details');
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nomor_permintaan', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('nama_peminta', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('institusi_lain', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        return $query->orderBy('created_at', 'desc')->paginate(15);
    }
     public function getJenisBiaya()
    {
        return JenisBiaya::select('id', 'kode', 'nama')
            ->orderBy('nama')
            ->get();
    }
    public function findById($id)
    {
        return PermintaanDarahExternal::with('details')->findOrFail($id);
    }

    public function findByNomor($nomor)
    {
        return PermintaanDarahExternal::with('details')->where('nomor_permintaan', $nomor)->firstOrFail();
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $data['nomor_permintaan'] = PermintaanDarahExternal::generateNomorPermintaan();
            $data['tanggal'] = $data['tanggal'] ?? date('Y-m-d');
            $data['petugas'] = $data['petugas'] ?? auth()->user()->name ?? 'SYSTEM';
            
            $permintaan = PermintaanDarahExternal::create($data);
            
            if (!empty($data['details'])) {
                foreach ($data['details'] as $detail) {
                    $permintaan->details()->create($detail);
                }
            }
            
            DB::commit();
            return $permintaan->load('details');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat permintaan: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, array $data)
{
    DB::beginTransaction();

    try {
        $permintaan = PermintaanDarahExternal::findOrFail($id);

        // update header
        $permintaan->update([
            'nama_peminta'   => $data['nama_peminta'] ?? $permintaan->nama_peminta,
            'petugas'        => $data['petugas'] ?? $permintaan->petugas,
            'petugas_kode'   => $data['petugas_kode'] ?? $permintaan->petugas_kode,
            'institusi_lain' => $data['institusi_lain'] ?? $permintaan->institusi_lain,
            'jenis_biaya'    => $data['jenis_biaya'] ?? $permintaan->jenis_biaya,
            'dropping'       => $data['dropping'] ?? $permintaan->dropping,
            'tanggal'        => $data['tanggal_perlu'] ?? $permintaan->tanggal_perlu,
            'keterangan'     => $data['keterangan'] ?? $permintaan->keterangan,
        ]);

        if (isset($data['details'])) {

            // hapus detail lama
            PermintaanDarahExternalDetail::where(
                'permintaan_id',
                $permintaan->id
            )->delete();

            // insert ulang detail
            foreach ($data['details'] as $detail) {

                PermintaanDarahExternalDetail::create([
                    'permintaan_id'   => $permintaan->id,
                    'jenis_darah'     => $detail['jenis_darah'],
                    'gol_darah'       => $detail['gol_darah'],
                    'rhesus'          => $detail['rhesus'],
                    'jumlah'          => $detail['jumlah'],
                    'donor_pengganti' => $detail['donor_pengganti'],
                    'no_fpup'         => $detail['no_fpup'] ?? null,
                    'fpup_id'         => $detail['fpup_id'] ?? null,
                    'tanggal_perlu'  => $data['tanggal_perlu'] ?? $permintaan->tanggal_perlu,
                    'keterangan'      => $detail['keterangan'] ?? null,
                ]);
            }
        }

        DB::commit();

        return $permintaan->load('details');

    } catch (\Exception $e) {

        DB::rollBack();
        throw $e;
    }
}

    public function delete($id)
    {
        $permintaan = $this->findById($id);
        return $permintaan->delete();
    }

    public function updateStatus(PermintaanDarahExternal $permintaan)
    {
        $total = $permintaan->details->sum('jumlah');
        $totalDipenuhi = $permintaan->details->sum('jumlah_dipenuhi');
        
        if ($totalDipenuhi >= $total) {
            $permintaan->status = 'SUDAH_DIPENUHI';
        } elseif ($totalDipenuhi > 0) {
            $permintaan->status = 'SEBAGIAN';
        } else {
            $permintaan->status = 'BELUM_DIPENUHI';
        }
        $permintaan->save();
    }

    public function updatePemenuhan($detailId, $jumlahDipenuhi)
    {
        $detail = PermintaanDarahExternalDetail::findOrFail($detailId);
        $detail->jumlah_dipenuhi = min($jumlahDipenuhi, $detail->jumlah);
        $detail->save();
        
        $this->updateStatus($detail->permintaan);
        return $detail;
    }
}