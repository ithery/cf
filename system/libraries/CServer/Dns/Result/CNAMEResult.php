<?php

class CServer_Dns_Result_CNAMEResult extends CServer_Dns_Result {
    /**
     * @var string
     */
    private $redirect;

    public function __construct($redirect) {
        parent::__construct();
        $this->setRedirect($redirect);
    }

    /**
     * @param string $redirect
     */
    public function setRedirect($redirect) {
        $this->redirect = $redirect;
    }

    /**
     * @return string
     */
    public function getRedirect() {
        return $this->redirect;
    }
}
