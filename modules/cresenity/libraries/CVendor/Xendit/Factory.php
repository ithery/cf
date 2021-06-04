<?php

class CVendor_Xendit_Factory {
    protected $apiRequestor;

    public function __construct(CVendor_Xendit $xendit) {
        $this->apiRequestor = new CVendor_Xendit_ApiRequestor($xendit->getSecretApiKey(), $xendit->getServerDomain(), $xendit->getLibVersion());
    }

    public function balance() {
        return new CVendor_Xendit_Balance($this->apiRequestor);
    }

    public function recurring() {
        return new CVendor_Xendit_Recurring($this->apiRequestor);
    }

    public function qrcode() {
        return new CVendor_Xendit_QRCode($this->apiRequestor);
    }

    public function virtualAccount() {
        return new CVendor_Xendit_VirtualAccount($this->apiRequestor);
    }

    public function card() {
        return new CVendor_Xendit_Card($this->apiRequestor);
    }

    public function retail() {
        return new CVendor_Xendit_Retail($this->apiRequestor);
    }

    public function ewallet() {
        return new CVendor_Xendit_EWallet($this->apiRequestor);
    }

    public function cardlessCredit() {
        return new CVendor_Xendit_CardlessCredit($this->apiRequestor);
    }
}
