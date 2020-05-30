<?php

/**
 * Description of Ejabberd
 * 
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since May 30, 2020 
 * @license Ittron Global Teknologi
 */
class CXMPP_Ejabberd {

    /**
     *
     * @var CXMPP_Ejabberd_Client 
     */
    protected $client;

    protected $config;
    
    /**
     * Ejabberd constructor.
     * @param $config
     * @throws Exception
     */
    public function __construct($config) {
        $this->config = $config;
        $this->client = new CXMPP_Ejabberd_Client($config);
    }

    public function createUser($user, $password, $host =null) {
        
        if($host==null) {
            $host = carr::get($this->config,'domain');
        }
        $command = new CXMPP_Ejabberd_Command_CreateUser($user, $password, $host);
        return $this->client->execute($command);
    }

}
