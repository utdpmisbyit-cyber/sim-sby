<?php

namespace App\Services;

use App\Models\OpnameDarah;
use App\Models\OpnameDarahDetail;
use App\Models\StokDarah;
use App\Models\BagianPetugas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OpnameDarahService
{
 
    public function generateNoOpname(): string
    {
        $prefix = 'OPN-' . now()->format('Ym') . '-';
        $last   = OpnameDarah::withTrashed()
            ->where('no_opname', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('no_opname');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }


    public function getData(array $filters = [])
{
    $q = OpnameDarah::with(['petugas', 'lokasiOpname'])
        ->withCount('detail')                          // ← tambah ini
        ->when($filters['search'] ?? null, function ($q, $s) {
            $q->where(function ($q) use ($s) {
                $q->where('no_opname', 'like', "%$s%")
                  ->orWhereHas('lokasiOpname', fn($q) => $q->where('nama', 'like', "%$s%"))
                  ->orWhere('lokasi_opname', 'like', "%$s%");
            });
        })
        ->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))
        ->when($filters['tgl_dari'] ?? null, fn($q, $v) => $q->whereDate('tgl_opname', '>=', $v))
        ->when($filters['tgl_sampai'] ?? null, fn($q, $v) => $q->whereDate('tgl_opname', '<=', $v))
        ->orderByDesc('tgl_opname')
        ->orderByDesc('id');

    return $q->paginate($filters['per_page'] ?? 15);
}

 
    public function store(array $data): OpnameDarah
    {
        return DB::transaction(function () use ($data) {
            $bagian = null;

            if (!empty($data['lokasi_opname_id'])) {
                $bagian = BagianPetugas::find($data['lokasi_opname_id']);
            }

            $opname = OpnameDarah::create([
                'no_opname'     => $this->generateNoOpname(),
                'tgl_opname'    => $data['tgl_opname'],
                'lokasi_opname' => $bagian?->nama,
                'lokasi_opname_id' => $bagian?->id,
                'keterangan'    => $data['keterangan'] ?? null,
                'petugas_id'    => $data['petugas_id'] ?? null,
                'created_by'    => Auth::id(),
                'status'        => 'draft',
            ]);

            if (!empty($data['detail'])) {
                foreach ($data['detail'] as $item) {
                    $stok = StokDarah::where('no_stok', $item['no_stok'])->first();

                    $jumlahSistem = $stok?->saldo ?? 0;
                    $jumlahFisik  = (int) ($item['jumlah_fisik'] ?? 0);

                    $opname->detail()->create([
                        'no_stok'        => $item['no_stok'],
                        'stok_darah_id'  => $stok?->id,
                        'jenis_darah'    => $item['jenis_darah']   ?? $stok?->jenis_darah,
                        'golongan_darah' => $item['golongan_darah'] ?? $stok?->golongan_darah,
                        'rhesus'         => $item['rhesus']        ?? $stok?->rhesus,
                        'tgl_kadaluarsa' => $item['tgl_kadaluarsa'] ?? $stok?->tgl_expired,
                        'jumlah_sistem'  => $jumlahSistem,
                        'jumlah_fisik'   => $jumlahFisik,
                        'selisih'        => $jumlahFisik - $jumlahSistem,
                        'keterangan'     => $item['keterangan'] ?? null,
                    ]);
                }
            }

            return $opname->load('detail.stokDarah', 'petugas');
        });
    }

  
    public function update(OpnameDarah $opname, array $data): OpnameDarah
    {
        abort_if($opname->status === 'selesai', 422, 'Opname yang sudah selesai tidak dapat diubah.');

        return DB::transaction(function () use ($opname, $data) {
            $bagian = null;

            if (!empty($data['lokasi_opname_id'])) {
                $bagian = \App\Models\BagianPetugas::find($data['lokasi_opname_id']);
            }

            $opname->update([
                'tgl_opname'    => $data['tgl_opname']    ?? $opname->tgl_opname,
                'lokasi_opname' => $bagian?->nama ?? $opname->lokasi_opname,
                'lokasi_opname_id' => $bagian?->id ?? $opname->lokasi_opname_id,
                'keterangan'    => $data['keterangan']    ?? $opname->keterangan,
                'petugas_id'    => $data['petugas_id']    ?? $opname->petugas_id,
            ]);

            if (isset($data['detail'])) {
                // Sync detail: hapus lama, buat ulang
                $opname->detail()->delete();

                foreach ($data['detail'] as $item) {
                    $stok = StokDarah::where('no_stok', $item['no_stok'])->first();

                    $jumlahSistem = $stok?->saldo ?? 0;
                    $jumlahFisik  = (int) ($item['jumlah_fisik'] ?? 0);

                    $opname->detail()->create([
                        'no_stok'        => $item['no_stok'],
                        'stok_darah_id'  => $stok?->id,
                        'jenis_darah'    => $item['jenis_darah']    ?? $stok?->jenis_darah,
                        'golongan_darah' => $item['golongan_darah'] ?? $stok?->golongan_darah,
                        'rhesus'         => $item['rhesus']         ?? $stok?->rhesus,
                        'tgl_kadaluarsa' => $item['tgl_kadaluarsa'] ?? $stok?->tgl_expired,
                        'jumlah_sistem'  => $jumlahSistem,
                        'jumlah_fisik'   => $jumlahFisik,
                        'selisih'        => $jumlahFisik - $jumlahSistem,
                        'keterangan'     => $item['keterangan'] ?? null,
                    ]);
                }
            }

            return $opname->fresh('detail.stokDarah', 'petugas');
        });
    }


    public function selesai(OpnameDarah $opname): OpnameDarah
    {
        abort_if($opname->status === 'selesai', 422, 'Opname sudah berstatus selesai.');
        abort_if($opname->detail()->count() === 0, 422, 'Opname belum memiliki detail.');

        $opname->update([
            'status'      => 'selesai',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return $opname->fresh();
    }

  
    public function show(OpnameDarah $opname): OpnameDarah
    {
        return $opname->load([
         'detail.stokDarah', 
         'petugas',
         'lokasiOpname',
         'createdBy', 'approvedBy']);
    }

 
    public function destroy(OpnameDarah $opname): void
    {
        abort_if($opname->status === 'selesai', 422, 'Opname yang sudah selesai tidak dapat dihapus.');

        DB::transaction(function () use ($opname) {
            $opname->detail()->delete();
            $opname->delete();
        });
    }


    public function cariStok(array $filters = [])
    {
        return StokDarah::query()
            ->when($filters['no_stok'] ?? null, fn($q, $v) => $q->where('no_stok', 'like', "%$v%"))
            ->when($filters['jenis_darah'] ?? null, fn($q, $v) => $q->where('jenis_darah', $v))
            ->when($filters['golongan_darah'] ?? null, fn($q, $v) => $q->where('golongan_darah', $v))
            ->when($filters['rhesus'] ?? null, fn($q, $v) => $q->where('rhesus', $v))
            ->where('status_stok', 'tersedia')
            ->select(['id', 'no_stok', 'jenis_darah', 'golongan_darah', 'rhesus', 'tgl_expired', 'saldo', 'ruang'])
            ->orderBy('tgl_expired')
            ->limit(50)
            ->get();
    }


    public function getSummary(OpnameDarah $opname): array
    {
        $detail = $opname->detail;

        return [
            'total_item'     => $detail->count(),
            'total_sistem'   => $detail->sum('jumlah_sistem'),
            'total_fisik'    => $detail->sum('jumlah_fisik'),
            'total_selisih'  => $detail->sum('selisih'),
            'item_lebih'     => $detail->where('selisih', '>', 0)->count(),
            'item_kurang'    => $detail->where('selisih', '<', 0)->count(),
            'item_sama'      => $detail->where('selisih', 0)->count(),
        ];
    }
}