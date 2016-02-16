<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!--	
</div>
-->
</div>
</div>
</div>
<div class="footer">

    <div class="footer-inner">

        <div class="container">

            <div class="row">

                <div class="span12">
                    &copy; 2016 <a href="http://ittron.co.id">Ittron Global Technology</a>.
                </div> <!-- /span12 -->

            </div> <!-- /row -->

        </div> <!-- /container -->

    </div> <!-- /footer-inner -->

</div> <!-- /footer -->

<script src="<?php echo curl::base(); ?>media/js/require.js"></script>
<!-- Load javascript here -->

<?php echo $end_client_script; ?>

<script language="javascript">
   
</script>
<script language="javascript">
	
<?php
echo $js;
echo $ready_client_script;
?>
    
    if (window) {
        window.onload = function() {
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