<?php

namespace App\Services;

class Service
{
    public function searchFilter($params, $model, $filters)
    {
        foreach ($filters as $filter) {
            $value = $params[$filter] ?? '';
            if ($value === 'null') $model = $model->whereNull($filter);
            else if ($value === 'not_null') $model = $model->whereNotNull($filter);
            else if ($value !== '') $model = $model->where($filter, $value);
        }
        return $model;
    }

    public function searchResponse($params, $model)
    {
        $with = $params['with'] ?? '';
        if ($with !== '') {
            if (is_string($with)) $with = explode(',', $with);
               $with = array_filter($with, function ($relation) use ($model) {
                return method_exists($model->getModel(), $relation);
            });

            if (!empty($with)) {
                $model = $model->with($with);
            }
        }

        $limit = $params['limit'] ?? '';
        if ($limit !== '') $model = $model->limit($limit);
        $skip = $params['skip'] ?? '';
        if ($skip !== '') $model = $model->skip($skip);

        $is_trash = $params['is_trash'] ?? '';
        if ($is_trash !== '') $model = $model->onlyTrashed();

        $with_trash = $params['with_trash'] ?? '';
        if ($with_trash !== '') $model = $model->withTrashed();

        $orders = $params['orders'] ?? '';
        if ($orders !== '') {
            foreach ($orders as $column => $direction) $model = $model->orderBy($column, $direction);
        }

        $count = $params['count'] ?? '';
        if ($count !== '') return $model->count();
        $sum = $params['sum'] ?? '';
        if ($sum !== '') return $model->sum($sum);
        $first = $params['first'] ?? '';
        if ($first !== '') return $model->first();
        $paginate = $params['paginate'] ?? '';
        if ($paginate !== '') return $model->paginate($paginate);

        $to_sql = $params['to_sql'] ?? '';
        if ($to_sql !== '') dd($model->toSql());

        return $model->get();
    }

    public function cleanNumber($params, $columns = [])
    {
        foreach ($columns as $column) if (!empty($params[$column])) $params[$column] = intval(unformatNumber($params[$column]));
        return $params;
    }

    public function cleanDate($params, $columns = [])
    {
        foreach ($columns as $column) if (!empty($params[$column])) $params[$column] = unformatDate($params[$column]);
        return $params;
    }

    public function curlRequest($url, $method, $fields = '', $header = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (in_array($http_code, [404, 500, 522])) {
            curl_close($curl);
            return ['success' => false, 'message' => 'Error Code ' . $http_code];
        }

        $response_data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) return ['success' => false, 'message' => 'Error JSON Data' , 'response' => json_last_error_msg()];

        return $response_data;
    }

}
