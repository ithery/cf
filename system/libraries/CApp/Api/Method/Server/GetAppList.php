<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_Server_GetAppList extends CApp_Api_Method_Server {
    /**
     * Lists the app folders present under the server's application directory.
     *
     * @return $this
     */
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
