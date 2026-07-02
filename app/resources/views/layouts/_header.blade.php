<div id="kt_header" class="header align-items-stretch rounded-lg px-6 border-bottom-0">
    <div class="w-100 d-flex align-items-stretch justify-content-between">
        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 w-lg-auto me-5">
            @if(count($current_menu['side_menus'] ?? []) > 0)
                <div class="btn btn-icon btn-active-icon-primary ms-n2 me-2 d-flex d-lg-none" id="kt_aside_toggle">
                    <i class="ki-duotone ki-abstract-14 fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            @endif
            <a href="{{ url('') }}">
                <img alt="Logo" src="{{ asset('images/logo.png') }}" class="d-none d-lg-inline img-logo h-50px theme-light-show" />
                <img alt="Logo" src="{{ asset('images/logo.png') }}" class="d-none d-lg-inline img-logo h-50px theme-dark-show" />
                <img alt="Logo" src="{{ asset('images/logo.png') }}" class="d-lg-none img-logo h-30px" />
            </a>
        </div>

        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
            <div class="d-flex align-items-stretch ms-0" id="kt_header_nav">
                @include('layouts._menu')
            </div>
            <div class="d-flex align-items-stretch flex-shrink-0">
                @include('layouts._list_cabang')
                @include('layouts._modules')
                @include('layouts._switcher')
                @include('layouts._user')
                <div class="d-flex d-xl-none align-items-center" title="Show header menu">
                    <button class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                        <i class="ki-duotone ki-text-align-left fs-1 fw-bold"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
