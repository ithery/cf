<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 4:28:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_ClientScript {

    /**
     * 
     * @deprecated please use registerCssFile
     * @param string $file
     * @param string $pos
     * @return $this
     */
    public function register_css_file($file, $pos = "head") {
        return $this->registerCssFile($file, $pos);
    }

    /**
     * 
     * @deprecated please use registerCssFile
     * @param string $file
     * @param string $pos
     * @return $this
     */
    public function register_js_file($file, $pos = "end") {
        return $this->registerJsFile($file,$pos);
    }
}
