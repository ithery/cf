<?php

/**
 * Class Billing
 *
 * Billing instrument used to charge the payer.
 *
 * @package PayPal\Api
 *
 * @property string billing_agreement_id
 */
class PayPal_Api_Billing extends PayPal_Common_PayPalModel
{
    /**
     * Identifier of the instrument in PayPal Wallet
     *
     * @param string $billing_agreement_id
     * 
     * @return $this
     */
    public function setBillingAgreementId($billing_agreement_id)
    {
        $this->billing_agreement_id = $billing_agreement_id;
        return $this;
    }

    /**
     * Identifier of the instrument in PayPal Wallet
     *
     * @return string
     */
    public function getBillingAgreementId()
    {
        return $this->billing_agreement_id;
    }

}
