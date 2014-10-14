<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php defined('SYSPATH') OR die('No direct access allowed.'); 


	$app = CApp::instance();
	$org = $app->org();
?>
		</div><!--/container-->		
<!-- End Content Part -->

<!--=== Footer ===-->
<div class="footer">
	<div class="container">
		<div class="row-fluid">
			<div class="span4">
                <!-- About -->
		        <div class="headline"><h3>About</h3></div>	
				<p class="margin-bottom-25">Unify is an incredibly beautiful responsive Bootstrap Template for corporate and creative professionals.</p>	

	            <!-- Monthly Newsletter -->
		        <div class="headline"><h3>Monthly Newsletter</h3></div>	
				<p>Subscribe to our newsletter and stay up to date with the latest news and deals!</p>
				<form class="form-inline">
					<div class="input-append">
						<input type="text" placeholder="Email Address" class="input-medium">
						<button class="btn-u">Subscribe</button>
					</div>
				</form>							
			</div><!--/span4-->	
			
			<div class="span4">
                <div class="posts">
                    <div class="headline"><h3>Recent Blog Entries</h3></div>
                    <dl class="dl-horizontal">
                        <dt><a href="#"><img src="<?php curl::base(); ?>cresenity/noimage/56/56" alt="" /></a></dt>
                        <dd>
                            <p><a href="#">Anim moon officia Unify is an incredibly beautiful responsive Bootstrap Template</a></p> 
                        </dd>
                    </dl>
                    <dl class="dl-horizontal">
                    <dt><a href="#"><img src="<?php curl::base(); ?>cresenity/noimage/56/56" alt="" /></a></dt>
                        <dd>
                            <p><a href="#">Anim moon officia Unify is an incredibly beautiful responsive Bootstrap Template</a></p> 
                        </dd>
                    </dl>
                    <dl class="dl-horizontal">
                    <dt><a href="#"><img src="<?php curl::base(); ?>cresenity/noimage/56/56" alt="" /></a></dt>
                        <dd>
                            <p><a href="#">Anim moon officia Unify is an incredibly beautiful responsive Bootstrap Template</a></p> 
                        </dd>
                    </dl>
                </div>
			</div><!--/span4-->

			<div class="span4">
	            <!-- Monthly Newsletter -->
		        <div class="headline"><h3>Contact Us</h3></div>	
                <address>
					25, Lorem Lis Street, Orange <br />
					California, US <br />
					Phone: 800 123 3456 <br />
					Fax: 800 123 3456 <br />
					Email: <a href="mailto:info@anybiz.com" class="">info@anybiz.com</a>
                </address>

                <!-- Stay Connected -->
		        <div class="headline"><h3>Stay Connected</h3></div>	
                <ul class="social-icons">
                    <li><a href="#" data-original-title="Feed" class="social_rss"></a></li>
                    <li><a href="#" data-original-title="Facebook" class="social_facebook"></a></li>
                    <li><a href="#" data-original-title="Twitter" class="social_twitter"></a></li>
                    <li><a href="#" data-original-title="Goole Plus" class="social_googleplus"></a></li>
                    <li><a href="#" data-original-title="Pinterest" class="social_pintrest"></a></li>
                    <li><a href="#" data-original-title="Linkedin" class="social_linkedin"></a></li>
                    <li><a href="#" data-original-title="Vimeo" class="social_vimeo"></a></li>
                </ul>
			</div><!--/span4-->
		</div><!--/row-fluid-->	
	</div><!--/container-->	
</div><!--/footer-->	
<!--=== End Footer ===-->

<!--=== Copyright ===-->
<div class="copyright">
	<div class="container">
		<div class="row-fluid">
			<div class="span8">						
	            <p>2013 &copy; <?php echo $org->name; ?>. ALL Rights Reserved. <a href="javascript:;">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
			</div>
			<div class="span4">	
				<?php
					//$logo_image = $org->logo_image;
					$logo_image = "";
					$imgsrc= curl::base().'cresenity/noimage/40/40';
					if(strlen($logo_image)>0) {
						$imgsrc=cimage::get_image_src("logo_image",$org->org_id,"thumbnail",$logo_image);
					}
				?>
				<a href="<?php echo curl::base(); ?>"><img id="logo-footer" src="<?php echo $imgsrc; ?>" class="pull-right" alt="" /></a>
			</div>
		</div><!--/row-fluid-->
	</div><!--/container-->	
</div><!--/copyright-->	
<!--=== End Copyright ===-->

	<!-- Load javascript here -->
	<script src="<?php echo curl::base();?>media/js/libs/json2.js"></script>
	<script src="<?php echo curl::base();?>media/js/libs/excanvas.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/libs/jquery.js"></script>
	<script src="<?php echo curl::base();?>media/js/libs/jquery.ui.custom.js"></script>

	<script src="<?php echo curl::base(); ?>media/js/libs/tmpl.min.js"></script>
	<script src="<?php echo curl::base(); ?>media/js/libs/load-image.min.js"></script>
	<script src="<?php echo curl::base(); ?>media/js/libs/canvas-to-blob.min.js"></script>
	<script src="<?php echo curl::base(); ?>media/js/libs/wysihtml5-0.3.0.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/libs/bootstrap.min.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/form/jquery.form.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/controls/jquery.controls.js"></script>
	
	
	<!-- Dialog -->
	<script src="<?php echo curl::base();?>media/js/plugins/dialog2/jquery.dialog2.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/dialog2/jquery.dialog2.helpers.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/effects/jquery.effects.core.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/effects/jquery.effects.slide.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/validation-engine/jquery.validationEngine.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/validation-engine/languages/jquery.validationEngine-en.js"></script>
	
	<!-- Datepicker -->
	<script src="<?php echo curl::base();?>media/js/plugins/datepicker/bootstrap-datepicker.js"></script>
	<!-- Timepicker -->
	<script src="<?php echo curl::base();?>media/js/plugins/timepicker/bootstrap-timepicker.min.js"></script>
	<!-- Colorpicker -->
	<script src="<?php echo curl::base();?>media/js/plugins/colorpicker/bootstrap-colorpicker.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/wysihtml5/bootstrap-wysihtml5.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/notify/bootstrap-notify.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/bootbox/jquery.bootbox.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.bar.order.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.pie.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.resize.min.js"></script>
	<script src="<?php echo curl::base();?>media/js/plugins/flot/jquery.flot.stack.js"></script>
	
	<script src="<?php echo curl::base();?>media/js/plugins/peity/jquery.peity.min.js"></script>

	<!-- Custom file upload -->
	<script src="<?php echo curl::base();?>media/js/plugins/fileupload/bootstrap-fileupload.min.js"></script>
	
	
	
	<script src="<?php echo curl::base();?>ccore/js"></script>
	<script language="javascript">
		jQuery(document).ready(function() {
			
		
		});
		
	</script>
	<script language="javascript">
		jQuery(document).ready(function() {
			<?php
				echo $js;
			?>
		});
		<?php echo $custom_js ?>
	</script>
	<?php echo $custom_footer; ?>
</body>
</html>
<?php

	$user = CApp::instance()->user();
	$user_id = null;
	if($user!=null) {
		$user_id = $user->user_id;
		
	}
	clog::request($user_id);
	


?>