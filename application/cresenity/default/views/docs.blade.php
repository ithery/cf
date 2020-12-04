<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', CF::getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Cresenity Framework - Documentation</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        @CApp('styles')
        <style>
            body {
                font-family: 'Nunito';
            }
        </style>
    </head>
    <body class="antialiased language-php h-full w-full font-sans text-gray-900 antialiased">
        <div class="page-wrapper toggled light-theme">
            <nav id="sidebar" class="sidebar-wrapper">
                <div class="sidebar-content">
                    <!-- sidebar-brand  -->
                    <div class="sidebar-item sidebar-brand text-white font-weight-bold">Documentation</div>
                    <!-- sidebar-header  -->
                    <!-- sidebar-search  -->
                    <div class="sidebar-item sidebar-search">
                        <div>
                            <div class="input-group">
                                <input type="text" class="form-control search-menu" placeholder="Search...">
                                <div class="input-group-append"> <span class="input-group-text">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </span> </div>
                            </div>
                        </div>
                    </div>
                    <!-- sidebar-menu  -->
                    <div class=" sidebar-item sidebar-menu">
                        @CAppNav('docs')
                    </div>
                    <!-- sidebar-menu  -->
                </div>
                <!-- sidebar-footer  -->
                <div class="sidebar-footer">
                    <div class="dropdown">
                        <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fa fa-bell"></i> <span class="badge badge-pill badge-primary notification">1</span> </a>
                        <div class="dropdown-menu notifications" aria-labelledby="dropdownMenuMessage">
                            <div class="notifications-header"> <i class="fa fa-bell"></i> Notifications </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <div class="notification-content">
                                    <div class="icon"> <i class="fas fa-check text-success border border-success"></i> </div>
                                    <div class="content">
                                        <div class="notification-detail">Download latest update </div>
                                        <div class="notification-time"> 20 minutes ago </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div> <a class="dropdown-item text-center" href="#">All notifications</a> </div>
                    </div>
                    <div>
                        <a id="pin-sidebar" href="#"> <i class="fa fa-power-off"></i> </a>
                    </div>
                    <div class="pinned-footer">
                        <a href="#"> <i class="fas fa-ellipsis-h"></i> </a>
                    </div>
                </div>
            </nav>
            <!-- page-content  -->
            <main class="page-content">
                <div id="overlay" class="overlay"></div>
                <div class="container-fluid">
                    <div class="row d-flex align-items-center p-3 border-bottom">
                        <div class="col-md-1">
                            <a id="toggle-sidebar" class="btn rounded-0 p-3" href="#"> <i class="fas fa-bars"></i> </a>
                        </div>
                        <div class="col-md-8">
                            <nav aria-label="breadcrumb" class="align-items-center">
                                <a href="index.html" class="breadcrumb-back" title="Back"></a>
                                <ol class="breadcrumb d-none d-lg-inline-flex m-0">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Layout 2</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-3 text-left"> <a href="https://sharebootstrap.com/docu-free-bootstrap-4-documentation-theme/" class="btn btn-sm btn-primary btn-rounded">Free Download</a> </div>
                    </div>
                    <div class="row p-lg-4">
                        <article class="main-content col-md-9 pr-lg-5">
                            @CApp('content')
                        </article>
                        <aside class="col-md-3 d-none d-md-block border-left">
                            <div class="widget widget-support-forum p-md-3 p-sm-2"> <span class="icon icon-forum"></span>
                                <h4>Looking for help? Join Community</h4>
                                <p>Couldn’t find what your are looking for ? Why not join out support forums and let us help you.</p> <a href="#" class="btn btn-light">Support Forum</a> </div>
                            <hr class="my-5">
                            <ul class="aside-nav nav flex-column">
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-1">Typography</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-2">Colors</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-3">File Tree</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-4">Table</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-5">Accordion</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-6">Video</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-7">Code</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-8">Alert</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-9">List</a> </li>
                                <li class="nav-item"> <a data-scroll="" class="nav-link text-dark" href="#section-10">Carousel</a> </li>
                            </ul>
                        </aside>
                    </div>
                </div>
            </main>
            <!-- page-content" -->
        </div>

        @CApp('scripts')
    </body> 
</html> 
