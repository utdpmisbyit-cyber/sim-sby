<?php

namespace App\Services;

use App\Models\User;

class UserService extends IoService
{
    public array $roles = User::ROLES;
    public function __construct()
    {
        $this->model = new User();
        $this->sort_by = ['name' => 'asc'];
        $this->filters = ['role'];
    }

    public function dynamic_search($model, $params = [])
    {
        $name = $params['nama'] ?? '';
        if ($name !== '') $model = $model->where('nama', 'like', '%' . $name . '%');
        $role_in = $params['role_in'] ?? '';
        if ($role_in !== '') $model = $model->whereIn('role', $role_in);
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
