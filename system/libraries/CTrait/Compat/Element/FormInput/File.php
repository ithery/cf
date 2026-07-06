<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_FormInput_File
 *
 * None of these properties are read anywhere in CElement_FormInput_File
 * itself (which renders via cres.js's own upload widget) -- they're legacy
 * config knobs for an older jQuery File Upload plugin, kept only so these
 * deprecated setters/getters round-trip a value without raising an undefined
 * property notice.
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_File {
    /**
     * @var bool
     */
    protected $resize = true;

    /**
     * @var bool
     */
    protected $auto_upload = true;

    /**
     * @var string
     */
    protected $paste_zone = "jQuery('body')";

    /**
     * @var string
     */
    protected $drop_zone = "jQuery('body')";

    /**
     * @var null|string
     */
    protected $url;

    /**
     * @var int
     */
    protected $max_file_size = 99999;

    /**
     * @var string
     */
    protected $accept_file_type = "/(\.|\/)(gif|jpe?g|png)$/i";

    /**
     * @var null|callable|Closure
     */
    protected $callback_drop;

    /**
     * @var null|callable|Closure
     */
    protected $before_submit;

    /**
     * @var null|callable|Closure
     */
    protected $callback_success;

    /**
     * @var null|callable|Closure
     */
    protected $callback_progress;

    /**
     * @var null|int
     */
    protected $max_number_of_files;

    /**
     * @var null|string
     */
    protected $input_help;

    /**
     * @param bool $bool
     *
     * @return $this
     *
     * @deprecated use setMultiple
     */
    public function set_multiple($bool) {
        /** @var CElement_FormInput_File $this */
        return $this->setMultiple($bool);
    }

    /**
     * @param bool $applyjs
     *
     * @return $this
     *
     * @deprecated use setApplyJs
     */
    public function set_applyjs($applyjs) {
        /** @var CElement_FormInput_File $this */
        return $this->setApplyJs($applyjs);
    }

    /**
     * @param bool $resize
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_resize($resize) {
        /** @var CElement_FormInput_File $this */
        $this->resize = $resize;

        return $this;
    }

    /**
     * @return string
     *
     * @deprecated
     */
    public function get_paste_zone() {
        /** @var CElement_FormInput_File $this */
        return $this->paste_zone;
    }

    /**
     * @return null|string
     *
     * @deprecated
     */
    public function get_url() {
        /** @var CElement_FormInput_File $this */
        return $this->url;
    }

    /**
     * @return int
     *
     * @deprecated
     */
    public function get_max_file_size() {
        /** @var CElement_FormInput_File $this */
        return $this->max_file_size;
    }

    /**
     * @return bool
     *
     * @deprecated
     */
    public function get_resize() {
        /** @var CElement_FormInput_File $this */
        return $this->resize;
    }

    /**
     * @return string
     *
     * @deprecated
     */
    public function get_accept_file_type() {
        /** @var CElement_FormInput_File $this */
        return $this->accept_file_type;
    }

    /**
     * @param string $paste_zone
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_paste_zone($paste_zone) {
        /** @var CElement_FormInput_File $this */
        $this->paste_zone = $paste_zone;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_url($url) {
        /** @var CElement_FormInput_File $this */
        $this->url = $url;

        return $this;
    }

    /**
     * @param int $max_file_size
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_max_file_size($max_file_size) {
        /** @var CElement_FormInput_File $this */
        $this->max_file_size = $max_file_size;

        return $this;
    }

    /**
     * @param string $accept_file_type
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_accept_file_type($accept_file_type) {
        /** @var CElement_FormInput_File $this */
        $this->accept_file_type = $accept_file_type;

        return $this;
    }

    /**
     * @return null|callable|Closure
     *
     * @deprecated
     */
    public function get_callback_drop() {
        /** @var CElement_FormInput_File $this */
        return $this->callback_drop;
    }

    /**
     * @param callable|Closure $callback_drop
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_callback_drop($callback_drop) {
        /** @var CElement_FormInput_File $this */
        $this->callback_drop = $callback_drop;

        return $this;
    }

    /**
     * @return null|callable|Closure
     *
     * @deprecated
     */
    public function get_before_submit() {
        /** @var CElement_FormInput_File $this */
        return $this->before_submit;
    }

    /**
     * @return null|callable|Closure
     *
     * @deprecated
     */
    public function get_callback_success() {
        /** @var CElement_FormInput_File $this */
        return $this->callback_success;
    }

    /**
     * @param callable|Closure $before_submit
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_before_submit($before_submit) {
        /** @var CElement_FormInput_File $this */
        $this->before_submit = $before_submit;

        return $this;
    }

    /**
     * @param callable|Closure $callback_success
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_callback_success($callback_success) {
        /** @var CElement_FormInput_File $this */
        $this->callback_success = $callback_success;

        return $this;
    }

    /**
     * @return null|callable|Closure
     *
     * @deprecated
     */
    public function get_callback_progress() {
        /** @var CElement_FormInput_File $this */
        return $this->callback_progress;
    }

    /**
     * @param callable|Closure $callback_progress
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_callback_progress($callback_progress) {
        /** @var CElement_FormInput_File $this */
        $this->callback_progress = $callback_progress;

        return $this;
    }

    /**
     * @return string
     *
     * @deprecated
     */
    public function get_drop_zone() {
        /** @var CElement_FormInput_File $this */
        return $this->drop_zone;
    }

    /**
     * @return bool
     *
     * @deprecated
     */
    public function get_auto_upload() {
        /** @var CElement_FormInput_File $this */
        return $this->auto_upload;
    }

    /**
     * @param string $drop_zone
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_drop_zone($drop_zone) {
        /** @var CElement_FormInput_File $this */
        $this->drop_zone = $drop_zone;

        return $this;
    }

    /**
     * @param bool $auto_upload
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_auto_upload($auto_upload) {
        /** @var CElement_FormInput_File $this */
        $this->auto_upload = $auto_upload;

        return $this;
    }

    /**
     * @return null|int
     *
     * @deprecated
     */
    public function get_max_number_of_files() {
        /** @var CElement_FormInput_File $this */
        return $this->max_number_of_files;
    }

    /**
     * @param int $max_number_of_files
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_max_number_of_files($max_number_of_files) {
        /** @var CElement_FormInput_File $this */
        $this->max_number_of_files = $max_number_of_files;

        return $this;
    }

    /**
     * @param string $param
     *
     * @return $this
     *
     * @deprecated
     */
    public function set_input_help($param) {
        /** @var CElement_FormInput_File $this */
        $this->input_help = $param;

        return $this;
    }
}
