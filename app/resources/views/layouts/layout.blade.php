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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.css" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/custom/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <style>

    </style>
    @stack('styles')
</head>

<body id="kt_body" class="@yield('body-class')">
<script>let defaultThemeMode = "light"; let themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>

@yield('body')

@stack('modals')

<div id="error_log"></div>

<script>const hostUrl = "{{ asset('assets') }}/";</script>
<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
@stack('js_plugins')
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="{{ asset('assets/plugins/custom/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/auto-numeric.js') }}"></script>
<script src="{{ asset('assets/js/io.js') }}"></script>
<script>
    @if(session()->has('success'))
        swal.fire('{{ session('success') }}');
    @endif
    @if(session()->has('error'))
    Swal.fire({
        icon: "error",
        title: "{{ session('error') }}",
        text: "{{ session('error_description') }}",
    });
    @endif
</script>
@stack('scripts')
</body>
</html>
