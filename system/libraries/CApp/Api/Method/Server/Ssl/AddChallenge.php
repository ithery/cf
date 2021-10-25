<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_Server_Ssl_AddChallenge extends CApp_Api_Method_Server {
    public function execute() {
        $data = [];

        $request = $this->request();
        $filename = carr::get($request, 'filename', null);
        $content = carr::get($request, 'content', null);

        if (empty($filename)) {
            $this->errCode++;
            $this->errMessage = "Filename required";
        }

        if (empty($content)) {
            $this->errCode++;
            $this->errMessage = "Content required";
        }

        if ($this->errCode == 0) {
            $folder = DOCROOT . '.well-known/acme-challenge/';
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            file_put_contents($folder . $filename, $content);

            if (!file_exists("$folder/$filename")) {
                $this->errCode++;
                $this->errMessage = "Failed to write file";
            }
        }

        $this->data = $data;

        return $this;
    }
}
