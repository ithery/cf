<nav id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-sidenav-theme navbar-default navbar-static-side layout-color-white" role="navigation">
    <div class="brand">
        <div class="brand-inner d-flex flex-nowrap align-items-center">
            <span class="brand-logo">
                <img src="{{ c::media('img/favico.png') }}" />
            </span>
            <a href={{ c::url('admin') }} class="brand-name sidenav-text font-weight-normal ml-2">
                CF DEMO
            </a>
        </div>
    </div>
    <div class="sidenav-divider mt-0"></div>
    <div class="sidenav-inner py-1 ps">
        @CAppNav
    </div>
</nav> <!-- /nav -->
