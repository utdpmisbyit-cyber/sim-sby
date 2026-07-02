<?php

namespace App\Http\Middleware;

use App\Services\CabangService;
use App\Services\MenuService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IoMiddleware
{
    protected $menuService;
    public function __construct()
    {
        $this->menuService = new MenuService();
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() === 'GET') {
            $head_route = $this->handleModules($request);
            $this->handleMenus($request, $head_route);
        }

        $cabangService = new CabangService();
        $cabangs = $cabangService->search();
        View::share('menu_cabangs', $cabangs);

        $active_cabang = session('active_cabang');
        if ($active_cabang != null) {
            $request->merge(['active_cabang' => $active_cabang]);
            $request->merge(['cabang_id' => $active_cabang['id']]);
        }

        return $next($request);
    }


    public function handleModules(Request $request)
    {
        $modules = $this->menuService->modules;
        View::share('modules', $modules);

        $current_route = $request->route()->getName();
        $head_route = head(explode('.', $current_route));
        View::share('current_module', $head_route);

        return $head_route;
    }

    public function handleMenus(Request $request, $head_route)
    {
        $user = auth()->user();
        $role = $user->role ?? 'Admin';
        $menus = $this->menuService->listMenu($head_route);
        View::share('menus', $menus);

        $this->handleCurrentMenu($request, $menus, $role, $head_route);
    }

    public function handleCurrentMenu(Request $request, $menus, $role, $head_route)
    {
        $current_route = $request->route()->getName();
        $current_menu = $this->menuService->currentMenu($menus, $current_route, $role, $head_route);
        View::share($current_menu);
    }
}
