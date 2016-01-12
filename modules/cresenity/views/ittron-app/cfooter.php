<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

                </section>
            </div>
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

<script src="<?php echo curl::base(); ?>media/js/require.js"></script>
<!-- Load javascript here -->

<?php echo $end_client_script; ?>
<script language="javascript">
<?php
    echo $js;
    echo $ready_client_script;
?>
    if (window) {
        window.onload = function () {
<?php echo $load_client_script; ?>
        }
    }
<?php echo $custom_js ?>
</script>
<?php echo $custom_footer; ?>
</body>
</html>
<?php
    if (ccfg::get("log_request")) {
        $user = CApp::instance()->user();
        if ($user != null) {

            clog::request($user->user_id);
        }
    }
?>