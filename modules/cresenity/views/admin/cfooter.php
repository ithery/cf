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
    				&copy; 2013 <a href="http://cresenity.com">Cresenity</a>.
    			</div> <!-- /span12 -->
    			
    		</div> <!-- /row -->
    		
		</div> <!-- /container -->
		
	</div> <!-- /footer-inner -->
	
</div> <!-- /footer -->
    
	<?php echo $end_client_script; ?>
	<!-- Load javascript here -->
	
	
	<script src="<?php echo curl::base();?>media/js/plugins/form/jquery.form.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/controls/jquery.controls.js"></script>

	
	<script src="<?php echo curl::base();?>media/js/plugins/event/jquery.event.move.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/event/jquery.event.swipe.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/slimscroll/jquery.slimscroll.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/slimscroll/jquery.slimscroll-horizontal.js"></script>
	
	
	<!-- Dialog -->
	<script src="<?php echo curl::base();?>media/js/plugins/dialog2/jquery.dialog2.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/dialog2/jquery.dialog2.helpers.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/effects/jquery.effects.core.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/effects/jquery.effects.slide.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/validation-engine/jquery.validationEngine.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/validation-engine/languages/jquery.validationEngine-en.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/datatable/jquery.dataTables.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/datatable/TableTools.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/datatable/ColReorder.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/datatable/ColVis.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/datatable/jquery.dataTables.columnFilter.js"></script>

	<script src="<?php echo curl::base();?>media/js/plugins/terminal/jquery.mousewheel-min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/terminal/jquery.terminal-min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/chosen/chosen.jquery.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/servertime/jquery.servertime.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/uniform/jquery.uniform.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/select2/select2.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/datepicker/bootstrap-datepicker.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/timepicker/bootstrap-timepicker.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/wysihtml5/bootstrap-wysihtml5.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/bootstrap-switch/bootstrap-switch.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/notify/bootstrap-notify.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/bootbox/jquery.bootbox.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.resize.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/peity/jquery.peity.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/multiselect/jquery.multi-select.js"></script>

	<!-- Custom file upload -->
	<script src="<?php echo curl::base();?>media/js/plugins/fileupload/bootstrap-fileupload.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/mockjax/jquery.mockjax.js"></script>

	<script src="<?php echo curl::base();?>media/js/plugins/elfinder/elfinder.min.js"></script>
	<!-- Virtual Keyboard -->
	<script src="<?php echo curl::base();?>media/js/plugins/vkeyboard/bootstrap-vkeyboard.js"></script>
	
	
	<script src="<?php echo curl::base();?>media/js/cresenity.func.js"></script>
	<script src="<?php echo curl::base();?>media/js/cresenity.js"></script>
	<script src="<?php echo curl::base();?>ccore/js"></script>
	<script language="javascript">
		jQuery(document).ready(function() {
			jQuery("#toggle-subnavbar").click(function() {
				var cmd = jQuery("#toggle-subnavbar span").html();
				if(cmd=='Hide') {
					jQuery('#subnavbar').slideUp('slow');
					jQuery("#toggle-subnavbar span").html('Show');
				} else {
					jQuery('#subnavbar').slideDown('slow');
					jQuery("#toggle-subnavbar span").html('Hide');
				
				}
				
			});
			$(document).ready(function(){
				$('#servertime').serverTime({
					ajaxFile: '<?php echo curl::base(); ?>cresenity/server_time',
					displayDateFormat: "yyyy-mm-dd HH:MM:ss"
				});
			});
		});
		
	</script>
	<script language="javascript">
		jQuery(document).ready(function() {
			<?php
				echo $js;
				echo $ready_client_script;
			?>
		});
		if(window) {
			window.onload=function() {
				<?php echo $load_client_script; ?>
			}
		}
		<?php echo $custom_js ?>
	</script>
	<?php echo $custom_footer; ?>
</body>
</html>