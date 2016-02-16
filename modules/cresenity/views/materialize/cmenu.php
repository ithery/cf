<!-- Sidebar -->
<nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
    <ul class="nav sidebar-nav">
        <li class="sidebar-brand">
            <a href="#">
               <?php
                $web_title = ccfg::get("title");
                //if($org!=null) $web_title = strtoupper($org->name);
                echo $web_title;
                ?>
            </a>
        </li>
        <?php echo CNavigation::instance()->render();?>
    </ul>
</nav>
<!-- /#sidebar-wrapper -->