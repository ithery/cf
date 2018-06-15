<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 1:59:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Api_Method_Server_DomainUpdate extends CApp_Api_Method_Server {

    public function execute() {
        $errCode = 0;
        $errMessage = '';
        $domain = $this->domain;


        $data = array();
        $request = $this->request();
        $domainToCreate = carr::get($request, 'domain');
        $appId = carr::get($request, 'app_id');
        $appCode = carr::get($request, 'app_code');
        $orgId = carr::get($request, 'org_id', null);
        $orgCode = carr::get($request, 'org_code', null);
        if ($errCode == 0) {
            if (strlen($domainToCreate) == 0) {
                $errCode++;
                $errMessage = 'parameter domain required';
            }
        }
        if ($errCode == 0) {
            if (strlen($appId) == 0) {
                $errCode++;
                $errMessage = 'parameter app_id required';
            }
        }
        if ($errCode == 0) {
            if (strlen($appCode) == 0) {
                $errCode++;
                $errMessage = 'parameter app_code required';
            }
        }
        if ($errCode == 0) {
            if (CFData::get($domain, 'domain') != null) {
                $errCode++;
                $errMessage = 'domain ' . $domainToCreate . ' already exist on this server';
            }
        }
        try {
            $domainData = array();
            $domainData['app_id'] = $appId;
            $domainData['app_code'] = $appCode;
            $domainData['org_id'] = $orgId;
            $domainData['org_code'] = $orgCode;
            $domainData['domain'] = $domainToCreate;
            CFData::set($domainToCreate, $domainData, 'domain');
        } catch (Exception $ex) {
            $errCode++;
            $errMessage = $ex->getMessage();
        }

        $this->errCode = $errCode;
        $this->errMessage = $errMessage;
        $this->data = $data;

        return $this;
    }

}
