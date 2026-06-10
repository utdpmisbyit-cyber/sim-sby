@extends('layouts.layout')

@section('body')
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-column flex-column-fluid ">
            <div id="kt_content_container" class="d-flex flex-column-fluid align-items-stretch mx-lg-10 m-6">
                <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
@endsection
