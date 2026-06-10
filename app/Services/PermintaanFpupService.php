<?php

namespace App\Services;

use App\Models\Diagnosa;
use App\Models\JenisBiaya;
use App\Models\PermintaanFpup;
use App\Models\PermintaanFpupDetail;
use Illuminate\Support\Facades\DB;

class PermintaanFpupService
{
    
    public function generateNoFpup(): string
    {
        $prefix = 'C' . now()->format('ymd');

        $last = PermintaanFpup::withTrashed()
            ->where('no_fpup', 'like', $prefix . '%')
            ->orderBy('no_fpup', 'desc')
            ->value('no_fpup');

        $seq = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function store(array $data): PermintaanFpup
    {
        return DB::transaction(function () use ($data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['no_fpup']   = $this->generateNoFpup();
            $data['tgl_minta'] = now()->toDateString();
            $data['jam_minta'] = now()->format('H:i:s');
            $data['status']    = 'baru';

            $data['no_reg'] = $data['no_reg']
                ?? $this->generateNoRegistrasi();

            $data['no_reg_online'] = $data['no_reg_online']
                ?? $this->generateNoRegistrasiOnline();

            $data['tgl_registrasi_online'] = $data['tgl_registrasi_online']
                ?? now()->toDateString();
           
            foreach (['transfusi_sebelumnya', 'reaksi_transfusi', 'pernah_serologi', 'hdn', 'pasien_referal', 'cetak_barcode'] as $field) {
                if (array_key_exists($field, $data)) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $fpup = PermintaanFpup::create($data);

            foreach ($details as $d) {
                $fpup->details()->create($d);
            }

            return $fpup->load('details');
        });
    }


    public function update(PermintaanFpup $fpup, array $data): PermintaanFpup
    {
        return DB::transaction(function () use ($fpup, $data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            foreach (['transfusi_sebelumnya', 'reaksi_transfusi', 'pernah_serologi', 'hdn', 'pasien_referal', 'cetak_barcode'] as $field) {
                if (array_key_exists($field, $data)) {
                    $data[$field] = filter_var($data[$field], FILTER_VALIDATE_BOOLEAN);
                }
            }

            $fpup->update($data);

            // Sync details: delete old, insert new
            $fpup->details()->delete();
            foreach ($details as $d) {
                $fpup->details()->create($d);
            }

            return $fpup->load('details');
        });
    }

    

    public function destroy(PermintaanFpup $fpup): void
    {
        DB::transaction(fn () => $fpup->delete());
    }

  

    public function list(array $filters = [])
    {
        $q = PermintaanFpup::with('details')
            ->orderBy('created_at', 'desc');

        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $q->where(function ($qb) use ($s) {
                $qb->where('no_fpup',     'like', "%$s%")
                   ->orWhere('nama_pasien', 'like', "%$s%")
                   ->orWhere('nama_rs',     'like', "%$s%")
                   ->orWhere('no_reg',      'like', "%$s%");
            });
        }

        if (! empty($filters['status'])) {
            $q->where('status', $filters['status']);
        }

        if (! empty($filters['tgl'])) {
            $q->whereDate('tgl_minta', $filters['tgl']);
        }

        return $q->paginate($filters['per_page'] ?? 15);
    }

   

    public function constants(): array
    {
        // Diagnosa dari tabel DB; fallback ke konstanta jika tabel kosong
        $diagnosaDb = Diagnosa::orderBy('nama')->pluck('nama')->toArray();
        $diagnosaList = ! empty($diagnosaDb) ? $diagnosaDb : PermintaanFpup::DIAGNOSA;

        return [
            'jenis_rs'       => PermintaanFpup::JENIS_RS,
            'kategori_rs'    => PermintaanFpup::KATEGORI_RS,
            'bagian'         => PermintaanFpup::BAGIAN,
            'kelas_rawat'    => PermintaanFpup::KELAS_RAWAT,
            'jns_permintaan' => PermintaanFpup::JNS_PERMINTAAN,
            'diagnosa'       => $diagnosaList,
            'cara_bayar'     => PermintaanFpup::CARA_BAYAR,
            'jns_biaya'      => JenisBiaya::orderBy('nama')
                               ->pluck('nama')
                                ->toArray(),
            'jns_donor'      => PermintaanFpup::JNS_DONOR,
            'gol_darah'      => PermintaanFpup::GOL_DARAH,
            'rhesus'         => PermintaanFpup::RHESUS,
            'jns_darah'      => PermintaanFpup::JNS_DARAH,
            'kebangsaan'     => PermintaanFpup::KEBANGSAAN,
            'status_list'    => PermintaanFpup::STATUS,
        ];
    }
    public function generateNoRegistrasi(): string
    {
        $prefix = 'REG' . now()->format('ymd');

        $last = PermintaanFpup::withTrashed()
            ->where('no_reg', 'like', $prefix . '%')
            ->orderBy('no_reg', 'desc')
            ->value('no_reg');

        $seq = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generateNoRegistrasiOnline(): string
    {
        $prefix = 'ONL' . now()->format('ymd');

        $last = PermintaanFpup::withTrashed()
            ->where('no_reg_online', 'like', $prefix . '%')
            ->orderBy('no_reg_online', 'desc')
            ->value('no_reg_online');

        $seq = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}