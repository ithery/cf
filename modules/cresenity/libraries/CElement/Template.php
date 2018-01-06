<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jan 1, 2018, 4:15:30 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRenderable_Template extends CRenderable {

    protected $template_name;
    protected $template_data;
    protected $html_output = '';
    protected $js_output = '';

    protected function __construct($template_name, $data = array()) {
        parent::__construct();
        $this->template_data = array();
        $this->template_name = $template_name;
        $this->set_data($data);
    }

    public function theme() {
        return CF::theme();
    }

    public function get_data() {
        return $this->template_data;
    }

    public function set_data($data) {
        foreach ($data as $k => $v) {
            $this->set_var($k, $v);
        }
        return $this;
    }

    public function get_var($key) {
        return carr::get($this->template_data, $key);
    }

    public function set_var($key, $val) {

        $this->template_data[$key] = $val;
        return $this;
    }

    public static function factory($template_name, $data = array()) {
        return new CTemplate($template_name, $data = array());
    }

    public function html($indent = 0) {
        $this->collect_html_js();
        return $this->html_output;
    }

    public function js($indent = 0) {
        $this->collect_html_js();
        return $this->js_output;
    }

    private function get_view_path($template_name) {
        $view_path = $template_name;

        return $view_path;
    }

    private function parse_view($template_name) {
        $view_path = $this->get_view_path($template_name);
        $view = PMView::factory($view_path);
        PMBlocks::instance()->set_data($this->template_data);
        $view->set($this->template_data);
        $output = $view->render();
        $output_js = "";
        preg_match_all('#<script>(.*?)</script>#ims', $output, $matches);

        foreach ($matches[1] as $value) {
            $output_js .= $value;
        }
        $output_html = preg_replace('#<script>(.*?)</script>#is', '', $output);

        return array(
            'html' => $output_html,
            'js' => $output_js,
        );
    }

    private function collect_html_js() {
        if ($this->html_output == null) {
            $result_header = array();
            $result_content = array();
            $result_footer = array();


            $result_content = $this->parse_view($this->template_name);


            $this->html_output = carr::get($result_header, 'html', '') . carr::get($result_content, 'html', '') . carr::get($result_footer, 'html', '');
            $this->js_output = carr::get($result_header, 'js', '') . carr::get($result_content, 'js', '') . carr::get($result_footer, 'js', '');
        }
        return true;
    }

}
