<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_File {
    protected $resize = true;

    protected $auto_upload = true;

    protected $paste_zone = "jQuery('body')";

    protected $drop_zone = "jQuery('body')";

    protected $url;

    protected $max_file_size = 99999;

    protected $accept_file_type = "/(\.|\/)(gif|jpe?g|png)$/i";

    protected $callback_drop;

    protected $before_submit;

    protected $callback_success;

    protected $callback_progress;

    protected $max_number_of_files;

    protected $input_help;

    public function set_multiple($bool) {
        return $this->setMultiple($bool);
    }

    public function set_applyjs($applyjs) {
        return $this->setApplyJs($applyjs);
    }

    public function set_resize($resize) {
        $this->resize = $resize;
        return $this;
    }

    public function get_paste_zone() {
        return $this->paste_zone;
    }

    public function get_url() {
        return $this->url;
    }

    public function get_max_file_size() {
        return $this->max_file_size;
    }

    public function get_resize() {
        return $this->resize;
    }

    public function get_accept_file_type() {
        return $this->accept_file_type;
    }

    public function set_paste_zone($paste_zone) {
        $this->paste_zone = $paste_zone;
        return $this;
    }

    public function set_url($url) {
        $this->url = $url;
        return $this;
    }

    public function set_max_file_size($max_file_size) {
        $this->max_file_size = $max_file_size;
        return $this;
    }

    public function set_accept_file_type($accept_file_type) {
        $this->accept_file_type = $accept_file_type;
        return $this;
    }

    public function get_callback_drop() {
        return $this->callback_drop;
    }

    public function set_callback_drop($callback_drop) {
        $this->callback_drop = $callback_drop;
        return $this;
    }

    public function get_before_submit() {
        return $this->before_submit;
    }

    public function get_callback_success() {
        return $this->callback_success;
    }

    public function set_before_submit($before_submit) {
        $this->before_submit = $before_submit;
        return $this;
    }

    public function set_callback_success($callback_success) {
        $this->callback_success = $callback_success;
        return $this;
    }

    public function get_callback_progress() {
        return $this->callback_progress;
    }

    public function set_callback_progress($callback_progress) {
        $this->callback_progress = $callback_progress;
        return $this;
    }

    public function get_drop_zone() {
        return $this->drop_zone;
    }

    public function get_auto_upload() {
        return $this->auto_upload;
    }

    public function set_drop_zone($drop_zone) {
        $this->drop_zone = $drop_zone;
        return $this;
    }

    public function set_auto_upload($auto_upload) {
        $this->auto_upload = $auto_upload;
        return $this;
    }

    public function get_max_number_of_files() {
        return $this->max_number_of_files;
    }

    public function set_max_number_of_files($max_number_of_files) {
        $this->max_number_of_files = $max_number_of_files;
        return $this;
    }

    public function set_input_help($param) {
        $this->input_help = $param;
        return $this;
    }
}
