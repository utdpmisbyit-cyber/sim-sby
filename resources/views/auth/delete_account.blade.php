@extends('layouts.layout')

@section('title')
    Delete Account -
@endsection

@section('body-class')
    auth-bg
@endsection

@push('styles')
    <style>
        body {
            background: url('{{ asset('images/bg-auth.jpg') }}') no-repeat center;
            background-size: cover;
        }

        [data-bs-theme="dark"] body {
            background: url('{{ asset('images/bg-auth.jpg') }}') no-repeat center;
            background-size: cover;
        }
    </style>
@endpush

@section('body')
    <div class="d-flex flex-column flex-center flex-column-fluid">
        <div class="d-flex flex-column flex-center text-center p-10">
            <div class="card card-flush w-lg-650px py-5 shadow">
                <div class="card-body py-15 py-lg-20">
                    <form action="{{ route('delete_account.process') }}" method="post">
                        @csrf
                        <a href="{{ url('/') }}" class=""><img alt="Logo" src="{{ !empty($active_profile) ? asset('storage/' . $active_profile->logo) : asset('images/logo.png') }}" class="h-80px mb-6"/></a>
                        <h1 class="fw-bolder text-gray-900 mb-2">Delete Account</h1>
                        <div class="fs-6 fw-semibold text-gray-500 mb-10">Input email to delete all your data</div>

                        <div class="d-flex flex-column mx-lg-20 mb-6">
                            <x-io-input name="password" type="password" caption="Password" placeholder="Password" :viewtype="2" />
                            <x-io-input name="password_confirmation" type="password" caption="Ulangi Password" placeholder="Ulangi Password" :viewtype="2" />
                        </div>

                        <div class="d-flex flex-column gap-3 mx-lg-20 mb-6">
                            <button type="submit" class="btn btn-primary ps-8">Delete Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
