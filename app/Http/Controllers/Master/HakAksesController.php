<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\IoResourceController;
use App\Services\HakAksesService;
use App\Services\MenuService;
use Illuminate\Http\Request;

class HakAksesController extends IoResourceController
{
    public function __construct()
    {
        $this->service = new HakAksesService();
        $this->viewPrefix = 'app.master.hak_akses';
        $this->itemVariable = 'hak_akses';

        $option_modules = $this->module_with_menus();
        view()->share('option_modules', $option_modules);
    }

    public function module_with_menus()
    {
        $menuService = new MenuService();
        $option_modules = $menuService->modules;
        foreach ($option_modules as $key => $module) $option_modules[$key]['menus'] = $menuService->listMenu($key);

        return $option_modules;
    }

    public function store(Request $request)
    {
        $request = $this->saveDescription($request);;
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $request = $this->saveDescription($request);
        return parent::update($request, $id);
    }

    public function saveDescription(Request $request)
    {
        $option_modules = $this->module_with_menus();
        $selected_modules = [];
        foreach ($option_modules as $key => $module) {
            $selected_menus = [];
            foreach ($module['menus'] as $key2 => $menu) {
                if ($request->has('menu_' . $key2)) $selected_menus[] = $key2;

                foreach (($menu['sub_menus'] ?? []) as $key3 => $sub_menu) {
                    if ($request->has('sub_menu_' . $key3)) $selected_menus[] = $key3;
                }
            }
            if ($request->has('module_' . $key)) {
                $selected_modules[$key] = $selected_menus;
            }
        }

        return $request->merge(['description' => json_encode($selected_modules)]);
    }
}
