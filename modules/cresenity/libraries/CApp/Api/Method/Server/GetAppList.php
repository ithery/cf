<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 28, 2019, 8:08:26 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_Server_GetAppList extends CApp_Api_Method_Server {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $apps = array();
        $allFolders = cfs::list_dir(DOCROOT . 'application');
        foreach ($allFolders as $folder) {

            $app = array(
                'app' => $folder,
                'created' => date('Y-m-d H:i:s', filectime($folder)),
            );
            $apps[] = $app;
        }
        $data = array();
        $data['list'] = $apps;
        $data['count'] = count($apps);

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
