<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 14, 2018, 9:11:33 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

abstract class CApp_Api_Method_App extends CApp_Api_Method {
    
    protected $appCode;


    public function __construct(CApp_Api $api, $method, $request = null) {
        parent::__construct($api, $method, $request);
        
        $this->appCode = carr::get($this->request(), 'appCode');
        
        if(empty($this->appCode)){
            $this->errCode++;
            $this->errMessage = 'appCode is required';
        }
    }
    
    public function appCode(){
        return $this->appCode;
    }
}