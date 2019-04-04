<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 4, 2019, 9:37:45 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_Server_GetFileList extends CApp_Api_Method_Server {

    public function execute() {


        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $directory = carr::get($request, 'directory');
        $allFiles = cfs::list_files(DOCROOT . $directory);
        $files = array();
        foreach ($allFiles as $filename) {


            $file = array(
                'filename' => $filename,
                'created' => date('Y-m-d H:i:s', filemtime($filename)),
            );
            $files[] = $file;
        }
        $data = array();
        $data['list'] = $files;
        $data['count'] = count($files);

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
