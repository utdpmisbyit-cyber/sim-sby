<div class="d-flex align-items-center ms-1 ms-lg-2">
    <a href="#" class="btn btn-icon btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
        <i class="ki-duotone ki-abstract-28 fs-1">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span>
        </i>
    </a>
    <div class="menu menu-sub menu-sub-dropdown menu-column w-100 w-sm-350px" data-kt-menu="true">
        <div class="card">
            <div class="card-header">
                <div class="card-title">List Modul</div>
            </div>

            <div class="card-body py-5">
                <div class="scroll-y me-n5 pe-5">
                    <div class="row g-2 p-3">
                        @foreach($modules as $module)
                            <div class="col-4 p-1">
                                <a href="{{ hasRoute($module['route']) }}" class="module-launcher-item module-card d-flex flex-column gap-2 justify-content-start flex-center text-center text-gray-800 h-100 rounded py-4 px-3 {{ $module['route'] == $current_module ? 'active' : '' }}">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="h-30px" style="fill: {{ $module['color'] }};">
                                        {!! $module['icon'] !!}
                                    </svg>
                                    <span class="fw-semibold lh-1 fs-8">{{ $module['caption'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

</style>
