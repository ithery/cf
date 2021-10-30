<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Api_Method_Server_Ssl_AddCertificate extends CApp_Api_Method_Server {
    public function execute() {
        $data = [];

        $request = $this->request();
        $private = carr::get($request, 'private', null);
        $public = carr::get($request, 'public', null);
        $certificate = carr::get($request, 'certificate', null);
        $fullchain = carr::get($request, 'fullchain', null);
        $domain = carr::get($request, 'domain', null);

        if (empty($private)) {
            $this->errCode++;
            $this->errMessage = "Private Key required";
        }

        if (empty($public)) {
            $this->errCode++;
            $this->errMessage = "Public Key required";
        }

        if (empty($domain)) {
            $this->errCode++;
            $this->errMessage = "Domain required";
        }

        if (empty($certificate)) {
            $this->errCode++;
            $this->errMessage = "Certificate required";
        }

        if (empty($fullchain)) {
            $this->errCode++;
            $this->errMessage = "Fullchain required";
        }

        $certDirectory = DOCROOT . "certificate/letsencrypt/$domain";
        if ($this->errCode == 0) {
            if (!file_exists($certDirectory)) {
                mkdir($certDirectory, 0777, true);
            }

            file_put_contents("$certDirectory/private.pem", $private);
            file_put_contents("$certDirectory/public.pem", $public);
            file_put_contents("$certDirectory/certificate.crt", $certificate);
            file_put_contents("$certDirectory/fullchain.crt", $fullchain);

            if (
                !file_exists("$certDirectory/private.pem")
                || !file_exists("$certDirectory/public.pem")
                || !file_exists("$certDirectory/certificate.crt")
                || !file_exists("$certDirectory/fullchain.crt")
            ) {
                $this->errCode++;
                $this->errMessage = "Failed to write certificate";
            }
        }

        if ($this->errCode == 0) {
            $data = [
                "path" => $certDirectory
            ];
        }

        $this->data = $data;

        return $this;
    }
}
