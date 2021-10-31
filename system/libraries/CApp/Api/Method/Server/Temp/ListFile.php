<?php

class CApp_Api_Method_Server_Temp_ListFile extends CApp_Api_Method_Server_Temp_Abstract {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->method->domain();
        $request = $this->method->request();
        $directory = carr::get($request, 'directory');
        $allFiles = cfs::list_files(DOCROOT . 'temp/' . ltrim($directory, '/'));
        $files = [];
        foreach ($allFiles as $filename) {
            $file = [
                'filename' => $filename,
                'created' => date('Y-m-d H:i:s', filemtime($filename)),
            ];
            $files[] = $file;
        }
        $data = [];
        $data['list'] = $files;
        $data['count'] = count($files);

        if ($errCode > 0) {
            throw new CApp_Api_Exception_InternalException($errMessage);
        }

        return $data;
    }
}
