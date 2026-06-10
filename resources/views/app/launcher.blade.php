@extends('layouts.layout')

@section('body')
    @auth
        @php($user = auth()->user())
        <div class="d-flex align-items-center shadow-sm rounded-4 position-absolute top-0 end-0 me-6 mt-6">
            <div class="btn btn-active-light d-flex align-items-center bg-white py-2 px-2 pe-lg-4 ps-lg-8" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                <div class="d-none d-md-flex flex-column align-items-end justify-content-center me-2">
                    <span class="text-dark fs-base fw-bold">{{ $user->petugas->nama ?? '' }}</span>
                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7 mb-0">{{ $user->petugas->cabang->nama ?? '' }}</a>
                </div>
                <div class="user-avatar-circle">
                    <i class="ki-duotone ki-user fs-4"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-auto" data-kt-menu="true">
                <div class="menu-item px-3">
                    <div class="menu-content d-flex align-items-center px-3">
                        <div class="user-avatar-circle me-3">
                            <i class="ki-duotone ki-user fs-4"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <div class="d-flex flex-column">
                            <div class="fw-bold d-flex align-items-center fs-5">{{ $user->petugas->nama ?? '' }}</div>
                            <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ $user->petugas->cabang->nama ?? '' }}</a>
                        </div>
                    </div>
                </div>
                <div class="separator my-2"></div>
                <div class="menu-item px-5">
                    <a href="{{ hasRoute('logout') }}" class="menu-link px-5 d-flex justify-content-between">
                        <span>Sign Out</span>
                        <i class="ki-duotone ki-exit-right fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </a>
                </div>
            </div>
        </div>
    @endauth


    <div class="launcher-wrapper">

        {{-- Header --}}
        <div class="launcher-header">
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-125px" />

            <p class="mt-6">Pilih modul yang ingin Anda akses</p>
        </div>

        {{-- Module Grid --}}
        <div class="modules-grid">
            @foreach($modules as $key => $mod)
                <a href="{{ url($mod['route']) }}" class="module-card" style="--module-color: {{ $mod['color'] }}; --module-bg: {{ $mod['bg'] }};">
                    <div class="module-badge"></div>
                    <div class="module-icon-wrap">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="fill: {{ $mod['color'] }};">
                            {!! $mod['icon'] !!}
                        </svg>
                    </div>
                    <h3>{{ $mod['caption'] }}</h3>
                    <span class="sub">{{ $mod['sub'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="launcher-footer">
            &copy; {{ date('Y') }} {{ env('APP_NAME') }} &mdash; Unit Transfusi Darah Surabaya
        </div>

    </div>

@endsection
