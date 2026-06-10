<?php

namespace App\Services;

use App\Models\Aftap;
use App\Models\SerologiDetail;

class SerologiDetailService extends IoService
{
    public function __construct()
    {
        $this->model = new SerologiDetail();
        $this->with = ['aftap.donor'];
        $this->filters = ['serologi_id', 'aftap_id', 'status', 'no_kantong'];
        $this->sort_by = ['id' => 'asc'];
    }

    public function filter_params($params, $id = '')
    {
        if (!empty($params['no_kantong'])) {
            $params['no_kantong'] = trim((string) $params['no_kantong']);
        }

        if (!array_key_exists('status', $params) || $params['status'] === '') {
            $params['status'] = !empty($params['hasil']) ? 'selesai' : 'pending';
        }

        if (!empty($params['hasil']) && ($params['status'] ?? '') === 'pending') {
            $params['status'] = 'selesai';
        }

        if (isset($params['keterangan']) && $params['keterangan'] === null) {
            $params['keterangan'] = '';
        }

        return $params;
    }

    public function getAftapByNoKantong(string $noKantong): ?Aftap
    {
        $noKantong = trim($noKantong);
        if ($noKantong === '') {
            return null;
        }

        return Aftap::query()
            ->with('donor')
            ->where('no_kantong', $noKantong)
            ->first();
    }

    public function splitBulkNoKantong(string $bulkInput): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $bulkInput) ?: [];
        $codes = [];
        foreach ($lines as $line) {
            $code = trim($line);
            if ($code !== '') {
                $codes[] = $code;
            }
        }

        return array_values(array_unique($codes));
    }
}
