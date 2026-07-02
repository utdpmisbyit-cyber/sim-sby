@extends('layouts.layout')

@section('title')
    403 Access Denied -
@endsection

@push('styles')
    <style>
        .floating {
            animation: floating 4s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
            100% { transform: translateY(0px); }
        }
    </style>
@endpush

@section('body-class')
    auth-bg
@endsection

@section('body')
    <div class="d-flex flex-column flex-center flex-column-fluid">
        <div class="card card-flush w-lg-700px py-10 shadow-sm rounded-4 border-0">
            <div class="card-body text-center px-10">

                <!-- Illustration -->
                <div class="mb-8">
                    <img
                        src="https://popsy-assets.6d7f1376eb77bb5467178bd740961d17.r2.cloudflarestorage.com/notion/security.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=74b6c77827cafff9c0444f8a29156c5e%2F20260124%2Fauto%2Fs3%2Faws4_request&X-Amz-Date=20260124T145757Z&X-Amz-Expires=900&X-Amz-Signature=4aae0f297848a0b8ccb17d7bf08d929e4753297decf6fca8042aaef254b0c73c&X-Amz-SignedHeaders=host&response-content-disposition=inline&x-amz-checksum-mode=ENABLED&x-id=GetObject"
                        alt="403 Forbidden"
                        class="img-fluid floating"
                        style="max-height: 260px;"
                    />
                </div>

                <!-- Title -->
                <h1 class="fw-bolder display-4 text-gray-900 mb-3">
                    403
                </h1>

                <h3 class="fw-semibold text-gray-700 mb-4">
                    Access Denied
                </h3>

                <p class="text-gray-500 fs-6 mb-8">
                    Sorry, you don’t have permission to access this page.
                    If you think this is a mistake, please contact the administrator.
                </p>

                <!-- Actions -->
                <div class="d-flex justify-content-center gap-4">
                    @if(in_array((auth()->user()->role ?? ''), ['Admin', 'Admin Medan', 'Staff']))
                        <a href="{{ url('admin') }}" class="btn btn-primary fw-bold px-8 py-3">
                            🏠 Back to Home
                        </a>
                    @else
                        <a href="{{ url('/') }}" class="btn btn-primary fw-bold px-8 py-3">
                            🏠 Back to Home
                        </a>
                    @endif


                    <a href="{{ url('login') }}" class="btn btn-light fw-bold px-8 py-3">
                        🔐 Login
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
