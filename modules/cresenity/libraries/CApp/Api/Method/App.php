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
        
        $appCode = carr::get($this->request(), 'appCode');
        
        if(empty($appCode)){
            $this->errCode++;
            $this->errMessage = 'appCode is required';
        }
        
        $this->auth();
    }
    
    public function auth() {
        $apiKey = CF::config("app.api_key");
        $secretKey = CF::config("app.secret_key");
        
        $requestApiKey = carr::get($this->request(), 'api_key');
        $requestSecretKey = carr::get($this->request(), 'secret_key');
        
        if(empty($apiKey)){
            $this->errCode++;
            $this->errMessage = 'Project api_key not configured yet.';
        }
        
        if(empty($secretKey)){
            $this->errCode++;
            $this->errMessage = 'Project secret_key not configured yet.';
        }
        
        if($apiKey != $requestApiKey){
            $this->errCode++;
            $this->errMessage = 'Invalid API Key';
        }
        
        if($secretKey != $requestSecretKey){
            $this->errCode++;
            $this->errMessage = 'Invalid Secret Key';
        }
        
        if(empty($requestApiKey)){
            $this->errCode++;
            $this->errMessage = 'api_key is required.';
        }
        
        if(empty($requestSecretKey)){
            $this->errCode++;
            $this->errMessage = 'secret_key is required.';
        }
        
        if($apiKey != $requestApiKey && $secretKey != $requestSecretKey){
            $this->errCode = 9999;
            $this->errMessage = 'Authentication Failed!';
        }
        
    }
    
    public function appCode(){
        return $this->appCode;
    }
}