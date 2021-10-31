<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2018, 4:28:05 PM
 */

 // @codingStandardsIgnoreStart
trait CTrait_Compat_ClientScript {
    /**
     * @deprecated please use registerJsFiles
     *
     * @param type $files
     * @param type $pos
     *
     * @return type
     */
    public function register_js_files($files, $pos = 'end') {
        return $this->registerJsFiles($files, $pos);
    }

    /**
     * @deprecated please use registerCssFiles
     *
     * @param type $files
     * @param type $pos
     *
     * @return type
     */
    public function register_css_files($files, $pos = 'head') {
        return $this->registerCssFiles($files, $pos);
    }

    /**
     * @deprecated please use registerCssFile
     *
     * @param string $file
     * @param string $pos
     *
     * @return $this
     */
    public function register_css_file($file, $pos = 'head') {
        return $this->registerCssFile($file, $pos);
    }

    /**
     * @deprecated please use registerCssFile
     *
     * @param string $file
     * @param string $pos
     *
     * @return $this
     */
    public function register_js_file($file, $pos = 'end') {
        return $this->registerJsFile($file, $pos);
    }

    /**
     * @deprecated
     */
    public function create_js_hash() {
        //return CResource::instance('js')->create_hash($this->jsFiles());
    }

    /**
     * @deprecated
     */
    public function create_css_hash() {
        //return CResource::instance('css')->create_hash($this->cssFiles());
    }

    /**
     * @param mixed $hash
     *
     * @deprecated
     */
    public function js($hash) {
        //return CResource::instance('js')->load($hash);
    }

    /**
     * @param mixed $hash
     *
     * @deprecated
     */
    public function css($hash) {
        //return CResource::instance('css')->load($hash);
    }
}
// @codingStandardsIgnoreEnd
