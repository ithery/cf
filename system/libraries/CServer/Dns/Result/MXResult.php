<?php

class CServer_Dns_Result_MXResult extends CServer_Dns_Result {
    /**
     * @var string
     */
    private $prio;

    /**
     * @var string
     */
    private $server;

    /**
     * @param string $prio
     */
    public function setPrio($prio) {
        $this->prio = $prio;
    }

    /**
     * @return string
     */
    public function getPrio() {
        return $this->prio;
    }

    /**
     * @param string $server
     */
    public function setServer($server) {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getServer() {
        return $this->server;
    }
}
