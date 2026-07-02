@extends('layouts.layout')

@section('title')
    500 Internal Server Error -
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
    <div class="d-flex flex-column flex-center flex-column-fluid p-10">

        <div class="card card-flush w-lg-800px shadow-sm rounded-4 border-0">
            <div class="card-body text-center px-10 py-12">

                <!-- Illustration -->
                <div class="mb-8">
                    <img
                        src="https://popsy-assets.6d7f1376eb77bb5467178bd740961d17.r2.cloudflarestorage.com/notion/bug-fix.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=74b6c77827cafff9c0444f8a29156c5e%2F20260124%2Fauto%2Fs3%2Faws4_request&X-Amz-Date=20260124T150252Z&X-Amz-Expires=900&X-Amz-Signature=b128d78f0e4570a55919e02255e6aaf3cd7ac6ba07ce4e891fb49f6ae0dac67d&X-Amz-SignedHeaders=host&response-content-disposition=inline&x-amz-checksum-mode=ENABLED&x-id=GetObject"
                        class="img-fluid floating"
                        style="max-height: 260px;"
                        alt="Server Error"
                    />
                </div>

                <!-- Title -->
                <h1 class="fw-bolder display-4 text-gray-900 mb-3">
                    500
                </h1>

                <h3 class="fw-semibold text-gray-700 mb-4">
                    Internal Server Error
                </h3>

                <p class="text-gray-500 fs-6 mb-8">
                    Something went wrong on our side.
                    Please try again later.
                </p>

                <div class="d-flex justify-content-center gap-4 mb-10">
                    <a href="{{ url('/') }}" class="btn btn-primary fw-bold px-8 py-3">
                        🏠 Home
                    </a>

                    @if(isset($exception))
                        <button
                            class="btn btn-light fw-bold px-8 py-3"
                            id="toggleErrorBtn"
                            onclick="toggleErrorDetails()"
                        >
                            🐞 Show error details
                        </button>
                    @endif
                </div>


                @if(isset($exception))
                    <div id="errorDetails" class="d-none">
                        <hr class="my-8">

                        <div class="text-start">
                            <h4 class="fw-bold text-danger mb-3">
                                🔥 Exception Details
                            </h4>

                            <p class="mb-2">
                                <strong>Message:</strong><br>
                                {{ $exception->getMessage() }}
                            </p>

                            <p class="mb-2">
                                <strong>File:</strong><br>
                                {{ $exception->getFile() }} : {{ $exception->getLine() }}
                            </p>

                            <h5 class="fw-bold mt-5 mb-3">Stack Trace</h5>

                            <pre>{{ $exception->getTraceAsString() }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        function toggleErrorDetails() {
            const details = document.getElementById('errorDetails');
            const btn = document.getElementById('toggleErrorBtn');

            if (!details || !btn) return;

            const isHidden = details.classList.contains('d-none');

            details.classList.toggle('d-none');
            btn.innerHTML = isHidden
                ? '🙈 Hide error details'
                : '🐞 Show error details';
        }
    </script>

@endpush
