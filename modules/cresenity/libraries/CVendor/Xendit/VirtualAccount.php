<?php

class CVendor_Xendit_VirtualAccount extends CVendor_Xendit_Base {
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;
    use CVendor_Xendit_ApiOperation_Update;

    /**
     * Instantiate base URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/callback_virtual_accounts';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return ['external_id', 'bank_code', 'name'];
    }

    /**
     * Instantiate required params for Update
     *
     * @return array
     */
    protected function updateReqParams() {
        return [];
    }

    /**
     * Get available VA banks
     *
     * @return array[
     *                'name' => string,
     *                'code' => string
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function getVABanks() {
        $url = '/available_virtual_account_banks';

        return $this->request('GET', $url);
    }

    /**
     * Get FVA payment
     *
     * @param string $payment_id payment ID
     *
     * @return array[
     *                'id'=> string,
     *                'payment_id'=> string,
     *                'callback_virtual_account_id'=> string,
     *                'external_id'=> string,
     *                'merchant_code'=> string,
     *                'account_number'=> string,
     *                'bank_code'=> string,
     *                'amount'=> int,
     *                'transaction_timestamp'=> string
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function getFVAPayment($payment_id) {
        $url = '/callback_virtual_account_payments/payment_id=' . $payment_id;

        return $this->request('GET', $url);
    }
}
