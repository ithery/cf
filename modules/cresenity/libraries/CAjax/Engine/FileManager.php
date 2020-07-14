<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CAjax_Engine_FileManager extends CAjax_Engine {

    public function execute() {
        $options = array();
        $options['path'] = DOCROOT . 'temp/files';
        $args = $this->getArgs();
        $data = $this->getData();
        $config = carr::get($data, 'config');
        $method = carr::get($args, 1);
        $connector = CManager_File::createConnector('FileManager', $config);
        $connector->run($method);
    }

}
