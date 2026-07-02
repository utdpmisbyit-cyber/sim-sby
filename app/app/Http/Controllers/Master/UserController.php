<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Http\Requests\UserSaveRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new UserService();
        $this->viewPrefix = 'app.master.user';
        $this->itemVariable = 'user';

        view()->share('roles', $this->service->roles);
    }

    public function store(Request $request)
    {
        $request->validate((new UserSaveRequest())->rules());
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $request->validate((new UserSaveRequest())->rules());
        return parent::update($request, $id);
    }
}
