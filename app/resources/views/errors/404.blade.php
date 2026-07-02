@extends('layouts.layout')

@section('title')
    404 Page Not Found -
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
                        src="https://illustrations.popsy.co/gray/crashed-error.svg"
                        alt="404 Not Found"
                        class="img-fluid floating"
                        style="max-height: 260px;"
                    />
                </div>

                <!-- Title -->
                <h1 class="fw-bolder display-4 text-gray-900 mb-3">
                    404
                </h1>

                <h3 class="fw-semibold text-gray-700 mb-4">
                    Oops! You look lost.
                </h3>

                <p class="text-gray-500 fs-6 mb-8">
                    The page you’re looking for doesn’t exist or was moved.
                    Let’s get you back on track.
                </p>

                <!-- Actions -->
                <div class="d-flex justify-content-center gap-4">
                    <a href="{{ url('/') }}" class="btn btn-primary fw-bold px-8 py-3">
                        🏠 Back to Home
                    </a>
                </div>

            </div>
        </div>
    </div>

@endsection
