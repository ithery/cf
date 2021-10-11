<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 28, 2019, 3:00:18 AM
 */
class CManager_File_Config_ElFinder extends CManager_File_ConfigAbstract {
    public function __construct(array $options) {
        $path = carr::get($options, 'path', DOCROOT . 'temp/files');
        $url = str_replace(DOCROOT, curl::base(), $path);
        $uploadAllow = carr::get($options, 'mime', ['image/jpeg', 'image/png']);
        $access = carr::get($options, 'access', 'access');
        // Documentation for connector options:
        // https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
        $opts = [
            // 'debug' => true,
            'roots' => [
                // Items volume
                [
                    'driver' => 'LocalFileSystem', // driver for accessing file system (REQUIRED)
                    'path' => $path, // path to files (REQUIRED)
                    'URL' => $url, // URL to files (REQUIRED)
                    'winHashFix' => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny' => [], // All Mimetypes not allowed to upload
                    'uploadAllow' => $uploadAllow,
                    'uploadOrder' => ['deny', 'allow'], // allowed Mimetype `image` and `text/plain` only
                    'accessControl' => $access                     // disable and hide dot starting files (OPTIONAL)
                ],
            ]
        ];
        $this->options = $opts;
    }
}
