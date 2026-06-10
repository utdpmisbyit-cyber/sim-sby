<?php

namespace App\Services;

use App\Models\PengembalianKantong;

class PengembalianKantongService extends IoService
{
    public function __construct()
    {
        $this->model   = new PengembalianKantong();
        $this->sort_by = [
            'tgl_kembali' => 'desc',
            'no_kembali'  => 'desc'
        ];

        $this->filters = [
            'no_kembali',
            'no_kantong',
            'kondisi',
            'tgl_kembali'
        ];

        $this->with = [
            'stokKantong',
            'details.tipe_kantong'
        ];
    }

    public function search($params = [])
    {
        $query = PengembalianKantong::query()
            ->with($this->with);

        $query = $this->dynamic_search($query, $params);

        return $query
            ->orderBy('tgl_kembali', 'desc')
            ->orderBy('no_kembali', 'desc')
            ->paginate($params['per_page'] ?? 10)
            ->withQueryString();
    }

    public function dynamic_search($model, $params = [])
    {
        $no_kembali  = $params['no_kembali'] ?? '';
        $no_kantong  = $params['no_kantong'] ?? '';
        $kondisi     = $params['kondisi'] ?? '';
        $tgl_kembali = $params['tgl_kembali'] ?? '';

        if ($no_kembali !== '') {
            $model->where(
                'no_kembali',
                'like',
                '%' . $no_kembali . '%'
            );
        }

        if ($no_kantong !== '') {
            $model->where(
                'no_kantong',
                'like',
                '%' . $no_kantong . '%'
            );
        }

        if ($kondisi !== '') {
            $model->where('kondisi', $kondisi);
        }

        if ($tgl_kembali !== '') {
            $model->whereDate('tgl_kembali', $tgl_kembali);
        }

        return $model;
    }

    /**
     * Generate no_kembali
     * KB + YYMM + 6 digit sequence
     * ex: KB2505000001
     */
    public function generateNoKembali(): string
    {
        $prefix = 'KB' . now()->format('ym');

        $last = PengembalianKantong::where(
            'no_kembali',
            'like',
            $prefix . '%'
        )
            ->orderByDesc('no_kembali')
            ->value('no_kembali');

        $seq = $last
            ? ((int) substr($last, strlen($prefix))) + 1
            : 1;

        return $prefix . str_pad(
            $seq,
            6,
            '0',
            STR_PAD_LEFT
        );
    }
}