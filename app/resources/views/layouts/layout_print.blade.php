<!DOCTYPE html>
<html lang="en">
<head>
    <base href="{{ url('/') }}" />
    <title>@yield('title') {{ $general_contents['website-title'] ?? env('APP_NAME') }}</title>
    <meta charset="utf-8" />
    <meta name="token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('css_plugins')
    <link href="{{ public_path('assets/plugins/custom/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ public_path('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_path('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .datepicker table tr td span.active.active, .datepicker table tr td span.active.disabled, .datepicker table tr td span.active.disabled.active, .datepicker table tr td span.active.disabled.disabled, .datepicker table tr td span.active.disabled:active, .datepicker table tr td span.active.disabled:hover, .datepicker table tr td span.active.disabled:hover.active, .datepicker table tr td span.active.disabled:hover.disabled, .datepicker table tr td span.active.disabled:hover:active, .datepicker table tr td span.active.disabled:hover:hover, .datepicker table tr td span.active.disabled:hover[disabled], .datepicker table tr td span.active.disabled[disabled], .datepicker table tr td span.active:active, .datepicker table tr td span.active:hover, .datepicker table tr td span.active:hover.active, .datepicker table tr td span.active:hover.disabled, .datepicker table tr td span.active:hover:active, .datepicker table tr td span.active:hover:hover, .datepicker table tr td span.active:hover[disabled], .datepicker table tr td span.active[disabled] {
            background: #019ef7!important;
        }
        div.datepicker-dropdown {
            padding: 12px;
        }
        div.datepicker td, div.datepicker th {
            padding: 8px;
        }
        .badge {
            border-radius: 50px;
        }
        .flatpickr-calendar {
            padding: 8px 0;
        }
        .dropify-wrapper .dropify-message p {
            font-size: 12pt;
        }
        .dropify-wrapper {
            border: 1px dashed #E5E5E5;
            border-radius: 5px;
        }
        .form-select {
            padding: .775rem 1rem;
        }
        .border-dashed {
            border-width: 1px;
        }
        .amcharts-main-div a {
            display: none!important;
        }
        .border-solid {
            border-style: solid!important;
        }
        .form-select-sm {
            padding: .55rem .75rem!important;
        }
        @media (min-width: 1400px) {
            .container, .container-lg, .container-md, .container-sm, .container-xl, .container-xxl {
                max-width: 95%;
            }
            #panel_home {
                top: 25%;
            }
        }
        .header .header-menu .menu>.menu-item>.menu-link>.menu-title {
            font-size: 1rem;
        }
        .table-bordered tr th, .table-bordered tr td {
            border: 1px solid #000!important;
        }
        @media (min-width: 992px) {
            .header-fixed .header {
                height: 64px;
            }
            body.header-fixed {
                padding-top: 110px;
            }
            .menu-root-here-bg-desktop>.menu-item.here>.menu-link {
                background-color: var(--bs-primary);
            }
            .menu-state-primary .menu-item.here>.menu-link .menu-title {
                color: white;
            }
        }
        @media (max-width: 991px) {
            #panel_home {
                top: 10%;
            }
            .header-tablet-and-mobile-fixed .header {
                height: 54px;
            }
            body.header-tablet-and-mobile-fixed {
                padding-top: 72px!important;
            }
        }
        @media (min-width: 1400px) {
            .container-landing {
                max-width: 80%;
            }
        }
    </style>
    @stack('styles')
</head>

<body id="kt_body" class="@yield('body-class')">
@yield('body')
@stack('scripts')
</body>
</html>
