@php($menu_cabangs = $menu_cabangs ?? [])
@php($active_cabang = session('active_cabang', []))
@if(count($menu_cabangs) > 0)
    <div class="d-flex align-items-center ms-1 ms-lg-2">
        <div class="d-flex flex-column align-items-start lh-1 cursor-pointer" data-kt-menu-trigger="hover" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <span class="fs-lg-7 fs-9">Cabang :</span>
            <span class="fw-bolder fs-lg-7 fs-9">{{ $active_cabang->nama ?? 'Semua Cabang' }}</span>
        </div>
        <div class="menu menu-sub menu-sub-dropdown menu-column w-auto py-3" data-kt-menu="true" id="kt_menu_notifications">
            @if(empty(auth()->user()->petugas))
                <div class="menu-item px-3 py-0 my-0">
                    <a href="{{ url('pilih_cabang/all' ) }}" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                        @if(($active_cabang->id ?? '') == '')
                            <span class="menu-icon me-3" data-kt-element="icon"><i class="ki-duotone ki-check-square fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i></span>
                        @else
                            <span class="menu-icon me-3 text-light" data-kt-element="icon"></span>
                        @endif
                        <span class="menu-title text-dark">All Studio</span>
                    </a>
                </div>
            @endif
            @foreach($menu_cabangs as $cabang)
                <div class="menu-item px-3 py-0 my-0">
                    <a href="{{ url('pilih_cabang/' . $cabang->id ) }}" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                        @if(($active_cabang->id ?? '') == $cabang->id)
                            <span class="menu-icon me-3" data-kt-element="icon"><i class="ki-duotone ki-check-square fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span><span class="path7"></span><span class="path8"></span><span class="path9"></span><span class="path10"></span></i></span>
                        @else
                            <span class="menu-icon me-3 text-light" data-kt-element="icon"></span>
                        @endif
                        <span class="menu-title text-dark fs-7">{{ $cabang->nama }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif
