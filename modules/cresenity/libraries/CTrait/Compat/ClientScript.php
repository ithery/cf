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
     * @deprecated please use registerJsFiles
     * @param type $files
     * @param type $pos
     * @return type
     */
    public function register_js_files($files, $pos = "end") {
        return $this->registerJsFiles($files, $pos);
    }

    /**
     * 
     * @deprecated please use registerCssFiles
     * @param type $files
     * @param type $pos
     * @return type
     */
    public function register_css_files($files, $pos = "head") {
        return $this->registerCssFiles($files, $pos);
    }

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
        return $this->registerJsFile($file, $pos);
    }

   

}
