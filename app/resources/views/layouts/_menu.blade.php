<div class="header-menu align-items-stretch"
     data-kt-drawer="true"
     data-kt-drawer-name="header-menu"
     data-kt-drawer-activate="{default: true, xl: false}"
     data-kt-drawer-overlay="true"
     data-kt-drawer-width="{default:'220px', '300px': '260px'}"
     data-kt-drawer-direction="end"
     data-kt-drawer-toggle="#kt_header_menu_mobile_toggle"
     data-kt-swapper="true"
     data-kt-swapper-mode="prepend"
     data-kt-swapper-parent="{default: '#kt_body', xl: '#kt_header_nav'}">

    <div class="menu menu-rounded menu-column text-nowrap gap-xl-1 menu-xl-row menu-root-here-bg-desktop menu-active-bg menu-state-danger menu-title-gray-800 menu-arrow-gray-400 align-items-stretch my-5 my-xl-0 px-2 px-xl-0 fw-semibold fs-6"
         id="#kt_header_menu"
         data-kt-menu="true">

        @php($menus = $menus ?? [])
        @php($current_menu = $current_menu ?? [])
        @php($current_sub_menu = $current_sub_menu ?? [])

        @foreach($menus as $menu)
            @if(empty($menu['sub_menus']))
                <a href="{{ hasRoute($menu['route'], $menu['params'] ?? '') }}"
                   class="menu-item {{ $menu == $current_menu ? 'here show' : '' }}">
                    <span class="menu-link py-2 px-3">
                        <span class="menu-title fs-7">{{ $menu['caption'] }}</span>
                    </span>
                </a>
            @else
                <div data-kt-menu-trigger="{default: 'click', xl: 'hover'}"
                     data-kt-menu-placement="bottom-start"
                     class="menu-item {{ $current_menu == $menu ? 'here' : '' }} menu-xl-down-accordion menu-sub-xl-down-indention">
                    <span class="menu-link py-2 px-3">
                        <span class="menu-title fs-7">{{ $menu['caption'] }}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-xl-down-accordion menu-sub-xl-dropdown px-xl-2 py-xl-4 min-w-xl-200px">
                        @foreach($menu['sub_menus'] as $sub_menu)
                            <div class="menu-item ">
                                <a class="menu-link py-2 {{ $current_sub_menu == $sub_menu ? 'active' : '' }}"
                                   href="{{ hasRoute($sub_menu['route'], $sub_menu['params'] ?? '') }}">
                                    <span class="menu-title fs-7 text-hover-light">{!! $sub_menu['caption'] !!}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

    </div>
</div>
