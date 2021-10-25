<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_Server_Ssl_AddCertificate extends CApp_Api_Method_Server {
    public function execute() {
        $data = [];

        $request = $this->request();
        $private = carr::get($request, 'private', null);
        $public = carr::get($request, 'public', null);
        $domain = carr::get($request, 'domain', null);

        if (empty($filename)) {
            $this->errCode++;
            $this->errMessage = "Filename required";
        }

        if (empty($content)) {
            $this->errCode++;
            $this->errMessage = "Content required";
        }

        if (empty($domain)) {
            $this->errCode++;
            $this->errMessage = "Domain required";
        }

        if ($this->errCode == 0) {
            $certDirectory = DOCROOT . "certificate/letsencrypt/$domain/account";
            if (!file_exists($certDirectory)) {
                mkdir($certDirectory, 0777, true);
            }

            file_put_contents("$certDirectory/private.pem", $private);
            file_put_contents("$certDirectory/public.pem", $public);

            if (!file_exists("$certDirectory/private.pem") || !file_exists("$certDirectory/public.pem")) {
                $this->errCode++;
                $this->errMessage = "Failed to write certificate";
            }
        }

        $this->data = $data;

        return $this;
    }
}
