
<?php
$sys = CSystem::instance();
$phpinfo = CPHPInfo::instance();
?>

<div class="row-fluid">
    <div class="span6">
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-table"></i></span>
                        <h5>System Vital</h5>

                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="taskDesc">System</td>
                                    <td class="taskStatus"><?php echo $phpinfo->system(); ?></td>
                                </tr>
                                <tr>
                                    <td class="taskDesc">OS</td>
                                    <td class="taskStatus"><?php echo csys::os(); ?></td>
                                </tr>
                                <tr>
                                    <td class="taskDesc">PHP Version</td>
                                    <td class="taskStatus"><?php echo csys::php_version(); ?></td>
                                </tr>
                                <tr>
                                    <td class="taskDesc">Mysql Version</td>
                                    <td class="taskStatus"><?php echo csys::mysql_version(); ?></td>
                                </tr>
                                <tr>
                                    <td class="taskDesc">Apache Version</td>
                                    <td class="taskStatus"><?php echo csys::apache_version(); ?></td>
                                </tr>
                                <tr>
                                    <td class="taskDesc">Hostname</td>
                                    <td class="taskStatus"><?php echo csys::hostname(); ?></td>
                                </tr>
                                <?php if (false): ?>
                                    <tr>
                                        <td class="taskDesc">Ip Address</td>
                                        <td class="taskStatus"><?php echo csys::ip_address(); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="taskDesc">Ext Ip Address</td>
                                        <td class="taskStatus"><?php echo csys::external_ip_address(); ?></td>
                                    </tr>

                                    <tr>
                                        <td class="taskDesc">Kernel</td>
                                        <td class="taskStatus"><?php echo $sys->kernel(); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="taskDesc">UName</td>
                                        <td class="taskStatus"><?php echo csys::u_name(); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (false): ?>
                                    <tr>
                                        <td class="taskDesc">Distro</td>
                                        <td class="taskStatus"><img src="<?php echo curl::base(); ?>media/img/sysinfo/<?php echo $sys->distro_icon(); ?>" width="20" height="20"/> <?php echo $sys->distro(); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="taskDesc">Users</td>
                                        <td class="taskStatus"><?php echo $sys->users(); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-table"></i></span>
                        <h5><?php echo clang::__('Application Available'); ?></h5>

                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th><?php echo clang::__('Code'); ?></th>
                                    <th><?php echo clang::__('Name'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $data = CJDB::instance()->get('app')->result_array();
                                foreach ($data as $row):
                                    ?>
                                    <tr>
                                        <td class="taskDesc"><?php echo $row['code']; ?></td>
                                        <td class="taskStatus"><?php echo $row['name']; ?></td>
                                    </tr>
                                    <?php
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="span6">
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-bolt"></i></span>
                        <h5>Server Load</h5>

                    </div>
                    <div class="widget-content ">
                        <div class="flot flot-line"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title">
                        <span class="icon"><i class="icon-table"></i></span>
                        <h5><?php echo clang::__('Server Status'); ?></h5>

                    </div>
                    <div class="widget-content nopadding">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td class="taskDesc">Internet Connection</td>
                                    <td class="taskStatus">
                                        <?php
                                        $iconnection = false;
                                        //$iconnection = cnet::ping('cresenitytech.com');
                                        if ($iconnection):
                                            ?>
                                            <span class="badge badge-success">CONNECTED</span>
                                        <?php else: ?>
                                            <span class="badge badge-error">NO CONNECTION</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
