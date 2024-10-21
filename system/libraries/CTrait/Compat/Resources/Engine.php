<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 10, 2018, 2:18:13 AM
 * @see CResources_Engine
 */

 //@codingStandardsIgnoreStart
trait CTrait_Compat_Resources_Engine {
    /**
     * @param null|mixed $filename
     * @param mixed      $size
     * @param mixed      $encode
     *
     * @deprecated 1.2
     */
    public function get_url($filename = null, $size = '', $encode = true) {
        return $this->getUrl($filename, $size, $encode);
    }

    /**
     * @param mixed      $filename
     * @param null|mixed $size
     *
     * @deprecated 1.2
     */
    public function get_path($filename, $size = null) {
        return $this->getPath($filename, $size);
    }

    public function get_root_directory() {
        return $this->getRootDirectory();
    }

    /**
     * @param string $rootDirectory
     *
     * @return $this
     *
     * @deprecated 1.2
     */
    public function set_root_directory($rootDirectory) {
        return $this->setRootDirectory($rootDirectory);
    }
}
