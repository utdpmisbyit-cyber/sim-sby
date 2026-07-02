<?php

namespace App\Services;

use App\Models\PengirimanDarahProlis;
use App\Models\JenisDarah;
use App\Models\RencanaProduksiDetail;
use App\Models\RencanaProduksi;
use App\Models\PengirimanSample;
use App\Models\PengirimanSampleDetail;
use App\Models\Donor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PengirimanDarahProlisService
{
    
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return PengirimanDarahProlis::query()
            ->with(['petugas', 'asalDarah'])
            ->search($filters['keyword'] ?? null)
            ->filterJenis($filters['jenis_darah'] ?? null)
            ->filterGolongan($filters['golongan_darah'] ?? null)
            ->filterTglPengiriman($filters['tgl_dari'] ?? null, $filters['tgl_sampai'] ?? null)
            ->when(!empty($filters['no_kantong']),    fn($q) => $q->where('no_kantong', 'like', '%'.$filters['no_kantong'].'%'))
            ->when(!empty($filters['no_stok']),       fn($q) => $q->where('no_stok', 'like', '%'.$filters['no_stok'].'%'))
            ->when(!empty($filters['data_barcode']),  fn($q) => $q->where('no_stok', $filters['data_barcode']))
            ->when(!empty($filters['status']),        fn($q) => $q->where('status', $filters['status']))
            ->orderByDesc('tgl_pengiriman')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): ?PengirimanDarahProlis
    {
        return PengirimanDarahProlis::with(['petugas', 'asalDarah'])->findOrFail($id);
    }

    public function findByNoPengiriman(string $noPengiriman): ?PengirimanDarahProlis
    {
        return PengirimanDarahProlis::where('no_pengiriman', $noPengiriman)->first();
    }

   
   public function store(array $data): ?PengirimanDarahProlis
    {
        try {
            DB::beginTransaction();
            
            // Generate no_pengiriman jika belum ada
            if (empty($data['no_pengiriman'])) {
                $data['no_pengiriman'] = $this->generateNoPengiriman();
            }
            
            // Set created_by
            $data['created_by'] = Auth::id();
            
            // Set default values jika kosong
            if (empty($data['status'])) {
                $data['status'] = '1';
            }
            
            if (empty($data['skrining'])) {
                $data['skrining'] = 'NEG';
            }
            
            if (empty($data['jumlah'])) {
                $data['jumlah'] = '1';
            }
            
            // Simpan data
            $record = PengirimanDarahProlis::create($data);
            
            DB::commit();
            
            Log::info('Data saved successfully', ['id' => $record->id, 'no_pengiriman' => $record->no_pengiriman]);
            
            return $record;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store service error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function update(int $id, array $data): ?PengirimanDarahProlis
    {
        try {
            DB::beginTransaction();
            
            $record = $this->find($id);
            if (!$record) {
                throw new \Exception('Data not found');
            }
            
            $data['updated_by'] = Auth::id();
            $record->update($data);
            
            DB::commit();
            
            Log::info('Data updated successfully', ['id' => $record->id]);
            
            return $record->fresh();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update service error: ' . $e->getMessage(), [
                'id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $record = $this->find($id);
            return (bool) $record->delete();
        });
    }

   
    public function generateNoPengiriman(): string
    {
        $prefix = 'K' . now()->format('y');
        $last   = PengirimanDarahProlis::where('no_pengiriman', 'like', $prefix . '%')
                    ->orderByDesc('no_pengiriman')
                    ->value('no_pengiriman');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 9, '0', STR_PAD_LEFT);
    }

    public function summary(array $filters = []): array
    {
        $base = PengirimanDarahProlis::query()
            ->filterTglPengiriman($filters['tgl_dari'] ?? null, $filters['tgl_sampai'] ?? null);

        return [
            'total'      => (clone $base)->count(),
            'tersedia'   => (clone $base)->where('status', '1')->count(),
            'terpakai'   => (clone $base)->where('status', '2')->count(),
            'kadaluarsa' => (clone $base)->where('status', '3')->count(),
        ];
    }


    public function optionsGolongan(): array
    {
        return ['A', 'B', 'AB', 'O'];
    }
    public function findByKantong(?string $noKantong, ?string $noStok): ?array
{
    try {
        $query = PengirimanSampleDetail::query();
        
        if ($noKantong) {
            $query->where('no_kantong', $noKantong);
        } elseif ($noStok) {
            $query->where('no_kantong', $noStok);
        }
        
        $detail = $query->first();
        
        if (!$detail) {
            return null;
        }
        
        $pengirimanSample = $detail->pengiriman_sample_id 
            ? PengirimanSample::find($detail->pengiriman_sample_id) 
            : null;
        $donor = $detail->donor_id ? Donor::find($detail->donor_id) : null;
        
        $jenisDarahValid = $this->mapJenisDarah($detail->jenis_kantong);
        $today = Carbon::now();
        
        $tglAftap = $detail->tanggal_aftap;
        if (!$tglAftap || $tglAftap == '0000-00-00') {
            $tglAftap = $today;
        }
        
        $tglProduksi = $pengirimanSample ? $pengirimanSample->tanggal_fpd : null;
        if (!$tglProduksi || $tglProduksi == '0000-00-00') {
            $tglProduksi = $today;
        }
        
        $tglExpired = $this->calculateExpiredDate($tglProduksi, $jenisDarahValid);

        // ── Hitung jumlah otomatis ──
        $jumlah = $this->calculateJumlah($detail, $noKantong, $noStok);
        
        return [
            'no_kantong'      => $detail->no_kantong,
            'no_stok'         => $detail->no_kantong,
            'jenis_darah'     => $jenisDarahValid,
            'golongan_darah'  => $detail->gol_darah ?? ($donor ? $donor->golongan_darah : null),
            'rhesus'          => $detail->rhesus ?? ($donor ? $donor->rhesus : null),
            'nama_asal_darah' => $detail->kode_asal_darah ?? ($donor ? $donor->nama : null),
            'tgl_aftap'       => $tglAftap instanceof Carbon ? $tglAftap->format('Y-m-d') : date('Y-m-d', strtotime($tglAftap)),
            'tgl_produksi'    => $tglProduksi instanceof Carbon ? $tglProduksi->format('Y-m-d') : date('Y-m-d', strtotime($tglProduksi)),
            'tgl_expired'     => $tglExpired instanceof Carbon ? $tglExpired->format('Y-m-d') : $tglExpired,
            'no_fpd'          => $pengirimanSample ? (string) $pengirimanSample->no_fpd : null,
            'skrining'        => $donor ? $donor->skrining : 'NEG',
            'suhu'            => $detail->suhu ?? ($pengirimanSample ? $pengirimanSample->suhu : null),
            'jumlah'          => $jumlah,  // ← hasil hitung otomatis
            'gr'              => $this->calculateGr($detail->jenis_kantong),
            'ml'              => $this->calculateMl($detail->jenis_kantong),
            'status'          => '1',
        ];
        
    } catch (\Exception $e) {
        Log::error('Error in findByKantong: ' . $e->getMessage());
        return null;
    }
}

/**
 * Hitung jumlah otomatis:
 * - Ambil dari field jumlah/volume di detail jika ada dan > 0
 * - Hitung dari gr/ml jika jumlah tidak tersedia
 * - Fallback ke '1' jika semua kosong/nol
 */
private function calculateJumlah($detail, ?string $noKantong, ?string $noStok): string
{
    // 1. Cek field jumlah langsung di detail
    $jumlahFields = ['jumlah', 'qty', 'volume', 'banyak'];
    foreach ($jumlahFields as $field) {
        if (isset($detail->$field) && !empty($detail->$field) && (float)$detail->$field > 0) {
            return (string)(int)$detail->$field;
        }
    }

    // 2. Hitung dari ml atau volume jika ada
    $mlFields = ['ml', 'volume_ml', 'isi_ml'];
    foreach ($mlFields as $field) {
        if (isset($detail->$field) && !empty($detail->$field) && (float)$detail->$field > 0) {
            $stdMl = $this->calculateMl($detail->jenis_kantong ?? 'PRC');
            if ((int)$stdMl > 0) {
                $hitungan = (int)round((float)$detail->$field / (int)$stdMl);
                if ($hitungan > 0) return (string)$hitungan;
            }
        }
    }

    // 3. Hitung dari gr jika ada
    $grFields = ['gr', 'gram', 'berat_gr'];
    foreach ($grFields as $field) {
        if (isset($detail->$field) && !empty($detail->$field) && (float)$detail->$field > 0) {
            $stdGr = $this->calculateGr($detail->jenis_kantong ?? 'PRC');
            if ((int)$stdGr > 0) {
                $hitungan = (int)round((float)$detail->$field / (int)$stdGr);
                if ($hitungan > 0) return (string)$hitungan;
            }
        }
    }

    // 4. Hitung dari no_kantong yang sama di tabel pengiriman_darah_prolis
    //    (cek sudah berapa kali kantong ini pernah dikirim, lalu sisa = total - terpakai)
    $totalPernah = \App\Models\PengirimanDarahProlis::where('no_kantong', $noKantong ?? $noStok)
        ->count();
    if ($totalPernah > 0) {
        // Sudah pernah ada, return sisa (minimal 1 untuk form baru)
        return '1';
    }

    // 5. Fallback
    return '1';
}
    
    /**
     * Mapping jenis kantong ke nama_pendek yang ada di tabel jenis_darah
     */
    private function mapJenisDarah($jenisKantong)
    {
        if (empty($jenisKantong)) {
            return 'PRC'; // default
        }
        
        // Mapping dari nilai di database ke nama_pendek
        $mapping = [
            'whole blood' => 'WB',
            'wb' => 'WB',
            'packed red cell' => 'PRC',
            'prc' => 'PRC',
            'fresh frozen plasma' => 'FFP',
            'ffp' => 'FFP',
            'thrombocyte concentrate' => 'TC',
            'tc' => 'TC',
            'leukocyte poor' => 'LP',
            'lp' => 'LP',
            'apheresis' => 'AP',
            'ap' => 'AP',
            'cryoprecipitate' => 'CRYO',
            'cryo' => 'CRYO',
        ];
        
        $jenisLower = strtolower(trim($jenisKantong));
        
        if (isset($mapping[$jenisLower])) {
            return $mapping[$jenisLower];
        }
        
        // Cek apakah sudah dalam format yang benar (WB, PRC, dll)
        $validTypes = ['WB', 'PRC', 'FFP', 'TC', 'LP', 'AP', 'CRYO'];
        if (in_array(strtoupper($jenisKantong), $validTypes)) {
            return strtoupper($jenisKantong);
        }
        
        return 'PRC'; // default jika tidak dikenal
    }
    
    private function calculateGr($jenisKantong)
    {
        $jenis = strtoupper($jenisKantong ?? 'PRC');
        $standard = ['WB' => '450', 'PRC' => '250', 'FFP' => '200', 'TC' => '50', 'LP' => '250', 'AP' => '400', 'CRYO' => '150'];
        return $standard[$jenis] ?? '250';
    }
    
    private function calculateMl($jenisKantong)
    {
        $jenis = strtoupper($jenisKantong ?? 'PRC');
        $standard = ['WB' => '450', 'PRC' => '250', 'FFP' => '200', 'TC' => '50', 'LP' => '250', 'AP' => '400', 'CRYO' => '150'];
        return $standard[$jenis] ?? '250';
    }
    
    private function calculateExpiredDate($tglProduksi, $jenisKantong)
    {
        if (!$tglProduksi) $tglProduksi = Carbon::now();
        if (!($tglProduksi instanceof Carbon)) $tglProduksi = Carbon::parse($tglProduksi);
        
        $expiredDays = ['WB' => 35, 'PRC' => 42, 'FFP' => 365, 'TC' => 5, 'LP' => 42, 'AP' => 365, 'CRYO' => 365];
        $days = $expiredDays[strtoupper($jenisKantong ?? 'PRC')] ?? 42;
        
        return $tglProduksi->copy()->addDays($days);
    }

   
   

    public function optionsJenis(): array
    {
        // Ambil dari tabel jenis_darah terlebih dahulu
        try {
            $jenisFromDb = JenisDarah::whereNull('deleted_at')
                ->pluck('nama_pendek')
                ->filter()
                ->sort()
                ->values()
                ->toArray();
                
            if (!empty($jenisFromDb)) {
                return $jenisFromDb;
            }
        } catch (\Exception $e) {
            Log::error('Error getting jenis from jenis_darah: ' . $e->getMessage());
        }
        
        // Fallback: ambil dari pengiriman_sample_detail
        try {
            return PengirimanSampleDetail::whereNotNull('jenis_kantong')
                ->distinct()
                ->pluck('jenis_kantong')
                ->filter()
                ->sort()
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error getting jenis from pengiriman_sample_detail: ' . $e->getMessage());
            return ['FFP', 'WB', 'PRC', 'TC', 'LP'];
        }
    }



}