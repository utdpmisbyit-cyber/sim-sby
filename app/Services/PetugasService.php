<?php

namespace App\Services;

use App\Models\Petugas;

class PetugasService extends IoService
{
    public function __construct()
    {
        $this->model = new Petugas();
        $this->sort_by = ['nama' => 'asc'];
        $this->filters = ['kode', 'cabang_id', 'jabatan_id', 'bagian_id', 'program_kerja_id'];
    }

    public function dynamic_search($model, $params = [])
    {
        $nama = $params['nama'] ?? '';
        if ($nama !== '') $model = $model->where('nama', 'like', '%' . $nama . '%');

        $nama_jabatan = $params['nama_jabatan'] ?? '';
        if ($nama_jabatan !== '') $model = $model->whereHas('jabatan', fn($jabatan) => $jabatan->where('nama', $nama_jabatan));

        $nama_bagian = $params['nama_bagian'] ?? '';
        if ($nama_bagian !== '') $model = $model->whereHas('bagian', fn($bagian) => $bagian->where('nama', $nama_bagian));

        return $model;
    }

    public function filter_params($params, $id = '')
    {
        $password = $params['password'] ?? '';
        if ($password !== '') $params['password'] = bcrypt($password);
        else unset($params['password']);

        return $params;
    }
}
