<nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center container-p-x bg-navbar-theme font-color-white"
    id="layout-navbar">

    <div class="layout-sidenav-toggle navbar-nav align-items-lg-center mr-3 d-lg-none">
        <a class="nav-item nav-link px-0 ml-2 ml-lg-0" href="javascript:void(0)">
            <i class="ion ion-md-menu text-large align-middle"></i>
        </a>
    </div>

    <div class="breadcrumb-container mr-auto">
        <ol class="breadcrumb text-big  m-0">

            @foreach ($breadcrumb as $k => $b)
                <li class="breadcrumb-item">
                    <a href="{{ $b }}" class="">{{ $k }}</a>
                </li>
            @endforeach
            <li class="breadcrumb-item active">
                <a href="javascript:;" class="active">{{ $title }}</a>
            </li>
        </ol>
    </div>

    <div class="navbar-nav align-items-lg-center">
        <div class="nav-item tablist-view navbar-user nav-item dropdown" id="dropdownNotification" data-unread="10"
            data-timestamp="0">
            <a href="javascript:;" class="nav-link btn-click-notification nav-link-notification" data-toggle="dropdown"
                aria-expanded="false">
                <span class="notif-unread notification-unread-count"></span>
                <i class="iz-icon-navbar iz-icon-bell"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-notification-container"
                id="notificationDropdownContainer">
                <!-- <div class="notification-container" data-total="0" data-last-page="0" data-per-page="0" data-current-page="0" data-timestamp="0" data-infinite-scroll="1">

                            </div> -->
            </div>
        </div>
    </div>



    <button class="navbar-toggler focus:outline-none collapsed" type="button" data-toggle="collapse"
        data-target="#layout-navbar-collapse" aria-expanded="false">
        <span class="fas fa-ellipsis-v text-lg ml-3"></span>
    </button>


    <div class="navbar-collapse collapse justify-content-end" id="layout-navbar-collapse">

        <div class="navbar-nav align-items-lg-center">

            <div class="navbar-user nav-item dropdown dropdown-pull-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
                    {{ \Cresenity\Demo\DemoVariable::themeLabel() }}
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    @foreach( \Cresenity\Demo\DemoVariable::themeData() as $themeKey => $themeLabel)
                    <li>
                        <a href="{{ c::url('demo/account/theme/change/' . $themeKey) }}">
                            {{ $themeLabel }}
                        </a>
                    </li>
                    @endforeach

                </ul>
            </div>
        </div>
        <div class="navbar-nav align-items-lg-center">

            <div class="navbar-user nav-item dropdown dropdown-pull-right">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
                    <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                        <i class="ti ti-user text-xl"></i>
                        <span class="px-1 mr-lg-2 ml-2 ml-lg-0">{{ \Cresenity\Demo\DemoVariable::username() }}</span>
                    </span>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li>
                        <a href="{{ c::url('demo/account/password/change') }}">
                            <i class="ti ti-key"></i>&nbsp;&nbsp; @lang('Change Password')
                        </a>
                    </li>
                    <li>
                        <a href="{{ c::url('/') }}">
                            <i class="ti ti-power-off"></i>&nbsp;&nbsp;@lang('Logout')
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="navbar-nav align-items-center ml-3">
            <a id="btn-demo-show-code" class="btn btn-info text-white btn-demo-show-code"
                data-uri="{{ c::request()->path() }}">Show Codes</a>
        </div>

    </div>
    <!--/.nav-collapse -->


</nav> <!-- /navbar -->
