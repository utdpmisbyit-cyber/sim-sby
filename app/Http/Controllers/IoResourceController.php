<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class IoResourceController extends Controller
{
    protected $service;
    protected $viewPrefix;
    protected $itemVariable = 'item';
    protected $prefixKode = '';

    public function index()
    {
        return view("{$this->viewPrefix}.index");
    }

    public function search(Request $request)
    {
        $items = $this->service->search($request->all());
        if ($request->has('ajax')) return $items;
        return view("{$this->viewPrefix}._table", [Str::plural($this->itemVariable) => $items]);
    }

    public function create()
    {
        return view("{$this->viewPrefix}._form");
    }

    public function store(Request $request)
    {
        $response = $this->service->store($request->all());
        if (!empty($response['errors'])) return response()->json($response['errors'], 401);
        return $response;
    }

    public function edit($id)
    {
        $item = $this->service->find($id);
        return view("{$this->viewPrefix}._form", [$this->itemVariable => $item]);
    }

    public function show($id)
    {
        return $this->service->find($id);
    }

    public function update(Request $request, $id)
    {
        $response = $this->service->update($request->all(), $id);
        if (!empty($response['errors'])) return response()->json($response['errors'], 401);
        return $response;
    }

    public function destroy($id)
    {
        $response = $this->service->delete($id);
        if (!empty($response['errors'])) return response()->json($response['errors'], 401);
        return $response;
    }

    public function restore($id)
    {
        $response = $this->service->restore($id);
        if (!empty($response['errors'])) return response()->json($response['errors'], 401);
        return $response;
    }
}
