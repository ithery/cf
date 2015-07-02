<?php

defined('SYSPATH') OR die('No direct access allowed.');

class App_setting_Controller extends CController {

    public function index() {
        $app = CApp::instance();

        $app->title('App Setting');
        $master_config = CF::get_config('master');

        $request = $this->input->post();

        if ($request != null) {
            unset($request['app_setting_submit']);
            $dir = CF::get_dir('config');
            $file = $dir . 'app_setting' . EXT;
            cphp::save_value($request, $file);
            curl::redirect(curl::base() . "app_setting");
        }

        $form = $app->add_form()->set_layout('vertical');
        $tabs = $form->add_tab_list()->set_ajax(false);
        $jsstmt = "";
        $change_refresh = array();
        foreach ($master_config as $master_key => $master) {

            $tab = $tabs->add_tab($master_key)->set_label(carr::get($master, 'label'));
            $all_config = carr::get($master, 'config');
            if ($all_config != null) {
                foreach ($all_config as $cfg) {

                    $field = $tab->add_field('field-' . $cfg['name']);
                    if ($cfg["type"] != "checkbox") {
                        $field->set_label(clang::__($cfg['label']));
                    }
                    $control = $field->add_control($cfg['name'], $cfg['type']);
                    $val = '';
                    //$val = $cfg["default"];

                    $ccfg_val = ccfg::get($cfg['name']);


                    if ($ccfg_val != null) {
                        $val = $ccfg_val;
                    }
                    if (isset($cfg["help"]) && strlen($cfg["help"]) > 0) {
                        $field->set_info_text($cfg["help"]);
                    }
                    $control->set_value($val);

                    if ($val && $cfg["type"] == "checkbox") {
                        $control->set_checked($val);
                    }
                    if ($cfg["type"] == "checkbox") {
                        $control->set_value("1");
                        //$control->set_applyjs("switch");
                        $control->set_applyjs("");
                        $control->set_label(clang::__($cfg['label']));
                    }
                    if ($cfg["type"] == "select") {
                        $control->set_applyjs("");
                        if (isset($cfg['list'])) {
                            $control->set_list($cfg['list']);
                        }
                        if (isset($cfg['multiple'])) {
                            $control->set_multiple($cfg['multiple']);
                            $control->set_applyjs("select2");
                        }
                    }
                    if (isset($cfg['requirement'])) {
                        $cond = '';
                        foreach ($cfg['requirement'] as $k => $v) {
                            if (!in_array($k, $change_refresh)) {
                                $change_refresh[] = $k;
                            }
                            if (strlen($cond) > 0)
                                $cond.="&&";
                            if (is_bool($v) === true) {
                                if ($v === true) {
                                    $cond.="(jQuery('#" . $k . "').is(':checked'))";
                                } else {
                                    $cond.="(!jQuery('#" . $k . "').is(':checked'))";
                                }
                            } else {
                                $cond.="(jQuery('#" . $k . "').val()=='" . $v . "')";
                            }
                        }
                        if (strlen($cond) == 0)
                            $cond = 'false';
                        $jsstmt.="
							
							pare = jQuery('#" . $cfg["name"] . "').parent().parent();
							
							if(pare.hasClass('controls')) {
								pare = pare.parent();
							}
							if(" . $cond . ") {
								//pare.fadeIn('slow',function() {jQuery(this).show()});
								pare.show();
							} else {
								pare.hide();
								//pare.fadeOut('slow',function() {jQuery(this).hide()});
							}";
                    }
                }
            }
        }

        $jschange = "";
        foreach ($change_refresh as $v) {
            $jschange.="
				jQuery('#" . $v . "').change(function() { refresh_controls() });
			";
        }
        $js = "
			function refresh_controls() {
				" . $jsstmt . "
			}
			jQuery(document).ready(function() {
				" . $jschange . "
				refresh_controls();
			});
			
		";
        $app->add_js($js);
        $form->add_action_list()->add_action('app_setting_submit')->set_submit(true)->set_label(clang::__('Save'));
        $form->set_action(curl::base() . 'app_setting');
        echo $app->render();
    }

}