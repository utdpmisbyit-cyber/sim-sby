@extends('layouts.layout')

@section('title')
    Reset Password -
@endsection

@section('body-class')
    auth-bg
@endsection

@section('body')
    <div class="position-absolute start-0 top-0 ms-6 mt-6 z-index-2 shadow-sm rounded-3">
        <div class="d-flex align-items-center"><a href="#" class="btn btn-icon btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"><i class="ki-duotone ki-night-day theme-light-show fs-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i><i class="ki-duotone ki-moon theme-dark-show fs-1"><span class="path1"></span><span class="path2"></span></i></a><div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu"><div class="menu-item px-3 my-0"><a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light"><span class="menu-icon" data-kt-element="icon"><i class="ki-duotone ki-night-day fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i></span><span class="menu-title">Light</span></a></div><div class="menu-item px-3 my-0"><a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark"><span class="menu-icon" data-kt-element="icon"><i class="ki-duotone ki-moon fs-2"><span class="path1"></span><span class="path2"></span></i></span><span class="menu-title">Dark</span></a></div><div class="menu-item px-3 my-0"><a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system"><span class="menu-icon" data-kt-element="icon"><i class="ki-duotone ki-screen fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span><span class="menu-title">System</span></a></div></div></div>
    </div>

    <div class="d-flex flex-column flex-center flex-column-fluid flex-root">
        <div class="d-flex flex-column flex-center text-center p-10">
            <div class="card card-flush w-lg-650px py-5 shadow rounded-4">
                <div class="card-body py-15 py-lg-20">
                    <form action="{{ route('reset_password.process', $token) }}" method="post">
                        @csrf
                        <a href="{{ url('/') }}" class=""><img alt="Logo" src="{{ !empty($active_profile) ? asset('storage/' . $active_profile->logo) : asset('images/logo.png') }}" class="h-80px mb-6"/></a>
                        <h1 class="fw-bolder text-gray-900 mb-2">Reset Password</h1>
                        <div class="fs-6 fw-semibold text-gray-500 mb-10">Register to create account before using {{ env('APP_NAME') }}</div>

                        <div class="d-flex flex-column mx-lg-20 mb-6">
                            <x-io-input name="password" type="password" caption="New Password" placeholder="Password" :viewtype="2" />
                            <x-io-input name="password_confirmation" type="password" caption="Repeat Password" placeholder="Make sure you don’t mistype your password" :viewtype="2" />
                        </div>

                        <div class="d-flex flex-column gap-3 mx-lg-20 mb-6">
                            <button type="submit" class="btn btn-primary ps-8">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
