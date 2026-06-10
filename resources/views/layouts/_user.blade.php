@php($active_user = auth()->user() ?? [])
@if(!empty($active_user))
    <div class="d-flex align-items-center ms-2" id="kt_header_user_menu_toggle">
        <div class="user-trigger-btn"
             data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
             data-kt-menu-attach="parent"
             data-kt-menu-placement="bottom-end">
            <div class="user-avatar-circle">
                <i class="ki-duotone ki-user fs-4"><span class="path1"></span><span class="path2"></span></i>
            </div>
            <span class="user-name-label d-none d-md-block">{{ $active_user->name }}</span>
            <i class="ki-duotone ki-down fs-6 text-muted"></i>
        </div>

        <div class="menu menu-sub menu-sub-dropdown menu-column menu-gray-800 menu-state-bg menu-state-color fw-semibold py-2 fs-6 w-200px"
             data-kt-menu="true"
             style="min-width: 220px;">

            <div class="menu-item px-3 py-2 mb-1">
                <div class="d-flex align-items-center gap-3 px-3 py-2" style="background: var(--accent-light); border-radius: 10px;">
                    <div style="width:38px;height:38px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="ki-duotone ki-user fs-3" style="color:#fff;"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <div style="overflow:hidden;">
                        <div class="fw-bold fs-6" style="color:var(--text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $active_user->nama }}</div>
                        <div class="fs-7" style="color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $active_user->email }}</div>
                    </div>
                </div>
            </div>

            <div class="separator my-1"></div>

            {{-- Sign out --}}
            <div class="menu-item px-3">
                <a href="{{ hasRoute('logout') }}" class="menu-link px-3 d-flex justify-content-between align-items-center">
                    <span>Sign Out</span>
                    <i class="ki-duotone ki-exit-right fs-4"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </div>

        </div>
    </div>
@endif
