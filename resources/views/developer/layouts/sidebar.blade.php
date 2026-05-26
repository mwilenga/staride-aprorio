    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route("developer.home")}}">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-laugh-wink"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Developer Tools</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.home")}}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.logs")}}">
                <i class="fas fa-fw fa-file"></i>
                <span>Logs</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{route("coordinates.testing")}}">
                <i class="fas fa-fw fa-file-archive"></i>
                <span>Coordinate Testing</span></a>
        </li>


        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.driver.settings")}}">
                <i class="fas fa-fw fa-image"></i>
                <span>Dev Settings</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.sms-gateway-testing")}}">
                <i class="fas fa-fw fa-file-archive"></i>
                <span>SMS Gateway Testing</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.user.token")}}">
                <i class="fas fa-fw fa-user"></i>
                <span>User Token Generate</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.driver.token")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Driver Token Generate</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.user.clientCreate")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Third Party Client Token</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.user.notification")}}">
                <i class="fas fa-fw fa-user"></i>
                <span>User Notificaton</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.driver.notification")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Driver Notificaton</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.api.testing")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>API Testing</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.preview.config")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Preview App Config</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("dynamic.home-screen-holder")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Add Dynamic Holders</span></a>
        </li>
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.homescreen.config")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>Home Screen Config</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.user-guide")}}">
                <i class="fas fa-fw fa-users"></i>
                <span>User Guide</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.image-gallery")}}">
                <i class="fas fa-fw fa-image"></i>
                <span>Image Gallery</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <li class="nav-item">
            <a class="nav-link" href="{{route("developer.segment-group-icon")}}">
                <i class="fas fa-fw fa-image"></i>
                <span>Handyman Segment Group Icon</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of Sidebar -->
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <ul class="navbar-nav ml-auto">
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{$merchant->BusinessName}}</span>
                            <img class="img-profile rounded-circle" src="{{ get_image($merchant->BusinessLogo,'business_logo',$merchant->id,true) }}" title="{{ $merchant->BusinessName }}">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->
