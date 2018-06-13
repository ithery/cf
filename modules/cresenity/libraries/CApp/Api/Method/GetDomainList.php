<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 4:40:47 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_GetDomainList extends CApp_Api_Method {

    public function execute() {


        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;

        $fileHelper = CHelper::file();
        $allFiles = $fileHelper->files(CFData::path() . 'domain');
        $files = array();
        foreach ($allFiles as $fileObject) {
            $file = $fileObject->getPathname();
            $file = basename($file);
            if (substr($file, -4) == '.php') {
                $file = substr($file, 0, strlen($file) - 4);
            }
            $files[] = $file;
        }


        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $files;

        return $this;
    }

}
