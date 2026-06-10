<?php

namespace App\Services;

use App\Models\RencanaProduksi;

class RencanaProduksiService extends IoService
{
    public function __construct()
    {
        $this->model = new RencanaProduksi();
        $this->with = ['pengirimanSample', 'tipeKantong', 'petugas', 'details'];
        $this->sort_by = ['tanggal' => 'desc', 'created_at' => 'desc'];
        $this->filters = ['pengiriman_sample_id', 'tanggal', 'petugas_id', 'tipe_kantong_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $keyword = trim($params['keyword'] ?? '');
        if ($keyword !== '') {
            $model = $model->where(function ($q) use ($keyword) {
                $q->where('tanggal', 'like', '%' . $keyword . '%')
                    ->orWhereHas('pengirimanSample', fn($ps) => $ps->where('no_fpd', 'like', '%' . $keyword . '%'))
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
        if (!empty($params['pengiriman_sample_id'])) {
            $ps = \App\Models\PengirimanSample::find($params['pengiriman_sample_id']);
            if ($ps && !empty($ps->type_kantong)) {
                $tk = \App\Models\TipeKantong::where('nama', $ps->type_kantong)->first();
                if ($tk) {
                    $params['tipe_kantong_id'] = $tk->id;
                }
            }
        }
        return $params;
    }
}
