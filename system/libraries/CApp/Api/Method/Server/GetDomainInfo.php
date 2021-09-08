<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 4:40:47 AM
 */
class CApp_Api_Method_Server_GetDomainInfo extends CApp_Api_Method_Server {
    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;
        $request = $this->request();
        $domainToInfo = carr::get($request, 'domain');
        $data = [];
        if ($errCode == 0) {
            if (strlen($domainToInfo) == 0) {
                $errCode++;
                $errMessage = 'parameter domain required';
            }
        }
        if ($errCode == 0) {
            $data = CFData::domain($domainToInfo);
        }
        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }
}
