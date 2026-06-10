<?php

namespace App\Services;

use App\Models\HakAkses;
use Illuminate\Support\Facades\DB;

class IoService extends Service
{
    protected $model;
    protected $filters = [];
    protected $sort_by = [];
    protected $with = [];
    protected $appends = [];

    public function search($params = [])
    {
        $model = $this->model->newQuery();
        if (count($this->sort_by) > 0) {
            foreach ($this->sort_by as $column => $direction) {
                $model = $model->orderBy($column, $direction);
            }
        }
        if (!empty($params['with'])) {
            $model = $model->with($params['with']);
            unset($params['with']); // penting agar tidak ikut filter
        }
        if (count($this->with) > 0) $params['with'] = $this->with;
        if (count($this->appends) > 0) $params['append'] = $this->appends;
        $model = $this->dynamic_search($model, $params);
        $model = $this->searchFilter($params, $model, $this->filters);
        return $this->searchResponse($params, $model);
    }

    public function dynamic_search($model, $params = [])
    {
        return $model;
    }

    public function find($value, $column = 'id')
    {
        return $this->model->where($column, $value)->first();
    }

    public function filter_params($params, $id = '')
    {
        return $params;
    }

    public function store($params)
    {
        $params = $this->filter_params($params);
        if (empty($params['kode']) && in_array('kode', $this->model->getFillable())) {
            $params['kode'] = $this->generateKode();
        }
        return $this->model->create($params);
    }

    public function update($params, $id)
    {
        $params = $this->filter_params($params, $id);
        $model = $this->model->find($id);
        if (!empty($model)) $model->update($params);
        return $model;
    }

    public function delete($id)
    {
        $model = $this->model->find($id);
        if (!empty($model)) {
            try { $model->delete(); } catch (\Exception $e) { return ['error' => 'Delete failed! This data currently being used']; }
        }
        return $model;
    }

    public function restore($id)
    {
        $model = $this->model->withTrashed()->where('id', $id)->first();
        if (!empty($model)) $model->restore();
        return $model;
    }

    public function dropdown($params = []): array
    {
        $data = $this->search($params);
        return array_combine($data->pluck('id')->toArray(), $data->pluck('nama')->toArray());
    }

    public function generateKode(): string
    {
        return DB::transaction(function () {
            $last = $this->model->newQuery()->lockForUpdate()->orderByDesc('kode')->withTrashed()->first();
            if (!$last) return '0001';
            $nextNumber = (int) $last->kode + 1;
            return str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
