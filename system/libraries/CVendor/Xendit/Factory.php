<?php

class CVendor_Xendit_Factory {
    protected $apiRequestor;

    public function __construct(CVendor_Xendit $xendit) {
        $this->apiRequestor = new CVendor_Xendit_ApiRequestor($xendit->getSecretApiKey(), $xendit->getServerDomain(), $xendit->getLibVersion());
    }

    /**
     * @return CVendor_Xendit_Account
     */
    public function account() {
        return new CVendor_Xendit_Account($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Customers
     */
    public function customers() {
        return new CVendor_Xendit_Customers($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Balance
     */
    public function balance() {
        return new CVendor_Xendit_Balance($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Recurring
     */
    public function recurring() {
        return new CVendor_Xendit_Recurring($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_QRCode
     */
    public function qrcode() {
        return new CVendor_Xendit_QRCode($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_VirtualAccount
     */
    public function virtualAccount() {
        return new CVendor_Xendit_VirtualAccount($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Card
     */
    public function card() {
        return new CVendor_Xendit_Card($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Retail
     */
    public function retail() {
        return new CVendor_Xendit_Retail($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_EWallet
     */
    public function ewallet() {
        return new CVendor_Xendit_EWallet($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_CardlessCredit
     */
    public function cardlessCredit() {
        return new CVendor_Xendit_CardlessCredit($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Promotion
     */
    public function promotion() {
        return new CVendor_Xendit_Promotion($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Payout
     */
    public function payout() {
        return new CVendor_Xendit_Payout($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Invoice
     */
    public function invoice() {
        return new CVendor_Xendit_Invoice($this->apiRequestor);
    }

    /**
     * @return CVendor_Xendit_Disbursement
     */
    public function disbursement() {
        return new CVendor_Xendit_Disbursement($this->apiRequestor);
    }
}
