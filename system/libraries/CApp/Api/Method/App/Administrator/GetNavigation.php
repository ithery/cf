<?php

class CApp_Api_Method_App_Administrator_GetNavigation extends CApp_Api_Method_App {
    public function execute() {
        $appCode = $this->appCode;
        $defaultPath = DOCROOT . 'application/' . $appCode . '/default/';
        $navPath = DOCROOT . 'application/' . $appCode . '/default/navs/';
        $configPath = DOCROOT . 'application/' . $appCode . '/default/config/';
        $navFiles = [];
        if (CFile::isDirectory($navPath)) {
            $allFiles = CFile::files($navPath);
            foreach ($allFiles as $file) {
                $file = $file->__toString();
                $navFiles[] = $file;
            }
        }
        if (CFile::isDirectory($configPath)) {
            $navFile = $configPath . 'nav.php';
            if (CFile::isFile($navFile)) {
                $navFiles[] = $navFile;
            }
        }
        $data = [];
        foreach ($navFiles as $file) {
            $pathParts = pathinfo($file);
            $navData = include $file;
            $data[$pathParts['filename']] = $navData;
        }
        $this->data = $data;
    }
}
