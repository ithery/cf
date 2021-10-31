<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 1:59:16 AM
 */
class CApp_Api_Method_Server_DomainDelete extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;

        $request = $this->request();
        $domainToDelete = carr::get($request, 'domain');

        $data = [];
        if ($errCode == 0) {
            if (strlen($domainToDelete) == 0) {
                $errCode++;
                $errMessage = 'parameter domain required';
            }
        }
        if ($errCode == 0) {
            try {
                CFData::delete($domainToDelete, 'domain');
            } catch (Exception $ex) {
                $errCode++;
                $errMessage = $ex->getMessage();
            }
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
