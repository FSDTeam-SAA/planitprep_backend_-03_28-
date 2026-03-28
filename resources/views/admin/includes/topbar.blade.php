<script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>

<header class="pc-header">
    <div class="header-wrapper">
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="ms-auto">
        <ul class="list-unstyled pe-4">
            <li class="dropdown pc-h-item">
                <a id="dropdownMenuButton1" class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="false" aria-expanded="false">
                    <svg class="pc-icon">
                        <use xlink:href="#custom-user-bold"></use>
                    </svg>
                </a>
                <div class="dropdown-menu dropdown-menu-end pc-h-dropdown" aria-labelledby="dropdownMenuButton1">
                    <a href="{{ route('admin.change-password') }}" class="dropdown-item">
                        <i class="ti ti-lock"></i>
                        <span>Change Password</span>
                    </a>

                    <a href="{{ route('admin.logout') }}" class="dropdown-item">
                        <i class="ti ti-power"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</header>
