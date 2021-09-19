<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 9:11:33 PM
 */
abstract class CApp_Api_Method_App extends CApp_Api_Method {
    protected $appCode;

    public function __construct(CApp_Api $api, $method, $request = null) {
        parent::__construct($api, $method, $request);

        $this->appCode = carr::get($this->request(), 'appCode');

        if (empty($this->appCode)) {
            $this->errCode++;
            $this->errMessage = 'appCode is required';
        }
        $avalableAppList = CF::getAvailableAppCode();

        if (!in_array($this->appCode, $avalableAppList)) {
            $this->errCode++;
            $this->errMessage = 'appCode ' . $this->appCode . ' not found';
        }
    }

    public function appCode() {
        return $this->appCode;
    }
}
