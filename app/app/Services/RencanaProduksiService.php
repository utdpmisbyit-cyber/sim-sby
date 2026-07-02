<?php

namespace App\Services;

use App\Models\RencanaProduksi;

class RencanaProduksiService extends IoService
{
    public function __construct()
    {
        $this->model = new RencanaProduksi();
        $this->with = ['pengirimanAftap', 'tipeKantong', 'petugas', 'details'];
        $this->sort_by = ['tanggal' => 'desc', 'created_at' => 'desc'];
        $this->filters = ['pengiriman_aftap_id', 'tanggal', 'petugas_id', 'tipe_kantong_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $keyword = trim($params['keyword'] ?? '');
        if ($keyword !== '') {
            $model = $model->where(function ($q) use ($keyword) {
                $q->where('tanggal', 'like', '%' . $keyword . '%')
                    ->orWhereHas('pengirimanAftap', fn($pa) => $pa->where('no_pengiriman', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('tipeKantong', fn($tk) => $tk->where('nama', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('petugas', fn($p) => $p->where('nama', 'like', '%' . $keyword . '%')->orWhere('kode', 'like', '%' . $keyword . '%'))
                    ->orWhereHas('details', fn($d) => $d->where('no_kantong', 'like', '%' . $keyword . '%')->orWhere('no_satelit', 'like', '%' . $keyword . '%'));
            });
        }

        return $model;
    }

    public function filter_params($params, $id = '')
    {
        $params = $this->cleanDate($params, ['tanggal']);
        if (!empty($params['pengiriman_aftap_id'])) {
            $pa = \App\Models\PengirimanAftap::find($params['pengiriman_aftap_id']);
            if ($pa && $pa->details()->exists()) {
                $firstDetail = $pa->details()->first();
                $penyimpanan = $firstDetail->penyimpanan_kantong;
                if ($penyimpanan) {
                    $params['tipe_kantong_id'] = $penyimpanan->tipe_kantong_id;
                }
            }
        }
        return $params;
    }
}
