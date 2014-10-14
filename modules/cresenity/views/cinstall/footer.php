<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div class="row-fluid">
    <div id="footer" class="span12">
        2012 &copy; Cresenity. <a href="www.cresenity.com">www.cresenity.com</a>
    </div>
</div>
</div>
</div>

<!-- Load javascript here -->
<script src="<?php echo curl::base(); ?>media/js/libs/jquery/jquery.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/jquery/jquery.ui.custom.js"></script>

<script src="<?php echo curl::base(); ?>media/js/libs/jquery/formValidator/jquery.validationEngine.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/jquery/formValidator/languages/jquery.validationEngine-en.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/jquery/DataTables/jquery.dataTables.min.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/jquery/chosen/jquery.chosen.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-transition.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-alert.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-modal.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-dropdown.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-scrollspy.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-tab.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-tooltip.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-popover.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-button.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-collapse.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-carousel.js"></script>
<script src="<?php echo curl::base(); ?>media/js/libs/bootstrap/bootstrap-typeahead.js"></script>
<script language="javascript">
    jQuery(document).ready(function() {
<?php
echo $js;
?>
    });
</script>
</body>
</html>