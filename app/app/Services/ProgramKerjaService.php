<?php

namespace App\Services;

use App\Models\ProgramKerja;

class ProgramKerjaService extends IoService
{
    public function __construct()
    {
        $this->model = new ProgramKerja();
        $this->sort_by = ['nama_program' => 'asc'];
        $this->filters = ['kode', 'pic_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama_program = $params['nama_program'] ?? '';
        if ($nama_program !== '') $model = $model->where('nama_program', 'like', '%' . $nama_program . '%');
        return $model;
    }
}
