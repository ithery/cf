<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 11, 2019, 1:38:49 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_File_Config_FileManager {

    protected $options;

    public function __construct($options) {
        $this->options = $options;
    }

    public function getConfig() {

        $path = carr::get($this->options, 'path', DOCROOT . 'temp/files');
        $url = str_replace(DOCROOT, curl::base(), $path);
        $uploadAllow = carr::get($this->options, 'mime', array('image/jpeg', 'image/png'));
        $access = carr::get($this->options, 'access', 'access');
        // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $opts = array(
            // 'debug' => true,
            'roots' => array(
                // Items volume
                array(
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => $path, // path to files (REQUIRED)
                    'URL' => $url, // URL to files (REQUIRED)
                    'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny' => array(), // All Mimetypes not allowed to upload
                    'uploadAllow' => $uploadAllow,
                    'uploadOrder' => array('deny', 'allow'), // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => $access                     // disable and hide dot starting files (OPTIONAL)
                ),
            )
        );
        return $opts;
    }

}
