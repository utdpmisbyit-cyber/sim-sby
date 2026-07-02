<?php

namespace App\Services;

use App\Models\Serologi;
use App\Models\SerologiDetail;
use Illuminate\Support\Facades\DB;

class SerologiService extends IoService
{
    public function __construct()
    {
        $this->model = new Serologi();
        $this->with = ['petugas', 'pemeriksaSerologi', 'diputarOleh', 'diperiksaOleh', 'disahkanOleh', 'jenisPeriksaSerologi', 'metodeSerologi', 'reagenSerologi', 'details'];
        $this->sort_by = ['tanggal' => 'desc', 'created_at' => 'desc'];
        $this->filters = ['nomor', 'tanggal', 'jenis_periksa_serologi_id', 'metode_serologi_id', 'reagen_serologi_id', 'no_lot_reagen', 'tanggal_expired_reagen', 'group', 'petugas_id', 'pemeriksa_serologi_id', 'diputar_oleh_id', 'diperiksa_oleh_id', 'disahkan_oleh_id', 'status'];
    }

    public function dynamic_search($model, $params = [])
    {
        $keyword = trim($params['keyword'] ?? '');
        if ($keyword !== '') {
            $model = $model->where(function ($q) use ($keyword) {
                $q->where('nomor', 'like', '%' . $keyword . '%')
                    ->orWhere('group', 'like', '%' . $keyword . '%')
                    ->orWhere('no_lot_reagen', 'like', '%' . $keyword . '%')
                    ->orWhereHas('jenisPeriksaSerologi', fn($j) => $j->where('nama', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('metodeSerologi', fn($m) => $m->where('nama', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('reagenSerologi', fn($r) => $r->where('nama', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('petugas', fn($p) => $p->where('nama', 'like', '%' . $keyword . '%')->orWhere('kode', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('pemeriksaSerologi', fn($p) => $p->where('nama', 'like', '%' . $keyword . '%')->orWhere('kode', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('diputarOleh', fn($p) => $p->where('nama', 'like', '%' . $keyword . '%')->orWhere('kode', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('diperiksaOleh', fn($p) => $p->where('nama', 'like', '%' . $keyword . '%')->orWhere('kode', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('disahkanOleh', fn($p) => $p->where('nama', 'like', '%' . $keyword . '%')->orWhere('kode', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('details', fn($d) => $d->where('no_kantong', 'like', '%' . $keyword . '%'));
            });
        }

        return $model;
    }

    public function filter_params($params, $id = '')
    {
        $params['status'] = $params['status'] ?? 'pending';
        $params = $this->cleanDate($params, ['tanggal', 'tanggal_expired_reagen']);

        return $params;
    }

    public function generateNomor(): string
    {
        return DB::transaction(function () {
            $prefix = 'SRL' . date('ymd');
            $last = $this->model
                ->newQuery()
                ->lockForUpdate()
                ->where('nomor', 'like', $prefix . '%')
                ->orderByDesc('nomor')
                ->first();

            if (!$last) {
                return $prefix . '001';
            }

            $lastNo = (int) substr($last->nomor, -3);
            return $prefix . str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    public function generateNoLot(): string
    {
        return DB::transaction(function () {
            $prefix = date('Ymd');
            $last = $this->model
                ->newQuery()
                ->lockForUpdate()
                ->where('no_lot_reagen', 'like', $prefix . '%')
                ->orderByDesc('no_lot_reagen')
                ->first();

            if (!$last) {
                return $prefix . '001';
            }

            $lastNo = (int) substr($last->no_lot_reagen, -3);
            return $prefix . str_pad($lastNo + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    public function generateGroupCode(): string
    {
        return 'GRP' . date('ymdHis');
    }

    public function storeGrouped(array $params): array
    {
        return DB::transaction(function () use ($params) {
            $nomors = $params['nomor_list'] ?? [];
            $jenisPeriksaList = $params['jenis_periksa_serologi_id_list'] ?? [];
            $metodeList = $params['metode_serologi_id_list'] ?? [];
            $reagenList = $params['reagen_serologi_id_list'] ?? [];
            $noLotList = $params['no_lot_reagen_list'] ?? [];
            $tanggalExpiredList = $params['tanggal_expired_reagen_list'] ?? [];
            $groupCode = $params['group'] ?? $this->generateGroupCode();

            $created = [];
            foreach ($nomors as $i => $nomor) {
                $nomor = trim((string) $nomor);
                $jenisPeriksaId = (int) ($jenisPeriksaList[$i] ?? 0);
                $metodeId = (int) ($metodeList[$i] ?? 0);
                $reagenId = (int) ($reagenList[$i] ?? 0);
                $noLot = $noLotList[$i] ?? null;
                if (empty($noLot)) {
                    $noLot = $this->generateNoLot();
                }
                $tanggalExpired = !empty($tanggalExpiredList[$i]) ? unformatDate($tanggalExpiredList[$i]) : null;

                if ($jenisPeriksaId <= 0 || $metodeId <= 0 || $reagenId <= 0) {
                    continue;
                }
                if ($nomor === '') {
                    $nomor = $this->generateNomor();
                }

                $created[] = $this->model->create([
                    'nomor' => $nomor,
                    'tanggal' => !empty($params['tanggal']) ? unformatDate($params['tanggal']) : date('Y-m-d'),
                    'jenis_periksa_serologi_id' => $jenisPeriksaId,
                    'metode_serologi_id' => $metodeId,
                    'reagen_serologi_id' => $reagenId,
                    'no_lot_reagen' => $noLot,
                    'tanggal_expired_reagen' => $tanggalExpired,
                    'group' => $groupCode,
                    'petugas_id' => $params['petugas_id'] ?? null,
                    'pemeriksa_serologi_id' => $params['pemeriksa_serologi_id'] ?? null,
                    'diputar_oleh_id' => $params['diputar_oleh_id'] ?? null,
                    'diperiksa_oleh_id' => $params['diperiksa_oleh_id'] ?? null,
                    'disahkan_oleh_id' => $params['disahkan_oleh_id'] ?? null,
                    'status' => 'pending',
                ]);
            }

            return [
                'group' => $groupCode,
                'created_ids' => collect($created)->pluck('id')->values()->toArray(),
                'id' => $created[0]->id ?? null,
                'count' => count($created),
            ];
        });
    }

    public function duplicateTransaction(int $id, array $params = [])
    {
        return DB::transaction(function () use ($id, $params) {
            $source = $this->model->with('details')->findOrFail($id);

            $new = $this->model->create([
                'nomor' => $params['nomor'] ?? $this->generateNomor(),
                'tanggal' => !empty($params['tanggal']) ? unformatDate($params['tanggal']) : ($source->tanggal ? $source->tanggal->format('Y-m-d') : date('Y-m-d')),
                'jenis_periksa_serologi_id' => $params['jenis_periksa_serologi_id'] ?? $source->jenis_periksa_serologi_id,
                'metode_serologi_id' => $params['metode_serologi_id'] ?? $source->metode_serologi_id,
                'reagen_serologi_id' => $params['reagen_serologi_id'] ?? $source->reagen_serologi_id,
                'no_lot_reagen' => $params['no_lot_reagen'] ?? $source->no_lot_reagen,
                'tanggal_expired_reagen' => !empty($params['tanggal_expired_reagen']) ? unformatDate($params['tanggal_expired_reagen']) : ($source->tanggal_expired_reagen ? $source->tanggal_expired_reagen->format('Y-m-d') : null),
                'group' => $params['group'] ?? $this->generateGroupCode(),
                'petugas_id' => $params['petugas_id'] ?? $source->petugas_id,
                'pemeriksa_serologi_id' => $params['pemeriksa_serologi_id'] ?? $source->pemeriksa_serologi_id,
                'diputar_oleh_id' => $params['diputar_oleh_id'] ?? $source->diputar_oleh_id,
                'diperiksa_oleh_id' => $params['diperiksa_oleh_id'] ?? $source->diperiksa_oleh_id,
                'disahkan_oleh_id' => $params['disahkan_oleh_id'] ?? $source->disahkan_oleh_id,
                'status' => 'pending',
            ]);

            foreach ($source->details as $detail) {
                SerologiDetail::create([
                    'serologi_id' => $new->id,
                    'aftap_id' => $detail->aftap_id,
                    'no_kantong' => $detail->no_kantong,
                    'status' => 'pending',
                    'hasil' => null,
                    'keterangan' => null,
                ]);
            }

            return $new->load(['petugas', 'pemeriksaSerologi', 'diputarOleh', 'diperiksaOleh', 'disahkanOleh', 'jenisPeriksaSerologi', 'metodeSerologi', 'reagenSerologi', 'details']);
        });
    }

    public function syncStatus(int $serologiId): void
    {
        $serologi = $this->model->with('details')->find($serologiId);
        if (!$serologi) {
            return;
        }

        $details = $serologi->details;
        if ($details->isEmpty()) {
            $serologi->update(['status' => 'pending']);
            return;
        }

        $allDone = $details->every(function ($detail) {
            return ($detail->status ?? 'pending') !== 'pending';
        });

        if ($allDone) {
            $serologi->update(['status' => 'selesai']);
            return;
        }

        $hasResult = $details->contains(function ($detail) {
            return !empty($detail->hasil) || ($detail->status ?? 'pending') !== 'pending';
        });

        $serologi->update(['status' => $hasResult ? 'proses' : 'pending']);
    }
}
