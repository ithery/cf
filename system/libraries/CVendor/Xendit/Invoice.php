<?php

class CVendor_Xendit_Invoice extends CVendor_Xendit_Base {
    use CVendor_Xendit_ApiOperation_Create;
    use CVendor_Xendit_ApiOperation_Retrieve;
    use CVendor_Xendit_ApiOperation_RetrieveAll;

    /**
     * Instantiate base URL
     *
     * @return string
     */
    protected function classUrl() {
        return '/v2/invoices';
    }

    /**
     * Instantiate required params for Create
     *
     * @return array
     */
    protected function createReqParams() {
        return ['external_id', 'description', 'amount'];
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
     * Expire Invoice
     *
     * @param string $id Invoice ID
     *
     * @return array[
     *                'id'=> string,
     *                'user_id'=> string,
     *                'external_id'=> string,
     *                'status'=> 'EXPIRED',
     *                'merchant_name'=> string,
     *                'merchant_profile_picture_url'=> string,
     *                'amount'=> int,
     *                'payer_email'=> string,
     *                'description'=> string,
     *                'invoice_url'=> string,
     *                'expiry_date'=> string,
     *                'available_banks'=> array,
     *                'should_exclude_credit_card'=> bool,
     *                'should_send_email'=> bool,
     *                'created'=> string,
     *                'updated'=> string
     *                ]
     *
     * @throws CVendor_Xendit_Exception_ApiException
     */
    public function expireInvoice($id) {
        $url = '/invoices/' . $id . '/expire!';

        return $this->request('POST', $url);
    }
}
