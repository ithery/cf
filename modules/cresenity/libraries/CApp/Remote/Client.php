<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 14, 2018, 9:16:56 PM
 */
class CApp_Remote_Client {
    protected $domain = '';
    protected $remote;

    public function __construct(CApp_Remote $remote) {
        $this->remote = $remote;
        $this->domain = $remote->getDomain();
    }

    private function createEngine($engineName) {
        $className = 'CApp_Remote_Client_Engine_' . $engineName;
        return new $className($this->engineOptions());
    }

    private function engineOptions() {
        $remoteOptions = $this->remote->getOptions();
        $domain = $this->remote->getDomain();
        $options = array_merge($remoteOptions, ['domain' => $domain]);
        return $options;
    }

    /**
     * @return CApp_Remote_Client_Engine_Server
     */
    public function server() {
        return $this->createEngine('Server');
    }

    /**
     * @return CApp_Remote_Client_Engine_App
     */
    public function app() {
        return $this->createEngine('App');
    }
}
