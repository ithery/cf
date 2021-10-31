<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 28, 2019, 8:08:26 PM
 */
class CApp_Api_Method_Server_GetAppList extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $apps = [];
        $allFolders = cfs::list_dir(DOCROOT . 'application');
        foreach ($allFolders as $folder) {
            $app = [
                'app' => $folder,
                'created' => date('Y-m-d H:i:s', filectime($folder)),
            ];
            $apps[] = $app;
        }
        $data = [];
        $data['list'] = $apps;
        $data['count'] = count($apps);

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
