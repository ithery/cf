<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

</div>



<script src="<?php echo curl::base(); ?>media/js/libs/jquery-2.1.1.min.js"></script>
<script src="<?php echo curl::base(); ?>media/js/materialize/materialize.min.js"></script>
<script src="<?php echo curl::base(); ?>media/js/materialize/swiper/swiper.jquery.js"></script>
<script src="<?php echo curl::base(); ?>media/js/require.js"></script>
<script language="javascript">
$(".button-collapse").sideNav();
</script>


<!-- Load javascript here -->

<?php echo $end_client_script; ?>

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



<!-- requirejs.config({
    baseUrl: '/media/js/materialize',
    paths: {
        'jquery': '../libs/jquery-2.1.1.min',
        'picker': 'date_picker/picker',
        'picker.date': 'date_picker/picker.date',
        'hammerjs': 'hammer.min',
        'jquery.easing': 'jquery.easing.1.3',
        'jquery.timeago': 'jquery.timeago.min',
        'velocity': 'velocity.min',
    },
    shim: {
        'jquery': {exports: '$'},
        'animation': {deps: ['jquery']},
        'buttons': {deps: ['jquery']},
        'cards': {deps: ['jquery']},
        'character_counter': {deps: ['jquery']},
        'collapsible': {deps: ['jquery']},
        'dropdown': {deps: ['jquery']},
        'forms': {deps: ['jquery', 'global']},
        'global': {deps: ['jquery'], exports: 'Materialize'},
        'hammerjs': {},
        'jquery.easing': {deps: ['jquery']},
        'jquery.hammer': {deps: ['jquery', 'hammerjs', 'waves']},
        'jquery.timeago': {deps: ['jquery']},
        'leanModal': {deps: ['jquery']},
        'materialbox': {deps: ['jquery']},
        'parallax': {deps: ['jquery']},
        'pushpin': {deps: ['jquery']},
        'scrollFire': {deps: ['jquery', 'global']},
        'scrollspy': {deps: ['jquery']},
        'sideNav': {deps: ['jquery']},
        'slider': {deps: ['jquery']},
        'tabs': {deps: ['jquery']},
        'velocity': {deps: ['jquery']},
        'toasts': {
            deps: ['global', 'hammerjs', 'velocity'], init: function(Materialize, Hammer, Vel) {
                window.Hammer = Hammer;
                window.Vel = Vel;
            }
        },
        'tooltip': {deps: ['jquery']},
        'transitions': {deps: ['jquery', 'scrollFire']},
        'waves': {exports: 'Waves'},
        'carousel': {deps: ['jquery']},
    },
    // exclude: [
    //     'jquery'
    // ],
    include: [
        'global', 'animation', 'buttons', 'cards', 'character_counter',
        'collapsible', 'dropdown', 'forms', 'hammerjs', 'jquery.easing',
        'jquery.hammer', 'jquery.timeago', 'leanModal', 'materialbox',
        'parallax', 'picker', 'picker.date', 'pushpin', 'scrollFire',
        'scrollspy', 'sideNav', 'slider', 'tabs', 'toasts', 'tooltip',
        'transitions', 'velocity', 'waves', 'carousel'
    ],
    wrap: {
        endFile: 'main.js'
    }
}); 
requirejs([
    'global', 'waves', 'animation', 'buttons', 'cards', 'character_counter',
    'collapsible', 'dropdown', 'forms', 'hammerjs', 'jquery.easing',
    'jquery.hammer', 'jquery.timeago', 'leanModal', 'materialbox',
    'parallax', 'picker', 'picker.date', 'pushpin', 'scrollFire',
    'scrollspy', 'sideNav', 'slider', 'tabs', 'toasts', 'tooltip',
    'transitions', 'velocity', 'carousel'
], function(Materialize, Waves) {
    Materialize.Waves = Waves;
    $(".button-collapse").sideNav();
    return Materialize;
});
-->